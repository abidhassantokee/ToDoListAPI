<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Auth controller login test.
     * @test
     * @dataProvider loginDataProvider
     * @return void
     */
    public function login($email, $password, $expectedStatusHttp, $expectedJson)
    {
        factory(\App\User::class)->create([
            'email' => 'john@doe.com',
            'password' => 'john1234'
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $response
            ->assertStatus($expectedStatusHttp)
            ->assertJson($expectedJson);
    }

    public function loginDataProvider()
    {
        return [
            [
                'email' => 'jane@doe.com',
                'password' => 'jane1234',
                'expectedStatusHttp' => 401,
                'expectedJson' => [
                    'error' => 'Unauthorized'
                ]
            ],
            [
                'email' => 'john@doe.com',
                'password' => 'john1234',
                'expectedStatusHttp' => 200,
                'expectedJson' => [
                    'token_type' => 'bearer',
                    'expires_in' => 3600
                ]
            ]
        ];
    }

    /**
     * Auth controller me test.
     * @test
     * @return void
     */
    public function me()
    {
        $this->signIn();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/auth/me');

        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => 'test@test.com'
            ]);
    }

    /**
     * Auth controller logout test.
     * @test
     * @return void
     */
    public function logout()
    {
        $this->signIn();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/auth/logout');

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);
    }

    /**
     * Auth controller refresh test.
     * @test
     * @return void
     */
    public function refresh()
    {
        $this->signIn();
        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/auth/refresh');

        $response
            ->assertStatus(200)
            ->assertJson([
                'token_type' => 'bearer',
                'expires_in' => 3600
            ]);
    }
}
