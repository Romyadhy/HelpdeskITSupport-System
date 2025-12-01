<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Helpdesk IT System') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        /* ===== Elegant Gradient Soft Waves Background ===== */
        body {
            --c1: #d4f4ef;
            --c2: #e6e9ff;
            --c3: #c8f5ff;
            background: linear-gradient(130deg, var(--c1), var(--c2), var(--c3));
            background-size: 200% 200%;
            animation: softMove 14s ease infinite;
        }

        @keyframes softMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Curved wave overlay */
        .wave-overlay {
            position: fixed;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='1440' height='600' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 150 Q360 260 720 150 T1440 150 V600 H0Z' fill='rgba(255,255,255,.35)'/%3E%3C/svg%3E");
            background-size: cover;
            animation: waveFloat 5s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes waveFloat {
            0% {
                transform: translateY(0px);
            }

            100% {
                transform: translateY(100px);
            }
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased overflow-hidden">

    {{-- Wave overlay layer --}}
    <div class="wave-overlay -z-10"></div>

    {{-- Center Wrapper --}}
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8">

        {{-- Card --}}
        <div
            class="w-full sm:max-w-md bg-white/85 backdrop-blur-xl shadow-xl border border-white/50 rounded-2xl px-8 py-10 animate-[fadeInUp_.7s_ease]">
            {{ $slot }}
        </div>

        <footer class="text-xs text-gray-600 mt-6">
            © {{ date('Y') }} Helpdesk IT System — All Rights Reserved
        </footer>
    </div>

    {{-- JS: toggle password + submit loader --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // password toggle
            const pwd = document.getElementById("password");
            const toggle = document.getElementById("togglePassword");
            if (toggle && pwd) {
                toggle.addEventListener("click", () => {
                    pwd.type = pwd.type === "password" ? "text" : "password";
                    toggle.classList.toggle("fa-eye");
                    toggle.classList.toggle("fa-eye-slash");
                });
            }

            // loading button
            // const form = document.getElementById("loginForm");
            // const btn = document.getElementById("loginBtn");
            // if (form && btn) {
            //     form.addEventListener("submit", () => {
            //         btn.disabled = true;
            //         btn.classList.add("opacity-70", "cursor-not-allowed");
            //         document.getElementById("loginBtnText").classList.add("hidden");
            //         document.getElementById("spinner").classList.remove("hidden");
            //     });
            // }
            const form = document.querySelector("#loginForm");
            const loginBtn = document.querySelector("#loginBtn");
            const loginText = document.querySelector("#loginBtnText");
            const spinner = document.querySelector("#spinner");

        if (form && loginBtn) {
            form.addEventListener("submit", () => {
                loginBtn.disabled = true;
                loginBtn.classList.add("opacity-70", "cursor-not-allowed");

                loginText.classList.add("hidden");   // hide "Masuk ke Sistem"
                spinner.classList.remove("hidden");  // show spinner
            });
        }
        });
    </script>

</body>

</html>
