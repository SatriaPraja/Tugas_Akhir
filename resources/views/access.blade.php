<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Sistem Informasi Geografis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2e00f2c6b.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>

<body class="bg-slate-50">

    <div id="access-page" class="relative flex flex-col items-center justify-center min-h-screen p-4 overflow-hidden">

        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-100 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-50 opacity-50 blur-3xl">
        </div>

        <div
            class="relative bg-white rounded-3xl shadow-2xl shadow-blue-100/50 p-10 max-w-md w-full border border-blue-50">

            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Sistem Informasi Geografis</h1>
                <p class="text-slate-500 mt-3 text-sm font-medium leading-relaxed">
                    Analisis Klaster Lahan Berbasis Geospasial untuk Pengelolaan Wilayah Terpadu
                </p>
                <div class="h-1.5 w-12 bg-blue-600 mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="space-y-4">
                {{-- Masuk ke Dashboard User - Diperbaiki ke Center --}}
                <a href="{{ route('user.dashboard') }}"
                    class="w-full btn-gradient text-white py-4 px-6 rounded-2xl flex items-center justify-center group">
                    <i class="fas fa-chart-line mr-3 text-blue-200"></i>
                    <span class="font-bold tracking-wide text-sm uppercase text-center">Lihat Dashboard Publik</span>
                </a>

                {{-- Masuk ke Halaman Login Admin - Diperbaiki ke Center --}}
                <a href="{{ route('login') }}"
                    class="w-full bg-white border-2 border-slate-100 hover:border-blue-200 text-slate-700 py-4 px-6 rounded-2xl flex items-center justify-center transition-all duration-300 group">
                    <i class="fas fa-user-shield mr-3 text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                    <span class="font-bold tracking-wide text-sm uppercase text-center">Akses Portal Admin</span>
                </a>
            </div>

            <div class="mt-10 text-center">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest flex items-center justify-center">
                    <span class="h-px w-8 bg-slate-200 mr-3"></span>
                    Login Portal
                    <span class="h-px w-8 bg-slate-200 ml-3"></span>
                </p>
            </div>
        </div>

        <p class="mt-8 text-slate-400 text-xs font-medium">© 2025 GIS Cluster Management System</p>
    </div>

</body>

</html>
