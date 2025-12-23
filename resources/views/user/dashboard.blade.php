<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Sistem Informasi Geografis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

        @media (max-width: 640px) {
            .top-right-controls {
                width: calc(100% - 30px);
                top: 10px;
                right: 15px;
            }

            .bottom-right-legend {
                bottom: 80px;
                width: auto;
                display: flex;
                gap: 10px;
            }
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="top-right-controls">
        <div class="custom-panel p-3">
            <div class="font-bold text-gray-800 text-sm mb-2">Pencarian & Filter</div>
            <div class="flex gap-1 mb-2">
                <input type="text" id="search-nop" placeholder="Cari NOP..."
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
            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-600">
                <span class="w-3 h-3 rounded-full bg-[#44aa44]"></span> Kecil
            </div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-600">
                <span class="w-3 h-3 rounded-full bg-[#facc15]"></span> Sedang
            </div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-gray-600">
                <span class="w-3 h-3 rounded-full bg-[#f56565]"></span> Besar
            </div>
        </div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Konsisten: 1=Hijau, 2=Kuning, 3=Merah
            function getColor(k) {
                const val = Number(k);
                if (val === 1) return "#44aa44";
                if (val === 2) return "#facc15";
                if (val === 3) return "#f56565";
                return "#777777";
            }

            let rawGeojsonData = null;
            const geojsonLayer = L.geoJSON(null, {
                style: f => ({
                    color: getColor(f.properties.klaster),
                    fillColor: getColor(f.properties.klaster),
                    weight: 2,
                    fillOpacity: 0.6
                }),
                onEachFeature: function(feature, layer) {
                    const p = feature.properties || {};
                    const jenis = ["-", "Aluvial Berpasir", "Grumosol", "Latosol Gelap",
                        "Latosol Putih"
                    ][Number(p.jenis_tanah) || 0];
                    // Skala 1: Kecil, 2: Sedang, 3: Besar
                    const skala = ["-", "Kecil", "Sedang", "Besar"][Number(p.klaster) || 0];

                    layer.bindPopup(`
                        <div style="min-width:180px">
                            <div style="display:flex;align-items:center;margin-bottom:8px;">
                                <div style="width:10px;height:10px;background:${getColor(p.klaster)};border-radius:50%;margin-right:8px;"></div>
                                <b>${p.nama || '-'}</b>
                            </div>
                            <div style="font-size:0.8rem; line-height:1.4; color:#444;">
                                <div><b>NOP:</b> ${p.nop || '-'}</div>
                                <div><b>Luas:</b> ${p.luas || '-'} m²</div>
                                <div><b>Tanah:</b> ${jenis}</div>
                                <div><b>Panen:</b> ${p.estimasi_panen || '-'} kg</div>
                                <div style="margin-top:4px; padding-top:4px; border-top:1px solid #eee;">
                                    <b>Skala:</b> <span style="font-weight:bold; color:${getColor(p.klaster)}">${skala}</span>
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

            async function loadFeatures() {
                try {
                    const res = await fetch('/api/lahan-geojson');
                    rawGeojsonData = await res.json();
                    geojsonLayer.addData(rawGeojsonData);
                    if (geojsonLayer.getLayers().length > 0) {
                        map.fitBounds(geojsonLayer.getBounds(), {
                            padding: [20, 20]
                        });
                    }
                } catch (e) {
                    console.error("Gagal memuat data:", e);
                }
            }
            loadFeatures();

            function doSearch() {
                const term = document.getElementById('search-nop').value.trim().toLowerCase();
                if (!term) return resetView();

                const filtered = rawGeojsonData.features.filter(f =>
                    String(f.properties.nop).toLowerCase().includes(term)
                );

                updateMapData(filtered, true);
            }

            function updateMapData(features, zoomTo) {
                geojsonLayer.clearLayers();
                if (features.length > 0) {
                    geojsonLayer.addData({
                        type: "FeatureCollection",
                        features: features
                    });
                    if (zoomTo) {
                        map.fitBounds(geojsonLayer.getBounds(), {
                            padding: [50, 50],
                            maxZoom: 18
                        });
                    }
                } else if (zoomTo) {
                    alert('Data tidak ditemukan!');
                }
            }

            function resetView() {
                document.getElementById('search-nop').value = '';
                document.getElementById('filter-klaster').value = 'all';
                updateMapData(rawGeojsonData.features, true);
            }

            // Event Listeners
            document.getElementById('btn-search').addEventListener('click', doSearch);
            document.getElementById('search-nop').addEventListener('keypress', e => {
                if (e.key === 'Enter') doSearch();
            });
            document.getElementById('btn-zoom-all').addEventListener('click', resetView);

            document.getElementById('filter-klaster').addEventListener('change', function(e) {
                const val = e.target.value;
                if (val === 'all') {
                    updateMapData(rawGeojsonData.features, true);
                } else {
                    const filtered = rawGeojsonData.features.filter(f =>
                        String(f.properties.klaster) === val
                    );
                    updateMapData(filtered, true);
                }
            });

            document.getElementById('select-basemap').addEventListener('change', function(e) {
                Object.values(basemaps).forEach(layer => map.removeLayer(layer));
                map.addLayer(basemaps[e.target.value]);
            });
        });
    </script>
</body>

</html>
