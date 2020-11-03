<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserRequest
 * @package App\Http\Requests\Users
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
            'email' => ['required_without:cell_phone', 'email', 'unique:users'],
            'cell_phone' => ['required_without:email', 'string', 'size:11', 'unique:users'],
            'password' => ['nullable', 'string', 'max:20', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'string', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'string', 'exists:permissions,id']
        ];
    }
}
