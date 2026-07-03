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
@endsection