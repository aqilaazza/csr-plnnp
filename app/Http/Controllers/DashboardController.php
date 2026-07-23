<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
     * Pecah string list yang dipisah koma (mis. "1, 1, 2, 2") jadi array per-item,
     * dengan tiap elemen di-trim. Dipakai untuk memasangkan jumlah_barang/satuan/
     * jenis_bantuan yang di database sama-sama berupa list dipisah koma.
     */
    private function splitCommaList(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        return array_map('trim', explode(',', $value));
    }

    /**
     * Anggap "-", "–", "—", atau string kosong sebagai "tidak ada nilai".
     * Beberapa baris di jumlah_barang/satuan diisi placeholder strip untuk
     * proposal bantuan dana (bukan barang fisik), jadi harus dianggap kosong,
     * bukan ikut digabung ke teks barang.
     */
    private function isBlankValue(?string $value): bool
    {
        if ($value === null) {
            return true;
        }
        $trimmed = trim($value);
        return $trimmed === '' || in_array($trimmed, ['-', '–', '—'], true);
    }

    /**
     * Gabungan teks barang disetujui dari data berita acara
     * (jumlah_barang + satuan + jenis_bantuan).
     *
     * Ketiga kolom ini masing-masing berupa list dipisah koma (mis. jumlah_barang =
     * "1, 1, 2, 2", satuan = "unit, unit, unit, unit", jenis_bantuan = "Mesin Las
     * Listrik Esab, Mesin Las Listrik Daiden, ..."). Item ke-N di tiap kolom saling
     * berpasangan, jadi harus di-zip per-index, bukan sekadar digabung mentah-mentah.
     *
     * FIX: jumlah_barang/satuan yang cuma berisi strip ("-") sekarang di-skip
     * (dianggap kosong), supaya proposal bantuan dana (yang jumlah/satuannya "-")
     * tidak menampilkan "- - <judul proposal>" di kolom Barang Disetujui.
     */
    private function formatBarangBeritaAcara($beritaAcara): ?string
    {
        if (!$beritaAcara) {
            return null;
        }

        $jumlahParts = $this->splitCommaList($beritaAcara->jumlah_barang);
        $satuanParts = $this->splitCommaList($beritaAcara->satuan);
        $jenisParts = $this->splitCommaList($beritaAcara->jenis_bantuan);

        $count = max(count($jumlahParts), count($satuanParts), count($jenisParts));
        if ($count === 0) {
            return null;
        }

        $lines = [];
        for ($i = 0; $i < $count; $i++) {
            $jumlah = $jumlahParts[$i] ?? null;
            $satuan = $satuanParts[$i] ?? null;
            $jenis = $jenisParts[$i] ?? null;

            // Kalau jumlah DAN satuan sama-sama kosong ("-"), baris ini bukan barang
            // fisik — biasanya ini proposal bantuan dana dan jenis_bantuan-nya cuma
            // berisi judul/deskripsi permohonan, bukan nama barang. Skip seluruh baris
            // (termasuk jenis_bantuan-nya), jangan cuma buang jumlah/satuannya saja.
            if ($this->isBlankValue($jumlah) && $this->isBlankValue($satuan)) {
                continue;
            }

            $line = array_filter(
                [$jumlah, $satuan, $jenis],
                fn ($v) => !$this->isBlankValue($v)
            );

            if ($line) {
                $lines[] = implode(' ', $line);
            }
        }

        return $lines ? implode(', ', $lines) : null;
    }

    /**
     * Jumlahkan semua angka yang muncul di sebuah teks. Dipakai untuk menghitung
     * "Total Barang" di export excel dari kolom Barang Disetujui yang formatnya
     * teks bebas (mis. "500 bibit Bibit Pohon Nangka, 5.000 ton Pupuk Organik"),
     * dengan menjumlahkan semua angka kuantitas yang ketemu di teks tersebut.
     */
    private function sumNumbersInText(?string $text): float
    {
        if (!$text) {
            return 0.0;
        }

        preg_match_all('/\d+(?:[.,]\d+)?/', $text, $matches);

        $sum = 0.0;
        foreach ($matches[0] as $num) {
            $sum += (float) str_replace(',', '.', $num);
        }

        return $sum;
    }

    /**
     * Bangun query Proposal dengan filter nama_pic + lokasi (kabupaten/kecamatan/kelurahan)
     * dari request. Dipakai bersama oleh index() dan exportApproved() supaya filter yang
     * sedang aktif di dashboard ikut terbawa saat export.
     */
    private function filteredProposalQuery(Request $request)
    {
        $selectedNamaPic = $request->get('nama_pic');
        $selectedKabupaten = $request->get('kabupaten');
        $selectedKecamatan = $request->get('kecamatan');
        $selectedKelurahan = $request->get('kelurahan');

        $query = Proposal::with(['namaPic', 'checklist.subProses', 'beritaAcara']);

        if ($selectedNamaPic) {
            $query->whereHas('namaPic', function ($q) use ($selectedNamaPic) {
                $q->where('nama', $selectedNamaPic);
            });
        }

        if ($selectedKabupaten) {
            $query->whereRaw(
                "TRIM(REGEXP_REPLACE(kabupaten_nama, '^(Kabupaten|Kota)\\\\s+', '')) = ?",
                [$selectedKabupaten]
            );
        }
        if ($selectedKecamatan) {
            $query->where('kecamatan_nama', $selectedKecamatan);
        }
        if ($selectedKelurahan) {
            $query->where('kelurahan_nama', $selectedKelurahan);
        }

        return $query;
    }

    /**
     * Ubah koleksi Proposal (yang sudah punya berita acara) jadi array approvedList
     * siap pakai (instansi, lokasi, nominal, barang). Dipisah dari buildApprovedList()
     * supaya bisa dipanggil baik untuk data yang di-filter (dashboard) maupun data
     * mentah tanpa filter (export excel).
     */
    private function mapToApprovedList($diterimaList)
    {
        return $diterimaList->map(function ($item) {
            $locParts = array_filter([$item->kelurahan_nama, $item->kecamatan_nama, $item->kabupaten_nama]);
            return [
                'instansi' => $item->instansi_pengajuan,
                'judul' => $item->judul,
                'lokasi' => $locParts ? implode(', ', $locParts) : '-',
                'nominal_disetujui' => $item->beritaAcara ? $this->parseNominal($item->beritaAcara->nominal) : null,
                'barang_disetujui' => $this->formatBarangBeritaAcara($item->beritaAcara),
            ];
        })->values();
    }

    /**
     * Bangun list "Data Disetujui" (proposal yang sudah punya berita acara),
     * mengikuti filter nama_pic/lokasi yang aktif di request. Dipakai untuk
     * tampilan dashboard (index()).
     */
    private function buildApprovedList(Request $request)
    {
        $proposal = $this->filteredProposalQuery($request)->get();
        $diterimaList = $proposal->filter(fn ($p) => $p->beritaAcara !== null)->values();

        return $this->mapToApprovedList($diterimaList);
    }

    /**
     * Bangun list "Data Disetujui" dari SELURUH data (tanpa filter PIC/lokasi
     * dashboard sama sekali). Dipakai khusus untuk export excel, karena export
     * sengaja dibuat TIDAK mengikuti filter dashboard — hanya mengikuti kata
     * kunci pencarian di tabel "Data Disetujui".
     */
    private function buildAllApprovedList()
    {
        $proposal = Proposal::with(['namaPic', 'beritaAcara'])->get();
        $diterimaList = $proposal->filter(fn ($p) => $p->beritaAcara !== null)->values();

        return $this->mapToApprovedList($diterimaList);
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
        $proposalQuery = $this->filteredProposalQuery($request);

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
        $approvedList = $this->buildApprovedList($request);

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
        // Kota Probolinggo, Kabupaten Probolinggo, dan Kabupaten Situbondo ditampilkan
        // sebagai slice terpisah (TIDAK digabung, beda dengan filter dropdown di atas),
        // sisanya digabung jadi "Lainnya". Dicocokkan case-insensitive langsung dari nama
        // asli (bukan hasil normalizeKabupaten) supaya Kota vs Kabupaten tetap terbedakan.
        $byKabupaten = $approvedOnly->groupBy(function ($p) {
            $raw = $p->kabupaten_nama ? strtoupper(trim($p->kabupaten_nama)) : null;

            return match ($raw) {
                'KABUPATEN PROBOLINGGO' => 'Kabupaten Probolinggo',
                'KOTA PROBOLINGGO' => 'Kota Probolinggo',
                'KABUPATEN SITUBONDO' => 'Kabupaten Situbondo',
                default => 'Lainnya',
            };
        })->map->count();
        $pieLokasiLabels = $byKabupaten->keys()->values()->all();
        $pieLokasiData = $byKabupaten->values()->all();

        // ---------- Breakdown "Belum Diterima" jadi 3 kelompok status (Ditolak / Pending / Disetujui tapi belum ada BA) ----------
        $tolakList = $belumDiterimaList->where('status', 'tolak')->values();
        $pendingList = $belumDiterimaList->where('status', 'pending')->values();
        $disetujuiBelumBAList = $belumDiterimaList->where('status', 'setuju')->values();

        // Mode 5: Barang - breakdown per status (Sudah Diterima / Ditolak / Pending / Disetujui Belum Ada BA)
        // Pakai parseFirstNumber (bukan parseNominal) karena jumlah_barang/barang_pengajuan
        // adalah kolom teks bebas, bukan kolom angka murni seperti nominal.
        $jumlahBarangDiterima = $diterimaList->sum(fn ($p) => $this->parseFirstNumber($p->beritaAcara->jumlah_barang ?? null));
        $jumlahBarangTolak = $tolakList->sum(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null));
        $jumlahBarangPending = $pendingList->sum(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null));
        $jumlahBarangDisetujuiBelumBA = $disetujuiBelumBAList->sum(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null));

        // "Jumlah proposal" tiap kelompok dihitung dari proposal yang benar-benar
        // punya nilai jumlah_barang/barang_pengajuan (>0), bukan sekadar masuk status itu.
        $jumlahProposalBarangDiterima = $diterimaList
            ->filter(fn ($p) => $this->parseFirstNumber($p->beritaAcara->jumlah_barang ?? null) > 0)
            ->count();
        $jumlahProposalBarangTolak = $tolakList
            ->filter(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null) > 0)
            ->count();
        $jumlahProposalBarangPending = $pendingList
            ->filter(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null) > 0)
            ->count();
        $jumlahProposalBarangDisetujuiBelumBA = $disetujuiBelumBAList
            ->filter(fn ($p) => $this->parseFirstNumber($p->barang_pengajuan ?? null) > 0)
            ->count();

        $pieBarangLabels = ['Sudah Diterima', 'Ditolak', 'Pending', 'Disetujui (On Going)'];
        $pieBarangData = [$jumlahBarangDiterima, $jumlahBarangTolak, $jumlahBarangPending, $jumlahBarangDisetujuiBelumBA];

        // Rincian detail untuk mode "Barang", ditampilkan sebagai tabel di bawah pie chart.
        $pieBarangDetail = [
            [
                'label' => 'Sudah Diterima (Ada Berita Acara)',
                'jumlah' => $jumlahProposalBarangDiterima,
                'barang' => $jumlahBarangDiterima,
            ],
            [
                'label' => 'Ditolak',
                'jumlah' => $jumlahProposalBarangTolak,
                'barang' => $jumlahBarangTolak,
            ],
            [
                'label' => 'Pending',
                'jumlah' => $jumlahProposalBarangPending,
                'barang' => $jumlahBarangPending,
            ],
            [
                'label' => 'Disetujui (On Going)',
                'jumlah' => $jumlahProposalBarangDisetujuiBelumBA,
                'barang' => $jumlahBarangDisetujuiBelumBA,
            ],
        ];

        // Mode 6: Nominal - breakdown per status yang sama (Sudah Diterima / Ditolak / Pending / Disetujui Belum Ada BA)
        $nominalDiterima = (float) $approvedOnly->sum(fn ($p) => $this->parseNominal($p->beritaAcara->nominal ?? null));
        $nominalTolak = (float) $tolakList->sum('nominal_pengajuan');
        $nominalPending = (float) $pendingList->sum('nominal_pengajuan');
        $nominalDisetujuiBelumBA = (float) $disetujuiBelumBAList->sum('nominal_pengajuan');

        // "Jumlah proposal" tiap kelompok dihitung dari proposal yang benar-benar
        // punya nilai nominal (>0), bukan sekadar masuk status itu.
        $jumlahProposalNominalDiterima = $diterimaList
            ->filter(fn ($p) => $this->parseNominal($p->beritaAcara->nominal ?? null) > 0)
            ->count();
        $jumlahProposalNominalTolak = $tolakList
            ->filter(fn ($p) => (float) ($p->nominal_pengajuan ?? 0) > 0)
            ->count();
        $jumlahProposalNominalPending = $pendingList
            ->filter(fn ($p) => (float) ($p->nominal_pengajuan ?? 0) > 0)
            ->count();
        $jumlahProposalNominalDisetujuiBelumBA = $disetujuiBelumBAList
            ->filter(fn ($p) => (float) ($p->nominal_pengajuan ?? 0) > 0)
            ->count();

        $pieStatusLabels = ['Sudah Diterima (Ada Berita Acara)', 'Ditolak', 'Pending', 'Disetujui (On Going)'];
        $pieStatusData = [$nominalDiterima, $nominalTolak, $nominalPending, $nominalDisetujuiBelumBA];
        $pieStatusDetail = [
            [
                'label' => 'Sudah Diterima (Ada Berita Acara)',
                'jumlah' => $jumlahProposalNominalDiterima,
                'nominal' => $nominalDiterima,
            ],
            [
                'label' => 'Ditolak',
                'jumlah' => $jumlahProposalNominalTolak,
                'nominal' => $nominalTolak,
            ],
            [
                'label' => 'Pending',
                'jumlah' => $jumlahProposalNominalPending,
                'nominal' => $nominalPending,
            ],
            [
                'label' => 'Disetujui (On Going)',
                'jumlah' => $jumlahProposalNominalDisetujuiBelumBA,
                'nominal' => $nominalDisetujuiBelumBA,
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

    /**
     * Export "Data Disetujui" ke Excel (.xlsx).
     * CATATAN: export ini SENGAJA tidak mengikuti filter nama_pic/kabupaten/
     * kecamatan/kelurahan yang aktif di dashboard — selalu mulai dari SEMUA
     * data disetujui. Satu-satunya filter yang berlaku adalah kata kunci
     * pencarian (?q=...) dari search box "Data Disetujui" (cocok dengan
     * instansi, judul, lokasi, barang, atau nominal). Kalau ?q= kosong/tidak
     * ada, export semua data disetujui tanpa terkecuali.
     */
    public function exportApproved(Request $request)
    {
        $approvedList = $this->buildAllApprovedList();

        $keyword = trim((string) $request->get('q', ''));
        if ($keyword !== '') {
            $needle = mb_strtolower($keyword);
            $approvedList = $approvedList->filter(function ($item) use ($needle) {
                $haystack = mb_strtolower(implode(' ', [
                    $item['instansi'] ?? '',
                    $item['judul'] ?? '',
                    $item['lokasi'] ?? '',
                    $item['barang_disetujui'] ?? '',
                    $item['nominal_disetujui'] ?? '',
                ]));
                return str_contains($haystack, $needle);
            })->values();
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Disetujui');

        $headers = ['Instansi', 'Judul Proposal', 'Lokasi', 'Nominal Disetujui', 'Barang Disetujui'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EEF8E6');

        $row = 2;
        foreach ($approvedList as $item) {
            $sheet->setCellValue("A{$row}", $item['instansi']);
            $sheet->setCellValue("B{$row}", $item['judul']);
            $sheet->setCellValue("C{$row}", $item['lokasi']);
            $sheet->setCellValue("D{$row}", $item['nominal_disetujui'] ?? 0);
            $sheet->setCellValue("E{$row}", $item['barang_disetujui'] ?? '-');
            $row++;
        }

        // Format kolom nominal jadi angka ribuan (untuk baris data)
        if ($row > 2) {
            $sheet->getStyle("D2:D" . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        // ---- Baris TOTAL di paling bawah: total nominal & total barang ----
        $totalNominal = $approvedList->sum(fn ($item) => $item['nominal_disetujui'] ?? 0);
        $totalBarang = $approvedList->sum(fn ($item) => $this->sumNumbersInText($item['barang_disetujui'] ?? null));

        $totalRow = $row;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
        $sheet->setCellValue("D{$totalRow}", $totalNominal);
        $sheet->setCellValue("E{$totalRow}", number_format($totalBarang, 0, ',', '.') . ' item');

        $sheet->getStyle("A{$totalRow}:E{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$totalRow}:E{$totalRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9EFC7');
        $sheet->getStyle("D{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'data-disetujui-' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}