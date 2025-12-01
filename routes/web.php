<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\PropertiesControllerAsUser;
use App\Http\Controllers\DetailTransactionController;
use App\Http\Controllers\Auth\RedirectAuthenticatedUsersController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Semua route web aplikasi. Dikelompokkan per area untuk memudahkan
| pemeliharaan. Gunakan prefix, name, dan middleware secara konsisten.
*/

/* =====================================================================
| PUBLIC (tanpa autentikasi)
|===================================================================== */

Route::get('/internalBapekom', [HomeController::class, 'internalBapekomp'])->name('internalBapekomp');

Route::get('/', [HomeController::class, 'index'])->name('home');



Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/bukuPanduan', [DashboardController::class, 'bukuPanduan'])->name('bukuPanduan');
Route::post('/customer_service/send', [CustomerServiceController::class, 'sendToWhatsapp'])->name('customer_service.send');
Route::get('/tabelKegiatan', [DashboardController::class, 'tabelKegiatan'])->name('tabelKegiatan');
Route::get('/calendar', [TransactionController::class, 'calendar'])->name('calendar');

/* =====================================================================
| AUTHENTICATED (hanya user login)
|===================================================================== */
Route::middleware('auth')->group(function () {

        /* --------------------------------------------------------------
    | Utilitas/cek ketersediaan
    |-------------------------------------------------------------- */
        Route::post('/transaction/check', [PropertiesController::class, 'checkAvailability'])->name('properties.check');
        Route::get('/kamar/check/{transaction}/{kamar}', [KamarController::class, 'check_kamar'])->name('kamar.check');

        /* --------------------------------------------------------------
    | Booking (user)
    |-------------------------------------------------------------- */
        Route::prefix('bookings')->group(function () {
                Route::get('/', [PropertiesControllerAsUser::class, 'index'])->name('bookings');
                Route::post('/store', [TransactionController::class, 'bookingStore'])->name('bookings.store');
        });



        /* --------------------------------------------------------------
    | Transactions (umum – user login)
    |-------------------------------------------------------------- */
        Route::prefix('transactions')->group(function () {
                // Riwayat transaksi user
                Route::get('/historyTransaction', [TransactionController::class, 'history_transaction'])->name('transactions.historyTransaction');

                // Halaman pinjam detail
                Route::get('/pinjam/{id}', [TransactionController::class, 'pinjam'])->name('transactions.pinjam');

                // ⛏ FIX: jangan pakai '/transactions/...' di dalam prefix('transactions')
                Route::patch('{id}/status', [TransactionController::class, 'update_status'])->name('transactions.updateStatus');

                // Upload dokumen
                Route::post('/updatePaymentReceipt/{id}', [TransactionController::class, 'update_payment_receipt'])->name('transactions.payment');
                Route::post('/updateRequestLetter/{id}', [TransactionController::class, 'update_request_letter'])->name('transactions.request_letter');
                Route::post('/updateDeskription/{id}', [TransactionController::class, 'update_deskription'])->name('transactions.updateDescription');


                // Ruangan – list/export/hapus
                Route::get('/ruangan/export', [TransactionController::class, 'ruangan_export'])->name('transactions.ruangan.export');

                Route::get('/export/ruangan-matrix', [TransactionController::class, 'ruangan_export_matrix'])
                        ->name('transactions.ruangan.export.matrix');

                Route::get('/ruangan/matrix', [TransactionController::class, 'ruangan_matrix_preview'])
                        ->name('transactions.ruangan.matrix');


                Route::get('/ruangan', [TransactionController::class, 'ruangan_show'])->name('transactions.ruangan.show');
                Route::delete('/ruangan', [TransactionController::class, 'ruangan_destroy'])->name('transactions.ruangan.destroy');


                Route::post('/{transaction}/email', [TransactionController::class, 'emailTransaction'])
                        ->name('transactions.email');
        });

        /* --------------------------------------------------------------
    | API (tetap di balik auth)
    |-------------------------------------------------------------- */
        Route::get('/api/properties/{id}', [PropertiesController::class, 'getPropertyById']);

        /* --------------------------------------------------------------
    | Redirect pasca login (sesuai role)
    |-------------------------------------------------------------- */
        Route::get('/redirectAuthenticatedUsers', [RedirectAuthenticatedUsersController::class, 'home']);

        /* --------------------------------------------------------------
    | ADMIN-ONLY
    |-------------------------------------------------------------- */
        Route::middleware('checkRole:admin | supervisor')->group(function () {

                Route::get('/dashboardAdmin', [DashboardController::class, 'dashboardAdmin'])->name('dashboardAdmin');

                Route::get('/export/tahunan', [DashboardController::class, 'exportPerTahun'])->name('export.perTahun');


                Route::get('bedroomsUse/export', [KamarController::class, 'bedroomsUse_export_matrix'])->name('bedroomsUse.export.matrix'); // /export/bedroomsUse-matrix

                /** Penghuni Kamar */
                Route::prefix('penghunis')->group(function () {
                        Route::get('/', [KamarController::class, 'kamarTerpakai'])->name('penghunis');
                        Route::put('/update', [KamarController::class, 'updatePenghunis'])->name('penghuni.update');
                        Route::delete('/destroy/{id}', [KamarController::class, 'destroyPenghunis'])->name('penghuni.destroy');
                });

                /** Properti (Admin) */
                Route::get('/properties', [PropertiesController::class, 'index'])->name('properties');
                // routes/web.php
                // routes/web.php
                Route::patch('/properties/{property}/status', [PropertiesController::class, 'updateStatus'])
                        ->name('properties.status');



                Route::prefix('admin')->group(function () {
                        Route::post('/properties/store', [PropertiesController::class, 'store'])->name('properties.store');
                        Route::patch('/properties/{id}', [PropertiesController::class, 'update'])->name('properties.update');
                        Route::delete('/properties/{id}', [PropertiesController::class, 'destroy'])->name('properties.destroy');
                });

                /** Kamar (Admin) */
                Route::prefix('kamar')->group(function () {
                        Route::get('/', [KamarController::class, 'index'])->name('kamar');
                        Route::post('/store', [KamarController::class, 'store'])->name('kamar.store');
                        Route::post('/edit/{id}', [KamarController::class, 'update'])->name('kamar.update');
                        Route::delete('/destroy/{id}', [KamarController::class, 'destroy'])->name('kamar.destroy');
                });

                /** Users (Admin) */
                Route::prefix('users')->group(function () {
                        Route::get('/', [UsersController::class, 'index'])->name('users');
                        Route::post('/store', [UsersController::class, 'store'])->name('users.store');
                        Route::put('/edit/{id}', [UsersController::class, 'update'])->name('users.update');
                        // Route::delete('/destroy/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
                });

                /** Ruangan – ringkasan/detail admin */
                Route::get('/ruangan/list', [TransactionController::class, 'ruangan_detail'])->name('ruangan.detail');

                /** Transactions (Admin) */
                Route::prefix('transactions')->group(function () {
                        Route::get('/', [TransactionController::class, 'transactionsAsAdmin'])->name('transactions');
                        Route::post('/DetailTransaction/store', [DetailTransactionController::class, 'store'])->name('transactions.DetailTransaction.store');
                        Route::post('/update/{id}', [TransactionController::class, 'transactionUpdate'])->name('transactions.update');
                });
        });

        /* --------------------------------------------------------------
    | USER-ONLY (placeholder)
    |-------------------------------------------------------------- */
        Route::middleware('checkRole:user')->group(function () {
                // Tambahkan route khusus user bila diperlukan
        });

        /* --------------------------------------------------------------
    | PROFILE (di balik auth)
    |-------------------------------------------------------------- */
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* =====================================================================
| AUTH scaffolding (Fortify/Breeze/Jetstream, dll)
|===================================================================== */
require __DIR__ . '/auth.php';

/* =====================================================================
| FALLBACK 404
|===================================================================== */
Route::fallback(function () {
        return view('errors.404');
});
