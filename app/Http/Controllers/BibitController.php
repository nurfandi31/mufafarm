<?php

namespace App\Http\Controllers;

use App\Models\Bibit;
use App\Models\Kolam;
use App\Models\PemberianPakan;
use App\Models\Panen;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class BibitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Bibit::with('kolam')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.bibit.index', ['title' => 'Bibit']);
    }

    public function list(Request $request)
    {
        $search = $request->get('q');

        $query = Kolam::select('id', 'nama', 'kapasitas_bibit');
        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        return response()->json(
            $query->get()->map(fn ($item) => [
                'id'            => $item->id,
                'nama'          => $item->nama,
                'kapasitas_bibit'     => $item->kapasitas_bibit
            ])
        );
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
            'kolam_id',
            'nama',
            'jenis',
            'tanggal_datang',
            'jumlah',
            'sumber',
        ]);
        $rules = [
            'kolam_id'          => 'required|exists:kolams,id',
            'nama'              => 'required|string|max:100',
            'jenis'             => 'required|string|max:100',
            'tanggal_datang'    => 'required|date',
            'jumlah'            => 'required|integer|min:1',
            'sumber'            => 'nullable|string|max:255',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bibit = Bibit::create([
            'kolam_id'          => $data['kolam_id'],
            'nama'              => $data['nama'],
            'jenis'             => $data['jenis'],
            'tanggal_datang'    => $data['tanggal_datang'],
            'jumlah'            => $data['jumlah'],
            'sumber'            => $data['sumber'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Bibit berhasil ditambahkan',
            'data' => $bibit
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(bibit $bibit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(bibit $bibit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, bibit $bibit)
    {
        $data = $request->only([
            'kolam_id',
            'nama',
            'jenis',
            'tanggal_datang',
            'jumlah',
            'sumber',
        ]);
        $rules = [
            'kolam_id'          => 'required|exists:kolams,id',
            'nama'              => 'required|string|max:100',
            'jenis'             => 'required|string|max:100',
            'tanggal_datang'    => 'required|date',
            'jumlah'            => 'required|integer|min:1',
            'sumber'            => 'nullable|string|max:255',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $bibit->update([
            'kolam_id'          => $data['kolam_id'],
            'nama'              => $data['nama'],
            'jenis'             => $data['jenis'],
            'tanggal_datang'    => $data['tanggal_datang'],
            'jumlah'            => $data['jumlah'],
            'sumber'            => $data['sumber'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Bibit berhasil diperbarui',
            'data' => $bibit
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(bibit $bibit)
    {
        $pakanCount = PemberianPakan::where('bibit_id', $bibit->id)->count();
        $panenCount = Panen::where('bibit_id', $bibit->id)->count();

        if ($pakanCount > 0 || $panenCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bibit tidak dapat dihapus karena masih memiliki pemberian pakan atau panen',
            ]);
        }

        $bibit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bibit berhasil dihapus',
        ]);
    }
}
