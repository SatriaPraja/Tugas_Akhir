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
                    {{-- Tombol Reset/Kembali - Hanya muncul jika ada filter atau pencarian --}}


                    <select name="filter_klaster" onchange="this.form.submit()"
                        class="bg-white border border-gray-200 text-gray-600 text-sm rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-all">
                        <option value="">Semua Klaster</option>
                        <option value="1" {{ request('filter_klaster') == '1' ? 'selected' : '' }}>Kecil</option>
                        <option value="2" {{ request('filter_klaster') == '2' ? 'selected' : '' }}>Sedang</option>
                        <option value="3" {{ request('filter_klaster') == '3' ? 'selected' : '' }}>Besar</option>
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
                    @if (request('search') || request('filter_klaster'))
                        <a href="{{ request()->url() }}"
                            class="bg-blue-600 text-white p-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center"
                            title="Kembali ke semua data">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </a>
                    @endif
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-blue-900 uppercase tracking-wider">
                                Pupuk
                            </th>
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
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-blue-700">Urea: {{ $item->urea ?? 0 }} <small
                                                class="text-gray-400">Kg</small></span>
                                        <span class="font-semibold text-green-700">NPK: {{ $item->npk ?? 0 }} <small
                                                class="text-gray-400">Kg</small></span>
                                    </div>
                                </td>
                                {{-- PERBAIKAN WARNA KLASTER --}}
                                <td class="px-6 py-4">
                                    @php
                                        // Pemetaan Label
                                        $labels = [1 => 'Kecil', 2 => 'Sedang', 3 => 'Besar'];
                                        $label = $labels[$item->klaster] ?? 'N/A';

                                        // Pemetaan Warna sesuai permintaan Anda
                                        $bgColor = '#gray-100';
                                        if ($item->klaster == 1) {
                                            $bgColor = '#44aa44';
                                        } // Hijau
                                        if ($item->klaster == 2) {
                                            $bgColor = '#facc15';
                                        } // Kuning
                                        if ($item->klaster == 3) {
                                            $bgColor = '#f56565';
                                        } // Merah
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm"
                                        style="background-color: {{ $bgColor }};">
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
                                        {{-- Tombol Hapus (Panggil Modal) --}}
                                        <button
                                            onclick="openDeleteModal('{{ $item->nop }} - {{ $item->nama }}', '{{ route('admin.lahan.delete', $item->id) }}')"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">Data lahan tidak
                                    ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="deleteModal"
        class="fixed inset-0 z-[100] hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 text-center">
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Hapus Data Lahan?</h2>
            <p class="text-gray-500 mb-8">Apakah Anda yakin menghapus <span id="deleteFileName"
                    class="font-bold text-gray-800"></span>?</p>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-3 border border-gray-200 text-gray-500 rounded-xl font-bold hover:bg-gray-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg transition-all">Ya,
                        Hapus</button>
                </div>
            </form>
        </div>
    </div>
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm transition-opacity"></div>

        <div class="relative min-h-screen flex items-center justify-center p-6">
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden ">

                <div class="bg-blue-600 px-8 py-5 border-b-2 border-blue-700">
                    <h3 class="text-xl font-bold text-white tracking-wide">Edit Data Lahan & Pupuk</h3>
                    <p class="text-blue-100 text-xs mt-1">Pastikan data yang dimasukkan sudah sesuai dengan sertifikat
                        lahan.</p>
                </div>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-8 space-y-6">

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider ml-1">Nomor
                                    Objek Pajak (NOP)</label>
                                <input type="text" id="editNop" name="nop"
                                    class="block w-full border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-base py-3 px-4 transition-all bg-gray-50/50">
                            </div>
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider ml-1">Nama
                                    Pemilik Lahan</label>
                                <input type="text" id="editNama" name="nama"
                                    class="block w-full border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-base py-3 px-4 transition-all bg-gray-50/50">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider ml-1">Luas
                                    Lahan (m²)</label>
                                <input type="number" id="editLuas" name="luas"
                                    class="block w-full border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-base py-3 px-4 transition-all bg-gray-50/50">
                            </div>
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider ml-1">Jenis
                                    Tanah</label>
                                <input type="text" id="editJenis" name="jenis_tanah"
                                    class="block w-full border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-base py-3 px-4 transition-all bg-gray-50/50">
                            </div>
                        </div>

                        <div class="p-5 bg-blue-50 rounded-2xl border-2 border-blue-200 shadow-sm space-y-4">
                            <h4 class="text-blue-800 text-sm font-bold flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                                    </path>
                                </svg>
                                Pupuk Yang Diperoleh
                            </h4>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-blue-600 uppercase mb-1">Dosis
                                        Urea</label>
                                    <input type="number" id="editUrea" name="urea"
                                        class="block w-full border-2 border-blue-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-base font-semibold py-2.5 px-4 bg-white shadow-inner">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-green-600 uppercase mb-1">Dosis
                                        NPK</label>
                                    <input type="number" id="editNpk" name="npk"
                                        class="block w-full border-2 border-green-200 rounded-lg focus:ring-green-500 focus:border-green-500 text-base font-semibold py-2.5 px-4 bg-white shadow-inner">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 items-center">
                            <div class="space-y-1">
                                <label
                                    class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider ml-1">Est.
                                    Hasil Panen (Kg)</label>
                                <input type="number" id="editPanen" name="estimasi_panen"
                                    class="block w-full border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-base py-3 px-4 transition-all font-bold text-blue-700 bg-gray-50/50">
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border-2 border-dashed border-gray-300">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori
                                    Klaster</label>
                                <input type="hidden" id="editKlaster" name="klaster">
                                <div id="displayKlaster" class="text-lg font-black text-gray-700 mt-1"></div>
                                <span class="text-[9px] text-gray-400 leading-tight italic">Sistem mengunci klaster
                                    ini.</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-8 py-6 flex justify-end space-x-4 border-t-2 border-gray-200/50">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-3 bg-red-50 text-red-600 border-2 border-red-100 text-sm font-bold rounded-xl hover:bg-red-600 hover:text-white hover:border-red-600 transition-all duration-200 active:scale-95">
                            Batal
                        </button>

                        <button type="submit"
                            class="px-10 py-3 bg-blue-600 text-white text-sm font-black rounded-xl hover:bg-blue-700 hover:shadow-lg active:transform active:scale-95 transition-all uppercase tracking-widest border-b-4 border-blue-800">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Inisialisasi variabel di dalam fungsi agar tidak error jika elemen belum dimuat
        function openDeleteModal(name, url) {
            const dModal = document.getElementById('deleteModal');
            const dForm = document.getElementById('deleteForm');
            const dFileName = document.getElementById('deleteFileName');

            dFileName.innerText = name;
            dForm.action = url;
            dModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(data) {
            const editModal = document.getElementById('editModal');
            document.getElementById('editForm').action = `/admin/lahan/update/${data.id}`;
            document.getElementById('editNama').value = data.nama;
            document.getElementById('editNop').value = data.nop;
            document.getElementById('editLuas').value = data.luas;
            document.getElementById('editJenis').value = data.jenis_tanah;
            document.getElementById('editPanen').value = data.estimasi_panen;
            document.getElementById('editUrea').value = data.urea ?? 0;
            document.getElementById('editNpk').value = data.npk ?? 0;

            const klasterNames = {
                1: 'Kecil',
                2: 'Sedang',
                3: 'Besar'
            };
            document.getElementById('editKlaster').value = data.klaster;
            document.getElementById('displayKlaster').innerText = `Kategori: ${klasterNames[data.klaster] || 'N/A'}`;

            editModal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal on click outside
        window.onclick = function(event) {
            const dModal = document.getElementById('deleteModal');
            const eModal = document.getElementById('editModal');
            if (event.target == dModal) closeDeleteModal();
            if (event.target == eModal) closeEditModal();
        }
    </script>
@endsection
