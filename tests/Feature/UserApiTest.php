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
}
