<?php

namespace App\Http\Controllers;

use App\Models\Kuliner;
use App\Models\Panen;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class KulinerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kuliner::with('panen')->get();

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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kuliner $kuliner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kuliner $kuliner)
    {
        //
    }
}
