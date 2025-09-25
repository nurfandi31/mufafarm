<?php

namespace App\Http\Controllers;

use App\Models\Kuliner;
use App\Models\Panen;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class KulinerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kuliner::with('panen.bibit.kolam')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kode', fn ($row) => strtoupper(substr($row->nama, 0, 1)) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT))
                ->addColumn('packing', function ($row) {
                    $packingArray = json_decode($row->packing, true);
                    if (is_array($packingArray) && count($packingArray) > 0) {
                        $packings = array_map(fn ($p) => $p['satuan'] . ' ' . $p['jenis'], $packingArray);
                        return implode(', ', $packings);
                    }
                    return '-';
                })
                ->make(true);
        }

        return view('app.kuliner.index', ['title' => 'Level Jabatan']);
    }

    public function list($id)
    {
        $panen = Panen::with('bibit.kolam')->find($id);

        if (!$panen) {
            return response()->json([
                'success' => false,
                'msg' => 'Data panen tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $panen
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "Tambah Kuliner";
        $panen = Panen::with('bibit.kolam')->get();
        return view('app.kuliner.create', compact('title', 'panen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'panen_id',
            'nama',
            'tanggal_produksi',
            'tanggal_kadaluarsa',
            'packing',
            'jenis',
            'jumlah',
            'biaya_produksi',
            'keterangan'
        ]);

        $rules = [
            'panen_id'          => 'required|exists:panens,id',
            'nama'              => 'required',
            'tanggal_produksi'  => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_produksi',
            'packing'           => 'required',
            'jenis'             => 'required',
            'jumlah'            => 'required|numeric|min:0.01',
            'biaya_produksi'    => 'required',
            'keterangan'        => 'nullable',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $panen = Panen::find($request->panen_id);

            $jumlahKg = (float) $request->jumlah;
            $jumlahEkor = floor(($jumlahKg / $panen->berat_total) * $panen->jumlah);

            if ($jumlahKg > $panen->berat_total || $jumlahEkor > $panen->jumlah) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Stok panen tidak mencukupi.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $packing = json_encode([
                [
                    'satuan' => $request->packing,
                    'jenis'  => $request->jenis,
                ]
            ]);

            $biaya_produksi = str_replace(['.', ','], '', $request->biaya_produksi);

            $kuliner = Kuliner::create([
                'panen_id'          => $request->panen_id,
                'nama'              => $request->nama,
                'tanggal_produksi'  => $request->tanggal_produksi,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'packing'           => $packing,
                'jumlah'            => $jumlahKg,
                'biaya_produksi'    => $biaya_produksi,
                'status'            => 'ready',
                'keterangan'        => $request->keterangan,
            ]);

            $panen->berat_total -= $jumlahKg;
            $panen->jumlah      -= $jumlahEkor;
            $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
            $panen->save();

            DB::commit();

            return response()->json([
                'success'       => true,
                'msg'           => 'Data penjualan berhasil disimpan.',
                'kuliner'       => $kuliner,
                'panen'         => $panen,
                'status_panen'  => $panen->status
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Kuliner $kuliner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kuliner $kuliner)
    {
        $title = "Edit Kuliner";
        $panen = Panen::with('bibit.kolam')->get();
        return view('app.kuliner.edit', compact('title', 'kuliner', 'panen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kuliner $kuliner)
    {
        $data = $request->only([
            'panen_id',
            'nama',
            'tanggal_produksi',
            'tanggal_kadaluarsa',
            'packing',
            'jenis',
            'jumlah',
            'biaya_produksi',
            'keterangan'
        ]);

        $rules = [
            'panen_id'           => 'required|exists:panens,id',
            'nama'               => 'required|string|max:255',
            'tanggal_produksi'   => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_produksi',
            'packing'            => 'required|string',
            'jenis'              => 'required|string',
            'jumlah'             => 'required|numeric|min:0.01',
            'biaya_produksi'     => 'required|numeric',
            'keterangan'         => 'nullable|string',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $panen = Panen::findOrFail($request->panen_id);

            $jumlahBaruKg = (float) $request->jumlah;
            $jumlahLamaKg = $kuliner->jumlah;

            if ($panen->jumlah <= 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Stok panen habis.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $rasio = $panen->berat_total / $panen->jumlah;

            $jumlahEkorLama = round($jumlahLamaKg / $rasio);
            $panen->berat_total += $jumlahLamaKg;
            $panen->jumlah      += $jumlahEkorLama;

            $jumlahBaruEkor = round($jumlahBaruKg / $rasio);

            if ($jumlahBaruKg > $panen->berat_total || $jumlahBaruEkor > $panen->jumlah) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Stok panen tidak mencukupi.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $packing = json_encode([[
                'satuan' => $request->packing,
                'jenis'  => $request->jenis
            ]]);

            $biaya_produksi = str_replace(['.', ','], '', $request->biaya_produksi);

            $kuliner->update([
                'panen_id'           => $request->panen_id,
                'nama'               => $request->nama,
                'tanggal_produksi'   => $request->tanggal_produksi,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'packing'            => $packing,
                'jumlah'             => $jumlahBaruKg,
                'biaya_produksi'     => $biaya_produksi,
                'keterangan'         => $request->keterangan,
            ]);

            $panen->berat_total -= $jumlahBaruKg;
            $panen->jumlah      -= $jumlahBaruEkor;
            $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
            $panen->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Data penjualan berhasil diperbarui.',
                'kuliner' => $kuliner,
                'panen' => $panen,
                'status_panen' => $panen->status
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kuliner $kuliner)
    {
        $kuliner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data penjualan berhasil dihapus.',
            'kuliner' => $kuliner
        ]);
    }
}
