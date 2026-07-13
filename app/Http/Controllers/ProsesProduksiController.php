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
    // 1.Tangkap input tanggal dari form filter
    $filterProses = $request->get('proses'); 
    $filterId     = $request->get('id');
    $startDate    = $request->get('start_date');
    $endDate      = $request->get('end_date');
    $filterJob      = $request->get('job');
    $filterOperator = $request->get('operator');
    
    $query = ProsesProduksi::query();

    if (!empty($filterId)) {
        $query->where('id', $filterId);
    }
    if (!empty($filterProses)) {
        $query->where('proses', $filterProses);
    }
    if (!empty($filterJob)) {
        $query->where('job', 'like', '%' . $filterJob . '%');
    }
    if (!empty($filterOperator)) {
        $query->where('operator', 'like', '%' . $filterOperator . '%');
    }
    // 2.Logika filter rentang tanggal
    if (!empty($startDate) && !empty($endDate)) {
        // Jika dari tanggal & sampai tanggal diisi keduanya
        $query->whereBetween('tanggal', [$startDate, $endDate]);
    } elseif (!empty($startDate)) {
        // Jika hanya mengisi "Dari Tanggal" saja
        $query->whereDate('tanggal', '>=', $startDate);
    } elseif (!empty($endDate)) {
        // Jika hanya mengisi "Sampai Tanggal" saja
        $query->whereDate('tanggal', '<=', $endDate);
    }

    $query->orderBy('id', 'desc');

    // Bagian ini sudah sangat tepat karena pakai ->appends($request->query())
    $prosesProduksi = $query->paginate(15)->appends($request->query());

    foreach ($prosesProduksi as $data) {
        $totalJam = 0;

        if (!empty($data->finish) && (!empty($data->set) || !empty($data->run))) {
            $waktuMulaiString = !empty($data->set) ? $data->set : $data->run;
            $waktuMulai = \Carbon\Carbon::parse($waktuMulaiString);
            $waktuFinish = \Carbon\Carbon::parse($data->finish);

            $selisihMenit = $waktuMulai->diffInMinutes($waktuFinish);
            $totalJam = $selisihMenit / 60;

            if (strtoupper($data->break) === 'TRUE' || $data->break == 1) {
                $totalJam -= 1;
            }
        }

        // Timpa langsung properti asli database
        $data->totaljam = max(0, round($totalJam, 2));
        
        $data->outputdrik = (float) str_replace('.', '', (string) $data->outputdrik);
        $data->upspk      = (float) str_replace('.', '', (string) $data->upspk);
        $data->jtdrik     = (float) str_replace('.', '', (string) $data->jtdrik);
        $data->jtpcs      = (float) str_replace('.', '', (string) $data->jtpcs);
        
        $data->outputpcs = $data->outputdrik * $data->upspk;
        $data->total_pengerjaan_drik = $data->jtdrik + $data->outputdrik;
        $data->total_pengerjaan_pcs  = $data->jtpcs + $data->outputpcs;
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
    $data = $query->latest()->get();

    // Hitung total dari data yang tampil
    $total = [
    'input'      => $data->sum(fn($x) => (int)$x->input),
    'jtpcs'      => $data->sum(fn($x) => (int)$x->jtpcs),
    'jtdrik'     => $data->sum(fn($x) => (int)$x->jtdrik),
    'outputpcs'  => $data->sum(fn($x) => (int)$x->outputpcs),
    'outputdrik' => $data->sum(fn($x) => (int)$x->outputdrik),
];

    // Kirim $daftarProses (bukan masterProses) ke view index
    return view('proses_produksi.index', compact('prosesProduksi', 'daftarProses', 'total'));
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
        $detailProses = ProsesProduksi::where('job', $job_id)->get();

        // Ambil nomor docket dari baris pertama (jika datanya ada)
        $docket = $detailProses->first()->designno ?? '-';

        // 2. HITUNG ATRIBUT VIRTUAL (ON-THE-FLY)
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

            $data->totaljam = max(0, round($totalJam, 2));

           
            // PENGAMANAN TIPE DATA (String ke Float)
            $data->outputdrik = (float) str_replace('.', '', (string) $data->outputdrik);
            $data->upspk      = (float) str_replace('.', '', (string) $data->upspk);
            $data->jtdrik     = (float) str_replace('.', '', (string) $data->jtdrik);
            $data->jtpcs      = (float) str_replace('.', '', (string) $data->jtpcs);

            // Perhitungan matematika menggunakan data yang sudah bersih
            $data->outputpcs = $data->outputdrik * $data->upspk;
            $data->total_pengerjaan_drik = $data->jtdrik + $data->outputdrik;
            $data->total_pengerjaan_pcs = $data->jtpcs + $data->outputpcs;
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

            $rangkuman[] = [
                'proses'                => $prosesName,
                // PERBAIKAN PENTING: Ubah menjadi totaljam karena jam_kalkulasi sudah tidak ada
                'jam'                   => $dataPerProses->sum('totaljam'),
                'jt_drik'               => $dataPerProses->sum('jtdrik'),
                'jt_pcs'                => $dataPerProses->sum('jtpcs'),
                'output_drik'           => $dataPerProses->sum('outputdrik'),
                'output_pcs'            => $dataPerProses->sum('outputpcs'),
                'total_pengerjaan_drik' => $dataPerProses->sum('total_pengerjaan_drik'),
                'total_pengerjaan_pcs'  => $dataPerProses->sum('total_pengerjaan_pcs'),
                'selisih_drik'          => 0, 
                'selisih_pcs'           => 0,
            ];
 
        }
     $total = [
    'input'      => $detailProses->sum(fn($x) => (float) $x->input),
    'jtpcs'      => $detailProses->sum(fn($x) => (float) $x->jtpcs),
    'jtdrik'     => $detailProses->sum(fn($x) => (float) $x->jtdrik),
    'outputpcs'  => $detailProses->sum(fn($x) => (float) $x->outputpcs),
    'outputdrik' => $detailProses->sum(fn($x) => (float) $x->outputdrik),
];

        // 4. KIRIM DATA KE BLADE
        return view('proses_produksi.show', compact('rangkuman', 'detailProses', 'job_id', 'docket','total'));
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

