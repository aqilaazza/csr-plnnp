@extends('layouts.app')
@section('title', 'CSR PLN Nusantara Power UP Paiton')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">

    <style>
        #kelayakanTable thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 20;
        }

        table.dataTable tbody td:first-child,
        table.dataTable thead th:first-child {
            position: sticky;
            left: 0;
            background-color: white !important;
            z-index: 11;
        }

        table.dataTable tbody td:nth-child(2),
        table.dataTable thead th:nth-child(2) {
            position: sticky;
            left: 60px;
            background-color: white !important;
            z-index: 11;
        }

        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
            background-color: #78C841 !important;
            border-color: #78C841 !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link:hover {
            background-color: #66b638 !important;
            color: white !important;
        }

        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }

        table.dataTable,
        table.dataTable th,
        table.dataTable td,
        table.dataTable td>*,
        table.dataTable th>* {
            white-space: normal !important;
        }

        .text-wrap,
        .text-break {
            white-space: normal !important;
        }

        #kelayakanTable * {
            white-space: normal !important;
            word-break: keep-all !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #kelayakanTable td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .DTFC_LeftWrapper table.dataTable thead th {
            background-color: #f8f9fa !important;
            position: relative;
            z-index: 10 !important;
            border-right: 1px solid #dee2e6 !important;
        }

        .DTFC_LeftWrapper table.dataTable tbody td {
            background-color: #ffffff !important;
            position: relative;
            z-index: 10 !important;
            border-right: 1px solid #dee2e6 !important;
        }

        .DTFC_LeftWrapper {
            background-color: #ffffff !important;
            z-index: 5 !important;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15) !important;
            border-right: 2px solid #dee2e6 !important;
        }

        .DTFC_LeftWrapper table.dataTable tbody tr:hover td {
            background-color: #f1f3f4 !important;
        }

        table.dataTable tbody tr:hover {
            background-color: transparent !important;
        }

        table.dataTable tbody tr:hover td:not(.DTFC_LeftWrapper td) {
            background-color: #f8f9fa !important;
        }

        .DTFC_ScrollWrapper table.dataTable {
            margin-left: 0 !important;
        }

        .DTFC_LeftWrapper table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .DTFC_LeftWrapper table.dataTable td,
        .DTFC_LeftWrapper table.dataTable th {
            border-left: 1px solid #dee2e6 !important;
        }

        .DTFC_LeftWrapper table.dataTable td:first-child,
        .DTFC_LeftWrapper table.dataTable th:first-child {
            border-left: 1px solid #dee2e6 !important;
        }

        .DTFC_ScrollWrapper table.dataTable td:first-child,
        .DTFC_ScrollWrapper table.dataTable th:first-child {
            border-left: none !important;
        }

        .DTFC_LeftWrapper .table td,
        .DTFC_LeftWrapper .table th {
            background-color: #fff !important;
            background: #fff !important;
        }

        .DTFC_LeftWrapper .table tbody tr td {
            background-color: #ffffff !important;
            background: #ffffff !important;
        }

        .DTFC_LeftWrapper .table thead tr th {
            background-color: #f8f9fa !important;
            background: #f8f9fa !important;
        }

        .DTFC_LeftWrapper .table-striped tbody tr:nth-of-type(odd) td,
        .DTFC_LeftWrapper .table tbody tr:hover td,
        .DTFC_LeftWrapper .table tbody tr.selected td {
            background-color: #ffffff !important;
            background: #ffffff !important;
        }

        .DTFC_LeftWrapper .table tbody tr:hover td {
            background-color: #f1f3f4 !important;
            background: #f1f3f4 !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4">Data Kelayakan</h5>
                    <div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <select id="filter-prioritas" class="form-select" style="min-width: 200px;">
                                <option value="">-- Semua Prioritas --</option>
                                <option value="Prioritas 1">Prioritas 1</option>
                                <option value="Prioritas 2">Prioritas 2</option>
                                <option value="Prioritas 3">Prioritas 3</option>
                                <option value="Prioritas 4">Prioritas 4</option>
                                <option value="Prioritas 5">Prioritas 5</option>
                            </select>

                            <select id="filter-dampak" class="form-select" style="min-width: 200px;">
                                <option value="">-- Semua Dampak --</option>
                                <option value="Tidak ada dampak">Tidak ada dampak</option>
                                <option value="Kecil">Kecil</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Tinggi">Tinggi</option>
                                <option value="Sangat Tinggi">Sangat Tinggi</option>
                            </select>
                        </div>

                        <a href="{{ route('kelayakan.create') }}" style="background-color: #78C841; color: white;"
                            class="btn">
                            <i class="fas fa-plus me-1"></i> Tambah Kelayakan
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="kelayakanTable" class="table table-bordered nowrap" style="width:100%">
                            <thead class="text-dark fs-4">
                                <tr>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">No</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Proposal</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Dasar Pelaksanaan</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Latar Belakang</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Tujuan</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Indikator Lingkungan</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Indikator Sosial</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Jumlah Penerima Manfaat</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Jenis Stakeholder</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Pejabat Instansi</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Data Terdahulu</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Prioritas</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Dampak</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Contact Person/Instansi</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Nama CP</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Catatan Khusus</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Revisi</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Upload</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">File</span>
                                    </th>
                                    <th style="white-space: nowrap;" class="nowrap">
                                        <span class="fw-semibold mb-0">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kelayakan as $data)
                                    <tr>
                                        <td>
                                            <h6 class="fw-normal mb-0">{{ $loop->iteration }}</h6>
                                        </td>
                                        <td>
                                            <h6 class="fw-normal mb-0">{{ $data->proposal->judul ?? '-' }}</h6>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->dasar_pelaksanaan }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->latar_belakang }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->tujuan }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->indikator_lingkungan }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->indikator_sosial }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->jumlah_penerima_manfaat ?? '-' }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->jenis_stakeholder }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->pejabat_instansi }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->data_terdahulu }}</p>
                                        </td>
                                        <td data-search="Prioritas {{ $data->prioritas }}"
                                            data-order="{{ $data->prioritas }}">
                                            <p class="mb-0 fw-normal">Prioritas {{ $data->prioritas }}</p>
                                        </td>
                                        <td data-search="{{ ['','Tidak ada dampak','Kecil','Sedang','Tinggi','Sangat Tinggi'][$data->dampak] ?? '-' }}"
                                            data-order="{{ $data->dampak }}">
                                            <p class="mb-0 fw-normal">
                                                @php
                                                    $labelDampak = [
                                                        1 => 'Tidak ada dampak',
                                                        2 => 'Kecil',
                                                        3 => 'Sedang',
                                                        4 => 'Tinggi',
                                                        5 => 'Sangat Tinggi',
                                                    ];
                                                @endphp
                                                {{ $labelDampak[$data->dampak] ?? '-' }}
                                            </p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->proposal->contact_person ?? '-' }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->proposal->nama_cp ?? '-' }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->catatan_khusus }}</p>
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-normal">{{ $data->revisi ?? '00' }}</p>
                                        </td>

                                        {{-- Upload Berkas --}}
                                        <td>
                                            @if($data->berkas_pdf)
                                                <a href="{{ asset('storage/'.$data->berkas_pdf) }}" target="_blank">
                                                    Lihat Berkas
                                                </a>
                                            @else
                                                <a href="#"
                                                class="btn-upload text-decoration-none"
                                                data-id="{{ $data->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#uploadModal">
                                                    Upload
                                                </a>
                                            @endif
                                        </td>

                                        {{-- PDF Hasil --}}
                                        <td>
                                            @if ($data->file_pdf)
                                                <a href="{{ asset('storage/'.$data->file_pdf) }}"
                                                target="_blank">
                                                    Lihat PDF
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <a href="{{ route('kelayakan.edit', $data->id) }}"
                                                    class="btn btn-sm btn-light border-0 text-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-sm btn-light border-0 text-danger btn-delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="{{ $data->id }}"
                                                    data-nama="{{ $data->proposal->judul ?? '-' }}">
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

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fs-4">Apakah Anda yakin ingin menghapus data berikut?</p>
                        <div class="alert alert-primary mb-0">
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

    <!-- Modal Upload Berkas -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Berkas PDF</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Pilih File PDF</label>
                            <input
                                type="file"
                                name="berkas_pdf"
                                class="form-control"
                                accept=".pdf"
                                required>
                        </div>

                        <small class="text-muted">
                            Maksimal ukuran file 5 MB.
                        </small>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-success">
                            Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
        <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#kelayakanTable').DataTable({
                    scrollX: true,
                    scrollY: "500px",
                    scrollCollapse: true,
                    paging: true,
                    fixedHeader: true,
                    fixedColumns: {
                        leftColumns: 2
                    },
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
            });
        </script>

        <script>
            function applyFilters() {
                const prioritas = $('#filter-prioritas').val();
                const dampak = $('#filter-dampak').val();

                const table = $('#kelayakanTable').DataTable();

                // Kolom Prioritas index 11, Dampak index 12 (mulai dari 0)
                table.column(11).search(prioritas ? '^' + prioritas + '$' : '', true, false);
                table.column(12).search(dampak ? '^' + dampak + '$' : '', true, false);

                table.draw();
            }

            $('#filter-prioritas, #filter-dampak').on('change', applyFilters);
        </script>

        {{-- DELETE MODAL --}}
        <script>
            $(document).on('click', '.btn-delete', function() {
                $('#deleteDataName').text("Proposal: " + $(this).data('nama'));
                $('#deleteForm').attr('action', '/kelayakan/' + $(this).data('id'));
            });
        </script>

        {{-- UPLOAD MODAL --}}
        <script>
            $(document).on('click', '.btn-upload', function () {
                let id = $(this).data('id');

            $('#uploadForm').attr('action', '/kelayakan/' + id + '/upload');

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
    @endpush
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
@endsection