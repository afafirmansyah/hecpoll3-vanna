<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $login = $request->username;
        $password = $request->password;

        // Check for admin user with username
        if ($login === 'afafirmansyah') {
            $user = User::where('email', 'fauzi@hectronic.in')->first();
            if ($user && Hash::check($password, $user->password) && $user->is_active) {
                Auth::login($user);
                $user->update(['last_login_at' => now()]);
                return redirect()->route('dashboard');
            }
        }

        // Check by email or username
        $user = User::where(function($query) use ($login) {
                    $query->where('email', $login)
                          ->orWhere('username', $login);
                })
                ->where('is_active', true)
                ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            $user->update(['last_login_at' => now()]);
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->with('error', 'Invalid credentials or account inactive');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}