<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara</title>

    <style>

        /* =====================================================
           PAGE / MARGIN
        ===================================================== */
        @page {
            margin-top: 4.3cm;
            margin-bottom: 2cm;
            margin-left: 2.2cm;
            margin-right: 2.2cm;
        }

        /* =====================================================
           BODY
        ===================================================== */
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.25;
            /*margin sudah diatur melalui @page */
            margin: 0;
        }

        /* =====================================================
           HEADER
        ===================================================== */
        header {
            position: fixed;
            /* Logo/header dinaikkan */
            top: 0.5cm;
            left: 2.2cm;
            right: 2.2cm;
            text-align: center;
        }

        /* =====================================================
           FOOTER
        ===================================================== */
        footer {
            position: fixed;
            bottom: 1cm;
            left: 2.2cm;
            right: 2.2cm;
            text-align: center;
        }

        /* =====================================================
           JUDUL
        ===================================================== */
        h2,
        h3 {
            text-align: center;
            margin: 0;
            font-weight: bold;
            font-size: 14px;
            line-height: 1.3;
        }

        /* =====================================================
           NOMOR BERITA ACARA
        ===================================================== */
        .nomor-ba {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin-top: 6px;
            margin-bottom: 12px;
        }

        /* =====================================================
           PARAGRAPH
        ===================================================== */
        p {
            text-align: justify;
            margin-top: 6px;
            margin-bottom: 6px;
        }

        .section {
            margin-top: 10px;
        }

        .section p {
            margin: 5px 0;
        }

        .section p.indent {
            padding-left: 4em;
            margin: 5px 0;
            line-height: 1.25;
        }

        .section p.spasi-atas {
            margin-top: 12px;
        }

        .tight-bottom {
            margin-bottom: 4px;
        }

        .tight-top {
            margin-top: 4px;
        }

        /* =====================================================
           LABEL
        ===================================================== */
        .label {
            display: inline-block;
            min-width: 80px;
        }

        .separator {
            display: inline-block;
            width: 10px;
        }

        .value {
            display: inline-block;
        }

        .content {
            margin-top: 0;
        }

        strong {
            font-weight: bold;
        }

        /* =====================================================
           TABEL BANTUAN
        ===================================================== */
        .tabel-bantuan {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .tabel-bantuan th,
        .tabel-bantuan td {
            border: 1px solid #000;
            padding: 7px;
            text-align: center;
        }

        .tabel-bantuan th {
            font-weight: bold;
        }

        /* =====================================================
           LIST CSR
        ===================================================== */
        .csr-list {
            padding-left: 1.2em;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .csr-list li {
            text-align: justify;
            margin-bottom: 8px;
        }

        .csr-list-custom {
            counter-reset: item;
            list-style-type: none;
            padding-left: 0;
            margin-left: 1.5em;
            font-size: 13px;
        }

        .csr-list-custom li {
            counter-increment: item;
            margin-bottom: 10px;
            text-align: justify;
            position: relative;
            padding-left: 2em;
            line-height: 1.25;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .csr-list-custom li::before {
            content: "(" counter(item) ")";
            position: absolute;
            left: 0;
            top: 0;
            font-weight: bold;
        }

        /* =====================================================
           TANGGAL
        ===================================================== */
        .tanggal {
            text-align: center;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            line-height: 1.2 !important;
        }

        /* =====================================================
           TANDA TANGAN
        ===================================================== */
       .ttd {
            width: calc(100% + 2cm);
            margin-left: -1cm;
            border-collapse: collapse;
            margin-top: 5px !important;
            table-layout: fixed;
        }

        .ttd td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 20px;
        }

        .spasi-ttd td {
            padding-top: 80px;
        }

    </style>
</head>

<body>

    <!-- =====================================================
         JUDUL
    ===================================================== -->
    <h2>
        BERITA ACARA SERAH TERIMA
    </h2>
    <h3>
        {{ strtoupper($data->proposal->judul) }}
    </h3>
    <p class="nomor-ba">
        {{ $nomorBeritaAcara }}
    </p>

    <!-- =====================================================
         PIHAK PERTAMA
    ===================================================== -->
    <p class="tight-bottom">
        Pada hari ini {{ \App\Helpers\DateHelper::tanggalTerbilang() }},
        yang bertanda tangan di bawah ini:
    </p>

    <div class="section tight-top">

        <p class="indent">
            <span class="label">Nama</span>
            <span class="separator">:</span>
            <span class="value">
                {{ $namaBisnisSupport }}
            </span>
        </p>
        <p class="indent">
            <span class="label">Jabatan</span>
            <span class="separator">:</span>
            <span class="value">
                {{ $jabatanBisnisSupport }}
            </span>
        </p>
        <p>
            Dalam hal ini bertindak sebagai
            {{ $jabatanBisnisSupport }}
            <strong>PT PLN Nusantara Power UP Paiton</strong>,
            yang selanjutnya disebut
            <strong>PIHAK PERTAMA</strong>.
        </p>

        <!-- =================================================
             PIHAK KEDUA
        ================================================= -->

        <p class="spasi-atas indent">
            <span class="label">Nama</span>
            <span class="separator">:</span>
            <span class="value">
                {{ $data->nama_penerima }}
            </span>
        </p>

        <p class="indent">
            <span class="label">Jabatan</span>
            <span class="separator">:</span>
            <span class="value">
                {{ $data->jabatan_penerima }}
            </span>
        </p>

        <p>
            Dalam hal ini bertindak untuk dan atas nama
            <strong>{{ $proposal->instansi_pengajuan }}</strong>,
            selanjutnya disebut
            <strong>PIHAK KEDUA</strong>.
        </p>

    </div>

    <!-- =====================================================
         BANTUAN
    ===================================================== -->
    <p>
        Dengan ini
        <strong>PIHAK PERTAMA</strong>
        menyerahkan bantuan kepada
        <strong>PIHAK KEDUA</strong>
        berupa:
    </p>

    <table class="tabel-bantuan">
        <thead>
            <tr>
                <th style="width: 40px;">
                    No
                </th>
                <th>
                    Jenis Bantuan
                </th>
                <th>
                    Jumlah
                </th>
            </tr>
        </thead>

        <tbody>

            @foreach($bantuan as $i => $item)
            <tr>
                <td style="text-align: center;">
                    {{ $i + 1 }}
                </td>

                <td style="text-align: left; padding-left: 10px;">
                    {{ $item['jenis'] }}
                </td>

                <td style="text-align: center;">
                    @if(!empty($item['nominal']))
                        Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                    @else
                        {{ $item['jumlah'] }}
                        {{ $item['satuan'] }}
                    @endif
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    <!-- =====================================================
         CSR
    ===================================================== -->
    <div class="section">

        <p>
            <strong>PIHAK PERTAMA</strong>
            menyerahkan bantuan kepada
            <strong>PIHAK KEDUA</strong>
            dengan mengedepankan azas Kepatuhan Terhadap Hukum
            dan Anti Penyuapan antara lain:
        </p>

        <ol class="csr-list-custom">

            <li>
                <strong>PARA PIHAK</strong> menyepakati bahwa pada saat melaksanakan program <i>Corporate Social
                Responsibility</i> ini berdasarkan pada prinsip itikad baik, tidak saling mempengaruhi baik langsung maupun
                tidak langsung guna memenuhi keinginannya, menerima serta bertanggungjawab atas segala keputusan yang
                ditetapkan sesuai dengan kesepakatan <strong>PARA PIHAK</strong>, menghindari serta mencegah terjadinya
                pertentangan kepentingan (<i>conflict of interest</i>), menghindari serta mencegah penyalahgunaan wewenang
                dan/atau kolusi dan/atau korupsi dengan tujuan untuk keuntungan pribadi-golongan-atau pihak lain, dan
                tidak menerima, tidak menawarkan atau tidak menjanjikan untuk memberi atau menerima hadiah, imbalan
                berupa apa saja kepada siapapun yang diketahui atau patut diduga berkaitan dengan pelaksanaan program
                <i>Corporate Social Responsibility</i> ini (penyuapan).
            </li>

            <li>
                <strong>PARA PIHAK</strong> menyepakati bahwa dalam pelaksanaan program <i>Corporate Social Responsibility</i>
                ini selalu mengambil tindakan yang cukup untuk memastikan <strong>PARA PIHAK</strong> patuh terhadap
                setiap hukum Indonesia yang berlaku, tidak terbatas pada Undang-Undang Nomor 31 Tahun 1999 Juncto
                Undang-Undang Nomor 20 Tahun 2001 tentang Pemberantasan Tindak Pidana Korupsi serta bersedia dikenakan
                sanksi berdasarkan ketentuan peraturan perundang-undangan apabila terbukti terlibat Korupsi, Kolusi,
                Nepotisme (KKN), penyuapan dan lain sebagainya.
            </li>

            <li>
                <strong>PIHAK KESATU</strong> dengan ini menjamin dalam pelaksanaan program <i>Corporate Social
                Responsibility</i> ini tidak menyalahgunakan uang dan/atau dana bantuan selain untuk tujuan sebagaimana
                diatur dalam Kesepakatan Kerjasama ini, tidak di bawah pengaruh kepentingan <strong>PIHAK KEDUA</strong>
                atau <strong>pihak lainnya</strong> dalam mengambil tindakan atau keputusan, serta tidak menerima
                kontribusi, pemberian uang, komisi politik, atau hal lainnya yang bernilai dari <strong>PIHAK KEDUA</strong>
                atau <strong>pihak lainnya</strong>.
            </li>

            <li>
                <strong>PIHAK KEDUA</strong> selaku penerima bantuan program <i>Corporate Social Responsibility</i> menjamin
                tidak akan menawarkan, menjanjikan, memberikan kontribusi, melakukan penyuapan, dan/atau memberikan
                manfaat lain kepada pegawai <strong>PIHAK KESATU</strong>, serta tidak menyalahgunakan dana bantuan
                program <i>Corporate Social Responsibility</i> tersebut selain untuk tujuan dalam Kesepakatan Kerja Sama ini.
            </li>

            <li>
                Apabila salah satu <strong>PIHAK</strong> terbukti melanggar ketentuan sebagaimana dimaksud dalam Pasal
                ini, maka Kesepakatan Kerja Sama akan berakhir.
            </li>

        </ol>

    </div>

    <!-- =====================================================
         PENUTUP
    ===================================================== -->
    <p>
        Demikian Berita Acara Serah Terima ini dibuat untuk
        dipergunakan sebagaimana mestinya.
        <br><br>
    </p>

    <!-- =====================================================
         TANGGAL
    ===================================================== -->
    <p class="tanggal">
        Paiton,
        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </p>

    <!-- =====================================================
         TANDA TANGAN
    ===================================================== -->
    <table class="ttd">

        <tr>
            <td>
                <strong>PIHAK PERTAMA</strong>
            </td>
            <td>
                <strong>PIHAK KEDUA</strong>
            </td>
        </tr>
        <tr class="spasi-ttd">
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <strong>
                    {{ $namaBisnisSupport }}
                </strong>
            </td>
            <td>
                <strong>
                    {{ $data->nama_penerima }}
                </strong>
            </td>
        </tr>

    </table>

</body>

</html>