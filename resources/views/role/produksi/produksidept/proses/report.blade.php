@extends('layouts.main')

@section('main-content')
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

        .produksi-modern .ppx-header {
            border-bottom: 1px solid var(--ppx-border);
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

        .produksi-modern .ppx-docket-pill {
            border-radius: var(--ppx-radius-md);
            background: rgba(var(--ppx-primary-rgb), .08);
            border: 1px solid rgba(var(--ppx-primary-rgb), .25);
        }

        .produksi-modern .ppx-docket-val {
            color: var(--ppx-primary-dark);
        }

        .produksi-modern .ppx-btn-apply {
            border-radius: var(--ppx-radius-sm);
            font-size: .82rem;
            font-weight: 600;
            height: 38px;
            box-shadow: 0 6px 16px -6px rgba(var(--ppx-primary-rgb), .55);
            white-space: nowrap;
        }

        .produksi-modern .ppx-btn-reset {
            border-radius: var(--ppx-radius-sm);
            font-size: .82rem;
            font-weight: 600;
            height: 38px;
            border: 1px solid var(--ppx-border);
            color: var(--ppx-muted);
            background: var(--ppx-surface-soft);
            white-space: nowrap;
        }

        .produksi-modern .ppx-btn-reset:hover {
            color: var(--ppx-ink);
            background: #f0f0f7;
        }

        .produksi-modern .btn-icon-only {
            padding-inline: .7rem;
        }

        /* ══════════════════════════════════════════════════════
                                                                                   FIX: kotak search job — dulu melebar mengikuti isi chip,
                                                                                   sekarang dikunci lebarnya (300px, lihat inline style di HTML)
                                                                                   dan discroll ke samping secara internal begitu chip penuh.
                                                                                   ══════════════════════════════════════════════════════ */
        .produksi-modern .ppx-chip-input {
            display: flex;
            align-items: center;
            border: 1px solid var(--ppx-border);
            border-radius: var(--ppx-radius-sm);
            background: var(--ppx-surface);
            height: 38px;
            padding-inline: .55rem;
            transition: border-color .15s ease, box-shadow .15s ease;
            overflow-x: auto;
            scrollbar-width: none;
            white-space: nowrap;
        }

        .produksi-modern .ppx-chip-input::-webkit-scrollbar {
            display: none;
        }

        .produksi-modern .ppx-chip-input:focus-within {
            border-color: var(--ppx-primary);
            box-shadow: 0 0 0 3px rgba(var(--ppx-primary-rgb), .12);
        }

        .produksi-modern .ppx-chip-input-icon {
            color: var(--ppx-muted);
            display: inline-flex;
            align-items: center;
            padding-right: .4rem;
            position: sticky;
            left: 0;
            background: var(--ppx-surface);
            flex-shrink: 0;
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
            white-space: nowrap;
        }

        .produksi-modern .ppx-chip-native-input {
            border: none;
            outline: none;
            background: transparent;
            font-size: .8rem;
            flex: 1 1 auto;
            min-width: 90px;
            padding-left: .3rem;
        }

        .produksi-modern .job-badge {
            background: rgba(var(--ppx-primary-rgb), .1) !important;
            color: var(--ppx-primary-dark) !important;
            font-size: .7rem !important;
            border-radius: 20px !important;
            flex-shrink: 0;
        }

        /* FIX: dropdown saran sekarang benar-benar "mengambang" (position-absolute
                                                                                   ditambahkan langsung di markup) dengan offset top yang jelas, sehingga
                                                                                   tidak lagi mendorong layout di bawahnya. */
        .produksi-modern .ppx-suggestions {
            z-index: 1055;
            top: calc(100% + 6px);
            max-height: 260px;
            overflow-y: auto;
            border-radius: var(--ppx-radius-sm);
            border: 1px solid var(--ppx-border);
            box-shadow: var(--ppx-shadow-md);
            background: var(--ppx-surface);
        }

        .produksi-modern .ppx-chip-filter {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .72rem;
            font-weight: 700;
            padding: .35rem .7rem;
            border-radius: 20px;
            background: rgba(var(--ppx-primary-rgb), .08);
            color: var(--ppx-primary-dark);
            border: 1px solid rgba(var(--ppx-primary-rgb), .18);
        }

        .produksi-modern .ppx-badge-count {
            background: rgba(var(--ppx-primary-rgb), .12);
            color: var(--ppx-primary-dark);
            font-weight: 700;
            font-size: .78rem;
            padding: .4em .8em;
            border-radius: 20px;
        }

        .produksi-modern .ppx-badge-count-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 .4rem;
            border-radius: 20px;
            background: rgba(var(--ppx-primary-rgb), .12);
            color: var(--ppx-primary-dark);
            font-weight: 700;
            font-size: .7rem;
        }

        /* ══════════════════════════════════════════════════════
                                                                                   KPI CARDS
                                                                                   ══════════════════════════════════════════════════════ */
        .produksi-modern .ppx-kpi-card {
            background: var(--ppx-surface);
            border-radius: var(--ppx-radius-lg);
            box-shadow: var(--ppx-shadow-sm);
            padding: 1.1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .9rem;
            height: 100%;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .produksi-modern .ppx-kpi-card:hover {
            box-shadow: var(--ppx-shadow-md);
            transform: translateY(-1px);
        }

        .produksi-modern .ppx-kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            flex-shrink: 0;
        }

        .produksi-modern .ppx-kpi-body {
            min-width: 0;
        }

        .produksi-modern .ppx-kpi-label {
            font-size: .66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--ppx-muted);
            margin-bottom: .2rem;
            white-space: nowrap;
        }

        .produksi-modern .ppx-kpi-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--ppx-ink);
            line-height: 1.2;
            white-space: nowrap;
        }

        .produksi-modern .ppx-kpi-unit {
            font-size: .7rem;
            font-weight: 600;
            color: var(--ppx-muted);
        }

        .produksi-modern .ppx-kpi-sub {
            font-size: .66rem;
            color: var(--ppx-muted);
            margin-top: .2rem;
        }

        .produksi-modern .ppx-table {
            margin-bottom: 0;
        }

        .produksi-modern .ppx-table> :not(caption)>*>* {
            padding: .85rem .9rem;
            font-size: .82rem;
            border-bottom-color: var(--ppx-border);
        }

        .produksi-modern .ppx-table-compact> :not(caption)>*>* {
            padding: .55rem .6rem !important;
            font-size: .78rem;
        }

        .produksi-modern .ppx-thead th {
            text-transform: uppercase;
            font-size: .68rem;
            letter-spacing: .04em;
            font-weight: 700;
            background: var(--ppx-surface-soft);
            position: sticky;
            top: 0;
            z-index: 5;
            white-space: nowrap;
        }

        .produksi-modern .ppx-th-sub {
            display: block;
            font-size: .58rem;
            font-weight: 600;
            letter-spacing: 0;
            color: var(--ppx-muted);
            text-transform: none;
        }

        .produksi-modern .ppx-table tbody tr:hover {
            background: rgba(var(--ppx-primary-rgb), .035);
        }

        .produksi-modern .ppx-tfoot td {
            background: var(--ppx-surface-soft);
            font-size: .8rem;
        }

        .produksi-modern .ppx-text-warning {
            color: #d9822b !important;
        }

        .produksi-modern .ppx-text-info {
            color: #1ea7c9 !important;
        }

        .produksi-modern .ppx-eq {
            color: var(--ppx-muted);
            font-weight: 400;
            margin: 0 .2rem;
        }

        .produksi-modern .ppx-link {
            color: var(--ppx-primary-dark);
        }

        /* Badge proses — sekarang SOFT (bg pastel + teks warna senada), bukan
                                                                                   solid + teks putih. Warna tetap dipilih deterministik per nama proses
                                                                                   lewat _prosesColor() di PHP (mengembalikan pasangan bg/text), dan dipakai
                                                                                   persis sama baik di baris tabel maupun di modal detail
                                                                                   (lihat 'proses_bg' & 'proses_text' pada JSON). */
        .produksi-modern .ppx-badge-proses {
            border-radius: 8px;
            font-size: .72rem;
            font-weight: 700;
            padding: .38em .75em;
            letter-spacing: .02em;
            display: inline-block;
        }

        .produksi-modern .ppx-group-row {
            background: var(--ppx-surface-soft);
            transition: background .15s ease;
        }

        .produksi-modern .ppx-group-row:hover {
            background: rgba(var(--ppx-primary-rgb), .07);
        }

        .produksi-modern .ppx-group-toggle-icon {
            transition: transform .2s ease;
            font-size: 1.1rem;
        }

        .produksi-modern .ppx-group-row.is-open .ppx-group-toggle-icon {
            transform: rotate(90deg);
            color: var(--ppx-primary);
        }

        .produksi-modern .ppx-expand-row {
            background: var(--ppx-surface);
        }

        .produksi-modern .ppx-expand-row td {
            border-bottom: 1px dashed var(--ppx-border);
        }

        .produksi-modern .ppx-btn-detail {
            border-radius: var(--ppx-radius-sm);
            border: 1px solid rgba(var(--ppx-primary-rgb), .25);
            color: var(--ppx-primary-dark);
            background: rgba(var(--ppx-primary-rgb), .07);
            font-size: .74rem;
            font-weight: 600;
            padding: .35rem .55rem;
        }

        .produksi-modern .ppx-btn-detail:hover {
            background: var(--ppx-primary);
            color: #fff;
        }

        .produksi-modern .inline-edit-cell {
            cursor: pointer;
            display: inline-block;
            min-width: 40px;
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
            min-width: 70px;
            max-width: 90px;
        }

        .produksi-modern .ppx-scroll-btn {
            position: absolute;
            top: 4.2%;
            bottom: 4.2%;
            width: 36px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
            color: white;
            background: rgba(var(--ppx-primary-rgb), 0.45);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(8px);
            cursor: pointer;
            transition: opacity .25s ease, background-color .25s ease;
            box-shadow: inset 0 0 0 1px rgba(var(--ppx-primary-rgb), .08);
        }

        .produksi-modern .ppx-scroll-btn:hover {
            background: rgba(var(--ppx-primary-rgb), 0.7);
        }

        .produksi-modern .ppx-scroll-btn.left {
            left: 0;
            border-right: 1px solid rgba(0, 0, 0, .05);
        }

        .produksi-modern .ppx-scroll-btn.right {
            right: 0;
            border-left: 1px solid rgba(0, 0, 0, .05);
        }

        .produksi-modern .ppx-scroll-btn i {
            font-size: 1.6rem;
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

        @media (max-width: 991.98px) {
            .produksi-modern .ppx-header form {
                width: 100%;
            }

            .produksi-modern .ppx-header form .position-relative {
                width: 100% !important;
            }
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y produksi-modern">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-4 ppx-alert" role="alert">
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

        {{-- ── Page Header ──────────────────────────────────────── --}}
        <div class="d-flex flex-column mb-4 gap-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 ppx-header pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h4 class="fw-bold mb-0">Report Production <span class="badge badge-primary"
                                style="font-size:12px">{{ $product }}</span></h4>
                        <p class="text-muted mb-0 small">Rekap proses produksi</p>
                    </div>
                </div>

                {{-- search --}}
                <div class="d-flex gap-4">
                    <form action="{{ route('proses-produksi.rangkuman', $job_id) }}" method="GET"
                        class="d-flex align-items-center gap-2">
                        <div class="position-relative" style="width:300px; flex-shrink:0;">
                            <div id="jobSearchWrapper" class="ppx-chip-input">
                                <span class="ppx-chip-input-icon"><i class="bx bx-search"></i></span>
                                <div id="selectedJobsContainer" class="ppx-chip-list">
                                    <span id="selectedJobsPlaceholder" class="ppx-chip-placeholder">Cari & Pilih
                                        Job...</span>
                                </div>
                                <input type="text" id="searchJob" class="ppx-chip-native-input" placeholder=""
                                    autocomplete="off" value="">
                            </div>

                            <input type="hidden" id="searchJobsHidden" name="search_jobs"
                                value="{{ request()->query('search_jobs') }}">

                            <div id="jobSuggestions" class="list-group ppx-suggestions position-absolute w-100 d-none">
                            </div>
                        </div>

                        @if (request()->query('search_jobs'))
                            <a href="{{ route('proses-produksi.rangkuman', $job_id) }}"
                                class="btn ppx-btn-reset btn-icon-only" title="Hapus pencarian">
                                <i class="bx bx-x"></i>
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary ppx-btn-apply">
                            <i class="bx bx-search-alt me-1"></i>Cari
                        </button>
                    </form>
                    <a href="{{ route('proses-produksi.indexdata') }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm px-3 py-1.5"
                        style="border-radius: var(--ppx-radius-sm); font-size: 0.82rem; font-weight: 600;">
                        <i class="bx bx-left-arrow-alt fs-5"></i> Kembali
                    </a>

                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
       KPI CARDS
       ══════════════════════════════════════════════════════ --}}
        @php
            $kpiTotalJam = collect($rangkuman)->sum('jam');
            $kpiTotalAktivitas = $detailProses->count();
            $kpiTotalJtDrik = $total['jtdrik'] ?? 0;
            $kpiTotalJtPcs = $total['jtpcs'] ?? 0;
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="ppx-kpi-card">
                    <div class="ppx-kpi-icon" style="background:#4F46E5">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <div class="ppx-kpi-body">
                        <div class="ppx-kpi-label">Total Jam</div>
                        <div class="ppx-kpi-value">
                            {{ number_format($kpiTotalJam, 2, ',', '.') }} <span class="ppx-kpi-unit">jam</span>
                        </div>
                        <div class="ppx-kpi-sub">Seluruh Job yang ditampilkan</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="ppx-kpi-card">
                    <div class="ppx-kpi-icon" style="background:#059669">
                        <i class="bx bx-list-check"></i>
                    </div>
                    <div class="ppx-kpi-body">
                        <div class="ppx-kpi-label">Total Aktivitas</div>
                        <div class="ppx-kpi-value">{{ number_format($kpiTotalAktivitas, 0, ',', '.') }}</div>
                        <div class="ppx-kpi-sub">Baris data proses produksi</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="ppx-kpi-card">
                    <div class="ppx-kpi-icon" style="background:#D97706">
                        <i class="bx bx-trending-up"></i>
                    </div>
                    <div class="ppx-kpi-body">
                        <div class="ppx-kpi-label">Produktivitas</div>
                        <div class="ppx-kpi-value">&mdash;</div>
                        <div class="ppx-kpi-sub">Coming Soon</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="ppx-kpi-card">
                    <div class="ppx-kpi-icon" style="background:#0891B2">
                        <i class="bx bx-package"></i>
                    </div>
                    <div class="ppx-kpi-body">
                        <div class="ppx-kpi-label">Total JT</div>
                        <div class="ppx-kpi-value">
                            {{ number_format($kpiTotalJtDrik, 0, ',', '.') }}<span
                                class="ppx-eq">=</span>{{ number_format($kpiTotalJtPcs, 0, ',', '.') }}
                        </div>
                        <div class="ppx-kpi-sub">Drik = Pcs</div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════
       RANGKUMAN + DETAIL (satu tabel, expand/collapse per proses)
       ══════════════════════════════════════════════════════ --}}
        {{-- Job Active Indicator --}}
        <div class="mb-2 d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted small fw-semibold">Job Active:</span>
            @foreach ($jobsToQuery as $activeJob)
                <span class="ppx-chip-filter ppx-chip-job">
                    <i class="bx bx-briefcase me-1"></i> {{ $activeJob }}
                </span>
            @endforeach
            <span class="ms-auto ppx-badge-count">
                <i class="bx bx-list-ul me-1"></i> {{ $detailProses->count() }} Aktivitas
            </span>
        </div>

        @php
            // Kelompokkan detail proses berdasarkan nama proses (bukan job), supaya bisa
            // ditempel sebagai baris expand di bawah masing-masing baris rangkuman.
            $detailByProses = $detailProses->groupBy(function ($item) {
                return strtolower(trim($item->proses ?? ''));
            });

            // Palet warna SOFT / pastel (bg lembut + teks senada gelap) — dipilih
            // deterministik berdasarkan nama proses (hash), jadi proses yang sama
            // SELALU dapat warna yang sama, baik di baris tabel rangkuman maupun
            // di modal detail (lihat _prosesColor() di bawah). Tiap proses beda warna,
            // tapi tidak lagi mencolok karena background pastel + teks gelap (bukan
            // background solid + teks putih).
            // PENTING: sebelumnya warna dipilih lewat hash (crc32 % jumlah_warna), yang
            // BISA membuat dua proses berbeda kebagian warna yang sama (collision) kalau
            // jumlah proses lebih banyak dari jumlah warna di palet. Sekarang warna
            // di-assign lewat urutan proses yang TETAP (sama seperti $detailOrder /
            // $masterProses di ProsesProduksiController@report), jadi setiap proses
            // dijamin dapat satu slot warna sendiri — tidak ada dua proses yang sama
            // warnanya selama jumlah warna >= jumlah proses di daftar ini.
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

            // 17 warna soft, tiap warna sengaja dibuat berbeda hue supaya gampang
            // dibedakan mata walau backgroundnya pastel. Urutannya PARALEL dengan
            // $processOrder di atas (index 0 = PRINT, index 1 = SORTIR CETAK, dst).
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

            function _prosesColor($namaProses, $softPalette, $processOrder)
            {
                $teks = strtoupper(trim((string) $namaProses)) ?: 'DEFAULT';
                $idx = array_search($teks, $processOrder, true);

                if ($idx === false) {
                    // Proses baru yang belum terdaftar di $processOrder (mis. nama proses
                    // baru yang belum diketahui) — tetap dikasih warna deterministik lewat
                    // hash, dari sisa slot warna supaya sebisa mungkin tidak bentrok
                    // dengan warna proses yang SUDAH dikenal di atas.
                    $fallbackSlots = max(1, count($softPalette) - count($processOrder));
                    $idx = count($processOrder) + (abs(crc32($teks)) % $fallbackSlots);
                }

                return $softPalette[$idx % count($softPalette)];
            }

            // SORTIR dan PACKING adalah bagian dari SORTPACKING juga, jadi baris detail
            // dengan proses SORTPACKING harus ikut muncul di expand SORTIR *dan* PACKING
            // (tampil di dua tempat), bukan cuma di salah satu / tidak muncul sama sekali.
            function _itemsUntukBarisProses($namaProses, $detailByProses)
            {
                $key = strtolower(trim($namaProses));
                $items = $detailByProses->get($key, collect());

                if (in_array($key, ['sortir', 'packing'])) {
                    $itemsSortpacking = $detailByProses->get('sortpacking', collect());
                    $items = $items->concat($itemsSortpacking);
                }

                return $items->sortByDesc('tanggal')->values();
            }

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
        @endphp

        <div class="card mt-2 border-0 ppx-card">
            <div class="card-body p-0">
                <div class="position-relative ppx-table-wrapper">
                    <div class="table-responsive ppx-table-scroll shadow shadow-xl" id="rangkumanScroll">
                        <table id="tblRangkuman" class="table mb-0 align-middle ppx-table ppx-table-compact">

                            <thead class="ppx-thead text-center">
                                <tr>
                                    <th style="width:36px"></th>
                                    <th class="text-start  fw-bold">PROCESS</th>
                                    <th class="text-start  fw-bold" style="min-width:130px">TANGGAL</th>
                                    <th class=" fw-bold">JAM</th>
                                    <th class=" fw-bold">UPSPK</th>
                                    <th>INPUT</th>
                                    <th class=" fw-bold" style="min-width:120px">JT <span class="ppx-th-sub">(drik =
                                            pcs)</span></th>
                                    <th class=" fw-bold" style="min-width:140px">OUTPUT <span class="ppx-th-sub">(drik =
                                            pcs)</span></th>
                                    <th class=" fw-bold" style="min-width:140px">TOTAL <span class="ppx-th-sub">(drik =
                                            pcs)</span></th>
                                    <th class=" fw-bold" style="min-width:140px">SELISIH <span class="ppx-th-sub">(drik =
                                            pcs)</span></th>
                                    <th class=" fw-bold" style="width:90px">ACTIVITY</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($rangkuman as $i => $row)
                                    @php
                                        $itemsForProses = _itemsUntukBarisProses($row['proses'], $detailByProses);
                                        $groupTarget = 'proses-group-' . $i;
                                        $prosesColor = _prosesColor($row['proses'], $softPalette, $processOrder);
                                        $firstItem = $itemsForProses->first();
                                    @endphp

                                    {{-- Baris Ringkasan Proses --}}
                                    <tr class="ppx-group-row" data-target="#{{ $groupTarget }}"
                                        style="cursor:pointer;">
                                        <td class="text-center">
                                            <i class="bx bx-chevron-right ppx-group-toggle-icon text-muted"></i>
                                        </td>
                                        <td class="text-start">
                                            <span class="ppx-badge-proses"
                                                style="background:{{ $prosesColor['bg'] }}; color:{{ $prosesColor['text'] }};">
                                                {{ _prosesLabel($row['proses']) }}
                                            </span>
                                        </td>
                                        <td class="text-start small">
                                            @if ($firstItem && $firstItem->tanggal)
                                                {{ \Carbon\Carbon::parse($firstItem->tanggal)->format('d M y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">{{ $row['jam'] ?: '0' }}</td>
                                        <td class="text-center fw-semibold">
                                            {{ $firstItem && $firstItem->upspk ? number_format($firstItem->upspk, 0, ',', '.') : '0' }}
                                        </td>
                                        <td class="text-center fw-semibold">
                                            {{ $row['input'] ? number_format($row['input'], 0, ',', '.') : '0' }}</td>
                                        <td class="text-center fw-semibold text-danger">
                                            {{ $row['jt_drik'] ? number_format($row['jt_drik'], 0, ',', '.') : '0' }}
                                            <span class="ppx-eq">=</span>
                                            {{ $row['jt_pcs'] ? number_format($row['jt_pcs'], 0, ',', '.') : '0' }}
                                        </td>
                                        <td class="text-center fw-semibold">
                                            {{ $row['output_drik'] ? number_format($row['output_drik'], 0, ',', '.') : '0' }}
                                            <span class="ppx-eq">=</span>
                                            {{ $row['output_pcs'] ? number_format($row['output_pcs'], 0, ',', '.') : '0' }}
                                        </td>
                                        <td class="text-center fw-semibold">
                                            {{ $row['total_pengerjaan_drik'] ? number_format($row['total_pengerjaan_drik'], 0, ',', '.') : '0' }}
                                            <span class="ppx-eq">=</span>
                                            {{ $row['total_pengerjaan_pcs'] ? number_format($row['total_pengerjaan_pcs'], 0, ',', '.') : '0' }}
                                        </td>
                                        <td class="text-center fw-bold ppx-text-warning">
                                            <span
                                                class="">{{ number_format($row['selisih_drik'], 0, ',', '.') }}</span>
                                            <span class="ppx-eq">=</span>
                                            <span
                                                class="">{{ number_format($row['selisih_pcs'], 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="ppx-badge-count-sm">{{ $itemsForProses->count() }}</span>
                                        </td>
                                    </tr>

                                    {{-- Baris Detail (expand) per proses --}}
                                    @forelse ($itemsForProses as $data)
                                        @php
                                            $detailProsesColor = _prosesColor(
                                                $data->proses ?? '-',
                                                $softPalette,
                                                $processOrder,
                                            );
                                        @endphp
                                        <tr id="{{ $groupTarget }}" class="ppx-expand-row d-none">
                                            <td></td>
                                            <td class="text-start">
                                                {{ $data->job }}
                                            </td>
                                            <td class="small text-nowrap">
                                                @if ($data->tanggal)
                                                    {{ \Carbon\Carbon::parse($data->tanggal)->format('d M y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center text-muted">{{ $data->totaljam }}</td>
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
                                                <span class="ppx-eq">=</span>
                                                <span class="inline-edit-cell" data-id="{{ $data->id }}"
                                                    data-field="jtpcs" data-value="{{ $data->jtpcs ?? 0 }}"
                                                    data-editable="{{ in_array(strtolower($data->proses ?? ''), ['lem', 'sortpacking']) ? '1' : '0' }}">
                                                    {{ $data->jtpcs ? number_format($data->jtpcs, 0, ',', '.') : '0' }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold">
                                                <span data-derived-field="outputdrik" data-id="{{ $data->id }}">
                                                    {{ $data->outputdrik ? number_format($data->outputdrik, 0, ',', '.') : '0' }}
                                                </span>
                                                <span class="ppx-eq">=</span>
                                                <span data-derived-field="outputpcs" data-id="{{ $data->id }}">
                                                    {{ $data->outputpcs ? number_format($data->outputpcs, 0, ',', '.') : '0' }}
                                                </span>
                                            </td>
                                            <td class="text-center text-muted">-</td>
                                            <td class="text-center text-muted">-</td>
                                            <td class="text-center d-flex gap-2">
                                                <button type="button"
                                                    class="btn btn-sm ppx-btn-detail d-inline-flex align-items-center gap-1 px-2 py-1"
                                                    title="Lihat semua detail" style="font-size:.75rem"
                                                    onclick="showDetail({{ json_encode([
                                                        'id' => $data->id,
                                                        'tanggal' => $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') : '',
                                                        'job' => $data->job ?? '-',
                                                        'proses' => $data->proses ?? '-',
                                                        'proses_bg' => $detailProsesColor['bg'],
                                                        'proses_text' => $detailProsesColor['text'],
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
                                                    <i class="bx bx-show" style="font-size:.75rem"></i>
                                                </button>
                                                <button
                                                    class="btn btn-sm ppx-btn-detail d-inline-flex align-items-center gap-1 px-2 py-1"
                                                    title="Lihat semua detail" style="font-size:.75rem"
                                                    onclick="showActivityLog({{ $data->id }}, '{{ addslashes($data->job ?? '-') }}', '{{ addslashes($data->product ?? '-') }}')">
                                                    <i class="bx bx-history" style="font-size:.75rem"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="{{ $groupTarget }}" class="ppx-expand-row d-none">
                                            <td></td>
                                            <td colspan="10" class="text-center text-muted py-3">
                                                <i class="bx bx-data me-1"></i> Belum ada data detail untuk proses ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                @endforeach

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>


        </div>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function formatTanggalOnly(val) {
            if (!val || val === '-') return '-';
            const s = String(val).trim().split('T')[0].split(' ')[0];
            const parts = s.split('-');
            if (parts.length === 3) {
                const months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
                const year = parts[0].slice(-2);
                const monthIdx = parseInt(parts[1], 10) - 1;
                const day = parts[2].padStart(2, '0');
                if (monthIdx >= 0 && monthIdx < 12) {
                    return `${day} ${months[monthIdx]} ${year}`;
                }
            }
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

        let modalBs;
        const modalBody = document.getElementById('modalBody');

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

        // ── Expand / collapse baris proses ─────────────────────────
        function initGroupToggle() {
            document.querySelectorAll('.ppx-group-row').forEach(function(row) {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.inline-edit-cell, .inline-edit-input, a, button')) return;

                    const targetId = row.dataset.target.replace('#', '');
                    const detailRows = document.querySelectorAll('#' + CSS.escape(targetId));
                    const isOpen = row.classList.toggle('is-open');

                    detailRows.forEach(function(r) {
                        r.classList.toggle('d-none', !isOpen);
                    });
                });
            });
        }

        // ── Scroll tabel rangkuman ──────────────────────────────────
        function updateScrollButtons(wrapper) {
            const parent = wrapper.closest('.ppx-table-wrapper');
            if (!parent) return;
            const btnLeft = parent.querySelector('.show-scroll-button.left');
            const btnRight = parent.querySelector('.show-scroll-button.right');
            if (!btnLeft || !btnRight) return;
            btnLeft.classList.toggle('d-none', wrapper.scrollLeft <= 0);
            btnRight.classList.toggle('d-none', wrapper.scrollLeft >= wrapper.scrollWidth - wrapper.clientWidth - 5);
        }

        function scrollShowTable(button) {
            const wrapper = document.querySelector(button.dataset.target);
            if (wrapper) {
                wrapper.scrollBy({
                    left: Number(button.dataset.delta || 0),
                    behavior: 'smooth'
                });
            }
        }

        function initScrollButtons() {
            document.querySelectorAll('.ppx-table-scroll').forEach(function(wrapper) {
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

                    $(`.inline-edit-cell[data-id="${id}"]`).each(function() {
                        const $span = $(this);
                        const fieldName = $span.data('field');
                        if (fieldName && values[fieldName] !== undefined) {
                            $span.data('value', values[fieldName]);
                            const isSpanNumeric = numericFields.includes(fieldName);
                            const isSpanTime = ['set', 'run', 'finish'].includes(fieldName);
                            const isSpanDate = fieldName === 'tanggal';
                            $span.text(isSpanNumeric ? parseFloat(values[fieldName]).toLocaleString(
                                'id-ID', {
                                    maximumFractionDigits: 0
                                }) : (isSpanTime ? formatTimeOnly(values[fieldName]) : (
                                isSpanDate ? formatTanggalOnly(values[fieldName]) : values[
                                    fieldName])));
                        }
                    });

                    $(`[data-derived-field][data-id="${id}"]`).each(function() {
                        const $span = $(this);
                        const derivedField = $span.data('derived-field');
                        if (derivedField && values[derivedField] !== undefined) {
                            $span.text(parseFloat(values[derivedField]).toLocaleString('id-ID', {
                                maximumFractionDigits: 0
                            }));
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

                    // Refresh isi tbody & tfoot tabel rangkuman supaya total tetap sinkron,
                    // tanpa menutup baris expand yang sedang terbuka.
                    const openTargets = Array.from(document.querySelectorAll('.ppx-group-row.is-open')).map(r =>
                        r.dataset.target);

                    $.get(window.location.href, function(html) {
                        const newDoc = new DOMParser().parseFromString(html, 'text/html');
                        const newTbody = newDoc.querySelector('#tblRangkuman tbody');
                        const newTfoot = newDoc.querySelector('#tblRangkuman tfoot');
                        if (newTbody) $('#tblRangkuman tbody').replaceWith($(newTbody));
                        if (newTfoot) $('#tblRangkuman tfoot').replaceWith($(newTfoot));

                        initGroupToggle();
                        openTargets.forEach(function(target) {
                            const targetId = target.replace('#', '');
                            const groupRow = document.querySelector(
                                `.ppx-group-row[data-target="${target}"]`);
                            const detailRows = document.querySelectorAll('#' + CSS.escape(
                                targetId));
                            if (groupRow) groupRow.classList.add('is-open');
                            detailRows.forEach(r => r.classList.remove('d-none'));
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

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('modalDetail');
            modalBs = new bootstrap.Modal(modalEl);
            initGroupToggle();
            initScrollButtons();

            $(document).on('dblclick', '.inline-edit-cell', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const cell = $(this);
                const field = cell.data('field');
                const isEditable = cell.data('editable') === 1 || cell.data('editable') === '1';

                if (field === 'jtpcs' && !isEditable) return;

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
                    cancelInlineEdit($(this).closest('.inline-edit-cell'), $(this).closest(
                        '.inline-edit-cell').data('value'));
                }
            });

            $(document).on('focusout', '.inline-edit-input', function() {
                saveInlineEdit($(this));
            });
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

            let html = '<div class="row justify-content-center g-4 p-2 produksi-modern">';

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
                            isEditableVal = d.proses.toLowerCase() === 'lem' ? '0' : '1';
                        } else if (r.field === 'jtpcs') {
                            isEditableVal = ['lem', 'sortpacking'].includes(d.proses.toLowerCase()) ? '1' :
                                '0';
                        }

                        const isNumeric = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift', 'qty'].includes(r
                            .field);
                        const isTime = ['set', 'run', 'finish'].includes(r.field);
                        const isDate = r.field === 'tanggal';
                        const formattedVal = isNumeric ?
                            parseFloat(r.val || 0).toLocaleString('id-ID', {
                                maximumFractionDigits: 0
                            }) :
                            (isTime ? formatDateTime(r.val) : (isDate ? formatTanggalOnly(r.val) : r.val));

                        valHtml =
                            `<span class="inline-edit-cell" data-id="${d.id}" data-field="${r.field}" data-value="${r.val}" data-editable="${isEditableVal}">${formattedVal}</span>`;
                    } else if (r.badge) {
                        // Badge Proses: warna SOFT sama persis dengan yang dipakai di baris tabel
                        // (dikirim dari server lewat d.proses_bg & d.proses_text, bukan warna statis).
                        const badgeBg = d.proses_bg || '#EAEAFE';
                        const badgeText = d.proses_text || '#5850EC';
                        valHtml =
                            `<span class="badge fw-bold text-uppercase" style="background:${badgeBg}; color:${badgeText}; border-radius:8px; padding:.38em .75em; font-size:.72rem; letter-spacing:.02em;">${r.val}</span>`;
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

                        valHtml = `<span ${classAttr} ${dataFieldAttr}>${formattedVal}</span>`;
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

        // ── Job search filter (form pencarian) ──────────────────────
        function renderSelectedJobs() {
            const selectedJobs = $('#searchJobsHidden').val().split(',').map(function(item) {
                return item.trim();
            }).filter(Boolean);

            const $container = $('#selectedJobsContainer');
            const $placeholder = $('#selectedJobsPlaceholder');

            $container.find('.job-badge').remove();

            if (selectedJobs.length) {
                $placeholder.hide();
            } else {
                $placeholder.show();
            }

            selectedJobs.forEach(function(job) {
                $('<span class="badge job-badge rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1">')
                    .append($('<span class="fw-semibold">').text(job))
                    .append($('<i class="bx bx-x cursor-pointer" style="font-size: 12px;"></i>').on('click',
                        function() {
                            removeSelectedJob(job);
                        }))
                    .appendTo($container);
            });

            // Auto-scroll box ke kanan supaya chip terbaru & input selalu terlihat
            const box = document.getElementById('jobSearchWrapper');
            if (box) {
                setTimeout(() => {
                    box.scrollLeft = box.scrollWidth;
                }, 30);
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

            if (!selectedJobs.includes(job)) selectedJobs.push(job);

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
                            <a href="#" class="list-group-item list-group-item-action border-0 px-3 py-2 d-flex align-items-center gap-2 pilih-job" data-job="${item.job}">
                                <i class="bx bx-briefcase text-primary"></i>
                                <span class="fw-semibold">${item.job}</span>
                            </a>`;
                        });
                        $suggestions.html(html).removeClass('d-none');
                    } else {
                        $suggestions.empty().addClass('d-none');
                    }
                }
            });
        });

        // Form submit: kalau ada teks yang belum "di-Enter" jadi chip, masukkan juga
        $('#jobSearchWrapper').closest('form').on('submit', function() {
            const typedVal = $('#searchJob').val() ? $('#searchJob').val().trim() : '';
            if (typedVal) {
                addSelectedJob(typedVal);
                $('#searchJob').val('');
            }
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
                        const isNumericField = ['input', 'jtdrik', 'jtpcs', 'outputpcs', 'outputdrik',
                            'total_pengerjaan_drik', 'total_pengerjaan_pcs', 'totaljam', 'upspk'
                        ].includes(l.field);
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
                            } catch (e) {
                                return v;
                            }
                        };
                        const oldHtml = isNull(l.old) ?
                            (isNumericField ?
                                `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#dc3545">0</span>` :
                                `<span style="color:#c4c8d0;font-style:italic;font-size:.74rem">null</span>`) :
                            `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#dc3545;white-space:nowrap;">${isDateField ? formatDateVal(l.old) : l.old}</span>`;
                        const newHtml = isNull(l.new) ?
                            (isNumericField ?
                                `<span style="font-size:.9rem;font-weight:600;font-family:monospace;color:#146c43">0</span>` :
                                `<span style="color:#c4c8d0;font-style:italic;font-size:.74rem">null</span>`) :
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
