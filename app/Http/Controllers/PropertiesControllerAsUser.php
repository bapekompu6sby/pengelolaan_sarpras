<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use Illuminate\Http\Request;

class PropertiesControllerAsUser extends Controller
{
    public function index()
    {
        $properties = Properties::orderBy('name', 'asc')->get();

        return view('user.properties_as_user', [
            'properties' => $properties
        ]);
    }
}
