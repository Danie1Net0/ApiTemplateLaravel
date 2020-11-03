<?php

namespace App\Http\Requests\AccessControl\Roles;

use App\Models\AccessControl\Role;
use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use App\Rules\Shared\CheckOrderParamRule;
use App\Rules\Shared\CheckSearchParamsRule;
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
            'conditions' => ['nullable', 'string', new CheckSearchParamsRule('roles')],
            'or-conditions' => ['nullable', 'string', new CheckSearchParamsRule('roles')],
            'columns' => ['nullable', 'string', new CheckIfColumnExistsRule('roles')],
            'order' => ['nullable', 'string', new CheckOrderParamRule('roles')],
            'relationships' => ['nullable', 'string', new CheckIfRelationshipExistsRule(Role::class)],
        ];
    }
}
