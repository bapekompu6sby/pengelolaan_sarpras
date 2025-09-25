<?php

namespace App\Http\Controllers;



use App\Models\User;

use App\Models\Kamar;
use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\WismaExports;
use Illuminate\Support\Carbon;
use App\Exports\RuanganExports;
use PHPUnit\Event\Code\Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DetailKamarTransaction;
use Illuminate\Support\Facades\Storage;
use App\Exports\RuanganMultiMonthExport;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{



    // public function emailTransaction(Transaction $transaction)
    // {
    //     $to = $transaction->user->email ?? 'someone@example.com';

    //     $data = [
    //         'user' => $transaction->user,
    //         'trx'  => $transaction, // kirim objek biar enak dipakai di blade
    //         // tambahkan field lain kalau perlu
    //     ];

    //     Mail::send('emails.transactions_success', $data, function ($message) use ($to, $transaction) {
    //         $message->to($to)
    //             ->subject('Transaksi Berhasil #' . ($transaction->code ?? $transaction->id));
    //     });

    //     return back()->with('success', 'Email transaksi terkirim ke ' . $to);
    // }

    public function history_transaction()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $baseQuery = Transaction::with([
            'properties.kamar',     // pastikan nama relasi sesuai model kamu
            'detailKamars.kamar',
            'detailKamars.penghunis',
        ]);

        $transactions = $user->role === 'admin'
            ? $baseQuery->get()
            : $baseQuery->where('user_id', $user->id)->get();

        // siapkan "floors" sebagai collection ter-group
        $transactions->each(function ($t) {
            $kamar = optional($t->properties)->kamar ?? collect();
            // jika tidak ada kolom 'lantai', sesuaikan key group-nya
            $floors = $kamar->groupBy(fn($k) => $k->lantai ?? 'Tanpa Lantai');
            // "tanamkan" ke relasi virtual biar $t->floors bisa dipakai di Blade
            $t->setRelation('floors', $floors);
        });

        $ruangan = Properties::all();

        return view('user.transactions.index', [
            'transactions' => $transactions,
            'ruangan'      => $ruangan,
        ]);
    }





    public function update_payment_receipt(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            ], [
                'payment_receipt.max' => 'Ukuran file terlalu besar, maksimal 20 MB.',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->with('failed', $e->validator->errors()->first());
        }

        $transaction = Transaction::findOrFail($id);

        if ($request->hasFile('payment_receipt')) {
            $path = $request->file('payment_receipt')->store('uploads/payment_receipt', 'public');
            $transaction->payment_receipt = basename($path);
        }

        $transaction->save();

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah');
    }

    public function update_request_letter(Request $request, $id)
    {
        try {
            $request->validate([
                'request_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            ], [
                'request_letter.max' => 'Ukuran file terlalu besar, maksimal 20 MB.',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->with('failed', $e->validator->errors()->first());
        }

        $transaction = Transaction::findOrFail($id);

        if ($request->hasFile('request_letter')) {
            $path = $request->file('request_letter')->store('uploads/request_letter', 'public');
            $transaction->request_letter = basename($path);
        }

        $transaction->save();

        return redirect()->back()->with('success', 'Surat permohonan berhasil diunggah');
    }




    public function pinjam($id)
    {
        $property = Properties::findOrFail($id);
        $userId = Auth::id();
        $user = User::findOrFail($userId);

        return view('user.properties_pinjam_as_user', [
            'property' => $property,
            'user' => $user,
        ]);
    }






    /* ========================================================
$$$$$$\  $$\   $$\  $$$$$$\  $$$$$$$\   $$$$$$\   $$$$$$\  $$$$$$$\  
$$  __$$\ $$ |  $$ | \____$$\ $$  __$$\ $$  __$$\  \____$$\ $$  __$$\ 
$$ |  \__|$$ |  $$ | $$$$$$$ |$$ |  $$ |$$ /  $$ | $$$$$$$ |$$ |  $$ |
$$ |      $$ |  $$ |$$  __$$ |$$ |  $$ |$$ |  $$ |$$  __$$ |$$ |  $$ |
$$ |      \$$$$$$  |\$$$$$$$ |$$ |  $$ |\$$$$$$$ |\$$$$$$$ |$$ |  $$ |
\__|       \______/  \_______|\__|  \__| \____$$ | \_______|\__|  \__|
                                        $$\   $$ |                    
                                        \$$$$$$  |                    
                                         \______/                     
 ======================================================== */
    public function ruangan_show()
    {
        $ruangan = Properties::whereIn('type', ['aula', 'kelas'])->get();

        return view('admin.transaction-ruangan', [
            'properties' => $ruangan,
        ]);
    }

    // public function check_available_ruangan($start, $end, $property_id) 
    public function check_available_ruangan($start, $end, $property_id)
    {
        // check url query parameter
        // dd(request()->all());
        // $start = request()->start;
        // $end = request()->end;
        // $property_id = request()->property_id;

        $transactions = Transaction::where('property_id', $property_id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end]);
            })
            ->get();

        return $transactions;
        // return response()->json($transactions);
    }



    public function bookingStore(Request $request)
    {
        $colors = [
            'primary'   => '#0d6efd',
            'secondary' => '#6c757d',
            'success'   => '#198754',
            'info'      => '#0dcaf0',
            'warning'   => '#ffc107',
            'danger'    => '#dc3545',
            'dark'      => '#212529',
        ];

        $request->validate([
            'name'           => 'required|string',
            'office'         => 'required|string',
            'event'          => 'required|string',
            'start'          => 'required|date',
            'end'            => 'required|date',
            'venue'          => 'required',
            'request_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'description'    => 'nullable|string',
            'phone_number'   => 'nullable|string',
            'email'          => 'nullable|email', // ← penting
            'affiliation'    => 'required|string',
            'ordered_unit'   => 'required|integer',
            'total_harga'    => 'required|integer',
        ]);

        // Upload bukti bayar (opsional)
        $paymentReceiptPath = $request->hasFile('payment_receipt')
            ? $request->file('payment_receipt')->store('uploads/payment_receipt', 'public')
            : null;

        // Upload surat permohonan (opsional)
        $requestLetterPath = $request->hasFile('request_letter')
            ? $request->file('request_letter')->store('uploads/request_letter', 'public')
            : null;

        $namePaymentReceipt = $paymentReceiptPath ? basename($paymentReceiptPath) : null;
        $nameRequestLetter  = $requestLetterPath ? basename($requestLetterPath) : null;

        $colorKey = array_rand($colors, 1);

        // SIMPAN TRANSAKSI
        $transaction = Transaction::create([
            'name'            => ucfirst($request->name),
            'instansi'        => ucfirst($request->office),
            'kegiatan'        => ucfirst($request->event),
            'start'           => $request->start,
            'end'             => $request->end,
            'total_harga'     => $request->total_harga,
            'color'           => $colors[$colorKey],
            'property_id'     => $request->venue,
            'payment_receipt' => $namePaymentReceipt,
            'request_letter'  => $nameRequestLetter,
            'description'     => $request->description,
            'user_id'         => auth()->id(),
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'status'          => 'pending',
            'affiliation'     => $request->affiliation,
            'ordered_unit'    => $request->ordered_unit,
        ]);

        // === KIRIM EMAIL (kalau pemesan mengisi email) ===
        if ($request->filled('email')) {
            $payload = [
                'user' => auth()->user(),      // atau $transaction->user kalau ada relasi
                'trx'  => $transaction,        // dipakai di emails.transactions_success
            ];

            try {
                Mail::send('emails.transactions_success', ['payload' => $payload], function ($message) use ($request, $transaction) {
                    $message->to($request->email)
                        ->subject('Transaksi Berhasil #' . ($transaction->code ?? $transaction->id));

                    if ($transaction->request_letter) {
                        $path = storage_path('app/public/uploads/request_letter/' . $transaction->request_letter);
                        if (is_file($path)) {
                            $message->attach($path, ['as' => 'surat_permohonan.pdf']);
                        }
                    }
                    if ($transaction->payment_receipt) {
                        $path = storage_path('app/public/uploads/payment_receipt/' . $transaction->payment_receipt);
                        if (is_file($path)) {
                            $message->attach($path, ['as' => 'bukti_bayar.' . pathinfo($path, PATHINFO_EXTENSION)]);
                        }
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Gagal kirim email transaksi: ' . $e->getMessage());
                // lanjut saja, jangan gagalkan booking
            }
        }

        return redirect()->back()->with('success', 'Jadwal berhasil dibuat.');
    }




    public function transactionUpdate(Request $request, $id)
    {
        try {
            // 1) Validasi
            $validated = $request->validate([
                'user_id'          => 'required|integer',
                'office'           => 'required|string|max:150',
                'affiliation'      => 'required|string|in:internal_pu,external_pu',
                'phone_number'     => 'required|string|max:20',
                'email'            => 'required|email',
                'event'            => 'required|string|max:255',
                'ordered_unit'     => 'required|integer|min:1',
                'description'      => 'nullable|string',
                'start'            => 'required|date',
                'end'              => 'required|date|after_or_equal:start',
                'status'           => 'required|string|in:pending,approved,rejected,waiting_payment',
                'rejection_reason' => 'nullable|string|max:255|required_if:status,rejected',
                'total_harga'      => 'required|numeric|min:0',
                'billing_code'     => 'nullable|string',
                'billing_qr'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
                'ruangan_id'       => 'required|exists:properties,id',

                'payment_receipt'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
                'request_letter'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
                'response_letter'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
            ], [
                'office.max' => 'Nama instansi terlalu panjang (maks 150 karakter).',
            ]);

            // 2) Transaksi DB
            DB::transaction(function () use ($request, $validated, $id) {
                $transaction = Transaction::findOrFail($id);

                // fallback file lama
                $paymentReceipt = $request->old_payment_receipt ?? $transaction->payment_receipt;
                $requestLetter  = $request->old_request_letter  ?? $transaction->request_letter;
                $responseLetter = $request->old_response_letter ?? $transaction->response_letter;
                $billingQr      = $request->old_billing_qr      ?? $transaction->billing_qr;
                $billingCode    = $transaction->billing_code;
                $rejectionReason = $transaction->rejection_reason;

                // upload file baru (kalau ada)
                if ($request->hasFile('payment_receipt')) {
                    $paymentReceipt = basename(
                        $request->file('payment_receipt')->store('uploads/payment_receipt', 'public')
                    );
                }
                if ($request->hasFile('request_letter')) {
                    $requestLetter = basename(
                        $request->file('request_letter')->store('uploads/request_letter', 'public')
                    );
                }
                if ($request->hasFile('response_letter')) {
                    $responseLetter = basename(
                        $request->file('response_letter')->store('uploads/response_letter', 'public')
                    );
                }
                if ($request->hasFile('billing_qr')) {
                    $billingQr = basename(
                        $request->file('billing_qr')->store('uploads/billing_qr', 'public')
                    );
                }
                if (!empty($validated['billing_code'])) {
                    $billingCode = $validated['billing_code'];
                }

                // status logic
                if ($validated['status'] === 'rejected') {
                    $rejectionReason = $validated['rejection_reason'] ?? null;
                    $billingCode = null;
                    $billingQr   = null;
                } elseif (!empty($validated['rejection_reason'])) {
                    $rejectionReason = $validated['rejection_reason'];
                }

                // update
                $updated = $transaction->update([
                    'user_id'         => $validated['user_id'],
                    'instansi'        => ucwords($validated['office']), // atau Str::title()
                    'kegiatan'        => ucwords($validated['event']),
                    'property_id'     => $validated['ruangan_id'],
                    'description'     => $validated['description'] ?? null,
                    'status'          => $validated['status'],
                    'rejection_reason' => $rejectionReason,
                    'billing_code'    => $billingCode,
                    'billing_qr'      => $billingQr,
                    'start'           => $validated['start'],
                    'end'             => $validated['end'],
                    'total_harga'     => $validated['total_harga'],
                    'phone_number'    => $validated['phone_number'],
                    'email'           => $validated['email'],
                    'affiliation'     => $validated['affiliation'],
                    'ordered_unit'    => $validated['ordered_unit'],
                    'payment_receipt' => $paymentReceipt,
                    'request_letter'  => $requestLetter,
                    'response_letter' => $responseLetter,
                ]);

                if (!$updated) {
                    throw new \RuntimeException('Tidak ada perubahan data.');
                }
            });

            // 3) sukses
            return back()->with('success', 'Transaksi berhasil diperbarui.');
        }
        // tangkap validasi: kirim error + flash failed + keep input
        catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->with('failed', 'Gagal memperbarui transaksi. Periksa form yang disorot.')
                ->withInput();
        }
        // tangkap error lain: log & flash failed
        catch (\Throwable $e) {
            Log::error('Transaction update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('failed', 'Gagal memperbarui transaksi. Silakan coba lagi.')
                ->withInput();
        }
    }



    public function ruangan_detail()
    {
        $user = auth()->user();

        $txQuery = Transaction::query()
            ->with([

                'properties:id,name,type,capacity,price,unit,image_path',
                'properties.kamar' => function ($q) {
                    $q->select('id', 'properties_id', 'nama_kamar', 'kapasitas', 'lantai')
                        ->orderBy('lantai')
                        ->orderBy('nama_kamar');
                },


                'detailKamars.kamar:id,nama_kamar,kapasitas,lantai,properties_id',
                'detailKamars.penghunis:id,detail_kamar_transaction_id,nama_penghuni',
            ])
            ->latest();


        if (!$user || $user->role !== 'admin') {
            $txQuery->where('user_id', $user?->id);
        }

        $transactions = $txQuery->get();


        $transactions->each(function ($t) {
            $floors = collect($t->properties?->kamar ?? [])
                ->filter(fn($k) => isset($k->lantai) && $k->lantai !== '' && (int)$k->lantai !== 0)
                ->groupBy(fn($k) => (string) $k->lantai);

            $t->setAttribute('floors', $floors);
        });

        $ruangan = Properties::with([
            'kamar' => function ($q) {
                $q->select('id', 'properties_id', 'nama_kamar', 'kapasitas', 'lantai')
                    ->orderBy('lantai')
                    ->orderBy('nama_kamar');
            }
        ])->get();

        $ruangan->transform(function ($r) {
            if (!in_array($r->type, ['asrama', 'paviliun'])) {
                $r->setRelation('kamar', collect());
            }
            return $r;
        });

        return redirect()->back()->with('transactions', 'ruangan');
    }








    public function ruangan_destroy()
    {
        $ids = explode(',', request()->selected);
        Transaction::destroy($ids);
        // arilmubin
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus');
    }

    public function ruangan_export()
    {
        $now = now()->toDateString();
        return Excel::download(new RuanganExports, "$now-rekap-ruangan.xlsx");
    }
    public function ruangan_export_matrix(Request $request)
    {
        // startMonth opsional. Default: bulan ini (format YYYY-MM)
        $startMonth = $request->input('start_month', now()->format('Y-m'));

        $start = Carbon::parse($startMonth . '-01')->startOfMonth();

        // jumlah bulan yang ingin dibuat sheet-nya
        $months = 3; // bulan ini + 2 bulan ke depan

        $fname = 'rekap-ruangan-matrix_' . $start->format('Ym') . '_+' . ($months - 1) . 'bulan.xlsx';
        return Excel::download(new RuanganMultiMonthExport($start, $months), $fname);
    }


    /* ========================================================
                   $$\                           $$\                     
                   $$ |                          $$ |                    
 $$$$$$$\ $$$$$$\  $$ | $$$$$$\  $$$$$$$\   $$$$$$$ | $$$$$$\   $$$$$$\  
$$  _____|\____$$\ $$ |$$  __$$\ $$  __$$\ $$  __$$ | \____$$\ $$  __$$\ 
$$ /      $$$$$$$ |$$ |$$$$$$$$ |$$ |  $$ |$$ /  $$ | $$$$$$$ |$$ |  \__|
$$ |     $$  __$$ |$$ |$$   ____|$$ |  $$ |$$ |  $$ |$$  __$$ |$$ |      
\$$$$$$$\\$$$$$$$ |$$ |\$$$$$$$\ $$ |  $$ |\$$$$$$$ |\$$$$$$$ |$$ |      
 \_______|\_______|\__| \_______|\__|  \__| \_______| \_______|\__|      
================================================================== */

    // public function calendar()
    // {
    //     $propreties = Properties::all();
    //     // $events = Transaction::all();

    //     // $events = $events->map(function ($item) {
    //     //     return [
    //     //         'title' => $item->kegiatan,
    //     //         'venue' => $item->properties->name,
    //     //         'start' => $item->start,
    //     //         'end' => $item->end,
    //     //         'color' => $item->color,
    //     //     ];
    //     // });

    //     return view('calendar', [
    //         // 'events' => $events,
    //         'properties' => $propreties,
    //     ]);
    // }
    public function calendar()
    {
        $properties = Properties::whereHas('transactions', function ($q) {
            $q->where('status', 'approved');
        })->get();

        return view('calendar', [
            'properties' => $properties,
        ]);
    }




    public function events()
    {
        $events = Transaction::where('status', 'approved')
            ->get();

        $events = $events->map(function ($item) {
            return [
                'title' => $item->properties->name . ' - ' . $item->kegiatan,
                'venue' => $item->properties->id,
                'start' => $item->start,
                'end' => date('Y-m-d', strtotime($item->end . ' +1 day')),
                'color' => $item->color,
                // 'url' => '/transactions/' . $item->id . '/detail',
            ];
        });


        return response()->json($events);
    }



    // new admin
    public function transactionsAsAdmin()
    {
        $transactions = Transaction::query()
            ->with([
                'properties:id,name,type,capacity,price,unit,image_path',
                'properties.kamar' => function ($q) {
                    $q->select('id', 'properties_id', 'nama_kamar', 'kapasitas', 'lantai')
                        ->orderBy('lantai')->orderBy('nama_kamar');
                },
                'detailKamars.kamar:id,nama_kamar,kapasitas,lantai,properties_id',
                'detailKamars.penghunis:id,detail_kamar_transaction_id,nama_penghuni',
            ])
            ->latest()
            ->get();

        // inject floors per transaksi
        $transactions->each(function ($t) {
            $floors = collect($t->properties?->kamar ?? [])
                ->filter(fn($k) => isset($k->lantai) && $k->lantai !== '' && (int)$k->lantai !== 0)
                ->groupBy(fn($k) => (string) $k->lantai);
            $t->setAttribute('floors', $floors);
        });

        // semua properti (filter kamar utk asrama/paviliun saja)
        $ruangan = Properties::with(['kamar' => function ($q) {
            $q->select('id', 'properties_id', 'nama_kamar', 'kapasitas', 'lantai')
                ->orderBy('lantai')->orderBy('nama_kamar');
        }])->get()
            ->transform(function ($r) {
                if (!in_array($r->type, ['asrama', 'paviliun'])) {
                    $r->setRelation('kamar', collect());
                }
                return $r;
            });

        return view('admin.transactions.index', compact('transactions', 'ruangan'));
    }
}
