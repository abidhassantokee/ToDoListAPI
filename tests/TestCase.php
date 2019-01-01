<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Holds the jwt auth token
     * @var
     */
    protected $token;

    /**
     * Log the user in
     */
    protected function signIn()
    {
        factory(\App\User::class)->create([
            'email' => 'test@test.com',
            'password' => 'test1234'
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/auth/login', [
            'email' => 'test@test.com',
            'password' => 'test1234'
        ]);

        $content = json_decode($response->getContent());

        $this->token = $content->access_token;
    }
}
