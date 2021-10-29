<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Label;
use Exception;
use Validator;
use JWTAuth;
use Auth;


/**
 * @since 06-oct-2021
 * 
 * This controller is responsible for performing CRUD operations 
 * on Labels.
 */
class LabelController extends Controller
{
    /**
     * This function takes the User access token and labelname
     * creates a label for that respective user.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function createLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'labelname' => 'required|string|between:2,15',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $currentUser = JWTAuth::parseToken()->authenticate();
        if ($currentUser)
        {
            $labelName = Label::where('labelname', $request->labelname)->first();
            if ($labelName)
            {
                Log::alert('Label Created : ',['email'=>$request->email]);
                return response()->json(['message' => 'Label Name already exists'],401);
            }
        
            $label = new Label;
            $label->labelname = $request->get('labelname');
            
            if($currentUser->labels()->save($label))
            {
                return response()->json(['message' => 'Label added Sucessfully'], 201);
            }
            return response()->json(['message' => 'Could not add label'], 405);
        }
        return response()->json(['message' => 'Invalid authorization token'], 404);
    }

    /**
     * This function takes the User access token and note id and 
     * creates a label for that respective note is and user.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLabelByNoteId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'note_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $currentUser = JWTAuth::parseToken()->authenticate();
        
        if($currentUser)
        {
            $id = $request->input('id');
            $note_id = $request->input('note_id');
            
            $label = $currentUser->labels()->find($id);
    
            if(!$label)
            {
                return response()->json([ 'message' => 'Label not Found'], 404);
            }

            $note = $currentUser->notes()->find($note_id);
    
            if(!$note)
            {
                return response()->json([ 'message' => 'Notes not Found'], 404);
            }

            $label->note_id = $request->get('note_id');
            
            if($currentUser->labels()->save($label))
            {
                return response()->json([ 'message' => 'Label Added to Note Sucessfully' ], 201);
            }
            return response()->json([ 'message' => 'Label Did Not added to Note' ], 403);

        }
        return response()->json([ 'message' => 'Invalid authorization token'], 404);
    }

    /**
     * This function takes the User access token and label id and 
     * displays that respective label id.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function displayLabelById(Request $request)
    {
        try
        {
            $id = $request->input('id');
            $User = JWTAuth::parseToken()->authenticate();
            $labels = $User->labels()->find($id);
            if($labels == '')
            {
                return response()->json([ 'message' => 'Label not found'], 404);
            }
        }
        catch(Exception $e)
        {
            return response()->json(['message' => 'Invalid authorization token' ], 404);
        }
        return $labels;
    }

    /**
     * This function takes the User access token and label id and 
     * updates the label for the respective id.
     * 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLabelById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'labelname' => 'required|string|between:2,20',
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }
        try
        {
            $id = $request->input('id');
            $currentUser = JWTAuth::parseToken()->authenticate();
            $label = $currentUser->labels()->find($id);
    
            if(!$label)
            {
                Log::error('Label Not Found',['label_id'=>$request->id]);
                return response()->json([ 'message' => 'Label not Found'], 404);
            }
    
            $label->fill($request->all());
    
            if($label->save())
            {
                Log::info('Label updated',['user_id'=>$currentUser,'label_id'=>$request->id]);
                return response()->json(['message' => 'Label updated Sucessfully' ], 201);
            }      
        }
        catch(Exception $e)
        {
            return response()->json(['message' => 'Invalid authorization token' ], 404);
        }
        return $label;
    }


    /**
     * This function takes the User access token and label id and 
     * and deleted that particular label id.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLabelById(Request $request)
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
            $label = $currentUser->labels()->find($id);
    
            if(!$label)
            {
                Log::error('Label Not Found',['label_id'=>$request->id]);
                return response()->json(['message' => 'Label not Found'], 404);
            }
    
            if($label->delete())
            {
                Log::info('Label deleted',['user_id'=>$currentUser,'label_id'=>$request->id]);
                return response()->json(['message' => 'Label deleted Sucessfully'], 201);
            }   
        }
        catch(Exception $e)
        {
            return response()->json(['message' => 'Invalid authorization token' ], 404);
        }
    }


    /**
     * This function takes the User access token and returns
     * all the labels present for particular User.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllLabels()
    {
        $labels = new Label();
        $labels->user_id = auth()->id();

        if ($labels->user_id == auth()->id()) 
        {
            $user = Label::select('id', 'labelname')
                ->where([
                    ['user_id', '=', $labels->user_id],
                ])
                ->get();
            if ($user=='[]'){
                Log::error('Labels Not Found',['user_id'=>$labels->user_id]);
                return response()->json([
                    'message' => 'Labels not found'
                ], 404);
            }
           
            return response()->json([
                'message' => 'Labels Fetched  Successfully',
                'Labels' => $user
            ], 201);
            
        }
        return response()->json([
            'status' => 403, 
            'message' => 'Invalid token'
        ]);
    }

}
