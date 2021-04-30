<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAulaAlunoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aula_aluno', function (Blueprint $table) {
            $table->integer('aula_id')->unsiged();
            $table->foreign('aula_id')->references('id')->on('aulas')->onDelete('cascade');
            $table->integer('aluno_id')->unsiged();
            $table->foreign('aluno_id')->references('id')->on('alunos')->onDelete('cascade');
            $table->integer('professor_id')->unsiged();
            $table->foreign('professor_id')->references('id')->on('professor')->onDelete('cascade');
            $table->string('status');
            $table->string('mensagem')->nullable();
            $table->tinyInteger('visualizado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aula_aluno');
    }
}
