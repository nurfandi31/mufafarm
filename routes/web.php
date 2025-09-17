<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BibitController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KolamController;
use App\Http\Controllers\LevelController;
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


    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
