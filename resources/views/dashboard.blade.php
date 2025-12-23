@extends('layouts.app')

@section('title', 'Manajemen GeoJSON Lahan')

@section('content')
<div style="display: flex; height: 90vh; gap: 15px;">

    <!-- Sidebar kiri -->
    <div style="width: 300px; background: #ffffff; padding: 15px; 
                overflow-y: auto; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1)">
        
        <h4 class="mb-3">Upload GeoJSON</h4>

        @if(session('success'))
            <div class="alert alert-success">{!! session('success') !!}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{!! session('error') !!}</div>
        @endif

        <form action="{{ route('admin.geojson.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="geojson" required class="form-control mb-2">
            <button class="btn btn-primary w-100">Upload</button>
        </form>

        <h5 class="mt-4">Daftar File GeoJSON</h5>

        <ul class="list-group mb-3">
            @forelse($files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $file }}
                    <form action="{{ route('admin.geojson.import', $file) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-success">Import</button>
                    </form>
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada file.</li>
            @endforelse
        </ul>

        <a href="{{ route('admin.geojson.importAll') }}" class="btn btn-success w-100">
            Import Semua GeoJSON
        </a>
    </div>

    <!-- Map kanan -->
    <div id="map" style="flex: 1; border:1px solid #ccc; border-radius:10px; overflow:hidden"></div>
</div>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
var map = L.map('map').setView([-7.3, 109.26], 14);

// Basemap ESRI
L.tileLayer(
    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', 
{
    attribution: 'Tiles © Esri | Maxar | Earthstar Geographics',
    maxZoom: 20
}).addTo(map);

// Layer untuk menampung polygon
var geojsonLayer = L.layerGroup().addTo(map);

// Load semua polygon dari API
function loadGeoJSON() {
    fetch('/api/lahan-geojson')
        .then(r => r.json())
        .then(json => {

            geojsonLayer.clearLayers();

            const layer = L.geoJSON(json, {
    style: feature => {
        const k = parseInt(feature.properties.klaster);

        return {
            color: k === 1 ? "red" :
                   k === 2 ? "blue" :
                   k === 3 ? "green" : "gray",
            fillColor: k === 1 ? "red" :
                       k === 2 ? "blue" :
                       k === 3 ? "green" : "gray",
            weight: 2,
            fillOpacity: 0.8
        };
    },

    onEachFeature: (feature, layer) => {
        layer.bindPopup(`
            <b>NOP:</b> ${feature.properties.nop}<br>
            <b>Nama:</b> ${feature.properties.nama}<br>
            <b>Klaster:</b> ${feature.properties.klaster}
        `);
    }
}).addTo(geojsonLayer);



            // "Zoom to extent" supaya peta fokus ke area data
            try {
                map.fitBounds(layer.getBounds());
            } catch(e) {
                console.warn("Tidak bisa fit bounds (mungkin data kosong)");
            }
        });
}

// Warna klaster
function getColor(k) {
    return k == 1 ? "#ff4444" :
           k == 2 ? "#4466ff" :
           k == 3 ? "#44aa44" :
                    "#777777";
}

// Load pertama kali
loadGeoJSON();
</script>

@endsection
