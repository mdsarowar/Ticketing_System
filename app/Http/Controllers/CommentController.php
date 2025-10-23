<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        Comment::create([
            'ticket_id' => $ticketId,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment added successfully');
    }

    // Update comment (Web - Inertia)
    public function update(Request $request, $ticketId, $commentId)
    {
        $comment = Comment::where('ticket_id', $ticketId)->findOrFail($commentId);

        // Only comment owner can update
        if ($comment->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment updated successfully');
    }

    // Delete comment (Web - Inertia)
    public function destroy(Request $request, $ticketId, $commentId)
    {
        $comment = Comment::where('ticket_id', $ticketId)->findOrFail($commentId);

        // Only comment owner or admin can delete
        if ($comment->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully');
    }
}
