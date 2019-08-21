<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use App\Events\MessageSent;

class ChatsController extends Controller
{
    // somente autenticados
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chats');
    }

    // buscar mensagens
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    // enviar novas mensagens
    public function sendMessage(Request $request)
    {
        $message = auth()->user()->messages()->create([
            'message' => $request->message
        ]);

        // transimite mensagem para o evento
        broadcast(new MessageSent($message->load('user')))->toOthers();

        return ['status' => 'success'];
    }
}
