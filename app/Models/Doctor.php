<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'phone',
        'email',
        'bio',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->feedbacks()->avg('rating') ?? 0;
    }

    /**
     * Check if doctor is available on specific date and time
     */
    public function isAvailable($date, $startTime, $endTime)
    {
        $dayOfWeek = strtolower(date('l', strtotime($date)));
        
        // Check if doctor has schedule on that day
        $schedule = $this->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return false;
        }

        // Normalize time format for comparison (HH:MM)
        $startTime = substr($startTime, 0, 5);
        $endTime = substr($endTime, 0, 5);
        
        // Extract time from schedule (SQLite stores as datetime)
        $scheduleStart = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
        $scheduleEnd = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');

        // Check if requested time is within doctor's working hours
        if ($startTime < $scheduleStart || $endTime > $scheduleEnd) {
            return false;
        }

        // Normalize date for comparison (SQLite stores as datetime)
        $dateOnly = date('Y-m-d', strtotime($date));

        // Check for conflicting bookings - use simpler logic for SQLite
        $bookings = $this->bookings()
            ->whereDate('booking_date', $dateOnly)
            ->whereIn('status', ['auto_approved', 'waiting_deposit', 'deposit_confirmed'])
            ->get();

        foreach ($bookings as $booking) {
            $bookingStart = substr($booking->booking_time, 0, 5);
            $bookingEnd = substr($booking->end_time, 0, 5);

            // Check for overlap:
            // 1. New slot starts before existing booking ends AND
            // 2. New slot ends after existing booking starts
            if ($startTime < $bookingEnd && $endTime > $bookingStart) {
                return false; // There's an overlap
            }
        }

        return true; // No conflicts
    }
}
