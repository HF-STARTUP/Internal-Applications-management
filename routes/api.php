<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\userController;
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
Route::middleware(['auth:api', 'admin'])->group(function (){
    Route::get('/users/get',[adminController::class, 'getUsers']);
    Route::post('/users/add',[adminController::class, 'addUser']);
    Route::get('/apps/get',[adminController::class, 'getApps']);
    Route::get('/users/get/{id}',[adminController::class, 'getUserByID']);
    Route::get('/apps/get/{id}',[adminController::class, 'getAppByID']);
    Route::get('/roles/get/{id}/users',[adminController::class, 'getUsersByRole']);
    Route::get('/roles/get/{id}',[adminController::class, 'getRoleById']);
    Route::get('/apps/get/{id}/users',[adminController::class, 'getUsersOfApp']);
    Route::get('/roles/get',[adminController::class, 'getRoles']);
    Route::get('/nonusers/get/{appID}',[adminController::class, 'getNonUsers']);

    Route::post('/users/{id}/edit',[adminController::class, 'editUser']);
    Route::post('/apps/add',[adminController::class, 'addApp']);
    Route::post('/apps/{id}/edit',[adminController::class, 'editApp']);
    Route::post('/app/{appID}/allow',[adminController::class, 'allowApp']);
    Route::post('/roles/add',[adminController::class, 'addRole']);
    Route::post('/roles/{id}/edit',[adminController::class, 'editRole']);
    Route::post('/switchState/{id}',[adminController::class,'enableOrDesableUser']);

    Route::get('/export-template', [adminController::class, 'exportTemplate'])->name('export.template');
    Route::get('/export-users', [adminController::class, 'export'])->name('export.users');
    Route::post('/import-users', [adminController::class, 'import'])->name('import.users');

    Route::delete('/app/{appID}/prevent/{userID}',[adminController::class, 'preventApp']);
    Route::delete('/users/{id}/delete',[adminController::class, 'removeUser']);
    Route::delete('/apps/{id}/delete',[adminController::class, 'removeApp']);
    Route::delete('/roles/{id}/delete',[adminController::class, 'deleteRole']);
});
    Route::post('/users/login',[userController::class, 'login']);
    Route::get('/resetPwd/{userEmail}',[userController::class, 'resetPwd']);   
    Route::middleware('auth:sanctum')->get('/user/get/{id}/apps', [userController::class, 'getAppsOfUser']);
    Route::middleware('auth:sanctum')->get('/app/{appID}/launch', [userController::class, 'launchApp']);
    Route::middleware('auth:sanctum')->delete('/users/logout', [userController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/app/{appID}/launch', [userController::class, 'launchApp']);
    Route::middleware('auth:sanctum')->post('updatePassword', [userController::class, 'updatePassword']);
