@extends('layouts.main')

@section('main-content')

<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Page Header ──────────────────────────────────────── --}}
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2 bg-primary bg-opacity-10 border border-primary rounded-3 px-4 py-2">
      <i class="bx bx-briefcase text-primary fs-5"></i>
      <span class="text-muted small fw-semibold">JOB</span>
      <span class="fw-bold text-primary fs-5">{{ $job_id }}</span>
    </div>
    
    <div>
        <h4 class="fw-bold mb-0">Rangkuman Job</h4>
        <p class="text-muted mb-0 small">Rekap seluruh proses produksi</p>
    </div>
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('proses-produksi.index') }}"
         class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
        <i class="bx bx-arrow-back"></i> Kembali
      </a>
    </div>
  </div>


  {{-- ══════════════════════════════════════════════════════
       SECTION 1 — RANGKUMAN TABLE
       ══════════════════════════════════════════════════════ --}}
 {{-- Card Wadah Tabel Rangkuman --}}
<div class="card mt-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bx bx-table me-2"></i>
            Rangkuman Produksi
        </h5>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-hover table-striped align-middle mb-0">

            <thead class="table-primary text-center">
                <tr>
                    <th style="min-width:180px">PROCESS</th>
                    <th>JAM</th>
                    <th>JT DRIK</th>
                    <th>JT PCS</th>
                    <th>OUTPUT/DRIK</th>
                    <th>OUTPUT/PCS</th>
                    <th>TOTAL<br>DRIK</th>
                    <th>SELISIH<br>DRIK</th>
                    <th>TOTAL<br>PCS</th>
                    <th>SELISIH<br>PCS</th>
                </tr>
            </thead>

            <tbody>

            @foreach($rangkuman as $row)

            <tr>

                <td class="fw-semibold">
                    {{ strtoupper($row['proses']) }}
                </td>

                <td class="text-end">
                    {{ $row['jam'] ?: '0' }}
                </td>

                <td class="text-end">
                    {{ $row['jt_drik'] ? number_format($row['jt_drik'],0,',','.') : '0' }}
                </td>

                <td class="text-end">
                    {{ $row['jt_pcs'] ? number_format($row['jt_pcs'],0,',','.') : '0' }}
                </td>

                <td class="text-end">
                    {{ $row['output_drik'] ? number_format($row['output_drik'],0,',','.') : '0' }}
                </td>

                <td class="text-end">
                    {{ $row['output_pcs'] ? number_format($row['output_pcs'],0,',','.') : '0' }}
                </td>

                <td class="text-end fw-semibold bg-label-secondary">
                    {{ $row['total_pengerjaan_drik'] ? number_format($row['total_pengerjaan_drik'],0,',','.') : '0' }}
                </td>

                <td class="text-end fw-bold text-warning">
                    {{ $row['selisih_drik'] ? number_format($row['selisih_drik'],0,',','.') : '0' }}
                </td>

                <td class="text-end fw-semibold bg-label-secondary">
                    {{ $row['total_pengerjaan_pcs'] ? number_format($row['total_pengerjaan_pcs'],0,',','.') : '0' }}
                </td>

                <td class="text-end fw-bold text-info">
                    {{ $row['selisih_pcs'] ? number_format($row['selisih_pcs'],0,',','.') : '0' }}
                </td>

            </tr>

            @endforeach

            </tbody>

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
                    <span class="fw-semibold text-dark">{{ $job_id }}</span>
                </small>
            </div>

            <span class="badge bg-label-primary fs-6">
                {{ $detailProses->count() }} Aktivitas
            </span>

        </div>
    </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0 position-relative">
           <!-- Tombol kiri -->
        <button
            type="button"
            id="btnScrollLeft"
            class="btn btn-icon btn-primary rounded-circle shadow position-absolute top-50 start-0 translate-middle-y z-3 d-none"
            style="margin-left:15px; opacity:.9;"
            onclick="scrollTabel(-350)"
            title="Geser ke Kiri">
            <i class="bx bx-chevron-left fs-2"></i>
        </button>

        <!-- Tombol kanan -->
        <button
            type="button"
            id="btnScrollRight"
            class="btn btn-icon btn-primary rounded-circle shadow position-absolute top-50 end-0 translate-middle-y z-3"
            style="margin-right:15px; opacity:.9;"
            onclick="scrollTabel(350)"
            title="Geser ke Kanan">
            <i class="bx bx-chevron-right fs-2"></i>
        </button>
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
            @forelse ($detailProses as $data)

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
            {{-- <tfoot class="table-light fw-bold">
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
        </tfoot> --}}
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
  </div>


</div>


{{-- ── Offcanvas Detail ──────────────────────────────────── --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasDetail"
     style="width:420px" aria-labelledby="offcanvasDetailLabel">
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
    .table > :not(caption) > * > * {
    padding: 1rem 0.30rem;
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
const wrapper = document.getElementById("tabelContainer");
const btnLeft = document.getElementById("btnScrollLeft");
const btnRight = document.getElementById("btnScrollRight");
let offcanvasBs;
const offcanvasBody = document.getElementById('offcanvasBody');

document.addEventListener('DOMContentLoaded', function () {
  offcanvasBs = new bootstrap.Offcanvas(document.getElementById('offcanvasDetail'));
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
  const sections = [
    {
      heading: 'Informasi Umum',
      rows: [
        { icon: 'bx-hash',            label: 'ID',        val: d.id },
        { icon: 'bx-calendar',        label: 'Tanggal',   val: d.tanggal },
        { icon: 'bx-briefcase',       label: 'Job',       val: d.job },
        { icon: 'bx-cog',             label: 'Proses',    val: d.proses,  badge: true },
        { icon: 'bx-box',             label: 'Produk',    val: d.product },
        { icon: 'bx-barcode',         label: 'Docket',    val: d.designno },
        { icon: 'bx-user',            label: 'Operator',  val: d.operator },
      ]
    },
    {
      heading: 'Jadwal & Plan',
      rows: [
        { icon: 'bx-time',            label: 'Jam Kalkulasi', val: d.jam_kalkulasi },
        { icon: 'bx-transfer-alt',    label: 'Shift',         val: d.shift },
        { icon: 'bx-list-ol',         label: 'PO',            val: d.po },
        { icon: 'bx-arrow-to-bottom', label: 'Input',         val: d.input },
      ]
    },
    {
      heading: 'Output & Hasil',
      rows: [
        { icon: 'bx-package',      label: 'JT PCS',                val: d.jtpcs },
        { icon: 'bx-package',      label: 'JT Drik',               val: d.jtdrik },
        { icon: 'bx-stats',        label: 'UPS PK',                val: d.upspk },
        { icon: 'bx-check-square', label: 'Output PCS',            val: d.outputpcs,            highlight: true },
        { icon: 'bx-check-square', label: 'Output Drik',           val: d.outputdrik,           highlight: true },
        { icon: 'bx-calculator',   label: 'Total Pengerjaan Drik', val: d.total_pengerjaan_drik },
        { icon: 'bx-calculator',   label: 'Total Pengerjaan PCS',  val: d.total_pengerjaan_pcs },
      ]
    }
  ];

  let html = '';
  sections.forEach(sec => {
    html += `<p class="text-uppercase fw-bold small text-muted mb-2 mt-4">${sec.heading}</p>
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