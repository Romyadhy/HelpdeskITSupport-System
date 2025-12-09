<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use App\Helpers\logActivity;
use Illuminate\Support\Facades\Auth;

class TicketCategoryController extends Controller
{
    public function index()
    {
        $categories = TicketCategory::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

   // store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_categories,name',
            'is_active' => 'boolean',
        ]);

        $category = TicketCategory::create([
            'name' => $request->name,
            'is_active' => $request->is_active ?? false,
        ]);

        logActivity::add('category', 'created', $category, 'Category created', [
            'new' => [
                'name' => $category->name,
                'is_active' => $category->is_active,
            ]
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.'
            ], 200);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    // update
    public function update(Request $request, TicketCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ticket_categories,name,' . $category->id,
            'is_active' => 'boolean',
        ]);

        $old = $category->toArray();

        $category->update([
            'name' => $request->name,
            'is_active' => $request->is_active ?? false,
        ]);

        logActivity::add('category', 'updated', $category, 'Category updated', [
            'old' => $old,
            'new' => $category->toArray()
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.'
            ], 200);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    //delete
    public function destroy(TicketCategory $category)
    {
        // Check if used in tickets
        if ($category->tickets()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category because it is used in existing tickets. Disable it instead.');
        }

        $category->delete();

        logActivity::add('category', 'deleted', $category, 'Category deleted', [
            'old' => $category->toArray()
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
