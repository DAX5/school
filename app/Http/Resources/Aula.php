<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Aula extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'professor' => $this->professor->name,
            'turma' => $this->turma->name,
            'titulo' => $this->titulo,
            'assunto' => $this->assunto,
            'inscricoes' => $this->inscricoes
        ];
    }
}
