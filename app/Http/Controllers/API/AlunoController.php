<?php
   
namespace App\Http\Controllers\API;
   
use Validator;
use App\Models\User;
use App\Models\Aluno;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Aluno as AlunoResource;
use App\Http\Controllers\API\BaseController as BaseController;
   
class AlunoController extends BaseController
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
        $alunos = Aluno::all();

        return $this->sendResponse(AlunoResource::collection($alunos), 'Alunoes retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|same:confirm-password',
            'nascimento'    => 'required|date_format:Y-m-d',
            'turma_id'      => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        
        $user = User::create($input);
        $user->assignRole(['Aluno']);
        $input['user_id'] = $user->id;

        $aluno = Aluno::create($input);

        return $this->sendResponse(new AlunoResource($aluno), 'Aluno created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function show(Aluno $aluno)
    {
        if (is_null($aluno)) {
            return $this->sendError('Aluno not found.');
        }
   
        return $this->sendResponse(new AlunoResource($aluno), 'Aluno retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Aluno $aluno)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,'.$aluno->user->id,
            'password'      => 'nullable|min:8|same:confirm-password',
            'nascimento'    => 'required|date_format:Y-m-d',
            'turma_id'      => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input,array('password'));    
        }
   
        $user = $aluno->user;
        
        $user->update($input);
        $aluno->update($input);
   
        return $this->sendResponse(new AlunoResource($aluno), 'Aluno updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Aluno  $aluno
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aluno $aluno)
    {
        $user = $aluno->user;
        $user->delete();
        $aluno->delete();
   
        return $this->sendResponse([], 'Aluno deleted successfully.');
    }
}
