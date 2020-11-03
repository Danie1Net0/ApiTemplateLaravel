<?php

namespace App\Http\Requests\AccessControl\Permissions;

use App\Models\AccessControl\Permission;
use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use App\Rules\Shared\CheckOrderParamRule;
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
            'conditions' => ['nullable', 'string', new CheckSearchParamsRule('permissions')],
            'or-conditions' => ['nullable', 'string', new CheckSearchParamsRule('permissions')],
            'columns' => ['nullable', 'string', new CheckIfColumnExistsRule('permissions')],
            'order' => ['nullable', 'string', new CheckOrderParamRule('permissions')],
            'relationships' => ['nullable', 'string', new CheckIfRelationshipExistsRule(Permission::class)],
        ];
    }
}
