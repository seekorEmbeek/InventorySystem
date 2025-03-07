<?php

namespace App\Http\Controllers;

use App\Models\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan file ini ada di resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (FacadesAuth::attempt($credentials)) {
            if ($request->email != 'admin@123.com')
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            else
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(){
        //  clear session dan memberitahu auth dengan status logout
            Session::flush();
            FacadesAuth::logout();

            return Redirect('login');
        }
}
