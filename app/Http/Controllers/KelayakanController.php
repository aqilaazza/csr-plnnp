<?php

namespace App\Http\Controllers;

use App\Models\Kelayakan;
use App\Models\Proposal;
use App\Services\KelayakanPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class KelayakanController extends Controller
{
    protected $pdfService;

    public function __construct(KelayakanPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $kelayakan = Kelayakan::latest()->get();

        // Ambil hanya proposal yang belum memiliki kelayakan
        $proposal = Proposal::doesntHave('kelayakan')->get();

            return view('form.kelayakan.index', compact('kelayakan', 'proposal'));
        }

    public function create()
    {
        // Eager load tipologi & kategoriInstansi supaya bisa ditampilkan sebagai preview di form (mengikuti tampilan PDF)
        $proposal = Proposal::with(['tipologi', 'kategoriInstansi'])->doesntHave('kelayakan')->get();

            return view('form.kelayakan.create', compact('proposal'));
    }

    public function store(Request $request)
    {
        $request->validate([
        'proposal_id' => 'required|exists:proposal,id',
        'dasar_pelaksanaan' => 'required|string',
        'latar_belakang' => 'required|string',
        'tujuan' => 'required|string',
        'indikator_lingkungan' => 'nullable|string',
        'indikator_sosial' => 'nullable|string',
        'jumlah_penerima_manfaat' => 'nullable|string|max:255',
        'pejabat_instansi' => 'nullable|string',
        'data_terdahulu' => 'nullable|string',
        // 'contact_person' dan 'jenis_stakeholder' TIDAK divalidasi/diinput dari form lagi,
        // karena datanya diambil otomatis dari tabel proposal.
        'catatan_khusus' => 'nullable|string',
        'prioritas' => 'required|in:1,2,3,4,5',
        'dampak' => 'required|in:1,2,3,4,5',
    ]);

        // Ambil proposal terkait untuk menarik data yang memang sumbernya dari proposal
        $proposal = Proposal::findOrFail($request->proposal_id);

        // Simpan data ke database
        $kelayakan = Kelayakan::create([
        'proposal_id' => $request->proposal_id,
        'dasar_pelaksanaan' => $request->dasar_pelaksanaan,
        'latar_belakang' => $request->latar_belakang,
        'tujuan' => $request->tujuan,
        'indikator_lingkungan' => $request->indikator_lingkungan,
        'indikator_sosial' => $request->indikator_sosial,
        'jumlah_penerima_manfaat' => $request->jumlah_penerima_manfaat,
        // Kategori Stakeholder diambil dari relasi Proposal->kategoriInstansi, bukan input form
        'jenis_stakeholder' => $proposal->kategoriInstansi->nama ?? null,
        'pejabat_instansi' => $request->pejabat_instansi, 
        'data_terdahulu' => $request->data_terdahulu,
        // Contact person diambil dari data Proposal, bukan input form Kelayakan
        'contact_person' => $proposal->contact_person,
        'catatan_khusus' => $request->catatan_khusus,
        'prioritas' => $request->prioritas,
        'dampak' => $request->dampak,
        ]);

        $this->pdfService->generate($kelayakan);

        return redirect()->route('kelayakan.index')->with('success', 'Form Kelayakan berhasil dibuat.');
    }

    public function edit($id)
    {
        $kelayakan = Kelayakan::with('proposal.tipologi', 'proposal.kategoriInstansi')->findOrFail($id);

        return view('form.kelayakan.edit', compact('kelayakan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
        'dasar_pelaksanaan' => 'required|string',
        'latar_belakang' => 'required|string',
        'tujuan' => 'required|string',
        'indikator_lingkungan' => 'nullable|string',
        'indikator_sosial' => 'nullable|string',
        'jumlah_penerima_manfaat' => 'nullable|string|max:255',
        'pejabat_instansi' => 'nullable|string',
        'data_terdahulu' => 'nullable|string',
        'catatan_khusus' => 'nullable|string',
        'prioritas' => 'required|in:1,2,3,4,5',
        'dampak' => 'required|in:1,2,3,4,5',
    ]);

        $kelayakan = Kelayakan::findOrFail($id);

        // Hapus PDF lama jika ada
        if ($kelayakan->file_pdf && Storage::exists('public/' . $kelayakan->file_pdf)) {
            Storage::delete('public/' . $kelayakan->file_pdf);
        }

        // $currentRevisi = (int) $kelayakan->revisi;
        // $newRevisi = str_pad($currentRevisi + 1, 2, '0', STR_PAD_LEFT);
        $kelayakan->increment('revisi');
        // $kelayakan->refresh();

        // Ambil ulang data proposal terkait, jaga-jaga kalau contact_person / kategori_instansi di proposal berubah
        $proposal = $kelayakan->proposal;

        // Update data di database
        $kelayakan->update([
            'dasar_pelaksanaan' => $request->dasar_pelaksanaan,
            'latar_belakang' => $request->latar_belakang,
            'tujuan' => $request->tujuan,
            'indikator_lingkungan' => $request->indikator_lingkungan,
            'indikator_sosial' => $request->indikator_sosial,
            'jumlah_penerima_manfaat' => $request->jumlah_penerima_manfaat,
            // Tetap sinkron dengan relasi kategoriInstansi proposal, bukan input manual
            'jenis_stakeholder' => $proposal?->kategoriInstansi?->nama,
            'pejabat_instansi' => $request->pejabat_instansi,
            'data_terdahulu' => $request->data_terdahulu,
            'catatan_khusus' => $request->catatan_khusus,
            'prioritas' => $request->prioritas,
            'dampak' => $request->dampak,
            // 'revisi' => $newRevisi,
        ]);

        $kelayakan->revisi = str_pad($kelayakan->revisi, 2, '0', STR_PAD_LEFT);
        $kelayakan->save();

        $this->pdfService->generate($kelayakan);
        return redirect()->route('kelayakan.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function uploadBerkas(Request $request, $id)
    {
        $request->validate([
            'berkas_pdf' => 'required|mimes:pdf|max:5120', // maksimal 5 MB
        ]);

        $kelayakan = Kelayakan::findOrFail($id);

        // Hapus file lama jika ada
        if ($kelayakan->berkas_pdf && Storage::exists('public/' . $kelayakan->berkas_pdf)) {
            Storage::delete('public/' . $kelayakan->berkas_pdf);
        }

        // Simpan file baru
        $file = $request->file('berkas_pdf');
        $namaFile = 'berkas_' . $kelayakan->id . '_' . time() . '.pdf';

        $file->storeAs('public/berkas_kelayakan', $namaFile);

        // Simpan path ke database
        $kelayakan->update([
            'berkas_pdf' => 'berkas_kelayakan/' . $namaFile
        ]);

        return redirect()->route('kelayakan.index')
            ->with('success', 'Berkas berhasil diupload.');
    }

    public function destroy($id)
    {
        $kelayakan = Kelayakan::findOrFail($id);

        // Hapus file PDF dari storage jika ada
        if ($kelayakan->file_pdf && Storage::exists('public/' . $kelayakan->file_pdf)) {
            Storage::delete('public/' . $kelayakan->file_pdf);
        }

        // Hapus data dari database
        $kelayakan->delete();

        return redirect()->route('kelayakan.index')
            ->with('success', 'Data kelayakan dan file PDF berhasil dihapus.');
    }
}