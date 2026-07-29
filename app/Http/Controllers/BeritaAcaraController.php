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
        // Data terbaru tampil paling atas
        // dan eager load relasi businessSupport biar nggak N+1 query pas ditampilkan di tabel
        $beritaacara = BeritaAcara::with('businessSupport')
            ->latest()
            ->get();

        // Semua proposal ditampilkan di dropdown, termasuk yang sudah pernah
        // dipakai di Berita Acara sebelumnya (boleh dipilih ulang / dipakai lagi)
        $proposal = Proposal::orderBy('judul')->get();

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
            'bantuan' => 'required|array|min:1',
            'bantuan.*.jenis' => 'required|string|max:255',
            'bantuan.*.jumlah' => 'nullable|string|max:255',
            'bantuan.*.satuan' => 'nullable|string|max:255',
            'bantuan.*.nominal' => 'nullable|string',
            'business_support_choice' => 'required|string',
            'bisnis_support_lainnya' => 'nullable|required_if:business_support_choice,lainnya|string|max:255',
        ]);

        // Ambil data bantuan per-baris (nested array), supaya jenis/jumlah/satuan/nominal
        // dijamin sinkron per index dan tidak geser walau ada field yang kosong/disabled.
        $bantuanInput = $request->input('bantuan', []);
        ksort($bantuanInput);

        $jenis   = array_map(fn($b) => trim($b['jenis'] ?? ''), $bantuanInput);
        $jumlah  = array_map(fn($b) => trim($b['jumlah'] ?? ''), $bantuanInput);
        $satuan  = array_map(fn($b) => trim($b['satuan'] ?? ''), $bantuanInput);
        $nominal = array_map(fn($b) => preg_replace('/[^0-9]/', '', $b['nominal'] ?? ''), $bantuanInput);

        // ======== GENERATE NOMOR SURAT PERMANEN =========

        $tahun = now()->format('Y'); // reset 00 tiap tahun baru
        // $tahun = 2027; cek untuk testing, biar bisa generate nomor surat baru tiap tahun

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
            'jenis_bantuan' => implode(',', $jenis),
            'jumlah_barang' => implode(',', $jumlah),
            'satuan' => implode(',', $satuan),
            'nominal' => implode(',', $nominal),
            'nomor_surat' => $nomorSurat,
            'business_support_id' => $bsData['business_support_id'],
            'bisnis_support_lainnya' => $bsData['bisnis_support_lainnya'],
        ]);

        $jenis = explode(',', $beritaAcara->jenis_bantuan ?? '');
        $jumlah = explode(',', $beritaAcara->jumlah_barang ?? '');
        $satuan = explode(',', $beritaAcara->satuan ?? '');
        $nominal = explode(',', $beritaAcara->nominal ?? '');

        $bantuan = [];

        foreach ($jenis as $i => $item) {
            $bantuan[] = [
                'jenis'   => trim($item),
                'jumlah'  => trim($jumlah[$i] ?? ''),
                'satuan'  => trim($satuan[$i] ?? ''),
                'nominal' => trim($nominal[$i] ?? ''),
            ];
        }
        $proposal = Proposal::find($beritaAcara->proposal_id);

        // TAMBAHAN: ambil nama & jabatan business support (otomatis "PH Manager Bisnis Support" jika manual)
        $bisnisSupportInfo = $this->getBisnisSupportInfo($beritaAcara);

        // Generate PDF pertama
        $pdf = Pdf::loadView('pdf.berita_acara', [
            'data' => $beritaAcara,
            'bantuan' => $bantuan,
            'proposal' => $proposal,
            'namaBisnisSupport' => $bisnisSupportInfo['nama'],
            'jabatanBisnisSupport' => $bisnisSupportInfo['jabatan'],
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

        $jenis = explode(',', $beritaAcara->jenis_bantuan ?? '');
        $jumlah = explode(',', $beritaAcara->jumlah_barang ?? '');
        $satuan = explode(',', $beritaAcara->satuan ?? '');
        $nominal = explode(',', $beritaAcara->nominal ?? '');

        $bantuan = [];

        foreach ($jenis as $i => $item) {
            $bantuan[] = [
                'jenis'   => trim($item),
                'jumlah'  => trim($jumlah[$i] ?? ''),
                'satuan'  => trim($satuan[$i] ?? ''),
                'nominal' => trim($nominal[$i] ?? ''),
            ];
        }

        $bisnisSupportInfo = $this->getBisnisSupportInfo($beritaAcara);

        return view('pdf.berita_acara', [
            'data'=>$beritaAcara,
            'bantuan'=>$bantuan,
            'proposal'=>$beritaAcara->proposal,
            'namaBisnisSupport'=>$bisnisSupportInfo['nama'],
            'jabatanBisnisSupport'=>$bisnisSupportInfo['jabatan'],
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:255',
            'jabatan_penerima' => 'required|string|max:255',
            'bantuan' => 'required|array|min:1',
            'bantuan.*.jenis' => 'required|string|max:255',
            'bantuan.*.jumlah' => 'nullable|string|max:255',
            'bantuan.*.satuan' => 'nullable|string|max:255',
            'bantuan.*.nominal' => 'nullable|string',
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

        // Ambil data bantuan per-baris (nested array), supaya jenis/jumlah/satuan/nominal
        // dijamin sinkron per index dan tidak geser walau ada field yang kosong/disabled.
        $bantuanInput = $request->input('bantuan', []);
        ksort($bantuanInput);

        $jenis   = array_map(fn($b) => trim($b['jenis'] ?? ''), $bantuanInput);
        $jumlah  = array_map(fn($b) => trim($b['jumlah'] ?? ''), $bantuanInput);
        $satuan  = array_map(fn($b) => trim($b['satuan'] ?? ''), $bantuanInput);
        $nominal = array_map(fn($b) => preg_replace('/[^0-9]/', '', $b['nominal'] ?? ''), $bantuanInput);

        $bsData = $this->resolveBusinessSupport($request);

        // Update data
        $beritaAcara->update([
            'nama_penerima' => $request->nama_penerima,
            'jabatan_penerima' => $request->jabatan_penerima,
            'jenis_bantuan' => implode(',', $jenis),
            'jumlah_barang' => implode(',', $jumlah),
            'satuan' => implode(',', $satuan),
            'nominal' => implode(',', $nominal),
            'business_support_id' => $bsData['business_support_id'],
            'bisnis_support_lainnya' => $bsData['bisnis_support_lainnya'],
        ]);

        // TAMBAHAN
        $bisnisSupportInfo = $this->getBisnisSupportInfo($beritaAcara);

        $proposal = Proposal::find($beritaAcara->proposal_id);

        $jenis = explode(',', $beritaAcara->jenis_bantuan ?? '');
        $jumlah = explode(',', $beritaAcara->jumlah_barang ?? '');
        $satuan = explode(',', $beritaAcara->satuan ?? '');
        $nominal = explode(',', $beritaAcara->nominal ?? '');

        $bantuan = [];

        foreach ($jenis as $i => $item) {
            $bantuan[] = [
                'jenis'   => trim($item),
                'jumlah'  => trim($jumlah[$i] ?? ''),
                'satuan'  => trim($satuan[$i] ?? ''),
                'nominal' => trim($nominal[$i] ?? ''),
            ];
        }

        // Generate ulang PDF (nomor tidak berubah)
        $pdf = Pdf::loadView('pdf.berita_acara', [
            'data' => $beritaAcara,
            'bantuan' => $bantuan,
            'proposal' => $proposal,
            'namaBisnisSupport' => $bisnisSupportInfo['nama'],
            'jabatanBisnisSupport' => $bisnisSupportInfo['jabatan'],
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

    /**
     * Upload foto dokumentasi. Foto otomatis di-resize & dikompres
     * di server (pakai GD bawaan PHP, tanpa package tambahan) supaya
     * ukuran filenya kecil tapi kualitasnya tetap layak dilihat.
     */
    public function uploadDokumentasi(Request $request, $id)
    {
        $request->validate([
            // max 10MB untuk file ASLI sebelum dikompres
            // HEIC sengaja TIDAK diizinkan karena tidak bisa dikompres (GD tidak support HEIC)
            'dokumentasi' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ], [
            'dokumentasi.required' => 'Silakan pilih foto terlebih dahulu.',
            'dokumentasi.image' => 'File yang diupload harus berupa gambar.',
            'dokumentasi.mimes' => 'Format foto harus JPG atau PNG. Foto HEIC (format default iPhone) tidak didukung — silakan convert ke JPG/PNG dulu sebelum upload.',
            'dokumentasi.max' => 'Ukuran foto maksimal 10MB. Silakan compress atau pilih foto lain.',
        ]);

        $beritaAcara = BeritaAcara::findOrFail($id);

        // Hapus foto dokumentasi lama kalau ada
        if ($beritaAcara->dokumentasi && Storage::exists('public/' . $beritaAcara->dokumentasi)) {
            Storage::delete('public/' . $beritaAcara->dokumentasi);
        }

        $path = $this->compressAndStoreImage(
            $request->file('dokumentasi'),
            'berita_acara_dokumentasi'
        );

        $beritaAcara->update(['dokumentasi' => $path]);

        return redirect()->route('berita-acara.index')
            ->with('success', 'Dokumentasi berhasil diupload.');
    }

    /**
     * Resize (max width 1280px, jaga aspect ratio) dan kompres (kualitas 70%)
     * gambar yang diupload, lalu simpan ke storage. Return path relatif
     * (tanpa prefix 'public/') yang bisa dipakai dengan asset('storage/...').
     *
     * Catatan: hanya menerima JPG/PNG (divalidasi sebelum method ini dipanggil).
     * HEIC sengaja ditolak di validasi karena GD bawaan PHP tidak bisa decode HEIC.
     */
    // private function compressAndStoreImage($file, string $folder, int $quality = 70, int $maxWidth = 1280): string
    private function compressAndStoreImage($file, string $folder, int $quality = 60, int $maxWidth = 1280): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        $source = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'png' => @imagecreatefrompng($file->getRealPath()),
            default => null,
        };

        // Fallback kalau GD gagal decode: simpan file asli tanpa kompresi
        if (!$source) {
            $filename = 'dokumentasi_' . uniqid() . '.' . $extension;
            $path = $folder . '/' . $filename;
            Storage::put('public/' . $path, file_get_contents($file->getRealPath()));
            return $path;
        }

        // Perbaiki orientasi foto dari HP (baca EXIF) supaya tidak kesamping/terbalik
        if (function_exists('exif_read_data') && in_array($extension, ['jpg', 'jpeg'])) {
            $exif = @exif_read_data($file->getRealPath());
            if (!empty($exif['Orientation'])) {
                $source = match ($exif['Orientation']) {
                    3 => imagerotate($source, 180, 0),
                    6 => imagerotate($source, -90, 0),
                    8 => imagerotate($source, 90, 0),
                    default => $source,
                };
            }
        }

        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);

        // Resize kalau lebih lebar dari $maxWidth, jaga aspect ratio
        if ($originalWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intdiv($originalHeight * $maxWidth, $originalWidth);

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Jaga transparansi untuk PNG
            if ($extension === 'png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            imagedestroy($source);
            $source = $resized;
        }

        $filename = 'dokumentasi_' . uniqid() . '.jpg';
        $path = $folder . '/' . $filename;
        $fullDiskPath = storage_path('app/public/' . $path);

        if (!file_exists(dirname($fullDiskPath))) {
            mkdir(dirname($fullDiskPath), 0755, true);
        }

        // Simpan sebagai JPG dengan kompresi kualitas $quality (0-100, makin kecil makin ringan)
        imagejpeg($source, $fullDiskPath, $quality);
        imagedestroy($source);

        return $path;
    }

    public function destroy($id)
    {
        $beritaAcara = BeritaAcara::findOrFail($id);

        if ($beritaAcara->file_pdf && Storage::exists('public/' . $beritaAcara->file_pdf)) {
            Storage::delete('public/' . $beritaAcara->file_pdf);
        }

        if ($beritaAcara->file_upload && Storage::exists('public/' . $beritaAcara->file_upload)) {
            Storage::delete('public/' . $beritaAcara->file_upload);
        }

        if ($beritaAcara->dokumentasi && Storage::exists('public/' . $beritaAcara->dokumentasi)) {
            Storage::delete('public/' . $beritaAcara->dokumentasi);
        }

        $beritaAcara->delete();

        return redirect()->route('berita-acara.index')
            ->with('success', 'Data Berita acara dan file PDF berhasil dihapus.');
    }

    public function getBantuan($id)
    {
        $beritaAcara = BeritaAcara::findOrFail($id);
        $jenis = explode(',', $beritaAcara->jenis_bantuan ?? '');
        $jumlah = explode(',', $beritaAcara->jumlah_barang ?? '');
        $satuan = explode(',', $beritaAcara->satuan ?? '');
        $nominal = explode(',', $beritaAcara->nominal ?? '');

        $data = [];

        foreach ($jenis as $i => $j) {

            $data[] = [
                'jenis' => trim($j),
                'jumlah' => trim($jumlah[$i] ?? ''),
                'satuan' => trim($satuan[$i] ?? ''),
                'nominal' => trim($nominal[$i] ?? ''),
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
                'jabatan' => 'PH Manager Business Support',
            ];
        }

        // fallback jika keduanya kosong
        return [
            'nama' => 'Sukarno',
            'jabatan' => 'Manager Business Support',
        ];
    }
}