public function edit($id)
{
    $prosesProduksi = ProsesProduksi::findOrFail($id);
 
    return view('proses_produksi.edit', compact('prosesProduksi'));
}
 
public function update(Request $request, $id)
{
    $prosesProduksi = ProsesProduksi::findOrFail($id);
 
    $rules = [
        'job'            => 'nullable|string',
        'tanggal'        => 'nullable|string',
        'product'        => 'nullable|string',
        'designno'       => 'nullable|string',
        'po'             => 'nullable|string',
        'qty'            => 'nullable|string',
        'palet'          => 'nullable|string',
        'proses'         => 'required|string',
        'mesin'          => 'nullable|string',
        'shift'          => 'nullable|string',
        'vendormat'      => 'nullable|string',
        'type'           => 'nullable|string',
        'operator'       => 'nullable|string',
        'jumlahtim'      => 'nullable|string',
        'toleransi'      => 'nullable|string',
        'pengawas'       => 'nullable|string',
        'shiftpengawas'  => 'nullable|string',
        'set'            => 'nullable|string',
        'run'            => 'nullable|string',
        'finish'         => 'nullable|string',
        'break'          => 'nullable|string',
        'input'          => 'nullable|string',
        'upspk'          => 'nullable|string',
        'target'         => 'nullable|string',
        'jtdrik'         => 'nullable|string',
        'jtpcs'          => 'nullable|string',
        'outputdrik'     => 'nullable|string',
        'karantina'      => 'nullable|string',
        'notok'          => 'nullable|string',
        'ok'             => 'nullable|string',
        // reject checkboxes
        'warna'          => 'nullable|string',
        'banjir'         => 'nullable|string',
        'beset'          => 'nullable|string',
        'powder'         => 'nullable|string',
        'wb'             => 'nullable|string',
        'uvkasar'        => 'nullable|string',
        'uvmbleset'      => 'nullable|string',
        'tidakuv'        => 'nullable|string',
        'hotprint'       => 'nullable|string',
        'laminating'     => 'nullable|string',
        'laminasikurang' => 'nullable|string',
        'laminasi'       => 'nullable|string',
        'tidakpresisi'   => 'nullable|string',
        'pecah'          => 'nullable|string',
        'emboss'         => 'nullable|string',
        'porforasi'      => 'nullable|string',
        'sobek'          => 'nullable|string',
        'lengket'        => 'nullable|string',
        'll'             => 'nullable|string',
        'noteoperator'   => 'nullable|string',
        'ket'            => 'nullable|string',
    ];
 
    $validated = $request->validate($rules);
 
    // Checkbox yang tidak dicentang tidak terkirim → set null / 0
    $checkboxFields = [
        'warna','banjir','beset','powder','wb','uvkasar','uvmbleset',
        'tidakuv','hotprint','laminating','laminasikurang','laminasi',
        'tidakpresisi','pecah','emboss','porforasi','sobek','lengket','ll',
    ];
    foreach ($checkboxFields as $field) {
        $validated[$field] = $request->has($field) ? '1' : null;
    }
 
    $prosesProduksi->update($validated);
 
    return redirect()->route('proses-produksi.index')
                     ->with('success', 'Data berhasil diperbarui.');
}

}
