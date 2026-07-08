<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {

            // Regenerate လုပ် ( Hacker )
            $request->session()->regenerate();

            // Role အလိုက်
            if (Auth::user()->isAdmin() || Auth::user()->isManager()) {
                return redirect()->intended('/products');
            }
            // Cashier ဆို
            return redirect()->intended('/pos');
        }

       // Error
        return back()->withErrors([
            'email' => __('messages.auth_failed'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
