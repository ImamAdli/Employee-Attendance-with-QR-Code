<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('absen.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is active
            if ($user->status != 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda belum diaktifkan. Mohon tunggu konfirmasi dari admin.',
                ]);
            }
            
            // Redirect based on role
            if ($user->role == 'admin') {
                return redirect()->intended(route('absen.dashboard'));
            } else {
                return redirect()->intended(route('siswa.dashboard'));
            }
        }
        
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->except('password'));
    }

    public function showRegistrationForm()
    {
        return view('absen.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
            'no_hp' => 'required|string|max:15',
            'asal_sekolah' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'institution' => $request->institution,
            'role' => 'siswa', // Default role for registration
            'status' => 'pending', // All new registrations need approval
        ]);

        // Create siswa record
        $user->siswa()->create([
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'asal_sekolah' => $request->asal_sekolah,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'foto_profil' => $request->hasFile('foto_profil') ? $request->file('foto_profil')->store('foto_profil', 'public') : null,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan tunggu konfirmasi dari admin untuk dapat login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
