<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kuliner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function auth(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {

                // Panggil fungsi update kuliner kadaluarsa
                $this->updateKulinerKadaluarsa();

                return redirect('/app/dashboard')->with('success', 'Selamat datang ' . $user->nama_lengkap);
            }
        }

        return redirect('/auth')->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        Auth::logout();

        session()->flush();
        session()->regenerate();

        return redirect('/auth')->with('success', 'Anda telah berhasil keluar');
    }

    /**
     * Update status kuliner yang kadaluarsa
     */
    private function updateKulinerKadaluarsa()
    {
        $today = Carbon::today();

        Kuliner::whereDate('tanggal_kadaluarsa', '<', $today)
            ->where('status', 'ready')
            ->update(['status' => 'tidak_layak']);
    }
}
