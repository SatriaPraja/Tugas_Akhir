@extends('layouts.app')

@section('title', 'Manajemen Data Lahan')

@section('content')
    <div class="p-8">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Data Spasial Lahan</h1>
                <p class="text-gray-500 mt-1">Kelola informasi klasterisasi, produktivitas, dan koordinat lahan.</p>
            </div>

            <div class="flex flex-col items-end space-y-3">

                <form action="{{ request()->url() }}" method="GET" class="flex items-center space-x-3">
                    <select name="filter_klaster" onchange="this.form.submit()"
                        class="bg-white border border-gray-200 text-gray-600 text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-all">
                        <option value="">Semua Klaster</option>
                        <option value="0" {{ request('filter_klaster') == '0' ? 'selected' : '' }}>Kecil</option>
                        <option value="2" {{ request('filter_klaster') == '2' ? 'selected' : '' }}>Sedang</option>
                        <option value="1" {{ request('filter_klaster') == '1' ? 'selected' : '' }}>Besar</option>
                    </select>

                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari NOP atau Nama..."
                            class="bg-white border border-gray-200 text-gray-600 text-sm rounded-xl pl-10 pr-4 py-2.5 w-64 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-all">
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-blue-600 text-white p-4 rounded-xl shadow-lg mb-6 flex items-center animate-fade-in">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            {{-- Tambah Header ID --}}
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider w-16">ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">NOP &
                                Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Luas
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Tanah
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">
                                Produktivitas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Est.
                                Panen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">Klaster
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-blue-900 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($lahan as $item)
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                {{-- Isi ID --}}
                                <td class="px-6 py-4 text-sm font-semibold text-gray-400">
                                    #{{ $item->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $item->nop }}</div>
                                    <div class="text-sm text-gray-500 flex items-center">
                                        {{ $item->nama }}

                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ number_format($item->luas) }} m²
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->jenis_tanah }}</td>
                                <td class="px-6 py-4 text-sm text-blue-600 font-bold">
                                    {{ $item->produktivitas ?? 0 }}
                                    <span class="text-[10px] text-gray-400 font-normal">Kg/Ha</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->estimasi_panen }}
                                    <span class="text-xs text-gray-400">Kg</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $klasterColor = [
                                            'Kecil' => 'bg-emerald-100 text-emerald-700',
                                            'Sedang' => 'bg-blue-100 text-blue-700',
                                            'Besar' => 'bg-purple-100 text-purple-700',
                                        ];
                                        $labels = [1 => 'Kecil', 2 => 'Sedang', 3 => 'Besar'];
                                        $label = $labels[$item->klaster] ?? 'N/A';
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-bold {{ $klasterColor[$label] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button onclick="openEditModal({{ json_encode($item) }})"
                                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.lahan.delete', $item->id) }}"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus data lahan ini?')"
                                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">Data lahan tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(data) {
            document.getElementById('editForm').action = `/admin/lahan/update/${data.id}`;
            document.getElementById('editNama').value = data.nama;
            document.getElementById('editNop').value = data.nop;
            document.getElementById('editLuas').value = data.luas;
            document.getElementById('editJenis').value = data.jenis_tanah;
            document.getElementById('editPanen').value = data.estimasi_panen;
            document.getElementById('editKlaster').value = data.klaster;

            if (document.getElementById('editProduktivitas')) {
                document.getElementById('editProduktivitas').value = data.produktivitas;
            }

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
@endsection
