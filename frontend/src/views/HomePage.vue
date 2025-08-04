<template>
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mt-10 px-6">
      <h1 class="text-4xl font-bold tracking-wide">🌍 Air Quality Dashboard</h1>
      <p class="text-sm text-black/80 mt-2">Monitor real-time air quality across the globe</p>
      <p class="text-xs text-black/70 mt-1">Last updated: {{ currentTime.toLocaleString() }}</p>
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

    <!-- Main -->
    <main class="p-6 space-y-10">
      <!-- Pollutant Filters -->
      <section>
        <h2 class="text-3xl font-semibold mb-4 text-gray-700">🌍 Global Air Quality Map</h2>
        <div class="flex flex-wrap gap-4 mb-4 items-center text-sm text-gray-700">
          <label v-for="pollutant in pollutants" :key="pollutant.value" class="flex items-center gap-2">
            <input
              type="radio"
              name="pollutant"
              :value="pollutant.value"
              v-model="selectedPollutant"
              @change="updateMap"
            />
            {{ pollutant.label }}
          </label>
        </div>

        <div v-if="loading" class="text-center text-gray-600">Loading map and data...</div>
        <div v-if="error" class="text-center text-red-600">{{ error }}</div>
        <div class="map-wrapper">
          <div id="map"></div>
        </div>
      </section>

      <!-- AQI Grid -->
      <section>
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">🌐 Recent Data</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="station in aqiData"
            :key="station.uid"
            class="bg-white rounded-lg shadow-md p-4 border border-gray-100"
          >
            <h3 class="text-lg font-semibold mb-2">{{ station.station.name }}</h3>
            <p class="text-sm text-gray-600 capitalize">
              {{ selectedPollutant.toUpperCase() }}: <span class="font-bold">{{ station.value }}</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">Status: {{ getStatusLabel(station.value) }}</p>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-velocity/dist/leaflet-velocity.min.js'
import 'leaflet-velocity/dist/leaflet-velocity.min.css'

const TOKEN = '9c81a4f2fcf022539c917fdefba185ff9369865d'
const aqiData = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')
const currentTime = ref(new Date())

let map = null
const markers = ref([])

const pollutants = ref([
  { label: 'AQI', value: 'aqi' },
  { label: 'PM₂.₅', value: 'pm25' },
  { label: 'PM₁₀', value: 'pm10' },
  { label: 'Ozone (O₃)', value: 'o3' },
  { label: 'NO₂', value: 'no2' },
  { label: 'SO₂', value: 'so2' },
  { label: 'CO', value: 'co' },
])
const selectedPollutant = ref('aqi')

// Setup map
const initMap = async () => {
  map = L.map('map', {
    center: [20, 100],
    zoom: 3,
    zoomControl: true,
    scrollWheelZoom: false
  })

  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap & CARTO',
    subdomains: 'abcd',
    maxZoom: 19,
  }).addTo(map)

  // Smooth scroll interaction
  map.on('focus', () => map.scrollWheelZoom.enable())
  map.on('blur', () => map.scrollWheelZoom.disable())
  map.on('mousewheel', (e) => {
    if (!e.originalEvent.ctrlKey) {
      map.scrollWheelZoom.disable()
      e.originalEvent.preventDefault()
    } else {
      map.scrollWheelZoom.enable()
    }
  })
}

// Add wind speed layer
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
        speedUnit: 'km/h',
      },
      data: windData,
      maxVelocity: 15,
    })

    velocityLayer.addTo(map)
  } catch (error) {
    console.error('Failed to load wind data:', error)
  }
}

// Fetch and update AQI map
const updateMap = async () => {
  loading.value = true
  aqiData.value = []
  markers.value.forEach(marker => map.removeLayer(marker))
  markers.value = []

  try {
    const bounds = '-85,-180,85,180'
    const boundsURL = `https://api.waqi.info/map/bounds/?latlng=${bounds}&token=${TOKEN}`
    const { data } = await axios.get(boundsURL)

    if (data.status === 'ok') {
      const stations = data.data.slice(0, 100)
      const results = []

      for (const s of stations) {
        const detailUrl = `https://api.waqi.info/feed/geo:${s.lat};${s.lon}/?token=${TOKEN}`
        const res = await axios.get(detailUrl)
        const detail = res.data.data

        const value = selectedPollutant.value === 'aqi'
          ? detail.aqi
          : detail.iaqi?.[selectedPollutant.value]?.v

        if (value !== undefined && value !== '-') {
          results.push({ ...s, value })

          const marker = L.circleMarker([s.lat, s.lon], {
            radius: 6,
            fillColor: getColor(value),
            color: '#000',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
          }).addTo(map)

          marker.bindPopup(`
            <div style="font-family: Arial; font-size: 13px;">
              <b>${s.station.name}</b><br/>
              ${selectedPollutant.value.toUpperCase()}: <strong>${value}</strong><br/>
              Status: <span style="color:${getColor(value)}">${getStatusLabel(value)}</span>
            </div>
          `)

          markers.value.push(marker)
        }
      }

      aqiData.value = results
    } else {
      error.value = 'Failed to load station data.'
    }
  } catch (err) {
    console.error(err)
    error.value = 'Error loading data.'
  } finally {
    loading.value = false
    currentTime.value = new Date()
  }
}

// Search location
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

const getColor = (value) => {
  const v = parseInt(value)
  if (v <= 50) return '#00e400'
  if (v <= 100) return '#ffff00'
  if (v <= 150) return '#ff7e00'
  if (v <= 200) return '#ff0000'
  if (v <= 300) return '#99004c'
  return '#7e0023'
}

const getStatusLabel = (value) => {
  const v = parseInt(value)
  if (v <= 50) return 'Good'
  if (v <= 100) return 'Moderate'
  if (v <= 150) return 'Unhealthy for Sensitive Groups'
  if (v <= 200) return 'Unhealthy'
  if (v <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}

onMounted(async () => {
  await initMap()
  await addWindLayer()
  await updateMap()
})
</script>

<style scoped>
.map-wrapper {
  height: 600px;
  width: 100%;
  padding: 0 1rem;
  position: relative;
  z-index: 0;
}

#map {
  width: 100%;
  height: 100%;
  border-radius: 0.75rem;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
}
</style>
