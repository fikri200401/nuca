<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'WELCOME2024',
                'name' => 'Voucher Selamat Datang',
                'description' => 'Diskon 20% untuk member baru dengan minimal transaksi 200rb',
                'type' => 'percentage',
                'value' => 20,
                'min_transaction' => 200000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_single_use' => true,
                'show_on_landing' => true,
            ],
            [
                'code' => 'PROMO50K',
                'name' => 'Promo Bulan Ini',
                'description' => 'Diskon 50ribu untuk transaksi minimal 500rb',
                'type' => 'nominal',
                'value' => 50000,
                'min_transaction' => 500000,
                'valid_from' => Carbon::now()->startOfMonth(),
                'valid_until' => Carbon::now()->endOfMonth(),
                'is_single_use' => false,
                'max_usage' => 100,
                'show_on_landing' => true,
            ],
            [
                'code' => 'MEMBER10',
                'name' => 'Voucher Member VIP',
                'description' => 'Diskon tambahan 10% untuk member',
                'type' => 'percentage',
                'value' => 10,
                'min_transaction' => 300000,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
                'is_single_use' => false,
                'show_on_landing' => false,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}
