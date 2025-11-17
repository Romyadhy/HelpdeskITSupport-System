<x-app-layout>
    <div class="container mx-auto mt-6">

        <h1 class="text-3xl font-bold mb-6 text-gray-800">Activity Log</h1>

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('activity.log') }}"
            class="mb-6 bg-white rounded-xl shadow-sm p-4 border border-gray-200">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Search --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Cari deskripsi, judul, dll..."
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                {{-- Filter user --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">User</label>
                    <select name="user_id"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div class="flex justify-end mt-4 space-x-2">
                <a href="{{ route('activity.log') }}"
                    class="text-xs px-4 py-2 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100">
                    Reset
                </a>
                <button type="submit"
                    class="text-xs px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                    Apply Filter
                </button>
            </div>
        </form>

        {{-- TIMELINE --}}
        <div class="relative">

            <div class="hidden md:block absolute left-4 top-0 bottom-0 border-l-2 border-gray-200"></div>

            <div class="space-y-6">
                @forelse ($logs as $log)

                    @php
                        $props = $log->properties ?? [];
                        $attributes = $props['attributes'] ?? [];
                        $old = $props['old'] ?? [];

                        // Subject (Ticket / Task / Report)
                        $ticketId = $log->subject->id ?? ($props['ticket_id'] ?? null);
                        $ticketTitle = $log->subject->title ?? ($props['ticket_title'] ?? null);

                        // Identify module
                        $module = $log->log_name ?? 'activity';

                        // DEFAULT FIELDS FOR TICKET
                        $displayOnlyBase = [
                            'title',
                            'description',
                            'status',
                            'priority',
                            'category_id',
                            'location_id',
                            'assigned_to',
                            'solution',
                        ];

                        if ($module === 'task_done') {
                            $displayOnly = ['complated_at', 'notes'];
                        } elseif ($module === 'report_daily' || $module === 'daily_report') {
                            $displayOnly = ['date', 'report_type', 'notes'];
                        } else {
                            $displayOnly = $displayOnlyBase;
                        }

                        $witaTime = $log->created_at
                            ? $log->created_at->setTimezone('Asia/Makassar')->format('d M Y, H:i')
                            : '';
                    @endphp

                    <div class="md:pl-10 flex space-x-3">

                        {{-- Bullet --}}
                        <div class="hidden md:flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow"></div>
                        </div>

                        {{-- CARD --}}
                        <div class="flex-1 bg-white shadow-sm rounded-xl p-4 border border-gray-200">

                            {{-- Header --}}
                            <div class="flex justify-between items-start">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-9 h-9 flex items-center justify-center rounded-full
                                        @if ($log->event === 'created') bg-blue-100 text-blue-600
                                        @elseif($log->event === 'deleted') bg-red-100 text-red-600
                                        @elseif($log->event === 'updated') bg-yellow-100 text-yellow-600
                                        @else bg-gray-100 text-gray-600 @endif">

                                        @if ($log->event === 'created')
                                            <i class="fa-solid fa-plus"></i>
                                        @elseif($log->event === 'deleted')
                                            <i class="fa-solid fa-trash"></i>
                                        @else
                                            <i class="fa-solid fa-pen"></i>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-800 text-sm">
                                                {{ $log->description ?? ucfirst($log->event ?? 'Activity') }}
                                            </span>

                                            <span
                                                class="text-[10px] px-2 py-1 rounded-full bg-gray-100 text-gray-600 uppercase">
                                                {{ Str::upper($module) }}
                                            </span>
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1">
                                            oleh <span class="font-medium">
                                                {{ optional($log->causer)->name ?? 'System' }}
                                            </span>
                                            • {{ $witaTime }} WITA
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right text-xs">
                                    @if ($ticketId)
                                        <div class="text-blue-700 font-semibold">#{{ $ticketId }}</div>
                                        @if ($ticketTitle)
                                            <div class="text-gray-500">{{ $ticketTitle }}</div>
                                        @endif
                                    @else
                                        <div class="text-gray-400 italic">— No Subject</div>
                                    @endif
                                </div>
                            </div>

                            {{-- DETAIL COLLAPSE --}}
                            <div x-data="{ open: false }" class="mt-3">

                                <button @click="open = !open"
                                    class="text-xs text-emerald-700 font-semibold flex items-center space-x-1">
                                    <span x-show="!open">Lihat detail perubahan</span>
                                    <span x-show="open">Sembunyikan detail</span>
                                    <i class="fa-solid fa-chevron-down text-[10px]" :class="{ 'rotate-180': open }"></i>
                                </button>

                                <div x-show="open" class="mt-3 space-y-3">

                                    {{-- TASK COMPLETION --}}
                                    @if ($module === 'task_done' && isset($log->completion))
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <div class="text-xs font-semibold text-blue-700 mb-1">
                                                Task Completion Details
                                            </div>

                                            <ul class="text-xs text-blue-800 space-y-1">
                                                <li><b>Task:</b> {{ $log->task->title ?? 'Unknown Task' }}</li>
                                                <li><b>Diselesaikan oleh:</b>
                                                    {{ $log->completion->user->name ?? 'Unknown' }}
                                                </li>
                                                <li><b>Tanggal Selesai:</b>
                                                    {{ optional($log->completion->complated_at)?->setTimezone('Asia/Makassar')->format('d M Y, H:i') }}
                                                    WITA
                                                </li>
                                                <li><b>Catatan:</b> {{ $log->completion->notes ?? '-' }}</li>
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- NEW VALUES --}}
                                    @if (!empty($attributes))
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <div class="text-xs font-semibold text-green-700 mb-1">
                                                Perubahan Baru
                                            </div>
                                            <ul class="text-xs text-green-800 space-y-1">
                                                @foreach ($attributes as $key => $value)
                                                    @if (in_array($key, $displayOnly))
                                                        <li>
                                                            <b>{{ ucfirst(str_replace('_', ' ', $key)) }}:</b>
                                                            {{ is_array($value) ? json_encode($value) : $value }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- OLD VALUES --}}
                                    @if (!empty($old))
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <div class="text-xs font-semibold text-red-700 mb-1">
                                                Sebelumnya
                                            </div>
                                            <ul class="text-xs text-red-800 space-y-1">
                                                @foreach ($old as $key => $value)
                                                    @if (in_array($key, $displayOnly))
                                                        <li>
                                                            <b>{{ ucfirst(str_replace('_', ' ', $key)) }}:</b>
                                                            {{ is_array($value) ? json_encode($value) : $value }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- EMPTY --}}
                                    @if (empty($attributes) && empty($old))
                                        <div class="text-xs text-gray-400">
                                            Tidak ada detail perubahan yang direkam.
                                        </div>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>

                @empty
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm text-gray-500">
                        Belum ada activity log.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
