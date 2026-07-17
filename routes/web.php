<?php

use App\Http\Controllers\produksi\ActivityLogController;
use App\Http\Controllers\produksi\ProsesProduksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/proses-produksi', [ProsesProduksiController::class, 'index'])
    ->name('proses-produksi.index');

Route::post('/proses-produksi', [ProsesProduksiController::class, 'store'])
    ->name('proses-produksi.store');

Route::get('/proses-produksi/create', [ProsesProduksiController::class, 'create'])->name('proses-produksi.create');
// grandtotal index
Route::get('/proses-produksi/grand-total', [ProsesProduksiController::class, 'getGrandTotal'])->name('proses-produksi.grand-total');
Route::get('/proses-produksi/search-suggestions', [ProsesProduksiController::class, 'searchSuggestions'])->name('proses-produksi.search-suggestions');

Route::get('/proses-produksi/job/{job_id}', [ProsesProduksiController::class, 'show'])->name('proses-produksi.show');

Route::match(['post', 'patch'], '/proses-produksi/inline-update', [ProsesProduksiController::class, 'inlineUpdate'])->name('proses-produksi.inline-update');
Route::get('/get-job-data/{job_id}', [ProsesProduksiController::class, 'getJobData']);
Route::get('/proses-produksi/{id}/edit', [ProsesProduksiController::class, 'edit'])
    ->name('proses-produksi.edit');

Route::put('/proses-produksi/{id}', [ProsesProduksiController::class, 'update'])
    ->name('proses-produksi.update');

// Route::middleware(['auth'])->group(function () {
// Route utama untuk menampilkan halaman daftar Activity Log
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
// (Opsional) Route API/AJAX jika Anda ingin menampilkan modal histori khusus untuk 1 baris Job tertentu
// Route::get('/activity-logs/proses/{proses_produksi_id}', [ActivityLogController::class, 'showByProses'])->name('activity-logs.show-by-proses');
// });
