<?php

namespace App\Http\Controllers\produksi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ProsesProduksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProsesProduksiController extends Controller
{
    public function calculateDerivedValues($record)
    {
        // Pastikan nilai input, jtdrik, dan upspk adalah angka sebelum melakukan perhitungan
        $input = (float) str_replace('.', '', (string) ($record->input ?? 0));
        $jtdrik = (float) str_replace('.', '', (string) ($record->jtdrik ?? 0));
        $upspk = (float) str_replace('.', '', (string) ($record->upspk ?? 0));
        $prosesName = strtolower((string) ($record->proses ?? ''));

        $record->input = $input;
        $record->jtdrik = $jtdrik;
        $record->upspk = $upspk;

        if ($prosesName === 'lem') {
            $record->jtdrik = $upspk > 0 ? $input / $upspk : 0;
            $record->jtpcs = $input;
            $record->outputdrik = $input - $record->jtdrik;
            $record->outputpcs = $record->outputdrik * $upspk;
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

    public function index(Request $request)
    {
        // 1.Tangkap input tanggal dari form filter
        $filterProses = $request->get('proses');
        $filterId = $request->get('id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $filterJob = $request->get('job');
        $filterOperator = $request->get('operator');
        $filterTanggal = $request->get('tanggal');
        $filterDocket = $request->get('designno');
        $filterProduct = $request->get('product');

        $query = ProsesProduksi::query();

        if (! empty($filterId)) {
            $query->where('id', $filterId);
        }
        if (! empty($filterProses)) {
            $query->where('proses', $filterProses);
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
            $query->where('designno', 'like', '%'.trim($filterDocket).'%');
        }
        if (! empty($filterProduct)) {

            $query->where('product', $productsList);

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
        $prosesProduksi = $query->paginate(9)->appends($request->query());

        foreach ($prosesProduksi as $data) {
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

            // Timpa langsung properti asli database
            $data->totaljam = max(0, round($totalJam, 2));
            $this->calculateDerivedValues($data);
        }

        // =================================================================
        // PERBAIKAN: Tarik daftar proses yang unik langsung dari database!
        // =================================================================
        $daftarProses = ProsesProduksi::select('proses')
            ->whereNotNull('proses')
            ->distinct()
            ->orderBy('proses')
            ->pluck('proses');

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

        return view('role.produksi.proses_produksi.index', compact('prosesProduksi', 'daftarProses', 'total'));
    }

    public function create()
    {
        $jobs = ProsesProduksi::select('job')
            ->whereNotNull('job')
            ->distinct()
            ->orderBy('job')
            ->get();

        return view('role.produksi.proses_produksi.create', compact('jobs'));
    }

    public function show(Request $request, $job_id)
    {
        // 1. CARI NOMOR DOCKET DARI JOB INI
        $firstRecord = ProsesProduksi::where('job', $job_id)->first();
        if (! $firstRecord) {
            abort(404, 'Job tidak ditemukan.');
        }
        $docket = $firstRecord->designno ?? '-';

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
                        return redirect()->route('proses-produksi.show', $job_id)
                            ->with('error', "Tidak ada Job ID yang cocok dengan pencarian '{$sJob}'.");
                    }

                    $matchedJobs = array_merge($matchedJobs, $jobsFound);
                }

                $matchedJobs = array_unique($matchedJobs);
                $validatedJobs = [];
                $commonDocket = null;

                foreach ($matchedJobs as $mJob) {
                    $checkRecord = ProsesProduksi::where('job', $mJob)->first();
                    if ($checkRecord) {
                        $jobDocket = $checkRecord->designno ?? '-';

                        if ($commonDocket === null) {
                            $commonDocket = $jobDocket;
                        } elseif ($jobDocket !== $commonDocket) {
                            return redirect()->route('proses-produksi.show', $job_id)
                                ->with('error', "Pencarian multi-job harus memiliki Docket/Design No yang sama. Job '{$mJob}' memiliki Docket '{$jobDocket}' yang berbeda dengan Docket '{$commonDocket}'.");
                        }

                        $validatedJobs[] = $mJob;
                    }
                }

                $jobsToQuery = $validatedJobs;
                $docket = $commonDocket;
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
                'PRETEL', 'LEM', 'LEM SETENGAH JADI', 'SORTIR', 'PACKING',
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

        // 3. HITUNG ATRIBUT VIRTUAL UNTUK SEMUA REKOR (agar rangkuman & grand total akurat)
        foreach ($allDetailProses as $data) {
            $calculateItem($data);
        }

        // Paginate detailProses (9 records per page)
        $detailProses = $detailQuery->paginate(9)->appends($request->query());
        foreach ($detailProses as $data) {
            $calculateItem($data);
        }

        // 4. BUAT TABEL RANGKUMAN (DOCKET-WIDE)
        $masterProses = [
            'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
            'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
            'PRETEL', 'LEM', 'SORTIR', 'PACKING',
        ];

        $rangkuman = [];

        foreach ($masterProses as $prosesName) {
            $jam = 0;
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
                    $jtdrik += 0;
                    $jtpcs += 0;
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
                $jtdrik = $dataPerProses->sum('jtdrik');
                $jtpcs = $dataPerProses->sum('jtpcs');
                $outputdrik = $dataPerProses->sum('outputdrik');
                $outputpcs = $dataPerProses->sum('outputpcs');
                $total_pengerjaan_drik = $dataPerProses->sum('total_pengerjaan_drik');
                $total_pengerjaan_pcs = $dataPerProses->sum('total_pengerjaan_pcs');
            }

            $rangkuman[] = [
                'proses' => $prosesName,
                'jam' => $jam,
                'jt_drik' => $jtdrik,
                'jt_pcs' => $jtpcs,
                'output_drik' => $outputdrik,
                'output_pcs' => $outputpcs,
                'total_pengerjaan_drik' => $total_pengerjaan_drik,
                'total_pengerjaan_pcs' => $total_pengerjaan_pcs,
                'selisih_drik' => 0,
                'selisih_pcs' => 0,
            ];
        }

        $total = [
            'input' => $allDetailProses->sum(fn ($x) => (float) ($x->input ?? 0)),
            'jtpcs' => $allDetailProses->sum(fn ($x) => (float) ($x->jtpcs ?? 0)),
            'jtdrik' => $allDetailProses->sum(fn ($x) => (float) ($x->jtdrik ?? 0)),
            'outputpcs' => $allDetailProses->sum(fn ($x) => (float) ($x->outputpcs ?? 0)),
            'outputdrik' => $allDetailProses->sum(fn ($x) => (float) ($x->outputdrik ?? 0)),
        ];

        // 5. KIRIM DATA KE BLADE
        return view('role.produksi.proses_produksi.show', compact('rangkuman', 'detailProses', 'job_id', 'docket', 'total', 'jobsToQuery'));
    }

    public function inlineUpdate(Request $request)
    {
        $allowedFields = ['input', 'jtdrik', 'jtpcs', 'upspk', 'shift', 'operator', 'set', 'run', 'finish', 'tanggal'];
        $stringFields = ['operator', 'set', 'run', 'finish', 'tanggal'];

        $valueRules = 'required';
        if (in_array($request->field, ['set', 'run', 'finish'], true)) {
            $valueRules = 'nullable|string';
        } elseif (in_array($request->field, $stringFields, true)) {
            $valueRules = 'required|string';
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

        if ($field === 'jtdrik' && $prosesName === 'lem') {
            return response()->json([
                'success' => false,
                'message' => 'Kolom JT Drik untuk proses LEM tidak dapat diinput secara inline.',
            ], 422);
        }

        if ($field === 'jtpcs' && ! in_array($prosesName, ['lem', 'sortpacking'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom JT PCS hanya dapat diubah untuk proses LEM dan SORTPACKING.',
            ], 422);
        }

        if ($prosesName === 'lem' && $field === 'jtpcs') {
            $saveField = 'input';
        }

        // Validation: JT Drik cannot be greater than Input
        if ($prosesName !== 'lem' && ! in_array($field, $stringFields, true)) {
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

        // 1. Simpan nilai lama sebelum diubah oleh database
        $oldValue = $record->{$saveField};

        // 2. Siapkan nilai baru yang sudah diformat sesuai tipe datanya
        $formattedNewValue = in_array($saveField, $stringFields, true)
            ? (empty($value) || $value === '-' ? null : $value)
            : (float) $value;

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
                'shift' => (float) $record->shift,
                'operator' => $record->operator,
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

        } catch (ValidationException $e) {
            // Jika validasi gagal, kembalikan seperti biasa
            return back()->withErrors($e->errors())->withInput();
        }

        try {

            DB::transaction(function () use ($validated, $rowsCount) {

                $dataInsert = [];

                for ($i = 0; $i < $rowsCount; $i++) {

                    $row = [];

                    foreach ($validated as $field => $values) {
                        $row[$field] = $values[$i] ?? null;
                    }

                    $row['created_at'] = now();
                    $row['updated_at'] = now();

                    $dataInsert[] = $row;
                }

                ProsesProduksi::insert($dataInsert);
            });

            return back()->with('success', $rowsCount.' data berhasil disimpan.');

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function searchSuggestions(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));
        $type = trim((string) $request->input('type', 'job'));

        $allowedTypes = [
            'job' => 'job',
            'designno' => 'designno',
            'product' => 'product',
            'operator' => 'operator',
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
}
