<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Sarowar\LaravelFileUpload\FileUpload;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        return Inertia::render('Tickets/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,general,complaint,feature_request',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath=FileUpload::fileUpload($request->file('attachment',),'attachment/');
        }

        Ticket::create([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'attachment' => $attachmentPath,
            'status' => 'open',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully');
    }

    public function show(Request $request, $id)
    {

        $ticket = Ticket::with(['user', 'comments.user'])->findOrFail($id);
//return $ticket;
        // Check authorization
//        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
//            abort(403, 'Unauthorized access');
//        }

        return Inertia::render('Tickets/show', [
            'ticket' => $ticket,
        ]);
    }

    public function edit(Request $request, $id)
    {
//        return $request;
        $user = auth()->user();
        $ticket = Ticket::findOrFail($id);

        // Check authorization
        if ($user->isCustomer() && $ticket->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('Tickets/edit', [
            'ticket' => $ticket,
        ]);
    }

    public function update(Request $request, $id)
    {

        $ticket = Ticket::findOrFail($id);

        // Check authorization
        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access');
        }

      $validatedata=$request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|in:technical,billing,general,complaint,feature_request',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $filePath = public_path($ticket->attachment);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
//            $ticket->attachment = $request->file('attachment')->store('attachments', 'public');
            $validatedata['attachment'] =FileUpload::fileUpload($request->file('attachment',),'attachment/');
        }else{
            $validatedata['attachment']=$ticket->attachment;
        }
//        return $request;
        $ticket->update($validatedata);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Ticket updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($request->user()->isCustomer() && $ticket->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access');
        }

        $filePath = public_path( $ticket->attachment);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }
}
