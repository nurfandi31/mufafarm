<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\user;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Level::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kode', fn ($row) => strtoupper(substr($row->nama, 0, 1)) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT))
                ->make(true);
        }

        return view('app.level.index', ['title' => 'Level Jabatan']);
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
        $data = $request->only([
            'nama',
        ]);

        $rules = [
            'nama' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $level = Level::create([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Level berhasil ditambahkan',
            'data' => $level,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        $data = $request->only(['nama']);

        $rules = [
            'nama' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $level->update([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Level berhasil diupdate',
            'data' => $level,
        ], Response::HTTP_OK);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        if (User::where('level_id', $level->id)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak bisa dihapus karena sudah dipakai di data Karyawan.'
            ], 400);
        }

        $level->delete();

        return response()->json([
            'success' => true,
            'message' => 'Level berhasil dihapus',
        ], Response::HTTP_OK);
    }
}
