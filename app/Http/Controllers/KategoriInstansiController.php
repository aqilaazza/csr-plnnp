<?php

namespace App\Http\Controllers;

use App\Models\KategoriInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    protected function validateKategoriInstansi(Request $request, ?string $ignoreId = null): array
    {
        $namaUniqueRule = 'required|string|max:255|unique:kategori_instansi,nama' . ($ignoreId ? ',' . $ignoreId : '');

        $validator = Validator::make($request->all(), [
            'nama'                  => $namaUniqueRule,
            'contoh'                => 'nullable|string|max:255',
            'sub_instansi'          => 'nullable|array',
            'sub_instansi.*'        => 'nullable|string|max:255',
            'sub_instansi_contoh'   => 'nullable|array',
            'sub_instansi_contoh.*' => 'nullable|string|max:255',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique'   => 'Nama kategori sudah ada, gunakan nama lain',
        ]);

        $validator->after(function ($validator) use ($request) {
            $subNama = collect($request->input('sub_instansi', []))
                ->map(fn ($v) => trim((string) $v))
                ->filter(fn ($v) => $v !== '')
                ->values();

            if ($subNama->isEmpty()) {
                // Tidak ada sub instansi -> contoh kategori wajib diisi.
                if (trim((string) $request->input('contoh')) === '') {
                    $validator->errors()->add('contoh', 'Contoh nama instansi wajib diisi.');
                }
            } else {
                // Ada sub instansi -> contoh tiap sub instansi yang diisi wajib diisi juga.
                $subContoh = $request->input('sub_instansi_contoh', []);
                foreach ($request->input('sub_instansi', []) as $index => $namaSub) {
                    if (trim((string) $namaSub) === '') {
                        continue;
                    }
                    if (trim((string) ($subContoh[$index] ?? '')) === '') {
                        $validator->errors()->add(
                            "sub_instansi_contoh.$index",
                            'Contoh untuk sub instansi "' . trim($namaSub) . '" wajib diisi.'
                        );
                    }
                }
            }
        });

        return $validator->validate();
    }

    public function store(Request $request)
    {
        $validated = $this->validateKategoriInstansi($request);

        DB::transaction(function () use ($validated) {
            $subNama   = $validated['sub_instansi'] ?? [];
            $hasSub    = collect($subNama)->filter(fn ($v) => trim((string) $v) !== '')->isNotEmpty();

            $kategoriInstansi = KategoriInstansi::create([
                'nama'   => $validated['nama'],
                'contoh' => $hasSub ? null : ($validated['contoh'] ?? null),
            ]);

            $subContoh = $validated['sub_instansi_contoh'] ?? [];

            foreach ($subNama as $index => $namaSub) {
                if (trim((string) $namaSub) === '') {
                    continue;
                }
                $kategoriInstansi->subInstansi()->create([
                    'nama'   => trim($namaSub),
                    'contoh' => isset($subContoh[$index]) ? trim($subContoh[$index]) : null,
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

        $validated = $this->validateKategoriInstansi($request, $id);

        DB::transaction(function () use ($validated, $kategoriInstansi) {
            $subNama = $validated['sub_instansi'] ?? [];
            $hasSub  = collect($subNama)->filter(fn ($v) => trim((string) $v) !== '')->isNotEmpty();

            $kategoriInstansi->update([
                'nama'   => $validated['nama'],
                'contoh' => $hasSub ? null : ($validated['contoh'] ?? null),
            ]);

            // Sinkron ulang: hapus sub lama, buat ulang sesuai input.
            // Kalau tidak ada input sama sekali, kategori ini jadi tanpa sub.
            $kategoriInstansi->subInstansi()->delete();

            $subContoh = $validated['sub_instansi_contoh'] ?? [];

            foreach ($subNama as $index => $namaSub) {
                if (trim((string) $namaSub) === '') {
                    continue;
                }
                $kategoriInstansi->subInstansi()->create([
                    'nama'   => trim($namaSub),
                    'contoh' => isset($subContoh[$index]) ? trim($subContoh[$index]) : null,
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