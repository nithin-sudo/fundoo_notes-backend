<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Auth;
use Exception;
use Validator;


class LabelController extends Controller
{
    public function createLabel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => 'required',
            'labelname' => 'required|string|between:2,20',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try
        {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $label = new Label;
            $label->note_id = $request->get('note_id');
            $label->labelname = $request->get('labelname');

            if($currentUser->labels()->save($label))
            {
                Log::info('Label created',['user_id'=>$currentUser,'note_id'=>$request->note_id]);
                return response()->json([
                    'message' => 'Label created Sucessfully'
                ], 201);
            } 
        }
        catch (Exception $e) 
		{
            Log::error('Invalid User');
            return response()->json([ 
                'message' => 'Invalid authorization token'
            ], 404);
        }
    }

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
