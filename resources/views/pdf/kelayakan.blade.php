<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir Kelayakan</title>
    <style>
        @page {
            /* margin: 130px 30px 60px 30px; sesuaikan margin atas agar tidak tabrakan dengan header */
            margin-top: 72pt;
            margin-bottom: 50pt;
            margin-left: 72pt;
            margin-right: 72pt;
        }

        .kop-header {
            position: fixed;
            top: 0px;
            left: 0;
            right: 0;
            text-align: left;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
            line-height: 1.5;
            padding-top: 80px;
        }

        h3 {
            text-align: center;
            margin: 4px 0;
        }

        h4 {
            text-align: center;
            margin: 4px 0;
            padding-bottom: 20px;
        }

        .header,
        .footer {
            text-align: left;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .section {
            margin-top: 10px;
        }

        .section p {
            margin: 2px 0;
        }

        .table-matriks {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        /* Hilangkan border kolom pertama (Prioritas) */
        .table-matriks td:first-child {
            border: none;
        }

        /* Hilangkan border baris terakhir (penjelasan kategori) */
        .table-matriks tr:last-child td {
            border: none;
        }

        .table-matriks th,
        .table-matriks td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .ttd {
            margin-top: 50px;
        }

        .ttd td {
            text-align: center;
            padding-top: 40px;
        }

        .kop-surat {
            font-size: 10px;
            margin-bottom: 10px;
        }

        .kop-surat table {
            border-collapse: collapse;
        }

        .kop-surat {
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .kop-surat td {
            vertical-align: top;
            padding: 2px 5px;
        }

        .kop-surat .label {
            font-weight: bold;
        }

        .kop-resmi {
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .kop-resmi td {
            vertical-align: top;
            padding: 2px 5px;
        }

        .kop-table {
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .kop-table td {
            border: 1px solid black;
            vertical-align: top;
            padding: 4px;
        }

        .kop-table tr {
            height: 30px;
        }

        .logo-cell {
            width: 15%;
            text-align: center;
            vertical-align: middle;
        }

        .judul-cell {
            width: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            line-height: 1.3;
        }

        .info-cell {
            width: 35%;
            font-size: 8px;
        }

        .section {
            margin-top: 10px;
            font-size: 12px;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif
        }

        .section p {
            margin: 2px 0;
        }

        .label,
        .separator,
        .value {
            display: table-cell;
            vertical-align: top;
        }

        .label {
            width: 200px;
            font-weight: bold;
        }

        .separator {
            width: 10px;
        }

        .value {
            width: auto;
        }

        .data-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
            line-height: 20pt;
        }

        .kategori-table {
            width: auto;
            max-width: 70%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 11px;
        }

        .kategori-table th,
        .kategori-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .ttd {
            margin-top: 40px;
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .ttd td {
            border: 1px solid black;
            /* agar terlihat jelas */
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="kop-header">
        <table class="kop-table" width="100%">
            <tr>
                <td rowspan="4" class="logo-cell">
                    <img src="{{ public_path('images/logos/logo-pln2.png') }}"
                        style="height: 0.64cm; width: 3.12cm; margin-top: 25px;">
                </td>
                <td class="judul-cell"><strong>PT PLN NUSANTARA POWER</strong></td>
                <td class="info-cell"><span style="font-size: 7px"><strong>Nomor Dokumen</strong> :
                        FMPT-328-12.5.1.a.b.e-001</span></td>
            </tr>
            <tr>
                <td class="judul-cell">PLN NP INTEGRATED MANAGEMENT SYSTEM</td>
                <td class="info-cell"><strong>Revisi</strong> : {{ str_pad($data->revisi, 2, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td rowspan="2" class="judul-cell">
                    FORMULIR ANALISIS PERMINTAAN BANTUAN PROGRAM CSR
                </td>
                <td class="info-cell"><strong>Tanggal Terbit</strong> : {{ \Carbon\Carbon::now()->format('d - m - Y') }}
                </td>
            </tr>
            <tr>
                {{-- <td class="info-cell"><strong>Halaman</strong> : 1 dari 3</td> --}}
            </tr>
        </table>
    </div>

    <h3>FORMULIR ANALISIS KELAYAKAN PERMINTAAN BANTUAN PROGRAM CSR</h3>
    <h4>PT PLN NP UNIT PEMBANGKITAN PAITON</h4>

    <div class="section">
        <div class="data-row">
            <div class="label">Nama Program</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->proposal->judul }}</div>
        </div>

        <div class="data-row">
            <div class="label">Tipologi</div>
            <div class="separator">:</div>
            <div class="value">{{ optional($data->proposal->tipologi)->deskripsi ?? '-' }}</div>
        </div>

        <div class="data-row">
            <div class="label">Dasar Pelaksanaan Program</div>
            <div class="separator">:</div>
            <div class="value">{!! nl2br(e($data->dasar_pelaksanaan)) !!}</div>
        </div>

        <div class="data-row">
            <div class="label">Latar Belakang Program</div>
            <div class="separator">:</div>
            <div class="value">{!! nl2br(e($data->latar_belakang)) !!}</div>
        </div>

        <div class="data-row">
            <div class="label">Tujuan</div>
            <div class="separator">:</div>
            <div class="value">
                @php $baris = explode("\n", $data->tujuan ?? ''); @endphp
                <table style="width:100%; border-collapse: collapse;">
                    @foreach($baris as $line)
                        @php $line = trim($line); @endphp
                        @if($line !== '')
                            @php
                                preg_match('/^(\d+[\.\)])\s*(.*)$/', $line, $m);
                                $nomor = $m[1] ?? '';
                                $isi = $m[2] ?? $line;
                            @endphp
                            <tr>
                                @if($nomor !== '')
                                    <td style="width:22px; vertical-align:top; padding:0; border:none;">{{ $nomor }}</td>
                                    <td style="vertical-align:top; padding:0; border:none;">{{ $isi }}</td>
                                @else
                                    <td colspan="2" style="vertical-align:top; padding:0; border:none;">{{ $isi }}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>

        <div class="data-row">
            <div class="label">Indikator Lingkungan</div>
            <div class="separator">:</div>
            <div class="value">{!! nl2br(e($data->indikator_lingkungan)) !!}</div>
        </div>

        <div class="data-row">
            <div class="label">Indikator Sosial</div>
            <div class="separator">:</div>
            <div class="value">
                {!! nl2br(e($data->indikator_sosial)) !!}
            </div>
        </div>

        <div class="data-row">
            <div class="label">Jumlah Penerima Manfaat</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->jumlah_penerima_manfaat }} penerima manfaat</div>
        </div>

        <div class="data-row">
            <div class="label">Asal Instansi</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->proposal->instansi_pengajuan }}</div>
        </div>

        <div class="data-row">
            <div class="label">Kategori Stakeholder</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->proposal->kategoriInstansi->nama ?? '-' }}</div>
        </div>

        <div class="data-row">
            <div class="label">Mengetahui (Pejabat Instansi)</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->pejabat_instansi }}</div>
        </div>

        <div class="data-row">
            <div class="label">Bantuan yang diajukan</div>
            <div class="separator">:</div>
            <div class="value">
                Proposal {{ $data->proposal->judul }} senilai Rp
                {{ number_format($data->proposal->nominal_pengajuan, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="page-break">
            <p><strong>Analisa Matriks</strong>:</p>
            @php
                $matriks = [
                    1 => ['Sedang', 'Tinggi', 'Tinggi', 'Ekstreme', 'Ekstreme'],
                    2 => ['Sedang', 'Sedang', 'Tinggi', 'Tinggi', 'Ekstreme'],
                    3 => ['Sedang', 'Sedang', 'Sedang', 'Tinggi', 'Tinggi'],
                    4 => ['Rendah', 'Rendah', 'Sedang', 'Sedang', 'Tinggi'],
                    5 => ['Rendah', 'Rendah', 'Rendah', 'Sedang', 'Sedang'],
                ];
                $warna = [
                    'Rendah'   => '#00b050',
                    'Sedang'   => '#ffc000',
                    'Tinggi'   => '#e36c09',
                    'Ekstreme' => '#ff0000',
                ];
                $prioritasTerpilih = (int) $data->prioritas;
                $dampakTerpilih = (int) $data->dampak;
            @endphp
            <table class="table-matriks">
                <tbody>
                    @foreach($matriks as $prioritasRow => $kolom)
                        <tr>
                            <td>Prioritas {{ $prioritasRow }}</td>
                            @foreach($kolom as $dampakIndex => $nilai)
                                @php
                                    $dampakCol = $dampakIndex + 1;
                                    $isHighlight = ($prioritasRow == $prioritasTerpilih && $dampakCol == $dampakTerpilih);
                                @endphp
                                <td style="background-color: {{ $warna[$nilai] }}; padding: 5px;">
                                    @if($isHighlight)
                                        <span style="display: inline-block; border: 2px solid #000; border-radius: 50%; padding: 2px 12px;">{{ $nilai }}</span>
                                    @else
                                        {{ $nilai }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td style="width:80px; word-wrap: break-word; white-space: normal;">Tidak ada dampak</td>
                        <td>Kecil</td>
                        <td>Sedang</td>
                        <td>Tinggi</td>
                        <td style="width:80px; word-wrap: break-word; white-space: normal;">Sangat Tinggi</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <p><strong>Keterangan:</strong></p>
            <table class="kategori-table">
                {{-- <thead>
                <tr>
                    <th style="width: 25%;">Kategori</th>
                    <th style="width: 25%">Keterangan</th>
                </tr>
            </thead> --}}
                <tbody>
                    <tr>
                        <td style="background-color: #00b050; text-align: center;">Rendah</td>
                        <td>Nilai bantuan 0% - 25%</td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffc000; text-align: center;">Sedang</td>
                        <td>Nilai bantuan 0% - 50%</td>
                    </tr>
                    <tr>
                        <td style="background-color: #e36c09; text-align: center;">Tinggi</td>
                        <td>Nilai bantuan 0% - 75%</td>
                    </tr>
                    <tr>
                        <td style="background-color: #ff0000; text-align: center;">Ekstreme</td>
                        <td>Nilai bantuan 0% - 100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="data-row">
            <div class="label">Data Terdahulu</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->data_terdahulu }}</div>
        </div>

        <div class="data-row">
            <div class="label">Nilai Bantuan yang disetujui</div>
            <div class="separator">:</div>
            <div class="value">Rp {{ number_format($data->proposal->nominal_disetujui, 0, ',', '.') }}</div>
        </div>

        <div class="data-row">
            <div class="label">Contact Person</div>
            <div class="separator">:</div>
            <div class="value">{{ $data->proposal->contact_person }}</div>
        </div>

        <div class="data-row">
            <div class="label">Catatan Khusus</div>
            <div class="separator">:</div>
            <div class="value">{!! nl2br(e($data->catatan_khusus)) !!}</div>
        </div>
    </div>

    <div class="section">
        <div class="page-break">
            <table class="ttd" width="100%">
                <tr>
                    <td colspan="5" style="text-align: center;"><strong>Paiton,
                            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: center;"><strong>Mengetahui :</strong></td>
                </tr>

                <!-- Baris Asman SDM -->
                <tr>
                    <td rowspan="3" style="width: 25%; text-align: center; font-weight: bold;">
                        Asman SDM,<br>Umum dan CSR
                    </td>
                    <td colspan="2" style="text-align: center; font-weight: bold; width: 20%;">Diterima</td>
                    <td colspan="2" style="text-align: center; font-weight: bold; width: 55%;">Arahan</td>
                </tr>
                <tr>
                    <td style="width: 10%; text-align: center;">Ya</td>
                    <td style="width: 10%; text-align: center;">Tidak</td>
                    <td colspan="2" rowspan="2" style="width: 55%;"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>

                <!-- Baris Manager -->
                <tr>
                    <td rowspan="3" style="width: 25%; text-align: center; font-weight: bold;">
                        Manager Business Support
                    </td>
                    <td colspan="2" style="text-align: center; font-weight: bold;">Diterima</td>
                    <td colspan="2" style="text-align: center; font-weight: bold;">Arahan</td>
                </tr>
                <tr>
                    <td style="text-align: center;">Ya</td>
                    <td style="text-align: center;">Tidak</td>
                    <td colspan="2" rowspan="2"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

</body>

</html>