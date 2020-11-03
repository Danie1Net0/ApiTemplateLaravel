<?php

namespace App\Http\Requests\AccessControl\Permissions;

use App\Models\AccessControl\Permission;
use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ShowRoleRequest
 * @package App\Http\Requests\AccessControl\Roles
 */
class ShowPermissionRequest extends FormRequest
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
            'columns' => ['nullable', 'string', new CheckIfColumnExistsRule('permissions')],
            'relationships' => ['nullable', 'string', new CheckIfRelationshipExistsRule(Permission::class)],
        ];
    }
}
