<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\MultiLoginController;
use App\Http\Controllers\Api\PenitipController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/produk', [ProdukController::class, 'index']);
Route::post('/register',[App\Http\Controllers\Api\AuthController::class,'register']);
Route::post('/login',[App\Http\Controllers\Api\AuthController::class,'login']);
Route::post('/multi-login', [MultiLoginController::class, 'login']);
Route::post('/multi-register', [MultiLoginController::class, 'register']);


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', [App\Http\Controllers\Api\AuthController::class, 'user']);
    Route::put('/user/update', [AuthController::class, 'updateProfile']);
    Route::post('/alamat', [AlamatController::class, 'store']);
    Route::get('/alamat', [AlamatController::class, 'index']);
    Route::get('/alamat/{id}', [AlamatController::class, 'show']);
    Route::put('/alamat/{id}', [AlamatController::class, 'update']);
    Route::delete('/alamat/{id}', [AlamatController::class, 'destroy']);
    Route::post('/penitip', [PenitipController::class, 'store']);
    Route::get('/penitip', [PenitipController::class, 'index']); // show all
    Route::delete('/penitip/{id}', [PenitipController::class, 'destroy']); // delete
    });
