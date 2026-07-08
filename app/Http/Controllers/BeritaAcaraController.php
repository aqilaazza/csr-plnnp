<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use App\Models\BusinessSupport;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        // TAMBAHAN: eager load relasi businessSupport biar nggak N+1 query pas ditampilkan di tabel
        $beritaacara = BeritaAcara::with('businessSupport')->get();

        // Ambil hanya proposal yang belum punya berita acara
        $proposal = Proposal::doesntHave('beritaAcara')->get();

        // Data master Business Support untuk dropdown
        $businessSupport = BusinessSupport::all();

        return view('form.berita-acara.index', compact('beritaacara', 'proposal', 'businessSupport'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposal,id',
            'nama_penerima' => 'required|string|max:255',
            'jabatan_penerima' => 'required|string|max:255',
            'jenis_bantuan' => 'required|array|min:1',
            'jenis_bantuan.*' => 'required|string|max:255',
            'jumlah_bantuan' => 'required|array|min:1',
            'jumlah_bantuan.*' => 'required|string|max:255',
            'business_support_choice' => 'required|string',
            'bisnis_support_lainnya' => 'nullable|required_if:business_support_choice,lainnya|string|max:255',
        ]);

        // Gabungkan jenis + jumlah
        $bantuan = [
            'jenis' => $request->jenis_bantuan,
            'jumlah' => $request->jumlah_bantuan,
        ];

        // ======== GENERATE NOMOR SURAT PERMANEN =========
        $tahun = now()->format('Y');

        $lastNumber = BeritaAcara::whereYear('created_at', $tahun)
            ->get()
            ->map(fn($item) => (int) explode('.', $item->nomor_surat)[0])
            ->max();

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;
        $no3Digit = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $nomorSurat = "{$no3Digit}.BA.KESP/076/UPPTN/{$tahun}";
        // ------------------------------------------------

        // Resolve pilihan business support (dari master ATAU manual)
        $bsData = $this->resolveBusinessSupport($request);

        // Simpan DB
        $beritaAcara = BeritaAcara::create([
            'proposal_id' => $request->proposal_id,
            'nama_penerima' => $request->nama_penerima,
            'jabatan_penerima' => $request->jabatan_penerima,
            'bantuan' => json_encode($bantuan),
            'nomor_surat' => $nomorSurat,
            'business_support_id' => $bsData['business_support_id'],
            'bisnis_support_lainnya' => $bsData['bisnis_support_lainnya'],
        ]);

        $bantuanArray = json_decode($beritaAcara->bantuan, true);
        $proposal = Proposal::find($beritaAcara->proposal_id);

        // TAMBAHAN: ambil nama & jabatan business support (otomatis "PH Manager Bisnis Support" jika manual)
        $bisnisSupportInfo = $this->getBisnisSupportInfo($beritaAcara);

        // Generate PDF pertama
        $pdf = PDF::loadView('pdf.berita_acara', [
            'data' => $beritaAcara,
            'jenis' => $bantuanArray['jenis'],
            'jumlah' => $bantuanArray['jumlah'],
            'namaBisnisSupport' => $bisnisSupportInfo['nama'],
            'jabatanBisnisSupport' => $bisnisSupportInfo['jabatan'], // TAMBAHAN
            'proposal' => $proposal,
            'nomorBeritaAcara' => $nomorSurat
        ]);

        $pdfName = 'berita_acara_' . $beritaAcara->id . '.pdf';
        Storage::put('public/berita_acara/' . $pdfName, $pdf->output());

        $beritaAcara->update(['file_pdf' => 'berita_acara/' . $pdfName]);

        return redirect()->route('berita-acara.index')
            ->with('success', 'Berita acara berhasil dibuat.');
    }

    public function show($id)
    {
        $beritaAcara = BeritaAcara::with('proposal')->findOrFail($id);

        $bantuan = json_decode($beritaAcara->bantuan, true) ?? ['jenis' => [], 'jumlah' => []];

        // TAMBAHAN
        $bisnisSupportInfo = $this->getBisnisSupportInfo($beritaAcara);

        return view('pdf.berita_acara', [
            'data' => $beritaAcara,
            'bantuan' => $bantuan,
            'jenis' => $bantuan['jenis'] ?? [],
            'jumlah' => $bantuan['jumlah'] ?? [],
            'namaBisnisSupport' => $bisnisSupportInfo['nama'],
            'jabatanBisnisSupport' => $bisnisSupportInfo['jabatan'], // TAMBAHAN
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'jabatan_penerima' => 'required|string|max:255',
            'jenis_bantuan' => 'required|array|min:1',
            'jenis_bantuan.*' => 'required|string|max:255',
            'jumlah_bantuan' => 'required|array|min:1',
            'jumlah_bantuan.*' => 'required|string|max:255',
            'business_support_choice' => 'required|string',
            'bisnis_support_lainnya' => 'nullable|required_if:business_support_choice,lainnya|string|max:255',
        ]);

        $beritaAcara = BeritaAcara::findOrFail($id);

        // nomor_surat TIDAK DIUBAH
        $nomorSurat = $beritaAcara->nomor_surat;

        // Hapus PDF sebelumnya
        if ($beritaAcara->file_pdf && Storage::exists('public/' . $beritaAcara->file_pdf)) {
            Storage::delete('public/' . $beritaAcara->file_pdf);
        }

        $bantuan = [
            'jenis' => $request->jenis_bantuan,
            'jumlah' => $request->jumlah_bantuan,
        ];

        $bsData = $this->resolveBusinessSupport($request);

        // Update data
        $beritaAcara->update([
            'nama_penerima' => $request->nama_penerima,
            'jabatan_penerima' => $request->jabatan_penerima,
            'bantuan' => json_encode($bantuan),
            'business_support_id' => $bsData['business_support_id'],
            'bisnis_support_lainnya' => $bsData['bisnis_support_lainnya'],
        ]);

        // TAMBAHAN
        $bisnisSupportInfo = $this->getBisnisSupportInfo($beritaAcara);

        $proposal = Proposal::find($beritaAcara->proposal_id);
        $bantuanArray = json_decode($beritaAcara->bantuan, true);

        // Generate ulang PDF (nomor tidak berubah)
        $pdf = Pdf::loadView('pdf.berita_acara', [
            'data' => $beritaAcara,
            'jenis' => $bantuanArray['jenis'],
            'jumlah' => $bantuanArray['jumlah'],
            'namaBisnisSupport' => $bisnisSupportInfo['nama'],
            'jabatanBisnisSupport' => $bisnisSupportInfo['jabatan'], // TAMBAHAN
            'proposal' => $proposal,
            'nomorBeritaAcara' => $nomorSurat
        ]);

        $pdfName = 'berita_acara_' . $beritaAcara->id . '.pdf';
        Storage::put('public/berita_acara/' . $pdfName, $pdf->output());

        $beritaAcara->update(['file_pdf' => 'berita_acara/' . $pdfName]);

        return redirect()->route('berita-acara.index')
            ->with('success', 'Berita acara berhasil diperbarui.');
    }

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file_upload' => 'required|mimes:jpg,jpeg,png,heic,pdf',
        ]);

        $beritaAcara = BeritaAcara::findOrFail($id);

        if ($beritaAcara->file_upload && Storage::exists('public/' . $beritaAcara->file_upload)) {
            Storage::delete('public/' . $beritaAcara->file_upload);
        }

        $file = $request->file('file_upload');
        $path = $file->store('public/berita_acara_upload');
        $beritaAcara->update(['file_upload' => str_replace('public/', '', $path)]);

        return redirect()->route('berita-acara.index')->with('success', 'File berhasil diupload.');
    }

    public function destroy($id)
    {
        $beritaAcara = BeritaAcara::findOrFail($id);

        if ($beritaAcara->file_pdf && Storage::exists('public/' . $beritaAcara->file_pdf)) {
            Storage::delete('public/' . $beritaAcara->file_pdf);
        }

        $beritaAcara->delete();

        return redirect()->route('berita-acara.index')
            ->with('success', 'Data Berita acara dan file PDF berhasil dihapus.');
    }

    public function getBantuan($id)
    {
        $beritaAcara = BeritaAcara::findOrFail($id);
        $bantuanArray = json_decode($beritaAcara->bantuan, true) ?? ['jenis' => [], 'jumlah' => []];

        $data = [];
        foreach ($bantuanArray['jenis'] as $i => $jenis) {
            $data[] = [
                'jenis_bantuan' => $jenis,
                'jumlah_bantuan' => $bantuanArray['jumlah'][$i] ?? '',
            ];
        }

        return response()->json($data);
    }

    /**
     * Tentukan business_support_id / bisnis_support_lainnya
     * berdasarkan pilihan user di form (dropdown master atau "lainnya").
     * Pilihan "lainnya" TIDAK membuat data baru di master BusinessSupport,
     * hanya disimpan sebagai teks bebas di tabel berita_acara.
     */
    private function resolveBusinessSupport(Request $request): array
    {
        $choice = $request->input('business_support_choice');

        if ($choice === 'lainnya') {
            return [
                'business_support_id' => null,
                'bisnis_support_lainnya' => $request->input('bisnis_support_lainnya'),
            ];
        }

        return [
            'business_support_id' => $choice,
            'bisnis_support_lainnya' => null,
        ];
    }

    /**
     * Ambil nama & jabatan business support untuk ditampilkan di PDF.
     * - Jika dari master (business_support_id terisi): jabatan = "Manager Business Support"
     * - Jika input manual "Lainnya" (bisnis_support_lainnya terisi): jabatan otomatis
     *   menjadi "PH Manager Bisnis Support"
     */
    private function getBisnisSupportInfo(BeritaAcara $beritaAcara): array
    {
        if ($beritaAcara->business_support_id) {
            $bs = BusinessSupport::find($beritaAcara->business_support_id);

            return [
                'nama' => $bs ? $bs->nama : 'Sukarno',
                'jabatan' => 'Manager Business Support',
            ];
        }

        if ($beritaAcara->bisnis_support_lainnya) {
            return [
                'nama' => $beritaAcara->bisnis_support_lainnya,
                'jabatan' => 'PH Manager Bisnis Support',
            ];
        }

        // fallback jika keduanya kosong
        return [
            'nama' => 'Sukarno',
            'jabatan' => 'Manager Business Support',
        ];
    }
}