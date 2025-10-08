<!-- Sidebar -->
<div 
    :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" 
    class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform 
           bg-gray-900 text-white shadow-lg lg:translate-x-0 lg:static lg:inset-0">

    <div class="flex flex-col h-full">

        <!-- Logo -->
        <div class="flex flex-col items-center justify-center mt-8">
            <div class="bg-emerald-500/20 p-3 rounded-xl shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h1 class="mt-3 text-2xl font-bold tracking-wide text-white">IT Support</h1>
            <p class="text-sm text-gray-400">Helpdesk Dashboard</p>
        </div>

        <!-- Divider -->
        <div class="mt-6 border-t border-gray-700/50"></div>

        <!-- Navigation -->
        <nav class="mt-6 flex-1 px-4 space-y-3">

            @php
                function is_active($routeNames) {
                    return in_array(request()->route()->getName(), (array) $routeNames);
                }
            @endphp

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 
                      {{ request()->routeIs('dashboard') 
                        ? 'bg-emerald-600 text-white shadow-md' 
                        : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6">
                    </path>
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <!-- Tickets -->
            <a href="{{ route('tickets.index') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 
                      {{ is_active(['tickets.index', 'tickets.create']) 
                        ? 'bg-emerald-600 text-white shadow-md' 
                        : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                    </path>
                </svg>
                <span class="text-sm">Tickets</span>
            </a>
           
        </nav>

        <!-- User Profile -->
        <div class="px-4 pb-6 border-t border-gray-700/50">
            <div class="relative mt-6" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="w-full flex items-center justify-between p-2 rounded-lg bg-gray-800 hover:bg-gray-700 focus:outline-none transition">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 rounded-full object-cover bg-emerald-500 p-1" xmlns="http://www.w3.org/2000/svg" fill="none" 
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="ml-3 text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                    </div>
                    <svg class="h-5 w-5 text-gray-300 transition-transform duration-200" 
                         :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" 
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown -->
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
