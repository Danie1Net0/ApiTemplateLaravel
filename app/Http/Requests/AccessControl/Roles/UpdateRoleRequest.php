<?php

namespace App\Http\Requests\AccessControl\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRoleRequest
 * @package App\Http\Requests\AccessControl\Roles
 */
class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Auth::user()->can('Editar Função');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', Rule::unique('roles')->ignore($this->role)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['required', 'integer', 'exists:permissions,id']
        ];
    }
}
