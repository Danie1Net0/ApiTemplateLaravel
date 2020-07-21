<?php

namespace App\Http\Requests\Auth;

use App\Repositories\Users\UserRepositoryEloquent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class VerificationRequest
 * @package App\Http\Requests\Auth
 */
class EmailVerificationRequest extends FormRequest
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
            'id' => ['required', 'integer', 'exists:users'],
            'activation_token' => ['required', 'string', 'exists:users'],
            'password' => [Rule::requiredIf(function () use ($userRepository) {
                return is_null($userRepository->find($this->request->get('id'))->password);
            }), 'string', 'max:20', 'confirmed'],
        ];
    }
}
