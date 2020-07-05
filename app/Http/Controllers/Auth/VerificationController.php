<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Auth\VerifiesEmails;

/**
 * Class VerificationController
 * @package App\Http\Controllers\Auth
 */
class VerificationController extends Controller
{
    use VerifiesEmails;
}
