<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            📘 Handbook & SOPs
        </h2>
        <p class="text-gray-500 text-sm mt-1">Panduan dan dokumen referensi utama untuk tim IT Support</p>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white">
            {{-- Card Utama --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📂 Daftar Dokumen Handbook</h3>

                @can('create-handbook')
                    <div class="flex justify-end mb-6">
                        <a href="{{ route('handbook.create') }}"
                            class="inline-flex item-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-md gap-2 transition duration-200">
                            <i class="fas fa-upload"></i>
                            New Handbook
                        </a>
                    </div>
                @endcan
            </div>

            {{-- Jika tidak ada handbook --}}
            @if ($handbooks->isEmpty())
                <div class="text-center py-10 text-gray-500">
                    <i class="fas fa-folder-open text-4xl mb-3"></i>
                    <p>Tidak ada dokumen handbook yang tersedia.</p>
                </div>
            @else
                {{-- Grid Dokumen --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 bg-white">
                    @foreach ($handbooks as $item)
                        <div
                            class="bg-gray-50 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition duration-200 p-5 flex flex-col justify-between">
                            <div>
                                {{-- Ikon berdasarkan kategori --}}
                                <div class="flex items-center justify-between mb-3">
                                    @php
                                        $icon = match ($item->category) {
                                            'SOP' => 'fa-clipboard-list',
                                            'Panduan Infrastruktur' => 'fa-network-wired',
                                            'Prosedur Backup' => 'fa-database',
                                            'Kontak Darurat dan Eskalasi' => 'fa-phone-alt',
                                            'Checklist' => 'fa-list-check',
                                            default => 'fa-file-alt',
                                        };
                                    @endphp
                                    <i class="fas {{ $icon }} text-3xl text-emerald-600"></i>
                                    <span
                                        class="text-xs bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full font-semibold">{{ $item->category }}</span>
                                </div>

                                <h4 class="text-lg font-semibold text-gray-800 truncate">
                                    {{ $item->title }}
                                </h4>

                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                    {{ $item->description }}
                                </p>
                            </div>

                            {{-- Info Upload --}}
                            <div class="mt-4 text-sm text-gray-400 flex justify-between items-center">
                                <span>
                                    <i class="fas fa-user-circle"></i>
                                    {{ $item->uploader->name ?? 'Unknown' }}
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i>
                                    {{ $item->created_at->format('d M Y') }}
                                </span>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="mt-4 flex justify-between items-center">
                                <a href="{{ route('handbook.show', $item->id) }}"
                                    class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                    <i class="fas fa-eye"></i> View
                                </a>

                                <div class="flex items-center gap-3">
                                    @can('edit-handbook')
                                        <a href="{{ route('handbook.edit', $item->id) }}"
                                            class="text-green-600 hover:text-green-800 flex items-center gap-1">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endcan

                                    @can('delete-handbook')
                                        <form action="{{ route('handbook.delete', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-800 flex items-center gap-1">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
