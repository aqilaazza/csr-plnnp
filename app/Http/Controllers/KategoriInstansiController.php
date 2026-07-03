<?php

namespace App\Http\Controllers;

use App\Models\KategoriInstansi;
use Illuminate\Http\Request;

class KategoriInstansiController extends Controller
{
    public function index()
    {
        $kategoriInstansi = KategoriInstansi::withCount('proposal')->get();
        return view('manajemen-data.kategori-instansi.index', compact('kategoriInstansi'));
    }

    public function create()
    {
        return view('manajemen-data.kategori-instansi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_instansi,nama',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain',
        ]);

        KategoriInstansi::create($validated);

        return redirect()->route('kategori-instansi.index')
            ->with('success', 'Kategori instansi berhasil ditambahkan.');
    }

    public function edit(string $id)
{
    $kategoriInstansi = KategoriInstansi::findOrFail($id);
    return view('manajemen-data.kategori-instansi.edit', compact('kategoriInstansi'));
}

    public function update(Request $request, string $id)
    {
        $kategoriInstansi = KategoriInstansi::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_instansi,nama,' . $id,
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain',
        ]);

        $kategoriInstansi->update($validated);

        return redirect()->route('kategori-instansi.index')
            ->with('success', 'Kategori instansi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $kategoriInstansi = KategoriInstansi::findOrFail($id);

        if ($kategoriInstansi->proposal()->exists()) {
            return redirect()->route('kategori-instansi.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih digunakan oleh data proposal.');
        }

        $kategoriInstansi->delete();

        return redirect()->route('kategori-instansi.index')
            ->with('success', 'Kategori instansi berhasil dihapus.');
    }
}