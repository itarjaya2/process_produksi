@extends('layouts.main')

@section('main-content')



<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Page Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="fw-bold mb-0">Proses Produksi</h4>
      <p class="text-muted mb-0 small">Kelola dan pantau seluruh data proses produksi</p>
    </div>
    <a href="{{ route('proses-produksi.create') }}"
       class="btn btn-primary d-flex align-items-center gap-1">
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
{{-- Filter Card --}}
  <div class="card mb-4 border-0 shadow-sm">
 
    {{-- Card header: judul filter + badge aktif --}}
    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-2">
        <i class="bx bx-filter-alt text-primary fs-5"></i>
        <span class="fw-semibold">Filter Data</span>
      </div>
      {{-- Badge hanya muncul kalau ada filter aktif
      @if(request()->hasAny(['id','job','operator','proses','start_date','end_date']))
        <a href="{{ route('proses-produksi.index') }}"
           class="badge bg-label-danger text-decoration-none d-flex align-items-center gap-1"
           title="Hapus semua filter">
          <i class="bx bx-x"></i> Reset Filter
        </a>
      @endif --}}
    </div>
 
    <div class="card-body pt-3 pb-3">
      <form action="{{ route('proses-produksi.index') }}" method="GET">
 
        {{-- ── Grup 1: Pencarian teks ─────────────────────── --}}
        <div class="mb-3">
          <p class="text-uppercase fw-bold mb-2"
             style="font-size:.65rem; letter-spacing:.07em; color:#a0a3b1">
            <i class="bx bx-search me-1"></i>Pencarian
          </p>
          <div class="row g-2">
            <div class="col-12 col-sm-4 col-lg-2">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-transparent border-end-0 text-muted">
                  <i class="bx bx-hash"></i>
                </span>
                <input type="number" name="id" value="{{ request('id') }}"
                       placeholder="ID"
                       class="form-control form-control-sm border-start-0 ps-0"
                       style="min-width:0">
              </div>
            </div>
            <div class="col-12 col-sm-4 col-lg-2">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-transparent border-end-0 text-muted">
                  <i class="bx bx-briefcase"></i>
                </span>
                <input type="text" name="job" value="{{ request('job') }}"
                       placeholder="No JOB"
                       class="form-control form-control-sm border-start-0 ps-0">
              </div>
            </div>
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
            <div class="col-12 col-sm-6 col-lg-2">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-transparent border-end-0 text-muted">
                  <i class="bx bx-cog"></i>
                </span>
                <select name="proses"
                        class="form-select form-select-sm border-start-0"
                        style="padding-left:.4rem">
                  <option value="">Semua Proses</option>
                  @foreach($daftarProses as $prosesName)
                    <option value="{{ $prosesName }}"
                      {{ request('proses') == $prosesName ? 'selected' : '' }}>
                      {{ $prosesName }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            {{-- tanggal --}}
           <div class="col-12 col-lg-4">
                <div class="input-group input-group-sm">

                    <span class="input-group-text bg-transparent">
                        <i class="bx bx-calendar"></i>
                    </span>

                    <input
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="form-control">

                    <span class="input-group-text bg-light">
                        s/d
                    </span>

                    <input
                        type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="form-control">

                </div>
            </div>
          </div>
        </div>
 
        {{-- ── Grup 2: Rentang tanggal ────────────────────── --}}
 
        {{-- ── Tombol aksi ────────────────────────────────── --}}
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary px-4">
            <i class="bx bx-filter-alt me-1"></i>Terapkan Filter
          </button>
          <a href="{{ route('proses-produksi.index') }}"
             class="btn btn-sm btn-outline-secondary px-3">
            Reset
          </a>
        </div>
 
      </form>
    </div>
  </div>

  {{-- Main Table Card --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0 position-relative">
           <!-- Tombol kiri -->
        <div class="position-relative table-wrapper">
            <!-- Tombol kiri -->
            <button
                id="btnScrollLeft"
                class="scroll-overlay left"
                onclick="scrollTabel(-350)">
                <i class="bx bx-chevron-left"></i>
            </button>

            <!-- Tombol kanan -->
            <button
                id="btnScrollRight"
                class="scroll-overlay right"
                onclick="scrollTabel(350)">
                <i class="bx bx-chevron-right"></i>
            </button>

        </div>
      <div class="table-responsive table-scroll" id="tabelContainer">
        <table class="table table-sm table-hover mb-0 align-middle" id="tblProduksi">
            <thead class="table-light text-uppercase small">
                <tr>
                <th style="width:10px">Tanggal</th>
                <th>Job</th>
                <th>Docket</th>
                <th>Proses</th>
                <th>Produk</th>
                <th>Operator</th>
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
            @forelse ($prosesProduksi as $data)

              {{-- Compact row --}}
              <tr>
                <td class="small text-nowrap">
                  {{ $data->tanggal ?? '-' }}
                </td>

                <td>
                  @if($data->job)
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
                        $paletWarna = ['primary', 'success', 'warning', 'info', 'danger', 'dark'];
                        
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
                 <td class="text-center fw-semibold">
                  {{ $data->upspk ?? '0' }}
                </td>
                <td class="text-center fw-semibold">
                  {{ $data->input ? number_format($data->input, 0, ',', '.') : '0' }}
                </td>
                <td class="small">{{ $data->jtdrik ? number_format($data->jtdrik, 0, ',', '.') : '0' }}</td>
                <td class="small">{{ $data->jtpcs ? number_format($data->jtpcs, 0, ',', '.') : '0' }}</td>

                <td class="text-center fw-semibold">
                  {{ $data->outputpcs ? number_format($data->outputpcs, 0, ',', '.') : '0' }}
                </td>
                <td class="text-center fw-semibold">
                  {{ $data->outputdrik ? number_format($data->outputdrik, 0, ',', '.') : '0' }}
                </td>
                {{-- Toggle button — opens offcanvas --}}
                <td class="text-center pe-4 d-flex gap-1">
                 <button
                      class="btn btn-sm btn-primary d-flex align-items-center gap-1 px-2 py-1"
                      title="Lihat semua detail"
                      style="font-size:.75rem"
                    onclick="showDetail({{ json_encode([
                      'id'                   => $data->id,
                      'tanggal'              => $data->tanggal ?? '-',
                      'job'                  => $data->job ?? '-',
                      'proses'               => $data->proses ?? '-',
                      'product'              => $data->product ?? '-',
                      'designno'             => $data->designno ?? '-',
                      'operator'             => $data->operator ?? '-',
                      'totaljam'        => $data->totaljam ?? '0',
                      'shift'                => $data->shift ?? '0',
                      'po'                   => $data->po ?? '0',
                      'input'                => $data->input ?? '0',
                      'jtpcs'                => $data->jtpcs ?? '0',
                      'jtdrik'               => $data->jtdrik ?? '0',
                      'upspk'                => $data->upspk ?? '0',
                      'outputpcs'            => $data->outputpcs ?? '0',
                      'outputdrik'           => $data->outputdrik ?? '0',
                      'total_pengerjaan_drik'=> $data->total_pengerjaan_drik ?? '0',
                      'total_pengerjaan_pcs' => $data->total_pengerjaan_pcs ?? '0',
                    ]) }})">
                     <i class="bx bx-show" style="font-size:.75rem"></i>          
                  </button>
                   <a href="{{ route('proses-produksi.edit', $data->id) }}"
                    class="btn btn-sm btn-warning d-flex align-items-center justify-content-center px-2 py-1"
                    title="Edit">
                        <i class="bx bx-edit-alt"></i>
                    </a>
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

                    <td class="text-start">
                        {{ number_format($total['jtdrik'], 0, ',', '.') }}
                    </td>

                    <td class="text-start">
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
    </div>

    {{-- Pagination --}}
    @if($prosesProduksi->hasPages())
    <div class="card-footer bg-transparent border-top py-3 px-4">
        {{-- Paksa menggunakan view Bootstrap 5 --}}
        {{ $prosesProduksi->links('pagination::bootstrap-5') }}
    </div>
    @endif
  </div>

