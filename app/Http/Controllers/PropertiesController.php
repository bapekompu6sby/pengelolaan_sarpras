<?php

namespace App\Http\Controllers;



use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PropertiesImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PropertiesController extends Controller
{
    public function updateStatus(Properties $property, Request $request)
    {
        $data = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $property->status = $data['is_active'] ? 'ative' : 'inactive'; // ejaan enum kamu
        $property->save();

        return response()->json([
            'ok' => true,
            'status' => $property->status,
        ]);
    }




    public function checkAvailability(Request $request)
    {
        $request->validate([
            'venue_id'     => 'required|integer',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date',
            'ordered_unit' => 'required|integer',
            'jam_start'    => 'nullable|date_format:H:i',
            'jam_end'      => 'nullable|date_format:H:i',
            'property_type' => 'nullable|string',
        ]);

        $propertyId = $request->venue_id;
        $start      = $request->start_date;
        $end        = $request->end_date;
        $unit       = $request->ordered_unit;
        $jamStart   = $request->jam_start;
        $jamEnd     = $request->jam_end;
        $type       = strtolower($request->property_type ?? '');

        $prop = Properties::findOrFail($propertyId);

        // Base query: cek transaksi yang sudah approved di rentang tanggal yang diminta
        $transactions = Transaction::where('property_id', $propertyId)
            ->where('status', '=', 'approved')
            ->where(function ($query) use ($start, $end) {
                // Overlap: transaksi yang start atau end-nya ada di dalam rentang yang diminta
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        // Kasus: transaksi yang "membungkus" rentang yang diminta
                        $q->where('start', '<=', $start)->where('end', '>=', $end);
                    });
            });

        // ✅ Filter jam HANYA untuk tipe fasilitas
        // Untuk aula, kelas, asrama, paviliun: SEMUA transaksi di rentang tanggal dihitung sebagai konflik
        if ($type === 'fasilitas') {
            // Abaikan transaksi fasilitas yang tidak punya data jam
            $transactions->whereNotNull('transactions.jam_start')
                ->whereNotNull('transactions.jam_end');

            // Cek overlap jam (termasuk skenario lintas tengah malam)
            if ($jamStart && $jamEnd) {
                $reqWrap = strtotime($jamEnd) <= strtotime($jamStart);

                $transactions->where(function ($q) use ($jamStart, $jamEnd, $reqWrap) {
                    if (!$reqWrap) {
                        // NORMAL: existing_start < req_end AND existing_end > req_start
                        $q->whereRaw('TIME(transactions.jam_start) < ? AND TIME(transactions.jam_end) > ?', [
                            $jamEnd,   // req_end
                            $jamStart, // req_start
                        ]);
                    } else {
                        // LINTAS TENGAH MALAM: existing_start < req_end OR existing_end > req_start
                        $q->where(function ($qq) use ($jamStart, $jamEnd) {
                            $qq->whereRaw('TIME(transactions.jam_start) < ?', [$jamEnd])
                                ->orWhereRaw('TIME(transactions.jam_end) > ?', [$jamStart]);
                        });
                    }
                });
            }
        }

        $transactions     = $transactions->get();
        $transactions_unit = $transactions->sum('ordered_unit');

        $avail_unit = $prop->unit - $transactions_unit;
        $avail      = $avail_unit >= $unit;

        return response()->json([
            'available'    => $avail,
            'avail_count'  => $avail_unit,
            'checked_type' => $type,
            'debug'        => [
                'start'     => $start,
                'end'       => $end,
                'jam_start' => $jamStart,
                'jam_end'   => $jamEnd,
            ],
        ]);
    }


    public function index()
    {
        $properties = Properties::all();
        return view('admin.properties.index', [
            'properties' => $properties
        ]);
    }

    public function show()
    {
        $ruangan = Properties::all();

        return view('admin.transaction', [
            'aulaKelas' => $ruangan,
        ]);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name'       => 'required|string|max:32',
    //         'type'       => 'required|string|max:10',
    //         'capacity'   => 'required|integer|min:1|max:1000',
    //         'room_type'  => 'nullable|string|max:50',
    //         'area'       => 'nullable|string|max:50',
    //         'facilities' => 'nullable|string',
    //         'price'      => 'nullable|numeric|min:0',
    //         'unit'       => 'nullable|integer|min:0',
    //         'img'        => 'nullable|image|mimes:jpg,jpeg,png',
    //     ]);

    //     $data = [
    //         'name'       => ucfirst($request->name),
    //         'type'       => $request->type,
    //         'capacity'   => $request->capacity,
    //         'room_type'  => $request->room_type,
    //         'area'       => $request->area,
    //         'facilities' => $request->facilities,
    //         'price'      => $request->price,
    //         'unit'       => $request->unit ?? 0,
    //     ];

    //     if ($request->hasFile('img')) {
    //         $file = $request->file('img');
    //         $filename = $file->hashName(); // nama unik otomatis
    //         $file->move(public_path('uploads'), $filename);
    //         $data['image_path'] = $filename;
    //     }

    //     Properties::create($data);

    //     return redirect()->route('properties')->with('success', 'Data berhasil ditambahkan');
    // }



    // public function update(Request $request, $id)
    // {
    //     $property = Properties::find($id);

    //     if ($property === null) {
    //         return redirect()->route('properties')->with('failed', 'Data tidak ditemukan');
    //     }

    //     // Validasi input
    //     $request->validate([
    //         'name'       => 'required|string|max:32',
    //         'type'       => 'required|string|max:10',
    //         'capacity'   => 'required|integer|min:1|max:1000',
    //         'room_type'  => 'nullable|string|max:50',
    //         'area'       => 'nullable|string|max:50',
    //         'facilities' => 'nullable|string',
    //         'price'      => 'nullable|numeric|min:0',
    //         'unit'       => 'nullable|integer|min:0',
    //         'img'        => 'nullable|image|mimes:jpg,jpeg,png',
    //     ]);

    //     // Data yang akan di-update
    //     $updateData = [
    //         'name'       => $request->name,
    //         'type'       => $request->type,
    //         'capacity'   => $request->capacity,
    //         'room_type'  => $request->room_type,
    //         'area'       => $request->area,
    //         'facilities' => $request->facilities,
    //         'price'      => $request->price,
    //         'unit'       => $request->unit ?? 0, // default 0 kalau null
    //     ];

    //     // Kalau ada upload gambar baru
    //     if ($request->hasFile('img')) {
    //         // Hapus gambar lama kalau ada
    //         if ($property->image_path && file_exists(public_path('uploads/' . $property->image_path))) {
    //             unlink(public_path('uploads/' . $property->image_path));
    //         }

    //         // Simpan gambar baru
    //         $file = $request->file('img');
    //         $filename = $file->hashName(); // nama unik otomatis
    //         $file->move(public_path('uploads'), $filename);
    //         $updateData['image_path'] = $filename;
    //     }


    //     $property->update($updateData);

    //     return redirect()->route('properties')->with('success', 'Data berhasil diubah');
    // }



    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:32',
            'type'       => 'required|string|max:10',
            'capacity'   => 'required|integer|min:1|max:1000',
            'room_type'  => 'nullable|string|max:50',
            'area'       => 'nullable|string|max:50',
            'facilities' => 'nullable|string',
            'price'      => 'nullable|numeric|min:0',
            'unit'       => 'nullable|integer|min:0',

            // cover (opsional)
            'img'        => 'nullable|image|mimes:jpg,jpeg,png|max:20480',

            // galeri (opsional, multiple) → untuk tombol +
            'gallery'    => 'nullable|array|max:20',
            'gallery.*'  => 'image|mimes:jpg,jpeg,png|max:20480',
        ]);

        $data = [
            'name'       => ucfirst($request->name),
            'type'       => $request->type,
            'capacity'   => $request->capacity,
            'room_type'  => $request->room_type,
            'area'       => $request->area,
            'facilities' => $request->facilities,
            'price'      => $request->price,
            'unit'       => $request->unit ?? 0,
        ];

        DB::transaction(function () use ($request, &$data) {
            // Simpan cover (nama file saja)
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $name = $file->hashName();
                $file->storeAs('uploads/properties/covers', $name, 'public');
                $data['image_path'] = $name;
            }

            /** @var Properties $property */
            $property = Properties::create($data);

            // Simpan galeri (opsional)
            $galleryFiles = $request->file('gallery', []);
            if (!is_array($galleryFiles)) $galleryFiles = [];

            foreach ($galleryFiles as $file) {
                $name = $file->hashName();
                $file->storeAs('uploads/properties/gallery', $name, 'public');

                PropertiesImage::create([
                    'property_id' => $property->id,
                    'image_path'  => $name,  // simpan NAMA file saja
                ]);
            }
        });

        return redirect()->route('properties')->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        /** @var Properties|null $property */
        $property = Properties::find($id);
        if (!$property) {
            return redirect()->route('properties')->with('failed', 'Data tidak ditemukan');
        }

        $request->validate([
            'name'       => 'required|string|max:32',
            'type'       => 'required|string|max:10',
            'capacity'   => 'required|integer|min:1|max:1000',
            'room_type'  => 'nullable|string|max:50',
            'area'       => 'nullable|string|max:50',
            'facilities' => 'nullable|string',
            'price'      => 'nullable|numeric|min:0',
            'unit'       => 'nullable|integer|min:0',

            // cover
            'img'           => 'nullable|image|mimes:jpg,jpeg,png|max:20480',
            'remove_cover'  => 'nullable|boolean', // <-- TAMBAH INI

            // galeri (+ / -)
            'gallery'       => 'nullable|array|max:20',
            'gallery.*'     => 'image|mimes:jpg,jpeg,png|max:20480',
            'remove_gallery' => 'nullable|array',
            'remove_gallery.*' => 'integer',
        ]);

        $updateData = [
            'name'       => ucfirst($request->name),
            'type'       => $request->type,
            'capacity'   => $request->capacity,
            'room_type'  => $request->room_type,
            'area'       => $request->area,
            'facilities' => $request->facilities,
            'price'      => $request->price,
            'unit'       => $request->unit ?? 0,
        ];

        DB::transaction(function () use ($request, $property, &$updateData) {
            /* =========================
         * 1) COVER: ganti / hapus
         * =======================*/
            if ($request->hasFile('img')) {
                // ganti cover dengan file baru
                $newFile = $request->file('img');
                $newName = $newFile->hashName();
                $newFile->storeAs('uploads/properties/covers', $newName, 'public');

                // hapus file lama bila ada
                if (!empty($property->image_path)) {
                    Storage::disk('public')->delete('uploads/properties/covers/' . $property->image_path);
                }

                $updateData['image_path'] = $newName;
            } elseif ($request->boolean('remove_cover')) {
                // hapus cover TANPA upload baru
                if (!empty($property->image_path)) {
                    Storage::disk('public')->delete('uploads/properties/covers/' . $property->image_path);
                }
                $updateData['image_path'] = null; // kosongkan di DB
            }

            // simpan perubahan properti
            $property->update($updateData);

            /* =========================
         * 2) HAPUS FOTO GALERI (−)
         * =======================*/
            $removeIds = $request->input('remove_gallery', []);
            if (!empty($removeIds)) {
                // pakai nama model yang benar di project kamu: PropertiesImage atau PropertyImage
                $imagesToDelete = \App\Models\PropertiesImage::whereIn('id', $removeIds)
                    ->where('property_id', $property->id)
                    ->get(['id', 'image_path']);

                if ($imagesToDelete->isNotEmpty()) {
                    Storage::disk('public')->delete(
                        $imagesToDelete->map(fn($img) => 'uploads/properties/gallery/' . $img->image_path)->all()
                    );
                    \App\Models\PropertiesImage::whereIn('id', $imagesToDelete->pluck('id'))->delete();
                }
            }

            /* =========================
         * 3) TAMBAH FOTO GALERI (+)
         * =======================*/
            $galleryFiles = $request->file('gallery', []);
            if (!is_array($galleryFiles)) $galleryFiles = [];

            foreach ($galleryFiles as $file) {
                $name = $file->hashName();
                $file->storeAs('uploads/properties/gallery', $name, 'public');

                \App\Models\PropertiesImage::create([
                    'property_id' => $property->id,
                    'image_path'  => $name,
                ]);
            }
        });

        return redirect()->route('properties')->with('success', 'Data berhasil diubah');
    }



    public function destroy($id)
    {
        $property = Properties::find($id);

        if (!$property) {
            return redirect()->route('properties')
                ->with('failed', 'Data tidak ditemukan');
        }

        // hapus semua transaksi yang terkait property ini
        $property->transactions()->delete();

        // hapus property
        $property->delete();

        return redirect()->route('properties')
            ->with('success', 'Data beserta transaksi terkait berhasil dihapus');
    }




    public function getPropertyById($id)
    {
        // load relasi images biar ikut diserialisasi ke JSON
        $property = Properties::with('images')->find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        return response()->json([
            'property' => $property,
            'user'     => auth()->user(),
        ]);
    }
}
