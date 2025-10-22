<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Data untuk admin dashboard
            $tickets = Ticket::with(['user', 'category'])->latest()->take(10)->get();
            return view('frontend.Dashbord.admindashboard', compact('tickets'));
        }
        
        if ($user->hasRole('manager')) {
            // Data untuk manager dashboard
            $pendingTickets = Ticket::where('status', 'pending')->count();
            return view('frontend.Dashbord.menagerdashboard', compact('pendingTickets'));
        }
        
        if ($user->hasRole('support')) {
            // Data untuk support dashboard
            $assignedTickets = Ticket::where('assigned_to', $user->id)
                                   ->where('status', 'open')
                                   ->get();
            return view('frontend.Dashbord.supportdashboard', compact('assignedTickets'));
        }

        // Default user dashboard
        $userTickets = Ticket::where('user_id', $user->id)->latest()->get();
        return view('frontend.Dashbord.userdahboard', compact('userTickets'));
    }
}