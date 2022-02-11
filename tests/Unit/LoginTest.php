<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\User;

class LoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /*
        Login validation test without email and password
        expected status 400 with error and data information
    */
    public function test_it_performs_login_validation()
    {
        $response = $this->json('POST', '/api/login');
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'true',
            'data' => [
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.']
            ]
        ]);
    }

    /*
        It performs validation on wrong credential information
        expected status 400 with message Invalid Login Details.
    */
    public function test_it_validates_wrong_login_information()
    {
        $user_data = ['email' => 'xyz@example.com', 'password' => '12341'];
        $response = $this->json('POST', '/api/login', $user_data);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'true', 'message' => 'Invalid Login Details.']);
    }

    /*
        It perform login attempt with valid user credentials
        expected status 200 with json information
    */
    public function test_it_with_genuine_login_information()
    {
        $user = User::factory(User::class)->make();

        $user_data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'spassword',
        ];

        $login_payload = [
            'email' => $user->email,
            'password' => 'spassword'
        ];

        $this->withoutExceptionHandling()->post('/api/register', $user_data);

        $this->post('/api/login', $login_payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    'access_token',
                    'token_type',
                    'expires_in',
                ]
            ]);
    }
}
