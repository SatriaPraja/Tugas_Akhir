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
                                    <a href="{{ route('admin.import.process', $file) }}"
                                        class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded font-bold shadow-sm">
                                        Import DB
                                    </a>

                                    <form action="{{ route('admin.import.delete', $file) }}" method="POST"
                                        onsubmit="return confirm('Hapus file CSV ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-400 hover:text-red-500 p-1">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
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
                                    <form action="{{ route('admin.geojson.delete', $file) }}" method="POST"
                                        onsubmit="return confirm('Hapus file GeoJSON ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-400 hover:text-red-500 p-1">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
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
@endsection
