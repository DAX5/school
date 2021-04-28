<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlunoRequest extends FormRequest
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
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|same:confirm-password',
            'nascimento'    => 'required'
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
            'name.required'         =>   'O campo Nome é obrigatório!',
            'password.required'     =>   'O campo Senha é obrigatório!',
            'email.required'        =>   'O campo Email é obrigatório!',
            'nascimento.required'   =>   'O campo Data de nascimento é obrigatório!',
            'email'                 =>   'Insira um email válido!',
            'unique'                =>   'O email inserido já foi cadastrado!',
            'min'                   =>   'A senha deve conter no mínimo :min caracteres!',
            'same'                  =>   'A confirmação da senha não combina!'
        ];
    }
}
