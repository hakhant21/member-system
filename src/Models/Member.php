<?php

namespace Det\Members\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Det\Members\Database\Factories\MemberFactory;


class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'members';
    public $guard_name = 'member'; // Important for Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationship to Profile
    public function profile()
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        // 1. Search (Name, Email, Phone)
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        // 2. Filter by Role
        $query->when($filters['role'] ?? null, function ($q, $role) {
            $q->whereHas('roles', fn($sub) => $sub->where('name', $role));
        });

        // 3. Filter by Active Status
        $query->when(isset($filters['is_active']), function ($q) use ($filters) {
            $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        });

        // 4. Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        // Allowable sort columns to prevent SQL injection
        if (in_array($sortBy, ['created_at', 'name', 'email'])) {
            $query->orderBy($sortBy, $sortDir);
        }
    }

    protected static function newFactory()
    {
        return MemberFactory::new();
    }
}