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

    public function index(Request $request)
    {
        $loggedInUser = Auth::user();
        $selectedNamaPic = $request->get('nama_pic');
        $selectedKabupaten = $request->get('kabupaten');
        $selectedKecamatan = $request->get('kecamatan');
        $selectedKelurahan = $request->get('kelurahan');

        // Query Proposal dengan eager loading namaPic
        $proposalQuery = Proposal::with([
            'namaPic',
            'checklist.subProses',
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

        // Statistik
        $jumlahPengajuan = $proposal->count();
        $totalPengajuan = $proposal->sum('nominal_pengajuan');
        $totalDisetujui = $proposal->sum('nominal_disetujui');

        $jumlahSetuju = $proposal->where('status', 'setuju')->count();
        $jumlahTolak = $proposal->where('status', 'tolak')->count();
        $jumlahPending = $proposal->where('status', 'pending')->count();

        // Rincian Disetujui per tipologi
        $rincianDisetujui = DB::table('tipologi')
            ->leftJoin('proposal', function ($join) use ($selectedNamaPic, $selectedKabupaten, $selectedKecamatan, $selectedKelurahan) {
                $join->on('proposal.tipologi_id', '=', 'tipologi.id')
                     ->where('proposal.status', '=', 'setuju');

                if ($selectedNamaPic) {
                    $join->whereIn('proposal.nama_pic_id', function ($subquery) use ($selectedNamaPic) {
                        $subquery->select('id')->from('users')->where('nama', $selectedNamaPic);
                    });
                }
                if ($selectedKabupaten) {
                    $join->whereRaw(
                        "TRIM(REGEXP_REPLACE(proposal.kabupaten_nama, '^(Kabupaten|Kota)\\\\s+', '')) = ?",
                        [$selectedKabupaten]
                    );
                }
                if ($selectedKecamatan) {
                    $join->where('proposal.kecamatan_nama', $selectedKecamatan);
                }
                if ($selectedKelurahan) {
                    $join->where('proposal.kelurahan_nama', $selectedKelurahan);
                }
            })
            ->groupBy('tipologi.id', 'tipologi.kode')
            ->select('tipologi.kode as kategori', DB::raw('COALESCE(SUM(proposal.nominal_disetujui), 0) as jumlah'))
            ->get();

        // Tipologi dan user list
        $tipologiList = DB::table('tipologi')->pluck('kode', 'id')->toArray();
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

        // ---------- Data disetujui: instansi, lokasi dinamis, nominal & barang disetujui ----------
        $approvedList = $proposal->where('status', 'setuju')->map(function ($item) {
            $locParts = array_filter([$item->kelurahan_nama, $item->kecamatan_nama, $item->kabupaten_nama]);
            return [
                'instansi' => $item->instansi_pengajuan,
                'judul' => $item->judul,
                'lokasi' => $locParts ? implode(', ', $locParts) : '-',
                'nominal_disetujui' => $item->nominal_disetujui,
                'barang_disetujui' => $item->barang_disetujui,
            ];
        })->values();

        // ---------- Pie chart 4 mode: per instansi (tipologi), per kategori instansi (+ drill-down sub instansi), per lokasi (kab/kota), total persetujuan ----------
        $approvedOnly = $proposal->where('status', 'setuju');

        // Mode 1: per tipologi (dari proposal yang disetujui)
        $byTipologi = $approvedOnly->groupBy('tipologi_id')->map->count();
        $pieInstansiLabels = [];
        $pieInstansiData = [];
        foreach ($tipologiList as $tid => $kode) {
            $pieInstansiLabels[] = $kode;
            $pieInstansiData[] = $byTipologi[$tid] ?? 0;
        }

        // Mode 2: per kategori instansi (dari proposal yang disetujui), dengan drill-down ke sub instansi
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
                continue; // sembunyikan kategori tanpa proposal disetujui, biar pie tidak penuh irisan 0
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

        // Mode 3: per lokasi kabupaten/kota (dari proposal yang disetujui)
        $byKabupaten = $approvedOnly->groupBy(function ($p) {
            return $this->normalizeKabupaten($p->kabupaten_nama) ?? 'Tidak diketahui';
        })->map->count();
        $pieLokasiLabels = $byKabupaten->keys()->values()->all();
        $pieLokasiData = $byKabupaten->values()->all();

        // Mode 4: total persetujuan - nominal disetujui vs (belum disetujui: pending + tolak)
        $nominalDisetujui = (float) $approvedOnly->sum('nominal_disetujui');
        $nominalPending = (float) $proposal->where('status', 'pending')->sum('nominal_pengajuan');
        $nominalTolak = (float) $proposal->where('status', 'tolak')->sum('nominal_pengajuan');

        $pieStatusLabels = ['Disetujui', 'Belum Disetujui'];
        $pieStatusData = [$nominalDisetujui, $nominalPending + $nominalTolak];
        $pieStatusDetail = [
            ['label' => 'Disetujui', 'jumlah' => $jumlahSetuju, 'nominal' => $nominalDisetujui],
            ['label' => 'Pending', 'jumlah' => $jumlahPending, 'nominal' => $nominalPending],
            ['label' => 'Ditolak', 'jumlah' => $jumlahTolak, 'nominal' => $nominalTolak],
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

            // data disetujui
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
            'pieStatusLabels' => $pieStatusLabels,
            'pieStatusData' => $pieStatusData,
            'pieStatusDetail' => $pieStatusDetail,
        ]);
    }
}