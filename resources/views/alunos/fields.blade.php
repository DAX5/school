<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Nome:</strong>
        {!! Form::text('name', null, array('placeholder' => 'Nome','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Data de nascimento:</strong>
        {!! Form::date('nascimento', null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Turma:</strong>
        {!! Form::select('turma_id', $turmas, isset($aluno) ? $aluno->turma_id : [], array('placeholder' => 'Selecione uma turma','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Email:</strong>
        {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Senha:</strong>
        {!! Form::password('password', array('placeholder' => 'Senha','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Confirmação da senha:</strong>
        {!! Form::password('confirm-password', array('placeholder' => 'Confirmação da senha','class' => 'form-control')) !!}
    </div>
</div>
