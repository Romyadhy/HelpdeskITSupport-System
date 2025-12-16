<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
    class="fixed inset-y-0 left-0 z-30 transition-all duration-300 transform bg-gray-900 text-white shadow-lg lg:translate-x-0 lg:static lg:inset-0 overflow-hidden flex-shrink-0"
    :class="sidebarExpanded ? 'w-72 min-w-[18rem] max-w-[18rem]' : 'w-24 min-w-[6rem] max-w-[6rem]'">

    {{-- Wadah Flexbox Vertikal Utama --}}
    <div class="flex flex-col h-full min-w-0">

        <!-- Bagian Atas: Logo & Toggle -->
        <div class="flex items-center justify-between h-20 px-4 border-b border-gray-800 flex-shrink-0 min-w-0">
            <!-- Logo -->
            <div class="flex items-center justify-center w-full min-w-0">
                <div x-show="sidebarExpanded" class="flex flex-col items-center transition-opacity duration-300 min-w-0">
                    <h1 class="text-xl font-bold tracking-wide text-white truncate w-full text-center">IT Support</h1>
                    <p class="text-xs text-gray-400 truncate w-full text-center">Helpdesk System</p>
                </div>

                <div x-show="!sidebarExpanded" class="transition-opacity duration-300">
                    <img src="{{ asset('images/logoKU.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                </div>
            </div>
        </div>

        <!-- Toggle Button (Desktop Only) -->
        <div class="hidden lg:flex justify-end px-4 py-3 flex-shrink-0">
            <button @click="sidebarExpanded = !sidebarExpanded" class="text-gray-400 hover:text-white focus:outline-none transition-colors">
                <i :class="sidebarExpanded ? 'fas fa-chevron-left' : 'fas fa-chevron-right'" class="text-sm"></i>
            </button>
        </div>

        <!-- Bagian Tengah: Navigasi -->
        <nav class="flex-1 px-3 space-y-2 overflow-y-auto mt-2 no-scrollbar min-w-0">
            <style>
                .no-scrollbar::-webkit-scrollbar { display: none; }
                .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            </style>

            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}"
                class="relative flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <svg class="w-5 h-5 flex-shrink-0 transition-all duration-200"
                    :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'"
                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"></path>
                </svg>

                <span x-show="sidebarExpanded"
                      class="text-sm font-medium transition-opacity duration-200 truncate flex-1 min-w-0">
                    Dashboard
                </span>

                <!-- Tooltip -->
                <div x-show="!sidebarExpanded"
                     class="absolute left-full top-1/2 transform -translate-y-1/2 ml-3 px-3 py-2 bg-gray-900
                     text-white text-xs font-medium rounded-md shadow-xl opacity-0 group-hover:opacity-100
                     transition-opacity duration-200 pointer-events-none z-50 whitespace-nowrap border border-gray-700">
                    Dashboard
                    <div class="absolute top-1/2 right-full transform -translate-y-1/2 -mr-1
                                border-4 border-transparent border-r-gray-900"></div>
                </div>
            </a>

            <!-- HANDBOOK -->
            @can('view-handbook')
            <a href="{{ route('handbook.index') }}"
                class="relative flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('handbook.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'
                                                     : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <svg class="w-5 h-5 flex-shrink-0 transition-all duration-200"
                    :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'"
                    fill="currentColor" viewBox="0 0 576 512">
                    <path d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H480c17.7 0 32-14.3
                    32-32V64c0-35.3-28.7-64-64-64H96zm64 96h256c8.8 0 16 7.2 16 16s-7.2
                    16-16 16H160c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path>
                </svg>

                <span x-show="sidebarExpanded"
                      class="text-sm font-medium transition-opacity duration-200 truncate flex-1 min-w-0">
                    Handbook
                </span>

            </a>
            @endcan

            <!-- TICKETS -->
            <a href="{{ route('tickets.index') }}"
                class="relative flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('tickets.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'
                                                    : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <svg class="w-5 h-5 flex-shrink-0 transition-all duration-200"
                    :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2
                            2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                    </path>
                </svg>

                <span x-show="sidebarExpanded"
                      class="text-sm font-medium transition-opacity duration-200 truncate flex-1 min-w-0">
                    Pelaporan Masalah
                </span>
            </a>

            <!-- TASKS -->
            @hasanyrole('admin|support|manager')
            <div x-data="{ open: {{ request()->routeIs('tasks.*') ? 'true' : 'false' }} }" class="relative">

                <button @click="open = !open; if(!sidebarExpanded) sidebarExpanded = true"
                    class="relative w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition group
                    {{ request()->routeIs('tasks.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'
                                                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                    <span class="flex items-center flex-1 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all duration-200"
                            :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002
                                2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9
                                5a2 2 0 002 2h2a2 2 0 002-2">
                            </path>
                        </svg>

                        <span x-show="sidebarExpanded"
                              class="text-sm font-medium transition-opacity duration-200 truncate">
                            Tugas-tugas
                        </span>
                    </span>

                    <i x-show="sidebarExpanded" :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"
                       class="text-[10px] transition-transform"></i>
                </button>

                <div x-show="open && sidebarExpanded" x-transition
                     class="mt-1 space-y-1 ml-9">
                    <a href="{{ route('tasks.daily') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('tasks.daily')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Tugas Harian
                    </a>

                    <a href="{{ route('tasks.monthly') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('tasks.monthly')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Tugas Bulanan
                    </a>
                </div>
            </div>
            @endhasanyrole

            <!-- REPORTS -->
            @hasanyrole('admin|support|manager')
            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }" class="relative">

                <button @click="open = !open; if(!sidebarExpanded) sidebarExpanded = true"
                    class="relative w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition group
                    {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'
                                                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                    <span class="flex items-center flex-1 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 transition-all"
                            :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'"
                            fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-2m3 2v-4m3 4v-6m2
                                10H7a2 2 0 01-2-2V5a2 2 0
                                012-2h5.586a1 1 0 01.707.293l5.414
                                5.414a1 1 0 01.293.707V19a2 2 0
                                01-2 2z"/>
                        </svg>

                        <span x-show="sidebarExpanded"
                              class="text-sm font-medium transition-opacity duration-200 truncate">
                            Laporan
                        </span>
                    </span>

                    <i x-show="sidebarExpanded"
                       :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"
                       class="text-[10px] transition-transform"></i>
                </button>

                <div x-show="open && sidebarExpanded" x-transition class="mt-1 space-y-1 ml-9">

                    <a href="{{ route('reports.daily') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('reports.daily')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Laporan Harian
                    </a>

                    @can('view-monthly-reports')
                    <a href="{{ route('reports.monthly') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('reports.monthly')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Laporan Bulanan
                    </a>
                    @endcan
                </div>
            </div>
            @endhasanyrole

            <!-- ADMIN -->
            @role('admin')
            <div x-data="{ open: {{ request()->routeIs('admin.*') ? 'true' : 'false' }} }" class="relative">

                <button @click="open = !open; if(!sidebarExpanded) sidebarExpanded = true"
                    class="relative w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition group
                    {{ request()->routeIs('admin.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'
                                                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                    <span class="flex items-center flex-1 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0 transition"
                            :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'"
                            fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756
                                2.924-1.756 3.35 0a1.724
                                1.724 0 002.573 1.066c1.543-.94
                                3.31.826 2.37 2.37a1.724 1.724 0
                                001.065 2.572c1.756.426 1.756
                                2.924 0 3.35a1.724 1.724 0
                                00-1.066 2.573c.94 1.543-.826
                                3.31-2.37 2.37a1.724 1.724 0
                                00-2.572 1.065c-.426 1.756-2.924
                                1.756-3.35 0a1.724 1.724 0
                                00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724
                                1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924
                                0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31
                                2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>

                        <span x-show="sidebarExpanded"
                              class="text-sm font-medium transition-opacity duration-200 truncate">
                            Admin
                        </span>
                    </span>

                    <i x-show="sidebarExpanded" :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"
                       class="text-[10px] transition-transform"></i>
                </button>

                <div x-show="open && sidebarExpanded" x-transition class="mt-1 space-y-1 ml-9">

                    <a href="{{ route('admin.users.index') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('admin.users.*')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Management User
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('admin.categories.*')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Kategori Masalah
                    </a>

                    <a href="{{ route('admin.locations.index') }}"
                       class="block px-3 py-2 rounded-lg text-xs font-medium
                       {{ request()->routeIs('admin.locations.*')
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Lokasi Masalah
                    </a>
                </div>
            </div>
            @endrole

            <!-- ACTIVITY LOG -->
            @hasanyrole('admin|manager')
            <a href="{{ route('activity.log') }}"
                class="relative flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 group
                {{ request()->routeIs('activity.log') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30'
                                                      : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">

                <svg class="w-5 h-5 flex-shrink-0 transition-all"
                    :class="!sidebarExpanded ? 'mx-auto' : 'mr-3'"
                    fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <span x-show="sidebarExpanded"
                      class="text-sm font-medium transition-opacity duration-200 truncate flex-1 min-w-0">
                    Activity Log
                </span>
            </a>
            @endhasanyrole
        </nav>

        <!-- User Profile -->
        <div class="px-4 pb-6 border-t border-gray-700/50 flex-shrink-0 min-w-0">
            <div class="mt-6 relative" x-data="{ open: false }">

                <button @click="open = !open; if(!sidebarExpanded) sidebarExpanded = true"
                    class="w-full flex items-center p-2 rounded-xl bg-gray-800 hover:bg-gray-700
                           focus:outline-none transition group"
                    :class="sidebarExpanded ? 'justify-between' : 'justify-center'">

                    <div class="flex items-center min-w-0">
                        <svg class="h-9 w-9 rounded-full object-cover bg-emerald-500 p-1 flex-shrink-0"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0
                                00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>

                        <span x-show="sidebarExpanded"
                              class="ml-3 text-sm font-medium text-white truncate flex-1 min-w-0">
                            {{ Auth::user()->name }}
                        </span>
                    </div>

                    <svg x-show="sidebarExpanded"
                        class="h-4 w-4 text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open"
                    @click.away="open = false"
                    x-transition
                    class="absolute bottom-full right-0 w-full mb-2 bg-white dark:bg-gray-800
                           rounded-md shadow-lg z-50">
                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            Keluar
                        </x-dropdown-link>
                    </form>
                </div>
            </div>
        </div>

    </div>
</aside>
