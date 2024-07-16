<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Http\Requests\StoreMessagesRequest;
use App\Models\Conversation;
use App\Models\Recipients;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user = Auth::user();
        $conversation = $user->conversations()->fiidOrFail($id);
        return $conversation->messages()->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessagesRequest $storeMessagesRequest)
    {
        $storeMessagesRequest->validated();

        $user = Auth::user();
        $user_id = $storeMessagesRequest->post('user_id');
        $conversation_id = $storeMessagesRequest->post('conversation_id');

        DB::beginTransaction();
        try {
            if ($conversation_id) {
                $conversation = $user->conversations()->findOrFail($conversation_id);
            } else {
                $conversation = Conversation::where('type', 'peer')
                    ->whereHas('participants', function ($query) use ($user_id, $user) {
                        $query->join('participants as participants2', 'participants2.conversation_id','=' ,'participants.conversation_id')
                        ->where('participants.user_id', $user_id)
                        ->where('participants2.user_id', $user->id);
                })->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'type' => 'peer',
                        'user_id' => $user->id,
                    ]);
                    $conversation->participants()->attach([
                        $user_id => ['joined_at' => now()],
                        $user->id => ['joined_at' => now()],
                    ]);
                }
            }

            $message = $conversation->messages()->create([
                'user_id' => $user_id,
                'body' => $storeMessagesRequest->post('message')
            ]);

            $conversation->update([
                'last_message_id' => $message->id,
            ]);

            DB::statement(
                'INSERT INTO recipients (user_id, message_id) SELECT user_id, ? FROM participants WHERE conversation_id = ?',
                [$message->id, $conversation->id]
            );
            DB::commit();

            broadcast(new MessageCreated($message));
        }catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Recipients::where([
            'user_id' => Auth::id(),
            'message_id' => $id
        ])->delete();
        return ['message' => 'Deleted'];
    }
}
