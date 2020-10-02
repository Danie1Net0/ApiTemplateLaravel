<?php

namespace App\Http\Requests\Users;

use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class IndexUserRequest
 * @package App\Http\Requests\Users
 */
class IndexUserRequest extends FormRequest
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
            'search' => ['nullable', 'array'],
            'search.*' => ['required', 'min:2', 'max:3'],
            'columns' => ['nullable', 'array'],
            'columns.*' => ['required', 'string', new CheckIfColumnExistsRule('users')],
            'relationships' => ['nullable', 'array'],
            'relationships.*' => ['required', 'string', new CheckIfRelationshipExistsRule(new User())],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'string', 'exists:roles,name']
        ];
    }
}
