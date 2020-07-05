<?php

namespace App;

use App\Models\Auth\PasswordReset;
use App\Models\Telephones\Telephone;
use App\Traits\Telephones\HasTelephones;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, HasTelephones;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'path_image',
        'is_active',
        'activation_token',
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
     * @return HasOne
     */
    public function passwordReset(): HasOne
    {
        return $this->hasOne(PasswordReset::class);
    }

}
