<?php

namespace App\Http\Controllers\API;

use Image;
use Exception;
use App\Models\Team;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function fetch(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $teamQuery = Team::withCount('employees');



        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
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


    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'icon.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            $name = $request->input('name');
            $teamIcons = [];

            if ($request->hasFile('icon')) {
                foreach ($request->file('icon') as $image) {
                    $imageName = time()  . rand(1, 100) . '.' . $image->getClientOriginalExtension();

                    $resizedImage = Image::make($image)->resize(850, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $resizedImage->save(public_path('files/' . $imageName), 90);

                    $teamIcons[] = $imageName;
                }
            }

            $iconString = implode(',', $teamIcons);

            $team = Team::create([
                'name' => $name,
                'icon' => $iconString,
                'company_id' => 15,
            ]);

            $response = [
                'message' => 'Data berhasil ditambahkan.',
                'data' => $team,
            ];

            return response()->json($response, 201);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Data gagal ditambahkan.'], 500);
        }
    }


    public function update(UpdateTeamRequest $request, $id)
    {

        try {
            $team = Team::find($id);
            if (!$team) {
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

            if (!$team) {
                throw new Exception('Team Not Found');
            }

            $team->delete();
            return ResponseFormatter::success($team, 'team Deleted');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}



//single image


// $request->validate([
//     'icon' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//     'name' => 'required|string|max:255',
// ]);

// $name = $request->input('name');

// // Simpan gambar menggunakan store()
// $image = $request->file('icon');
// $imageName = time()  . rand(1, 100) . '.' . $image->getClientOriginalExtension();

// $resizedImage = Image::make($image)->resize(850, null, function ($constraint) {
//     $constraint->aspectRatio();
// });
// $resizedImage->save(public_path('files/' . $imageName), 90);

// $team = Team::create([
//     'name' => $name,
//     'icon' => $imageName,
//     'company_id' => 15,
// ]);

// $team->save();

// $response = [
//     'message' => 'Data berhasil ditambahkan.',
//     'data' => $team,
// ];

// return response()->json($response, 201);
