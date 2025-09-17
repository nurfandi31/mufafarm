<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pembelian::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.pembelian.index', ['title' => 'Pembelian Barang']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Pembelian Barang';

        return view('app.pembelian.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'tanggal',
            'jenis',
            'nama_barang',
            'jumlah',
            'harga_satuan',
            'total',
            'supplier',
        ]);
        $rules = [
            'tanggal'       => 'required',
            'jenis'         => 'required',
            'nama_barang'   => 'required',
            'jumlah'        => 'required',
            'harga_satuan'  => 'required',
            'total'         => 'required',
            'supplier'      => 'nullable',
        ];

        $harga_satuan = str_replace(['.', ','], '', $request->harga_satuan);
        $total = str_replace(['.', ','], '', $request->total);

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $pembelian = Pembelian::create([
            'tanggal'       => $data['tanggal'],
            'jenis'         => $data['jenis'],
            'nama_barang'   => $data['nama_barang'],
            'jumlah'        => $data['jumlah'],
            'harga_satuan'  => $harga_satuan,
            'total'         => $total,
            'supplier'      => $data['supplier'],
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Data pembelian barang berhasil disimpan',
            'data' => $pembelian,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(pembelian $pembelian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pembelian $pembelian)
    {
        $title = 'Edit Pembelian Barang';

        return view('app.pembelian.edit', compact('title', 'pembelian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pembelian $pembelian)
    {
        $data = $request->only([
            'tanggal',
            'jenis',
            'nama_barang',
            'jumlah',
            'harga_satuan',
            'total',
            'supplier',
        ]);
        $rules = [
            'tanggal'       => 'required',
            'jenis'         => 'required',
            'nama_barang'   => 'required',
            'jumlah'        => 'required',
            'harga_satuan'  => 'required',
            'total'         => 'required',
            'supplier'      => 'nullable',
        ];

        $harga_satuan = str_replace(['.', ','], '', $request->harga_satuan);
        $total = str_replace(['.', ','], '', $request->total);

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $pembelian->update([
            'tanggal'       => $data['tanggal'],
            'jenis'         => $data['jenis'],
            'nama_barang'   => $data['nama_barang'],
            'jumlah'        => $data['jumlah'],
            'harga_satuan'  => $harga_satuan,
            'total'         => $total,
            'supplier'      => $data['supplier'],
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Data pembelian barang berhasil diupdate',
            'data' => $pembelian,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pembelian $pembelian)
    {
        $pembelian->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pembelian barang berhasil dihapus',
        ], Response::HTTP_OK);
    }
}
