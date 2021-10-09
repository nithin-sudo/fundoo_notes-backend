<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\User;
use App\Models\Label;
use Exception;
use Validator;
use JWTAuth;
use Auth;


/**
 * @since 06-oct-2021
 * 
 * This controller is responsible for performing CRUD operations 
 * on notes.
 */
class NoteController extends Controller
{

    /**
     * This function takes User access token and checks if it is
     * authorised or not if so and it procees for the note creation 
     * and created it successfully.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
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

            $value = Cache::remember('notes', 1, function () {
                return DB::table('notes')->get();
            });
        } 
		catch (Exception $e) 
		{
            Log::error('Invalid User');
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

    /**
     * This function takes JWT access token and note id and finds 
     * if there is any note existing on that User id and note id if so
     * it successfully returns that note id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function displayNoteById(Request $request)
    {
        try
        {
            $id = $request->input('id');
            $User = JWTAuth::parseToken()->authenticate();

            $value = Cache::remember('notes', 0.5, function () {
                return DB::table('notes')->get();
            });
            
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

    /**
     * This function takes the USer access token and note id which 
     * user wants to update and finds the note id if it is existed
     * or not if so, updates it successfully.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * This function takes the USer access token and note id which 
     * user wants to delete and finds the note id if it is existed
     * or not if so, deletes it successfully.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
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


    /**
     * This function takes the User access token and checks if it 
     * authorised or not if so, it returns the notes and associated
     * labels.  
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllNotes()
    {
        $notes = new Note();
        $notes->user_id = auth()->id();

        if ($notes->user_id == auth()->id()) 
        {
            // $user = Note::join('labels', 'notes.id', '=', 'labels.note_id')
            //                 ->get(['notes.*','labels.labelname']);
            
            // $user = User::join('notes', 'notes.user_id', '=', 'users.id')
            //   ->join('labels', 'labels.note_id', '=', 'notes.id')
            //   ->get(['users.*','notes.*', 'notes.description','labels.labelname']);

            // $user = Note::leftJoin('labels', 'labels.note_id', '=', 'notes.id')
            // ->select('notes.*')
            // ->get();

            
            // $user = User::join('notes', 'notes.user_id', '=', 'users.id')
            //     ->join('labels', 'labels.note_id', '=','notes.id')
            //     //->where('users.id', '=', $notes->user_id)
            //     ->get(['users.id', 'notes.id', 'labels.note_id']);
                
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

    public function pinNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $id = $request->input('id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $currentUser->notes()->find($id);

        if(!$note)
        {
            Log::error('Notes Not Found',['user'=>$currentUser,'id'=>$request->id]);
            return response()->json(['message' => 'Notes not Found'], 404);
        }

        if($note->pin == 0)
        {
            $user = Note::where('id', $request->id)
                            ->update(['pin' => 1]);

            Log::info('notes Pinned',['user_id'=>$currentUser,'note_id'=>$request->id]);
            return response()->json(['message' => 'Note Pinned Sucessfully' ], 201);
        }

        $user = Note::where('id', $request->id)
                         ->update(['pin' => 0]);

        Log::info('notes UnPinned',['user_id'=>$currentUser,'note_id'=>$request->id]);
        return response()->json(['message' => 'Note UnPinned ' ], 201);    
    }

    public function getAllPinnedNotes()
    {
        $notes = new Note();
        $notes->user_id = auth()->id();

        if ($notes->user_id == auth()->id()) 
        {
            $usernotes = Note::select('id', 'title', 'description')
                ->where([
                    ['user_id', '=', $notes->user_id],['pin','=', 1]
                ])
                ->get();
            
            if ($usernotes=='[]'){
                return response()->json([
                    'message' => 'Notes not found'
                ], 404);
            }
            return
            response()->json([
                'message' => 'Fetched Pinned Notes Successfully',
                'notes' => $usernotes
            ], 201);
        }
        return response()->json([
            'status' => 403, 
            'message' => 'Invalid token'
        ],403);
    }


    public function archiveNoteById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $id = $request->input('id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $currentUser->notes()->find($id);

        if(!$note)
        {
            Log::error('Notes Not Found',['user'=>$currentUser,'id'=>$request->id]);
            return response()->json(['message' => 'Notes not Found'], 404);
        }

        if($note->Archive == 0)
        {
            $user = Note::where('id', $request->id)
                            ->update(['archive' => 1]);

            Log::info('notes Archived',['user_id'=>$currentUser,'note_id'=>$request->id]);
            return response()->json(['message' => 'Note Archived Sucessfully' ], 201);
        }

        $user = Note::where('id', $request->id)
                         ->update(['archive' => 0]);

        Log::info('notes UnArchived',['user_id'=>$currentUser,'note_id'=>$request->id]);
        return response()->json(['message' => 'Note UnArchived ' ], 201);    
    }

    public function getAllArchivedNotes()
    {
        $notes = new Note();
        $notes->user_id = auth()->id();

        if ($notes->user_id == auth()->id()) 
        {
            $usernotes = Note::select('id', 'title', 'description')
                ->where([
                    ['user_id', '=', $notes->user_id],['Archive','=', 1]
                ])
                ->get();
            
            if ($usernotes=='[]'){
                return response()->json([
                    'message' => 'Notes not found'
                ], 404);
            }
            return
            response()->json([
                'message' => 'Fetched Archived Notes',
                'notes' => $usernotes
            ], 201);
        }
        return response()->json([
            'status' => 403, 
            'message' => 'Invalid token'
        ],403);
    }




}
