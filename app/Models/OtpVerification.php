<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'whatsapp_number',
        'otp_code',
        'purpose',
        'expires_at',
        'attempts',
        'verified',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean',
    ];

    /**
     * Check if OTP is expired
     */
    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }

    /**
     * Check if OTP is valid
     */
    public function isValid($code)
    {
        return !$this->isExpired() && 
               !$this->verified && 
               $this->otp_code === $code && 
               $this->attempts < 5;
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts()
    {
        $this->increment('attempts');
    }

    /**
     * Mark as verified
     */
    public function markAsVerified()
    {
        $this->update(['verified' => true]);
    }
}
