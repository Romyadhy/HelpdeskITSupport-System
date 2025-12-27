<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🗓️ Laporan Harian Overview</h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary Card --}}
            <div class="bg-white shadow rounded-xl p-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Hi, {{ Auth::user()->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ now()->format('l, d F Y') }}</p>
                    <p class="text-md text-gray-500 pt-1">Laporan harian sebagai bentuk dokumentasi dan akuntabilitas.
                    </p>
                </div>

                @if ($hasReportToday)
                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                    ✅ Sudah Lapor Hari Ini
                </span>
                @else
                @can('create-daily-report')
                <a href="{{ route('reports.daily.create') }}"
                    class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow">
                    + Buat Laporan Harian
                </a>
                @endcan
                @endif
            </div>

            {{-- Statistik kecil --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Total Laporan Bulan Ini</h4>
                    <p class="text-3xl font-bold text-blue-600">{{ $monthlyReportsCount }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Tugas Diselesaikan Hari Ini</h4>
                    <p class="text-3xl font-bold text-green-600">{{ $completedTasksCount }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow text-center">
                    <h4 class="text-gray-600 text-sm">Ticket Ditangani Hari Ini</h4>
                    <p class="text-3xl font-bold text-yellow-600">{{ $handledTicketsCount }}</p>
                </div>
            </div>

            {{-- 🔍 Search & Filter Section --}}
            <div x-data="dailyReportFilter()" class="space-y-4">
                {{-- Search Bar --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">📋 Riwayat Laporan</h3>
                        <p class="text-sm text-gray-500">Cari dan filter laporan harian</p>
                    </div>

                    {{-- Search Input --}}
                    <form id="searchForm" method="GET" action="{{ route('reports.daily') }}" class="w-full sm:w-auto">
                        <div class="relative max-w-sm">
                            <span class="absolute top-3 left-3 text-gray-400">
                                <template x-if="loading">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                </template>
                                <template x-if="!loading">
                                    <i class="fas fa-search"></i>
                                </template>
                            </span>

                            <input type="text" x-model="search" name="search"
                                placeholder="Cari laporan atau nama..."
                                @input.debounce.500ms="submitSearch()"
                                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-white border border-gray-300
                                       text-gray-700 placeholder-gray-400 shadow-sm
                                       focus:border-teal-500 focus:ring-2 focus:ring-teal-400 transition">

                            <button x-show="search.length > 0" type="button" @click="clearSearch()"
                                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>

                            {{-- Preserve other filters --}}
                            <input type="hidden" name="date" x-model="date">
                            <input type="hidden" name="status" x-model="status">
                            <input type="hidden" name="user_id" x-model="userId">
                            <input type="hidden" name="sort" x-model="sort">
                        </div>
                    </form>
                </div>

                {{-- 🌟 Premium Filter Bar --}}
                <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-filter text-teal-500"></i>
                            Filters
                            <template x-if="activeCount > 0">
                                <span class="px-2 py-0.5 text-xs bg-teal-500 text-white rounded-full font-semibold"
                                    x-text="activeCount"></span>
                            </template>
                        </h4>

                        <button x-show="activeCount > 0" @click="clearAll()"
                            class="text-sm text-red-500 hover:text-red-600 underline transition">
                            Clear All
                        </button>
                    </div>

                    <form id="filterForm" method="GET" action="{{ route('reports.daily') }}">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                            {{-- Date Filter --}}
                            <div>
                                <label class="text-sm text-gray-600 mb-1 block">Tanggal</label>
                                <input type="date" x-model="date" name="date"
                                    @change="submitFilters()"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl
                                           focus:border-teal-500 focus:ring-2 focus:ring-teal-400 transition">
                            </div>

                            {{-- Status Filter --}}
                            <div class="relative">
                                <label class="text-sm text-gray-600 mb-1 block">Status Verifikasi</label>
                                <button type="button" @click="statusOpen = !statusOpen"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl
                                           flex justify-between items-center hover:border-teal-500 transition">
                                    <span x-text="statusLabel"></span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </button>

                                <div x-show="statusOpen" @click.outside="statusOpen = false" x-transition
                                    class="absolute z-20 mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-xl p-2">
                                    <template x-for="item in statusList" :key="item.value">
                                        <div @click="status = item.value; statusOpen = false; $nextTick(() => submitFilters())"
                                            class="px-3 py-2 rounded-lg hover:bg-teal-50 cursor-pointer flex items-center justify-between">
                                            <span x-text="item.label"></span>
                                            <i class="fas fa-check text-teal-600" x-show="status === item.value"></i>
                                        </div>
                                    </template>
                                </div>

                                <input type="hidden" name="status" x-model="status">
                            </div>

                            {{-- Sort --}}
                            <div class="relative">
                                <label class="text-sm text-gray-600 mb-1 block">Urutkan</label>
                                <button type="button" @click="sortOpen = !sortOpen"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl
                                           flex justify-between items-center hover:border-teal-500 transition">
                                    <span x-text="sortLabel"></span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </button>

                                <div x-show="sortOpen" @click.outside="sortOpen = false" x-transition
                                    class="absolute z-20 mt-2 w-full bg-white shadow-lg border border-gray-200 rounded-xl p-2">
                                    <template x-for="item in sortList" :key="item.value">
                                        <div @click="sort = item.value; sortOpen = false; $nextTick(() => submitFilters())"
                                            class="px-3 py-2 rounded-lg hover:bg-teal-50 cursor-pointer flex items-center justify-between">
                                            <span x-text="item.label"></span>
                                            <i class="fas fa-check text-teal-600" x-show="sort === item.value"></i>
                                        </div>
                                    </template>
                                </div>

                                <input type="hidden" name="sort" x-model="sort">
                            </div>

                        </div>

                        {{-- Hidden search field --}}
                        <input type="hidden" name="search" x-model="search">
                    </form>
                </div>
            </div>

            {{-- Riwayat laporan --}}
            <div class="bg-white shadow rounded-lg p-6">


                @forelse ($dailyReports as $report)
                <div class="border-b py-4">

                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                @if ($report->user)
                                <span class="text-sm text-gray-500"> oleh {{ $report->user->name }}</span>
                                @endif
                            </p>

                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($report->content, 80) }}</p>

                            <div class="text-xs text-gray-500 mt-2 mb-2">
                                @if ($report->tasks->count())
                                <p><strong>Tasks:</strong> {{ $report->tasks->pluck('title')->join(', ') }}</p>
                                @endif

                                @if ($report->tickets->count())
                                <p><strong>Tickets:</strong> {{ $report->tickets->pluck('title')->join(', ') }}
                                </p>
                                @endif
                            </div>
                        </div>

                        {{-- Status verifikasi --}}
                        @if ($report->verified_at)
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full">Verified</span>
                        @else
                        <span
                            class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Pending</span>
                        @endif
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex gap-3 mt-3">

                        {{-- Export PDF --}}
                        <button onclick="confirmExport('{{ route('reports.daily.pdf', $report->id) }}')"
                            class="inline-flex items-center gap-2 bg-rose-500 text-white px-3 py-1.5 rounded-md text-sm font-medium hover:bg-rose-600 transition">
                            <i class="fas fa-file-pdf text-xs"></i> Export PDF
                        </button>
                        {{-- detail report --}}
                        <a href="{{ route('reports.daily.show', $report->id) }}"
                            class="rounded-md px-1.5 py-1.5 text-white bg-teal-500 hover:bg-teal-600 text-sm transition"><i class="fa-solid fa-circle-info"></i> Lihat
                            Detail
                        </a>
                        {{-- edit --}}
                        @can('edit-daily-report')
                        @if(auth()->id() === $report->user_id && is_null($report->verified_at))
                        <a href="{{ route('reports.daily.edit', $report->id) }}" class="px-1.5 py-1.5 text-white bg-yellow-500 hover:bg-yellow-600 text-sm transition rounded-md"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                        @endif
                        @endcan
                        {{-- delete --}}
                        @can('delete-daily-report')
                        @if(auth()->id() === $report->user_id && is_null($report->verified_at))
                        <form id="delete-report-form-{{ $report->id }}"
                            action="{{ route('reports.daily.destroy', $report) }}"
                            method="POST"
                            class="inline">
                            @csrf
                            @method('DELETE')

                            <button type="button"
                                onclick="confirmDeleteReport({{ $report->id }})"
                                class="px-1.5 py-1.5 text-white bg-red-500 hover:bg-red-600 text-sm transition rounded-md">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </form>
                        @endif
                        @endcan
                        {{-- Verifikasi --}}
                        @if (Auth::user()->hasRole('admin') && !$report->verified_at)
                        <form id="verifyForm-{{ $report->id }}"
                            action="{{ route('reports.daily.verify', $report->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <button type="button" onclick="confirmVerify('verifyForm-{{ $report->id }}')"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-1.5 py-1.5 rounded-md">
                                <i class="fas fa-check mr-1"></i> Verify
                            </button>
                        </form>
                        @endif

                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Belum ada laporan yang dibuat.</p>
                @endforelse
            </div>
            <!-- Modern Pagination with Info -->
            <div class="px-6 py-5 border-t bg-white flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-lg">

                <!-- Left: Showing Info -->
                <div class="text-sm text-gray-600">
                    Showing
                    <span class="font-semibold text-gray-900">{{ $dailyReports->firstItem() }}</span>
                    to
                    <span class="font-semibold text-gray-900">{{ $dailyReports->lastItem() }}</span>
                    of
                    <span class="font-semibold text-gray-900">{{ $dailyReports->total() }}</span>
                    results
                </div>

                <!-- Right: Pagination -->
                <div class="flex items-center space-x-1">

                    {{-- Previous --}}
                    @if ($dailyReports->onFirstPage())
                    <span class="px-3 py-2 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                    @else
                    <a href="{{ $dailyReports->previousPageUrl() }}"
                        class="px-3 py-2 rounded-xl bg-white border border-gray-300
                text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($dailyReports->links()->elements[0] as $page => $url)
                    @if ($page == $dailyReports->currentPage())
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
                    @if ($dailyReports->hasMorePages())
                    <a href="{{ $dailyReports->nextPageUrl() }}"
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

    {{-- SWEETALERT --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('
                success ') }}',
                showConfirmButton: false,
                timer: 2000
            });
            @endif

            @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: '{{ session('
                warning ') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#facc15'
            });
            @endif
        });

        // Export PDF
        function confirmExport(url) {
            Swal.fire({
                title: 'Export ke PDF?',
                text: "Laporan ini akan diunduh dalam format PDF.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Export',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        // Verify
        function confirmVerify(formId) {
            Swal.fire({
                title: 'Verifikasi laporan?',
                text: 'Laporan ini akan ditandai sebagai sudah diverifikasi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // delete
        function confirmDeleteReport(reportId) {
            Swal.fire({
                title: 'Hapus Laporan?',
                text: 'Laporan ini akan dihapus dan tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-report-form-${reportId}`).submit();
                }
            });
        }

        // Daily Report Filter Component
        function dailyReportFilter() {
            const usersData = @json($users ?? []);

            return {
                search: '{{ $filters["search"] ?? "" }}',
                date: '{{ $filters["date"] ?? "" }}',
                status: '{{ $filters["status"] ?? "" }}',
                userId: '{{ $filters["user_id"] ?? "" }}',
                sort: '{{ $filters["sort"] ?? "" }}',
                loading: false,
                users: usersData,
                statusOpen: false,
                sortOpen: false,

                statusList: [{
                        value: '',
                        label: 'Semua'
                    },
                    {
                        value: 'verified',
                        label: 'Verified'
                    },
                    {
                        value: 'pending',
                        label: 'Pending'
                    }
                ],

                sortList: [{
                        value: '',
                        label: 'Terbaru'
                    },
                    {
                        value: 'oldest',
                        label: 'Terlama'
                    }
                ],

                get statusLabel() {
                    const found = this.statusList.find(item => item.value === this.status);
                    return found ? found.label : 'Semua';
                },

                get sortLabel() {
                    const found = this.sortList.find(item => item.value === this.sort);
                    return found ? found.label : 'Terbaru';
                },

                get userLabel() {
                    if (!this.userId) return 'Semua';
                    const found = this.users.find(u => String(u.id) === String(this.userId));
                    return found ? found.name : 'Semua';
                },

                get activeCount() {
                    let count = 0;
                    if (this.search) count++;
                    if (this.date) count++;
                    if (this.status) count++;
                    if (this.userId) count++;
                    if (this.sort) count++;
                    return count;
                },

                submitSearch() {
                    this.loading = true;
                    document.getElementById('searchForm').submit();
                },

                submitFilters() {
                    document.getElementById('filterForm').submit();
                },

                clearSearch() {
                    this.search = '';
                    this.submitFilters();
                },

                clearAll() {
                    this.search = '';
                    this.date = '';
                    this.status = '';
                    this.userId = '';
                    this.sort = '';
                    window.location.href = '{{ route("reports.daily") }}';
                }
            };
        }
    </script>

</x-app-layout>