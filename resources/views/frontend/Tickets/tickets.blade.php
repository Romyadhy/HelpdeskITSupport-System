<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pusat Laporan Masalah dan Permintaan Bantuan
            </h2>
            @can('create-ticket')
            <button @click="$dispatch('open-create-modal')"
                class="mt-4 sm:mt-0 inline-flex items-center bg-teal-500 text-white font-medium px-4 py-2 rounded-lg shadow hover:bg-teal-600 transition">
                + Laporkan Masalah Baru
            </button>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen" x-data="ticketManagement()" @open-create-modal.window="openCreateModal()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-teal-600">Semua Ticket Pelaporan</h1>
                    <p class="text-gray-500 mt-1">Daftar semua permintaan bantuan dan laporan masalah.</p>
                </div>


                {{-- search --}}
                <div x-data="ticketSearch()" class="mb-4">
                    <form id="searchForm" method="GET" action="{{ route('tickets.index') }}">
                        <div class="relative max-w-sm">


                            <!-- Clear Button -->
                            <button x-show="search.length > 0" type="button" @click="clearSearch"
                                class="absolute right-10 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>

                            <!-- Search Icon / Spinner -->
                            <span class="absolute top-3 mx-auto pl-2 text-gray-400 text-lg">
                                <template x-if="loading">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                </template>
                                <template x-if="!loading">
                                    <i class="fas fa-search"></i>
                                </template>
                            </span>

                            <!-- Input -->
                            <input type="text" x-model="search" name="search" placeholder="Cari laporan..."
                                class="w-full pl-8 pr-11 py-2.5 rounded-xl bg-white border border-gray-300
                       text-gray-700 placeholder-gray-400 shadow-sm
                       focus:border-teal-500 focus:ring-2 focus:ring-teal-400 transition">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="priority" value="{{ request('priority') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">


                        </div>
                    </form>
                </div>
            </div>

            <!-- 🌟 PREMIUM FILTER BAR -->
            <div x-data="premiumFilter()" class="mb-4">

                <!-- Header -->
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        Filters

                        <!-- Badge Active Filters -->
                        <template x-if="activeCount > 0">
                            <span class="px-2 py-0.5 text-xs bg-teal-500 text-white rounded-full font-semibold"
                                x-text="activeCount"></span>
                        </template>
                    </h3>

                    <!-- Clear All Button -->
                    <button x-show="activeCount > 0" @click="clearAll()"
                        class="text-sm text-red-500 hover:text-red-600 underline transition">
                        Clear All
                    </button>
                </div>

                <!-- Filter Container -->
                <form id="premiumFilterForm" method="GET" action="{{ route('tickets.index') }}"
                    class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <!-- Status Filter -->
                        <div class="relative" x-data="{ open: false }">
                            <label class="text-sm text-gray-600">Status</label>
                            <button type="button" @click="open = !open"
                                class="w-full px-3 py-2 mt-1 bg-white border-gray-300 border rounded-xl flex justify-between items-center hover:border-teal-500 transition">
                                <span x-text="status ? status : 'All'"></span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-10 mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-xl p-2">
                                <template x-for="item in statusList">
                                    <div @click="status = item.value; submitFilters(); open = false"
                                        class="px-3 py-2 rounded-lg hover:bg-teal-50 cursor-pointer flex items-center justify-between">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check text-teal-600" x-show="status === item.value"></i>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" name="status" x-model="status">
                        </div>

                        <!-- Priority -->
                        <div class="relative" x-data="{ open: false }">
                            <label class="text-sm text-gray-600">Prioritas</label>
                            <button type="button" @click="open = !open"
                                class="w-full px-3 py-2 mt-1 bg-white border-gray-300 border rounded-xl flex justify-between items-center hover:border-teal-500 transition">
                                <span x-text="priority ? priority : 'All'"></span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-10 mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-xl p-2">
                                <template x-for="item in priorityList">
                                    <div @click="priority = item.value; submitFilters(); open = false"
                                        class="px-3 py-2 rounded-lg hover:bg-teal-50 cursor-pointer flex items-center justify-between">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check text-teal-600" x-show="priority === item.value"></i>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" name="priority" x-model="priority">
                        </div>

                        <!-- Category -->
                        <div class="relative" x-data="{ open: false }">
                            <label class="text-sm text-gray-600">Kategori</label>
                            <button type="button" @click="open = !open"
                                class="w-full px-3 py-2 mt-1 bg-white border-gray-300 border rounded-xl flex justify-between items-center hover:border-teal-500 transition">
                                <span x-text="category ? category : 'All'"></span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-10 mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-xl p-2">
                                <template x-for="item in categoryList">
                                    <div @click="category = item.value; submitFilters(); open = false"
                                        class="px-3 py-2 rounded-lg hover:bg-teal-50 cursor-pointer flex items-center justify-between">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check text-teal-600" x-show="category === item.value"></i>
                                    </div>
                                </template>
                            </div>

                            <input type="hidden" name="category" x-model="category">

                        </div>

                        <!-- Sort By Date -->
                        <div class="relative" x-data="{ open: false }">
                            <label class="text-sm text-gray-600">Urutkan</label>
                            <button type="button" @click="open = !open"
                                class="w-full px-3 py-2 mt-1 bg-white border-gray-300 border rounded-xl flex justify-between items-center hover:border-teal-500 transition">
                                <span x-text="sort ? sort : 'Newest First'"></span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-10 mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-xl p-2">
                                <template x-for="item in sortList">
                                    <div @click="sort = item.value; submitFilters(); open = false"
                                        class="px-3 py-2 rounded-lg hover:bg-teal-50 cursor-pointer flex items-center justify-between">
                                        <span x-text="item.label"></span>
                                        <i class="fas fa-check text-teal-600" x-show="sort === item.value"></i>
                                    </div>
                                </template>
                            </div>

                            {{-- <input type="hidden" name="sort" x-model="sort"> --}}
                            <input type="hidden" name="sort" x-model="sort">
                        </div>
                        <input type="hidden" name="search" id="filter-search" value="{{ request('search') }}">

                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Ticket ID</th>
                                <th class="px-6 py-3 text-left font-semibold">Subject</th>
                                <th class="px-6 py-3 text-left font-semibold hidden md:table-cell">User</th>
                                <th class="px-6 py-3 text-left font-semibold">Status</th>
                                {{-- <th class="px-6 py-3 text-left font-semibold">Request Prioritas</th> --}}
                                <th class="px-6 py-3 text-left font-semibold">Prioritas</th>
                                <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition text-left">
                                {{-- Ticket ID --}}
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                                </td>

                                {{-- Subject --}}
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $ticket->title }}</div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Created: {{ $ticket->created_at->format('Y-m-d') }}
                                    </p>
                                </td>

                                {{-- User --}}
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center font-bold text-teal-600 mr-2">
                                            {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <span>{{ $ticket->user->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <span @class([ 'px-3 py-1 text-xs font-semibold rounded-full' , 'bg-red-100 text-red-600'=> $ticket->status === 'Open',
                                        'bg-yellow-100 text-yellow-700' => $ticket->status === 'In Progress',
                                        'bg-green-100 text-green-700' => $ticket->status === 'Closed',
                                        'bg-purple-100 text-purple-700' => $ticket->status === 'Escalated',
                                        ])>
                                        {{ $ticket->status }}
                                    </span>
                                </td>

                                {{-- <td class="py-4 px-6"> --}}
                                {{-- <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700"> --}}
                                {{-- {{ $ticket->request_priority ?? '-' }} --}}
                                {{-- </span> --}}
                                {{-- </td> --}}

                                {{-- Priority --}}
                                <td class="px-6 py-4">
                                    @can('set-ticket-priority')
                                    @if ($ticket->status === 'Open')
                                    {{-- ✅ ADMIN / MANAGER - BISA EDIT --}}
                                    <div x-data="{
                                                    priority: '{{ $ticket->priority }}',
                                                    previousPriority: '{{ $ticket->priority }}',
                                                    isUpdating: false,
                                                    async updatePriority(ticketId, newPriority) {
                                                        if (newPriority === this.previousPriority) return;

                                                        const priorityLabels = {
                                                            'High': 'Tinggi',
                                                            'Medium': 'Sedang',
                                                            'Low': 'Rendah',
                                                            '': 'Tidak Ada'
                                                        };
                                                        const displayPriority = priorityLabels[newPriority] || newPriority || 'Tidak Ada';

                                                        const result = await Swal.fire({
                                                            title: 'Konfirmasi Perubahan Prioritas',
                                                            html: `Apakah Anda yakin ingin mengubah prioritas menjadi <strong>${displayPriority}</strong>?`,
                                                            icon: 'question',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#14b8a6',
                                                            cancelButtonColor: '#6b7280',
                                                            confirmButtonText: 'Ya, Ubah!',
                                                            cancelButtonText: 'Batal',
                                                            showLoaderOnConfirm: true,
                                                            allowOutsideClick: () => !Swal.isLoading(),
                                                            preConfirm: async () => {
                                                                try {
                                                                    const response = await fetch(`/tickets/${ticketId}/set-priority`, {
                                                                        method: 'PATCH',
                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                                                                            'Accept': 'application/json'
                                                                        },
                                                                        body: JSON.stringify({ priority: newPriority })
                                                                    });

                                                                    const data = await response.json();

                                                                    if (!response.ok) {
                                                                        throw new Error(data.message || 'Gagal mengubah prioritas.');
                                                                    }

                                                                    return data;
                                                                } catch (error) {
                                                                    Swal.showValidationMessage(error.message || 'Terjadi kesalahan.');
                                                                    return false;
                                                                }
                                                            }
                                                        });

                                                        if (result.isConfirmed) {
                                                            this.previousPriority = newPriority;
                                                            Swal.fire({
                                                                icon: 'success',
                                                                title: 'Berhasil!',
                                                                text: 'Prioritas berhasil diubah.',
                                                                showConfirmButton: false,
                                                                timer: 1500
                                                            });
                                                        } else {
                                                            // User cancelled - revert to previous priority
                                                            this.priority = this.previousPriority;
                                                        }
                                                    }
                                                }">
                                        <select x-model="priority"
                                            @change="updatePriority({{ $ticket->id }}, $event.target.value)"
                                            :disabled="isUpdating"
                                            class="px-5 py-1 text-xs font-semibold rounded-full cursor-pointer transition"
                                            :class="{
                                                            'bg-red-100 text-red-600': priority === 'High',
                                                            'bg-orange-100 text-orange-600': priority === 'Medium',
                                                            'bg-green-100 text-green-700': priority === 'Low',
                                                            'bg-gray-100 text-gray-500': !priority
                                                        }">
                                            <option value="">-</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                    </div>
                                    @else
                                    {{-- admin --}}
                                    <span @class([ 'px-3 py-1 text-xs font-semibold rounded-full' , 'bg-red-100 text-red-600'=> $ticket->priority === 'High',
                                        'bg-orange-100 text-orange-600' => $ticket->priority === 'Medium',
                                        'bg-green-100 text-green-700' => $ticket->priority === 'Low',
                                        'bg-gray-100 text-gray-500' => !$ticket->priority,
                                        ])>
                                        {{ $ticket->priority ?? '-' }}
                                    </span>
                                    @endif
                                    @else
                                    {{-- selain admin --}}
                                    <span @class([ 'px-3 py-1 text-xs font-semibold rounded-full' , 'bg-red-100 text-red-600'=> $ticket->priority === 'High',
                                        'bg-orange-100 text-orange-600' => $ticket->priority === 'Medium',
                                        'bg-green-100 text-green-700' => $ticket->priority === 'Low',
                                        'bg-gray-100 text-gray-500' => !$ticket->priority,
                                        ])>
                                        {{ $ticket->priority ?? '-' }}
                                    </span>
                                    @endcan
                                </td>

                                {{-- ACTIONS --}}
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">

                                    {{-- View --}}
                                    <button @click="openShowModal({{ $ticket->id }})" title="View Ticket"
                                        class="text-gray-400 hover:text-indigo-600 p-2 rounded-lg transition">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- ==== User ==== --}}
                                    @can('edit-own-ticket', $ticket)
                                    @if ($ticket->status === 'Open')
                                    <button
                                        @click="openEditModal({{ $ticket->id }}, '{{ addslashes($ticket->title) }}', '{{ addslashes($ticket->description) }}', {{ $ticket->category_id }}, {{ $ticket->location_id }})"
                                        title="Edit Ticket"
                                        class="text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endif
                                    @endcan

                                    @can('delete-own-ticket', $ticket)
                                    @if (in_array($ticket->status, ['Open']))
                                    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST"
                                        class="inline delete-ticket-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" title="Delete Ticket"
                                            class="delete-ticket-btn text-gray-400 hover:text-red-600 p-2 rounded-lg transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan

                                    {{-- ==== Support ==== --}}
                                    @can('handle-ticket')
                                    @if ($ticket->status === 'Open' && !$ticket->assigned_to && $ticket->priority)
                                    {{-- Handle Ticket --}}
                                    <form action="{{ route('tickets.start', $ticket->id) }}" method="POST"
                                        class="inline handle-ticket-form">
                                        @csrf
                                        <button type="button" title="Handle Ticket"
                                            class="handle-ticket-btn text-gray-400 hover:text-yellow-600 p-2 rounded-lg transition">
                                            <i class="fas fa-wrench mr-1"></i>
                                        </button>
                                    </form>
                                    @elseif($ticket->status === 'In Progress' && $ticket->assigned_to === auth()->id())
                                    {{-- Close Ticket --}}
                                    @can('close-ticket')
                                    <button type="button" title="Finish Ticket"
                                        @click="openFinishModal({{ $ticket->id }})"
                                        class="text-gray-400 hover:text-green-600 p-2 rounded-lg transition">
                                        <i class="fas fa-check mr-1"></i>
                                    </button>
                                    @endcan

                                    {{-- Escalate --}}
                                    @can('escalate-ticket')
                                    <button type="button" title="Escalate Ticket"
                                        class="escalate-ticket-btn text-gray-400 hover:text-purple-600 p-2 rounded-lg transition"
                                        data-action="{{ route('tickets.escalate', $ticket->id) }}">
                                        <i class="fas fa-level-up-alt mr-1"></i>
                                    </button>
                                    @endcan

                                    {{-- 🟠 Cancel Ticket --}}
                                    <form action="{{ route('tickets.cancel', $ticket->id) }}" method="POST"
                                        class="inline cancel-ticket-form">
                                        @csrf
                                        <button type="button" title="Cancel Handling"
                                            class="cancel-ticket-btn text-gray-400 hover:text-orange-600 p-2 rounded-lg transition"
                                            data-ticket-id="{{ $ticket->id }}">
                                            <i class="fas fa-ban mr-1"></i>
                                        </button>
                                    </form>
                                    @elseif($ticket->status === 'In Progress' && $ticket->assigned_to !== auth()->id())
                                    @can('take-over')
                                    <form action="{{ route('tickets.takeOver', $ticket->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Take Over Ticket"
                                            class="take-over-ticket-btn text-gray-400 hover:text-yellow-600 p-2 rounded-lg transition">
                                            <i class="fas fa-hand-paper mr-1"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    @endif
                                    @endcan

                                    {{-- ==== Admin ==== --}}
                                    @can('handle-escalated-ticket')
                                    @if (!$ticket->is_escalation && $ticket->status === 'In Progress' && $ticket->assigned_to === auth()->id())
                                    @can('close-ticket')
                                    <button type="button" title="Close Ticket"
                                        class="close-ticket-btn text-gray-400 hover:text-green-600 p-2 rounded-lg transition"
                                        data-action="{{ route('tickets.close', $ticket->id) }}">
                                        <i class="fas fa-check mr-1"></i>
                                    </button>
                                    @endcan
                                    <form action="{{ route('tickets.cancel', $ticket->id) }}" method="POST"
                                        class="inline cancel-ticket-form">
                                        @csrf
                                        <button type="button" title="Cancel Handling"
                                            class="cancel-ticket-btn text-gray-400 hover:text-orange-600 p-2 rounded-lg transition"
                                            data-ticket-id="{{ $ticket->id }}">
                                            <i class="fas fa-ban mr-1"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if ($ticket->is_escalation && $ticket->status === 'In Progress')
                                    <form action="{{ route('tickets.handleEscalated', $ticket->id) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" title="Handle Escalated Ticket"
                                            class="handle-escalated-ticket-btn text-gray-400 hover:text-blue-600 p-2 rounded-lg transition">
                                            <i class="fas fa-user-check mr-1"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No tickets found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Modern Pagination with Info -->
                    <div
                        class="px-6 py-5 border-t bg-white flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                        <!-- Left: Showing Info -->
                        <div class="text-sm text-gray-600">
                            Showing
                            <span class="font-semibold text-gray-900">{{ $tickets->firstItem() }}</span>
                            to
                            <span class="font-semibold text-gray-900">{{ $tickets->lastItem() }}</span>
                            of
                            <span class="font-semibold text-gray-900">{{ $tickets->total() }}</span>
                            results
                        </div>

                        <!-- Right: Pagination -->
                        <div class="flex items-center space-x-1">

                            {{-- Previous --}}
                            @if ($tickets->onFirstPage())
                            <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                            @else
                            <a href="{{ $tickets->previousPageUrl() }}"
                                class="px-3 py-2 rounded-xl bg-white border border-gray-300
                      text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($tickets->links()->elements[0] as $page => $url)
                            @if ($page == $tickets->currentPage())
                            <span class="px-4 py-2 rounded-xl bg-teal-500 text-white font-semibold shadow">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $url }}"
                                class="px-4 py-2 rounded-xl bg-white border border-gray-300
                          text-gray-700 hover:bg-gray-100 transition">
                                {{ $page }}
                            </a>
                            @endif
                            @endforeach

                            {{-- Next --}}
                            @if ($tickets->hasMorePages())
                            <a href="{{ $tickets->nextPageUrl() }}"
                                class="px-3 py-2 rounded-xl bg-white border border-gray-300
                      text-gray-600 hover:bg-gray-100 transition">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            @else
                            <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                            @endif

                        </div>
                    </div>


                </div>
            </div>
        </div>
        {{-- Create Ticket Modal --}}
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form @submit.prevent="submitCreate()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create New Ticket</h3>

                            <!-- Error Display -->
                            <div x-show="Object.keys(errors).length > 0"
                                class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    <template x-for="(error, field) in errors" :key="field">
                                        <li x-text="error[0]"></li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Title -->
                            <div class="mb-4">
                                <label for="create-title"
                                    class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" x-model="createFormData.title" id="create-title"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="create-description"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea x-model="createFormData.description" id="create-description" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required></textarea>
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <label for="create-category"
                                    class="block text-sm font-medium text-gray-700">Category</label>
                                <select x-model="createFormData.category_id" id="create-category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                                    <option value="">-- Select Category --</option>
                                    <x-category-options :categories="$categories" />
                                </select>
                            </div>

                            <!-- Location -->
                            <div class="mb-4">
                                <label for="create-location"
                                    class="block text-sm font-medium text-gray-700">Location</label>
                                <select x-model="createFormData.location_id" id="create-location"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                                    <option value="">-- Select Location --</option>
                                    <x-location-options :locations="$locations" />
                                </select>
                            </div>

                            {{-- Request Priority --}}
                            <div class="mb-4">
                                <label for="create-priority" class="block text-sm font-medium text-gray-700">Request
                                    Priority</label>

                                <select x-model="createFormData.priority" id="create-priority"
                                    class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                                    <option value="">-- Select Priority --</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="isSubmitting"
                                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isSubmitting ? 'Creating...' : 'Create Ticket'"></span>
                            </button>
                            <button type="button" @click="closeModals()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Ticket Modal --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form @submit.prevent="submitEdit()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Ticket</h3>

                            <!-- Error Display -->
                            <div x-show="Object.keys(errors).length > 0"
                                class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    <template x-for="(error, field) in errors" :key="field">
                                        <li x-text="error[0]"></li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Title -->
                            <div class="mb-4">
                                <label for="edit-title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input type="text" x-model="editFormData.title" id="edit-title"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="edit-description"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea x-model="editFormData.description" id="edit-description" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required></textarea>
                            </div>

                            <!-- Category -->
                            <div class="mb-4">
                                <label for="edit-category"
                                    class="block text-sm font-medium text-gray-700">Category</label>
                                <select x-model="editFormData.category_id" id="edit-category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                                    <option value="">-- Select Category --</option>
                                    <x-category-options :categories="$categories" />
                                </select>
                            </div>

                            <!-- Location -->
                            <div class="mb-4">
                                <label for="edit-location"
                                    class="block text-sm font-medium text-gray-700">Location</label>
                                <select x-model="editFormData.location_id" id="edit-location"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    required>
                                    <option value="">-- Select Location --</option>
                                    <x-location-options :locations="$locations" />
                                </select>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="isSubmitting"
                                :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                                class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-500 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isSubmitting ? 'Updating...' : 'Update Ticket'"></span>
                            </button>
                            <button type="button" @click="closeModals()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Finish Ticket Modal -->
        <div x-show="showFinishModal" x-cloak x-transition.opacity class="fixed inset-0 z-50"
            @keydown.escape.window="closeFinishModal()">
            <!-- Backdrop (click outside closes) -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeFinishModal()"></div>

            <!-- Modal Wrapper -->
            <div class="relative flex min-h-screen items-center justify-center p-4 sm:p-6">
                <!-- Modal Card -->
                <div class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden"
                    @click.stop>
                    <!-- Header -->
                    <div class="px-6 py-4 border-b flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Finish Ticket</h3>
                            <p class="text-sm text-gray-500">
                                Dokumentasikan solusi sebelum menutup ticket
                            </p>
                        </div>

                        <button type="button" @click="closeFinishModal()"
                            class="shrink-0 rounded-lg p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                            aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Body (scrollable area) -->
                    <form @submit.prevent="submitFinishTicket()" class="flex flex-col">
                        <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                            <!-- Solution Text -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">
                                    Solusi
                                </label>
                                <textarea x-model="finishForm.solution" rows="5"
                                    placeholder="Jelaskan langkah penyelesaian masalah secara jelas dan singkat..."
                                    class="w-full rounded-xl border-gray-300 shadow-sm
                                        focus:border-teal-500 focus:ring-2 focus:ring-teal-500
                                        resize-none"
                                    required></textarea>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Lampiran (Opsional)
                                    </label>

                                    <!-- Replace button (only if preview exists) -->
                                    <button type="button" x-show="finishForm.preview" @click="triggerFilePicker()"
                                        class="text-xs font-semibold text-teal-600 hover:text-teal-700 underline">
                                        Replace
                                    </button>
                                </div>

                                <!-- Upload box (hidden when preview exists) -->
                                <div x-show="!finishForm.preview">
                                    <label
                                        class="flex flex-col items-center justify-center
                                            border-2 border-dashed border-gray-300
                                            rounded-xl p-6 cursor-pointer
                                            hover:border-teal-500 transition">
                                        <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
                                        <span class="text-sm text-gray-500">
                                            Klik untuk upload gambar solusi
                                        </span>

                                        <input x-ref="finishFile" type="file" class="hidden" accept="image/*"
                                            @change="previewImage">
                                    </label>
                                </div>

                                <!-- Preview block -->
                                <div x-show="finishForm.preview" class="mt-4">
                                    <div class="relative w-full overflow-hidden rounded-xl border bg-gray-50">
                                        <!-- Remove (X) -->
                                        <button type="button" @click="clearImage()"
                                            class="absolute top-2 right-2 z-10 rounded-full bg-white/90 p-2 shadow hover:bg-white transition"
                                            aria-label="Remove image">
                                            <i class="fas fa-times text-gray-700"></i>
                                        </button>

                                        <!-- Image -->
                                        <img :src="finishForm.preview" alt="Preview"
                                            class="block w-full max-h-[320px] object-contain">

                                        <!-- Click to open full -->
                                        <button type="button"
                                            class="w-full text-center py-2 text-xs text-gray-500 hover:text-gray-700 border-t bg-white"
                                            @click="window.open(finishForm.preview, '_blank')">
                                            Click to open full image
                                        </button>
                                    </div>

                                    <!-- Hidden file input for replacing -->
                                    <input x-ref="finishFileReplace" type="file" class="hidden" accept="image/*"
                                        @change="previewImage">
                                </div>
                            </div>
                        </div>

                        <!-- Footer (sticky-ish, always visible) -->
                        <div class="px-6 py-4 border-t bg-white flex justify-end gap-3">
                            <button type="button" @click="closeFinishModal()"
                                class="px-4 py-2 rounded-xl border border-gray-300
                                    text-gray-700 hover:bg-gray-100 transition">
                                Cancel
                            </button>

                            <button type="submit"
                                class="px-5 py-2 rounded-xl bg-teal-600 text-white
                                    font-semibold hover:bg-teal-700 shadow transition">
                                Submit & Close Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Show Ticket Modal (Detail View) --}}
        <div x-show="showShowModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showShowModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="closeModals()"></div>

                <!-- Modal panel -->
                <div x-show="showShowModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[90vh] overflow-y-auto">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        {{-- <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Ticket Details</h3>
                            <button @click="closeModals()" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div> --}}
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Ticket Details</h3>

                            <div class="flex items-center gap-2">
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>


                        <div x-show="loading" class="text-center py-8">
                            <i class="fas fa-circle-notch fa-spin text-4xl text-teal-500"></i>
                            <p class="mt-2 text-gray-600">Loading ticket details...</p>
                        </div>

                        <!-- Ticket Details Display -->
                        <div x-show="!loading" class="space-y-6">
                            <!-- Header with Title & Status -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-4 border-b">
                                <h4 class="text-3xl font-extrabold text-teal-600 uppercase" x-text="showData.title">
                                </h4>
                                <span class="mt-2 md:mt-0 px-4 py-1.5 text-sm font-semibold rounded-full shadow-sm"
                                    :class="{
                                    'bg-blue-100 text-blue-800': showData.status === 'Open',
                                    'bg-yellow-100 text-yellow-800': showData.status === 'In Progress',
                                    'bg-green-100 text-green-800': showData.status === 'Closed',
                                }"
                                    x-text="showData.status"></span>
                            </div>

                            <!-- Description -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h5 class="text-lg font-semibold text-gray-800 mb-2">
                                    <i class="fas fa-file-alt text-teal-500 mr-2"></i>Deskripsi Masalah:
                                </h5>
                                <p class="text-gray-700 leading-relaxed" x-text="showData.description"></p>
                            </div>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">

                                <!-- Left Column -->
                                <div class="space-y-3">

                                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <i class="fas fa-layer-group text-teal-500 w-5"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Category</p>
                                            <p class="font-semibold text-gray-800" x-text="showData.category || '-'">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <i class="fas fa-map-marker-alt text-teal-500 w-5"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Location</p>
                                            <p class="font-semibold text-gray-800" x-text="showData.location || '-'">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <i class="fas fa-user text-teal-500 w-5"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Created By</p>
                                            <p class="font-semibold text-gray-800" x-text="showData.user || 'Unknown'">
                                            </p>
                                        </div>
                                    </div>

                                </div>

                                <!-- Right Column -->
                                <div class="space-y-3">

                                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <i class="fas fa-calendar text-teal-500 w-5"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Created At</p>
                                            <p class="font-semibold text-gray-800" x-text="showData.created_at"></p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-center justify-between gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-flag text-teal-500 w-5"></i>
                                            <div>
                                                <p class="text-xs text-gray-500">Priority</p>
                                                <p class="font-semibold text-gray-800" x-text="showData.priority"></p>
                                            </div>
                                        </div>

                                        <span class="px-2 py-0.5 rounded text-xs font-medium"
                                            :class="{
                                            'bg-yellow-100 text-yellow-800': showData.priority === 'Low',
                                            'bg-orange-100 text-orange-800': showData.priority === 'Medium',
                                            'bg-red-100 text-red-800': showData.priority === 'High'
                                        }">
                                            <span x-text="showData.priority"></span>
                                        </span>
                                    </div>

                                    <!-- Grid informations -->
                                    <div class="border border-gray-200 rounded-lg p-3 bg-white shadow-sm">
                                        <div class="flex items-center justify-between mb-2">
                                            <h5 class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                                                <i class="fas fa-stopwatch text-teal-500"></i>
                                                Informasi Durasi
                                            </h5>

                                            <span class="text-sm font-bold text-teal-600"
                                                x-text="showData.total_duration || '-'"></span>
                                        </div>

                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">⏳ Menunggu</span>
                                                <span class="font-semibold text-gray-800"
                                                    x-text="showData.waiting_duration || '-'"></span>
                                            </div>

                                            <div class="flex justify-between">
                                                <span class="text-gray-500">🔧 Pengerjaan</span>
                                                <span class="font-semibold text-gray-800"
                                                    x-text="showData.progress_duration || '-'"></span>
                                            </div>

                                            <div class="pt-1 border-t flex justify-between">
                                                <span class="font-semibold text-gray-700">✅ Total</span>
                                                <span class="font-bold text-teal-600"
                                                    x-text="showData.total_duration || '-'"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- Notes Section -->
                            <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-3 space-y-2">

                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <i class="fas fa-sticky-note text-teal-500"></i>
                                        Catatan Admin
                                    </h5>

                                    @role('admin')
                                    <template x-if="showData.status === 'Open'">
                                        <button @click="addNote(showData.id)"
                                            class="text-xs inline-flex items-center gap-1 px-3 py-1.5 rounded-md border border-teal-500 text-teal-600 hover:bg-teal-50 transition">
                                            <i class="fas fa-plus"></i>
                                            Tambah
                                        </button>
                                        @endrole
                                    </template>
                                </div>

                                <!-- JIKA NOTES KOSONG -->
                                <div x-show="!showData.notes || showData.notes.length === 0"
                                    class="text-xs text-gray-400 italic text-center py-2">
                                    Tidak Ada Catatan Admin
                                </div>

                                <!-- LIST NOTES -->
                                <div x-show="showData.notes && showData.notes.length"
                                    class="space-y-2 max-h-40 overflow-y-auto pr-1">

                                    <template x-for="note in showData.notes" :key="note.id">
                                        <div class="border border-gray-200 rounded-md px-2 py-1.5 bg-white">
                                            <p class="text-xs text-gray-700 leading-relaxed" x-text="note.note"></p>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-[10px] text-gray-500">
                                                    <i class="fas fa-user-shield mr-1"></i>
                                                    <span x-text="note.author"></span>
                                                </span>
                                                <span class="text-[10px] text-gray-400" x-text="note.created_at"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                            </div>


                            <!-- Solution Section (only if Closed) -->
                            <div x-show="showData.status === 'Closed' && showData.solution"
                                class="bg-green-50 border-l-4 border-green-500 rounded-lg p-5">
                                <h5 class="text-lg font-semibold text-green-700 mb-2 flex items-center">
                                    <i class="fas fa-tools mr-2"></i> Solusi dari Masalah
                                </h5>
                                <p class="text-gray-700 leading-relaxed"
                                    x-text="showData.solution || 'Belum ada solusi yang tercatat.'">
                                </p>
                                <template x-if="showData.solution_image_url">
                                    <img :src="showData.solution_image_url"
                                        class="rounded-lg max-h-60 cursor-pointer shadow"
                                        @click="window.open(showData.solution_image_url, '_blank')">
                                </template>
                            </div>

                            <!-- Assigned To -->
                            <div x-show="showData.assigned_to"
                                class="bg-teal-50 border-l-4 border-teal-500 rounded-lg p-5">
                                <h5 class="text-lg font-semibold text-teal-700 mb-2 flex items-center">
                                    <i class="fas fa-user-cog mr-2"></i> Ditangani Oleh
                                </h5>
                                <p class="text-gray-700" x-text="showData.assigned_to || 'Unknown'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ticket Management Modals --}}
    <script>
        // Define ticketManagement in global scope for Alpine.js
        window.ticketManagement = function() {
            return {
                // Modal states
                showCreateModal: false,
                showEditModal: false,
                showShowModal: false,
                showFinishModal: false,

                // Form data
                createFormData: {
                    title: '',
                    description: '',
                    category_id: '',
                    location_id: '{{ auth()->user()->location_id ?? '
                    ' }}',
                    priority: ''
                },

                editFormData: {
                    id: null,
                    title: '',
                    description: '',
                    category_id: '',
                    location_id: ''
                },


                finishTicketId: null,
                finishForm: {
                    solution: '',
                    image: null,
                    preview: null
                },

                showData: {
                    notes: []
                },
                errors: {},
                loading: false,
                isSubmitting: false, // NEW: Prevent double submissions

                // Methods
                openCreateModal() {
                    this.createFormData = {
                        title: '',
                        description: '',
                        category_id: '',
                        location_id: '{{ auth()->user()->location_id ?? '
                        ' }}'
                    };
                    this.errors = {};
                    this.showCreateModal = true;
                },

                openEditModal(id, title, description, categoryId, locationId) {
                    this.editFormData = {
                        id: id,
                        title: title,
                        description: description,
                        category_id: categoryId,
                        location_id: locationId
                    };
                    this.errors = {};
                    this.showEditModal = true;
                },

                openFinishModal(ticketId) {
                    this.finishTicketId = ticketId;

                    // reset (and revoke old preview if exists)
                    if (this.finishForm?.preview) {
                        URL.revokeObjectURL(this.finishForm.preview);
                    }

                    this.finishForm = {
                        solution: '',
                        image: null,
                        preview: null
                    };
                    this.showFinishModal = true;

                    this.lockBodyScroll(true);
                },

                closeFinishModal() {
                    // revoke preview to avoid memory leak
                    if (this.finishForm?.preview) {
                        URL.revokeObjectURL(this.finishForm.preview);
                    }

                    this.showFinishModal = false;
                    this.lockBodyScroll(false);
                },

                previewImage(e) {
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;

                    if (!file.type.startsWith('image/')) {
                        Swal.fire('Invalid File', 'Please select an image file.', 'warning');
                        e.target.value = '';
                        return;
                    }

                    if (this.finishForm.preview) {
                        URL.revokeObjectURL(this.finishForm.preview);
                    }

                    this.finishForm.image = file;
                    this.finishForm.preview = URL.createObjectURL(file);
                    e.target.value = '';
                },

                clearImage() {
                    if (this.finishForm.preview) {
                        URL.revokeObjectURL(this.finishForm.preview);
                    }

                    this.finishForm.image = null;
                    this.finishForm.preview = null;

                    if (this.$refs.finishFile) this.$refs.finishFile.value = '';
                    if (this.$refs.finishFileReplace) this.$refs.finishFileReplace.value = '';
                },

                triggerFilePicker() {
                    if (this.finishForm.preview) {
                        this.$refs.finishFileReplace?.click();
                    } else {
                        this.$refs.finishFile?.click();
                    }
                },

                lockBodyScroll(isLocked) {
                    document.body.style.overflow = isLocked ? 'hidden' : '';
                },


                async submitFinishTicket() {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('solution', this.finishForm.solution);

                    if (this.finishForm.image) {
                        formData.append('solution_image', this.finishForm.image);
                    }

                    const response = await fetch(
                        `/tickets/${this.finishTicketId}/close`, {
                            method: 'POST',
                            body: formData
                        }
                    );

                    if (!response.ok) {
                        Swal.fire('Error', 'Failed to close ticket', 'error');
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Ticket Closed',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
                },

                async openShowModal(ticketId) {
                    this.loading = true;
                    this.showShowModal = true;

                    try {
                        // Fetch ticket data as JSON instead of HTML
                        const response = await fetch(`/tickets/${ticketId}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Error response:', errorText);
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const ticket = await response.json();

                        // Store ticket data for display
                        // this.showData = ticket;
                        this.showData = {
                            ...ticket,
                            notes: ticket.notes || []
                        };
                    } catch (error) {
                        console.error('Error fetching ticket:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load ticket details.'
                        });
                        this.showShowModal = false;
                    } finally {
                        this.loading = false;
                    }
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showShowModal = false;
                    this.errors = {};
                },

                async submitCreate() {
                    // Show confirmation dialog
                    const result = await Swal.fire({
                        title: 'Create Ticket?',
                        text: 'Are you sure you want to create this ticket?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#14b8a6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, create it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (!result.isConfirmed) {
                        return; // User cancelled
                    }

                    this.errors = {};
                    this.isSubmitting = true; // Start loading

                    try {
                        const response = await fetch("{{ route('tickets.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.createFormData)
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Ticket created successfully.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'An error occurred.'
                                });
                            }
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.'
                        });
                    } finally {
                        this.isSubmitting = false; // Stop loading
                    }
                },

                async submitEdit() {
                    // Show confirmation dialog
                    const result = await Swal.fire({
                        title: 'Update Ticket?',
                        text: 'Are you sure you want to update this ticket?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#14b8a6',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel'
                    });

                    if (!result.isConfirmed) {
                        return; // User cancelled
                    }

                    this.errors = {};
                    this.isSubmitting = true; // Start loading

                    try {
                        const response = await fetch(`/tickets/${this.editFormData.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title: this.editFormData.title,
                                description: this.editFormData.description,
                                category_id: this.editFormData.category_id,
                                location_id: this.editFormData.location_id
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.closeModals();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Ticket updated successfully.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'An error occurred.'
                                });
                            }
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.'
                        });
                    } finally {
                        this.isSubmitting = false; // Stop loading
                    }
                },
                //add notes
                addNote(ticketId) {
                    Swal.fire({
                        title: 'Tambah Catatan',
                        text: 'Berikan alasan kenapa ticket ini belum dikerjakan.',
                        input: 'textarea',
                        inputPlaceholder: 'Contoh: Sparepart belum tersedia, perbaikan dijadwalkan besok.',
                        showCancelButton: true,
                        confirmButtonText: 'Simpan Note',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#14b8a6',
                        cancelButtonColor: '#6b7280',
                        preConfirm: (value) => {
                            if (!value || !value.trim()) {
                                Swal.showValidationMessage('Catatan tidak boleh kosong');
                            }
                            return value;
                        }
                    }).then(async (result) => {
                        if (!result.isConfirmed) return;

                        try {
                            const response = await fetch(`/tickets/${ticketId}/notes`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    note: result.value
                                })
                            });

                            const data = await response.json();

                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Gagal menyimpan catatan.');
                            }

                            // ✅ Langsung update UI tanpa reload
                            if (!this.showData.notes) {
                                this.showData.notes = [];
                            }
                            this.showData.notes.unshift(data.note);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Catatan berhasil ditambahkan.',
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Terjadi kesalahan saat menyimpan catatan.'
                            });
                        }
                    });
                }

            }
        }
    </script>
    {{-- ✅ SweetAlert Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ✅ Global Success Alert
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('
                success ') }}',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            @endif

            // 🗑️ Delete
            document.querySelectorAll('.delete-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Delete this ticket?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // 🔧 Handle
            document.querySelectorAll('.handle-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Start Handling?',
                        text: 'You will take responsibility for this ticket.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Handle it',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6b7280'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            //  Close Ticket (solution input)
            // document.querySelectorAll('.close-ticket-btn').forEach(button => {
            //     button.addEventListener('click', e => {
            //         // const button = e.target.closest('.close-ticket-btn');
            //         // if (!button) return;
            //
            //         e.preventDefault();
            //
            //         const actionUrl = button.dataset.action;
            //
            //         Swal.fire({
            //             title: 'Finish Ticket',
            //             html: `
            //                 <textarea id="solution" class="swal2-textarea" placeholder="Enter solution..."></textarea>
            //                 <input type="file" id="solution_image" class="swal2-file" accept="image/*">
            //             `,
            //             // text: 'Please describe your solution before closing.',
            //             // input: 'textarea',
            //             // inputPlaceholder: 'Enter solution here...',
            //             showCancelButton: true,
            //             confirmButtonText: 'Submit Solution',
            //             confirmButtonColor: '#16a34a',
            //             cancelButtonColor: '#6b7280',
            //             preConfirm: () => {
            //                 const solution = document.getElementById('solution').value;
            //                 const image = document.getElementById('solution_image').files[0];
            //
            //                 if(!solution.trim()){
            //                     Swal.showValidationMessage('Solution is required');
            //                     return false;
            //                 }
            //
            //                 const formData = new FormData();
            //                 formData.append('_token', '{{ csrf_token() }}');
            //                 formData.append('solution', solution);
            //                 if (image) formData.append('solution_image', image);
            //
            //                 return fetch(actionUrl, {
            //                     method: 'POST',
            //                     body: formData
            //                 });
            //
            //                 if(!response.ok){
            //                     throw new Error('Failed to close ticket');
            //                 }
            //
            //                 return response;
            //
            //             }
            //         }).then(result => {
            //             // if (result.isConfirmed) {
            //             //     const form = document.createElement('form');
            //             //     form.method = 'POST';
            //             //     form.action = actionUrl;
            //             //     const token = document.createElement('input');
            //             //     token.type = 'hidden';
            //             //     token.name = '_token';
            //             //     token.value = '{{ csrf_token() }}';
            //             //     const solution = document.createElement('input');
            //             //     solution.type = 'hidden';
            //             //     solution.name = 'solution';
            //             //     solution.value = result.value;
            //             //     form.append(token, solution);
            //             //     document.body.append(form);
            //             //     form.submit();
            //             // }
            //             if (result.isConfirmed) {
            //                 Swal.fire({
            //                     icon: 'success',
            //                     title: 'Ticket Closed',
            //                     timer: 1500,
            //                     showConfirmButton: false
            //                 }).then(() => window.location.reload());
            //             }
            //         });
            //     });
            // });

            // ⬆Escalate
            document.querySelectorAll('.escalate-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const actionUrl = button.dataset.action;
                    Swal.fire({
                        title: 'Escalate Ticket?',
                        text: 'This will forward the issue to higher support.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#9333ea',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, escalate it'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = actionUrl;
                            const token = document.createElement('input');
                            token.type = 'hidden';
                            token.name = '_token';
                            token.value = '{{ csrf_token() }}';
                            form.appendChild(token);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // Cancel Ticket (new polish)
            document.querySelectorAll('.cancel-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Cancel Handling?',
                        text: 'This ticket will be reopened and available for other support members.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, release it'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            document.querySelectorAll('.take-over-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Take Over Ticket?',
                        text: 'This ticket will be taken over by you.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, take over it'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            document.querySelectorAll('.handle-escalated-ticket-btn').forEach(button => {
                button.addEventListener('click', e => {
                    e.preventDefault();
                    const form = button.closest('form');
                    Swal.fire({
                        title: 'Handle Escalated Ticket?',
                        text: 'This ticket will be handled by you.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, handle it'
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

        });

        //search
        function ticketSearch() {
            return {
                search: "{{ $search ?? '' }}",
                loading: false,
                timeout: null,

                init() {
                    this.$watch('search', value => {
                        clearTimeout(this.timeout)
                        this.loading = true

                        this.timeout = setTimeout(() => {
                            document.getElementById('searchForm').submit()
                        }, 400)
                    })
                },

                clearSearch() {
                    this.search = ""
                    this.loading = true
                    setTimeout(() => {
                        document.getElementById('searchForm').submit()
                    }, 200)
                }
            }
        }


        //filters

        function premiumFilter() {
            return {
                status: "{{ $filters['status'] ?? '' }}",
                priority: "{{ $filters['priority'] ?? '' }}",
                category: "{{ $filters['category'] ?? '' }}",
                sort: "{{ request('sort') ?? '' }}",

                statusList: [{
                        label: "All",
                        value: ""
                    },
                    {
                        label: "Open",
                        value: "Open"
                    },
                    {
                        label: "In Progress",
                        value: "In Progress"
                    },
                    {
                        label: "Closed",
                        value: "Closed"
                    },
                ],

                priorityList: [{
                        label: "All",
                        value: ""
                    },
                    {
                        label: "Low",
                        value: "Low"
                    },
                    {
                        label: "Medium",
                        value: "Medium"
                    },
                    {
                        label: "High",
                        value: "High"
                    },
                ],

                categoryList: [{
                        label: "All",
                        value: ""
                    },
                    {
                        label: "Hardware",
                        value: "Hardware"
                    },
                    {
                        label: "Software",
                        value: "Software"
                    },
                    {
                        label: "Network",
                        value: "Network"
                    },
                    {
                        label: "Other",
                        value: "Other"
                    },
                ],

                sortList: [{
                        label: "Newest First",
                        value: ""
                    },
                    {
                        label: "Oldest First",
                        value: "oldest"
                    },
                ],

                get activeCount() {
                    let count = 0;
                    if (this.status) count++;
                    if (this.priority) count++;
                    if (this.category) count++;
                    if (this.sort) count++;
                    return count;
                },

                submitFilters() {
                    setTimeout(() => {
                        document.getElementById("premiumFilterForm").submit();
                    }, 200);
                },

                clearAll() {
                    // this.status = '';
                    // this.priority = '';
                    // this.category = '';
                    // this.sort = '';
                    // this.submitFilters();
                    // document.getElementById('filter-search').value = '';
                    window.location.href = "{{ route('tickets.index') }}";
                }
            }
        }
    </script>



    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</x-app-layout>
