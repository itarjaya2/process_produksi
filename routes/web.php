<?php

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
Route::get('/proses-produksi/search-suggestions', [ProsesProduksiController::class, 'searchSuggestions'])->name('proses-produksi.search-suggestions');
// Route untuk melihat detail berdasarkan nomor job
// Route::get('/proses-produksi/rangkuman', [ProsesProduksiController::class, 'show'])->name('proses-produksi.show');
Route::get('/proses-produksi/rangkuman', [ProsesProduksiController::class, 'show'])
    ->name('proses-produksi.rangkuman');
Route::match(['post', 'patch'], '/proses-produksi/inline-update', [ProsesProduksiController::class, 'inlineUpdate'])->name('proses-produksi.inline-update');
Route::get('/get-job-data/{job_id}', [ProsesProduksiController::class, 'getJobData']);
Route::get('/proses-produksi/{id}/edit', [ProsesProduksiController::class, 'edit'])
    ->name('proses-produksi.edit');

Route::put('/proses-produksi/{id}', [ProsesProduksiController::class, 'update'])
    ->name('proses-produksi.update');
