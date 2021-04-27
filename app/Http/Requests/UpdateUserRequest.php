<?php

namespace App\Http\Requests;

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
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email,'.$this->user->id,
            'password'  => 'nullable|min:8|same:confirm-password',
            'roles'     => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'     =>   'O campo Nome é obrigatório!',
            'email.required'    =>   'O campo Email é obrigatório!',
            'roles.required'    =>   'O campo Papel é obrigatório!',
            'email'             =>   'Insira um email válido!',
            'unique'            =>   'O email inserido já foi cadastrado!',
            'min'               =>   'A senha deve conter no mínimo :min caracteres!',
            'same'              =>   'A confirmação da senha não combina!'
        ];
    }
}
