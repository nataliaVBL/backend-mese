<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\SensorController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Rotas do Painel
|--------------------------------------------------------------------------
*/

Route::post('/usuario/{id_usuario}/painel', [PainelController::class, 'store']); 
Route::put('/usuario/{id_usuario}/painel/{id_painel}', [PainelController::class, 'update']);
Route::delete('usuario/{id_usuario}/painel/{id_painel}', [PainelController::class, 'destroy']);
Route::get('usuario/{id_usuario}/paineis', [PainelController::class, 'read']);
Route::get('usuario/{id_usuario}/painel/search', [PainelController::class, 'search']);



Route::post('/usuario', [UsuarioController::class, 'store']); 

Route::post('/modulo/{id}', [ModuloController::class, 'store']); 

Route::post('/sensor/{id}', [SensorController::class, 'store']);

Route::get('usuario/{id_usuario}/painel/{id_painel}', [ModuloController::class, 'read']);

Route::patch('usuario/{id_usuario}/painel/{id_painel}', [ModuloController::class, 'update']);

Route::delete('usuario/{id_usuario}/painel/{id_painel}/{id}', [ModuloController::class, 'destroy']);

Route::get('usuario/{id_usuario}/painel/{id_painel}/modulo/search', [ModuloController::class, 'search']);

Route::patch('/usuario/{id_usuario}/painel/{id_painel}/modulo/{id_modulo}', [SensorController::class, 'update']);

Route::delete('usuario/{id_usuario}/painel/{id_painel}/modulo/{id_modulo}/sensor/{id_sensor}', [SensorController::class, 'destroy']);

Route::get('usuario/{id_usuario}/painel/{id_painel}/modulo/{id_modulo}/sensor/search', [SensorController::class, 'search']);

Route::get('usuario/{id_usuario}/painel/{id_painel}/modulo/{id_modulo}', [SensorController::class, 'read']);

