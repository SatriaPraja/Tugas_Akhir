@extends('layouts.app')

@section('title', 'Peta Informasi Geografis Lahan')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            width: 100%;
            height: calc(100vh - 64px);
            /* Menyesuaikan tinggi navbar */
            z-index: 1;
        }

        /* Panel Floating UI */
        .custom-panel {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            pointer-events: auto;
        }

        .top-right-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bottom-right-legend {
            position: absolute;
            bottom: 30px;
            right: 20px;
            z-index: 1000;
            width: 160px;
            padding: 12px;
        }

        .input-compact {
            height: 38px;
            font-size: 0.85rem !important;
        }

        /* Menghilangkan margin default card jika ada di layout app */
        .main-content {
            padding: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="relative w-full h-full">
        <div class="top-right-controls">
            <div class="custom-panel p-4">
                <div class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                    <i class="fas fa-search-location text-blue-600"></i> Pencarian & Filter
                </div>

                <div class="flex gap-1 mb-3">
                    <input type="text" id="search-nop" placeholder="Cari NOP Lahan..."
                        class="input-compact flex-1 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm transition-all">
                    <button id="btn-search"
                        class="px-4 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 active:scale-95 transition-all text-xs">
                        Cari
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <select id="filter-klaster"
                        class="input-compact px-2 border border-gray-200 rounded-lg bg-gray-50 text-xs font-semibold text-gray-700 outline-none hover:bg-white cursor-pointer transition-colors">
                        <option value="all">Semua Klaster</option>
                        <option value="0">Kecil</option>
                        <option value="2">Sedang</option>
                        <option value="1">Besar</option>
                    </select>
                    <select id="select-basemap"
                        class="input-compact px-2 border border-gray-200 rounded-lg bg-gray-50 text-xs font-semibold text-gray-700 outline-none hover:bg-white cursor-pointer transition-colors">
                        <option value="esri">Satelit</option>
                        <option value="osm">Jalanan</option>
                        <option value="carto">Terang</option>
                    </select>
                </div>

                <button id="btn-zoom-all"
                    class="w-full py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-md flex items-center justify-center gap-2 text-[11px] uppercase tracking-widest transition-all">
                    <i class="fas fa-sync-alt"></i> Tampilkan Semua
                </button>
            </div>
        </div>

        <div class="custom-panel bottom-right-legend">
            <div class="font-bold text-gray-700 text-xs mb-2 border-b pb-1 flex items-center gap-1">
                <i class="fas fa-map-marker-alt text-red-500"></i> Legenda
            </div>
            <div class="space-y-2">
                <div
                    class="flex items-center gap-3 text-[11px] font-bold text-gray-600 hover:text-gray-900 transition-colors cursor-default">
                    <span class="w-3 h-3 rounded-full bg-[#44aa44] shadow-sm"></span> Klaster Kecil
                </div>
                <div
                    class="flex items-center gap-3 text-[11px] font-bold text-gray-600 hover:text-gray-900 transition-colors cursor-default">
                    <span class="w-3 h-3 rounded-full bg-[#facc15] shadow-sm"></span> Klaster Sedang
                </div>
                <div
                    class="flex items-center gap-3 text-[11px] font-bold text-gray-600 hover:text-gray-900 transition-colors cursor-default">
                    <span class="w-3 h-3 rounded-full bg-[#f56565] shadow-sm"></span> Klaster Besar
                </div>
            </div>
        </div>

        <div id="map"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Basemaps
            const basemaps = {
                esri: L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles &copy; Esri',
                        maxZoom: 20
                    }),
                osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OSM',
                    maxZoom: 19
                }),
                carto: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; Carto',
                    maxZoom: 20
                })
            };

            // 2. Inisialisasi Map
            const map = L.map('map', {
                layers: [basemaps.esri],
                zoomControl: true
            }).setView([-7.3, 109.26], 13);

            map.zoomControl.setPosition('bottomleft');

            function getColor(k) {
                return k === 1 ? "#44aa44" : k === 2 ? "#facc15" : k === 3 ? "#f56565" : "#777777";
            }

            let rawGeojsonData = null;

            // 3. Setup GeoJSON Layer
            const geojsonLayer = L.geoJSON(null, {
                style: function(feature) {
                    const k = Number(feature.properties.klaster);
                    return {
                        color: getColor(k),
                        fillColor: getColor(k),
                        weight: 2,
                        fillOpacity: 0.6
                    };
                },
                onEachFeature: function(feature, layer) {
                    const p = feature.properties || {};
                    const jenis = ["-", "Aluvial Berpasir", "Grumosol", "Latosol Gelap",
                        "Latosol Putih"
                    ][Number(p.jenis_tanah) || 0];
                    const skala = ["Kecil", "Besar", "Sedang"][Number(p.klaster) || 0];
                    const color = getColor(Number(p.klaster));

                    layer.bindPopup(`
                    <div style="min-width:200px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                        <div style="display:flex; align-items:center; margin-bottom:10px; border-bottom:1px solid #eee; padding-bottom:5px;">
                            <div style="width:12px; height:12px; background:${color}; border-radius:50%; margin-right:8px;"></div>
                            <b style="font-size:14px; color:#333;">${p.nama || '-'}</b>
                        </div>
                        <div style="font-size:12px; line-height:1.6; color:#666;">
                            <b>NOP:</b> ${p.nop || '-'}<br>
                            <b>Luas:</b> ${p.luas || '-'} m²<br>
                            <b>Tanah:</b> ${jenis}<br>
                            <b>Estimasi Panen:</b> <span style="color:#2d3748; font-weight:bold;">${p.estimasi_panen || '-'} kg</span><br>
                            <div style="margin-top:5px; padding:4px 8px; background:#f7fafc; border-radius:4px; display:inline-block;">
                                <b>Skala Usaha:</b> ${skala}
                            </div>
                        </div>
                    </div>
                `);

                    layer.on('mouseover', () => layer.setStyle({
                        weight: 4,
                        fillOpacity: 0.8
                    }));
                    layer.on('mouseout', () => geojsonLayer.resetStyle(layer));
                }
            }).addTo(map);

            // 4. Fetch Data
            async function loadFeatures() {
                try {
                    const res = await fetch('/api/lahan-geojson');
                    rawGeojsonData = await res.json();
                    geojsonLayer.addData(rawGeojsonData);
                    if (geojsonLayer.getLayers().length > 0) {
                        map.fitBounds(geojsonLayer.getBounds(), {
                            padding: [40, 40]
                        });
                    }
                } catch (e) {
                    console.error("Load GeoJSON Error:", e);
                }
            }

            // 5. Fungsi Kontrol & Event
            function doSearch() {
                const term = document.getElementById('search-nop').value.trim();
                if (!term) return resetView();

                const filtered = rawGeojsonData.features.filter(f =>
                    String(f.properties.nop).toLowerCase() === term.toLowerCase()
                );

                if (filtered.length > 0) {
                    geojsonLayer.clearLayers();
                    geojsonLayer.addData({
                        type: "FeatureCollection",
                        features: filtered
                    });
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [100, 100],
                        maxZoom: 18
                    });
                    geojsonLayer.eachLayer(l => l.openPopup());
                } else {
                    alert('Nomor Objek Pajak (NOP) tidak ditemukan.');
                }
            }

            function resetView() {
                document.getElementById('search-nop').value = '';
                document.getElementById('filter-klaster').value = 'all';
                geojsonLayer.clearLayers();
                geojsonLayer.addData(rawGeojsonData);
                if (geojsonLayer.getLayers().length > 0) {
                    map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [40, 40]
                    });
                }
            }

            // Event Listeners
            document.getElementById('btn-search').addEventListener('click', doSearch);
            document.getElementById('search-nop').addEventListener('keypress', e => {
                if (e.key === 'Enter') doSearch();
            });
            document.getElementById('btn-zoom-all').addEventListener('click', resetView);

            document.getElementById('filter-klaster').addEventListener('change', function(e) {
                const v = e.target.value;
                geojsonLayer.clearLayers();
                if (v === 'all') {
                    geojsonLayer.addData(rawGeojsonData);
                } else {
                    geojsonLayer.addData({
                        type: "FeatureCollection",
                        features: rawGeojsonData.features.filter(f => String(f.properties
                            .klaster) === v)
                    });
                }
                if (geojsonLayer.getLayers().length > 0) map.fitBounds(geojsonLayer.getBounds(), {
                    padding: [50, 50]
                });
            });

            document.getElementById('select-basemap').addEventListener('change', function(e) {
                Object.values(basemaps).forEach(layer => map.removeLayer(layer));
                map.addLayer(basemaps[e.target.value]);
            });

            loadFeatures();
        });
    </script>
@endpush
