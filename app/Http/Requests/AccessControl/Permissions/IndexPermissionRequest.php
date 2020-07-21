<?php

namespace App\Http\Requests\AccessControl\Permissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class IndexPermissionRequest
 * @package App\Http\Requests\AccessControl\Permissions
 */
class IndexPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check() && Auth::user()->can('Listar Permissão');
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
