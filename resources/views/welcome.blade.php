@extends('layouts.app')

@section('title', 'Dashboard Peta')

@section('content')
<div style="display: flex; height: 90vh;">

    <!-- Sidebar kiri -->
    <div style="width: 250px; background: #f5f5f5; padding: 15px; overflow-y: auto;">
        <h3>Kontrol Map</h3>
        <label>
            <input type="checkbox" id="toggleCluster1" checked> Klaster 1
        </label><br>
        <label>
            <input type="checkbox" id="toggleCluster2" checked> Klaster 2
        </label><br>
        <label>
            <input type="checkbox" id="toggleCluster3" checked> Klaster 3
        </label>
        <hr>
        <p>Kontrol tambahan bisa ditambahkan di sini</p>
    </div>

    <!-- Map kanan -->
    <div id="map" style="flex: 1;"></div>

</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
var map = L.map('map').setView([-7.3, 109.26], 14);

// Tile layer ESRI World Imagery
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics',
    maxZoom: 20
}).addTo(map);

// Layer klaster
var clusterLayers = {
    1: L.layerGroup().addTo(map),
    2: L.layerGroup().addTo(map),
    3: L.layerGroup().addTo(map)
};

// Load GeoJSON
fetch("/api/lahan-geojson")
    .then(r => r.json())
    .then(json => {
        L.geoJSON(json, {
            style: f => ({
                color: f.properties.klaster == 1 ? "red" :
                       f.properties.klaster == 2 ? "blue" :
                       f.properties.klaster == 3 ? "green" : "gray",
                weight: 2
            }),
            onEachFeature: (feature, layer) => {
                layer.bindPopup(`
                    <b>NOP:</b> ${feature.properties.nop}<br>
                    <b>Nama:</b> ${feature.properties.nama}<br>
                    <b>Klaster:</b> ${feature.properties.klaster}
                `);
                clusterLayers[feature.properties.klaster].addLayer(layer);
            }
        });
    });

// Toggle klaster via sidebar
document.getElementById('toggleCluster1').addEventListener('change', e => {
    if(e.target.checked) map.addLayer(clusterLayers[1]);
    else map.removeLayer(clusterLayers[1]);
});
document.getElementById('toggleCluster2').addEventListener('change', e => {
    if(e.target.checked) map.addLayer(clusterLayers[2]);
    else map.removeLayer(clusterLayers[2]);
});
document.getElementById('toggleCluster3').addEventListener('change', e => {
    if(e.target.checked) map.addLayer(clusterLayers[3]);
    else map.removeLayer(clusterLayers[3]);
});
</script>
@endsection
