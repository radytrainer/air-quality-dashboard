<template>
  <div class="container mx-auto p-4">
    <!-- Analyst Page Header -->
    <header class="text-center mb-8">
      <h1 class="text-4xl font-extrabold text-white">Air Quality Analyst Dashboard</h1>
      <p class="text-gray-300 mt-2 text-lg">Explore, filter, and analyze real-time AQI data around the world.</p>
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
            {{ station.name }}: <span :style="{ color: getColor(station.aqi) }">{{ station.aqi }}</span>
            <img :src="station.flag" alt="Flag" class="w-12 h-8 inline-block ml-2">
          </li>
        </ul>
      </div>
      <div class="stats-card p-4 bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-xl font-bold mb-2 text-white">Top 10 Lowest AQI</h2>
        <ul class="text-white">
          <li v-for="(station, index) in top10LowAQI" :key="index" class="mb-2">
            {{ station.name }}: <span :style="{ color: getColor(station.aqi) }">{{ station.aqi }}</span>
            <img :src="station.flag" alt="Flag" class="w-12 h-8 inline-block ml-2">
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
import axios from 'axios'
import Chart from 'chart.js/auto'
import FilterBar from '@/components/FilterBar.vue'

const TOKEN = '9c81a4f2fcf022539c917fdefba185ff9369865d'
const aqiData = ref([])
const filteredData = ref([])
const top10HighAQI = ref([])
const top10LowAQI = ref([])
const filters = ref({})
let aqiChart = null
let map = null
let markers = []

const getColor = (aqi) => {
  if (aqi <= 50) return '#00e400'
  if (aqi <= 100) return '#ffff00'
  if (aqi <= 150) return '#ff7e00'
  if (aqi <= 200) return '#ff0000'
  if (aqi <= 300) return '#99004c'
  return '#7e0023'
}

const handleFilterChange = (newFilters) => {
  filters.value = newFilters
  filterAQIDataAndUpdate()
}

const updateTop10 = (data) => {
  const sorted = [...data].filter(s => !isNaN(parseInt(s.aqi))).sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))
  top10HighAQI.value = sorted.slice(0, 10)
  top10LowAQI.value = sorted.slice(-10).reverse()
}

const updateChartWithData = (data) => {
  const ctx = document.getElementById('aqiChart').getContext('2d')
  if (aqiChart) aqiChart.destroy()

  aqiChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.map(station => station.station.name),
      datasets: [{
        label: 'AQI Levels',
        data: data.map(station => parseInt(station.aqi)),
        backgroundColor: data.map(station => getColor(parseInt(station.aqi))),
        borderColor: '#000',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  })
}

const renderMarkers = (data) => {
  markers.forEach(marker => marker.remove())
  markers = []

  data.forEach(station => {
    const { lat, lon, aqi } = station
    const color = getColor(parseInt(aqi))

    const marker = L.circleMarker([lat, lon], {
      radius: 6,
      fillColor: color,
      color: '#000',
      weight: 0.8,
      opacity: 1,
      fillOpacity: 0.8
    }).addTo(map)

    marker.bindPopup(`
      <div style="font-family: Arial; font-size: 13px;">
        <b>${station.station.name}</b><br/>
        AQI: <strong style="color: ${color}">${aqi}</strong>
      </div>
    `)

    markers.push(marker)
  })
}

const filterAQIDataAndUpdate = () => {
  let data = [...aqiData.value]

  if (filters.value.pollutant) {
    data = data.filter(s => s.iaqi?.[filters.value.pollutant]?.v !== undefined)
  }
  if (filters.value.country) {
    data = data.filter(s => s.flag.includes(`/${filters.value.country.toLowerCase()}.png`))
  }

  filteredData.value = data
  updateTop10(data)
  renderMarkers(data)
  updateChartWithData(data)
}

const fetchAQIData = async () => {
  try {
    const bounds = '-85,-180,85,180'
    const url = `https://api.waqi.info/map/bounds/?latlng=${bounds}&token=${TOKEN}`
    const { data } = await axios.get(url)

    if (data.status === 'ok') {
      aqiData.value = data.data.map(station => ({
        ...station,
        name: station.station.name.split(',')[1]?.trim() || station.station.name,
        flag: `https://flagcdn.com/w160/${station.station.name.split(',')[1]?.trim().toLowerCase() || 'unknown'}.png`
      }))
      filterAQIDataAndUpdate()
    }
  } catch (err) {
    console.error('Failed to fetch AQI:', err)
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
  map.on('mousewheel', (e) => {
    if (!e.originalEvent.ctrlKey) {
      map.scrollWheelZoom.disable()
      e.originalEvent.preventDefault()
    } else {
      map.scrollWheelZoom.enable()
    }
  })
}

onMounted(() => {
  initMap()
  fetchAQIData()
  setInterval(fetchAQIData, 30000)
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

#aqiChart {
  max-height: 300px;
}
</style>
