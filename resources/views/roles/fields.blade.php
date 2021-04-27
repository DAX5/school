<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Nome:</strong>
        {!! Form::text('name', null, array('placeholder' => 'Nome','class' => 'form-control')) !!}
    </div>
</div>
<div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
        <strong>Permissão:</strong>
        <br/>
        @foreach($permission as $value)
            @if(isset($rolePermissions))
                <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                {{ $value->name }}</label>
            @else
                <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                {{ $value->name }}</label>
            @endif
        <br/>
        @endforeach
    </div>
</div>