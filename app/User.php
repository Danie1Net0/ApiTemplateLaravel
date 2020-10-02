<?php

namespace App;

use App\Models\Auth\PasswordReset;
use App\Models\Shared\Image;
use App\Models\Shared\Telephone;
use App\Traits\Shared\HasImages;
use App\Traits\Shared\HasTelephones;
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
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'activation_token',
    ];

    /**
     * @var string[]
     */
    protected $visible = [
        'id',
        'name',
        'email',
        'password',
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
        'activation_token',
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
