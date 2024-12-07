<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login() {
        return view("page.auth.login");
    }

    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
        
        // Cek kredensial pengguna
        if (Auth::attempt($credentials)) {
            // Login sukses
            return redirect("simple-additive-weighting");
        }
        
        // Login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->withInput();
        
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Mengeluarkan user dari sesi

        // Mengalihkan pengguna ke halaman login atau halaman lain setelah logout
        return redirect('/auth/login')->with('status', 'Anda telah berhasil logout.');
    }
}
