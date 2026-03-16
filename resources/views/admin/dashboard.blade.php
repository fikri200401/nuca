@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="p-6 space-y-6">

    {{-- ===================================================
         TOP BAR: Title + Period Filter
    =================================================== --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pantau performa klinik dan aktivitas operasional hari ini.</p>
        </div>
        <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm text-sm">
            @foreach(['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan', 'custom' => 'Custom'] as $k => $label)
            <button onclick="setPeriod('{{ $k }}')" id="period_{{ $k }}"
                    class="period-btn px-4 py-1.5 rounded-lg font-medium transition
                        {{ $k === 'hari' ? 'bg-indigo-600 text-white' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
            @endforeach
            <button class="ml-1 p-1.5 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            </button>
        </div>
    </div>

    {{-- ===================================================
         STAT CARDS
    =================================================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Booking Hari Ini --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                @php $g = $stats['bookings_growth']; @endphp
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $g >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                    {{ $g >= 0 ? '+' : '' }}{{ $g }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_bookings_today'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Booking Hari Ini</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $g >= 0 ? '+' . $g : $g }}% dari kemarin</p>
        </div>

        {{-- DP Pending --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-600">Pending</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_deposits'] }}</p>
            <p class="text-sm text-gray-500 mt-1">DP Pending</p>
            <p class="text-xs text-gray-400 mt-0.5">Menunggu verifikasi pembayaran</p>
        </div>

        {{-- Total Member --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                    +{{ $stats['new_members_week'] }} baru
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_members']) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Member</p>
            <p class="text-xs text-gray-400 mt-0.5">+{{ $stats['new_members_week'] }} bergabung minggu ini</p>
        </div>

        {{-- Voucher Aktif --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Aktif</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_vouchers'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Voucher Aktif</p>
            <p class="text-xs text-gray-400 mt-0.5">Voucher yang dapat digunakan</p>
        </div>
    </div>

    {{-- ===================================================
         MAIN CONTENT GRID: Chart + Deposit Sidebar
    =================================================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- LEFT: Analisis Tren Chart --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Analisis Tren</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Perbandingan aktivitas berdasarkan filter yang dipilih.</p>
                </div>
                <div class="flex gap-1 text-sm">
                    <button onclick="setChartMode('kunjungan')" id="chartBtn_kunjungan"
                            class="chart-btn px-3 py-1.5 rounded-lg font-medium bg-indigo-600 text-white transition">
                        Kunjungan
                    </button>
                    <button onclick="setChartMode('pendapatan')" id="chartBtn_pendapatan"
                            class="chart-btn px-3 py-1.5 rounded-lg font-medium text-gray-500 hover:bg-gray-100 transition">
                        Pendapatan
                    </button>
                </div>
            </div>
            <div class="h-52">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- RIGHT: Deposit Pending List --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col">
            <div class="flex items-center justify-between mb-1">
                <h2 class="text-base font-semibold text-gray-900">Deposit Pending</h2>
                @if($stats['pending_deposits'] > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['pending_deposits'] }}</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mb-4">Menunggu verifikasi pembayaran.</p>

            <div class="flex-1 space-y-3 overflow-y-auto">
                @forelse($pendingDeposits as $deposit)
                <div class="border border-gray-100 rounded-xl p-3 hover:border-indigo-200 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $deposit->booking->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $deposit->booking->treatment->name ?? '-' }}</p>
                        </div>
                        <span class="text-sm font-bold text-indigo-700">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Deadline: {{ \Carbon\Carbon::parse($deposit->deadline_at)->format('d M, H:i') }}
                        </span>
                        <a href="{{ route('admin.deposits.show', $deposit) }}"
                           class="text-xs font-medium text-indigo-600 hover:underline">Verifikasi →</a>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-32 text-gray-400 text-sm">
                    <svg class="w-8 h-8 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tidak ada deposit pending
                </div>
                @endforelse
            </div>

            @if($stats['pending_deposits'] > 5)
            <a href="{{ route('admin.deposits.index') }}"
               class="mt-4 block text-center text-sm text-indigo-600 font-medium hover:underline">
                Lihat Semua Deposit →
            </a>
            @endif
        </div>
    </div>

    {{-- ===================================================
         QUICK ACTION CARDS
    =================================================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $actions = [
                ['route' => 'admin.bookings.index',   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',                                                                'ibg' => 'bg-indigo-100', 'ic' => 'text-indigo-600', 'border' => 'border-indigo-100 hover:border-indigo-300', 'title' => 'Kelola Booking',   'sub' => 'Lihat semua jadwal dan booking'],
                ['route' => 'admin.treatments.index', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'ibg' => 'bg-teal-100',   'ic' => 'text-teal-600',   'border' => 'border-teal-100 hover:border-teal-300',   'title' => 'Kelola Treatment', 'sub' => 'Update menu dan harga'],
                ['route' => 'admin.doctors.index',    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',                                                                                         'ibg' => 'bg-purple-100', 'ic' => 'text-purple-600', 'border' => 'border-purple-100 hover:border-purple-300', 'title' => 'Kelola Dokter',    'sub' => 'Atur aktif dan jadwal'],
                ['route' => 'admin.deposits.index',   'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',     'ibg' => 'bg-orange-100', 'ic' => 'text-orange-600', 'border' => 'border-orange-100 hover:border-orange-300', 'title' => 'Verifikasi DP',     'sub' => 'Proses deposit pelanggan'],
            ];
        @endphp
        @foreach($actions as $a)
        <a href="{{ route($a['route']) }}"
           class="bg-white rounded-2xl border {{ $a['border'] }} shadow-sm p-4 flex items-center gap-4 transition group">
            <div class="w-11 h-11 rounded-xl {{ $a['ibg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $a['ic'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $a['icon'] }}"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">{{ $a['title'] }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $a['sub'] }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 flex-shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>

    {{-- ===================================================
         BOOKING MENDATANG TABLE
    =================================================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Booking Mendatang</h2>
                <p class="text-xs text-gray-400 mt-0.5">Daftar pasien yang dijadwalkan hari ini.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z"/></svg>
                    <input type="text" id="bookingSearch" onkeyup="filterBookings()" placeholder="Cari Pasien..."
                           class="pl-8 pr-4 py-1.5 text-sm border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <a href="{{ route('admin.bookings.create') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Booking
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full" id="bookingTable">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">ID</th>
                        <th class="px-5 py-3 text-left">Pasien</th>
                        <th class="px-5 py-3 text-left">Layanan</th>
                        <th class="px-5 py-3 text-left">Jam</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($todayBookings as $booking)
                    @php
                        $statusMap = [
                            'auto_approved'     => ['label' => 'Dikonfirmasi', 'class' => 'bg-green-100 text-green-700'],
                            'deposit_confirmed' => ['label' => 'Dikonfirmasi', 'class' => 'bg-green-100 text-green-700'],
                            'waiting_deposit'   => ['label' => 'Menunggu',    'class' => 'bg-yellow-100 text-yellow-700'],
                            'completed'         => ['label' => 'Selesai',     'class' => 'bg-blue-100 text-blue-700'],
                            'cancelled'         => ['label' => 'Dibatalkan',  'class' => 'bg-red-100 text-red-600'],
                            'no_show'           => ['label' => 'No-show',     'class' => 'bg-gray-100 text-gray-600'],
                        ];
                        $st = $statusMap[$booking->status] ?? ['label' => ucfirst($booking->status), 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <tr class="booking-row hover:bg-gray-50 transition">
                        <td class="px-5 py-4 font-mono text-xs text-gray-400">{{ $booking->booking_code }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-semibold text-xs flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-900 booking-name">{{ $booking->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $booking->treatment->name ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="flex items-center gap-1 text-gray-600">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $booking->booking_time }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                               class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Tidak ada booking hari ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($todayBookingsTotal > 5)
        <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
            <span>Menampilkan 5 dari {{ $todayBookingsTotal }} booking hari ini</span>
            <a href="{{ route('admin.bookings.index') }}" class="text-indigo-600 hover:underline text-xs font-medium">
                Lihat Semua → Selanjutnya
            </a>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartLabels = @json($chartLabels);
const visitData   = @json($chartData);

let trendChart;

function buildChart(mode) {
    const ctx   = document.getElementById('trendChart').getContext('2d');
    const data  = visitData; // revenue would come from server; using visits as placeholder
    const color = mode === 'kunjungan' ? '#6366f1' : '#10b981';
    const fill  = mode === 'kunjungan' ? 'rgba(99,102,241,0.12)' : 'rgba(16,185,129,0.12)';

    if (trendChart) trendChart.destroy();
    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                data: data,
                borderColor: color,
                backgroundColor: fill,
                borderWidth: 2.5,
                tension: 0.45,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: color,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', font: { size: 11 }, beginAtZero: true } }
            }
        }
    });
}

function setChartMode(mode) {
    document.querySelectorAll('.chart-btn').forEach(b => {
        b.classList.remove('bg-indigo-600','text-white');
        b.classList.add('text-gray-500','hover:bg-gray-100');
    });
    const btn = document.getElementById('chartBtn_' + mode);
    btn.classList.add('bg-indigo-600','text-white');
    btn.classList.remove('text-gray-500','hover:bg-gray-100');
    buildChart(mode);
}

function setPeriod(p) {
    document.querySelectorAll('.period-btn').forEach(b => {
        b.classList.remove('bg-indigo-600','text-white');
        b.classList.add('text-gray-500');
    });
    const btn = document.getElementById('period_' + p);
    btn.classList.add('bg-indigo-600','text-white');
    btn.classList.remove('text-gray-500');
}

function filterBookings() {
    const q = document.getElementById('bookingSearch').value.toLowerCase();
    document.querySelectorAll('.booking-row').forEach(row => {
        const name = row.querySelector('.booking-name')?.textContent.toLowerCase() || '';
        row.style.display = name.includes(q) ? '' : 'none';
    });
}

buildChart('kunjungan');
</script>
@endpush