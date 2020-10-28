<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ResendVerificationRequest
 * @package App\Http\Requests\Auth
 */
class ResendVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required_without:phone', 'email', 'exists:users'],
            'phone' => ['required_without:email', 'string', 'size:11', 'exists:users,cell_phone'],
        ];
    }
}
