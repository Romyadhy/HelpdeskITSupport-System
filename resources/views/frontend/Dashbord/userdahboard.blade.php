{{-- <x-app-layout> --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Welcome section --}}
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-teal-600">Welcome, {{ auth()->user()->name }} 👋</h1>
                <p class="text-gray-500 mt-2 text-lg">
                    Selamat datang di sistem layanan pengaduan online.<br>
                    Laporkan permasalahan teknis Anda dengan mudah, dan tim IT siap membantu.
                </p>
            </div>

            {{-- Info summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-teal-500">
                    <i class="fas fa-folder-open text-teal-500 text-3xl mb-2"></i>
                    <p class="text-gray-500 text-sm">Open Tickets</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $openTicketsCount ?? 0 }}</h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-yellow-500">
                    <i class="fas fa-spinner text-yellow-500 text-3xl mb-2"></i>
                    <p class="text-gray-500 text-sm">In Progress</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $inProgressTicketsCount ?? 0 }}</h3>
                </div>

                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-green-500">
                    <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                    <p class="text-gray-500 text-sm">Closed</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $closedTicketsCount ?? 0 }}</h3>
                </div>
            </div>

            {{-- CTA Section --}}
            <div class="text-center mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                    Ada masalah yang ingin kamu laporkan?
                </h2>
                <a href="{{ route('tickets.index') }}" 
                   class="inline-flex items-center bg-teal-500 text-white font-medium px-6 py-3 rounded-lg shadow hover:bg-teal-600 transition">
                    <i class="fas fa-plus mr-2"></i> Buat Tiket Baru
                </a>
            </div>

        </div>
    </div>
{{-- </x-app-layout> --}}
