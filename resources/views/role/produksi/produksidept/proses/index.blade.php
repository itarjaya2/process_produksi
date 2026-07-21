@extends('layouts.main')

@php
    if (!function_exists('_prosesLabel')) {
        function _prosesLabel($namaProses)
        {
            $p = strtolower(trim((string) $namaProses));
            $map = [
                'lem' => 'GLUED',
                'lem setengah jadi' => 'HALF GLUE',
                'sortir lem' => 'SORTIR GLUE',
            ];
            return $map[$p] ?? $namaProses;
        }
    }
@endphp

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y produksi-modern">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 ppx-header">
            <div>
                <h4 class="fw-bold mb-1">Proses Produksi</h4>
                <p class="text-muted mb-0 small">Kelola dan pantau seluruh data proses produksi</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('spreadsheet.index') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <i class="bx bx-plus fs-5"></i>
                    Add Data
                </a>
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                    <i class="bx bx-history fs-5"></i>
                    Activity Log
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4 ppx-alert"
                role="alert">
                <i class="bx bx-check-circle fs-5"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-4 ppx-alert" role="alert">
                <i class="bx bx-error-circle fs-5"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Card --}}
        <div class="card mb-4 border-0 ppx-card ppx-filter-card">

            <div class="card-header bg-transparent d-flex align-items-center justify-content-between ppx-filter-header"
                data-bs-toggle="collapse" data-bs-target="#ppxFilterBody" role="button" aria-expanded="true"
                aria-controls="ppxFilterBody">
                <div class="d-flex align-items-center gap-2">
                    <span class="ppx-icon-badge"><i class="bx bx-slider-alt"></i></span>
                    <span class="fw-semibold">Filter Data</span>
                    @if (request()->filled('job') ||
                            request()->filled('designno') ||
                            request()->filled('product') ||
                            request()->filled('operator') ||
                            request()->filled('proses') ||
                            request()->filled('mesin') ||
                            request()->filled('start_date') ||
                            request()->filled('end_date'))
                        <span class="badge ppx-badge-count">Aktif</span>
                    @endif
                </div>
                <i class="bx bx-chevron-down ppx-collapse-caret"></i>
            </div>

            <div class="collapse show" id="ppxFilterBody">
                <div class="card-body pt-4 pb-4">
                    <form action="{{ route('proses-produksi.indexdata') }}" method="GET">
                        <div class="row g-3">
                            {{-- no job --}}
                            <div class="col">
                                <label class="ppx-field-label"><i class="bx bx-briefcase"></i>Job</label>
                                <div class="position-relative">
                                    <div id="jobSearchWrapper" class="ppx-chip-input">
                                        <div id="scrollableContainer" class="ppx-chip-scroll">
                                            <div id="selectedJobsContainer" class="ppx-chip-list"></div>
                                            <input type="text" id="searchJob" class="ppx-chip-native-input"
                                                placeholder="No Job" autocomplete="off">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchJobsHidden" name="job" value="{{ request('job') }}">
                                    <div id="jobSuggestions"
                                        class="list-group ppx-suggestions position-absolute w-100 d-none"></div>
                                </div>
                            </div>

                            {{-- docket --}}
                            <div class="col">
                                <label class="ppx-field-label"><i class="bx bx-barcode"></i>Docket</label>
                                <div class="position-relative">
                                    <div id="docketSearchWrapper" class="ppx-chip-input">
                                        <div id="docketScrollableContainer" class="ppx-chip-scroll">
                                            <div id="selectedDocketsContainer" class="ppx-chip-list"></div>
                                            <input type="text" id="searchDocket" class="ppx-chip-native-input"
                                                placeholder="Docket" autocomplete="off">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchDocketsHidden" name="designno"
                                        value="{{ request('designno') }}">
                                    <div id="docketSuggestions"
                                        class="list-group ppx-suggestions position-absolute w-100 d-none"></div>
                                </div>
                            </div>

                            {{-- product --}}
                            <div class="col">
                                <label class="ppx-field-label"><i class="bx bx-box"></i>Produk</label>
                                <div class="position-relative">
                                    <div id="productSearchWrapper" class="ppx-chip-input">
                                        <div id="productScrollableContainer" class="ppx-chip-scroll">
                                            <div id="selectedProductsContainer" class="ppx-chip-list"></div>
                                            <input type="text" id="searchProduct" class="ppx-chip-native-input"
                                                placeholder="Produk" autocomplete="off">
                                        </div>
                                    </div>
                                    <input type="hidden" id="searchProductsHidden" name="product"
                                        value="{{ request('product') }}">
                                    <div id="productSuggestions"
                                        class="list-group ppx-suggestions position-absolute w-100 d-none"></div>
                                </div>
                            </div>


                            {{-- proses --}}
                            <div class="col">
                                <label class="ppx-field-label"><i class="bx bx-cog"></i>Proses</label>
                                <select name="proses" class="form-select ppx-input">
                                    <option value="">Semua Proses</option>
                                    @foreach ($daftarProses as $prosesName)
                                        <option value="{{ $prosesName }}"
                                            {{ request('proses') == $prosesName ? 'selected' : '' }}>
                                            {{ _prosesLabel($prosesName) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- mesin --}}
                            <div class="col">
                                <label class="ppx-field-label"><i class="bx bx-chip"></i>Mesin</label>
                                <select name="mesin" class="form-select ppx-input">
                                    <option value="">Semua Mesin</option>
                                    @foreach ($daftarMesin as $mesinName)
                                        <option value="{{ $mesinName }}"
                                            {{ request('mesin') == $mesinName ? 'selected' : '' }}>
                                            {{ $mesinName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- tanggal --}}
                            <div class="col">
                                <label class="ppx-field-label"><i class="bx bx-calendar"></i>Rentang Tanggal</label>
                                <div class="ppx-date-range">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="form-control ppx-input">
                                    <span class="ppx-date-sep">s/d</span>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="form-control ppx-input">
                                </div>
                            </div>

                            {{-- Tombol aksi --}}
                            <div class="col d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary ppx-btn-apply">
                                    <i class="bx bx-filter-alt me-1"></i>Filter
                                </button>
                                <a href="{{ route('proses-produksi.indexdata') }}" class="btn ppx-btn-reset">
                                    <i class="bx bx-refresh me-1"></i>Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
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
                $activeFilters['Proses'] = _prosesLabel(request('proses'));
            }
            if (request()->filled('mesin')) {
                $activeFilters['Mesin'] = request('mesin');
            }
            if (request()->filled('start_date') || request()->filled('end_date')) {
                $dateStr = '';
                $dateStr .= request()->filled('start_date')
                    ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y')
                    : 'Awal';
                $dateStr .= ' s/d ';
                $dateStr .= request()->filled('end_date')
                    ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y')
                    : 'Akhir';
                $activeFilters['Tanggal'] = $dateStr;
            }
        @endphp
        @if (count($activeFilters) > 0)
            <div class="ppx-active-filters mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold"><i class="bx bx-info-circle me-1"></i>Filter Aktif:</span>
                    @foreach ($activeFilters as $key => $val)
                        <span class="ppx-chip-filter">
                            <strong>{{ $key }}</strong><span
                                class="ppx-chip-filter-val">{{ $val }}</span>
                        </span>
                    @endforeach
                </div>
                <a href="{{ route('spreadsheet.index') }}"
                    class="btn btn-sm ppx-btn-clear d-flex align-items-center gap-1">
                    <i class="bx bx-trash-alt"></i> Bersihkan Semua
                </a>
            </div>
        @endif

        {{-- Main Table Card --}}
        <div class="card mb-4 border-0 ppx-card" id="mainTableCard">
            <div class="card-body p-0">

                <div class="position-relative ppx-table-wrapper">
                    <button id="btnScrollLeft" class="ppx-scroll-btn left" onclick="scrollTabel(-350)"
                        aria-label="Geser ke kiri">
                        <i class="bx bx-chevron-left"></i>
                    </button>
                    <button id="btnScrollRight" class="ppx-scroll-btn right" onclick="scrollTabel(350)"
                        aria-label="Geser ke kanan">
                        <i class="bx bx-chevron-right"></i>
                    </button>
                    <div class="ppx-fade left" id="ppxFadeLeft"></div>
                    <div class="ppx-fade right" id="ppxFadeRight"></div>

                    <form action="{{ route('spreadsheet.index') }}" method="GET" class="w-100">
                        <div class="table-responsive ppx-table-scroll" id="tabelContainer">
                            <table class="table mb-0 align-middle ppx-table" id="tblProduksi">
                                <thead class="ppx-thead">
                                    <tr>
                                        @php
                                            $curSort = request()->query('sort');
                                            $curDir = request()->query('dir', 'desc');

                                            $processOrder = [
                                                'PRINT',
                                                'SORTIR CETAK',
                                                'WATERBASE',
                                                'HOCK',
                                                'HOTPRINT',
                                                'LAMINASI',
                                                'LAMINATING',
                                                'EMBOSS',
                                                'DIECUT',
                                                'CUTTING',
                                                'PRETEL',
                                                'LEM SETENGAH JADI',
                                                'LEM',
                                                'SORTIR LEM',
                                                'SORTIR',
                                                'PACKING',
                                                'SORTPACKING',
                                            ];

                                            $softPalette = [
                                                ['bg' => '#EAEAFE', 'text' => '#5850EC'], // 0  PRINT            - indigo
                                                ['bg' => '#E1F2FB', 'text' => '#0B76B7'], // 1  SORTIR CETAK     - sky
                                                ['bg' => '#E1F6ED', 'text' => '#0C8457'], // 2  WATERBASE        - emerald
                                                ['bg' => '#FCF1DC', 'text' => '#B4740E'], // 3  HOCK             - amber
                                                ['bg' => '#FBE7E7', 'text' => '#C1454B'], // 4  HOTPRINT         - soft red
                                                ['bg' => '#F1E7FC', 'text' => '#7C4DCC'], // 5  LAMINASI         - violet
                                                ['bg' => '#E1F3F5', 'text' => '#1080A0'], // 6  LAMINATING       - cyan
                                                ['bg' => '#FBE6F0', 'text' => '#BD3E7B'], // 7  EMBOSS           - pink
                                                ['bg' => '#EEF3DE', 'text' => '#647F1E'], // 8  DIECUT           - olive
                                                ['bg' => '#E9EBEF', 'text' => '#5A6577'], // 9  CUTTING          - slate
                                                ['bg' => '#FDEAE0', 'text' => '#C2600C'], // 10 PRETEL           - orange
                                                ['bg' => '#F5E6F5', 'text' => '#9C3F9C'], // 11 LEM SETENGAH JADI- plum
                                                ['bg' => '#E0F5F1', 'text' => '#0F8A72'], // 12 LEM              - teal
                                                ['bg' => '#F8EBD9', 'text' => '#A05A10'], // 13 SORTIR LEM       - bronze
                                                ['bg' => '#F3E9DE', 'text' => '#8B5E34'], // 14 SORTIR           - brown
                                                ['bg' => '#E3EAFB', 'text' => '#2D5FCC'], // 15 PACKING          - blue
                                                ['bg' => '#F1F5DA', 'text' => '#6B8E1E'], // 16 SORTPACKING      - chartreuse
                                            ];

                                            if (!function_exists('_prosesColor')) {
                                                function _prosesColor($namaProses, $softPalette, $processOrder)
                                                {
                                                    $teks = strtoupper(trim((string) $namaProses)) ?: 'DEFAULT';
                                                    $idx = array_search($teks, $processOrder, true);

                                                    if ($idx === false) {
                                                        $fallbackSlots = max(
                                                            1,
                                                            count($softPalette) - count($processOrder),
                                                        );
                                                        $idx =
                                                            count($processOrder) + (abs(crc32($teks)) % $fallbackSlots);
                                                    }

                                                    return $softPalette[$idx % count($softPalette)];
                                                }
                                            }

                                            if (!function_exists('_formatJamOnly')) {
                                                function _formatJamOnly($val)
                                                {
                                                    if (empty($val) || $val === '-') {
                                                        return '-';
                                                    }
                                                    try {
                                                        $s = trim(str_replace('T', ' ', (string) $val));
                                                        return \Carbon\Carbon::parse($s)->format('H:i');
                                                    } catch (\Exception $e) {
                                                        return $val;
                                                    }
                                                }
                                            }

                                            function _sortHeader($key, $label, $curSort, $curDir)
                                            {
                                                $isActive = $curSort == $key;
                                                $dirToggle = $isActive ? ($curDir == 'asc' ? 'desc' : 'asc') : 'asc';
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
                                        <th class="ppx-sticky-col">{!! _sortHeader('job', 'Job', $curSort, $curDir) !!}</th>
                                        <th>{!! _sortHeader('docket', 'Docket', $curSort, $curDir) !!}</th>
                                        <th>{!! _sortHeader('proses', 'Proses', $curSort, $curDir) !!}</th>
                                        <th>{!! _sortHeader('product', 'Produk', $curSort, $curDir) !!}</th>
                                        <th class="text-center">Qty</th>
                                        <th>{!! _sortHeader('mesin', 'Mesin', $curSort, $curDir) !!}</th>
                                        <th>{!! _sortHeader('operator', 'Operator', $curSort, $curDir) !!}</th>
                                        <th style="width:10px">{!! _sortHeader('tanggal', 'Tanggal', $curSort, $curDir) !!}</th>
                                        <th class="text-center">Set</th>
                                        <th class="text-center">Run</th>
                                        <th class="text-center">Finish</th>
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
                                        <tr>
                                            <td class="ppx-sticky-col">
                                                @if ($data->job)
                                                    <a href="{{ route('proses-produksi.rangkuman', $data->job) }}"
                                                        class="fw-semibold text-decoration-none ppx-link">
                                                        {{ $data->job }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="small text-nowrap">{{ $data->designno ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $c = _prosesColor(
                                                        $data->proses ?? 'DEFAULT',
                                                        $softPalette,
                                                        $processOrder,
                                                    );
                                                @endphp
                                                <span class="badge fw-semibold ppx-badge-proses"
                                                    style="background-color: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                                                    {{ _prosesLabel($data->proses ?? '-') }}
                                                </span>
                                            </td>
                                            <td class="small text-nowrap">
                                                @if (strlen($data->product ?? '') > 15)
                                                    <span class="product-toggle ppx-toggle-text"
                                                        data-full="{{ $data->product }}"
                                                        data-short="{{ \Illuminate\Support\Str::limit($data->product, 15) }}">
                                                        {{ \Illuminate\Support\Str::limit($data->product, 15) }}
                                                    </span>
                                                @else
                                                    {{ $data->product ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="qty" data-value="{{ $data->qty ?? 0 }}">
                                                    {{ $data->qty ? number_format($data->qty, 0, ',', '.') : '0' }}
                                                </span>
                                            </td>
                                            <td class="small text-nowrap">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="mesin" data-value="{{ $data->mesin ?? '' }}">
                                                    {{ $data->mesin ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="small text-nowrap">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="operator" data-value="{{ $data->operator ?? '' }}">
                                                    @if (strlen($data->operator ?? '') > 10)
                                                        <span class="operator-toggle ppx-toggle-text"
                                                            data-full="{{ $data->operator }}"
                                                            data-short="{{ \Illuminate\Support\Str::limit($data->operator, 10) }}">
                                                            {{ \Illuminate\Support\Str::limit($data->operator, 10) }}
                                                        </span>
                                                    @else
                                                        {{ $data->operator ?? '-' }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="small text-nowrap">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="tanggal"
                                                    data-value="{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') : '' }}">
                                                    @if ($data->tanggal)
                                                        {{ strtoupper(\Carbon\Carbon::parse($data->tanggal)->format('d M y')) }}
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-center small text-nowrap">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="set" data-value="{{ $data->set ?? '' }}">
                                                    {{ _formatJamOnly($data->set) }}
                                                </span>
                                            </td>
                                            <td class="text-center small text-nowrap">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="run" data-value="{{ $data->run ?? '' }}">
                                                    {{ _formatJamOnly($data->run) }}
                                                </span>
                                            </td>
                                            <td class="text-center small text-nowrap">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="finish" data-value="{{ $data->finish ?? '' }}">
                                                    {{ _formatJamOnly($data->finish) }}
                                                </span>
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
                                            <td class="text-center fw-semibold">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="jtdrik" data-value="{{ $data->jtdrik ?? 0 }}"
                                                    data-editable="{{ in_array(strtolower($data->proses ?? ''), ['lem', 'lem setengah jadi', 'sortir lem']) ? '0' : '1' }}">
                                                    {{ $data->jtdrik ? number_format($data->jtdrik, 0, ',', '.') : '0' }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="jtpcs" data-value="{{ $data->jtpcs ?? 0 }}"
                                                    data-editable="{{ in_array(strtolower($data->proses ?? ''), ['lem', 'lem setengah jadi', 'sortir lem', 'sortpacking']) ? '1' : '0' }}">
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
                                                                    'tanggal' => $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') : '',
                                                                    'job' => $data->job ?? '-',
                                                                    'proses' => $data->proses ?? '-',
                                                                    'mesin' => $data->mesin ?? '-',
                                                                    'product' => $data->product ?? '-',
                                                                    'designno' => $data->designno ?? '-',
                                                                    'operator' => $data->operator ?? '-',
                                                                    'set' => $data->set ?? '-',
                                                                    'run' => $data->run ?? '-',
                                                                    'finish' => $data->finish ?? '-',
                                                                    'totaljam' => $data->totaljam ?? '0',
                                                                    'shift' => $data->shift ?? '0',
                                                                    'po' => $data->po ?? '0',
                                                                    'qty' => $data->qty ?? '0',
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
                                            <td colspan="18" class="text-center py-5 text-muted">
                                                <i class="bx bx-data fs-1 d-block mb-2 opacity-25"></i>
                                                Belum ada data proses produksi yang tersimpan.
                                            </td>
                                        </tr>
                                    @endforelse
                                <tfoot class="ppx-tfoot fw-bold">
                                    <tr>
                                        <td colspan="10" class="text-center">GRAND TOTAL</td>
                                        <td class="text-center">{{ number_format($total['input'], 0, ',', '.') }}</td>
                                        <td class="text-center">{{ number_format($total['jtdrik'], 0, ',', '.') }}</td>
                                        <td class="text-center">{{ number_format($total['jtpcs'], 0, ',', '.') }}</td>
                                        <td class="text-center">{{ number_format($total['outputpcs'], 0, ',', '.') }}</td>
                                        <td class="text-center">{{ number_format($total['outputdrik'], 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <datalist id="datalistMesinInline">
                        <option value="CX"></option>
                        <option value="SM-03"></option>
                        <option value="SAKURAI"></option>
                        <option value="KOMORI"></option>
                        <option value="LAMINATING"></option>
                        <option value="LAMINASI"></option>
                        <option value="WATERBASE"></option>
                        <option value="HOCK"></option>
                        <option value="HMC-01"></option>
                        <option value="HMC-02"></option>
                        <option value="HMC-03"></option>
                        <option value="HMC-04"></option>
                        <option value="LABEL"></option>
                        <option value="MANUAL SONGSONG"></option>
                        <option value="MANUAL MONDO"></option>
                        <option value="LEM AB"></option>
                        <option value="LEM G2"></option>
                        <option value="LEM G3"></option>
                        <option value="LEM G4"></option>
                        <option value="CUTTING"></option>
                        <option value="MESIN PRETEL"></option>
                        <option value="MANUAL PRETEL"></option>
                        <option value="POLAR 115"></option>
                        <option value="POLAR 137"></option>
                        <option value="DIECUT MANUAL"></option>
                        <option value="KAWAL BORONGAN"></option>
                        <option value="SORTIR TURUN PALET"></option>
                    </datalist>

                    <div class="ppx-scroll-progress-track" id="ppxScrollTrack">
                        <div class="ppx-scroll-progress-thumb" id="ppxScrollThumb"></div>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($prosesProduksi->hasPages())
                <div class="card-footer bg-transparent border-top py-3 px-4">
                    {{ $prosesProduksi->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Toast container for notifications -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 99999 !important; margin-top: 60px;">
            <div id="liveToast" class="toast align-items-center text-white border-0 ppx-toast" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        {{-- Modal Detail --}}
        <div class="modal fade ppx-modal" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title d-flex align-items-center gap-3" id="modalDetailLabel">
                            <span class="ppx-icon-badge"><i class="bx bx-layer"></i></span>
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
            .produksi-modern {
                --ppx-primary: #696cff;
                --ppx-primary-rgb: 105, 108, 255;
                --ppx-primary-dark: #5f61e6;
                --ppx-ink: #2b2c40;
                --ppx-muted: #8a8d99;
                --ppx-border: #e7e7f0;
                --ppx-surface: #ffffff;
                --ppx-surface-soft: #f8f8fc;
                --ppx-radius-lg: 16px;
                --ppx-radius-md: 12px;
                --ppx-radius-sm: 8px;
                --ppx-shadow-sm: 0 1px 2px rgba(43, 44, 64, 0.04), 0 1px 1px rgba(43, 44, 64, 0.03);
                --ppx-shadow-md: 0 10px 30px -12px rgba(43, 44, 64, 0.16);
                color: var(--ppx-ink);
            }

            .produksi-modern .ppx-card {
                border-radius: var(--ppx-radius-lg);
                box-shadow: var(--ppx-shadow-sm);
                background: var(--ppx-surface);
            }

            .produksi-modern .ppx-alert {
                border-radius: var(--ppx-radius-md);
                border: none;
                box-shadow: var(--ppx-shadow-sm);
            }

            .produksi-modern .ppx-icon-badge {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, rgba(var(--ppx-primary-rgb), .16), rgba(var(--ppx-primary-rgb), .06));
                color: var(--ppx-primary);
                font-size: 1.05rem;
                flex-shrink: 0;
            }

            .produksi-modern .ppx-filter-card {
                overflow: visible;
            }

            .produksi-modern .ppx-filter-header {
                padding: 1rem 1.25rem;
                cursor: pointer;
                border-bottom: 1px solid var(--ppx-border);
                user-select: none;
            }

            .produksi-modern .ppx-badge-count {
                background: rgba(var(--ppx-primary-rgb), .12);
                color: var(--ppx-primary-dark);
                font-weight: 600;
                font-size: .68rem;
                padding: .3em .6em;
                border-radius: 20px;
            }

            .produksi-modern .ppx-collapse-caret {
                transition: transform .25s ease;
                color: var(--ppx-muted);
                font-size: 1.2rem;
            }

            .produksi-modern .ppx-filter-header[aria-expanded="false"] .ppx-collapse-caret {
                transform: rotate(-90deg);
            }

            .produksi-modern .ppx-field-label {
                display: flex;
                align-items: center;
                gap: .35rem;
                font-size: .68rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .05em;
                color: var(--ppx-muted);
                margin-bottom: .4rem;
            }

            .produksi-modern .ppx-field-label i {
                font-size: .95rem;
                color: var(--ppx-primary);
            }

            .produksi-modern .ppx-input {
                border: 1px solid var(--ppx-border);
                border-radius: var(--ppx-radius-sm);
                font-size: .82rem;
                padding: .5rem .7rem;
                height: 38px;
                transition: border-color .15s ease, box-shadow .15s ease;
            }

            .produksi-modern .ppx-input:focus {
                border-color: var(--ppx-primary);
                box-shadow: 0 0 0 3px rgba(var(--ppx-primary-rgb), .12);
            }

            .produksi-modern .ppx-date-range {
                display: flex;
                align-items: center;
                gap: .5rem;
            }

            .produksi-modern .ppx-date-sep {
                font-size: .72rem;
                color: var(--ppx-muted);
                font-weight: 600;
            }

            .produksi-modern .ppx-btn-apply {
                border-radius: var(--ppx-radius-sm);
                font-size: .82rem;
                font-weight: 600;
                height: 38px;
                padding-inline: 1.1rem;
                box-shadow: 0 6px 16px -6px rgba(var(--ppx-primary-rgb), .55);
            }

            .produksi-modern .ppx-btn-reset {
                border-radius: var(--ppx-radius-sm);
                font-size: .82rem;
                font-weight: 600;
                height: 38px;
                padding-inline: 1rem;
                border: 1px solid var(--ppx-border);
                color: var(--ppx-muted);
                background: var(--ppx-surface-soft);
            }

            .produksi-modern .ppx-btn-reset:hover {
                color: var(--ppx-ink);
                background: #f0f0f7;
            }

            .produksi-modern .ppx-chip-input {
                display: flex;
                align-items: center;
                border: 1px solid var(--ppx-border);
                border-radius: var(--ppx-radius-sm);
                background: var(--ppx-surface);
                height: 38px;
                padding-inline: .55rem;
                transition: border-color .15s ease, box-shadow .15s ease;
            }

            .produksi-modern .ppx-chip-input:focus-within {
                border-color: var(--ppx-primary);
                box-shadow: 0 0 0 3px rgba(var(--ppx-primary-rgb), .12);
            }

            .produksi-modern .ppx-chip-scroll {
                display: flex;
                align-items: center;
                flex: 1 1 auto;
                overflow-x: auto;
                scrollbar-width: none;
                white-space: nowrap;
                height: 100%;
            }

            .produksi-modern .ppx-chip-scroll::-webkit-scrollbar,
            .produksi-modern #scrollableContainer::-webkit-scrollbar {
                display: none;
            }

            .produksi-modern .ppx-chip-list {
                display: flex;
                align-items: center;
                gap: .3rem;
                flex-shrink: 0;
            }

            .produksi-modern .ppx-chip-placeholder {
                font-size: .78rem;
                color: var(--ppx-muted);
            }

            .produksi-modern .ppx-chip-native-input {
                border: none;
                outline: none;
                background: transparent;
                font-size: .8rem;
                flex: 1 1 auto;
                min-width: 40px;
                padding-left: .3rem;
            }

            .produksi-modern .job-badge,
            .produksi-modern .docket-badge,
            .produksi-modern .product-badge,
            .produksi-modern .operator-badge {
                background: rgba(var(--ppx-primary-rgb), .1) !important;
                color: var(--ppx-primary-dark) !important;
                font-size: .7rem !important;
                border-radius: 20px !important;
            }

            .produksi-modern .ppx-suggestions {
                position: absolute;
                left: 0;
                top: calc(100% + 4px);
                width: 100%;
                z-index: 1055;
                max-height: 200px;
                overflow-y: auto;
                border-radius: var(--ppx-radius-sm);
                border: 1px solid var(--ppx-border);
                box-shadow: var(--ppx-shadow-md);
                background: var(--ppx-surface, #ffffff);
            }

            .produksi-modern .ppx-active-filters {
                background: var(--ppx-surface);
                border: 1px solid var(--ppx-border);
                border-radius: var(--ppx-radius-md);
                padding: .65rem .9rem;
            }

            .produksi-modern .ppx-chip-filter {
                display: inline-flex;
                align-items: center;
                gap: .35rem;
                font-size: .72rem;
                font-weight: 600;
                padding: .32rem .6rem;
                border-radius: 20px;
                background: rgba(var(--ppx-primary-rgb), .08);
                color: var(--ppx-primary-dark);
            }

            .produksi-modern .ppx-chip-filter-val {
                font-weight: 500;
                color: var(--ppx-ink);
            }

            .produksi-modern .ppx-btn-clear {
                border-radius: 20px;
                border: 1px solid #f3d3d6;
                color: #d9455f;
                background: #fdf3f4;
                font-weight: 600;
                font-size: .74rem;
            }

            .produksi-modern .ppx-btn-clear:hover {
                background: #fbe6e8;
            }

            .produksi-modern .ppx-table-wrapper {
                position: relative;
                overflow: hidden;
            }

            .produksi-modern .ppx-table-scroll {
                overflow-x: auto;
                scrollbar-width: none;
            }

            .produksi-modern .ppx-table-scroll::-webkit-scrollbar {
                display: none;
            }

            .produksi-modern .ppx-table {
                margin-bottom: 0;
            }

            .produksi-modern .ppx-table> :not(caption)>*>* {
                padding: .85rem .9rem;
                font-size: .82rem;
                border-bottom-color: var(--ppx-border);
            }

            .produksi-modern .ppx-thead th {
                text-transform: uppercase;
                font-size: .68rem;
                letter-spacing: .04em;
                font-weight: 700;
                color: var(--ppx-muted);
                background: var(--ppx-surface-soft);
                position: sticky;
                top: 0;
                z-index: 5;
                white-space: nowrap;
            }

            .produksi-modern .ppx-sticky-col {
                position: sticky;
                left: 0;
                z-index: 6;
                background-color: var(--ppx-surface, #ffffff);
                box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.08);
            }

            .produksi-modern .ppx-thead th.ppx-sticky-col {
                z-index: 10;
                background-color: var(--ppx-surface-soft, #f8f8fc);
            }

            .produksi-modern .ppx-table tbody tr:hover td.ppx-sticky-col {
                background-color: #f3f3f9;
            }

            .produksi-modern .ppx-table tbody tr:hover {
                background: rgba(var(--ppx-primary-rgb), .035);
            }

            .produksi-modern .ppx-tfoot td {
                background: var(--ppx-surface-soft);
                font-size: .8rem;
            }

            .produksi-modern .sort-icon {
                font-size: 16px;
                line-height: 14px;
                color: var(--ppx-muted);
            }

            .produksi-modern .sort-icon.active {
                color: var(--ppx-primary);
            }

            .produksi-modern .ajax-sort {
                cursor: pointer;
                color: inherit;
            }

            .produksi-modern .ajax-sort:hover .sort-icon {
                color: var(--ppx-primary);
            }

            .produksi-modern .ppx-link {
                color: var(--ppx-primary-dark);
            }

            .produksi-modern .ppx-toggle-text {
                cursor: pointer;
            }

            .produksi-modern .ppx-toggle-text:hover {
                color: var(--ppx-primary-dark);
            }

            .produksi-modern .ppx-badge-proses {
                border-radius: 6px;
                font-size: .7rem;
                padding: .35em .6em;
            }

            .produksi-modern .ppx-btn-detail {
                border-radius: var(--ppx-radius-sm);
                border: 1px solid rgba(var(--ppx-primary-rgb), .25);
                color: var(--ppx-primary-dark);
                background: rgba(var(--ppx-primary-rgb), .07);
                font-size: .74rem;
                font-weight: 600;
                padding: .35rem .65rem;
            }

            .produksi-modern .ppx-btn-detail:hover {
                background: var(--ppx-primary);
                color: #fff;
            }

            .produksi-modern .inline-edit-cell {
                cursor: pointer;
                display: inline-block;
                min-width: 70px;
                border-bottom: 1px dashed rgba(var(--ppx-primary-rgb), .5);
                padding: 2px 6px;
                border-radius: 5px;
                transition: all .2s ease;
            }

            .produksi-modern .inline-edit-cell:hover {
                color: var(--ppx-primary-dark);
                border-bottom-style: solid;
                background-color: rgba(var(--ppx-primary-rgb), .08);
            }

            .produksi-modern .inline-edit-cell[data-editable="0"] {
                cursor: not-allowed;
                color: #b7b9c4;
                border-bottom: none;
            }

            .produksi-modern .inline-edit-input {
                min-width: 90px;
                max-width: 110px;
            }

            .produksi-modern .ppx-scroll-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 34px;
                height: 34px;
                border-radius: 50%;
                border: 1px solid var(--ppx-border);
                background: var(--ppx-primary);
                backdrop-filter: blur(6px);
                color: rgba(255, 255, 255, .92);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                box-shadow: var(--ppx-shadow-md);
                z-index: 20;
                cursor: pointer;
                transition: opacity .2s ease, transform .2s ease, background .2s ease;
            }

            .produksi-modern .ppx-scroll-btn:hover {
                background: #fff;
                color: var(--ppx-primary);
                transform: translateY(-50%) scale(1.08);
            }

            .produksi-modern .ppx-scroll-btn.left {
                left: 10px;
            }

            .produksi-modern .ppx-scroll-btn.right {
                right: 10px;
            }

            .produksi-modern .ppx-scroll-btn.d-none {
                display: none !important;
            }

            .produksi-modern .ppx-fade {
                position: absolute;
                top: 0;
                bottom: 0;
                width: 56px;
                pointer-events: none;
                z-index: 15;
                opacity: 0;
                transition: opacity .2s ease;
            }

            .produksi-modern .ppx-fade.left {
                left: 0;
                background: linear-gradient(90deg, rgba(255, 255, 255, .95), rgba(255, 255, 255, 0));
            }

            .produksi-modern .ppx-fade.right {
                right: 0;
                background: linear-gradient(270deg, rgba(255, 255, 255, .95), rgba(255, 255, 255, 0));
            }

            .produksi-modern .ppx-fade.show {
                opacity: 1;
            }

            .produksi-modern .ppx-scroll-progress-track {
                height: 4px;
                margin: 0 1rem .9rem;
                background: var(--ppx-surface-soft);
                border-radius: 4px;
                overflow: hidden;
            }

            .produksi-modern .ppx-scroll-progress-thumb {
                height: 100%;
                width: 20%;
                border-radius: 4px;
                background: linear-gradient(90deg, var(--ppx-primary), var(--ppx-primary-dark));
                transition: transform .1s linear, width .1s linear;
            }

            .produksi-modern .ppx-toast {
                border-radius: var(--ppx-radius-md);
            }

            /* Modal detail styling */
            .ppx-modal .modal-content {
                border-radius: var(--ppx-radius-lg);
                border: none;
                box-shadow: var(--ppx-shadow-md);
            }

            .ppx-modal .ppx-detail-card {
                border-radius: var(--ppx-radius-md);
                border: 1px solid var(--ppx-border) !important;
                background: var(--ppx-surface-soft);
            }

            @media (max-width: 767.98px) {
                .produksi-modern .ppx-header {
                    align-items: flex-start;
                }
            }
        </style>

        {{-- js --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
            function formatTanggalOnly(val) {
                if (!val || val === '-') return '-';
                const d = new Date(val);
                if (isNaN(d.getTime())) return val;
                const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
                const dd = String(d.getDate()).padStart(2, '0');
                const mm = months[d.getMonth()];
                const yy = String(d.getFullYear()).slice(-2);
                return `${dd} ${mm} ${yy}`;
            }

            function formatTimeOnly(val) {
                if (!val || val === '-') return '-';
                const s = String(val).trim().replace('T', ' ');
                const d = new Date(s.includes(' ') ? s : '1970-01-01 ' + s);
                if (isNaN(d.getTime())) return val;
                const hh = String(d.getHours()).padStart(2, '0');
                const mi = String(d.getMinutes()).padStart(2, '0');
                return `${hh}:${mi}`;
            }

            function formatProsesLabel(val) {
                if (!val || val === '-') return '-';
                const p = String(val).trim().toLowerCase();
                const map = {
                    'lem': 'GLUED',
                    'lem setengah jadi': 'HALF GLUE',
                    'sortir lem': 'SORTIR GLUE'
                };
                return map[p] || val;
            }

            // Format datetime as DD-MM-YY HH:MM for display
            function formatDateTime(val) {
                if (!val || val === '-') return '-';
                const s = String(val).trim().replace('T', ' ');
                const d = new Date(s.includes(' ') ? s : '1970-01-01 ' + s);
                if (isNaN(d.getTime())) return val;
                if (!s.includes('-') && !s.includes('/')) {
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
                if (s.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/)) return s.substring(0, 16);
                if (s.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/)) return s.replace(' ', 'T').substring(0, 16);
                const today = new Date().toISOString().substring(0, 10);
                const parts = s.split(':');
                const timeStr = parts.length >= 2 ? parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0') : '00:00';
                return today + 'T' + timeStr;
            }

            function showToast(message, type = 'success') {
                const toastEl = document.getElementById('liveToast');
                const toastBody = document.getElementById('toastMessage');
                toastEl.className = 'toast align-items-center text-white border-0 ppx-toast bg-' + (type === 'error' || type ===
                    'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'success'));
                toastBody.textContent = message;
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 2000
                });
                toast.show();
            }

            let wrapper = document.getElementById('tabelContainer');
            let btnLeft = document.getElementById('btnScrollLeft');
            let btnRight = document.getElementById('btnScrollRight');
            let fadeLeft = document.getElementById('ppxFadeLeft');
            let fadeRight = document.getElementById('ppxFadeRight');
            let scrollThumb = document.getElementById('ppxScrollThumb');
            let modalBs;
            const modalBody = document.getElementById('modalBody');

            function updateScrollButton() {
                if (!wrapper) return;
                const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
                const atStart = wrapper.scrollLeft <= 0;
                const atEnd = wrapper.scrollLeft >= maxScroll - 5;
                const isScrollable = maxScroll > 5;

                btnLeft.classList.toggle('d-none', atStart || !isScrollable);
                btnRight.classList.toggle('d-none', atEnd || !isScrollable);
                fadeLeft.classList.toggle('show', !atStart && isScrollable);
                fadeRight.classList.toggle('show', !atEnd && isScrollable);

                if (scrollThumb) {
                    if (!isScrollable) {
                        scrollThumb.style.width = '100%';
                        scrollThumb.style.transform = 'translateX(0)';
                    } else {
                        const visibleRatio = wrapper.clientWidth / wrapper.scrollWidth;
                        const widthPct = Math.max(visibleRatio * 100, 8);
                        const travelPct = 100 - widthPct;
                        const scrolledRatio = wrapper.scrollLeft / maxScroll;
                        scrollThumb.style.width = widthPct + '%';
                        scrollThumb.style.transform = `translateX(${(scrolledRatio * travelPct / widthPct) * 100}%)`;
                    }
                }
            }

            function bindScrollWrapper() {
                wrapper = document.getElementById('tabelContainer');
                btnLeft = document.getElementById('btnScrollLeft');
                btnRight = document.getElementById('btnScrollRight');
                fadeLeft = document.getElementById('ppxFadeLeft');
                fadeRight = document.getElementById('ppxFadeRight');
                scrollThumb = document.getElementById('ppxScrollThumb');
                if (wrapper) {
                    wrapper.addEventListener('scroll', updateScrollButton);
                    updateScrollButton();
                }
            }

            window.addEventListener('load', bindScrollWrapper);
            window.addEventListener('resize', updateScrollButton);
            bindScrollWrapper();

            function scrollTabel(x) {
                wrapper.scrollBy({
                    left: x,
                    behavior: 'smooth'
                });
            }

            $(function() {
                // AJAX sorting handler
                $(document).on('click', '.ajax-sort', function(e) {
                    e.preventDefault();
                    const sort = $(this).data('sort');
                    const dir = $(this).data('dir');

                    const params = new URLSearchParams(window.location.search);
                    params.set('sort', sort);
                    params.set('dir', dir);

                    const url = window.location.pathname + '?' + params.toString();

                    $.get(url, function(html) {
                        const newDoc = new DOMParser().parseFromString(html, 'text/html');
                        const newCard = newDoc.getElementById('mainTableCard');
                        if (newCard) {
                            $('#mainTableCard').replaceWith($(newCard));
                            window.history.pushState({}, '', url);
                            bindScrollWrapper();
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
                        allowSpaces
                    } = config;

                    function renderBadges() {
                        const val = $(`#${hiddenId}`).val() || '';
                        const selectedItems = val.split(',').map(item => item.trim()).filter(Boolean);

                        const $container = $(`#${containerId}`);
                        const $placeholder = $(`#${placeholderId}`);

                        $container.find(`.${badgeClass}`).remove();

                        if (selectedItems.length) {
                            if ($placeholder.length) $placeholder.hide();
                            selectedItems.forEach(function(item) {
                                $('<span class="badge rounded-pill px-2 py-0.5 d-inline-flex align-items-center gap-1 ' +
                                        badgeClass +
                                        '" style="font-size: 0.7rem; line-height: 1.2; flex-shrink: 0; white-space: nowrap;">'
                                    )
                                    .append($('<span class="fw-semibold">').text(item))
                                    .append($('<i class="bx bx-x cursor-pointer" style="font-size: 11px;"></i>')
                                        .on('click', function(e) {
                                            e.stopPropagation();
                                            removeItem(item);
                                        }))
                                    .appendTo($container);
                            });
                        } else {
                            if ($placeholder.length) $placeholder.show();
                        }

                        const container = $container.closest('.ppx-chip-scroll')[0];
                        if (container) {
                            setTimeout(() => {
                                container.scrollLeft = container.scrollWidth;
                            }, 50);
                        }
                    }

                    function removeItem(item) {
                        const val = $(`#${hiddenId}`).val() || '';
                        const selectedItems = val.split(',').map(i => i.trim()).filter(i => i && i !== item);
                        $(`#${hiddenId}`).val(selectedItems.join(', '));
                        renderBadges();
                    }

                    function addItem(item) {
                        const val = $(`#${hiddenId}`).val() || '';
                        const selectedItems = val.split(',').map(i => i.trim()).filter(Boolean);
                        if (!selectedItems.includes(item)) selectedItems.push(item);
                        $(`#${hiddenId}`).val(selectedItems.join(', '));
                        renderBadges();
                    }

                    renderBadges();

                    $(`#${inputId}`).on('keyup', function(e) {
                        if (e.which === 13) return;
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
                                        <a href="#" class="list-group-item list-group-item-action border-0 px-3 py-2 d-flex align-items-center gap-2 pilih-${type}" data-value="${item[itemKey]}">
                                            <i class="bx ${icon} text-primary" style="font-size: 0.85rem;"></i>
                                            <span class="fw-semibold" style="font-size: 0.8rem;">${item[itemKey]}</span>
                                        </a>`;
                                    });
                                    $suggestions.html(html).removeClass('d-none');
                                } else {
                                    $suggestions.empty().addClass('d-none');
                                }
                            }
                        });
                    });

                    $(`#${inputId}`).on('keydown', function(e) {
                        const isDelimiter = e.which === 188 || e.which === 186 || (!allowSpaces && e.which ===
                            32);
                        if (isDelimiter) {
                            const val = $(this).val().trim().replace(/[,;]+$/, '');
                            if (val) {
                                e.preventDefault();
                                addItem(val);
                                $(this).val('');
                                $(`#${suggestionsId}`).empty().addClass('d-none');
                            }
                        }
                    });

                    $(`#${inputId}`).on('paste', function(e) {
                        const clipboardData = e.originalEvent.clipboardData || window.clipboardData;
                        const pastedData = clipboardData.getData('Text');
                        if (pastedData) {
                            const regex = allowSpaces ? /[,;|]+/ : /[\s,;|]+/;
                            const items = pastedData.split(regex).map(s => s.trim()).filter(Boolean);
                            if (items.length > 1) {
                                e.preventDefault();
                                items.forEach(item => addItem(item));
                                $(this).val('');
                                $(`#${suggestionsId}`).empty().addClass('d-none');
                            }
                        }
                    });

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

                    $(`#${inputId}`).on('focus', function() {
                        $(`#${wrapperId}`).css({
                            'border-color': 'var(--ppx-primary)',
                            'box-shadow': '0 0 0 3px rgba(var(--ppx-primary-rgb), .12)'
                        });
                    }).on('blur', function() {
                        $(`#${wrapperId}`).css({
                            'border-color': '',
                            'box-shadow': ''
                        });
                    });

                    $(`#${wrapperId}`).on('click', function(e) {
                        if (e.target.id !== inputId && !$(e.target).closest(`.${badgeClass}`).length) {
                            $(`#${inputId}`).focus();
                        }
                    });

                    $(`#${wrapperId}`).closest('form').on('submit', function() {
                        const typedVal = $(`#${inputId}`).val() ? $(`#${inputId}`).val().trim() : '';
                        if (typedVal) {
                            const regex = allowSpaces ? /[,;|]+/ : /[\s,;|]+/;
                            const items = typedVal.split(regex).map(s => s.trim()).filter(Boolean);
                            items.forEach(item => addItem(item));
                            $(`#${inputId}`).val('');
                        }
                    });
                }

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
                    allowSpaces: false
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
                    allowSpaces: true
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

                $('#ppxFilterBody').on('shown.bs.collapse hidden.bs.collapse', function() {
                    const expanded = $(this).hasClass('show');
                    $('.ppx-filter-header').attr('aria-expanded', expanded);
                });

                let activeCell = null;
                let activeValue = '';

                function cancelInlineEdit() {
                    if (activeCell) {
                        $(activeCell).html(activeValue);
                        activeCell = null;
                        activeValue = '';
                    }
                }

                function saveInlineEdit($input) {
                    const id = $input.data('id');
                    const field = $input.data('field');
                    const value = $input.val();
                    const cell = $input.closest('.inline-edit-cell');

                    const numericFields = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift', 'qty'];
                    const isNumericField = numericFields.includes(field);
                    if (isNumericField && (value === '' || isNaN(value))) {
                        showToast('Nilai harus berupa angka.', 'danger');
                        cancelInlineEdit();
                        return;
                    }

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

                            $(`.inline-edit-cell[data-id="${id}"]`).each(function() {
                                const $span = $(this);
                                const fieldName = $span.data('field');
                                if (fieldName && values[fieldName] !== undefined) {
                                    $span.data('value', values[fieldName]);
                                    const isSpanNumeric = numericFields.includes(fieldName);
                                    const isSpanTime = ['set', 'run', 'finish'].includes(fieldName);
                                    const isSpanDate = fieldName === 'tanggal';
                                    $span.text(isSpanNumeric ? parseFloat(values[fieldName])
                                        .toLocaleString('id-ID', {
                                            maximumFractionDigits: 0
                                        }) : (isSpanTime ? formatTimeOnly(values[fieldName]) :
                                            (isSpanDate ? formatTanggalOnly(values[fieldName]) :
                                                values[fieldName])));
                                }
                            });

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
                                if (newTbody) $('#tblProduksi tbody').replaceWith($(newTbody));
                                if (newTfoot) $('#tblProduksi tfoot').replaceWith($(newTfoot));
                            });

                            activeCell = null;
                            activeValue = '';

                            if (response.message) showToast(response.message, 'success');
                        },
                        error: function(xhr) {
                            cancelInlineEdit();
                            const msg = xhr.responseJSON?.message || 'Gagal memperbarui data.';
                            showToast(msg, 'danger');
                        }
                    });
                }

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

                $(document).on('dblclick', '.inline-edit-cell', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const cell = $(this);
                    const field = cell.data('field');
                    const isEditable = cell.data('editable') === 1 || cell.data('editable') === '1';

                    if (field === 'jtpcs' && !isEditable) return;

                    if (activeCell && activeCell !== this) $(activeCell).html(activeValue);

                    activeCell = this;
                    activeValue = cell.html();

                    const id = cell.data('id');
                    const value = cell.data('value') ?? '';
                    const isTimeField = ['set', 'run', 'finish'].includes(field);

                    if (isTimeField) {
                        cell.html(
                            `<input type="datetime-local" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${toDatetimeLocalVal(value)}" style="min-width:180px" />`
                        );
                    } else if (field === 'tanggal') {
                        cell.html(
                            `<input type="date" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${value}" />`
                        );
                    } else if (field === 'mesin') {
                        cell.html(
                            `<input type="text" list="datalistMesinInline" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${value}" autocomplete="off" />`
                        );
                    } else {
                        cell.html(
                            `<input type="text" class="form-control form-control-sm inline-edit-input" data-id="${id}" data-field="${field}" value="${value}" />`
                        );
                    }
                    cell.find('input').focus();
                });

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

            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('modalDetail');
                modalBs = new bootstrap.Modal(modalEl);
                bindScrollWrapper();
            });

            function showDetail(d) {
                const sections = [{
                        heading: 'Informasi Umum',
                        rows: [{
                                icon: 'bx-cog',
                                label: 'Proses',
                                val: formatProsesLabel(d.proses),
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
                            {
                                icon: 'bx-layer',
                                label: 'Qty',
                                val: d.qty,
                                field: 'qty',
                                editable: true
                            },
                        ]
                    },
                    {
                        heading: 'Jadwal & Plan',
                        rows: [{
                                icon: 'bx-calendar',
                                label: 'Tanggal',
                                val: d.tanggal,
                                field: 'tanggal',
                                editable: true
                            },
                            {
                                icon: 'bx-chip',
                                label: 'Mesin',
                                val: d.mesin,
                                field: 'mesin',
                                editable: true
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
                            },
                            {
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

                let html = '<div class="row justify-content-center g-4 p-2">';

                sections.forEach((sec, idx) => {
                    let colClass = 'col-lg-4 col-md-6';
                    let labelWidth = idx === 2 ? '175px' : '100px';

                    html += `
      <div class="${colClass}">
        <div class="card h-100 ppx-detail-card">
          <div class="card-body p-4">
            <h6 class="text-uppercase fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em; color: var(--ppx-primary-dark);">
              ${sec.heading}
            </h6>
            <div class="list-group list-group-flush">`;

                    sec.rows.forEach(r => {
                        let valHtml = '';
                        if (r.editable) {
                            let isEditableVal = '1';
                            if (r.field === 'jtdrik') {
                                isEditableVal = ['lem', 'lem setengah jadi', 'sortir lem'].includes(d.proses.toLowerCase()) ? '0' : '1';
                            } else if (r.field === 'jtpcs') {
                                isEditableVal = ['lem', 'lem setengah jadi', 'sortir lem', 'sortpacking'].includes(d.proses.toLowerCase()) ? '1' : '0';
                            }

                            const isNumeric = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift'].includes(r.field);
                            const isTime = ['set', 'run', 'finish'].includes(r.field);
                            const isDate = r.field === 'tanggal';
                            const formattedVal = isNumeric ?
                                parseFloat(r.val || 0).toLocaleString('id-ID', {
                                    maximumFractionDigits: 0
                                }) :
                                (isTime ? formatDateTime(r.val) : (isDate ? formatTanggalOnly(r.val) : r.val));

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
                            const classAttr = r.field ?
                                'class="modal-derived-val' + (r.highlight ? ' fw-bold text-primary' :
                                    ' text-body-emphasis') + '"' :
                                (r.highlight ? 'class="fw-bold text-primary"' : 'class="text-body-emphasis"');

                            valHtml = r.badge ?
                                `<span class="badge bg-label-primary fw-normal">${formattedVal}</span>` :
                                `<span ${classAttr} ${dataFieldAttr}>${formattedVal}</span>`;
                        }

                        html += `
              <div class="list-group-item px-0 py-2 d-flex align-items-start gap-3 border-0 border-bottom bg-transparent">
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
                            const isNumericField = ['input', 'jtdrik', 'jtpcs', 'outputpcs', 'outputdrik',
                                'total_pengerjaan_drik', 'total_pengerjaan_pcs', 'totaljam', 'upspk'
                            ].includes(l.field);
                            const isDateField = ['tanggal', 'set', 'run', 'finish'].includes(l.field);
                            const formatDateVal = (v, fieldName) => {
                                if (!v) return '';
                                try {
                                    const s = String(v).trim().replace('T', ' ');
                                    const d = new Date(s.includes(' ') ? s : (s.includes('-') || s.includes(
                                        '/') ? s : '1970-01-01 ' + s));
                                    if (isNaN(d.getTime())) return v;
                                    const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU',
                                        'SEP', 'OKT', 'NOV', 'DES'
                                    ];
                                    const dd = String(d.getDate()).padStart(2, '0');
                                    const mm = months[d.getMonth()];
                                    const yy = String(d.getFullYear()).slice(-2);
                                    const dateStr = `${dd} ${mm} ${yy}`;
                                    if (fieldName === 'tanggal' || !s.includes(':')) {
                                        return dateStr;
                                    }
                                    const hh = String(d.getHours()).padStart(2, '0');
                                    const min = String(d.getMinutes()).padStart(2, '0');
                                    return `${dateStr} ${hh}:${min}`;
                                } catch (e) {
                                    return v;
                                }
                            };
                            const oldHtml = isNull(l.old) ?
                                (isNumericField ?
                                    `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#dc3545">0</span>` :
                                    `<span style="color:#c4c8d0;font-style:italic;font-size:.74rem">null</span>`) :
                                `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#dc3545;white-space:nowrap;">${isDateField ? formatDateVal(l.old, l.field) : l.old}</span>`;
                            const newHtml = isNull(l.new) ?
                                (isNumericField ?
                                    `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#146c43">0</span>` :
                                    `<span style="color:#c4c8d0;font-style:italic;font-size:.74rem">null</span>`) :
                                `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#146c43;white-space:nowrap;">${isDateField ? formatDateVal(l.new, l.field) : l.new}</span>`;

                            const parts = (l.waktu || '').trim().split(' ');
                            let logDateStr = l.waktu || '';
                            let logTimeStr = '';
                            if (parts.length >= 4) {
                                logDateStr = `${parts[0]} ${parts[1]} ${parts[2]}`;
                                logTimeStr = parts[3];
                            } else if (parts.length === 2) {
                                logDateStr = parts[0];
                                logTimeStr = parts[1];
                            }

                            return `
                            <tr>
                                <td style="color:#c4c6cc;font-size:.67rem;font-family:monospace;white-space:nowrap;padding:.7rem .9rem">${i + 1}</td>
                                <td style="white-space:nowrap;padding:.7rem .9rem">
                                    <div class="ts-d" style="font-weight:600;font-size:.8rem">${logDateStr}</div>
                                    <div class="ts-t" style="font-size:.68rem;color:#8592a3;font-family:monospace">${logTimeStr}</div>
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
