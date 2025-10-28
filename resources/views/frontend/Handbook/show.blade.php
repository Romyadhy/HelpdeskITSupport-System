<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fas fa-book-open text-emerald-600"></i> Detail Handbook / SOP
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-xl p-6">

                {{-- Header --}}
                <div class="mb-6 border-b pb-4">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-1">
                        {{ $handbook->title }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <div>
                            <i class="fas fa-user mr-1 text-emerald-500"></i>
                            Dibuat oleh: <span class="font-medium text-gray-700">{{ $handbook->uploader->name ?? 'Unknown' }}</span>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt mr-1 text-emerald-500"></i>
                            Diupload: {{ $handbook->created_at->translatedFormat('d M Y, H:i') }}
                        </div>
                        <div>
                            <i class="fas fa-layer-group mr-1 text-emerald-500"></i>
                            Kategori: <span class="font-semibold text-gray-700">{{ $handbook->category }}</span>
                        </div>
                    </div>
                </div>

                {{-- Isi / Deskripsi --}}
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                        <i class="fas fa-align-left text-emerald-500"></i> Ringkasan / Isi Dokumen
                    </h4>
                    <div class="bg-gray-50 border rounded-lg p-4 text-gray-700 leading-relaxed">
                        {!! nl2br(e($handbook->description)) !!}
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('handbook.index') }}"
                        class="px-4 py-2 rounded-md bg-gray-500 text-white hover:bg-gray-600 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>

                    @can('edit-handbook')
                        <a href="{{ route('handbook.edit', $handbook->id) }}"
                            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    @endcan

                    @can('delete-handbook')
                        <form action="{{ route('handbook.delete', $handbook->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus handbook ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
