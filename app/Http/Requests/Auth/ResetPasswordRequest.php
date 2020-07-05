<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ResetPasswordRequest
 * @package App\Http\Requests\Auth
 */
class ResetPasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id', 'exists:password_resets'],
            'token' => ['required', 'string', 'exists:password_resets'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed']
        ];
    }
}
