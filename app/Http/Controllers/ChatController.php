<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Store chat message (Web - Inertia)
    public function store(Request $request, $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $chatMessage = Chat::create([
            'ticket_id' => $ticketId,
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        // Load user relationship
        $chatMessage->load('user');

        // Broadcast event
        broadcast(new MessageSent($chatMessage))->toOthers();

        return back();
    }
}
