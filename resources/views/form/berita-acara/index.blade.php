@extends('layouts.app')
 @section('title', 'CSR PLN Nusantara Power UP Paiton')
 @push('styles')
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
         rel="stylesheet" />

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

         .table-responsive table {
             margin-left: 0 !important;
             width: 100% !important;
         }

         .dataTables_scrollBody {
             border-top: none !important;
         }

         /* Efek bayangan halus di sisi kanan kolom freeze */
         .dtfc-fixed-left {
             background: white;
             box-shadow: 3px 0 5px rgba(0, 0, 0, 0.1);
             z-index: 2 !important;
         }

         .bantuan-item{
            border: 2px solid #adb5bd !important;
            border-radius: .5rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
         }
     </style>
 @endpush
 @section('content')
     <div class="row">
         <div class="col-lg-12 d-flex align-items-stretch">
             <div class="card w-100">
                 <div class="card-body p-4">
                     <h5 class="card-title fw-semibold mb-4">Data Berita Acara</h5>
                     <div class="mb-3 text-end">
                         <button style="background-color: #78C841; color: white;" class="btn" data-bs-toggle="modal"
                             data-bs-target="#createModal">
                             <i class="fas fa-plus me-1"></i> Tambah Berita Acara
                         </button>
                     </div>
                     <div class="table-responsive">
                         <table id="tipologiTable" class="table table-bordered mb-0 align-middle">
                             <thead class="text-dark fs-4">
                                 <tr>
                                     <th>
                                         <h6 class="fw-semibold mb-0">No</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">Business Support</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">Proposal</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">Nama</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">Jabatan</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">File</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">Upload</h6>
                                     </th>
                                     <th>
                                         <h6 class="fw-semibold mb-0">Aksi</h6>
                                     </th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($beritaacara as $data)
                                     <tr>
                                         <td>
                                             <h6 class="fw-normal mb-0">{{ $loop->iteration }}</h6>
                                         </td>
                                         <td style="white-space: normal;">
                                             <p class="mb-0 fw-normal">
                                                 {{ $data->businessSupport->nama ?? $data->bisnis_support_lainnya ?? '-' }}
                                             </p>
                                         </td>
                                         <td style="white-space: normal;">
                                             <h6 class="fw-normal mb-0">{{ $data->proposal->judul }}</h6>
                                         </td>
                                         <td>
                                             <p class="mb-0 fw-normal">{{ $data->nama_penerima }}</p>
                                         </td>
                                         <td>
                                             <p class="mb-0 fw-normal">{{ $data->jabatan_penerima }}</p>
                                         </td>
                                         <td>
                                             <p class="mb-0 fw-normal"> <a href="{{ asset('storage/' . $data->file_pdf) }}"
                                                     target="_blank">Lihat
                                                     PDF</a></p>

                                         </td>
                                         <td class="text-center">
                                             @if ($data->file_upload)
                                                 <a href="{{ asset('storage/' . $data->file_upload) }}" target="_blank"
                                                     class="text-primary fw-normal">Lihat File</a>
                                             @else
                                                 <a href="#" class="text-primary fw-normal" data-bs-toggle="modal"
                                                     data-bs-target="#uploadModal" data-id="{{ $data->id }}">
                                                     Upload File
                                                 </a>
                                             @endif
                                         </td>

                                         <td>
                                             <div class="d-flex justify-content-center align-items-center gap-2">
                                                 {{-- Tombol Edit --}}
                                                 <button type="button"
                                                     class="btn btn-sm btn-light border-0 text-primary btn-edit"
                                                     data-bs-toggle="modal" data-bs-target="#editModal"
                                                     data-id="{{ $data->id }}"
                                                     data-proposal="{{ $data->proposal->judul }}"
                                                     data-nama="{{ $data->nama_penerima }}"
                                                     data-jabatan="{{ $data->jabatan_penerima }}"
                                                     data-business-support-id="{{ $data->business_support_id }}"
                                                     data-bisnis-lainnya="{{ $data->bisnis_support_lainnya }}">
                                                     <i class="fas fa-edit"></i>
                                                 </button>

                                                 {{-- Tombol Hapus --}}
                                                 <button type="button"
                                                     class="btn btn-sm btn-light border-0 text-danger btn-delete"
                                                     data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                     data-id="{{ $data->id }}"
                                                     data-nama="{{ $data->proposal->judul }}">
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

     <!-- Modal Edit -->
     <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
         <div class="modal-dialog">
             <form method="POST" id="editForm">
                 @csrf
                 @method('PUT')
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="editModalLabel">Edit Berita Acara</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                     </div>
                     <div class="modal-body">
                         {{-- TAMBAHAN: Dropdown Business Support (Edit) - dipindah ke paling atas --}}
                         <div class="mb-3">
                             <label for="edit-business_support_choice" class="form-label">Business Support</label>
                             <select name="business_support_choice" id="edit-business_support_choice"
                                 class="form-select" required>
                                 <option value="">-- Pilih Business Support --</option>
                                 @foreach ($businessSupport as $bs)
                                     <option value="{{ $bs->id }}">{{ $bs->nama }}</option>
                                 @endforeach
                                 <option value="lainnya">Lainnya (ketik manual)</option>
                             </select>
                         </div>

                         <div class="mb-3 d-none" id="edit-bisnis-support-lainnya-wrapper">
                             <label for="edit-bisnis_support_lainnya" class="form-label">Nama Business Support
                                 (Manual)</label>
                             <input type="text" class="form-control" name="bisnis_support_lainnya"
                                 id="edit-bisnis_support_lainnya" placeholder="Ketik nama business support">
                         </div>
                         {{-- END TAMBAHAN --}}

                         <div class="mb-3">
                             <label for="edit-proposal" class="form-label">Proposal</label>
                             <input type="text" class="form-control" id="edit-proposal" disabled>
                         </div>
                         <div class="mb-3">
                             <label for="edit-nama" class="form-label">Nama Penerima</label>
                             <textarea class="form-control" id="edit-nama" name="nama_penerima" required></textarea>
                         </div>

                         <div class="mb-3">
                             <label for="edit-jabatan" class="form-label">Jabatan Penerima</label>
                             <textarea class="form-control" id="edit-jabatan" name="jabatan_penerima" required></textarea>
                         </div>

                         <div id="edit-bantuan-wrapper"></div>
                         <button type="button" id="edit-add-bantuan" class="btn btn-sm btn-secondary mb-3">+ Tambah
                             Jenis
                             Bantuan</button>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn bg-secondary-subtle text-dark"
                             data-bs-dismiss="modal">Batal</button>
                         <button type="submit" style="background-color: #78C841; color: white;" class="btn">Simpan
                             Perubahan</button>
                     </div>
                 </div>
             </form>
         </div>
     </div>
     <!-- Modal Create -->
     <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
         <div class="modal-dialog">
             <form method="POST" action="{{ route('berita-acara.store') }}">
                 @csrf
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="createModalLabel">Tambah Berita Acara</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                     </div>

                     <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                         {{-- TAMBAHAN: Dropdown Business Support (Create) - dipindah ke paling atas --}}
                         <div class="mb-3">
                             <label for="business_support_choice" class="form-label">Business Support</label>
                             <select name="business_support_choice" id="business_support_choice"
                                 class="form-select {{ $errors->has('business_support_choice') ? 'is-invalid' : '' }}"
                                 required>
                                 <option value="">-- Pilih Business Support --</option>
                                 @foreach ($businessSupport as $bs)
                                     <option value="{{ $bs->id }}"
                                         {{ old('business_support_choice') == $bs->id ? 'selected' : '' }}>
                                         {{ $bs->nama }}
                                     </option>
                                 @endforeach
                                 <option value="lainnya" {{ old('business_support_choice') == 'lainnya' ? 'selected' : '' }}>
                                     Lainnya (ketik manual)
                                 </option>
                             </select>
                             @error('business_support_choice')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>

                         <div class="mb-3 {{ old('business_support_choice') == 'lainnya' ? '' : 'd-none' }}"
                             id="bisnis-support-lainnya-wrapper">
                             <label for="bisnis_support_lainnya" class="form-label">Nama Business Support
                                 (Manual)</label>
                             <input type="text"
                                 class="form-control {{ $errors->has('bisnis_support_lainnya') ? 'is-invalid' : '' }}"
                                 name="bisnis_support_lainnya" id="bisnis_support_lainnya"
                                 value="{{ old('bisnis_support_lainnya') }}"
                                 placeholder="Ketik nama business support">
                             @error('bisnis_support_lainnya')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>
                         {{-- END TAMBAHAN --}}

                         <div class="mb-3">
                             <label class="form-label">Proposal</label>
                             <select name="proposal_id" id="select-proposal"
                                 class="form-select {{ $errors->has('proposal_id') ? 'is-invalid' : '' }}" required>
                                 <option value="">-- Pilih Proposal --</option>
                                 @foreach ($proposal as $item)
                                     <option value="{{ $item->id }}"
                                         {{ old('proposal_id') == $item->id ? 'selected' : '' }}>
                                         {{ $item->judul }}
                                     </option>
                                 @endforeach
                             </select>
                             @error('proposal_id')
                                 <div class="invalid-feedback">{{ $message }}</div>
                             @enderror
                         </div>

                         <div class="mb-3">
                             <label for="nama_penerima" class="form-label">Nama Pihak Kedua</label>
                             <textarea class="form-control" id="nama_penerima" name="nama_penerima" required>{{ old('nama_penerima') }}</textarea>
                         </div>

                         <div class="mb-3">
                             <label for="jabatan_penerima" class="form-label">Jabatan Pihak Kedua</label>
                             <textarea class="form-control" id="jabatan_penerima" name="jabatan_penerima" required>{{ old('jabatan_penerima') }}</textarea>
                         </div>

                    <div id="bantuan-wrapper">

                        <div class="bantuan-item mb-4 border border-2 rounded p-3 position-relative">

                            <button type="button"
                                class="btn btn-danger btn-sm btn-remove position-absolute top-0 end-0 m-2">
                                &times;
                            </button>

                            <div class="mb-3">
                                <label class="form-label">Jenis Bantuan</label>
                                <input type="text"
                                    name="jenis_bantuan[]"
                                    class="form-control"
                                    placeholder="Contoh: Bantuan Cangkul"
                                    required>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number"
                                        name="jumlah_barang[]"
                                        class="form-control jumlah-input"
                                        placeholder="Contoh: 100">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Satuan</label>
                                    <input type="text"
                                        name="satuan_barang[]"
                                        class="form-control satuan-input"
                                        placeholder="Unit / Paket / Bibit">
                                </div>

                            </div>

                            <div class="mt-3">
                                <label class="form-label">Nominal</label>
                                <input type="text"
                                    name="nominal[]"
                                    class="form-control nominal-input"
                                    placeholder="Masukkan Nominal">
                            </div>

                        </div>

                    </div>
                    <button type="button"id="add-bantuan"class="btn btn-sm btn-primary">
                        + Tambah Jenis Bantuan
                    </button>
                    
                </div> {{-- tutup modal-body --}}

                     <div class="modal-footer">
                         <button type="button" class="btn bg-secondary-subtle text-dark"
                             data-bs-dismiss="modal">Batal</button>
                         <button type="submit" style="background-color: #78C841; color:whitesmoke"
                             class="btn">Tambah</button>
                     </div>
                 </div>
             </form>
         </div>
     </div>

     <script>
         const allowed = ['jpg', 'jpeg', 'png', 'heic', 'pdf'];
         const fileInput = document.getElementById('fileInput');
         const btnUpload = document.getElementById('btnUpload');
         const errorMsg = document.getElementById('fileError');

         fileInput.addEventListener('change', function() {
             const file = this.files[0];

             if (!file) return;

             const ext = file.name.split('.').pop().toLowerCase();

             if (!allowed.includes(ext)) {
                 errorMsg.classList.remove('d-none');
                 btnUpload.disabled = true;
             } else {
                 errorMsg.classList.add('d-none');
                 btnUpload.disabled = false;
             }
         });
     </script>

     <script>
         const uploadModal = document.getElementById('uploadModal');
         uploadModal.addEventListener('show.bs.modal', function(event) {
             const button = event.relatedTarget;
             const id = button.getAttribute('data-id');
             const form = document.getElementById('uploadForm');
             form.action = `/berita-acara/${id}/upload`;
         });
     </script>


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

    {{-- Modal Upload --}}
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="uploadForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">
                            Upload File
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input
                            type="file"
                            name="file_upload"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,.heic"
                            required>

                        <small class="text-muted">
                            File yang diizinkan: PDF atau Gambar
                        </small>

                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn btn-success">
                            Upload
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('scripts')

        <!-- jQuery paling awal -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- DataTables + Plugins -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
        <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

        <!-- Select2 -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        {{-- DATATABLE & SELECT2 --}}
        <script>
            $(document).ready(function() {

                $('#select-proposal').select2({
                    theme: 'bootstrap4',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#createModal'),
                    language: {
                        searching: function() {
                            return "Mencari...";
                        },
                        inputTooShort: function() {
                            return "Ketik untuk mencari proposal...";
                        },
                        noResults: function() {
                            return "Tidak ada hasil ditemukan";
                        }
                    }
                });

                $('#select-proposal').on('select2:open', function() {
                    $('.select2-search__field')
                        .attr('placeholder', 'Ketik untuk mencari proposal...');
                });

                $('#createModal').on('hidden.bs.modal', function() {
                    $('#select-proposal').val(null).trigger('change');
                });

                $('#createModal').on('shown.bs.modal', function() {
                    $('#select-proposal').select2({
                        dropdownParent: $('#createModal'),
                        width: '100%',
                        theme: 'bootstrap4',
                        placeholder: '-- Pilih Proposal --'
                    });
                });

                $('#tipologiTable').DataTable({
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
                    drawCallback: function() {
                        $('.dataTables_paginate > .pagination')
                            .addClass('pagination-sm');
                    }
                });

            });
        </script>

        {{-- UTILITY FUNCTION --}}
        <script>

            // FORMAT RUPIAH
            function formatRupiah(input) {

                let angka = input.value.replace(/\D/g, '');

                if (angka === '') {
                    input.value = '';
                    return;
                }

                input.value = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            // TOGGLE JENIS BANTUAN
            function toggleJenis(item) {

                let jenis = item.find('input[name="jenis_bantuan[]"]')
                                .val()
                                .toLowerCase()
                                .trim();

                // JENIS BANTUAN UANG / DANA
                if (jenis.includes('dana') || jenis.includes('uang')) {

                    // Nominal bisa diisi
                    item.find('.nominal-input')
                        .prop('disabled', false);

                    // Jumlah dikunci dan dikosongkan
                    item.find('.jumlah-input')
                        .val('')
                        .prop('disabled', true);

                    // Satuan dikunci dan dikosongkan
                    item.find('.satuan-input')
                        .val('')
                        .prop('disabled', true);

                }

                // JENIS BANTUAN BARANG
                else {

                    // Nominal dikunci dan dikosongkan
                    item.find('.nominal-input')
                        .val('')
                        .prop('disabled', true);

                    // Jumlah bisa diisi
                    item.find('.jumlah-input')
                        .prop('disabled', false);

                    // Satuan bisa diisi
                    item.find('.satuan-input')
                        .prop('disabled', false);

                }

            }

            // JUMLAH HANYA BOLEH ANGKA
            $(document).on('input', '.jumlah-input', function () {

                this.value = this.value.replace(/\D/g, '');

            });

            // FORMAT NOMINAL
            $(document).on('input', '.nominal-input', function () {

                formatRupiah(this);

            });

        </script>

        {{-- CREATE MODAL --}}
        <script>

            // TAMBAH ITEM BANTUAN
            document.getElementById('add-bantuan')
                .addEventListener('click', function () {

                    let wrapper = document.getElementById('bantuan-wrapper');

                    let newItem = document.createElement('div');

                    newItem.className =
                        'bantuan-item mb-4 border rounded p-3 position-relative';

                    newItem.innerHTML = `
                        <button type="button"
                            class="btn btn-danger btn-sm btn-remove position-absolute top-0 end-0 m-2">
                            &times;
                        </button>

                        <div class="mb-3">
                            <label class="form-label">Jenis Bantuan</label>
                            <input type="text"
                                name="jenis_bantuan[]"
                                class="form-control"
                                placeholder="Contoh: Bantuan Cangkul"
                                required>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <input type="number"
                                    name="jumlah_barang[]"
                                    class="form-control jumlah-input"
                                    placeholder="Contoh: 100">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan</label>
                                <input type="text"
                                    name="satuan_barang[]"
                                    class="form-control satuan-input"
                                    placeholder="Unit/Paket/Bibit">
                            </div>

                        </div>

                        <div class="mt-3">
                            <label class="form-label">Nominal</label>
                            <input
                                type="text"
                                name="nominal[]"
                                class="form-control nominal-input"
                                placeholder="Masukkan Nominal">
                        </div>
                    `;

                    wrapper.appendChild(newItem);

                    let lastItem = $(wrapper)
                        .find('.bantuan-item')
                        .last();

                    // Item baru: semua input dibuka dulu
                    lastItem.find('.jumlah-input').prop('disabled', false);
                    lastItem.find('.satuan-input').prop('disabled', false);
                    lastItem.find('.nominal-input').prop('disabled', false);

                });

            // CEK JENIS BANTUAN
            function toggleJenis(item) {

                let jenis = item.find('input[name="jenis_bantuan[]"]')
                    .val()
                    .trim()
                    .toLowerCase();

                let jumlahInput = item.find('.jumlah-input');
                let satuanInput = item.find('.satuan-input');
                let nominalInput = item.find('.nominal-input');

                // JIKA JENIS = UANG / DANA
                if (jenis.includes('uang') || jenis.includes('dana')) {

                    // Nominal bisa diisi
                    nominalInput
                        .prop('disabled', false);

                    // Jumlah dikunci
                    jumlahInput
                        .val('')
                        .prop('disabled', true);

                    // Satuan dikunci
                    satuanInput
                        .val('')
                        .prop('disabled', true);

                }

                // JIKA JENIS BELUM DIISI ATAU BUKAN UANG / DANA
                else {

                    // Jumlah bisa diisi
                    jumlahInput
                        .prop('disabled', false);

                    // Satuan bisa diisi
                    satuanInput
                        .prop('disabled', false);

                    // Nominal bisa diisi
                    nominalInput
                        .val('')
                        .prop('disabled', false);

                }

            }

            // JIKA JENIS BANTUAN DIUBAH
            $(document).on(
                'input',
                'input[name="jenis_bantuan[]"]',
                function () {

                    toggleJenis(
                        $(this).closest('.bantuan-item')
                    );

                }
            );

            // JUMLAH / SATUAN DIISI
            // =========================
            $(document).on(
                'input',
                '.jumlah-input, .satuan-input',
                function () {

                    let item = $(this).closest('.bantuan-item');

                    let jenis = item
                        .find('input[name="jenis_bantuan[]"]')
                        .val()
                        .trim()
                        .toLowerCase();

                    // Hanya berlaku untuk jenis BARANG
                    if (
                        !jenis.includes('uang') &&
                        !jenis.includes('dana')
                    ) {

                        let jumlah = item
                            .find('.jumlah-input')
                            .val()
                            .trim();

                        let satuan = item
                            .find('.satuan-input')
                            .val()
                            .trim();

                        // Jika Jumlah atau Satuan diisi
                        if (jumlah !== '' || satuan !== '') {

                            // Nominal dikunci
                            item.find('.nominal-input')
                                .val('')
                                .prop('disabled', true);

                        } else {

                            // Jika Jumlah dan Satuan kosong
                            // Nominal tetap dikunci karena ini Barang
                            item.find('.nominal-input')
                                .prop('disabled', true);

                        }

                    }

                }
            );

            // =========================
            // NOMINAL DIISI
            // =========================
            $(document).on(
                'input',
                '.nominal-input',
                function () {

                    // Format Rupiah
                    formatRupiah(this);

                    let item = $(this).closest('.bantuan-item');

                    let jenis = item
                        .find('input[name="jenis_bantuan[]"]')
                        .val()
                        .trim()
                        .toLowerCase();

                    // Hanya berlaku jika bukan UANG / DANA
                    if (
                        !jenis.includes('uang') &&
                        !jenis.includes('dana')
                    ) {

                        let nominal = $(this)
                            .val()
                            .trim();

                        // Jika nominal diisi
                        if (nominal !== '') {

                            // Jumlah dikunci
                            item.find('.jumlah-input')
                                .val('')
                                .prop('disabled', true);

                            // Satuan dikunci
                            item.find('.satuan-input')
                                .val('')
                                .prop('disabled', true);

                        }

                    }

                }
            );

            // =========================
            // HAPUS ITEM BANTUAN
            // =========================
            document.getElementById('bantuan-wrapper')
                .addEventListener('click', function (e) {

                    if (e.target.classList.contains('btn-remove')) {

                        e.target
                            .closest('.bantuan-item')
                            .remove();

                    }

                });

        </script>

        {{-- BUSINESS SUPPORT --}}
        <script>

            $(document).ready(function(){

                $('#business_support_choice').on('change', function(){

                    if($(this).val() === 'lainnya'){

                        $('#bisnis-support-lainnya-wrapper')
                            .removeClass('d-none');

                        $('#bisnis_support_lainnya')
                            .prop('required', true);

                    }else{

                        $('#bisnis-support-lainnya-wrapper')
                            .addClass('d-none');

                        $('#bisnis_support_lainnya')
                            .prop('required', false)
                            .val('');

                    }

                });

                $('#createModal').on('hidden.bs.modal', function(){

                    $('#business_support_choice').val('');

                    $('#bisnis-support-lainnya-wrapper')
                        .addClass('d-none');

                    $('#bisnis_support_lainnya')
                        .prop('required', false)
                        .val('');

                });

                $('#edit-business_support_choice').on('change', function(){

                    if($(this).val() === 'lainnya'){

                        $('#edit-bisnis-support-lainnya-wrapper')
                            .removeClass('d-none');

                        $('#edit-bisnis_support_lainnya')
                            .prop('required', true);

                    }else{

                        $('#edit-bisnis-support-lainnya-wrapper')
                            .addClass('d-none');

                        $('#edit-bisnis_support_lainnya')
                            .prop('required', false)
                            .val('');

                    }

                });

            });

        </script>

        {{-- EDIT MODAL --}}
        <script>

        // =====================================================
        // BUKA MODAL EDIT
        // =====================================================

        $(document).on('click', '.btn-edit', function () {

            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const jabatan = $(this).data('jabatan');
            const proposal = $(this).data('proposal');

            const businessSupportId = $(this).data('business-support-id');
            const bisnisLainnya = $(this).data('bisnis-lainnya');

            // =================================================
            // ISI DATA UTAMA
            // =================================================

            $('#edit-nama').val(nama);
            $('#edit-jabatan').val(jabatan);
            $('#edit-proposal').val(proposal);

            // Action Form
            $('#editForm').attr(
                'action',
                '/berita-acara/' + id
            );


            // =================================================
            // BUSINESS SUPPORT
            // =================================================

            if (bisnisLainnya) {

                $('#edit-business_support_choice')
                    .val('lainnya')
                    .trigger('change');

                $('#edit-bisnis_support_lainnya')
                    .val(bisnisLainnya);

            } else if (businessSupportId) {

                $('#edit-business_support_choice')
                    .val(String(businessSupportId))
                    .trigger('change');

            } else {

                $('#edit-business_support_choice')
                    .val('')
                    .trigger('change');

            }


            // =================================================
            // KOSONGKAN BANTUAN LAMA
            // =================================================

            $('#edit-bantuan-wrapper').html('');


            // =================================================
            // AMBIL DATA BANTUAN DARI DATABASE
            // =================================================

            $.get(
                `/berita-acara/${id}/bantuan`,
                function (data) {

                    $('#edit-bantuan-wrapper').html('');

                    data.forEach(function (item) {

                        let row = `

                        <div class="bantuan-item mb-4 border border-2 rounded p-3 position-relative">

                            <button
                                type="button"
                                class="btn btn-danger btn-sm btn-remove position-absolute top-0 end-0 m-2">
                                &times;
                            </button>


                            <!-- JENIS BANTUAN -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Jenis Bantuan
                                </label>

                                <input
                                    type="text"
                                    name="jenis_bantuan[]"
                                    value="${item.jenis ?? ''}"
                                    class="form-control"
                                    required>

                            </div>


                            <!-- JUMLAH & SATUAN -->
                            <div class="row">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Jumlah
                                    </label>

                                    <input
                                        type="number"
                                        name="jumlah_barang[]"
                                        value="${item.jumlah ?? ''}"
                                        class="form-control jumlah-input"
                                        placeholder="Contoh: 100">

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Satuan
                                    </label>

                                    <input
                                        type="text"
                                        name="satuan_barang[]"
                                        value="${item.satuan ?? ''}"
                                        class="form-control satuan-input"
                                        placeholder="Unit/Paket/Bibit">

                                </div>

                            </div>


                            <!-- NOMINAL -->
                            <div class="mt-3">

                                <label class="form-label">
                                    Nominal
                                </label>

                                <input
                                    type="text"
                                    name="nominal[]"
                                    value="${item.nominal ?? ''}"
                                    class="form-control nominal-input"
                                    placeholder="Masukkan Nominal">

                            </div>

                        </div>

                        `;


                        // Masukkan bantuan ke modal
                        $('#edit-bantuan-wrapper')
                            .append(row);


                        // Ambil item terakhir
                        let lastItem = $('#edit-bantuan-wrapper .bantuan-item')
                            .last();


                        // Format nominal jika ada
                        let nominalInput = lastItem
                            .find('.nominal-input')[0];

                        if (
                            nominalInput &&
                            nominalInput.value.trim() !== ''
                        ) {

                            formatRupiah(nominalInput);

                        }


                        // Terapkan aturan input
                        updateInputBantuanEdit(lastItem);

                    });

                }
            );

        });



        // =====================================================
        // FORMAT RUPIAH
        // =====================================================

        function formatRupiah(input) {

            let angka = input.value.replace(/\D/g, '');

            if (angka === '') {

                input.value = '';

                return;

            }

            input.value = new Intl.NumberFormat(
                'id-ID',
                {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }
            ).format(angka);

        }

        // =====================================================
        // UPDATE INPUT BERDASARKAN KONDISI
        // =====================================================

        function updateInputBantuanEdit(item) {

            let jenis = item
                .find('input[name="jenis_bantuan[]"]')
                .val()
                .trim()
                .toLowerCase();

            let jumlahInput = item.find('.jumlah-input');
            let satuanInput = item.find('.satuan-input');
            let nominalInput = item.find('.nominal-input');

            let jumlah = jumlahInput.val().trim();
            let satuan = satuanInput.val().trim();
            let nominal = nominalInput.val().trim();


            // =================================================
            // JIKA JENIS = UANG / DANA
            // =================================================

            if (
                jenis.includes('uang') ||
                jenis.includes('dana')
            ) {

                // Nominal bisa diisi
                nominalInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

                // Jumlah dikunci
                jumlahInput
                    .prop('readonly', true)
                    .addClass('bg-light');

                // Satuan dikunci
                satuanInput
                    .prop('readonly', true)
                    .addClass('bg-light');

                return;
            }


            // =================================================
            // JIKA BUKAN UANG / DANA
            // =================================================

            // Jika Nominal sudah diisi
            if (nominal !== '') {

                // Jumlah dikunci
                jumlahInput
                    .prop('readonly', true)
                    .addClass('bg-light');

                // Satuan dikunci
                satuanInput
                    .prop('readonly', true)
                    .addClass('bg-light');

                // Nominal bisa diedit
                nominalInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

            }

            // Jika Jumlah atau Satuan sudah diisi
            else if (
                jumlah !== '' ||
                satuan !== ''
            ) {

                // Jumlah bisa diedit
                jumlahInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

                // Satuan bisa diedit
                satuanInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

                // Nominal dikunci
                nominalInput
                    .prop('readonly', true)
                    .addClass('bg-light');

            }

            // Jika semuanya kosong
            else {

                // Jumlah bisa diisi
                jumlahInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

                // Satuan bisa diisi
                satuanInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

                // Nominal bisa diisi
                nominalInput
                    .prop('readonly', false)
                    .removeClass('bg-light');

            }

        }



        // =====================================================
        // JIKA JENIS BANTUAN DIUBAH
        // =====================================================

        $(document).on(
            'input',
            '#editModal input[name="jenis_bantuan[]"]',
            function () {

                let item = $(this)
                    .closest('.bantuan-item');

                updateInputBantuanEdit(item);

            }
        );

        // =====================================================
        // NOMINAL DIISI
        // =====================================================

        $(document).on(
            'input',
            '#editModal .nominal-input',
            function () {

                let item = $(this)
                    .closest('.bantuan-item');

                let jenis = item
                    .find('input[name="jenis_bantuan[]"]')
                    .val()
                    .trim()
                    .toLowerCase();


                // Format Rupiah
                formatRupiah(this);


                // Jika Uang / Dana
                // Jumlah dan Satuan memang sudah dikunci
                if (
                    jenis.includes('uang') ||
                    jenis.includes('dana')
                ) {

                    return;

                }


                let nominal = $(this)
                    .val()
                    .trim();


                // Jika Nominal diisi
                if (nominal !== '') {

                    // Jumlah dikunci
                    item.find('.jumlah-input')
                        .prop('readonly', true)
                        .addClass('bg-light');

                    // Satuan dikunci
                    item.find('.satuan-input')
                        .prop('readonly', true)
                        .addClass('bg-light');

                } else {

                    // Jika Nominal dikosongkan,
                    // cek kembali kondisi input
                    updateInputBantuanEdit(item);

                }

            }
        );

        // =====================================================
        // JUMLAH / SATUAN DIISI
        // =====================================================

        $(document).on(
            'input',
            '#editModal .jumlah-input, #editModal .satuan-input',
            function () {

                let item = $(this)
                    .closest('.bantuan-item');


                let jenis = item
                    .find('input[name="jenis_bantuan[]"]')
                    .val()
                    .trim()
                    .toLowerCase();


                // Jika Uang / Dana
                // Tidak perlu diproses
                if (
                    jenis.includes('uang') ||
                    jenis.includes('dana')
                ) {

                    return;

                }


                let jumlah = item
                    .find('.jumlah-input')
                    .val()
                    .trim();

                let satuan = item
                    .find('.satuan-input')
                    .val()
                    .trim();


                // Jika Jumlah atau Satuan diisi
                if (
                    jumlah !== '' ||
                    satuan !== ''
                ) {

                    // Nominal dikunci
                    item.find('.nominal-input')
                        .prop('readonly', true)
                        .addClass('bg-light');

                } else {

                    // Jika keduanya kosong
                    // cek kembali kondisi input
                    updateInputBantuanEdit(item);

                }

            }
        );

        // =====================================================
        // JUMLAH HANYA BOLEH ANGKA
        // =====================================================

        $(document).on(
            'input',
            '#editModal .jumlah-input',
            function () {

                this.value = this.value.replace(/\D/g, '');

            }
        );

        // =====================================================
        // TAMBAH BANTUAN BARU
        // =====================================================

        $('#edit-add-bantuan').on(
            'click',
            function () {

                let row = `

                <div class="bantuan-item mb-4 border border-2 rounded p-3 position-relative">

                    <button
                        type="button"
                        class="btn btn-danger btn-sm btn-remove position-absolute top-0 end-0 m-2">
                        &times;
                    </button>


                    <div class="mb-3">

                        <label class="form-label">
                            Jenis Bantuan
                        </label>

                        <input
                            type="text"
                            name="jenis_bantuan[]"
                            class="form-control"
                            required>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <label class="form-label">
                                Jumlah
                            </label>

                            <input
                                type="number"
                                name="jumlah_barang[]"
                                class="form-control jumlah-input"
                                placeholder="Contoh: 100">

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Satuan
                            </label>

                            <input
                                type="text"
                                name="satuan_barang[]"
                                class="form-control satuan-input"
                                placeholder="Unit/Paket/Bibit">

                        </div>

                    </div>


                    <div class="mt-3">

                        <label class="form-label">
                            Nominal
                        </label>

                        <input
                            type="text"
                            name="nominal[]"
                            class="form-control nominal-input"
                            placeholder="Masukkan Nominal">

                    </div>

                </div>

                `;


                $('#edit-bantuan-wrapper')
                    .append(row);


                // Ambil item baru
                let lastItem = $('#edit-bantuan-wrapper .bantuan-item')
                    .last();


                // Semua input baru bisa diisi
                updateInputBantuanEdit(lastItem);

            }
        );

        // =====================================================
        // HAPUS BANTUAN
        // =====================================================

        $(document).on(
            'click',
            '#editModal .btn-remove',
            function () {

                $(this)
                    .closest('.bantuan-item')
                    .remove();

            }
        );

    </script>

        {{-- DELETE MODAL --}}
        <script>
            $(document).on('click', '.btn-delete', function () {

                const id   = $(this).data('id');
                const nama = $(this).data('nama');

                // Tampilkan nama pada modal konfirmasi
                $('#deleteDataName').text(nama);

                // Set action form delete
                $('#deleteForm').attr('action', '/berita-acara/' + id);

            });
        </script>

        {{-- UPLOAD MODAL --}}
        <script>
        $(document).on('click', '[data-bs-target="#uploadModal"]', function(){

            const id = $(this).data('id');

            $('#uploadForm').attr(
                'action',
                '/berita-acara/' + id + '/upload'
            );

        });
        </script>

         {{-- TOAST --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {

                const toastElList = [].slice.call(document.querySelectorAll('.toast'));

                toastElList.map(function (toastEl) {

                    const toast = new bootstrap.Toast(toastEl, {
                        delay: 8000
                    });

                    toast.show();

                });

            });
        </script>

        {{-- AUTO-OPEN MODAL CREATE JIKA ADA VALIDATION ERROR --}}
        <script>
            @if ($errors->any())
                document.addEventListener('DOMContentLoaded', function () {
                    var modal = new bootstrap.Modal(document.getElementById('createModal'));
                    modal.show();
                });
            @endif
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