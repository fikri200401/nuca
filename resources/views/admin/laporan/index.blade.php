@extends('layouts.admin')

@section('title', 'Laporan Analitik')

@section('content')
<div class="p-6">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Analitik</h1>
                <p class="text-sm text-gray-500 mt-0.5">Analisis data kunjungan & pendapatan klinik</p>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse inline-block"></span>
                Data Real-time
            </span>
        </div>
        <div class="text-xs text-gray-400">
            Terakhir diperbarui: {{ now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

    {{-- ===== TABS ===== --}}
    <div class="flex gap-1 mb-5 bg-gray-100 p-1 rounded-xl w-fit">
        <a href="{{ route('admin.laporan.index', array_merge(request()->query(), ['tab' => 'pengunjung'])) }}"
           class="px-5 py-2 rounded-lg text-sm font-semibold transition-all
                  {{ $tab === 'pengunjung' ? 'bg-white text-indigo-700 shadow' : 'text-gray-500 hover:text-gray-700' }}">
            Rekap Pengunjung
        </a>
        <a href="{{ route('admin.laporan.index', array_merge(request()->query(), ['tab' => 'pendapatan'])) }}"
           class="px-5 py-2 rounded-lg text-sm font-semibold transition-all
                  {{ $tab === 'pendapatan' ? 'bg-white text-indigo-700 shadow' : 'text-gray-500 hover:text-gray-700' }}">
            Rekap Pendapatan
        </a>
        <a href="{{ route('admin.laporan.index', array_merge(request()->query(), ['tab' => 'treatment'])) }}"
           class="px-5 py-2 rounded-lg text-sm font-semibold transition-all
                  {{ $tab === 'treatment' ? 'bg-white text-indigo-700 shadow' : 'text-gray-500 hover:text-gray-700' }}">
            Rekap Treatment
        </a>
    </div>

    {{-- ===== DATE FILTER + EXPORT ===== --}}
    <form method="GET" action="{{ route('admin.laporan.index') }}" id="filterForm"
          class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6 bg-white rounded-xl shadow-sm p-4">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Periode:</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <span class="text-gray-400 text-sm">–</span>
            <input type="date" name="to" value="{{ $to->toDateString() }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>

        {{-- Quick range buttons --}}
        <div class="flex gap-2 flex-wrap">
            <button type="button" onclick="setRange('week')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 hover:bg-indigo-50 hover:border-indigo-400 hover:text-indigo-700 transition">
                Minggu Ini
            </button>
            <button type="button" onclick="setRange('month')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 hover:bg-indigo-50 hover:border-indigo-400 hover:text-indigo-700 transition">
                Bulan Ini
            </button>
            <button type="button" onclick="setRange('30')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 hover:bg-indigo-50 hover:border-indigo-400 hover:text-indigo-700 transition">
                30 Hari
            </button>
            <button type="button" onclick="setRange('year')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 hover:bg-indigo-50 hover:border-indigo-400 hover:text-indigo-700 transition">
                Tahun Ini
            </button>
        </div>

        <div class="flex gap-2 sm:ml-auto">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Tampilkan
            </button>
            @canDo('laporan', 'view')
            <a href="{{ route('admin.laporan.export-csv', ['from' => $from->toDateString(), 'to' => $to->toDateString(), 'tab' => $tab]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor CSV
            </a>
            @endCanDo
        </div>
    </form>

    @if($tab !== 'treatment')
    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Pengunjung --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Pengunjung</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-800">{{ number_format($stats['total_pengunjung']) }}</span>
                @if($stats['growth_pengunjung'] != 0)
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full
                             {{ $stats['growth_pengunjung'] >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $stats['growth_pengunjung'] >= 0 ? '▲' : '▼' }}
                    {{ abs($stats['growth_pengunjung']) }}%
                </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1">vs periode sebelumnya</p>
        </div>

        {{-- Pendapatan Bersih --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pendapatan Bersih</p>
            <div class="flex items-end justify-between">
                <span class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}
                </span>
                @if($stats['growth_pendapatan'] != 0)
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full
                             {{ $stats['growth_pendapatan'] >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $stats['growth_pendapatan'] >= 0 ? '▲' : '▼' }}
                    {{ abs($stats['growth_pendapatan']) }}%
                </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1">dari booking selesai</p>
        </div>

        {{-- Rata-rata Tiket --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Rata-rata Tiket</p>
            <div class="flex items-end justify-between">
                <span class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($stats['rata_rata_tiket'], 0, ',', '.') }}
                </span>
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                    per visit
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">avg transaction value</p>
        </div>

        {{-- Pertumbuhan Member --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Member Baru</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-800">{{ $stats['new_members'] }}</span>
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full bg-purple-100 text-purple-700">
                    +{{ $stats['new_members'] }} baru
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">registrasi periode ini</p>
        </div>
    </div>

    {{-- ===== CHART ===== --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-800">
                    {{ $tab === 'pengunjung' ? 'Tren Kunjungan' : 'Tren Pendapatan' }} Harian
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $from->format('d M') }} – {{ $to->format('d M Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <button onclick="toggleChartMode('kunjungan')" id="btnKunjungan"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-indigo-500 bg-indigo-500 text-white transition">
                    Kunjungan
                </button>
                <button onclick="toggleChartMode('pendapatan')" id="btnPendapatan"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-gray-300 text-gray-600 hover:border-indigo-400 hover:text-indigo-600 transition">
                    Pendapatan
                </button>
            </div>
        </div>
        <div class="relative" style="height: 280px;">
            <canvas id="laporanChart"></canvas>
        </div>
    </div>

    {{-- ===== TRANSACTION TABLE ===== --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">Rincian Data Transaksi</h2>
            <span class="text-xs text-gray-400">{{ $transactions->total() }} total baris</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori Layanan</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Volume</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Nominal</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($row->booking_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-gray-800 font-medium">
                            {{ $row->treatment->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 font-semibold text-sm">
                                {{ $row->volume }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-800">
                            Rp {{ number_format($row->total_nominal, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Selesai
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Tidak ada data transaksi untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

    @else
    {{-- ===== TREATMENT TAB CONTENT ===== --}}

    {{-- Treatment Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Booking Treatment --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Booking</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-800">{{ number_format($treatmentTotalBookings) }}</span>
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    periode ini
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">semua treatment</p>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Pendapatan</p>
            <div class="flex items-end justify-between">
                <span class="text-2xl font-bold text-gray-800">
                    Rp {{ number_format($treatmentTotalRevenue, 0, ',', '.') }}
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">dari semua treatment</p>
        </div>

        {{-- Jumlah Treatment Aktif --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Jenis Treatment</p>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-bold text-gray-800">{{ $treatmentUniqueCount }}</span>
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full bg-cyan-100 text-cyan-700">
                    aktif
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">treatment yang dipesan</p>
        </div>

        {{-- Treatment Terpopuler --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Treatment Terpopuler</p>
            <div class="flex items-end justify-between">
                <span class="text-lg font-bold text-gray-800 truncate" title="{{ $treatmentTopName }}">{{ $treatmentTopName }}</span>
                <span class="inline-flex items-center gap-0.5 text-xs font-bold px-2 py-1 rounded-full bg-amber-100 text-amber-700">
                    🏆 #1
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">paling banyak dipilih</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Doughnut Chart: Treatment Distribution --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="mb-4">
                <h2 class="text-base font-bold text-gray-800">Distribusi Treatment</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $from->format('d M') }} – {{ $to->format('d M Y') }}
                </p>
            </div>
            <div class="relative flex justify-center" style="height: 280px;">
                <canvas id="treatmentDoughnutChart"></canvas>
            </div>
        </div>

        {{-- Bar Chart: Treatment Ranking --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Ranking Treatment</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Berdasarkan jumlah booking</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleTreatmentBarMode('bookings')" id="btnBarBookings"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-indigo-500 bg-indigo-500 text-white transition">
                        Booking
                    </button>
                    <button onclick="toggleTreatmentBarMode('revenue')" id="btnBarRevenue"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold border-2 border-gray-300 text-gray-600 hover:border-emerald-400 hover:text-emerald-600 transition">
                        Pendapatan
                    </button>
                </div>
            </div>
            <div class="relative" style="height: 280px;">
                <canvas id="treatmentBarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Monthly Trend Chart --}}
    @if(count($trendLabels ?? []) > 0)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="mb-4">
            <h2 class="text-base font-bold text-gray-800">Tren Treatment per Bulan</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                Top 5 treatment — {{ $from->format('d M') }} – {{ $to->format('d M Y') }}
            </p>
        </div>
        <div class="relative" style="height: 300px;">
            <canvas id="treatmentTrendChart"></canvas>
        </div>
    </div>
    @endif

    {{-- Treatment Ranking Table --}}
    <div class="bg-white rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">Detail Rekap Treatment</h2>
            <span class="text-xs text-gray-400">{{ count($treatmentRecap) }} treatment</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Treatment</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Booking</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pendapatan</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Rata-rata / Booking</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($treatmentRecap as $index => $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-center">
                            @if($index === 0)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">🥇</span>
                            @elseif($index === 1)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-600 font-bold text-sm">🥈</span>
                            @elseif($index === 2)
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-bold text-sm">🥉</span>
                            @else
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500 font-semibold text-sm">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $treatmentChartColors[$index] ?? 'rgba(156,163,175,0.8)' }}"></div>
                                <span class="font-medium text-gray-800">{{ $row->treatment->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold text-sm">
                                {{ number_format($row->total_bookings) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-800">
                            Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600">
                            Rp {{ number_format($row->avg_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $percentage = $treatmentTotalBookings > 0 ? round(($row->total_bookings / $treatmentTotalBookings) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background: {{ $treatmentChartColors[$index] ?? 'rgba(156,163,175,0.8)' }}"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-600">{{ $percentage }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Tidak ada data treatment untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif {{-- end treatment tab --}}

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Quick range helpers
function setRange(range) {
    const now = new Date();
    let from, to = now.toISOString().slice(0,10);

    if (range === 'week') {
        const day = now.getDay() || 7;
        const monday = new Date(now);
        monday.setDate(now.getDate() - day + 1);
        from = monday.toISOString().slice(0,10);
    } else if (range === 'month') {
        from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0,10);
    } else if (range === '30') {
        const d = new Date(now);
        d.setDate(now.getDate() - 29);
        from = d.toISOString().slice(0,10);
    } else if (range === 'year') {
        from = new Date(now.getFullYear(), 0, 1).toISOString().slice(0,10);
    }

    document.querySelector('input[name="from"]').value = from;
    document.querySelector('input[name="to"]').value   = to;
}

@if($tab === 'treatment')
// ==================== TREATMENT TAB CHARTS ====================
const treatmentLabels = @json($treatmentChartLabels);
const treatmentBookingsData = @json($treatmentChartData);
const treatmentColors = @json($treatmentChartColors);
const treatmentRevenueData = @json(collect($treatmentRecap)->pluck('total_revenue')->toArray());

// --- Doughnut Chart ---
(function() {
    const ctx = document.getElementById('treatmentDoughnutChart');
    if (!ctx) return;

    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: treatmentLabels,
            datasets: [{
                data: treatmentBookingsData,
                backgroundColor: treatmentColors,
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '55%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': ' + ctx.parsed + ' booking (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
})();

// --- Bar Chart ---
let barChart = null;
let barMode = 'bookings';

function buildBarChart(mode) {
    const ctx = document.getElementById('treatmentBarChart');
    if (!ctx) return;

    if (barChart) barChart.destroy();

    const isRevenue = mode === 'revenue';
    const data = isRevenue ? treatmentRevenueData : treatmentBookingsData;
    const colors = isRevenue
        ? treatmentColors.map(c => c.replace('0.8', '0.6'))
        : treatmentColors;

    barChart = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: treatmentLabels,
            datasets: [{
                label: isRevenue ? 'Pendapatan (Rp)' : 'Jumlah Booking',
                data: data,
                backgroundColor: colors,
                borderColor: treatmentColors,
                borderWidth: 1,
                borderRadius: 6,
                barPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return isRevenue
                                ? 'Rp ' + ctx.parsed.x.toLocaleString('id-ID')
                                : ctx.parsed.x + ' booking';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11 },
                        color: '#6b7280',
                        callback: v => isRevenue
                            ? 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v.toLocaleString('id-ID'))
                            : v
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#374151' }
                }
            }
        }
    });
}

function toggleTreatmentBarMode(mode) {
    barMode = mode;
    buildBarChart(mode);
    document.getElementById('btnBarBookings').className =
        'px-3 py-1.5 rounded-lg text-xs font-semibold border-2 transition ' +
        (mode === 'bookings'
            ? 'border-indigo-500 bg-indigo-500 text-white'
            : 'border-gray-300 text-gray-600 hover:border-indigo-400 hover:text-indigo-600');
    document.getElementById('btnBarRevenue').className =
        'px-3 py-1.5 rounded-lg text-xs font-semibold border-2 transition ' +
        (mode === 'revenue'
            ? 'border-emerald-500 bg-emerald-500 text-white'
            : 'border-gray-300 text-gray-600 hover:border-emerald-400 hover:text-emerald-600');
}

// Init bar chart
buildBarChart('bookings');

// --- Trend Chart (line - top 5 treatments monthly) ---
(function() {
    const trendLabels = @json($trendLabels ?? []);
    const trendDatasets = @json($trendDatasets ?? []);
    const ctx = document.getElementById('treatmentTrendChart');
    if (!ctx || trendLabels.length === 0) return;

    const datasets = trendDatasets.map(function(ds) {
        return {
            label: ds.label,
            data: ds.data,
            borderColor: ds.color,
            backgroundColor: ds.color.replace('0.8', '0.1'),
            fill: false,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 2
        };
    });

    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: { labels: trendLabels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + ' booking'
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#6b7280' } },
                y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, color: '#6b7280', stepSize: 1 }, beginAtZero: true }
            }
        }
    });
})();

@else
// ==================== PENGUNJUNG / PENDAPATAN TAB CHARTS ====================
const visitData    = @json($chartVisits);
const revenueData  = @json($chartRevenue);
const chartLabels  = @json($chartLabels);

let currentMode = 'kunjungan';
let chart;

function buildChart(mode) {
    const ctx = document.getElementById('laporanChart').getContext('2d');
    const isRevenue = mode === 'pendapatan';

    if (chart) chart.destroy();

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: isRevenue ? 'Pendapatan (Rp)' : 'Jumlah Kunjungan',
                data: isRevenue ? revenueData : visitData,
                borderColor: isRevenue ? 'rgb(16,185,129)' : 'rgb(99,102,241)',
                backgroundColor: isRevenue
                    ? 'rgba(16,185,129,0.08)'
                    : 'rgba(99,102,241,0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: isRevenue ? 'rgb(16,185,129)' : 'rgb(99,102,241)',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => isRevenue
                            ? 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                            : ctx.parsed.y + ' kunjungan'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#6b7280' }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11 },
                        color: '#6b7280',
                        callback: v => isRevenue
                            ? 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v.toLocaleString('id-ID'))
                            : v
                    }
                }
            }
        }
    });
}

function toggleChartMode(mode) {
    currentMode = mode;
    buildChart(mode);
    document.getElementById('btnKunjungan').className =
        'px-3 py-1.5 rounded-lg text-xs font-semibold border-2 transition ' +
        (mode === 'kunjungan'
            ? 'border-indigo-500 bg-indigo-500 text-white'
            : 'border-gray-300 text-gray-600 hover:border-indigo-400 hover:text-indigo-600');
    document.getElementById('btnPendapatan').className =
        'px-3 py-1.5 rounded-lg text-xs font-semibold border-2 transition ' +
        (mode === 'pendapatan'
            ? 'border-emerald-500 bg-emerald-500 text-white'
            : 'border-gray-300 text-gray-600 hover:border-emerald-400 hover:text-emerald-600');
}

// Init
buildChart('{{ $tab === "pendapatan" ? "pendapatan" : "kunjungan" }}');
@if($tab === 'pendapatan')
toggleChartMode('pendapatan');
@endif
@endif
</script>
@endpush
