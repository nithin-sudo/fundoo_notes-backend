<?php

namespace App\Http\Controllers;
use App\Models\Note;
use Illuminate\Http\Request;
//use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Exception;
use Validator;

class NoteController extends Controller
{
    public function createNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,20',
            'description' => 'required|string|between:3,1000',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try 
		{
            $note = new Note;
            $note->title = $request->input('title');
            $note->description = $request->input('description');
            $note->user_id = Auth::user()->id;
            $note->save();
        } 
		catch (Exception $e) 
		{
            return response()->json([
                'status' => 404, 
                'message' => 'Invalid authorization token'
            ], 404);
        }

        return response()->json([
		'status' => 201, 
		'message' => 'notes created successfully'
        ],400);
    }

    public function displayNoteById(Request $request)
    {
        //return Note::where('id', $request->input('id')->where('user_id', $request->user()->id)->first());
        
        try
        {
            $note = new Note;
            $note->id = $request->input('id');
            $note->user_id = Auth::user()->id;
            $note->user_id->find($request->input('id'));
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Notes not Found!'
            ], 404);
        }

        return $note;
        
    }


}
