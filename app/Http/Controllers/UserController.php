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
 * @since 
 * 
 * This is the main controller that is responsible for user registration,login,user-profile 
 * refresh and logout API's.
 */
class UserController extends Controller
{
    
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|between:2,20',
            'lastname' => 'required|string|between:2,20',
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::where('email', $request->email)->first();
        
        if ($user)
        {
            return response()->json(['message' => 'The email has already been taken'],401);
        
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

     /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) 
        {
            return response()->json($validator->errors(), 422);
        }

         $user = User::where('email', $request->email)->first();

         if(!$user)
         {
             return response()->json([
                 'message' => 'we can not find the user with that e-mail address You need to register first'
             ], 401);
         }

         if (!$token = auth()->attempt($validator->validated()))
         {
             return response()->json(['error' => 'Unauthorized'], 401);
         }

        return response()->json([ 
            'message' => 'Login successfull',  
            'access_token' => $token
        ],200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() 
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() 
    {
        auth()->logout();

        return response()->json([
            'message' => 'User successfully signed out'
        ],201);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
        ]);
    }
}
