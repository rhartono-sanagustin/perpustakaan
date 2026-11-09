<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard peminjam.
     */
    public function index(): View
    {
        return view('borrower.dashboard');
    }
}
