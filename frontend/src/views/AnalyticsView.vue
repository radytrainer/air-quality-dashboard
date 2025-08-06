<template>
  <div class="container mx-auto p-4">
    <!-- Analyst Page Header -->
    <header class="text-center mb-8">
      <h1 class="text-4xl font-extrabold text-black">Air Quality Analyst Dashboard</h1>
      <p class="text-gray-500 mt-2 text-lg">Explore, filter, and analyze real-time AQI data around the world.</p>
    </header>

    <!-- Filter Controls -->
    <FilterBar @update:filters="handleFilterChange" />

    <!-- Map Section -->
    <div class="map-wrapper relative mb-6">
      <div id="map" class="rounded-lg shadow-lg border border-gray-700 overflow-hidden"></div>
    </div>

    <!-- Stats + Chart -->
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
import { ref, onMounted } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import Chart from 'chart.js/auto'
import FilterBar from '@/components/FilterBar.vue'

const aqiData = ref([])
const filteredData = ref([])
const top10HighAQI = ref([])
const top10LowAQI = ref([])
const filters = ref({})
let aqiChart = null
let map = null
let markers = []

// AQI color scale
const getColor = (aqi) => {
  const num = parseInt(aqi)
  if (num <= 50) return '#00e400'
  if (num <= 100) return '#ffff00'
  if (num <= 150) return '#ff7e00'
  if (num <= 200) return '#ff0000'
  if (num <= 300) return '#99004c'
  return '#7e0023'
}

// Update Top 10 rankings
const updateTop10 = () => {
  const sorted = [...aqiData.value]
    .filter(s => !isNaN(parseInt(s.aqi)))
    .sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))

  top10HighAQI.value = sorted.slice(0, 10)
  top10LowAQI.value = sorted.slice(-10).reverse()
}

// Render AQI bar chart
const updateChart = () => {
  const ctx = document.getElementById('aqiChart').getContext('2d')
  if (aqiChart) aqiChart.destroy()

  aqiChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: aqiData.value.slice(0, 20).map(station => station.name),
      datasets: [
        {
          label: 'AQI Levels',
          data: aqiData.value.slice(0, 20).map(station => parseInt(station.aqi)),
          backgroundColor: aqiData.value.slice(0, 20).map(station => getColor(parseInt(station.aqi))),
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

// Render markers on Leaflet map
const renderMarkers = () => {
  markers.forEach(marker => marker.remove())
  markers = []

  aqiData.value.forEach(station => {
    const { lat, lon, aqi, name } = station
    if (!lat || !lon) return
    const color = getColor(aqi)

    const marker = L.circleMarker([lat, lon], {
      radius: 6,
      fillColor: color,
      color: '#000',
      weight: 0.8,
      opacity: 1,
      fillOpacity: 0.8,
    }).addTo(map)

    marker.bindPopup(`
      <div style="font-family: Arial; font-size: 13px;">
        <b>${name}</b><br/>
        AQI: <strong style="color: ${color}">${aqi}</strong>
      </div>
    `)

    markers.push(marker)
  })
}

// Fetch AQI data from Laravel API
const fetchAQIData = async () => {
  try {
    const { data } = await axios.get('/api/aqi/global') // Laravel backend

    if (data.status === 'ok') {
      aqiData.value = data.data
      updateTop10()
      renderMarkers()
      updateChart()
    }
  } catch (err) {
    console.error('Failed to fetch AQI from backend:', err)
  }
}

// Initialize Leaflet map
const initMap = () => {
  map = L.map('map', {
    center: [20, 100],
    zoom: 3,
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
}

// On page load
onMounted(() => {
  initMap()
  fetchAQIData()
  setInterval(fetchAQIData, 60000) // auto-refresh every 60s
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
}

.stats-grid {
  margin-top: 20px;
}

.stats-card {
  border-radius: 0.75rem;
}

#aqiChart {
  max-height: 300px;
}
</style>
