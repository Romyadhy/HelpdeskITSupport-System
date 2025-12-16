<x-guest-layout>

    <div class="flex flex-col items-center text-center mb-6">
        <img src="{{ asset('images/logoKU.png') }}" class="w-20 h-20 drop-shadow-lg" alt="system logo">
        <h1 class="text-2xl font-bold text-gray-800 mt-3">Helpdesk IT System</h1>
        <p class="text-gray-500 text-sm">Masuk untuk melanjutkan pekerjaanmu</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm" onsubmit="this.querySelector('button').disabled = true;">
        @csrf

        {{-- Email --}}
        <div class="mb-4 text-left">
            <label class="text-sm font-semibold text-gray-700">Email</label>
            <input type="email" name="email"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-600 focus:ring-teal-600"
                value="{{ old('email') }}" required autofocus>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        {{-- Password --}}
        <div class="mb-4 text-left relative">
            <label class="text-sm font-semibold text-gray-700">Password</label>
            <input id="password" type="password" name="password"
                class="mt-1 block w-full pr-10 rounded-lg border-gray-300 focus:border-teal-600 focus:ring-teal-600"
                required>
            <i id="togglePassword"
                class="fa-solid fa-eye absolute right-3 top-[42px] text-gray-400 hover:text-gray-600 cursor-pointer">
            </i>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        {{-- Remember --}}
        {{-- <label class="flex items-center gap-2 mb-5 cursor-pointer text-sm text-gray-600"> --}}
        {{--    <input type="checkbox" name="remember" class="rounded border-gray-300 text-teal-600 focus:ring-teal-600"> --}}
        {{--    Ingat saya --}}
        {{-- </label> --}}

        {{-- Button --}}
        {{-- <button id="loginBtn" type="submit" --}}
        {{--     class="w-full flex justify-center items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow-md transition"> --}}
        {{--     <span id="loginBtnText">Masuk ke Sistem</span> --}}
        {{--     <span id="spinner" class="hidden flex-col items-center justify-center mr-3 size-5 animate-spin"> --}}
        {{--         <svg class="" xmlns="http://www.w3.org/2000/svg" fill="none" --}}
        {{--             viewBox="0 0 24 24"> --}}
        {{--             <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" --}}
        {{--                 stroke-width="4"></circle> --}}
        {{--             <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path> --}}
        {{--         </svg> --}}
        {{--         Loading... --}}
        {{--     </span> --}}
        {{-- </button> --}}


<button id="loginBtn" type="submit"
    class="w-full flex justify-center items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700
           text-white font-semibold rounded-lg shadow-md transition">

    <!-- Normal text -->
    <span id="loginBtnText">Masuk ke Sistem</span>

    <!-- Spinner wrapper -->
    <span id="spinner" class="hidden flex items-center gap-2">
        <svg class="w-5 h-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span>Loading...</span>
    </span>
</button>





        {{-- {{-- Forgot --}}
        {{-- @if (Route::has('password.request')) --}}
        {{--    <div class="text-center mt-4"> --}}
        {{--        <a class="text-sm text-teal-600 hover:text-teal-700" href="{{ route('password.request') }}"> --}}
        {{--            Lupa password? --}}
        {{--        </a> --}}
        {{--    </div> --}}
        {{-- @endif --}}
    </form>

</x-guest-layout>
