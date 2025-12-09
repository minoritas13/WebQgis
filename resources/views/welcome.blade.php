<div id="map" style="height:500px">

</div>
<script>
var map = L.map('map').setView([-7.5, 110.2], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

fetch('/api/geojson-file')
  .then(r => r.json())
  .then(data => {
    L.geoJSON(data, {
      style: feature => ({
        color: feature.properties?.warna ?? '#3388ff',
        weight: feature.properties?.ketebalan ?? 2
      }),
      pointToLayer: (feature, latlng) => {
        return L.marker(latlng, {
          icon: L.icon({ iconUrl: '/icons/marker.png', iconSize: [25,41] })
        });
      },
      onEachFeature: (feature, layer) => {
        if (feature.properties) {
          let html = Object.entries(feature.properties).map(([k,v]) => `<b>${k}</b>: ${v}`).join('<br>');
          layer.bindPopup(html);
        }
      }
    }).addTo(map);
  });
</script>
