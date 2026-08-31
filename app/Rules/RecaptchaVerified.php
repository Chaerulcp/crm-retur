<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Verifikasi respons Google reCAPTCHA v2 di sisi server.
 *
 * Aturan hanya bermakna ketika services.recaptcha.secret terisi;
 * bila kosong (CAPTCHA tidak dikonfigurasi) verifikasi dilewati.
 */
class RecaptchaVerified implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret');

        if (blank($secret)) {
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => (string) $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->json('success')) {
            $fail('Verifikasi CAPTCHA gagal. Silakan coba lagi.');
        }
    }
}