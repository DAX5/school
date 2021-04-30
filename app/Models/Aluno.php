<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'turma_id',
        'name',
        'nascimento',
        'turma'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function turma(){
        return $this->belongsTo(Turma::class, 'turma_id');
    }
}
