<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Responsibility;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
    
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $responsibilityQuery = Responsibility::query();



       if($id){
        $responsibility = $responsibilityQuery->find($id);

        if($responsibility){
            return ResponseFormatter::success($responsibility, 'Responsibility Found');
        }

        return ResponseFormatter::error('responsibility Not Found', '404');
       }

        
        // Get multiple data
        $responsibilities = $responsibilityQuery->where('role_id', $request->role_id);

        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Responsibilities found'
        );
    }

    public function create(CreateResponsibilityRequest $request)
    {
       try {
      

        $responsibility = Responsibility::create([
            'name' => $request->name,
            'role_id' => $request->role_id
        ]);

        if (!$responsibility) {
           throw new Exception('responsibility Nout Found');
        }


        return ResponseFormatter::success($responsibility, 'responsibility Created');
       } catch (\Throwable $th) {
        return ResponseFormatter::error($th->getMessage(), 500);
       }
    }


   

    public function destroy($id)
    {
        try {
            $responsibility = Responsibility::find($id);

            if(!$responsibility){
                throw new Exception('Responsibility Not Found');
            }

            $responsibility->delete();
            return ResponseFormatter::success($responsibility, 'responsibility Deleted');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
