<?php

namespace App\Http\Controllers;

use App\Models\Kolam;
use App\Models\Bibit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class KolamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kolam::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.kolam.index', ['title' => 'Kolam']);
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
            'type',
            'kapasitas_bibit',
            'lokasi_kolam',
        ]);
        $rules = [
            'nama'              => 'required',
            'type'              => 'required',
            'kapasitas_bibit'   => 'required',
            'lokasi_kolam'      => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $kolam = Kolam::create([
            'nama'              => $request->nama,
            'type'              => $request->type,
            'kapasitas_bibit'   => $request->kapasitas_bibit,
            'lokasi_kolam'      => $request->lokasi_kolam,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Kolam berhasil ditambahkan',
            'data' => $kolam,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(kolam $kolam)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(kolam $kolam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, kolam $kolam)
    {
        $data = $request->only([
            'nama',
            'type',
            'kapasitas_bibit',
            'lokasi_kolam',
        ]);
        $rules = [
            'nama'              => 'required',
            'type'              => 'required',
            'kapasitas_bibit'   => 'required',
            'lokasi_kolam'      => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $kolam->update([
            'nama'              => $request->nama,
            'type'              => $request->type,
            'kapasitas_bibit'   => $request->kapasitas_bibit,
            'lokasi_kolam'      => $request->lokasi_kolam,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Kolam berhasil diubah',
            'data' => $kolam,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(kolam $kolam)
    {
        if (Bibit::where('kolam_id', $kolam->id)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak bisa dihapus karena sudah dipakai di data Bibit.'
            ], 400);
        }
        $kolam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kolam berhasil dihapus',
        ], Response::HTTP_OK);
    }
}
