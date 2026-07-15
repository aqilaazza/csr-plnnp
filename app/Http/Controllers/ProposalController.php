<?php

namespace App\Http\Controllers;

use App\Exports\ProposalExport;
use App\Models\Proposal;
use App\Models\Kelayakan;
use App\Services\KelayakanPdfService;
use App\Models\KategoriInstansi;
use App\Models\SubInstansi;
use App\Models\SubProses;
use App\Models\TipeProses;
use App\Models\Tipologi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProposalController extends Controller
{
    protected $pdfService;

    public function __construct(KelayakanPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $proposal = Proposal::with(['beritaAcara', 'kelayakan', 'kategoriInstansi', 'subInstansi'])->get();
        return view('proposal.pengajuan.index', compact('proposal'));
    }

    public function create()
    {
        return view('proposal.pengajuan.create', [
            'tipologi' => Tipologi::all(),
            'proses'   => TipeProses::with('subProses')->get(),
            'kategoriInstansi' => KategoriInstansi::all(),
        ]);
    }

    /**
     * AJAX: ambil daftar sub instansi berdasarkan kategori instansi yang dipilih
     */
    public function getSubInstansi(string $kategoriInstansiId)
    {
        $subInstansi = SubInstansi::where('kategori_instansi_id', $kategoriInstansiId)
            ->orderBy('nama')
            ->get(['id', 'nama']);

        return response()->json($subInstansi);
    }

    public function store(Request $request)
    {
        $isManual = empty($request->kabupaten_id);

        $rules = [
            'judul'                 => 'required|string|max:255',
            'kategori_instansi_id'  => 'required|exists:kategori_instansi,id',
            'sub_instansi_id'       => 'nullable|exists:sub_instansi,id',
            'instansi_pengajuan'    => 'required|string|max:255',
            'contact_person'        => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'nama_cp'               => 'required|string|max:100',
            'tanggal_disposisi'     => 'required|date',
            'nominal_pengajuan'     => 'nullable|string',
            'barang_pengajuan'      => 'nullable|string|max:255',
            'tipologi_id'           => 'required|exists:tipologi,id',
            'status'                => 'required',
            'nominal_disetujui'     => 'nullable',
            'barang_disetujui'      => 'nullable|string|max:255',
            'nama_pic_id'           => 'required|string|max:255',
            'tipe_proses_id'        => 'required|exists:tipe_proses,id',
            'keterangan'            => 'nullable|string|max:1000',
            'overdue'               => 'required|date',
        ];

        if (!$isManual) {
            $rules = array_merge($rules, [
                'kabupaten_id'   => 'required',
                'kabupaten_nama' => 'required|string',
                'kecamatan_id'   => 'required',
                'kecamatan_nama' => 'required|string',
                'kelurahan_id'   => 'required',
                'kelurahan_nama' => 'required|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'kabupaten_manual' => 'required|string|max:50',
                'kecamatan_manual' => 'required|string|max:50',
                'kelurahan_manual' => 'required|string|max:50',
            ]);
        }

        $messages = [
            'contact_person.required' => 'No. HP Contact Person/Instansi wajib diisi.',
            'contact_person.regex'    => 'No. HP Contact Person/Instansi hanya boleh berisi angka.',
            'contact_person.min'      => 'No. HP Contact Person/Instansi minimal 10 digit.',
            'contact_person.max'      => 'No. HP Contact Person/Instansi maksimal 15 digit.',
            'nama_cp.required'        => 'Nama Contact Person/Instansi wajib diisi.',
            'nama_cp.max'             => 'Nama Contact Person/Instansi maksimal 100 karakter.',
        ];

        $validated = $request->validate($rules, $messages);

        // Cek manual: sub instansi wajib diisi kalau kategori yang dipilih punya sub.
        // Dibuat manual (bukan closure di dalam $rules) karena closure biasa
        // otomatis di-skip oleh Laravel saat value-nya kosong dan rule "nullable" dipakai.
        $adaSub = SubInstansi::where('kategori_instansi_id', $request->kategori_instansi_id)->exists();
        if ($adaSub && empty($request->sub_instansi_id)) {
            return back()
                ->withErrors(['sub_instansi_id' => 'Sub Instansi wajib dipilih untuk kategori ini.'])
                ->withInput();
        }

        $validated['nominal_pengajuan'] =
            ($request->nominal_pengajuan === '-' || empty($request->nominal_pengajuan))
            ? null
            : preg_replace('/[^0-9]/', '', $request->nominal_pengajuan);

        $validated['nominal_disetujui'] =
            ($request->nominal_disetujui === '-' || empty($request->nominal_disetujui))
            ? null
            : preg_replace('/[^0-9]/', '', $request->nominal_disetujui);

        if ($isManual) {
            $validated['kabupaten_id']   = null;
            $validated['kabupaten_nama'] = $request->kabupaten_manual;
            $validated['kecamatan_id']   = null;
            $validated['kecamatan_nama'] = $request->kecamatan_manual;
            $validated['kelurahan_id']   = null;
            $validated['kelurahan_nama'] = $request->kelurahan_manual;
        }

        $proposal = Proposal::create($validated);

        $subProsesList = SubProses::where('tipe_proses_id', $proposal->tipe_proses_id)->get();
        foreach ($subProsesList as $subProses) {
            \App\Models\ProposalProsesChecklist::create([
                'proposal_id'   => $proposal->id,
                'sub_proses_id' => $subProses->id,
                'is_checked'    => false,
                'checked_at'    => null,
            ]);
        }

        return redirect()->route('proposal.index')->with('success', 'Data proposal berhasil disimpan.');
    }

    public function edit(string $id)
    {
        $proposal = Proposal::findOrFail($id);

        $tipologi = Tipologi::all();
        $proses   = TipeProses::all();
        $kategoriInstansi = KategoriInstansi::all();

        // sub instansi milik kategori yang sedang dipilih, buat pre-fill dropdown saat halaman edit dibuka
        $subInstansi = SubInstansi::where('kategori_instansi_id', $proposal->kategori_instansi_id)
            ->orderBy('nama')
            ->get();

        return view('proposal.pengajuan.edit', compact('proposal', 'tipologi', 'proses', 'kategoriInstansi', 'subInstansi'));
    }

    public function update(Request $request, $id)
    {
        $isAuto = !empty($request->kabupaten_id);

        $rules = [
            'judul'                 => 'required|string|max:255',
            'kategori_instansi_id'  => 'required|exists:kategori_instansi,id',
            'sub_instansi_id'       => 'nullable|exists:sub_instansi,id',
            'instansi_pengajuan'    => 'required|string|max:255',
            'contact_person'        => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'nama_cp'               => 'required|string|max:100',
            'tanggal_disposisi'     => 'required|date',
            'nominal_pengajuan'     => 'nullable',
            'barang_pengajuan'      => 'nullable|string|max:255',
            'tipologi_id'           => 'required|exists:tipologi,id',
            'status'                => 'required',
            'nominal_disetujui'     => 'nullable',
            'barang_disetujui'      => 'nullable|string|max:255',
            'tipe_proses_id'        => 'required|exists:tipe_proses,id',
            'keterangan'            => 'nullable|string|max:1000',
            'overdue'               => 'nullable|date',
        ];

        if ($isAuto) {
            $rules = array_merge($rules, [
                'kabupaten_id'   => 'required|string',
                'kabupaten_nama' => 'required|string',
                'kecamatan_id'   => 'required|string',
                'kecamatan_nama' => 'required|string',
                'kelurahan_id'   => 'required|string',
                'kelurahan_nama' => 'required|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'kabupaten_manual' => 'required|string|max:255',
                'kecamatan_manual' => 'required|string|max:255',
                'kelurahan_manual' => 'required|string|max:255',
            ]);
        }

        $messages = [
            'contact_person.required' => 'No. HP Contact Person/Instansi wajib diisi.',
            'contact_person.regex'    => 'No. HP Contact Person/Instansi hanya boleh berisi angka.',
            'contact_person.min'      => 'No. HP Contact Person/Instansi minimal 10 digit.',
            'contact_person.max'      => 'No. HP Contact Person/Instansi maksimal 15 digit.',
            'nama_cp.required'        => 'Nama Contact Person/Instansi wajib diisi.',
            'nama_cp.max'             => 'Nama Contact Person/Instansi maksimal 100 karakter.',
        ];

        $validated = $request->validate($rules, $messages);

        // Cek manual: sub instansi wajib diisi kalau kategori yang dipilih punya sub.
        $adaSub = SubInstansi::where('kategori_instansi_id', $request->kategori_instansi_id)->exists();
        if ($adaSub && empty($request->sub_instansi_id)) {
            return back()
                ->withErrors(['sub_instansi_id' => 'Sub Instansi wajib dipilih untuk kategori ini.'])
                ->withInput();
        }

        $validated['nominal_pengajuan'] = $request->nominal_pengajuan
            ? preg_replace('/[^0-9]/', '', $request->nominal_pengajuan)
            : null;

        $validated['nominal_disetujui'] = $request->nominal_disetujui
            ? preg_replace('/[^0-9]/', '', $request->nominal_disetujui)
            : null;

        if (!$isAuto) {
            $validated['kabupaten_id']   = null;
            $validated['kabupaten_nama'] = $request->kabupaten_manual;

            $validated['kecamatan_id']   = null;
            $validated['kecamatan_nama'] = $request->kecamatan_manual;

            $validated['kelurahan_id']   = null;
            $validated['kelurahan_nama'] = $request->kelurahan_manual;
        }

        $proposal = Proposal::findOrFail($id);

        $oldData = $proposal->only([
            'judul',
            'nominal_pengajuan',
            'nominal_disetujui',
            'barang_pengajuan',
            'barang_disetujui',
            'contact_person',
            'nama_cp',
            'kategori_instansi_id',
            'sub_instansi_id',
        ]);

        $proposal->update($validated);

        $newData = $proposal->fresh()->only([
            'judul',
            'nominal_pengajuan',
            'nominal_disetujui',
            'barang_pengajuan',
            'barang_disetujui',
            'contact_person',
            'nama_cp',
            'kategori_instansi_id',
            'sub_instansi_id',
        ]);

        if ($oldData != $newData) {

            $kelayakan = Kelayakan::where('proposal_id', $proposal->id)->first();

            if ($kelayakan) {

                $kelayakan->update([
                    'contact_person' => $proposal->contact_person,
                    'jenis_stakeholder' => optional($proposal->kategoriInstansi)->nama,
                ]);

                $this->pdfService->generate($kelayakan);
            }
        }

        return redirect()->route('proposal.index')->with('success', 'Data proposal berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $proposal = Proposal::findOrFail($id);
        $proposal->delete();

        return redirect()->route('proposal.index')->with('success', 'Data proposal berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = Proposal::with(['tipologi', 'tipeProses.subProses', 'namaPic']);

        if ($request->has('pic') && $request->pic !== null) {
            $query->whereHas('namaPic', function ($q) use ($request) {
                $q->where('nama', $request->pic);
            });
        }

        if ($request->has('tipologi') && $request->tipologi !== null) {
            $query->whereHas('tipologi', function ($q) use ($request) {
                $q->where('kode', $request->tipologi);
            });
        }

        $data = $query->get();

        return Excel::download(new ProposalExport($data), 'data_proposal.xlsx');
    }
}