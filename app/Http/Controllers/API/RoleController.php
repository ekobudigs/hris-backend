<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function fetch(Request $request)
    {
    
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $with_responsibility = $request->input('with_responsibility', false);

        $roleQuery = Role::query();



       if($id){
        $role = $roleQuery->with('responsbilities')->find($id);

        if($role){
            return ResponseFormatter::success($role, 'Role Found');
        }

        return ResponseFormatter::error('role Not Found', '404');
       }

        
        // Get multiple data
        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibility) {
            $roles->with('responsbilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Roles found'
        );
    }

    public function create(CreateRoleRequest $request)
    {
       try {
      

        $role = Role::create([
            'name' => $request->name,
            'company_id' => $request->company_id
        ]);

        if (!$role) {
           throw new Exception('role Nout Found');
        }


        return ResponseFormatter::success($role, 'role Created');
       } catch (\Throwable $th) {
        return ResponseFormatter::error($th->getMessage(), 500);
       }
    }


    public function update(UpdateRoleRequest $request, $id)
    {
       
        try {
            $role = Role::find($id);
            if(!$role ){
                throw new Exception('Role not Found');
            }

           

            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id
            ]);
            
            return ResponseFormatter::success($role, 'Role Updated');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }

    }

    public function destroy($id)
    {
        try {
            $role = Role::find($id);

            if(!$role){
                throw new Exception('Role Not Found');
            }

            $role->delete();
            return ResponseFormatter::success($role, 'role Deleted');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
