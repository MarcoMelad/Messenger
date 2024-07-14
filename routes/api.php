<?php

use App\Http\Controllers\ConversationsController;
use App\Http\Controllers\MessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations', [ConversationsController::class,'index']);
    Route::get('/conversation/{conversation}', [ConversationsController::class,'show']);
    Route::post('/conversation/{conversation}/add-participant', [ConversationsController::class,'addParticipant']);
    Route::delete('/conversation/{conversation}/delete-participant', [ConversationsController::class,'removeParticipant']);

    Route::get('/conversations/{id}/messages', [MessagesController::class,'index']);
    Route::post('/messages', [MessagesController::class,'store']);
    Route::get('/messages/{id}', [ConversationsController::class,'destroy']);
//});
