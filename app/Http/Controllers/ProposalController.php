<?php

namespace App\Http\Controllers;

use App\Exports\ProposalExport;
use App\Models\Proposal;
use App\Models\SubProses;
use App\Models\TipeProses;
use App\Models\Tipologi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $proposal = Proposal::with(['beritaAcara', 'kelayakan'])->get();
        return view('proposal.pengajuan.index', compact('proposal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proposal.pengajuan.create', [
            'tipologi' => Tipologi::all(),
            'proses'   => TipeProses::with('subProses')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Wilayah manual ditentukan dari ada/tidaknya kabupaten_id (dropdown Wilayah)
        // (dulu: $metode = $request->metode_input;)
        $isManual = empty($request->kabupaten_id);

        // Validasi umum
        $rules = [
            'judul'              => 'required|string|max:255',
            'kategori_instansi' => 'required|in:Pemerintahan,APH,TNI,Lembaga Masyarakat',
            'instansi_pengajuan' => 'required|string|max:255',
            'contact_person' => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'tanggal_disposisi'  => 'required|date',
            'nominal_pengajuan'  => 'nullable|string',
            'barang_pengajuan'   => 'nullable|string|max:255',
            'tipologi_id'        => 'required|exists:tipologi,id',
            'status'             => 'required',
            'nominal_disetujui'  => 'nullable',
            'barang_disetujui'   => 'nullable|string|max:255',
            'nama_pic_id'        => 'required|string|max:255',
            'tipe_proses_id'     => 'required|exists:tipe_proses,id',
            'keterangan'         => 'nullable|string|max:1000',
            'overdue'            => 'required|date',
        ];

        if (!$isManual) {
            // Validasi untuk wilayah dropdown (Kab. Probolinggo / Kota Probolinggo / Kab. Situbondo)
            $rules = array_merge($rules, [
                'kabupaten_id'   => 'required',
                'kabupaten_nama' => 'required|string',
                'kecamatan_id'   => 'required',
                'kecamatan_nama' => 'required|string',
                'kelurahan_id'   => 'required',
                'kelurahan_nama' => 'required|string',
            ]);
        } else {
            // Validasi untuk wilayah "Kab. Lainnya" (input manual)
            $rules = array_merge($rules, [
                'kabupaten_manual' => 'required|string|max:50',
                'kecamatan_manual' => 'required|string|max:50',
                'kelurahan_manual' => 'required|string|max:50',
            ]);
        }

        $messages = [
            'contact_person.required' => 'Contact Person / No. HP Instansi wajib diisi.',
            'contact_person.regex'    => 'Contact Person / No. HP Instansi hanya boleh berisi angka.',
            'contact_person.min'      => 'Contact Person / No. HP Instansi minimal 10 digit.',
            'contact_person.max'      => 'Contact Person / No. HP Instansi maksimal 15 digit.',
        ];

       $validated = $request->validate($rules, $messages);

        // Normalisasi nominal
        $validated['nominal_pengajuan'] =
            ($request->nominal_pengajuan === '-' || empty($request->nominal_pengajuan))
            ? null
            : preg_replace('/[^0-9]/', '', $request->nominal_pengajuan);

        $validated['nominal_disetujui'] =
            ($request->nominal_disetujui === '-' || empty($request->nominal_disetujui))
            ? null
            : preg_replace('/[^0-9]/', '', $request->nominal_disetujui);

        // Sesuaikan data wilayah
        if ($isManual) {
            $validated['kabupaten_id']   = null;
            $validated['kabupaten_nama'] = $request->kabupaten_manual;
            $validated['kecamatan_id']   = null;
            $validated['kecamatan_nama'] = $request->kecamatan_manual;
            $validated['kelurahan_id']   = null;
            $validated['kelurahan_nama'] = $request->kelurahan_manual;
        }

        // Simpan proposal
        $proposal = Proposal::create($validated);

        // Tambahkan checklist sub_proses
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
        $proposal = Proposal::findOrFail($id); // Ambil data proposal berdasarkan ID

        // Ambil data relasi yang dibutuhkan untuk dropdown
        $tipologi = Tipologi::all();
        $proses   = TipeProses::all();

        // Kirim ke view edit
        return view('proposal.pengajuan.edit', compact('proposal', 'tipologi', 'proses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Wilayah dropdown ditentukan dari ada/tidaknya kabupaten_id
        // (dulu: $isAuto = $request->metode_input === 'auto';)
        $isAuto = !empty($request->kabupaten_id);

        // Validasi dasar
        $rules = [
            'judul'              => 'required|string|max:255',
            'kategori_instansi' => 'required|in:Pemerintahan,APH,TNI,Lembaga Masyarakat',
            'instansi_pengajuan' => 'required|string|max:255',
            'contact_person' => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'tanggal_disposisi'  => 'required|date',
            'nominal_pengajuan'  => 'nullable',
            'barang_pengajuan'   => 'nullable|string|max:255',
            'tipologi_id'        => 'required|exists:tipologi,id',
            'status'             => 'required',
            'nominal_disetujui'  => 'nullable',
            'barang_disetujui'   => 'nullable|string|max:255',
            'tipe_proses_id'     => 'required|exists:tipe_proses,id',
            'keterangan'         => 'nullable|string|max:1000',
            'overdue'            => 'nullable|date',
        ];

        if ($isAuto) {
            // Kalau pilih Wilayah dari dropdown → id + nama wajib
            $rules = array_merge($rules, [
                'kabupaten_id'   => 'required|string',
                'kabupaten_nama' => 'required|string',
                'kecamatan_id'   => 'required|string',
                'kecamatan_nama' => 'required|string',
                'kelurahan_id'   => 'required|string',
                'kelurahan_nama' => 'required|string',
            ]);
        } else {
            // Kalau pilih "Kab. Lainnya" → id kosong, tapi nama wajib
            $rules = array_merge($rules, [
                'kabupaten_manual' => 'required|string|max:255',
                'kecamatan_manual' => 'required|string|max:255',
                'kelurahan_manual' => 'required|string|max:255',
            ]);
        }

        $messages = [
            'contact_person.required' => 'Contact Person / No. HP Instansi wajib diisi.',
            'contact_person.regex'    => 'Contact Person / No. HP Instansi hanya boleh berisi angka.',
            'contact_person.min'      => 'Contact Person / No. HP Instansi minimal 10 digit.',
            'contact_person.max'      => 'Contact Person / No. HP Instansi maksimal 15 digit.',
        ];

        $validated = $request->validate($rules, $messages);

        // Normalisasi nominal
        $validated['nominal_pengajuan'] = $request->nominal_pengajuan
            ? preg_replace('/[^0-9]/', '', $request->nominal_pengajuan)
            : null;

        $validated['nominal_disetujui'] = $request->nominal_disetujui
            ? preg_replace('/[^0-9]/', '', $request->nominal_disetujui)
            : null;

        // Mapping manual ke field DB
        if (!$isAuto) {
            $validated['kabupaten_id']   = null;
            $validated['kabupaten_nama'] = $request->kabupaten_manual;

            $validated['kecamatan_id']   = null;
            $validated['kecamatan_nama'] = $request->kecamatan_manual;

            $validated['kelurahan_id']   = null;
            $validated['kelurahan_nama'] = $request->kelurahan_manual;
        }

        // Update proposal
        $proposal = Proposal::findOrFail($id);
        $proposal->update($validated);

        return redirect()->route('proposal.index')->with('success', 'Data proposal berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
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
