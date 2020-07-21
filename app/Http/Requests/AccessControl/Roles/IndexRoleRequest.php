<?php

namespace App\Http\Requests\AccessControl\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class IndexRoleRequest
 * @package App\Http\Requests\AccessControl\Permissions
 */
class IndexRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Auth::user()->can('Listar Função');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'paginate' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'array'],
            'search.*' => ['required', 'min:2', 'max:3']
        ];
    }
}
