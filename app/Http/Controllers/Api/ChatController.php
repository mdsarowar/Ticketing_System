<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Get chat messages for a ticket
    public function index(Request $request, $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $messages = Chat::where('ticket_id', $ticketId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Chat::where('ticket_id', $ticketId)
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    // Send chat message
    public function store(Request $request, $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $chatMessage = Chat::create([
            'ticket_id' => $ticketId,
            'user_id' => $request->user()->id,
            'message' => $request->message,
        ]);

        // Broadcast event (for real-time chat)
        broadcast(new MessageSent($chatMessage->load('user')))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $chatMessage->load('user'),
        ], 201);
    }

    // Get unread message count
    public function unreadCount(Request $request, $ticketId)
    {
        $count = Chat::where('ticket_id', $ticketId)
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => ['unread_count' => $count],
        ]);
    }
}
