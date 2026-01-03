@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto pb-8">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Admin</h1>
            <p class="text-gray-500 mt-1 uppercase tracking-widest text-xs font-semibold">
                Sistem Informasi Geografis & Analisis Klaster Lahan
            </p>
            <div class="h-1 w-20 bg-blue-600 mt-2 rounded-full"></div>
        </div>
        

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Lahan --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 stat-card shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Lahan</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalPetani) }}</h3>
                    </div>
                    <div class="p-4 bg-green-50 rounded-xl">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span>Data terverifikasi</span>
                </div>
            </div>

            {{-- Total Luas --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 stat-card shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Luas Lahan</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalLuas) }} <span
                                class="text-lg font-normal text-gray-400">m²</span></h3>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <i class="fas fa-map-marked-alt text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-blue-600">
                    <i class="fas fa-vector-square mr-1"></i>
                    <span>Cakupan wilayah aktif</span>
                </div>
            </div>

            {{-- Jumlah Klaster --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 stat-card shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Jumlah Klaster</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">3</h3>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-xl">
                        <i class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-purple-600">
                    <i class="fas fa-layer-group mr-1"></i>
                    <span>Kecil, Sedang, Besar</span>
                </div>
            </div>
        </div>

        {{-- Populasi Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl border-l-4 border-green-500 p-4 shadow-sm flex items-center justify-between">
                <span class="text-gray-600 font-medium">Klaster Kecil (1)</span>
                <span class="text-xl font-bold text-gray-800">{{ $klaster1 }}</span>
            </div>
            <div class="bg-white rounded-xl border-l-4 border-yellow-500 p-4 shadow-sm flex items-center justify-between">
                <span class="text-gray-600 font-medium">Klaster Sedang (2)</span>
                <span class="text-xl font-bold text-gray-800">{{ $klaster2 }}</span>
            </div>
            <div class="bg-white rounded-xl border-l-4 border-red-500 p-4 shadow-sm flex items-center justify-between">
                <span class="text-gray-600 font-medium">Klaster Besar (3)</span>
                <span class="text-xl font-bold text-gray-800">{{ $klaster3 }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Donut Chart Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-base font-bold text-gray-800 mb-4">Distribusi & Populasi</h2>

                <div class="flex items-center gap-6">
                    {{-- Ukuran ditingkatkan sedikit agar donut lebih jelas --}}
                    <div class="flex-shrink-0" style="width: 180px; height:250px;">
                        <canvas id="donutKlaster"></canvas>
                    </div>

                    <div class="flex-1 space-y-3">
                        <div
                            class="px-3 py-2 bg-green-50 rounded-lg border-l-4 border-green-500 flex justify-between items-center shadow-sm">
                            <span class="text-xs font-bold text-gray-700">Kecil</span>
                            <span class="text-xs font-semibold text-green-700">{{ $klaster1 }} L /
                                {{ number_format($avgPanen1) }}kg</span>
                        </div>
                        <div
                            class="px-3 py-2 bg-yellow-50 rounded-lg border-l-4 border-yellow-500 flex justify-between items-center shadow-sm">
                            <span class="text-xs font-bold text-gray-700">Sedang</span>
                            <span class="text-xs font-semibold text-yellow-700">{{ $klaster2 }} L /
                                {{ number_format($avgPanen2) }}kg</span>
                        </div>
                        <div
                            class="px-3 py-2 bg-red-50 rounded-lg border-l-4 border-red-500 flex justify-between items-center shadow-sm">
                            <span class="text-xs font-bold text-gray-700">Besar</span>
                            <span class="text-xs font-semibold text-red-700">{{ $klaster3 }} L /
                                {{ number_format($avgPanen3) }}kg</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bar Chart Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-base font-bold text-gray-800 mb-4">Rata-rata Panen (kg)</h2>
                {{-- Tinggi container dinaikkan ke 180px agar sejajar sempurna dengan area donut --}}
                <div style="height: 250px; width: 100%;">
                    <canvas id="chartPanen"></canvas>
                </div>
                <div class="mt-3 py-2 px-3 bg-blue-50 rounded-lg border border-blue-100 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2 text-xs"></i>
                    <p class="text-[10px] text-blue-800 leading-tight">
                        Data berdasarkan pengelompokkan <strong>Skala Usaha</strong> klaster lahan.
                    </p>
                </div>
            </div>

        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const colors = ['#44aa44', '#facc15', '#f56565'];

                // Donut Chart - Layout Seimbang
                const ctxDonut = document.getElementById('donutKlaster').getContext('2d');
                new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Kecil', 'Sedang', 'Besar'],
                        datasets: [{
                            data: [{{ $klaster1 }}, {{ $klaster2 }}, {{ $klaster3 }}],
                            backgroundColor: colors,
                            borderWidth: 0, // Dibuat 0 agar lebih clean (flat design)
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        cutout: '70%',
                        responsive: true,
                        maintainAspectRatio: false, // Penting agar mengisi container
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        layout: {
                            padding: 10
                        }
                    }
                });

                // Bar Chart - Tinggi Menyesuaikan
                const ctxBar = document.getElementById('chartPanen').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: ['Kecil', 'Sedang', 'Besar'],
                        datasets: [{
                            data: [{{ $avgPanen1 }}, {{ $avgPanen2 }}, {{ $avgPanen3 }}],
                            backgroundColor: colors,
                            borderRadius: 8,
                            barThickness: 40 // Ditebalkan sedikit karena container lebih luas
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: '#f3f4f6',
                                    drawBorder: false
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
