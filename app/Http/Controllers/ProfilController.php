<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $title  = 'Profil';
        $profil = Profil::first();
        $user   = User::with('level')->find(auth()->user()->id);

        return view('app.profil.index', compact('title', 'user', 'profil'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(profil $profil)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(profil $profil)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $formType = $request->input('form_type');

        if ($formType === 'user') {
            $request->validate([
                'username' => 'required|string|max:50',
                'password' => 'nullable|string|min:6|confirmed',
                'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user = User::findOrFail($id);
            $user->username = $request->username;
            if ($request->password) $user->password = bcrypt($request->password);

            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('foto')->store('foto', 'public');
            }

            $user->save();
            return response()->json(['success' => true, 'msg' => 'User berhasil diperbarui!']);
        }

        if ($formType === 'profil') {
            $request->validate([
                'kode' => 'required',
                'nama' => 'required',
                'nama_mitra' => 'required',
                'alamat' => 'required',
                'telpon' => 'required',
                'penanggung_jawab' => 'required',
            ]);

            $profil = Profil::findOrFail($id);
            $profil->kode = $request->kode;
            $profil->nama = $request->nama;
            $profil->nama_mitra = $request->nama_mitra;
            $profil->alamat = $request->alamat;
            $profil->telpon = $request->telpon;
            $profil->penanggung_jawab = $request->penanggung_jawab;
            $profil->save();

            return response()->json(['success' => true, 'msg' => 'Profil berhasil diperbarui!']);
        }

        return response()->json(['success' => false, 'msg' => 'Form tidak dikenali']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(profil $profil)
    {
        //
    }
}
