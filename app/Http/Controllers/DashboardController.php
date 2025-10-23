<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $stats = [
                'total_tickets' => Ticket::count(),
                'open_tickets' => Ticket::where('status', 'open')->count(),
                'in_progress_tickets' => Ticket::where('status', 'in_progress')->count(),
                'resolved_tickets' => Ticket::where('status', 'resolved')->count(),
            ];
        } else {
            $stats = [
                'total_tickets' => Ticket::where('user_id', $user->id)->count(),
                'open_tickets' => Ticket::where('user_id', $user->id)->where('status', 'open')->count(),
                'in_progress_tickets' => Ticket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
                'resolved_tickets' => Ticket::where('user_id', $user->id)->where('status', 'resolved')->count(),
            ];
        }

        return Inertia::render('dashboard', [
            'stats' => $stats,
        ]);
    }
}
