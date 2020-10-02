<?php

namespace App\Http\Requests\Users;

use App\Rules\Shared\CheckIfColumnExistsRule;
use App\Rules\Shared\CheckIfRelationshipExistsRule;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ShowUserRequest
 * @package App\Http\Requests\Users
 */
class ShowUserRequest extends FormRequest
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
            'columns' => ['nullable', 'array'],
            'columns.*' => ['required', 'string', new CheckIfColumnExistsRule('users')],
            'relationships' => ['nullable', 'array'],
            'relationships.*' => ['required', 'string', new CheckIfRelationshipExistsRule(new User())]
        ];
    }
}
