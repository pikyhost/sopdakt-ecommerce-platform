<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail, HasLocalePreference
{
    use HasFactory, Notifiable, HasRoles, HasPermissions,HasApiTokens;

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return $this->hasAnyRole([
                UserRole::SuperAdmin->value,
                UserRole::Admin->value,
            ]);
        }

        if (Filament::getCurrentPanel()->getId() === 'client') {
            return $this->hasRole(UserRole::Client->value);
        }

        return false;
    }

    public function savedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'saved_products')
            ->withTimestamps();
    }

    /**
     * Get the user's preferred locale.
     */
    public function preferredLocale(): string
    {
        return $this->locale;
    }
}
