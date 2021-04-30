<?php
   
namespace App\Http\Controllers\API;
   
use Validator;
use App\Models\Aula;
use App\Models\User;
use App\Models\Aluno;
use App\Models\Professor;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
use App\Http\Controllers\API\BaseController as BaseController;
   
class UserController extends BaseController
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
        $users = User::all();

        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
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
            'role'          => 'required',
            'disciplina'    => 'required_if:role,==,Professor|nullable',
            'nascimento'    => 'required_if:role,==,Aluno|nullable|date_format:Y-m-d',
            'turma_id'      => 'required_if:role,==,Aluno|nullable'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
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
   
        return $this->sendResponse(new UserResource($user), 'User created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if (is_null($user)) {
            return $this->sendError('User not found.');
        }
   
        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'password'  => 'nullable|min:8|same:confirm-password'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
   
        $user->update($input);

        if($user->roles->pluck('name')[0] == 'Professor') {
            $user->professor->update(['name' => $input['name']]);
        } else if($user->roles->pluck('name')[0] == 'Aluno') {
            $user->aluno->update(['name' => $input['name']]);
        }
   
        return $this->sendResponse(new UserResource($user), 'User updated successfully.');
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
            return $this->sendError('Unable to remove a user with role '.$admin->name.'.');
        }

        $aluno = $user->aluno;
        if($aluno) {
            DB::table('aula_aluno')->where('aluno_id', $aluno->id)->delete();
            $aluno->delete();
        }
        
        $professor = $user->professor;
        if($professor) {
            $aulas = Aula::where('professor_id', $professor->id)->get();
            foreach($aulas as $aula) {
                DB::table('aula_aluno')->where('aula_id', $aula->id)->delete();
                $aula->delete();
            }
            $professor->delete();
        }
        
        $user->delete();
   
        return $this->sendResponse([], 'User deleted successfully.');
    }
}
