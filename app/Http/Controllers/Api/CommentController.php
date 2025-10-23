<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Get all comments for a ticket
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

        $comments = Comment::where('ticket_id', $ticketId)
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    // Add comment to ticket
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
            'comment' => 'required|string',
        ]);

        $comment = Comment::create([
            'ticket_id' => $ticketId,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => $comment->load('user'),
        ], 201);
    }

    // Update comment
    public function update(Request $request, $ticketId, $commentId)
    {
        $comment = Comment::where('ticket_id', $ticketId)->findOrFail($commentId);

        // Only comment owner can update
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment->load('user'),
        ]);
    }

    // Delete comment
    public function destroy(Request $request, $ticketId, $commentId)
    {
        $comment = Comment::where('ticket_id', $ticketId)->findOrFail($commentId);

        // Only comment owner or admin can delete
        if ($comment->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }
}
