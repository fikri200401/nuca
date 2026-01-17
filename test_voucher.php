<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test voucher COBAIN26
$voucher = \App\Models\Voucher::where('code', 'COBAIN26')->first();

if (!$voucher) {
    echo "Voucher tidak ditemukan!\n";
    exit;
}

echo "=== VOUCHER INFO ===\n";
echo "Code: " . $voucher->code . "\n";
echo "Name: " . $voucher->name . "\n";
echo "Type: " . $voucher->type . "\n";
echo "Value: " . $voucher->value . "\n";
echo "Min Transaction: Rp " . number_format($voucher->min_transaction, 0, ',', '.') . "\n";
echo "Is Active: " . ($voucher->is_active ? 'YES' : 'NO') . "\n";
echo "Valid From: " . $voucher->valid_from->format('Y-m-d') . "\n";
echo "Valid Until: " . $voucher->valid_until->format('Y-m-d') . "\n";
echo "Is Single Use: " . ($voucher->is_single_use ? 'YES' : 'NO') . "\n";
echo "Usage Count: " . $voucher->usage_count . "\n";

echo "\n=== VALIDATION ===\n";
echo "Is Valid: " . ($voucher->isValid() ? 'YES' : 'NO') . "\n";
echo "Can be used by user 3 with Rp 450.000: " . ($voucher->canBeUsedBy(3, 450000) ? 'YES' : 'NO') . "\n";

echo "\n=== DISCOUNT CALCULATION ===\n";
echo "Discount for Rp 450.000: Rp " . number_format($voucher->calculateDiscount(450000), 0, ',', '.') . "\n";

// Check if user 3 already used this voucher
$hasUsed = \App\Models\VoucherUsage::where('voucher_id', $voucher->id)
    ->where('user_id', 3)
    ->exists();

echo "\n=== USAGE CHECK ===\n";
echo "User 3 has used this voucher: " . ($hasUsed ? 'YES' : 'NO') . "\n";
