<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Register controller create test.
     * @test
     * @dataProvider createDataProvider
     * @return void
     */
    public function create($name, $email, $password, $passwordConfirmation, $expectedStatusHttp, $expectedJson)
    {
        factory(\App\User::class)->create([
            'email' => 'jane@doe.com'
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation
        ]);

        $response
            ->assertStatus($expectedStatusHttp)
            ->assertExactJson($expectedJson);
    }

    public function createDataProvider()
    {
        return [
            [
                'name' => 'Jane Doe',
                'email ' => 'jane@doe.com',
                'password' => 'john1234',
                'passwordConfirmation' => 'john1234',
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'email' => ['The email has already been taken.']
                    ]
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => 'john@doe.com',
                'password' => 'john1234',
                'passwordConfirmation' => 'john1234',
                'expectedStatusHttp' => 201,
                'expectedJson' => [
                    'message' => 'Successfully registered'
                ]
            ]
        ];
    }
}
