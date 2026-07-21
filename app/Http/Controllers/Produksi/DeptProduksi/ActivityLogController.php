<?php

namespace App\Http\Controllers\Produksi\DeptProduksi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai query dengan Eager Loading ('prosesProduksi' & 'user')
        // Ini adalah rahasia agar website TIDAK LAMBAT saat load ribuan data (Anti N+1 Query)
        $query = ActivityLog::with(['prosesProduksi', 'user']);

        // --- FITUR SEARCH & FILTER ---

        // Filter 1: Pencarian berdasarkan Nomor Job
        if ($request->filled('job')) {
            $query->whereHas('prosesProduksi', function ($q) use ($request) {
                $jobsList = preg_split('/[\s,;|]+/', trim($request->job));
                $jobsList = array_filter($jobsList);
                if (count($jobsList) > 1) {
                    $q->where(function ($innerQuery) use ($jobsList) {
                        foreach ($jobsList as $jobItem) {
                            $innerQuery->orWhere('job', 'like', '%'.$jobItem.'%');
                        }
                    });
                } elseif (count($jobsList) == 1) {
                    $q->where('job', 'like', '%'.$jobsList[0].'%');
                }
            });
        }

        // Filter 2: Dropdown berdasarkan Mesin / Proses Produksi
        if ($request->filled('proses')) {
            $query->whereHas('prosesProduksi', function ($q) use ($request) {
                $q->where('proses', $request->proses);
            });
        }

        // Filter 3: Dropdown berdasarkan Operator / User yang mengedit
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter 4: Filter berdasarkan Nama Kolom yang diubah (e.g., input, jtpcs, totaljam)
        if ($request->filled('field_name')) {
            $query->where('field_name', $request->field_name);
        }

        // Filter 5: Rentang Tanggal Kejadian (From - To)
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('created_at', [
                $request->tanggal_dari.' 00:00:00',
                $request->tanggal_sampai.' 23:59:59',
            ]);
        } elseif ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        } elseif ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        // 2. Pengurutan & Pagination
        // Ambil data terbaru terlebih dahulu, tampilkan 50 baris per halaman
        $logs = $query->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        // 3. Siapkan data untuk opsi Dropdown Filter di halaman UI
        $listProses = [
            'Print', 'Sortir Cetak', 'Waterbase', 'Hock', 'Hotprint',
            'Laminasi', 'Laminating', 'Emboss', 'Diecut', 'Cutting',
            'Pretel', 'Lem', 'Sortir', 'Packing',
        ];

        // Ambil daftar user yang pernah melakukan aktivitas log saja untuk dropdown
        $listUsers = User::whereHas('activityLogs')->orderBy('name')->get();

        return view('role.produksi.produksidept.activitylog.index', compact('logs', 'listProses', 'listUsers'));
    }

    /**
     * Menampilkan riwayat log khusus untuk 1 baris Job/Proses tertentu
     */
    public function showByProses($proses_produksi_id)
    {
        $logs = ActivityLog::with('user')
            ->where('proses_produksi_id', $proses_produksi_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'waktu' => $log->created_at
                        ? strtoupper(Carbon::parse($log->created_at)->format('d M y H:i:s'))
                        : '-',
                    'user' => optional($log->user)->name ?? 'System',
                    'field' => $log->field_name,
                    'old' => $log->old_value,
                    'new' => $log->new_value,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
