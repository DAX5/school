@section('title')
Aulas
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aulas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container mt-5">
                        <h2 class="mb-4">Aulas 
                            @can('aula-create')
                                <a class="btn btn-primary" href="{{ route('aulas.create') }}" title="Novo Aluno"> Novo </a>
                            @endcan
                        </h2>

                        <table class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Título</th>
                                    <th>Professor</th>
                                    <th>Horário</th>
                                    <th>Turma</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script type="text/javascript">
  $(function () {
    
    var table = $('.yajra-datatable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
        },
        processing: true,
        serverSide: true,
        ajax: "{{ route('aulas.list') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'titulo', name: 'titulo'},
            {data: 'professor', name: 'professor'},
            {data: 'horario', name: 'horario'},
            {data: 'turma', name: 'turma'},
            {
                data: 'action', 
                name: 'action', 
                orderable: true, 
                searchable: true
            },
        ]
    });
    
  });
</script>
