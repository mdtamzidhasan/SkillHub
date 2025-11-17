<?php
namespace App\Services;
use App\Models\Service;
use App\Repositories\UserRepositories;
use Illuminate\Support\Facades\Auth;


class AuthService{

    protected $users;

    public function __construct(UserRepositories $users) {
        $this->users = $users;
    
    }

    public function register(array $data) {
        $user = $this->users->createUser($data);
        /** @var \App\Models\User $user */
        $token = $user->createToken('api-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }
   public function login(string $email, string $password) {
       if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $token = $user->createToken('api-token')->plainTextToken;
        return ['user'=>$user, 'token'=>$token];
    }

    public function logout($user) {
        $user->tokens()->delete();
    }
}