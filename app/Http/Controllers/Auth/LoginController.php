<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // ✅ Block banned users immediately after login succeeds
            $user = Auth::user();
            if ($user && $user->is_banned) {
                $reason = $user->ban_reason ?: 'Votre compte est suspendu.'; // or English if you prefer
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => $reason])
                    ->onlyInput('email');
            }

            // ✅ Then continue with your existing redirects
            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.causes.index');
            }
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Identifiants invalides.',
        ])->onlyInput('email');
    }



    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // change to your home route
    }
}
