<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta WebGIS</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        #map { height: 600px; width: 100%; }

        #list {
            margin-top: 20px;
            padding: 15px;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .item {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
        }

        .item:last-child { border-bottom: none; }
        .item:hover { color: blue; }
    </style>
</head>

<body>

    <h2 style="text-align: center; margin-top: 10px;">Peta WebGIS (Leaflet + GeoJSON)</h2>

    <div id="map"></div>

    <h3>Daftar Sekolahan / Titik (10 Data)</h3>
    <div id="list"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Inisialisasi Peta
        var map = L.map('map').setView([-5.3565, 104.9747], 12);

        // Tambahkan basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Ambil GeoJSON
        fetch("{{ route('geojson.show') }}")
            .then(response => response.json())
            .then(data => {

                var listContainer = document.getElementById("list");
                var markers = [];  // Simpan semua marker

                // Custom Icon
                var customIcon = L.icon({
                    iconUrl: "/storage/icons/gas.png",
                    iconSize: [35, 35],
                    iconAnchor: [17, 34],
                    popupAnchor: [0, -30]
                });

                // Tambahkan GeoJSON hanya sekali (TIDAK DOUBEL)
                var geoLayer = L.geoJSON(data, {

                    pointToLayer: function(feature, latlng) {

                        let marker = L.marker(latlng, { icon: customIcon });

                        // Simpan marker untuk daftar
                        markers.push({
                            name: feature.properties?.NAMA_SEKOLAH ?? "Tidak ada nama",
                            popupContent: feature.properties,
                            marker: marker,
                            latlng: latlng
                        });

                        return marker;
                    },

                    style: function(feature) {
                        let type = feature.geometry.type;

                        if (type === "Polygon" || type === "MultiPolygon") {
                            return { color: "red", weight: 2, fillOpacity: 0.4 };
                        }
                        if (type === "LineString" || type === "MultiLineString") {
                            return { color: "blue", weight: 3 };
                        }
                    },

                    onEachFeature: function(feature, layer) {
                        let content = "<b>Informasi Atribut</b><br>";
                        for (const key in feature.properties) {
                            content += `${key}: ${feature.properties[key]}<br>`;
                        }
                        layer.bindPopup(content);
                    }

                }).addTo(map);

                // Auto zoom ke seluruh GeoJSON
                map.fitBounds(geoLayer.getBounds());

                // ==== Daftar 10 sekolah ====
                let limitedMarkers = markers.slice(0, 10);

                limitedMarkers.forEach((item, index) => {
                    let div = document.createElement("div");
                    div.className = "item";
                    div.innerHTML = `${index + 1}. ${item.name}`;
                    div.onclick = () => {
                        map.setView(item.latlng, 17);
                        item.marker.openPopup();
                    };
                    listContainer.appendChild(div);
                });

            })
            .catch(error => console.error("Error load GeoJSON:", error));
    </script>

</body>
</html>
