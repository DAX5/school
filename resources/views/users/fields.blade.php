<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Nome:</strong>
        {!! Form::text('name', null, array('placeholder' => 'Nome','class' => 'form-control')) !!}
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
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Papel:</strong>
        {!! Form::select('roles[]', $roles, isset($userRole) ? $userRole : [], array('class' => 'form-control','multiple')) !!}
    </div>
</div>
