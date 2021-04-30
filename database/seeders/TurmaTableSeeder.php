<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurmaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $turmas = [
            '1º Ano',
            '2º Ano',
            '3º Ano',
        ];
    
        foreach ($turmas as $turma) {
            DB::table('turmas')->insert(['name' => $turma]);
        }
    }
}
