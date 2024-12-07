<?php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SawController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/settings-template', function () {
    return view('dashboard.home');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/simple-additive-weighting', [SawController::class, 'index'])->name("simple-additive-weighting");
    Route::post('/simple-additive-weighting', [SawController::class, 'store'])->name("saw.store");;
    Route::get('/simple-additive-weighting/{id}', [SawController::class, 'detail']);
    Route::post('/simple-additive-weighting/{id}/kriteria', [KriteriaController::class, 'store'])->name('kriteria.store');
    Route::post('/simple-additive-weighting/{id}/kriteria/{id_kriteria}', [KriteriaController::class, 'edit'])->name('edit.kriteria');
    Route::delete('/simple-additive-weighting/{id}/kriteria/{id_kriteria}', [KriteriaController::class, 'delete'])->name('delete.kriteria');
    Route::get('/simple-additive-weighting/{id}/detail-alternatif/{id_alternatif}', [AlternatifController::class, 'detail']);
    Route::post('/simple-additive-weighting/{id}/alternatif', [AlternatifController::class, 'store']);
    Route::post('/delete-alternatif', [AlternatifController::class, 'deleteMasal']);
    Route::get('/simple-additive-weighting/{id}/alternatif/{id_alternatif}', [AlternatifController::class, 'edit']);
    Route::put('/simple-additive-weighting/{id}/alternatif/{id_alternatif}', [AlternatifController::class, 'update']);
    Route::get('/download-template-alternatif/{id}', [AlternatifController::class, 'downloadTemlpateAlternatif']);
    Route::post('/import-data', [AlternatifController::class, 'importAlternatif']);
    Route::get('/getRincianBobot/{id}', [KriteriaController::class, 'getRincianBobot']);
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

});

Route::middleware(['guest'])->group(function () {
    Route::get('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/login', [AuthController::class, 'loginProcess']);
});




