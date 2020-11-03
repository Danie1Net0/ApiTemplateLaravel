<?php

namespace App\Http\Requests\AccessControl\Roles;

use App\Models\AccessControl\Role;
use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ShowRoleRequest
 * @package App\Http\Requests\AccessControl\Roles
 */
class ShowRoleRequest extends FormRequest
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
            'columns' => ['nullable', 'string', new CheckIfColumnExistsRule('roles')],
            'relationships' => ['nullable', 'string', new CheckIfRelationshipExistsRule(Role::class)],
        ];
    }
}
