<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function all(Request $request)
    {

        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });



        if ($id) {
            $company = $companyQuery->find($id);

            if ($company) {
                return ResponseFormatter::success($company, 'Company Found');
            }

            return ResponseFormatter::error('company Not Found', '404');
        }


        // Get multiple data
        $companies = $companyQuery;

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            $company = Company::create([
                'name' => $request->name,
                'logo' => isset($path) ? $path : ''
            ]);

            if (!$company) {
                throw new Exception('Company Nout Found');
            }


            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            $company->load('users');

            return ResponseFormatter::success($company, 'Company Created');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }


    public function update(UpdateCompanyRequest $request, $id)
    {

        try {
            $company = Company::find($id);
            if (!$company) {
                throw new Exception('Company not Found');
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            $company->update([
                'name' => $request->name,
                'logo' =>  isset($path) ? $path : $company->logo
            ]);

            return ResponseFormatter::success($company, 'Company Updated');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
