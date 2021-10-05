<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    //user registration success test
    public function test_IfGiven_UserCredentials_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/register', [
            "firstname" => "Nithin",
            "lastname" => "Krishna",
            "email" => "nithin0krishna@gmail.com",
            "password" => "nithin@123",
            "password_confirmation" => "nithin@123"
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'User successfully registered']);
    }

    //user registration Error test
    public function test_IfGiven_UserCredentialsSame_ShouldValidate_AndReturnErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/register', 
        [
            "firstname" => "Nithin",
            "lastname" => "krishna",
            "email" => "nithin0krishna@gmail.com",
            "password" => "nithin@123",
            "password_confirmation" => "nithin@123"
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'The email has already been taken']);

    }

    //login success test
    public function test_IfGiven_LoginCredentials_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/login', 
        [
            "email" => "nithin0krishna@gmail.com",
            "password" => "nithin@123",
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Login successfull']);
    }

    //login error status
    public function test_IfGiven_NotRegistered_LoginCredentials_ShouldValidate_AndReturnErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/login', 
        [
            "email" => "simba@gmail.com",
            "password" => "simba@123",
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'we can not find the user with that e-mail address You need to register first']);
    }

    //logout success status
    public function test_IfGiven_AccessToken_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQwNTIwMywiZXhwIjoxNjMzNDA4ODAzLCJuYmYiOjE2MzM0MDUyMDMsImp0aSI6IndJVkhITk5GTW5vVmhJT2giLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.7jYn6UvYo0X5xkTEMN3vKf-Jeg21UDan2DfntCAU-Qg'
        ])->json('POST', '/api/auth/logout');
        $response->assertStatus(201)->assertJson(['message'=> 'User successfully signed out']);
    }   

    //logout failure status





    //forgot passsword success
    //not working
    public function test_IfGiven_Registered_EmailId_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/forgotpasssword', 
        [
            "email" => "nithin0krishna@gmail.com",
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'we have emailed your password reset link to respective mail']);
    }

    //forgot password failure
    public function test_IfGiven_WrongEmailId_ShouldValidate_AndErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/forgotpasssword', 
        [
            "email" => "nkrishna@gmail.com",
        ]);
        $response->assertStatus(404)->assertJson(['message' => 'we can not find a user with that email address']);
    }    

    //reset password success
    public function test_IfGiven_NewAndConfirmPassword_ShouldValidate_AndSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/resetpasssword', 
        [
            "new_pasword" => "nithin123",
            "confirm_pasword" => "nithin123",
            "token" => " "
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'Password reset successfull!']);
    }   

    //reset password failure
    public function test_IfGiven_NewAndConfirmPasswordAndWrongToken_ShouldValidate_AndReturnErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/resetpasssword', 
        [
            "new_pasword" => "nithin123",
            "confirm_pasword" => "nithin123",
            "token" => " "
        ]);
        $response->assertStatus(401)->assertJson(['message' => 'This token is invalid']);
    }   




    //reset password failure


}
