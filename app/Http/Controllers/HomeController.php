<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('welcome', [
            'carouselSlides' => CarouselSlide::query()->active()->ordered()->get(),
        ]);
    }
}
