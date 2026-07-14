@extends('layouts.main')

@section('main-content')



    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">Proses Produksi</h4>
                <p class="text-muted mb-0 small">Kelola dan pantau seluruh data proses produksi</p>
            </div>
            <a href="{{ route('proses-produksi.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="bx bx-plus fs-5"></i>
                Tambah Data
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bx bx-check-circle fs-5"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bx bx-error-circle fs-5"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Card --}}
        <div class="card mb-4 border-0 shadow-sm">

            {{-- Card header: judul filter + badge aktif --}}
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-filter-alt text-primary fs-5"></i>
                    <span class="fw-semibold">Filter Data</span>
                </div>
            </div>

            <div class="card-body pt-3 pb-3">
                <form action="{{ route('proses-produksi.index') }}" method="GET">

                    {{-- ── Grup 1: Pencarian teks ─────────────────────── --}}
                    <div class="mb-3">
                        <p class="text-uppercase fw-bold mb-2"
                            style="font-size:.65rem; letter-spacing:.07em; color:#a0a3b1">
                            <i class="bx bx-search me-1"></i>Pencarian
                        </p>
                        {{-- id --}}
                        <div class="row g-2">
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-hash"></i>
                                    </span>
                                    <input type="number" name="id" value="{{ request('id') }}" placeholder="ID"
                                        class="form-control form-control-sm border-start-0 ps-0" style="min-width:0">
                                </div>
                            </div>
                            {{-- no job --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-briefcase"></i>
                                    </span>
                                    <input type="text" name="job" value="{{ request('job') }}" placeholder="No JOB"
                                        class="form-control form-control-sm border-start-0 ps-0">
                                </div>
                            </div>
                            {{-- docket --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-barcode"></i>
                                    </span>
                                    <input type="text" name="designno" value="{{ request('designno') }}"
                                        placeholder="Docket" class="form-control form-control-sm border-start-0 ps-0">
                                </div>
                            </div>
                            {{-- product --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-box"></i>
                                    </span>
                                    <input type="text" name="product" value="{{ request('product') }}"
                                        placeholder="Produk" class="form-control form-control-sm border-start-0 ps-0">
                                </div>
                            </div>
                            {{-- operator --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-user"></i>
                                    </span>
                                    <input type="text" name="operator" value="{{ request('operator') }}"
                                        placeholder="Nama Operator"
                                        class="form-control form-control-sm border-start-0 ps-0">
                                </div>
                            </div>
                            {{-- proses --}}
                            <div class="col-12 col-sm-6 col-lg-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-cog"></i>
                                    </span>
                                    <select name="proses" class="form-select form-select-sm border-start-0"
                                        style="padding-left:.4rem">
                                        <option value="">Semua Proses</option>
                                        @foreach ($daftarProses as $prosesName)
                                            <option value="{{ $prosesName }}"
                                                {{ request('proses') == $prosesName ? 'selected' : '' }}>
                                                {{ $prosesName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mt-2">
                            {{-- tanggal --}}
                            <div class="col-12 col-lg-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="form-control" placeholder="Dari Tanggal">
                                    <span class="input-group-text bg-light">
                                        s/d
                                    </span>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="form-control" placeholder="Sampai Tanggal">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Tombol aksi ────────────────────────────────── --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-4">
                            <i class="bx bx-filter-alt me-1"></i>Terapkan Filter
                        </button>
                        <a href="{{ route('proses-produksi.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- filter active --}}
        @php
            $activeFilters = [];
            if (request()->filled('id')) {
                $activeFilters['ID'] = request('id');
            }
            if (request()->filled('job')) {
                $activeFilters['Job'] = request('job');
            }
            if (request()->filled('designno')) {
                $activeFilters['Docket'] = request('designno');
            }
            if (request()->filled('product')) {
                $activeFilters['Product'] = request('product');
            }
            if (request()->filled('operator')) {
                $activeFilters['Operator'] = request('operator');
            }
            if (request()->filled('proses')) {
                $activeFilters['Proses'] = request('proses');
            }
            if (request()->filled('start_date') || request()->filled('end_date')) {
                $dateStr = '';
                if (request()->filled('start_date')) {
                    $dateStr .= \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y');
                } else {
                    $dateStr .= 'Awal';
                }
                $dateStr .= ' s/d ';
                if (request()->filled('end_date')) {
                    $dateStr .= \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y');
                } else {
                    $dateStr .= 'Akhir';
                }
                $activeFilters['Tanggal'] = $dateStr;
            }
        @endphp
        {{-- jika filter active ditampilkan --}}
        @if (count($activeFilters) > 0)
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold"><i class="bx bx-info-circle me-1"></i>Filter
                        Aktif:</span>
                    @foreach ($activeFilters as $key => $val)
                        <span class="badge bg-label-primary font-monospace px-2 py-1">
                            <strong>{{ $key }}:</strong> {{ $val }}
                        </span>
                    @endforeach
                </div>
                <a href="{{ route('proses-produksi.index') }}"
                    class="btn btn-sm btn-outline-danger py-1 px-2 d-flex align-items-center gap-1">
                    <i class="bx bx-trash-alt"></i> Bersihkan Semua Filter
                </a>
            </div>
        @endif

        {{-- Main Table Card --}}
        <div class="card mb-4 border-0 shadow-sm bg-light">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="position-relative table-wrapper">
                        {{-- scroll left --}}
                        <button id="btnScrollLeft" class="scroll-overlay left" onclick="scrollTabel(-350)">
                            <i class="bx bx-chevron-left"></i>
                        </button>
                        {{-- scroll right --}}
                        <button id="btnScrollRight" class="scroll-overlay right" onclick="scrollTabel(350)">
                            <i class="bx bx-chevron-right"></i>
                        </button>
                        {{-- tabel thead --}}
                        <form action="{{ route('proses-produksi.index') }}" method="GET" class="w-100">
                            <div class="table-responsive table-scroll" id="tabelContainer">
                                <table class="table table-sm table-hover mb-0 align-middle" id="tblProduksi">
                                    <thead class="table-light text-uppercase small">
                                        <tr>
                                            <th>Job</th>
                                            <th>Docket</th>
                                            <th>Proses</th>
                                            <th>Produk</th>
                                            <th>Operator</th>
                                            <th style="width:10px">Tanggal</th>
                                            <th class="text-center">Upspk</th>
                                            <th class="text-center">Input</th>
                                            <th class="text-center">Jtdrik</th>
                                            <th class="text-center">Jtpcs</th>
                                            <th class="text-center">OutPCS</th>
                                            <th class="text-center">OutDrik</th>
                                            <th class="text-center pe-4" style="width:120px">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($prosesProduksi as $data)
                                            {{-- Compact row --}}
                                            <tr>
                                                {{-- job --}}
                                                <td>
                                                    @if ($data->job)
                                                        <a href="{{ route('proses-produksi.show', $data->job) }}"
                                                            class="fw-semibold text-primary text-decoration-none">
                                                            {{ $data->job }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="small text-nowrap">
                                                    {{ $data->designno ?? '-' }}
                                                </td>
                                                <td>
                                                    @php
                                                        // 1. Daftar warna badge khas Sneat Bootstrap
                                                        $paletWarna = [
                                                            'primary',
                                                            'success',
                                                            'warning',
                                                            'info',
                                                            'danger',
                                                            'dark',
                                                        ];

                                                        // 2. Ubah teks proses menjadi indeks angka tetap (konsisten)
                                                        $teks = $data->proses ?? 'default';
                                                        $indeksWarna = abs(crc32($teks)) % count($paletWarna);

                                                        // 3. Ambil warna terpilih
                                                        $badgeColor = $paletWarna[$indeksWarna];
                                                    @endphp

                                                    <span class="badge bg-label-{{ $badgeColor }} fw-semibold">
                                                        {{ $data->proses ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="small text-nowrap">{{ $data->product ?? '-' }}</td>
                                                <td class="small text-nowrap">{{ $data->operator ?? '-' }}</td>
                                                {{-- tanggal --}}
                                                <td class="small text-nowrap">
                                                    @if ($data->tanggal)
                                                        {{ strtoupper(substr(\Carbon\Carbon::parse($data->tanggal)->format('l'), 0, 4)) }}
                                                        - {{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center fw-semibold">
                                                    {{ $data->upspk ?? '0' }}
                                                </td>
                                                <td class="text-center fw-semibold">
                                                    <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                        data-field="input" data-value="{{ $data->input ?? 0 }}">
                                                        {{ $data->input ? number_format($data->input, 0, ',', '.') : '0' }}
                                                    </span>
                                                </td>
                                                {{-- jtdrik --}}
                                                <td class="text-center fw-semibold">
                                                    <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                        data-field="jtdrik" data-value="{{ $data->jtdrik ?? 0 }}"
                                                        data-editable="{{ strtolower($data->proses ?? '') === 'lem' ? '0' : '1' }}">
                                                        {{ $data->jtdrik ? number_format($data->jtdrik, 0, ',', '.') : '0' }}
                                                    </span>
                                                </td>
                                                {{-- pengkondisian pcs --}}
                                                <td class="text-center fw-semibold">
                                                    <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                        data-field="jtpcs" data-value="{{ $data->jtpcs ?? 0 }}"
                                                        data-editable="{{ in_array(strtolower($data->proses ?? ''), ['lem', 'sortpacking']) ? '1' : '0' }}">
                                                        {{ $data->jtpcs ? number_format($data->jtpcs, 0, ',', '.') : '0' }}
                                                    </span>
                                                </td>

                                                <td class="text-center fw-semibold">
                                                    {{ $data->outputpcs ? number_format($data->outputpcs, 0, ',', '.') : '0' }}
                                                </td>
                                                <td class="text-center fw-semibold">
                                                    {{ $data->outputdrik ? number_format($data->outputdrik, 0, ',', '.') : '0' }}
                                                </td>
                                                {{-- Toggle button — opens offcanvas --}}
                                                <td class="text-center pe-4 d-flex gap-1">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-2 py-1"
                                                        title="Lihat semua detail" style="font-size:.75rem"
                                                        onclick="showDetail({{ json_encode([
                                                            'id' => $data->id,
                                                            'tanggal' => $data->tanggal ? strtolower(\Carbon\Carbon::parse($data->tanggal)->format('l - d - m - y')) : '-',
                                                            'job' => $data->job ?? '-',
                                                            'proses' => $data->proses ?? '-',
                                                            'product' => $data->product ?? '-',
                                                            'designno' => $data->designno ?? '-',
                                                            'operator' => $data->operator ?? '-',
                                                            'totaljam' => $data->totaljam ?? '0',
                                                            'shift' => $data->shift ?? '0',
                                                            'po' => $data->po ?? '0',
                                                            'input' => $data->input ?? '0',
                                                            'jtpcs' => $data->jtpcs ?? '0',
                                                            'jtdrik' => $data->jtdrik ?? '0',
                                                            'upspk' => $data->upspk ?? '0',
                                                            'outputpcs' => $data->outputpcs ?? '0',
                                                            'outputdrik' => $data->outputdrik ?? '0',
                                                            'total_pengerjaan_drik' => $data->total_pengerjaan_drik ?? '0',
                                                            'total_pengerjaan_pcs' => $data->total_pengerjaan_pcs ?? '0',
                                                        ]) }})">Detail
                                                        <i class="bx bx-show" style="font-size:.75rem"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="bx bx-data fs-1 d-block mb-2 opacity-25"></i>
                                                    Belum ada data proses produksi yang tersimpan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                GRAND TOTAL
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($total['input'], 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($total['jtdrik'], 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($total['jtpcs'], 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($total['outputpcs'], 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                {{ number_format($total['outputdrik'], 0, ',', '.') }}
                                            </td>

                                            <td></td>
                                        </tr>
                                    </tfoot>
                                    </tbody>

                                </table>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($prosesProduksi->hasPages())
                    <div class="card-footer bg-transparent border-top py-3 px-4">
                        {{-- Paksa menggunakan view Bootstrap 5 --}}
                        {{ $prosesProduksi->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Toast container for notifications -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090">
            <div id="liveToast" class="toast align-items-center text-white border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        {{-- offcanvas detail --}}
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetail" style="width: 420px;"
            aria-labelledby="offcanvasDetailLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title d-flex align-items-center gap-2" id="offcanvasDetailLabel">
                    <i class="bx bx-layer text-primary fs-4"></i>
                    Detail Proses Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body" id="offcanvasBody">
                {{-- Filled dynamically by JS --}}
            </div>

        </div>

        {{-- css --}}
        <style>
            .table> :not(caption)>*>* {
                padding: 1rem 0.70rem;
            }

            .table-wrapper {
                position: relative;
                overflow: hidden;
            }

            .table-scroll {
                overflow-x: auto;
                scrollbar-width: none;
            }

            .table-scroll::-webkit-scrollbar {
                display: none;
            }

            .scroll-overlay {
                position: absolute;
                top: 0;
                bottom: 0;
                opacity: 0.8;
                width: 36px;
                border: none;
                transition: opacity .25s ease, background-color .25s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 20;
                color: white;
                background: rgba(105, 108, 255, 0.45);
                backdrop-filter: blur(3px);
                -webkit-backdrop-filter: blur(8px);
                /* Safari */
                cursor: pointer;
                box-shadow: inset 0 0 0 1px rgba(105, 108, 255, .08);
            }

            .scroll-overlay:hover {
                opacity: 1;
                background: rgba(105, 108, 255, 0.7);
            }

            .scroll-overlay.left {
                left: 0;
                border-right: 1px solid rgba(0, 0, 0, .05);
            }

            .scroll-overlay.right {
                right: 0;
                border-left: 1px solid rgba(0, 0, 0, .05);
            }

            .scroll-overlay i {
                font-size: 78px;
            }

            .table-filter-input {
                min-width: 120px;
                width: 100%;
                font-size: .75rem;
                padding: .25rem .4rem;
            }

            .header-filter-wrap {
                position: relative;
            }

            .header-filter-toggle {
                color: #697a8d;
                text-decoration: none;
            }

            .header-filter-toggle.active {
                color: #696cff;
            }

            .header-filter-panel {
                position: absolute;
                top: calc(100% + 6px);
                left: 0;
                z-index: 30;
                min-width: 190px;
                padding: .5rem;
                background: #fff;
                border: 1px solid #e3e6ef;
                border-radius: .5rem;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            }

            .inline-edit-cell {
                cursor: pointer;
                display: inline-block;
                min-width: 70px;
                border-bottom: 1px dashed #696cff;
                padding: 2px 4px;
                border-radius: 3px;
                transition: all 0.2s ease;
            }

            .inline-edit-cell:hover {
                color: #696cff;
                border-bottom-style: solid;
                background-color: rgba(105, 108, 255, 0.08);
            }

            .inline-edit-cell[data-editable="0"] {
                cursor: not-allowed;
                color: #89909b;
                border-bottom: none;
            }

            .inline-edit-input {
                min-width: 90px;
                max-width: 110px;
            }
        </style>

        {{-- js --}}
        <script>
            // function toast
            function showToast(message, type = 'success') {
                const toastEl = document.getElementById('liveToast');
                const toastBody = document.getElementById('toastMessage');
                toastEl.className = 'toast align-items-center text-white border-0 bg-' + (type === 'error' || type ===
                    'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'success'));
                toastBody.textContent = message;
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 4000
                });
                toast.show();
            }

            const wrapper = document.getElementById('tabelContainer');
            const btnLeft = document.getElementById('btnScrollLeft');
            const btnRight = document.getElementById('btnScrollRight');
            let offcanvasBs;
            const offcanvasBody = document.getElementById('offcanvasBody');

            function updateScrollButton() {
                btnLeft.classList.toggle('d-none', wrapper.scrollLeft <= 0);
                btnRight.classList.toggle(
                    'd-none',
                    wrapper.scrollLeft >= wrapper.scrollWidth - wrapper.clientWidth - 5
                );
            }

            // scroll event listener
            wrapper.addEventListener('scroll', updateScrollButton);
            window.addEventListener('load', updateScrollButton);

            function scrollTabel(x) {
                wrapper.scrollBy({
                    left: x,
                    behavior: 'smooth'
                });
            }

            // funciton jquery untuk inline edit
            $(function() {
                // auto-merge job search values
                $('form').on('submit', function(e) {
                    const $jobInput = $('input[name="job"]');
                    if ($jobInput.length) {
                        const newValue = $jobInput.val().trim();
                        const oldValue = '{{ request('job') }}'.trim();

                        if (newValue && oldValue && newValue !== oldValue) {
                            const oldList = oldValue.split(/[\s,;|]+/).map(s => s.trim()).filter(Boolean);
                            const newList = newValue.split(/[\s,;|]+/).map(s => s.trim()).filter(Boolean);

                            if (newList.length === 1 && !oldList.includes(newList[0])) {
                                const combined = [...oldList, newList[0]];
                                $jobInput.val(combined.join(', '));
                            }
                        }
                    }

                    // gabung docket input
                    const $docketInput = $('input[name="designno"]');
                    if ($docketInput.length) {
                        const newValue = $docketInput.val().trim();
                        const oldValue = '{{ request('designno') }}'.trim();

                        if (newValue && oldValue && newValue !== oldValue) {
                            const oldList = oldValue.split(/[\s,;|]+/).map(s => s.trim()).filter(Boolean);
                            const newList = newValue.split(/[\s,;|]+/).map(s => s.trim()).filter(Boolean);

                            if (newList.length === 1 && !oldList.includes(newList[0])) {
                                const combined = [...oldList, newList[0]];
                                $docketInput.val(combined.join(', '));
                            }
                        }
                    }
                });
                // filter header toggle
                $('.header-filter-toggle').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = $(this).data('target');
                    const $target = $('#' + targetId);

                    $('.header-filter-panel').addClass('d-none');
                    $('.header-filter-toggle').removeClass('active');

                    if ($target.hasClass('d-none')) {
                        $target.removeClass('d-none');
                        $(this).addClass('active');
                    }
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.header-filter-wrap').length) {
                        $('.header-filter-panel').addClass('d-none');
                        $('.header-filter-toggle').removeClass('active');
                    }
                });

                // variabel untuk menyimpan sel aktif dan nilai awalnya
                let activeCell = null;
                let activeValue = '';

                // fungsi untuk membatalkan inline edit
                function cancelInlineEdit() {
                    if (activeCell) {
                        $(activeCell).html(activeValue);
                        activeCell = null;
                        activeValue = '';
                    }
                }

                // fungsi untuk menyimpan perubahan inline edit
                function saveInlineEdit($input) {
                    const id = $input.data('id');
                    const field = $input.data('field');
                    const value = $input.val();
                    const cell = $input.closest('.inline-edit-cell');

                    // Validasi input: pastikan nilai adalah angka
                    if (value === '' || isNaN(value)) {
                        showToast('Nilai harus berupa angka.', 'danger');
                        cancelInlineEdit();
                        return;
                    }

                    // Kirim data ke server menggunakan AJAX
                    $.ajax({
                        url: '{{ route('proses-produksi.inline-update') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id,
                            field: field,
                            value: value
                        },
                        success: function(response) {
                            const values = response.values || {};
                            const row = cell.closest('tr');

                            cell.data('value', value);
                            cell.text(parseFloat(value).toLocaleString('id-ID', {
                                maximumFractionDigits: 0
                            }));

                            row.find('.inline-edit-cell').each(function() {
                                const $span = $(this);
                                const field = $span.data('field');

                                if (field && values[field] !== undefined) {
                                    $span.data('value', values[field]);
                                    $span.text(parseFloat(values[field]).toLocaleString('id-ID', {
                                        maximumFractionDigits: 0
                                    }));
                                }
                            });

                            activeCell = null;
                            activeValue = '';

                            if (response.message) {
                                showToast(response.message, 'success');
                            }
                        },
                        error: function(xhr) {
                            cancelInlineEdit();
                            const msg = xhr.responseJSON?.message || 'Gagal memperbarui data.';
                            showToast(msg, 'danger');
                        }
                    });
                }

                // fungsi untuk menangani double click pada sel yang dapat diedit
                $(document).on('dblclick', '.inline-edit-cell', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const cell = $(this);
                    const field = cell.data('field');
                    const isEditable = cell.data('editable') === 1 || cell.data('editable') === '1';

                    // handle jika field adalah jtpcs dan tidak dapat diedit, maka tidak bisa di edit
                    if (field === 'jtpcs' && !isEditable) {
                        return;
                    }

                    // handle jika ada cell yang sedang diedit
                    if (activeCell && activeCell !== this) {
                        $(activeCell).html(activeValue);
                    }

                    activeCell = this;
                    activeValue = cell.html();

                    const id = cell.data('id');
                    const value = cell.data('value') ?? '';

                    cell.html(
                        `<input type="text" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${value}" />`
                    );
                    cell.find('input').focus().select();
                });

                // fungsi untuk menangani keydown pada input inline edit
                $(document).on('keydown', '.inline-edit-input', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        saveInlineEdit($(this));
                    }

                    if (e.which === 27) {
                        e.preventDefault();
                        cancelInlineEdit();
                    }
                });

                $(document).on('focusout', '.inline-edit-input', function() {
                    saveInlineEdit($(this));
                });
            });


            // Function to initialize the offcanvas detail
            document.addEventListener('DOMContentLoaded', function() {
                const offcanvasEl = document.getElementById('offcanvasDetail');
                offcanvasBs = new bootstrap.Offcanvas(offcanvasEl);
                updateScrollButton();
            });

            // function to show detail
            function showDetail(d) {

                // ── Label map : [label, value, icon, highlight] ──────────────
                const sections = [{
                        heading: 'Informasi Umum',
                        rows: [{
                                icon: 'bx-hash',
                                label: 'ID',
                                val: d.id
                            },
                            {
                                icon: 'bx-calendar',
                                label: 'Tanggal',
                                val: d.tanggal
                            },
                            {
                                icon: 'bx-briefcase',
                                label: 'Job',
                                val: d.job
                            },
                            {
                                icon: 'bx-cog',
                                label: 'Proses',
                                val: d.proses,
                                badge: true
                            },
                            {
                                icon: 'bx-box',
                                label: 'Produk',
                                val: d.product
                            },
                            {
                                icon: 'bx-barcode',
                                label: 'Docket',
                                val: d.designno
                            },
                            {
                                icon: 'bx-user',
                                label: 'Operator',
                                val: d.operator
                            },
                        ]
                    },
                    {
                        heading: 'Jadwal & Plan',
                        rows: [{
                                icon: 'bx-time',
                                label: 'Total Jam',
                                val: d.totaljam
                            },
                            {
                                icon: 'bx-transfer-alt',
                                label: 'Shift',
                                val: d.shift
                            },
                            {
                                icon: 'bx-list-ol',
                                label: 'PO',
                                val: d.po
                            },
                            {
                                icon: 'bx-arrow-to-bottom',
                                label: 'Input',
                                val: d.input
                            },
                        ]
                    },
                    {
                        heading: 'Output & Hasil',
                        rows: [{
                                icon: 'bx-package',
                                label: 'JT PCS',
                                val: d.jtpcs
                            },
                            {
                                icon: 'bx-package',
                                label: 'JT Drik',
                                val: d.jtdrik
                            },
                            {
                                icon: 'bx-stats',
                                label: 'UPS PK',
                                val: d.upspk
                            },
                            {
                                icon: 'bx-check-square',
                                label: 'Output PCS',
                                val: d.outputpcs,
                                highlight: true
                            },
                            {
                                icon: 'bx-check-square',
                                label: 'Output Drik',
                                val: d.outputdrik,
                                highlight: true
                            },
                            {
                                icon: 'bx-calculator',
                                label: 'Total Pengerjaan Drik',
                                val: d.total_pengerjaan_drik
                            },
                            {
                                icon: 'bx-calculator',
                                label: 'Total Pengerjaan PCS',
                                val: d.total_pengerjaan_pcs
                            },
                        ]
                    }
                ];

                // Render the sections into HTML
                let html = '';

                sections.forEach(sec => {
                    html += `
      <p class="text-uppercase fw-bold small text-muted mb-2 mt-4">${sec.heading}</p>
      <div class="list-group list-group-flush mb-1">`;

                    sec.rows.forEach(r => {
                        const val = r.badge ?
                            `<span class="badge bg-label-primary fw-normal">${r.val}</span>` :
                            r.highlight ?
                            `<span class="fw-bold text-primary">${r.val}</span>` :
                            `<span class="text-body-emphasis">${r.val}</span>`;

                        html += `
        <div class="list-group-item px-0 py-2 d-flex align-items-center gap-3 border-0 border-bottom">
          <span class="text-muted" style="width:20px"><i class="bx ${r.icon}"></i></span>
          <span class="text-muted small" style="width:160px">${r.label}</span>
          ${val}
        </div>`;
                    });

                    html += `</div>`;
                });

                offcanvasBody.innerHTML = html;
                offcanvasBs.show();
            }
        </script>

    @endsection
