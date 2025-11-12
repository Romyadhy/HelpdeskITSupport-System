<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
        <i class="fas fa-upload text-emerald-600"></i> Upload Handbook / SOP
    </h2>
</x-slot>

<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-xl p-6 relative overflow-hidden">

            {{-- Background loading overlay (fade in/out transition) --}}
            <div wire:loading.delay.long wire:target="save"
                class="absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10 transition-all duration-500">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-emerald-600 border-t-transparent mb-3"></div>
                <p class="text-emerald-700 font-medium animate-pulse">Menyimpan dokumen...</p>
            </div>

            {{-- Alert Sukses --}}
            @if (session()->has('success'))
                <div class="mb-6 rounded border-l-4 border-emerald-500 bg-emerald-50 p-4 text-emerald-700 transition-all duration-500 ease-in-out">
                    <p class="font-semibold mb-1 flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i> Berhasil
                    </p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

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

            {{-- Form Upload --}}
            <form wire:submit.prevent="save" class="space-y-6">

                {{-- Judul --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Dokumen
                    </label>
                    <input type="text" id="title" wire:model.defer="title"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Contoh: SOP Penanganan Permintaan IT" required>
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori Dokumen
                    </label>
                    <select id="category" wire:model.defer="category"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="SOP">SOP</option>
                        <option value="Panduan Infrastruktur">Panduan Infrastruktur</option>
                        <option value="Prosedur Backup">Prosedur Backup</option>
                        <option value="Kontak Darurat dan Eskalasi">Kontak Darurat dan Eskalasi</option>
                        <option value="Checklist">Checklist</option>
                    </select>
                    @error('category') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi / Ringkasan Dokumen
                    </label>
                    <textarea id="description" wire:model.defer="description" rows="6"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Tuliskan isi atau ringkasan dari dokumen ini..." required></textarea>
                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Upload File PDF --}}
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
                        File PDF (opsional)
                    </label>
                    <input type="file" id="file" wire:model="file" accept="application/pdf"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700
                                   hover:file:bg-emerald-100 cursor-pointer border">
                    <p class="text-sm text-gray-500 mt-1">Hanya file PDF, maksimal 5MB.</p>

                    {{-- Progress Upload --}}
                    <div wire:loading wire:target="file" class="text-emerald-600 text-sm mt-1 animate-pulse">
                        <i class="fas fa-spinner fa-spin"></i> Sedang mengunggah file...
                    </div>
                    @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('handbook.index') }}"
                        class="px-4 py-2 rounded-md bg-gray-500 text-white hover:bg-gray-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>

                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 transition flex items-center gap-2"
                        wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-save"></i> Simpan Dokumen
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SweetAlert (opsional) --}}
<script>
    document.addEventListener('livewire:navigate', () => {
        if (@this.get('showSuccess')) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Dokumen berhasil disimpan!',
                confirmButtonColor: '#059669'
            });
        }
    });
</script>
