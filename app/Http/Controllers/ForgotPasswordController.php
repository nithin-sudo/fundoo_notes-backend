<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Requests\SendEmailRequest;
use Exception;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;


 /**
 * @since 24-sep-2021
 * This is the forgot passwors controller from this we are going to 
 * send reset email link to user specified email.
 */
class ForgotPasswordController extends Controller
{

    /**
     * This API Takes the request which is the email id and validates it and check where that email id 
     * is present in DB or not if it is not,it returns failure with the appropriate response code and 
     * checks for password reset model once the email is valid and by creating an object of the 
     * sendEmail function which is there in App\Http\Requests\SendEmailRequest and calling the function
     * by passing args and successfully sending the password reset link to the specified email id.
     * 
     * @return success reponse about reset link.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            return response()->json(['status' => 401, 'message' => "we can't find a user with that email address."]);
        }
        
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],

            [
                'email' => $user->email,
                'token' => JWTAuth::fromUser($user)
            ]
        );
        
        if ($user && $passwordReset) 
        {
            $sendEmail = new SendEmailRequest();
            $sendEmail->sendEmail($user->email,$passwordReset->token);
        }

        return response()->json(['status' => 200, 'message' => 'we have emailed your password reset link to respective mail']);

    }

    /**
     * This API Takes the request which has new password and confirm password and validates both of them
     * if validation fails returns failure resonse and if it passes it checks with DB whether the token 
     * is there or not if not returns a failure response and checks the user email also if everything is 
     * good resets the password successfully.
     * 
     * 
     */
    public function resetPassword(Request $request)
    {
        $validate = FacadesValidator::make($request->all(), [
            'new_password' => 'min:6|required|',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validate->fails())
        {

            return response()->json(
                ['status' => 201, 
                 'message' => "Password doesn't match"
                ]);
        }
        
        $passwordReset = PasswordReset::where('token', $request->token)->first();


        if (!$passwordReset) 
        {
            return response()->json(['status' => 401, 'message' => 'This token is invalid']);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user)
        {
            return response()->json([
                'status' => 201, 
                'message' => "we can't find the user with that e-mail address"
            ], 201);
        }
        else
        {
            $user->password = bcrypt($request->new_password);
            $user->save();
            $passwordReset->delete();
            return response()->json([
                'status' => 201, 
                'message' => 'Password reset successfull!'
            ]);
        }
    }
}
