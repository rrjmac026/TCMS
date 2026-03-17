<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('tenants.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return match ($user->role) {
                'admin'   => redirect()->intended(route('admin.dashboard')),
                'trainer' => redirect()->intended(route('trainer.dashboard')),
                'trainee' => redirect()->intended(route('trainee.dashboard')),
                default   => redirect()->intended('/'),
            };
        }

        return back()->withInput($request->only('email'))
                     ->withErrors([
                         'email' => 'The provided credentials do not match our records.',
                     ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}