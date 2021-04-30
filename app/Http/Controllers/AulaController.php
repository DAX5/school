<?php
    
namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAulaRequest;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\UpdateAulaRequest;
use Yajra\DataTables\Facades\DataTables;
    
class AulaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:aula-list|aula-create|aula-edit|aula-delete', ['only' => ['index','show']]);
        $this->middleware('permission:aula-create', ['only' => ['create','store']]);
        $this->middleware('permission:aula-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:aula-delete', ['only' => ['destroy']]);
        $this->middleware('permission:aula-register', ['only' => ['register']]);
        $this->middleware('permission:aula-accept', ['only' => ['accept']]);
        $this->middleware('permission:aula-cancel', ['only' => ['cancel']]);
        $this->middleware('permission:aula-reject', ['only' => ['reject']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('aulas.index');
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
        return view('aulas.create',compact('turmas'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreAulaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAulaRequest $request)
    {   
        if(auth()->user()->roles->pluck('name')[0] != 'Professor') {
            Session::flash('error', 'Apenas professores podem criar aulas.');
            return redirect()->route('aulas.index');
        }

        $input = $request->all();
        $input['professor_id'] = auth()->user()->professor->id;
        
        Aula::create($input);
    
        Session::flash('success', 'Aula criada com sucesso.');
        return redirect()->route('aulas.index');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function show(Aula $aula)
    {
        $inscricao = null;
        $inscricoes = null;
        if(auth()->user()->roles->pluck('name')[0] == 'Aluno') {
            $inscricao = DB::table('aula_aluno')->where('aula_id', $aula->id)->where('aluno_id',auth()->user()->aluno->id)->first();
        } else if(auth()->user()->roles->pluck('name')[0] == 'Professor' && $aula->professor_id == auth()->user()->professor->id) {
            $inscricoes = DB::table('aula_aluno')
                ->select(DB::raw('aula_aluno.*, alunos.name as name'))
                ->where('aula_id', $aula->id)
                ->leftJoin('alunos', 'aula_aluno.aluno_id', 'alunos.id')
                ->get();
        }
        
        return view('aulas.show',compact('aula','inscricao','inscricoes'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function edit(Aula $aula)
    {
        $all_turmas = Turma::get();
        $turmas = [];
        foreach($all_turmas as $turma) {
            $turmas[$turma->id] = $turma->name;
        }
        
        return view('aulas.edit',compact('aula','turmas'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateAulaRequest  $request
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAulaRequest $request, Aula $aula)
    {
        $aula->update($request->all());
    
        Session::flash('success', 'Aula atualizada com sucesso.');
        return redirect()->route('aulas.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aula $aula)
    {
        DB::table('aula_aluno')->where('aula_id', $aula->id)->delete();
        $aula->delete();

        Session::flash('success', 'Aula removida com sucesso.');
        return redirect()->route('aulas.index');
    }

    /**
     * Makes participation request for the aula.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function register(Aula $aula)
    {
        if(auth()->user()->roles->pluck('name')[0] != 'Aluno' || $aula->turma->id != auth()->user()->aluno->turma_id) {
            Session::flash('error', 'Você não pode se inscrever na aula selecionada!');
            return redirect()->route('aulas.index');
        }

        $inscricoes = DB::table('aula_aluno')
            ->select(DB::raw('aula_aluno.*, aulas.horario as horario'))
            ->where('aula_id', $aula->id)->orWhere('aluno_id',auth()->user()->aluno->id)
            ->leftJoin('aulas', 'aula_aluno.aula_id', 'aulas.id')
            ->get();
        
        foreach($inscricoes as $inscricao) {
            if ($inscricao->aula_id == $aula->id && $inscricao->aluno_id == auth()->user()->aluno->id) {
                Session::flash('error', 'Você já fez inscrição nessa aula!');
                return redirect()->route('aulas.index');
            }
            if ($inscricao->horario == $aula->horario) {
                Session::flash('error', 'Você já está inscrito em outra aula no mesmo horário!');
                return redirect()->route('aulas.index');
            }
        }
        
        DB::table('aula_aluno')->insert([
            'aula_id'       => $aula->id,
            'aluno_id'      => auth()->user()->aluno->id,
            'professor_id'  => $aula->professor_id,
            'status'        => 'Pendente',
            'visualizado'   => 1
        ]);

        Session::flash('success', 'Pedido de partição realizado com sucesso.');
        return redirect()->route('aulas.index');
    }

    /**
     * Cancel participation for the aula.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function cancel(Aula $aula)
    {
        DB::table('aula_aluno')->where('aula_id',$aula->id)->where('aluno_id',auth()->user()->aluno->id)->delete();

        Session::flash('success', 'Inscrição cancelada com sucesso.');
        return redirect()->route('aulas.show', $aula->id);
    }

    /**
     * Accept participation for the aula.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function accept(Aula $aula, Aluno $aluno)
    {
        if(auth()->user()->roles->pluck('name')[0] == 'Professor' && auth()->user()->professor->id == $aula->professor_id) {
            DB::table('aula_aluno')->where('aula_id',$aula->id)->where('aluno_id',$aluno->id)->update(['status' => 'Aceito', 'visualizado' => 0]);

            Session::flash('success', 'Inscrição aceita com sucesso.');
            return redirect()->route('aulas.show', $aula->id);
        }

        Session::flash('error', 'Você não pode aceitar a inscrição selecionada!');
        return redirect()->route('aulas.show', $aula->id);
    }

    /**
     * Reject participation for the aula.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request)
    {
        $aula = Aula::find($request->aula_id);
        $aluno = Aluno::find($request->aluno_id);

        if(auth()->user()->roles->pluck('name')[0] == 'Professor' && auth()->user()->professor->id == $aula->professor_id) {
            DB::table('aula_aluno')->where('aula_id',$aula->id)->where('aluno_id',$aluno->id)->update([
                'status' => 'Rejeitado',
                'mensagem' => $request->mensagem,
                'visualizado' => 0,
            ]);

            Session::flash('success', 'Inscrição rejeitada com sucesso.');
            return redirect()->route('aulas.show', $aula->id);
        }

        Session::flash('error', 'Você não pode rejeitar a inscrição selecionada!');
        return redirect()->route('aulas.show', $aula->id);
    }

    /**
     * Get users for DataTable list.
     *
     * @return Yajra\DataTables\Facades\DataTables
     */
    public function getAulas(Request $request)
    {
        if ($request->ajax()) {
            if(auth()->user()->roles->pluck('name')[0] == 'Aluno') {
                $data = Aula::where('turma_id', auth()->user()->aluno->turma_id)->orderBy('horario', 'asc')->get();
            } else {
                $data = Aula::orderBy('horario', 'asc')->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('professor', function($row) {
                    return $row->professor->name;
                })
                ->editColumn('horario', function($row) {
                    return date('d/m/Y - H:i', strtotime($row->horario));
                })
                ->addColumn('turma', function($row) {
                    return $row->turma->name;
                })
                ->addColumn('action', function($row){
                    $edit = '';
                    $delete_open = '';
                    $delete_close = '';
                    if(auth()->user()->can('aula-edit') && ((auth()->user()->roles->pluck('name')[0] == 'Professor' && auth()->user()->professor->id === $row->professor_id) || auth()->user()->roles->pluck('name')[0] == 'Admin')) {
                        $edit = '<a href="' . route('aulas.edit', $row->id) . '" class="edit btn btn-success btn-sm" title="Editar"><i class="fa fa-edit"></i></a>';
                    }
                    if(auth()->user()->can('aula-delete') && ((auth()->user()->roles->pluck('name')[0] == 'Professor' && auth()->user()->professor->id === $row->professor_id) || auth()->user()->roles->pluck('name')[0] == 'Admin')) {
                        $delete_open = '<form action="'. route('aulas.destroy', $row->id) .'" method="POST"> ';
                        $delete_close = csrf_field() . ' <input type="hidden" name="_method" value="DELETE"><button type="submit" class="delete btn btn-danger btn-sm" title="Remover" onclick="return confirm(\'Tem certeza que deseja remover o aula '. $row->titulo .'?\')"><i class="fa fa-trash"></i></button> </form>';
                    }
                    $actionBtn = $delete_open . '<a href="' . route('aulas.show', $row->id) . '" class="show btn btn-primary btn-sm" title="Mostrar"><i class="fa fa-eye"></i></a> '.$edit . $delete_close;
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
