<?php

namespace App\Http\Requests\Users\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ShowUserRequest
 * @package App\Http\Requests\Users\Users
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
        return Auth::check() && Auth::user()->can('Visualizar Usuário');
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
            'columns.*' => ['required', 'string']
        ];
    }
}
