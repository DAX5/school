<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'role'          => 'required',
            'disciplina'    => 'required_if:role,==,Professor|nullable',
            'nascimento'    => 'required_if:role,==,Aluno|nullable',
            'turma_id'      => 'required_if:role,==,Aluno|nullable'
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
            'name.required'             =>   'O campo Nome é obrigatório!',
            'email.required'            =>   'O campo Email é obrigatório!',
            'password.required'         =>   'O campo Senha é obrigatório!',
            'role.required'             =>   'O campo Perfil é obrigatório!',
            'disciplina.required_if'    =>   'O campo Disciplina é obrigatório para o perfil Professor!',
            'nascimento.required_if'    =>   'O campo Data de nascimento é obrigatório para o perfil Aluno!',
            'turma_id.required_if'      =>   'O campo Turma é obrigatório para o perfil Aluno!',
            'email'                     =>   'Insira um email válido!',
            'unique'                    =>   'O email inserido já foi cadastrado!',
            'min'                       =>   'A senha deve conter no mínimo :min caracteres!',
            'same'                      =>   'A confirmação da senha não combina!'
        ];
    }
}
