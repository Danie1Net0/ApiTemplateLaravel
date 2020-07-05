<?php

namespace App\Models\Auth;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

/**
 * Class PasswordReset
 * @package App\Models\Auth
 */
class PasswordReset extends Model
{
    use Notifiable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'email', 'token'
    ];

    /**
     * Relationship.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
