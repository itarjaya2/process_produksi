<?php
use App\Http\Controllers\ProsesProduksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/proses-produksi', [ProsesProduksiController::class, 'index'])
    ->name('proses-produksi.index');

Route::post('/proses-produksi', [ProsesProduksiController::class, 'store'])
    ->name('proses-produksi.store');

Route::get('/proses-produksi/create', [ProsesProduksiController::class, 'create'])->name('proses-produksi.create');
// Route untuk melihat detail berdasarkan nomor job
Route::get('/proses-produksi/job/{job_id}', [ProsesProduksiController::class, 'show'])->name('proses-produksi.show');
Route::get('/get-job-data/{job_id}', [ProsesProduksiController::class, 'getJobData']);
Route::get('/proses-produksi/{id}/edit', [ProsesProduksiController::class, 'edit'])
    ->name('proses-produksi.edit');



Route::put('/proses-produksi/{id}', [ProsesProduksiController::class, 'update'])
    ->name('proses-produksi.update');