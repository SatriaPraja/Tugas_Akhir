<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Informasi Geografis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .custom-panel {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
            pointer-events: auto;
        }

        .top-right-controls {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
            width: 280px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bottom-right-legend {
            position: absolute;
            bottom: 25px;
            right: 15px;
            z-index: 1000;
            width: 150px;
            padding: 10px;
        }

        .input-compact {
            height: 36px;
            font-size: 0.85rem !important;
        }
    </style>
</head>

<body class="bg-gray-100 overflow-hidden">

    <div id="notif-container" class="fixed top-5 left-1/2 -translate-x-1/2 z-[2000] pointer-events-none">
        <div id="notif-message"
            class="hidden transform transition-all duration-300 translate-y-[-20px] opacity-0 bg-red-600 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span class="font-bold text-sm tracking-wide">Data tidak ditemukan!</span>
        </div>
    </div>

    <div class="top-right-controls">
        <div class="custom-panel p-3">
            <div class="font-bold text-gray-800 text-sm mb-2">Pencarian & Filter</div>
            <div class="flex gap-1 mb-2">
                <input type="text" id="search-nop" placeholder="Cari Nama atau NOP..."
                    class="input-compact flex-1 px-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 shadow-sm">
                <button id="btn-search"
                    class="px-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 active:scale-95 transition-all text-xs">
                    Cari
                </button>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-2">
                <select id="filter-klaster"
                    class="input-compact px-1 border border-gray-200 rounded-lg bg-gray-50 text-xs font-semibold text-gray-700 outline-none">
                    <option value="all">Semua Klaster</option>
                    <option value="1">Kecil</option>
                    <option value="2">Sedang</option>
                    <option value="3">Besar</option>
                </select>
                <select id="select-basemap"
                    class="input-compact px-1 border border-gray-200 rounded-lg bg-gray-50 text-xs font-semibold text-gray-700 outline-none">
                    <option value="esri">Satelit</option>
                    <option value="osm">Jalanan</option>
                    <option value="carto">Terang</option>
                </select>
            </div>

            <button id="btn-zoom-all"
                class="w-full py-1.5 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow flex items-center justify-center gap-2 text-[11px] uppercase tracking-wide">
                🔄 Tampilkan Semua
            </button>
        </div>
    </div>

    <div class="custom-panel bottom-right-legend">
        <div class="font-bold text-gray-700 text-xs mb-2 border-b pb-1">📍 Legenda</div>
        <div class="space-y-1.5">
            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-600"><span
                    class="w-3 h-3 rounded-full bg-[#44aa44]"></span> Kecil</div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-600"><span
                    class="w-3 h-3 rounded-full bg-[#facc15]"></span> Sedang</div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-600"><span
                    class="w-3 h-3 rounded-full bg-[#f56565]"></span> Besar</div>
        </div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Map & Basemap
            const basemaps = {
                esri: L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Esri'
                    }),
                osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'OSM'
                }),
                carto: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: 'Carto'
                })
            };

            const map = L.map('map', {
                layers: [basemaps.esri],
                zoomControl: true
            }).setView([-7.3, 109.26], 13);
            map.zoomControl.setPosition('bottomleft');

            let rawGeojsonData = null;
            const geojsonLayer = L.geoJSON(null, {
                style: f => ({
                    color: getColor(f.properties.klaster),
                    fillColor: getColor(f.properties.klaster),
                    weight: 2,
                    fillOpacity: 0.6
                }),
                onEachFeature: setupFeature
            }).addTo(map);

            function getColor(k) {
                const val = Number(k);
                if (val === 1) return "#44aa44";
                if (val === 2) return "#facc15";
                if (val === 3) return "#f56565";
                return "#777777";
            }

            function setupFeature(feature, layer) {
                const p = feature.properties || {};
                const skala = {
                    1: "Kecil",
                    2: "Sedang",
                    3: "Besar"
                } [p.klaster] || "Tidak Diketahui";
                const color = getColor(p.klaster);

                layer.bindPopup(`
                    <div style="min-width:200px;">
                        <b style="font-size:14px;">${p.nama || '-'}</b><br>
                        <small>NOP: ${p.nop || '-'}</small><hr style="margin:5px 0">
                        <b>Luas:</b> ${p.luas || '-'} m²<br>
                        <b>Panen:</b> ${p.estimasi_panen || '0'} kg
                        <div style="margin-top:8px; padding:5px; background:${color}; color:white; border-radius:4px; text-align:center;">
                            <b>${skala}</b>
                        </div>
                    </div>
                `);
                layer.on('mouseover', () => layer.setStyle({
                    weight: 4,
                    fillOpacity: 0.8
                }));
                layer.on('mouseout', () => geojsonLayer.resetStyle(layer));
            }

            // 2. Fungsi Utama
            async function loadFeatures() {
                try {
                    const res = await fetch('/api/lahan-geojson');
                    rawGeojsonData = await res.json();
                    updateMapData(rawGeojsonData.features, true);
                } catch (e) {
                    console.error("Gagal memuat data:", e);
                }
            }

            function doSearch() {
                const term = document.getElementById('search-nop').value.trim().toLowerCase();
                if (!term) return resetView();

                const filtered = rawGeojsonData.features.filter(f => {
                    const nop = String(f.properties.nop || "").toLowerCase();
                    const nama = String(f.properties.nama || "").toLowerCase();
                    return nop.includes(term) || nama.includes(term);
                });

                updateMapData(filtered, true);
            }

            function updateMapData(features, zoomTo) {
                geojsonLayer.clearLayers();
                if (features && features.length > 0) {
                    geojsonLayer.addData({
                        type: "FeatureCollection",
                        features: features
                    });
                    if (zoomTo) map.fitBounds(geojsonLayer.getBounds(), {
                        padding: [50, 50],
                        maxZoom: 18
                    });

                    // Auto buka popup jika hanya 1 hasil
                    if (features.length === 1) {
                        setTimeout(() => {
                            const layers = geojsonLayer.getLayers();
                            if (layers.length > 0) layers[0].openPopup();
                        }, 500);
                    }
                } else {
                    showNotification();
                    if (rawGeojsonData) geojsonLayer.addData(rawGeojsonData);
                }
            }

            function showNotification() {
                const notif = document.getElementById('notif-message');
                notif.classList.remove('hidden');
                setTimeout(() => notif.classList.remove('translate-y-[-20px]', 'opacity-0'), 10);
                setTimeout(() => {
                    notif.classList.add('translate-y-[-20px]', 'opacity-0');
                    setTimeout(() => notif.classList.add('hidden'), 300);
                }, 3000);
            }

            function resetView() {
                document.getElementById('search-nop').value = '';
                document.getElementById('filter-klaster').value = 'all';
                updateMapData(rawGeojsonData.features, true);
            }

            // 3. Event Listeners
            document.getElementById('btn-search').addEventListener('click', doSearch);
            document.getElementById('search-nop').addEventListener('keypress', e => {
                if (e.key === 'Enter') doSearch();
            });
            document.getElementById('btn-zoom-all').addEventListener('click', resetView);
            document.getElementById('filter-klaster').addEventListener('change', e => {
                const val = e.target.value;
                const filtered = val === 'all' ? rawGeojsonData.features : rawGeojsonData.features.filter(
                    f => String(f.properties.klaster) === val);
                updateMapData(filtered, true);
            });
            document.getElementById('select-basemap').addEventListener('change', e => {
                Object.values(basemaps).forEach(l => map.removeLayer(l));
                map.addLayer(basemaps[e.target.value]);
            });

            loadFeatures();
        });
    </script>
</body>

</html>
