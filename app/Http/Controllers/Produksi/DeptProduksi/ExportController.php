<?php

namespace App\Http\Controllers\Produksi\DeptProduksi;

use App\Http\Controllers\Controller;
use App\Models\ProsesProduksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
// format csv
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function exportProductionSummary(Request $request)
    {
        // Urutan proses "reguler" yang masing-masing dapat 5 kolom sendiri:
        // TOTAL JAM | OUTPUT DRIK | OUTPUT PCS | JT DRIK | JT PCS
        // (PRETEL sengaja tidak dimasukkan karena tidak diminta di kolom export)
        $processOrder = [
            'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
            'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
            'LEM',
        ];

        // Label kolom yang ditampilkan (kalau beda dengan nama proses di database)
        $processLabel = [
            'PRINT' => 'CETAK',
            'SORTIR CETAK' => 'SORTIRCETAK',
            'LEM SETENGAH JADI' => 'HALF GLUE',
            'LEM' => 'LEM',
            'SORTIR LEM' => 'SORTIR GLUE',
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary Production');

        // ===================== HEADER =====================
        $headers = ['', 'JOB', 'CUST', 'PRODUCT', 'DOCKET', 'PO', 'QTY ORDER'];

        foreach ($processOrder as $i => $prosesName) {
            $label = $processLabel[$prosesName] ?? $prosesName;
            if ($i === 0) {
                // Kolom pertama (PRINT) dapat tambahan kolom tanggal di depannya
                $headers[] = 'TGL START';
            }
            if ($prosesName === 'DIECUT') {
                $headers[] = 'START DIECUT';
            }
            if ($prosesName === 'PRINT') {
                $headers[] = 'OUTPUT '.$label;
            } elseif ($prosesName === 'SORTIR CETAK') {
                $headers[] = 'JT SORTIR CETAK';
                $headers[] = '% JT CETAK';
            } elseif ($prosesName === 'CUTTING') {
                $headers[] = 'OUTPUT '.$label;
            } else {
                // $headers[] = 'OUTPUT PCS '.$label;
                $headers[] = 'OUTPUT '.$label;
                // $headers[] = 'OUTPUT PCS '.$label;
                $headers[] = 'JT '.$label;
                $headers[] = '% JT '.$label;
            }
        }

        $headers[] = 'OUTPUT SORTIR';
        $headers[] = 'JT SORTIR';
        $headers[] = '% JT SORT';
        $headers[] = 'OUTPUT PACKING';
        $headers[] = 'TGL FINISH';
        $headers[] = 'JT ALL PROSES';
        $headers[] = '% JT ALL PROSES';
        $headers[] = 'INPUT CETAK';
        $headers[] = 'TOTAL KIRIM';

        $sheet->fromArray($headers, null, 'A1');

        // ===================== DATA =====================
        $jobs = ProsesProduksi::select('job')
            ->whereNotNull('job')
            ->where('job', '!=', '')
            ->distinct()
            ->orderBy('job')
            ->pluck('job');

        $row = 2;

        foreach ($jobs as $job) {
            $records = ProsesProduksi::where('job', $job)->get();

            if ($records->isEmpty()) {
                continue;
            }

            // Hitung nilai turunan (totaljam, outputdrik, outputpcs, dst) untuk
            // setiap baris — persis logika yang dipakai di halaman index/report.
            foreach ($records as $r) {
                $this->calcTotalJamExport($r);
                $this->calcDerivedValuesExport($r);
                $this->applyReportOverridesExport($r);
            }

            $firstRecord = $records->first();

            // ---- Rekap per proses reguler ----
            $prosesData = [];

            foreach ($processOrder as $prosesName) {
                $filtered = $records->filter(function ($item) use ($prosesName) {
                    $p = strtoupper(trim((string) $item->proses));
                    if ($prosesName === 'SORTIR CETAK') {
                        return $p === 'SORTIR CETAK' || $p === 'SORTIRCETAK';
                    }

                    return $p === $prosesName;
                });

                $prosesData[$prosesName] = [
                    'jam' => $filtered->sum('totaljam'),
                    'jt_drik' => $filtered->sum('jtdrik'),
                    'jt_pcs' => $filtered->sum('jtpcs'),
                    'output_drik' => $filtered->sum('outputdrik'),
                    'output_pcs' => $filtered->sum('outputpcs'),
                ];
            }

            // SORTIR (gabungan SORTIR + SORTPACKING)
            $sortirItems = $records->filter(function ($item) {
                $p = strtoupper(trim((string) $item->proses));

                return $p === 'SORTIR' || $p === 'SORTPACKING';
            });
            $sortirJtDrik = 0;
            $sortirJtPcs = 0;
            $sortirOutputDrik = 0;
            $sortirOutputPcs = 0;
            foreach ($sortirItems as $item) {
                $itemInput = (float) str_replace('.', '', (string) ($item->input ?? 0));
                $itemUpspk = (float) str_replace('.', '', (string) ($item->upspk ?? 0));
                $itemJtpcs = (float) str_replace('.', '', (string) ($item->jtpcs ?? 0));

                $itemJtdrik = $itemUpspk > 0 ? $itemJtpcs / $itemUpspk : 0;
                $itemOutputpcs = $itemInput + $itemJtpcs;
                $itemOutputdrik = $itemUpspk > 0 ? $itemOutputpcs / $itemUpspk : 0;

                $sortirJtDrik += $itemJtdrik;
                $sortirJtPcs += $itemJtpcs;
                $sortirOutputDrik += $itemOutputdrik;
                $sortirOutputPcs += $itemOutputpcs;
            }

            // PACKING (gabungan PACKING + SORTPACKING)
            $packingItems = $records->filter(function ($item) {
                $p = strtoupper(trim((string) $item->proses));

                return $p === 'PACKING' || $p === 'SORTPACKING';
            });
            $packingOutputDrik = 0;
            $packingOutputPcs = 0;
            foreach ($packingItems as $item) {
                $itemInput = (float) str_replace('.', '', (string) ($item->input ?? 0));
                $itemUpspk = (float) str_replace('.', '', (string) ($item->upspk ?? 0));

                $packingOutputPcs += $itemInput;
                $packingOutputDrik += $itemUpspk > 0 ? $itemInput / $itemUpspk : 0;
            }

            // SORTPACKING (khusus, hanya proses SORTPACKING murni — untuk jam & tanggal)
            $sortpackingItems = $records->filter(function ($item) {
                return strtoupper(trim((string) $item->proses)) === 'SORTPACKING';
            });
            $sortpackingJam = $sortpackingItems->sum('totaljam');
            $sortpackingFirstDate = $sortpackingItems
                ->filter(fn ($item) => ! empty($item->tanggal))
                ->sortBy('tanggal')
                ->first();

            $printFirstDate = $records
                ->filter(fn ($item) => strtoupper(trim((string) $item->proses)) === 'PRINT' && ! empty($item->tanggal))
                ->sortBy('tanggal')
                ->first();

            $diecutFirstDate = $records
                ->filter(fn ($item) => strtoupper(trim((string) $item->proses)) === 'DIECUT' && ! empty($item->tanggal))
                ->sortBy('tanggal')
                ->first();

            $lastDate = $records
                ->filter(fn ($item) => ! empty($item->tanggal))
                ->sortByDesc('tanggal')
                ->first();

            $inputCetak = $records
                ->filter(fn ($item) => strtoupper(trim((string) $item->proses)) === 'PRINT')
                ->sum(function ($r) {
                    $itemInput = (float) str_replace('.', '', (string) ($r->input ?? 0));
                    $itemUpspk = (float) str_replace('.', '', (string) ($r->upspk ?? 0));

                    return $itemInput * $itemUpspk;
                });

            // ===================== TULIS BARIS =====================
            $col = 'A';

            $sheet->setCellValue($col.$row, '');
            $col++;
            $sheet->setCellValue($col.$row, $job);
            $col++;
            $sheet->setCellValue($col.$row, ''); // CUSTOMER
            $col++;
            $sheet->setCellValue($col.$row, $firstRecord->product ?? '-');
            $col++;
            $sheet->setCellValue($col.$row, $firstRecord->designno ?? '-');
            $col++;
            $sheet->setCellValue($col.$row, $firstRecord->po ?? '-');
            $col++;
            $sheet->setCellValue($col.$row, $firstRecord->qty ?? 0);
            $col++;

            $totalJtAllProses = 0;

            foreach ($processOrder as $i => $prosesName) {
                if ($i === 0) {
                    $sheet->setCellValue($col.$row, $printFirstDate && $printFirstDate->tanggal
                        ? Carbon::parse($printFirstDate->tanggal)->format('d-m-Y')
                        : '-');
                    $col++;
                }

                if ($prosesName === 'DIECUT') {
                    $sheet->setCellValue($col.$row, $diecutFirstDate && $diecutFirstDate->tanggal
                        ? Carbon::parse($diecutFirstDate->tanggal)->format('d-m-Y')
                        : '-');
                    $col++;
                }

                $data = $prosesData[$prosesName];

                if ($prosesName === 'PRINT') {
                    $sheet->setCellValue($col.$row, round($data['output_pcs'], 0));
                    $col++;
                } elseif ($prosesName === 'SORTIR CETAK') {
                    $val = (float) ($data['jt_pcs'] ?? 0);
                    $totalJtAllProses += $val;
                    $sheet->setCellValue($col.$row, round($val, 0));
                    $col++;
                    $sheet->setCellValue($col.$row, round(0));
                    $col++;
                } elseif ($prosesName === 'CUTTING') {
                    $sheet->setCellValue($col.$row, round($data['output_pcs'], 0));
                    $col++;
                } else {
                    $sheet->setCellValue($col.$row, round($data['output_pcs'], 0));
                    $col++;
                    if ($prosesName === 'HOTPRINT') {
                        $val = (float) ($data['jt_pcs'] ?? 0);
                        $totalJtAllProses += $val;
                        $sheet->setCellValue($col.$row, round($val, 0));
                    } else {
                        $val = (float) ($data['jt_pcs'] ?? 0);
                        $totalJtAllProses += $val;
                        $sheet->setCellValue($col.$row, round($val, 0));
                    }
                    $col++;
                    $sheet->setCellValue($col.$row, round(0));
                    $col++;
                }
            }

            $sheet->setCellValue($col.$row, round($sortirOutputDrik, 0));
            $col++;
            $totalJtAllProses += (float) $sortirJtPcs;
            $sheet->setCellValue($col.$row, round($sortirJtPcs, 0));
            $col++;
            $sheet->setCellValue($col.$row, round(0));
            $col++;
            $sheet->setCellValue($col.$row, round($packingOutputDrik, 0));
            $col++;
            $sheet->setCellValue($col.$row, $lastDate && $lastDate->tanggal
                ? Carbon::parse($lastDate->tanggal)->format('d-m-Y')
                : '-');
            $col++;
            $sheet->setCellValue($col.$row, round($totalJtAllProses, 0));
            $col++;
            $sheet->setCellValue($col.$row, round(0));
            $col++;
            $sheet->setCellValue($col.$row, round($inputCetak, 0));
            $col++;
            $sheet->setCellValue($col.$row, round(0));

            $row++;
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            if (ob_get_length()) {
                ob_end_clean();
            }

            // $writer = new Xlsx($spreadsheet);
            $writer = new Csv($spreadsheet);
            $writer->save('php://output');
            exit;

        }, 'summary_production_'.date('Ymd_His').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * EXPORT 2: MUTASI PRODUCTION
     * Semua baris inputan proses produksi (tidak ditotal), untuk seluruh job.
     * (Tidak berubah dari sebelumnya — di sini drik/pcs memang sudah kolom
     * terpisah sejak awal karena ini per baris data mentah, bukan rekap.)
     */
    public function exportProductionMutasi(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mutasi Production');

        $headers = [
            'JOB', 'PRODUCT', 'DOCKET', 'PO', 'QTY',
            'PROSES', 'MESIN', 'SHIFT', 'OPERATOR', 'TANGGAL',
            'JAM SET', 'JAM RUN', 'JAM FINISH', 'BREAK', 'TOTAL JAM',
            'UPSPK', 'INPUT', 'JTDRIK', 'JTPCS', 'OUTPUTDRIK', 'OUTPUTPCS',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $row = 2;

        ProsesProduksi::orderBy('id', 'desc')->chunk(500, function ($chunk) use ($sheet, &$row) {
            foreach ($chunk as $data) {
                $this->calcTotalJamExport($data);
                $this->calcDerivedValuesExport($data);

                $sheet->setCellValue('A'.$row, $data->job ?? '-');
                $sheet->setCellValue('B'.$row, $data->product ?? '-');
                $sheet->setCellValue('C'.$row, $data->designno ?? '-');
                $sheet->setCellValue('D'.$row, $data->po ?? '-');
                $sheet->setCellValue('E'.$row, $data->qty ?? 0);
                $sheet->setCellValue('F'.$row, $data->proses ?? '-');
                $sheet->setCellValue('G'.$row, $data->mesin ?? '-');
                $sheet->setCellValue('H'.$row, $data->shift ?? '-');
                $sheet->setCellValue('I'.$row, $data->operator ?? '-');
                $sheet->setCellValue('J'.$row, $data->tanggal ? Carbon::parse($data->tanggal)->format('d-m-Y') : '-');
                $sheet->setCellValue('K'.$row, $data->set ? Carbon::parse($data->set)->format('d-m-Y H:i') : '-');
                $sheet->setCellValue('L'.$row, $data->run ? Carbon::parse($data->run)->format('d-m-Y H:i') : '-');
                $sheet->setCellValue('M'.$row, $data->finish ? Carbon::parse($data->finish)->format('d-m-Y H:i') : '-');
                $sheet->setCellValue('N'.$row, $data->break ?? '-');
                $sheet->setCellValue('O'.$row, $data->totaljam ?? 0);
                $sheet->setCellValue('P'.$row, $data->upspk ?? 0);
                $sheet->setCellValue('Q'.$row, $data->input ?? 0);
                $sheet->setCellValue('R'.$row, $data->jtdrik ?? 0);
                $sheet->setCellValue('S'.$row, $data->jtpcs ?? 0);
                $sheet->setCellValue('T'.$row, $data->outputdrik ?? 0);
                $sheet->setCellValue('U'.$row, $data->outputpcs ?? 0);

                $row++;
            }
        });

        return response()->streamDownload(function () use ($spreadsheet) {
            if (ob_get_length()) {
                ob_end_clean();
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        }, 'mutasi_production_'.date('Ymd_His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * EXPORT 3: MUTASI PRODUCTION (FILTERED)
     * Mengexport data mutasi berdasarkan filter yang aktif di halaman index.
     */
    public function exportProductionMutasiFiltered(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mutasi Production Filter');

        $headers = [
            'JOB', 'PRODUCT', 'DOCKET', 'PO', 'QTY',
            'PROSES', 'MESIN', 'SHIFT', 'OPERATOR', 'TANGGAL',
            'JAM SET', 'JAM RUN', 'JAM FINISH', 'BREAK', 'TOTAL JAM',
            'UPSPK', 'INPUT', 'JTDRIK', 'JTPCS', 'OUTPUTDRIK', 'OUTPUTPCS',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $row = 2;

        $query = ProsesProduksi::query();
        $this->applyFilters($request, $query);

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

        $query->chunk(500, function ($chunk) use ($sheet, &$row) {
            foreach ($chunk as $data) {
                $this->calcTotalJamExport($data);
                $this->calcDerivedValuesExport($data);

                $sheet->setCellValue('A'.$row, $data->job ?? '-');
                $sheet->setCellValue('B'.$row, $data->product ?? '-');
                $sheet->setCellValue('C'.$row, $data->designno ?? '-');
                $sheet->setCellValue('D'.$row, $data->po ?? '-');
                $sheet->setCellValue('E'.$row, $data->qty ?? 0);
                $sheet->setCellValue('F'.$row, $data->proses ?? '-');
                $sheet->setCellValue('G'.$row, $data->mesin ?? '-');
                $sheet->setCellValue('H'.$row, $data->shift ?? '-');
                $sheet->setCellValue('I'.$row, $data->operator ?? '-');
                $sheet->setCellValue('J'.$row, $data->tanggal ? Carbon::parse($data->tanggal)->format('d-m-Y') : '-');
                $sheet->setCellValue('K'.$row, $data->set ? Carbon::parse($data->set)->format('d-m-Y H:i') : '-');
                $sheet->setCellValue('L'.$row, $data->run ? Carbon::parse($data->run)->format('d-m-Y H:i') : '-');
                $sheet->setCellValue('M'.$row, $data->finish ? Carbon::parse($data->finish)->format('d-m-Y H:i') : '-');
                $sheet->setCellValue('N'.$row, $data->break ?? '-');
                $sheet->setCellValue('O'.$row, $data->totaljam ?? 0);
                $sheet->setCellValue('P'.$row, $data->upspk ?? 0);
                $sheet->setCellValue('Q'.$row, $data->input ?? 0);
                $sheet->setCellValue('R'.$row, $data->jtdrik ?? 0);
                $sheet->setCellValue('S'.$row, $data->jtpcs ?? 0);
                $sheet->setCellValue('T'.$row, $data->outputdrik ?? 0);
                $sheet->setCellValue('U'.$row, $data->outputpcs ?? 0);

                $row++;
            }
        });

        return response()->streamDownload(function () use ($spreadsheet) {
            if (ob_get_length()) {
                ob_end_clean();
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        }, 'mutasi_production_filtered_'.date('Ymd_His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Terapkan filter pencarian yang sama dengan indexdata.
     */
    private function applyFilters(Request $request, $query)
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
            $productsList = array_map('trim', $productsList);
            $productsList = array_filter($productsList);
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
    }

    // ────────────────────────────────────────────────────────────────────────────
    // HELPER PRIVATE — copy juga ke dalam class ExportController
    // ────────────────────────────────────────────────────────────────────────────

    /**
     * Hitung TOTAL JAM dari set/run/finish (dikurangi 1 jam kalau break aktif).
     * Sama persis dengan logika di SpreadsheetController@indexdata / @report.
     */
    private function calcTotalJamExport($record)
    {
        $totalJam = 0;

        if (! empty($record->finish) && (! empty($record->set) || ! empty($record->run))) {
            $waktuMulaiString = ! empty($record->set) ? $record->set : $record->run;
            $waktuMulai = Carbon::parse($waktuMulaiString);
            $waktuFinish = Carbon::parse($record->finish);

            $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
            $totalJam = $selisihMenit / 60;

            if (strtoupper((string) $record->break) === 'TRUE' || $record->break == 1) {
                $totalJam -= 1;
            }
        }

        $record->totaljam = max(0, round($totalJam, 2));

        return $record;
    }

    /**
     * Hitung jtdrik/jtpcs/outputdrik/outputpcs/total_pengerjaan_* .
     * Sama persis dengan SpreadsheetController@calculateDerivedValues.
     */
    private function calcDerivedValuesExport($record)
    {
        $input = (float) str_replace('.', '', (string) ($record->input ?? 0));
        $jtdrik = (float) str_replace('.', '', (string) ($record->jtdrik ?? 0));
        $upspk = (float) str_replace('.', '', (string) ($record->upspk ?? 0));
        $jtpcs = (float) str_replace('.', '', (string) ($record->jtpcs ?? 0));
        $prosesName = strtolower((string) ($record->proses ?? ''));

        $record->jtpcs = $jtpcs;
        $record->input = $input;
        $record->jtdrik = $jtdrik;
        $record->upspk = $upspk;

        if (in_array($prosesName, ['lem', 'lem setengah jadi', 'sortir lem'])) {
            $record->jtdrik = $upspk > 0 ? $record->jtpcs / $upspk : 0;
            $record->outputpcs = $input - $record->jtpcs;
            $record->outputdrik = $upspk > 0 ? $record->outputpcs / $upspk : 0;
            $record->total_pengerjaan_drik = $record->jtdrik + $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->jtpcs + $record->outputpcs;

            return $record;
        }

        if ($prosesName === 'sortpacking') {
            $record->outputpcs = $input;
            $record->outputdrik = $upspk > 0 ? $record->outputpcs / $upspk : 0;
            $record->total_pengerjaan_drik = $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->outputpcs;

            return $record;
        }

        $record->jtpcs = $jtdrik * $upspk;
        $record->outputdrik = $input - $jtdrik;
        $record->outputpcs = $record->outputdrik * $upspk;
        $record->total_pengerjaan_drik = $jtdrik + $record->outputdrik;
        $record->total_pengerjaan_pcs = $record->jtpcs + $record->outputpcs;

        return $record;
    }

    /**
     * Override khusus untuk PACKING / SORTIR / SORTPACKING — sama persis dengan
     * yang dipakai di SpreadsheetController@report (halaman rangkuman), supaya
     * angka di export SUMMARY konsisten dengan yang tampil di halaman rangkuman.
     * (Export MUTASI tidak memakai ini — dia hanya pakai calcDerivedValuesExport,
     * sama seperti halaman index/indexdata.)
     */
    private function applyReportOverridesExport($record)
    {
        $procName = strtoupper(trim((string) $record->proses));
        $input = (float) str_replace('.', '', (string) ($record->input ?? 0));
        $upspk = (float) str_replace('.', '', (string) ($record->upspk ?? 0));

        if ($procName === 'PACKING') {
            $record->outputdrik = $upspk > 0 ? $input / $upspk : 0;
            $record->outputpcs = $input;
            $record->total_pengerjaan_drik = $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->outputpcs;
        } elseif ($procName === 'SORTIR') {
            $jtpcs = (float) str_replace('.', '', (string) ($record->jtpcs ?? 0));
            $record->jtdrik = $upspk > 0 ? $jtpcs / $upspk : 0;
            $record->outputpcs = $input + $jtpcs;
            $record->outputdrik = $upspk > 0 ? $record->outputpcs / $upspk : 0;
            $record->total_pengerjaan_drik = $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->outputpcs;
        } elseif ($procName === 'SORTPACKING') {
            $jtpcs = (float) str_replace('.', '', (string) ($record->jtpcs ?? 0));
            $record->jtdrik = $upspk > 0 ? $jtpcs / $upspk : 0;
            $record->outputpcs = (2 * $input) + $jtpcs;
            $record->outputdrik = $upspk > 0 ? $record->outputpcs / $upspk : 0;
            $record->total_pengerjaan_drik = $record->outputdrik;
            $record->total_pengerjaan_pcs = $record->outputpcs;
        }

        return $record;
    }

    private function downloadResponse($spreadsheet, $fileName)
    {
        $writer = new Xlsx($spreadsheet);

        // Hapus semua buffer teks/error PHP tersembunyi agar file tidak corrupt
        if (ob_get_contents()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit; // Menghentikan script dengan tegas agar tidak ada teks baru masuk
    }
}
