<?php
    
namespace App\Http\Controllers;
    
use DB;
use Hash;
use App\Models\Aula;
use App\Models\User;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Professor;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\UpdateUserRequest;
use Yajra\DataTables\Facades\DataTables;
    
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = [
            'Admin' => 'Administrador',
            'Professor' => 'Professor',
            'Aluno' => 'Aluno',
        ];
        $all_turmas = Turma::get();
        $turmas = [];
        foreach($all_turmas as $turma) {
            $turmas[$turma->id] = $turma->name;
        }
        return view('users.create',compact('roles','turmas'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {    
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
    
        Session::flash('success', 'Usuário criado com sucesso.');
        return redirect()->route('users.index');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name')[0];
        
        return view('users.edit',compact('user','roles','userRole'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $input = $request->all();
        if(!empty($input['password'])) { 
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input,array('password'));    
        }
        
        $user->update($input);
        
        if($user->roles->pluck('name')[0] == 'Professor') {
            $user->professor->update(['name' => $input['name']]);
        } else if($user->roles->pluck('name')[0] == 'Aluno') {
            $user->aluno->update(['name' => $input['name']]);
        }
    
        Session::flash('success', 'Usuário atualizado com sucesso.');
        return redirect()->route('users.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $admin = DB::table('roles')->first();
        if($user->hasRole($admin->name)) {
            Session::flash('error', 'Impossível remover um usuário com perfil '.$admin->name.'.');
            return redirect()->route('users.index');
        }
        
        $aluno = $user->aluno;
        if($aluno) {
            DB::table('aula_aluno')->where('aluno_id', $aluno->id)->delete();
            $aluno->delete();
        }
        
        $professor = $user->professor;
        if($professor) {
            DB::table('aula_aluno')->where('professor_id', $professor->id)->delete();
            Aula::where('professor_id', $professor->id)->delete();
            $professor->delete();
        }
        
        $user->delete();

        Session::flash('success', 'Usuário removido com sucesso.');
        return redirect()->route('users.index');
    }

    /**
     * Get users for DataTable list.
     *
     * @return Yajra\DataTables\Facades\DataTables
     */
    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::orderBy('name', 'ASC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function($row) {
                    return $row->roles->pluck('name')[0];
                })
                ->addColumn('action', function($row){
                    $edit = '';
                    $delete_open = '';
                    $delete_close = '';
                    if(auth()->user()->can('user-edit')) {
                        $edit = '<a href="' . route('users.edit', $row->id) . '" class="edit btn btn-success btn-sm" title="Editar"><i class="fa fa-edit"></i></a>';
                    }
                    if(auth()->user()->can('user-delete')) {
                        $delete_open = '<form action="'. route('users.destroy', $row->id) .'" method="POST"> ';
                        $delete_close = csrf_field() . ' <input type="hidden" name="_method" value="DELETE"><button type="submit" class="delete btn btn-danger btn-sm" title="Remover" onclick="return confirm(\'Tem certeza que deseja remover o usuário '. $row->name .'? Todos dados relacionados ao usuário serão excluidos!\')"><i class="fa fa-trash"></i></button> </form>';
                    }
                    $actionBtn = $delete_open . '<a href="' . route('users.show', $row->id) . '" class="show btn btn-primary btn-sm" title="Mostrar"><i class="fa fa-eye"></i></a> '.$edit . $delete_close;
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Send notification for Aluno or Professor.
     * GET|HEAD /notification
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function notification() {
        $aula = null;
        if(auth()->user()->roles->pluck('name')[0] == 'Aluno') {
            $aula = DB::table('aula_aluno')
                ->select(DB::raw('aula_aluno.*, aulas.titulo as titulo'))
                ->where('aluno_id', auth()->user()->aluno->id)
                ->where('visualizado', 0)
                ->leftJoin('aulas', 'aula_aluno.aula_id', 'aulas.id')
                ->first();
            if($aula) {
                $aula->perfil = 'Aluno';
            }
        } else if(auth()->user()->roles->pluck('name')[0] == 'Professor') {
            $aula = DB::table('aula_aluno')
                ->select(DB::raw('aula_aluno.*, aulas.titulo as titulo'))
                ->where('aula_aluno.professor_id', auth()->user()->professor->id)
                ->where('status', 'Pendente')
                ->leftJoin('aulas', 'aula_aluno.aula_id', 'aulas.id')
                ->first();
            if($aula) {
                $aula->perfil = 'Professor';
            }
        }
        return response()->json($aula);  
    }

    /**
     * Read notification for Aluno.
     * POST|HEAD /readNotification
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function readNotification(Request $request) {
        DB::table('aula_aluno')->where('aula_id',$request->aula_id)->where('aluno_id',auth()->user()->aluno->id)->update(['visualizado' => 1]);
        return response()->json($request->all());  
    }
}
