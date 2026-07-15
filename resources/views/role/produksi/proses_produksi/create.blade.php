<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Produksi</title>
    <!-- CDN Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .row-time-error {
            border: 2px solid #dc2626;
            /* red-600 */
            background-color: #fef2f2;
            /* red-50 */
        }
    </style>

</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-xl font-bold text-center text-blue-800 mb-6 border-b pb-2">PROSES PRODUKSI</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="bg-yellow-100 text-yellow-700 p-3 rounded mb-4">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Blade: form rows dengan Add Row + semua script --}}
        <div>
            <form method="POST" action="{{ route('proses-produksi.store') }}">
                @csrf
                <div id="rows-container">
                    <!-- Row template (clone-able). visible pertama kali -->
                    <div class="row-item border p-4 mb-4 rounded" data-row-index="1">
                        <div class="flex justify-between items-center mb-2">
                            <div class="collapse-preview text-sm text-gray-700 font-semibold"></div>
                            <button type="button"
                                class="toggle-collapse bg-gray-300 hover:bg-gray-400 text-xs px-2 py-1 rounded">Collapse</button>
                        </div>
                        <div class="row-content">
                            <!-- DETAIL JOB -->
                            <div class="mb-8">
                                <h2 class="font-semibold text-blue-700 mb-3 border-b border-blue-300 pb-1">DETAIL JOB
                                </h2>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">Proses</label>
                                        <select name="proses[]"
                                            class="proses border border-gray-300 rounded-md px-2 py-1 w-2/3">
                                            <option value="" selected>--Pilih Proses--</option>
                                            <option value="PRINT">PRINT</option>
                                            <option value="WATERBASE">WATERBASE</option>
                                            <option value="HOCK">HOCK</option>
                                            <option value="HOTPRINT">HOTPRINT</option>
                                            <option value="LAMINASI">LAMINASI</option>
                                            <option value="LAMINATING">LAMINATING</option>
                                            <option value="EMBOSS">EMBOSS</option>
                                            <option value="DIECUT">DIECUT</option>
                                            <option value="CUTTING">CUTTING</option>
                                            <option value="PRETEL">PRETEL</option>
                                            <option value="LEM">LEM</option>
                                            <option value="LEM SETENGAH JADI">LEM SETENGAH JADI</option>
                                            <option value="SORTPACKING">SORTPACKING</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">Pengawas</label>
                                        <select name="pengawas[]"
                                            class="border border-gray-300 rounded-md px-2 py-1 w-2/3" required>
                                            <option value="" selected>--Pilih Pengawas--</option>
                                            <option value="">TIDAK ADA</option>
                                            {{-- @foreach ($karyawanstaff as $staff)
                      @if ($staff->departement == 'Produksi' && $staff->jabatan == 'PENGAWAS' && $staff->status == 'AKTIF')
                        <option>{{ $staff->nama }}</option>
                      @endif
                    @endforeach --}}
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">Shift Pengawas</label>
                                        <select name="shiftpengawas[]"
                                            class="border border-gray-300 rounded-md px-2 py-1 w-2/3" required>
                                            <option value="" selected>--Pilih Shift--</option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">JOB</label>
                                        <input type="text" list="job" name="job[]" placeholder="250001..."
                                            class="job-input border border-gray-300 rounded-md px-2 py-1 w-2/3">
                                        <datalist id="job">
                                            @foreach ($jobs as $job)
                                                <option value="{{ $job->job }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">Product</label>
                                        <input type="text" name="product[]"
                                            class="product-input border border-gray-300 rounded-md px-2 py-1 w-2/3 bg-gray-100"
                                            readonly>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">Docket</label>
                                        <input type="text" name="designno[]"
                                            class="designno-input border border-gray-300 rounded-md px-2 py-1 w-2/3 bg-gray-100"
                                            readonly>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">PO</label>
                                        <input type="text" name="po[]"
                                            class="po-input border border-gray-300 rounded-md px-2 py-1 w-2/3 bg-gray-100"
                                            readonly>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3 text-gray-700">ORDER (pcs)</label>
                                        <input type="number" name="qty[]"
                                            class="qty-input border border-gray-300 rounded-md px-2 py-1 w-2/3 font-semibold bg-gray-100">
                                    </div>
                                </div>
                            </div>

                            <!-- DETAIL PENGERJAAN -->
                            <div class="mb-8">
                                <h2 class="font-semibold text-blue-700 mb-3 border-b border-blue-300 pb-1">DETAIL
                                    PENGERJAAN</h2>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <label class="w-1/3">Tanggal</label>
                                        <input type="date" name="tanggal[]"
                                            class="productionDate border px-2 py-1 w-2/3 rounded-md">
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Mesin</label>
                                        <select name="mesin[]" class="border px-2 py-1 w-2/3 rounded-md">
                                            <option value="" selected>--Pilih Mesin--</option>
                                            <option>CX</option>
                                            <option>SM-03</option>
                                            <option>SAKURAI</option>
                                            <option>KOMORI</option>
                                            <option>LAMINATING</option>
                                            <option>LAMINASI</option>
                                            <option>WATERBASE</option>
                                            <option>HOCK</option>
                                            <option>HMC-01</option>
                                            <option>HMC-02</option>
                                            <option>HMC-03</option>
                                            <option>HMC-04</option>
                                            <option>LABEL</option>
                                            <option>MANUAL SONGSONG</option>
                                            <option>MANUAL MONDO</option>
                                            <option>LEM AB</option>
                                            <option>LEM G2</option>
                                            <option>LEM G3</option>
                                            <option>LEM G4</option>
                                            <option>CUTTING</option>
                                            <option>MESIN PRETEL</option>
                                            <option>MANUAL PRETEL</option>
                                            <option>POLAR 115</option>
                                            <option>POLAR 137</option>
                                            <option>DIECUT MANUAL</option>
                                            <option>KAWAL BORONGAN</option>
                                            <option>SORTIR TURUN PALET</option>
                                        </select>
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Operator</label>
                                        <div class="operator-wrapper relative w-2/3">
                                            <div class="operator-badges flex flex-wrap gap-1 mb-1"></div>
                                            <input type="text"
                                                class="operator-input border px-2 py-1 w-full rounded-md"
                                                placeholder="Ketik atau pilih operator..." autocomplete="off">
                                            <div
                                                class="operator-list border rounded-md mt-1 bg-white shadow hidden max-h-40 overflow-y-auto absolute w-full z-10">
                                            </div>
                                            <input type="hidden" name="operator[]">
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Shift</label>
                                        <select required name="shift[]" class="border px-2 py-1 w-2/3 rounded-md">
                                            <option value="" selected>--Pilih Shift--</option>
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                        </select>
                                    </div>

                                    <div class="flex items-center tim-section hidden">
                                        <label class="w-1/3">Jumlah Tim</label>
                                        <input name="jumlahtim[]" type="number"
                                            class="border px-2 py-1 w-2/3 rounded-md">
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Start Setting</label>
                                        <input name="set[]" type="datetime-local"
                                            class="set-input border px-2 py-1 w-2/3 rounded-md">
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3">Start Running</label>
                                        <input name="run[]" type="datetime-local"
                                            class="run-input border px-2 py-1 w-2/3 rounded-md">
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3">Finish Running</label>
                                        <input name="finish[]" type="datetime-local"
                                            class="finish-input border px-2 py-1 w-2/3 rounded-md">
                                    </div>

                                    <div class="flex items-center form-check">
                                        <label class="w-1/3">Break</label>
                                        <select name="break[]" class="border px-2 py-1 w-2/3 rounded-md">
                                            <option value="" selected>--Pilih Break--</option>
                                            <option value="TRUE">YES</option>
                                            <option value="FALSE">NO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- DETAIL OUTPUT -->
                            <div class="mb-8">
                                <h2 class="font-semibold text-blue-700 mb-3 border-b border-blue-300 pb-1">DETAIL
                                    OUTPUT</h2>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <label class="w-1/3">UP SPK</label>
                                        <input class="upspk-input border px-2 py-1 w-2/3 rounded-md" name="upspk[]"
                                            type="number">
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Keterangan</label>
                                        <input type="text" name="ket[]"
                                            class="border px-2 py-1 w-2/3 rounded-md">
                                    </div>

                                    <div class="box-section hidden space-y-2 text-sm">
                                        <div class="flex items-center">
                                            <label class="w-1/3">BOX</label>
                                            <input name="box[]" type="text"
                                                class="box-input border px-2 py-1 w-2/3 rounded-md">
                                        </div>
                                        <div class="flex items-center">
                                            <label class="w-1/3">ISIBOX</label>
                                            <input name="isibox[]" type="text"
                                                class="isibox-input border px-2 py-1 w-2/3 rounded-md">
                                        </div>
                                        <!-- TAMBAHAN BARU -->
                                        <div class="flex items-center">
                                            <label class="w-1/3">TAMBAHAN ISI</label>
                                            <input name="tambahanisi[]" type="text"
                                                class="tambahanisi-input border px-2 py-1 w-2/3 rounded-md"
                                                placeholder="Tambahan pcs">
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <label class="w-1/3">Input (DRIK)<sup class="text-red-600"> *Jika LEM, Input
                                                = PCS</sup></label>
                                        <input name="input[]" type="text"
                                            class="input-drk border px-2 py-1 w-2/3 rounded-md">
                                    </div>

                                    <!-- SORTPACKING -->
                                    <div class="sortpacking-section hidden space-y-2 text-sm">
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="font-semibold">JT</label>
                                            <button type="button"
                                                class="add-jt-btn bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-xs">+
                                                Add JT</button>
                                        </div>

                                        <div class="jt-container space-y-2"></div>

                                        <div class="flex items-center">
                                            <label class="w-1/3">Sortir PCS</label>
                                            <input type="number" name="sortirpcs[]"
                                                class="sortirpcs-input border px-2 py-1 w-2/3 bg-gray-100" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label class="w-1/3">Sortir Drik</label>
                                            <input type="number" name="sortirdrik[]"
                                                class="sortirdrik-input border px-2 py-1 w-2/3 bg-gray-100" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label class="w-1/3">Packing PCS</label>
                                            <input type="number" name="packingpcs[]"
                                                class="packingpcs-input border px-2 py-1 w-2/3 bg-gray-100" readonly>
                                        </div>

                                        <div class="flex items-center">
                                            <label class="w-1/3">Packing Drik</label>
                                            <input type="number" name="packingdrik[]"
                                                class="packingdrik-input border px-2 py-1 w-2/3 bg-gray-100" readonly>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">JT Drik</label>
                                        <input type="number" name="jtdrik[]"
                                            class="jtdrik-input border px-2 py-1 w-2/3 rounded-md">
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">JS PCS</label>
                                        <input type="number" name="jtpcs[]"
                                            class="jtpcs-input border px-2 py-1 w-2/3 bg-gray-100 rounded-md">
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Output Drik</label>
                                        <input type="number" name="outputdrik[]"
                                            class="outputdrik-input border px-2 py-1 w-2/3 font-semibold bg-gray-100 rounded-md"
                                            readonly>
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Output Pcs</label>
                                        <input type="number" name="outputpcs[]"
                                            class="outputpcs-input border px-2 py-1 w-2/3 font-semibold bg-gray-100 rounded-md"
                                            readonly>
                                    </div>

                                    <div class="flex items-center">
                                        <label class="w-1/3">Target Drik/Hour</label>
                                        <input type="number" name="target[]"
                                            class="border px-2 py-1 w-2/3 font-semibold rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end row-item -->
        </div> <!-- end rows-container -->

        <!-- Tombol Add Row -->
        <div class="mt-4">
            <button type="button" id="add-row-btn"
                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">+ Add Row</button>
        </div>

        <!-- Submit -->
        <div class="mt-4">
            <button type="submit" id="submitBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <svg id="loadingSpinner" class="hidden animate-spin h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>

                <span id="btnText">Simpan</span>
            </button>

        </div>
        </form>
    </div>

    {{-- SCRIPT: semua logic ORIGINAL, di-scope per-row, mendukung clone --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Data operator dari Blade (sama seperti di scriptmu)


            // JT types (sama persis)
            const allJtTypes = [
                'Warna', 'Banjir', 'Beset', 'Not Ok', 'Powder', 'WB', 'Uv Kasar', 'Uv Mbleset',
                'Tidak UV', 'Hotprint', 'Laminating', 'Laminasi Kurang', 'Laminasi',
                'Tidak Presisi', 'Pecah', 'Emboss', 'Porforasi', 'Sobek', 'Lengket', 'LL'
            ];

            // Operator lookup placeholder: jika tidak ada data karyawan, kosong saja supaya skrip tidak error.
            const allOptions = [];

            // Helper format / unformat (sama persis)
            function formatNumber(num) {
                if (num === '' || isNaN(num)) return '';
                return new Intl.NumberFormat('id-ID').format(num);
            }

            function unformatNumber(num) {
                return num === undefined || num === null ? '' : num.toString().replace(/\./g, '').replace(/,/g, '');
            }

            // Inisialisasi satu row (semua logic original disini, scoping per-row)
            function initRow(row) {
                // query scoping: ambil elemen dari row
                const produksiDate = row.querySelector('.productionDate');
                const setInput = row.querySelector('.set-input');
                const runInput = row.querySelector('.run-input');
                const finishInput = row.querySelector('.finish-input');

                const proses = row.querySelector('.proses');
                const upspk = row.querySelector('.upspk-input');
                const inputDrik = row.querySelector('.input-drk');
                const jtDrik = row.querySelector('.jtdrik-input');
                const jtPcs = row.querySelector('.jtpcs-input');
                const outputDrik = row.querySelector('.outputdrik-input');
                const outputPcs = row.querySelector('.outputpcs-input');
                const boxSection = row.querySelector('.box-section');
                const boxInput = row.querySelector('.box-input');
                const isiboxInput = row.querySelector('.isibox-input');
                const tambahanIsiInput = row.querySelector('.tambahanisi-input');


                const sortirSection = row.querySelector('.sortpacking-section');
                const timSection = row.querySelector('.tim-section');
                const sortirpcs = row.querySelector('.sortirpcs-input');
                const sortirdrik = row.querySelector('.sortirdrik-input');
                const packingpcs = row.querySelector('.packingpcs-input');
                const packingdrik = row.querySelector('.packingdrik-input');

                const operatorInput = row.querySelector('.operator-input');
                const operatorList = row.querySelector('.operator-list');
                const operatorBadges = row.querySelector('.operator-badges');
                const operatorHidden = row.querySelector('input[type="hidden"][name="operator[]"]');

                const jtContainer = row.querySelector('.jt-container');
                const addJtBtn = row.querySelector('.add-jt-btn');

                const jobInput = row.querySelector('.job-input');
                const productInput = row.querySelector('.product-input');
                const designnoInput = row.querySelector('.designno-input');
                const poInput = row.querySelector('.po-input');
                const qtyInput = row.querySelector('.qty-input');

                // ===== date/time max & dblclick showPicker (sama persis) =====
                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const hh = String(now.getHours()).padStart(2, '0');
                const min = String(now.getMinutes()).padStart(2, '0');
                const todayDate = `${yyyy}-${mm}-${dd}`;
                const maxDatetime = `${yyyy}-${mm}-${dd}T${hh}:${min}`;

                produksiDate?.setAttribute('max', todayDate);
                [setInput, runInput, finishInput].forEach(input => {
                    input?.setAttribute('max', maxDatetime);
                });

                [produksiDate, setInput, runInput, finishInput].forEach(input => {
                    if (!input) return;
                    input.addEventListener('dblclick', function() {
                        if (input.showPicker) input.showPicker();
                        else input.focus();
                    });
                });

                // ===== validate order (sama persis) =====
                function validateOrder() {
                    const proc = (proses?.value || '').toUpperCase();

                    // Kalau SORTPACKING, abaikan SET
                    if (proc === 'SORTPACKING') {
                        if (!runInput || !finishInput) return;
                    } else {
                        if (!setInput || !runInput || !finishInput) return;
                    }

                    const badge = row.querySelector('.time-error-badge');

                    // reset error state dulu
                    row.classList.remove('row-time-error');
                    badge?.classList.add('hidden');

                    if (!setInput.value || !runInput.value || !finishInput.value) return;

                    const setTime = new Date(setInput.value);
                    const runTime = new Date(runInput.value);
                    const finishTime = new Date(finishInput.value);

                    let hasError = false;

                    if (proc === 'SORTPACKING') {
                        if (runTime > finishTime) hasError = true;
                    } else {
                        if (setTime > runTime) hasError = true;
                        if (runTime > finishTime) hasError = true;
                        if (setTime > finishTime) hasError = true;
                    }

                    if (hasError) {
                        // tandai row ini error
                        row.classList.add('row-time-error');
                        badge?.classList.remove('hidden');

                        // auto expand kalau sedang collapse
                        if (row.dataset.collapsed === 'true') {
                            row.querySelector('.toggle-collapse')?.click();
                        }

                        // auto scroll ke row error
                        row.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }

                [setInput, runInput, finishInput].forEach(input => input?.addEventListener('change',
                validateOrder));

                // ===== format angka (sama persis) =====
                // convert number inputs to text with formatting
                const numberInputs = row.querySelectorAll(
                    'input[type="number"], input.input-drk, input.upspk-input, input.jtdrik-input, input.jtpcs-input, input.box-input, input.isibox-input'
                    );
                numberInputs.forEach(input => {
                    // convert initial type to text to control formatting (keep name attribute)
                    if (input) {
                        input.type = 'text';
                        input.addEventListener('input', e => {
                            const el = e.target;
                            const oldValue = el.value;
                            const oldPos = el.selectionStart || oldValue.length;
                            const raw = oldValue.replace(/\D/g, '');
                            if (!raw) {
                                el.value = '';
                                return;
                            }
                            const formatted = new Intl.NumberFormat('id-ID').format(raw);
                            el.value = formatted;
                            const diff = formatted.length - oldValue.length;
                            const newPos = oldPos + diff;
                            requestAnimationFrame(() => {
                                try {
                                    el.setSelectionRange(newPos, newPos);
                                } catch (err) {}
                            });
                        });
                    }
                });

                // ===== operator filtering & badges (sama persis) =====
                let selectedOperators = [];
                let filteredOperators = [];

                function updateOperatorList(filteredOptions, keyword = '') {
                    if (!operatorList) return;
                    operatorList.innerHTML = '';
                    const shown = filteredOptions.filter(opt =>
                        opt.text.toLowerCase().includes(keyword.toLowerCase())
                    );
                    if (shown.length === 0) {
                        operatorList.classList.add('hidden');
                        return;
                    }
                    shown.forEach(opt => {
                        const item = document.createElement('div');
                        item.textContent = opt.text;
                        item.className = 'px-2 py-1 hover:bg-blue-100 cursor-pointer';
                        item.addEventListener('click', () => addOperator(opt.value));
                        operatorList.appendChild(item);
                    });
                    operatorList.classList.remove('hidden');
                }

                function updateBadges() {
                    if (!operatorBadges) return;
                    operatorBadges.innerHTML = '';
                    selectedOperators.forEach(name => {
                        const badge = document.createElement('div');
                        badge.className =
                            'bg-blue-200 text-blue-800 px-2 py-1 rounded flex items-center gap-1';
                        badge.innerHTML =
                            `${name} <span class="cursor-pointer font-bold" data-name="${name}">&times;</span>`;
                        operatorBadges.appendChild(badge);
                    });
                    if (operatorHidden) operatorHidden.value = selectedOperators.join(', ');
                }

                function filterOperators() {
                    const selectedProses = (proses?.value || '').toUpperCase();
                    let targetBagian = null;
                    let targetGender = null;
                    switch (selectedProses) {
                        case 'PRINT':
                            targetBagian = 'PRINTING';
                            break;
                        case 'WATERBASE':
                        case 'LAMINASI':
                        case 'LAMINATING':
                        case 'HOCK':
                            targetBagian = ['COATING', 'LAMINASI', 'LAMINATING'];
                            break;
                        case 'HOTPRINT':
                        case 'EMBOSS':
                        case 'DIECUT':
                            targetBagian = 'DIECUT';
                            break;
                        case 'CUTTING':
                            targetBagian = 'CUTTING';
                            break;
                        case 'PRETEL':
                            targetBagian = 'PRETEL';
                            break;
                        case 'LEM':
                        case 'LEM SETENGAH JADI':
                            targetBagian = 'FINISHING';
                            targetGender = 'LAKI-LAKI';
                            break;
                        case 'SORTPACKING':
                            targetBagian = 'FINISHING';
                            targetGender = 'PEREMPUAN';
                            break;
                        default:
                            targetBagian = null;
                    }

                    filteredOperators = allOptions.filter(opt =>
                        opt.departement === 'PRODUKSI' &&
                        opt.status === 'AKTIF' &&
                        (!targetBagian ||
                            (Array.isArray(targetBagian) ?
                                targetBagian.includes(opt.bagian) :
                                opt.bagian === targetBagian)
                        ) &&
                        (!targetGender || opt.kelamin === targetGender)
                    );

                    selectedOperators = [];
                    updateBadges();
                    if (operatorList) operatorList.classList.add('hidden');
                }

                function addOperator(name) {
                    if (!name) return;
                    const valid = filteredOperators.some(o => o.value === name);
                    if (!valid || selectedOperators.includes(name)) return;
                    selectedOperators.push(name);
                    updateBadges();
                    if (operatorInput) operatorInput.value = '';
                    if (operatorList) operatorList.classList.add('hidden');
                }

                if (operatorBadges) {
                    operatorBadges.addEventListener('click', e => {
                        if (e.target.dataset.name) {
                            selectedOperators = selectedOperators.filter(n => n !== e.target.dataset.name);
                            updateBadges();
                        }
                    });
                }

                operatorInput?.addEventListener('input', e => {
                    const keyword = e.target.value.trim();
                    if (keyword.length === 0) {
                        operatorList.classList.add('hidden');
                        return;
                    }
                    updateOperatorList(filteredOperators, keyword);
                });

                operatorInput?.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addOperator(operatorInput.value.trim());
                    }
                });

                // ===== JT Sortpacking Dinamis (sama persis) =====
                let selectedJt = [];
                addJtBtn?.addEventListener('click', () => {
                    const available = allJtTypes.filter(jt => !selectedJt.includes(jt));
                    if (available.length === 0) return alert('Semua jenis JT sudah ditambahkan.');

                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-center space-x-2 mb-1';

                    const select = document.createElement('select');
                    select.className = 'border px-2 py-1 rounded w-1/2';
                    select.innerHTML = `<option value="">--Pilih JT--</option>` +
                        available.map(jt => `<option value="${jt}">${jt}</option>`).join('');

                    const input = document.createElement('input');
                    input.type = 'number';
                    input.className = 'border px-2 py-1 rounded w-1/2 hidden';

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.textContent = '✕';
                    removeBtn.className = 'bg-red-500 text-white px-2 py-1 rounded text-xs hidden';

                    select.addEventListener('change', () => {
                        const val = select.value;
                        if (!val) return;
                        if (selectedJt.includes(val)) return alert('JT sudah dipilih.');
                        selectedJt.push(val);
                        input.name = val.toLowerCase().replace(/\s+/g, '') + '[]';
                        input.placeholder = `Jumlah ${val}`;
                        input.classList.remove('hidden');
                        removeBtn.classList.remove('hidden');
                        select.disabled = true;
                        // supaya hitung ketika user input JT
                        input.addEventListener('input', () => {
                            calculate(); // hanya hitung ulang, tidak reset operator
                        });
                    });

                    removeBtn.addEventListener('click', () => {
                        const val = select.value;
                        selectedJt = selectedJt.filter(jt => jt !== val);
                        wrapper.remove();
                        proses.dispatchEvent(new Event('change'));
                    });

                    wrapper.append(select, input, removeBtn);
                    jtContainer.append(wrapper);
                });

                // ===== Kalkulasi (sama persis) =====
                function calculate() {
                    const proc = (proses?.value || '').trim().toUpperCase();
                    const up = parseFloat(unformatNumber(upspk?.value)) || 0;
                    const jtVal = parseFloat(unformatNumber(jtDrik?.value)) || 0;
                    const jtpcsVal = parseFloat(unformatNumber(jtPcs?.value)) || 0;
                    let inputVal = 0; // Inisialisasi inputVal

                    if (proc === 'SORTPACKING') {
                        // Logika baru untuk SORTPACKING
                        const boxVal = parseFloat(unformatNumber(boxInput?.value)) || 0;
                        const isiboxVal = parseFloat(unformatNumber(isiboxInput?.value)) || 0;
                        const tambahanVal = parseFloat(unformatNumber(tambahanIsiInput?.value)) || 0;

                        // RUMUS BARU:
                        // (box × isibox) + tambahan isi
                        inputVal = (boxVal * isiboxVal) + tambahanVal;


                        // Tampilkan hasil perhitungan di kolom Input (DRIK)
                        if (inputDrik) inputDrik.value = formatNumber(Math.floor(inputVal));
                        if (inputDrik) inputDrik.readOnly = true; // Jadikan readOnly jika hasil rumus
                    } else {
                        // Logika default, ambil nilai dari inputDrik
                        inputVal = parseFloat(unformatNumber(inputDrik?.value)) || 0;
                        if (inputDrik) inputDrik.readOnly = false; // Boleh diisi manual
                    }
                    [tambahanIsiInput].forEach(el => {
                        if (!el) return;
                        el.addEventListener('input', calculate);
                        el.addEventListener('change', calculate);
                    });

                    // default hide/show
                    if (!sortirSection?.classList) {}
                    if (proc === 'SORTPACKING') {
                        sortirSection?.classList.remove('hidden');
                        timSection?.classList.remove('hidden');
                        boxSection?.classList.remove('hidden'); // Tampilkan box section
                        if (outputDrik && outputDrik.parentElement) outputDrik.parentElement.style.display = 'none';
                        if (outputPcs && outputPcs.parentElement) outputPcs.parentElement.style.display = 'none';

                        let jtSum = 0;
                        // Hitung semua input JT yang aktif (per row)
                        jtContainer.querySelectorAll('input[type="number"], input[type="text"]').forEach(inp => {
                            // some JT inputs were turned to text by formatting — accept both
                            jtSum += parseFloat(unformatNumber(inp.value)) || 0;
                        });

                        const jtdrikVal = up > 0 ? jtSum / up : 0;
                        const sortirpcsVal = inputVal + jtSum;
                        const sortirdrikVal = up > 0 ? sortirpcsVal / up : 0;
                        const packingpcsVal = inputVal;
                        const packingdrikVal = up > 0 ? packingpcsVal / up : 0;

                        if (jtPcs) jtPcs.value = formatNumber(Math.floor(jtSum));
                        if (jtDrik) jtDrik.value = formatNumber(Math.floor(jtdrikVal));
                        if (sortirpcs) sortirpcs.value = formatNumber(Math.floor(sortirpcsVal));
                        if (sortirdrik) sortirdrik.value = formatNumber(Math.floor(sortirdrikVal));
                        if (packingpcs) packingpcs.value = formatNumber(Math.floor(packingpcsVal));
                        if (packingdrik) packingdrik.value = formatNumber(Math.floor(packingdrikVal));

                    } else if (proc === 'LEM' || proc === 'LEM SETENGAH JADI') {
                        sortirSection?.classList.add('hidden');
                        timSection?.classList.add('hidden');
                        boxSection?.classList.add('hidden'); // Sembunyikan box section
                        if (outputDrik && outputDrik.parentElement) outputDrik.parentElement.style.display = '';
                        if (outputPcs && outputPcs.parentElement) outputPcs.parentElement.style.display = '';

                        if (jtDrik) jtDrik.readOnly = true;
                        if (jtPcs) jtPcs.readOnly = false;

                        const outputpcsVal = inputVal - jtpcsVal;
                        const jtdrikVal = up > 0 ? jtpcsVal / up : 0;
                        const outputdrikVal = up > 0 ? outputpcsVal / up : 0;

                        if (outputPcs) outputPcs.value = formatNumber(Math.max(0, Math.floor(outputpcsVal)));
                        if (jtDrik) jtDrik.value = formatNumber(Math.max(0, Math.floor(jtdrikVal)));
                        if (outputDrik) outputDrik.value = formatNumber(Math.max(0, Math.floor(outputdrikVal)));
                    } else {
                        // default case
                        sortirSection?.classList.add('hidden');
                        timSection?.classList.add('hidden');
                        boxSection?.classList.add('hidden'); // Sembunyikan box section
                        if (outputDrik && outputDrik.parentElement) outputDrik.parentElement.style.display = '';
                        if (outputPcs && outputPcs.parentElement) outputPcs.parentElement.style.display = '';

                        if (jtDrik) jtDrik.readOnly = false;
                        if (jtPcs) jtPcs.readOnly = true;

                        const outputdrikVal = inputVal - jtVal;
                        const jtpcsVal2 = jtVal * up;
                        const outputpcsVal = outputdrikVal * up;

                        if (outputDrik) outputDrik.value = formatNumber(Math.max(0, Math.floor(outputdrikVal)));
                        if (jtPcs) jtPcs.value = formatNumber(Math.max(0, Math.floor(jtpcsVal2)));
                        if (outputPcs) outputPcs.value = formatNumber(Math.max(0, Math.floor(outputpcsVal)));
                    }
                }

                // ===== Event listeners to trigger calculate & operator filter =====
                proses?.addEventListener('change', () => {
                    filterOperators();
                    calculate();
                    // toggle sections exactly like original intent:
                    if ((proses.value || '').toUpperCase() === 'SORTPACKING') {
                        sortirSection?.classList.remove('hidden');
                        boxSection?.classList.remove('hidden'); // TAMBAHKAN INI
                    } else {
                        sortirSection?.classList.add('hidden');
                        boxSection?.classList.add('hidden'); // TAMBAHKAN INI
                    }
                    if ((proses.value || '').toUpperCase() === 'SORTPACKING') {
                        timSection?.classList.remove('hidden');
                    } else {
                        timSection?.classList.add('hidden');
                    }
                    if ((proses.value || '').toUpperCase() === 'SORTPACKING') {
                        setInput?.parentElement?.classList.add('hidden');
                    } else {
                        setInput?.parentElement?.classList.remove('hidden');
                    }

                });
                // Tambahkan listener untuk BOX dan ISIBOX
                [boxInput, isiboxInput].forEach(el => {
                    if (!el) return;
                    el.addEventListener('input', calculate);
                    el.addEventListener('change', calculate);
                });
                [upspk, inputDrik, jtDrik, jtPcs].forEach(el => {
                    if (!el) return;
                    el.addEventListener('input', calculate);
                    el.addEventListener('change', calculate);
                });

                // attach listeners for any possible JT named fields from fieldsJT (original)
                const fieldsJT = [
                    'warna', 'banjir', 'beset', 'notok', 'powder', 'wb', 'uvkasar', 'uvmbleset', 'tidakuv',
                    'hotprint', 'laminating', 'laminasikurang', 'laminasi', 'tidakpresisi', 'pecah',
                    'emboss', 'porforasi', 'sobek', 'lengket', 'll'
                ];
                fieldsJT.forEach(f => {
                    const el = row.querySelector(`input[name="${f}"]`);
                    if (el) {
                        el.addEventListener('input', calculate);
                        el.addEventListener('change', calculate);
                    }
                });

                // ===== job lookup (sama persis) =====
                jobInput?.addEventListener('change', function() {
                    const jobId = this.value.trim();
                    if (jobId === '') return;
                    fetch(`/get-job-data/${jobId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data) {
                                productInput.value = data.product || '';
                                designnoInput.value = data.designno || '';
                                poInput.value = data.po || '';
                                // format qty with dot thousands
                                qtyInput.value = (data.qty !== undefined && data.qty !== null) ? data
                                    .qty.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                            } else {
                                productInput.value = '';
                                designnoInput.value = '';
                                poInput.value = '';
                                qtyInput.value = '';
                            }
                        })
                        .catch(() => {
                            productInput.value = '';
                            designnoInput.value = '';
                            poInput.value = '';
                            qtyInput.value = '';
                        });
                });

                qtyInput?.addEventListener('input', function(e) {
                    const raw = this.value.replace(/\./g, '');
                    if (isNaN(raw)) return;
                    this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    try {
                        this.setSelectionRange(this.value.length, this.value.length);
                    } catch (err) {}
                });

                // initial filterOperators & calculate for this row
                filterOperators();
                calculate();

                // provide a remove-row button (if you want per-row remove)
                if (!row.querySelector('.remove-row-btn')) {
                    const h2 = row.querySelector('h2');
                    // add Remove Row button in header
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-row-btn ml-3 bg-red-500 text-white px-2 py-1 rounded text-xs';
                    removeBtn.textContent = 'Remove Row';
                    removeBtn.addEventListener('click', () => {
                        row.remove();
                    });
                    if (h2) h2.appendChild(removeBtn);
                }
                // ===== COLLAPSE/EXPAND LOGIC =====
                const toggleBtn = row.querySelector('.toggle-collapse');
                const content = row.querySelector('.row-content');
                const preview = row.querySelector('.collapse-preview');
                const prosesSel = row.querySelector('.proses');
                const jobSel = row.querySelector('.job-input');
                const productSel = row.querySelector('.product-input');

                // Fungsi update preview
                function updatePreview() {
                    const prosesText = (prosesSel?.value || '').toUpperCase();
                    const jobText = (jobSel?.value || '');
                    const prodText = (productSel?.value || '');
                    if (!prosesText && !jobText && !prodText) {
                        preview.textContent = '(belum diisi)';
                    } else {
                        preview.textContent = `${prosesText} ${jobText} ${prodText}`.trim();
                    }
                }

                // Fungsi toggle collapse
                function setCollapsed(state) {
                    if (state) {
                        content.style.display = 'none';
                        toggleBtn.textContent = 'Expand';
                        updatePreview();
                    } else {
                        content.style.display = '';
                        toggleBtn.textContent = 'Collapse';
                        preview.textContent = '';
                    }
                    row.dataset.collapsed = state ? 'true' : 'false';
                }

                // Event tombol collapse
                toggleBtn.addEventListener('click', () => {
                    const isCollapsed = row.dataset.collapsed === 'true';
                    setCollapsed(!isCollapsed);
                });

                // Auto update preview ketika data berubah
                [prosesSel, jobSel, productSel].forEach(el => {
                    el?.addEventListener('change', updatePreview);
                    el?.addEventListener('input', updatePreview);
                });

                // Default: expand saat pertama kali
                setCollapsed(false);
            } // end initRow

            // initialize existing rows
            document.querySelectorAll('.row-item').forEach(initRow);

            // Add Row logic: clone the first row and re-init events
            document.getElementById('add-row-btn')?.addEventListener('click', () => {
                const container = document.getElementById('rows-container');
                const first = container.querySelector('.row-item');
                if (!first) return;
                const clone = first.cloneNode(true);

                // reset values in clone
                clone.querySelectorAll('input, select, textarea').forEach(el => {
                    // keep hidden operator-hidden input but clear value
                    if (el.type === 'hidden') el.value = '';
                    else if (el.tagName.toLowerCase() === 'select') el.selectedIndex = 0;
                    else el.value = '';
                });
                // ===== RESET ERROR STATE (PENTING) =====
                clone.classList.remove('row-time-error');
                clone.dataset.collapsed = 'false';

                // sembunyikan badge error
                const badge = clone.querySelector('.time-error-badge');
                if (badge) badge.classList.add('hidden');

                // remove jt entries in clone
                clone.querySelectorAll('.jt-container').forEach(c => c.innerHTML = '');
                // hide dynamic sections initially
                clone.querySelectorAll('.sortpacking-section').forEach(s => s.classList.add('hidden'));
                clone.querySelectorAll('.tim-section').forEach(t => t.classList.add('hidden'));
                // append clone and init
                container.appendChild(clone);
                initRow(clone);
                document.querySelectorAll('.row-item').forEach(r => {
                    const btn = r.querySelector('.toggle-collapse');
                    const content = r.querySelector('.row-content');
                    const preview = r.querySelector('.collapse-preview');
                    if (r === clone) {
                        content.style.display = ''; // expand
                        btn.textContent = 'Collapse';
                        preview.textContent = '';
                        r.dataset.collapsed = 'false';
                    } else {
                        content.style.display = 'none'; // collapse
                        btn.textContent = 'Expand';
                        const prosesText = (r.querySelector('.proses')?.value || '').toUpperCase();
                        const jobText = (r.querySelector('.job-input')?.value || '');
                        const prodText = (r.querySelector('.product-input')?.value || '');
                        preview.textContent = `${prosesText} ${jobText} ${prodText}`.trim();
                        r.dataset.collapsed = 'true';
                    }
                });
                // scroll into view
                clone.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            // On form submit: unformat numeric fields (convert formatted string to plain digits)
            document.querySelector('form')?.addEventListener('submit', (ev) => {
                // select all formatted number inputs across all rows and remove dots
                document.querySelectorAll('input').forEach(inp => {
                    if (!inp) return;
                    const name = inp.getAttribute('name') || '';
                    // We will unformat inputs which originally are numeric in your original script:
                    const checkNames = ['input[]', 'upspk[]', 'jtdrik[]', 'jtpcs[]', 'sortirpcs[]',
                        'sortirdrik[]', 'packingpcs[]', 'packingdrik[]', 'outputdrik[]',
                        'outputpcs[]', 'qty[]'
                    ];
                    // Accept also non-suffixed names if present
                    if (name && (checkNames.includes(name) || checkNames.some(n => name.startsWith(n
                            .replace('[]', ''))))) {
                        inp.value = unformatNumber(inp.value);
                    }
                });
                // allow form to submit
            });

        }); // end DOMContentLoaded
        // Event delegation untuk tombol Remove Row (bekerja untuk tombol yang di-clone)
        const rowsContainer = document.getElementById('rows-container');
        rowsContainer?.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-row-btn');
            if (!btn) return;
            const row = btn.closest('.row-item');
            if (!row) return;

            // jangan hapus semua row, minimal 1 row tersisa
            const total = rowsContainer.querySelectorAll('.row-item').length;
            if (total <= 1) {
                alert('Minimal harus ada 1 row.');
                return;
            }
            row.remove();
        });
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('loadingSpinner');
            const btnText = document.getElementById('btnText');

            if (!form || !submitBtn) return;

            let isSubmitting = false;

            form.addEventListener('submit', () => {
                if (isSubmitting) {
                    return false; // ⛔ STOP double submit
                }

                isSubmitting = true;

                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                btnText.textContent = 'Loading...';
            });
        });
    </script>

    </div>



</body>

</html>
