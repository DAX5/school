<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Título:</strong>
        {!! Form::text('titulo', null, array('placeholder' => 'Título','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Horário:</strong>
        <input type="datetime-local" class="form-control" name="horario" value="{{ isset($aula) ? date('Y-m-d\TH:i', strtotime($aula->horario)) : '' }}">
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Turma:</strong>
        {!! Form::select('turma_id', $turmas, isset($aula) ? $aula->turma_id : [], array('placeholder' => 'Selecione uma turma','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Assunto:</strong>
        {!! Form::textarea('assunto', null, array('placeholder' => 'Assunto','class' => 'form-control','rows' => 3)) !!}
    </div>
</div>
