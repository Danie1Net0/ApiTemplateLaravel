<?php

namespace App\Http\Requests\Users\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserRequest
 * @package App\Http\Requests\Users\User
 */
class CreateUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:20', 'confirmed'],
            'telephones' => ['nullable', 'array'],
            'telephones.*.number' => ['required', 'string', 'max:15'],
            'telephones.*.type' => ['required', 'string', 'max:15']
        ];
    }
}
