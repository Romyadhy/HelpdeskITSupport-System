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
                        $old = $props['old'] ?? [];
                        $new = $props['new'] ?? [];

                        $ticketId = $log->subject->id ?? null;
                        $ticketTitle = $log->subject->title ?? null;

                        $module = $log->log_name ?? 'activity';

                        // Mapping event label
                        $eventLabels = [
                            'created' => 'Dibuat',
                            'updated' => 'Diperbarui',
                            'deleted' => 'Dihapus',
                            'start' => 'Mulai Ditangani',
                            'takeover' => 'Diambil alih',
                            'close' => 'Ditutup',
                            'escalate' => 'Dieskalasi',
                            'cancel' => 'Dibatalkan',
                            'handle-escalated' => 'Tangani Eskalasi',
                        ];

                        $eventLabel = $eventLabels[$log->event] ?? ucfirst($log->event ?? 'Aktivitas');

                        // ICON + COLORS
                        $eventIcons = [
                            'created' => ['icon' => 'fa-plus', 'bg' => 'bg-blue-100 text-blue-600'],
                            'updated' => ['icon' => 'fa-pen', 'bg' => 'bg-yellow-100 text-yellow-600'],
                            'deleted' => ['icon' => 'fa-trash', 'bg' => 'bg-red-100 text-red-600'],
                            'start' => ['icon' => 'fa-play', 'bg' => 'bg-green-100 text-green-700'],
                            'takeover' => ['icon' => 'fa-handshake', 'bg' => 'bg-purple-100 text-purple-700'],
                            'close' => ['icon' => 'fa-check', 'bg' => 'bg-emerald-100 text-emerald-700'],
                            'escalate' => ['icon' => 'fa-arrow-up', 'bg' => 'bg-orange-100 text-orange-700'],
                            'cancel' => ['icon' => 'fa-xmark', 'bg' => 'bg-gray-200 text-gray-700'],
                            'handle-escalated' => ['icon' => 'fa-user-shield', 'bg' => 'bg-teal-100 text-teal-700'],
                        ];

                        $info = $eventIcons[$log->event] ?? ['icon' => 'fa-info', 'bg' => 'bg-gray-100 text-gray-600'];

                        // Format waktu
                        $witaTime = $log->created_at
                            ? $log->created_at->setTimezone('Asia/Makassar')->format('d M Y, H:i')
                            : '';
                    @endphp

                    <div class="md:pl-10 flex space-x-3">

                        {{-- BULLET --}}
                        <div class="hidden md:flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow"></div>
                        </div>

                        {{-- CARD --}}
                        <div class="flex-1 bg-white shadow-sm rounded-xl p-4 border border-gray-200">

                            {{-- HEADER --}}
                            <div class="flex justify-between items-start">
                                <div class="flex items-center space-x-3">

                                    {{-- ICON --}}
                                    <div class="w-9 h-9 flex items-center justify-center rounded-full {{ $info['bg'] }}">
                                        <i class="fa-solid {{ $info['icon'] }}"></i>
                                    </div>

                                    {{-- TEXT --}}
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-800 text-sm">
                                                {{ $log->description ?? $eventLabel }}
                                            </span>

                                            <span
                                                class="text-[10px] px-2 py-1 rounded-full bg-gray-100 text-gray-600 uppercase">
                                                {{ Str::upper($module) }}
                                            </span>
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1">
                                            oleh <span class="font-medium">{{ optional($log->causer)->name ?? 'System' }}</span>
                                            • {{ $witaTime }} WITA
                                        </div>

                                        <div class="text-[11px] text-gray-400">
                                            Aksi: <b>{{ $eventLabel }}</b> ({{ $log->event }})
                                        </div>
                                    </div>
                                </div>

                                {{-- SUBJECT --}}
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

                            {{-- COLLAPSE DETAIL --}}
                            <div x-data="{ open: false }" class="mt-3">

                                <button @click="open = !open"
                                    class="text-xs text-emerald-700 font-semibold flex items-center space-x-1">
                                    <span x-show="!open">Lihat detail perubahan</span>
                                    <span x-show="open">Sembunyikan detail</span>
                                    <i class="fa-solid fa-chevron-down text-[10px]" :class="{ 'rotate-180': open }"></i>
                                </button>

                                <div x-show="open" class="mt-3 space-y-3">

                                    {{-- NEW VALUES --}}
                                    @if (!empty($new))
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                            <div class="text-xs font-semibold text-green-700 mb-1">Perubahan Baru</div>
                                            <ul class="text-xs text-green-800 space-y-1">
                                                @foreach ($new as $key => $value)
                                                    <li>
                                                        <b>{{ ucfirst(str_replace('_', ' ', $key)) }}:</b>
                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- OLD VALUES --}}
                                    @if (!empty($old))
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <div class="text-xs font-semibold text-red-700 mb-1">Sebelumnya</div>
                                            <ul class="text-xs text-red-800 space-y-1">
                                                @foreach ($old as $key => $value)
                                                    <li>
                                                        <b>{{ ucfirst(str_replace('_', ' ', $key)) }}:</b>
                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- NO DETAILS --}}
                                    @if (empty($old) && empty($new))
                                        <div class="text-xs text-gray-400">Tidak ada detail perubahan yang direkam.</div>
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
        </div>

        {{-- PAGINATION --}}
        @if ($logs->hasPages())
            <div
                class="mt-6 px-6 py-5 border-t rounded-xl bg-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $logs->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold text-gray-900">{{ $logs->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-gray-900">{{ $logs->total() }}</span> results
                </div>

                <div class="flex items-center space-x-1">
                    {{-- Previous --}}
                    @if ($logs->onFirstPage())
                        <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}"
                            class="px-3 py-2 rounded-xl bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $current = $logs->currentPage();
                        $last = $logs->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $logs->url(1) }}"
                            class="px-3 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            1
                        </a>
                        @if ($start > 2)
                            <span class="px-2 text-gray-500">...</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $current)
                            <span class="px-4 py-2 rounded-xl bg-emerald-500 text-white font-semibold shadow">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $logs->url($page) }}"
                                class="px-4 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if ($end < $last)
                        @if ($end < $last - 1)
                            <span class="px-2 text-gray-500">...</span>
                        @endif
                        <a href="{{ $logs->url($last) }}"
                            class="px-3 py-2 rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            {{ $last }}
                        </a>
                    @endif

                    {{-- Next --}}
                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}"
                            class="px-3 py-2 rounded-xl bg-white border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>

            </div>
        @endif

    </div>
</x-app-layout>
