<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertiesControllerAsUser extends Controller
{
    public function index()
    {
        $properties = \App\Models\Properties::all();
        return view('user.properties_as_user', [
            'properties' => $properties
        ]);
    }
}
