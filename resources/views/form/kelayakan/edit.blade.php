@extends('layouts.app')

@section('title', 'Edit Form Kelayakan')

@section('content')

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col">
            <h3 class="fw-bold">Edit Form Kelayakan</h3>
            <p class="text-muted mb-0">
                Perbarui informasi Form Kelayakan.
            </p>
        </div>
    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-light">
            <h5 class="mb-0 fw-semibold">
                Data Form Kelayakan
            </h5>
        </div>

        <form action="{{ route('kelayakan.update', $kelayakan->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="row">

                    {{-- Proposal --}}
                    <div class="col-lg-12 mb-4">

                        <label class="form-label">
                            Proposal
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $kelayakan->proposal->judul }}"
                            disabled>

                    </div>

                    {{-- Dasar Pelaksanaan --}}
                    <div class="col-lg-12 mb-4">

                        <label class="form-label">
                            Dasar Pelaksanaan
                        </label>

                        <textarea
                            id="dasar_pelaksanaan"
                            name="dasar_pelaksanaan"
                            rows="6"
                            class="form-control @error('dasar_pelaksanaan') is-invalid @enderror"
                            required>{{ old('dasar_pelaksanaan', $kelayakan->dasar_pelaksanaan) }}</textarea>

                        @error('dasar_pelaksanaan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Latar Belakang --}}
                    <div class="col-lg-12 mb-4">

                        <label class="form-label">
                            Latar Belakang
                        </label>

                        <textarea
                            id="latar_belakang"
                            name="latar_belakang"
                            rows="8"
                            class="form-control @error('latar_belakang') is-invalid @enderror"
                            required>{{ old('latar_belakang', $kelayakan->latar_belakang) }}</textarea>

                    </div>

                    {{-- Tujuan --}}
                    <div class="col-lg-12 mb-4">

                        <label class="form-label">
                            Tujuan
                        </label>

                        <textarea
                            id="tujuan"
                            name="tujuan"
                            rows="6"
                            class="form-control @error('tujuan') is-invalid @enderror"
                            required>{{ old('tujuan', $kelayakan->tujuan) }}</textarea>

                    </div>

                    {{-- Indikator Lingkungan --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Indikator Lingkungan
                        </label>

                        <textarea
                            id="indikator_lingkungan"
                            name="indikator_lingkungan"
                            rows="6"
                            class="form-control">{{ old('indikator_lingkungan', $kelayakan->indikator_lingkungan) }}</textarea>

                    </div>

                    {{-- Indikator Sosial --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Indikator Sosial
                        </label>

                        <textarea
                            id="indikator_sosial"
                            name="indikator_sosial"
                            rows="6"
                            class="form-control">{{ old('indikator_sosial', $kelayakan->indikator_sosial) }}</textarea>

                    </div>

                                        {{-- Jumlah Penerima Manfaat --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Jumlah Penerima Manfaat
                        </label>

                        <input
                            type="text"
                            name="jumlah_penerima_manfaat"
                            class="form-control @error('jumlah_penerima_manfaat') is-invalid @enderror"
                            value="{{ old('jumlah_penerima_manfaat', $kelayakan->jumlah_penerima_manfaat) }}">

                    </div>

                    {{-- Prioritas --}}
                    <div class="col-lg-3 mb-4">

                        <label class="form-label">
                            Prioritas
                        </label>

                        <select
                            name="prioritas"
                            class="form-select @error('prioritas') is-invalid @enderror"
                            required>

                            <option value="">-- Pilih Prioritas --</option>

                            @for($i=1;$i<=5;$i++)
                                <option
                                    value="{{ $i }}"
                                    {{ old('prioritas', $kelayakan->prioritas)==$i ? 'selected' : '' }}>
                                    Prioritas {{ $i }}
                                </option>
                            @endfor

                        </select>

                    </div>

                    {{-- Dampak --}}
                    <div class="col-lg-3 mb-4">

                        <label class="form-label">
                            Dampak
                        </label>

                        <select
                            name="dampak"
                            class="form-select @error('dampak') is-invalid @enderror"
                            required>

                            <option value="">-- Pilih Dampak --</option>

                            <option value="1" {{ old('dampak', $kelayakan->dampak)==1 ? 'selected':'' }}>
                                Tidak Ada Dampak
                            </option>

                            <option value="2" {{ old('dampak', $kelayakan->dampak)==2 ? 'selected':'' }}>
                                Kecil
                            </option>

                            <option value="3" {{ old('dampak', $kelayakan->dampak)==3 ? 'selected':'' }}>
                                Sedang
                            </option>

                            <option value="4" {{ old('dampak', $kelayakan->dampak)==4 ? 'selected':'' }}>
                                Tinggi
                            </option>

                            <option value="5" {{ old('dampak', $kelayakan->dampak)==5 ? 'selected':'' }}>
                                Sangat Tinggi
                            </option>

                        </select>

                    </div>

                    {{-- Jenis Stakeholder --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Jenis Stakeholder
                        </label>

                        <textarea
                            id="jenis_stakeholder"
                            name="jenis_stakeholder"
                            rows="5"
                            class="form-control">{{ old('jenis_stakeholder', $kelayakan->jenis_stakeholder) }}</textarea>

                    </div>

                    {{-- Pejabat Instansi --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Pejabat Instansi
                        </label>

                        <textarea
                            id="pejabat_instansi"
                            name="pejabat_instansi"
                            rows="5"
                            class="form-control">{{ old('pejabat_instansi', $kelayakan->pejabat_instansi) }}</textarea>

                    </div>

                    {{-- Data Terdahulu --}}
                    <div class="col-lg-12 mb-4">

                        <label class="form-label">
                            Data Terdahulu
                        </label>

                        <textarea
                            id="data_terdahulu"
                            name="data_terdahulu"
                            rows="6"
                            class="form-control">{{ old('data_terdahulu', $kelayakan->data_terdahulu) }}</textarea>

                    </div>

                    {{-- Contact Person --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Contact Person
                        </label>

                        <textarea
                            id="contact_person"
                            name="contact_person"
                            rows="4"
                            class="form-control">{{ old('contact_person', $kelayakan->contact_person) }}</textarea>

                    </div>

                    {{-- Catatan Khusus --}}
                    <div class="col-lg-6 mb-4">

                        <label class="form-label">
                            Catatan Khusus
                        </label>

                        <textarea
                            id="catatan_khusus"
                            name="catatan_khusus"
                            rows="4"
                            class="form-control">{{ old('catatan_khusus', $kelayakan->catatan_khusus) }}</textarea>

                    </div>

                </div>

            </div>

            <div class="card-footer text-end">

                <a href="{{ route('kelayakan.index') }}"
                    class="btn btn-secondary">
                    Kembali
                </a>

                <button
                    type="submit"
                    class="btn"
                    style="background-color:#78C841;color:white;">

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')

    {{-- CKEditor 5 --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        const editors = [
            'dasar_pelaksanaan',
            'latar_belakang',
            'tujuan',
            'indikator_lingkungan',
            'indikator_sosial',
            'jenis_stakeholder',
            'pejabat_instansi',
            'data_terdahulu',
            'contact_person',
            'catatan_khusus'
        ];

        editors.forEach(function(id) {

            const element = document.querySelector('#' + id);

            if (!element) return;

            ClassicEditor
                .create(element, {
                    toolbar: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'underline',
                        '|',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'insertTable',
                        'blockQuote',
                        'link',
                        '|',
                        'undo',
                        'redo'
                    ]
                })
                .catch(error => {
                    console.error(error);
                });

        });
    </script>

@endpush