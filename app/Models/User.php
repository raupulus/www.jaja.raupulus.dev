<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
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
        ];
    }

    /**
     * Devuelve la url hacia el avatar del usuario.
     *
     * @return string
     */
    public function getUrlImageAttribute(): string
    {
        if (!$this->image || ($this->image === 'images/default/avatar.webp')) {
            return asset('images/default/avatar.webp');
        }

        return asset('storage/' . $this->image);
    }


    /** Filament Panel **/

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->urlImage;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;


        // TODO: Cuando se tengan claras las condiciones de acceso a los paneles, se puede quitar este comentario.


        ##return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

}
