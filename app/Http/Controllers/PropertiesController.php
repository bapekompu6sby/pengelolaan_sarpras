<?php

namespace App\Http\Controllers;



use App\Models\Properties;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PropertiesController extends Controller
{


    public function checkAvailability(Request $request)
    {
        Log::info('=== CHECK AVAILABILITY START ===');
        Log::info('Request data:', $request->all());

        $request->validate([
            'venue_id'    => 'required|integer',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'ordered_unit' => 'required|integer'
        ]);

        $propertyId = $request->venue_id;
        $start  = $request->start_date;
        $end    = $request->end_date;
        $unit = $request->ordered_unit;

        $transactions = Transaction::where('property_id', $propertyId)
            ->where('status', '=', 'approved') // Optional
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start', [$start, $end])
                    ->orWhereBetween('end', [$start, $end]);
            })->get();
        $transactions_unit = $transactions->sum('ordered_unit');
        $avail = false;
        $prop = Properties::find($propertyId);
        $avail_unit = $prop->unit - $transactions_unit;

        Log::info("Property Unit: {$prop->unit}");
        Log::info("Available Unit After Check: {$avail_unit}");

        if ($avail_unit >= $unit) {
            $avail = true;
        }

        Log::info("Final Availability Status: " . ($avail ? 'AVAILABLE' : 'NOT AVAILABLE'));
        Log::info('=== CHECK AVAILABILITY END ===');

        return response()->json([
            'available' => $avail,
            'avail_count' => $avail_unit
        ]);
    }

    public function index()
    {
        $properties = Properties::all();
        return view('admin.index-properties', [
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
            'img'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = $file->hashName(); // nama unik otomatis
            $file->move(public_path('uploads'), $filename);
            $data['image_path'] = $filename;
        }

        Properties::create($data);

        return redirect()->route('properties')->with('success', 'Data berhasil ditambahkan');
    }



    public function update(Request $request, $id)
    {
        $property = Properties::find($id);

        if ($property === null) {
            return redirect()->route('properties')->with('failed', 'Data tidak ditemukan');
        }

        // Validasi input
        $request->validate([
            'name'       => 'required|string|max:32',
            'type'       => 'required|string|max:10',
            'capacity'   => 'required|integer|min:1|max:1000',
            'room_type'  => 'nullable|string|max:50',
            'area'       => 'nullable|string|max:50',
            'facilities' => 'nullable|string',
            'price'      => 'nullable|numeric|min:0',
            'unit'       => 'nullable|integer|min:0',
            'img'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Data yang akan di-update
        $updateData = [
            'name'       => $request->name,
            'type'       => $request->type,
            'capacity'   => $request->capacity,
            'room_type'  => $request->room_type,
            'area'       => $request->area,
            'facilities' => $request->facilities,
            'price'      => $request->price,
            'unit'       => $request->unit ?? 0, // default 0 kalau null
        ];

        // Kalau ada upload gambar baru
        if ($request->hasFile('img')) {
            // Hapus gambar lama kalau ada
            if ($property->image_path && file_exists(public_path('uploads/' . $property->image_path))) {
                unlink(public_path('uploads/' . $property->image_path));
            }

            // Simpan gambar baru
            $file = $request->file('img');
            $filename = $file->hashName(); // nama unik otomatis
            $file->move(public_path('uploads'), $filename);
            $updateData['image_path'] = $filename;
        }


        $property->update($updateData);

        return redirect()->route('properties')->with('success', 'Data berhasil diubah');
    }




    public function destroy($id)
    {
        $ifFound = Properties::find($id);
        if ($ifFound === null) {
            return redirect()->route('properties')->with('failed', 'Data tidak ditemukan');
        }

        Properties::destroy($id);
        return redirect()->route('properties')->with('success', 'Data berhasil dihapus');
    }

    public function showImage(Properties $properties)
    {
        $path = $properties->image_path;

        if (!Storage::disk('ftp')->exists($path)) {
            abort(404, 'Image not found on FTP');
        }

        $file = Storage::disk('ftp')->get($path);
        $mime = Storage::disk('ftp')->mimeType($path);

        return response($file, 200)->header('Content-Type', $mime);
    }

    public function getPropertyById($id)
    {
        $property = Properties::find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $user = auth()->user();

        return response()->json([
            'property' => $property,
            'user' => $user,
        ]);
    }
}
