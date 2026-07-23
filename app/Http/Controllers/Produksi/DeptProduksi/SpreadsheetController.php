<?php

namespace App\Http\Controllers\Produksi\DeptProduksi;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessSpreadsheetAndDbJob;
use App\Models\ActivityLog;
use App\Models\Karyawan;
use App\Models\Karyawanstaff;
use App\Models\Prodev;
use App\Models\ProsesProduksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SpreadsheetController extends Controller
{
    // create proses spreadsheet
    public function index()
    {
        $karyawan = Karyawan::all();
        $karyawanstaff = Karyawanstaff::all();
        $job = Prodev::all();

        return view('role.produksi.produksidept.proses.spreadsheet', compact('karyawan', 'karyawanstaff', 'job'));
    }

    public function getJob($id)
    {
        $job = Prodev::find($id);
        if ($job) {
            return response()->json([
                'product' => $job->product ?? '',
                'designno' => $job->designno ?? '',
                'po' => $job->po ?? '',
                'qty' => $job->qty ?? '',
            ]);
        }

        return response()->json([], 404);
    }

    public function store(Request $request)
    {
        // Validasi: (SEMUA RULES ANDA DISALIN LENGKAP)
        $rules = [
            'proses' => 'required|array|min:1',
            'proses.*' => 'required|string',
            'job' => 'nullable|array',
            'job.*' => 'nullable|string',
            'product' => 'nullable|array',
            'product.*' => 'nullable|string',
            'designno' => 'nullable|array',
            'designno.*' => 'nullable|string',
            'po' => 'nullable|array',
            'po.*' => 'nullable|string',
            'qty' => 'nullable|array',
            'qty.*' => 'nullable|string',
            'pengawas' => 'nullable|array',
            'pengawas.*' => 'nullable|string',
            'shiftpengawas' => 'nullable|array',
            'shiftpengawas.*' => 'nullable|string',
            'upspk' => 'nullable|array',
            'upspk.*' => 'nullable|string',
            'tanggal' => 'nullable|array',
            'tanggal.*' => 'nullable|string',
            'mesin' => 'nullable|array',
            'mesin.*' => 'nullable|string',
            'vendormat' => 'nullable|array',
            'vendormat.*' => 'nullable|string',
            'shift' => 'nullable|array',
            'shift.*' => 'nullable|string',
            'palet' => 'nullable|array',
            'palet.*' => 'nullable|string',
            'set' => 'nullable|array',
            'set.*' => 'nullable|string',
            'operator' => 'nullable|array',
            'operator.*' => 'nullable|string',
            'jumlahtim' => 'nullable|array',
            'jumlahtim.*' => 'nullable|string',
            'run' => 'nullable|array',
            'run.*' => 'nullable|string',
            'finish' => 'nullable|array',
            'finish.*' => 'nullable|string',
            'break' => 'nullable|array',
            'break.*' => 'nullable|string',
            'totaljam' => 'nullable|array',
            'totaljam.*' => 'nullable|string',
            'input' => 'nullable|array',
            'input.*' => 'nullable|string',
            'ket' => 'nullable|array',
            'ket.*' => 'nullable|string',
            'jtdrik' => 'nullable|array',
            'jtdrik.*' => 'nullable|string',
            'target' => 'nullable|array',
            'target.*' => 'nullable|string',
            'karantina' => 'nullable|array',
            'karantina.*' => 'nullable|string',
            'outputdrik' => 'nullable|array',
            'outputdrik.*' => 'nullable|string',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string',
            'toleransi' => 'nullable|array',
            'toleransi.*' => 'nullable|string',
            'ok' => 'nullable|array',
            'ok.*' => 'nullable|string',
            'jtpcs' => 'nullable|array',
            'jtpcs.*' => 'nullable|string',
            'warna' => 'nullable|array',
            'warna.*' => 'nullable|string',
            'banjir' => 'nullable|array',
            'banjir.*' => 'nullable|string',
            'beset' => 'nullable|array',
            'beset.*' => 'nullable|string',
            'notok' => 'nullable|array',
            'notok.*' => 'nullable|string',
            'powder' => 'nullable|array',
            'powder.*' => 'nullable|string',
            'wb' => 'nullable|array',
            'wb.*' => 'nullable|string',
            'uvkasar' => 'nullable|array',
            'uvkasar.*' => 'nullable|string',
            'uvmbleset' => 'nullable|array',
            'uvmbleset.*' => 'nullable|string',
            'tidakuv' => 'nullable|array',
            'tidakuv.*' => 'nullable|string',
            'hotprint' => 'nullable|array',
            'hotprint.*' => 'nullable|string',
            'laminating' => 'nullable|array',
            'laminating.*' => 'nullable|string',
            'laminasikurang' => 'nullable|array',
            'laminasikurang.*' => 'nullable|string',
            'laminasi' => 'nullable|array',
            'laminasi.*' => 'nullable|string',
            'tidakpresisi' => 'nullable|array',
            'tidakpresisi.*' => 'nullable|string',
            'pecah' => 'nullable|array',
            'pecah.*' => 'nullable|string',
            'emboss' => 'nullable|array',
            'emboss.*' => 'nullable|string',
            'porforasi' => 'nullable|array',
            'porforasi.*' => 'nullable|string',
            'sobek' => 'nullable|array',
            'sobek.*' => 'nullable|string',
            'lengket' => 'nullable|array',
            'lengket.*' => 'nullable|string',
            'll' => 'nullable|array',
            'll.*' => 'nullable|string',
            'noteoperator' => 'nullable|array',
            'noteoperator.*' => 'nullable|string',
        ];

        // 1. Validasi data
        try {
            $validated = $request->validate($rules);
            $rowsCount = count($validated['proses'] ?? []);

            if ($rowsCount === 0) {
                return back()->with('error', 'Tidak ada baris untuk disimpan.');
            }

            // =================================================================
            // --- TAMBAHAN LOGIKA CEK DUPLIKAT DI SINI ---
            // =================================================================
            $duplikatList = [];
            $processedKeys = [];

            for ($i = 0; $i < $rowsCount; $i++) {
                $currentJob = trim($validated['job'][$i] ?? '');
                $currentOperator = trim($validated['operator'][$i] ?? '');
                $currentShift = trim($validated['shift'][$i] ?? '');

                if (! empty($currentJob)) {
                    $uniqueKey = $currentJob.'|'.$currentOperator.'|'.$currentShift;

                    // A. Cek ketik ganda di dalam form yang sedang di-submit
                    if (in_array($uniqueKey, $processedKeys)) {
                        $duplikatList[] = "Job <b>{$currentJob}</b> (Operator: {$currentOperator}, Shift: {$currentShift}) - <i>Ketik ganda di form</i>";

                        continue;
                    }

                    // B. Cek apakah sudah ada di database MySQL?
                    // (Pastikan Model ProsesProduksi dipanggil, bisa pakai \App\Models\ProsesProduksi)
                    $existsInDb = ProsesProduksi::where('job', $currentJob)
                        ->where('operator', $currentOperator)
                        ->where('shift', $currentShift)
                        ->exists();

                    if ($existsInDb) {
                        $duplikatList[] = "Job <b>{$currentJob}</b> (Operator: {$currentOperator}, Shift: {$currentShift}) - <i>Sudah ada di database</i>";

                        continue;
                    }

                    $processedKeys[] = $uniqueKey;
                }
            }

            // JIKA KETEMU DUPLIKAT: Tolak saat itu juga & tampilkan pesan ke layar!
            if (! empty($duplikatList)) {
                return back()->withInput()
                    ->with('error_duplikat', '<b>Data gagal disimpan!</b> Terdapat data pengerjaan yang sudah pernah diinput:<br>• '.implode('<br>• ', $duplikatList));
            }
            // =================================================================

        } catch (ValidationException $e) {
            // Jika validasi gagal, kembalikan seperti biasa
            return back()->withErrors($e->errors())->withInput();
        }

        // 2. Kirim data ke Job untuk diproses di background (Jika aman 100% dari duplikat)
        try {
            // Kita hanya perlu melempar data yang sudah divalidasi
            ProcessSpreadsheetAndDbJob::dispatch($validated);

            // 3. Langsung beri respon ke user (JANGAN DITUNGGU)
            return back()->with('success', 'Berhasil! '.$rowsCount.' baris data berhasil terkirim. Ini mungkin perlu beberapa menit untuk tampil di spreadsheet.');

        } catch (\Exception $e) {
            Log::error('Gagal dispatch job: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan internal saat memproses request.');
        }
    }

    public function calculateDerivedValues($record)
    {
        // Pastikan nilai input, jtdrik, dan upspk adalah angka sebelum melakukan perhitungan
        $input = (float) str_replace('.', '', (string) ($record->input ?? 0));
        $jtdrik = (float) str_replace('.', '', (string) ($record->jtdrik ?? 0));
        $upspk = (float) str_replace('.', '', (string) ($record->upspk ?? 0));
        $jtpcs = (float) str_replace('.', '', (string) ($record->jtpcs ?? 0));
        $prosesName = strtolower((string) ($record->proses ?? ''));

        $record->jtpcs = $jtpcs;
        $record->input = $input;
        $record->jtdrik = $jtdrik;
        $record->upspk = $upspk;

        // input = output pcs
        if (in_array($prosesName, [
            'lem',
            'lem setengah jadi',
            'sortir lem',
        ])) {
            // jtdrik = jtpcs/upspk
            $record->jtdrik = $upspk > 0 ? $record->jtpcs / $upspk : 0;
            // outputpcs = input - jt pcs
            $record->outputpcs = $input - $record->jtpcs;
            // outputdrik = outputpcs/upspk
            $record->outputdrik = $upspk > 0 ? $record->outputpcs / $upspk : 0;
            // $record->outputpcs = $record->outputdrik * $upspk;
            $record->total_pengerjaan_drik = $record->jtdrik + $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->jtpcs + $record->outputpcs;

            return $record;
        }

        if ($prosesName === 'sortpacking') {
            // input, jtdrik, jtpcs berasal dari database
            $record->outputpcs = $input;
            $record->outputdrik = $upspk > 0 ? $record->outputpcs / $upspk : 0;

            $record->total_pengerjaan_drik = $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->outputpcs;

            return $record;
        }

        // Perhitungan outputdrik dan outputpcs
        $record->jtpcs = $jtdrik * $upspk;
        $record->outputdrik = $input - $jtdrik;
        $record->outputpcs = $record->outputdrik * $upspk;
        $record->total_pengerjaan_drik = $jtdrik + $record->outputdrik;
        $record->total_pengerjaan_pcs = $record->jtpcs + $record->outputpcs;

        return $record;
    }

    // list data proses
    public function indexdata(Request $request)
    {
        // 1.Tangkap input tanggal dari form filter
        $filterProses = $request->get('proses');
        $filterMesin = $request->get('mesin');
        $filterId = $request->get('id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterJob = $request->get('job');
        $filterOperator = $request->get('operator');
        $filterTanggal = $request->get('tanggal');
        $filterDocket = $request->get('designno');
        $filterProduct = $request->get('product');
        $filterShift = $request->get('shift');

        $query = ProsesProduksi::query();

        // filter
        if (! empty($filterId)) {
            $query->where('id', $filterId);
        }
        if (! empty($filterProses)) {
            $query->where('proses', $filterProses);
        }
        if (! empty($filterMesin)) {
            $query->where('mesin', $filterMesin);
        }
        // filter multi job di index
        if (! empty($filterJob)) {
            $jobsList = preg_split('/[\s,;|]+/', trim($filterJob));
            $jobsList = array_filter($jobsList);
            if (count($jobsList) > 1) {
                $query->where(function ($q) use ($jobsList) {
                    foreach ($jobsList as $jobItem) {
                        $q->orWhere('job', 'like', '%'.$jobItem.'%');
                    }
                });
            } elseif (count($jobsList) == 1) {
                $query->where('job', 'like', '%'.$jobsList[0].'%');
            }
        }
        if (! empty($filterOperator)) {
            $operatorsList = preg_split('/[,;|]+/', trim($filterOperator));
            $operatorsList = array_filter(array_map('trim', $operatorsList));
            if (! empty($operatorsList)) {
                $query->where(function ($q) use ($operatorsList) {
                    foreach ($operatorsList as $operatorItem) {
                        $q->orWhere('operator', 'like', '%'.$operatorItem.'%');
                    }
                });
            }
        }
        if (! empty($filterTanggal)) {
            $query->whereDate('tanggal', $filterTanggal);
        }
        if (! empty($filterDocket)) {
            $docketsList = preg_split('/[\s,;|]+/', trim($filterDocket));
            $docketsList = array_filter($docketsList);
            if (count($docketsList) > 1) {
                $query->where(function ($q) use ($docketsList) {
                    foreach ($docketsList as $docketItem) {
                        $q->orWhere('designno', 'like', '%'.$docketItem.'%');
                    }
                });
            } elseif (count($docketsList) == 1) {
                $query->where('designno', 'like', '%'.$docketsList[0].'%');
            }
        }
        if (! empty($filterProduct)) {
            $productsList = preg_split('/[,;|]+/', trim($filterProduct));
            $productsList = array_map('trim', $productsList);
            $productsList = array_filter($productsList);
            if (! empty($productsList)) {
                $query->whereIn('product', $productsList);
            }
        }
        if ($filterShift !== null && $filterShift !== '') {
            $query->where('shift', $filterShift);
        }
        // 2.Logika filter rentang tanggal
        if (! empty($startDate) && ! empty($endDate)) {
            // Jika dari tanggal & sampai tanggal diisi keduanya
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (! empty($startDate)) {
            // Jika hanya mengisi "Dari Tanggal" saja
            $query->whereDate('tanggal', '>=', $startDate);
        } elseif (! empty($endDate)) {
            // Jika hanya mengisi "Sampai Tanggal" saja
            $query->whereDate('tanggal', '<=', $endDate);
        }

        // Sorting support
        $allowedSorts = [
            'job' => 'job',
            'docket' => 'designno',
            'proses' => 'proses',
            'mesin' => 'mesin',
            'product' => 'product',
            'operator' => 'operator',
            'tanggal' => 'tanggal',
        ];

        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sort && isset($allowedSorts[$sort])) {
            $query->orderBy($allowedSorts[$sort], $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Bagian ini sudah sangat tepat karena pakai ->appends($request->query())
        $prosesProduksi = $query->paginate(10)->appends($request->query());

        foreach ($prosesProduksi as $data) {
            $totalJam = 0;

            if (! empty($data->finish) && (! empty($data->set) || ! empty($data->run))) {
                $waktuMulaiString = ! empty($data->set) ? $data->set : $data->run;
                $waktuMulai = Carbon::parse($waktuMulaiString);
                $waktuFinish = Carbon::parse($data->finish);

                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;
                // jika break true kurangi 1 jam
                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            }

            // Timpa langsung properti asli database
            $data->totaljam = max(0, round($totalJam, 2));
            $this->calculateDerivedValues($data);
        }

        // =================================================================
        // PERBAIKAN: Tarik daftar proses & mesin unik langsung dari database!
        // =================================================================
        $daftarProses = ProsesProduksi::select('proses')
            ->whereNotNull('proses')
            ->distinct()
            ->orderBy('proses')
            ->pluck('proses');

        $daftarMesin = ProsesProduksi::select('mesin')
            ->whereNotNull('mesin')
            ->where('mesin', '!=', '')
            ->distinct()
            ->orderBy('mesin')
            ->pluck('mesin');

        $daftarShift = ProsesProduksi::select('shift')
            ->whereNotNull('shift')
            ->where('shift', '!=', '')
            ->distinct()
            ->orderBy('shift')
            ->pluck('shift');

        // Ambil data
        $data = $query->get();

        // Hitung total dari data yang tampil
        $total = [
            'input' => $prosesProduksi->sum(fn ($x) => (float) ($x->input ?? 0)),
            'jtpcs' => $prosesProduksi->sum(fn ($x) => (float) ($x->jtpcs ?? 0)),
            'jtdrik' => $prosesProduksi->sum(fn ($x) => (float) ($x->jtdrik ?? 0)),
            'outputpcs' => $prosesProduksi->sum(fn ($x) => (float) ($x->outputpcs ?? 0)),
            'outputdrik' => $prosesProduksi->sum(fn ($x) => (float) ($x->outputdrik ?? 0)),
        ];

        return view('role.produksi.produksidept.proses.index', compact('prosesProduksi', 'daftarProses', 'daftarMesin', 'daftarShift', 'total'));
    }

    // report proses
    public function report(Request $request, $job_id)
    {
        // 1. CARI NOMOR DOCKET DARI JOB INI
        $firstRecord = ProsesProduksi::where('job', $job_id)->first();
        if (! $firstRecord) {
            abort(404, 'Job tidak ditemukan.');
        }
        $docket = $firstRecord->designno ?? '-';
        $product = $firstRecord->product ?? '-';

        // 2. KUMPULKAN DAFTAR NOMOR JOB YANG AKAN DITAMPILKAN
        $jobsToQuery = [];

        $searchJobsInput = $request->query('search_jobs');
        if (! empty($searchJobsInput)) {
            // Bersihkan dan pisahkan input pencarian berdasarkan koma/spasi/titik koma/pipe (sama seperti index)
            $searchedJobs = preg_split('/[\s,;|]+/', trim($searchJobsInput));
            // Hapus elemen kosong dan duplikat
            $searchedJobs = array_filter(array_unique(array_map('trim', $searchedJobs)));

            if (! empty($searchedJobs)) {
                $matchedJobs = [];
                foreach ($searchedJobs as $sJob) {
                    $jobsFound = ProsesProduksi::where('job', 'like', '%'.$sJob.'%')
                        ->distinct()
                        ->pluck('job')
                        ->toArray();

                    if (empty($jobsFound)) {
                        return redirect()->route('proses-produksi.rangkuman', $job_id)
                            ->with('error', "Tidak ada Job ID yang cocok dengan pencarian '{$sJob}'.");
                    }

                    $matchedJobs = array_merge($matchedJobs, $jobsFound);
                }

                $matchedJobs = array_unique($matchedJobs);
                $validatedJobs = [];
                $commonDocket = null;
                $commonProduct = null;

                foreach ($matchedJobs as $mJob) {
                    $checkRecord = ProsesProduksi::where('job', $mJob)->first();
                    if ($checkRecord) {
                        $jobDocket = $checkRecord->designno ?? '-';
                        $jobProduct = $checkRecord->product ?? '-';

                        if ($commonDocket === null || $commonProduct === null) {
                            $commonDocket = $jobDocket;
                            $commonProduct = $jobProduct;
                        } elseif ($jobDocket !== $commonDocket) {
                            return redirect()->route('proses-produksi.rangkuman', $job_id)
                                ->with('error', "Pencarian multi-job harus memiliki Docket/Design No yang sama. Job '{$mJob}' memiliki Docket '{$jobDocket}' yang berbeda dengan Docket '{$commonDocket}'.");
                        } elseif ($jobProduct !== $commonProduct) {
                            return redirect()->route('proses-produksi.rangkuman', $job_id)
                                ->with('error', "Pencarian multi-job harus memiliki Product No yang sama. Job '{$mJob}' memiliki Product '{$jobProduct}' yang berbeda dengan Docket '{$commonProduct}'.");
                        }
                        $validatedJobs[] = $mJob;
                    }
                }

                $jobsToQuery = $validatedJobs;
                $docket = $commonDocket;
                $product = $commonProduct;
            }
        }

        // Jika tidak melakukan pencarian khusus, tampilkan job_id dari URL saja
        if (empty($jobsToQuery)) {
            $jobsToQuery = [$job_id];
        }

        // 3. AMBIL DATA HANYA UNTUK JOB YANG DIPILIH / HASIL PENCARIAN
        $allowedSorts = [
            'job' => 'job',
            'docket' => 'designno',
            'proses' => 'proses',
            'product' => 'product',
            'operator' => 'operator',
            'tanggal' => 'tanggal',
        ];

        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'asc')) === 'asc' ? 'asc' : 'desc';

        $detailQuery = ProsesProduksi::whereIn('job', $jobsToQuery);

        if ($sort && isset($allowedSorts[$sort])) {
            $detailQuery->orderBy($allowedSorts[$sort], $dir);
        } else {
            $detailOrder = [
                'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
                'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
                'PRETEL', 'LEM SETENGAH JADI', 'LEM', 'SORTIR LEM', 'SORTIR', 'PACKING', 'SORTPACKING',
            ];
            $caseSql = 'CASE UPPER(proses) ';
            foreach ($detailOrder as $index => $processName) {
                $caseSql .= "WHEN ? THEN {$index} ";
            }
            $caseSql .= 'ELSE '.count($detailOrder).' END';

            $detailQuery->orderByRaw($caseSql, $detailOrder)
                ->orderBy('tanggal', 'desc');
        }

        $allDetailProses = $detailQuery->get();

        // Anonymous function to handle calculations for virtual/derived attributes
        $calculateItem = function ($data) {
            $totalJam = 0;

            if (! empty($data->finish) && (! empty($data->set) || ! empty($data->run))) {
                $waktuMulaiString = ! empty($data->set) ? $data->set : $data->run;
                $waktuMulai = Carbon::parse($waktuMulaiString);
                $waktuFinish = Carbon::parse($data->finish);

                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;

                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            }

            $data->totaljam = max(0, round($totalJam, 2));

            $this->calculateDerivedValues($data);

            // Custom Show-page calculations for PACKING and SORTIR
            $procName = strtoupper(trim((string) $data->proses));
            $input = (float) str_replace('.', '', (string) ($data->input ?? 0));
            $upspk = (float) str_replace('.', '', (string) ($data->upspk ?? 0));

            if ($procName === 'PACKING') {
                $data->outputdrik = $upspk > 0 ? $input / $upspk : 0;
                $data->outputpcs = $input;
                $data->total_pengerjaan_drik = $data->outputdrik;
                $data->total_pengerjaan_pcs = $data->outputpcs;
            } elseif ($procName === 'SORTIR') {
                $jtpcs = (float) str_replace('.', '', (string) ($data->jtpcs ?? 0));
                $data->jtdrik = $upspk > 0 ? $jtpcs / $upspk : 0;
                $data->outputpcs = $input + $jtpcs;
                $data->outputdrik = $upspk > 0 ? $data->outputpcs / $upspk : 0;
                $data->total_pengerjaan_drik = $data->outputdrik;
                $data->total_pengerjaan_pcs = $data->outputpcs;
            } elseif ($procName === 'SORTPACKING') {
                $jtpcs = (float) str_replace('.', '', (string) ($data->jtpcs ?? 0));
                $data->jtdrik = $upspk > 0 ? $jtpcs / $upspk : 0;
                $data->outputpcs = (2 * $input) + $jtpcs;
                $data->outputdrik = $upspk > 0 ? $data->outputpcs / $upspk : 0;
                $data->total_pengerjaan_drik = $data->outputdrik;
                $data->total_pengerjaan_pcs = $data->outputpcs;
            }
        };

        // HITUNG ATRIBUT VIRTUAL UNTUK SEMUA REKOR (agar rangkuman & grand total akurat)
        foreach ($allDetailProses as $data) {
            $calculateItem($data);
        }

        // Tidak lagi dipaginate — seluruh data yang sama dipakai untuk detail per-proses,
        // supaya saat expand di tabel rangkuman, semua aktivitas proses tersebut ikut tampil
        // (tidak terpotong halaman).
        $detailProses = $allDetailProses;

        // 4. HITUNG DATA PER PROSES (DOCKET-WIDE)
        // Urutan ini penting: dipakai juga untuk mencari "proses sebelumnya yang ada
        // datanya" pada perhitungan SELISIH di bawah.
        $masterProses = [
            'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
            'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
            'PRETEL', 'LEM SETENGAH JADI', 'LEM', 'SORTIR LEM', 'SORTIR', 'PACKING',
        ];

        $prosesData = [];

        foreach ($masterProses as $prosesName) {
            $jam = 0;
            $input = 0;
            $jtdrik = 0;
            $jtpcs = 0;
            $outputdrik = 0;
            $outputpcs = 0;
            $total_pengerjaan_drik = 0;
            $total_pengerjaan_pcs = 0;

            if ($prosesName === 'SORTIR') {
                $dataPerProses = $allDetailProses->filter(function ($item) {
                    $p = strtoupper(trim((string) $item->proses));

                    return $p === 'SORTIR' || $p === 'SORTPACKING';
                });
                foreach ($dataPerProses as $item) {
                    $itemInput = (float) str_replace('.', '', (string) ($item->input ?? 0));
                    $itemUpspk = (float) str_replace('.', '', (string) ($item->upspk ?? 0));
                    $itemJtpcs = (float) str_replace('.', '', (string) ($item->jtpcs ?? 0));

                    $itemJtdrik = $itemUpspk > 0 ? $itemJtpcs / $itemUpspk : 0;
                    $itemOutputpcs = $itemInput + $itemJtpcs;
                    $itemOutputdrik = $itemUpspk > 0 ? $itemOutputpcs / $itemUpspk : 0;

                    $jam += (float) ($item->totaljam ?? 0);
                    $input += $itemInput;
                    $jtdrik += $itemJtdrik;
                    $jtpcs += $itemJtpcs;
                    $outputdrik += $itemOutputdrik;
                    $outputpcs += $itemOutputpcs;
                    $total_pengerjaan_drik += $itemOutputdrik;
                    $total_pengerjaan_pcs += $itemOutputpcs;
                }
            } elseif ($prosesName === 'PACKING') {
                $dataPerProses = $allDetailProses->filter(function ($item) {
                    $p = strtoupper(trim((string) $item->proses));

                    return $p === 'PACKING' || $p === 'SORTPACKING';
                });
                foreach ($dataPerProses as $item) {
                    $itemInput = (float) str_replace('.', '', (string) ($item->input ?? 0));
                    $itemUpspk = (float) str_replace('.', '', (string) ($item->upspk ?? 0));

                    $itemOutputpcs = $itemInput;
                    $itemOutputdrik = $itemUpspk > 0 ? $itemInput / $itemUpspk : 0;

                    $jam += (float) ($item->totaljam ?? 0);
                    $input += $itemInput;
                    $outputdrik += $itemOutputdrik;
                    $outputpcs += $itemOutputpcs;
                    $total_pengerjaan_drik += $itemOutputdrik;
                    $total_pengerjaan_pcs += $itemOutputpcs;
                }
            } else {
                $dataPerProses = $allDetailProses->filter(function ($item) use ($prosesName) {
                    return strtoupper(trim((string) $item->proses)) === $prosesName;
                });
                $jam = $dataPerProses->sum('totaljam');
                $input = $dataPerProses->sum('input');
                $jtdrik = $dataPerProses->sum('jtdrik');
                $jtpcs = $dataPerProses->sum('jtpcs');
                $outputdrik = $dataPerProses->sum('outputdrik');
                $outputpcs = $dataPerProses->sum('outputpcs');
                $total_pengerjaan_drik = $dataPerProses->sum('total_pengerjaan_drik');
                $total_pengerjaan_pcs = $dataPerProses->sum('total_pengerjaan_pcs');
            }

            $prosesData[$prosesName] = [
                'proses' => $prosesName,
                'jam' => $jam,
                'input' => $input,
                'jt_drik' => $jtdrik,
                'jt_pcs' => $jtpcs,
                'output_drik' => $outputdrik,
                'output_pcs' => $outputpcs,
                'total_pengerjaan_drik' => $total_pengerjaan_drik,
                'total_pengerjaan_pcs' => $total_pengerjaan_pcs,
                'selisih_drik' => 0,
                'selisih_pcs' => 0,
                'has_selisih' => false,
                'has_data' => $dataPerProses->isNotEmpty(),
            ];
        }

        // 5. SELISIH — dihitung untuk SEMUA proses (kecuali PACKING), masing-masing
        // dibandingkan ke proses SEBELUMNYA (mundur di urutan $masterProses) yang
        // ada datanya. Contoh: SORTIR dibandingkan ke LEM; kalau LEM kosong, ke
        // PRETEL; kalau kosong juga, ke CUTTING, dst. Lalu LEM sendiri juga
        // dibandingkan ke PRETEL (atau proses sebelumnya lagi kalau kosong), dan
        // begitu seterusnya mundur sampai ke proses paling awal (PRINT, yang tidak
        // punya pembanding sehingga tidak dihitung selisihnya).
        // Rumus: Total Pengerjaan Drik/Pcs proses ini dikurangi Output Drik/Pcs
        // proses pembanding.
        foreach ($masterProses as $idx => $prosesName) {
            if ($prosesName === 'PACKING') {
                continue; // packing tidak dihitung selisihnya
            }
            if ($idx === 0) {
                continue; // proses paling awal tidak punya proses sebelumnya untuk dibandingkan
            }
            if (empty($prosesData[$prosesName]['has_data'])) {
                continue; // proses ini sendiri kosong, tidak perlu dihitung
            }

            $prosesPembanding = null;
            for ($j = $idx - 1; $j >= 0; $j--) {
                $namaSebelumnya = $masterProses[$j];
                if (! empty($prosesData[$namaSebelumnya]['has_data'])) {
                    $prosesPembanding = $namaSebelumnya;
                    break;
                }
            }

            if ($prosesPembanding !== null) {
                $prosesData[$prosesName]['selisih_drik'] = $prosesData[$prosesName]['total_pengerjaan_drik'] - $prosesData[$prosesPembanding]['output_drik'];
                $prosesData[$prosesName]['selisih_pcs'] = $prosesData[$prosesName]['total_pengerjaan_pcs'] - $prosesData[$prosesPembanding]['output_pcs'];
                $prosesData[$prosesName]['has_selisih'] = true;
            }
        }

        // 6. HANYA TAMPILKAN PROSES YANG BENAR-BENAR ADA DATANYA
        $rangkuman = collect($prosesData)
            ->filter(fn ($row) => $row['has_data'])
            ->values()
            ->all();

        $total = [
            'input' => $allDetailProses->sum(fn ($x) => (float) ($x->input ?? 0)),
            'jtpcs' => $allDetailProses->sum(fn ($x) => (float) ($x->jtpcs ?? 0)),
            'jtdrik' => $allDetailProses->sum(fn ($x) => (float) ($x->jtdrik ?? 0)),
            'outputpcs' => $allDetailProses->sum(fn ($x) => (float) ($x->outputpcs ?? 0)),
            'outputdrik' => $allDetailProses->sum(fn ($x) => (float) ($x->outputdrik ?? 0)),
        ];

        // 7. KIRIM DATA KE BLADE
        return view('role.produksi.produksidept.proses.report', compact('rangkuman', 'detailProses', 'job_id', 'docket', 'product', 'total', 'jobsToQuery'));
    }

    public function inlineUpdate(Request $request)
    {
        $allowedFields = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift', 'operator', 'set', 'run', 'finish', 'tanggal', 'qty', 'mesin'];
        $stringFields = ['operator', 'set', 'run', 'finish', 'tanggal', 'mesin'];

        $valueRules = 'required';
        if (in_array($request->field, $stringFields, true)) {
            $valueRules = 'nullable|string';
        } elseif ($request->field === 'shift') {
            $valueRules = 'nullable|numeric';
        } else {
            $valueRules = 'required|numeric';
        }

        $request->validate([
            'id' => 'required|integer|exists:proses_produksis,id',
            'field' => 'required|string|in:'.implode(',', $allowedFields),
            'value' => $valueRules,
        ]);

        $record = ProsesProduksi::findOrFail($request->id);
        $field = $request->field;
        $value = $request->input('value');
        $prosesName = strtolower((string) ($record->proses ?? ''));
        $saveField = $field;

        // Custom Validation: Tanggal
        if ($field === 'tanggal') {
            try {
                Carbon::parse($value);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format Tanggal tidak valid.',
                ], 422);
            }
        }

        // Custom Validation: Set, Run, Finish
        if (in_array($field, ['set', 'run', 'finish'], true)) {
            $parsedVal = null;
            if (! empty($value) && $value !== '-') {
                try {
                    $parsedVal = Carbon::parse($value);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format waktu tidak valid.',
                    ], 422);
                }
            }

            // Fetch existing dates if available
            $existingSet = ! empty($record->set) && $record->set !== '-' ? Carbon::parse($record->set) : null;
            $existingRun = ! empty($record->run) && $record->run !== '-' ? Carbon::parse($record->run) : null;
            $existingFinish = ! empty($record->finish) && $record->finish !== '-' ? Carbon::parse($record->finish) : null;

            // Set and Run cannot both be empty
            if ($field === 'set' && $parsedVal === null) {
                if ($existingRun === null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Set dan Run tidak boleh keduanya kosong. Minimal salah satu wajib diisi.',
                    ], 422);
                }
            }

            if ($field === 'run' && $parsedVal === null) {
                if ($existingSet === null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Set dan Run tidak boleh keduanya kosong. Minimal salah satu wajib diisi.',
                    ], 422);
                }
            }

            if ($field === 'set' && $parsedVal !== null) {
                if ($existingRun !== null && $parsedVal->greaterThan($existingRun)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai Set tidak boleh lebih besar dari Run ('.$existingRun->format('d-m-Y H:i').').',
                    ], 422);
                }
                if ($existingFinish !== null && $parsedVal->greaterThan($existingFinish)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai Set tidak boleh lebih besar dari Finish ('.$existingFinish->format('d-m-Y H:i').').',
                    ], 422);
                }
            }

            if ($field === 'run' && $parsedVal !== null) {
                if ($existingSet !== null && $parsedVal->lessThan($existingSet)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai Run tidak boleh lebih kecil dari Set ('.$existingSet->format('d-m-Y H:i').').',
                    ], 422);
                }
                if ($existingFinish !== null && $parsedVal->greaterThan($existingFinish)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai Run tidak boleh lebih besar dari Finish ('.$existingFinish->format('d-m-Y H:i').').',
                    ], 422);
                }
            }

            if ($field === 'finish' && $parsedVal !== null) {
                if ($existingSet !== null && $parsedVal->lessThan($existingSet)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai Finish tidak boleh lebih kecil dari Set ('.$existingSet->format('d-m-Y H:i').').',
                    ], 422);
                }
                if ($existingRun !== null && $parsedVal->lessThan($existingRun)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nilai Finish tidak boleh lebih kecil dari Run ('.$existingRun->format('d-m-Y H:i').').',
                    ], 422);
                }
            }
        }

        if ($field === 'jtdrik' && in_array($prosesName, ['lem', 'lem setengah jadi', 'sortir lem'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom JT Drik untuk proses '.strtoupper($prosesName).' tidak dapat diinput secara inline.',
            ], 422);
        }

        if ($field === 'jtpcs' && ! in_array($prosesName, ['lem', 'lem setengah jadi', 'sortir lem', 'sortpacking'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom JT PCS hanya dapat diubah untuk proses LEM, LEM SETENGAH JADI, SORTIR LEM, dan SORTPACKING.',
            ], 422);
        }

        // Validation: JT Drik/PCS cannot be greater than Input
        if (in_array($prosesName, ['lem', 'lem setengah jadi', 'sortir lem'], true)) {
            if ($field === 'jtpcs' && (float) $value > $record->input) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolom JT PCS tidak boleh lebih besar dari Input.',
                ], 422);
            }
            if ($field === 'input' && $record->jtpcs > (float) $value) {
                return response()->json([
                    'success' => false,
                    'message' => 'Input tidak boleh lebih kecil dari JT PCS.',
                ], 422);
            }
        } else {
            if (! in_array($field, $stringFields, true)) {
                $numericValue = (float) $value;
                if ($field === 'jtdrik' && $numericValue > $record->input) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kolom JT Drik tidak boleh lebih besar dari Input.',
                    ], 422);
                }
                if ($field === 'input' && $record->jtdrik > $numericValue) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Input tidak boleh lebih kecil dari JT Drik.',
                    ], 422);
                }
            }
        }

        // 1. Simpan nilai lama sebelum diubah oleh database
        $oldValue = $record->{$saveField};

        // 2. Siapkan nilai baru yang sudah diformat sesuai tipe datanya
        $formattedNewValue = in_array($saveField, $stringFields, true)
            ? (empty($value) || $value === '-' ? null : $value)
            : (($saveField === 'shift' && (empty($value) || $value === '-')) ? null : (float) $value);

        DB::transaction(function () use ($record, $saveField, $formattedNewValue, $oldValue) {
            // Update data utama
            $record->{$saveField} = $formattedNewValue;
            $record->save();

            // 3. Cek apakah ada perubahan nilai (agar tidak mencatat spam jika nilai sama)
            if ($oldValue != $formattedNewValue && ! is_null($oldValue)) {
                ActivityLog::create([
                    'proses_produksi_id' => $record->id,
                    'user_id' => auth()->id() ?? 1,
                    'field_name' => $saveField,
                    'old_value' => $oldValue,
                    'new_value' => $formattedNewValue,
                ]);
            }
        });

        $this->calculateDerivedValues($record);

        // Hitung totaljam dari set/run/finish (sama seperti logika di index())
        $totalJam = 0;
        if (! empty($record->finish) && (! empty($record->set) || ! empty($record->run))) {
            $waktuMulai = Carbon::parse(! empty($record->set) ? $record->set : $record->run);
            $waktuFinish = Carbon::parse($record->finish);
            $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
            $totalJam = $selisihMenit / 60;
            if (strtoupper($record->break) === 'TRUE' || $record->break == 1) {
                $totalJam -= 1;
            }
        }
        $record->totaljam = max(0, round($totalJam, 2));

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'value' => in_array($field, $stringFields) ? $value : number_format((float) $value, 0, ',', '.'),
            'values' => [
                'input' => (float) $record->input,
                'jtdrik' => (float) $record->jtdrik,
                'jtpcs' => (float) $record->jtpcs,
                'upspk' => (float) $record->upspk,
                'shift' => $record->shift !== null ? (float) $record->shift : null,
                'qty' => (float) $record->qty,
                'operator' => $record->operator,
                'mesin' => $record->mesin,
                'product' => $record->product,
                'tanggal' => $record->tanggal ? Carbon::parse($record->tanggal)->format('Y-m-d') : null,
                'set' => $record->set,
                'run' => $record->run,
                'finish' => $record->finish,
                'totaljam' => (float) $record->totaljam,
                'outputdrik' => (float) $record->outputdrik,
                'outputpcs' => (float) $record->outputpcs,
                'total_pengerjaan_drik' => (float) $record->total_pengerjaan_drik,
                'total_pengerjaan_pcs' => (float) $record->total_pengerjaan_pcs,
            ],
        ]);
    }

    public function getJobData($job_id)
    {
        // Menggunakan model Proses_Produksi untuk mencari berdasarkan kolom 'job'
        $jobData = ProsesProduksi::where('job', $job_id)->first();

        // Jika data job tersebut pernah diinput dan ditemukan di tabel
        if ($jobData) {
            return response()->json([
                'product' => $jobData->product,
                'designno' => $jobData->designno,
                'po' => $jobData->po,
                'qty' => $jobData->qty,
            ]);
        }

        // Jika job baru dan belum ada datanya sama sekali
        return response()->json(null);
    }

    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));
        $type = trim((string) $request->input('type', 'job'));

        $allowedTypes = [
            'job' => 'job',
            'designno' => 'designno',
            'product' => 'product',
        ];

        if (! isset($allowedTypes[$type])) {
            return response()->json([]);
        }

        $column = $allowedTypes[$type];

        $query = ProsesProduksi::query()
            ->select($column)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column);

        if ($keyword !== '') {
            $query->where($column, 'like', "%{$keyword}%");
        }

        $results = $query->limit(10)->get();

        return response()->json($results->map(function ($item) use ($column) {
            return [$column => $item->{$column}];
        })->values());
    }

    /**
     * ══════════════════════════════════════════════════════════════════
     * EXPORT EXCEL (PhpOffice\PhpSpreadsheet)
     * ══════════════════════════════════════════════════════════════════
     */

    /**
     * Filter query sama persis dengan yang dipakai di indexdata(), tapi
     * diekstrak jadi method sendiri supaya bisa dipakai ulang oleh kedua
     * fungsi export tanpa duplikasi & tanpa menyentuh indexdata() yang sudah
     * berjalan (menghindari risiko regresi).
     */
    private function applyProsesFilters($query, Request $request)
    {
        $filterProses = $request->get('proses');
        $filterMesin = $request->get('mesin');
        $filterId = $request->get('id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterJob = $request->get('job');
        $filterOperator = $request->get('operator');
        $filterTanggal = $request->get('tanggal');
        $filterDocket = $request->get('designno');
        $filterProduct = $request->get('product');
        $filterShift = $request->get('shift');

        if (! empty($filterId)) {
            $query->where('id', $filterId);
        }
        if (! empty($filterProses)) {
            $query->where('proses', $filterProses);
        }
        if (! empty($filterMesin)) {
            $query->where('mesin', $filterMesin);
        }
        if (! empty($filterJob)) {
            $jobsList = preg_split('/[\s,;|]+/', trim($filterJob));
            $jobsList = array_filter($jobsList);
            if (count($jobsList) > 1) {
                $query->where(function ($q) use ($jobsList) {
                    foreach ($jobsList as $jobItem) {
                        $q->orWhere('job', 'like', '%'.$jobItem.'%');
                    }
                });
            } elseif (count($jobsList) == 1) {
                $query->where('job', 'like', '%'.$jobsList[0].'%');
            }
        }
        if (! empty($filterOperator)) {
            $operatorsList = preg_split('/[,;|]+/', trim($filterOperator));
            $operatorsList = array_filter(array_map('trim', $operatorsList));
            if (! empty($operatorsList)) {
                $query->where(function ($q) use ($operatorsList) {
                    foreach ($operatorsList as $operatorItem) {
                        $q->orWhere('operator', 'like', '%'.$operatorItem.'%');
                    }
                });
            }
        }
        if (! empty($filterTanggal)) {
            $query->whereDate('tanggal', $filterTanggal);
        }
        if (! empty($filterDocket)) {
            $docketsList = preg_split('/[\s,;|]+/', trim($filterDocket));
            $docketsList = array_filter($docketsList);
            if (count($docketsList) > 1) {
                $query->where(function ($q) use ($docketsList) {
                    foreach ($docketsList as $docketItem) {
                        $q->orWhere('designno', 'like', '%'.$docketItem.'%');
                    }
                });
            } elseif (count($docketsList) == 1) {
                $query->where('designno', 'like', '%'.$docketsList[0].'%');
            }
        }
        if (! empty($filterProduct)) {
            $productsList = preg_split('/[,;|]+/', trim($filterProduct));
            $productsList = array_filter(array_map('trim', $productsList));
            if (! empty($productsList)) {
                $query->whereIn('product', $productsList);
            }
        }
        if ($filterShift !== null && $filterShift !== '') {
            $query->where('shift', $filterShift);
        }
        if (! empty($startDate) && ! empty($endDate)) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (! empty($startDate)) {
            $query->whereDate('tanggal', '>=', $startDate);
        } elseif (! empty($endDate)) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        return $query;
    }

    /** Hitung totaljam sebuah record persis seperti logika di indexdata()/report(). */
    private function computeTotalJam($data): float
    {
        $totalJam = 0;
        if (! empty($data->finish) && (! empty($data->set) || ! empty($data->run))) {
            try {
                $waktuMulaiString = ! empty($data->set) ? $data->set : $data->run;
                $waktuMulai = Carbon::parse($waktuMulaiString);
                $waktuFinish = Carbon::parse($data->finish);
                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;
                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            } catch (\Exception $e) {
                $totalJam = 0;
            }
        }

        return max(0, round($totalJam, 2));
    }

    /**
     * Rangkum SEMUA data satu Job menjadi satu baris pivot (44 kolom sesuai
     * urutan proses produksi), memakai rumus derived value yang sama persis
     * dengan yang dipakai di calculateDerivedValues() & report().
     */
    private function summarizeJobRow(string $jobId): ?array
    {
        $records = ProsesProduksi::where('job', $jobId)->get();

        if ($records->isEmpty()) {
            return null;
        }

        $first = $records->first();

        foreach ($records as $data) {
            $data->totaljam = $this->computeTotalJam($data);
            $this->calculateDerivedValues($data);
        }

        $byProses = $records->groupBy(function ($item) {
            return strtoupper(trim((string) ($item->proses ?? '')));
        });

        $sumField = fn ($collection, $field) => $collection->sum(fn ($x) => (float) ($x->{$field} ?? 0));
        $firstByTanggal = fn ($collection) => $collection->sortBy('tanggal')->first();
        $breakLabel = fn ($item) => $item ? ((strtoupper((string) $item->break) === 'TRUE' || $item->break == 1) ? 'YA' : 'TIDAK') : '-';
        $tglFmt = fn ($item) => $item && $item->tanggal ? Carbon::parse($item->tanggal)->format('d-m-Y') : '-';
        $jamFmt = fn ($val) => $val ? Carbon::parse($val)->timezone('Asia/Jakarta')->format('H:i') : '-';

        $row = [
            'JOB' => $jobId,
            'PRODUCT' => $first->product ?? '-',
            'DOCKET' => $first->designno ?? '-',
            'PO' => $first->po ?? '-',
            'QTY' => (float) ($first->qty ?? 0),
        ];

        // PRINT
        $print = $byProses->get('PRINT', collect());
        $printFirst = $firstByTanggal($print);
        $row['TGL PRINT'] = $tglFmt($printFirst);
        $row['BREAK'] = $breakLabel($printFirst);
        $row['TOTAL JAM'] = $sumField($print, 'totaljam');
        $row['OUTPUT PRINT'] = $sumField($print, 'outputdrik');
        $row['JT PRINT'] = $sumField($print, 'jtdrik');

        // SORTIR CETAK
        $sortirCetak = $byProses->get('SORTIR CETAK', collect());
        $sortirCetakFirst = $firstByTanggal($sortirCetak);
        $row['TGL SORTIRCETAK'] = $tglFmt($sortirCetakFirst);
        $row['OUTPUT SORTIRCETAK'] = $sumField($sortirCetak, 'outputdrik');
        $row['JT SORTIRCETAK'] = $sumField($sortirCetak, 'jtdrik');

        // Proses yang hanya butuh OUTPUT & JT
        $simpleProcesses = ['WATERBASE', 'HOCK', 'HOTPRINT', 'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING'];
        foreach ($simpleProcesses as $p) {
            $coll = $byProses->get($p, collect());
            $row["OUTPUT {$p}"] = $sumField($coll, 'outputdrik');
            $row["JT {$p}"] = $sumField($coll, 'jtdrik');
        }

        // LEM SETENGAH JADI
        $lemSetengah = $byProses->get('LEM SETENGAH JADI', collect());
        $row['TOTAL JAM LEM SETENGAH JADI'] = $sumField($lemSetengah, 'totaljam');
        $row['OUTPUT LEM SETENGAH JADI'] = $sumField($lemSetengah, 'outputdrik');
        $row['JT LEM SETENGAH JADI'] = $sumField($lemSetengah, 'jtdrik');

        // LEM
        $lem = $byProses->get('LEM', collect());
        $lemFirst = $firstByTanggal($lem);
        $row['JAM SET LEM'] = $jamFmt($lemFirst->set ?? null);
        $row['JAM RUN LEM'] = $jamFmt($lemFirst->run ?? null);
        $row['JAM FINISH LEM'] = $jamFmt($lemFirst->finish ?? null);
        $row['BREAK LEM'] = $breakLabel($lemFirst);
        $row['TOTAL JAM LEM'] = $sumField($lem, 'totaljam');
        $row['OUTPUT LEM'] = $sumField($lem, 'outputdrik');
        $row['JT LEM'] = $sumField($lem, 'jtdrik');

        // SORTIR LEM
        $sortirLem = $byProses->get('SORTIR LEM', collect());
        $row['TOTAL JAM SORTIR LEM'] = $sumField($sortirLem, 'totaljam');
        $row['OUTPUT SORTIR LEM'] = $sumField($sortirLem, 'outputdrik');
        $row['JT SORTIR LEM'] = $sumField($sortirLem, 'jtdrik');

        // SORTPACKING — dipecah jadi porsi SORTIR & PACKING, rumus sama seperti report()
        $sortpacking = $byProses->get('SORTPACKING', collect());
        $outputSortir = 0;
        $jtSortir = 0;
        $outputPacking = 0;
        foreach ($sortpacking as $item) {
            $itemInput = (float) str_replace('.', '', (string) ($item->input ?? 0));
            $itemUpspk = (float) str_replace('.', '', (string) ($item->upspk ?? 0));
            $itemJtpcs = (float) str_replace('.', '', (string) ($item->jtpcs ?? 0));

            $itemJtdrikSortir = $itemUpspk > 0 ? $itemJtpcs / $itemUpspk : 0;
            $itemOutputpcsSortir = $itemInput + $itemJtpcs;
            $itemOutputdrikSortir = $itemUpspk > 0 ? $itemOutputpcsSortir / $itemUpspk : 0;
            $itemOutputdrikPacking = $itemUpspk > 0 ? $itemInput / $itemUpspk : 0;

            $jtSortir += $itemJtdrikSortir;
            $outputSortir += $itemOutputdrikSortir;
            $outputPacking += $itemOutputdrikPacking;
        }
        $row['TOTAL JAM SORTPACKING'] = $sumField($sortpacking, 'totaljam');
        $row['OUTPUT SORTIR'] = $outputSortir;
        $row['JT SORTIR'] = $jtSortir;
        $row['OUTPUT PACKING'] = $outputPacking;

        return $row;
    }

    /** Kasih style header (bold + fill ungu + center) & freeze baris 1. */
    private function styleHeaderRow($sheet, int $columnCount): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($columnCount);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '696CFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->freezePane('A2');
    }

    private function autoSizeColumns($sheet, int $columnCount): void
    {
        for ($i = 1; $i <= $columnCount; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /** Stream file .xlsx langsung ke browser sebagai download. */
    private function streamExcel(Spreadsheet $spreadsheet, string $filename)
    {
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * EXPORT SUMMARY — satu baris per JOB, semua proses dipivot jadi kolom.
     * Job mana saja yang diikutkan mengikuti filter yang sedang aktif di
     * halaman index (job/docket/product/operator/proses/mesin/tanggal),
     * tapi begitu satu Job "kena" filter, SEMUA data Job itu (lintas
     * proses) tetap dipakai — sama seperti halaman Report.
     */
    public function exportSummary(Request $request)
    {
        $query = ProsesProduksi::query();
        $this->applyProsesFilters($query, $request);

        $jobIds = (clone $query)
            ->whereNotNull('job')
            ->where('job', '!=', '')
            ->distinct()
            ->orderBy('job')
            ->pluck('job');

        if ($jobIds->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        $headers = [
            'JOB', 'PRODUCT', 'DOCKET', 'PO', 'QTY',
            'TGL PRINT', 'BREAK', 'TOTAL JAM', 'OUTPUT PRINT', 'JT PRINT',
            'TGL SORTIRCETAK', 'OUTPUT SORTIRCETAK', 'JT SORTIRCETAK',
            'OUTPUT WATERBASE', 'JT WATERBASE',
            'OUTPUT HOCK', 'JT HOCK',
            'OUTPUT HOTPRINT', 'JT HOTPRINT',
            'OUTPUT LAMINASI', 'JT LAMINASI',
            'OUTPUT LAMINATING', 'JT LAMINATING',
            'OUTPUT EMBOSS', 'JT EMBOSS',
            'OUTPUT DIECUT', 'JT DIECUT',
            'OUTPUT CUTTING', 'JT CUTTING',
            'TOTAL JAM LEM SETENGAH JADI', 'OUTPUT LEM SETENGAH JADI', 'JT LEM SETENGAH JADI',
            'JAM SET LEM', 'JAM RUN LEM', 'JAM FINISH LEM', 'BREAK', 'TOTAL JAM LEM', 'OUTPUT LEM', 'JT LEM',
            'TOTAL JAM SORTIR LEM', 'OUTPUT SORTIR LEM', 'JT SORTIR LEM',
            'TOTAL JAM SORTPACKING', 'OUTPUT SORTIR', 'JT SORTIR', 'OUTPUT PACKING',
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary Production');
        $sheet->fromArray($headers, null, 'A1');
        $this->styleHeaderRow($sheet, count($headers));

        $rowNum = 2;
        foreach ($jobIds as $jobId) {
            $data = $this->summarizeJobRow($jobId);
            if (! $data) {
                continue;
            }

            $sheet->fromArray([
                $data['JOB'], $data['PRODUCT'], $data['DOCKET'], $data['PO'], $data['QTY'],
                $data['TGL PRINT'], $data['BREAK'], $data['TOTAL JAM'], $data['OUTPUT PRINT'], $data['JT PRINT'],
                $data['TGL SORTIRCETAK'], $data['OUTPUT SORTIRCETAK'], $data['JT SORTIRCETAK'],
                $data['OUTPUT WATERBASE'], $data['JT WATERBASE'],
                $data['OUTPUT HOCK'], $data['JT HOCK'],
                $data['OUTPUT HOTPRINT'], $data['JT HOTPRINT'],
                $data['OUTPUT LAMINASI'], $data['JT LAMINASI'],
                $data['OUTPUT LAMINATING'], $data['JT LAMINATING'],
                $data['OUTPUT EMBOSS'], $data['JT EMBOSS'],
                $data['OUTPUT DIECUT'], $data['JT DIECUT'],
                $data['OUTPUT CUTTING'], $data['JT CUTTING'],
                $data['TOTAL JAM LEM SETENGAH JADI'], $data['OUTPUT LEM SETENGAH JADI'], $data['JT LEM SETENGAH JADI'],
                $data['JAM SET LEM'], $data['JAM RUN LEM'], $data['JAM FINISH LEM'], $data['BREAK LEM'], $data['TOTAL JAM LEM'], $data['OUTPUT LEM'], $data['JT LEM'],
                $data['TOTAL JAM SORTIR LEM'], $data['OUTPUT SORTIR LEM'], $data['JT SORTIR LEM'],
                $data['TOTAL JAM SORTPACKING'], $data['OUTPUT SORTIR'], $data['JT SORTIR'], $data['OUTPUT PACKING'],
            ], null, 'A'.$rowNum);
            $rowNum++;
        }

        $this->autoSizeColumns($sheet, count($headers));

        return $this->streamExcel($spreadsheet, 'summary-production-'.now()->format('Ymd-His').'.xlsx');
    }

    /**
     * EXPORT MUTASI — satu baris per record mentah (tidak ditotal), sama
     * seperti data yang tampil di tabel index, mengikuti filter & sort yang
     * sedang aktif.
     */
    public function exportMutasi(Request $request)
    {
        $query = ProsesProduksi::query();
        $this->applyProsesFilters($query, $request);

        $allowedSorts = [
            'job' => 'job',
            'docket' => 'designno',
            'proses' => 'proses',
            'mesin' => 'mesin',
            'product' => 'product',
            'operator' => 'operator',
            'tanggal' => 'tanggal',
        ];
        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        if ($sort && isset($allowedSorts[$sort])) {
            $query->orderBy($allowedSorts[$sort], $dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        foreach ($records as $data) {
            $data->totaljam = $this->computeTotalJam($data);
            $this->calculateDerivedValues($data);
        }

        $headers = [
            'JOB', 'PRODUCT', 'DOCKET', 'PO', 'QTY', 'PROSES', 'MESIN', 'OPERATOR', 'TANGGAL',
            'JAM SET', 'JAM RUN', 'JAM FINISH', 'BREAK', 'TOTAL JAM',
            'UPSPK', 'INPUT', 'JTDRIK', 'JTPCS', 'OUTPUTDRIK', 'OUTPUTPCS',
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mutasi Production');
        $sheet->fromArray($headers, null, 'A1');
        $this->styleHeaderRow($sheet, count($headers));

        $rowNum = 2;
        foreach ($records as $data) {
            $sheet->fromArray([
                $data->job ?? '-',
                $data->product ?? '-',
                $data->designno ?? '-',
                $data->po ?? '-',
                (float) ($data->qty ?? 0),
                $data->proses ?? '-',
                $data->mesin ?? '-',
                $data->operator ?? '-',
                $data->tanggal ? Carbon::parse($data->tanggal)->format('d-m-Y') : '-',
                $data->set ? Carbon::parse($data->set)->format('H:i') : '-',
                $data->run ? Carbon::parse($data->run)->format('H:i') : '-',
                $data->finish ? Carbon::parse($data->finish)->format('H:i') : '-',
                (strtoupper((string) $data->break) === 'TRUE' || $data->break == 1) ? 'YA' : 'TIDAK',
                (float) ($data->totaljam ?? 0),
                (float) ($data->upspk ?? 0),
                (float) ($data->input ?? 0),
                (float) ($data->jtdrik ?? 0),
                (float) ($data->jtpcs ?? 0),
                (float) ($data->outputdrik ?? 0),
                (float) ($data->outputpcs ?? 0),
            ], null, 'A'.$rowNum);
            $rowNum++;
        }

        $this->autoSizeColumns($sheet, count($headers));

        return $this->streamExcel($spreadsheet, 'mutasi-production-'.now()->format('Ymd-His').'.xlsx');
    }
}
