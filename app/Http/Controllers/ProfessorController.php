<?php
    
namespace App\Http\Controllers;

use Hash;
use App\Models\Aula;
use App\Models\User;
use App\Models\Professor;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreProfessorRequest;
use App\Http\Requests\UpdateProfessorRequest;
    
class ProfessorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:professor-list|professor-create|professor-edit|professor-delete', ['only' => ['index','show']]);
        $this->middleware('permission:professor-create', ['only' => ['create','store']]);
        $this->middleware('permission:professor-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:professor-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('professors.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('professors.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreProfessorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProfessorRequest $request)
    {   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        
        $user = User::create($input);
        $user->assignRole(['Professor']);
        $input['user_id'] = $user->id;

        Professor::create($input);
    
        Session::flash('success', 'Professor criado com sucesso.');
        return redirect()->route('professors.index');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function show(Professor $professor)
    {
        return view('professors.show',compact('professor'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function edit(Professor $professor)
    {
        if(auth()->user()->roles->pluck('name')[0] == 'Professor' && $professor->id != auth()->user()->professor->id) {
            Session::flash('error', 'Você não pode editar os dados do professor selecionado!');
            return redirect()->route('professors.index');
        }

        $professor->email = $professor->user->email;
        return view('professors.edit',compact('professor'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateProfessorRequest  $request
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfessorRequest $request, Professor $professor)
    {
        $input = $request->all();
        if(!empty($input['password'])) { 
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input,array('password'));    
        }

        $user = $professor->user;
        
        $user->update($input);
        $professor->update($input);
    
        Session::flash('success', 'Professor atualizado com sucesso.');
        return redirect()->route('professors.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Professor $professor)
    {
        $aulas = Aula::where('professor_id', $professor->id)->get();
        foreach($aulas as $aula) {
            DB::table('aula_aluno')->where('aula_id', $aula->id)->delete();
            $aula->delete();
        }

        $user = $professor->user;
        $professor->delete();
        $user->delete();

        Session::flash('success', 'Professor removido com sucesso.');
        return redirect()->route('professors.index');
    }

    /**
     * Get users for DataTable list.
     *
     * @return Yajra\DataTables\Facades\DataTables
     */
    public function getProfessors(Request $request)
    {
        if ($request->ajax()) {
            $data = Professor::orderBy('name', 'ASC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('email', function($row) {
                    return $row->user->email;
                })
                ->addColumn('action', function($row){
                    $edit = '';
                    $delete_open = '';
                    $delete_close = '';
                    if(auth()->user()->can('professor-edit') && ((auth()->user()->roles->pluck('name')[0] == 'Professor' && auth()->user()->professor->id === $row->id) || auth()->user()->roles->pluck('name')[0] == 'Admin')) {
                        $edit = '<a href="' . route('professors.edit', $row->id) . '" class="edit btn btn-success btn-sm" title="Editar"><i class="fa fa-edit"></i></a>';
                    }
                    if(auth()->user()->can('professor-delete')) {
                        $delete_open = '<form action="'. route('professors.destroy', $row->id) .'" method="POST"> ';
                        $delete_close = csrf_field() . ' <input type="hidden" name="_method" value="DELETE"><button type="submit" class="delete btn btn-danger btn-sm" title="Remover" onclick="return confirm(\'Tem certeza que deseja remover o professor '. $row->name .'? Todos dados relacionados ao professor serão excluidos!\')"><i class="fa fa-trash"></i></button> </form>';
                    }
                    $actionBtn = $delete_open . '<a href="' . route('professors.show', $row->id) . '" class="show btn btn-primary btn-sm" title="Mostrar"><i class="fa fa-eye"></i></a> '.$edit . $delete_close;
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
