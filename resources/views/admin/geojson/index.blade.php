@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800 tracking-tight">Manajemen Import Lahan</h2>
            <p class="text-gray-500">Pisahkan pembaruan data klaster (CSV) dan sinkronisasi polygon (GeoJSON)</p>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-6 border border-green-200 shadow-sm">
                {!! session('success') !!}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-6 border border-red-200 shadow-sm">
                {!! session('error') !!}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- BAGIAN KIRI: MANAJEMEN CSV (DATA ATRIBUT) --}}
            <div class="space-y-6">
                <div
                    class="p-5 bg-white rounded-xl shadow-sm border-t-4 border-emerald-500 border-x border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                            <i class="fas fa-file-csv text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Data Atribut & Klaster (CSV)</h4>
                            <p class="text-xs text-gray-500">Gunakan file hasil olahan Python</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.import.upload') }}" method="POST" enctype="multipart/form-data"
                        class="flex gap-2">
                        @csrf
                        <input type="file" name="csv_file" required
                            class="border rounded-lg px-3 py-2 w-full text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-bold transition-all whitespace-nowrap">
                            Upload CSV
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    @php
                        // 1. Definisikan Path langsung di Blade
                        $csvDir = storage_path('app/private/csv');

                        // 2. Cek folder & ambil file
                        $rawFiles = File::exists($csvDir) ? glob($csvDir . '/*.csv') : [];

                        // 3. Bersihkan nama file agar hanya muncul nama.csv
                        $directCsvFiles = array_map(function ($path) {
                            return basename($path);
                        }, $rawFiles);
                    @endphp

                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <span class="font-bold text-gray-700">Daftar File CSV (Direct)</span>
                        <span class="text-xs font-bold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">
                            {{ count($directCsvFiles) }} File
                        </span>
                    </div>

                    <ul class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                        @forelse($directCsvFiles as $file)
                            <li class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition">
                                <span class="text-sm text-gray-600 font-medium truncate mr-4">{{ $file }}</span>
                                <div class="flex items-center gap-2">
                                    {{-- Route tetap mengarah ke controller untuk prosesnya --}}
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.import.process', $file) }}"
                                            class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded font-bold shadow-sm">
                                            Import DB
                                        </a>

                                        <button type="button"
                                            onclick="openDeleteModal('{{ $file }}', '{{ route('admin.import.delete', $file) }}')"
                                            class="text-gray-400 hover:text-red-500 p-1">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-10 text-center text-gray-400 italic text-sm">
                                Belum ada file CSV di server. <br>
                                <code class="text-[10px] bg-gray-100 p-1">{{ $csvDir }}</code>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- BAGIAN KANAN: MANAJEMEN GEOJSON (DATA SPASIAL) --}}
            <div class="space-y-6">
                <div class="p-5 bg-white rounded-xl shadow-sm border-t-4 border-blue-500 border-x border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <i class="fas fa-map-marked-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800">Data Spasial (GeoJSON)</h4>
                            <p class="text-xs text-gray-500">Update polygon berdasarkan NOP</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.geojson.upload') }}" method="POST" enctype="multipart/form-data"
                        class="flex gap-2">
                        @csrf
                        <input type="file" name="geojson" required
                            class="border rounded-lg px-3 py-2 w-full text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold transition-all whitespace-nowrap">
                            Upload GeoJSON
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <span class="font-bold text-gray-700 text-sm">Daftar File GeoJSON</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.geojson.importAll') }}"
                                class="text-[10px] bg-orange-500 hover:bg-orange-600 text-white px-2 py-1 rounded font-bold uppercase">Import
                                Semua</a>
                            <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                {{ count($files ?? []) }} File
                            </span>
                        </div>
                    </div>
                    <ul class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto shadow-inner">
                        @forelse($files ?? [] as $file)
                            <li class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition">
                                <span class="text-sm text-gray-600 font-medium truncate mr-4">{{ $file }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            onclick="openDeleteModal('{{ $file }}', '{{ route('admin.geojson.delete', $file) }}')"
                                            class="text-gray-400 hover:text-red-500 p-1">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-10 text-center text-gray-400 italic text-sm">Belum ada file GeoJSON di
                                server.</li>
                        @endforelse
                    </ul>
                </div>
            </div>


        </div>
    </div>
    <div id="deleteModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 transform transition-all text-center">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Hapus File?</h2>
            <p class="text-gray-500 mb-8">Apakah Anda yakin ingin menghapus file <span id="deleteFileName"
                    class="font-bold text-gray-800"></span>? Tindakan ini tidak dapat dibatalkan.</p>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-3 border border-gray-200 text-gray-500 rounded-xl font-bold hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all">Ya,
                        Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteFileName = document.getElementById('deleteFileName');

        function openDeleteModal(file, url) {
            deleteFileName.innerText = file;
            deleteForm.action = url;
            deleteModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function closeModal() {
            deleteModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == deleteModal) closeModal();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast-item');

            toasts.forEach((toast) => {
                // Hilangkan setelah 5 detik
                setTimeout(() => {
                    toast.classList.add('translate-x-[150%]'); // Animasi keluar ke kanan
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
    {{-- Toast Notification Container --}}
    <div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-3">
        @if (session('success'))
            <div
                class="toast-item bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-500 translate-x-0">
                <i class="fas fa-check-circle"></i>
                <span class="font-bold text-sm">{!! session('success') !!}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="toast-item bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-500 translate-x-0">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="font-bold text-sm">{!! session('error') !!}</span>
            </div>
        @endif

        {{-- Untuk Error Validasi (misal file bukan CSV/GeoJSON) --}}
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div
                    class="toast-item bg-orange-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-500 translate-x-0">
                    <i class="fas fa-info-circle"></i>
                    <span class="font-bold text-sm">{{ $error }}</span>
                </div>
            @endforeach
        @endif
    </div>
@endsection
