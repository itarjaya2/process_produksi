<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\ProsesProduksi; // <-- Sesuaikan path ke model Anda
use Carbon\Carbon;

class ProcessSpreadsheetAndDbJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $validated;

    /**
     * Create a new job instance.
     */
    public function __construct($validated)
    {
        $this->validated = $validated;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Ambil data dari constructor
        $validated = $this->validated;
        $rowsCount = count($validated['proses'] ?? 0);
        if ($rowsCount === 0) {
            Log::info("ProcessSpreadsheetAndDbJob: Tidak ada data untuk diproses.");
            return;
        }

        // --- SEMUA MAPPING ANDA DISALIN LENGKAP ---
        $mapping = [
            'PRINT' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'tanggal', 'P' => 'mesin', 'Q' => 'operator',
                'R' => 'shift', 'S' => 'set', 'T' => 'run', 'U' => 'finish', 'V' => 'break',
                'W' => 'totaljam', 'X' => 'input', 'Y' => 'ket', 'Z' => 'jtdrik',
                'AB' => 'karantina', 'AC' => 'outputdrik', 'AE' => 'target',
            ],
            'WATERBASE' => [
                'C' => 'job', 'O' => 'tanggal', 'P' => 'shift', 'Q' => 'operator',
                'R' => 'set', 'S' => 'run', 'T' => 'finish', 'U' => 'break',
                'V' => 'totaljam', 'W' => 'input', 'X' => 'ket', 'Y' => 'jtdrik', 'AC' => 'target',
            ],
            'HOCK' => [
                'C' => 'job', 'H' => 'upspk', 'M' => 'qtyspk', 'O' => 'tanggal',
                'P' => 'shift', 'Q' => 'operator', 'R' => 'set', 'S' => 'run',
                'T' => 'finish', 'U' => 'break', 'V' => 'totaljam', 'W' => 'input',
                'X' => 'ket', 'Y' => 'jtdrik', 'AC' => 'target',
            ],
            'HOTPRINT' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'tanggal', 'P' => 'shift',
                'Q' => 'operator', 'R' => 'set', 'S' => 'run', 'T' => 'finish',
                'U' => 'break', 'V' => 'totaljam', 'W' => 'input',
                'X' => 'jtdrik', 'AB' => 'target',
            ],
            'LAMINASI' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'tanggal', 'P' => 'vendormat',
                'Q' => 'shift', 'R' => 'operator', 'S' => 'set', 'T' => 'run',
                'U' => 'finish', 'V' => 'break', 'W' => 'totaljam', 'X' => 'input',
                'Y' => 'ket', 'Z' => 'jtdrik', 'AD' => 'target',
            ],
            'LAMINATING' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'tanggal', 'P' => 'shift',
                'Q' => 'palet', 'R' => 'operator', 'S' => 'set', 'T' => 'run',
                'U' => 'finish', 'V' => 'break', 'W' => 'totaljam',
                'X' => 'input', 'Y' => 'jtdrik', 'AC' => 'target',
            ],
            'EMBOSS' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'mesin', 'Q' => 'tanggal',
                'R' => 'shift', 'S' => 'operator', 'T' => 'set', 'U' => 'run',
                'V' => 'finish', 'W' => 'break', 'X' => 'totaljam',
                'Y' => 'input', 'Z' => 'jtdrik', 'AD' => 'target',
            ],
            'DIECUT' => [
                'C' => 'job', 'H' => 'upspk', 'P' => 'mesin', 'O' => 'tanggal',
                'Q' => 'shift', 'R' => 'operator', 'S' => 'set', 'T' => 'run',
                'U' => 'finish', 'V' => 'break', 'W' => 'totaljam',
                'X' => 'input', 'Z' => 'jtdrik', 'AD' => 'target',
            ],
            'CUTTING' => [
                'C' => 'job', 'H' => 'upspk', 'P' => 'tanggal', 'Q' => 'shift',
                'R' => 'operator', 'S' => 'set', 'T' => 'run', 'U' => 'finish',
                'V' => 'break', 'W' => 'totaljam', 'X' => 'input',
                'Y' => 'ket', 'Z' => 'jtdrik', 'AD' => 'target',
            ],
            'PRETEL' => [
                'C' => 'job', 'H' => 'upspk', 'P' => 'tanggal', 'Q' => 'shift',
                'R' => 'operator', 'S' => 'set', 'T' => 'run', 'U' => 'finish',
                'V' => 'break', 'W' => 'totaljam', 'X' => 'toleransi', 'Y' => 'input',
                'Z' => 'ok', 'AA' => 'karantina', 'AB' => 'jtpcs', 'AF' => 'target',
            ],
            'LEM' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'tanggal', 'P' => 'shift',
                'Q' => 'operator', 'R' => 'mesin', 'S' => 'set', 'T' => 'run',
                    'U' => 'finish', 'V' => 'break', 'W' => 'totaljam', 'X' => 'input',
                'Y' => 'ket', 'Z' => 'jtpcs', 'AD' => 'target',
            ],
            'LEM SETENGAH JADI' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'tanggal', 'P' => 'shift',
                'Q' => 'operator', 'R' => 'mesin', 'S' => 'set', 'T' => 'run',
                'U' => 'finish', 'V' => 'break', 'W' => 'totaljam', 'X' => 'input',
                'Y' => 'ket', 'Z' => 'jtpcs', 'AD' => 'target',
            ],
            'SORTPACKING' => [
                'C' => 'job', 'H' => 'upspk', 'O' => 'mesin', 'P' => 'tanggal', 'Q' => 'shift',
                'R' => 'operator', 'S' => 'run', 'T' => 'finish', 'U' => 'break',
                'V' => 'totaljam', 'W' => 'input', 'X' => 'warna', 'Y' => 'banjir',
                'Z' => 'beset', 'AA' => 'notok', 'AB' => 'powder', 'AC' => 'wb',
                'AD' => 'uvkasar', 'AE' => 'uvmbleset', 'AF' => 'tidakuv', 'AG' => 'hotprint',
                'AI' => 'laminating', 'AJ' => 'laminasikurang', 'AK' => 'laminasi',
                'AL' => 'tidakpresisi', 'AM' => 'pecah', 'AN' => 'emboss', 'AO' => 'porforasi',
                'AP' => 'sobek', 'AQ' => 'lengket', 'AR' => 'll', 'AY' => 'noteoperator',
            ],
        ];


        try {
            // Inisialisasi Google Service
            $client = $this->getClient(); // Panggil helper di bawah
            $service = new \Google\Service\Sheets($client);
            $spreadsheetId = env('GOOGLE_SHEET_ID');

            // --- OPTIMASI 1: BATCH DATABASE INSERT ---
            $dbInsertData = [];
            $dataBySheet = []; // Untuk mengelompokkan data per sheet
            $now = Carbon::now();

            for ($i = 0; $i < $rowsCount; $i++) {
                // Build row data (LENGKAP SEMUA FIELD)
                $rowData = [
                    'proses' => $validated['proses'][$i] ?? '',
                    'job' => $validated['job'][$i] ?? '',
                    'product' => $validated['product'][$i] ?? '',
                    'designno' => $validated['designno'][$i] ?? '',
                    'po' => $validated['po'][$i] ?? '',
                    'qty' => $validated['qty'][$i] ?? '',
                    'pengawas' => $validated['pengawas'][$i] ?? '',
                    'shiftpengawas' => $validated['shiftpengawas'][$i] ?? '',
                    'upspk' => $validated['upspk'][$i] ?? '',
                    'tanggal' => $validated['tanggal'][$i] ?? '',
                    'mesin' => $validated['mesin'][$i] ?? '',
                    'vendormat' => $validated['vendormat'][$i] ?? '',
                    'shift' => $validated['shift'][$i] ?? '',
                    'palet' => $validated['palet'][$i] ?? '',
                    'set' => $validated['set'][$i] ?? '',
                    'operator' => $validated['operator'][$i] ?? '',
                    'jumlahtim' => $validated['jumlahtim'][$i] ?? '',
                    'run' => $validated['run'][$i] ?? '',
                    'finish' => $validated['finish'][$i] ?? '',
                    'break' => $validated['break'][$i] ?? '',
                    'totaljam' => $validated['totaljam'][$i] ?? '',
                    'input' => $validated['input'][$i] ?? '',
                    'ket' => $validated['ket'][$i] ?? '',
                    'jtdrik' => $validated['jtdrik'][$i] ?? '',
                    'target' => $validated['target'][$i] ?? '',
                    'karantina' => $validated['karantina'][$i] ?? '',
                    'outputdrik' => $validated['outputdrik'][$i] ?? '',
                    'type' => $validated['type'][$i] ?? '',
                    'toleransi' => $validated['toleransi'][$i] ?? '',
                    'ok' => $validated['ok'][$i] ?? '',
                    'jtpcs' => $validated['jtpcs'][$i] ?? '',
                    // SORTPACKING fields
                    'warna' => $validated['warna'][$i] ?? '',
                    'banjir' => $validated['banjir'][$i] ?? '',
                    'beset' => $validated['beset'][$i] ?? '',
                    'notok' => $validated['notok'][$i] ?? '',
                    'powder' => $validated['powder'][$i] ?? '',
                    'wb' => $validated['wb'][$i] ?? '',
                    'uvkasar' => $validated['uvkasar'][$i] ?? '',
                    'uvmbleset' => $validated['uvmbleset'][$i] ?? '',
                    'tidakuv' => $validated['tidakuv'][$i] ?? '',
                    'hotprint' => $validated['hotprint'][$i] ?? '',
                    'laminating' => $validated['laminating'][$i] ?? '',
                    'laminasikurang' => $validated['laminasikurang'][$i] ?? '',
                    'laminasi' => $validated['laminasi'][$i] ?? '',
                    'tidakpresisi' => $validated['tidakpresisi'][$i] ?? '',
                    'pecah' => $validated['pecah'][$i] ?? '',
                    'emboss' => $validated['emboss'][$i] ?? '',
                    'porforasi' => $validated['porforasi'][$i] ?? '',
                    'sobek' => $validated['sobek'][$i] ?? '',
                    'lengket' => $validated['lengket'][$i] ?? '',
                    'll' => $validated['ll'][$i] ?? '',
                    'noteoperator' => $validated['noteoperator'][$i] ?? '',
                ];

                // Tambahkan data untuk batch insert DB
                $dbInsertData[] = $rowData + ['created_at' => $now, 'updated_at' => $now];

                // Kelompokkan data untuk Google Sheets
                $procUpper = strtoupper(trim($rowData['proses']));
                if (isset($mapping[$procUpper])) {
                    $dataBySheet[$procUpper][] = $rowData;
                }
            }

            // Jalankan 1x DB Insert untuk SEMUA baris
            if (!empty($dbInsertData)) {
                ProsesProduksi::insert($dbInsertData); // Jauh lebih cepat!
            }

            // --- OPTIMASI 2: BATCH GOOGLE SHEETS UPDATE ---
            
            // Helper $getNextRowForSheet (milik Anda)
            $sheetRowCounters = [];
            $getNextRowForSheet = function($sheetName) use ($service, $spreadsheetId, &$sheetRowCounters) {
                if (isset($sheetRowCounters[$sheetName])) return $sheetRowCounters[$sheetName];
                $getRange = "{$sheetName}!C:C"; // Cek kolom C untuk jumlah baris
                try {
                    $response = $service->spreadsheets_values->get($spreadsheetId, $getRange);
                    $nextRow = count($response->getValues() ?? []) + 1;
                } catch (\Exception $e) { $nextRow = 2; } // Mulai dari baris 2 jika error
                $sheetRowCounters[$sheetName] = $nextRow;
                return $nextRow;
            };

            // Array untuk menampung SEMUA update
            $batchValueRanges = []; 

            foreach ($dataBySheet as $sheetName => $rows) {
                // 1. Dapatkan baris awal untuk sheet ini (1 API Call per TIPE sheet)
                $nextRow = $getNextRowForSheet($sheetName);

                foreach ($rows as $rowData) {
                    // 2. Bangun value range untuk tiap sel di baris ini
                    foreach ($mapping[$sheetName] as $col => $field) {
                        $cellRange = "{$sheetName}!{$col}{$nextRow}";
                        $value = $rowData[$field] ?? '';

                        $valueRange = new \Google_Service_Sheets_ValueRange();
                        $valueRange->setRange($cellRange);
                        $valueRange->setValues([[$value]]);

                        $batchValueRanges[] = $valueRange; // Kumpulkan semua
                    }
                    $nextRow++; // Pindah ke baris selanjutnya untuk sheet ini
                }
                // Simpan baris terakhir untuk sheet ini (ini tidak perlu, tapi logikanya tetap)
                // $sheetRowCounters[$sheetName] = $nextRow; 
            }

            // 3. Jalankan 1x API CALL untuk SEMUA sel
            if (!empty($batchValueRanges)) {
                $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateValuesRequest();
                $batchUpdateRequest->setValueInputOption('USER_ENTERED');
                $batchUpdateRequest->setData($batchValueRanges);

                $service->spreadsheets_values->batchUpdate($spreadsheetId, $batchUpdateRequest);
            }

            Log::info("ProcessSpreadsheetAndDbJob sukses memproses " . $rowsCount . " baris.");

        } catch (\Exception $e) {
            // Jika job gagal, catat di log
            Log::error("ProcessSpreadsheetAndDbJob GAGAL: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // Lempar lagi agar job di-retry (jika Anda mau)
            throw $e;
        }
    }
    private function getClient()
    {
        $client = new \Google\Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/laravelspreadsheet-474306-094813654d37.json'));
        $client->setAccessType('offline');
        return $client;
    }
}
