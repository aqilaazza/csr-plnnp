@extends('layouts.app')
@section('title', 'Edit Kelayakan')
@push('styles')
    <style>
        /* Field yang datanya otomatis diambil dari Proposal - dibekukan (readonly) */
        .field-locked {
            background-color: #e9ecef !important;
            cursor: not-allowed;
        }

               /* ===== Dynamic List ===== */
        .dynamic-item{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:10px;
        }

        .dynamic-number{
            width:20px;
            text-align:center;
            font-weight:600;
            font-size:12px;
        }

        .dynamic-item textarea{
            width:1200px;          /* bisa ubah 750-850 sesuai selera */
            min-height:50px;
            resize:vertical;
        }

        .action-group{
            display:flex;
            align-items:center;
            gap:10px;
            margin-left:12px;
        }

        .btn-action{
            background:none !important;
            border:none !important;
            box-shadow:none !important;
            padding:4px;
            width:auto;
            height:auto;
        }

        .btn-action i{
            font-size:20px;
            transition:.2s;
        }

        .btn-add-row i{
            color:#78C841;
        }

        .btn-remove i{
            color:#dc3545;
        }

        .btn-add-row:hover i{
            color:#5fad31;
            transform:scale(1.15);
        }

        .btn-remove:hover i{
            color:#bb2d3b;
            transform:scale(1.15);
        }

        .action-buttons{
            display:flex;
            gap:10px;
            align-items:center;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-semibold mb-0">Edit Form Kelayakan</h5>
                        <a href="{{ route('kelayakan.index') }}" class="btn bg-secondary-subtle text-dark">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>

                    <form method="POST" action="{{ route('kelayakan.update', $kelayakan->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- 1. PROPOSAL (dibekukan, tidak bisa diganti saat edit) --}}
                        <div class="mb-3">
                            <label for="proposal" class="form-label">Proposal</label>
                            <input type="text" class="form-control field-locked" id="proposal"
                                value="{{ $kelayakan->proposal->judul ?? '-' }}" readonly tabindex="-1">
                        </div>

                        {{-- 2. NAMA PROGRAM (otomatis dari Proposal->judul, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Program</label>
                            <input type="text" class="form-control field-locked"
                                value="{{ $kelayakan->proposal->judul ?? '-' }}" readonly tabindex="-1">
                        </div>

                        {{-- 3. TIPOLOGI (otomatis dari Proposal->tipologi, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Tipologi</label>
                            <input type="text" class="form-control field-locked"
                                value="{{ $kelayakan->proposal->tipologi->deskripsi ?? '-' }}" readonly tabindex="-1">
                        </div>

                        {{-- 4. DASAR PELAKSANAAN (input user) --}}
                        <div class="mb-3">
                            <label for="dasar_pelaksanaan" class="form-label">Dasar Pelaksanaan</label>
                            <textarea class="form-control @error('dasar_pelaksanaan') is-invalid @enderror" id="dasar_pelaksanaan"
                                name="dasar_pelaksanaan">{{ old('dasar_pelaksanaan', $kelayakan->dasar_pelaksanaan) }}</textarea>
                            @error('dasar_pelaksanaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 5. LATAR BELAKANG (input user, dynamic list) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Latar Belakang</label>   
                            </div>

                            <div id="latar-list" class="dynamic-list-wrapper"></div>

                            <input
                                type="hidden"
                                name="latar_belakang"
                                id="latar-hidden"
                                value="{{ old('latar_belakang', $kelayakan->latar_belakang) }}">

                            @error('latar_belakang')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- 6. TUJUAN (input user, dynamic list) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Tujuan</label>
                            </div>

                            <div id="tujuan-list" class="dynamic-list-wrapper"></div>

                            <input type="hidden" name="tujuan" id="tujuan-hidden" value="{{ old('tujuan', $kelayakan->tujuan) }}">

                            @error('tujuan')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 7. INDIKATOR LINGKUNGAN (input user, dynamic list) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">
                                    Indikator Lingkungan
                                </label>
                            </div>

                            <div id="lingkungan-list" class="dynamic-list-wrapper"></div>

                            <input
                                type="hidden"
                                name="indikator_lingkungan"
                                id="lingkungan-hidden"
                                value="{{ old('indikator_lingkungan', $kelayakan->indikator_lingkungan) }}">
                        </div>

                        {{-- 8. INDIKATOR SOSIAL (input user, dynamic list) --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <label class="form-label mb-0">
                                    Indikator Sosial
                                </label>
                            </div>

                            <div id="sosial-list" class="dynamic-list-wrapper"></div>

                            <input
                                type="hidden"
                                name="indikator_sosial"
                                id="sosial-hidden"
                                value="{{ old('indikator_sosial', $kelayakan->indikator_sosial) }}">
                        </div>

                        {{-- 9. JUMLAH PENERIMA MANFAAT (input user) --}}
                        <div class="mb-3">
                            <label for="jumlah_penerima_manfaat" class="form-label">Jumlah Penerima Manfaat</label>
                            <input type="text" class="form-control" id="jumlah_penerima_manfaat"
                                name="jumlah_penerima_manfaat"
                                value="{{ old('jumlah_penerima_manfaat', $kelayakan->jumlah_penerima_manfaat) }}">
                        </div>

                        {{-- 10. ASAL INSTANSI (otomatis dari Proposal->instansi_pengajuan, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Asal Instansi</label>
                            <input type="text" class="form-control field-locked"
                                value="{{ $kelayakan->proposal->instansi_pengajuan ?? '-' }}" readonly tabindex="-1">
                        </div>

                        {{-- CONTACT PERSON (otomatis dari Proposal, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" class="form-control field-locked"
                                value="{{ $kelayakan->contact_person ?? '-' }}" readonly tabindex="-1">
                        </div>

                        {{-- 11. KATEGORI STAKEHOLDER (otomatis dari Proposal->kategoriInstansi, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Kategori Stakeholder</label>
                            <input type="text" class="form-control field-locked"
                                value="{{ $kelayakan->proposal->kategoriInstansi->nama ?? $kelayakan->jenis_stakeholder ?? '-' }}"
                                readonly tabindex="-1">
                        </div>

                        {{-- 12. PEJABAT INSTANSI / MENGETAHUI (input user) --}}
                        <div class="mb-3">
                            <label for="pejabat_instansi" class="form-label">Pejabat Instansi</label>
                            <textarea class="form-control" id="pejabat_instansi" name="pejabat_instansi">{{ old('pejabat_instansi', $kelayakan->pejabat_instansi) }}</textarea>
                        </div>

                        {{-- 13. BANTUAN YANG DIAJUKAN (otomatis dari Proposal, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Bantuan yang Diajukan</label>
                            @php
                                $barang = $kelayakan->proposal->barang_pengajuan ?? '-';
                                $nominal = $kelayakan->proposal->nominal_pengajuan ?? null;
                                $bantuanText = $nominal
                                    ? $barang . ' senilai Rp ' . number_format($nominal, 0, ',', '.')
                                    : $barang;
                            @endphp
                            <input type="text" class="form-control field-locked" value="{{ $bantuanText }}"
                                readonly tabindex="-1">
                        </div>

                        {{-- NOMINAL DISETUJUI (otomatis dari Proposal->nominal_disetujui, dibekukan) --}}
                        <div class="mb-3">
                            <label class="form-label">Nominal Disetujui</label>
                            @php
                                $nominalDisetujui = $kelayakan->proposal->nominal_disetujui ?? null;
                            @endphp
                            <input type="text" class="form-control field-locked"
                                value="{{ $nominalDisetujui ? 'Rp ' . number_format($nominalDisetujui, 0, ',', '.') : '-' }}"
                                readonly tabindex="-1">
                        </div>

                        <div class="mb-3">
                            <label for="data_terdahulu" class="form-label">Data Terdahulu</label>
                            <textarea class="form-control" id="data_terdahulu" name="data_terdahulu">{{ old('data_terdahulu', $kelayakan->data_terdahulu) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prioritas" class="form-label">Prioritas</label>
                                <select name="prioritas" id="prioritas"
                                    class="form-control @error('prioritas') is-invalid @enderror" required>
                                    <option value="">-- Pilih Prioritas --</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}"
                                            {{ old('prioritas', $kelayakan->prioritas) == $i ? 'selected' : '' }}>
                                            Prioritas {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="dampak" class="form-label">Dampak</label>
                                @php
                                    $labelDampak = [
                                        1 => 'Tidak ada dampak',
                                        2 => 'Kecil',
                                        3 => 'Sedang',
                                        4 => 'Tinggi',
                                        5 => 'Sangat Tinggi',
                                    ];
                                @endphp
                                <select name="dampak" id="dampak"
                                    class="form-control @error('dampak') is-invalid @enderror" required>
                                    <option value="">-- Pilih Dampak --</option>
                                    @foreach ($labelDampak as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('dampak', $kelayakan->dampak) == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dampak')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
                            <textarea class="form-control" id="catatan_khusus" name="catatan_khusus">{{ old('catatan_khusus', $kelayakan->catatan_khusus) }}</textarea>
                        </div>

                        <div class="alert alert-light border d-flex align-items-center gap-2 mb-4">
                            <i class="fas fa-circle-info text-secondary"></i>
                            <small class="mb-0">PDF akan dibuat ulang otomatis dan nomor revisi akan bertambah setelah
                                data disimpan.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('kelayakan.index') }}" class="btn bg-secondary-subtle text-dark">Batal</a>
                            <button type="submit" style="background-color: #78C841; color: white;" class="btn">
                                Simpan Perubahan &amp; Generate Ulang PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        $(document).ready(function(){

        function initDynamicList(config){
            const $list = $(config.list);
                const $hidden = $(config.hidden);

                function renumber() {
                    const $items = $list.find('.dynamic-item');
                    const total = $items.length;

                    $items.each(function(index) {

                        const $number = $(this).find('.dynamic-number');

                        if(total > 1){
                            $number.text((index+1)+'.').removeClass('d-none');
                        }else{
                            $number.text('').addClass('d-none');
                        }

                    });
                }

                function syncHidden() {

                    let values=[];

                    $list.find('textarea').each(function(){

                        const val=$(this).val().trim();

                        if(val!=''){
                            values.push(val);
                        }

                    });

                    let result;

                    if(values.length>1){
                        result=values.map((v,i)=>(i+1)+'. '+v);
                    }else{
                        result=values;
                    }

                    $hidden.val(result.join('\n'));

                }

                function addRow(text=''){

                    const row=$(`
                        <div class="dynamic-item">

                            <span class="dynamic-number"></span>

                            <textarea
                                class="form-control"
                                rows="3"
                                placeholder="${config.placeholder}">
                            </textarea>

                            <div class="action-buttons">

                                <button
                                    type="button"
                                    class="btn btn-action btn-add-row">
                                    <i class="fas fa-plus"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-action btn-remove">
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>

                        </div>
                    `);

                    row.find('textarea').val(text);

                    $list.append(row);

                    renumber();

                }

                const oldValue=$hidden.val();

                if(oldValue && oldValue.trim()!=''){

                    oldValue
                        .split('\n')
                        .filter(x=>x.trim()!='')
                        .forEach(function(line){

                            addRow(
                                line.replace(/^\d+\.\s*/,'')
                            );

                        });

                }else{

                    addRow();

                }

                $list.on('click','.btn-add-row',function(){

                    addRow();

                });

                $list.on('click','.btn-remove',function(){

                    if($list.find('.dynamic-item').length>1){

                        $(this).closest('.dynamic-item').remove();

                        renumber();

                    }else{

                        $(this).closest('.dynamic-item')
                            .find('textarea')
                            .val('');

                    }

                });

                $('form').on('submit',function(){

                    syncHidden();

                });
        }

        initDynamicList({
            list:'#tujuan-list',
            hidden:'#tujuan-hidden',
            button:'#btn-add-tujuan',
            placeholder:'Tulis tujuan...'
        });

        initDynamicList({
            list:'#latar-list',
            hidden:'#latar-hidden',
            button:'#btn-add-latar',
            placeholder:'Tulis latar belakang...'
        });

        initDynamicList({
            list:'#lingkungan-list',
            hidden:'#lingkungan-hidden',
            button:'#btn-add-lingkungan',
            placeholder:'Tulis indikator lingkungan...'
        });

        initDynamicList({
            list:'#sosial-list',
            hidden:'#sosial-hidden',
            button:'#btn-add-sosial',
            placeholder:'Tulis indikator sosial...'
        });

    });
    </script>
@endpush