<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Bibit;
use App\Models\Panen;
use App\Models\Settings;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Penjualan::with(
                'panen.bibit',
                'panen.bibit.kolam',
            )->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.penjualan.index', ['title' => 'Penjualan Barang']);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $panen = Panen::with('bibit')->get();
        $settings = Settings::first();

        $title = 'Tambah Penjualan Barang ';

        return view('app.penjualan.create', compact('title', 'panen', 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $data = $request->only([
            'tanggal',
            'panen_id',
            'pembeli',
            'jumlah',
            'jumlah_ekor',
            'harga_satuan',
            'total',
        ]);

        $rules = [
            'tanggal'       => 'required',
            'panen_id'      => 'required',
            'pembeli'       => 'nullable|string|max:255',
            'jumlah'        => 'required|numeric|min:0.1',
            'jumlah_ekor'   => 'required|numeric|min:1',
            'harga_satuan'  => 'required',
            'total'         => 'required',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hargaSatuan  = (float) str_replace(['.', ','], '', $request->harga_satuan);
        $total        = (float) str_replace(['.', ','], '', $request->total);
        $jumlah       = (float) $request->jumlah;
        $jumlahEkor   = (int) $request->jumlah_ekor;

        DB::beginTransaction();
        try {
            $panen = Panen::find($request->panen_id);
            if (!$panen) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Data panen tidak ditemukan.'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($jumlah > (float) $panen->berat_total) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Stok panen tidak mencukupi.'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($jumlahEkor > (int) $panen->jumlah) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Stok ekor tidak mencukupi.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $penjualan = Penjualan::create([
                'tanggal'       => $request->tanggal,
                'panen_id'      => $request->panen_id,
                'pembeli'       => $request->pembeli,
                'jumlah'        => $jumlah,
                'jumlah_ekor'   => $jumlahEkor,
                'harga_satuan'  => $hargaSatuan,
                'total'         => $total,
            ]);

            // Kurangi stok
            $panen->berat_total -= $jumlah;
            $panen->jumlah      -= $jumlahEkor;

            // Update status
            $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
            $panen->save();

            DB::commit();

            return response()->json([
                'success'       => true,
                'msg'           => 'Data penjualan berhasil disimpan.',
                'penjualan'     => $penjualan,
                'panen'         => $panen,
                'status_panen'  => $panen->status
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPanen($id)
    {
        $panen = Panen::with('bibit')->find($id);

        if (!$panen) {
            return response()->json([
                'success' => false,
                'msg' => 'Data panen tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'jumlah' => (int) $panen->jumlah,           // total ekor
                'berat_total' => (float) $panen->berat_total // total kg
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(penjualan $penjualan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(penjualan $penjualan)
    {
        $title = 'Edit Penjualan Barang ';
        $panen = Panen::with('bibit')->get();
        $settings = Settings::first();
        return view('app.penjualan.edit', compact('title', 'penjualan', 'panen', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Penjualan $penjualan)
    {
        $data = $request->only([
            'tanggal', 'panen_id', 'pembeli', 'jumlah', 'jumlah_ekor', 'harga_satuan', 'total',
        ]);

        $rules = [
            'tanggal'       => 'required|date',
            'panen_id'      => 'required|exists:panens,id',
            'pembeli'       => 'nullable|string|max:255',
            'jumlah'        => 'required|numeric|min:0.1',
            'jumlah_ekor'   => 'required|numeric|min:1',
            'harga_satuan'  => 'required',
            'total'         => 'required',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Bersihkan format ribuan -> angka murni
        $hargaSatuan  = (float) str_replace(['.', ','], '', $request->harga_satuan);
        $total        = (float) str_replace(['.', ','], '', $request->total);
        $jumlah       = (float) $request->jumlah;
        $jumlahEkor   = (int) $request->jumlah_ekor;

        DB::beginTransaction();
        try {
            $panen = Panen::find($request->panen_id);
            if (!$panen) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'msg' => 'Data panen tidak ditemukan.'
                ], Response::HTTP_NOT_FOUND);
            }

            $oldPanen = Panen::find($penjualan->panen_id);

            // Rollback stok lama
            if ($oldPanen) {
                if ($oldPanen->id === $panen->id) {
                    // Kasus edit di panen yang sama → rollback ke $panen langsung
                    $panen->berat_total += (float) $penjualan->jumlah;
                    $panen->jumlah      += (int) $penjualan->jumlah_ekor;
                } else {
                    // Kasus pindah panen → kembalikan ke oldPanen, lalu simpan
                    $oldPanen->berat_total += (float) $penjualan->jumlah;
                    $oldPanen->jumlah      += (int) $penjualan->jumlah_ekor;
                    $oldPanen->status = ($oldPanen->berat_total <= 0 || $oldPanen->jumlah <= 0) ? 'habis' : 'ready';
                    $oldPanen->save();
                }
            }

            // Validasi stok panen baru setelah rollback
            if ($jumlah > $panen->berat_total) {
                DB::rollBack();
                return response()->json(['success' => false, 'msg' => 'Stok panen tidak mencukupi.'], Response::HTTP_BAD_REQUEST);
            }
            if ($jumlahEkor > $panen->jumlah) {
                DB::rollBack();
                return response()->json(['success' => false, 'msg' => 'Stok ekor tidak mencukupi.'], Response::HTTP_BAD_REQUEST);
            }

            // Kurangi stok panen baru
            $panen->berat_total -= $jumlah;
            $panen->jumlah      -= $jumlahEkor;

            if ($panen->berat_total < 0) $panen->berat_total = 0;
            if ($panen->jumlah < 0) $panen->jumlah = 0;

            $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
            $panen->save();

            // Update data penjualan
            $penjualan->update([
                'tanggal'       => $request->tanggal,
                'panen_id'      => $request->panen_id,
                'pembeli'       => $request->pembeli,
                'jumlah'        => $jumlah,
                'jumlah_ekor'   => $jumlahEkor,
                'harga_satuan'  => $hargaSatuan,
                'total'         => $total,
            ]);

            DB::commit();

            return response()->json([
                'success'   => true,
                'msg'       => 'Data penjualan berhasil diperbarui.',
                'penjualan' => $penjualan,
                'panen'     => $panen
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
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
    public function destroy(penjualan $penjualan)
    {
        $panen = Panen::find($penjualan->panen_id);
        if ($panen) {
            $panen->berat_total = (float) $panen->berat_total + (float) $penjualan->jumlah;
            $panen->jumlah      = (int) $panen->jumlah + (int) $penjualan->jumlah_ekor;
            $panen->save();
        }

        $penjualan->delete();

        return response()->json([
            'success' => true,
            'msg' => 'Data penjualan berhasil dihapus.'
        ], Response::HTTP_OK);
    }
}
