<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Hand-written authentication (see the "Authentication" lesson).
 * Starter kits generate this for you; here you can read every line.
 */
class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('demo.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create($data);            // the 'hashed' cast hashes the password
        Auth::login($user);
        $request->session()->regenerate();

        return to_route('demo.account')->with('status', "Welcome, {$user->name}! Your account is ready.");
    }

    public function showLogin(): View
    {
        return view('demo.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();   // prevent session fixation

        return redirect()->intended(route('demo.account'))->with('status', 'Logged in.');
    }

    public function account(Request $request): View
    {
        return view('demo.auth.account', ['user' => $request->user()]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('demo.login')->with('status', 'You have been logged out.');
    }
}
