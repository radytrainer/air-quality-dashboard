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
  <div class="flex justify-between mt-20 mx-6 gap-6">
      <!-- Wind Card -->
      <div class="bg-gray-800 rounded-2xl p-6 w-[48%] h-80 shadow-lg text-white">
        <div class="flex justify-center mb-4">
          <span class="text-5xl">🌬️</span>
        </div>
        <h3 class="text-xl font-semibold mb-1">Wind Speed</h3>
        <p class="text-4xl font-bold mb-2">
          {{ windSpeed !== null ? windSpeed : '-' }} m/s
        </p>
        <button
          class="bg-blue-200 text-gray-800 font-medium py-1.5 px-4 rounded-full mb-4 text-sm hover:bg-blue-300 transition"
        >
          {{ directionString }}
        </button>

        <div class="flex justify-between text-left mb-4">
          <div>
            <p class="text-sm text-gray-400">Gust Speed</p>
            <p class="text-xl font-bold flex items-center">
              <span class="text-2xl mr-1">🌬️</span>
              {{ gustSpeed !== null ? gustSpeed : '-' }} m/s
            </p>
          </div>
          <div>
            <p class="text-sm text-gray-400">Direction</p>
            <p class="text-xl font-bold flex items-center">
              <span class="text-blue-400 mr-1 text-2xl">↗️</span>
              {{ windDirection || '-' }}
            </p>
          </div>
        </div>
        <p class="text-xs text-gray-500 text-center">
          Current wind speed is {{ windSpeed !== null ? windSpeed : '-' }} m/s,
          with gusts at {{ gustSpeed !== null ? gustSpeed : '-' }} m/s
        </p>
      </div>

      <!-- Suggestion Card -->
      <div class="bg-gray-800 rounded-2xl p-6 w-[48%] h-80 shadow-lg text-white">
        <div class="flex justify-between mb-2 text-sm text-gray-400">
          <span>Suggestions for</span>
          <span>Today</span>
        </div>
        <div class="flex justify-between mb-6 text-sm text-gray-400">
          <span>{{ searchQuery }}</span>
          <span>{{ currentTime }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center mb-4">
          <div>
            <p class="text-lg font-semibold mb-1">Heat Stroke</p>
            <p class="text-blue-400 flex items-center justify-center text-sm">
              <span class="text-2xl mr-1">🌡️</span> Avoid Outdoor
            </p>
          </div>
          <div>
            <p class="text-lg font-semibold mb-1">Clothing</p>
            <p class="text-blue-400 flex items-center justify-center text-sm">
              <span class="text-2xl mr-1">👕</span> Breathable
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-center">
          <div>
            <p class="text-lg font-semibold mb-1">Driving</p>
            <p class="text-blue-400 flex items-center justify-center text-sm">
              <span class="text-2xl mr-1">🚗</span> Enjoy Driving
            </p>
          </div>
          <div>
            <p class="text-lg font-semibold mb-1">Sunscreen</p>
            <p class="text-blue-400 flex items-center justify-center text-sm">
              <span class="text-2xl mr-1">☀️</span> Apply
            </p>
          </div>
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
const windSpeed = ref(null)
const windDirection = ref(null)

let map = null
let markers = []

const getColor = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return '#3cb43c'    // stronger green
  if (val <= 100) return '#e6e600'   // brighter yellow
  if (val <= 150) return '#ff9900'   // deeper orange
  if (val <= 200) return '#ff4d4d'   // stronger red
  if (val <= 300) return '#9b59b6'   // richer purple
  return '#c0392b'                   // darker maroon
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
  if (!Array.isArray(aqiData.value)) {
    console.error('aqiData is not an array in updateTop10', aqiData.value)
    return
  }
  const sorted = [...aqiData.value]
    .filter(s => !isNaN(parseInt(s.aqi)))
    .sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))
  top10HighAQI.value = sorted.slice(0, 10)
  top10LowAQI.value = sorted.slice(-10).reverse()
}

