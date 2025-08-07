<template>
  <div class="container mx-auto p-4">
    <!-- Overview Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow">
        <h3 class="text-gray-400 mb-2 text-sm font-semibold">TOTAL COUNTRIES</h3>
        <p class="text-2xl font-bold">{{ totalCountries }}</p>
        <p class="text-xs text-gray-400 mt-1">Monitoring air quality</p>
      </div>
      <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow">
        <h3 class="text-gray-400 mb-2 text-sm font-semibold">HIGHEST POLLUTION</h3>
        <p class="text-xl font-bold text-red-500 truncate">{{ highestPollutionStation.name }}</p>
        <div class="flex items-center justify-between mt-1">
          <span class="text-lg font-mono">{{ highestPollutionStation.aqi }} AQI</span>
          <span class="text-xs px-2 py-1 rounded" :style="{ backgroundColor: getColor(highestPollutionStation.aqi, 0.2), color: getColor(highestPollutionStation.aqi) }">
            {{ getStatus(highestPollutionStation.aqi) }}
          </span>
        </div>
      </div>
      <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow">
        <h3 class="text-gray-400 mb-2 text-sm font-semibold">LOWEST POLLUTION</h3>
        <p class="text-xl font-bold text-green-500 truncate">{{ lowestPollutionStation.name }}</p>
        <div class="flex items-center justify-between mt-1">
          <span class="text-lg font-mono">{{ lowestPollutionStation.aqi }} AQI</span>
          <span class="text-xs px-2 py-1 rounded" :style="{ backgroundColor: getColor(lowestPollutionStation.aqi, 0.2), color: getColor(lowestPollutionStation.aqi) }">
            {{ getStatus(lowestPollutionStation.aqi) }}
          </span>
        </div>
      </div>
      <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow">
        <h3 class="text-gray-400 mb-2 text-sm font-semibold">GLOBAL AVERAGE</h3>
        <p class="text-2xl font-bold">{{ globalAverageAQI }} AQI</p>
        <p class="text-xs text-gray-400 mt-1">Across all stations</p>
      </div>
    </div>

    <!-- Map Section -->
    <div class="map-wrapper relative mb-6 rounded-lg overflow-hidden shadow-lg">
      <div id="map" class="w-full h-full border border-gray-700"></div>
      <div class="absolute bottom-4 left-4 z-[1000] bg-gray-900 bg-opacity-70 text-white text-xs p-2 rounded">
        Wind Speed Visualization Enabled
      </div>
    </div>

    <!-- Data Tables and Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Top 10 Most Polluted -->
      <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-800 overflow-hidden">
        <div class="p-4 border-b border-gray-800 bg-gray-950">
          <h2 class="text-xl font-bold text-red-400 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
            </svg>
            TOP 10 MOST POLLUTED
          </h2>
          <p class="text-xs text-gray-400">Updated: {{ lastUpdated }}</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-gray-200">
            <thead>
              <tr class="border-b border-gray-800 bg-gray-950">
                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-400 uppercase">RANK</th>
                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-400 uppercase">LOCATION</th>
                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-400 uppercase">AQI</th>
                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-400 uppercase">STATUS</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(station, index) in top10HighAQI" :key="'high-'+index" class="border-b border-gray-800 hover:bg-gray-850 transition-colors">
                <td class="py-3 px-4 font-mono">{{ index + 1 }}</td>
                <td class="py-3 px-4">
                  <div class="flex items-center">
                    <img :src="station.flag" alt="Flag" class="w-5 h-3 mr-2 rounded-sm" v-if="station.flag" />
                    <div>
                      <div class="font-medium">{{ extractCityName(station.name) }}</div>
                      <div class="text-xs text-gray-400">{{ extractCountryName(station.name) }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4 text-right font-mono">
                  <span :style="{ color: getColor(station.aqi) }" class="font-bold">{{ station.aqi }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="text-xs px-2 py-1 rounded-full" :style="{ backgroundColor: getColor(station.aqi, 0.2), color: getColor(station.aqi) }">
                    {{ getStatus(station.aqi) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top 10 Least Polluted -->
      <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-800 overflow-hidden">
        <div class="p-4 border-b border-gray-800 bg-gray-950">
          <h2 class="text-xl font-bold text-green-400 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            TOP 10 LEAST POLLUTED
          </h2>
          <p class="text-xs text-gray-400">Updated: {{ lastUpdated }}</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-gray-200">
            <thead>
              <tr class="border-b border-gray-800 bg-gray-950">
                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-400 uppercase">RANK</th>
                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-400 uppercase">LOCATION</th>
                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-400 uppercase">AQI</th>
                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-400 uppercase">STATUS</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(station, index) in top10LowAQI" :key="'low-'+index" class="border-b border-gray-800 hover:bg-gray-850 transition-colors">
                <td class="py-3 px-4 font-mono">{{ index + 1 }}</td>
                <td class="py-3 px-4">
                  <div class="flex items-center">
                    <img :src="station.flag" alt="Flag" class="w-5 h-3 mr-2 rounded-sm" v-if="station.flag" />
                    <div>
                      <div class="font-medium">{{ extractCityName(station.name) }}</div>
                      <div class="text-xs text-gray-400">{{ extractCountryName(station.name) }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4 text-right font-mono">
                  <span :style="{ color: getColor(station.aqi) }" class="font-bold">{{ station.aqi }}</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="text-xs px-2 py-1 rounded-full" :style="{ backgroundColor: getColor(station.aqi, 0.2), color: getColor(station.aqi) }">
                    {{ getStatus(station.aqi) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Chart -->
      <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-800 p-4 col-span-1 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-white">AQI TRENDS: TOP 10 HIGHEST & LOWEST</h2>
          <div class="flex space-x-2">
            <button @click="changeChartType('line')" :class="{'bg-blue-600 text-white': chartType === 'line', 'bg-gray-800 text-gray-300': chartType !== 'line'}" class="px-3 py-1 rounded text-xs">
              Line Chart
            </button>
            <button @click="changeChartType('bar')" :class="{'bg-blue-600 text-white': chartType === 'bar', 'bg-gray-800 text-gray-300': chartType !== 'bar'}" class="px-3 py-1 rounded text-xs">
              Bar Chart
            </button>
          </div>
        </div>
        <canvas id="aqiChart" class="w-full" style="height: 350px;"></canvas>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-velocity/dist/leaflet-velocity.min.js'
import 'leaflet-velocity/dist/leaflet-velocity.min.css'
import axios from 'axios'
import Chart from 'chart.js/auto'

const aqiData = ref([])
const top10HighAQI = ref([])
const top10LowAQI = ref([])
const lastUpdated = ref(new Date().toLocaleString())
const chartType = ref('line') // Default to line chart
let aqiChart = null
let map = null
let markers = []
let velocityLayer = null

// Helper functions to extract city and country names
const extractCityName = (name) => {
  if (!name) return 'Unknown'
  const parts = name.split(',')
  return parts[0].trim()
}

const extractCountryName = (name) => {
  if (!name) return 'Unknown'
  const parts = name.split(',')
  if (parts.length > 1) {
    return parts[1].split('(')[0].trim()
  }
  return 'Unknown'
}

// Computed properties for overview boxes
const totalCountries = computed(() => {
  if (aqiData.value.length === 0) return 0
  const countries = new Set()
  aqiData.value.forEach(station => {
    const country = extractCountryName(station.name)
    if (country && country !== 'Unknown') {
      countries.add(country)
    }
  })
  return countries.size
})

const highestPollutionStation = computed(() => {
  if (aqiData.value.length === 0) return { name: 'Loading...', aqi: 0 }
  const sorted = [...aqiData.value].sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))
  return sorted[0] || { name: 'N/A', aqi: 0 }
})

const lowestPollutionStation = computed(() => {
  if (aqiData.value.length === 0) return { name: 'Loading...', aqi: 0 }
  const sorted = [...aqiData.value].sort((a, b) => parseInt(a.aqi) - parseInt(b.aqi))
  return sorted[0] || { name: 'N/A', aqi: 0 }
})

const globalAverageAQI = computed(() => {
  if (aqiData.value.length === 0) return 0
  const validStations = aqiData.value.filter(station => !isNaN(parseInt(station.aqi)))
  if (validStations.length === 0) return 0
  const total = validStations.reduce((sum, item) => sum + parseInt(item.aqi), 0)
  return Math.round(total / validStations.length)
})

const getColor = (aqi, opacity = 1) => {
  const val = parseInt(aqi)
  if (val <= 50) return `rgba(0, 228, 0, ${opacity})`      // Good - Green
  if (val <= 100) return `rgba(255, 255, 0, ${opacity})`   // Moderate - Yellow
  if (val <= 150) return `rgba(255, 126, 0, ${opacity})`   // Unhealthy for Sensitive Groups - Orange
  if (val <= 200) return `rgba(255, 0, 0, ${opacity})`     // Unhealthy - Red
  if (val <= 300) return `rgba(153, 0, 76, ${opacity})`    // Very Unhealthy - Purple
  return `rgba(126, 0, 35, ${opacity})`                    // Hazardous - Maroon
}

const getStatus = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return 'Good'
  if (val <= 100) return 'Moderate'
  if (val <= 150) return 'Unhealthy (Sensitive)'
  if (val <= 200) return 'Unhealthy'
  if (val <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}

const updateTop10 = () => {
  const validStations = [...aqiData.value].filter(s => !isNaN(parseInt(s.aqi)))
  const sortedHigh = [...validStations].sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))
  const sortedLow = [...validStations].sort((a, b) => parseInt(a.aqi) - parseInt(b.aqi))
  
  top10HighAQI.value = sortedHigh.slice(0, 10)
  top10LowAQI.value = sortedLow.slice(0, 10)
  lastUpdated.value = new Date().toLocaleString()
}

const changeChartType = (type) => {
  chartType.value = type
  updateChart()
}

const updateChart = () => {
  const ctx = document.getElementById('aqiChart')
  if (!ctx) return
  
  if (aqiChart) aqiChart.destroy()

  // Prepare data for chart - showing top 10 high and low separately
  const highLabels = top10HighAQI.value.map(station => extractCityName(station.name))
  const highData = top10HighAQI.value.map(station => parseInt(station.aqi) || 0)
  const lowLabels = top10LowAQI.value.map(station => extractCityName(station.name))
  const lowData = top10LowAQI.value.map(station => parseInt(station.aqi) || 0)

  const chartData = {
    labels: [...highLabels, ...lowLabels],
    datasets: [
      {
        label: 'Most Polluted',
        data: [...highData, ...Array(lowData.length).fill(null)],
        borderColor: '#ff6b6b',
        backgroundColor: 'rgba(255, 107, 107, 0.2)',
        tension: 0.3,
        fill: true,
        pointBackgroundColor: highData.map(aqi => getColor(aqi)),
        pointRadius: 5,
        pointHoverRadius: 7
      },
      {
        label: 'Least Polluted',
        data: [...Array(highData.length).fill(null), ...lowData],
        borderColor: '#51cf66',
        backgroundColor: 'rgba(81, 207, 102, 0.2)',
        tension: 0.3,
        fill: true,
        pointBackgroundColor: lowData.map(aqi => getColor(aqi)),
        pointRadius: 5,
        pointHoverRadius: 7
      }
    ]
  }

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
        labels: {
          color: '#e0e0e0',
          font: {
            size: 12
          }
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            let station
            if (context.datasetIndex === 0 && context.dataIndex < highLabels.length) {
              station = top10HighAQI.value[context.dataIndex]
            } else if (context.datasetIndex === 1) {
              station = top10LowAQI.value[context.dataIndex - highLabels.length]
            }
            return [
              `AQI: ${context.raw}`,
              `Status: ${getStatus(context.raw)}`,
              `Location: ${station?.name || ''}`
            ]
          }
        },
        displayColors: true,
        usePointStyle: true,
        bodyFont: {
          size: 12
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        title: {
          display: true,
          text: 'AQI Value',
          color: '#9ca3af',
          font: {
            size: 12
          }
        },
        grid: {
          color: 'rgba(55, 65, 81, 0.5)'
        },
        ticks: {
          color: '#9ca3af',
          font: {
            size: 11
          }
        }
      },
      x: {
        title: {
          display: true,
          text: 'Monitoring Stations',
          color: '#9ca3af',
          font: {
            size: 12
          }
        },
        grid: {
          display: false
        },
        ticks: {
          color: '#9ca3af',
          font: {
            size: 11
          }
        }
      }
    }
  }

  aqiChart = new Chart(ctx, {
    type: chartType.value,
    data: chartData,
    options: chartOptions
  })
}

