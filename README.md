# SkillHub — OAuth 2.0 & Email Auth Implementation

## Overview

SkillHub uses a **Hybrid Authentication System** — users can login via Email/Password or Google OAuth 2.0.

---

## Tech Stack

- **Framework**: Laravel 12
- **Auth Package**: Laravel Socialite
- **Database**: MySQL
- **Session**: File-based
- **OAuth Provider**: Google

---

## System Architecture

```
User
 ├── Email/Password → AuthController → DB → Session → Dashboard
 └── Google OAuth   → GoogleController → Google API → DB → Session → Dashboard
```

---

## Database Schema

### `users` table

| Column | Type | Nullable | Description |
|---|---|---|---|
| id | bigint | No | Primary key |
| name | varchar(255) | No | User full name |
| email | varchar(255) | No | Unique email |
| password | varchar(255) | Yes | Null for Google users |
| google_id | varchar(255) | Yes | Google account ID |
| avatar | varchar(255) | Yes | Google profile picture URL |
| access_token | text | Yes | Google access token |
| refresh_token | text | Yes | Google refresh token |
| remember_token | varchar(100) | Yes | Remember me token |
| created_at | timestamp | Yes | — |
| updated_at | timestamp | Yes | — |

---

## Implementation Workflow

### Part 1 — Email/Password Auth

#### Step 1: Routes

```php
// routes/web.php
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
```

#### Step 2: Register Flow

```
User fills form (name, email, password, confirm password)
        ↓
AuthController@register validates input
        ↓
Hash::make($password) — bcrypt hash করে
        ↓
User::create() — DB তে save করে
        ↓
Auth::login($user) — session তৈরি করে
        ↓
Redirect → /dashboard
```

#### Step 3: Login Flow

```
User fills form (email, password)
        ↓
AuthController@login validates input
        ↓
Auth::attempt(['email', 'password'], $remember)
        ↓
Laravel DB থেকে user খোঁজে + password verify করে
        ↓
Match হলে → session তৈরি → /dashboard
No match  → back with error
```

#### Step 4: Logout Flow

```
User clicks logout
        ↓
Auth::logout() — session থেকে user সরায়
        ↓
session()->invalidate() — session destroy করে
        ↓
session()->regenerateToken() — CSRF token নতুন করে
        ↓
Redirect → /login
```

---

### Part 2 — Google OAuth 2.0 Auth

#### Step 1: Install Socialite

```bash
composer require laravel/socialite
```

#### Step 2: Google Console Setup

```
1. console.cloud.google.com → New project
2. APIs & Services → Credentials → Create OAuth Client ID
3. Application type: Web application
4. Authorized redirect URI: http://127.0.0.1:8000/auth/google/callback
5. Copy Client ID and Client Secret
```

#### Step 3: Environment Config

```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

#### Step 4: Services Config

```php
// config/services.php
'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI'),
],
```

#### Step 5: Routes

```php
// routes/web.php
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
```

#### Step 6: Google OAuth Flow

```
User clicks "Continue with Google"
        ↓
GoogleController@redirect
        ↓
Socialite::driver('google')->redirect()
        ↓
User → Google login page
        ↓
User logs in + grants permission
        ↓
Google → sends Authorization Code → Laravel callback URL
        ↓
Socialite exchanges code for Access Token (internal API call)
        ↓
Socialite fetches user info with token (internal API call)
        ↓
$googleUser object contains:
  - getName()
  - getEmail()
  - getId()      (google_id)
  - getAvatar()
  - token        (access_token)
  - refreshToken (refresh_token)
        ↓
Check DB: User::where('email', $googleUser->getEmail())->first()
        ↓
User exists?
  YES → update google_id, avatar, tokens
  NO  → create new user (password = null)
        ↓
Auth::login($user)
        ↓
Redirect → /dashboard
```

#### Step 7: GoogleController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login failed.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $user->update([
                'google_id'     => $googleUser->getId(),
                'avatar'        => $googleUser->getAvatar(),
                'access_token'  => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
            ]);
        } else {
            $user = User::create([
                'name'          => $googleUser->getName(),
                'email'         => $googleUser->getEmail(),
                'google_id'     => $googleUser->getId(),
                'avatar'        => $googleUser->getAvatar(),
                'password'      => null,
                'access_token'  => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
```

---

## Session & Cookie System

```
Login হলে:
  Auth::login($user)
        ↓
  Laravel → unique Session ID তৈরি করে
        ↓
  Session ID → browser এ encrypted cookie হিসেবে পাঠায়
        ↓
  Session data (user_id) → storage/framework/sessions/ এ save করে

পরের request এ:
  Browser → cookie তে Session ID পাঠায়
        ↓
  Laravel → Session ID দিয়ে session file খোঁজে
        ↓
  user_id পায় → users table থেকে user load করে
        ↓
  Auth::user() কাজ করে
```

### Session Config (.env)

```env
SESSION_DRIVER=file     # session file এ store হয়
SESSION_LIFETIME=120    # 120 minutes inactivity timeout
```

---

## Middleware

| Middleware | কে access করতে পারে | না পারলে কোথায় যায় |
|---|---|---|
| `guest` | Logged out user | `/dashboard` |
| `auth` | Logged in user | `/login` |

---

## User Model Fillable

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'google_id',
    'avatar',
    'access_token',
    'refresh_token',
];
```

---

## Key Concepts

### OAuth 2.0 vs Session

| | OAuth 2.0 | Session |
|---|---|---|
| উদ্দেশ্য | Google থেকে user info নেওয়া | Laravel এ login state রাখা |
| কোথায় থাকে | Google server | Server এর file/DB |
| কতক্ষণ | Token expiry পর্যন্ত | SESSION_LIFETIME পর্যন্ত |

### Token কখন দরকার

| কাজ | Token লাগে? |
|---|---|
| Google দিয়ে login | না — শুধু একবার |
| Google Calendar API | হ্যাঁ |
| Google Drive API | হ্যাঁ |
| Gmail API | হ্যাঁ |

---

## Commands Reference

```bash
# Install Socialite
composer require laravel/socialite

# Migration run
php artisan migrate

# Cache clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Autoload refresh
composer dump-autoload

# Local server
php artisan serve
```

---

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── AuthController.php      # Email/Password auth
│       └── GoogleController.php   # Google OAuth
├── Models/
│   └── User.php                   # User model with fillable

config/
└── services.php                   # Google credentials config

database/
└── migrations/
    ├── create_users_table.php
    ├── add_google_fields_to_users_table.php
    └── add_tokens_to_users_table.php

resources/views/
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
└── dashboard.blade.php

routes/
└── web.php                        # All auth routes
```