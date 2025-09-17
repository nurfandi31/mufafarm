<?php

namespace App\Http\Controllers;

use App\Models\Pakan;
use App\Models\PemberianPakan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class PakanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pakan::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.pakan.index', ['title' => 'Pakan']);
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
            'stok',
            'satuan',
            'harga',
        ]);
        $rules = [
            'nama'      => 'required',
            'stok'      => 'required',
            'satuan'    => 'required',
            'harga'     => 'required',
        ];

        $harga = str_replace(['.', ','], '', $request->harga);

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        };

        $pakan = Pakan::create([
            'nama' => $request->nama,
            'stok' => $request->stok,
            'satuan' => $request->satuan,
            'harga' => $harga,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Data pakan berhasil ditambahkan',
            'data' => $pakan
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(pakan $pakan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pakan $pakan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pakan $pakan)
    {
        $data = $request->only([
            'nama',
            'stok',
            'satuan',
            'harga',
        ]);
        $rules = [
            'nama'      => 'required',
            'stok'      => 'required',
            'satuan'    => 'required',
            'harga'     => 'required',
        ];

        $harga = str_replace(['.', ','], '', $request->harga);

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        };

        $pakan->update([
            'nama' => $request->nama,
            'stok' => $request->stok,
            'satuan' => $request->satuan,
            'harga' => $harga,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Data pakan berhasil diubah',
            'data' => $pakan
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pakan $pakan)
    {
        $usedInPemberian = PemberianPakan::where('pakan_id', $pakan->id)->exists();
        if ($usedInPemberian) {
            return response()->json([
                'success' => false,
                'message' => 'Data pakan tidak dapat dihapus karena sedang digunakan di pemberian pakan',
            ], Response::HTTP_BAD_REQUEST);
        }

        $pakan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data pakan berhasil dihapus',
        ], Response::HTTP_OK);
    }
}
