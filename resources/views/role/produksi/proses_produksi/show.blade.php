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
                {{-- search --}}
                <div class="d-flex align-items-center gap-3">
                    <form action="{{ route('proses-produksi.show', $job_id) }}" method="GET"
                        class="d-flex align-items-center gap-2">

                        <div class="position-relative" style="width:320px">

                            <div id="jobSearchWrapper"
                                class="input-group input-group-merge align-items-center border rounded-3 px-2 py-1 bg-white"
                                style="height: 38px; overflow-x: auto; flex-wrap: nowrap; scrollbar-width: none; -ms-overflow-style: none;">
                                <span class="input-group-text border-0 bg-transparent px-1"
                                    style="position: sticky; left: 0; background: #fff; z-index: 2;">
                                    <i class="bx bx-search fs-6"></i>
                                </span>

                                <div id="selectedJobsContainer" class="d-flex align-items-center gap-1"
                                    style="flex-wrap: nowrap;">
                                    {{-- Badges will be rendered here --}}
                                </div>

                                <input type="text" id="searchJob" name="search_jobs"
                                    class="form-control form-control-sm border-0 shadow-none ms-1"
                                    placeholder="Cari & Pilih Job..." autocomplete="off" value=""
                                    style="flex: 1; min-width: 100px;">
                            </div>

                            <input type="hidden" id="searchJobsHidden" name="search_jobs"
                                value="{{ request()->query('search_jobs') }}">

                            <!-- Dropdown hasil pencarian -->
                            <div id="jobSuggestions"
                                class="list-group position-absolute w-100 shadow-sm border rounded-3 overflow-hidden bg-white d-none"
                                style="z-index: 1055; top: calc(100% + 6px); max-height: 280px; overflow-y: auto;">
                            </div>

                        </div>

                        @if (request()->query('search_jobs'))
                            <a href="{{ route('proses-produksi.show', $job_id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bx bx-x"></i>
                            </a>
                        @endif

                        <button type="submit" class="btn btn-sm btn-primary">
                            Cari
                        </button>

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
            @foreach ($jobsToQuery as $activeJob)
                <span
                    class="badge bg-label-success px-3 py-2 fs-7 fw-bold border border-success border-opacity-25 rounded-pill">
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
                            <i class="bx bx-chevron-down toggle-icon text-muted fs-5"></i>
                        </div>
                    </div>
                </div>

                {{-- Table container wrapper --}}
                <div id="rows-job-{{ $clean_job_key }}" class="{{ $is_active ? '' : 'd-none' }}">
                    <div class="position-relative table-wrapper-show">
                        <button type="button" class="scroll-overlay left show-scroll-button"
                            data-target="#rows-job-{{ $clean_job_key }} .table-scroll-show" data-delta="-350">
                            <i class="bx bx-chevron-left"></i>
                        </button>
                        <button type="button" class="scroll-overlay right show-scroll-button"
                            data-target="#rows-job-{{ $clean_job_key }} .table-scroll-show" data-delta="350">
                            <i class="bx bx-chevron-right"></i>
                        </button>
                        <div class="table-responsive table-scroll-show">
                            <table id="tbl-job-{{ $clean_job_key }}"
                                class="table table-sm table-hover mb-0 align-middle">
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
                                                    <span class="product-toggle cursor-pointer" style="cursor: pointer;"
                                                        data-full="{{ $data->product }}"
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
                                                            style="cursor: pointer;" data-full="{{ $data->operator }}"
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
                                            <td class="text-center fw-semibold">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="jtdrik" data-value="{{ $data->jtdrik ?? 0 }}"
                                                    data-editable="{{ strtolower($data->proses ?? '') === 'lem' ? '0' : '1' }}">
                                                    {{ $data->jtdrik ? number_format($data->jtdrik, 0, ',', '.') : '0' }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold">
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="jtpcs" data-value="{{ $data->jtpcs ?? 0 }}"
                                                    data-editable="{{ in_array(strtolower($data->proses ?? ''), ['lem', 'sortpacking']) ? '1' : '0' }}">
                                                    {{ $data->jtpcs ? number_format($data->jtpcs, 0, ',', '.') : '0' }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold" data-derived-field="outputpcs"
                                                data-id="{{ $data->id }}">
                                                {{ $data->outputpcs ? number_format($data->outputpcs, 0, ',', '.') : '0' }}
                                            </td>
                                            <td class="text-center fw-semibold" data-derived-field="outputdrik"
                                                data-id="{{ $data->id }}">
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
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold text-end">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            GRAND TOTAL
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($items->sum('input'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($items->sum('jtdrik'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($items->sum('jtpcs'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($items->sum('outputpcs'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
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

        {{-- Pagination --}}
        @if ($detailProses->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4 mt-3">
                {{ $detailProses->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>


    <!-- Toast container for notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 99999 !important; margin-top: 60px;">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
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

    <style>
        .table:not(#tblRangkuman)> :not(caption)>*>* {
            padding: 1rem 0.30rem;
        }

        #tblRangkuman> :not(caption)>*>* {
            padding: 0.25rem 0.35rem !important;
        }

        .table-wrapper-show {
            position: relative;
            overflow: hidden;
        }

        .table-scroll-show {
            overflow-x: auto;
            scrollbar-width: none;
        }

        .table-scroll-show::-webkit-scrollbar,
        #jobSearchWrapper::-webkit-scrollbar {
            display: none;
        }

        .scroll-overlay {
            position: absolute;
            top: 4.2%;
            bottom: 4.2%;
            opacity: 1;
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
        let modalBs;
        const modalBody = document.getElementById('modalBody');

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

        function updateScrollButtons(wrapper) {
            const parent = wrapper.closest('.table-wrapper-show');
            if (!parent) {
                return;
            }
            const btnLeft = parent.querySelector('.show-scroll-button.left');
            const btnRight = parent.querySelector('.show-scroll-button.right');
            if (!btnLeft || !btnRight) {
                return;
            }
            btnLeft.classList.toggle('d-none', wrapper.scrollLeft <= 0);
            btnRight.classList.toggle('d-none', wrapper.scrollLeft >= wrapper.scrollWidth - wrapper.clientWidth - 5);
        }

        function scrollShowTable(button) {
            const targetSelector = button.dataset.target;
            const delta = Number(button.dataset.delta || 0);
            const wrapper = document.querySelector(targetSelector);
            if (wrapper) {
                wrapper.scrollBy({
                    left: delta,
                    behavior: 'smooth'
                });
            }
        }

        function initShowScrolls() {
            document.querySelectorAll('.table-scroll-show').forEach(function(wrapper) {
                updateScrollButtons(wrapper);
                wrapper.addEventListener('scroll', function() {
                    updateScrollButtons(wrapper);
                });
            });

            document.querySelectorAll('.show-scroll-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    scrollShowTable(button);
                });
            });
        }

        function cancelInlineEdit(activeCell, activeValue) {
            if (activeCell) {
                $(activeCell).html(activeValue);
            }
        }

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
                cancelInlineEdit(cell, cell.data('value'));
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
                    const isTimeField = ['set', 'run', 'finish'].includes(field);

                    // 1. Update all matching inline-edit cells in the entire document (both tables and modal)
                    $(`.inline-edit-cell[data-id="${id}"]`).each(function() {
                        const $span = $(this);
                        const fieldName = $span.data('field');
                        if (fieldName && values[fieldName] !== undefined) {
                            $span.data('value', values[fieldName]);
                            const isSpanNumeric = numericFields.includes(fieldName);
                            const isSpanTime = ['set', 'run', 'finish'].includes(fieldName);
                            $span.text(isSpanNumeric ? parseFloat(values[fieldName]).toLocaleString(
                                'id-ID', {
                                    maximumFractionDigits: 0
                                }) : (isSpanTime ? formatDateTime(values[fieldName]) : values[
                                fieldName]));
                        }
                    });

                    // 2. Update all matching derived cells in the entire document (main tables)
                    $(`[data-derived-field][data-id="${id}"]`).each(function() {
                        const $td = $(this);
                        const derivedField = $td.data('derived-field');
                        if (derivedField && values[derivedField] !== undefined) {
                            $td.text(parseFloat(values[derivedField]).toLocaleString('id-ID', {
                                maximumFractionDigits: 0
                            }));
                        }
                    });

                    // 3. Update modal derived values if modal is open for the same ID
                    const $modalDetail = $('#modalDetail');
                    const modalId = $modalDetail.find('.inline-edit-cell').first().data('id');
                    if (modalId === id) {
                        $modalDetail.find('.modal-derived-val').each(function() {
                            const $span = $(this);
                            const fieldName = $span.data('field');
                            if (fieldName && values[fieldName] !== undefined) {
                                const isSpanNumeric = numericFields.includes(fieldName);
                                const isSpanTime = ['set', 'run', 'finish'].includes(fieldName);
                                $span.text(isSpanNumeric ? parseFloat(values[fieldName]).toLocaleString(
                                    'id-ID', {
                                        maximumFractionDigits: 0
                                    }) : (isSpanTime ? formatDateTime(values[fieldName]) :
                                    values[fieldName]));
                            }
                        });
                    }

                    if (response.message) {
                        showToast(response.message, 'success');
                    }

                    // 5. Refresh only tfoot (Grand Total) and Rangkuman table via $.get
                    $.get(window.location.href, function(html) {
                        const newDoc = new DOMParser().parseFromString(html, 'text/html');

                        // Update Rangkuman Table
                        const newRangkumanTbody = newDoc.querySelector('#tblRangkuman tbody');
                        const newRangkumanTfoot = newDoc.querySelector('#tblRangkuman tfoot');
                        if (newRangkumanTbody) {
                            $('#tblRangkuman tbody').replaceWith($(newRangkumanTbody));
                        }
                        if (newRangkumanTfoot) {
                            $('#tblRangkuman tfoot').replaceWith($(newRangkumanTfoot));
                        }

                        // Update tbody and tfoot of each Detail Job Table
                        newDoc.querySelectorAll('[id^="tbl-job-"]').forEach(function(newTable) {
                            const tableId = newTable.id;
                            const oldTable = document.getElementById(tableId);
                            if (oldTable) {
                                const newTbody = newTable.querySelector('tbody');
                                const newTfoot = newTable.querySelector('tfoot');
                                if (newTbody) {
                                    $(oldTable).find('tbody').replaceWith($(newTbody));
                                }
                                if (newTfoot) {
                                    $(oldTable).find('tfoot').replaceWith($(newTfoot));
                                }
                            }
                        });
                    });
                },
                error: function(xhr) {
                    cancelInlineEdit(cell, cell.data('value'));
                    const msg = xhr.responseJSON?.message || 'Gagal memperbarui data.';
                    showToast(msg, 'danger');
                }
            });
        }

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
            if (s.match(/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/)) return s.substring(0, 16);
            if (s.match(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/)) return s.replace(' ', 'T').substring(0, 16);
            const today = new Date().toISOString().substring(0, 10);
            const parts = s.split(':');
            const timeStr = parts.length >= 2 ?
                parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0') :
                '00:00';
            return today + 'T' + timeStr;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('modalDetail');
            modalBs = new bootstrap.Modal(modalEl);
            initShowScrolls();

            $(document).on('dblclick', '.inline-edit-cell', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const cell = $(this);
                const field = cell.data('field');
                const isEditable = cell.data('editable') === 1 || cell.data('editable') === '1';

                if (field === 'jtpcs' && !isEditable) {
                    return;
                }

                const activeCell = document.querySelector('.inline-edit-input') ? document.querySelector(
                    '.inline-edit-input').closest('.inline-edit-cell') : null;
                if (activeCell && activeCell !== this) {
                    const activeValue = $(activeCell).data('value') ?? '';
                    cancelInlineEdit(activeCell, activeValue);
                }

                const activeValue = cell.html();
                const id = cell.data('id');
                const value = cell.data('value') ?? '';
                const isTimeField = ['set', 'run', 'finish'].includes(field);

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

            $(document).on('keydown', '.inline-edit-input', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    saveInlineEdit($(this));
                }
                if (e.which === 27) {
                    e.preventDefault();
                    cancelInlineEdit($(this).closest('.inline-edit-cell'), $(this).closest(
                        '.inline-edit-cell').data('value'));
                }
            });

            // toggle product text
            $(document).on('click', '.product-toggle', function() {
                const $span = $(this);
                const isShort = $span.text().trim() === $span.data('short').trim();
                $span.text(isShort ? $span.data('full') : $span.data('short'));
            });

            // toggle operator text
            $(document).on('click', '.operator-toggle', function() {
                const $span = $(this);
                const isShort = $span.text().trim() === $span.data('short').trim();
                $span.text(isShort ? $span.data('full') : $span.data('short'));
            });

            $(document).on('focusout', '.inline-edit-input', function() {
                saveInlineEdit($(this));
            });
        });

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

        // Selected Jobs Management
        function renderSelectedJobs() {
            const selectedJobs = $('#searchJobsHidden').val().split(',').map(function(item) {
                return item.trim();
            }).filter(Boolean);

            const $container = $('#selectedJobsContainer');
            const $placeholder = $('#selectedJobsPlaceholder');

            if (selectedJobs.length) {
                $placeholder.hide();
                $container.find('.job-badge').remove();

                selectedJobs.forEach(function(job) {
                    $('<span class="badge bg-label-primary rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1 job-badge">')
                        .append($('<span class="fw-semibold">').text(job))
                        .append($('<i class="bx bx-x cursor-pointer" style="font-size: 12px;"></i>').on('click',
                            function() {
                                removeSelectedJob(job);
                            }))
                        .appendTo($container);
                });
            } else {
                $container.find('.job-badge').remove();
                $placeholder.show();
            }
        }

        function removeSelectedJob(job) {
            const selectedJobs = $('#searchJobsHidden').val().split(',').map(function(item) {
                return item.trim();
            }).filter(function(item) {
                return item && item !== job;
            });

            $('#searchJobsHidden').val(selectedJobs.join(', '));
            renderSelectedJobs();
        }

        function addSelectedJob(job) {
            const selectedJobs = $('#searchJobsHidden').val().split(',').map(function(item) {
                return item.trim();
            }).filter(Boolean);

            if (!selectedJobs.includes(job)) {
                selectedJobs.push(job);
            }

            $('#searchJobsHidden').val(selectedJobs.join(', '));
            $('#searchJob').val('');
            renderSelectedJobs();
        }

        // search suggestions
        $('#searchJob').on('keyup', function() {
            let keyword = $(this).val().trim();
            const $suggestions = $('#jobSuggestions');

            if (keyword.length < 2) {
                $suggestions.empty().addClass('d-none');
                return;
            }

            $.ajax({
                url: "{{ route('proses-produksi.search-suggestions') }}",
                type: "GET",
                data: {
                    q: keyword
                },
                success: function(response) {
                    let html = '';

                    if (response.length) {
                        response.forEach(function(item) {
                            html += `
                                <a href="#"
                                    class="list-group-item list-group-item-action border-0 px-3 py-2 d-flex align-items-center gap-2 pilih-job"
                                    data-job="${item.job}">
                                    <i class="bx bx-briefcase text-primary"></i>
                                    <span class="fw-semibold">${item.job}</span>
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

        $(document).on('click', '.pilih-job', function(e) {
            e.preventDefault();

            const job = $(this).data('job');
            addSelectedJob(job);
            $('#jobSuggestions').empty().addClass('d-none');
            $('#searchJob').focus();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#jobSuggestions, #jobSearchWrapper').length) {
                $('#jobSuggestions').empty().addClass('d-none');
            }
        });

        $(document).ready(function() {
            renderSelectedJobs();
        });
    </script>

    {{-- scipt modal log --}}
    <script>
        // ── showActivityLog : modal tersendiri 
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

        // function log activity
        function showActivityLog(id, job, product) {
            const alModal = new bootstrap.Modal(document.getElementById('modalActivityLog'));
            const alBody = document.getElementById('alModalBody');
            const alBadge = document.getElementById('alBadgeCount');
            const alSubtitle = document.getElementById('alSubtitle');

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
                                    <i class="bx bx-folder-open mb-2" style="font-size:2.5rem;opacity:.2"></i>
                                    <p class="fw-semibold mb-1" style="color:#697a8d">Belum Ada Riwayat Perubahan</p>
                                    <small>Tidak ada log aktivitas untuk baris ini.</small>
                                </div>`;
                        return;
                    }

                    const isNull = v => v === null || v === '' || v === undefined;

                    // tabel activity log
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

                        // modal activity log 
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
                                            <th style="font-size:.62rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#697a8d;padding:.72rem .9rem;border-bottom:2px solid rgba(105,108,255,.1)">Kolom (Field)</th>
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
