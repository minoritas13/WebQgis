<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peta Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .card-list-item:hover {
            background: #f9fafb;
        }
    </style>
</head>

<body class="font-sans">

    <!-- NAVBAR -->
    <nav class="w-full bg-orange-500 text-white px-6 py-4 shadow-lg sticky top-0 z-50">
        <div class="flex justify-between items-center max-w-full">
            <div class="text-2xl font-bold">WebGIS Sekolah</div>
        </div>
    </nav>

    <div class="max-w-full p-4 mt-4">
        <div class="rounded-3xl p-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LIST SEKOLAH -->
                <div class="col-span-1 bg-gray-50 rounded-2xl p-4 overflow-y-auto max-h-[650px] shadow-inner">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Daftar Sekolah</h3>
                    <div id="list" class="space-y-3"></div>
                </div>

                <!-- MAP -->
                <div class="col-span-2 relative">
                    <div id="map" class="w-full h-[650px] rounded-2xl shadow"></div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([-5.3565, 104.9747], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        const listContainer = document.getElementById('list');
        const markers = [];

        fetch("{{ route('geojson.show') }}")
            .then(response => response.json())
            .then(data => {
                const geojsonData = data.geojson;

                const customIcon = L.icon({
                    iconUrl: '/storage/icons/gas.png',
                    iconSize: [35, 35],
                    iconAnchor: [17, 34]
                });

                const geoLayer = L.geoJSON(geojsonData, {
                    pointToLayer: function(feature) {
                        const coords = feature.geometry.coordinates;
                        const latlng = [coords[1], coords[0]];

                        const marker = L.marker(latlng, {
                            icon: customIcon
                        });
                        markers.push({
                            name: feature.properties?.NAMA_SEKOLAH ?? 'Tidak ada nama',
                            data: feature.properties,
                            marker: marker,
                            latlng: latlng
                        });
                        return marker;
                    },
                    onEachFeature: (feature, layer) => {
                        let content = '<b>Informasi Sekolah</b><br>';
                        for (const key in feature.properties) content +=
                            `${key}: ${feature.properties[key]}<br>`;
                        layer.bindPopup(content);
                    }
                }).addTo(map);

                map.fitBounds(geoLayer.getBounds());

                const limitedMarkers = markers.slice(0, 10);

                limitedMarkers.forEach((item, index) => {
                    const div = document.createElement('div');
                    div.className = 'card-list-item p-4 rounded-xl border bg-white shadow cursor-pointer';
                    div.innerHTML = `
        <div class="font-bold text-lg text-gray-800">${item.name}</div>
        <div class="text-sm text-gray-600">${item.data.JENJANG} • ${item.data.STATUS}</div>
        <div class="text-xs text-gray-500 mt-1">${item.data.ALAMAT}</div>
      `;
                    div.onclick = () => {
                        map.setView(item.latlng, 17);
                        item.marker.openPopup();
                    };
                    listContainer.appendChild(div);
                });
            })
            .catch(err => console.error('Error load GeoJSON:', err));
    </script>
</body>

</html>
