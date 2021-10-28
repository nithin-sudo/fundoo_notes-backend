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
            "confirm_password" => "nithin@123"
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
            "email" => "balupinisetty@gmail.com",
            "password" => "jennifer@123C",
            "confirm_password" => "jennifer@123C"
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
        $this->withoutExceptionHandling();
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNDYzNjIyMCwiZXhwIjoxNjM0NjM5ODIwLCJuYmYiOjE2MzQ2MzYyMjAsImp0aSI6IlNjeWFhekF0b1prVldZMXUiLCJzdWIiOjcsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.FAd0DyV1sM3shANnfXsqaA2qHPX0JWqd5LKoYH_Vj5k;'
        ])->json('POST', '/api/auth/logout');
        $response->assertStatus(201)->assertJson(['message'=> 'User successfully signed out']);
    }   

    //logout error test
    public function test_IfGiven_WrongAccessToken_ShouldValidate_AndReturnErrorStatus()
    {
        $this->withoutExceptionHandling();
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNDYzNjIyMCwiZXhwIjoxNjM0NjM5ODIwLCJuYmYiOjE2MzQ2MzYyMjAsImp0aSI6IlNjeWFhekF0b1prVldZMXUiLCJzdWIiOjcsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.FAd0DyV1sM3shANnfXsqaA2qHPX0JWqd5LKoYH_Vj5'
        ])->json('POST', '/api/auth/logout');
        $response->assertStatus(404)->assertJson(['message'=> 'Invalid authorization token']);
    }  

    //forgot passsword success
    public function test_IfGiven_Registered_EmailId_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/auth/forgotpassword', [
            "email" => "nithin0krishna@gmail.com"
        ]);
        
        $response->assertStatus(200)->assertJson(['message'=> 'we have mailed your password reset link to respective E-mail']);
    }

    //forgot password failure
    public function test_IfGiven_WrongEmailId_ShouldValidate_AndReturnErrorStatus()
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
            "new_password" => "nithin123",
            "confirm_password" => "nithin123",
            "token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2FwaVwvYXV0aFwvZm9yZ290cGFzc3dvcmQiLCJpYXQiOjE2MzQ2MzU4MTMsImV4cCI6MTYzNDYzOTQxMywibmJmIjoxNjM0NjM1ODEzLCJqdGkiOiIxNWpuNVdhUXVnSmlvVk5zIiwic3ViIjo3LCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.1URGEBFXOLDEzwT6SN8AaJiRQEof3A1OkQVOI_Zq93s"
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

}
