<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-2xl text-gray-800 tracking-tight">
                Admin Dashboard
            </h2>
            <p class="text-sm text-gray-500">Welcome back, Admin 👋</p>
        </div>
    </x-slot>

    <div class="py-10 px-6 max-w-7xl mx-auto space-y-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- CARD ITEM --}}
            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-700 text-xl">
                    📊
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Tickets</p>
                    <h3 class="text-3xl font-semibold">{{ $totalTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-green-100 text-green-700 text-xl">
                    ✅
                </div>
                <div>
                    <p class="text-sm text-gray-500">Closed Tickets</p>
                    <h3 class="text-3xl font-semibold">{{ $closedTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-700 text-xl">
                    🕓
                </div>
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <h3 class="text-3xl font-semibold">{{ $pendingTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-indigo-100 text-indigo-700 text-xl">
                    🔓
                </div>
                <div>
                    <p class="text-sm text-gray-500">Open Tickets</p>
                    <h3 class="text-3xl font-semibold">{{ $openTickets }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-700 text-xl">
                    👥
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Users</p>
                    <h3 class="text-3xl font-semibold">{{ $totalUsers }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-4 border rounded-xl bg-white shadow-sm p-5">
                <div class="p-3 rounded-lg bg-pink-100 text-pink-700 text-xl">
                    ⏱️
                </div>
                <div>
                    <p class="text-sm text-gray-500">Avg SLA Resolution</p>
                    <h3 class="text-xl font-semibold">{{ $avgSlaFormatted }}</h3>
                </div>
            </div>

        </div>
        {{-- =========================== --}}
        {{-- CHART SECTION --}}
        {{-- =========================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- TICKET TREND --}}
            <div class=" border rounded-xl bg-white shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Ticket Trend</h3>
                        <p class="text-sm text-gray-500">Last 30 days activity</p>
                    </div>
                </div>
                <div class="w-full max-w-lg mx-auto h-72">
                    <canvas id="ticketTrendChart"></canvas>
                </div>
            </div>

            {{-- SLA CHART --}}
            <div class="border rounded-xl bg-white shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800">SLA by Category</h3>
                <p class="text-sm text-gray-500 mb-4">Average resolution duration</p>

                {{-- <canvas id="slaChart" height="180" class="pt-4"></canvas> --}}
                <div class="w-full max-w-lg mx-auto h-72">
                    <canvas id="slaChart"></canvas>
                </div>
            </div>

        </div>



        {{-- =========================== --}}
        {{-- RECENT TICKETS TABLE --}}
        {{-- =========================== --}}
        <div class="border rounded-xl bg-white shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Tickets</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b text-gray-600">
                        <tr>
                            <th class="p-3 text-left">User</th>
                            <th class="p-3 text-left">Title</th>
                            <th class="p-3 text-left">Category</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Created</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-3">{{ $ticket->user->name }}</td>
                                <td class="p-3">{{ $ticket->title }}</td>
                                <td class="p-3">{{ $ticket->category_name }}</td>
                                <td class="p-3">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs
                                    @if ($ticket->status == 'Open') bg-blue-100 text-blue-700
                                    @elseif($ticket->status == 'In Progress') bg-yellow-100 text-yellow-700
                                    @else bg-green-100 text-green-700 @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="p-3">{{ $ticket->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>



    {{-- =========================== --}}
    {{-- CHART JS --}}
    {{-- =========================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Ticket Trend Chart
        new Chart(document.getElementById('ticketTrendChart'), {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                        label: "Tickets Created",
                        data: @json($trendCreated),
                        borderColor: "#2563eb",
                        backgroundColor: "rgba(37,99,235,0.15)",
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: "Tickets Closed",
                        data: @json($trendClosed),
                        borderColor: "#059669",
                        backgroundColor: "rgba(5,150,105,0.15)",
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        position: "bottom"
                    }
                },
                responsive: true,
            }
        });


        // // SLA Chart
        // new Chart(document.getElementById('slaChart'), {
        //     type: 'bar',
        //     data: {
        //         labels: @json($slaCategories),
        //         datasets: [{
        //             data: @json($slaDurations),
        //             backgroundColor: "#6366f1",
        //             borderRadius: 6
        //         }]
        //     },
        //     options: { plugins: { legend: { display: false } } }
        // });

        function formatDuration(totalMinutes) {
            const days = Math.floor(totalMinutes / 1440);
            const hours = Math.floor((totalMinutes % 1440) / 60);
            const minutes = totalMinutes % 60;

            let result = [];

            if (days > 0) result.push(`${days} hari`);
            if (hours > 0) result.push(`${hours} jam`);
            if (minutes > 0) result.push(`${minutes} menit`);

            return result.join(" ");
        }

        new Chart(document.getElementById('slaChart'), {
            type: 'bar',
            data: {
                labels: @json($slaCategories),
                datasets: [{
                    label: "Avg SLA",
                    data: @json($slaDurations), // masih dalam menit
                    backgroundColor: "#818cf8",
                    borderRadius: 8,
                    barThickness: 40,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return " " + formatDuration(value);
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 10,
                        left: 0,
                        right: 0
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return formatDuration(value);
                            }
                        }
                    }
                }
            }
        });
    </script>

</x-app-layout>
