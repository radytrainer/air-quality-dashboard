<template>
  <div class="container mx-auto p-6 space-y-8">
    <!-- Top Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl shadow-md p-5 flex flex-col items-start">
        <div class="flex items-center gap-3">
          <span class="bg-red-100 text-red-600 p-3 rounded-full text-2xl">
            📈
          </span>
          <div>
            <p class="text-sm text-gray-500">Highest AQI</p>
            <p class="text-2xl font-semibold">
              {{ top10HighAQI[0]?.aqi || 'N/A' }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md p-5 flex flex-col items-start">
        <div class="flex items-center gap-3">
          <span class="bg-green-100 text-green-600 p-3 rounded-full text-2xl">
            🌿
          </span>
          <div>
            <p class="text-sm text-gray-500">Lowest AQI</p>
            <p class="text-2xl font-semibold">
              {{ top10LowAQI[0]?.aqi || 'N/A' }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md p-5 flex flex-col items-start">
        <div class="flex items-center gap-3">
          <span class="bg-blue-100 text-blue-600 p-3 rounded-full text-2xl">
            📊
          </span>
          <div>
            <p class="text-sm text-gray-500">Average AQI</p>
            <p class="text-2xl font-semibold">
              {{ avgAQI }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md p-5 flex flex-col items-start">
        <div class="flex items-center gap-3">
          <span class="bg-purple-100 text-purple-600 p-3 rounded-full text-2xl">
            🌍
          </span>
          <div>
            <p class="text-sm text-gray-500">Cities Tracked</p>
            <p class="text-2xl font-semibold">
              {{ aqiData.length }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Middle Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 ">
      <div class="bg-white rounded-xl shadow-md p-4 relative">
  <h2 class="text-lg font-semibold mb-4">AQI Map</h2>
  <div class="map-wrapper rounded-lg overflow-hidden pt-16">
    <div id="map" class="h-[400px] w-full"></div>
  </div>
</div>

      <div class="bg-white rounded-xl shadow-md p-4">
        <h2 class="text-lg font-semibold mb-4">AQI Trend</h2>
        <canvas id="aqiChart" class="h-[400px] w-full"></canvas>
      </div>
    </div>

    <!-- Bottom Section: AQI Categories Summary -->
    <div class="bg-white rounded-xl shadow-md p-6">
      <h2 class="text-lg font-semibold mb-6">AQI Categories Summary</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
        <div v-for="category in aqiCategories" :key="category.name"
          class="p-4 rounded-lg border flex flex-col items-center justify-center">
          <div class="w-10 h-10 rounded-full mb-2" :style="{ backgroundColor: category.color }"></div>
          <p class="font-semibold">{{ category.name }}</p>
          <p class="text-2xl font-bold" :style="{ color: category.color }">
            {{ category.count }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import axios from 'axios'
import Chart from 'chart.js/auto'

const aqiData = ref([])
const top10HighAQI = ref([])
const top10LowAQI = ref([])

let map = null
let markers = []
let aqiChart = null

const getColor = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return '#00e400'        // Good - Green
  if (val <= 100) return '#ffff00'       // Moderate - Yellow
  if (val <= 150) return '#ff7e00'       // Unhealthy for Sensitive Groups - Orange
  if (val <= 200) return '#ff0000'       // Unhealthy - Red
  if (val <= 300) return '#99004c'       // Very Unhealthy - Purple
  return '#7e0023'                       // Hazardous - Maroon
}

const avgAQI = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const sum = aqiData.value.reduce((acc, s) => acc + (parseInt(s.aqi) || 0), 0)
  return Math.round(sum / aqiData.value.length)
})

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
    const marker = L.circleMarker([station.lat, station.lon], {
      radius: 6,
      fillColor: color,
      color: '#000',
      weight: 0.8,
      opacity: 1,
      fillOpacity: 0.8,
    }).addTo(map)
    marker.bindPopup(
      `<b>${station.name}</b><br/>AQI: <span style="color:${color}">${station.aqi}</span>`
    )
    markers.push(marker)
  })
}

const truncateLabel = (label, maxLength = 10) => {
  if (label.length <= maxLength) return label
  return label.slice(0, maxLength - 1) + '…'
}

const updateChart = () => {
  if (!aqiChart) return
  const dataPoints = top10HighAQI.value.map(station => parseInt(station.aqi) || 0)
  const labels = top10HighAQI.value.map(station => truncateLabel(station.name, 10))
  const maxAQI = Math.max(...dataPoints)
  const pointColors = dataPoints.map(val => val === maxAQI ? '#ff0000' : 'rgba(16, 185, 129, 1)')
  aqiChart.data.labels = labels
  aqiChart.data.datasets[0].data = dataPoints
  aqiChart.data.datasets[0].pointBackgroundColor = pointColors
  aqiChart.update()
}

// New computed for AQI categories count
const aqiCategories = computed(() => {
  const categories = [
    { name: 'Good', range: [0, 50], color: '#00e400', count: 0 },
    { name: 'Moderate', range: [51, 100], color: '#ffff00', count: 0 },
    { name: 'Unhealthy SG', range: [101, 150], color: '#ff7e00', count: 0 },
    { name: 'Unhealthy', range: [151, 200], color: '#ff0000', count: 0 },
    { name: 'Very Unhealthy', range: [201, 300], color: '#99004c', count: 0 },
    { name: 'Hazardous', range: [301, Infinity], color: '#7e0023', count: 0 },
  ]

  aqiData.value.forEach(station => {
    const aqi = parseInt(station.aqi)
    if (isNaN(aqi)) return
    const cat = categories.find(c => aqi >= c.range[0] && aqi <= c.range[1])
    if (cat) cat.count++
  })

  return categories
})

const fetchAQIData = async () => {
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/aqi-global')
    if (data.status === 'ok' && Array.isArray(data.data)) {
      aqiData.value = data.data
      updateTop10()
      renderMarkers()
      updateChart()
    }
  } catch (err) {
    console.error(err)
  }
}

const initMap = () => {
  map = L.map('map', {
    center: [20, 100],
    zoom: 4,
    zoomControl: true,
    scrollWheelZoom: false,
  })
  L.tileLayer(
    'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
    {
      attribution: '&copy; OpenStreetMap & CARTO',
      subdomains: 'abcd',
      maxZoom: 19,
    }
  ).addTo(map)
}

onMounted(() => {
  initMap()

  const ctx = document.getElementById('aqiChart').getContext('2d')
  const gradient = ctx.createLinearGradient(0, 0, 0, 400)
  gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)')
  gradient.addColorStop(1, 'rgba(16, 185, 129, 0)')

  aqiChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'AQI',
        data: [],
        borderColor: 'rgba(16, 185, 129, 1)',
        backgroundColor: gradient,
        fill: true,
        tension: 0.4,
        pointRadius: 0,
        pointHoverRadius: 5,
        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#fff',
          titleColor: '#000',
          bodyColor: '#000',
          borderColor: '#ccc',
          borderWidth: 1,
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#666', font: { size: 12 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.05)' },
          ticks: { color: '#666', font: { size: 12 } }
        }
      }
    }
  })

  fetchAQIData()
  setInterval(fetchAQIData, 30000)
})
</script>
<style scoped>
.map-wrapper {
  height: 500px;
  width: 100%;
  padding: 0 1rem;
  box-sizing: border-box;
  position: relative;
  z-index: 0;
}

</style>