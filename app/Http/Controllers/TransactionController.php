<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Properties;
use App\Models\Wisma;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RuanganExports;
use App\Exports\WismaExports;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
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
        $colors = [
            'primary'   => '#0d6efd',
            'secondary' => '#6c757d',
            'success'   => '#198754',
            'info'      => '#0dcaf0',
            'warning'   => '#ffc107',
            'danger'    => '#dc3545',
            'dark'      => '#212529',
        ];

        $isValidate = $request->validate([
            'office' => 'required|string|max:32',
            'event' => 'required|string|max:32',
            'start' => 'required|date',
            'end' => 'required|date',
            'venue' => 'required',
            'description' => 'nullable|string',
        ]);

        if (!$isValidate) {
            return redirect()->route('transactions.ruangan.show')->withInput($request->all());
        }

        $transactions = $this->check_available_ruangan($request->start, $request->end, $request->venue);
        if ($transactions->count() > 0) {
            return redirect()->route('transactions.ruangan.show')
                ->with('failed', 'Ruangan sudah terpakai');
        }

        // generate random color from color list above
        $color = array_rand($colors, 1);

        Transaction::create([
            'instansi' => ucfirst($request->office),
            'kegiatan' => ucfirst($request->event),
            'start' => $request->start,
            'end' => $request->end,
            'color' => $colors[$color],
            'property_id' => $request->venue,
            'description' => $request->description,
        ]);

        return redirect()->route('transactions.ruangan.show')->with('success', 'Jadwal berhasil dibuat');
    }
    
    public function ruangan_update(Request $request, $id)
    {
        $isValidate = $request->validate([
            'office' => 'required|string|max:32',
            'event' => 'required|string|max:32',
            'venue' => 'required',
            'description' => 'nullable|string',
        ]);

        if (!$isValidate) {
            return redirect()->route('transactions.ruangan.show')->withInput($request->all());
        }

        $transaction = Transaction::find($id);
        $transaction->instansi = ucfirst($request->office);
        $transaction->kegiatan = ucfirst($request->event);
        $transaction->property_id = $request->venue;
        $transaction->description = $request->description;
        $transaction->save();

        return redirect()->route('transactions.ruangan.show')->with('success', 'Jadwal berhasil diubah');
    }

    public function ruangan_detail()
    {
        $transactions = Transaction::all();
        $ruangan = Properties::whereIn('type', ['aula', 'kelas'])->get();

        return view('admin.transaction-ruangan-detail', [
            'transactions' => $transactions,
            'ruangan' => $ruangan,
        ]);
    }

    public function ruangan_destroy()
    {
        $ids = explode(',', request()->selected);
        Transaction::destroy($ids);
        return redirect()->route('transactions.ruangan.show');
    
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

    public function check_expired() {
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

    public function wisma_show_admin() {
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

        if (auth()->check()){
            if (auth()->user()->role == 'admin') {
                return view('admin.transaction-wisma', [
                    'wisma' => $wismas->pluck('room'),
                    'nama' => $wismas->pluck('name'),
                    'kegiatan' => $wismas->pluck('kegiatan'),
                ]);
            }
        }
        
        return view('wisma.index', [
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

    public function calendar()
    {
        $propreties = Properties::all();
        // $events = Transaction::all();

        // $events = $events->map(function ($item) {
        //     return [
        //         'title' => $item->kegiatan,
        //         'venue' => $item->properties->name,
        //         'start' => $item->start,
        //         'end' => $item->end,
        //         'color' => $item->color,
        //     ];
        // });

        return view('calendar', [
            // 'events' => $events,
            'properties' => $propreties,
        ]);
    }

    public function events()
    {
        $events = Transaction::all();

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