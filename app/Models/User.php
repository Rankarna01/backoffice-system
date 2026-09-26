<?php

namespace App\Models;

use App\Domain\Identity\Models\MentorProfile;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'avatar_media_id',
        'timezone',
        'locale',
        'status',
        'last_login_at',
        'terms_accepted_version',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Determine if user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow access to admin panel if user is super_admin, admin, or mentor
        if ($this->hasAnyRole(['super_admin', 'admin', 'mentor'])) {
            return true;
        }

        // Allow super admin by email if roles aren't seeded yet
        if ($this->email === 'admin@tradingedu.com') {
            return true;
        }

        return false;
    }

    /**
     * Relationship to MentorProfile
     */
    public function mentorProfile(): HasOne
    {
        return $this->hasOne(MentorProfile::class);
    }

    public function liveSessionRegistrations(): HasMany
    {
        return $this->hasMany(\App\Domain\Community\Models\LiveSessionRegistration::class);
    }

    public function registeredLiveSessions(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domain\Community\Models\LiveSession::class,
            'live_session_registrations',
            'user_id',
            'live_session_id'
        )->withPivot(['attended', 'attended_at', 'notes'])->withTimestamps();
    }
}
