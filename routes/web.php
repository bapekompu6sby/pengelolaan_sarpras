<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\PropertiesControllerAsUser;
use App\Http\Controllers\Auth\RedirectAuthenticatedUsersController;
use App\Http\Controllers\DetailTransactionController;

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

// routes/web.php


Route::get('/images/{properties}', [PropertiesController::class, 'showImage'])->name('properties.image');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/asrama', [TransactionController::class, 'wisma_show'])->name('asrama');


Route::get('/bukuPanduan', [DashboardController::class, 'bukuPanduan'])->name('bukuPanduan');

Route::post('/customer_service/send', [CustomerServiceController::class, 'sendToWhatsapp'])->name('customer_service.send');

Route::group(['middleware' => 'auth'], function () {

        Route::post('/DetailTransaction/store', [DetailTransactionController::class, 'store'])->name('DetailTransaction.store');


        // routes/web.php
        Route::post('/transaction/check', [PropertiesController::class, 'checkAvailability'])->name('properties.check');
        Route::get('/kamar/check/{transaction}/{kamar}', [KamarController::class, 'check_kamar'])->name('kamar.check');

        Route::get('/kamarTerpakai', [KamarController::class, 'kamarTerpakai'])->name('kamarTerpakai');


        //baru
        Route::get('/PropertiesAsUser', [PropertiesControllerAsUser::class, 'index'])->name('PropertiesAsUser');

        Route::prefix('transactions')->group(function () {
                Route::get('/historyTransaction', [TransactionController::class, 'history_transaction'])->name('transactions.historyTransaction');

                Route::get('/pinjam/{id}', [TransactionController::class, 'pinjam'])->name('transactions.pinjam');
                Route::post('/pinjam/store', [TransactionController::class, 'pinjam_store'])->name('transactions.pinjam.store');

                Route::patch('/transactions/{id}/status', [TransactionController::class, 'update_status'])->name('transactions.updateStatus');

                Route::post('/updatePaymentReceipt/{id}', [TransactionController::class, 'update_payment_receipt'])->name('transactions.payment');
                Route::post('/updateRequestLetter/{id}', [TransactionController::class, 'update_request_letter'])->name('transactions.request_letter');
        });

        Route::get('/api/properties/{id}', [PropertiesController::class, 'getPropertyById'])
                ->middleware('auth');


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

        Route::get('/transactions/ruangan', [TransactionController::class, 'ruangan_show'])
                ->name('transactions.ruangan.show');

        Route::get('/properties', [PropertiesController::class, 'index'])->name('properties');

        // Data master semua penghuni wisma

        // Menyiapkan data untuk transaksi ruangan dan wisma

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

        Route::group(['middleware' => 'checkRole:admin'], function () {
                // prefik untuk admin
                Route::prefix('admin')->group(function () {
                        Route::post('/properties/store', [PropertiesController::class, 'store'])->name('properties.store');
                        Route::patch('/properties/{id}', [PropertiesController::class, 'update'])->name('properties.update');
                        Route::delete('/properties/{id}', [PropertiesController::class, 'destroy'])->name('properties.destroy');
                        Route::get('/wisma', [TransactionController::class, 'wisma_show_admin'])->name('wisma-admin');
                });
        });
        Route::prefix('kamar')->group(
                function () {
                        Route::get('/', [KamarController::class, 'index'])->name('kamar');
                        Route::post('/store', [KamarController::class, 'store'])->name('kamar.store');
                        Route::post('/edit/{id}', [KamarController::class, 'update'])->name('kamar.update');
                        Route::delete('/destroy/{id}', [KamarController::class, 'destroy'])->name('kamar.destroy');
                }
        );
        Route::group(['middleware' => 'checkRole:user'], function () {
                // prefik untuk wisma

        });
});

Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('tabelKegiatan', [DashboardController::class, 'tabelKegiatan'])->name('tabelKegiatan');

Route::get('calendar', [TransactionController::class, 'calendar'])->name('calendar');

Route::fallback(function () {
        return view('errors.404');
});
