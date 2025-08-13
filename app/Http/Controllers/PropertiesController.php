<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\Transaction;

class PropertiesController extends Controller
{

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'venue_id'    => 'required|integer',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'ordered_unit' => 'required|integer'
        ]);

        $propertyId = $request->venue_id;
        $start  = $request->start_date;
        $end    = $request->end_date;
        $unit = $request->ordered_unit;

        $exists = Transaction::where('property_id', $propertyId)
            ->where('status', '=', 'approved') // Optional
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start', [$start, $end])
                    //   ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function($query) use ($start, $end) {
                          $query->where('start', '<=', $start)
                                ->where('end', '>=', $end);
                      });
            })
            ->count();

        $avail = false;
        $prop = Properties::find($propertyId);
        $avail_unit = $prop->unit - $exists;

        if ($avail_unit >= $unit){
            $avail = true;
        };
        

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
        $ruangan = Properties::whereIn('type', ['aula', 'kelas'])->get();

        return view('admin.transaction', [
            'aulaKelas' => $ruangan,
        ]);
    }

    public function store(Request $request)
    {
        $isValdate = $request->validate([
            'name' => 'required|string|max:32',
            'type' => 'required|string|max:10',
            'capacity' => 'required|int|min:1|max:1000',
            'image' => 'nullable|image|max:4096',
            'property_type'=> 'required|string|',
            'price'=> 'required|string|'
        ]);

        if ($isValdate) {
            // Properties::create([
            //     'name' => ucfirst($request->name),
            //     'type' => $request->type,
            //     'capacity' => $request->capacity,
            // ]);

            $property = new Properties();
            $property->name = ucfirst($request->name);
            $property->type = $request->type;
            $property->capacity = $request->capacity;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = Storage::disk('ftp')->put('', $image);

                $property->image_path = $path; // Save FTP path in DB
            }

            $property->save();

            return redirect()->route('properties')->with('success', 'Data berhasil ditambahkan');
        }

        return view('admin.index-properties')->withInput($request->name);
    }

    public function update(Request $request, $id)
    {
        $ifFound = Properties::find($id);

        if ($ifFound === null) {
            return redirect()->route('properties')->with('failed', 'Data tidak ditemukan');
        }
        
        $isValdate = $request->validate([
            'name' => 'required|string|max:32',
            'type' => 'required|string|max:10',
            'capacity' => 'required|int|min:1|max:1000',
        ]);

        if ($isValdate) {
            Properties::where('id', $id)->update([
                'name' => $request->name,
                'type' => $request->type,
                'capacity' => $request->capacity,
            ]);

            return redirect()->route('properties')->with('success', 'Data berhasil diubah');
        }
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
}
