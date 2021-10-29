<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    //create notes success
    public function test_IfGiven_TitleAndDescription_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
        ])->json('POST', '/api/auth/createnote', 
        [
            "title" => "title ",
            "description" => "description ",
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'notes created successfully']);
    }

    //create notes error status
    public function test_IfGiven_WrongAccessToken_ShouldReturnInvalidStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzM2OTU5NCwiZXhwIjoxNjMzMzczMTk0LCJuYmYiOjE2MzMzNjk1OTQsImp0aSI6InQ0MHo5djlXNFNGOUZRblEiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.t09kohx5hV15u8aAH-WXAsdnzmh9HsAqCOLci9QKyQ'
        ])->json('POST', '/api/auth/createnote', 
        [
            "title" => "title test one",
            "description" => "description test one",
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Invalid authorization token']);
    }


    //update notes success status
    public function test_IfGiven_Id_TitleAndDescription_ShouldValidate_AndReturnSuccess_UpdateStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
        ])->json('PUT', '/api/auth/updatenote', 
        [
            "id" => "16",
            "title" => "title update",
            "description" => "description update",
        ]);
        $response->assertStatus(201)->assertJson(['message' => 'Note updated Sucessfully']);
    }

     //update notes Error status
     public function test_IfGiven_WrongId_TitleAndDescription_ShouldValidate_AndReturnNotes_NotFoundStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
         ])->json('PUT', '/api/auth/updatenote', 
         [
             "id" => "20",
             "title" => "titleupdate",
             "description" => "description test one update",
         ]);
         $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
     }

     //delete success status
     public function test_IfGiven_Id_ShouldValidate_AndReturn_Delete_SuccessStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
         ])->json('DELETE', '/api/auth/deletenote', 
         [
             "id" => "16",
         ]);
         $response->assertStatus(201)->assertJson(['message' => 'Note deleted Sucessfully']);
     }

     /*
     //delete error status
     public function test_IfGiven_WrongId_ShouldValidate_AndReturnNotes_NotFoundStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
         ])->json('POST', '/api/auth/deletenote', 
         [
             "id" => "20",
         ]);
         $response->assertStatus(404)->assertJson(['message' => 'Notes not Found']);
     }*/


     //get all notes success
     public function test_IfGiven_AuthorisedToken_AndReturnAllNotes_SuccessStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzNTQ4MzM4NiwiZXhwIjoxNjM1NDg2OTg2LCJuYmYiOjE2MzU0ODMzODYsImp0aSI6IlJ6VUpsWWdtQ2VUdmFYUUUiLCJzdWIiOjEwLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.CjJ80kSAVmbT8rPHBfkxmgH94PmfEdMnSU63KsnrEb4'
         ])->json('GET', '/api/auth/getallnotes');

         $response->assertStatus(201)->assertJson(['message' => 'Fetched Notes Successfully']);
     }

    /*
     //get all notes Error
     public function test_IfGiven_WrongAuthorisedToken_AndReturnInvalid_ErrorStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz'
         ])->json('GET', '/api/auth/getallnotes');

         $response->assertStatus(403)->assertJson(['message' => 'Invalid token']);
     }*/

}
