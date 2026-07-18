@extends('layouts.main')

@section('main-content')



    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">Proses Produksi</h4>
                <p class="text-muted mb-0 small">Kelola dan pantau seluruh data proses produksi</p>
            </div>
            {{-- <a href="{{ route('proses-produksi.rangkuman') }}" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="bx bx-plus fs-5"></i>
                Rangkuman
            </a> --}}
            <div class="d-flex gap-2">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                    <i class="bx bx-history fs-5"></i>
                    Activity Log
                </a>
                <a href="{{ route('proses-produksi.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="bx bx-plus fs-5"></i>
                    Tambah Data
                </a>
            </div>


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
                            {{-- no job --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="position-relative">
                                    <div id="jobSearchWrapper"
                                        class="input-group input-group-sm flex-nowrap align-items-center bg-white"
                                        style="border: 1px solid #d9dee3; border-radius: 0.375rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; height: 31px; overflow: hidden;">
                                        <span class="input-group-text bg-transparent border-0 text-muted pe-1">
                                            <i class="bx bx-briefcase"></i>
                                        </span>
                                        <div id="scrollableContainer"
                                            class="d-flex align-items-center flex-grow-1 flex-nowrap"
                                            style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; height: 100%;">
                                            <div id="selectedJobsContainer"
                                                class="d-flex flex-nowrap align-items-center gap-1 py-1 ps-0">
                                            </div>
                                            <input type="text" id="searchJob"
                                                class="form-control form-control-sm border-0 ps-1 bg-transparent"
                                                placeholder="No Job" autocomplete="off"
                                                style="min-width: 50px; flex: 1 1 auto; font-size: 0.75rem; box-shadow: none; height: 100%; border: none;">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchJobsHidden" name="job" value="{{ request('job') }}">
                                    <!-- Dropdown hasil pencarian -->
                                    <div id="jobSuggestions"
                                        class="list-group position-absolute w-100 shadow-sm border rounded-3 overflow-hidden bg-white d-none"
                                        style="z-index: 1055; top: calc(100% + 4px); max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                            </div>
                            {{-- docket --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="position-relative">
                                    <div id="docketSearchWrapper"
                                        class="input-group input-group-sm flex-nowrap align-items-center bg-white"
                                        style="border: 1px solid #d9dee3; border-radius: 0.375rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; height: 31px; overflow: hidden;">
                                        <span class="input-group-text bg-transparent border-0 text-muted pe-1">
                                            <i class="bx bx-barcode"></i>
                                        </span>
                                        <div id="docketScrollableContainer"
                                            class="d-flex align-items-center flex-grow-1 flex-nowrap"
                                            style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; height: 100%;">
                                            <div id="selectedDocketsContainer"
                                                class="d-flex flex-nowrap align-items-center gap-1 py-1 ps-0">

                                            </div>
                                            <input type="text" id="searchDocket"
                                                class="form-control form-control-sm border-0 ps-1 bg-transparent"
                                                placeholder="docket" autocomplete="off"
                                                style="min-width: 50px; flex: 1 1 auto; font-size: 0.75rem; box-shadow: none; height: 100%; border: none;">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchDocketsHidden" name="designno"
                                        value="{{ request('designno') }}">
                                    <!-- Dropdown hasil pencarian -->
                                    <div id="docketSuggestions"
                                        class="list-group position-absolute w-100 shadow-sm border rounded-3 overflow-hidden bg-white d-none"
                                        style="z-index: 1055; top: calc(100% + 4px); max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                            </div>
                            {{-- product --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="position-relative">
                                    <div id="productSearchWrapper"
                                        class="input-group input-group-sm flex-nowrap align-items-center bg-white"
                                        style="border: 1px solid #d9dee3; border-radius: 0.375rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; height: 31px; overflow: hidden;">
                                        <span class="input-group-text bg-transparent border-0 text-muted pe-1">
                                            <i class="bx bx-box"></i>
                                        </span>
                                        <div id="productScrollableContainer"
                                            class="d-flex align-items-center flex-grow-1 flex-nowrap"
                                            style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; height: 100%;">
                                            <div id="selectedProductsContainer"
                                                class="d-flex flex-nowrap align-items-center gap-1 py-1 ps-0">
                                            </div>
                                            <input type="text" id="searchProduct"
                                                class="form-control form-control-sm border-0 ps-1 bg-transparent"
                                                placeholder="Produk" autocomplete="off"
                                                style="min-width: 50px; flex: 1 1 auto; font-size: 0.75rem; box-shadow: none; height: 100%; border: none;">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchProductsHidden" name="product"
                                        value="{{ request('product') }}">
                                    <!-- Dropdown hasil pencarian -->
                                    <div id="productSuggestions"
                                        class="list-group position-absolute w-100 shadow-sm border rounded-3 overflow-hidden bg-white d-none"
                                        style="z-index: 1055; top: calc(100% + 4px); max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                            </div>
                            {{-- operator --}}
                            <div class="col-12 col-sm-4 col-lg-2">
                                <div class="position-relative">
                                    <div id="operatorSearchWrapper"
                                        class="input-group input-group-sm flex-nowrap align-items-center bg-white"
                                        style="border: 1px solid #d9dee3; border-radius: 0.375rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; height: 31px; overflow: hidden;">
                                        <span class="input-group-text bg-transparent border-0 text-muted pe-1">
                                            <i class="bx bx-user"></i>
                                        </span>
                                        <div id="operatorScrollableContainer"
                                            class="d-flex align-items-center flex-grow-1 flex-nowrap"
                                            style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; height: 100%;">
                                            <div id="selectedOperatorsContainer"
                                                class="d-flex flex-nowrap align-items-center gap-1 py-1 ps-0">
                                            </div>
                                            <input type="text" id="searchOperator"
                                                class="form-control form-control-sm border-0 ps-1 bg-transparent"
                                                placeholder="Nama Operator" autocomplete="off"
                                                style="min-width: 50px; flex: 1 1 auto; font-size: 0.75rem; box-shadow: none; height: 100%; border: none;">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchOperatorsHidden" name="operator"
                                        value="{{ request('operator') }}">
                                    <!-- Dropdown hasil pencarian -->
                                    <div id="operatorSuggestions"
                                        class="list-group position-absolute w-100 shadow-sm border rounded-3 overflow-hidden bg-white d-none"
                                        style="z-index: 1055; top: calc(100% + 4px); max-height: 200px; overflow-y: auto;">
                                    </div>
                                </div>
                            </div>
                            {{-- proses --}}
                            <div class="col-12 col-sm-4 col-lg-1">
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
                            {{-- tanggal --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                                        <i class="bx bx-calendar"></i>
                                    </span>

                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="form-control border-start-0">

                                    <span class="input-group-text bg-light">
                                        s/d
                                    </span>

                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="form-control">
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
        <div class="card mb-4 border-0 shadow-sm bg-light" id="mainTableCard">
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
                                            @php
                                                $curSort = request()->query('sort');
                                                $curDir = request()->query('dir', 'desc');

                                                function _sortHeader($key, $label, $curSort, $curDir)
                                                {
                                                    $isActive = $curSort == $key;
                                                    $dirToggle = $isActive
                                                        ? ($curDir == 'asc'
                                                            ? 'desc'
                                                            : 'asc')
                                                        : 'asc';
                                                    // determine icon: if active and asc -> up, if active and desc -> down, else down (inactive)
                                                    if ($isActive && $curDir == 'asc') {
                                                        $icon = 'bx-chevron-up';
                                                        $activeClass = 'active';
                                                    } elseif ($isActive && $curDir == 'desc') {
                                                        $icon = 'bx-chevron-down';
                                                        $activeClass = 'active';
                                                    } else {
                                                        $icon = 'bx-chevron-down';
                                                        $activeClass = 'inactive';
                                                    }

                                                    $html = "<a href=\"#\" class=\"ajax-sort text-decoration-none d-inline-flex align-items-center gap-1\" data-sort=\"{$key}\" data-dir=\"{$dirToggle}\">";
                                                    $html .= "<span class=\"me-1\">{$label}</span>";
                                                    $html .= "<i class=\"bx {$icon} sort-icon {$activeClass} ms-1\"></i>";
                                                    $html .= '</a>';
                                                    return $html;
                                                }
                                            @endphp
                                            <th>{!! _sortHeader('job', 'Job', $curSort, $curDir) !!}</th>
                                            <th>{!! _sortHeader('docket', 'Docket', $curSort, $curDir) !!}</th>
                                            <th>{!! _sortHeader('proses', 'Proses', $curSort, $curDir) !!}</th>
                                            <th>{!! _sortHeader('product', 'Produk', $curSort, $curDir) !!}</th>
                                            <th>{!! _sortHeader('operator', 'Operator', $curSort, $curDir) !!}</th>
                                            <th style="width:10px">{!! _sortHeader('tanggal', 'Tanggal', $curSort, $curDir) !!}</th>
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
                                                        $paletWarna = [
                                                            'primary',
                                                            'success',
                                                            'warning',
                                                            'info',
                                                            'danger',
                                                            'dark',
                                                        ];
                                                        $teks = $data->proses ?? 'default';
                                                        $indeksWarna = abs(crc32($teks)) % count($paletWarna);
                                                        $badgeColor = $paletWarna[$indeksWarna];
                                                    @endphp
                                                    <span class="badge bg-label-{{ $badgeColor }} fw-semibold">
                                                        {{ $data->proses ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="small text-nowrap">
                                                    @if (strlen($data->product ?? '') > 20)
                                                        <span class="product-toggle cursor-pointer"
                                                            style="cursor: pointer;" data-full="{{ $data->product }}"
                                                            data-short="{{ \Illuminate\Support\Str::limit($data->product, 20) }}">
                                                            {{ \Illuminate\Support\Str::limit($data->product, 20) }}
                                                        </span>
                                                    @else
                                                        {{ $data->product ?? '-' }}
                                                    @endif
                                                </td>
                                                <td class="small text-nowrap">
                                                    <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                        data-field="operator" data-value="{{ $data->operator ?? '' }}">
                                                        @if (strlen($data->operator ?? '') > 20)
                                                            <span class="operator-toggle cursor-pointer"
                                                                style="cursor: pointer;"
                                                                data-full="{{ $data->operator }}"
                                                                data-short="{{ \Illuminate\Support\Str::limit($data->operator, 20) }}">
                                                                {{ \Illuminate\Support\Str::limit($data->operator, 20) }}
                                                            </span>
                                                        @else
                                                            {{ $data->operator ?? '-' }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="small text-nowrap">
                                                    @if ($data->tanggal)
                                                        {{ strtoupper(substr(\Carbon\Carbon::parse($data->tanggal)->format('l'), 0, 4)) }}
                                                        - {{ \Carbon\Carbon::parse($data->tanggal)->format('d-m-y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center fw-semibold">
                                                    <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                        data-field="upspk" data-value="{{ $data->upspk ?? 0 }}">
                                                        {{ $data->upspk ? number_format($data->upspk, 0, ',', '.') : '0' }}
                                                    </span>
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
                                                {{-- Manage Dropdown --}}
                                                <td class="text-center pe-4">
                                                    <div class="dropdown">
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary dropdown-toggle d-flex align-items-center gap-1 px-2 py-1"
                                                            data-bs-toggle="dropdown" aria-expanded="false"
                                                            style="font-size:.75rem">
                                                            <i class="bx bx-cog" style="font-size:.8rem"></i> Manage
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-2"
                                                                    href="#"
                                                                    onclick="showDetail({{ json_encode([
                                                                        'id' => $data->id,
                                                                        'tanggal' => $data->tanggal ? strtolower(\Carbon\Carbon::parse($data->tanggal)->format('l - d - m - y')) : '-',
                                                                        'job' => $data->job ?? '-',
                                                                        'proses' => $data->proses ?? '-',
                                                                        'product' => $data->product ?? '-',
                                                                        'designno' => $data->designno ?? '-',
                                                                        'operator' => $data->operator ?? '-',
                                                                        'set' => $data->set ?? '-',
                                                                        'run' => $data->run ?? '-',
                                                                        'finish' => $data->finish ?? '-',
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
                                                                    ]) }})">
                                                                    <i class="bx bx-show text-primary"></i> Detail
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item d-flex align-items-center gap-2"
                                                                    href="#"
                                                                    onclick="showActivityLog({{ $data->id }}, '{{ addslashes($data->job ?? '-') }}', '{{ addslashes($data->product ?? '-') }}')">
                                                                    <i class="bx bx-history text-warning"></i> Riwayat
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
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
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 99999 !important; margin-top: 60px;">
            <div id="liveToast" class="toast align-items-center text-white border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        {{-- Modal Detail --}}
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title d-flex align-items-center gap-4" id="modalDetailLabel">
                            <i class="bx bx-layer text-primary fs-4"></i>
                            Detail Proses Produksi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        {{-- Filled dynamically by JS --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Activity Log --}}
        <div class="modal fade" id="modalActivityLog" tabindex="-1" aria-labelledby="modalActivityLogLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title d-flex align-items-center gap-3 mb-4" id="modalActivityLogLabel">
                            <i class="bx bx-history text-warning fs-4"></i>
                            <span>Riwayat Perubahan</span>
                            <span class="badge bg-label-warning" id="alBadgeCount"
                                style="font-size:.68rem; border-radius:8px">–</span>
                            <span class="text-muted fw-normal" id="alSubtitle" style="font-size: .8rem;">–</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" id="alModalBody">
                        <div class="d-flex align-items-center justify-content-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <small>Memuat riwayat log…</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- css --}}
        <style>
            .sort-icon {
                font-size: 18px;
                line-height: 14px;
                opacity: 1;
            }

            .sort-icon.active {
                opacity: 1;
                color: #0d6efd;
                transform: translateY(-1px);
            }

            .sort-icon.inactive {
                opacity: 1;
                color: #0d6efd;
            }

            .ajax-sort {
                cursor: pointer;
            }

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
                top: 5.5%;
                bottom: 5.5%;
                width: 36px;
                border: none;
                transition: opacity .25s ease, background-color .25s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 20;
                color: white;
                background: #373BFF;
                /* Safari */
                cursor: pointer;
                box-shadow: inset 0 0 0 1px rgba(105, 108, 255, .08);
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

            #scrollableContainer::-webkit-scrollbar {
                display: none;
            }
        </style>

        {{-- js --}}
        <script>
            // Format datetime as DD-MM-YY HH:MM for display
            function formatDateTime(val) {
                if (!val || val === '-') return '-';
                const s = String(val).trim().replace('T', ' ');
                const d = new Date(s.includes(' ') ? s : '1970-01-01 ' + s);
                if (isNaN(d.getTime())) return val;
                if (!s.includes('-') && !s.includes('/')) {
                    // Only time stored — show just HH:MM
                    return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
                }
                const dd = String(d.getDate()).padStart(2, '0');
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const yy = String(d.getFullYear()).slice(-2);
                const hh = String(d.getHours()).padStart(2, '0');
                const mi = String(d.getMinutes()).padStart(2, '0');
                return `${dd}-${mm}-${yy} ${hh}:${mi}`;
            }

            // Normalize a stored value to YYYY-MM-DDTHH:MM for datetime-local input
            function toDatetimeLocalVal(val) {
                if (!val || val === '-') return '';
                const s = String(val).trim();
                // Already datetime-local format
                if (s.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/)) return s.substring(0, 16);
                // Datetime with space separator
                if (s.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/)) return s.replace(' ', 'T').substring(0, 16);
                // Just time HH:MM or HH:MM:SS — prepend today's date
                const today = new Date().toISOString().substring(0, 10);
                const parts = s.split(':');
                const timeStr = parts.length >= 2 ?
                    parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0') :
                    '00:00';
                return today + 'T' + timeStr;
            }

            // function toast
            function showToast(message, type = 'success') {
                const toastEl = document.getElementById('liveToast');
                const toastBody = document.getElementById('toastMessage');
                toastEl.className = 'toast align-items-center text-white border-0 bg-' + (type === 'error' || type ===
                    'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'success'));
                toastBody.textContent = message;
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 2000
                });
                toast.show();
            }

            const wrapper = document.getElementById('tabelContainer');
            const btnLeft = document.getElementById('btnScrollLeft');
            const btnRight = document.getElementById('btnScrollRight');
            let modalBs;
            const modalBody = document.getElementById('modalBody');

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
                // AJAX sorting handler: intercept clicks on headers with .ajax-sort
                $(document).on('click', '.ajax-sort', function(e) {
                    e.preventDefault();
                    const sort = $(this).data('sort');
                    const dir = $(this).data('dir');

                    // Build query preserving existing querystring except sort/dir
                    const params = new URLSearchParams(window.location.search);
                    params.set('sort', sort);
                    params.set('dir', dir);

                    const url = window.location.pathname + '?' + params.toString();

                    // Fetch sorted table via AJAX and replace #mainTableCard
                    $.get(url, function(html) {
                        // Parse returned HTML and replace table card content
                        const newDoc = new DOMParser().parseFromString(html, 'text/html');
                        const newCard = newDoc.getElementById('mainTableCard');
                        if (newCard) {
                            $('#mainTableCard').replaceWith($(newCard));
                            // Update URL without reloading
                            window.history.pushState({}, '', url);

                            // Rebind scroll buttons for the new table container
                            const w = document.getElementById('tabelContainer');
                            const bl = document.getElementById('btnScrollLeft');
                            const br = document.getElementById('btnScrollRight');
                            if (w && bl && br) {
                                const update = function() {
                                    bl.classList.toggle('d-none', w.scrollLeft <= 0);
                                    br.classList.toggle('d-none', w.scrollLeft >= w.scrollWidth - w
                                        .clientWidth - 5);
                                };
                                w.removeEventListener('scroll', updateScrollButton);
                                w.addEventListener('scroll', update);
                                update();
                            }
                        }
                    }).fail(function() {
                        showToast('Gagal memuat data terurut.', 'danger');
                    });
                });
                // Reusable Autocomplete & Multi-select filter setup
                function setupAutocompleteFilter(config) {
                    const {
                        wrapperId,
                        containerId,
                        placeholderId,
                        inputId,
                        hiddenId,
                        suggestionsId,
                        type,
                        badgeClass,
                        itemKey,
                        icon,
                        allowSpaces,
                        isMulti = true
                    } = config;

                    function renderBadges() {
                        const val = $(`#${hiddenId}`).val() || '';
                        let selectedItems = val.split(',').map(function(item) {
                            return item.trim();
                        }).filter(Boolean);

                        if (!isMulti && selectedItems.length > 1) {
                            selectedItems = selectedItems.slice(0, 1);
                            $(`#${hiddenId}`).val(selectedItems[0]);
                        }

                        const $container = $(`#${containerId}`);
                        const $placeholder = $(`#${placeholderId}`);

                        $container.find(`.${badgeClass}`).remove();

                        if (selectedItems.length) {
                            $placeholder.hide();

                            selectedItems.forEach(function(item) {
                                $('<span class="badge bg-label-primary rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 ' +
                                        badgeClass +
                                        '" style="font-size: 0.7rem; line-height: 1.2; flex-shrink: 0; white-space: nowrap;">'
                                    )
                                    .append($('<span class="fw-semibold">').text(item))
                                    .append($('<i class="bx bx-x cursor-pointer" style="font-size: 11px;"></i>')
                                        .on(
                                            'click',
                                            function(e) {
                                                e.stopPropagation();
                                                removeItem(item);
                                            }))
                                    .appendTo($container);
                            });
                        } else {
                            $placeholder.show();
                        }

                        // Auto scroll container to the right
                        const container = $container.closest('.d-flex')[0];
                        if (container) {
                            setTimeout(() => {
                                container.scrollLeft = container.scrollWidth;
                            }, 50);
                        }
                    }

                    function removeItem(item) {
                        const val = $(`#${hiddenId}`).val() || '';
                        const selectedItems = val.split(',').map(function(i) {
                            return i.trim();
                        }).filter(function(i) {
                            return i && i !== item;
                        });

                        $(`#${hiddenId}`).val(selectedItems.join(', '));
                        renderBadges();
                    }

                    function addItem(item) {
                        const val = $(`#${hiddenId}`).val() || '';
                        let selectedItems = val.split(',').map(function(i) {
                            return i.trim();
                        }).filter(Boolean);

                        if (!isMulti) {
                            selectedItems = [item];
                        } else {
                            if (!selectedItems.includes(item)) {
                                selectedItems.push(item);
                            }
                        }

                        $(`#${hiddenId}`).val(selectedItems.join(', '));
                        renderBadges();
                    }

                    // Render badges initially
                    renderBadges();

                    // Keyup autocomplete suggestions
                    $(`#${inputId}`).on('keyup', function(e) {
                        if (e.which === 13) {
                            return; // Let form submit/enter handle
                        }
                        let keyword = $(this).val().trim();
                        const $suggestions = $(`#${suggestionsId}`);

                        if (keyword.length < 2) {
                            $suggestions.empty().addClass('d-none');
                            return;
                        }

                        $.ajax({
                            url: "{{ route('proses-produksi.search-suggestions') }}",
                            type: "GET",
                            data: {
                                q: keyword,
                                type: type
                            },
                            success: function(response) {
                                let html = '';

                                if (response.length) {
                                    response.forEach(function(item) {
                                        html += `
                                            <a href="#"
                                                class="list-group-item list-group-item-action border-0 px-3 py-2 d-flex align-items-center gap-2 pilih-${type}"
                                                data-value="${item[itemKey]}">
                                                <i class="bx ${icon} text-primary" style="font-size: 0.85rem;"></i>
                                                <span class="fw-semibold" style="font-size: 0.8rem;">${item[itemKey]}</span>
                                            </a>
                                        `;
                                    });
                                    $suggestions.html(html).removeClass('d-none');
                                } else {
                                    $suggestions.empty().addClass('d-none');
                                }
                            }
                        });
                    });

                    // Keydown: space, comma, semicolon to chips
                    $(`#${inputId}`).on('keydown', function(e) {
                        const isDelimiter = e.which === 188 || e.which === 186 || (!allowSpaces && e.which ===
                            32);
                        if (isDelimiter) { // comma, semicolon, space
                            const val = $(this).val().trim().replace(/[,;]+$/, '');
                            if (val) {
                                e.preventDefault();
                                addItem(val);
                                $(this).val('');
                                $(`#${suggestionsId}`).empty().addClass('d-none');
                            }
                        }
                    });

                    // Paste support
                    $(`#${inputId}`).on('paste', function(e) {
                        const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
                        const pastedData = clipboardData.getData('Text');
                        if (pastedData) {
                            const regex = allowSpaces ? /[,;|]+/ : /[\s,;|]+/;
                            const items = pastedData.split(regex).map(s => s.trim()).filter(Boolean);
                            if (items.length > 1 || (!isMulti && items.length > 0)) {
                                e.preventDefault();
                                if (!isMulti) {
                                    addItem(items[0]);
                                } else {
                                    items.forEach(function(item) {
                                        addItem(item);
                                    });
                                }
                                $(this).val('');
                                $(`#${suggestionsId}`).empty().addClass('d-none');
                            }
                        }
                    });

                    // Suggestion item click
                    $(document).on('click', `.pilih-${type}`, function(e) {
                        e.preventDefault();
                        const val = $(this).data('value');
                        addItem(val);
                        $(`#${suggestionsId}`).empty().addClass('d-none');
                        $(`#${inputId}`).val('').focus();
                    });

                    $(document).on('click', function(e) {
                        if (!$(e.target).closest(`#${suggestionsId}, #${wrapperId}`).length) {
                            $(`#${suggestionsId}`).empty().addClass('d-none');
                        }
                    });

                    // Focus/blur styling
                    $(`#${inputId}`).on('focus', function() {
                        $(`#${wrapperId}`).css({
                            'border-color': '#86b7fe',
                            'box-shadow': '0 0 0 0.25rem rgba(13, 110, 253, 0.25)'
                        });
                    }).on('blur', function() {
                        $(`#${wrapperId}`).css({
                            'border-color': '',
                            'box-shadow': ''
                        });
                    });

                    // Wrapper click focuses input
                    $(`#${wrapperId}`).on('click', function(e) {
                        if (e.target.id !== inputId && !$(e.target).closest(`.${badgeClass}`).length) {
                            $(`#${inputId}`).focus();
                        }
                    });

                    // Form submit check
                    $(`#${wrapperId}`).closest('form').on('submit', function() {
                        const typedVal = $(`#${inputId}`).val() ? $(`#${inputId}`).val().trim() : '';
                        if (typedVal) {
                            const regex = allowSpaces ? /[,;|]+/ : /[\s,;|]+/;
                            const items = typedVal.split(regex).map(s => s.trim()).filter(Boolean);
                            if (items.length > 0) {
                                if (!isMulti) {
                                    addItem(items[0]);
                                } else {
                                    items.forEach(function(item) {
                                        addItem(item);
                                    });
                                }
                            }
                            $(`#${inputId}`).val('');
                        }
                    });
                }

                // Initialize autocomplete filters for Job, Docket, Product, and Operator
                setupAutocompleteFilter({
                    wrapperId: 'jobSearchWrapper',
                    containerId: 'selectedJobsContainer',
                    placeholderId: 'selectedJobsPlaceholder',
                    inputId: 'searchJob',
                    hiddenId: 'searchJobsHidden',
                    suggestionsId: 'jobSuggestions',
                    type: 'job',
                    badgeClass: 'job-badge',
                    itemKey: 'job',
                    icon: 'bx-briefcase',
                    allowSpaces: false
                });

                setupAutocompleteFilter({
                    wrapperId: 'docketSearchWrapper',
                    containerId: 'selectedDocketsContainer',
                    placeholderId: 'selectedDocketsPlaceholder',
                    inputId: 'searchDocket',
                    hiddenId: 'searchDocketsHidden',
                    suggestionsId: 'docketSuggestions',
                    type: 'designno',
                    badgeClass: 'docket-badge',
                    itemKey: 'designno',
                    icon: 'bx-barcode',
                    allowSpaces: false,
                    isMulti: false
                });

                setupAutocompleteFilter({
                    wrapperId: 'productSearchWrapper',
                    containerId: 'selectedProductsContainer',
                    placeholderId: 'selectedProductsPlaceholder',
                    inputId: 'searchProduct',
                    hiddenId: 'searchProductsHidden',
                    suggestionsId: 'productSuggestions',
                    type: 'product',
                    badgeClass: 'product-badge',
                    itemKey: 'product',
                    icon: 'bx-box',
                    allowSpaces: true,
                    isMulti: false
                });

                setupAutocompleteFilter({
                    wrapperId: 'operatorSearchWrapper',
                    containerId: 'selectedOperatorsContainer',
                    placeholderId: 'selectedOperatorsPlaceholder',
                    inputId: 'searchOperator',
                    hiddenId: 'searchOperatorsHidden',
                    suggestionsId: 'operatorSuggestions',
                    type: 'operator',
                    badgeClass: 'operator-badge',
                    itemKey: 'operator',
                    icon: 'bx-user',
                    allowSpaces: true
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

                    // Validasi input: pastikan nilai adalah angka jika field numerik
                    const numericFields = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift'];
                    const isNumericField = numericFields.includes(field);
                    if (isNumericField && (value === '' || isNaN(value))) {
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

                            // 1. Update all matching inline-edit cells in the entire document (both tables and modal)
                            $(`.inline-edit-cell[data-id="${id}"]`).each(function() {
                                const $span = $(this);
                                const fieldName = $span.data('field');
                                if (fieldName && values[fieldName] !== undefined) {
                                    $span.data('value', values[fieldName]);
                                    const isSpanNumeric = numericFields.includes(fieldName);
                                    const isSpanTime = ['set', 'run', 'finish'].includes(fieldName);
                                    $span.text(isSpanNumeric ? parseFloat(values[fieldName])
                                        .toLocaleString('id-ID', {
                                            maximumFractionDigits: 0
                                        }) : (isSpanTime ? formatDateTime(values[fieldName]) :
                                            values[fieldName]));
                                }
                            });

                            // 2. Update modal derived values if modal is open for the same ID
                            const $modalDetail = $('#modalDetail');
                            const modalId = $modalDetail.find('.inline-edit-cell').first().data('id');
                            if (modalId === id) {
                                $modalDetail.find('.modal-derived-val').each(function() {
                                    const $span = $(this);
                                    const fieldName = $span.data('field');
                                    if (fieldName && values[fieldName] !== undefined) {
                                        const isSpanNumeric = numericFields.includes(fieldName);
                                        const isSpanTime = ['set', 'run', 'finish'].includes(
                                            fieldName);
                                        $span.text(isSpanNumeric ? parseFloat(values[fieldName])
                                            .toLocaleString('id-ID', {
                                                maximumFractionDigits: 0
                                            }) : (isSpanTime ? formatDateTime(values[
                                                fieldName]) : values[fieldName]));
                                    }
                                });
                            }


                            $.get(window.location.href, function(html) {
                                const newDoc = new DOMParser().parseFromString(html, 'text/html');

                                const newTbody = newDoc.querySelector('#tblProduksi tbody');
                                const newTfoot = newDoc.querySelector('#tblProduksi tfoot');

                                if (newTbody) {
                                    $('#tblProduksi tbody').replaceWith($(newTbody));
                                }

                                if (newTfoot) {
                                    $('#tblProduksi tfoot').replaceWith($(newTfoot));
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

                // Handler to toggle short / full product or operator name when clicked
                $(document).on('click', '.product-toggle, .operator-toggle', function() {
                    const $span = $(this);
                    const isFull = $span.data('is-full') === true;
                    if (isFull) {
                        $span.text($span.data('short'));
                        $span.data('is-full', false);
                    } else {
                        $span.text($span.data('full'));
                        $span.data('is-full', true);
                    }
                });

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
                    const isTimeField = ['set', 'run', 'finish'].includes(field);

                    // For time fields, use input type="datetime-local"
                    if (isTimeField) {
                        cell.html(
                            `<input type="datetime-local" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${toDatetimeLocalVal(value)}" style="min-width:180px" />`
                        );
                    } else {
                        cell.html(
                            `<input type="text" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${value}" />`
                        );
                    }
                    cell.find('input').focus();
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


            // Function to initialize the modal detail
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('modalDetail');
                modalBs = new bootstrap.Modal(modalEl);
                updateScrollButton();
            });

            // function to show detail
            function showDetail(d) {

                // ── Label map : [label, value, icon, highlight] ──────────────
                const sections = [{
                        heading: 'Informasi Umum',
                        rows: [{
                                icon: 'bx-cog',
                                label: 'Proses',
                                val: d.proses,
                                badge: true
                            },
                            {
                                icon: 'bx-briefcase',
                                label: 'Job',
                                val: d.job
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
                                icon: 'bx-list-ol',
                                label: 'PO',
                                val: d.po
                            },

                        ]
                    },
                    {
                        heading: 'Jadwal & Plan',
                        rows: [{
                                icon: 'bx-calendar',
                                label: 'Tanggal',
                                val: d.tanggal ? d.tanggal.toUpperCase() : '-'
                            },
                            {
                                icon: 'bx-user',
                                label: 'Operator',
                                val: d.operator,
                                field: 'operator',
                                editable: true
                            },
                            {
                                icon: 'bx-transfer-alt',
                                label: 'Shift',
                                val: d.shift,
                                field: 'shift',
                                editable: true
                            },
                            {
                                icon: 'bx-cog',
                                label: 'Set',
                                val: d.set ?? '-',
                                field: 'set',
                                editable: true
                            },
                            {
                                icon: 'bx-play',
                                label: 'Run',
                                val: d.run ?? '-',
                                field: 'run',
                                editable: true
                            },
                            {
                                icon: 'bx-check-double',
                                label: 'Finish',
                                val: d.finish ?? '-',
                                field: 'finish',
                                editable: true
                            },
                            {
                                icon: 'bx-time',
                                label: 'Total Jam',
                                val: d.totaljam,
                                field: 'totaljam'
                            },



                        ]
                    },
                    {
                        heading: 'Output & Hasil',
                        rows: [{
                                icon: 'bx-arrow-to-bottom',
                                label: 'Input',
                                val: d.input,
                                field: 'input',
                                editable: true
                            }, {
                                icon: 'bx-package',
                                label: 'JT PCS',
                                val: d.jtpcs,
                                field: 'jtpcs',
                                editable: true
                            },
                            {
                                icon: 'bx-package',
                                label: 'JT Drik',
                                val: d.jtdrik,
                                field: 'jtdrik',
                                editable: true
                            },
                            {
                                icon: 'bx-stats',
                                label: 'UPSPK',
                                val: d.upspk,
                                field: 'upspk',
                                editable: true
                            },
                            {
                                icon: 'bx-check-square',
                                label: 'Output PCS',
                                val: d.outputpcs,
                                field: 'outputpcs',
                                highlight: true
                            },
                            {
                                icon: 'bx-check-square',
                                label: 'Output Drik',
                                val: d.outputdrik,
                                field: 'outputdrik',
                                highlight: true
                            },
                            {
                                icon: 'bx-calculator',
                                label: 'Total Pengerjaan Drik',
                                val: d.total_pengerjaan_drik,
                                field: 'total_pengerjaan_drik'
                            },
                            {
                                icon: 'bx-calculator',
                                label: 'Total Pengerjaan PCS',
                                val: d.total_pengerjaan_pcs,
                                field: 'total_pengerjaan_pcs'
                            },
                        ]
                    }
                ];

                // Render the sections into HTML
                let html = '<div class="row justify-content-center g-4 p-2">';

                sections.forEach((sec, idx) => {
                    let colClass = 'col-lg-4 col-md-6';
                    let labelWidth = '140px';
                    if (idx === 0) {
                        colClass = 'col-lg-4 col-md-6';
                        labelWidth = '100px';
                    } else if (idx === 1) {
                        colClass = 'col-lg-4 col-md-6';
                        labelWidth = '100px';
                    } else if (idx === 2) {
                        colClass = 'col-lg-4 col-md-6';
                        labelWidth = '175px';
                    }

                    html += `
      <div class="${colClass}">
        <div class="card h-100 border shadow-none">
          <div class="card-body p-4">
            <h6 class="text-uppercase fw-bold text-primary mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
              ${sec.heading}
            </h6>
            <div class="list-group list-group-flush">`;

                    sec.rows.forEach(r => {
                        let valHtml = '';
                        if (r.editable) {
                            let isEditableVal = '1';
                            if (r.field === 'jtdrik') {
                                isEditableVal = d.proses.toLowerCase() === 'lem' ? '0' : '1';
                            } else if (r.field === 'jtpcs') {
                                isEditableVal = ['lem', 'sortpacking'].includes(d.proses.toLowerCase()) ? '1' :
                                    '0';
                            }

                            const isNumeric = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift'].includes(r.field);
                            const isTime = ['set', 'run', 'finish'].includes(r.field);
                            const formattedVal = isNumeric ?
                                parseFloat(r.val || 0).toLocaleString('id-ID', {
                                    maximumFractionDigits: 0
                                }) :
                                (isTime ? formatDateTime(r.val) : r.val);

                            valHtml =
                                `<span class="inline-edit-cell" data-id="${d.id}" data-field="${r.field}" data-value="${r.val}" data-editable="${isEditableVal}">${formattedVal}</span>`;
                        } else {
                            const formattedVal = (r.field && ['outputpcs', 'outputdrik',
                                    'total_pengerjaan_drik', 'total_pengerjaan_pcs'
                                ].includes(r.field)) ?
                                parseFloat(r.val || 0).toLocaleString('id-ID', {
                                    maximumFractionDigits: 0
                                }) :
                                r.val;

                            const dataFieldAttr = r.field ? `data-field="${r.field}"` : '';
                            const classAttr = r.field ? 'class="modal-derived-val' + (r.highlight ?
                                ' fw-bold text-primary' : ' text-body-emphasis') + '"' : (r.highlight ?
                                'class="fw-bold text-primary"' : 'class="text-body-emphasis"');

                            valHtml = r.badge ?
                                `<span class="badge bg-label-primary fw-normal">${formattedVal}</span>` :
                                `<span ${classAttr} ${dataFieldAttr}>${formattedVal}</span>`;
                        }

                        html += `
              <div class="list-group-item px-0 py-2 d-flex align-items-start gap-3 border-0 border-bottom">
                <div class="d-flex align-items-center gap-2 text-muted" style="width: ${labelWidth}; flex-shrink: 0;">
                  <span style="width:20px; display:inline-block;"><i class="bx ${r.icon}"></i></span>
                  <span class="small fw-semibold">${r.label}</span>
                </div>
                <div class="flex-grow-1 text-wrap text-break">
                  ${valHtml}
                </div>
              </div>`;
                    });
                    html += `
            </div>
          </div>
        </div>
      </div>`;
                });

                html += '</div>';

                modalBody.innerHTML = html;
                modalBs.show();
            }
        </script>

        {{-- script modal log --}}
        <script>
            // ── showActivityLog : modal tersendiri ──────────────────────
            const AL_FIELD_MAP = {
                job: 'No. Job',
                proses: 'Proses',
                product: 'Produk',
                designno: 'Docket',
                operator: 'Operator',
                tanggal: 'Tanggal',
                shift: 'Shift',
                upspk: 'UP SPK',
                input: 'Input',
                jtdrik: 'JT Drik',
                jtpcs: 'JT PCS',
                outputpcs: 'Output PCS',
                outputdrik: 'Output Drik',
                po: 'PO',
                set: 'Set',
                run: 'Run',
                finish: 'Finish',
                totaljam: 'Total Jam',
                total_pengerjaan_drik: 'Peng. Drik',
                total_pengerjaan_pcs: 'Peng. PCS'
            };

            function showActivityLog(id, job, product) {
                const alModal = new bootstrap.Modal(document.getElementById('modalActivityLog'));
                const alBody = document.getElementById('alModalBody');
                const alBadge = document.getElementById('alBadgeCount');
                const alSubtitle = document.getElementById('alSubtitle');

                // Reset state
                alSubtitle.textContent = 'No. Job: ' + job + ' | Produk: ' + (product || '-');
                alBadge.textContent = '–';
                alBody.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <small>Memuat riwayat log…</small>
                    </div>`;
                alModal.show();


                fetch(`/activity-logs/proses/${id}`)
                    .then(r => r.json())
                    .then(res => {
                        const logs = res.data ?? [];
                        alBadge.textContent = logs.length + ' log';

                        if (!logs.length) {
                            alBody.innerHTML = `
                                <div class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open text-center mb-2" style="font-size:2.5rem;opacity:.2"></i>
                                    <p class="fw-semibold mb-1" style="color:#697a8d">Belum Ada Riwayat Perubahan</p>
                                    <small>Tidak ada log aktivitas untuk baris ini.</small>
                                </div>`;
                            return;
                        }

                        const isNull = v => v === null || v === '' || v === undefined;

                        // modal body
                        const rows = logs.map((l, i) => {
                            const niceField = AL_FIELD_MAP[l.field] ?? l.field;
                            const isNumericField = ['input', 'jtdrik', 'jtpcs', 'outputpcs', 'outputdrik', 'total_pengerjaan_drik', 'total_pengerjaan_pcs', 'totaljam', 'upspk'].includes(l.field);
                            const isDateField = ['tanggal', 'set', 'run', 'finish'].includes(l.field);
                            const formatDateVal = (v) => {
                                if (!v) return '';
                                try {
                                    const d = new Date(v);
                                    if (isNaN(d)) return v;
                                    const dd = String(d.getDate()).padStart(2, '0');
                                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                                    const yy = String(d.getFullYear()).slice(-2);
                                    const hh = String(d.getHours()).padStart(2, '0');
                                    const min = String(d.getMinutes()).padStart(2, '0');
                                    return `${dd}/${mm}/${yy} ${hh}:${min}`;
                                } catch(e) { return v; }
                            };
                            const oldHtml = isNull(l.old) ?
                                (isNumericField ? `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#dc3545">0</span>` : `<span style="color:#c4c8d0;font-style:italic;font-size:.74rem">null</span>`) :
                                `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#dc3545;white-space:nowrap;">${isDateField ? formatDateVal(l.old) : l.old}</span>`;
                            const newHtml = isNull(l.new) ?
                                (isNumericField ? `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#146c43">0</span>` : `<span style="color:#c4c8d0;font-style:italic;font-size:.74rem">null</span>`) :
                                `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#146c43;white-space:nowrap;">${isDateField ? formatDateVal(l.new) : l.new}</span>`;

                            return `
                            <tr>
                                <td style="color:#c4c6cc;font-size:.67rem;font-family:monospace;white-space:nowrap;padding:.7rem .9rem">${i + 1}</td>
                                <td style="white-space:nowrap;padding:.7rem .9rem">
                                    <div style="font-weight:600;font-size:.8rem">${l.waktu.split(' ')[0] ?? ''}</div>
                                    <div style="font-size:.68rem;color:#8592a3;font-family:monospace">${l.waktu.split(' ')[1] ?? ''}</div>
                                </td>
                                <td style="white-space:nowrap;padding:.7rem .9rem">
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="font-size:.8rem;font-weight:600">${l.user}</span>
                                    </div>
                                </td>
                                <td style="padding:.7rem .9rem">
                                    <span style="display:inline-flex;flex-direction:column;padding:.2rem .58rem;border-radius:7px;background:rgba(105,108,255,.09);border:1px solid rgba(105,108,255,.13)">
                                        <span style="font-size:.74rem;font-weight:600;color:#696cff;line-height:1.2">${niceField}</span>
                                        
                                    </span>
                                </td>
                                <td class="text-center" style="padding:.7rem .9rem">${oldHtml}</td>
                                <td class="text-center" style="padding:.7rem .9rem">${newHtml}</td>
                            </tr>`;
                        }).join('');

                        alBody.innerHTML = `
                            <div style="overflow-x:auto;scrollbar-width:thin;scrollbar-color:rgba(105,108,255,.18) transparent">
                                <table class="table table-sm table-hover mb-0 align-middle" style="min-width:640px">
                                    <thead style="background:#f5f5f9">
                                        <tr>
                                            <th style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1);width:38px">#</th>
                                            <th style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1)">Waktu</th>
                                            <th style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1)">User</th>
                                            <th style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1)">Kolom</th>
                                            <th class="text-center" style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1)">
                                                <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:7px;height:7px;border-radius:50%;background:#dc3545;display:inline-block"></span>Sebelum</span>
                                            </th>
                                            <th class="text-center" style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1)">
                                                <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:7px;height:7px;border-radius:50%;background:#198754;display:inline-block"></span>Sesudah</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size:.81rem">${rows}</tbody>
                                </table>
                            </div>`;
                    })
                    .catch(() => {
                        alBody.innerHTML =
                            `<div class="text-center py-4 text-danger small"><i class="bx bx-error-circle me-1"></i>Gagal memuat riwayat log.</div>`;
                    });
            }
        </script>

    @endsection
