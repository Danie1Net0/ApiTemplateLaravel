<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\Auth\RegistrationConfirmation;

/**
 * Class VerificationController
 *
 *  @OA\Tag(
 *     name="Projects",
 *     description="API Endpoints of Projects"
 * )
 *
 * @package App\Http\Controllers\Auth
 */
class VerificationController extends Controller
{
    use RegistrationConfirmation;
}
