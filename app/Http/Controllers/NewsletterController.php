<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Subscribe a new or returning user to the newsletter list.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if ($subscriber) {
            if ($subscriber->is_active) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Alamat email ini sudah terdaftar sebagai pelanggan newsletter kami.',
                    ]);
                }
                return back()->with('error', 'Alamat email ini sudah terdaftar sebagai pelanggan newsletter kami.');
            }

            // Re-activate inactive subscriber
            $subscriber->update([
                'is_active' => true,
                'confirmed_at' => now(),
            ]);

            $msg = 'Terima kasih telah bergabung kembali! Langganan newsletter Anda telah diaktifkan kembali.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }
            return back()->with('success', $msg);
        }

        // Create new subscriber record
        NewsletterSubscriber::create([
            'email'        => $request->email,
            'is_active'    => true,
            'confirmed_at' => now(),
        ]);

        $msg = 'Selamat! Alamat email Anda berhasil didaftarkan untuk mendapatkan newsletter.';
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }
        return back()->with('success', $msg);
    }
}
