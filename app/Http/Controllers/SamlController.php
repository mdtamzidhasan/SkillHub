<?php

namespace App\Http\Controllers;

use Aacotroneo\Saml2\Saml2Auth as SamlAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SamlController extends Controller
{
    protected $samlAuth;

    public function __construct(SamlAuth $samlAuth)
    {
        $this->samlAuth = $samlAuth;
    }

    // Okta এ redirect করো
    public function login()
    {
        return redirect($this->samlAuth->login());
    }

    // Okta থেকে Assertion receive করো
    public function acs()
    {
        $this->samlAuth->processSamlResponse();

        $errors = $this->samlAuth->getErrors();
        if (!empty($errors)) {
            return redirect('/login')->with('error', 'SAML login failed: ' . implode(', ', $errors));
        }

        $samlUser = $this->samlAuth->getSaml2User();

        $email = $samlUser->getUserId();
        $attrs = $samlUser->getAttributes();
        $name  = ($attrs['firstName'][0] ?? '') . ' ' . ($attrs['lastName'][0] ?? '');

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name'  => trim($name), 'password' => null]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }

    // SP Metadata XML
    public function metadata()
    {
        $metadata = $this->samlAuth->getMetadata();
        return response($metadata, 200, [
            'Content-Type' => 'text/xml'
        ]);
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect($this->samlAuth->logout());
    }
}