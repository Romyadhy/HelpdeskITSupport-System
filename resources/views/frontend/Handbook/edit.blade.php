
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
                <form id="editForm" method="POST" action="{{ route('handbook.update', $handbook->id) }}"
                      enctype="multipart/form-data" class="space-y-6">
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

                    {{-- File PDF --}}
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
                            Ganti File PDF (opsional)
                        </label>

                        {{-- Jika sudah ada file sebelumnya --}}
                        @if ($handbook->file_path)
                            <div class="bg-gray-50 border rounded-lg p-4 mb-3 flex justify-between items-center">
                                <div>
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                    <span class="text-sm text-gray-700">{{ basename($handbook->file_path) }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ asset('storage/' . $handbook->file_path) }}" target="_blank"
                                        class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">
                                        <i class="fas fa-eye mr-1"></i> Lihat PDF
                                    </a>
                                    <a href="{{ route('handbook.download', $handbook->id) }}"
                                        class="px-3 py-1 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Input file baru --}}
                        <input type="file" id="file" name="file" accept="application/pdf"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700
                                   hover:file:bg-emerald-100">
                        <p class="text-sm text-gray-500 mt-1">Upload file baru jika ingin mengganti PDF lama (maks. 5MB)</p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('handbook.index') }}"
                            class="px-4 py-2 rounded-md bg-gray-500 text-white hover:bg-gray-600 transition">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="button" id="btnSave"
                            class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ==== SCRIPT SECTION ==== --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                // ✅ Tambahkan confirm box sebelum submit
                const btnSave = document.getElementById('btnSave');
                const form = document.getElementById('editForm');

                btnSave.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Yakin ingin menyimpan perubahan?',
                        text: "Pastikan data yang diubah sudah benar.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#059669',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });

                // ✅ Alert untuk success/error dari session
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#ef4444'
                    });
                @endif
            });
        </script>
</x-app-layout>
