@extends('layouts.app')
@section('title', 'Tambah Kelayakan')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />
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

                        <div class="mb-3">
                            <label class="form-label">Proposal</label>
                            <select name="proposal_id" id="select-proposal"
                                class="form-select @error('proposal_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Proposal --</option>
                                @foreach ($proposal as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('proposal_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->judul }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proposal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dasar_pelaksanaan" class="form-label">Dasar Pelaksanaan</label>
                            <textarea class="form-control @error('dasar_pelaksanaan') is-invalid @enderror" id="dasar_pelaksanaan"
                                name="dasar_pelaksanaan" required>{{ old('dasar_pelaksanaan') }}</textarea>
                            @error('dasar_pelaksanaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="latar_belakang" class="form-label">Latar Belakang</label>
                            <textarea class="form-control @error('latar_belakang') is-invalid @enderror" id="latar_belakang"
                                name="latar_belakang" required>{{ old('latar_belakang') }}</textarea>
                            @error('latar_belakang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan</label>
                            <textarea class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan"
                                required>{{ old('tujuan') }}</textarea>
                            @error('tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="indikator_lingkungan" class="form-label">Indikator Lingkungan</label>
                            <textarea class="form-control" id="indikator_lingkungan" name="indikator_lingkungan">{{ old('indikator_lingkungan') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="indikator_sosial" class="form-label">Indikator Sosial</label>
                            <textarea class="form-control" id="indikator_sosial" name="indikator_sosial">{{ old('indikator_sosial') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_penerima_manfaat" class="form-label">Jumlah Penerima Manfaat</label>
                            <input type="text" class="form-control" name="jumlah_penerima_manfaat"
                                id="jumlah_penerima_manfaat" value="{{ old('jumlah_penerima_manfaat') }}">
                        </div>

                        <div class="mb-3">
                            <label for="jenis_stakeholder" class="form-label">Jenis Stakeholder</label>
                            <textarea class="form-control" id="jenis_stakeholder" name="jenis_stakeholder">{{ old('jenis_stakeholder') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="pejabat_instansi" class="form-label">Pejabat Instansi</label>
                            <textarea class="form-control" id="pejabat_instansi" name="pejabat_instansi">{{ old('pejabat_instansi') }}</textarea>
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
                                    <option value="1" {{ old('prioritas') == 1 ? 'selected' : '' }}>Prioritas 1
                                    </option>
                                    <option value="2" {{ old('prioritas') == 2 ? 'selected' : '' }}>Prioritas 2
                                    </option>
                                    <option value="3" {{ old('prioritas') == 3 ? 'selected' : '' }}>Prioritas 3
                                    </option>
                                    <option value="4" {{ old('prioritas') == 4 ? 'selected' : '' }}>Prioritas 4
                                    </option>
                                    <option value="5" {{ old('prioritas') == 5 ? 'selected' : '' }}>Prioritas 5
                                    </option>
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
                                    <option value="1" {{ old('dampak') == 1 ? 'selected' : '' }}>Tidak ada dampak
                                    </option>
                                    <option value="2" {{ old('dampak') == 2 ? 'selected' : '' }}>Kecil</option>
                                    <option value="3" {{ old('dampak') == 3 ? 'selected' : '' }}>Sedang</option>
                                    <option value="4" {{ old('dampak') == 4 ? 'selected' : '' }}>Tinggi</option>
                                    <option value="5" {{ old('dampak') == 5 ? 'selected' : '' }}>Sangat Tinggi
                                    </option>
                                </select>
                                @error('dampak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <textarea class="form-control" id="contact_person" name="contact_person">{{ old('contact_person') }}</textarea>
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
                    searching: function() {
                        return "Mencari...";
                    },
                    inputTooShort: function() {
                        return "Ketik untuk mencari proposal...";
                    },
                    noResults: function() {
                        return "Tidak ada hasil ditemukan";
                    }
                }
            });

            $('#select-proposal').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Ketik untuk mencari proposal...');
            });
        });
    </script>
@endpush