<!-- Sidebar -->
<div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
    class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform
           bg-gray-900 text-white shadow-lg lg:translate-x-0 lg:static lg:inset-0">

    {{-- Wadah Flexbox Vertikal Utama --}}
    <div class="flex flex-col h-full">

        <!-- Bagian Atas: Logo -->
        <div>
            <div class="flex flex-col items-center justify-center mt-8">
                <div class="p-5 rounded-xl shadow-inner">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg> --}}
                    <img src="{{ asset('images/logo3.png') }}" alt="IT Support Logo"
                        class="object-contain h-10 w-10 sm:h-12 sm:w-12 lg:h-16 lg:w-16" width="100%" height="100%"
                        loading="eager" decoding="async">
                </div>
                <h1 class="mt-3 text-2xl font-bold tracking-wide text-white">IT Support</h1>
                <p class="text-sm text-gray-400">Helpdesk</p>
                <p class="text-sm text-gray-400">RSU Kertha Usada</p>
            </div>
            <div class="mt-6 border-t border-gray-700/50"></div>
        </div>

        <!-- Bagian Tengah: Navigasi (Bisa di-scroll) -->
        <nav class="mt-6 flex-1 px-4 space-y-3 overflow-y-auto">

            {{-- @php
                function is_active($routeNames)
                {
                    return in_array(request()->route()->getName(), (array) $routeNames);
                }
            @endphp --}}

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"></path>
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>

            {{-- @hasanyrole('admin|support|manager') --}}
            @can('view-handbook')
                <a href="{{ route('handbook.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200
                    {{ request()->routeIs('handbook.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">

                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 576 512">
                        <path
                            d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H480c17.7 0 32-14.3 32-32V64c0-35.3-28.7-64-64-64H96zm64 96h256c8.8 0 16 7.2 16 16s-7.2 16-16 16H160c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64h256c8.8 0 16 7.2 16 16s-7.2 16-16 16H160c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64h128c8.8 0 16 7.2 16 16s-7.2 16-16 16H160c-8.8 0-16-7.2-16-16s7.2-16 16-16z" />
                    </svg>

                    <span class="text-sm font-medium">Handbook/SOP</span>
                </a>
            @endcan
            {{-- @endhasanyrole --}}

            <!-- Tickets -->
            <a href="{{ route('tickets.index') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ is_active(['tickets.index', 'tickets.create', 'tickets.show', 'tickets.edit']) ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                    </path>
                </svg>
                <span class="text-sm">Tickets</span>
            </a>

            <!-- Tasks Dropdown -->
            @hasanyrole('admin|support|manager')
                <div x-data="{ open: {{ request()->routeIs('tasks.*') ? 'true' : 'false' }} }" class="mt-2">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition
               {{ request()->routeIs('tasks.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                            </svg>
                            <span class="text-sm">Tasks</span>
                        </span>
                        <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-1 ml-9 space-y-1">
                        <a href="{{ route('tasks.daily') }}"
                            class="block px-3 py-2 rounded-md text-sm
                 {{ request()->routeIs('tasks.daily') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Daily Tasks
                        </a>
                        <a href="{{ route('tasks.monthly') }}"
                            class="block px-3 py-2 rounded-md text-sm
                 {{ request()->routeIs('tasks.monthly') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Monthly Tasks
                        </a>
                    </div>
                </div>
            @endhasanyrole

            <!-- Reports Dropdown -->
            @hasanyrole('admin|support|manager')
                <div x-data="{ open: {{ request()->routeIs('repots.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition
                        {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm">Reports</span>
                        </span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-transition class="mt-1 ml-9 space-y-1">
                        <a href="{{ route('reports.daily') }}"
                            class="block px-3 py-2 rounded-md text-sm
                 {{ request()->routeIs('reports.daily') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Daily Report
                        </a>
                        @can('view-monthly-reports')
                            <a href="{{ route('reports.monthly') }}"
                                class="block px-3 py-2 rounded-md text-sm
                                {{ request()->routeIs('reports.monthly') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                                Monthly Report
                            @endcan
                        </a>
                    </div>
                </div>
            @endhasanyrole

            @hasanyrole('admin|manager')
                <a href="{{ route('ticket.log') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200
        {{ request()->routeIs('ticket.log') ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">

                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <span class="text-sm">Activity Log</span>
                </a>
            @endhasanyrole
        </nav>

        <!-- User Profile -->
        <div class="px-4 pb-6 border-t border-gray-700/50">
            <div class="relative mt-6" x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between p-2 rounded-lg bg-gray-800 hover:bg-gray-700 focus:outline-none transition">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 rounded-full object-cover bg-emerald-500 p-1"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                    </div>
                    <svg class="h-5 w-5 text-gray-300 transition-transform duration-200"
                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
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
