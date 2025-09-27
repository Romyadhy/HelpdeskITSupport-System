    <!--
    Struktur Sidebar:
    - Overlay untuk mobile saat sidebar terbuka
    - Konten sidebar utama
    -->

    <!-- Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black opacity-50 transition-opacity lg:hidden"></div>

    <!-- Konten Sidebar -->
    <div 
        :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" 
        class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
        
        <div class="flex flex-col h-full">
            <!-- Logo dan Judul Aplikasi -->
            <div class="flex items-center justify-center mt-8">
                <div class="flex items-center">
                    <!-- Ganti SVG ini dengan logo aplikasimu jika ada -->
                    <svg class="h-8 w-8 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 20.944a12.02 12.02 0 0010.395-5.228 11.955 11.955 0 013.223-7.712z" />
                    </svg>
                    <span class="ml-2 text-2xl font-semibold text-white">IT Support</span>
                </div>
            </div>

            <!-- Menu Navigasi -->
            <nav class="mt-10 flex-1 px-2">
                <!-- Helper untuk link aktif -->
                @php
                    function is_active($routeNames) {
                        return in_array(request()->route()->getName(), (array) $routeNames);
                    }
                @endphp
                
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </x-slot>
                    {{ __('Dashboard') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('tickets')" :active="is_active(['tickets.index', 'tickets.create'])">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </x-slot>
                    {{ __('Tickets') }}
                </x-sidebar-link>
            </nav>

            <!-- Bagian Profil User (Dipindahkan dari navigation.blade.php) -->
            <div class="px-2 pb-4">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = ! open" class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-700 focus:outline-none">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 rounded-full object-cover bg-emerald-500 p-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="ml-3 text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                        </div>
                        <svg class="h-5 w-5 text-white transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute bottom-full right-0 w-full mb-2 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10"
                         style="display: none;">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
