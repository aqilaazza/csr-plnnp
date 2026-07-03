@extends('layouts.app')
@section('title', 'CSR PLN Nusantara Power UP Paiton')

@section('content')
    @push('styles')
        {{-- Select2 CSS --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        {{-- Select2 Bootstrap 4 Theme --}}
        <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
            rel="stylesheet" />
    @endpush
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-4">Input Data Proposal</h5>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data"
                                novalidate id="formProposal">
                                @csrf
                                <input type="hidden" id="kabupaten_id" name="kabupaten_id">
                                <input type="hidden" id="kabupaten_nama" name="kabupaten_nama">
                                <input type="hidden" id="kecamatan_id" name="kecamatan_id">
                                <input type="hidden" id="kecamatan_nama" name="kecamatan_nama">
                                <input type="hidden" id="kelurahan_id" name="kelurahan_id">
                                <input type="hidden" id="kelurahan_nama" name="kelurahan_nama">


                                <div class="mb-3">
                                    <label class="form-label">Judul Pengajuan</label>
                                    <input type="text" id="judul"
                                        class="form-control @error('judul') is-invalid @enderror" name="judul"
                                        value="{{ old('judul') }}" required
                                        placeholder="Contoh: Pengajuan Bantuan Dana Desa">
                                    @error('judul')
                                        <div class="invalid-feedback">Judul Pengajuan wajib diisi</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kategori Instansi</label>
                                    <select name="kategori_instansi"
                                        class="form-select @error('kategori_instansi') is-invalid @enderror"
                                        required>
                                        <option value="">-- Pilih Kategori Instansi --</option>
                                        <option value="Pemerintahan"
                                            {{ old('kategori_instansi') == 'Pemerintahan' ? 'selected' : '' }}>
                                            Pemerintahan
                                        </option>
                                        <option value="APH"
                                            {{ old('kategori_instansi') == 'APH' ? 'selected' : '' }}>
                                            APH (Polisi, Kejaksaan, Pengadilan)
                                        </option>
                                        <option value="TNI"
                                            {{ old('kategori_instansi') == 'TNI' ? 'selected' : '' }}>
                                            TNI
                                        </option>
                                        <option value="Lembaga Masyarakat"
                                            {{ old('kategori_instansi') == 'Lembaga Masyarakat' ? 'selected' : '' }}>
                                            Lembaga Masyarakat
                                        </option>
                                    </select>

                                    @error('kategori_instansi')
                                        <div class="invalid-feedback">Kategori Instansi wajib dipilih</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Instansi Pengajuan</label>
                                    <input type="text"
                                        class="form-control @error('instansi_pengajuan') is-invalid @enderror"
                                        name="instansi_pengajuan" value="{{ old('instansi_pengajuan') }}" required
                                        placeholder="Contoh: Dinas Sosial Kabupaten Malang">

                                    @error('instansi_pengajuan')
                                        <div class="invalid-feedback">Instansi Pengajuan wajib diisi</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Contact Person / No. HP Instansi</label>
                                    <input type="text"
                                        class="form-control @error('contact_person') is-invalid @enderror"
                                        name="contact_person"
                                        value="{{ old('contact_person') }}"
                                        placeholder="Contoh: 081234567890"
                                        inputmode="numeric"
                                        pattern="[0-9]+"
                                        required>

                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>



                                {{-- ===================== WILAYAH (UPDATED) ===================== --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Wilayah</label>
                                        <select id="kabupaten"
                                            class="form-select @error('kabupaten_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Wilayah --</option>
                                            <option value="3513" data-nama="Probolinggo">Kab. Probolinggo</option>
                                            <option value="3574" data-nama="Kota Probolinggo">Kota Probolinggo</option>
                                            <option value="3512" data-nama="Situbondo">Kab. Situbondo</option>
                                            <option value="lainnya">Kab / Kota Lainnya</option>
                                        </select>
                                        <div class="form-text">Pilih wilayah pengajuan.</div>
                                        @error('kabupaten_id')
                                            <div class="invalid-feedback">Kabupaten / Kota wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>

                                <div id="wilayah_auto" class="d-none">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kecamatan</label>
                                            <select id="kecamatan"
                                                class="form-select @error('kecamatan_id') is-invalid @enderror"
                                                required>
                                                <option value="">-- Pilih Kecamatan --</option>
                                            </select>
                                            <div class="form-text">Pilih kecamatan sesuai dengan wilayah pengajuan yang
                                                berada
                                                di Kabupaten.</div>
                                            @error('kecamatan_id')
                                                <div class="invalid-feedback">Kecamatan wajib diisi</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kelurahan / Desa</label>
                                            <select id="kelurahan"
                                                class="form-select @error('kelurahan_id') is-invalid @enderror"
                                                required>
                                                <option value="">-- Pilih Kelurahan / Desa --</option>
                                            </select>
                                            <div class="form-text">Pilih kelurahan atau desa yang berada di dalam
                                                kecamatan
                                                yang
                                                telah dipilih.</div>
                                            @error('kelurahan_id')
                                                <div class="invalid-feedback">Kelurahan wajib diisi</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="wilayah_manual" class="d-none">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kabupaten / Kota</label>
                                            <input type="text" id="kabupaten_manual" name="kabupaten_manual"
                                                class="form-control" placeholder="Ketik nama kabupaten/kota">
                                            <div class="form-text">
                                                Ketik nama Kabupaten atau Kota sesuai wilayah pengajuan (contoh: Malang,
                                                Surabaya).
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Kecamatan</label>
                                                <input type="text" id="kecamatan_manual" name="kecamatan_manual"
                                                    class="form-control" placeholder="Ketik nama kecamatan">
                                                <div class="form-text">
                                                    Masukkan nama Kecamatan sesuai Kabupaten/Kota (contoh: Lowokwaru).
                                                </div>

                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Kelurahan / Desa</label>
                                                <input type="text" id="kelurahan_manual" name="kelurahan_manual"
                                                    class="form-control" placeholder="Ketik nama kelurahan/desa">
                                                <div class="form-text">
                                                    Masukkan nama Kelurahan atau Desa sesuai Kecamatan (contoh: Tulusrejo).
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ===================== END WILAYAH (UPDATED) ===================== --}}

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Disposisi</label>
                                    <input type="date"
                                        class="form-control @error('tanggal_disposisi') is-invalid @enderror"
                                        name="tanggal_disposisi" value="{{ old('tanggal_disposisi') }}" required>
                                    @error('tanggal_disposisi')
                                        <div class="invalid-feedback">Tanggal Disposisi wajib diisi</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nominal Pengajuan</label>
                                        <input type="text" id="nominal_pengajuan"
                                            class="form-control @error('nominal_pengajuan') is-invalid @enderror"
                                            name="nominal_pengajuan" value="{{ old('nominal_pengajuan') }}"
                                            placeholder="Masukkan Angka">
                                        <div class="form-text">Nominal Pengajuan wajib diisi. Gunakan tanda '-'
                                            apabila data kosong.</div>
                                        @error('nominal_pengajuan')
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Barang Pengajuan</label>
                                        <input type="text"
                                            class="form-control @error('barang_pengajuan') is-invalid @enderror"
                                            name="barang_pengajuan" value="{{ old('barang_pengajuan') }}"
                                            placeholder="Contoh: 26 Papan Peringatan">
                                        <div class="form-text">Barang Pengajuan wajib diisi. Gunakan tanda '-'
                                            apabila data kosong</div>
                                        @error('barang_pengajuan')
                                        @enderror
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipologi</label>
                                        <select name="tipologi_id"
                                            class="form-control @error('tipologi_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Tipologi --</option>
                                            @foreach ($tipologi as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('tipologi_id') == $item->id ? 'selected' : '' }}>
                                                    {{ $item->kode }} - {{ $item->deskripsi }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipologi_id')
                                            <div class="invalid-feedback">Tipologi wajib diisi</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Setuju / Pending / Tolak</label>
                                        <select class="form-control @error('status') is-invalid @enderror" name="status"
                                            required>
                                            <option value="">-- Pilih Status Persetujuan --</option>
                                            <option value="setuju" {{ old('status') == 'setuju' ? 'selected' : '' }}>
                                                Setuju
                                            </option>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                                Pending
                                            </option>
                                            <option value="tolak" {{ old('status') == 'tolak' ? 'selected' : '' }}>
                                                Tolak
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">Setuju / Tidak setuju wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nominal Disetujui</label>
                                        <input type="text" id="nominal_disetujui"
                                            class="form-control @error('nominal_disetujui') is-invalid @enderror"
                                            name="nominal_disetujui" value="{{ old('nominal_disetujui') }}"
                                            placeholder="Masukkan Angka" required>
                                        <div class="form-text">Nominal Disetujui wajib diisi. Gunakan tanda '-' apabila
                                            data kosong.</div>
                                        @error('nominal_disetujui')
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Barang Disetujui</label>
                                        <input type="text"
                                            class="form-control @error('barang_disetujui') is-invalid @enderror"
                                            name="barang_disetujui" value="{{ old('barang_disetujui') }}"
                                            placeholder="Contoh: 26 Papan Peringatan">
                                        <div class="form-text">Barang Disetujui wajib diisi. Gunakan tanda '-' apabila
                                            data kosong.</div>
                                        @error('barang_disetujui')
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">PIC</label>
                                    <input type="text" class="form-control @error('nama_pic_id') is-invalid @enderror"
                                        name="nama_pic_id_display" value="{{ Auth::user()->nama }}" disabled>
                                    <input type="hidden" name="nama_pic_id" value="{{ Auth::user()->id }}">
                                    <div class="form-text">Nama PIC diatur secara otomatis sesuai dengan pengguna yang
                                        sedang login.</div>
                                    @error('nama_pic_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Proses</label>
                                    <select name="tipe_proses_id"
                                        class="form-control @error('tipe_proses_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Proses --</option>
                                        @foreach ($proses as $item)
                                            @php
                                                $subList = $item->subProses->pluck('nama_sub')->implode(' - ');
                                                $label = $item->nama . ($subList ? " ($subList)" : '');
                                            @endphp
                                            <option value="{{ $item->id }}"
                                                {{ old('tipe_proses_id') == $item->id ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipe_proses_id')
                                        <div class="invalid-feedback">Proses wajib diisi</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                                        name="keterangan" value="{{ old('keterangan') }}"
                                        placeholder="Contoh: Disetujui sebagian karena keterbatasan anggaran">
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deadline</label>
                                    <input type="date" name="overdue"
                                        class="form-control @error('overdue') is-invalid @enderror" required>
                                    @error('overdue')
                                        <div class="invalid-feedback">Deadline wajib diisi</div>
                                    @enderror


                                    <button type="submit" style="background-color: #78C841; color: white;"
                                        class="btn mt-3">
                                        Submit
                                    </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Select2 JS --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        {{-- ===================== WILAYAH SCRIPT (UPDATED) ===================== --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const kabupatenSelect = document.getElementById('kabupaten');
                const kecamatanSelect = document.getElementById('kecamatan');
                const kelurahanSelect = document.getElementById('kelurahan');

                const wilayahAuto = document.getElementById('wilayah_auto');
                const wilayahManual = document.getElementById('wilayah_manual');

                const kabupatenManualInput = document.getElementById('kabupaten_manual');
                const kecamatanManualInput = document.getElementById('kecamatan_manual');
                const kelurahanManualInput = document.getElementById('kelurahan_manual');

                const kabupatenIdInput = document.getElementById('kabupaten_id');
                const kabupatenNamaInput = document.getElementById('kabupaten_nama');
                const kecamatanIdInput = document.getElementById('kecamatan_id');
                const kecamatanNamaInput = document.getElementById('kecamatan_nama');
                const kelurahanIdInput = document.getElementById('kelurahan_id');
                const kelurahanNamaInput = document.getElementById('kelurahan_nama');

                kabupatenSelect.addEventListener('change', function() {
                    const selectedId = this.value;
                    const selectedNama = this.options[this.selectedIndex].getAttribute('data-nama');

                    // Reset kecamatan & kelurahan setiap kali wilayah berganti
                    kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                    kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
                    kecamatanIdInput.value = '';
                    kecamatanNamaInput.value = '';
                    kelurahanIdInput.value = '';
                    kelurahanNamaInput.value = '';

                    if (kabupatenManualInput) kabupatenManualInput.value = '';
                    if (kecamatanManualInput) kecamatanManualInput.value = '';
                    if (kelurahanManualInput) kelurahanManualInput.value = '';

                    wilayahAuto.classList.add('d-none');
                    wilayahManual.classList.add('d-none');

                    if (selectedId === 'lainnya') {
                        kabupatenIdInput.value = '';
                        kabupatenNamaInput.value = '';
                        wilayahManual.classList.remove('d-none');
                    } else if (selectedId) {
                        kabupatenIdInput.value = selectedId;
                        kabupatenNamaInput.value = selectedNama;
                        wilayahAuto.classList.remove('d-none');

                        fetch(`/kecamatan/${selectedId}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(item => {
                                    const option = new Option(item.name, item.id);
                                    option.setAttribute('data-name', item.name);
                                    kecamatanSelect.add(option);
                                });
                            });
                    } else {
                        kabupatenIdInput.value = '';
                        kabupatenNamaInput.value = '';
                    }
                });

                kecamatanSelect.addEventListener('change', function() {
                    const kecamatanId = this.value;
                    const kecamatanNama = this.options[this.selectedIndex].getAttribute('data-name');

                    kecamatanIdInput.value = kecamatanId;
                    kecamatanNamaInput.value = kecamatanNama;

                    kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan / Desa --</option>';
                    kelurahanIdInput.value = '';
                    kelurahanNamaInput.value = '';

                    if (kecamatanId) {
                        fetch(`/kelurahan/${kecamatanId}`)
                            .then(response => response.json())
                            .then(data => {
                                data.forEach(item => {
                                    const option = new Option(item.name, item.id);
                                    option.setAttribute('data-name', item.name);
                                    kelurahanSelect.add(option);
                                });
                            });
                    }
                });

                kelurahanSelect.addEventListener('change', function() {
                    const kelurahanId = this.value;
                    const kelurahanNama = this.options[this.selectedIndex].getAttribute('data-name');

                    kelurahanIdInput.value = kelurahanId;
                    kelurahanNamaInput.value = kelurahanNama;
                });

                // Kalau pakai wilayah manual, kabupaten_id/kecamatan_id/kelurahan_id dikosongkan
                // dan hidden input *_nama diisi dari input manual saat submit.
                document.getElementById('formProposal').addEventListener('submit', function() {
                    if (kabupatenSelect.value === 'lainnya') {
                        kabupatenNamaInput.value = kabupatenManualInput ? kabupatenManualInput.value : '';
                        kecamatanNamaInput.value = kecamatanManualInput ? kecamatanManualInput.value : '';
                        kelurahanNamaInput.value = kelurahanManualInput ? kelurahanManualInput.value : '';
                    }
                });
            });
        </script>
        {{-- ===================== END WILAYAH SCRIPT (UPDATED) ===================== --}}

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Format Rupiah
                const inputPengajuan = document.getElementById('nominal_pengajuan');
                const inputDisetujui = document.getElementById('nominal_disetujui');


                input.addEventListener('input', function(e) {
                    let raw = e.target.value; // nilai asli dari input

                    // Kalau user isi "-", langsung biarkan tanpa format
                    if (raw === '-') {
                        return; // biarkan apa adanya
                    }

                    // Kalau bukan "-", baru kita bersihkan ke angka
                    let value = raw.replace(/[^0-9]/g, '');
                    e.target.value = value ? formatRupiah(value) : '';
                });

                function formatRupiah(angka, prefix = 'Rp') {
                    let number_string = angka.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        const separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix + ' ' + rupiah;
                }
            });
        </script>
        <script>
            function formatRupiah(input) {
                // Hapus karakter selain angka
                let angka = input.value.replace(/[^0-9]/g, "");

                // Format dengan titik ribuan
                input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            document.addEventListener("DOMContentLoaded", function() {
                const fields = ["nominal_pengajuan", "nominal_disetujui"];

                fields.forEach(id => {
                    const input = document.getElementById(id);

                    if (input) {
                        // Format ulang saat halaman dimuat
                        input.value = input.value.replace(/\D/g, "")
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                        // Format saat mengetik
                        input.addEventListener("input", function() {
                            formatRupiah(this);
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection