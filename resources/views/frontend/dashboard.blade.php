{{-- @extends('layouts.app')

@section('content')
    <div class="bg-gray-10 min-h-screen flex items-center justify-center">
        <h1 class="text-6xl font-bold text-teal-500 ">BIRJON KERENLAH</h1>
    </div>
@endsection --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-teal-500">
                    <h1 class="text-6xl font-bold">BIRJONN KEREN</h1>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>