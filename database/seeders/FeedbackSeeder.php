<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Feedback;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get completed bookings
        $completedBookings = Booking::where('status', 'completed')->get();

        if ($completedBookings->count() >= 1) {
            $booking = $completedBookings[0];
            // Feedback 1 - Excellent rating
            Feedback::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'treatment_id' => $booking->treatment_id,
                'doctor_id' => $booking->doctor_id,
                'rating' => 5,
                'comment' => 'Pelayanan sangat memuaskan! Dokternya ramah dan profesional. Hasil facial treatment bikin wajah glowing banget. Tempatnya juga bersih dan nyaman. Pasti balik lagi!',
                'is_visible' => true,
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(9),
            ]);
        }

        if ($completedBookings->count() >= 2) {
            $booking = $completedBookings[1];
            // Feedback 2 - Very good rating
            Feedback::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'treatment_id' => $booking->treatment_id,
                'doctor_id' => $booking->doctor_id,
                'rating' => 5,
                'comment' => 'Whitening treatment nya bagus banget! Kulitku jadi lebih cerah dalam sekali treatment. Staff nya helpful dan explain step by step. Recommended!',
                'is_visible' => true,
                'created_at' => Carbon::now()->subDays(13),
                'updated_at' => Carbon::now()->subDays(13),
            ]);
        }

        if ($completedBookings->count() >= 3) {
            $booking = $completedBookings[2];
            // Feedback 3 - Good rating with minor complaint
            Feedback::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'treatment_id' => $booking->treatment_id,
                'doctor_id' => $booking->doctor_id,
                'rating' => 4,
                'comment' => 'Treatment nya oke, hasilnya juga bagus. Cuma agak nunggu lama karena dokter telat 15 menit. Overall puas sih, cuma tolong lebih tepat waktu ya.',
                'is_visible' => true,
                'created_at' => Carbon::now()->subDays(29),
                'updated_at' => Carbon::now()->subDays(29),
            ]);
        }

        echo "✓ Created " . Feedback::count() . " feedback records\n";
        echo "✓ All feedback visible on landing page\n";
    }
}
