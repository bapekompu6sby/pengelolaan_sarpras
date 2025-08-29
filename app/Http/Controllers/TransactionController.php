<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Wisma;
use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Exports\WismaExports;
use App\Exports\RuanganExports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DetailKamarTransaction;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller


{
    public function history_transaction()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $baseQuery = Transaction::with([
            'properties.kamar',     // pastikan nama relasi sesuai model kamu
            'detailKamars.kamar',
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

        return view('user.history_transaction', [
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

    public function ruangan_store(Request $request)
    {
        // echo "<pre>";
        // print_r($request->toArray());
        // echo "</pre>";
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
            'name' => 'required|string',
            'office' => 'required|string|max:32',
            'event' => 'required|string|max:32',
            'start' => 'required|date',
            'end' => 'required|date',
            'venue' => 'required',
            // 'payment_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'request_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|string',
            'affiliation' => 'required|string',
            'ordered_unit' => 'required|integer',
            'total_harga' => 'required|integer',
        ]);

        // $transactions = $this->check_available_ruangan($request->start, $request->end, $request->venue);

        $property = Properties::findOrFail($request->venue);

        $isExclusive = in_array($property->type, ['aula', 'kelas']);

        $conflicts = $this->check_available_ruangan($request->start, $request->end, $property->id);

        if ($isExclusive && $conflicts->isNotEmpty()) {
            return redirect()->route('transactions.ruangan.show')
                ->with('failed', 'Ruangan sudah terpakai');
        }



        $paymentReceiptPath = null;
        if ($request->hasFile('payment_receipt')) {
            $paymentReceiptPath = $request->file('payment_receipt')->store('uploads/payment_receipt', 'public');
        }


        $requestLetterPath = null;
        if ($request->hasFile('request_letter')) {
            $requestLetterPath = $request->file('request_letter')->store('uploads/request_letter', 'public');
        }

        $namePaymentReceipt = $paymentReceiptPath ? basename($paymentReceiptPath) : null;
        $nameRequestLetter = $requestLetterPath ? basename($requestLetterPath) : null;





        $color = array_rand($colors, 1);

        Transaction::create([
            'name' => ucfirst($request->name),
            'instansi' => ucfirst($request->office),
            'kegiatan' => ucfirst($request->event),
            'start' => $request->start,
            'end' => $request->end,
            'total_harga' => $request->total_harga,
            'color' => $colors[$color],
            'property_id' => $request->venue,
            'payment_receipt' => $namePaymentReceipt,
            'request_letter' => $nameRequestLetter,
            'description' => $request->description,
            'user_id' => auth()->user()->id,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => 'pending',
            'affiliation' => $request->affiliation,
            'ordered_unit' => $request->ordered_unit,
        ]);

        return redirect()->route('ruangan.detail')->with('success', 'Jadwal berhasil dibuat');
    }




    public function ruangan_update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id'          => 'required|integer',
            'office'           => 'required|string|max:32',
            'affiliation'      => 'required|string|in:internal_pu,external_pu',
            'phone_number'     => 'required|string|max:15',
            'email'            => 'required|email',
            'event'            => 'required|string|max:100',
            'ordered_unit'     => 'required|integer|min:1',
            'description'      => 'nullable|string',
            'start'            => 'required|date',
            'end'              => 'required|date|after_or_equal:start',
            'status'           => 'required|string|in:pending,approved,rejected,waiting_payment',
            'rejection_reason' => 'required_if:status,rejected',
            'total_harga'      => 'required|numeric|min:0',
            'billing_code'     => 'nullable|string',
            'billing_qr'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
            'ruangan_id'       => 'required|exists:properties,id',

        ]);

        DB::transaction(function () use ($request, $id) {
            $transaction = Transaction::findOrFail($id);


            $paymentReceipt   = $request->old_payment_receipt ?? $transaction->payment_receipt;
            $requestLetter    = $request->old_request_letter ?? $transaction->request_letter;
            $billingQr        = $transaction->billing_qr;
            $billingCode      = $transaction->billing_code;
            $rejectionReason  = $transaction->rejection_reason;


            if ($request->hasFile('payment_receipt')) {
                $path = $request->file('payment_receipt')->store('uploads/payment_receipt', 'public');
                $paymentReceipt = basename($path);
            }
            if ($request->hasFile('request_letter')) {
                $path = $request->file('request_letter')->store('uploads/request_letter', 'public');
                $requestLetter = basename($path);
            }

            if ($request->status === 'rejected') {
                $rejectionReason = $request->rejection_reason;
                $billingCode = null;
                $billingQr   = null;
            }

            if ($request->status === 'waiting_payment') {
                $billingCode = $request->billing_code;
                if ($request->hasFile('billing_qr')) {
                    $path = $request->file('billing_qr')->store('uploads/billing_qr', 'public');
                    $billingQr = basename($path);
                }
                $rejectionReason = null;
            }

            $propertyId = $request->ruangan_id ?? $transaction->property_id;

            $transaction->update([
                'instansi'         => ucwords($request->office),
                'kegiatan'         => ucwords($request->event),
                'property_id'      => $propertyId,
                'description'      => $request->description,
                'status'           => $request->status,
                'rejection_reason' => $rejectionReason,
                'billing_code'     => $billingCode,
                'billing_qr'       => $billingQr,
                'start'            => $request->start,
                'end'              => $request->end,
                'total_harga'      => $request->total_harga,
                'phone_number'     => $request->phone_number,
                'email'            => $request->email,
                'affiliation'      => $request->affiliation,
                'ordered_unit'     => $request->ordered_unit ?? $transaction->ordered_unit,
                'payment_receipt'  => $paymentReceipt,
                'request_letter'   => $requestLetter,
            ]);
        });

        return back()->with('success', 'Transaksi berhasil diperbarui (detail kamar tidak diubah).');
    }










    // $transaction->save();

    // return redirect()->route('transactions.ruangan.show')
    //     ->with('success', 'Jadwal berhasil diubah');


    // $isValidate = $request->validate([
    //     'office' => 'required|string|max:32',
    //     'event' => 'required|string|max:32',
    //     'venue' => 'required',
    //     'description' => 'nullable|string',
    // ]);

    // if (!$isValidate) {
    //     return redirect()->route('transactions.ruangan.show')->withInput($request->all());
    // }

    // $transaction = Transaction::find($id);
    // $transaction->instansi = ucfirst($request->office);
    // $transaction->kegiatan = ucfirst($request->event);
    // $transaction->property_id = $request->venue;
    // $transaction->description = $request->description;
    // $transaction->save();

    // return redirect()->route('transactions.ruangan.show')->with('success', 'Jadwal berhasil diubah');

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

        return view('admin.transaction-ruangan-detail', compact('transactions', 'ruangan'));
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

    /* ========================================================
               /$$                                  
              |__/                                  
 /$$  /$$  /$$ /$$  /$$$$$$$ /$$$$$$/$$$$   /$$$$$$ 
| $$ | $$ | $$| $$ /$$_____/| $$_  $$_  $$ |____  $$
| $$ | $$ | $$| $$|  $$$$$$ | $$ \ $$ \ $$  /$$$$$$$
| $$ | $$ | $$| $$ \____  $$| $$ | $$ | $$ /$$__  $$
|  $$$$$/$$$$/| $$ /$$$$$$$/| $$ | $$ | $$|  $$$$$$$
 \_____/\___/ |__/|_______/ |__/ |__/ |__/ \_______/
// ======================================================== */

    public function check_expired()
    {
        $transactions = Wisma::where('end', '<', now()->toDateString())->get();
        $transactions->each(function ($item) {
            $item->isOut = 1;
            $item->save();
        });
    }

    public function check_available_asrama($start, $end, $room)
    {
        $transactions = Wisma::where('room', $room)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end]);
            })
            ->get();

        return $transactions;
    }

    public function wisma_store(Request $request)
    {
        $isValidate = $request->validate([
            'name' => 'required|string|max:32',
            'asal' => 'required|string|max:32',
            'kegiatan' => 'max:32',
            'rooms' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        if (!$isValidate) {
            return redirect()->route('transactions.wisma.show');
        }

        $rooms = explode(',', $request->rooms);

        // error untuk orang yang belum dapet ruangan
        $errorRoom = [];
        foreach ($rooms as $room) {

            $transactions = $this->check_available_asrama($request->start, $request->end, $room);
            if ($transactions->count() > 0) {
                // return redirect()->route('transactions.wisma.show')
                //     ->with('failed', 'Kamar sudah terpakai');
                array_push($errorRoom, $room);
                continue;
            }

            Wisma::create([
                'name' => ucfirst($request->name),
                'from' => ucfirst($request->asal),
                'kegiatan' => ucfirst($request->kegiatan),
                'room' => $room,
                'start' => $request->start,
                'end' => $request->end,
            ]);
        }

        if (count($errorRoom) > 0) {
            $listRooms =  implode(',', $errorRoom);

            return redirect()->route('transactions.wisma.show')
                ->with('failed', "$request->name kamar $listRooms, gagal ditambahkan karena ruangan sudah digunakan")
                ->withInput($request->all());
        }

        return redirect()->route('transactions.wisma.show')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function wisma_update(Request $request, $id)
    {
        $isValidate = $request->validate([
            'name' => 'required|string|max:32',
            'asal' => 'required|string|max:32',
            'kegiatan' => 'string|max:32',
        ]);

        if (!$isValidate) {
            return redirect()->route('transactions.wisma.show')->with('failed', 'Data tidak valid');
        }

        Wisma::where('id', $id)->update([
            'name' => ucfirst($request->name),
            'from' => ucfirst($request->asal),
            'kegiatan' => ucfirst($request->kegiatan),
        ]);

        return redirect()->route('wisma-admin')
            ->with('success', 'Data berhasil diubah');
    }

    public function wisma_show_admin()
    {
        $this->check_expired();

        $wisma = Wisma::all();
        return view('admin.index-wisma', [
            'transactions' => $wisma
        ]);
    }

    public function wisma_show()
    {
        $this->check_expired();
        $today = now()->toDateString();

        $wismas = Wisma::where('isOut', 0)
            ->whereRaw('? BETWEEN start AND end', [$today])
            ->get();

        if (auth()->check()) {
            if (auth()->user()->role == 'admin') {
                return view('admin.transaction-wisma', [
                    'wisma' => $wismas->pluck('room'),
                    'nama' => $wismas->pluck('name'),
                    'kegiatan' => $wismas->pluck('kegiatan'),
                ]);
            }
        }

        return view('admin.transaction-wisma', [
            'wisma' => $wismas->pluck('room'),
            'nama' => $wismas->pluck('name'),
            'kegiatan' => $wismas->pluck('kegiatan'),
        ]);
    }

    public function wisma_destroy(Request $request)
    {
        $ids = explode(',', $request->selected);
        Wisma::destroy($ids);
        return redirect()->route('wisma-admin');
    }

    public function wisma_export()
    {
        $now = now()->toDateString();
        return Excel::download(new WismaExports, "$now-rekap-wisma.xlsx");
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
}
