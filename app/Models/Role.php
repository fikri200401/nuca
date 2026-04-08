<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active'   => 'boolean',
    ];

    /**
     * Users that belong to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Count active users with this role.
     */
    public function activeUsersCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    /**
     * Check if this role has a given permission.
     */
    public function hasPermission(string $module, string $action = 'view'): bool
    {
        $permissions = $this->permissions ?? [];
        return (bool) ($permissions[$module][$action] ?? false);
    }

    /**
     * All available modules with their display names.
     */
    public static function availableModules(): array
    {
        return [
            'dashboard'      => 'Dashboard',
            'laporan'        => 'Laporan Analitik',
            'manajemen_user' => 'Manajemen User',
            'manajemen_role' => 'Manajemen Role',
            'treatments'     => 'Treatment',
            'doctors'        => 'Dokter',
            'bookings'       => 'Booking',
            'deposits'       => 'Deposit',
            'vouchers'       => 'Voucher',
            'members'        => 'Member',
            'feedbacks'      => 'Feedback',
            'settings'       => 'Konfigurasi',
        ];
    }

    /**
     * Scope: only active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
