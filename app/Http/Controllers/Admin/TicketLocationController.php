<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketLocation;
use Illuminate\Http\Request;
use App\Helpers\logActivity;
use Illuminate\Support\Facades\Auth;

class TicketLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = TicketLocation::latest()->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_locations,name',
            'is_active' => 'boolean',
        ]);

        $location = TicketLocation::create([
            'name' => $request->name,
            'is_active' => $request->is_active ?? false,
        ]);

        logActivity::add('location', 'created', $location, 'Location created', [
            'new' => [
                'name' => $location->name,
                'is_active' => $location->is_active,
            ]
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Location created successfully.'
            ], 200);
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketLocation $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketLocation $location)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_locations,name,' . $location->id,
            'is_active' => 'boolean',
        ]);

        $old = $location->toArray();

        $location->update([
            'name' => $request->name,
            'is_active' => $request->is_active ?? false,
        ]);

        logActivity::add('location', 'updated', $location, 'Location updated', [
            'old' => $old,
            'new' => $location->toArray()
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully.'
            ], 200);
        }

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketLocation $location)
    {
        // Check if used in tickets
        if ($location->tickets()->exists()) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete location because it is used in existing tickets. Disable it instead.');
        }

        $location->delete();

        logActivity::add('location', 'deleted', $location, 'Location deleted', [
            'old' => $location->toArray()
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
