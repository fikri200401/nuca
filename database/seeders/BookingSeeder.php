<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Treatment;
use App\Models\Doctor;
use App\Models\Deposit;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\BeforeAfterPhoto;
use App\Models\NoShowNote;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        $treatments = Treatment::all();
        $doctors = Doctor::all();
        $voucher = Voucher::where('code', 'WELCOME2024')->first(); // Use existing voucher

        // Get specific treatments by name
        $facialBasic = Treatment::where('name', 'Facial Basic')->first();
        $facialAcne = Treatment::where('name', 'Facial Acne')->first();
        $whitening = Treatment::where('name', 'Whitening Treatment')->first();
        $peeling = Treatment::where('name', 'Chemical Peeling')->first();
        $laser = Treatment::where('name', 'Laser Hair Removal')->first();
        $microderma = Treatment::where('name', 'Microdermabrasion')->first();

        // 1. Booking COMPLETED dengan Before/After Photos (sudah selesai)
        $booking1 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $facialBasic->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->subDays(10),
            'booking_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'completed',
            'total_price' => 150000,
            'discount_amount' => 15000, // member discount 10%
            'final_price' => 135000,
            'customer_notes' => 'Kulit saya sensitif, mohon gunakan produk yang lembut',
            'admin_notes' => 'Treatment berjalan lancar, customer puas',
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(15),
            'updated_at' => Carbon::now()->subDays(10),
        ]);

        // Add Before/After Photos
        BeforeAfterPhoto::create([
            'booking_id' => $booking1->id,
            'before_photo' => 'before-after/before1.jpg',
            'after_photo' => 'before-after/after1.jpg',
            'notes' => 'Hasil facial treatment terlihat wajah lebih cerah dan glowing',
            'uploaded_by' => 1, // admin
            'created_at' => Carbon::now()->subDays(10),
        ]);

        // 2. Booking COMPLETED dengan Before/After Photos (2 minggu lalu)
        $booking2 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $whitening->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->subDays(14),
            'booking_time' => '14:00',
            'end_time' => '16:00',
            'status' => 'completed',
            'total_price' => 350000,
            'discount_amount' => 35000,
            'final_price' => 315000,
            'customer_notes' => null,
            'admin_notes' => 'Customer sangat puas dengan hasilnya',
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(20),
            'updated_at' => Carbon::now()->subDays(14),
        ]);

        BeforeAfterPhoto::create([
            'booking_id' => $booking2->id,
            'before_photo' => 'before-after/before2.jpg',
            'after_photo' => 'before-after/after2.jpg',
            'notes' => 'Kulit terlihat lebih putih dan cerah setelah whitening treatment',
            'uploaded_by' => 1,
            'created_at' => Carbon::now()->subDays(14),
        ]);

        // 3. Booking DEPOSIT_CONFIRMED (menunggu jadwal treatment)
        $booking3 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $laser->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->addDays(15),
            'booking_time' => '10:00',
            'end_time' => '10:45',
            'status' => 'deposit_confirmed',
            'total_price' => 500000,
            'discount_amount' => 75000, // 50k member + 25k voucher
            'final_price' => 425000,
            'customer_notes' => 'Pertama kali laser, mohon dijelaskan prosedurnya',
            'admin_notes' => null,
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Deposit approved
        $deposit3 = Deposit::create([
            'booking_id' => $booking3->id,
            'amount' => 50000,
            'proof_of_payment' => 'deposits/proof3.jpg',
            'status' => 'approved',
            'deadline_at' => Carbon::now()->subDays(1)->addHours(24),
            'verified_at' => Carbon::now()->subDays(1),
            'verified_by' => 1,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(1),
        ]);

        // Voucher usage
        VoucherUsage::create([
            'voucher_id' => $voucher->id,
            'user_id' => $customer->id,
            'booking_id' => $booking3->id,
            'discount_amount' => 25000,
            'created_at' => Carbon::now()->subDays(2),
        ]);
        $voucher->increment('usage_count');

        // 4. Booking WAITING_DEPOSIT (butuh upload bukti DP)
        $booking4 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $peeling->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->addDays(20),
            'booking_time' => '11:00',
            'end_time' => '12:30',
            'status' => 'waiting_deposit',
            'total_price' => 400000,
            'discount_amount' => 40000,
            'final_price' => 360000,
            'customer_notes' => 'Saya ada kulit kering, apakah bisa di-peeling?',
            'admin_notes' => null,
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(5),
        ]);

        // Deposit pending (belum upload)
        Deposit::create([
            'booking_id' => $booking4->id,
            'amount' => 50000,
            'proof_of_payment' => null,
            'status' => 'pending',
            'deadline_at' => Carbon::now()->addHours(19),
            'created_at' => Carbon::now()->subHours(5),
        ]);

        // 5. Booking DEPOSIT_REJECTED (harus upload ulang)
        $booking5 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $facialAcne->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->addDays(12),
            'booking_time' => '13:00',
            'end_time' => '14:30',
            'status' => 'deposit_rejected',
            'total_price' => 250000,
            'discount_amount' => 25000,
            'final_price' => 225000,
            'customer_notes' => 'Jerawat banyak di pipi dan dahi',
            'admin_notes' => 'Mohon upload ulang bukti transfer yang lebih jelas',
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Deposit rejected
        Deposit::create([
            'booking_id' => $booking5->id,
            'amount' => 50000,
            'proof_of_payment' => 'deposits/proof5_blurry.jpg',
            'status' => 'rejected',
            'deadline_at' => Carbon::now()->subDays(2)->addHours(24),
            'verified_at' => Carbon::now()->subDays(2),
            'verified_by' => 1,
            'rejection_reason' => 'Bukti transfer tidak jelas, mohon upload foto yang lebih terang dan fokus',
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // 6. Booking AUTO_APPROVED (booking dekat, tidak perlu DP)
        $booking6 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $facialBasic->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->addDays(3),
            'booking_time' => '15:00',
            'end_time' => '16:00',
            'status' => 'auto_approved',
            'total_price' => 150000,
            'discount_amount' => 15000,
            'final_price' => 135000,
            'customer_notes' => null,
            'admin_notes' => null,
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now()->subHours(2),
        ]);

        // 7. Booking CANCELLED (dibatalkan customer)
        $booking7 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $microderma->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->addDays(5),
            'booking_time' => '09:00',
            'end_time' => '10:15',
            'status' => 'cancelled',
            'total_price' => 450000,
            'discount_amount' => 45000,
            'final_price' => 405000,
            'customer_notes' => 'Ingin coba microdermabrasion',
            'admin_notes' => 'Customer membatalkan karena perlu konsultasi lebih lanjut',
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(4),
            'updated_at' => Carbon::now()->subDays(3),
        ]);

        // 8. Booking CANCELLED (customer tidak datang - marked as cancelled)
        $booking8 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $whitening->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->subDays(5),
            'booking_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'cancelled',
            'total_price' => 350000,
            'discount_amount' => 35000,
            'final_price' => 315000,
            'customer_notes' => null,
            'admin_notes' => 'Customer tidak hadir tanpa pemberitahuan (no-show)',
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(5),
        ]);

        // No show note (simulated no-show scenario)
        NoShowNote::create([
            'user_id' => $customer->id,
            'booking_id' => $booking8->id,
            'note' => 'Customer tidak hadir dan tidak mengangkat telepon. Sudah dicoba hubungi via WhatsApp.',
            'created_by' => 1,
            'created_at' => Carbon::now()->subDays(5),
        ]);

        // 9. Booking EXPIRED (deadline DP terlewat)
        $booking9 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $microderma->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->addDays(10),
            'booking_time' => '14:00',
            'end_time' => '15:15',
            'status' => 'expired',
            'total_price' => 450000,
            'discount_amount' => 45000,
            'final_price' => 405000,
            'customer_notes' => 'Penasaran dengan microdermabrasion',
            'admin_notes' => 'Booking expired karena tidak melakukan pembayaran DP',
            'is_manual_entry' => false,
            'created_at' => Carbon::now()->subDays(6),
            'updated_at' => Carbon::now()->subDays(4),
        ]);

        // Expired deposit
        Deposit::create([
            'booking_id' => $booking9->id,
            'amount' => 50000,
            'proof_of_payment' => null,
            'status' => 'pending',
            'deadline_at' => Carbon::now()->subDays(4),
            'created_at' => Carbon::now()->subDays(6),
        ]);

        // 10. Booking COMPLETED (manual entry by admin)
        $booking10 = Booking::create([
            'booking_code' => 'BK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
            'user_id' => $customer->id,
            'treatment_id' => $laser->id,
            'doctor_id' => $doctors->random()->id,
            'booking_date' => Carbon::now()->subDays(30),
            'booking_time' => '11:00',
            'end_time' => '11:45',
            'status' => 'completed',
            'total_price' => 500000,
            'discount_amount' => 50000,
            'final_price' => 450000,
            'customer_notes' => null,
            'admin_notes' => 'Customer walk-in, booking dibuat oleh admin',
            'is_manual_entry' => true,
            'created_at' => Carbon::now()->subDays(30),
            'updated_at' => Carbon::now()->subDays(30),
        ]);

        BeforeAfterPhoto::create([
            'booking_id' => $booking10->id,
            'before_photo' => 'before-after/before3.jpg',
            'after_photo' => 'before-after/after3.jpg',
            'notes' => 'Laser hair removal area ketiak, hasil memuaskan',
            'uploaded_by' => 1,
            'created_at' => Carbon::now()->subDays(30),
        ]);

        echo "✓ Created 10 bookings with various statuses\n";
        echo "✓ Created 3 before/after photo records\n";
        echo "✓ Created deposits (pending, approved, rejected, expired)\n";
        echo "✓ Created 1 voucher usage\n";
        echo "✓ Created 1 no-show note\n";
    }
}
