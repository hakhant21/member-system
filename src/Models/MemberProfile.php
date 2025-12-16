<?php

namespace DET\Members\Models;

use DET\Members\Database\Factories\MemberProfileFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $member_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property array|null $settings
 * @property array|null $preferences
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class MemberProfile extends Model
{
    use SoftDeletes;

    protected $table = 'member_profiles';

    protected $fillable = [
        'member_id',
        'first_name', 'last_name',
        'phone', 'phone_verified_at',
        'date_of_birth', 'gender',
        'avatar', 'bio',
        'address', 'city', 'state', 'country', 'postal_code',
        'facebook', 'twitter', 'linkedin', 'instagram', 'website',
        'company', 'job_title', 'department',
        'settings', 'preferences',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'phone_verified_at' => 'datetime',
        'settings' => 'array',      // Automatically converts JSON to Array
        'preferences' => 'array',   // Automatically converts JSON to Array
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    protected static function newFactory()
    {
        return MemberProfileFactory::new();
    }
}
