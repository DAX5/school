<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'disciplina'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aula(){
        return $this->hasOne(Aula::class, 'professor_id');
    }
}
