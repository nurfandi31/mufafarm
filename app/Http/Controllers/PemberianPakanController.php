<?php

namespace App\Http\Controllers;

use App\Models\PemberianPakan;
use App\Models\Bibit;
use App\Models\Pakan;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class PemberianPakanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PemberianPakan::with('kolam', 'bibit', 'pakan')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.pemberian-pakan.index', ['title' => 'Pemberian Pakan']);
    }

    public function bibitlist(Request $request)
    {
        $search = $request->get('q');
        $query = Bibit::with('kolam')->select('id', 'nama', 'jenis', 'kolam_id');
        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $data = $query->get()->map(function ($item) {
            $kolamNama = $item->kolam->nama ?? '-';
            $jenis = $item->jenis ?? '';
            $text = $kolamNama . ' (' . $item->nama . ($jenis ? " - $jenis" : '') . ')';
            return [
                'id'   => $item->id,
                'text' => $text,
            ];
        });

        return response()->json($data);
    }

    public function pakanlist(Request $request)
    {
        $search = $request->get('q');

        $query = Pakan::select('id', 'nama', 'satuan', 'stok')
            ->where('stok', '>', 0);

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        return response()->json(
            $query->get()->map(fn ($item) => [
                'id'     => $item->id,
                'nama'   => $item->nama,
                'satuan' => $item->satuan,
                'stok'   => $item->stok,
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
            'bibit_id',
            'pakan_id',
            'jumlah',
            'tanggal_pemberian',
        ]);
        $rules = [
            'bibit_id'          => 'required',
            'pakan_id'          => 'required',
            'jumlah'            => 'required',
            'tanggal_pemberian' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        };

        $pemberianPakan = PemberianPakan::create([
            'bibit_id'          => $request->bibit_id,
            'pakan_id'          => $request->pakan_id,
            'jumlah'            => $request->jumlah,
            'tanggal_pemberian' => $request->tanggal_pemberian,
        ]);

        $pakan = Pakan::where('id', $request->pakan_id)->decrement('stok', $request->jumlah);

        return response()->json([
            'success' => true,
            'msg' => 'Data pemberian pakan berhasil ditambahkan',
            'data' => $pemberianPakan,
            'pakan' => $pakan
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(pemberianPakan $pemberianPakan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pemberianPakan $pemberianPakan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PemberianPakan $pemberianPakan)
    {
        $data = $request->only([
            'bibit_id',
            'pakan_id',
            'jumlah',
            'tanggal_pemberian',
        ]);

        $rules = [
            'bibit_id'          => 'required',
            'pakan_id'          => 'required',
            'jumlah'            => 'required',
            'tanggal_pemberian' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $oldPakanId = $pemberianPakan->pakan_id;
        $oldJumlah = $pemberianPakan->jumlah;

        Pakan::where('id', $oldPakanId)->increment('stok', $oldJumlah);

        Pakan::where('id', $request->pakan_id)->decrement('stok', $request->jumlah);

        $pemberianPakan->update([
            'bibit_id'          => $request->bibit_id,
            'pakan_id'          => $request->pakan_id,
            'jumlah'            => $request->jumlah,
            'tanggal_pemberian' => $request->tanggal_pemberian,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Data pemberian pakan berhasil diubah',
            'data' => $pemberianPakan,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pemberianPakan $pemberianPakan)
    {
        $pemberianPakan->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data pemberian pakan berhasil dihapus',
        ], Response::HTTP_OK);
    }
}
