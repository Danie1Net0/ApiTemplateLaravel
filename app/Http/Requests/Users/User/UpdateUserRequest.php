<?php

namespace App\Http\Requests\Users\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest
 * @package App\Http\Requests\Users\User
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:20', 'confirmed'],
            'telephones.*' => ['nullable', 'string', 'max:15']
        ];
    }
}
