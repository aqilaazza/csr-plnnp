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
    --blue:#2196f3;
    --blue-bg:#e5f0fd;
    --purple:#7c3aed;
    --purple-bg:#f1ebfc;
    --teal:#0f9d82;
    --teal-bg:#e3f7f2;
    --ink-900:#16202e;
    --ink-600:#59677c;
    --ink-400:#94a1b3;
    --line:#eaedf2;
    --radius:16px;
    font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    color:var(--ink-900);
    /* ---- REDESIGN: tekstur titik halus di area kosong latar dashboard ---- */
    background-image:radial-gradient(circle at 1px 1px, rgba(120,200,65,0.07) 1px, transparent 0);
    background-size:22px 22px;
  }
  .dash-modern h1,.dash-modern h2,.dash-modern h3,.dash-modern h4,.dash-modern h5,
  .dash-modern .num{font-family:'Manrope',-apple-system,sans-serif;}
  .dash-modern .card{
    border:none;border-radius:var(--radius);box-shadow:0 3px 14px rgba(22,32,46,0.06);
    transition:transform .18s ease,box-shadow .18s ease;height:100%;
    position:relative;overflow:hidden; /* REDESIGN: perlu utk garis aksen atas & biar radius rapi */
  }
  .dash-modern .card:hover{box-shadow:0 14px 30px rgba(22,32,46,0.12);transform:translateY(-3px);}
  .dash-modern .card-body{padding:1.35rem 1.5rem;}

  /* ---- REDESIGN: garis aksen warna di atas tiap card, sesuai kategori datanya ---- */
  .dash-modern .card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:4px;background:transparent;
  }
  .dash-modern .card:has(.dm-stat-top:not(.blue-value))::before{
    background:linear-gradient(90deg,var(--pln-green),var(--pln-green-dark));
  }
  .dash-modern .card:has(.dm-stat-top.blue-value)::before{
    background:linear-gradient(90deg,#2196f3,#1d63b8);
  }
    .dash-modern .card:has(.dm-progress-list)::before{
        background:linear-gradient(90deg,#78C841,#e69a1f,#e0463c);
    }
  .dash-modern .card:has(#jumlahPieChart)::before{
    background:linear-gradient(90deg,#2196f3,#7c3aed);
  }
 .dash-modern .card:has(#rincianDonut)::before{
    background:linear-gradient(90deg,var(--teal),var(--pln-green));
 }

  /* ---- filter bar ---- */
  .dm-filter-card{
    background:#fff;border-radius:var(--radius);box-shadow:0 3px 14px rgba(22,32,46,0.06);padding:16px 18px;
    position:relative;overflow:hidden; /* REDESIGN */
  }
  .dm-filter-card::before{
    /* REDESIGN: aksen tipis di atas filter bar, senada dgn hero */
    content:"";position:absolute;top:0;left:0;right:0;height:3px;
    background:linear-gradient(90deg,#155e35,var(--pln-green),var(--teal));
  }
  .dm-filter-card .form-label{font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--ink-400);margin-bottom:5px;}
  .dm-filter-card .form-select{border-radius:10px;border:1px solid var(--line);font-size:13.5px;padding:8px 12px;background:#fbfcfd;transition:border-color .15s ease;}
  .dm-filter-card .form-select:hover{border-color:#bfe3a4;}
  .dm-filter-card .form-select:focus{border-color:var(--pln-green);box-shadow:0 0 0 3px rgba(120,200,65,0.15);}
  .dm-reset-btn{border-radius:10px;font-size:13px;font-weight:600;border:1px solid var(--line);color:var(--ink-600);background:#fff;padding:8px 14px;}
  .dm-reset-btn:hover{background:#f4f6f8;color:var(--ink-900);}

  /* ---- icon chip ---- */
  /* REDESIGN: gradient halus + ring tipis biar tidak flat, warna disesuaikan per kategori */
  .dm-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
    background:linear-gradient(135deg,#eef8e6,#dcf3c8);color:var(--pln-green-dark);
    box-shadow:inset 0 0 0 1px rgba(79,156,38,0.08);
  }
  .dm-icon svg{width:20px;height:20px;}
  .dm-icon.amber{background:linear-gradient(135deg,#fdf1dd,#fbe1b8);color:#a3720f;box-shadow:inset 0 0 0 1px rgba(163,114,15,0.1);}
  .dm-icon.red{background:linear-gradient(135deg,#fbe6e4,#f7c9c5);color:var(--red);box-shadow:inset 0 0 0 1px rgba(224,70,60,0.1);}
  .dm-icon.slate{background:linear-gradient(135deg,var(--teal-bg),#c8ece2);color:var(--teal);box-shadow:inset 0 0 0 1px rgba(15,157,130,0.12);}
  .dm-icon.blue{background:linear-gradient(135deg,#e5f0fd,#c7e2fb);color:#1d63b8;box-shadow:inset 0 0 0 1px rgba(29,99,184,0.1);}
  .dm-icon.purple{background:linear-gradient(135deg,#f1ebfc,#e0d1f7);color:var(--purple);box-shadow:inset 0 0 0 1px rgba(124,58,237,0.1);}

  .dm-stat-top{display:flex;align-items:flex-start;gap:14px;}
  .dm-stat-top .label{font-size:12px;font-weight:700;color:var(--ink-400);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;}
  .dm-stat-top .value{font-size:24px;font-weight:800;line-height:1.1;color:var(--pln-green-dark);}
  .dm-stat-top.blue-value .value{color:#1d63b8;}
  .dm-stat-trend{font-size:11.5px;font-weight:700;color:var(--pln-green-dark);margin-top:8px;display:flex;align-items:center;gap:4px;}
  .dm-stat-trend svg{width:12px;height:12px;}

  /* ---- donut + legend layout ---- */
  .dm-donut-row{display:flex;align-items:center;gap:18px;}
  .dm-donut-canvas-wrap{width:150px;height:150px;flex-shrink:0;position:relative;}
  .dm-legend{flex:1;min-width:0;display:flex;flex-direction:column;gap:12px;}
  .dm-legend-item{display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:13px;}
  .dm-legend-item .l-left{display:flex;align-items:center;gap:9px;min-width:0;}
  .dm-legend-dot{
    width:9px;height:9px;border-radius:50%;flex-shrink:0;
    box-shadow:0 0 0 3px #fff, 0 0 0 4px rgba(22,32,46,.05); /* REDESIGN: ring halus biar dot lebih "hidup" */
  }
  .dm-legend-label{color:var(--ink-600);font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
  .dm-legend-value{font-weight:800;color:var(--ink-900);flex-shrink:0;white-space:nowrap;}
  .dm-legend-total{display:flex;align-items:center;justify-content:space-between;padding-top:10px;margin-top:2px;border-top:1px solid var(--line);font-size:13px;font-weight:800;}
  .dm-legend-total .dm-nominal{font-size:14px;}

  /* ---- compact variant (sudah tidak dipakai, dibiarkan agar tidak menghapus apapun) ---- */
  .dm-legend-compact .dm-legend-item{font-size:13px;}
  .dm-legend-compact .dm-legend-total{font-size:13px;padding-top:9px;}
  .dm-legend-compact .dm-legend-total .dm-nominal{font-size:14px;}

  /* ---- kolom kanan & card Rincian Nominal Disetujui menyesuaikan tinggi kolom kiri ---- */
/* .dm-right-col{display:flex;flex-direction:column;}
.dm-right-col .card.h-100{flex:1;}
.dm-right-col .card.h-100 .card-body{display:flex;flex-direction:column;flex:1;}
.dm-right-col .card.h-100 .dm-donut-row{flex:1;align-items:center;} */
.dash-modern .card.h-100 .card-body{display:flex;flex-direction:column;flex:1;}
.dash-modern .card.h-100 .dm-donut-row{align-items:flex-start;}

  /* ---- pie card ---- */
  .dm-pie-head{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:nowrap;margin-bottom:14px;}
  .dm-pie-head h5{margin:0;font-size:15px;font-weight:700;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
  #pieModeSelect.form-select{border-radius:20px;font-size:12.5px;font-weight:600;padding:6px 30px 6px 14px;border:1px solid var(--line);width:auto;flex-shrink:0;}
  #pieStatusDetailTable table, #pieBarangDetailTable table{font-size:12.5px;}
  #pieStatusDetailTable thead th, #pieBarangDetailTable thead th{color:var(--ink-400);font-weight:700;text-transform:uppercase;font-size:10.5px;letter-spacing:.3px;}
  .dm-pie-hint{font-size:11px;color:var(--ink-400);margin:10px 0 0;}
  .dm-pie-back{display:none;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--pln-green-dark);background:var(--green-bg);border:none;border-radius:20px;padding:5px 12px;margin-bottom:12px;cursor:pointer;}
  .dm-pie-back.show{display:inline-flex;}
  .dm-pie-back:hover{background:#e0f2d3;}

  /* ---- generic table polish ---- */
  .dash-modern .table{font-size:13px;margin-bottom:0;}
  .dash-modern .table thead th{
    background:linear-gradient(180deg,#f3faee,#eef3f8); /* REDESIGN: tint hijau lembut, dari abu polos */
    color:var(--ink-400);font-weight:700;text-transform:uppercase;font-size:10.8px;letter-spacing:.4px;border-bottom:1px solid var(--line);white-space:nowrap;
  }
  .dash-modern .table td{border-color:var(--line);vertical-align:middle;}
  .dash-modern .table-bordered{border-color:var(--line);}
  .dash-modern .table tbody tr:hover{background:#fafcfa;}

  .dm-loc-chip{display:inline-block;background:#eef2f8;color:#3d4c63;font-size:11.5px;padding:3px 10px;border-radius:7px;}
  .dm-nominal{font-weight:800;color:var(--pln-green-dark);white-space:nowrap;}

  /* ---- search box ---- */
  .dm-search-box{position:relative;max-width:5000px;flex:1 1 420px;margin-bottom:16px;}
  .dm-search-box svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--pln-green-dark);width:17px;height:17px;pointer-events:none;}
  .dm-search-box input{width:100%;border-radius:10px;border:1.5px solid var(--pln-green);font-size:13.5px;padding:9px 14px 9px 38px;background:var(--green-bg);transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;}
  .dm-search-box input::placeholder{color:#5a8a3f;}
  .dm-search-box input:focus{outline:none;border-color:var(--pln-green-dark);box-shadow:0 0 0 3px rgba(120,200,65,0.2);background:#fff;}
  @media (max-width:767px){
    .dm-search-box{max-width:100%;}
  }

  .dm-section-title{font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--ink-400);margin:30px 0 12px;}

  /* ---- export button ---- */
  .dm-export-btn{
    display:inline-flex;align-items:center;gap:6px;border-radius:10px;font-size:13px;font-weight:700;
    border:1px solid var(--pln-green);color:var(--pln-green-dark);background:var(--green-bg);padding:8px 14px;
    white-space:nowrap;text-decoration:none;transition:background .15s ease,color .15s ease,box-shadow .15s ease;flex-shrink:0;
    box-shadow:0 2px 8px rgba(120,200,65,.12); /* REDESIGN */
  }
  .dm-export-btn:hover{background:var(--pln-green);color:#fff;box-shadow:0 8px 18px rgba(120,200,65,.32);}
  .dm-export-btn svg{width:15px;height:15px;}

  .dm-approved-left{flex-wrap:nowrap;min-width:0;}
  .dm-approved-left .card-title,
  .dm-approved-left .badge{flex-shrink:0;}
  @media (max-width:767px){
    .dm-approved-left{flex-wrap:wrap;}
  }
  .dash-modern .badge.rounded-pill{box-shadow:0 3px 10px rgba(120,200,65,.35);} /* REDESIGN: badge jumlah lebih hidup */

  .dm-show-all-wrap{text-align:center;padding-top:14px;}
  .dm-show-all-btn{display:inline-flex;align-items:center;gap:6px;border-radius:20px;font-size:13px;font-weight:700;border:1px solid var(--line);color:var(--ink-600);background:#fff;padding:8px 18px;cursor:pointer;}
  .dm-show-all-btn:hover{background:#f4f6f8;color:var(--ink-900);}
  .dm-show-all-btn svg{width:13px;height:13px;transition:transform .2s ease;}
  .dm-show-all-btn.expanded svg{transform:rotate(180deg);}

  /* ---- PIC table wrapper ---- */
  .dm-pic-wrap{border-radius:var(--radius);overflow:hidden;box-shadow:0 3px 14px rgba(22,32,46,0.06);}
  .dm-pic-wrap .card{box-shadow:none;border-radius:0;}
  .dm-pic-wrap thead tr:first-child th{
    background:linear-gradient(90deg,#155e35,var(--pln-green-dark),var(--teal)); /* REDESIGN: tambah stop teal, senada hero */
    color:#fff;font-size:13px;letter-spacing:.6px;padding:12px;
  }

  /* ---- reminders ---- */
  .dm-reminder-card{
    background:#fff;border-radius:var(--radius);box-shadow:0 3px 14px rgba(22,32,46,0.06);overflow:hidden;
    display:flex;flex-direction:column;position:relative; /* REDESIGN: perlu utk garis aksen atas */
  }
  .dm-reminder-card::before{
    content:"";position:absolute;top:0;left:0;right:0;height:4px;
    background:linear-gradient(90deg,var(--purple),#a78bfa); /* REDESIGN */
  }
  .dm-reminder-head{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--line);}
  .dm-reminder-head .dm-icon{width:34px;height:34px;border-radius:10px;}
  .dm-reminder-head .dm-icon svg{width:17px;height:17px;}
  .dm-reminder-head span.title{font-size:12.5px;font-weight:800;color:var(--ink-600);text-transform:uppercase;letter-spacing:.4px;}
  .dm-reminder-head .dm-see-all{margin-left:auto;font-size:12px;font-weight:700;color:var(--pln-green-dark);text-decoration:none;display:inline-flex;align-items:center;gap:4px;}
  .dm-reminder-head .dm-see-all:hover{text-decoration:underline;}
  .dm-reminder-head .dm-see-all svg{width:12px;height:12px;}
  .dm-reminder-list{max-height:175px;min-height:175px;overflow-y:auto;flex:1;}
  .dm-reminder-list::-webkit-scrollbar{width:6px;}
  .dm-reminder-list::-webkit-scrollbar-thumb{background:#dfe3e9;border-radius:10px;}
  .dm-ritem{display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:inherit;border-bottom:1px solid var(--line);border-left:3px solid transparent;transition:background .15s ease;}
  .dm-ritem:last-child{border-bottom:none;}
  .dm-ritem:hover{background:#fafcfa;color:inherit;}
  .dm-ritem .dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;}
  .dm-ritem .info{min-width:0;flex:1;}
  .dm-ritem .info .judul{font-size:12.5px;font-weight:700;color:var(--ink-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
  .dm-ritem .info .berkas{font-size:11px;color:var(--ink-400);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
  .dm-ritem .tag{flex-shrink:0;font-size:11px;font-weight:800;padding:4px 11px;border-radius:20px;white-space:nowrap;}

  /* blok baris reminder: merah jika jatuh tempo hari ini, abu jika sudah terlambat */
  .dm-ritem-today{background:var(--red-bg);border-left-color:var(--red);}
  .dm-ritem-today:hover{background:#f9d9d6;}
  .dm-ritem-overdue{background:#f3f4f6;border-left-color:#9aa4b2;}
  .dm-ritem-overdue:hover{background:#eceef1;}

  .dm-tag-overdue{background:#e2e5ea;color:#5b6470;}
  .dm-tag-today{background:var(--red);color:#fff;box-shadow:0 2px 8px rgba(224,70,60,.25);}
  .dm-tag-urgent{background:var(--red-bg);color:#c0392b;}
  .dm-tag-soon{background:var(--amber-bg);color:#a3720f;}
  .dm-tag-upcoming{background:var(--blue-bg);color:#1d63b8;}
  .dm-tag-later{background:var(--green-bg);color:var(--pln-green-dark);}

  /* REDESIGN: ring glow halus di tiap dot status biar lebih "hidup" */
  .dot-overdue{background:#9aa4b2;box-shadow:0 0 0 3px rgba(154,164,178,.18);}
  .dot-today{background:var(--red);box-shadow:0 0 0 3px rgba(224,70,60,.18);}
  .dot-urgent{background:#e0463c;box-shadow:0 0 0 3px rgba(224,70,60,.14);}
  .dot-soon{background:var(--amber);box-shadow:0 0 0 3px rgba(230,154,31,.16);}
  .dot-upcoming{background:var(--blue);box-shadow:0 0 0 3px rgba(33,150,243,.16);}
  .dot-later{background:var(--pln-green);box-shadow:0 0 0 3px rgba(120,200,65,.18);}

  .dm-reminder-empty{flex:1;min-height:175px;display:flex;align-items:center;justify-content:center;color:var(--ink-400);font-size:13px;padding:24px;text-align:center;}

  /* ---- page greeting header ---- */
  /* REDESIGN: elemen signature dashboard ini — hero gradient hijau→teal + tekstur titik + blob blur lembut,
     merepresentasikan "energi" (sesuai identitas PLN Nusantara Power) */
  .dm-page-header{
    position:relative;overflow:hidden;
    margin-bottom:26px;
    padding:28px 32px;
    border-radius:20px;
    background:linear-gradient(135deg,#155e35 0%,#3fa15c 50%,#0f8f7a 100%);
    box-shadow:0 16px 34px rgba(21,94,53,0.28);
  }
  .dm-page-header::before{
    content:"";position:absolute;inset:0;
    background-image:radial-gradient(circle,rgba(255,255,255,.16) 1.5px,transparent 1.5px);
    background-size:20px 20px;
    opacity:.55;
  }
  .dm-page-header::after{
    content:"";position:absolute;
    width:280px;height:280px;
    background:radial-gradient(circle,rgba(255,255,255,.22),transparent 70%);
    border-radius:50%;
    top:-100px;right:-70px;
  }
  .dm-page-header .greeting{position:relative;z-index:1;font-size:14px;color:rgba(255,255,255,.85);font-weight:500;margin-bottom:2px;}
  .dm-page-header h1{position:relative;z-index:1;font-size:26px;font-weight:800;color:#fff;margin:0 0 6px;}
  .dm-page-header p{position:relative;z-index:1;font-size:13.5px;color:rgba(255,255,255,.82);margin:0;}

  @media (max-width:767px){
    .dm-filter-card .row > div{margin-bottom:8px;}
    .dm-donut-row{flex-direction:column;align-items:stretch;}
    .dm-page-header{padding:22px 20px;}
  }

  /* ---- progress bar Total Proposal (versi list per kategori) ---- */
    .dm-progress-total{font-family:'Manrope',sans-serif;font-weight:800;font-size:32px;color:var(--ink-900);line-height:1;margin-bottom:20px;}
    .dm-progress-list{display:flex;flex-direction:column;gap:18px;}
    .dm-progress-item-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;}
    .dm-progress-label{font-size:13.5px;font-weight:700;color:var(--ink-900);}
    .dm-progress-value{font-size:13.5px;font-weight:800;color:var(--ink-900);}
    .dm-progress-track{width:100%;height:8px;border-radius:20px;background:#eef0f3;overflow:hidden;}
    .dm-progress-fill{height:100%;border-radius:20px;transition:width .3s ease;}
</style>

<div class="dash-modern">

    <!-- ================= PAGE GREETING ================= -->
    <div class="dm-page-header">
        <div class="greeting">Selamat datang,</div>
        <h1>{{ Auth::user()->nama }} 👋</h1>
        <p>Semoga hari Anda produktif! Mari bersama wujudkan proses pengajuan yang lebih cepat, transparan, dan berdampak.</p>
    </div>

    <!-- ================= ROW 1: Filter+Stat (kiri) sejajar Reminder (kanan) ================= -->
    <div class="row g-3 mb-3 align-items-stretch">
        <div class="col-lg-8">

            <!-- Filter -->
            <div class="dm-filter-card mb-3">
                <form method="GET" action="{{ route('dashboard.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="filter-kabupaten" class="form-label">Kota / Kabupaten</label>
                            <select name="kabupaten" id="filter-kabupaten" class="form-select w-100"
                                onchange="dmUpdateFilter('kabupaten', this.value)">
                                <option value="">Semua</option>
                                @foreach ($kabupatenList as $kab)
                                    <option value="{{ $kab }}" {{ $selectedKabupaten == $kab ? 'selected' : '' }}>{{ $kab }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="filter-kecamatan" class="form-label">Kecamatan</label>
                            <select name="kecamatan" id="filter-kecamatan" class="form-select w-100"
                                onchange="dmUpdateFilter('kecamatan', this.value)">
                                <option value="">Semua</option>
                                @foreach ($kecamatanList as $kec)
                                    <option value="{{ $kec }}" {{ $selectedKecamatan == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="filter-kelurahan" class="form-label">Kelurahan / Desa</label>
                            <select name="kelurahan" id="filter-kelurahan" class="form-select w-100"
                                onchange="dmUpdateFilter('kelurahan', this.value)">
                                <option value="">Semua</option>
                                @foreach ($kelurahanList as $kel)
                                    <option value="{{ $kel }}" {{ $selectedKelurahan == $kel ? 'selected' : '' }}>{{ $kel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="filter-pic" class="form-label">Tampilkan data milik</label>
                            <select name="nama_pic" id="filter-pic" class="form-select w-100"
                                onchange="dmUpdateFilter('nama_pic', this.value)">
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

                        @if ($selectedKabupaten || $selectedKecamatan || $selectedKelurahan || $selectedNamaPic)
                            <div class="col-auto">
                                <a href="{{ route('dashboard.index') }}" class="dm-reset-btn">Reset filter</a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Stat cards -->
            <div class="row g-3">
                <div class="col-md-6">
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
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="dm-stat-top blue-value">
                                <div class="dm-icon blue">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M18 12a2 2 0 0 0 0 4h3v-4Z"/></svg>
                                </div>
                                <div>
                                    <div class="label">Nominal Disetujui</div>
                                    <div class="value">Rp{{ number_format($totalDisetujui ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <!-- Reminder -->
            <div class="dm-reminder-card h-100">
                <div class="dm-reminder-head">
                    <div class="dm-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    </div>
                    <span class="title">Reminder Jatuh Tempo</span>
                    <a href="javascript:void(0)" class="dm-see-all" id="dmOpenNotifBtn">
                        Lihat Semua
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
                @if($dashboardReminders->count())
                    <div class="dm-reminder-list">
                        @foreach($dashboardReminders as $reminder)
                        @php
                            $sisaHari = $reminder['sisaHari'];
                            if ($sisaHari < 0) {
                                $tier = 'overdue';
                                $badgeText = 'Terlambat';
                            } elseif ($sisaHari == 0) {
                                $tier = 'today';
                                $badgeText = 'Hari Ini';
                            } elseif ($sisaHari <= 2) {
                                $tier = 'urgent';
                                $badgeText = 'H-' . $sisaHari;
                            } elseif ($sisaHari <= 6) {
                                $tier = 'soon';
                                $badgeText = 'H-' . $sisaHari;
                            } elseif ($sisaHari <= 10) {
                                $tier = 'upcoming';
                                $badgeText = 'H-' . $sisaHari;
                            } else {
                                $tier = 'later';
                                $badgeText = 'H-' . $sisaHari;
                            }
                        @endphp
                            <a href="{{ route('monitoring.index', ['search' => $reminder['judul']]) }}" class="dm-ritem @if($tier === 'today') dm-ritem-today @elseif($tier === 'overdue') dm-ritem-overdue @endif">
                                <span class="dot dot-{{ $tier }}"></span>
                                <div class="info">
                                    <div class="judul">{{ $reminder['judul'] }}</div>
                                    <div class="berkas">{{ $reminder['berkas'] }} &middot; {{ $reminder['deadline']->format('d M Y') }}</div>
                                </div>
                                <span class="tag dm-tag-{{ $tier }}">{{ $badgeText }}</span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="dm-reminder-empty">Tidak ada reminder jatuh tempo saat ini.</div>
                @endif
            </div>

        </div>
    </div>

    <!-- ================= ROW 2: Total Proposal | Disetujui per Instansi | Rincian Nominal — 1 row, 3 kolom sejajar ================= -->
    <div class="row g-3 mb-4 align-items-stretch">

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-semibold" style="font-size:15px;">Total Proposal</h5>
                    @php
                        $totalProposalAll = $jumlahSetuju + $jumlahTolak + $jumlahPending;
                        $pctSetuju = $totalProposalAll > 0 ? round($jumlahSetuju / $totalProposalAll * 100, 1) : 0;
                        $pctTolak = $totalProposalAll > 0 ? round($jumlahTolak / $totalProposalAll * 100, 1) : 0;
                        $pctPending = $totalProposalAll > 0 ? round($jumlahPending / $totalProposalAll * 100, 1) : 0;
                    @endphp
                    <div class="dm-progress-total">{{ $totalProposalAll }}</div>

                    <div class="dm-progress-list">
                        <div class="dm-progress-item">
                            <div class="dm-progress-item-top">
                                <span class="dm-progress-label">Setuju</span>
                                <span class="dm-progress-value">{{ $jumlahSetuju }}</span>
                            </div>
                            <div class="dm-progress-track">
                                <div class="dm-progress-fill" style="width:{{ $pctSetuju }}%;background:#78C841;"></div>
                            </div>
                        </div>

                        <div class="dm-progress-item">
                            <div class="dm-progress-item-top">
                                <span class="dm-progress-label">Tidak Setuju</span>
                                <span class="dm-progress-value">{{ $jumlahTolak }}</span>
                            </div>
                            <div class="dm-progress-track">
                                <div class="dm-progress-fill" style="width:{{ $pctTolak }}%;background:#e0463c;"></div>
                            </div>
                        </div>

                        <div class="dm-progress-item">
                            <div class="dm-progress-item-top">
                                <span class="dm-progress-label">Pending</span>
                                <span class="dm-progress-value">{{ $jumlahPending }}</span>
                            </div>
                            <div class="dm-progress-track">
                                <div class="dm-progress-fill" style="width:{{ $pctPending }}%;background:#e69a1f;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="dm-pie-head">
                        <h5 id="pieChartTitle">Disetujui per Instansi</h5>
                        <select id="pieModeSelect" class="form-select">
                            <option value="instansi">Per Tipologi</option>
                            <option value="kategori">Per Kategori Instansi</option>
                            <option value="lokasi">Per Lokasi (Kab/Kota)</option>
                            <option value="barang">Status Barang Pengajuan</option>
                            <option value="status">Status Nominal Pengajuan</option>
                        </select>
                    </div>
                    <button type="button" id="pieBackBtn" class="dm-pie-back">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Kembali ke Kategori Instansi
                    </button>
                    <div class="dm-donut-row">
                        <div class="dm-donut-canvas-wrap">
                            <canvas id="jumlahPieChart"></canvas>
                        </div>
                        <div class="dm-legend" id="pieLegendList"></div>
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
                    <div id="pieBarangDetailTable" class="mt-3" style="display:none;">
                        <table class="table table-sm table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Jumlah Proposal</th>
                                    <th class="text-end">Jumlah Barang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pieBarangDetail as $d)
                                    <tr>
                                        <td>{{ $d['label'] }}</td>
                                        <td class="text-end">{{ $d['jumlah'] }}</td>
                                        <td class="text-end dm-nominal" style="font-size:12.5px;">{{ number_format($d['barang'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-semibold" style="font-size:15px;">Rincian Nominal Disetujui</h5>
                    @if(count($rincianDisetujui))
                        <div class="dm-donut-row">
                            <div class="dm-donut-canvas-wrap">
                                <canvas id="rincianDonut"></canvas>
                            </div>
                            <div class="dm-legend" id="rincianLegendList"></div>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada data disetujui</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Data Disetujui -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3 dm-approved-left">
                    <h5 class="card-title fw-semibold mb-0" style="font-size:15px;">Data Disetujui</h5>
                    <span class="badge rounded-pill" style="background-color:var(--pln-green);">{{ $approvedList->count() }}</span>
                    <div class="dm-search-box mb-0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" id="approvedSearchInput" placeholder="Cari instansi, lokasi, atau barang...">
                    </div>
                    <select id="filter-tahun" class="form-select" style="width:110px; min-width: 110px; border-radius:10px;border:1.5px solid var(--pln-green);background:var(--green-bg);font-size:13px;padding:9px 10px;"
                        onchange="dmUpdateFilter('tahun', this.value)">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ $selectedTahun == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="#" id="exportApprovedBtn" class="dm-export-btn mb-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                    Export Excel
                </a>
            </div>
            <div class="table-responsive" id="approvedTableWrap" style="max-height: 320px; overflow-y: auto;">
                <table class="table table-bordered align-middle mb-0">
                    <thead style="position: sticky; top: 0;">
                        <tr>
                            <th>Instansi</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th class="text-end">Nominal Disetujui</th>
                            <th>Barang Disetujui</th>
                        </tr>
                    </thead>
                    <tbody id="approvedTableBody">
                        @forelse ($approvedList as $item)
                            <tr class="approved-row">
                                <td>
                                    <div class="fw-semibold">{{ $item['instansi'] }}</div>
                                    <div class="text-muted" style="font-size:11.5px;">{{ $item['judul'] }}</div>
                                </td>
                                <td><span class="dm-loc-chip">{{ $item['lokasi'] }}</span></td>
                                <td class="text-muted" style="white-space:nowrap;">{{ $item['tanggal'] ?: '—' }}</td>
                                <td class="text-end dm-nominal">
                                    {{ $item['nominal_disetujui'] ? 'Rp' . number_format($item['nominal_disetujui'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="text-muted">{{ $item['barang_disetujui'] ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Tidak ada proposal disetujui yang cocok dengan filter ini.</td>
                            </tr>
                        @endforelse
                        <tr id="approvedNoMatchRow" style="display:none;">
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada hasil yang cocok dengan pencarian.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($approvedList->count() > 0)
                <div class="dm-show-all-wrap">
                    <button type="button" id="showAllApprovedBtn" class="dm-show-all-btn">
                        <span id="showAllApprovedLabel">Lihat Semua Data</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                </div>
            @endif
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


</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ---- Helper terpusat untuk semua filter dashboard ----
        function dmUpdateFilter(name, value) {
            const u = new URL('{{ route('dashboard.index') }}', window.location.origin);
            const p = new URLSearchParams(window.location.search);
            if (value) {
                p.set(name, value);
            } else {
                p.delete(name);
            }
            u.search = p.toString();
            window.location.href = u.toString();
        }

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

        function buildDonutChart(canvasId, rawLabels, rawData, colors, isNominal, centerLabel, onSliceClick, showCenterText = true) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;
            const ctx = canvas.getContext('2d');
            const stuckChart = Chart.getChart(canvas);
            if (stuckChart) stuckChart.destroy();
            const total = rawData.reduce((a, b) => a + b, 0);

            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: rawLabels,
                    datasets: [{
                        data: rawData,
                        backgroundColor: colors.slice(0, rawLabels.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }],
                },
                options: {
                    cutout: '68%',
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: onSliceClick || undefined,
                    onHover: (event, elements) => {
                        event.native.target.style.cursor = (onSliceClick && elements.length) ? 'pointer' : 'default';
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const v = context.raw;
                                    const pct = total > 0 ? ((v / total) * 100).toFixed(1) : 0;
                                    const valueText = isNominal ? rupiah(v) : v;
                                    return `${context.label}: ${valueText} (${pct}%)`;
                                }
                            },
                        },
                        centerTextValue: isNominal ? rupiah(total) : total,
                        centerTextLabel: centerLabel,
                    },
                },
                plugins: showCenterText ? [centerTextPlugin] : [],
            });
        }

        function buildLegend(containerId, rawLabels, rawData, colors, isNominal) {
            const el = document.getElementById(containerId);
            if (!el) return;
            const total = rawData.reduce((a, b) => a + b, 0);
            el.innerHTML = rawLabels.map((label, i) => {
                const value = rawData[i];
                const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                const valueText = isNominal ? rupiah(value) : value;
                return `<div class="dm-legend-item">
                    <div class="l-left">
                        <span class="dm-legend-dot" style="background:${colors[i]};"></span>
                        <span class="dm-legend-label">${label}</span>
                    </div>
                    <span class="dm-legend-value">${valueText} (${pct}%)</span>
                </div>`;
            }).join('');
        }


        // ---- Rincian Nominal Disetujui donut ----
        const rincianLabels = {!! json_encode($rincianDisetujui->pluck('kategori')) !!};
        const rincianData = {!! json_encode($rincianDisetujui->pluck('jumlah')) !!};
        const rincianColors = ['#78C841', '#ffcb3d', '#2196f3', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'];
        if (rincianLabels.length) {
            buildDonutChart('rincianDonut', rincianLabels, rincianData, rincianColors, true, 'nominal', null, false);
            buildLegend('rincianLegendList', rincianLabels, rincianData, rincianColors, true);
            const rincianLegendEl = document.getElementById('rincianLegendList');
            if (rincianLegendEl) {
                rincianLegendEl.insertAdjacentHTML('beforeend',
                    `<div class="dm-legend-total"><span>Total</span><span class="dm-nominal">{{ 'Rp' . number_format($totalDisetujui ?? 0, 0, ',', '.') }}</span></div>`
                );
            }
        }

        // ---- Disetujui per Instansi (mode selector + drilldown) ----
        const pieDatasets = {
            instansi: {
                title: 'Disetujui per Instansi',
                labels: {!! json_encode($pieInstansiLabels) !!},
                data: {!! json_encode($pieInstansiData) !!},
                colors: ['#78C841', '#ffcb3d', '#2196f3', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'],
                isNominal: false,
                centerLabel: 'proposal',
            },
            kategori: {
                title: 'Disetujui per Kategori Instansi',
                labels: {!! json_encode($pieKategoriLabels) !!},
                data: {!! json_encode($pieKategoriData) !!},
                ids: {!! json_encode($pieKategoriIds) !!},
                colors: ['#78C841', '#ffcb3d', '#2196f3', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'],
                isNominal: false,
                centerLabel: 'proposal',
                drillable: true,
            },
            lokasi: {
                title: 'Disetujui per Lokasi (Kab/Kota)',
                labels: {!! json_encode($pieLokasiLabels) !!},
                data: {!! json_encode($pieLokasiData) !!},
                colors: ['#78C841', '#2196f3', '#ffcb3d', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'],
                isNominal: false,
                centerLabel: 'proposal',
            },
            barang: {
                title: 'Rincian Status Barang Pengajuan',
                labels: {!! json_encode($pieBarangLabels) !!},
                data: {!! json_encode($pieBarangData) !!},
                colors: ['#78C841', '#e0463c', '#e69a1f', '#2196f3'],
                isNominal: false,
                centerLabel: 'barang',
                hasDetail: 'barang',
            },
            status: {
                title: 'Rincian Status Nominal Pengajuan',
                labels: {!! json_encode($pieStatusLabels) !!},
                data: {!! json_encode($pieStatusData) !!},
                colors: ['#78C841', '#e0463c', '#e69a1f', '#2196f3'],
                isNominal: true,
                centerLabel: 'nominal',
                hasDetail: 'status',
            },
        };

        const subInstansiDrilldown = {!! json_encode($subInstansiDrilldown) !!};

        let currentMode = 'instansi';

        function renderPie(mode) {
            currentMode = mode;
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

            buildDonutChart('jumlahPieChart', ds.labels, ds.data, ds.colors, ds.isNominal, ds.centerLabel || (ds.isNominal ? 'nominal' : 'proposal'), onSliceClick);
            buildLegend('pieLegendList', ds.labels, ds.data, ds.colors, ds.isNominal);

            document.getElementById('pieChartTitle').textContent = ds.title;
            document.getElementById('pieStatusDetailTable').style.display = ds.hasDetail === 'status' ? 'block' : 'none';
            document.getElementById('pieBarangDetailTable').style.display = ds.hasDetail === 'barang' ? 'block' : 'none';
            document.getElementById('pieBackBtn').classList.remove('show');
            document.getElementById('pieChartHint').style.display = ds.drillable ? 'block' : 'none';
        }

        function renderSubInstansiPie(kategoriId, drill) {
            const colors = ['#78C841', '#2196f3', '#ffcb3d', '#e0463c', '#9c27b0', '#00bcd4', '#f4a300'];
            buildDonutChart('jumlahPieChart', drill.labels, drill.data, colors, false, 'proposal', null);
            buildLegend('pieLegendList', drill.labels, drill.data, colors, false);

            document.getElementById('pieChartTitle').textContent = drill.title;
            document.getElementById('pieStatusDetailTable').style.display = 'none';
            document.getElementById('pieBarangDetailTable').style.display = 'none';
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

        // ---- Search filter untuk tabel "Data Disetujui" ----
        const approvedSearchInput = document.getElementById('approvedSearchInput');
        if (approvedSearchInput) {
            approvedSearchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim().toLowerCase();
                const rows = document.querySelectorAll('#approvedTableBody tr.approved-row');
                let visibleCount = 0;

                rows.forEach((row) => {
                    const matches = row.textContent.toLowerCase().includes(query);
                    row.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });

                const noMatchRow = document.getElementById('approvedNoMatchRow');
                if (noMatchRow) {
                    noMatchRow.style.display = (query && visibleCount === 0) ? '' : 'none';
                }
            });
        }

        // ---- Lihat Semua Data (expand/collapse tabel Data Disetujui) ----
        const showAllBtn = document.getElementById('showAllApprovedBtn');
        const approvedTableWrap = document.getElementById('approvedTableWrap');
        if (showAllBtn && approvedTableWrap) {
            let expanded = false;
            showAllBtn.addEventListener('click', () => {
                expanded = !expanded;
                approvedTableWrap.style.maxHeight = expanded ? 'none' : '320px';
                document.getElementById('showAllApprovedLabel').textContent = expanded ? 'Sembunyikan' : 'Lihat Semua Data';
                showAllBtn.classList.toggle('expanded', expanded);
            });
        }

        // ---- Export Excel "Data Disetujui" ----
        const exportApprovedBtn = document.getElementById('exportApprovedBtn');
        if (exportApprovedBtn) {
            exportApprovedBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const q = approvedSearchInput ? approvedSearchInput.value.trim() : '';
                const tahun = document.getElementById('filter-tahun') ? document.getElementById('filter-tahun').value : '';
                const params = new URLSearchParams();
                if (q) params.set('q', q);
                if (tahun) params.set('tahun', tahun);
                const queryStr = params.toString();
                const url = queryStr
                    ? `{{ route('dashboard.export-approved') }}?${queryStr}`
                    : `{{ route('dashboard.export-approved') }}`;
                window.location.href = url;
            });
        }

        // ---- "Lihat Semua" di card Reminder Jatuh Tempo -> buka dropdown notifikasi di header ----
        const dmOpenNotifBtn = document.getElementById('dmOpenNotifBtn');
        if (dmOpenNotifBtn) {
            dmOpenNotifBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); // <-- kunci fix: cegah event ini "bocor" ke document listener Bootstrap yang nutup dropdown

                const bellToggle = document.getElementById('notificationDropdown');
                if (!bellToggle) return;

                const dropdownInstance = bootstrap.Dropdown.getOrCreateInstance(bellToggle);
                dropdownInstance.show();

                bellToggle.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

    </script>
@endpush