@section('title')
Aluno
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
                                    <h2>Aluno</h2>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Nome:</strong>
                                    <p>{{ $aluno->name }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Data de nascimento:</strong>
                                    <p>{{ date('d/m/Y', strtotime($aluno->nascimento)) }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Email</strong>
                                    <p>{{ $aluno->user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <a class="btn btn-primary" href="{{ route('alunos.index') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>