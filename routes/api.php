<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\LabelController;

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
    Log::channel('custom');
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('refresh', [UserController::class, 'refresh']);
    Route::post('forgotpassword', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('resetpassword', [ForgotPasswordController::class, 'resetPassword']);

    Route::post('createnote', [NoteController::class, 'createNote']);
    Route::get('displaynote', [NoteController::class, 'displayNoteById']);
    Route::post('deletenote', [NoteController::class, 'deleteNoteById']);
    Route::put('updatenote', [NoteController::class, 'updateNoteById']);
    Route::get('getallnotes', [NoteController::class, 'getAllNotes']);
    
    Route::post('createlable', [LabelController::class, 'createLabel']);
    Route::get('displaylable', [LabelController::class, 'displayLabelById']);
    Route::put('updatelable', [LabelController::class, 'updateLabelById']);
    Route::post('deletelable', [LabelController::class, 'deleteLabelById']);
    Route::get('displayall', [LabelController::class, 'getAllLabels']);
});   