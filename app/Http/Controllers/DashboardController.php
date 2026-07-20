<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;

class DashboardController extends Controller
{
    /**
     * Rapikan "Kabupaten X" / "Kota X" / "X" jadi satu nama grup ("X")
     * supaya filter lokasi tidak pecah jadi entri ganda.
     */
    private function normalizeKabupaten(?string $kabupaten): ?string
    {
        if (!$kabupaten) {
            return null;
        }
        return trim(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $kabupaten));
    }

    /**
     * Ambil angka PERTAMA yang muncul dalam sebuah teks (bukan gabungan semua digit).
     * Dipakai untuk kolom teks bebas seperti jumlah_barang / barang_pengajuan yang
     * kadang berisi kalimat atau kode, supaya tidak ikut "tergabung" jadi angka raksasa
     * seperti yang terjadi kalau memakai parseNominal() (yang menggabung semua digit).
     */
    private function parseFirstNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (preg_match('/\d+(?:[.,]\d+)?/', (string) $value, $match)) {
            return (float) str_replace(',', '.', $match[0]);
        }

        return 0.0;
    }

    /**
     * Bersihkan nilai nominal dari berita_acara supaya aman dijumlahkan.
     * Kolom ini kadang diisi dengan format teks (mis. "Rp 5.000.000"),
     * jadi tidak bisa langsung dijumlahkan sebagai angka.
     */
    private function parseNominal($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        // Buang semua karakter selain digit (mis. "Rp 5.000.000" -> "5000000")
        $clean = preg_replace('/[^0-9]/', '', (string) $value);

        return $clean === '' ? 0.0 : (float) $clean;
    }

    /**
     * Gabungan teks barang disetujui dari data berita acara
     * (jumlah_barang + satuan + jenis_bantuan).
     */
    private function formatBarangBeritaAcara($beritaAcara): ?string
    {
        if (!$beritaAcara) {
            return null;
        }

        $parts = array_filter([
            $beritaAcara->jumlah_barang,
            $beritaAcara->satuan,
            $beritaAcara->jenis_bantuan,
        ], fn ($v) => $v !== null && $v !== '');

        return $parts ? implode(' ', $parts) : null;
    }

    public function index(Request $request)
    {
        $loggedInUser = Auth::user();
        $selectedNamaPic = $request->get('nama_pic');
        $selectedKabupaten = $request->get('kabupaten');
        $selectedKecamatan = $request->get('kecamatan');
        $selectedKelurahan = $request->get('kelurahan');

        // Query Proposal dengan eager loading namaPic + beritaAcara
        // (beritaAcara dipakai sebagai acuan "data diterima/disetujui")
        $proposalQuery = Proposal::with([
            'namaPic',
            'checklist.subProses',
            'beritaAcara',
        ]);

        if ($selectedNamaPic) {
            $proposalQuery->whereHas('namaPic', function ($q) use ($selectedNamaPic) {
                $q->where('nama', $selectedNamaPic);
            });
        }

        // Filter lokasi: Kabupaten/Kota (dirapikan dulu namanya), Kecamatan, Kelurahan/Desa
        if ($selectedKabupaten) {
            $proposalQuery->whereRaw(
                "TRIM(REGEXP_REPLACE(kabupaten_nama, '^(Kabupaten|Kota)\\\\s+', '')) = ?",
                [$selectedKabupaten]
            );
        }
        if ($selectedKecamatan) {
            $proposalQuery->where('kecamatan_nama', $selectedKecamatan);
        }
        if ($selectedKelurahan) {
            $proposalQuery->where('kelurahan_nama', $selectedKelurahan);
        }

        // Ambil data proposal yang difilter
        $proposal = $proposalQuery->get();

        $allNamaPics = DB::table('users')
            ->pluck('nama')
            ->toArray();

        // ---------- Opsi dropdown filter lokasi (cascading, mengikuti filter yang aktif) ----------
        $kabupatenList = DB::table('proposal')
            ->whereNotNull('kabupaten_nama')
            ->pluck('kabupaten_nama')
            ->map(fn ($k) => $this->normalizeKabupaten($k))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $kecamatanQuery = DB::table('proposal')->whereNotNull('kecamatan_nama');
        if ($selectedKabupaten) {
            $kecamatanQuery->whereRaw(
                "TRIM(REGEXP_REPLACE(kabupaten_nama, '^(Kabupaten|Kota)\\\\s+', '')) = ?",
                [$selectedKabupaten]
            );
        }
        $kecamatanList = $kecamatanQuery->pluck('kecamatan_nama')->filter()->unique()->sort()->values();

        $kelurahanQuery = DB::table('proposal')->whereNotNull('kelurahan_nama');
        if ($selectedKabupaten) {
            $kelurahanQuery->whereRaw(
                "TRIM(REGEXP_REPLACE(kabupaten_nama, '^(Kabupaten|Kota)\\\\s+', '')) = ?",
                [$selectedKabupaten]
            );
        }
        if ($selectedKecamatan) {
            $kelurahanQuery->where('kecamatan_nama', $selectedKecamatan);
        }
        $kelurahanList = $kelurahanQuery->pluck('kelurahan_nama')->filter()->unique()->sort()->values();

        // ---------- Proposal yang sudah "diterima": punya minimal 1 berita acara ----------
        $diterimaList = $proposal->filter(fn ($p) => $p->beritaAcara !== null)->values();
        $belumDiterimaList = $proposal->filter(fn ($p) => $p->beritaAcara === null)->values();

        // Statistik
        $jumlahPengajuan = $proposal->count();
        $totalPengajuan = $proposal->sum('nominal_pengajuan');

        // Nominal "diterima" sekarang diambil dari berita_acara.nominal, bukan proposal.nominal_disetujui
        $totalDisetujui = $diterimaList->sum(fn ($p) => $this->parseNominal($p->beritaAcara->nominal ?? null));

        // Catatan: bar Setuju/Tidak Setuju/Pending di kartu "Total Proposal" tetap memakai
        // kolom status proposal (keputusan approval), bukan berita acara (tahap administrasi).
        $jumlahSetuju = $proposal->where('status', 'setuju')->count();
        $jumlahTolak = $proposal->where('status', 'tolak')->count();
        $jumlahPending = $proposal->where('status', 'pending')->count();

        // Rincian Disetujui per tipologi, dihitung dari data berita acara
        $tipologiList = DB::table('tipologi')->pluck('kode', 'id')->toArray();

        $rincianDisetujui = collect($tipologiList)->map(function ($kode, $tipologiId) use ($diterimaList) {
            $jumlah = $diterimaList
                ->where('tipologi_id', $tipologiId)
                ->sum(fn ($p) => $this->parseNominal($p->beritaAcara->nominal ?? null));

            return (object) ['kategori' => $kode, 'jumlah' => $jumlah];
        })->values();

        // User list
        $picList = DB::table('users')->pluck('nama', 'id')->toArray();

        $totalPerTipologi = Proposal::when($selectedNamaPic, function ($query) use ($selectedNamaPic) {
            $query->whereHas('namaPic', function ($q) use ($selectedNamaPic) {
                $q->where('nama', $selectedNamaPic);
            });
        })
            ->select('tipologi_id', DB::raw('COUNT(*) as total'))
            ->groupBy('tipologi_id')
            ->pluck('total', 'tipologi_id');

        $jumlahPerPicTipologi = Proposal::when($selectedNamaPic, function ($query) use ($selectedNamaPic) {
            $query->whereHas('namaPic', function ($q) use ($selectedNamaPic) {
                $q->where('nama', $selectedNamaPic);
            });
        })
            ->select('nama_pic_id', 'tipologi_id', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('nama_pic_id', 'tipologi_id')
            ->get()
            ->groupBy('nama_pic_id');

        $progressPerPicTipologi = Proposal::when($selectedNamaPic, function ($query) use ($selectedNamaPic) {
            $query->whereHas('namaPic', function ($q) use ($selectedNamaPic) {
                $q->where('nama', $selectedNamaPic);
            });
        })
            ->select('nama_pic_id', 'tipologi_id', DB::raw('AVG(progress) as avg_progress'))
            ->groupBy('nama_pic_id', 'tipologi_id')
            ->get()
            ->groupBy('nama_pic_id');

        $picTable = [];
        foreach ($picList as $picId => $picNama) {
            $row = ['nama' => $picNama, 'jumlah' => [], 'persen' => [], 'total' => 0];

            foreach ($tipologiList as $tipologiId => $kode) {
                $found = isset($jumlahPerPicTipologi[$picId])
                    ? $jumlahPerPicTipologi[$picId]->firstWhere('tipologi_id', $tipologiId)
                    : null;
                $jumlah = $found ? $found->jumlah : 0;

                $foundProgress = isset($progressPerPicTipologi[$picId])
                    ? $progressPerPicTipologi[$picId]->firstWhere('tipologi_id', $tipologiId)
                    : null;
                $persen = $foundProgress ? round($foundProgress->avg_progress) : 0;

                $row['jumlah'][$kode] = $jumlah;
                $row['persen'][$kode] = $persen;
                $row['total'] += $jumlah;
            }

            $picTable[] = $row;
        }

        // ---------- Data disetujui: instansi, lokasi dinamis, nominal & barang dari berita acara ----------
        $approvedList = $diterimaList->map(function ($item) {
            $locParts = array_filter([$item->kelurahan_nama, $item->kecamatan_nama, $item->kabupaten_nama]);
            return [
                'instansi' => $item->instansi_pengajuan,
                'judul' => $item->judul,
                'lokasi' => $locParts ? implode(', ', $locParts) : '-',
                'nominal_disetujui' => $item->beritaAcara ? $this->parseNominal($item->beritaAcara->nominal) : null,
                'barang_disetujui' => $this->formatBarangBeritaAcara($item->beritaAcara),
            ];
        })->values();

        // ---------- Pie chart 4 mode ----------
        // $approvedOnly sekarang = proposal yang sudah punya berita acara (bukan lagi status='setuju')
        $approvedOnly = $diterimaList;

        // Mode 1: per tipologi (dari proposal yang sudah diterima)
        $byTipologi = $approvedOnly->groupBy('tipologi_id')->map->count();
        $pieInstansiLabels = [];
        $pieInstansiData = [];
        foreach ($tipologiList as $tid => $kode) {
            $pieInstansiLabels[] = $kode;
            $pieInstansiData[] = $byTipologi[$tid] ?? 0;
        }

        // Mode 2: per kategori instansi (dari proposal yang sudah diterima), dengan drill-down ke sub instansi
        $kategoriInstansiList = DB::table('kategori_instansi')->pluck('nama', 'id');
        $subInstansiByKategori = DB::table('sub_instansi')->get()->groupBy('kategori_instansi_id');
        $byKategoriInstansi = $approvedOnly->groupBy('kategori_instansi_id');

        $pieKategoriLabels = [];
        $pieKategoriData = [];
        $pieKategoriIds = [];
        $subInstansiDrilldown = [];

        foreach ($kategoriInstansiList as $kid => $knama) {
            $items = $byKategoriInstansi->get($kid) ?? collect();
            if ($items->isEmpty()) {
                continue; // sembunyikan kategori tanpa proposal diterima, biar pie tidak penuh irisan 0
            }

            $pieKategoriIds[] = $kid;
            $pieKategoriLabels[] = $knama;
            $pieKategoriData[] = $items->count();

            // Kalau kategori ini punya daftar sub instansi, siapkan data drill-down-nya
            $subsForKategori = $subInstansiByKategori->get($kid) ?? collect();
            if ($subsForKategori->isNotEmpty()) {
                $bySub = $items->groupBy('sub_instansi_id');

                $subLabels = [];
                $subData = [];
                foreach ($subsForKategori as $sub) {
                    $subLabels[] = $sub->nama;
                    $subData[] = $bySub->has($sub->id) ? $bySub->get($sub->id)->count() : 0;
                }

                $tanpaSub = $items->whereNull('sub_instansi_id')->count();
                if ($tanpaSub > 0) {
                    $subLabels[] = 'Tanpa Sub Instansi';
                    $subData[] = $tanpaSub;
                }

                $subInstansiDrilldown[$kid] = [
                    'title' => "Sub Instansi - {$knama}",
                    'labels' => $subLabels,
                    'data' => $subData,
                ];
            }
        }

        $tanpaKategori = $byKategoriInstansi->get(null) ?? collect();
        if ($tanpaKategori->isNotEmpty()) {
            $pieKategoriIds[] = null;
            $pieKategoriLabels[] = 'Tanpa Kategori';
            $pieKategoriData[] = $tanpaKategori->count();
        }

        // Mode 3: per lokasi kabupaten/kota (dari proposal yang sudah diterima)
        $byKabupaten = $approvedOnly->groupBy(function ($p) {
            return $this->normalizeKabupaten($p->kabupaten_nama) ?? 'Tidak diketahui';
        })->map->count();
        $pieLokasiLabels = $byKabupaten->keys()->values()->all();
        $pieLokasiData = $byKabupaten->values()->all();

        // Mode 5: Barang - total jumlah barang yang sudah diterima (ada berita acara) vs yang belum
        // Pakai parseFirstNumber (bukan parseNominal) karena jumlah_barang/barang_pengajuan
        // adalah kolom teks bebas, bukan kolom angka murni seperti nominal.
        $jumlahBarangDiterima = $diterimaList->sum(fn ($p) => $this->parseFirstNumber($p->beritaAcara->jumlah_barang ?? null));
        $jumlahBarangBelumDiterima = $belumDiterimaList->sum(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null));

        // "Jumlah proposal" untuk rincian barang HARUS dihitung dari proposal yang
        // benar-benar punya nilai jumlah_barang/barang_pengajuan (>0), bukan sekadar
        // proposal yang sudah/belum punya berita acara — supaya tidak sama persis
        // dengan angka jumlah proposal di rincian nominal.
        $jumlahProposalBarangDiterima = $diterimaList
            ->filter(fn ($p) => $this->parseFirstNumber($p->beritaAcara->jumlah_barang ?? null) > 0)
            ->count();
        $jumlahProposalBarangBelumDiterima = $belumDiterimaList
            ->filter(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null) > 0)
            ->count();

        $pieBarangLabels = ['Sudah Diterima', 'Belum Diterima'];
        $pieBarangData = [$jumlahBarangDiterima, $jumlahBarangBelumDiterima];

        // Rincian detail untuk mode "Barang", ditampilkan sebagai tabel di bawah pie chart —
        // formatnya disamakan dengan rincian mode "Total Persetujuan" (Status / Jumlah / Nilai),
        // tapi "Jumlah" di sini dihitung dari proposal yang punya jumlah barang, bukan berita acara.
        $pieBarangDetail = [
            [
                'label' => 'Sudah Diterima (Ada Berita Acara)',
                'jumlah' => $jumlahProposalBarangDiterima,
                'barang' => $jumlahBarangDiterima,
            ],
            [
                'label' => 'Belum Diterima (Belum Ada Berita Acara)',
                'jumlah' => $jumlahProposalBarangBelumDiterima,
                'barang' => $jumlahBarangBelumDiterima,
            ],
        ];

        // Mode 6: total persetujuan - nominal diterima (ada berita acara) vs belum diterima (belum ada berita acara)
        $nominalDiterima = (float) $approvedOnly->sum(fn ($p) => $this->parseNominal($p->beritaAcara->nominal ?? null));
        $nominalBelumDiterima = (float) $belumDiterimaList->sum('nominal_pengajuan');

        // "Jumlah proposal" untuk rincian nominal dihitung dari proposal yang benar-benar
        // punya nilai nominal (>0), bukan sekadar proposal yang sudah/belum punya berita acara.
        $jumlahProposalNominalDiterima = $diterimaList
            ->filter(fn ($p) => $this->parseNominal($p->beritaAcara->nominal ?? null) > 0)
            ->count();
        $jumlahProposalNominalBelumDiterima = $belumDiterimaList
            ->filter(fn ($p) => (float) ($p->nominal_pengajuan ?? 0) > 0)
            ->count();

        $pieStatusLabels = ['Sudah Diterima (Ada Berita Acara)', 'Belum Diterima'];
        $pieStatusData = [$nominalDiterima, $nominalBelumDiterima];
        $pieStatusDetail = [
            [
                'label' => 'Sudah Diterima (Ada Berita Acara)',
                'jumlah' => $jumlahProposalNominalDiterima,
                'nominal' => $nominalDiterima,
            ],
            [
                'label' => 'Belum Diterima (Belum Ada Berita Acara)',
                'jumlah' => $jumlahProposalNominalBelumDiterima,
                'nominal' => $nominalBelumDiterima,
            ],
        ];

        // ---------- Reminder proposal (checklist berikutnya jatuh tempo H-2/H-1/hari ini) ----------
        $dashboardReminders = collect();

        foreach ($proposal as $item) {
            if (($item->progress ?? 0) >= 100) {
                continue;
            }
            if (!$item->overdue) {
                continue;
            }

            $nextChecklist = $item->checklist
                ->sortBy('sub_proses_id')
                ->firstWhere('is_checked', 0);

            if (!$nextChecklist) {
                continue;
            }

            $deadline = \Carbon\Carbon::parse($item->overdue);
            $sisaHari = now()->startOfDay()->diffInDays($deadline, false);

            if ($sisaHari >= 0 && $sisaHari <= 2) {
                $dashboardReminders->push([
                    'judul' => $item->judul,
                    'berkas' => $nextChecklist->subProses->nama_sub,
                    'deadline' => $deadline,
                    'sisaHari' => $sisaHari,
                ]);
            }
        }

        $dashboardReminders = $dashboardReminders->sortBy('sisaHari')->values();

        return view('dashboard.index', [
            'proposal' => $proposal,
            'jumlahPengajuan' => $jumlahPengajuan,
            'totalPengajuan' => $totalPengajuan,
            'totalDisetujui' => $totalDisetujui,
            'jumlahSetuju' => $jumlahSetuju,
            'jumlahTolak' => $jumlahTolak,
            'jumlahPending' => $jumlahPending,
            'rincianDisetujui' => $rincianDisetujui,
            'tipologiList' => $tipologiList,
            'totalPerTipologi' => $totalPerTipologi,
            'picTable' => $picTable,
            'selectedNamaPic' => $selectedNamaPic,
            'allNamaPics' => $allNamaPics,
            'dashboardReminders' => $dashboardReminders,

            // lokasi filter
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'kelurahanList' => $kelurahanList,
            'selectedKabupaten' => $selectedKabupaten,
            'selectedKecamatan' => $selectedKecamatan,
            'selectedKelurahan' => $selectedKelurahan,

            // data disetujui (berbasis berita acara)
            'approvedList' => $approvedList,

            // pie chart 4 mode
            'pieInstansiLabels' => $pieInstansiLabels,
            'pieInstansiData' => $pieInstansiData,
            'pieKategoriLabels' => $pieKategoriLabels,
            'pieKategoriData' => $pieKategoriData,
            'pieKategoriIds' => $pieKategoriIds,
            'subInstansiDrilldown' => $subInstansiDrilldown,
            'pieLokasiLabels' => $pieLokasiLabels,
            'pieLokasiData' => $pieLokasiData,
            'pieBarangLabels' => $pieBarangLabels,
            'pieBarangData' => $pieBarangData,
            'pieBarangDetail' => $pieBarangDetail,
            'pieStatusLabels' => $pieStatusLabels,
            'pieStatusData' => $pieStatusData,
            'pieStatusDetail' => $pieStatusDetail,
        ]);
    }
}