@extends('layouts.app')
@section('title', 'Edit Kategori Instansi')
@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Edit Kategori Instansi</h5>

                <form method="POST" action="{{ route('kategori-instansi.update', $kategoriInstansi->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $kategoriInstansi->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sub Instansi <span class="text-muted fw-normal">(opsional)</span></label>
                        <div id="sub-instansi-wrapper">
                            @php
                                $subList = old('sub_instansi', $kategoriInstansi->subInstansi->pluck('nama')->toArray());
                            @endphp
                            @foreach ($subList as $sub)
                                <div class="sub-instansi-item mb-2 d-flex gap-2">
                                    <input type="text" name="sub_instansi[]" class="form-control"
                                        value="{{ $sub }}" placeholder="Contoh: Pengadilan">
                                    <button type="button" class="btn btn-outline-danger btn-remove-sub">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="btn-add-sub" class="btn btn-sm bg-secondary-subtle text-dark mt-1">
                            <i class="fas fa-plus me-1"></i> Tambah Sub Instansi
                        </button>
                    </div>

                    <button type="submit" style="background-color: #78C841; color: white;" class="btn">
                        Update
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
    document.getElementById('btn-add-sub').addEventListener('click', function () {
        const wrapper = document.getElementById('sub-instansi-wrapper');
        const div = document.createElement('div');
        div.className = 'sub-instansi-item mb-2 d-flex gap-2';
        div.innerHTML = `
            <input type="text" name="sub_instansi[]" class="form-control" placeholder="Contoh: Pengadilan">
            <button type="button" class="btn btn-outline-danger btn-remove-sub">
                <i class="fas fa-times"></i>
            </button>
        `;
        wrapper.appendChild(div);
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-sub')) {
            e.target.closest('.sub-instansi-item').remove();
        }
    });
</script>
@endpush
@endsection