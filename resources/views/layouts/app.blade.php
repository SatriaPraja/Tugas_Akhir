<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GIS - Distribusi Pupuk')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    @stack('head')
</head>

<body class="bg-gray-50 font-sans text-gray-800">

    <div class="min-h-screen flex">

        <button id="openSidebar"
            class="fixed top-4 left-4 z-40 text-gray-700 bg-white shadow-md rounded-md px-3 py-2 md:hidden">
            <i class="fas fa-bars"></i>
        </button>


        <aside id="sidebar"
            class="bg-gray-800 text-white w-64 fixed inset-y-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50">

            <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-tractor"></i>
                    <span class="font-bold">Admin Panel</span>
                </div>

                <button id="closeSidebar" class="text-gray-300 hover:text-white md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-3 hover:bg-gray-700 flex items-center {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-chart-line mr-2 w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('map.view') }}"
                    class="block px-4 py-3 hover:bg-gray-700 flex items-center {{ request()->routeIs('map.view') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-map-marked-alt mr-2 w-5 text-center"></i>
                    <span>Lihat Peta</span>
                </a>

                <a href="{{ route('admin.lahan.index') }}"
                    class="block px-4 py-3 hover:bg-gray-700 flex items-center {{ request()->routeIs('admin.lahan.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-th-list mr-2 w-5 text-center"></i>
                    <span>Data Lahan</span>
                </a>

                <a href="{{ route('admin.geojson.index') }}"
                    class="block px-4 py-3 hover:bg-gray-700 flex items-center {{ request()->routeIs('admin.geojson.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-file-import mr-2 w-5 text-center"></i>
                    <span>Import Lahan</span>
                </a>

                <a href="{{ route('admin.account.index') }}"
                    class="block px-4 py-3 hover:bg-gray-700 flex items-center {{ request()->routeIs('admin.account.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-users-cog mr-2 w-5 text-center"></i>
                    <span>Manajemen Akun</span>
                </a>

                <div class="border-t border-gray-700 my-4"></div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="block px-4 py-3 hover:bg-red-900/40 text-red-400 font-semibold flex items-center transition-colors">
                    <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i>
                    <span>Keluar (Logout)</span>
                </a>
            </nav>

            @stack('sidebar')
        </aside>

        <main class="flex-1 px-4 py-6 overflow-auto md:ml-64">
            @yield('content')
        </main>
    </div>


    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const openSidebar = document.getElementById('openSidebar'); // Tombol di pojok kiri atas
        const closeSidebar = document.getElementById('closeSidebar'); // Tombol di samping Admin Panel

        // Fungsi untuk mengelola visibilitas Tombol Buka di Pojok Kiri Atas
        function toggleOpenButtonVisibility() {
            // Cek jika lebar layar adalah mobile (kurang dari 768px - titik henti 'md')
            if (window.innerWidth < 768) {
                // Jika sidebar tertutup, tampilkan tombol buka di luar
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar.classList.remove('hidden');
                } else {
                    // Jika sidebar terbuka, sembunyikan tombol buka di luar
                    openSidebar.classList.add('hidden');
                }
            } else {
                // Di desktop, selalu sembunyikan tombol buka di luar
                openSidebar.classList.add('hidden');
            }
        }

        // 1. Aksi Buka Sidebar (Dipicu oleh tombol di pojok kiri atas)
        openSidebar.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            // Update visibilitas tombol
            toggleOpenButtonVisibility();
        });

        // 2. Aksi Tutup Sidebar (Dipicu oleh tombol di samping Admin Panel)
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            // Update visibilitas tombol
            toggleOpenButtonVisibility();
        });

        // Jalankan saat halaman dimuat untuk inisialisasi status awal di mobile
        toggleOpenButtonVisibility();

        // Juga jalankan saat ukuran jendela diubah (misalnya dari desktop ke mobile)
        window.addEventListener('resize', toggleOpenButtonVisibility);
    </script>


    @stack('scripts')
</body>

</html>
