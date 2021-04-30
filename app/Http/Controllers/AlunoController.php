<?php
    
namespace App\Http\Controllers;

use Hash;
use App\Models\User;
use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreAlunoRequest;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\UpdateAlunoRequest;
    
class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:aluno-list|aluno-create|aluno-edit|aluno-delete', ['only' => ['index','show']]);
        $this->middleware('permission:aluno-create', ['only' => ['create','store']]);
        $this->middleware('permission:aluno-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:aluno-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('alunos.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $all_turmas = Turma::get();
        $turmas = [];
        foreach($all_turmas as $turma) {
            $turmas[$turma->id] = $turma->name;
        }
        return view('alunos.create',compact('turmas'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreAlunoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlunoRequest $request)
    {   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        
        $user = User::create($input);
        $user->assignRole(['Aluno']);
        $input['user_id'] = $user->id;

        Aluno::create($input);
    
        Session::flash('success', 'Aluno criado com sucesso.');
        return redirect()->route('alunos.index');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function show(Aluno $aluno)
    {
        if(auth()->user()->roles->pluck('name')[0] == 'Aluno' && $aluno->id != auth()->user()->aluno->id) {
            Session::flash('error', 'Você não pode acessar os dados do usuário selecionado!');
            return redirect()->route('alunos.index');
        }

        return view('alunos.show',compact('aluno'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function edit(Aluno $aluno)
    {
        if(auth()->user()->roles->pluck('name')[0] == 'Aluno' && $aluno->id != auth()->user()->aluno->id) {
            Session::flash('error', 'Você não pode editar os dados do aluno selecionado!');
            return redirect()->route('alunos.index');
        }

        $all_turmas = Turma::get();
        $turmas = [];
        foreach($all_turmas as $turma) {
            $turmas[$turma->id] = $turma->name;
        }
        $aluno->email = $aluno->user->email;
        return view('alunos.edit',compact('aluno','turmas'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateAlunoRequest  $request
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlunoRequest $request, Aluno $aluno)
    {
        $input = $request->all();
        if(!empty($input['password'])) { 
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input,array('password'));    
        }

        $user = $aluno->user;
        
        $user->update($input);
        $aluno->update($input);
    
        Session::flash('success', 'Aluno atualizado com sucesso.');
        return redirect()->route('alunos.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aluno $aluno)
    {
        DB::table('aula_aluno')->where('aluno_id', $aluno->id)->delete();
        $user = $aluno->user;
        $aluno->delete();
        $user->delete();

        Session::flash('success', 'Aluno removido com sucesso.');
        return redirect()->route('alunos.index');
    }

    /**
     * Get users for DataTable list.
     *
     * @return Yajra\DataTables\Facades\DataTables
     */
    public function getAlunos(Request $request)
    {
        if ($request->ajax()) {
            if(auth()->user()->roles->pluck('name')[0] == 'Aluno') {
                $data = Aluno::where('id', auth()->user()->aluno->id)->get();
            } else {
                $data = Aluno::orderBy('name', 'ASC')->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('email', function($row) {
                    return $row->user->email;
                })
                ->editColumn('nascimento', function($row) {
                    return date('d/m/Y', strtotime($row->nascimento));
                })
                ->addColumn('turma', function($row) {
                    return $row->turma->name;
                })
                ->addColumn('action', function($row){
                    $edit = '';
                    $delete_open = '';
                    $delete_close = '';
                    if(auth()->user()->can('aluno-edit')) {
                        $edit = '<a href="' . route('alunos.edit', $row->id) . '" class="edit btn btn-success btn-sm" title="Editar"><i class="fa fa-edit"></i></a>';
                    }
                    if(auth()->user()->can('aluno-delete')) {
                        $delete_open = '<form action="'. route('alunos.destroy', $row->id) .'" method="POST"> ';
                        $delete_close = csrf_field() . ' <input type="hidden" name="_method" value="DELETE"><button type="submit" class="delete btn btn-danger btn-sm" title="Remover" onclick="return confirm(\'Tem certeza que deseja remover o aluno '. $row->name .'? Todos dados relacionados ao aluno serão excluidos!\')"><i class="fa fa-trash"></i></button> </form>';
                    }
                    $actionBtn = $delete_open . '<a href="' . route('alunos.show', $row->id) . '" class="show btn btn-primary btn-sm" title="Mostrar"><i class="fa fa-eye"></i></a> '.$edit . $delete_close;
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
