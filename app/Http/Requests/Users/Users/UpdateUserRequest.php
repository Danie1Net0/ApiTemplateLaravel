<?php

namespace App\Http\Requests\Users\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class UpdateUserRequest
 * @package App\Http\Requests\Users\Users
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
        return Auth::check() && Auth::user()->can('Editar Usuário');
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
            'email' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->user)],
            'telephones' => ['nullable', 'array'],
            'telephones.*.number' => ['required', 'string', 'max:15'],
            'telephones.*.type' => ['required', 'string', 'max:15'],
            'avatar' => ['nullable', 'image', 'max:2048']
        ];
    }
}
