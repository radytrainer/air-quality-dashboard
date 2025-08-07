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

    <!-- Top 10 Lists -->
    <div class="stats-grid grid grid-cols-1 md:grid-cols-2 gap-6 px-6 mt-10">
      <div class="stats-card p-4 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-2 text-white">Top 10 Highest AQI</h2>
        <ul class="text-white">
          <li v-for="(station, index) in top10HighAQI" :key="index" class="mb-2">
            {{ station.name }}: 
            <span :style="{ color: getColor(station.aqi) }">{{ station.aqi }}</span>
            <span class="ml-1 text-sm italic">({{ getStatus(station.aqi) }})</span>
            <img :src="station.flag" alt="Flag" class="w-12 h-8 inline-block ml-2" />
          </li>
        </ul>
      </div>

      <div class="stats-card p-4 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-2 text-white">Top 10 Lowest AQI</h2>
        <ul class="text-white">
          <li v-for="(station, index) in top10LowAQI" :key="index" class="mb-2">
            {{ station.name }}: 
            <span :style="{ color: getColor(station.aqi) }">{{ station.aqi }}</span>
            <span class="ml-1 text-sm italic">({{ getStatus(station.aqi) }})</span>
            <img :src="station.flag" alt="Flag" class="w-12 h-8 inline-block ml-2" />
          </li>
        </ul>
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
  if (val <= 300) return '#99004c'     // Very Unhealthy - Purple
  return '#7e0023'                     // Hazardous - Maroon
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

const fetchAQIData = async () => {
  try {
    const url = 'http://127.0.0.1:8000/api/aqi-global' // Your backend API URL
    const { data } = await axios.get(url)

    if (data.status === 'ok' && Array.isArray(data.data)) {
      aqiData.value = data.data.map(station => {
        // Example: generate flag url using country code (assuming station.country_code)
        // Use a fallback flag image if no country_code or image available
        const flag = station.country_code
          ? `https://flagcdn.com/w40/${station.country_code.toLowerCase()}.png`
          : ''

        return {
          ...station,
          flag,
        }
      })

      updateTop10()
      renderMarkers()
      currentTime.value = new Date()
    } else {
      console.error('API returned unexpected data:', data)
    }
  } catch (err) {
    console.error('Failed to fetch AQI:', err)
  }
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

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap & CARTO',
    subdomains: 'abcd',
    maxZoom: 19,
  }).addTo(map)

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
