<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" value="Nome" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" value="Email" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" value="Senha" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmação da senha" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" />
            </div>

            <!-- Role -->
            <div class="mt-4">
                <x-label for="role" value="Perfil" />

                {!! Form::select('role', $roles, [], array('placeholder' => 'Selecione um perfil','class' => 'form-control select','id' => 'role')) !!}
            </div>

            <div id="complementar"></div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">Já tem cadastro?</a>

                <x-button class="ml-4">Registrar</x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>

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
                <div class="mt-4">
                    <hr>
                    <h4 style="margin-top: 10px;">Dados complementares</h4>
                </div>
                <div class="mt-4">
                    <x-label for="disciplina" value="Disciplina" />

                    <x-input id="disciplina" class="block mt-1 w-full" type="text" name="disciplina" :value="old('disciplina')" />
                </div>
            `);
        } else if(role === 'Aluno') {
            $('#complementar').empty();
            $('#complementar').append(`
            <div class="mt-4">
                    <hr>
                    <h4 style="margin-top: 10px;">Dados complementares</h4>
                </div>
                <div class="mt-4">
                    <x-label for="nascimento" value="Data de nascimento" />

                    <x-input id="nascimento" class="block mt-1 w-full" type="date" name="nascimento" :value="old('nascimento')" />
                </div>
                <div class="mt-4">
                    <x-label for="turma_id" value="Turma" />

                    {!! Form::select('turma_id', $turmas, [], array('placeholder' => 'Selecione uma turma','class' => 'form-control select')) !!}
                </div>
            `);
        }
    });
</script>

<style>
    .select {
        width: 100%;
        --tw-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        margin-top: 0.25rem;
        display: block;
        border-radius: 0.375rem;
        --tw-border-opacity: 1;
        border-color: rgba(209, 213, 219, var(--tw-border-opacity));
    }
    .select:focus {
        appearance: none;
        background-color: #fff;
        border-color: rgba(59, 130, 246, 0.5);
        border-width: 1px;
        padding-top: 0.5rem;
        padding-right: 0.75rem;
        padding-bottom: 0.5rem;
        padding-left: 0.75rem;
        font-size: 1rem;
        line-height: 1.5rem;
        --tw-ring-offset-width: 0px;
        --tw-ring-offset-color: #fff;
        --tw-ring-color: rgba(59, 130, 246, 0.5);
        --tw-ring-offset-shadow: 0 0 #0000;
        --tw-ring-shadow: 0 0 #0000;
    }
</style>
