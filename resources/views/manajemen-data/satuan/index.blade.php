@extends('layouts.app')
@section('title', 'Satuan')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    /* Warna tombol pagination aktif */
    .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
        background-color: #78C841 !important;
        border-color: #78C841 !important;
        color: white !important;
    }

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

                <h5 class="card-title fw-semibold mb-4">
                    Data Satuan
                </h5>

                <div class="mb-3 text-end">
                    <a href="{{ route('satuan.create') }}"
                        class="btn"
                        style="background-color:#78C841;color:white;">
                        <i class="fas fa-plus me-1"></i>
                        Tambah Satuan
                    </a>
                </div>

                <div class="table-responsive">
                    <table id="satuanTable" class="table table-bordered text-nowrap mb-0 align-middle">

                        <thead class="text-dark fs-4">
                            <tr>
                                <th width="70">No</th>
                                <th>Nama Satuan</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($satuan as $item)

                            <tr>

                                <td>
                                    <h6 class="fw-semibold mb-0">
                                        {{ $loop->iteration }}
                                    </h6>
                                </td>

                                <td>
                                    <h6 class="fw-normal mb-0">
                                        {{ $item->nama }}
                                    </h6>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="{{ route('satuan.edit',$item->id) }}"
                                            class="btn btn-sm btn-light border-0 text-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button
                                            class="btn btn-sm btn-light border-0 text-danger btn-delete"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->nama }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal">

                                            <i class="fas fa-trash-alt"></i>

                                        </button>

                                    </div>
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Delete --}}
<div class="modal fade"
    id="deleteModal"
    tabindex="-1"
    aria-labelledby="deleteModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">

        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Konfirmasi Hapus
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <p>
                        Apakah Anda yakin ingin menghapus satuan berikut?
                    </p>

                    <div class="alert alert-danger mb-0">
                        <strong id="deleteDataName"></strong>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn bg-secondary-subtle text-dark"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">

                        Ya, Hapus

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@push('scripts')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#satuanTable').DataTable({

        language: {
            search: "Cari",
            lengthMenu: "Tampil _MENU_",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data satuan.",
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

        lengthMenu: [
            [10,25,50,-1],
            [10,25,50,"Semua"]
        ],

        pagingType: "full_numbers",

        drawCallback: function () {
            $('.dataTables_paginate > .pagination')
                .addClass('pagination-sm');
        }

    });

});
</script>

<script>
$(document).on('click','.btn-delete',function(){

    let id=$(this).data('id');
    let nama=$(this).data('nama');

    $('#deleteDataName').text('Nama : '+nama);
    $('#deleteForm').attr('action','/satuan/'+id);

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const toastElList=[].slice.call(document.querySelectorAll('.toast'));

    toastElList.map(function(toastEl){

        const toast=new bootstrap.Toast(toastEl,{
            delay:8000
        });

        toast.show();

    });

});
</script>

@if(session('success'))

<div class="position-fixed top-0 end-0 p-3 mt-5 me-5" style="z-index:9999">

    <div
        class="toast align-items-center border-0 show"
        style="background:#78C841;color:white;">

        <div class="d-flex">

            <div class="toast-body">
                {{ session('success') }}
            </div>

            <button
                type="button"
                class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast">
            </button>

        </div>

    </div>

</div>

@endif

@endpush

@endsection