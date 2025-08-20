<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerServiceController extends Controller
{
    public function sendToWhatsapp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'message' => 'required|string|max:255',
        ]);


        $csNumber = '6285647234364';


        $name = $request->name;
        $phone = $request->phone;
        $message = $request->message;


        $text = "Halo, saya $name ($phone).%0A%0A$message";


        $waUrl = "https://wa.me/$csNumber?text=$text";


        return redirect()->away($waUrl);
    }
}
