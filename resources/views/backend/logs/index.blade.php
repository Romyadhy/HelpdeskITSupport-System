<x-app-layout>

<div class="container mx-auto mt-6">

    <h1 class="text-2xl font-bold mb-6">Ticket Activity Log</h1>

    <div class="space-y-6">

        @foreach($logs as $log)

            @php
                $props = $log->properties ?? [];
                $attributes = $props['attributes'] ?? [];
                $old = $props['old'] ?? [];

                $ticketId = $log->subject->id ?? ($props['ticket_id'] ?? null);
                $ticketTitle = $log->subject->title ?? ($props['ticket_title'] ?? null);

                $displayOnly = [
                    'title','description','status','priority',
                    'category_id','location_id','assigned_to','solution'
                ];
            @endphp

            <div class="bg-white shadow-sm rounded-xl p-5 border border-gray-200">
                
                {{-- Header --}}
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-3">

                        {{-- Icon --}}
                        <div class="w-10 h-10 flex items-center justify-center 
                            rounded-full bg-gray-100 text-gray-600">
                            @if($log->event === 'created')
                                <i class="fa-solid fa-plus"></i>
                            @elseif($log->event === 'deleted')
                                <i class="fa-solid fa-trash"></i>
                            @else
                                <i class="fa-solid fa-pen"></i>
                            @endif
                        </div>

                        <div>
                            <div class="font-semibold text-lg capitalize">
                                {{ $log->description }}
                            </div>
                            <div class="text-xs text-gray-500">
                                oleh <b>{{ optional($log->causer)->name ?? 'System' }}</b> •
                                {{ $log->created_at->setTimezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                            </div>
                        </div>
                    </div>

                    {{-- Ticket Info --}}
                    <div class="text-right">
                        @if($ticketId)
                            <div class="font-semibold text-teal-700">Ticket #{{ $ticketId }}</div>
                            <div class="text-xs text-gray-500">{{ $ticketTitle }}</div>
                        @else
                            <div class="text-xs text-gray-400 italic">— Ticket tidak ditemukan</div>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                {{-- Detail perubahan --}}
                <div x-data="{ open:false }">

                    <button @click="open = !open" 
                        class="text-blue-600 font-semibold text-sm">
                        Lihat Detail Perubahan
                    </button>

                    <div x-show="open" class="mt-3 space-y-4">

                        {{-- Baru --}}
                        @if(!empty($attributes))
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                            <div class="font-semibold text-green-700 mb-2">Perubahan Baru</div>

                            <ul class="text-sm text-green-800 space-y-1">
                                @foreach($attributes as $key => $value)
                                    @if(in_array($key, $displayOnly))
                                        <li><b>{{ ucfirst(str_replace('_',' ',$key)) }}:</b> {{ $value }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Lama --}}
                        @if(!empty($old))
                        <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                            <div class="font-semibold text-red-700 mb-2">Sebelumnya</div>

                            <ul class="text-sm text-red-800 space-y-1">
                                @foreach($old as $key => $value)
                                    @if(in_array($key, $displayOnly))
                                        <li><b>{{ ucfirst(str_replace('_',' ',$key)) }}:</b> {{ $value }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif

                    </div>

                </div>
            </div>

        @endforeach

        <div class="mt-4">
            {{ $logs->links() }}
        </div>

    </div>

</div>

</x-app-layout>
