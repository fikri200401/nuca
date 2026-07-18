<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'whatsapp_number',
        'member_number',
        'username',
        'birth_date',
        'gender',
        'address',
        'is_member',
        'member_discount',
        'role',
        'role_id',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_member' => 'boolean',
            'is_active' => 'boolean',
            'member_discount' => 'decimal:2',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function noShowNotes()
    {
        return $this->hasMany(NoShowNote::class);
    }

    public function voucherUsages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Scopes
     */
    public function scopeMembers($query)
    {
        return $query->where('is_member', true);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeStaff($query)
    {
        return $query->whereIn('role', ['admin', 'owner', 'doctor', 'frontdesk']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper methods
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user has a specific permission via their role's permission matrix.
     * If the user has a role_id assigned, the permission matrix always takes precedence.
     * Only users without a role_id (e.g. legacy super-admin) fall back to full access.
     */
    public function canDo(string $module, string $action = 'view'): bool
    {
        // If a role is assigned via role_id, ALWAYS use its permission matrix.
        // This applies to everyone — owner, admin, frontdesk, doctor, etc.
        if ($this->role_id && $this->roleModel) {
            return $this->roleModel->hasPermission($module, $action);
        }

        // Fallback for users with no role_id (e.g. legacy super-admin account):
        // owner/admin enum get full access, everyone else is denied.
        return in_array($this->role, ['owner', 'admin']);
    }
}
