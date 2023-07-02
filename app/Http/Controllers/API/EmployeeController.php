<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
    
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');
        $age = $request->input('age');
        $phone = $request->input('phone');
        $team_id = $request->input('team_id');
        $role_id = $request->input('role_id');
        $limit = $request->input('limit', 10);

        $employeeQuery = Employee::query();



       if($id){
        $employee = $employeeQuery->with(['team', 'role'])->find($id);

        if($employee){
            return ResponseFormatter::success($employee, 'Employee Found');
        }

        return ResponseFormatter::error('employee Not Found', '404');
       }

        
        // Get multiple data
        $employees = $employeeQuery;

        if ($name) {
            $employees->where('name', 'like', '%' . $name . '%');
        }
        if ($email) {
            $employees->where('email', $email);
        }
        if ($age) {
            $employees->where('age', $age);
        }
        if ($phone) {
            $employees->where('phone', $phone);
        }
        if ($team_id) {
            $employees->where('team_id', $team_id);
        }
        if ($role_id) {
            $employees->where('role_id', $role_id);
        }

        return ResponseFormatter::success(
            $employees->paginate($limit),
            'Employees found'
        );
    }

    public function create(CreateEmployeeRequest $request)
    {
    
       try {
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/photos');
        }

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'age' => $request->age,
            'phone' => $request->phone,
            'photo' => $path,
            'team_id' => $request->team_id,
            'role_id' => $request->role_id,
        ]);

        if (!$employee) {
           throw new Exception('employee Nout Found');
        }


        return ResponseFormatter::success($employee, 'employee Created');
       } catch (\Throwable $th) {
        return ResponseFormatter::error($th->getMessage(), 500);
       }
    }


    public function update(UpdateEmployeeRequest $request, $id)
    {
       
        try {
            $employee = Employee::find($id);
            if(!$employee ){
                throw new Exception('Employee not Found');
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }

            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $employee->photo,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);
            
            return ResponseFormatter::success($employee, 'Employee Updated');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }

    }

    public function destroy($id)
    {
        try {
            $employee = Employee::find($id);

            if(!$employee){
                throw new Exception('Employee Not Found');
            }

            $employee->delete();
            return ResponseFormatter::success($employee, 'employee Deleted');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
