<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        {{-- sidebar --}}
        <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">

            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden">

              <header class="flex justify-between items-center p-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-900 focus:outline-none lg:hidden">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="flex-1">
                    @if (isset($header))
                        {{ $header }}
                    @endif
                </div>
                </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    {{-- @yield('content')
                    @yield('tickets') --}}
                    {{ $slot }}
                </div>
            </main>
        </div>
        </div>
    </body>
</html>
