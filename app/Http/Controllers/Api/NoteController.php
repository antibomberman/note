<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NoteStoreRequest;
use App\Http\Requests\Api\NoteUpdateRequest;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::query()
            ->when(!Auth::user()->isAdmin(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->latest()
            ->with('user')
            ->get();

        return response()->json($notes);
    }
    public function store(NoteStoreRequest $request)
    {
        $note = Auth::user()->notes()->create($request->validated());

        return response()->json($note);
    }
    public function show($id)
    {
        $note = Note::query()
            ->when(!Auth::user()->isAdmin(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->where('id', $id)
            ->latest()
            ->with('user')
            ->firstOrFail();

        return response()->json($note);
    }
    public function update(NoteUpdateRequest $request,$id)
    {
        $note = Note::query()
            ->when(!Auth::user()->isAdmin(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->where('id', $id)
            ->latest()
            ->firstOrFail();

        $note->update($request->validated());
        return response()->json($note);
    }
    public function delete(Note $id)
    {
        $note = Note::query()
            ->when(!Auth::user()->isAdmin(), function ($query) {
                return $query->where('user_id', Auth::id());
            })
            ->where('id', $id)
            ->latest()
            ->firstOrFail();

        $note->delete();
        return response()->json(['message' => 'Note deleted']);
    }
}
