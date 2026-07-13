@extends('layouts.main')

@section('main-content')

<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ── Page Header ──────────────────────────────────────── --}}
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('proses-produksi.index') }}"
         class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
        <i class="bx bx-arrow-back"></i> Kembali
      </a>
      <div>
        <h4 class="fw-bold mb-0">Edit Proses Produksi</h4>
        <p class="text-muted mb-0 small">ID #{{ $prosesProduksi->id }} &mdash; {{ $prosesProduksi->job ?? '-' }}</p>
      </div>
    </div>
    {{-- Job badge --}}
    <div class="d-flex align-items-center gap-2 bg-warning bg-opacity-10 border border-warning rounded-3 px-4 py-2">
      <i class="bx bx-edit-alt text-warning fs-5"></i>
      <span class="text-muted small fw-semibold">EDIT</span>
      <span class="fw-bold text-warning fs-6">{{ $prosesProduksi->job ?? '-' }}</span>
    </div>
  </div>

  {{-- Flash --}}
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible d-flex align-items-start gap-2 mb-4">
      <i class="bx bx-error-circle fs-5 mt-1"></i>
      <div>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <form action="{{ route('proses-produksi.update', $prosesProduksi->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">

      {{-- ══════════════════════════════════════════════════
           KOLOM KIRI
           ══════════════════════════════════════════════════ --}}
      <div class="col-12 col-lg-8">

        {{-- ── CARD 1: Identitas Job ──────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
            <span class="bg-primary rounded-2 d-flex align-items-center justify-content-center"
                  style="width:28px;height:28px">
              <i class="bx bx-briefcase text-white" style="font-size:.85rem"></i>
            </span>
            <h6 class="fw-bold mb-0">Identitas Job</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Job <span class="text-danger">*</span></label>
                <input type="text" name="job"
                       value="{{ old('job', $prosesProduksi->job) }}"
                       class="form-control @error('job') is-invalid @enderror"
                       placeholder="No. / Nama Job">
                @error('job')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Tanggal</label>
                <input type="date" name="tanggal"
                       value="{{ old('tanggal', $prosesProduksi->tanggal) }}"
                       class="form-control @error('tanggal') is-invalid @enderror">
                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Produk</label>
                <input type="text" name="product"
                       value="{{ old('product', $prosesProduksi->product) }}"
                       class="form-control @error('product') is-invalid @enderror"
                       placeholder="Nama produk">
                @error('product')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Docket / Design No.</label>
                <input type="text" name="designno"
                       value="{{ old('designno', $prosesProduksi->designno) }}"
                       class="form-control @error('designno') is-invalid @enderror"
                       placeholder="No. Docket">
                @error('designno')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">PO</label>
                <input type="text" name="po"
                       value="{{ old('po', $prosesProduksi->po) }}"
                       class="form-control @error('po') is-invalid @enderror"
                       placeholder="No. PO">
                @error('po')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">QTY</label>
                <input type="text" name="qty"
                       value="{{ old('qty', $prosesProduksi->qty) }}"
                       class="form-control @error('qty') is-invalid @enderror"
                       placeholder="Jumlah order">
                @error('qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Palet</label>
                <input type="text" name="palet"
                       value="{{ old('palet', $prosesProduksi->palet) }}"
                       class="form-control @error('palet') is-invalid @enderror"
                       placeholder="No. Palet">
                @error('palet')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

            </div>
          </div>
        </div>

        {{-- ── CARD 2: Proses & Mesin ─────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
            <span class="bg-success rounded-2 d-flex align-items-center justify-content-center"
                  style="width:28px;height:28px">
              <i class="bx bx-cog text-white" style="font-size:.85rem"></i>
            </span>
            <h6 class="fw-bold mb-0">Proses & Mesin</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Proses <span class="text-danger">*</span></label>
                <select name="proses"
                        class="form-select @error('proses') is-invalid @enderror">
                  <option value="">-- Pilih Proses --</option>
                  @foreach(['PRINT','SORTIR CETAK','WATERBASE','HOCK','HOTPRINT','LAMINASI','LAMINATING','EMBOSS','DIECUT','CUTTING','PRETEL','LEM','SORTIR','PACKING'] as $p)
                    <option value="{{ $p }}"
                      {{ old('proses', $prosesProduksi->proses) == $p ? 'selected' : '' }}>
                      {{ $p }}
                    </option>
                  @endforeach
                </select>
                @error('proses')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Mesin</label>
                <input type="text" name="mesin"
                       value="{{ old('mesin', $prosesProduksi->mesin) }}"
                       class="form-control @error('mesin') is-invalid @enderror"
                       placeholder="Nama / No. Mesin">
                @error('mesin')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Shift</label>
                <select name="shift"
                        class="form-select @error('shift') is-invalid @enderror">
                  <option value="">-- Pilih --</option>
                  @foreach(['1','2','3'] as $s)
                    <option value="{{ $s }}"
                      {{ old('shift', $prosesProduksi->shift) == $s ? 'selected' : '' }}>
                      Shift {{ $s }}
                    </option>
                  @endforeach
                </select>
                @error('shift')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Vendor Mat</label>
                <input type="text" name="vendormat"
                       value="{{ old('vendormat', $prosesProduksi->vendormat) }}"
                       class="form-control @error('vendormat') is-invalid @enderror"
                       placeholder="Vendor material">
                @error('vendormat')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Type</label>
                <input type="text" name="type"
                       value="{{ old('type', $prosesProduksi->type) }}"
                       class="form-control @error('type') is-invalid @enderror"
                       placeholder="Type">
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

            </div>
          </div>
        </div>

        {{-- ── CARD 3: Operator & Tim ──────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
            <span class="bg-info rounded-2 d-flex align-items-center justify-content-center"
                  style="width:28px;height:28px">
              <i class="bx bx-group text-white" style="font-size:.85rem"></i>
            </span>
            <h6 class="fw-bold mb-0">Operator & Tim</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Operator</label>
                <input type="text" name="operator"
                       value="{{ old('operator', $prosesProduksi->operator) }}"
                       class="form-control @error('operator') is-invalid @enderror"
                       placeholder="Nama operator">
                @error('operator')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-3">
                <label class="form-label fw-semibold small">Jumlah Tim</label>
                <input type="text" name="jumlahtim"
                       value="{{ old('jumlahtim', $prosesProduksi->jumlahtim) }}"
                       class="form-control @error('jumlahtim') is-invalid @enderror"
                       placeholder="0">
                @error('jumlahtim')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-3">
                <label class="form-label fw-semibold small">Toleransi</label>
                <input type="text" name="toleransi"
                       value="{{ old('toleransi', $prosesProduksi->toleransi) }}"
                       class="form-control @error('toleransi') is-invalid @enderror"
                       placeholder="0">
                @error('toleransi')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Pengawas</label>
                <input type="text" name="pengawas"
                       value="{{ old('pengawas', $prosesProduksi->pengawas) }}"
                       class="form-control @error('pengawas') is-invalid @enderror"
                       placeholder="Nama pengawas">
                @error('pengawas')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Shift Pengawas</label>
                <select name="shiftpengawas"
                        class="form-select @error('shiftpengawas') is-invalid @enderror">
                  <option value="">-- Pilih --</option>
                  @foreach(['1','2','3'] as $s)
                    <option value="{{ $s }}"
                      {{ old('shiftpengawas', $prosesProduksi->shiftpengawas) == $s ? 'selected' : '' }}>
                      Shift {{ $s }}
                    </option>
                  @endforeach
                </select>
                @error('shiftpengawas')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

            </div>
          </div>
        </div>

        {{-- ── CARD 4: Waktu Pengerjaan ────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
            <span class="bg-warning rounded-2 d-flex align-items-center justify-content-center"
                  style="width:28px;height:28px">
              <i class="bx bx-time text-white" style="font-size:.85rem"></i>
            </span>
            <h6 class="fw-bold mb-0">Waktu Pengerjaan</h6>
            <span class="ms-auto badge bg-label-warning" style="font-size:.7rem">
              Total jam dihitung otomatis dari Set/Run → Finish
            </span>
          </div>
          <div class="card-body">
            <div class="row g-3">

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Waktu Set</label>
                <input type="time" name="set"
                       value="{{ old('set', $prosesProduksi->set) }}"
                       class="form-control @error('set') is-invalid @enderror">
                <div class="form-text">Waktu mulai setup mesin</div>
                @error('set')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Waktu Run</label>
                <input type="time" name="run"
                       value="{{ old('run', $prosesProduksi->run) }}"
                       class="form-control @error('run') is-invalid @enderror">
                <div class="form-text">Waktu mulai jalan</div>
                @error('run')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Waktu Finish</label>
                <input type="time" name="finish"
                       value="{{ old('finish', $prosesProduksi->finish) }}"
                       class="form-control @error('finish') is-invalid @enderror">
                <div class="form-text">Waktu selesai</div>
                @error('finish')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Break</label>
                <select name="break"
                        class="form-select @error('break') is-invalid @enderror">
                  <option value="FALSE" {{ old('break', $prosesProduksi->break) == 'FALSE' ? 'selected' : '' }}>
                    Tidak (tidak dikurangi 1 jam)
                  </option>
                  <option value="TRUE" {{ old('break', $prosesProduksi->break) == 'TRUE' || old('break', $prosesProduksi->break) == '1' ? 'selected' : '' }}>
                    Ya (dikurangi 1 jam)
                  </option>
                </select>
                @error('break')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

            </div>
          </div>
        </div>

        {{-- ── CARD 5: Data Produksi ───────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
            <span class="bg-danger rounded-2 d-flex align-items-center justify-content-center"
                  style="width:28px;height:28px">
              <i class="bx bx-bar-chart-alt-2 text-white" style="font-size:.85rem"></i>
            </span>
            <h6 class="fw-bold mb-0">Data Produksi</h6>
          </div>
          <div class="card-body">
            <div class="row g-3">

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Input</label>
                <input type="text" name="input"
                       value="{{ old('input', $prosesProduksi->input) }}"
                       class="form-control @error('input') is-invalid @enderror"
                       placeholder="0">
                @error('input')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">UPS PK</label>
                <input type="text" name="upspk"
                       value="{{ old('upspk', $prosesProduksi->upspk) }}"
                       class="form-control @error('upspk') is-invalid @enderror"
                       placeholder="0">
                <div class="form-text">Output PCS = Output Drik × UPS PK</div>
                @error('upspk')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Target</label>
                <input type="text" name="target"
                       value="{{ old('target', $prosesProduksi->target) }}"
                       class="form-control @error('target') is-invalid @enderror"
                       placeholder="0">
                @error('target')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              {{-- JT --}}
              <div class="col-12"><hr class="my-1"><p class="small text-muted fw-semibold mb-2">Jam Tayang (JT)</p></div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">JT Drik</label>
                <input type="text" name="jtdrik"
                       value="{{ old('jtdrik', $prosesProduksi->jtdrik) }}"
                       class="form-control @error('jtdrik') is-invalid @enderror"
                       placeholder="0">
                @error('jtdrik')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">JT PCS</label>
                <input type="text" name="jtpcs"
                       value="{{ old('jtpcs', $prosesProduksi->jtpcs) }}"
                       class="form-control @error('jtpcs') is-invalid @enderror"
                       placeholder="0">
                @error('jtpcs')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              {{-- Output --}}
              <div class="col-12"><hr class="my-1"><p class="small text-muted fw-semibold mb-2">Output</p></div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small">Output Drik</label>
                <input type="text" name="outputdrik"
                       value="{{ old('outputdrik', $prosesProduksi->outputdrik) }}"
                       class="form-control @error('outputdrik') is-invalid @enderror"
                       placeholder="0">
                @error('outputdrik')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-6">
                <label class="form-label fw-semibold small d-flex align-items-center gap-1">
                  Output PCS
                  <span class="badge bg-label-secondary fw-normal" style="font-size:.65rem">Otomatis</span>
                </label>
                <input type="text" readonly
                       id="previewOutputPcs"
                       class="form-control bg-light text-muted"
                       placeholder="Dihitung otomatis">
                <div class="form-text">= Output Drik × UPS PK</div>
              </div>

              {{-- Karantina & Not OK --}}
              <div class="col-12"><hr class="my-1"><p class="small text-muted fw-semibold mb-2">Reject & Karantina</p></div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Karantina</label>
                <input type="text" name="karantina"
                       value="{{ old('karantina', $prosesProduksi->karantina) }}"
                       class="form-control @error('karantina') is-invalid @enderror"
                       placeholder="0">
                @error('karantina')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">Not OK</label>
                <input type="text" name="notok"
                       value="{{ old('notok', $prosesProduksi->notok) }}"
                       class="form-control @error('notok') is-invalid @enderror"
                       placeholder="0">
                @error('notok')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 col-sm-4">
                <label class="form-label fw-semibold small">OK</label>
                <input type="text" name="ok"
                       value="{{ old('ok', $prosesProduksi->ok) }}"
                       class="form-control @error('ok') is-invalid @enderror"
                       placeholder="0">
                @error('ok')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

            </div>
          </div>
        </div>

      </div>{{-- /col kiri --}}


      {{-- ══════════════════════════════════════════════════
           KOLOM KANAN (sticky sidebar)
           ══════════════════════════════════════════════════ --}}
      <div class="col-12 col-lg-4">

        {{-- ── CARD: Keterangan Kualitas ───────────────────── --}}
        <div class="card border-0 shadow-sm mb-4" style="position:sticky; top:80px">
          <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center gap-2">
            <span class="bg-secondary rounded-2 d-flex align-items-center justify-content-center"
                  style="width:28px;height:28px">
              <i class="bx bx-list-check text-white" style="font-size:.85rem"></i>
            </span>
            <h6 class="fw-bold mb-0">Keterangan Kualitas</h6>
          </div>
          <div class="card-body">

            {{-- Checkbox grid --}}
            <p class="small text-uppercase fw-bold text-muted mb-2" style="font-size:.68rem; letter-spacing:.05em">
              Jenis Reject
            </p>

            @php
              $rejectFields = [
                'warna'          => 'Warna',
                'banjir'         => 'Banjir',
                'beset'          => 'Beset',
                'powder'         => 'Powder',
                'wb'             => 'WB',
                'uvkasar'        => 'UV Kasar',
                'uvmbleset'      => 'UV MBL Eset',
                'tidakuv'        => 'Tidak UV',
                'hotprint'       => 'Hotprint',
                'laminating'     => 'Laminating',
                'laminasikurang' => 'Laminasi Kurang',
                'laminasi'       => 'Laminasi',
                'tidakpresisi'   => 'Tidak Presisi',
                'pecah'          => 'Pecah',
                'emboss'         => 'Emboss',
                'porforasi'      => 'Porforasi',
                'sobek'          => 'Sobek',
                'lengket'        => 'Lengket',
                'll'             => 'LL',
              ];
            @endphp

            <div class="row g-2 mb-3">
              @foreach($rejectFields as $fieldName => $label)
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="{{ $fieldName }}"
                           id="chk_{{ $fieldName }}"
                           value="1"
                           {{ old($fieldName, $prosesProduksi->$fieldName) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="chk_{{ $fieldName }}">
                      {{ $label }}
                    </label>
                  </div>
                </div>
              @endforeach
            </div>

            <hr>

            {{-- Catatan Operator --}}
            <div class="mb-3">
              <label class="form-label fw-semibold small">Catatan Operator</label>
              <textarea name="noteoperator" rows="3"
                        class="form-control form-control-sm @error('noteoperator') is-invalid @enderror"
                        placeholder="Keterangan tambahan dari operator...">{{ old('noteoperator', $prosesProduksi->noteoperator) }}</textarea>
              @error('noteoperator')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold small">Keterangan Umum</label>
              <textarea name="ket" rows="2"
                        class="form-control form-control-sm @error('ket') is-invalid @enderror"
                        placeholder="Keterangan umum...">{{ old('ket', $prosesProduksi->ket) }}</textarea>
              @error('ket')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr>

            {{-- Preview computed fields --}}
            <p class="small text-uppercase fw-bold text-muted mb-2" style="font-size:.68rem; letter-spacing:.05em">
              Preview Kalkulasi
            </p>
            <div class="list-group list-group-flush mb-3">
              <div class="list-group-item px-0 py-2 d-flex justify-content-between border-0 border-bottom small">
                <span class="text-muted">Output PCS</span>
                <span class="fw-semibold text-primary" id="displayOutputPcs">—</span>
              </div>
              <div class="list-group-item px-0 py-2 d-flex justify-content-between border-0 border-bottom small">
                <span class="text-muted">Total Pengerjaan Drik</span>
                <span class="fw-semibold" id="displayTotalDrik">—</span>
              </div>
              <div class="list-group-item px-0 py-2 d-flex justify-content-between border-0 small">
                <span class="text-muted">Total Pengerjaan PCS</span>
                <span class="fw-semibold" id="displayTotalPcs">—</span>
              </div>
            </div>

            {{-- Submit --}}
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-warning fw-semibold">
                <i class="bx bx-save me-1"></i> Simpan Perubahan
              </button>
              <a href="{{ route('proses-produksi.index') }}"
                 class="btn btn-outline-secondary">
                Batal
              </a>
            </div>

          </div>
        </div>

      </div>{{-- /col kanan --}}

    </div>{{-- /row --}}
  </form>

</div>{{-- /container --}}

<script>
// ── Live preview kalkulasi ─────────────────────────────
const inOutputDrik = document.querySelector('[name="outputdrik"]');
const inUps        = document.querySelector('[name="upspk"]');
const inJtDrik     = document.querySelector('[name="jtdrik"]');
const inJtPcs      = document.querySelector('[name="jtpcs"]');

const previewOutputPcs  = document.getElementById('previewOutputPcs');
const displayOutputPcs  = document.getElementById('displayOutputPcs');
const displayTotalDrik  = document.getElementById('displayTotalDrik');
const displayTotalPcs   = document.getElementById('displayTotalPcs');

function fmt(n) {
  return isNaN(n) ? '—' : n.toLocaleString('id-ID');
}

function clean(v) {
  return parseFloat((v || '0').toString().replace(/\./g, '').replace(',', '.')) || 0;
}

function recalc() {
  const outDrik = clean(inOutputDrik.value);
  const ups     = clean(inUps.value);
  const jtDrik  = clean(inJtDrik.value);
  const jtPcs   = clean(inJtPcs.value);

  const outputPcs  = outDrik * ups;
  const totalDrik  = jtDrik + outDrik;
  const totalPcs   = jtPcs + outputPcs;

  previewOutputPcs.value    = outputPcs > 0 ? fmt(outputPcs) : '';
  displayOutputPcs.textContent  = fmt(outputPcs);
  displayTotalDrik.textContent  = fmt(totalDrik);
  displayTotalPcs.textContent   = fmt(totalPcs);
}

[inOutputDrik, inUps, inJtDrik, inJtPcs].forEach(el => el.addEventListener('input', recalc));

// Jalankan saat load supaya nilai existing langsung ter-preview
document.addEventListener('DOMContentLoaded', recalc);
</script>

@endsection