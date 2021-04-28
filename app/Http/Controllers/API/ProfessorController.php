<?php
   
namespace App\Http\Controllers\API;
   
use Validator;
use App\Models\User;
use App\Models\Professor;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Professor as ProfessorResource;
use App\Http\Controllers\API\BaseController as BaseController;
   
class ProfessorController extends BaseController
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
        $prefessors = Professor::all();

        return $this->sendResponse(ProfessorResource::collection($prefessors), 'Professores retrieved successfully.');
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
            'disciplina'    => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        
        $user = User::create($input);
        $user->assignRole(['Professor']);
        $input['user_id'] = $user->id;

        $professor = Professor::create($input);

        return $this->sendResponse(new ProfessorResource($professor), 'Professor created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function show(Professor $professor)
    {
        if (is_null($professor)) {
            return $this->sendError('Professor not found.');
        }
   
        return $this->sendResponse(new ProfessorResource($professor), 'Professor retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Professor $professor)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,'.$professor->user->id,
            'password'      => 'nullable|min:8|same:confirm-password',
            'disciplina'    => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
   
        $user = $professor->user;
        
        $user->update($input);
        $professor->update($input);
   
        return $this->sendResponse(new ProfessorResource($professor), 'Professor updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Professor  $professor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Professor $professor)
    {
        $user = $professor->user;
        $user->delete();
        $professor->delete();
   
        return $this->sendResponse([], 'Professor deleted successfully.');
    }
}
