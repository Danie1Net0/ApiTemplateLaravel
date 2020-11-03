<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Auth\ResetPasswords;

/**
 * Class ResetPassordController
 * @package App\Http\Controllers\Auth
 */
class ResetPasswordController extends Controller
{
    use ResetPasswords;
}
