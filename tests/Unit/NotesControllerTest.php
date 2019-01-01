<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Notes controller index test.
     * @test
     * @return void
     */
    public function index()
    {
        $this->signIn();

        factory(\App\Note::class, 3)->create([
            'user_id' => auth()->user()->id
        ]);

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('GET', '/api/notes/');

        $response
            ->assertStatus(200)
            ->assertJsonCount(3, 'notes');
    }

    /**
     * Notes controller create test.
     * @test
     * @dataProvider createNoteDataProvider
     * @return void
     */
    public function createNote($note, $expectedStatusHttp, $expectedJson)
    {
        $this->signIn();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('POST', '/api/notes/create', [
            'note' => $note,
        ]);

        $response
            ->assertStatus($expectedStatusHttp)
            ->assertJson($expectedJson);
    }

    public function createNoteDataProvider()
    {
        return [
            [
                'note' => null,
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'note' => str_repeat('Hello World ', 500),
                'expectedStatusHttp' => 422,
                'expectedJson' => [
                    'message' => 'The given data was invalid.'
                ]
            ],
            [
                'note' => str_repeat('Hello World ', 10),
                'expectedStatusHttp' => 201,
                'expectedJson' => [
                    'message' => 'Successfully saved.'
                ]
            ]
        ];
    }

    /**
     * Notes controller delete test.
     * @test
     * @dataProvider deleteNoteDataProvider
     * @return void
     */
    public function deleteNote($noteId, $expectedStatusHttp, $expectedJson)
    {
        $this->signIn();

        $otherUser = factory(\App\User::class)->create();

        $otherUserNote = factory(\App\Note::class)->create([
            'user_id' => $otherUser->id
        ]);

        $currentUserNote = factory(\App\Note::class)->create([
            'user_id' => auth()->user()->id
        ]);

        if ($noteId == 'OtherUser') {
            $noteId = $otherUserNote->id;
        }

        if ($noteId == 'CurrentUser') {
            $noteId = $currentUserNote->id;
        }

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])->json('DELETE', '/api/notes/delete/' . $noteId);

        $response
            ->assertStatus($expectedStatusHttp)
            ->assertJson($expectedJson);
    }

    public function deleteNoteDataProvider()
    {
        return [
            [
                'noteId' => null,
                'expectedStatusHttp' => 404,
                'expectedJson' => []
            ],
            [
                'noteId' => 'OtherUser',
                'expectedStatusHttp' => 400,
                'expectedJson' => [
                    'error' => 'Invalid user permission.'
                ]
            ],
            [
                'noteId' => 'CurrentUser',
                'expectedStatusHttp' => 200,
                'expectedJson' => [
                    'message' => 'Successfully deleted.'
                ]
            ]
        ];
    }
}
