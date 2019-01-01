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
            ->assertJson($expectedJson);
    }

    public function createDataProvider()
    {
        return [
            [
                'name' => null,
                'email ' => null,
                'password' => null,
                'passwordConfirmation' => null,
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => null,
                'password' => null,
                'passwordConfirmation' => null,
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => 'john@doe.com',
                'password' => null,
                'passwordConfirmation' => null,
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => 'john@doe.com',
                'password' => 'john1234',
                'passwordConfirmation' => null,
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => 'john@doe.com',
                'password' => 'john1234',
                'passwordConfirmation' => 'john123',
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => 'john@doe.com',
                'password' => 'john',
                'passwordConfirmation' => 'john',
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'Jane Doe',
                'email ' => 'jane@doe.com',
                'password' => 'john1234',
                'passwordConfirmation' => 'john1234',
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'name' => 'John Doe',
                'email ' => 'john@doe.com',
                'password' => 'john1234',
                'passwordConfirmation' => 'john1234',
                'expectedStatusHttp' => 201,
                'expectedJson' => [
                    'message' => 'Successfully registered.'
                ]
            ]
        ];
    }
}
