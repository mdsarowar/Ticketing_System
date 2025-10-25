<?php

use App\Models\Ticket;
use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});


Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    $ticket = Ticket::find($ticketId);

    if (!$ticket) {
        return false;
    }

    // Admin can access all ticket channels
    if ($user->isAdmin()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    // Customer can only access their own ticket channels
    if ($ticket->user_id === $user->id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    return false;
});
