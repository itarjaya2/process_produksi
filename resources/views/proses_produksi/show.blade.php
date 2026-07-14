@extends('layouts.main')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

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

        {{-- ── Page Header ──────────────────────────────────────── --}}
        <div class="d-flex flex-column mb-4 gap-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        class="d-flex align-items-center gap-2 bg-primary bg-opacity-10 border border-primary rounded-3 px-4 py-2">
                        <i class="bx bx-barcode text-primary fs-5"></i>
                        <span class="text-muted small fw-semibold">DOCKET</span>
                        <span class="fw-bold text-primary fs-5">{{ $docket }}</span>
                    </div>
                </div>

                <div>
                    <h4 class="fw-bold mb-0">Rangkuman Design No</h4>
                    <p class="text-muted mb-0 small">Rekap seluruh proses produksi untuk Design No {{ $docket }}
                        (Job: {{ implode(', ', $jobsToQuery) }})</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <form action="{{ route('proses-produksi.show', $job_id) }}" method="GET" class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search fs-6"></i></span>
                            <input type="text" name="search_jobs" class="form-control form-control-sm"
                                placeholder="Multi-job (misal: 260732, 260733)"
                                value="{{ request()->query('search_jobs') }}" style="width: 250px;">
                            @if(request()->query('search_jobs'))
                                <a href="{{ route('proses-produksi.show', $job_id) }}" class="btn btn-sm btn-outline-secondary px-2 d-flex align-items-center">
                                    <i class="bx bx-x fs-5"></i>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                    </form>

                    <a href="{{ route('proses-produksi.index') }}"
                        class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════
       SECTION 1 — RANGKUMAN TABLE
       ══════════════════════════════════════════════════════ --}}
        {{-- Job Active Indicator --}}
        <div class="mt-4 mb-2 d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted small fw-semibold">Job Active:</span>
            @foreach($jobsToQuery as $activeJob)
                <span class="badge bg-label-success px-3 py-2 fs-7 fw-bold border border-success border-opacity-25 rounded-pill">
                    <i class="bx bx-briefcase me-1"></i> {{ $activeJob }}
                </span>
            @endforeach
        </div>

        {{-- Card Wadah Tabel Rangkuman --}}
        <div class="card mt-2 shadow-sm">
            {{-- <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bx bx-table me-2"></i>
                    Rangkuman Produksi
                </h5>
            </div> --}}

            <div class="table-responsive text-nowrap">
                <table id="tblRangkuman" class="table table-sm table-hover align-middle mb-0" style="font-size: 0.78rem;">

                    <thead class="table-light text-uppercase text-center small">
                        <tr>
                            <th style="min-width:180px">PROCESS</th>
                            <th>JAM</th>
                            <th>JT DRIK</th>
                            <th>JT PCS</th>
                            <th>OUTPUT/DRIK</th>
                            <th>OUTPUT/PCS</th>
                            <th>TOTAL DRIK</th>
                            <th>SELISIH DRIK</th>
                            <th>TOTAL PCS</th>
                            <th>SELISIH PCS</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($rangkuman as $row)
                            <tr>

                                <td class="fw-semibold">
                                    {{ strtoupper($row['proses']) }}
                                </td>

                                <td class="text-center">
                                    {{ $row['jam'] ?: '0' }}
                                </td>

                                <td class="text-center">
                                    {{ $row['jt_drik'] ? number_format($row['jt_drik'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center">
                                    {{ $row['jt_pcs'] ? number_format($row['jt_pcs'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center">
                                    {{ $row['output_drik'] ? number_format($row['output_drik'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center">
                                    {{ $row['output_pcs'] ? number_format($row['output_pcs'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center fw-semibold">
                                    {{ $row['total_pengerjaan_drik'] ? number_format($row['total_pengerjaan_drik'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center fw-bold text-warning">
                                    {{ $row['selisih_drik'] ? number_format($row['selisih_drik'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center fw-semibold">
                                    {{ $row['total_pengerjaan_pcs'] ? number_format($row['total_pengerjaan_pcs'], 0, ',', '.') : '0' }}
                                </td>

                                <td class="text-center fw-bold text-info">
                                    {{ $row['selisih_pcs'] ? number_format($row['selisih_pcs'], 0, ',', '.') : '0' }}
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot class="table-light fw-bold text-center">
                        <tr>
                            <td class="text-center">GRAND TOTAL</td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('jam'), 2, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('jt_drik'), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('jt_pcs'), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('output_drik'), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('output_pcs'), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('total_pengerjaan_drik'), 0, ',', '.') }}
                            </td>
                            <td class="text-warning">
                                {{ number_format(collect($rangkuman)->sum('selisih_drik'), 0, ',', '.') }}
                            </td>
                            <td>
                                {{ number_format(collect($rangkuman)->sum('total_pengerjaan_pcs'), 0, ',', '.') }}
                            </td>
                            <td class="text-info">
                                {{ number_format(collect($rangkuman)->sum('selisih_pcs'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>

                </table>
            </div>
        </div>




        {{-- ══════════════════════════════════════════════════════
       SECTION 2 — DETAIL PROSES
       ══════════════════════════════════════════════════════ --}}
        {{-- Main Table Card --}}
        <div class="card-header bg-white border-bottom px-5 py-3 mt-8">
            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1 fw-bold">
                        <i class="bx bx-list-ul text-primary me-2"></i>
                        Detail Proses Produksi
                    </h5>

                    <small class="text-muted">
                        Riwayat seluruh aktivitas proses produksi untuk Job
                        <span class="fw-semibold text-dark">{{ implode(', ', $jobsToQuery) }}</span>
                    </small>
                </div>

                <span class="badge bg-label-primary fs-6">
                    {{ $detailProses->count() }} Aktivitas
                </span>

            </div>
        </div>
        @php
            $groupedDetail = $detailProses->groupBy('job')->sortBy(function ($items, $key) use ($job_id) {
                return $key == $job_id ? 0 : 1;
            });
        @endphp

        @forelse ($groupedDetail as $jobGroupKey => $items)
            @php
                $is_active = true;
                $clean_job_key = preg_replace('/[^A-Za-z0-9\-]/', '_', $jobGroupKey ?: 'empty');
            @endphp

            <div class="card border mb-4 shadow-sm mt-3">
                {{-- Header Row (acting as Collapse Toggle button) --}}
                <div class="card-header bg-light py-3 px-4 job-group-header cursor-pointer"
                    data-target="#rows-job-{{ $clean_job_key }}" style="cursor: pointer;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fw-bold">
                            <i class="bx bx-briefcase text-secondary me-1"></i> Job:
                            {{ $jobGroupKey ?: 'Tanpa Job' }}
                        </div>
                        <div>
                            <i
                                class="bx bx-chevron-down toggle-icon text-muted fs-5"></i>
                        </div>
                    </div>
                </div>

                {{-- Table container wrapper --}}
                <div id="rows-job-{{ $clean_job_key }}" class="table-responsive {{ $is_active ? '' : 'd-none' }}">
                    <table class="table table-sm table-hover mb-0 align-middle">
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
                                <th class="text-center pe-4" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $data)
                                <tr>
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
                                            $paletWarna = ['primary', 'success', 'warning', 'info', 'danger', 'dark'];
                                            $teks = $data->proses ?? 'default';
                                            $indeksWarna = abs(crc32($teks)) % count($paletWarna);
                                            $badgeColor = $paletWarna[$indeksWarna];
                                        @endphp
                                        <span class="badge bg-label-{{ $badgeColor }} fw-semibold">
                                            {{ $data->proses ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="small text-nowrap">{{ $data->product ?? '-' }}</td>
                                    <td class="small text-nowrap">{{ $data->operator ?? '-' }}</td>
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
                                        {{ $data->input ? number_format($data->input, 0, ',', '.') : '0' }}
                                    </td>
                                    <td class="small">
                                        {{ $data->jtdrik ? number_format($data->jtdrik, 0, ',', '.') : '0' }}</td>
                                    <td class="small">
                                        {{ $data->jtpcs ? number_format($data->jtpcs, 0, ',', '.') : '0' }}
                                    </td>
                                    <td class="text-center fw-semibold">
                                        {{ $data->outputpcs ? number_format($data->outputpcs, 0, ',', '.') : '0' }}
                                    </td>
                                    <td class="text-center fw-semibold">
                                        {{ $data->outputdrik ? number_format($data->outputdrik, 0, ',', '.') : '0' }}
                                    </td>
                                    {{-- Toggle button — opens offcanvas --}}
                                    <td class="text-center pe-4 d-flex gap-1">
                                        <button type="button"
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
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold text-end">
                            <tr>
                                <td colspan="7" class="text-center">
                                    GRAND TOTAL
                                </td>
                                <td>
                                    {{ number_format($items->sum('input'), 0, ',', '.') }}
                                </td>
                                <td class="text-start">
                                    {{ number_format($items->sum('jtdrik'), 0, ',', '.') }}
                                </td>
                                <td class="text-start">
                                    {{ number_format($items->sum('jtpcs'), 0, ',', '.') }}
                                </td>
                                <td>
                                    {{ number_format($items->sum('outputpcs'), 0, ',', '.') }}
                                </td>
                                <td>
                                    {{ number_format($items->sum('outputdrik'), 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="card border shadow-sm mt-3">
                <div class="card-body py-5 text-center text-muted">
                    <i class="bx bx-data fs-1 d-block mb-2 opacity-25"></i>
                    Belum ada data proses produksi yang tersimpan.
                </div>
            </div>
        @endforelse


    </div>


    {{-- ── Offcanvas Detail ──────────────────────────────────── --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetail" style="width:420px"
        aria-labelledby="offcanvasDetailLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2" id="offcanvasDetailLabel">
                <i class="bx bx-layer text-primary fs-4"></i>
                Detail Proses Produksi
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="offcanvasBody"></div>
    </div>

    <style>
        .table:not(#tblRangkuman)> :not(caption)>*>* {
            padding: 1rem 0.30rem;
        }

        #tblRangkuman> :not(caption)>*>* {
            padding: 0.25rem 0.35rem !important;
        }

        /* #tblProduksi thead th {
                                position: sticky;
                                top: 70px;
                                z-index: 1020;
                                background: #fff;
                            }

                            .table-light th{
                                background: #f8f9fa !important;
                            } */
    </style>

    <script>
        let offcanvasBs;
        const offcanvasBody = document.getElementById('offcanvasBody');

        document.addEventListener('DOMContentLoaded', function() {
            offcanvasBs = new bootstrap.Offcanvas(document.getElementById('offcanvasDetail'));
        });

        function showDetail(d) {
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

            let html = '';
            sections.forEach(sec => {
                html += `<p class="text-uppercase fw-bold small text-muted mb-2 mt-4">${sec.heading}</p>
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

        // Toggle job row collapse
        $(function() {
            $('.job-group-header').on('click', function() {
                const target = $(this).data('target');
                const $targetTbody = $(target);
                const $icon = $(this).find('.toggle-icon');

                if ($targetTbody.hasClass('d-none')) {
                    $targetTbody.removeClass('d-none');
                    $icon.removeClass('bx-chevron-right').addClass('bx-chevron-down');
                } else {
                    $targetTbody.addClass('d-none');
                    $icon.removeClass('bx-chevron-down').addClass('bx-chevron-right');
                }
            });
        });
    </script>

@endsection
