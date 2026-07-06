@extends('layouts.app')
@section('title', 'Tambah Kelayakan')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />
    <style>
        /* Field yang datanya otomatis diambil dari Proposal - dibekukan (readonly) */
        .field-locked {
            background-color: #e9ecef !important;
            cursor: not-allowed;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-semibold mb-0">Tambah Form Kelayakan</h5>
                        <a href="{{ route('kelayakan.index') }}" class="btn bg-secondary-subtle text-dark">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    <form method="POST" action="{{ route('kelayakan.store') }}">
                        @csrf

                        {{-- 1. PILIH PROPOSAL --}}
                        <div class="mb-3">
                            <label class="form-label">Proposal</label>
                            <select name="proposal_id" id="select-proposal"
                                class="form-select @error('proposal_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Proposal --</option>
                                @foreach ($proposal as $item)
                                    <option value="{{ $item->id }}"
                                        data-judul="{{ $item->judul }}"
                                        data-tipologi="{{ $item->tipologi->deskripsi ?? '-' }}"
                                        data-instansi="{{ $item->instansi_pengajuan }}"
                                        data-kategori="{{ $item->kategoriInstansi->nama ?? '-' }}"
                                        data-contact="{{ $item->contact_person ?? '-' }}"
                                        data-bantuan="{{ $item->barang_pengajuan ?? '-' }}"
                                        data-nominal="{{ $item->nominal_pengajuan ? number_format($item->nominal_pengajuan, 0, ',', '.') : '' }}"
                                        data-nominal-disetujui="{{ $item->nominal_disetujui ? number_format($item->nominal_disetujui, 0, ',', '.') : '' }}"
                                        {{ old('proposal_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->judul }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proposal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. NAMA PROGRAM (otomatis dari Proposal->judul, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Program</label>
                            <input type="text" id="prev-judul" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        {{-- 3. TIPOLOGI (otomatis dari Proposal->tipologi, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Tipologi</label>
                            <input type="text" id="prev-tipologi" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        {{-- 4. DASAR PELAKSANAAN (input user) --}}
                        <div class="mb-3">
                            <label for="dasar_pelaksanaan" class="form-label">Dasar Pelaksanaan</label>
                            <textarea class="form-control @error('dasar_pelaksanaan') is-invalid @enderror" id="dasar_pelaksanaan"
                                name="dasar_pelaksanaan" required>{{ old('dasar_pelaksanaan') }}</textarea>
                            @error('dasar_pelaksanaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 5. LATAR BELAKANG (input user) --}}
                        <div class="mb-3">
                            <label for="latar_belakang" class="form-label">Latar Belakang</label>
                            <textarea class="form-control @error('latar_belakang') is-invalid @enderror" id="latar_belakang"
                                name="latar_belakang" required>{{ old('latar_belakang') }}</textarea>
                            @error('latar_belakang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 6. TUJUAN (input user, dynamic list) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Tujuan</label>
                                <button type="button" id="btn-add-tujuan" class="btn btn-sm" style="background-color:#78C841; color:white;">
                                    <i class="fas fa-plus me-1"></i> Tambah
                                </button>
                            </div>

                            <div id="tujuan-list"></div>

                            <input type="hidden" name="tujuan" id="tujuan-hidden" value="{{ old('tujuan') }}">

                            @error('tujuan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 7. INDIKATOR LINGKUNGAN (input user) --}}
                        <div class="mb-3">
                            <label for="indikator_lingkungan" class="form-label">Indikator Lingkungan</label>
                            <textarea class="form-control" id="indikator_lingkungan" name="indikator_lingkungan">{{ old('indikator_lingkungan') }}</textarea>
                        </div>

                        {{-- 8. INDIKATOR SOSIAL (input user) --}}
                        <div class="mb-3">
                            <label for="indikator_sosial" class="form-label">Indikator Sosial</label>
                            <textarea class="form-control" id="indikator_sosial" name="indikator_sosial">{{ old('indikator_sosial') }}</textarea>
                        </div>

                        {{-- 9. JUMLAH PENERIMA MANFAAT (input user) --}}
                        <div class="mb-3">
                            <label for="jumlah_penerima_manfaat" class="form-label">Jumlah Penerima Manfaat</label>
                            <input type="text" class="form-control" name="jumlah_penerima_manfaat"
                                id="jumlah_penerima_manfaat" value="{{ old('jumlah_penerima_manfaat') }}">
                        </div>

                        {{-- 10. ASAL INSTANSI (otomatis dari Proposal->instansi_pengajuan, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Asal Instansi</label>
                            <input type="text" id="prev-instansi" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        {{-- CONTACT PERSON (otomatis dari Proposal, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" id="prev-contact" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        {{-- 11. KATEGORI STAKEHOLDER (otomatis dari Proposal->kategori_instansi, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Kategori Stakeholder</label>
                            <input type="text" id="prev-kategori" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        {{-- 12. PEJABAT INSTANSI / MENGETAHUI (input user) --}}
                        <div class="mb-3">
                            <label for="pejabat_instansi" class="form-label">Pejabat Instansi</label>
                            <textarea class="form-control" id="pejabat_instansi" name="pejabat_instansi">{{ old('pejabat_instansi') }}</textarea>
                        </div>

                        {{-- 13. BANTUAN YANG DIAJUKAN (otomatis dari Proposal, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Bantuan yang Diajukan</label>
                            <input type="text" id="prev-bantuan" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        {{-- NOMINAL DISETUJUI (otomatis dari Proposal->nominal_disetujui, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Nominal Disetujui</label>
                            <input type="text" id="prev-nominal-disetujui" class="form-control field-locked" readonly tabindex="-1" value="-">
                        </div>

                        <div class="mb-3">
                            <label for="data_terdahulu" class="form-label">Data Terdahulu</label>
                            <textarea class="form-control" id="data_terdahulu" name="data_terdahulu">{{ old('data_terdahulu') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prioritas" class="form-label">Prioritas</label>
                                <select name="prioritas" id="prioritas"
                                    class="form-control @error('prioritas') is-invalid @enderror" required>
                                    <option value="">-- Pilih Prioritas --</option>
                                    <option value="1" {{ old('prioritas') == 1 ? 'selected' : '' }}>Prioritas 1</option>
                                    <option value="2" {{ old('prioritas') == 2 ? 'selected' : '' }}>Prioritas 2</option>
                                    <option value="3" {{ old('prioritas') == 3 ? 'selected' : '' }}>Prioritas 3</option>
                                    <option value="4" {{ old('prioritas') == 4 ? 'selected' : '' }}>Prioritas 4</option>
                                    <option value="5" {{ old('prioritas') == 5 ? 'selected' : '' }}>Prioritas 5</option>
                                </select>
                                @error('prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="dampak" class="form-label">Dampak</label>
                                <select name="dampak" id="dampak"
                                    class="form-control @error('dampak') is-invalid @enderror" required>
                                    <option value="">-- Pilih Dampak --</option>
                                    <option value="1" {{ old('dampak') == 1 ? 'selected' : '' }}>Tidak ada dampak</option>
                                    <option value="2" {{ old('dampak') == 2 ? 'selected' : '' }}>Kecil</option>
                                    <option value="3" {{ old('dampak') == 3 ? 'selected' : '' }}>Sedang</option>
                                    <option value="4" {{ old('dampak') == 4 ? 'selected' : '' }}>Tinggi</option>
                                    <option value="5" {{ old('dampak') == 5 ? 'selected' : '' }}>Sangat Tinggi</option>
                                </select>
                                @error('dampak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
                            <textarea class="form-control" id="catatan_khusus" name="catatan_khusus">{{ old('catatan_khusus') }}</textarea>
                        </div>

                        <div class="alert alert-light border d-flex align-items-center gap-2 mb-4">
                            <i class="fas fa-circle-info text-secondary"></i>
                            <small class="mb-0">File PDF akan dibuat otomatis oleh sistem setelah data disimpan.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kelayakan.index') }}" class="btn bg-secondary-subtle text-dark">Batal</a>
                            <button type="submit" style="background-color: #78C841; color: white;" class="btn">
                                Simpan &amp; Generate PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#select-proposal').select2({
                theme: 'bootstrap4',
                allowClear: true,
                width: '100%',
                language: {
                    searching: function() { return "Mencari..."; },
                    inputTooShort: function() { return "Ketik untuk mencari proposal..."; },
                    noResults: function() { return "Tidak ada hasil ditemukan"; }
                }
            });

            $('#select-proposal').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Ketik untuk mencari proposal...');
            });

            // Isi field-field yang dibekukan (readonly) begitu proposal dipilih
            function renderProposalPreview() {
                const $selected = $('#select-proposal option:selected');

                if (!$selected.val()) {
                    $('#prev-judul, #prev-tipologi, #prev-instansi, #prev-kategori, #prev-contact, #prev-bantuan, #prev-nominal-disetujui').val('-');
                    return;
                }

                $('#prev-judul').val($selected.data('judul') || '-');
                $('#prev-tipologi').val($selected.data('tipologi') || '-');
                $('#prev-instansi').val($selected.data('instansi') || '-');
                $('#prev-kategori').val($selected.data('kategori') || '-');
                $('#prev-contact').val($selected.data('contact') || '-');

                const barang = $selected.data('bantuan') || '-';
                const nominal = $selected.data('nominal');
                const bantuanText = nominal ? `${barang} senilai Rp ${nominal}` : barang;
                $('#prev-bantuan').val(bantuanText);

                const nominalDisetujui = $selected.data('nominal-disetujui');
                $('#prev-nominal-disetujui').val(nominalDisetujui ? `Rp ${nominalDisetujui}` : '-');
            }

            $('#select-proposal').on('change', renderProposalPreview);
            renderProposalPreview(); // untuk kasus old('proposal_id') saat validasi gagal
        });
    </script>
    <script>
        $(document).ready(function() {
            const $list = $('#tujuan-list');
            const $hidden = $('#tujuan-hidden');

            // Angka hanya ditampilkan kalau item tujuan lebih dari 1
            function renumberTujuan() {
                const $items = $list.find('.tujuan-item');
                const total = $items.length;

                $items.each(function(index) {
                    const $number = $(this).find('.tujuan-number');
                    if (total > 1) {
                        $number.text((index + 1) + '.').removeClass('d-none');
                    } else {
                        $number.text('').addClass('d-none');
                    }
                });
            }

            // Data yang disimpan ke hidden input mengikuti aturan yang sama:
            // nomor hanya ditulis kalau jumlah tujuan lebih dari 1
            function syncTujuanHidden() {
                let values = [];
                $list.find('.tujuan-item textarea').each(function() {
                    const val = $(this).val().trim();
                    if (val !== '') {
                        values.push(val);
                    }
                });

                let combined;
                if (values.length > 1) {
                    combined = values.map((val, index) => (index + 1) + '. ' + val);
                } else {
                    combined = values;
                }

                $hidden.val(combined.join('\n'));
            }

            function addTujuanRow(text = '') {
                // tujuan-number dan textarea disamakan padding-top-nya (0.375rem, sama dengan
                // padding bawaan Bootstrap .form-control) supaya nomor sejajar lurus dengan baris pertama teks
                const row = $(`
                    <div class="tujuan-item d-flex align-items-start gap-2 mb-2">
                        <span class="tujuan-number fw-semibold" style="min-width: 24px; padding-top: 0.375rem; line-height: 1.5;"></span>
                        <textarea class="form-control" rows="2" placeholder="Tulis tujuan..."></textarea>
                        <button type="button" class="btn btn-sm btn-light text-danger btn-remove-tujuan">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `);
                row.find('textarea').val(text);
                $list.append(row);
                renumberTujuan();
            }

            const oldTujuan = $hidden.val();
            if (oldTujuan && oldTujuan.trim() !== '') {
                const lines = oldTujuan.split('\n').filter(l => l.trim() !== '');
                lines.forEach(line => {
                    const cleaned = line.replace(/^\d+\.\s*/, '');
                    addTujuanRow(cleaned);
                });
            } else {
                addTujuanRow();
            }

            $('#btn-add-tujuan').on('click', function() {
                addTujuanRow();
            });

            $list.on('click', '.btn-remove-tujuan', function() {
                if ($list.find('.tujuan-item').length > 1) {
                    $(this).closest('.tujuan-item').remove();
                    renumberTujuan();
                } else {
                    $(this).closest('.tujuan-item').find('textarea').val('');
                }
            });

            $('form').on('submit', function() {
                syncTujuanHidden();
            });
        });
    </script>
@endpush