const renderMarkers = () => {
  if (!map) return
  
  // Clear existing markers
  markers.forEach(marker => marker.remove())
  markers = []

  // Add new markers
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
      <div class="text-sm">
        <div class="font-bold">${station.name}</div>
        <div class="mt-1">
          <span class="font-semibold">AQI:</span> 
          <span style="color: ${color}">${station.aqi}</span>
        </div>
        <div><span class="font-semibold">Status:</span> ${status}</div>
        <div class="text-xs text-gray-400 mt-1">Click for details</div>
      </div>
    `)

    markers.push(marker)
  })

  // Fit bounds to show all markers if we have any
  if (markers.length > 0) {
    const group = new L.featureGroup(markers)
    map.fitBounds(group.getBounds().pad(0.2))
  }
}

const addWindLayer = async () => {
  try {
    // Remove existing layer if it exists
    if (velocityLayer) {
      map.removeLayer(velocityLayer)
    }

    const res = await axios.get('https://raw.githubusercontent.com/danwild/leaflet-velocity/master/demo/wind-global.json')
    const windData = res.data

    velocityLayer = L.velocityLayer({
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
      colorScale: [
        "rgb(36,104, 180)",
        "rgb(60,157, 194)",
        "rgb(128,205,193 )",
        "rgb(151,218,168 )",
        "rgb(198,231,181)",
        "rgb(238,247,217)",
        "rgb(255,238,159)",
        "rgb(252,217,125)",
        "rgb(255,182,100)",
        "rgb(252,150,75)",
        "rgb(250,112,52)",
        "rgb(245,64,32)",
        "rgb(237,45,28)",
        "rgb(220,24,32)",
        "rgb(180,0,35)"
      ]
    })

    velocityLayer.addTo(map)
  } catch (error) {
    console.error('Failed to load wind data:', error)
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
      updateChart()
    } else {
      console.error('API returned unexpected data:', data)
    }
  } catch (err) {
    console.error('Failed to fetch AQI:', err)
  }
}

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

  // Add wind layer
  addWindLayer()

  map.on('focus', () => map.scrollWheelZoom.enable())
  map.on('blur', () => map.scrollWheelZoom.disable())
}

onMounted(() => {
  initMap()
  fetchAQIData()
  setInterval(fetchAQIData, 30000) // refresh every 30 seconds
})
</script>

<style scoped>
.container {
  margin-top: 6px;
  background-color: #1a1a2e;
  color: #e0e0e0;
}

.map-wrapper {
  height: 500px;
  width: 100%;
  box-sizing: border-box;
  position: relative;
  z-index: 0;
}

#map {
  width: 100%;
  height: 100%;
  background-color: #2a2a3e;
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
  max-height: 350px;
  background-color: #2a2a3e;
  border-radius: 0.5rem;
  padding: 1rem;
}
</style>