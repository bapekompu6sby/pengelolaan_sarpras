<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Auth\RedirectAuthenticatedUsersController;
use App\Http\Controllers\ProfileController;

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

// auth 
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/asrama', [TransactionController::class, 'wisma_show'])->name('asrama');
Route::group(['middleware' => 'auth'], function() {
    
        Route::get("/redirectAuthenticatedUsers", [RedirectAuthenticatedUsersController::class, "home"]);
    
        // role khusus untuk pak heru :) saja saja ada
        Route::get('/transactions/ruangan/list', [TransactionController::class, 'ruangan_detail'])
        ->name('ruangan.detail');
        Route::get('/transactions/wisma/list', [TransactionController::class, 'wisma_show_admin'])
        ->name('wisma.detail');

        // export route untuk ruangan
        Route::get('/transactions/ruangan/export', [TransactionController::class, 'ruangan_export'])
        ->name('transactions.ruangan.export');

        // export route untuk wisma
        Route::get('/transactions/wisma/export', [TransactionController::class, 'wisma_export'])
        ->name('transactions.wisma.export');

        Route::prefix('wisma')->group(function () {
                Route::get('/', [TransactionController::class, 'wisma_show'])->name('wisma_show_user');
        });

        Route::group(['middleware' => 'checkRole:admin'], function() {
            // prefik untuk admin
            Route::prefix('admin')->group(function () {
                Route::get('/properties', [PropertiesController::class, 'index'])->name('properties');
                Route::post('/properties/store', [PropertiesController::class, 'store'])->name('properties.store');
                Route::patch('/properties/{id}', [PropertiesController::class, 'update'])->name('properties.update');
                Route::delete('/properties/{id}', [PropertiesController::class, 'destroy'])->name('properties.destroy');

                // Data master semua penghuni wisma
                Route::get('/wisma', [TransactionController::class, 'wisma_show_admin'])->name('wisma-admin');

                // Menyiapkan data untuk transaksi ruangan dan wisma
                Route::get('/transactions/ruangan', [TransactionController::class, 'ruangan_show'])
                        ->name('transactions.ruangan.show');
          
                Route::post('/transactions/ruangan', [TransactionController::class, 'ruangan_store'])
                        ->name('transactions.ruangan.store');
                Route::post('/transactions/ruangan/update/{id}', [TransactionController::class, 'ruangan_update'])
                        ->name('transactions.ruangan.update');
                Route::delete('/transactions/ruangan', [TransactionController::class, 'ruangan_destroy'])
                        ->name('transactions.ruangan.destroy');

                Route::get('/transactions/wisma', [TransactionController::class, 'wisma_show'])
                        ->name('transactions.wisma.show');
                Route::post('/transactions/wisma', [TransactionController::class, 'wisma_store'])
                        ->name('transactions.wisma.store');
                Route::patch('/transactions/wisma/{id}', [TransactionController::class, 'wisma_update'])
                        ->name('transactions.wisma.update');
                Route::delete('/transactions/wisma/destroy', [TransactionController::class, 'wisma_destroy'])
                        ->name('transactions.wisma.destroy');
            });
        });
        Route::group(['middleware' => 'checkRole:user'], function() {
            // prefik untuk wisma

        });
    });
    
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    
    require __DIR__.'/auth.php';

Route::get('calendar', [TransactionController::class, 'calendar'])->name('calendar');

Route::fallback(function () {
    return view('errors.404');
});
