@extends('layouts.app')
@section('title', 'Kategori Instansi')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        /* Warna tombol pagination aktif dari DataTables (Bootstrap 5) */
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
            background-color: #78C841 !important;
            border-color: #78C841 !important;
            color: white !important;
        }

        /* Opsional: hover untuk tombol aktif */
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link:hover {
            background-color: #66b638 !important;
            color: white !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Data Kategori Instansi</h5>
                    <div class="mb-3 text-end">
                        <a href="{{ route('kategori-instansi.create') }}"
                            style="background-color: #78C841; color: white;" class="btn">
                            <i class="fas fa-plus me-1"></i> Tambah Kategori
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table id="kategoriTable" class="table table-bordered text-nowrap mb-0 align-middle">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th>
                                        <h6 class="fw-semibold mb-0">No</h6>
                                    </th>
                                    <th>
                                        <h6 class="fw-semibold mb-0">Nama Kategori</h6>
                                    </th>
                                    <th>
                                        <h6 class="fw-semibold mb-0">Jumlah Proposal</h6>
                                    </th>
                                    <th>
                                        <h6 class="fw-semibold mb-0">Aksi</h6>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kategoriInstansi as $item)
                                    <tr>
                                        <td>
                                            <h6 class="fw-semibold mb-0">{{ $loop->iteration }}</h6>
                                        </td>
                                        <td>
                                            <h6 class="fw-semibold mb-0">{{ $item->nama }}</h6>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $item->proposal_count }}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('kategori-instansi.edit', $item->id) }}"
                                                    class="btn btn-sm btn-light border-0 text-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Tombol Hapus --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-light border-0 text-danger btn-delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-id="{{ $item->id }}" data-nama="{{ $item->nama }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data kategori instansi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus kategori berikut?</p>
                        <div class="alert alert-danger mb-0">
                            <strong id="deleteDataName"></strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-secondary-subtle text-dark"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $('#kategoriTable').DataTable({
                language: {
                    search: "Cari",
                    lengthMenu: "Tampil _MENU_",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0–0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "«",
                        last: "»",
                        previous: "‹",
                        next: "›"
                    }
                },
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                pagingType: "full_numbers",
                drawCallback: function(settings) {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm');
                }
            });
        </script>

        <script>
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                $('#deleteDataName').text("Nama: " + nama);
                $('#deleteForm').attr('action', '/kategori-instansi/' + id);
            });
        </script>

        {{-- TOAST --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const toastElList = [].slice.call(document.querySelectorAll('.toast'))
                toastElList.map(function(toastEl) {
                    const toast = new bootstrap.Toast(toastEl, {
                        delay: 8000,
                    });
                    toast.show();
                });
            });
        </script>

        @if (session('success'))
            <div class="position-fixed top-0 end-0 p-3 mt-5 me-5" style="z-index: 9999">
                <div style="background-color: #78C841; color: white;" class="toast align-items-center border-0 show"
                    role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="position-fixed top-0 end-0 p-3 mt-5 me-5" style="z-index: 9999">
                <div style="background-color: #dc3545; color: white;" class="toast align-items-center border-0 show"
                    role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
    @endpush
@endsection