<?php

namespace App\Http\Controllers;

use App\Models\Panen;
use App\Models\Bibit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class PanenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Panen::with('bibit.kolam')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jumlah_raw', function ($row) {
                    return $row->jumlah;
                })
                ->editColumn('jumlah', function ($row) {
                    return $row->jumlah . ' ekor';
                })
                ->addColumn('berat_raw', function ($row) {
                    return $row->berat_total;
                })
                ->editColumn('berat_total', function ($row) {
                    return $row->berat_total . ' ' . ($row->bibit->kolam->kapasitas_bibit ?? '-');
                })

                ->make(true);
        }

        return view('app.panen.index', ['title' => 'Data Panen']);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Data Panen';
        $panen = Panen::get();
        $bibit = Bibit::with('kolam')->get();
        return view('app.panen.create', compact('title', 'panen', 'bibit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'bibit_id',
            'jumlah',
            'berat_total',
            'tanggal_panen',
        ]);
        $rules = [
            'bibit_id'      => 'required',
            'jumlah'        => 'required',
            'berat_total'   => 'required',
            'tanggal_panen' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $panen = Panen::create([
            'bibit_id'      => $request->bibit_id,
            'jumlah'        => $request->jumlah,
            'berat_total'   => $request->berat_total,
            'tanggal_panen' => $request->tanggal_panen,
            'status'        => 'ready',
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Data panen berhasil ditambahkan',
            'data'    => $panen
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(panen $panen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(panen $panen)
    {
        $title = 'Edit Data Panen';
        $bibit = Bibit::with('kolam')->get();
        return view('app.panen.edit', compact('title', 'panen', 'bibit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, panen $panen)
    {
        $data = $request->only([
            'bibit_id',
            'jumlah',
            'berat_total',
            'tanggal_panen',
        ]);
        $rules = [
            'bibit_id'      => 'required',
            'jumlah'        => 'required',
            'berat_total'   => 'required',
            'tanggal_panen' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $panen->update([
            'bibit_id'      => $request->bibit_id,
            'jumlah'        => $request->jumlah,
            'berat_total'   => $request->berat_total,
            'tanggal_panen' => $request->tanggal_panen,
            'status'        => 'ready',
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Data panen berhasil diupdate',
            'data'    => $panen
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(panen $panen)
    {
        $panen->delete();

        return response()->json([
            'success' => true,
            'msg' => 'Data panen berhasil dihapus',
        ], Response::HTTP_OK);
    }
}
