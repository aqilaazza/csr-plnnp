@extends('layouts.app')
@section('title', 'Edit Satuan')

@section('content')
<div class="body-wrapper-inner">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Edit Satuan</h5>

                <form action="{{ route('satuan.update', $satuan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Satuan</label>

                        <input type="text"
                               name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $satuan->nama) }}"
                               placeholder="Contoh: Unit"
                               required>

                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit"
                        class="btn"
                        style="background:#78C841;color:white">
                        Update
                    </button>

                    <a href="{{ route('satuan.index') }}"
                       class="btn bg-secondary-subtle text-dark">
                        Batal
                    </a>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection