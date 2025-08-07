<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;

class PropertiesController extends Controller
{
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
        ]);

        if ($isValdate) {
            Properties::create([
                'name' => ucfirst($request->name),
                'type' => $request->type,
                'capacity' => $request->capacity,
            ]);

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
}
