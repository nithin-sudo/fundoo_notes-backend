<?php

namespace App\Http\Controllers;
use App\Models\Note;
use App\Models\User;
use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            //Log::error('Invalid User');
            Log::channel('mydailylogs')->critical('This token is invalid');
            return response()->json([
                'status' => 404, 
                'message' => 'Invalid authorization token'
            ], 404);
        }

        Log::info('notes created',['user_id'=>$note->user_id]);
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
                Log::error('Notes Not Found',['id'=>$request->id]);
                return response()->json([ 'message' => 'Notes not Found'], 404);
            }
    
            $note->fill($request->all());
    
            if($note->save())
            {
                Log::info('notes updated',['user_id'=>$currentUser,'note_id'=>$request->id]);
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
                Log::error('Notes Not Found',['id'=>$request->id]);
                return response()->json(['message' => 'Notes not Found'], 404);
            }
    
            if($note->delete())
            {
                Log::info('notes deleted',['user_id'=>$currentUser,'note_id'=>$request->id]);
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


            $user = User::join('notes', 'notes.user_id', '=', 'users.id')
                ->join('labels', 'labels.note_id', '=','notes.id')
                //->where('users.id', '=', $notes->user_id)
                ->get(['users.id', 'notes.id', 'labels.note_id']);

            // $user = Note::select("id","title","description")
            //                     ->where([
            //                         ['user_id', '=', $notes->user_id](
            //                             Label::select('id','labelname')
            //                             ->where([
            //                                 ['note_id', '=', $notes->user_id]
            //                             ]))
            //                     ])       
            //                 ->join("labels","labels.id", "=", "labels.id")
            //                 ->get();
            // $user = DB::table('users')
            //             ->join('notes','users.id', '=', 'notes.user_id')
            //             ->join('labels','notes.id', '=', 'labels.note_id')
            //             ->select('notes.id', 'notes.title', 'notes.description', 'labels.id', 'labels.labelname')
            //             ->get();


            /*
            $user = Note::select('id', 'title', 'description')
                ->where([
                    ['user_id', '=', $notes->user_id],
                ])
                ->get();
            $label = Label::select('id', 'labelname') 
                ->where([
                    ['user_id', '=', $notes->user_id],
                ])
                ->get();  
                */  
            if ($user=='[]'){
                return response()->json([
                    'message' => 'Notes not found'
                ], 404);
            }
            return response()->json([
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
