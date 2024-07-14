<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConversationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return $user->conversations()->paginate(10);
    }
    public function show(Conversation $conversation)
    {
        return $conversation->load('participants');
    }
    public function addParticipant(Conversation $conversation, Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $conversation->participants()->attach($request->post('user_id'),[
            'joined_at' => Carbon::now()
        ]);
    }
    public function removeParticipant(Conversation $conversation, Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);
        $conversation->participants()->detach($request->post('user_id'));
    }
}
