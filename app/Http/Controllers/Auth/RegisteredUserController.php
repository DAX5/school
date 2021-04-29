<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Professor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = [
            'Professor' => 'Professor',
            'Aluno' => 'Aluno',
        ];
        $all_turmas = Turma::get();
        $turmas = [];
        foreach($all_turmas as $turma) {
            $turmas[$turma->id] = $turma->name;
        }
        return view('auth.register',compact('roles','turmas'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'O campo Nome é obrigatório!',
            'email.required' => 'O campo Email é obrigatório!',
            'password.required' => 'O campo Senha é obrigatório!',
            'role.required' => 'O campo Perfil é obrigatório!',
            'disciplina.required_if' => 'O campo Disciplina é obrigatório para o perfil Professor!',
            'nascimento.required_if' => 'O campo Data de nascimento é obrigatório para o perfil Aluno!',
            'turma_id.required_if' => 'O campo Turma é obrigatório para o perfil Aluno!',
            'email' => 'Insira um email válido!',
            'unique' => 'O email inserido já foi cadastrado!',
            'min' => 'A senha deve conter no mínimo :min caracteres!',
            'confirmed' => 'A confirmação da senha não combina!'
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required',
            'disciplina' => 'required_if:role,==,Professor|nullable',
            'nascimento' => 'required_if:role,==,Aluno|nullable',
            'turma_id' => 'required_if:role,==,Aluno|nullable',
        ], $messages);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole([$input['role']]);
        $input['user_id'] = $user->id;

        if($input['role'] == 'Professor') {
            Professor::create($input);
        } else if($input['role'] == 'Aluno') {
            Aluno::create($input);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
