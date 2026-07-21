 <?php

use App\Http\Controllers\Produksi\DeptProduksi\ActivityLogController;
use App\Http\Controllers\Produksi\DeptProduksi\SpreadsheetController;

Route::get('/get-job-spreadsheet/{id}', [SpreadsheetController::class, 'getJob']);
Route::get('/spreadsheet', [SpreadsheetController::class, 'index'])->name('spreadsheet.index');
Route::post('/spreadsheet', [SpreadsheetController::class, 'store'])->name('spreadsheet.store');
Route::get('/proses-produksi', [SpreadsheetController::class, 'indexdata'])->name('proses-produksi.indexdata');
Route::get('/proses-produksi/search-suggestions', [SpreadsheetController::class, 'searchSuggestions'])->name('proses-produksi.search-suggestions');
Route::get('/proses-produksi/job/{job_id}', [SpreadsheetController::class, 'report'])->name('proses-produksi.rangkuman');
Route::match(['post', 'patch'], '/proses-produksi/inline-update', [SpreadsheetController::class, 'inlineUpdate'])->name('proses-produksi.inline-update');
Route::get('/get-job-data/{job_id}', [SpreadsheetController::class, 'getJobData']);
Route::put('/proses-produksi/{id}', [SpreadsheetController::class, 'update'])->name('proses-produksi.update');
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
Route::get('/activity-logs/proses/{proses_produksi_id}', [ActivityLogController::class, 'showByProses'])->name('activity-logs.show-by-proses');
