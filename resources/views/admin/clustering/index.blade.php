@extends('layouts.app')

@section('title', 'Proses Klasterisasi')

@section('content')
    <div class="p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Proses Klasterisasi Lahan</h1>
            <p class="text-gray-500 mt-1">Menggunakan Kecerdasan Buatan (K-Means Clustering) untuk pemetaan skala usaha tani
                desa.</p>
        </div>

        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded-xl mb-6 shadow-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Card Form (Kiri) --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    <div class="bg-blue-600 px-8 py-5 text-center">
                        <i class="fas fa-brain text-white text-3xl mb-2"></i>
                        <h3 class="text-xl font-bold text-white uppercase tracking-wider">Konfigurasi AI</h3>
                    </div>

                    <form action="{{ route('admin.clustering.process') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        <div class="space-y-3">
                            <label class="block text-xs font-extrabold text-gray-500 uppercase tracking-wider ml-1">
                                Jumlah Klaster (Optimal K)
                            </label>

                            {{-- Input diubah menjadi readonly dan ditambah class bg-gray-50/cursor-not-allowed --}}
                            <input type="number" name="k_value" value="3" readonly
                                class="block w-full border-2 border-gray-200 rounded-xl bg-gray-50 text-lg py-3 px-4 transition-all font-bold text-blue-600 cursor-not-allowed focus:outline-none"
                                title="Nilai K telah dikunci untuk hasil optimal">

                            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r-xl">
                                <p class="text-[11px] text-blue-800 leading-relaxed italic">
                                    <i class="fas fa-lock mr-1"></i>
                                    <strong>Sistem Terkunci:</strong> Nilai <b>K=3</b> telah ditetapkan sebagai konfigurasi
                                    terbaik untuk akurasi klasterisasi.
                                </p>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-layer-group text-blue-600 mr-3 text-lg"></i>
                                <span>Total Sampel: <strong>{{ $totalLahan }} Lahan</strong></span>
                            </div>
                        </div>

                        <button type="submit" id="btn-cluster"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-lg transition-all transform active:scale-95 uppercase tracking-widest border-b-4 border-blue-900 flex items-center justify-center group">
                            <i
                                class="fas fa-sync-alt mr-2 text-xl group-hover:rotate-180 transition-transform duration-500"></i>
                            <span id="btn-text">Mulai Proses</span>
                        </button>
                    </form>
                </div>
            </div>
            {{-- Card Info & Alur (Kanan) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h4 class="text-gray-800 font-bold text-xl mb-6 flex items-center">
                        <i class="fas fa-project-diagram mr-3 text-blue-600"></i> Alur Kerja Sistem
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-5 bg-blue-50 rounded-2xl border border-blue-100">
                            <div
                                class="w-10 h-10 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm font-black text-blue-600">
                                1</div>
                            <h5 class="font-bold text-blue-900 mb-1">Normalisasi</h5>
                            <p class="text-[10px] text-blue-700 leading-relaxed">Menyesuaikan skala Luas dan Produktivitas
                                agar seimbang.</p>
                        </div>

                        <div class="p-5 bg-blue-50 rounded-2xl border border-blue-100">
                            <div
                                class="w-10 h-10 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm font-black text-blue-600">
                                2</div>
                            <h5 class="font-bold text-blue-900 mb-1">Iterasi K-Means</h5>
                            <p class="text-[10px] text-blue-700 leading-relaxed">Mencari titik pusat (centroid) terbaik
                                untuk setiap kelompok.</p>
                        </div>

                        <div class="p-5 bg-blue-50 rounded-2xl border border-blue-100">
                            <div
                                class="w-10 h-10 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm font-black text-blue-600">
                                3</div>
                            <h5 class="font-bold text-blue-900 mb-1">Penyimpanan</h5>
                            <p class="text-[10px] text-blue-700 leading-relaxed">Melabeli lahan ke database sesuai hasil
                                pengelompokan.</p>
                        </div>
                    </div>
                </div>

                {{-- Penjelasan Skala Usaha --}}
                {{-- Penjelasan Skala Usaha dengan Kode Warna Custom --}}
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h4 class="text-gray-800 font-bold text-lg mb-6 flex items-center">
                        <i class="fas fa-palette mr-2 text-blue-600"></i> Legenda Klaster Lahan
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-5 rounded-2xl border-2 transition-all"
                            style="border-color: #44aa44; background-color: rgba(68, 170, 68, 0.05);">
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 rounded-full mr-2 shadow-sm" style="background-color: #44aa44;"></div>
                                <span class="font-extrabold text-sm" style="color: #44aa44;">KLASTER 1</span>
                            </div>
                            <h6 class="text-2xl font-black mb-1" style="color: #44aa44;">BESAR</h6>
                            <p class="text-[11px] text-gray-600 leading-snug">Lahan dengan luas maksimal dan produktivitas
                                tinggi.</p>
                        </div>

                        <div class="p-5 rounded-2xl border-2 transition-all"
                            style="border-color: #facc15; background-color: rgba(250, 204, 21, 0.05);">
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 rounded-full mr-2 shadow-sm" style="background-color: #facc15;"></div>
                                <span class="font-extrabold text-sm" style="color: #856404;">KLASTER 2</span>
                            </div>
                            <h6 class="text-2xl font-black mb-1" style="color: #856404;">SEDANG</h6>
                            <p class="text-[11px] text-gray-600 leading-snug">Lahan kategori menengah dengan potensi stabil.
                            </p>
                        </div>

                        <div class="p-5 rounded-2xl border-2 transition-all"
                            style="border-color: #f56565; background-color: rgba(245, 101, 101, 0.05);">
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 rounded-full mr-2 shadow-sm" style="background-color: #f56565;"></div>
                                <span class="font-extrabold text-sm" style="color: #f56565;">KLASTER 3</span>
                            </div>
                            <h6 class="text-2xl font-black mb-1" style="color: #f56565;">KECIL</h6>
                            <p class="text-[11px] text-gray-600 leading-snug">Lahan terbatas yang memerlukan perhatian
                                khusus.</p>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-cluster');
            const text = document.getElementById('btn-text');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            text.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Sedang Menganalisis...';
        });
    </script>
@endsection
