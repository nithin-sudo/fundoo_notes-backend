<?php

namespace App\Http\Controllers;
use App\Models\Note;
use Illuminate\Http\Request;
use Auth;
use Exception;
use Validator;
use JWTAuth;

class NoteController extends Controller
{
    public function createNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,50',
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
        ],201);
    }

    public function displayNoteById(Request $request)
    {
        try
        {
            $id = $request->input('id');
            $User = JWTAuth::parseToken()->authenticate();
            $notes = $User->notes()->find($id);
            if($notes == '')
            {
                return response()->json([ 'message' => 'Notes not found'], 404);
            }
        }
        catch(Exception $e)
        {
            return response()->json(['message' => 'Invalid authorization token' ], 404);
        }
        return $notes;
    }

    public function updateNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'title' => 'string|between:2,30',
            'description' => 'string|between:3,1000',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try
        {
            $id = $request->input('id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            $note = $currentUser->notes()->find($id);
    
            if(!$note)
            {
                return response()->json([ 'message' => 'Notes not Found'], 404);
            }
    
            $note->fill($request->all());
    
            if($note->save())
            {
                return response()->json(['message' => 'Note updated Sucessfully' ], 201);
            }      
        }
        catch(Exception $e)
        {
            return response()->json(['message' => 'Invalid authorization token' ], 404);
        }
        return $note;
    }

    public function deleteNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try
        {
            $id = $request->input('id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            $note = $currentUser->notes()->find($id);
    
            if(!$note)
            {
                return response()->json(['message' => 'Notes not Found'], 404);
            }
    
            if($note->delete())
            {
                return response()->json(['message' => 'Note deleted Sucessfully'], 201);
            }   
        }
        catch(Exception $e)
        {
            return response()->json(['message' => 'Invalid authorization token' ], 404);
        }
        
    }



    public function getAllNotes()
    {
        $notes = new Note();
        $notes->user_id = auth()->id();

        if ($notes->user_id == auth()->id()) 
        {
            $user = Note::select('id', 'title', 'description')
                ->where([
                    ['user_id', '=', $notes->user_id],
                    ['notes', '=', '0']
                ])
                ->get();
            if ($user=='[]'){
                return response()->json([
                    'message' => 'Notes not found'
                ], 404);
            }
            return
            response()->json([
                'notes' => $user,
                'message' => 'Fetched Notes Successfully'
            ], 201);
        }
        return response()->json([
            'status' => 403, 
            'message' => 'Invalid token'
        ],403);
    }

}
