@extends('layouts.app')
@section('title', 'Tambah Kategori Instansi')
@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Tambah Kategori Instansi</h5>

                <form method="POST" action="{{ route('kategori-instansi.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama') }}" required
                            placeholder="Contoh: Pemerintahan">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="kategori-contoh-wrapper">
                        <label class="form-label">Contoh Nama Instansi</label>
                        <input type="text" name="contoh" id="kategori-contoh-input"
                            class="form-control @error('contoh') is-invalid @enderror"
                            value="{{ old('contoh') }}"
                            placeholder="Contoh: Dinas Sosial Kabupaten Malang">
                        @error('contoh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Wajib diisi jika kategori ini tidak punya sub instansi. Teks ini dipakai sebagai contoh pada form pengajuan proposal.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sub Instansi <span class="text-muted fw-normal">(opsional)</span></label>
                        <small class="text-muted d-block mb-2">Jika diisi, kolom "Contoh nama instansi" pada tiap baris sub instansi wajib diisi.</small>
                        <div id="sub-instansi-wrapper">
                            @if (old('sub_instansi'))
                                @foreach (old('sub_instansi') as $index => $sub)
                                    <div class="sub-instansi-item mb-2 d-flex gap-2">
                                        <div class="flex-fill">
                                            <input type="text" name="sub_instansi[]" class="form-control"
                                                value="{{ $sub }}" placeholder="Nama sub instansi, contoh: Pengadilan">
                                        </div>
                                        <div class="flex-fill">
                                            <input type="text" name="sub_instansi_contoh[]"
                                                class="form-control @error('sub_instansi_contoh.' . $index) is-invalid @enderror"
                                                value="{{ old('sub_instansi_contoh')[$index] ?? '' }}"
                                                placeholder="Contoh nama instansi, mis: Pengadilan Negeri Malang">
                                            @error('sub_instansi_contoh.' . $index)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-remove-sub align-self-start">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" id="btn-add-sub" class="btn btn-sm bg-secondary-subtle text-dark mt-1">
                            <i class="fas fa-plus me-1"></i> Tambah Sub Instansi
                        </button>
                    </div>

                    <button type="submit" style="background-color: #78C841; color: white;" class="btn">
                        Simpan
                    </button>
                    <a href="{{ route('kategori-instansi.index') }}" class="btn bg-secondary-subtle text-dark">
                        Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const kategoriContohWrapper = document.getElementById('kategori-contoh-wrapper');
    const kategoriContohInput = document.getElementById('kategori-contoh-input');
    const subInstansiWrapper = document.getElementById('sub-instansi-wrapper');

    function toggleKategoriContoh() {
        const hasSub = subInstansiWrapper.querySelectorAll('.sub-instansi-item').length > 0;

        if (hasSub) {
            kategoriContohWrapper.style.display = 'none';
            kategoriContohInput.disabled = true;
            kategoriContohInput.required = false;
        } else {
            kategoriContohWrapper.style.display = '';
            kategoriContohInput.disabled = false;
            kategoriContohInput.required = true;
        }

        // Kolom "contoh" tiap sub instansi wajib diisi hanya jika ada sub instansi
        subInstansiWrapper.querySelectorAll('input[name="sub_instansi_contoh[]"]').forEach(function (input) {
            input.required = hasSub;
        });
    }

    document.getElementById('btn-add-sub').addEventListener('click', function () {
        const div = document.createElement('div');
        div.className = 'sub-instansi-item mb-2 d-flex gap-2';
        div.innerHTML = `
            <div class="flex-fill">
                <input type="text" name="sub_instansi[]" class="form-control" placeholder="Nama sub instansi, contoh: Pengadilan">
            </div>
            <div class="flex-fill">
                <input type="text" name="sub_instansi_contoh[]" class="form-control" placeholder="Contoh nama instansi, mis: Pengadilan Negeri Malang">
            </div>
            <button type="button" class="btn btn-outline-danger btn-remove-sub align-self-start">
                <i class="fas fa-times"></i>
            </button>
        `;
        subInstansiWrapper.appendChild(div);
        toggleKategoriContoh();
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-sub')) {
            e.target.closest('.sub-instansi-item').remove();
            toggleKategoriContoh();
        }
    });

    // Jalankan sekali saat halaman dimuat (menangani kasus old() input setelah validasi gagal)
    toggleKategoriContoh();
</script>
@endpush
@endsection