@section('title')
Novo Usuário
@endsection

<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col-lg-12 margin-tb">
                                <div class="pull-left">
                                    <h2>Novo Usuário</h2>
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Ops!</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        {!! Form::open(array('route' => 'users.store','method'=>'POST','autocomplete'=>'off')) !!}

                            <div class="row">
                                @include('users.fields')

                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>Perfil:</strong>
                                        {!! Form::select('role', $roles, [], array('placeholder' => 'Selecione um perfil','class' => 'form-control','id' => 'role')) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="complementar"></div>
                                
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <a class="btn btn-primary" href="{{ route('users.index') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                                </div>
                            </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    $(window).on('load',function(){
        $('#role').val('');
    });

    $('#role').change(function() {
        
        let role = $("#role option:selected").val();
        
        if(role === 'Admin') {
            $('#complementar').empty();
        } else if(role === 'Professor') {
            $('#complementar').empty();
            $('#complementar').append(`
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <hr>
                    <h4>Dados complementares</h4>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Disciplina:</strong>
                        <input type="text" class="form-control" placeholder="Disciplina" name="disciplina" />
                    </div>
                </div>
            `);
        } else if(role === 'Aluno') {
            $('#complementar').empty();
            $('#complementar').append(`
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <hr>
                    <h4>Dados complementares</h4>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Data de nascimento:</strong>
                        <input type="date" class="form-control" name="nascimento" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Turma:</strong>
                        {!! Form::select('turma_id', $turmas, [], array('placeholder' => 'Selecione uma turma','class' => 'form-control select')) !!}
                    </div>
                </div>
            `);
        }
    });
</script>