@section('title')
Perfil
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
                                    <h2>Perfil</h2>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Nome:</strong>
                                    <p>{{ $role->name }}</p>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <strong>Permissões:</strong>
                                    @if(!empty($rolePermissions))
                                        <ul>
                                        @foreach($rolePermissions as $v)
                                            <li><label class="label label-success">{{ $v->name }}</label></li>
                                        @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <a class="btn btn-primary" href="{{ route('roles.index') }}" title="Go back"> <i class="fas fa-backward "></i> </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>