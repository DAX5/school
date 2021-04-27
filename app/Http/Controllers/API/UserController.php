<?php
   
namespace App\Http\Controllers\API;
   
use Validator;
use App\Models\User;
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

        return $this->sendResponse(UserResource::collection($users), 'Roles retrieved successfully.');
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
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|same:confirm-password',
            'roles'     => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
   
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
            'password'  => 'nullable|min:8|same:confirm-password',
            'roles'     => 'required'
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
        DB::table('model_has_roles')->where('model_id',$user->id)->delete();
    
        $user->assignRole($request->input('roles'));
   
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

        $user->delete();
   
        return $this->sendResponse([], 'Role deleted successfully.');
    }
}
