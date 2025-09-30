{{-- @extends('layouts.app')

@section('tickets')
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="flex-col items-center mb-6">
            <h1 class="text-6xl font-bold text-teal-500 ">TICKET KEREN</h1>
        </div>
        <div class="flex-col w-full max-w-4xl p-6">
            @foreach($tickets as $ticket)
                <div class="bg-white p-4 rounded-lg shadow-md mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">{{ $ticket->title }}</h2>
                    <p class="text-gray-600 mt-2">{{ $ticket->description }}</p>
                    <p class="text-gray-600 mt-2">Status: {{ $ticket->status }}</p>
                    <p class="text-gray-600 mt-2">Priority: {{ $ticket->priority }}</p>
                    <p class="text-gray-600 mt-1">User: <span class="font-medium text-gray-700">{{ $ticket->user->name }}</span></p>
                    <span class="text-sm text-gray-500 mt-2">Created at: {{ $ticket->created_at->format('d M Y, H:i') }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection --}}



<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tickets
        </h2>
    </x-slot>

    @section('tickets')        
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-4xl font-bold text-teal-500">TICKET KEREN</h1>
                <p class="mt-1 text-gray-600">Manage and track all support requests.</p>
                {{-- create ticket --}}
                <a href="{{ route('tickets.create') }}" class="mt-4 inline-block bg-teal-500 text-white py-2 px-4 rounded-lg">Create Ticket</a>
            </div>
            
            <div class="space-y-4">
                @forelse($tickets as $ticket)
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $ticket->title }}</h2>
                        <p class="text-gray-600 mt-2">{{ $ticket->description }}</p>
                        <div class="mt-4 border-t pt-4">
                            <p class="text-gray-600">Status: {{ $ticket->status }}</p>
                            <p class="text-gray-600 mt-1">Priority: {{ $ticket->priority }}</p>
                            <p class="text-gray-600 mt-1">User: <span class="font-medium text-gray-700">{{ $ticket->user->name }}</span></p>
                            <p class="text-gray-600 mt-1">Assignee: {{-- $ticket->assignee --}}</p>
                            <span class="text-sm text-gray-500 mt-2 block">Created at: {{ $ticket->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-6 rounded-lg shadow-md text-center">
                        <p class="text-gray-500">No tickets found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endsection
</x-app-layout>