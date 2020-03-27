<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'patronymic' => 'required|string|min:3',
            'role' => 'required|string|in:admin,user',
            'user_category_id' => 'required|integer',
            'email' => 'required|email',
            'password' => 'nullable|string|min:6',
        ];
    }
}
