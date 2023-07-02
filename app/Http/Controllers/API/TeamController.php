<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {
    
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery = Team::query();



       if($id){
        $team = $teamQuery->find($id);

        if($team){
            return ResponseFormatter::success($team, 'Team Found');
        }

        return ResponseFormatter::error('team Not Found', '404');
       }

        
        // Get multiple data
        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams found'
        );
    }

    public function create(CreateTeamRequest $request)
    {
       try {
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('public/icons');
        }

        $team = Team::create([
            'name' => $request->name,
            'icon' => $path,
            'company_id' => $request->company_id
        ]);

        if (!$team) {
           throw new Exception('team Nout Found');
        }


        return ResponseFormatter::success($team, 'team Created');
       } catch (\Throwable $th) {
        return ResponseFormatter::error($th->getMessage(), 500);
       }
    }


    public function update(UpdateTeamRequest $request, $id)
    {
       
        try {
            $team = Team::find($id);
            if(!$team ){
                throw new Exception('Team not Found');
            }

            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            $team->update([
                'name' => $request->name,
                'icon' => isset($path) ? $path : $team->icon,
                'company_id' => $request->company_id
            ]);
            
            return ResponseFormatter::success($team, 'Team Updated');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }

    }

    public function destroy($id)
    {
        try {
            $team = Team::find($id);

            if(!$team){
                throw new Exception('Team Not Found');
            }

            $team->delete();
            return ResponseFormatter::success($team, 'team Deleted');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
