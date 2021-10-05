<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelApiTest extends TestCase
{
    //create label success
    public function test_IfGiven_Note_idAnd_LabelName_ShouldValidate_AndReturnSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz4'
        ])->json('POST', '/api/auth/createlable', 
        [
            "note_id" => "2",
            "labelname" => "Label 21",
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Label created Sucessfully']);
    }

    //create label Error
    public function test_IfGiven_Note_idAnd_LabelNameAndWrongToken_ShouldValidate_AndReturnErrorsStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz'
        ])->json('POST', '/api/auth/createlable', 
        [
            "note_id" => "2",
            "labelname" => "Label 21",
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Invalid authorization token']);
    }

    //label update success
    public function test_IfGiven_Label_idAnd_LabelNameAndToken_ShouldValidate_AndReturnUpdateSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz4'
        ])->json('PUT', '/api/auth/updatelable', 
        [
            "id" => "2",
            "labelname" => "Label update",
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Label updated Sucessfully']);
    }

    //label update error
    public function test_IfGiven_WrongLabel_idAnd_LabelNameAndToken_ShouldValidate_AndReturnUpdateErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz4'
        ])->json('PUT', '/api/auth/updatelable', 
        [
            "id" => "20",
            "labelname" => "Label update",
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Label not Found']);
    }

    //label delete success
    public function test_IfGiven_Label_idAnd_ShouldValidate_AndReturnDeleteSuccessStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz4'
        ])->json('POST', '/api/auth/deletelable', 
        [
            "id" => "6",
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Label deleted Sucessfully']);
    }

    //delete error
    public function test_IfGiven_WrongLabel_idAnd_ShouldValidate_AndReturnDeleteErrorStatus()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz4'
        ])->json('POST', '/api/auth/deletelable', 
        [
            "id" => "20",
        ]);

        $response->assertStatus(404)->assertJson(['message' => 'Label not Found']);
    }

    //get all labels success
    public function test_IfGiven_AuthorisedToken_AndReturnAllLabels_SuccessStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz4'
         ])->json('GET', '/api/auth/displayall');

         $response->assertStatus(201)->assertJson(['message' => 'Labels Fetched  Successfully']);
     }

     //get all labels error
     public function test_IfGiven_WrongAuthorisedToken_AndReturnAllLabels_SuccessStatus()
     {
         $response = $this->withHeaders([
             'Content-Type' => 'Application/json',
             'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzMzQxMTQxMCwiZXhwIjoxNjMzNDE1MDEwLCJuYmYiOjE2MzM0MTE0MTAsImp0aSI6Imd5WThtclFFWG50N2JDUTgiLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.b4CReGFIWLimgv0auC4sRTEHn8S6cZ8t9L6H_rNqwz'
         ])->json('GET', '/api/auth/displayall');

         $response->assertStatus(404)->assertJson(['message' => 'Labels not found']);
     }
}
