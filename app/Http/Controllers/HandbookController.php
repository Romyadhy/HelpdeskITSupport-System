<?php

namespace App\Http\Controllers;

use App\Models\Handbook;
use Auth;
use Illuminate\Http\Request;

class HandbookController extends Controller
{
    //   public function __construct()
    // {
    //     // Middleware berdasarkan permission
    //     $this->middleware('can:view-handbooks')->only(['index', 'show']);
    //     $this->middleware('can:create-handbook')->only(['create', 'store']);
    //     $this->middleware('can:edit-handbook')->only(['edit', 'update']);
    //     $this->middleware('can:delete-handbook')->only(['destroy']);
    // }

    public function index()
    {
        $handbooks = Handbook::with('uploader')->latest()->get();
        return view('frontend.Handbook.index', compact('handbooks'));
    }



    public function show($id)
    {
        $handbook = Handbook::with('uploader')->findOrFail($id);
        // dd($handbook);
        return view('frontend.Handbook.show', compact('handbook'));
    }



    public function create()
    {
        return view('frontend.Handbook.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'required',
        ]);

        Handbook::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('handbook.index')->with('success', 'Handbook berhasil ditambahkan');
    }



    public function edit($id)
    {
        $handbook = Handbook::with('uploader')->findOrFail($id);
        return view('frontend.Handbook.edit', compact('handbook'));
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'required',
        ]);

        $handbook = Handbook::findOrFail($id);

        $handbook->update($validated);

        return redirect()->route('handbook.index')->with('success', 'Handbook berhasil diperbarui!');
    }



    public function destroy(Handbook $handbook)
    {
        $handbook->delete();
        return redirect()->route('handbook.index')->with('success', 'Handbook berhasil dihapus!');
    }
}
