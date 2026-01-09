<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'proof_of_payment',
        'status',
        'deadline_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deadline_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Helper methods
     */
    public function isExpired()
    {
        return $this->status === 'pending' && now()->greaterThan($this->deadline_at);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function approve($adminId)
    {
        $this->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $adminId,
        ]);
    }

    public function reject($adminId, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => $adminId,
            'rejection_reason' => $reason,
        ]);
    }
}
