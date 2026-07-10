<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProsesProduksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ProsesProduksiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap parameter dari URL (Sekarang hanya ID dan Proses)
        $filterProses = $request->get('proses'); 
        $filterId     = $request->get('id');
        
        // 2. Mulai merakit Query Database
        $query = ProsesProduksi::query();

        // Jika user mengetikkan ID, cari ID yang cocok
        if (!empty($filterId)) {
            $query->where('id', $filterId);
        }

        // Jika user memilih filter proses, tambahkan ke pencarian
        if (!empty($filterProses)) {
            $query->where('proses', $filterProses);
        }

        // Otomatis selalu urutkan dari data terbaru (ID terbesar) ke terlama
        $query->orderBy('id', 'desc');

        // Eksekusi data
        $prosesProduksi = $query->paginate(20)->appends($request->query());

        // 3. Looping data untuk menghitung total jam secara on-the-fly
        foreach ($prosesProduksi as $data) {
            $totalJam = 0;

            // Pastikan kolom finish tidak kosong, dan salah satu dari set ATAU run terisi
            if (!empty($data->finish) && (!empty($data->set) || !empty($data->run))) {
                
                // Tentukan waktu mulai: Utamakan 'set', jika kosong baru pakai 'run'
                $waktuMulaiString = !empty($data->set) ? $data->set : $data->run;

                $waktuMulai = \Carbon\Carbon::parse($waktuMulaiString);
                $waktuFinish = \Carbon\Carbon::parse($data->finish);

                // Hitung selisih dalam menit, lalu bagi 60 menjadi jam
                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;

                // Logika potong 1 jam jika break = TRUE (atau 1)
                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            }

            // Gunakan max(0, ...) agar jika waktu salah input, tidak jadi minus
            $data->jam_kalkulasi = max(0, round($totalJam, 2));
            
            // ==========================================
            // PENGAMANAN TIPE DATA (String ke Float)
            // ==========================================
            $outputdrik = (float) str_replace('.', '', (string) $data->outputdrik);
            $upspk      = (float) str_replace('.', '', (string) $data->upspk);
            $jtdrik     = (float) str_replace('.', '', (string) $data->jtdrik);
            $jtpcs      = (float) str_replace('.', '', (string) $data->jtpcs);
            
            // output = drik x upspk
            $data->outputpcs = $outputdrik * $upspk;
            
            // total pengerjaan = jt drik + output drik
            $data->total_pengerjaan_drik = $jtdrik + $outputdrik;
            
            // total pengerjaan = jt pcs + output pcs
            $data->total_pengerjaan_pcs = $jtpcs + $data->outputpcs;
        }

        // 4. Siapkan master proses untuk mengisi dropdown di halaman Blade
        $masterProses = [
            'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
            'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
            'PRETEL', 'LEM', 'SORTIR', 'PACKING'
        ];

        // 5. Kirim ke view beserta masterProses
        return view('proses_produksi.index', compact('prosesProduksi', 'masterProses'));
    }
    
    public function create()
    {
        $jobs = ProsesProduksi::select('job')
            ->whereNotNull('job')
            ->distinct()
            ->orderBy('job')
            ->get();

        return view('proses_produksi.create', compact('jobs'));
    }
   
    public function show($job_id)
    {
        // 1. AMBIL DATA KHUSUS JOB INI SAJA
        // Gunakan where() agar hanya data dengan job_id tersebut yang ditarik
        $detailProses = ProsesProduksi::where('job', $job_id)->get();

        // Ambil nomor docket dari baris pertama (jika datanya ada)
        $docket = $detailProses->first()->designno ?? '-';

        // 2. HITUNG ATRIBUT VIRTUAL (ON-THE-FLY)
        // Kita hitung dulu jam dan pcs untuk setiap baris detailnya
        foreach ($detailProses as $data) {
            $totalJam = 0;

            if (!empty($data->finish) && (!empty($data->set) || !empty($data->run))) {
                $waktuMulaiString = !empty($data->set) ? $data->set : $data->run;
                $waktuMulai = Carbon::parse($waktuMulaiString);
                $waktuFinish = Carbon::parse($data->finish);

                $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
                $totalJam = $selisihMenit / 60;

                if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                    $totalJam -= 1;
                }
            }

            $data->jam_kalkulasi = max(0, round($totalJam, 2));
            // ==========================================
            // PENGAMANAN TIPE DATA (String ke Float)
            // ==========================================
            // Timpa langsung properti bawaan $data dengan angka yang sudah dibersihkan
            $data->outputdrik = (float) str_replace('.', '', (string) $data->outputdrik);
            $data->upspk      = (float) str_replace('.', '', (string) $data->upspk);
            $data->jtdrik     = (float) str_replace('.', '', (string) $data->jtdrik);
            $data->jtpcs      = (float) str_replace('.', '', (string) $data->jtpcs);

            // Karena sekarang $data->... sudah dijamin berupa angka (float),
            // kita bisa langsung memakainya untuk matematika tanpa error string + string
            $data->outputpcs = $data->outputdrik * $data->upspk;
            
            // total pengerjaan = jt drik + output drik
            $data->total_pengerjaan_drik = $data->jtdrik + $data->outputdrik;
            
            // total pengerjaan = jt pcs + output pcs
            $data->total_pengerjaan_pcs = $data->jtpcs + $data->outputpcs;
            // ==========================================
        }

        // 3. BUAT TABEL RANGKUMAN
        $masterProses = [
            'PRINT', 'SORTIR CETAK', 'WATERBASE', 'HOCK', 'HOTPRINT',
            'LAMINASI', 'LAMINATING', 'EMBOSS', 'DIECUT', 'CUTTING',
            'PRETEL', 'LEM', 'SORTIR', 'PACKING'
        ];

        $rangkuman = [];

        foreach ($masterProses as $prosesName) {
            
            // Filter data yang sudah dihitung di atas, khusus untuk proses ini
            $dataPerProses = $detailProses->filter(function ($item) use ($prosesName) {
                return strtoupper($item->proses) == $prosesName;
            });

            // Gunakan fungsi sum() dari Laravel (bekerja persis seperti fungsi SUMIF)
            $rangkuman[] = [
                'proses'                => $prosesName,
                'jam'                   => $dataPerProses->sum('jam_kalkulasi'),
                'jt_drik'               => $dataPerProses->sum('jtdrik'),
                'jt_pcs'               => $dataPerProses->sum('jtpcs'),
                'output_drik'           => $dataPerProses->sum('outputdrik'),
                'output_pcs'            => $dataPerProses->sum('outputpcs'),
                'total_pengerjaan_drik' => $dataPerProses->sum('total_pengerjaan_drik'),
                'total_pengerjaan_pcs'  => $dataPerProses->sum('total_pengerjaan_pcs'),
                'selisih_drik'          => 0, 
                'selisih_pcs'           => 0,
            ];
        }

        // 4. KIRIM DATA KE BLADE
        // Kita kirim 'rangkuman' untuk tabel atas, dan 'detailProses' untuk tabel riwayat di bawah
        return view('proses_produksi.show', compact('rangkuman', 'detailProses', 'job_id', 'docket'));
    }

    public function getJobData($job_id)
    {
        // Menggunakan model Proses_Produksi untuk mencari berdasarkan kolom 'job'
        $jobData = ProsesProduksi::where('job', $job_id)->first();

        // Jika data job tersebut pernah diinput dan ditemukan di tabel
        if ($jobData) {
            return response()->json([
                'product'  => $jobData->product,
                'designno' => $jobData->designno,
                'po'       => $jobData->po,
                'qty'      => $jobData->qty
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

    } catch (\Illuminate\Validation\ValidationException $e) {
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

    return back()->with('success', $rowsCount . ' data berhasil disimpan.');

} catch (\Exception $e) {

    Log::error($e->getMessage());

    return back()->with('error', 'Gagal menyimpan data.');
}
}



}
