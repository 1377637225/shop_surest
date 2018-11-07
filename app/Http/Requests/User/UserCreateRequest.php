<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\User\IsPhoneRule;
use App\Rules\User\IsEmailRule;

class UserCreateRequest extends FormRequest
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
            'name' => ['required','between:2,16','unique:users'],
            'password' => ['required','between:6,35','confirmed'],
            'phone' => [new IsPhoneRule(true)],
            'email' => [new IsEmailRule(true)]
        ];
    }

    public function attributes()
    {
        return [
            'name' => '用户名',
            'password' => '密码',
        ];
    }
}
