<?php

use App\Http\Controllers\Admin\MahasiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::resource('/master/mahasiswa', MahasiswaController::class)->names('data-mahasiswa');
    Route::get('/master/mahasiswa/sync-all/range', [MahasiswaController::class, 'syncAllRange'])->name('data-mahasiswa.sync-all-range');
    Route::post('/master/mahasiswa/sync-all/{angkatan}', [MahasiswaController::class, 'syncAllYear'])->name('data-mahasiswa.sync-all-year');
});
