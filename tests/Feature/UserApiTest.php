<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Response;
use App\Models\User;
use GuzzleHttp\Promise\Create;

class UserApiTest extends TestCase
{

    use WithFaker;

    public function test_can_login()
    {
        $user = User::factory()->create();
        $payload = [
            "email" => $user->email,
            "password" => 'password',
        ];

        $response = $this->json('post','api/login', $payload,['Accept' => 'application/json'])
            ->assertStatus(Response::HTTP_OK);
        
        $user->delete();

    }

    public function test_can_request_users()
    {
        $user = User::inRandomOrder()->first();
        $this->actingAs($user, 'api');

        $payload = [];

        $response = $this->json('get', 'api/user', $payload,['Accept' => 'application/json'])
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_can_create_user()
    {
        $user = User::inRandomOrder()->first();
        $this->actingAs($user, 'api');

        // first remove the user in db if any
        User::where('email','justme@gmail.com')->delete();

        $payload = [
            "first_name" => "Justin",
            "last_name" => "Me",
            "email" => "justme@gmail.com",
            "phone" => "123456789",
            "password" => 'password'
        ];

        $response = $this->json('POST', 'api/user', $payload, ['Accept' => 'application/json'])
            ->assertStatus(Response::HTTP_CREATED);
        
        unset($payload["password"]);    
        $tmp = array_intersect($payload,$response["user"]);
        $this->assertSame($payload,$tmp, "User is not created correctly");
        
        // remove the created user
        User::where('email','justme@gmail.com')->delete();
    }

    public function test_can_delete_user()
    {
        $tmp = User::factory()->create();
        $user = User::where('is_admin',1)->first();
        $this->actingAs($user, 'api');
        $payload = [];
        $response = $this->json('DELETE', 'api/user/'.$tmp->id, $payload, ['Accept' => 'application/json'])
            ->assertStatus(Response::HTTP_ACCEPTED);
        // should check if the record is actually removed from db ?       
    }

    public function test_can_update_user()
    {
        $user = User::inRandomOrder()->first();
        $this->actingAs($user, 'api');
        $tmp = User::factory()->create();
        $payload = [
            "first_name" => "Justin",
            "last_name" => "Me",
            "email" => rand(1,999)."justme@gmail.com",
            "phone" => "123456789",
        ];

        $response = $this->json('PUT', 'api/user/'.$tmp->id, $payload, ['Accept' => 'application/json'])
            ->assertStatus(Response::HTTP_ACCEPTED);
        
        
        $this->assertSame($payload,array_intersect($payload,$response["user"]), "User is not updated correctly");
        // remove the created user
        User::where('id',$tmp->id)->delete();    
    }
}