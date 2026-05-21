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


        $csNumber = env('CS_WHATSAPP_NUMBER', '6281230143714');

        $name = $request->name;
        $phone = $request->phone;
        $message = $request->message;

        $text = rawurlencode("Halo, saya {$name} ({$phone}).\n\n{$message}");

        $waUrl = "https://wa.me/{$csNumber}?text={$text}";

        return redirect()->away($waUrl);
    }
}
