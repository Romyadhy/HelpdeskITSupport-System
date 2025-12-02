<x-app-layout>
    {{-- ================= HEADER ================= --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Task #{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl p-8">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h3 class="text-3xl font-extrabold text-emerald-600">
                    {{ strtoupper($task->title) }}
                </h3>

                {{-- Status Badge --}}
                <span @class([
                    'mt-2 md:mt-0 px-4 py-1.5 text-sm font-semibold rounded-full shadow-sm',
                    'bg-green-100 text-green-800' => $task->is_active,
                    'bg-gray-200 text-gray-800' => !$task->is_active,
                ])>
                    {{ $task->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Description --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi Task:</h4>
                <p class="text-gray-700 leading-relaxed">
                    {{ $task->description ?? 'Tidak ada deskripsi.' }}
                </p>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-gray-700 mb-6">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-sync-alt text-emerald-500"></i>
                    <span><strong>Frequency:</strong> {{ ucfirst($task->frequency) }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-calendar-check text-emerald-500"></i>
                    <span><strong>Created At:</strong>
                        {{ optional($task->created_at, fn($date) => $date->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i')) ?? 'Unknown' }}
                        WITA
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-tasks text-emerald-500"></i>
                    <span><strong>Completed This Month:</strong> {{ $completedCountThisMonth }}x</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-power-off text-emerald-500"></i>
                    <span><strong>Status:</strong> {{ $task->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>

            {{-- Completion Logs --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-emerald-500 mr-2"></i> Riwayat Penyelesaian
                </h4>

                @if ($completions->isEmpty())
                    <p class="text-gray-500 italic">Belum ada riwayat penyelesaian untuk task ini.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nama User</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Tanggal Selesai</th>
                                    {{-- <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Catatan</th> --}}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($completions as $index => $completion)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-800 font-medium">
                                            {{ $completion->user->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $completion->complated_at->setTimezone('Asia/Makassar')->translatedFormat('d M Y, H:i') }}
                                            WITA
                                        </td>
                                        {{-- <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $completion->notes ?? '-' }}
                                        </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Back Button --}}
            <div class="mt-8 flex justify-end">
                <a href="{{ route('tasks.daily') }}"
                    class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg shadow transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>

    {{-- ================= SWEETALERT SCRIPTS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ Show success popup after edit/update
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Task Updated!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
</x-app-layout>
