<?php

namespace App\Http\Controllers;

use App\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    /**
     * Create a new NotesController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Returns the current user's all notes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(['notes' => Note::currentUserNotes()->get()]);
    }

    /**
     * Saves a new note for the logged in user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'note' => 'required|string|max:500',
        ]);
        $requests = $request->all();
        $requests['user_id'] = Auth::user()->id;
        $note = Note::create($requests);
        return response()->json(['message' => 'Successfully saved.', 'note' => $note], 201);
    }

    /**
     * Updates a current user's note
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $note = Note::findOrFail((int)$request->input('id'));
        if ($note->user_id != Auth::user()->id) {
            return response()->json(['error' => 'Invalid user permission.'], 400);
        }
        $this->validate($request, [
            'note' => 'required|string|max:500',
        ]);
        $note->note = $request->input('note');
        $note->save();
        return response()->json(['message' => 'Successfully updated.']);
    }

    /**
     * Deletes a current user's note
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $note = Note::findOrFail($id);
        if ($note->user_id != Auth::user()->id) {
            return response()->json(['error' => 'Invalid user permission.'], 400);
        }
        $note->delete();
        return response()->json(['message' => 'Successfully deleted.']);
    }
}
