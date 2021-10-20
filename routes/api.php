<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\CollabaratorController;

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
    Route::delete('deletenote', [NoteController::class, 'deleteNoteById']);
    Route::put('updatenote', [NoteController::class, 'updateNoteById']);
    Route::get('getallnotes', [NoteController::class, 'getAllNotes']);

    Route::post('pinnotes', [NoteController::class, 'pinNoteById']);
    Route::get('getallpinnotes', [NoteController::class, 'getAllPinnedNotes']);

    Route::post('archivenotes', [NoteController::class, 'archiveNoteById']);
    Route::post('archivenotes', [NoteController::class, 'archiveNoteById']);
    Route::get('getallarchivednote', [NoteController::class, 'getAllArchivedNotes']);

    Route::post('colour', [NoteController::class, 'colourNoteById']);
    Route::get('getcolourednotes', [NoteController::class, 'getColouredNotes']);

    Route::post('createlable', [LabelController::class, 'createLabel']);
    Route::post('addlabelbynoteid', [LabelController::class, 'addLabelByNoteId']);
    Route::get('displaylable', [LabelController::class, 'displayLabelById']);
    Route::put('updatelable', [LabelController::class, 'updateLabelById']);
    Route::delete('deletelable', [LabelController::class, 'deleteLabelById']);
    Route::get('displayall', [LabelController::class, 'getAllLabels']);

    Route::post('addcollab', [CollabaratorController::class, 'addCollabatorByNoteId']);
    Route::put('editcollabnote', [CollabaratorController::class, 'updateNoteByCollabarator']);
    Route::delete('deletecollab', [CollabaratorController::class, 'deleteCollabarator']);
    Route::get('allcollab', [CollabaratorController::class, 'getAllCollabarators']);
    Route::get('paginatenote', [NoteController::class, 'getpaginateNoteData']);
    Route::get('searchnotes', [NoteController::class, 'searchAllNotes']);
});   