</div>{{-- /container --}}


{{-- ============================================================
     OFFCANVAS — Full Detail Panel
     ============================================================ --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetail"
     style="width: 420px;" aria-labelledby="offcanvasDetailLabel">

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


{{-- ============================================================
     JS — Detail renderer
     ============================================================ --}}

<style>
    .table > :not(caption) > * > * {
    padding: 1rem 0.70rem;
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
.table-wrapper{
    position:relative;
}

.scroll-overlay{

    position:absolute;

    top:0;
    bottom:0;

    width:48px;

    border:none;

    opacity:0;

    transition:.25s ease;

    display:flex;

    align-items:center;

    justify-content:center;

    z-index:20;

    color:#696cff;

    backdrop-filter:blur(6px);

    background:rgba(255,255,255,.25);

    cursor:pointer;
}

.table-wrapper:hover .scroll-overlay{

    opacity:1;
}

.scroll-overlay:hover{

    background:rgba(105,108,255,.15);

    color:#696cff;
}

.scroll-overlay.left{

    left:0;

    border-right:1px solid rgba(0,0,0,.05);
}

.scroll-overlay.right{

    right:0;

    border-left:1px solid rgba(0,0,0,.05);
}

.scroll-overlay i{

    font-size:34px;
}

</style>


<script>
const container = document.getElementById('tabelContainer');
const wrapper = document.getElementById("tabelContainer");
const btnLeft = document.getElementById("btnScrollLeft");
const btnRight = document.getElementById("btnScrollRight");
let offcanvasBs;
const offcanvasBody = document.getElementById('offcanvasBody');

function updateScrollButton(){

    const left = document.getElementById('btnScrollLeft');
    const right = document.getElementById('btnScrollRight');

    left.style.display =
        container.scrollLeft <= 5 ? 'none' : 'flex';

    right.style.display =
        container.scrollLeft + container.clientWidth >= container.scrollWidth - 5
            ? 'none'
            : 'flex';
}

container.addEventListener('scroll', updateScrollButton);

window.addEventListener('load', updateScrollButton);

document.addEventListener('DOMContentLoaded', function () {
  const offcanvasEl = document.getElementById('offcanvasDetail');
  offcanvasBs = new bootstrap.Offcanvas(offcanvasEl);
});
    function updateScrollButton() {

    btnLeft.classList.toggle(
        "d-none",
        wrapper.scrollLeft <= 0
    );

    btnRight.classList.toggle(
        "d-none",
        wrapper.scrollLeft >= wrapper.scrollWidth - wrapper.clientWidth - 5
    );
}

wrapper.addEventListener("scroll", updateScrollButton);
window.addEventListener("load", updateScrollButton);

function scrollTabel(x) {
    wrapper.scrollBy({
        left: x,
        behavior: "smooth"
    });
}

function showDetail(d) {

  // ── Label map : [label, value, icon, highlight] ──────────────
  const sections = [
    {
      heading: 'Informasi Umum',
      rows: [
        { icon: 'bx-hash',          label: 'ID',       val: d.id },
        { icon: 'bx-calendar',      label: 'Tanggal',  val: d.tanggal },
        { icon: 'bx-briefcase',     label: 'Job',      val: d.job },
        { icon: 'bx-cog',           label: 'Proses',   val: d.proses, badge: true },
        { icon: 'bx-box',           label: 'Produk',   val: d.product },
        { icon: 'bx-barcode',       label: 'Docket',   val: d.designno },
        { icon: 'bx-user',          label: 'Operator', val: d.operator },
      ]
    },
    {
      heading: 'Jadwal & Plan',
      rows: [
        { icon: 'bx-time',          label: 'Total Jam', val: d.totaljam },
        { icon: 'bx-transfer-alt',  label: 'Shift',         val: d.shift },
        { icon: 'bx-list-ol',       label: 'PO',            val: d.po },
        { icon: 'bx-arrow-to-bottom', label: 'Input',       val: d.input },
      ]
    },
    {
      heading: 'Output & Hasil',
      rows: [
        { icon: 'bx-package',       label: 'JT PCS',           val: d.jtpcs },
        { icon: 'bx-package',       label: 'JT Drik',          val: d.jtdrik },
        { icon: 'bx-stats',         label: 'UPS PK',           val: d.upspk },
        { icon: 'bx-check-square',  label: 'Output PCS',       val: d.outputpcs,   highlight: true },
        { icon: 'bx-check-square',  label: 'Output Drik',      val: d.outputdrik,  highlight: true },
        { icon: 'bx-calculator',    label: 'Total Pengerjaan Drik', val: d.total_pengerjaan_drik },
        { icon: 'bx-calculator',    label: 'Total Pengerjaan PCS',  val: d.total_pengerjaan_pcs },
      ]
    }
  ];

  let html = '';

  sections.forEach(sec => {
    html += `
      <p class="text-uppercase fw-bold small text-muted mb-2 mt-4">${sec.heading}</p>
      <div class="list-group list-group-flush mb-1">`;

    sec.rows.forEach(r => {
      const val = r.badge
        ? `<span class="badge bg-label-primary fw-normal">${r.val}</span>`
        : r.highlight
          ? `<span class="fw-bold text-primary">${r.val}</span>`
          : `<span class="text-body-emphasis">${r.val}</span>`;

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