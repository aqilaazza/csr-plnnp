@extends('layouts.app')
@section('title', 'CSR PLN Nusantara Power UP Paiton')
@stack('scripts')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
  .dash-modern{
    --pln-green:#78C841;
    --pln-green-dark:#4f9c26;
    --green-bg:#eef8e6;
    --amber:#e69a1f;
    --amber-bg:#fdf1dd;
    --red:#e0463c;
    --red-bg:#fbe6e4;
    --ink-900:#16202e;
    --ink-600:#59677c;
    --ink-400:#94a1b3;
    --line:#eaedf2;
    --radius:16px;
    font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    color:var(--ink-900);
  }
  .dash-modern h1,.dash-modern h2,.dash-modern h3,.dash-modern h4,.dash-modern h5,
  .dash-modern .num{font-family:'Manrope',-apple-system,sans-serif;}
  .dash-modern .card{border:none;border-radius:var(--radius);box-shadow:0 3px 14px rgba(22,32,46,0.06);transition:transform .15s ease,box-shadow .15s ease;}
  .dash-modern .card:hover{box-shadow:0 10px 26px rgba(22,32,46,0.10);}
  .dash-modern .card-body{padding:1.35rem 1.5rem;}

  /* ---- filter bar ---- */
  .dm-filter-card{background:#fff;border-radius:var(--radius);box-shadow:0 3px 14px rgba(22,32,46,0.06);padding:16px 18px;margin-bottom:22px;}
  .dm-filter-card .form-label{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--ink-400);margin-bottom:5px;}
  .dm-filter-card .form-select{border-radius:10px;border:1px solid var(--line);font-size:13.5px;padding:8px 12px;background:#fbfcfd;}
  .dm-filter-card .form-select:focus{border-color:var(--pln-green);box-shadow:0 0 0 3px rgba(120,200,65,0.15);}
  .dm-reset-btn{border-radius:10px;font-size:13px;font-weight:600;border:1px solid var(--line);color:var(--ink-600);background:#fff;padding:8px 14px;}
  .dm-reset-btn:hover{background:#f4f6f8;color:var(--ink-900);}

  /* ---- icon chip ---- */
  .dm-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--green-bg);color:var(--pln-green-dark);}
  .dm-icon svg{width:20px;height:20px;}
  .dm-icon.amber{background:var(--amber-bg);color:#a3720f;}
  .dm-icon.red{background:var(--red-bg);color:var(--red);}
  .dm-icon.slate{background:#eef1f6;color:#48566b;}

  .dm-stat-top{display:flex;align-items:flex-start;gap:14px;}
  .dm-stat-top .label{font-size:12px;font-weight:700;color:var(--ink-400);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;}
  .dm-stat-top .value{font-size:24px;font-weight:800;line-height:1.1;color:var(--pln-green-dark);}

  /* ---- progress rows ---- */
  .dm-progress-row{margin-bottom:14px;}
  .dm-progress-row:last-child{margin-bottom:0;}
  .dm-progress-row .pr-head{display:flex;justify-content:space-between;align-items:center;font-size:12.5px;font-weight:600;color:var(--ink-600);margin-bottom:6px;}
  .dm-progress-row .pr-head b{color:var(--ink-900);font-weight:800;}
  .dm-bar-track{background:#eef1f5;border-radius:20px;height:12px;overflow:hidden;}
  .dm-bar-fill{height:100%;border-radius:20px;transition:width .4s ease;}
  .dm-bar-fill.green{background:linear-gradient(90deg,var(--pln-green),#a3d977);}
  .dm-bar-fill.red{background:linear-gradient(90deg,var(--red),#f08f88);}
  .dm-bar-fill.amber{background:linear-gradient(90deg,var(--amber),#f6c35f);}

  /* ---- pie card ---- */
  .dm-pie-head{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px;}
  .dm-pie-head h5{margin:0;font-size:15px;font-weight:700;}
  #pieModeSelect.form-select{border-radius:20px;font-size:12.5px;font-weight:600;padding:6px 30px 6px 14px;border:1px solid var(--line);width:auto;}
  #pieStatusDetailTable table{font-size:12.5px;}
  #pieStatusDetailTable thead th{color:var(--ink-400);font-weight:700;text-transform:uppercase;font-size:10.5px;letter-spacing:.3px;}
  .dm-pie-hint{font-size:11px;color:var(--ink-400);margin:2px 0 0;}
  .dm-pie-back{display:none;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--pln-green-dark);background:var(--green-bg);border:none;border-radius:20px;padding:5px 12px;margin-bottom:8px;cursor:pointer;}
  .dm-pie-back.show{display:inline-flex;}
  .dm-pie-back:hover{background:#e0f2d3;}

  /* ---- generic table polish ---- */
  .dash-modern .table{font-size:13px;margin-bottom:0;}
  .dash-modern .table thead th{background:#f7f9fb;color:var(--ink-400);font-weight:700;text-transform:uppercase;font-size:10.8px;letter-spacing:.4px;border-bottom:1px solid var(--line);white-space:nowrap;}
  .dash-modern .table td{border-color:var(--line);vertical-align:middle;}
  .dash-modern .table-bordered{border-color:var(--line);}
  .dash-modern .table tbody tr:hover{background:#fafcfa;}

  .dm-loc-chip{display:inline-block;background:#eef2f8;color:#3d4c63;font-size:11.5px;padding:3px 10px;border-radius:7px;}
  .dm-nominal{font-weight:800;color:var(--pln-green-dark);white-space:nowrap;}

  .dm-section-title{font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--ink-400);margin:30px 0 12px;}

  /* ---- PIC table wrapper ---- */
  .dm-pic-wrap{border-radius:var(--radius);overflow:hidden;box-shadow:0 3px 14px rgba(22,32,46,0.06);}
  .dm-pic-wrap .card{box-shadow:none;border-radius:0;}
  .dm-pic-wrap thead tr:first-child th{background:linear-gradient(90deg,var(--pln-green-dark),var(--pln-green));color:#fff;font-size:13px;letter-spacing:.6px;padding:12px;}

  /* ---- reminders ---- */
  .dm-reminder-scroll{display:flex;gap:14px;overflow-x:auto;padding:4px 2px 12px;}
  .dm-reminder-scroll::-webkit-scrollbar{height:6px;}
  .dm-reminder-scroll::-webkit-scrollbar-thumb{background:#dfe3e9;border-radius:10px;}
  .dm-rcard{flex:0 0 300px;background:#fff;border-radius:var(--radius);padding:16px;box-shadow:0 8px 22px rgba(22,32,46,0.09);border-top:3px solid var(--amber);text-decoration:none;color:inherit;display:block;transition:transform .15s ease;}
  .dm-rcard:hover{transform:translateY(-3px);color:inherit;}
  .dm-rcard.today{border-top-color:var(--red);}
  .dm-rcard-top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px;}
  .dm-rcard-badge{font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;background:var(--amber-bg);color:#a3720f;white-space:nowrap;}
  .dm-rcard-badge.today{background:var(--red-bg);color:#a3241c;}
  .dm-rcard h6{font-size:13.5px;margin:0 0 4px;font-weight:700;line-height:1.35;}
  .dm-rcard p{margin:0;font-size:12px;color:var(--ink-600);}
  .dm-rcard .berkas{margin-top:8px;font-size:11.5px;color:var(--ink-400);}
  .dm-ring text{font-family:'Manrope',sans-serif;}

  @media (max-width:767px){
    .dm-filter-card .row > div{margin-bottom:8px;}
  }
</style>

<div class="dash-modern">

    <div class="dm-filter-card">
        <form method="GET" action="{{ route('dashboard.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-auto">
                    <label for="nama_pic" class="form-label">Tampilkan data milik</label>
                    <select name="nama_pic" id="filter-pic" class="form-select" style="min-width: 190px;"
                        onchange="this.form.submit()">
                        <option value="" {{ $selectedNamaPic === null || $selectedNamaPic === '' ? 'selected' : '' }}>
                            Semua PIC
                        </option>
                        <option value="{{ auth()->user()->nama }}"
                            {{ request('nama_pic') == auth()->user()->nama ? 'selected' : '' }}>
                            {{ auth()->user()->nama }} (Saya)
                        </option>
                        @foreach ($allNamaPics as $namaPic)
                            @if ($namaPic !== auth()->user()->nama)
                                <option value="{{ $namaPic }}" {{ request('nama_pic') == $namaPic ? 'selected' : '' }}>
                                    {{ $namaPic }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <label for="filter-kabupaten" class="form-label">Kota / Kabupaten</label>
                    <select name="kabupaten" id="filter-kabupaten" class="form-select" style="min-width: 170px;"
                        onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach ($kabupatenList as $kab)
                            <option value="{{ $kab }}" {{ $selectedKabupaten == $kab ? 'selected' : '' }}>{{ $kab }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <label for="filter-kecamatan" class="form-label">Kecamatan</label>
                    <select name="kecamatan" id="filter-kecamatan" class="form-select" style="min-width: 170px;"
                        onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach ($kecamatanList as $kec)
                            <option value="{{ $kec }}" {{ $selectedKecamatan == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <label for="filter-kelurahan" class="form-label">Kelurahan / Desa</label>
                    <select name="kelurahan" id="filter-kelurahan" class="form-select" style="min-width: 170px;"
                        onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @foreach ($kelurahanList as $kel)
                            <option value="{{ $kel }}" {{ $selectedKelurahan == $kel ? 'selected' : '' }}>{{ $kel }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $selectedNamaPic)
                    <div class="col-auto">
                        <a href="{{ route('dashboard.index') }}" class="dm-reset-btn">Reset filter</a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <div class="row">
        <!-- Kolom 1: Total Pengajuan + Progress -->
        <div class="col-lg-4 col-md-6 mb-4 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-body">
                    <div class="dm-stat-top">
                        <div class="dm-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        </div>
                        <div>
                            <div class="label">Total Nominal Pengajuan</div>
                            <div class="value">Rp{{ number_format($totalPengajuan, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card flex-grow-1">
                <div class="card-body">
                    <h5 class="card-title mb-1 fw-semibold" style="font-size:15px;">Total Proposal</h5>
                    <div class="num" style="font-size:38px;font-weight:800;margin-bottom:18px;">{{ $jumlahPengajuan }}</div>

                    <div class="dm-progress-row">
                        <div class="pr-head"><span>Setuju</span><b>{{ $jumlahSetuju }}</b></div>
                        <div class="dm-bar-track">
                            <div class="dm-bar-fill green" style="width: {{ $jumlahPengajuan > 0 ? ($jumlahSetuju / $jumlahPengajuan) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="dm-progress-row">
                        <div class="pr-head"><span>Tidak Setuju</span><b>{{ $jumlahTolak }}</b></div>
                        <div class="dm-bar-track">
                            <div class="dm-bar-fill red" style="width: {{ $jumlahPengajuan > 0 ? ($jumlahTolak / $jumlahPengajuan) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="dm-progress-row">
                        <div class="pr-head"><span>Pending</span><b>{{ $jumlahPending }}</b></div>
                        <div class="dm-bar-track">
                            <div class="dm-bar-fill amber" style="width: {{ $jumlahPengajuan > 0 ? ($jumlahPending / $jumlahPengajuan) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 2: Nominal Disetujui + Pie Chart -->
        <div class="col-lg-4 col-md-6 mb-4 d-flex flex-column gap-3">
            <div class="card">
                <div class="card-body">
                    <div class="dm-stat-top">
                        <div class="dm-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M18 12a2 2 0 0 0 0 4h3v-4Z"/></svg>
                        </div>
                        <div>
                            <div class="label">Nominal Disetujui</div>
                            <div class="value">Rp{{ number_format($totalDisetujui ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card flex-grow-1">
                <div class="card-body">
                    <div class="dm-pie-head">
                        <h5 id="pieChartTitle">Disetujui per Instansi</h5>
                        <select id="pieModeSelect" class="form-select">
                            <option value="instansi">Per Instansi</option>
                            <option value="kategori">Per Kategori Instansi</option>
                            <option value="lokasi">Per Lokasi (Kab/Kota)</option>
                            <option value="status">Total Persetujuan</option>
                        </select>
                    </div>
                    <button type="button" id="pieBackBtn" class="dm-pie-back">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Kembali ke Kategori Instansi
                    </button>
                    <div id="pieChartContainer" style="height: 220px;">
                        <canvas id="jumlahPieChart"></canvas>
                    </div>
                    <p id="pieChartHint" class="dm-pie-hint" style="display:none;">Klik salah satu irisan untuk melihat rincian sub instansi.</p>
                    <div id="pieStatusDetailTable" class="mt-3" style="display:none;">
                        <table class="table table-sm table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pieStatusDetail as $d)
                                    <tr>
                                        <td>{{ $d['label'] }}</td>
                                        <td class="text-end">{{ $d['jumlah'] }}</td>
                                        <td class="text-end dm-nominal" style="font-size:12.5px;">Rp{{ number_format($d['nominal'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 3: Rincian Nominal Disetujui -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="dm-stat-top" style="margin-bottom:16px;">
                        <div class="dm-icon slate">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg>
                        </div>
                        <div>
                            <h5 class="card-title fw-semibold mb-0" style="font-size:15px;">Rincian Nominal Disetujui</h5>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rincianDisetujui as $item)
                                <tr>
                                    <td>{{ $item->kategori }}</td>
                                    <td class="text-end dm-nominal">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data disetujui</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Disetujui -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-3">
                <h5 class="card-title fw-semibold mb-0" style="font-size:15px;">Data Disetujui</h5>
                <span class="badge rounded-pill" style="background-color:var(--pln-green);">{{ $approvedList->count() }}</span>
            </div>
            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-bordered align-middle mb-0">
                    <thead style="position: sticky; top: 0;">
                        <tr>
                            <th>Instansi</th>
                            <th>Lokasi</th>
                            <th class="text-end">Nominal Disetujui</th>
                            <th>Barang Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($approvedList as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item['instansi'] }}</div>
                                    <div class="text-muted" style="font-size:11.5px;">{{ $item['judul'] }}</div>
                                </td>
                                <td><span class="dm-loc-chip">{{ $item['lokasi'] }}</span></td>
                                <td class="text-end dm-nominal">
                                    {{ $item['nominal_disetujui'] ? 'Rp' . number_format($item['nominal_disetujui'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="text-muted">{{ $item['barang_disetujui'] ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Tidak ada proposal disetujui yang cocok dengan filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel PIC -->
    <div class="dm-pic-wrap mb-4">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0">
                        <thead>
                            <tr>
                                <th colspan="{{ 2 + count($tipologiList) * 2 }}">PIC</th>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <th>Total</th>
                                @foreach ($tipologiList as $kode)
                                    <th>{{ $kode }}</th>
                                @endforeach
                                @foreach ($tipologiList as $kode)
                                    <th>{{ $kode }} (%)</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($picTable as $row)
                                <tr>
                                    <td class="fw-semibold">{{ $row['nama'] }}</td>
                                    <td>{{ $row['total'] }}</td>
                                    @foreach ($tipologiList as $kode)
                                        <td>{{ $row['jumlah'][$kode] ?? 0 }}</td>
                                    @endforeach
                                    @foreach ($tipologiList as $kode)
                                        <td>{{ $row['persen'][$kode] ?? 0 }}%</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr class="fw-bold" style="background:#f7f9fb;">
                                <td>Total</td>
                                <td>{{ collect($picTable)->sum('total') }}</td>
                                @foreach ($tipologiList as $kode)
                                    <td>{{ collect($picTable)->sum(fn($r) => $r['jumlah'][$kode] ?? 0) }}</td>
                                @endforeach
                                <td colspan="{{ count($tipologiList) }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reminder Proposal -->
    @if($dashboardReminders->count())
        <div class="dm-section-title">🔔 Reminder proposal jatuh tempo</div>
        <div class="dm-reminder-scroll">
            @foreach($dashboardReminders as $reminder)
                @php
                    $isToday = $reminder['sisaHari'] == 0;
                    $ringColor = $isToday ? '#e0463c' : '#e69a1f';
                    $ringPct = $isToday ? 1 : ($reminder['sisaHari'] == 1 ? 0.66 : 0.33);
                    $circumference = 2 * M_PI * 15;
                    $offset = $circumference * (1 - $ringPct);
                    $badgeText = $isToday ? 'Hari Ini' : ($reminder['sisaHari'] == 1 ? 'H-1' : 'H-2');
                @endphp
                <a href="{{ route('monitoring.index', ['search' => $reminder['judul']]) }}"
                   class="dm-rcard {{ $isToday ? 'today' : '' }}">
                    <div class="dm-rcard-top">
                        <span class="dm-rcard-badge {{ $isToday ? 'today' : '' }}">{{ $badgeText }} &middot; {{ $reminder['deadline']->format('d M Y') }}</span>
                        <svg class="dm-ring" width="38" height="38" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#eef1f5" stroke-width="4"/>
                            <circle cx="18" cy="18" r="15" fill="none" stroke="{{ $ringColor }}" stroke-width="4"
                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                                stroke-linecap="round" transform="rotate(-90 18 18)"/>
                            <text x="18" y="22" text-anchor="middle" font-size="11" font-weight="700" fill="{{ $ringColor }}">{{ $reminder['sisaHari'] }}</text>
                        </svg>
                    </div>
                    <h6>{{ $reminder['judul'] }}</h6>
                    <p>Menunggu penyelesaian berkas</p>
                    <div class="berkas">{{ $reminder['berkas'] }}</div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data dasar untuk tiap mode pie chart
        const pieDatasets = {
            instansi: {
                title: 'Disetujui per Instansi',
                labels: {!! json_encode($pieInstansiLabels) !!},
                data: {!! json_encode($pieInstansiData) !!},
                colors: ['#78C841', '#ffcb3d', '#2196f3', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'],
                isNominal: false,
            },
            kategori: {
                title: 'Disetujui per Kategori Instansi',
                labels: {!! json_encode($pieKategoriLabels) !!},
                data: {!! json_encode($pieKategoriData) !!},
                ids: {!! json_encode($pieKategoriIds) !!},
                colors: ['#78C841', '#ffcb3d', '#2196f3', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'],
                isNominal: false,
                drillable: true,
            },
            lokasi: {
                title: 'Disetujui per Lokasi (Kab/Kota)',
                labels: {!! json_encode($pieLokasiLabels) !!},
                data: {!! json_encode($pieLokasiData) !!},
                colors: ['#78C841', '#2196f3', '#ffcb3d', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'],
                isNominal: false,
            },
            status: {
                title: 'Total Persetujuan (Nominal)',
                labels: {!! json_encode($pieStatusLabels) !!},
                data: {!! json_encode($pieStatusData) !!},
                colors: ['#78C841', '#e0463c'],
                isNominal: true,
            },
        };

        // Data drill-down sub instansi, keyed by kategori_instansi_id.
        // Hanya kategori yang benar-benar punya daftar sub instansi yang muncul di sini.
        const subInstansiDrilldown = {!! json_encode($subInstansiDrilldown) !!};

        const ctx = document.getElementById('jumlahPieChart').getContext('2d');
        let pieChart = null;

        // State untuk mode & drill-down saat ini
        let currentMode = 'instansi';
        let inDrilldown = false;
        let drilldownParentId = null;

        function rupiah(n) {
            return 'Rp' + Number(n).toLocaleString('id-ID');
        }

        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                const { ctx: cx, chartArea } = chart;
                if (!chartArea) return;
                const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                const cxMid = (chartArea.left + chartArea.right) / 2;
                const cyMid = (chartArea.top + chartArea.bottom) / 2;
                cx.save();
                cx.textAlign = 'center';
                cx.textBaseline = 'middle';
                cx.fillStyle = '#16202e';
                cx.font = '800 20px Manrope, sans-serif';
                const mainText = chart.config.options.plugins.centerTextValue || total;
                cx.fillText(mainText, cxMid, cyMid - 8);
                cx.fillStyle = '#94a1b3';
                cx.font = '600 10px Inter, sans-serif';
                cx.fillText(chart.config.options.plugins.centerTextLabel || 'total', cxMid, cyMid + 13);
                cx.restore();
            }
        };

        function buildLabels(rawLabels, rawData, isNominal) {
            const total = rawData.reduce((a, b) => a + b, 0);
            return rawLabels.map((label, i) => {
                const value = rawData[i];
                const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                const valueText = isNominal ? rupiah(value) : value;
                return `${label}: ${valueText} (${percent}%)`;
            });
        }

        function drawChart(rawLabels, rawData, colors, isNominal, centerLabel, onSliceClick) {
            const labels = buildLabels(rawLabels, rawData, isNominal);
            const total = rawData.reduce((a, b) => a + b, 0);

            if (pieChart) pieChart.destroy();
            pieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: rawData,
                        backgroundColor: colors.slice(0, rawLabels.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }],
                },
                options: {
                    cutout: '66%',
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: onSliceClick || undefined,
                    onHover: (event, elements) => {
                        event.native.target.style.cursor = (onSliceClick && elements.length) ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { color: '#59677c', font: { size: 10.5 }, boxWidth: 10, padding: 10 },
                        },
                        tooltip: {
                            callbacks: { label: (context) => `${context.label}` },
                        },
                        centerTextValue: isNominal ? rupiah(total) : total,
                        centerTextLabel: centerLabel,
                    },
                },
                plugins: [centerTextPlugin],
            });
        }

        function renderPie(mode) {
            currentMode = mode;
            inDrilldown = false;
            drilldownParentId = null;

            const ds = pieDatasets[mode];

            let onSliceClick = null;
            if (ds.drillable) {
                onSliceClick = (evt, elements) => {
                    if (!elements.length) return;
                    const idx = elements[0].index;
                    const kategoriId = ds.ids[idx];
                    const drill = subInstansiDrilldown[kategoriId];
                    if (drill) {
                        renderSubInstansiPie(kategoriId, drill);
                    }
                };
            }

            drawChart(ds.labels, ds.data, ds.colors, ds.isNominal, ds.isNominal ? 'nominal' : 'proposal', onSliceClick);

            document.getElementById('pieChartTitle').textContent = ds.title;
            document.getElementById('pieStatusDetailTable').style.display = mode === 'status' ? 'block' : 'none';
            document.getElementById('pieBackBtn').classList.remove('show');
            document.getElementById('pieChartHint').style.display = ds.drillable ? 'block' : 'none';
        }

        function renderSubInstansiPie(kategoriId, drill) {
            inDrilldown = true;
            drilldownParentId = kategoriId;

            const colors = ['#78C841', '#2196f3', '#ffcb3d', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'];
            drawChart(drill.labels, drill.data, colors, false, 'proposal', null);

            document.getElementById('pieChartTitle').textContent = drill.title;
            document.getElementById('pieStatusDetailTable').style.display = 'none';
            document.getElementById('pieBackBtn').classList.add('show');
            document.getElementById('pieChartHint').style.display = 'none';
        }

        document.getElementById('pieModeSelect').addEventListener('change', (e) => {
            renderPie(e.target.value);
        });

        document.getElementById('pieBackBtn').addEventListener('click', () => {
            renderPie(currentMode);
        });

        renderPie('instansi');
    </script>
@endpush