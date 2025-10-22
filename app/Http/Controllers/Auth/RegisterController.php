<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','string','email','max:255','unique:users,email'],
            'password'              => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'user', // default role
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // ✅ Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.causes.index');
        }
        return redirect()->route('home');
    }

}
