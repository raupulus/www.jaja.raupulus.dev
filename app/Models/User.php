<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\DeletesStoredImages;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo User
 *
 * Representa un usuario registrado, administrador o colaborador de la plataforma.
 *
 * @property int $id
 * @property string $name
 * @property string|null $nick
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $avatar
 * @property int $role_id
 * @property-read string $urlImage
 * @property-read bool $isAdmin
 * @property-read Collaborator|null $collaborator
 */
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use DeletesStoredImages, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Atributos de imagen gestionados en Storage::disk('public').
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['avatar' => 'images/default/avatar.webp'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nick',
        'email',
        'password',
        'avatar',
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
     * Relación con el colaborador del usuario.
     *
     * @return HasOne<Collaborator, $this>
     */
    public function collaborator(): HasOne
    {
        return $this->hasOne(Collaborator::class, 'user_id', 'id');
    }

    /**
     * Devuelve la url hacia el avatar del usuario.
     */
    public function getUrlImageAttribute(): string
    {
        if (! $this->avatar || ($this->avatar === 'images/default/avatar.webp')) {
            return asset('images/default/avatar.webp');
        }

        return asset('storage/'.$this->avatar);
    }

    /**
     * Es administrador este usuario.
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->role_id === 1;
    }

    /** Filament Panel **/
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->urlImage;
    }

    /**
     * Determina si el usuario puede acceder a cada panel de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin,
            'panel' => true,
            default => false,
        };
    }
}
