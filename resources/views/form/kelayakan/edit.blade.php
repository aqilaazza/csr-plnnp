@extends('layouts.app')
@section('title', 'Edit Kelayakan')
@section('content')
    <div class="row">
        <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-semibold mb-0">Edit Form Kelayakan</h5>
                        <a href="{{ route('kelayakan.index') }}" class="btn bg-secondary-subtle text-dark">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    <form method="POST" action="{{ route('kelayakan.update', $kelayakan->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="proposal" class="form-label">Proposal</label>
                            <input type="text" class="form-control" id="proposal"
                                value="{{ $kelayakan->proposal->judul ?? '-' }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="dasar_pelaksanaan" class="form-label">Dasar Pelaksanaan</label>
                            <textarea class="form-control @error('dasar_pelaksanaan') is-invalid @enderror" id="dasar_pelaksanaan"
                                name="dasar_pelaksanaan">{{ old('dasar_pelaksanaan', $kelayakan->dasar_pelaksanaan) }}</textarea>
                            @error('dasar_pelaksanaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="latar_belakang" class="form-label">Latar Belakang</label>
                            <textarea class="form-control @error('latar_belakang') is-invalid @enderror" id="latar_belakang"
                                name="latar_belakang">{{ old('latar_belakang', $kelayakan->latar_belakang) }}</textarea>
                            @error('latar_belakang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan</label>
                            <textarea class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan">{{ old('tujuan', $kelayakan->tujuan) }}</textarea>
                            @error('tujuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="indikator_lingkungan" class="form-label">Indikator Lingkungan</label>
                            <textarea class="form-control" id="indikator_lingkungan" name="indikator_lingkungan">{{ old('indikator_lingkungan', $kelayakan->indikator_lingkungan) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="indikator_sosial" class="form-label">Indikator Sosial</label>
                            <textarea class="form-control" id="indikator_sosial" name="indikator_sosial">{{ old('indikator_sosial', $kelayakan->indikator_sosial) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah_penerima_manfaat" class="form-label">Jumlah Penerima Manfaat</label>
                            <input type="text" class="form-control" id="jumlah_penerima_manfaat"
                                name="jumlah_penerima_manfaat"
                                value="{{ old('jumlah_penerima_manfaat', $kelayakan->jumlah_penerima_manfaat) }}">
                        </div>

                        <div class="mb-3">
                            <label for="jenis_stakeholder" class="form-label">Jenis Stakeholder</label>
                            <textarea class="form-control" id="jenis_stakeholder" name="jenis_stakeholder">{{ old('jenis_stakeholder', $kelayakan->jenis_stakeholder) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="pejabat_instansi" class="form-label">Pejabat Instansi</label>
                            <textarea class="form-control" id="pejabat_instansi" name="pejabat_instansi">{{ old('pejabat_instansi', $kelayakan->pejabat_instansi) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="data_terdahulu" class="form-label">Data Terdahulu</label>
                            <textarea class="form-control" id="data_terdahulu" name="data_terdahulu">{{ old('data_terdahulu', $kelayakan->data_terdahulu) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prioritas" class="form-label">Prioritas</label>
                                <select name="prioritas" id="prioritas"
                                    class="form-control @error('prioritas') is-invalid @enderror" required>
                                    <option value="">-- Pilih Prioritas --</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}"
                                            {{ old('prioritas', $kelayakan->prioritas) == $i ? 'selected' : '' }}>
                                            Prioritas {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="dampak" class="form-label">Dampak</label>
                                @php
                                    $labelDampak = [
                                        1 => 'Tidak ada dampak',
                                        2 => 'Kecil',
                                        3 => 'Sedang',
                                        4 => 'Tinggi',
                                        5 => 'Sangat Tinggi',
                                    ];
                                @endphp
                                <select name="dampak" id="dampak"
                                    class="form-control @error('dampak') is-invalid @enderror" required>
                                    <option value="">-- Pilih Dampak --</option>
                                    @foreach ($labelDampak as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('dampak', $kelayakan->dampak) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dampak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <textarea class="form-control" id="contact_person" name="contact_person">{{ old('contact_person', $kelayakan->contact_person) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
                            <textarea class="form-control" id="catatan_khusus" name="catatan_khusus">{{ old('catatan_khusus', $kelayakan->catatan_khusus) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">File PDF Saat Ini (Revisi {{ $kelayakan->revisi ?? '00' }})</label>
                            @if ($kelayakan->file_pdf)
                                <a href="{{ asset('storage/' . $kelayakan->file_pdf) }}" target="_blank">Lihat PDF</a>
                            @else
                                <span class="text-muted">Belum ada file</span>
                            @endif
                        </div>

                        <div class="alert alert-light border d-flex align-items-center gap-2 mb-4">
                            <i class="fas fa-circle-info text-secondary"></i>
                            <small class="mb-0">PDF akan dibuat ulang otomatis dan nomor revisi akan bertambah setelah
                                data disimpan.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kelayakan.index') }}" class="btn bg-secondary-subtle text-dark">Batal</a>
                            <button type="submit" style="background-color: #78C841; color: white;" class="btn">
                                Simpan Perubahan &amp; Generate Ulang PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection