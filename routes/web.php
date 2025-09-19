<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\BibitController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KolamController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KulinerController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\DokumentasiKegiatanController;
use App\Http\Controllers\PakanController;
use App\Http\Controllers\PanenController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PemberianPakanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\DashboardController;
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
Route::get('/auth', [AuthController::class, 'index']);
Route::post('/auth', [AuthController::class, 'auth']);
Route::get('/link', function () {
    $target = __DIR__ . '/../storage/app/public';
    $shortcut = __DIR__ . '/../public/storage';

    try {
        symlink($target, $shortcut);
        return response()->json("Symlink created successfully.");
    } catch (\Exception $e) {
        return response()->json("Failed to create symlink: " . $e->getMessage());
    }
});

Route::group(['middleware' => ['auth'], 'prefix' => 'app'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::resource('/profile', ProfilController::class);

    Route::resource('/level', LevelController::class);

    Route::get('/user/create', [UserController::class, 'create']);
    Route::resource('/user', UserController::class);

    Route::resource('/kolam', KolamController::class);

    route::get('/bibit/list', [BibitController::class, 'list']);
    Route::resource('/bibit', BibitController::class);

    Route::resource('/pakan', PakanController::class);

    route::get('/pp-bibit/list', [PemberianPakanController::class, 'bibitlist']);
    route::get('/pp-pakan/list', [PemberianPakanController::class, 'pakanlist']);
    Route::resource('/pemberian-pakan', PemberianPakanController::class);

    Route::resource('/panen', PanenController::class);

    Route::resource('/kuliner', KulinerController::class);

    Route::resource('/pembelian', PembelianController::class);

    Route::get('/panen/detail/{id}', [PenjualanController::class, 'getPanen']);
    Route::resource('/penjualan', PenjualanController::class);

    Route::resource('/keuangan', KeuanganController::class);

    Route::resource('/transaksi', TransaksiController::class);

    Route::get('/laporan', [PelaporanController::class, 'index']);
    Route::get('/pelaporan/preview', [PelaporanController::class, 'preview']);
    Route::get('/pelaporan/sub-laporan/{file}', [PelaporanController::class, 'subLaporan']);
    Route::get('/pelaporan/simpan-saldo/{tahun}/{bulan?}', [PelaporanController::class, 'simpanSaldo']);

    Route::resource('/dokumentasi-kegiatan', DokumentasiKegiatanController::class);

    Route::resource('/presensi', PresensiController::class);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
