<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('john1234')
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
            ]
        ];
    }
}
