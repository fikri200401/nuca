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
        [$chartLabels, $chartVisitData, $chartRevenueData] = $this->buildChartData('minggu');

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
            'chartVisitData',
            'chartRevenueData',
            'pendingDeposits',
            'todayBookings',
            'todayBookingsTotal',
            'upcomingBookings'
        ));
    }

    /**
     * AJAX endpoint: return chart data for a given period
     */
    public function chartData(\Illuminate\Http\Request $request)
    {
        $period = $request->get('period', 'minggu');
        $from   = $request->get('from');
        $to     = $request->get('to');

        [$labels, $visitData, $revenueData] = $this->buildChartData($period, $from, $to);

        return response()->json([
            'labels'      => $labels,
            'visitData'   => $visitData,
            'revenueData' => $revenueData,
        ]);
    }

    private function buildChartData(string $period, ?string $from = null, ?string $to = null): array
    {
        $labels      = [];
        $visitData   = [];
        $revenueData = [];

        switch ($period) {
            case 'hari':
                // Last 24 hours by hour
                for ($i = 23; $i >= 0; $i--) {
                    $hour = Carbon::now()->subHours($i);
                    $labels[]      = $hour->format('H:00');
                    $visitData[]   = Booking::whereDate('booking_date', $hour->toDateString())
                        ->whereRaw('HOUR(booking_time) = ?', [$hour->hour])
                        ->count();
                    $revenueData[] = (float) Booking::whereDate('booking_date', $hour->toDateString())
                        ->whereRaw('HOUR(booking_time) = ?', [$hour->hour])
                        ->where('status', 'completed')
                        ->sum('final_price');
                }
                break;

            case 'bulan':
                // Last 30 days
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $labels[]      = $date->format('d/m');
                    $visitData[]   = Booking::whereDate('booking_date', $date)->count();
                    $revenueData[] = (float) Booking::whereDate('booking_date', $date)
                        ->where('status', 'completed')->sum('final_price');
                }
                break;

            case 'custom':
                // Use provided from/to dates, fallback to last 30 days
                $start = $from ? Carbon::parse($from)->startOfDay() : Carbon::today()->subDays(29);
                $end   = $to   ? Carbon::parse($to)->startOfDay()   : Carbon::today();

                // Limit to max 90 days to avoid huge response
                if ($start->diffInDays($end) > 90) {
                    $start = $end->copy()->subDays(89);
                }

                $current = $start->copy();
                while ($current <= $end) {
                    $labels[]      = $current->format('d/m');
                    $visitData[]   = Booking::whereDate('booking_date', $current)->count();
                    $revenueData[] = (float) Booking::whereDate('booking_date', $current)
                        ->where('status', 'completed')->sum('final_price');
                    $current->addDay();
                }
                break;

            default: // minggu
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $labels[]      = $date->translatedFormat('D');
                    $visitData[]   = Booking::whereDate('booking_date', $date)->count();
                    $revenueData[] = (float) Booking::whereDate('booking_date', $date)
                        ->where('status', 'completed')->sum('final_price');
                }
        }

        return [$labels, $visitData, $revenueData];
    }
}
