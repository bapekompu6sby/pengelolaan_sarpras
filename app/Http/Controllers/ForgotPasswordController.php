<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function shareLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        // 1) Sukses kirim link
        if ($status === Password::RESET_LINK_SENT || $status === 'passwords.sent') {
            return back()->with('success', 'Link reset password sudah terkirim ke email kamu. Cek inbox atau folder spam ya.');
        }

        // 2) Email tidak ditemukan di database
        if (
            (defined('Illuminate\\Support\\Facades\\Password::INVALID_USER') && $status === Password::INVALID_USER)
            || $status === 'passwords.user'
        ) {
            return back()->with('failed', 'Masukkan email yang pernah kamu daftarkan.');
        }


        // 4) Fallback untuk status lain (biar tetap informatif)
        return back()->with('failed', __($status));
    }


    public function showResetForm(Request $request, $token)
    {


        $decryptedEmail = $request->email;


        return view('auth.reset-password', [
            'token' => $token,
            'email' => $decryptedEmail
        ]);
    }
    public function reset(Request $request)
    {

        //     try {
        //         $email = $request->input('email');
        //     } catch (\Exception $e) {
        //         return back()->withErrors([
        //             'email' => 'Decrypt gagal: ' . $e->getMessage(),
        //         ]);
        //     }

        $email = $request->input('email');



        $request->merge(['email' => $email]);

        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );

        // return $status == Password::PASSWORD_RESET
        //     ? redirect()->route('guest.landing_pages')->with('status', __($status))
        //     : back()->withErrors(['email' => [__($status)]]);

        if ($status == Password::PASSWORD_RESET) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Reset password berhasil. Anda sudah login.');
        } else {
            return back()->withErrors([
                'email' => 'Reset password gagal: ' . __($status),
            ]);
        }
    }
}
