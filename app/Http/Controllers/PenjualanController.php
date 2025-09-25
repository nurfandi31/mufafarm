<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Profil;
use App\Models\Kuliner;
use App\Models\Detailpenjualan;
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
                'items.kuliner',
                'items.panen.bibit.kolam',
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
        $panen = Panen::with('bibit.kolam')->get();
        $Kuliner = Kuliner::with('panen.bibit.kolam')->get();
        $settings = Settings::first();

        $title = 'Tambah Penjualan Barang ';

        return view('app.penjualan.create', compact('title', 'panen', 'Kuliner', 'settings'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function cetak_struk($id)
    {
        $title = 'Cetak Struk Penjualan';
        $profil = Profil::first();
        $penjualan = Penjualan::with(
            'items.kuliner',
            'items.panen.bibit.kolam',
            'panen.bibit.kolam',
        )->findOrFail($id);

        $total = $penjualan->items->sum('total');

        return view('app.penjualan.cetak_struk', compact('title', 'total', 'profil', 'penjualan'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['tanggal', 'pembeli', 'pelaksana']);

        $rules = [
            'tanggal'   => 'required|date',
            'pembeli'   => 'required|string',
            'pelaksana' => 'required|array|min:1',
            'pelaksana.*.item_id' => 'required|string',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'request' => $request->all()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validasi tiap item harus lengkap
        foreach ($data['pelaksana'] as $i => $item) {
            list($type, $id) = explode('-', $item['item_id']);

            if ($type === 'panen') {
                $requiredFields = ['jumlah_panen', 'harga_satuan_panen', 'jumlah_satuan_panen', 'total_panen'];
            } elseif ($type === 'kuliner') {
                $requiredFields = ['jumlah_kuliner', 'harga_satuan_kuliner', 'total_kuliner'];
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => "Tipe item tidak dikenal pada baris ke-$i"
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            foreach ($requiredFields as $field) {
                if (!isset($item[$field]) || $item[$field] === '' || $item[$field] === null) {
                    return response()->json([
                        'success' => false,
                        'msg' => "Field '$field' wajib diisi untuk item $item[item_id] pada baris ke-$i"
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
        }

        DB::beginTransaction();
        try {
            $kode_penjualan = 'PJ-' . date('YmdHis');

            // Simpan penjualan utama
            $penjualan = Penjualan::create([
                'kode_penjualan' => $kode_penjualan,
                'tanggal'        => $request->tanggal,
                'pembeli'        => $request->pembeli,
            ]);

            foreach ($request->pelaksana as $item) {
                list($type, $id) = explode('-', $item['item_id']);

                if ($type === 'panen') {
                    $jumlah      = (float) $item['jumlah_panen'];
                    $jumlahEkor  = (int) $item['jumlah_satuan_panen'];
                    $hargaSatuan = (float) $item['harga_satuan_panen'];
                    $total       = (float) $item['total_panen'];

                    $panen = Panen::find($id);
                    if (!$panen) throw new \Exception("Panen ID {$id} tidak ditemukan.");
                    if ($jumlah > $panen->berat_total) throw new \Exception("Stok panen tidak mencukupi.");
                    if ($jumlahEkor > $panen->jumlah) throw new \Exception("Stok ekor tidak mencukupi.");

                    DetailPenjualan::create([
                        'penjualan_id'  => $penjualan->id,
                        'item_type'     => 'panen',
                        'item_id'       => $id,
                        'jumlah'        => $jumlah,
                        'jumlah_satuan' => $jumlahEkor,
                        'harga_satuan'  => $hargaSatuan,
                        'total'         => $total,
                    ]);

                    $panen->berat_total -= $jumlah;
                    $panen->jumlah      -= $jumlahEkor;
                    $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
                    $panen->save();
                }

                if ($type === 'kuliner') {
                    $jumlah      = (int) $item['jumlah_kuliner'];
                    $hargaSatuan = (float) $item['harga_satuan_kuliner'];
                    $total       = (float) $item['total_kuliner'];

                    $kuliner = Kuliner::find($id);
                    if (!$kuliner) throw new \Exception("Kuliner ID {$id} tidak ditemukan.");

                    $packing = is_string($kuliner->packing) ? json_decode($kuliner->packing, true) : $kuliner->packing;
                    if (empty($packing)) throw new \Exception("Data packing kuliner tidak valid.");

                    $satuanNow = (int) $packing[0]['satuan'];
                    if ($jumlah > $satuanNow) throw new \Exception("Stok kuliner tidak mencukupi.");

                    $packing[0]['satuan'] = (string) ($satuanNow - $jumlah);
                    $kuliner->packing = json_encode($packing);
                    $kuliner->status = ($packing[0]['satuan'] <= 0) ? 'habis' : 'ready';
                    $kuliner->save();

                    DetailPenjualan::create([
                        'penjualan_id'  => $penjualan->id,
                        'item_type'     => 'kuliner',
                        'item_id'       => $id,
                        'jumlah'        => $jumlah,
                        'jumlah_satuan' => $jumlah,
                        'harga_satuan'  => $hargaSatuan,
                        'total'         => $total,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Data penjualan berhasil disimpan.',
                'kode_penjualan' => $kode_penjualan
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
                'jumlah' => (int) $panen->jumlah,
                'berat_total' => (float) $panen->berat_total
            ]
        ]);
    }

    public function getKuliner($id)
    {
        $kuliner = Kuliner::with('panen')->find($id);

        if (!$kuliner) {
            return response()->json([
                'success' => false,
                'msg' => 'Data kuliner tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'packing' => json_decode($kuliner->packing, true),
                'jumlah'  => (float) $kuliner->jumlah
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
    public function edit(Penjualan $penjualan)
    {
        $title = 'Edit Penjualan Barang';

        $panen = Panen::with('bibit.kolam')->get();
        $Kuliner = Kuliner::with('panen.bibit.kolam')->get();
        $Detailpenjualan = DetailPenjualan::with(
            'kuliner',
            'penjualan',
            'panen.bibit.kolam'
        )->get();
        $settings = Settings::first();

        $penjualan->load('items');

        return view('app.penjualan.edit', compact('title', 'penjualan', 'Detailpenjualan', 'Kuliner', 'panen', 'settings'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Penjualan $penjualan)
    {
        $data = $request->only(['tanggal', 'pembeli', 'pelaksana']);

        $rules = [
            'tanggal'   => 'required|date',
            'pembeli'   => 'required|string',
            'pelaksana' => 'required|array|min:1',
            'pelaksana.*.item_id' => 'required|string',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'request' => $request->all()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        try {
            $existingItems = $penjualan->items()->get();
            $newItemIds = collect($request->pelaksana)->pluck('item_id')->toArray();

            // Hapus item lama yang sudah tidak ada di request baru
            foreach ($existingItems as $oldItem) {
                $key = $oldItem->item_type . '-' . $oldItem->item_id;
                if (!in_array($key, $newItemIds)) {
                    if ($oldItem->item_type === 'panen') {
                        $panen = Panen::find($oldItem->item_id);
                        if ($panen) {
                            $panen->berat_total += $oldItem->jumlah;
                            $panen->jumlah += $oldItem->jumlah_satuan;
                            $batas = json_decode($panen->jumlah_panen_keseluruhan, true)[0] ?? null;
                            if ($batas) {
                                $panen->berat_total = min($panen->berat_total, (float)$batas['berat_total']);
                                $panen->jumlah = min($panen->jumlah, (int)$batas['jumlah']);
                            }
                            $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
                            $panen->save();
                        }
                    }

                    if ($oldItem->item_type === 'kuliner') {
                        $kuliner = Kuliner::find($oldItem->item_id);
                        if ($kuliner) {
                            $packing = json_decode($kuliner->packing, true);
                            if (is_array($packing) && isset($packing[0])) {
                                $packing[0]['satuan'] = (string)((int)$packing[0]['satuan'] + $oldItem->jumlah);
                                $kuliner->packing = json_encode($packing);
                                $kuliner->status = ((int)$packing[0]['satuan'] <= 0) ? 'habis' : 'ready';
                                $kuliner->save();
                            }
                        }
                    }

                    $oldItem->delete();
                }
            }

            // Update atau create item baru
            foreach ($request->pelaksana as $item) {
                list($type, $id) = explode('-', $item['item_id']);

                if ($type === 'panen') {
                    $jumlah = (float)$item['jumlah_panen'];
                    $jumlahEkor = (int)$item['jumlah_satuan_panen'];
                    $hargaSatuan = (float)$item['harga_satuan_panen'];
                    $total = (float)$item['total_panen'];

                    $panen = Panen::find($id);
                    if (!$panen) throw new \Exception("Panen ID {$id} tidak ditemukan.");

                    $existingItem = $penjualan->items()->where('item_type', 'panen')->where('item_id', $id)->first();
                    $oldJumlah = $existingItem->jumlah ?? 0;
                    $oldJumlahEkor = $existingItem->jumlah_satuan ?? 0;

                    $diffJumlah = $oldJumlah - $jumlah;
                    $diffEkor = $oldJumlahEkor - $jumlahEkor;

                    // kembalikan stok jika jumlah baru < lama
                    if ($diffJumlah > 0) $panen->berat_total += $diffJumlah;
                    if ($diffEkor > 0) $panen->jumlah += $diffEkor;

                    // kurangi stok jika jumlah baru > lama
                    if ($diffJumlah < 0) $panen->berat_total += $diffJumlah; // diffJumlah negatif = kurangi
                    if ($diffEkor < 0) $panen->jumlah += $diffEkor;

                    $batas = json_decode($panen->jumlah_panen_keseluruhan, true)[0] ?? null;
                    if ($batas) {
                        $panen->berat_total = min($panen->berat_total, (float)$batas['berat_total']);
                        $panen->jumlah = min($panen->jumlah, (int)$batas['jumlah']);
                    }

                    $panen->status = ($panen->berat_total <= 0 || $panen->jumlah <= 0) ? 'habis' : 'ready';
                    $panen->save();

                    $penjualan->items()->updateOrCreate(
                        ['item_type' => 'panen', 'item_id' => $id],
                        ['jumlah' => $jumlah, 'jumlah_satuan' => $jumlahEkor, 'harga_satuan' => $hargaSatuan, 'total' => $total]
                    );
                }

                if ($type === 'kuliner') {
                    $jumlah = (int)$item['jumlah_kuliner'];
                    $hargaSatuan = (float)$item['harga_satuan_kuliner'];
                    $total = (float)$item['total_kuliner'];

                    $kuliner = Kuliner::find($id);
                    if (!$kuliner) throw new \Exception("Kuliner ID {$id} tidak ditemukan.");

                    $existingItem = $penjualan->items()->where('item_type', 'kuliner')->where('item_id', $id)->first();
                    $oldJumlah = $existingItem->jumlah ?? 0;
                    $diff = $oldJumlah - $jumlah;

                    $packing = json_decode($kuliner->packing, true);
                    if ($diff > 0) {
                        // jumlah dikurangi → kembalikan stok
                        $packing[0]['satuan'] = (string)((int)$packing[0]['satuan'] + $diff);
                    } elseif ($diff < 0) {
                        // jumlah bertambah → kurangi stok
                        $packing[0]['satuan'] = (string)((int)$packing[0]['satuan'] + $diff); // diff negatif
                    }

                    $kuliner->packing = json_encode($packing);
                    $kuliner->status = ((int)$packing[0]['satuan'] <= 0) ? 'habis' : 'ready';
                    $kuliner->save();

                    $penjualan->items()->updateOrCreate(
                        ['item_type' => 'kuliner', 'item_id' => $id],
                        ['jumlah' => $jumlah, 'jumlah_satuan' => $jumlah, 'harga_satuan' => $hargaSatuan, 'total' => $total]
                    );
                }
            }

            $penjualan->update([
                'tanggal' => $request->tanggal,
                'pembeli' => $request->pembeli,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Data penjualan berhasil diperbarui.',
                'penjualan' => $penjualan->load('items')
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
        $Detailpenjualan = Detailpenjualan::where('penjualan_id', $penjualan->id)->get();
        foreach ($Detailpenjualan as $item) {
            $item->delete();
        }

        $panen = Panen::find($penjualan->panen_id);
        if ($panen) {
            $panen->berat_total = (float) $panen->berat_total + (float) $penjualan->jumlah;
            $panen->jumlah      = (int) $panen->jumlah + (int) $penjualan->jumlah_satuan;
            $panen->save();
        }

        $penjualan->delete();

        return response()->json([
            'success' => true,
            'msg' => 'Data penjualan berhasil dihapus.'
        ], Response::HTTP_OK);
    }
}
