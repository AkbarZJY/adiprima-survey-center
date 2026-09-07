<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];

        // Determine if login is email, username, or NIK
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt login with username or email
        $attempt = Auth::attempt([$fieldType => $loginInput, 'password' => $password], $request->boolean('remember'));

        if (!$attempt) {
            // Fallback attempt with NIK
            $attempt = Auth::attempt(['nik' => $loginInput, 'password' => $password], $request->boolean('remember'));
        }

        if ($attempt) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('home'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'login' => 'Username, NIK, atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
