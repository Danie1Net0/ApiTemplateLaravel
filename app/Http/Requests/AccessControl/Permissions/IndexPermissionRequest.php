<?php

namespace App\Http\Requests\AccessControl\Permissions;

use App\Rules\Shared\CheckSearchParamsRule;
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
        return Auth::check() && Auth::user()->can($this->route()->getName());
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
            'conditions' => ['nullable', 'string', new CheckSearchParamsRule('users')],
            'or-conditions' => ['nullable', 'string', new CheckSearchParamsRule('users')],
        ];
    }
}
