<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ResponsibilityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/company', [CompanyController::class, 'all'])->middleware('auth:sanctum');
Route::post('/company', [CompanyController::class, 'create'])->middleware('auth:sanctum')->name('create');
Route::post('/company/update/{id}', [CompanyController::class, 'update'])->middleware('auth:sanctum')->name('update');


Route::get('/team', [TeamController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('/team', [TeamController::class, 'create'])->middleware('auth:sanctum')->name('create');
Route::post('/team/update/{id}', [TeamController::class, 'update'])->middleware('auth:sanctum')->name('update');
Route::delete('/team/{id}', [TeamController::class, 'destroy'])->middleware('auth:sanctum')->name('delete');



Route::get('/role', [RoleController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('/role', [RoleController::class, 'create'])->middleware('auth:sanctum')->name('create');
Route::post('/role/update/{id}', [RoleController::class, 'update'])->middleware('auth:sanctum')->name('update');
Route::delete('/role/{id}', [RoleController::class, 'destroy'])->middleware('auth:sanctum')->name('delete');



Route::get('/responsibility', [ResponsibilityController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('/responsibility', [ResponsibilityController::class, 'create'])->middleware('auth:sanctum')->name('create');
Route::delete('/responsibility/{id}', [ResponsibilityController::class, 'destroy'])->middleware('auth:sanctum')->name('delete');


Route::get('/employee', [EmployeeController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('/employee', [EmployeeController::class, 'create'])->middleware('auth:sanctum')->name('create');
Route::post('/employee/update/{id}', [EmployeeController::class, 'update'])->middleware('auth:sanctum')->name('update');
Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->middleware('auth:sanctum')->name('delete');




// Auth API
Route::name('auth.')->group(function () {
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('register', [UserController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('user', [UserController::class, 'fetch'])->name('fetch');
    });
});
