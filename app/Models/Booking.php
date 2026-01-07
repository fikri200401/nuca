<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'treatment_id',
        'doctor_id',
        'booking_date',
        'booking_time',
        'end_time',
        'status',
        'total_price',
        'discount_amount',
        'final_price',
        'is_manual_entry',
        'admin_notes',
        'customer_notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'is_manual_entry' => 'boolean',
    ];

    /**
     * Get booking_time as HH:MM format
     */
    public function getBookingTimeAttribute($value)
    {
        if (!$value) return null;
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    /**
     * Get end_time as HH:MM format
     */
    public function getEndTimeAttribute($value)
    {
        if (!$value) return null;
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->booking_code) {
                $booking->booking_code = 'BK-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function beforeAfterPhotos()
    {
        return $this->hasOne(BeforeAfterPhoto::class);
    }

    public function voucherUsage()
    {
        return $this->hasOne(VoucherUsage::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['auto_approved', 'waiting_deposit', 'deposit_confirmed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', ['auto_approved', 'deposit_confirmed'])
                     ->where('booking_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->toDateString());
    }

    public function scopeWaitingDeposit($query)
    {
        return $query->where('status', 'waiting_deposit');
    }

    /**
     * Helper methods
     */
    public function needsDeposit()
    {
        $bookingDate = \Carbon\Carbon::parse($this->booking_date);
        $daysDifference = now()->diffInDays($bookingDate, false);
        
        return $daysDifference >= 7; // Booking 7 hari atau lebih butuh DP
    }

    public function canBeFeedback()
    {
        return $this->status === 'completed' && !$this->feedback;
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isPending()
    {
        return $this->status === 'waiting_deposit';
    }

    public function isConfirmed()
    {
        return in_array($this->status, ['auto_approved', 'deposit_confirmed']);
    }
}
