<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Main report page — supports ?tab=pengunjung|pendapatan|treatment&from=&to=
     */
    public function index(Request $request)
    {
        $tab  = $request->get('tab', 'pengunjung');
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfWeek();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : Carbon::now()->endOfDay();

        // ---- Summary stats ----
        $totalPengunjung  = Booking::whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])->count();
        $totalPendapatan  = Booking::completed()->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])->sum('final_price');
        $rataRataTiket    = $totalPengunjung > 0 ? $totalPendapatan / $totalPengunjung : 0;

        // Member growth in period
        $newMembers = User::members()->whereBetween('created_at', [$from, $to])->count();

        // Compare to previous equal period
        $periodLength  = $from->diffInDays($to) + 1;
        $prevFrom      = $from->copy()->subDays($periodLength);
        $prevTo        = $to->copy()->subDays($periodLength);

        $prevPengunjung  = Booking::whereBetween('booking_date', [$prevFrom->toDateString(), $prevTo->toDateString()])->count();
        $prevPendapatan  = Booking::completed()->whereBetween('booking_date', [$prevFrom->toDateString(), $prevTo->toDateString()])->sum('final_price');

        $growthPengunjung = $prevPengunjung > 0 ? round((($totalPengunjung - $prevPengunjung) / $prevPengunjung) * 100, 1) : 0;
        $growthPendapatan = $prevPendapatan > 0 ? round((($totalPendapatan - $prevPendapatan) / $prevPendapatan) * 100, 1) : 0;
        $growthTiket      = 0;
        $growthMembers    = 0;

        $stats = [
            'total_pengunjung'   => $totalPengunjung,
            'total_pendapatan'   => $totalPendapatan,
            'rata_rata_tiket'    => $rataRataTiket,
            'new_members'        => $newMembers,
            'growth_pengunjung'  => $growthPengunjung,
            'growth_pendapatan'  => $growthPendapatan,
            'growth_tiket'       => $growthTiket,
            'growth_members'     => $growthMembers,
        ];

        // ---- Chart: daily data across range ----
        $chartLabels = [];
        $chartVisits = [];
        $chartRevenue = [];

        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to->copy()->startOfDay())) {
            $dateStr = $cursor->toDateString();
            $chartLabels[]  = $cursor->format('d M');
            $chartVisits[]  = Booking::whereDate('booking_date', $dateStr)->count();
            $chartRevenue[] = (int) Booking::completed()->whereDate('booking_date', $dateStr)->sum('final_price');
            $cursor->addDay();
        }

        // ---- Transaction table ----
        $transactions = Booking::with(['treatment'])
            ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
            ->select(
                'booking_date',
                DB::raw('COUNT(*) as volume'),
                DB::raw('SUM(final_price) as total_nominal'),
                'treatment_id',
                DB::raw("'Selesai' as status_label")
            )
            ->groupBy('booking_date', 'treatment_id')
            ->orderBy('booking_date', 'desc')
            ->paginate(5)
            ->withQueryString();

        // ---- Treatment Recap data (for treatment tab) ----
        $treatmentRecap = [];
        $treatmentChartLabels = [];
        $treatmentChartData = [];
        $treatmentChartColors = [];
        $treatmentTotalBookings = 0;
        $treatmentTotalRevenue = 0;
        $treatmentUniqueCount = 0;
        $treatmentTopName = '-';
        $trendLabels = [];
        $trendDatasets = [];

        if ($tab === 'treatment') {
            // Ranking treatments by number of bookings in the period
            $treatmentRecap = Booking::with('treatment')
                ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
                ->select(
                    'treatment_id',
                    DB::raw('COUNT(*) as total_bookings'),
                    DB::raw('SUM(final_price) as total_revenue'),
                    DB::raw('AVG(final_price) as avg_revenue')
                )
                ->groupBy('treatment_id')
                ->orderByDesc('total_bookings')
                ->get();

            // Stats for summary cards
            $treatmentTotalBookings = $treatmentRecap->sum('total_bookings');
            $treatmentTotalRevenue = $treatmentRecap->sum('total_revenue');
            $treatmentUniqueCount = $treatmentRecap->count();
            $treatmentTopName = $treatmentRecap->first()?->treatment?->name ?? '-';

            // Chart data for doughnut/bar chart — top treatments
            $colorPalette = [
                'rgba(99,102,241,0.8)',   // indigo
                'rgba(16,185,129,0.8)',   // emerald
                'rgba(245,158,11,0.8)',   // amber
                'rgba(239,68,68,0.8)',    // red
                'rgba(139,92,246,0.8)',   // violet
                'rgba(6,182,212,0.8)',    // cyan
                'rgba(236,72,153,0.8)',   // pink
                'rgba(34,197,94,0.8)',    // green
                'rgba(249,115,22,0.8)',   // orange
                'rgba(168,85,247,0.8)',   // purple
            ];

            foreach ($treatmentRecap as $i => $row) {
                $treatmentChartLabels[] = $row->treatment->name ?? 'Unknown';
                $treatmentChartData[] = $row->total_bookings;
                $treatmentChartColors[] = $colorPalette[$i % count($colorPalette)];
            }

            // Monthly trend for treatment tab
            $treatmentMonthlyTrend = Booking::whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
                ->select(
                    DB::raw("DATE_FORMAT(booking_date, '%Y-%m') as period"),
                    DB::raw("DATE_FORMAT(booking_date, '%b %Y') as period_label"),
                    'treatment_id',
                    DB::raw('COUNT(*) as total_bookings')
                )
                ->groupBy('period', 'period_label', 'treatment_id')
                ->orderBy('period')
                ->get();

            // Get top 5 treatments for trend chart
            $topTreatmentIds = $treatmentRecap->take(5)->pluck('treatment_id')->toArray();
            $topTreatments = Treatment::whereIn('id', $topTreatmentIds)->pluck('name', 'id');

            // Build monthly trend datasets
            $trendLabels = $treatmentMonthlyTrend->pluck('period_label')->unique()->values()->toArray();
            $trendDatasets = [];

            foreach ($topTreatmentIds as $idx => $tId) {
                $data = [];
                foreach ($trendLabels as $label) {
                    $found = $treatmentMonthlyTrend->where('treatment_id', $tId)->where('period_label', $label)->first();
                    $data[] = $found ? $found->total_bookings : 0;
                }
                $trendDatasets[] = [
                    'label' => $topTreatments[$tId] ?? 'Unknown',
                    'data'  => $data,
                    'color' => $colorPalette[$idx % count($colorPalette)],
                ];
            }
        }

        return view('admin.laporan.index', compact(
            'tab', 'from', 'to',
            'stats',
            'chartLabels', 'chartVisits', 'chartRevenue',
            'transactions',
            'treatmentRecap', 'treatmentChartLabels', 'treatmentChartData', 'treatmentChartColors',
            'treatmentTotalBookings', 'treatmentTotalRevenue', 'treatmentUniqueCount', 'treatmentTopName',
            'trendLabels', 'trendDatasets'
        ));
    }

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfWeek();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()     : Carbon::now()->endOfDay();

        $bookings = Booking::with(['user', 'treatment', 'doctor'])
            ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('booking_date')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Booking', 'Pasien', 'Treatment', 'Dokter', 'Tanggal', 'Jam', 'Status', 'Total']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_code,
                    $b->user->name ?? '-',
                    $b->treatment->name ?? '-',
                    $b->doctor->name ?? '-',
                    $b->booking_date->format('d/m/Y'),
                    $b->booking_time,
                    $b->status,
                    $b->final_price,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
