<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIG</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E3A8A',
                        secondary: '#3B82F6',
                        darkBlue: '#172554',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                        'fade-in': 'fadeIn 0.5s ease-out',
                    }
                }
            }
        }
    </script>
    <style>
        .animate-fade-in-up {
            opacity: 0;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        body {
            background: radial-gradient(circle at top left, #eff6ff, #dbeafe);
        }
    </style>
</head>

<body class="font-sans antialiased flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8 animate-fade-in-up">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-2xl shadow-2xl mb-6 transform hover:rotate-3 transition duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                    </path>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-primary leading-tight px-4">
                Sistem Infomasi Geografis Lahan Spasial
            </h1>
            <p class="text-gray-600 mt-2 text-sm font-medium tracking-wide uppercase">Desa Klapagading</p>
        </div>

        <div
            class="bg-white p-8 rounded-2xl shadow-[0_20px_50px_rgba(30,58,138,0.15)] border border-blue-50 animate-fade-in-up delay-100">

            <div class="mb-8">
                <h2 class="text-xl font-bold text-darkBlue">Login Admin</h2>
                <p class="text-gray-400 text-sm">Gunakan akun Anda untuk mengakses sistem</p>
            </div>

            <form action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf

                @error('email')
                    <div
                        class="p-3 mb-6 text-sm font-medium text-red-800 bg-red-50 border-l-4 border-red-500 rounded animate-fade-in">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Input Email - Menggunakan type="email" lebih aman & validasi otomatis --}}
                <div class="mb-5 relative">
                    <label for="email" class="block text-xs font-bold text-primary uppercase mb-1 ml-1">Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:ring-2 focus:ring-secondary focus:bg-white focus:border-secondary transition duration-200 peer"
                            placeholder="nama@email.com" value="{{ old('email') }}" autofocus>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 peer-focus:text-secondary">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                {{-- Input Password --}}
                <div class="mb-8 relative">
                    <label for="password" class="block text-xs font-bold text-primary uppercase mb-1 ml-1">Kata
                        Sandi</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                            class="w-full pl-12 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:ring-2 focus:ring-secondary focus:bg-white focus:border-secondary transition duration-200 peer"
                            placeholder="••••••••">

                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 peer-focus:text-secondary">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-secondary transition-colors focus:outline-none"
                            title="Tampilkan/Sembunyikan Password">
                            <i id="eye-icon" data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 hover:bg-darkBlue hover:-translate-y-0.5 transition duration-300 active:scale-95 text-sm tracking-widest uppercase">
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <div class="text-center mt-10">
            <p class="text-xs text-gray-500 font-medium">
                &copy; {{ date('Y') }} Klasterisasi Skala Usaha Petani <br>
                <span class="text-primary/60 italic">Desa Klapagading</span>
            </p>
        </div>
    </div>

    <script>
        // Inisialisasi Icon Lucide
        lucide.createIcons();

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>

</html>
