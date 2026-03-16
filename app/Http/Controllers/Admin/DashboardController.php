<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = today();
        $yesterday = today()->subDay();
        $lastWeek  = today()->subWeek();

        // ---- Stat cards ----
        $bookingsToday     = Booking::whereDate('booking_date', $today)->count();
        $bookingsYesterday = Booking::whereDate('booking_date', $yesterday)->count();

        $pendingDepositsCount = Deposit::pending()->count();
        $expiredDepositsCount = Deposit::expired()->count();
        $pendingDepLastWeek   = Deposit::pending()->where('created_at', '<', $lastWeek)->count();

        $totalMembers     = User::members()->count();
        $newMembersThisWeek = User::members()->where('created_at', '>=', $lastWeek)->count();

        $activeVouchers     = Voucher::active()->count();
        $activeVouchersLastWeek = Voucher::active()->where('created_at', '<', $lastWeek)->count();

        $stats = [
            'total_bookings_today'  => $bookingsToday,
            'bookings_growth'       => $bookingsYesterday > 0 ? round((($bookingsToday - $bookingsYesterday) / $bookingsYesterday) * 100, 1) : 0,
            'pending_deposits'      => $pendingDepositsCount,
            'expired_deposits'      => $expiredDepositsCount,
            'total_members'         => $totalMembers,
            'new_members_week'      => $newMembersThisWeek,
            'active_vouchers'       => $activeVouchers,
        ];

        // ---- Weekly chart: last 7 days visits ----
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D'); // Sen, Sel, ...
            $chartData[]   = Booking::whereDate('booking_date', $date)->count();
        }

        // ---- Pending deposits (sidebar list, latest 5) ----
        $pendingDeposits = Deposit::pending()
            ->with(['booking.user', 'booking.treatment'])
            ->orderBy('deadline_at')
            ->limit(5)
            ->get();

        // ---- Today's bookings table (limit 5) ----
        $todayBookings = Booking::with(['user', 'treatment', 'doctor'])
            ->whereDate('booking_date', $today)
            ->orderBy('booking_time')
            ->limit(5)
            ->get();

        $todayBookingsTotal = Booking::whereDate('booking_date', $today)->count();

        // ---- Upcoming bookings (next 7 days, limit 5) ----
        $upcomingBookings = Booking::with(['user', 'treatment', 'doctor'])
            ->where('booking_date', '>', $today)
            ->where('booking_date', '<=', today()->addDays(7))
            ->whereNotIn('status', ['cancelled', 'no_show', 'expired'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'chartLabels',
            'chartData',
            'pendingDeposits',
            'todayBookings',
            'todayBookingsTotal',
            'upcomingBookings'
        ));
    }
}
