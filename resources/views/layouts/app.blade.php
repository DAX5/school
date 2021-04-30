<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous">
    </script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous">
    </script>

    <!-- jQuery 3.1.1 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- Bootstrap 4.5.0 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- DataTables 1.10.21 -->
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

<div class="toast" id="professor" style="position: absolute; top: 20px; right: 20px;" data-autohide="false">
    <div class="toast-header">
        <strong class="mr-auto">Notificação</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body" style="background-color: #efefef">
        <p style="text-decoration: none; color: black;" id="mensagem">Você tem novos pedidos de participação pendentes para sua aula.</p>
    </div>
</div>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<!-- ToastR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">

<script>
    // Notification alert
    $(document).ready(function() {
        $.getJSON('/notification', function(data) {
            if (data.aula_id) {
                if (data.perfil === "Aluno") {
                    if (data.status === "Rejeitado") {
                        $('#mensagem').text('Sua participação na aula ' + data.titulo + ' foi recusada pelo motivo: ' + data.mensagem + '.');
                    } else if (data.status === "Aceito") {
                        $('#mensagem').text('Sua participação na aula ' + data.titulo + ' foi aceita.');
                    }
                    $('.toast-body').css('cursor', 'pointer').click(function() {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            url: '/readNotification',
                            type: 'POST',
                            data: data,
                            dataType: 'JSON',

                            success: function() {
                                location.href = '/aulas/' + data.aula_id;
                            }
                        });
                    });
                    $('.toast').toast('show');
                } else if (data.perfil === "Professor") {
                    $('#mensagem').text('Você tem um novo pedido para participação da aula ' + data.titulo + ' pendente.');
                    $('.toast-body').css('cursor', 'pointer').click(function() {
                        location.href = "/aulas/" + data.aula_id;
                    });
                    $('.toast').toast('show');
                }
            }
        });
    });

    // Toast Notification 
    @if(Session::has('success'))
    toastr.success("{{ Session::get('success')}}")
    @endif

    @if(Session::has('error'))
    toastr.error("{{ Session::get('error')}}")
    @endif
</script>

</html>