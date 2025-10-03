<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use Illuminate\Http\Request;

class PropertiesControllerAsUser extends Controller
{
    public function index()
    {
        $properties = Properties::query()
            ->with('images') // eager load biar ga N+1
            ->where('status', 'ative') // typo 'active' -> 'ative') // typo 'ative' -> 'active'
            ->orderByRaw("FIELD(type, 'aula','kelas','asrama','paviliun','fasilitas')")
            ->get();

        

        return view('user.bookings.index', compact('properties'));
    }
}
