<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAulaRequest extends FormRequest
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
            'titulo'        => 'required|string',
            'horario'       => 'required',
            'turma_id'      => 'required',
            'assunto'       => 'required|string'
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
            'turma_id.required'     =>   'O campo Turma é obrigatório!',
            'titulo.required'       =>   'O campo Título é obrigatório!',
            'assunto.required'      =>   'O campo Assunto é obrigatório!',
            'horario.required'      =>   'O campo Horário é obrigatório!',
        ];
    }
}
