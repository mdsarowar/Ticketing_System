<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    // Get all tickets (Admin sees all, Customer sees own)
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $tickets = Ticket::with('user')->latest()->get();
        } else {
            $tickets = Ticket::where('user_id', $user->id)->latest()->get();
        }
        return Inertia::render('Tickets/index', [
            'tickets' => $tickets
        ]);

//        return response()->json([
//            'success' => true,
//            'data' => $tickets,
//        ]);
    }

    // Create new ticket
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,general,complaint,feature_request',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // 5MB max
        ]);

        $attachmentPath = null;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'attachment' => $attachmentPath,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'data' => $ticket->load('user'),
        ], 201);
    }

    // Get single ticket
    public function show(Request $request, $id)
    {
        $ticket = Ticket::with(['user', 'comments.user', 'chatMessages.user'])->findOrFail($id);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    // Update ticket
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|in:technical,billing,general,complaint,feature_request',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($ticket->attachment) {
                Storage::disk('public')->delete($ticket->attachment);
            }
            $ticket->attachment = $request->file('attachment')->store('attachments', 'public');
        }

        $ticket->update($request->except(['attachment']));

        return response()->json([
            'success' => true,
            'message' => 'Ticket updated successfully',
            'data' => $ticket->load('user'),
        ]);
    }

    // Delete ticket
    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Only admin or ticket owner can delete
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        // Delete attachment file
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully',
        ]);
    }
}
