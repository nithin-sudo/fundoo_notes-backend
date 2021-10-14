<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\SendEmailRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Collabarator;
use App\Models\Note;
use App\Models\User;

/**
 * @since 14-oct-2021
 * 
 * This controller is responsible for performing CRUD operations 
 * on collabarators.
 */
class CollabaratorController extends Controller
{

    /**
     * This function takes User access token and checks if it is
     * authorised or not if so and takes note_id, email if those 
     * parameters are valid it will successfully creates a 
     * collabarator.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCollabatorByNoteId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'email' => 'required|string|email|max:100',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $currentUser = JWTAuth::parseToken()->authenticate();
        $note = $currentUser->notes()->find($request->input('note_id'));
        $user = User::where('email', $request->email)->first();

        if($currentUser)
        {
            if($note)
            {
                if($user)
                {

                    $collabUser = Collabarator::select('id')->where([
                        ['note_id','=',$request->input('note_id')],
                        ['email','=',$request->input('email')]
                    ])->get();
                        
                    if($collabUser != '[]')
                    {
                        return response()->json(['message' => 'Collabarater Already Created' ], 404); 
                    }

                    $collab = new Collabarator;
                    $collab->note_id = $request->get('note_id');
                    $collab->email = $request->get('email');
                    $collabarator = Note::select('id','title','description')->where([['id','=',$request->note_id]])->get();
                    if($currentUser->collabarators()->save($collab))
                    {
                        $sendEmail = new SendEmailRequest();
                        $sendEmail->sendEmailToCollab($request->email,$collabarator,$currentUser->email);
                        return response()->json(['message' => 'Collabarator created Sucessfully'], 201);
                    }
                    return response()->json(['message' => 'Could not add collab'], 404);            
                }
               return response()->json(['message' => 'User Not Registered'], 404);
            }
           return response()->json([ 'message' => 'Notes not found'], 404); 
        }
        return response()->json([ 'message' => 'Invalid authorization token'], 404);
    }


    /**
     * This function takes User access token of collabarator and
     * checks if it is authorised or not if so and takes note details
     * as parametres if those are valid updates the notes successfully. 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNoteByCollabarator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'title' => 'string|between:2,30',
            'description' => 'string|between:3,1000',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        $id = $request->input('note_id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        if($currentUser)
        {
            $collabUser = Collabarator::where('email', $currentUser->email)->first();
            if($collabUser)
            {
                $id = $request->input('note_id');
                $email = $currentUser->email;

                $collab = Collabarator::select('id')->where([
                    ['note_id','=',$id],
                    ['email','=',$email]
                ])->get();
            
                if($collab == '[]')
                {
                    return response()->json(['message' => 'note_id is not correct'], 404); 
                }
                
                $user = Note::where('id', $request->note_id)
                            ->update(['title' => $request->title,'description'=>$request->description]);

                if($user)
                {
                    return response()->json(['message' => 'Note updated Sucessfully' ], 201);
                }
                return response()->json(['message' => 'Note could not updated' ], 201);      
            }
            return response()->json(['message' => 'Collabarator Email not registered' ], 404);
        }
        return response()->json(['message' => 'Invalid authorization token' ], 404);
    }

    /**
     * This function takes User access token and checks if it is 
     * authorised or not if so and takes note_id and collabarator email
     * as parametres if those are valid deletes the notes successfully. 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCollabarator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'email' => 'required|string|email|max:100',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        $id = $request->input('note_id');
        $currentUser = JWTAuth::parseToken()->authenticate();
        if($currentUser)
        {
            $id = $request->input('note_id');
            $email =  $request->input('email');

            $collabarator = Collabarator::select('id')->where([
                                    ['note_id','=',$id],
                                    ['email','=',$email]
                                    ])->get();
                    
            if($collabarator == '[]')
            {
                return response()->json(['message' => 'Collabarater Not created' ], 404); 
            }

            $collabDelete = DB::table('collabarators')->where('note_id', '=', $id)->where('email', '=', $email)->delete();
            if($collabDelete)
            {
                return response()->json(['message' => 'Collabarator deleted Sucessfully' ], 201);
            }
            return response()->json(['message' => 'Collabarator could not deleted' ], 201);      
        }
    }

    /**
     * This function takes User access token and checks if it is
     *  authorised or not if so it returns all the collabarators
     *  he has created.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCollabarators()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();

        if ($currentUser) 
        {
            $collabarator = Collabarator::select('note_id', 'email') ->where([['user_id', '=', $currentUser->id],])->get();

            if ($collabarator=='[]')
            {
                return response()->json(['message' => 'Collabarators not found'], 404);
            }
            return response()->json([
                'message' => 'Fetched Collabarators Successfully',
                'Collaborator' => $collabarator
            ], 201);       
        }
        return response()->json(['message' => 'Invalid authorization token'],403);
    }
}