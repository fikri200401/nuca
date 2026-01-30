<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_transaction',
        'valid_from',
        'valid_until',
        'is_single_use',
        'max_usage',
        'usage_count',
        'is_active',
        'show_on_landing',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_single_use' => 'boolean',
        'max_usage' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
        'show_on_landing' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('valid_from', '<=', now())
                     ->where('valid_until', '>=', now());
    }

    public function scopeForLanding($query)
    {
        return $query->where('show_on_landing', true);
    }

    /**
     * Helper methods
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if (now()->lt($this->valid_from) || now()->gt($this->valid_until)) {
            return false;
        }

        if ($this->max_usage && $this->usage_count >= $this->max_usage) {
            return false;
        }

        return true;
    }

    public function canBeUsedBy($userId, $transactionAmount)
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($transactionAmount < $this->min_transaction) {
            return false;
        }

        if ($this->is_single_use) {
            $hasUsed = $this->usages()->where('user_id', $userId)->exists();
            if ($hasUsed) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'nominal') {
            return min($this->value, $amount);
        }

        // percentage
        return ($amount * $this->value) / 100;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function getFormattedValueAttribute()
    {
        if ($this->type === 'nominal') {
            return 'Rp ' . number_format($this->value, 0, ',', '.');
        }
        return $this->value . '%';
    }
}
