<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login pengguna.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('status', __('messages.login_success', ['name' => $request->user()->name]));
    }

    /**
     * Proses logout pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', __('messages.logout_success'));
    }
}
