<?php

namespace App;

use App\Models\Auth\PasswordReset;
use App\Models\Shared\Image;
use App\Models\Shared\Telephone;
use App\Traits\Shared\HasImages;
use App\Traits\Shared\HasTelephones;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, HasTelephones, HasImages;

    /**
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'cell_phone',
        'password',
        'is_active',
        'confirmation_token',
    ];

    /**
     * @var string[]
     */
    protected $visible = [
        'id',
        'name',
        'email',
        'cell_phone',
        'password',
        'is_active',
        'telephones',
        'roles',
        'permissions',
        'avatar_url',
        'is_active',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'confirmation_token',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'is_active' => 'bool',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * Relationship.
     *
     * @return MorphMany
     */
    public function telephones(): MorphMany
    {
        return $this->morphMany(Telephone::class, 'telephonable');
    }

    /**
     * Relationship.
     *
     * @return MorphOne
     */
    public function avatar(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Relationship.
     *
     * @return HasOne
     */
    public function passwordReset(): HasOne
    {
        return $this->hasOne(PasswordReset::class);
    }

    /**
     * Accessor.
     *
     * @return string
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar()->first()->path;
    }
}
