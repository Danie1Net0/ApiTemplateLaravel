<?php

namespace App\Models;

use App\Models\Auth\PasswordReset;
use App\Models\Shared\Image;
use App\Models\Shared\Telephone;
use App\Traits\Shared\HasImages;
use App\Traits\Shared\HasTelephones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasApiTokens, HasFactory, HasImages, HasRoles, HasTelephones, Notifiable;

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
        'is_active',
        'telephones',
        'roles',
        'permissions',
        'avatar',
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
}
