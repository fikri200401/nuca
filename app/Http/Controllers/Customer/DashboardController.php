<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $totalBookings = $user->bookings()->count();
        $completedBookings = $user->bookings()->where('status', 'completed')->count();
        $pendingBookings = $user->bookings()->whereIn('status', ['pending', 'confirmed', 'waiting_payment'])->count();
        
        $recentBookings = $user->bookings()
            ->with(['treatment', 'doctor'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->limit(5)
            ->get();

        return view('customer.dashboard', compact('user', 'totalBookings', 'completedBookings', 'pendingBookings', 'recentBookings'));
    }
}
