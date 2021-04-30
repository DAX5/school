<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'professor_id',
        'turma_id',
        'titulo',
        'assunto',
        'horario'
    ];

    public function professor(){
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    public function turma(){
        return $this->belongsTo(Turma::class, 'turma_id');
    }
}
