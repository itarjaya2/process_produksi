@extends('layouts.main')

@section('main-content')

    @php
        /**
         */
        $fieldLabels = [
            'job' => ['label' => 'No. Job', 'code' => 'job'],
            'proses' => ['label' => 'Proses', 'code' => 'proses'],
            'product' => ['label' => 'Produk', 'code' => 'product'],
            'designno' => ['label' => 'Docket', 'code' => 'designno'],
            'operator' => ['label' => 'Operator', 'code' => 'operator'],
            'tanggal' => ['label' => 'Tanggal', 'code' => 'tanggal'],
            'shift' => ['label' => 'Shift', 'code' => 'shift'],
            'upspk' => ['label' => 'UP SPK', 'code' => 'upspk'],
            'input' => ['label' => 'Input', 'code' => 'input'],
            'jtdrik' => ['label' => 'JT Drik', 'code' => 'jtdrik'],
            'jtpcs' => ['label' => 'JT PCS', 'code' => 'jtpcs'],
            'outputpcs' => ['label' => 'Output PCS', 'code' => 'outputpcs'],
            'outputdrik' => ['label' => 'Output Drik', 'code' => 'outputdrik'],
            'po' => ['label' => 'PO', 'code' => 'po'],
            'set' => ['label' => 'Set', 'code' => 'set'],
            'run' => ['label' => 'Run', 'code' => 'run'],
            'finish' => ['label' => 'Finish', 'code' => 'finish'],
            'totaljam' => ['label' => 'Total Jam', 'code' => 'totaljam'],
            'total_pengerjaan_drik' => ['label' => 'Peng. Drik', 'code' => 'total_pengerjaan_drik'],
            'total_pengerjaan_pcs' => ['label' => 'Peng. PCS', 'code' => 'total_pengerjaan_pcs'],
        ];

        $badgePalette = ['primary', 'success', 'warning', 'info', 'danger', 'dark'];

        // Summary stats dihitung dari collection halaman saat ini
        $collection = $logs->getCollection();
        $todayTotal = $collection
            ->filter(function ($l) {
                if (!$l->created_at) {
                    return false;
                }
                try {
                    $logDate = \Carbon\Carbon::parse($l->created_at)->format('Y-m-d');
                    $todayDate = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
                    return $logDate === $todayDate;
                } catch (\Exception $e) {
                    return false;
                }
            })
            ->count();
        $uniqueProses = $collection->map(fn($l) => $l->prosesProduksi->proses ?? null)->filter()->unique()->count();
        $spkTerlibat = $collection->map(fn($l) => $l->prosesProduksi->job ?? null)->filter()->unique()->count();

        $hasFilter = collect(['job', 'proses', 'user_id', 'field_name', 'tanggal_dari', 'tanggal_sampai'])->contains(
            fn($k) => request()->filled($k),
        );
    @endphp

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- SCOPED CSS                                                       --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <style>
        /* ── Stat cards ─────────────────────────────────────────────────── */
        .al-stat {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 8px rgba(105, 108, 255, .09), 0 1px 3px rgba(0, 0, 0, .04);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .al-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(105, 108, 255, .14), 0 2px 6px rgba(0, 0, 0, .06);
        }

        .al-stat .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .al-stat .stat-val {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -.03em;
        }

        .al-stat .stat-lbl {
            font-size: .67rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #a0a3b1;
        }

        .al-stat .stat-sub {
            font-size: .70rem;
            color: #8592a3;
            margin-top: 1px;
        }

        /* ── Cards shared ───────────────────────────────────────────────── */
        .al-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 1px 8px rgba(105, 108, 255, .08), 0 1px 3px rgba(0, 0, 0, .04);
            position: relative;
        }

        .al-card-filter {
            z-index: 100;
            overflow: visible;
        }

        .al-card-table {
            z-index: 1;
        }

        .al-card>.card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, .055);
            padding: .85rem 1.25rem;
        }

        /* ── Section micro-label ────────────────────────────────────────── */
        .al-group-lbl {
            font-size: .60rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #a0a3b1;
            margin-bottom: .5rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Active filter strip ────────────────────────────────────────── */
        .al-filter-strip {
            padding: .5rem 1rem;
            margin-bottom: 1rem;
            background: rgba(105, 108, 255, .045);
            border: 1px solid rgba(105, 108, 255, .12);
            border-radius: 10px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        /* ── Table ──────────────────────────────────────────────────────── */
        #tblLog thead th {
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #697a8d;
            white-space: nowrap;
            padding: .78rem 1rem;
            background: #f5f5f9;
            border-bottom: 2px solid rgba(105, 108, 255, .1);
        }

        #tblLog tbody td {
            padding: .76rem 1rem;
            vertical-align: middle;
            font-size: .82rem;
            border-color: rgba(0, 0, 0, .038);
        }

        #tblLog tbody tr {
            transition: background .1s ease;
        }

        #tblLog tbody tr:hover {
            background: rgba(105, 108, 255, .028);
        }

        .diff-chip {
            display: inline-flex;
            align-items: flex-start;
            gap: 5px;
            padding: .22rem .6rem;
            border-radius: 7px;
            font-size: .9rem;
            font-weight: 600;
            font-family: 'SFMono-Regular', 'Consolas', 'Courier New', monospace;
            max-width: 180px;
            word-break: break-all;
            white-space: normal;
            line-height: 1.4;
        }

        .diff-chip.text-danger {
            color: #dc3545 !important;
        }

        .diff-chip.text-success {
            color: #146c43 !important;
        }

        .diff-chip i {
            font-size: .8rem;
            opacity: .65;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .diff-null {
            color: #c4c8d0;
            font-style: italic;
            font-size: .76rem;
        }

        /* ── Field badge pill ───────────────────────────────────────────── */
        .field-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: .2rem .58rem;
            border-radius: 8px;
            background: rgba(105, 108, 255, .09);
            border: 1px solid rgba(105, 108, 255, .14);
            padding: .2rem .58rem;
            border-radius: 8px;
            background: rgba(105, 108, 255, .09);
            border: 1px solid rgba(105, 108, 255, .14);
        }

        .field-pill .fp-label {
            font-size: .75rem;
            font-weight: 600;
            color: #696cff;
            line-height: 1.25;
        }

        .field-pill .fp-code {
            font-size: .59rem;
            color: #8d98a7;
            font-family: 'SFMono-Regular', 'Consolas', monospace;
            letter-spacing: .03em;
            line-height: 1.1;
        }

        /* ── User avatar ────────────────────────────────────────────────── */
        .uavatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .7rem;
            font-weight: 700;
            flex-shrink: 0;
            background: rgba(105, 108, 255, .12);
            color: #696cff;
        }

        /* ── Timestamp ──────────────────────────────────────────────────── */
        .ts-d {
            font-weight: 600;
            font-size: .82rem;
            line-height: 1.2;
        }

        .ts-t {
            font-size: .69rem;
            color: #8592a3;
            font-family: monospace;
            line-height: 1;
        }

        /* ── Row number ─────────────────────────────────────────────────── */
        .rn {
            color: #c4c6cc;
            font-size: .68rem;
            font-family: monospace;
        }

        /* ── Empty state ────────────────────────────────────────────────── */
        .al-empty {
            padding: 5rem 1rem;
            text-align: center;
        }

        .al-empty .ei-wrap {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: rgba(105, 108, 255, .07);
            margin: 0 auto 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: rgba(105, 108, 255, .4);
        }

        .al-empty .et {
            font-size: 1rem;
            font-weight: 700;
            color: #697a8d;
            margin-bottom: .3rem;
        }

        .al-empty .es {
            font-size: .81rem;
            color: #a0a3b1;
        }

        /* ── Footer ─────────────────────────────────────────────────────── */
        .al-footer {
            background: transparent;
            border-top: 1px solid rgba(0, 0, 0, .055);
            padding: .78rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .al-footer .dinfo {
            font-size: .74rem;
            color: #8592a3;
        }

        .al-footer .dinfo strong {
            color: #566a7f;
        }

        /* ── Table scroll ───────────────────────────────────────────────── */
        .al-tscroll {
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(105, 108, 255, .2) transparent;
        }

        .al-tscroll::-webkit-scrollbar {
            height: 4px;
        }

        .al-tscroll::-webkit-scrollbar-thumb {
            background: rgba(105, 108, 255, .22);
            border-radius: 10px;
        }

        /* ── Refresh spin ───────────────────────────────────────────────── */
        @keyframes al-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-refresh.spin i {
            animation: al-spin .5s linear;
        }

        /* ── Webkit Date Picker Indicator Customization ─────────────────── */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%238592a3' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E") !important;
            background-repeat: no-repeat;
            background-size: 14px 14px;
            background-position: center;
            opacity: 0.5;
            cursor: pointer;
            width: 14px;
            height: 14px;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 0.9;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- PAGE HEADER  --}}
        <div class="d-flex align-items-start justify-content-between mb-4 gap-3 flex-wrap">
            <div>
                <h4 class="fw-bold mb-1" style="letter-spacing:-.02em">Activity Log Prodution</h4>
                <p class="text-muted mb-0 small">
                    Riwayat lengkap setiap perubahan data — siapa mengubah apa, kapan, serta nilai sebelum &amp; sesudahnya.
                </p>
            </div>
            <div class="d-flex gap-2 flex-shrink-0">
                <a href="{{ route('proses-produksi.index') }}"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                    <i class="bx bx-arrow-back fs-5"></i>
                    <span>Kembali</span>
                </a>
                <a href="{{ url()->current() }}"
                    class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 btn-refresh" id="btnRefresh">
                    <i class="bx bx-refresh fs-5"></i>
                    <span class="d-none d-sm-inline">Refresh Data</span>
                </a>
            </div>
        </div>

        {{-- SUMMARY STAT CARDS --}}

        <div class="row g-3 mb-4">

            {{-- Total Semua Log --}}
            <div class="col-6 col-lg-4">
                <div class="card al-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box bg-label-primary">
                            <i class="bx bx-list-ul text-primary"></i>
                        </div>
                        <div style="min-width:0">
                            <div class="stat-val text-primary">{{ number_format($logs->total(), 0, ',', '.') }}</div>
                            <div class="stat-lbl">Total Log</div>
                            <div class="stat-sub">Semua riwayat</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aktivitas Hari Ini --}}
            <div class="col-6 col-lg-4">
                <div class="card al-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box bg-label-success">
                            <i class="bx bx-pulse text-success"></i>
                        </div>
                        <div style="min-width:0">
                            <div class="stat-val text-success">{{ number_format($todayTotal, 0, ',', '.') }}</div>
                            <div class="stat-lbl">Hari Ini</div>
                            <div class="stat-sub">Aktivitas hari ini</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Proses Unik (halaman ini) --}}
            <div class="col-6 col-lg-4">
                <div class="card al-stat h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box bg-label-warning">
                            <i class="bx bx-cog text-warning"></i>
                        </div>
                        <div style="min-width:0">
                            <div class="stat-val text-warning">{{ $uniqueProses }}</div>
                            <div class="stat-lbl">Proses</div>
                            <div class="stat-sub">Mesin terlibat</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- FILTER CARD --}}
        <div class="card al-card al-card-filter mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-1">
                    <i class="bx bx-filter-alt text-primary fs-5"></i>
                    <span class="fw-semibold">Filter &amp; Pencarian</span>
                </div>
                @if ($hasFilter)
                    <span class="badge bg-label-primary" style="font-size:.68rem; border-radius:8px">
                        Filter Aktif
                    </span>
                @endif
            </div>
            <div class="card-body pt-3 pb-3">
                <form action="{{ url()->current() }}" method="GET" id="formFilter">
                    <div class="row g-2 mb-3">

                        {{-- No. Job --}}
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="position-relative">
                                <div id="jobSearchWrapper"
                                    class="input-group input-group-sm flex-nowrap align-items-center bg-white"
                                    style="border: 1px solid #d9dee3; border-radius: 0.375rem; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; height: 31px; overflow: hidden;">
                                    <span class="input-group-text bg-transparent border-0 text-muted pe-1">
                                        <i class="bx bx-briefcase"></i>
                                    </span>
                                    <div id="alScrollableContainer"
                                        class="d-flex align-items-center flex-grow-1 flex-nowrap"
                                        style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; height: 100%;">
                                        <div id="alSelectedJobsContainer"
                                            class="d-flex flex-nowrap align-items-center gap-1 py-1 ps-0">
                                        </div>
                                        <input type="text" id="alSearchJob"
                                            class="form-control form-control-sm border-0 ps-1 bg-transparent"
                                            placeholder="No. Job" autocomplete="off"
                                            style="min-width: 50px; flex: 1 1 auto; font-size: 0.75rem; box-shadow: none; height: 100%; border: none;">
                                    </div>
                                </div>
                                <input type="hidden" id="alSearchJobsHidden" name="job"
                                    value="{{ request('job') }}">
                                <div id="alJobSuggestions"
                                    class="list-group position-absolute w-100 shadow-sm border rounded-3 overflow-hidden bg-white d-none"
                                    style="z-index: 1055; top: calc(100% + 4px); max-height: 200px; overflow-y: auto;">
                                </div>
                            </div>
                        </div>
                        {{-- END No. Job --}}

                        {{-- Proses Produksi --}}
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted">
                                    <i class="bx bx-cog"></i>
                                </span>
                                <select name="proses" class="form-select form-select-sm border-start-0"
                                    style="padding-left:.4rem">
                                    <option value="">Semua Proses</option>
                                    @foreach ($listProses as $p)
                                        <option value="{{ $p }}"
                                            {{ request('proses') == $p ? 'selected' : '' }}>
                                            {{ $p }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- User / Operator --}}
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted">
                                    <i class="bx bx-user"></i>
                                </span>
                                <select name="user_id" class="form-select form-select-sm border-start-0"
                                    style="padding-left:.4rem">
                                    <option value="">Semua User</option>
                                    @foreach ($listUsers as $u)
                                        <option value="{{ $u->id }}"
                                            {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Kolom (Field) yang Diubah --}}
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted">
                                    <i class="bx bx-columns"></i>
                                </span>
                                <select name="field_name" class="form-select form-select-sm border-start-0"
                                    style="padding-left:.4rem">
                                    <option value="">Semua Kolom</option>
                                    @foreach ($fieldLabels as $rawKey => $info)
                                        <option value="{{ $rawKey }}"
                                            {{ request('field_name') == $rawKey ? 'selected' : '' }}>
                                            {{ $info['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Rentang Tanggal --}}
                        <div class="col-12 col-sm-12 col-md-8 col-lg-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-transparent border-end-0 text-muted">
                                    <i class="bx bx-calendar"></i>
                                </span>
                                <input type="date" name="tanggal_dari"
                                    class="form-control form-control-sm border-start-0"
                                    value="{{ request('tanggal_dari') }}" title="Tanggal Dari">
                                <span class="input-group-text bg-light text-muted" style="font-size:.72rem">s/d</span>
                                <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                    value="{{ request('tanggal_sampai') }}" title="Tanggal Sampai">
                            </div>
                        </div>

                    </div>

                    {{-- Action buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary px-4">
                            <i class="bx bx-filter-alt me-1"></i>Terapkan Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="bx bx-reset me-1"></i>Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Active filter badges --}}
        @if ($hasFilter)
            @php
                $activeBadges = [];
                if (request()->filled('job')) {
                    $activeBadges['No. Job'] = request('job');
                }
                if (request()->filled('proses')) {
                    $activeBadges['Proses'] = request('proses');
                }
                if (request()->filled('user_id')) {
                    $activeBadges['User ID'] = request('user_id');
                }
                if (request()->filled('field_name')) {
                    $fn = request('field_name');
                    $activeBadges['Kolom'] = isset($fieldLabels[$fn]) ? $fieldLabels[$fn]['label'] : $fn;
                }
                if (request()->filled('tanggal_dari')) {
                    $activeBadges['Dari'] = \Carbon\Carbon::parse(request('tanggal_dari'))->format('d-m-Y');
                }
                if (request()->filled('tanggal_sampai')) {
                    $activeBadges['Sampai'] = \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d-m-Y');
                }
            @endphp
            <div class="al-filter-strip">
                <span class="text-muted small fw-semibold">
                    <i class="bx bx-info-circle me-1"></i>Filter aktif:
                </span>
                @foreach ($activeBadges as $k => $v)
                    <span class="badge bg-label-primary font-monospace px-2 py-1" style="font-size:.68rem">
                        <strong>{{ $k }}:</strong> {{ $v }}
                    </span>
                @endforeach
                <a href="{{ url()->current() }}"
                    class="ms-auto btn btn-sm btn-outline-danger py-1 px-2 d-flex align-items-center gap-1">
                    <i class="bx bx-trash-alt"></i>Bersihkan
                </a>
            </div>
        @endif

        {{-- TABEL RIWAYAT --}}
        <div class="card al-card al-card-table mb-4">

            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-code-alt text-primary fs-5"></i>
                    <span class="fw-semibold">Riwayat Perubahan Data</span>
                    <span class="badge bg-label-secondary ms-1" style="font-size:.67rem; border-radius:8px">
                        {{ number_format($logs->total(), 0, ',', '.') }} entri
                    </span>
                </div>
                <small class="text-muted d-none d-md-flex align-items-center gap-2" style="font-size:.70rem">
                    <span class="d-flex align-items-center gap-1">
                        <span
                            style="width:8px;height:8px;border-radius:2px;background:rgba(220,53,69,.35);display:inline-block"></span>
                        Nilai lama
                    </span>
                    <span class="text-muted">|</span>
                    <span class="d-flex align-items-center gap-1">
                        <span
                            style="width:8px;height:8px;border-radius:2px;background:rgba(25,135,84,.35);display:inline-block"></span>
                        Nilai baru
                    </span>
                </small>
            </div>

            <div class="card-body p-0">
                <div class="al-tscroll">
                    <table class="table table-sm table-hover mb-0 align-middle" id="tblLog">
                        <thead>
                            <tr>
                                <th style="width:44px">#</th>
                                <th style="min-width:130px">Waktu</th>
                                <th style="min-width:145px">User</th>
                                <th style="min-width:110px">No. Job</th>
                                <th style="min-width:145px">Produk</th>
                                <th style="min-width:130px">Proses Produksi</th>
                                <th style="min-width:145px">Kolom</th>
                                <th style="min-width:165px" class="text-start">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <span
                                            style="width:7px;height:7px;border-radius:50%;background:#dc3545;display:inline-block"></span>
                                        Sebelum
                                    </span>
                                </th>
                                <th style="min-width:165px" class="text-start">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <span
                                            style="width:7px;height:7px;border-radius:50%;background:#198754;display:inline-block"></span>
                                        Sesudah
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $i => $log)
                                @php
                                    $produk = $log->prosesProduksi->product ?? '-';
                                    $proses = $log->prosesProduksi->proses ?? null;
                                    $jobNo = $log->prosesProduksi->job ?? '-';
                                    $rawField = $log->field_name ?? '';
                                    $fInfo = $fieldLabels[$rawField] ?? [
                                        'label' => ucwords(str_replace('_', ' ', $rawField)),
                                        'code' => $rawField,
                                    ];
                                    $teksBadge = $proses ?? 'default';
                                    $bColor = $badgePalette[abs(crc32($teksBadge)) % count($badgePalette)];
                                    $oldVal = $log->old_value;
                                    $newVal = $log->new_value;
                                    $isNumericField = in_array($rawField, [
                                        'input',
                                        'jtdrik',
                                        'jtpcs',
                                        'outputpcs',
                                        'outputdrik',
                                        'total_pengerjaan_drik',
                                        'total_pengerjaan_pcs',
                                        'totaljam',
                                        'upspk',
                                    ]);
                                @endphp
                                <tr>
                                    {{-- No --}}
                                    <td><span class="rn">{{ $logs->firstItem() + $i }}</span></td>

                                    {{-- Waktu --}}
                                    <td class="text-nowrap">
                                        @if ($log->created_at)
                                            <div class="ts-d">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/y') }}</div>
                                            <div class="ts-t">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</div>
                                        @else
                                            <span class="text-muted small">–</span>
                                        @endif
                                    </td>

                                    {{-- User --}}
                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold small">{{ $log->user->name ?? '–' }}</span>
                                        </div>
                                    </td>

                                    {{-- No. Job --}}
                                    <td class="text-nowrap">
                                        <span class="fw-bold small">{{ $jobNo }}</span>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="fw-bold small">{{ $produk }}</span>
                                    </td>

                                    {{-- Proses Produksi --}}
                                    <td>
                                        @if ($proses)
                                            <span class="badge bg-label-{{ $bColor }} fw-semibold"
                                                style="font-size:.74rem; border-radius:8px; padding:.22rem .58rem">
                                                {{ $proses }}
                                            </span>
                                        @else
                                            <span class="text-muted small">–</span>
                                        @endif
                                    </td>

                                    {{-- Kolom --}}
                                    <td>
                                        <span class="field-pill">
                                            <span class="fp-label">{{ $fInfo['label'] }}</span>
                                        </span>
                                    </td>

                                    {{-- Sebelum (Old Value) --}}
                                    <td class="td-old text-start">
                                        @if ($oldVal !== null && $oldVal !== '')
                                            <span class="diff-chip text-danger"
                                                style="white-space: nowrap; max-width: none;">
                                                @if (in_array($rawField, ['tanggal', 'set', 'run', 'finish']))
                                                    {{ \Carbon\Carbon::parse($oldVal)->format('d/m/y H:i') }}
                                                @else
                                                    {{ is_numeric($oldVal) && $rawField !== 'job'
                                                        ? (floor($oldVal) == $oldVal
                                                            ? number_format((float) $oldVal, 0, ',', '.')
                                                            : number_format((float) $oldVal, 2, ',', '.'))
                                                        : $oldVal }}
                                                @endif
                                            </span>
                                        @else
                                            {!! $isNumericField
                                                ? '<span class="diff-chip ' . ($oldVal === null || $oldVal === '' ? 'text-danger' : 'text-success') . '">0</span>'
                                                : '<span class="diff-null">null</span>' !!}
                                        @endif
                                    </td>

                                    {{-- Sesudah (New Value) --}}
                                    <td class="td-new text-start">
                                        @if ($newVal !== null && $newVal !== '')
                                            <span class="diff-chip text-success"
                                                style="white-space: nowrap; max-width: none;">
                                                @if (in_array($rawField, ['tanggal', 'set', 'run', 'finish']))
                                                    {{ \Carbon\Carbon::parse($newVal)->format('d/m/y H:i') }}
                                                @else
                                                    {{ is_numeric($newVal) && $rawField !== 'job' ? (floor($newVal) == $newVal ? number_format((float) $newVal, 0, ',', '.') : number_format((float) $newVal, 2, ',', '.')) : $newVal }}
                                                @endif
                                            </span>
                                        @else
                                            {!! $isNumericField
                                                ? '<span class="diff-chip ' . ($oldVal === null || $oldVal === '' ? 'text-danger' : 'text-success') . '">0</span>'
                                                : '<span class="diff-null">null</span>' !!}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="al-empty">
                                            <div class="ei-wrap">
                                                <i class="bx bx-folder-open"></i>
                                            </div>
                                            <p class="et">Belum Ada Riwayat Perubahan</p>
                                            <p class="es">
                                                Tidak ada log aktivitas yang cocok dengan filter yang diterapkan.<br>
                                                Coba ubah parameter filter atau reset ke tampilan semua data.
                                            </p>
                                            @if ($hasFilter)
                                                <a href="{{ url()->current() }}"
                                                    class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="bx bx-reset me-1"></i>Tampilkan Semua Data
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer: info jumlah + pagination --}}
            @if ($logs->total() > 0)
                <div class="al-footer">
                    <span class="dinfo">
                        <i class="bx bx-list-ul me-1"></i>
                        Menampilkan
                        <strong>{{ $logs->firstItem() ?? 0 }}</strong>–<strong>{{ $logs->lastItem() ?? 0 }}</strong>
                        dari <strong>{{ number_format($logs->total(), 0, ',', '.') }}</strong> entri log
                    </span>
                    <div>
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>{{-- /al-table-card --}}

    </div>{{-- /container --}}

    <script>
        document.getElementById('btnRefresh')?.addEventListener('click', function() {
            this.classList.add('spin');
        });

        // ── Autocomplete Multi-tag Filter No. Job (Activity Log) ──────────────
        (function($) {
            const wrapperId = 'jobSearchWrapper';
            const containerId = 'alSelectedJobsContainer';
            const inputId = 'alSearchJob';
            const hiddenId = 'alSearchJobsHidden';
            const suggestionsId = 'alJobSuggestions';
            const badgeClass = 'al-job-badge';

            function renderBadges() {
                const val = $('#' + hiddenId).val() || '';
                const selectedItems = val.split(',').map(s => s.trim()).filter(Boolean);
                const $container = $('#' + containerId);
                $container.find('.' + badgeClass).remove();
                if (selectedItems.length) {
                    $('#' + inputId).attr('placeholder', '');
                    selectedItems.forEach(function(item) {
                        $('<span class="badge bg-label-primary rounded-pill px-2 d-inline-flex align-items-center gap-1 ' +
                                badgeClass +
                                '" style="font-size:0.7rem;line-height:1.2;flex-shrink:0;white-space:nowrap;">')
                            .append($('<span class="fw-semibold">').text(item))
                            .append($('<i class="bx bx-x cursor-pointer" style="font-size:11px;">').on('click',
                                function(e) {
                                    e.stopPropagation();
                                    removeItem(item);
                                }))
                            .appendTo($container);
                    });
                } else {
                    $('#' + inputId).attr('placeholder', 'No. Job');
                }
                const sc = $container.closest('.d-flex')[0];
                if (sc) setTimeout(() => {
                    sc.scrollLeft = sc.scrollWidth;
                }, 50);
            }

            function removeItem(item) {
                const val = $('#' + hiddenId).val() || '';
                const items = val.split(',').map(s => s.trim()).filter(s => s && s !== item);
                $('#' + hiddenId).val(items.join(', '));
                renderBadges();
            }

            function addItem(item) {
                const val = $('#' + hiddenId).val() || '';
                const items = val.split(',').map(s => s.trim()).filter(Boolean);
                if (!items.includes(item)) items.push(item);
                $('#' + hiddenId).val(items.join(', '));
                renderBadges();
            }

            renderBadges();

            // Keydown: comma, semicolon, space → buat chip
            $('#' + inputId).on('keydown', function(e) {
                if (e.which === 188 || e.which === 186 || e.which === 32) {
                    const v = $(this).val().trim().replace(/[,;]+$/, '');
                    if (v) {
                        e.preventDefault();
                        addItem(v);
                        $(this).val('');
                        $('#' + suggestionsId).empty().addClass('d-none');
                    }
                }
            });

            // Keyup: autocomplete
            $('#' + inputId).on('keyup', function(e) {
                if (e.which === 13) return;
                const keyword = $(this).val().trim();
                if (keyword.length < 2) {
                    $('#' + suggestionsId).empty().addClass('d-none');
                    return;
                }
                $.get('{{ route('proses-produksi.search-suggestions') }}', {
                    q: keyword,
                    type: 'job'
                }, function(response) {
                    if (response.length) {
                        let html = '';
                        response.forEach(function(item) {
                            html +=
                                '<a href="#" class="list-group-item list-group-item-action border-0 px-3 py-2 d-flex align-items-center gap-2 pilih-al-job" data-value="' +
                                item.job + '">' +
                                '<i class="bx bx-briefcase text-primary" style="font-size:0.85rem;"></i>' +
                                '<span class="fw-semibold" style="font-size:0.8rem;">' + item
                                .job + '</span></a>';
                        });
                        $('#' + suggestionsId).html(html).removeClass('d-none');
                    } else {
                        $('#' + suggestionsId).empty().addClass('d-none');
                    }
                });
            });

            // Paste support
            $('#' + inputId).on('paste', function(e) {
                const pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('Text');
                if (pasted) {
                    const items = pasted.split(/[\s,;|]+/).map(s => s.trim()).filter(Boolean);
                    if (items.length > 1) {
                        e.preventDefault();
                        items.forEach(function(item) {
                            addItem(item);
                        });
                        $(this).val('');
                        $('#' + suggestionsId).empty().addClass('d-none');
                    }
                }
            });

            // Suggestion click
            $(document).on('click', '.pilih-al-job', function(e) {
                e.preventDefault();
                addItem($(this).data('value'));
                $('#' + suggestionsId).empty().addClass('d-none');
                $('#' + inputId).val('').focus();
            });

            // Close on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#' + suggestionsId + ', #' + wrapperId).length) {
                    $('#' + suggestionsId).empty().addClass('d-none');
                }
            });

            // Focus/blur styling
            $('#' + inputId).on('focus', function() {
                $('#' + wrapperId).css({
                    'border-color': '#86b7fe',
                    'box-shadow': '0 0 0 0.25rem rgba(13,110,253,0.25)'
                });
            }).on('blur', function() {
                $('#' + wrapperId).css({
                    'border-color': '',
                    'box-shadow': ''
                });
            });

            // Wrapper click → focus input
            $('#' + wrapperId).on('click', function(e) {
                if (e.target.id !== inputId && !$(e.target).closest('.' + badgeClass).length) {
                    $('#' + inputId).focus();
                }
            });

            // Form submit: flush typed value to chips
            $('#' + wrapperId).closest('form').on('submit', function() {
                const typed = $('#' + inputId).val().trim();
                if (typed) {
                    typed.split(/[\s,;|]+/).map(s => s.trim()).filter(Boolean).forEach(function(item) {
                        addItem(item);
                    });
                    $('#' + inputId).val('');
                }
            });
        })(jQuery);
    </script>

@endsection
