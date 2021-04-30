@section('title')
Aula
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
                                    <h2>Aula</h2>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Título:</strong>
                                    <p>{{ $aula->titulo }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Professor:</strong>
                                    <p>{{ $aula->professor->name }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Horário:</strong>
                                    <p>{{ date('d/m/Y - H:i', strtotime($aula->horario)) }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Turma:</strong>
                                    <p>{{ $aula->turma->name }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Assunto</strong>
                                    <p>{{ $aula->assunto }}</p>
                                </div>
                            </div>
                        </div>

                        @can('aula-accept')
                        @if($inscricoes)
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nº</th>
                                            <th>Aluno</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i=0; $i<count($inscricoes); $i++) <tr>
                                            <td>{{ $i+1 }}</td>
                                            <td>{{ $inscricoes[$i]->name }}</td>
                                            <td>{{ $inscricoes[$i]->status }}</td>
                                            <td>
                                                @if($inscricoes[$i]->status == 'Pendente')
                                                <a href="{{ route('aulas.accept', [$aula->id, $inscricoes[$i]->aluno_id]) }}" class="btn btn-success btn-sm" title="Aprovar">Aprovar</a>
                                                <a class="btn btn-danger btn-sm" title="Rejeitar" data-toggle="modal" data-target="#rejectModal" onclick="modal('{{$inscricoes[$i]->aluno_id}}')" style="color: white;">Rejeitar</a>
                                                @endif
                                            </td>
                                            </tr>
                                            @endfor
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        @endcan

                        @can('aula-register')
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center" style="margin-bottom: 20px;">
                            @if($inscricao)
                            <h5>Status da inscrição: <strong>{{ $inscricao->status }}</strong></h5><br>
                            @if($inscricao->status == "Rejeitado")
                            <p>Motivo: {{ $inscricao->mensagem }}</p>
                            @endif
                            <a class="btn btn-danger" href="{{ route('aulas.cancel', $aula->id) }}" title="Cancelar inscrição"> Cancelar inscrição </a>
                            @else
                            <a class="btn btn-success" href="{{ route('aulas.register', $aula->id) }}" title="Inscrever na aula"> Inscrever na aula </a>
                            @endif
                        </div>
                        @endcan

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <a class="btn btn-primary" href="{{ route('aulas.index') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</x-app-layout>

<!-- Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Insira o motivo da rejeição</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(array('route' => 'aulas.reject','method'=>'POST','autocomplete'=>'off')) !!}
            <div class="modal-body">
                <input type="hidden" name="aula_id" value="{{ $aula->id }}" />
                <input type="hidden" name="aluno_id" id="aluno_id" />
                <input type="text" name="mensagem" class="form-control" placeholder="Motivo" required />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    function modal(aluno_id) {
        $('#aluno_id').val(aluno_id);
    }
</script>