// Global object to hold markers keyed by station id or lat-lon string
const markersMap = new Map()

const renderMarkers = () => {
  if (!Array.isArray(aqiData.value)) {
    console.error('aqiData is not an array in renderMarkers', aqiData.value)
    return
  }

  const activeKeys = new Set()

  aqiData.value.forEach(station => {
    if (!station.lat || !station.lon) return

    const key = `${station.lat},${station.lon}`
    activeKeys.add(key)

    const color = getColor(station.aqi)

    const iconHtml = `
      <div style="
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: ${color};
        color: white;
        font-size: 8px;
        font-weight: 600;
        border: 1px solid rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
      ">
        ${station.aqi}
      </div>
    `;



    const customIcon = L.divIcon({
      html: iconHtml,
      className: '', // Remove default Leaflet styles
      iconSize: [32, 32],
      iconAnchor: [16, 16], // center the icon
    })

    if (markersMap.has(key)) {
      const marker = markersMap.get(key)
      marker.setLatLng([station.lat, station.lon])
      marker.setIcon(customIcon)
    } else {
      const marker = L.marker([station.lat, station.lon], { icon: customIcon }).addTo(map)
      markersMap.set(key, marker)
    }
  })

  // Clean up markers no longer needed
  for (const [key, marker] of markersMap.entries()) {
    if (!activeKeys.has(key)) {
      map.removeLayer(marker)
      markersMap.delete(key)
    }
  }
}


const fetchAQIData = async () => {
  try {
    const url = 'http://127.0.0.1:8000/api/aqi-global'
    const { data } = await axios.get(url)

    if (data.status === 'ok' && Array.isArray(data.data)) {
      aqiData.value = data.data
      updateTop10()
      renderMarkers()
      // Removed updateChart() because it was undefined
    } else {
      console.error('API returned unexpected data:', data)
      aqiData.value = []
    }
  } catch (err) {
    console.error('Failed to fetch AQI:', err)
    aqiData.value = []
  }
  currentTime.value = new Date()
}

const addWindLayer = async () => {
  try {
    const res = await axios.get(
      'https://raw.githubusercontent.com/danwild/leaflet-velocity/master/demo/wind-global.json'
    )
    const windData = res.data

    // Create velocity layer
    const velocityLayer = L.velocityLayer({
      displayValues: true,
      displayOptions: {
        velocityType: 'Global Wind',
        position: 'bottomleft',
        emptyString: 'No wind data',
        speedUnit: 'm/s',
        directionString: 'Direction'
      },
      data: windData,
      maxVelocity: 15
    })

    velocityLayer.addTo(map)

    // Listen for clicks
    map.on('click', (e) => {
      const lat = e.latlng.lat
      const lng = e.latlng.lng

      // Find nearest wind data point
      let nearest = null
      let minDist = Infinity
      windData[0].data.forEach(point => {
        const [lon, latPoint, u, v] = point
        const dist = Math.sqrt(
          Math.pow(lat - latPoint, 2) + Math.pow(lng - lon, 2)
        )
        if (dist < minDist) {
          minDist = dist
          nearest = { lon, latPoint, u, v }
        }
      })

      if (nearest) {
        const speed = Math.sqrt(nearest.u ** 2 + nearest.v ** 2).toFixed(2)
        const direction = (Math.atan2(nearest.u, nearest.v) * 180 / Math.PI + 360) % 360

        // Update reactive variables
        windSpeed.value = parseFloat(speed)
        windDirection.value = parseFloat(direction.toFixed(1))

        // Optional: keep popup
        L.popup()
          .setLatLng(e.latlng)
          .setContent(
            `<b>Wind Speed:</b> ${speed} m/s<br><b>Direction:</b> ${direction.toFixed(1)}°`
          )
          .openOn(map)
      }
    })
  } catch (error) {
    console.error('Error loading wind data:', error)
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
