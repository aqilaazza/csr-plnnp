<?php

namespace App\Http\Controllers;

use App\Models\KategoriInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriInstansiController extends Controller
{
    public function index()
    {
        $kategoriInstansi = KategoriInstansi::withCount('proposal')
            ->with('subInstansi')
            ->get();

        return view('manajemen-data.kategori-instansi.index', compact('kategoriInstansi'));
    }

    public function create()
    {
        return view('manajemen-data.kategori-instansi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'           => 'required|string|max:255|unique:kategori_instansi,nama',
            'sub_instansi'   => 'nullable|array',
            'sub_instansi.*' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain',
        ]);

        DB::transaction(function () use ($validated) {
            $kategoriInstansi = KategoriInstansi::create([
                'nama' => $validated['nama'],
            ]);

            foreach ($validated['sub_instansi'] ?? [] as $namaSub) {
                if (trim((string) $namaSub) === '') {
                    continue;
                }
                $kategoriInstansi->subInstansi()->create([
                    'nama' => trim($namaSub),
                ]);
            }
        });

        return redirect()->route('kategori-instansi.index')
            ->with('success', 'Kategori instansi berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $kategoriInstansi = KategoriInstansi::with('subInstansi')->findOrFail($id);
        return view('manajemen-data.kategori-instansi.edit', compact('kategoriInstansi'));
    }

    public function update(Request $request, string $id)
    {
        $kategoriInstansi = KategoriInstansi::findOrFail($id);

        $validated = $request->validate([
            'nama'           => 'required|string|max:255|unique:kategori_instansi,nama,' . $id,
            'sub_instansi'   => 'nullable|array',
            'sub_instansi.*' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain',
        ]);

        DB::transaction(function () use ($validated, $kategoriInstansi) {
            $kategoriInstansi->update(['nama' => $validated['nama']]);

            // Sinkron ulang: hapus sub lama, buat ulang sesuai input.
            // Kalau tidak ada input sama sekali, kategori ini jadi tanpa sub.
            $kategoriInstansi->subInstansi()->delete();

            foreach ($validated['sub_instansi'] ?? [] as $namaSub) {
                if (trim((string) $namaSub) === '') {
                    continue;
                }
                $kategoriInstansi->subInstansi()->create([
                    'nama' => trim($namaSub),
                ]);
            }
        });

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