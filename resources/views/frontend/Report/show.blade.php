<x-app-layout>
    <div x-data="ticketModal()">

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight flex items-center gap-2">
                <i class="fas fa-file-alt text-indigo-600"></i>
                Detail Laporan Harian
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('reports.daily') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm shadow-sm transition">
                    <i class="fas fa-arrow-left text-sm"></i>
                    Kembali
                </a>

                <a href="{{ route('reports.daily.pdf', $report->id) }}"
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-md text-sm shadow">
                    <i class="fas fa-file-pdf text-sm"></i>
                    Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4">

            {{-- CARD UTAMA --}}
            <div class="bg-white rounded-xl shadow-md p-8 space-y-8">

                {{-- Header Info --}}
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-500">Dibuat oleh</p>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $report->user->name }}</h2>

                        <p class="text-sm text-gray-600 mt-1 flex items-center gap-2">
                            <i class="fas fa-calendar text-indigo-500"></i>
                            {{ $report->report_date->setTimezone('Asia/Makassar')->format('l, d F Y') }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Dikirim pada:
                            {{ $report->created_at->setTimezone('Asia/Makassar')->format('d M Y, H:i') }}
                        </p>
                    </div>

                    {{-- Status Verifikasi --}}
                    <div class="text-right">
                        @if ($report->verified_at)
                            <span class="px-4 py-1.5 bg-green-100 text-green-700 text-sm rounded-full font-medium inline-flex items-center gap-2">
                                <i class="fas fa-check-circle"></i> Diverifikasi
                            </span>
                            <p class="text-xs text-gray-500 mt-1">
                                oleh {{ $report->verifier->name ?? 'Admin' }} —
                                {{ $report->verified_at->setTimezone('Asia/Makassar')->format('d M Y, H:i') }}
                            </p>
                        @else
                            <span class="px-4 py-1.5 bg-yellow-100 text-yellow-700 text-sm rounded-full font-medium inline-flex items-center gap-2">
                                <i class="fas fa-clock"></i> Menunggu Verifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <hr class="border-gray-200">

                {{-- SECTION: Isi Laporan --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-align-left text-indigo-600"></i> Isi Laporan
                    </h3>

                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-line leading-relaxed border">
                        {{ $report->content }}
                    </div>
                </div>

                {{-- SECTION: Tugas Diselesaikan --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-tasks text-blue-600"></i> Tugas Harian yang Diselesaikan
                    </h3>

                    @if ($report->tasks->count() > 0)
                        <ul class="space-y-2">
                            @foreach ($report->tasks as $task)
                                <li class="flex items-center gap-2 text-gray-700 text-sm">
                                    <i class="fas fa-check text-green-500"></i>
                                    {{ $task->title }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 italic text-sm">Tidak ada tugas rutin yang dilaporkan.</p>
                    @endif
                </div>

                {{-- SECTION: Tiket Ditangani --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-ticket-alt text-orange-600"></i> Tiket yang Ditangani
                    </h3>

                    @if ($report->tickets->count() > 0)
                        <div class="space-y-4">

                            @foreach ($report->tickets as $ticket)
                                <div @click="openTicket({{ $ticket->id }})" class="cursor-pointer border rounded-xl p-4 bg-gray-50 hover:bg-gray-100 transition shadow-sm">
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-semibold text-gray-800">
                                            #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}. {{ $ticket->title }}
                                        </h4>

                                        <span @class([
                                            'px-3 py-1 text-xs font-semibold rounded-full',
                                            'bg-red-100 text-red-600' => $ticket->status === 'Open',
                                            'bg-yellow-100 text-yellow-700' => $ticket->status === 'In Progress',
                                            'bg-green-100 text-green-700' => $ticket->status === 'Closed',
                                            'bg-purple-100 text-purple-700' => $ticket->status === 'Escalated',
                                        ])>
                                            {{ $ticket->status }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Prioritas: {{ $ticket->priority }}
                                    </p>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <p class="text-gray-500 italic text-sm">Tidak ada tiket yang dilaporkan.</p>
                    @endif
                </div>



                {{-- SECTION: Verifikasi (Admin Only) --}}
                @can('verify-daily-report')
                    @if (!$report->verified_at)
                        <div class="pt-6 text-center">
                            <form method="POST" action="{{ route('reports.daily.verify', $report->id) }}">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg shadow font-semibold inline-flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Verifikasi Laporan Ini
                                </button>
                            </form>
                        </div>
                    @endif
                @endcan

            </div>
        </div>
    </div>
   @include('frontend.Tickets.partials.show-modal')
 </div>
    <script>
        function ticketModal(){
            return {
                showShowModal: false,
                loading: false,
                showData: {},

                async openTicket(ticketId){
                    this.loading = true;
                    this.showShowModal = true;

                    try{
                        const response = await fetch(`/tickets/${ticketId}`, {
                            headers: {'Accept': 'application/json'}
                        });

                        const data = await response.json();
                        this.showData = data;
                    } catch(e) {
                        console.error(e);
                        alert("Failed to load tickets");
                        this.showShowModal = false;
                    }

                    this.loading = false;
                },

                closeModals(){
                    this.showShowModal = false;
                }

            }
        }
    </script>
</x-app-layout>
