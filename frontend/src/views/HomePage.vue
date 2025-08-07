<template>
  <div class="container mx-auto p-4">
    <!-- Header -->
    <div class="mt-10 px-6">
      <div class="bg-gradient-to-r from-sky-100 to-white rounded-2xl shadow-sm p-6 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-800 flex items-center justify-center gap-2">
          <span class="text-sky-600 text-4xl"></span> Air Quality Dashboard
        </h1>
        <p class="text-base sm:text-lg text-gray-600 mt-2">Monitor real-time air quality across the globe</p>
        <p class="text-xs text-gray-500 mt-1 italic">Last updated: {{ currentTime.toLocaleString() }}</p>
      </div>
    </div>

    <!-- Search -->
    <div class="px-6 mt-4 flex flex-col items-end space-y-6 mb-10">
      <div class="flex space-x-2 w-full max-w-md">
        <input
          v-model="searchQuery"
          @keydown.enter="searchLocation"
          type="text"
          placeholder="Search by city or country"
          class="flex-1 border border-gray-300 rounded-md px-3 py-2"
        />
        <button @click="searchLocation" class="bg-sky-500 hover:bg-sky-600 text-white rounded-md px-4 py-2">
          Search
        </button>
      </div>
    </div>

    <!-- Map -->
    <div class="map-wrapper relative mb-6 px-6">
      <div id="map" class="rounded-lg shadow-lg border border-gray-700 overflow-hidden" style="height: 500px;"></div>
    </div>



    <!-- Top 10 AQI Lists -->
    <h1 class="text-3xl md:text-4xl font-extrabold text-center text-gray-800 tracking-tight mb-8 drop-shadow-sm">
      Top 10 Air Quality Index (AQI) Lists
    </h1>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-6 mt-12">
      <!-- Top 10 Most Polluted Cities -->
      <div class="bg-white rounded-2xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-red-600 mb-4 border-b pb-2 flex items-center gap-2">
          🔥 Top 10 Most Polluted Cities
        </h2>
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto text-sm">
            <thead class="bg-red-600 text-white uppercase text-xs">
              <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Flag</th>
                <th class="px-4 py-3 text-left">City</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-center">AQI</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-800">
              <tr v-for="(station, index) in top10HighAQI" :key="index" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2">
                  <img :src="station.flag" alt="Flag" class="w-10 h-6 object-cover rounded-sm shadow" />
                </td>
                <td class="px-4 py-2 font-semibold">{{ station.name }}</td>
                <td class="px-4 py-2">
                  <span
                    class="px-3 py-1 rounded-full text-white text-xs font-medium shadow whitespace-nowrap"
                    :style="{ backgroundColor: getColor(station.aqi) }"
                  >
                    {{ getStatus(station.aqi) }}
                  </span>
                </td>

                <td class="px-4 py-2 text-center">
                  <span
                    class="px-3 py-1 rounded-full text-white text-xs font-semibold"
                    :style="{ backgroundColor: getColor(station.aqi) }"
                  >
                    {{ station.aqi }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top 10 Cleanest Cities -->
      <div class="bg-white rounded-2xl shadow-md p-6">
        <h2 class="text-xl font-semibold text-green-600 mb-4 border-b pb-2 flex items-center gap-2">
          🌱 Top 10 Cleanest Air Cities
        </h2>
        <div class="overflow-x-auto">
          <table class="min-w-full table-auto text-sm">
            <thead class="bg-green-600 text-white uppercase text-xs">
              <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Flag</th>
                <th class="px-4 py-3 text-left">City</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-center">AQI</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-800">
              <tr v-for="(station, index) in top10LowAQI" :key="index" class="hover:bg-gray-50">
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2">
                  <img :src="station.flag" alt="Flag" class="w-10 h-6 object-cover rounded-sm shadow" />
                </td>
                <td class="px-4 py-2 font-semibold">{{ station.name }}</td>
                <td class="px-4 py-2">
                  <span
                    class="px-3 py-1 rounded-full text-white text-xs font-semibold"
                    :style="{ backgroundColor: getColor(station.aqi) }"
                  >
                    {{ getStatus(station.aqi) }}
                  </span>
                </td>
                <td class="px-4 py-2 text-center">
                  <span
                    class="px-3 py-1 rounded-full text-white text-xs font-semibold"
                    :style="{ backgroundColor: getColor(station.aqi) }"
                  >
                    {{ station.aqi }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-velocity/dist/leaflet-velocity.min.js'
import 'leaflet-velocity/dist/leaflet-velocity.min.css'
import axios from 'axios'

const aqiData = ref([])
const top10HighAQI = ref([])
const top10LowAQI = ref([])
const currentTime = ref(new Date())
const searchQuery = ref('')

let map = null
let markers = []


const getColor = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return '#00e400'      // Good - Green
  if (val <= 100) return '#ffff00'     // Moderate - Yellow
  if (val <= 150) return '#ff7e00'     // Unhealthy for Sensitive Groups - Orange
  if (val <= 200) return '#ff0000'     // Unhealthy - Red
  if (val <= 300) return '#7702ad'     // Very Unhealthy - Purple
  return '#94012a'                     // Hazardous - Maroon
}

const getStatus = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return 'Good'
  if (val <= 100) return 'Moderate'
  if (val <= 150) return 'Unhealthy for Sensitive Groups'
  if (val <= 200) return 'Unhealthy'
  if (val <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}


const updateTop10 = () => {
  const sorted = [...aqiData.value]
    .filter(s => !isNaN(parseInt(s.aqi)))
    .sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))
  top10HighAQI.value = sorted.slice(0, 10)
  top10LowAQI.value = sorted.slice(-10).reverse()
}

