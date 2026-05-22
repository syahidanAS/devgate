<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret_key');
        
        if (empty($secret) || empty(config('services.recaptcha.site_key'))) {
            // Bypass validation if keys are not set yet (for local development ease)
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (!$response->successful() || !($response->json('success'))) {
            $fail('Verifikasi reCAPTCHA gagal. Pastikan Anda bukan robot.');
        }
    }
}
