<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Rule;
use App\Rules\IdIsRequired;
use App\Rules\IdIsExist;


class RoleRequest extends FormRequest
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
            'name' => ['required','between:2,10','unique:roles'],
            'id' => [new IdIsExist],
            'ids' => ['required','array',new IdIsRequired],
        ];
    }

    public function attributes()
    {
        return [
            'name' => '角色名',
            'ids' => '权限',
            'description' => '描述'
        ];
    }
}
