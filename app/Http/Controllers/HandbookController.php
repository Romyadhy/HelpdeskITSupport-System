<?php

namespace App\Http\Controllers;

use App\Models\Handbook;
use Auth;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'file' => 'nullable|mimes:pdf|max:5120', // max 5MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $fileName = Str::slug($request->title) . '-' . time() . '.pdf';
            $filePath = $request->file('file')->storeAs('handbooks', $fileName, 'public');
        }

        Handbook::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'uploaded_by' => Auth::id(),
            'file_path' => $filePath,
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
            'file' => 'nullable|mimes:pdf|max:5120',
        ]);

        $handbook = Handbook::findOrFail($id);

        if ($request->hasFile('file')) {
            // hapus file lama
            if ($handbook->file_path && Storage::disk('public')->exists($handbook->file_path)) {
                Storage::disk('public')->delete($handbook->file_path);
            }

            $fileName = Str::slug($request->title) . '-' . time() . '.pdf';
            $filePath = $request->file('file')->storeAs('handbooks', $fileName, 'public');
            $validated['file_path'] = $filePath;
        }

        $handbook->update($validated);

        return redirect()->route('handbook.index')->with('success', 'Handbook berhasil diperbarui!');
    }

    public function destroy(Handbook $handbook)
    {
        $handbook->delete();
        return redirect()->route('handbook.index')->with('success', 'Handbook berhasil dihapus!');
    }

    // public function exportPdf()
    // {
    //     $handbooks = Handbook::with('uploader')->latest()->get();

    //     return Pdf::view('pdf.handbook-list', [
    //         'handbooks' => $handbooks,
    //         'exported_at' => now(),
    //     ])
    //         ->format('a4')
    //         ->margins(10, 10, 15, 10)
    //         ->name('Handbook-List-' . now()->format('Ymd') . '.pdf');
    //     // ->download();
    // }

    public function downloadPdf($id)
    {
        $handbook = Handbook::findOrFail($id);

        if (!$handbook->file_path || !Storage::disk('public')->exists($handbook->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        return response()->download(storage_path('app/public/' . $handbook->file_path));
    }
}
