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
@endsection