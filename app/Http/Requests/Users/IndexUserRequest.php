<?php

namespace App\Http\Requests\Users;

use App\Repositories\Implementations\AccessControl\RoleRepositoryEloquent;
use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use App\Rules\Shared\CheckIfRoleExistsRule;
use App\Rules\Shared\CheckSearchParamsRule;
use App\Models\User;
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
     * @param RoleRepositoryEloquent $roleRepository
     * @return array
     */
    public function rules(RoleRepositoryEloquent $roleRepository)
    {
        return [
            'paginate' => ['nullable', 'integer', 'min:1'],
            'conditions' => ['nullable', 'string', new CheckSearchParamsRule('users')],
            'or-conditions' => ['nullable', 'string', new CheckSearchParamsRule('users')],
            'columns' => ['nullable', 'string', new CheckIfColumnExistsRule('users')],
            'relationships' => ['nullable', 'string', new CheckIfRelationshipExistsRule(User::class)],
            'roles' => ['nullable', 'string', new CheckIfRoleExistsRule($roleRepository)],
        ];
    }
}
