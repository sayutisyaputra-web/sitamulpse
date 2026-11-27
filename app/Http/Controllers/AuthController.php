<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Periksa Turnstile lebih dulu, sebelum email dan password dicek
        if (! $this->lolosTurnstile($request)) {
            LogAktivitas::catat('login_captcha_gagal', 'Email: ' . $data['email']);

            return back()
                ->withErrors(['captcha' => 'Verifikasi keamanan gagal. Muat ulang halaman lalu coba lagi.'])
                ->onlyInput('email');
        }

        // Hanya akun yang aktif yang boleh login
        $berhasil = Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password'], 'aktif' => true],
            $request->boolean('ingat')
        );

        if (! $berhasil) {
            LogAktivitas::catat('login_gagal', 'Email: ' . $data['email']);

            return back()
                ->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        LogAktivitas::catat('login');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        LogAktivitas::catat('logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Kirim token Turnstile ke Cloudflare untuk diverifikasi.
     * Jika token kosong, kunci belum diatur, atau Cloudflare tidak terjangkau, hasilnya gagal.
     */
    private function lolosTurnstile(Request $request): bool
    {
        $token  = $request->input('cf-turnstile-response');
        $secret = config('services.turnstile.secret_key');

        if (! $token || ! $secret) {
            return false;
        }

        try {
            $respon = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $e) {
            return false;
        }

        return $respon->successful() && $respon->json('success') === true;
    }
}