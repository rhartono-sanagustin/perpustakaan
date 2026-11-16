<?php

namespace App\Http\Middleware;

use App\Models\LibrarySetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVisitKioskIsEnabled
{
    /**
     * Tolak akses kiosk saat admin menonaktifkannya dan cegah cache halaman.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless((bool) LibrarySetting::getValue('visit_kiosk_enabled', true), 404);

        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, max-age=0');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
