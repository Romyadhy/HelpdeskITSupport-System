<?php

namespace App\Livewire\Handbook;

use App\Http\Controllers\HandbookController;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Handbook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateHandBook extends Component
{
    // public function render()
    // {
    //     return view('frontend.Handbook.create')->layout('layouts.app');
    // }
    use WithFileUploads;

    public $title, $description, $category, $file;

    // ✅ Method untuk menyimpan data
    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'required|string|max:100',
            'file' => 'nullable|mimes:pdf|max:5120', // 5MB
        ]);

        $filePath = null;

        if ($this->file) {
            $fileName = Str::slug($this->title) . '-' . time() . '.pdf';
            $filePath = $this->file->storeAs('handbooks', $fileName, 'public');
        }

        Handbook::create([
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'uploaded_by' => Auth::id(),
            'file_path' => $filePath,
        ]);

        // Reset form setelah disimpan
        $this->reset(['title', 'description', 'category', 'file']);

        // Flash message
        session()->flash('success', 'Handbook berhasil ditambahkan!');

        // Redirect balik ke index (SPA)
        return redirect()->route('handbook.index');
    }

    public function render()
    {
        return view('livewire.handbook.create-hand-book')->layout('layouts.app');
    }
}
