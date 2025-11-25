<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
            <i class="fa-solid fa-user-circle text-teal-600"></i>
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-6">

            {{-- PROFILE CARD --}}
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-100">

                {{-- Profile Top --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-6 border-b pb-6">
                    {{-- Avatar --}}
                    <div class="flex justify-center sm:justify-start">
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-br from-teal-500 to-emerald-600
                            text-white flex items-center justify-center text-4xl font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>

                    {{-- Basic info --}}
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
                        <p class="text-gray-500 text-sm">{{ Auth::user()->email }}</p>
                        <span
                            class="inline-flex mt-2 text-xs bg-teal-100 text-teal-700 px-3 py-1 rounded-full font-semibold">
                            {{ Auth::user()->roles->first()->name ?? 'User' }}
                        </span>
                    </div>
                </div>

                {{-- Update Form --}}
                <form method="POST" action="{{ route('profile.update') }}" class="mt-8 space-y-6" id="profileForm">
                    @csrf
                    @method('PATCH')

                    {{-- Name --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 block mb-1">Full Name</label>
                        <input type="text" name="name"
                            class="w-full rounded-lg border-gray-300 focus:border-teal-600 focus:ring-teal-600"
                            value="{{ old('name', Auth::user()->name) }}" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="text-sm font-semibold text-gray-700 block mb-1">Email Address</label>
                        <input type="email" name="email"
                            class="w-full rounded-lg border-gray-300 focus:border-teal-600 focus:ring-teal-600"
                            value="{{ old('email', Auth::user()->email) }}" required>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Button --}}
                    <div class="pt-2">
                        <button type="button" id="confirmSaveBtn"
                            class="px-6 py-2 flex items-center gap-2 bg-teal-600 hover:bg-teal-700
                            text-white font-semibold rounded-lg shadow-md transition w-full sm:w-auto">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- SweetAlert Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // SUCCESS ALERT (after update)
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1700,
                    timerProgressBar: true
                });
            @endif

            // CONFIRM BEFORE SUBMIT
            const confirmBtn = document.getElementById("confirmSaveBtn");
            const form = document.getElementById("profileForm");

            if (confirmBtn && form) {
                confirmBtn.addEventListener("click", () => {
                    Swal.fire({
                        title: "Simpan Perubahan?",
                        text: "Perubahan pada profil Anda akan diperbarui.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#059669",
                        cancelButtonColor: "#6b7280",
                        confirmButtonText: "Ya, Simpan",
                        cancelButtonText: "Batal",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>

</x-app-layout>
