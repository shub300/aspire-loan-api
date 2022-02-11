<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\User;

class RegisterTest extends TestCase
{
    /**
     *
     * @return void
     */
    /*
        Registration test with new name, email and password
        expected status 200 with json data response
    */
    public function test_it_register_user_with_required_info()
    {

        $user = User::factory(User::class)->make();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'spassword',
        ];


        $response = $this->withoutExceptionHandling()->post('/api/register', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'name',
                'email',
            ]]);
    }

    /*
        Register process validation test without name, email and password
        expected status 400 with json error information
    */
    public function test_it_checks_name_email_password_validation()
    {
        $this->json('POST', '/api/register')
            ->assertStatus(400)
            ->assertJson([
                "error" => true,
                'data' =>
                [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ]
            ]);
    }
}
