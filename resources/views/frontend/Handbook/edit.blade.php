<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-edit text-emerald-600"></i> Edit Handbook / SOP
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-xl p-6">

                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="mb-6 rounded border-l-4 border-red-500 bg-red-50 p-4 text-red-700">
                        <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Edit --}}
                <form method="POST" action="{{ route('handbook.update', $handbook->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Judul --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Judul Dokumen
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $handbook->title) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                            required>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori Dokumen
                        </label>
                        <select id="category" name="category"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                            required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="SOP" {{ old('category', $handbook->category) == 'SOP' ? 'selected' : '' }}>SOP</option>
                            <option value="Panduan Infrastruktur" {{ old('category', $handbook->category) == 'Panduan Infrastruktur' ? 'selected' : '' }}>Panduan Infrastruktur</option>
                            <option value="Prosedur Backup" {{ old('category', $handbook->category) == 'Prosedur Backup' ? 'selected' : '' }}>Prosedur Backup</option>
                            <option value="Kontak Darurat dan Eskalasi" {{ old('category', $handbook->category) == 'Kontak Darurat dan Eskalasi' ? 'selected' : '' }}>Kontak Darurat dan Eskalasi</option>
                            <option value="Checklist" {{ old('category', $handbook->category) == 'Checklist' ? 'selected' : '' }}>Checklist</option>
                        </select>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Deskripsi / Ringkasan Dokumen
                        </label>
                        <textarea id="description" name="description" rows="6"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                            required>{{ old('description', $handbook->description) }}</textarea>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('handbook.index') }}"
                            class="px-4 py-2 rounded-md bg-gray-500 text-white hover:bg-gray-600 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit"
                            class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
