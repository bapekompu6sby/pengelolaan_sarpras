<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Properties;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Properties::with('kamar')
            ->whereIn('type', ['asrama', 'paviliun'])
            ->get();

        return view('admin.kamar', compact('properties'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'penginapan_id' => 'required|exists:properties,id',
            'nama_kamar'    => 'required|string|max:100',
            'kapasitas'     => 'required|integer|min:1',
        ]);

        // Simpan ke database
        Kamar::create([
            'penginapan_id' => $request->penginapan_id,
            'nama_kamar'    => $request->nama_kamar,
            'kapasitas'     => $request->kapasitas,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Kamar berhasil ditambahkan!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'nama_kamar' => 'required|string|max:100',
        ]);

        // Ambil kamar berdasarkan ID
        $kamar = Kamar::findOrFail($id);

        // Update data
        $kamar->nama_kamar = $request->nama_kamar;
        $kamar->save();

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Kamar berhasil diupdate!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Cari kamar berdasarkan ID
        $kamar = Kamar::find($id);

        if (!$kamar) {
            return redirect()->back()->with('failed', 'Kamar tidak ditemukan.');
        }

        // Hapus kamar
        $kamar->delete();

        return redirect()->back()->with('success', 'Kamar berhasil dihapus.');
    }
}
