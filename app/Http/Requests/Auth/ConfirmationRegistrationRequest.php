<?php

namespace App\Http\Requests\Auth;

use App\Repositories\Implementations\Users\UserRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class VerificationRequest
 * @package App\Http\Requests\Auth
 */
class ConfirmationRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param UserRepositoryEloquent $userRepository
     * @return array
     */
    public function rules(UserRepositoryEloquent $userRepository)
    {
        return [
            'email' => ['required_without:phone', 'email', 'exists:users'],
            'phone' => ['required_without:email', 'string', 'size:11', 'exists:users,cell_phone'],
            'token' => ['required', 'string', 'min:6', 'max:6', 'exists:users,confirmation_token'],
            'password' => [Rule::requiredIf(function () use ($userRepository) {
                $user = $userRepository->scopeQuery(function ($query)  {
                    return $query->where('email', $this->get('email'))
                        ->orWhere('cell_phone', $this->get('phone'));
                })->first();

                return $user ? is_null($user->password) : false;
            }), 'string', 'max:20', 'confirmed'],
        ];
    }
}
