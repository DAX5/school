<?php
   
namespace App\Http\Controllers\API;
   
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\Role as RoleResource;
use App\Http\Controllers\API\BaseController as BaseController;
   
class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        foreach($roles as &$role) {
            $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                ->where("role_has_permissions.role_id",$role->id)
                ->select('id', 'name')
                ->get();

            $permissions = $rolePermissions->map(function ($aux) {
                return collect($aux->toArray())->all();
            })->toArray();

            $role->permissions = $permissions;
        }
        
        return $this->sendResponse(RoleResource::collection($roles), 'Roles retrieved successfully.');
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
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $all_permissions = DB::table('permissions')->get();
        $permissions = [];

        foreach($input['permission'] as $p) {
            foreach($all_permissions as $permission) {
                if($permission->name === $p['name']) {
                    array_push($permissions, $permission->id);
                }
            }
        }
        
        // return $this->sendResponse($permissions, 'Role created successfully.');
   
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($permissions);
   
        return $this->sendResponse(new RoleResource($role), 'Role created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Role::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Role not found.');
        }
   
        return $this->sendResponse(new RoleResource($product), 'Role retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $product)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();
   
        return $this->sendResponse(new RoleResource($product), 'Role updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $product)
    {
        $product->delete();
   
        return $this->sendResponse([], 'Role deleted successfully.');
    }
}
