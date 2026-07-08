<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('manajemen-data.satuan.index', [
            'satuan' => Satuan::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manajemen-data.satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:satuan,nama',
        ]);

        Satuan::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('satuan.index')->with('success', 'Jenis satuan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:50|unique:satuan,nama,' . $id,
        ]);

        $satuan = Satuan::findOrFail($id);

        $satuan->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);

        $satuan->delete();

        return redirect()->route('satuan.index')
            ->with('success', 'Data satuan berhasil dihapus.');
    }
}