<?php
   
namespace App\Http\Controllers\API;
   
use Validator;
use App\Models\Aula;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Aula as AulaResource;
use App\Http\Controllers\API\BaseController as BaseController;
   
class AulaController extends BaseController
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
        if(auth()->user()->roles->pluck('name')[0] == 'Aluno') {
            $aulas = Aula::where('turma_id', auth()->user()->aula->turma_id)->orderBy('horario', 'asc')->get();
        } else {
            $aulas = Aula::all();
        }

        return $this->sendResponse(AulaResource::collection($aulas), 'Aulas retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->roles->pluck('name')[0] != 'Professor') {
            return $this->sendError('Only professores can create aulas.');
        }

        $input = $request->all();
   
        $validator = Validator::make($input, [
            'titulo'    => 'required|string',
            'horario'   => 'required|date_format:Y-m-d H:i:s',
            'turma_id'  => 'required',
            'assunto'   => 'required|string'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['professor_id'] = auth()->user()->professor->id;
        
        $aula = Aula::create($input);

        return $this->sendResponse(new AulaResource($aula), 'Aula created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function show(Aula $aula)
    {
        if (is_null($aula)) {
            return $this->sendError('Aula not found.');
        }

        if(auth()->user()->roles->pluck('name')[0] == 'Aluno') {
            $inscricao = DB::table('aula_aluno')->where('aula_id', $aula->id)->where('aluno_id',auth()->user()->aluno->id)->first();
            $aula->inscricoes = $inscricao;
        } else if(auth()->user()->roles->pluck('name')[0] == 'Professor' && $aula->professor_id == auth()->user()->professor->id) {
            $inscricoes = DB::table('aula_aluno')
                ->select(DB::raw('aula_aluno.*, alunos.name as name'))
                ->where('aula_id', $aula->id)
                ->leftJoin('alunos', 'aula_aluno.aluno_id', 'alunos.id')
                ->get();
            $aula->inscricoes = $inscricoes;
        }
   
        return $this->sendResponse(new AulaResource($aula), 'Aula retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Aula $aula)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'titulo'        => 'required|string',
            'horario'       => 'required|date_format:Y-m-d H:i:s',
            'turma_id'      => 'required',
            'assunto'       => 'required|string'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $aula->update($input);
   
        return $this->sendResponse(new AulaResource($aula), 'Aula updated successfully.');
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
   
        return $this->sendResponse([], 'Aula deleted successfully.');
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
            return $this->sendError('You cannot participate in this aula.');
        }

        $inscricoes = DB::table('aula_aluno')
            ->select(DB::raw('aula_aluno.*, aulas.horario as horario'))
            ->where('aula_id', $aula->id)->orWhere('aluno_id',auth()->user()->aluno->id)
            ->leftJoin('aulas', 'aula_aluno.aula_id', 'aulas.id')
            ->get();
        
        foreach($inscricoes as $inscricao) {
            if ($inscricao->aula_id == $aula->id && $inscricao->aluno_id == auth()->user()->aluno->id) {
                return $this->sendError('You already signed up for this aula.');
            }
            if ($inscricao->horario == $aula->horario) {
                return $this->sendError('You are already enrolled in another aula at the same time.');
            }
        }
        
        $inscricao = [
            'aula_id'       => $aula->id,
            'aluno_id'      => auth()->user()->aluno->id,
            'professor_id'  => $aula->professor_id,
            'status'        => 'Pendente',
            'visualizado'   => 1
        ];
        DB::table('aula_aluno')->insert($inscricao);

        $aula->inscricoes = $inscricao;

        return $this->sendResponse(new AulaResource($aula), 'Participation successfully requested.');
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

        return $this->sendResponse([], 'Participation canceled successfully.');
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
            $inscricao = DB::table('aula_aluno')->where('aula_id',$aula->id)->where('aluno_id',$aluno->id)->first();
            $aula->inscricoes = $inscricao;

            return $this->sendResponse(new AulaResource($aula), 'Participation accept successfully.');
        }

        return $this->sendError('You cannot accept the selected participation.');
    }

    /**
     * Reject participation for the aula.
     *
     * @param  \App\Models\Aula  $aula
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aula_id'   => 'required',
            'aluno_id'  => 'required',
            'mensagem'  => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $aula = Aula::find($request->aula_id);
        $aluno = Aluno::find($request->aluno_id);

        if(auth()->user()->roles->pluck('name')[0] == 'Professor' && auth()->user()->professor->id == $aula->professor_id) {
            DB::table('aula_aluno')->where('aula_id',$aula->id)->where('aluno_id',$aluno->id)->update([
                'status' => 'Rejeitado',
                'mensagem' => $request->mensagem,
                'visualizado' => 0,
            ]);

            $inscricao = DB::table('aula_aluno')->where('aula_id',$aula->id)->where('aluno_id',$aluno->id)->first();
            $aula->inscricoes = $inscricao;

            return $this->sendResponse(new AulaResource($aula), 'Participation accept successfully.');
        }

        return $this->sendError('You cannot reject the selected participation.');
    }
}
