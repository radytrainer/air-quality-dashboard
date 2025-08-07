<template>
  <div class="container mx-auto p-4">
    <div class="map-wrapper relative mb-6">
      <div id="map" class="rounded-lg shadow-lg border border-gray-700 overflow-hidden"></div>
    </div>

    <div class="stats-grid grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <div class="stats-card p-4 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-2 text-white">Top 10 Highest AQI</h2>
        <ul class="text-white">
          <li v-for="(station, index) in top10HighAQI" :key="index" class="mb-2">
            {{ station.name }} ({{ station.country }}): 
            <span :style="{ color: getColor(station.aqi) }">{{ station.aqi }}</span>
            <img :src="station.flag" alt="Flag" class="w-12 h-8 inline-block ml-2" />
          </li>
        </ul>
      </div>
      <div class="stats-card p-4 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-2 text-white">Top 10 Lowest AQI</h2>
        <ul class="text-white">
          <li v-for="(station, index) in top10LowAQI" :key="index" class="mb-2">
            {{ station.name }} ({{ station.country }}): 
            <span :style="{ color: getColor(station.aqi) }">{{ station.aqi }}</span>
            <img :src="station.flag" alt="Flag" class="w-12 h-8 inline-block ml-2" />
          </li>
        </ul>
      </div>
      <div class="stats-card p-4 bg-gray-800 rounded-lg shadow-lg col-span-2">
        <h2 class="text-xl font-bold mb-2 text-white">AQI Trend Graph</h2>
        <canvas id="aqiChart"></canvas>
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
import Chart from 'chart.js/auto'

const aqiData = ref([])
const top10HighAQI = ref([])
const top10LowAQI = ref([])
let aqiChart = null
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

const updateChart = () => {
  const ctx = document.getElementById('aqiChart').getContext('2d')
  if (aqiChart) aqiChart.destroy()

  aqiChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: aqiData.value.map(station => station.name),
      datasets: [
        {
          label: 'AQI Levels',
          data: aqiData.value.map(station => parseInt(station.aqi) || 0),
          backgroundColor: aqiData.value.map(station => getColor(parseInt(station.aqi) || 0)),
          borderColor: '#000',
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true },
      },
    },
  })
}

const renderMarkers = () => {
  markers.forEach(marker => marker.remove())
  markers = []

  aqiData.value.forEach(station => {
    const { lat, lon, aqi, name } = station
    if (!lat || !lon) return
    const color = getColor(aqi)

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
    const url = 'http://127.0.0.1:8000/api/aqi-global' // Your backend Laravel API
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

onMounted(() => {
  initMap()
  fetchAQIData()
  setInterval(fetchAQIData, 30000) // refresh every 30 seconds
})
</script>

<style scoped>
.container {
  margin-top: 60px;
  background-color: #1a1a2e;
  color: #e0e0e0;
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
}

.stats-grid {
  margin-top: 20px;
}

.stats-card {
  border-radius: 0.75rem;
  background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
}

.stats-card table {
  border-collapse: collapse;
}

.stats-card th, .stats-card td {
  padding: 0.5rem;
}

.stats-card th {
  font-weight: bold;
  color: #b0b0b0;
}

.stats-card tbody tr:hover {
  background-color: #33334d;
}

#aqiChart {
  max-height: 300px;
  background-color: #2a2a3e;
  border-radius: 0.5rem;
  padding: 1rem;
}
</style>