const renderMarkers = () => {
  markers.forEach(marker => marker.remove())
  markers = []

  aqiData.value.forEach(station => {
    if (!station.lat || !station.lon) return

    const color = getColor(station.aqi)
    const status = getStatus(station.aqi)

    const marker = L.circleMarker([station.lat, station.lon], {
      radius: 6,
      fillColor: color,
      color: '#000',
      weight: 0.8,
      opacity: 1,
      fillOpacity: 0.8,
    }).addTo(map)

    marker.bindPopup(`
      <div style="font-family: Arial; font-size: 13px;">
        <b>${station.name}</b><br/>
        AQI: <strong style="color: ${color}">${station.aqi}</strong><br/>
        Status: <em>${status}</em>
      </div>
    `)

    markers.push(marker)
  })
}


// Fetch AQI data (from your backend API)
const fetchAQIData = async () => {
  try {
    const url = 'http://127.0.0.1:8000/api/aqi-global'
    const { data } = await axios.get(url)

    if (data.status === 'ok' && Array.isArray(data.data)) {
      aqiData.value = data.data
      updateTop10()
      renderMarkers()
      updateChart()
    } else {
      console.error('API returned unexpected data:', data)
    }
  } catch (err) {
    console.error('Failed to fetch AQI:', err)
  }
  currentTime.value = new Date()
}
const addWindLayer = async () => {
  try {
    const res = await axios.get('https://raw.githubusercontent.com/danwild/leaflet-velocity/master/demo/wind-global.json')
    const windData = res.data

    const velocityLayer = L.velocityLayer({
      displayValues: true,
      displayOptions: {
        velocityType: 'Global Wind',
        position: 'bottomleft',
        emptyString: 'No wind data',
        angleConvention: 'bearingCW',
        speedUnit: 'mph',
      },
      data: windData,
      maxVelocity: 15,
    })

    velocityLayer.addTo(map)
  } catch (error) {
    console.error('Failed to load wind data:', error)
  }
}

const initMap = () => {
  map = L.map('map', {
    center: [20, 100],
    zoom: 4,
    zoomControl: true,
    scrollWheelZoom: false,
    attributionControl: false,
  })

  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap & CARTO',
    subdomains: 'abcd',
    maxZoom: 19,
  }).addTo(map)

  addLegend()

  map.on('focus', () => map.scrollWheelZoom.enable())
  map.on('blur', () => map.scrollWheelZoom.disable())
  map.on('mousewheel', e => {
    if (!e.originalEvent.ctrlKey) {
      map.scrollWheelZoom.disable()
      e.originalEvent.preventDefault()
    } else {
      map.scrollWheelZoom.enable()
    }
  })

  addWindLayer()
}

const addLegend = () => {
  const legend = L.control({ position: 'bottomright' })

  legend.onAdd = () => {
    const div = L.DomUtil.create('div', 'info legend')
    const grades = [0, 51, 101, 151, 201, 301]
    const labels = ['Good', 'Moderate', 'Unhealthy for Sensitive Groups', 'Unhealthy', 'Very Unhealthy', 'Hazardous']
    const colors = ['#00e400', '#ffff00', '#ff7e00', '#ff0000', '#7702ad', '#94012a']

    let html = '<h4>AQI Legend</h4>'
    for (let i = 0; i < grades.length; i++) {
      html +=
        `<i style="background:${colors[i]}; width:18px; height:18px; display:inline-block; margin-right:8px;"></i>` +
        `${labels[i]}<br/>`
    }
    div.innerHTML = html
    return div
  }

  legend.addTo(map)
}

const searchLocation = async () => {
  if (!searchQuery.value) return
  try {
    const result = await axios.get(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery.value)}&format=json&limit=1`)
    if (result.data.length) {
      const { lat, lon } = result.data[0]
      map.setView([parseFloat(lat), parseFloat(lon)], 8)
    } else {
      alert('Location not found')
    }
  } catch (e) {
    alert('Error searching location')
  }
}

onMounted(() => {
  initMap()
  fetchAQIData()
  setInterval(fetchAQIData, 30000) // refresh every 30 seconds
})
</script>

<style scoped>
.container {
  margin-top: 60px;
}

.map-wrapper {
  height: 500px;
  width: 100%;
  padding: 0 1rem;
  box-sizing: border-box;
  position: relative;
  z-index: 0;
}

#map {
  width: 100%;
  height: 100%;
  border-radius: 0.75rem;
  transition: box-shadow 0.3s ease;
}


.stats-grid {
  margin-top: 20px;
}

.stats-card {
  border-radius: 0.75rem;
}
</style>
