<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use Illuminate\Http\Request;

class PropertiesControllerAsUser extends Controller
{
    public function index()
    {
        $properties = Properties::query()
        ->where('status', 'ative') // hanya properti yang aktif yang ditampilkan
            ->orderByRaw("FIELD(type, 'aula','kelas','asrama','paviliun', 'fasilitas')")
            ->get();


        return view('user.bookings.index', [
            'properties' => $properties
        ]);
    }
}
