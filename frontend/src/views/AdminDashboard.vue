<template>
  <div class="dashboard">
    <!-- Main Content -->
    <main class="main-content">
      <!-- Summary Cards -->
      <section class="summary-section">
        <div class="summary-cards">
          <div class="summary-card" @click="viewCitiesDetail">
            <div class="card-header">
              <h3>Total Cities Monitored</h3>
              <MapPin />
            </div>
            <div class="card-content">
              <div class="card-value">{{ summaryData.totalCities }}</div>
              <div class="card-subtitle">+12 from last month</div>
            </div>
          </div>

          <div class="summary-card" @click="viewAPIStatus">
            <div class="card-header">
              <h3>API Status</h3>
              <Server />
            </div>
            <div class="card-content">
              <div class="status-indicator">
                <CheckCircle class="status-icon" />
                <span class="card-value">{{ summaryData.apiStatus }}</span>
              </div>
              <div class="card-subtitle">99.9% uptime this month</div>
            </div>
          </div>

          <div class="summary-card" @click="viewPollutedCity">
            <div class="card-header">
              <h3>Most Polluted City Today</h3>
              <AlertTriangle />
            </div>
            <div class="card-content">
              <div class="card-value">{{ summaryData.mostPollutedCity.name }}</div>
              <div class="pollution-info">
                <span :class="['aqi-badge', getAQIClass(summaryData.mostPollutedCity.aqi)]">
                  AQI {{ summaryData.mostPollutedCity.aqi }}
                </span>
                <span class="card-subtitle">{{ summaryData.mostPollutedCity.level }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Dashboard Grid -->
      <section class="dashboard-grid">
        <!-- Real-time AQI Map -->
        <div class="dashboard-card map-card">
          <div class="card-header-with-action">
            <div>
              <h2>Real-time AQI Map</h2>
              <p>Current air quality across monitored cities</p>
            </div>
            <button class="refresh-btn" @click="refreshMapData" :disabled="isRefreshing">
              <RefreshCw :class="{ 'animate-spin': isRefreshing }" />
              Refresh
            </button>
          </div>
          <div id="map" class="map-container"></div>
        </div>

        <!-- AQI Graphs -->
        <div class="dashboard-card graph-card">
          <h2>Top 10 Most Polluted Cities</h2>
          <canvas id="mostPollutedChart"></canvas>
        </div>
        <div class="dashboard-card graph-card">
          <h2>Top 10 Least Polluted Cities</h2>
          <canvas id="leastPollutedChart"></canvas>
        </div>
      </section>

      <!-- Quick Actions -->
      <section class="quick-actions-section">
        <div class="dashboard-card">
          <div class="card-header">
            <h2>Quick Actions</h2>
            <p>Common administrative tasks</p>
          </div>
          <div class="quick-actions">
            <button class="action-btn" @click="refreshAllData" :disabled="isLoading">
              <RefreshCw :class="{ 'animate-spin': isLoading }" />
              Refresh All Data
            </button>
            <button class="action-btn" @click="clearCache" :disabled="isLoading">
              <Server />
              Clear Cache
            </button>
            <button class="action-btn" @click="addNewCity" :disabled="isLoading">
              <MapPin />
              Add New City
            </button>
            <button class="action-btn" @click="viewFullLogs">
              <Activity />
              View Full Logs
            </button>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-velocity/dist/leaflet-velocity.min.js'
import 'leaflet-velocity/dist/leaflet-velocity.min.css'
import axios from 'axios'
import Chart from 'chart.js/auto'
import {
  MapPin,
  Server,
  CheckCircle,
  AlertTriangle,
  RefreshCw,
  Activity
} from 'lucide-vue-next'

// API keys
const NINJA_API_KEY = 'ymN5uLOtTZ0lIYjWUBD30w==OgcDgtbkNz5YMqTo'
const WAQI_TOKEN = '9c81a4f2fcf022539c917fdefba185ff9369865d'

// City coordinates mapping
const cityCoordinates = {
  'New York': { lat: 40.7128, lng: -74.0060 },
  'Los Angeles': { lat: 34.0522, lng: -118.2437 },
  'London': { lat: 51.5074, lng: -0.1278 },
  'Tokyo': { lat: 35.6762, lng: 139.6503 },
  'Delhi': { lat: 28.7041, lng: 77.1025 },
  'Beijing': { lat: 39.9042, lng: 116.4074 },
  'Mumbai': { lat: 19.0760, lng: 72.8777 },
  'Sydney': { lat: -33.8688, lng: 151.2093 },
  'Paris': { lat: 48.8566, lng: 2.3522 },
  'Berlin': { lat: 52.5200, lng: 13.4050 },
  'Toronto': { lat: 43.6532, lng: -79.3832 },
  'Singapore': { lat: 1.3521, lng: 103.8198 },
  'Dubai': { lat: 25.2048, lng: 55.2708 }
}

// Reactive data
const summaryData = reactive({
  totalCities: 0,
  apiStatus: 'Operational',
  mostPollutedCity: {
    name: '',
    aqi: 0,
    level: ''
  }
})

const cityData = ref([])

// State management
const isLoading = ref(false)
const isRefreshing = ref(false)
const hoveredCity = ref(null)
let realTimeInterval = null
let map = null
let markers = []
let mostPollutedChart = null
let leastPollutedChart = null

// Computed properties
const mostPollutedCity = computed(() => {
  return cityData.value.reduce((max, city) => 
    city.aqi > max.aqi ? city : max, { aqi: 0 })
})

const top10HighAQI = computed(() => {
  return cityData.value
    .filter(s => !isNaN(parseInt(s.aqi)))
    .sort((a, b) => parseInt(b.aqi) - parseInt(a.aqi))
    .slice(0, 10)
})

const top10LowAQI = computed(() => {
  return cityData.value
    .filter(s => !isNaN(parseInt(s.aqi)))
    .sort((a, b) => parseInt(a.aqi) - parseInt(b.aqi))
    .slice(0, 10)
})

// Utility functions
const getAQILevel = (aqi) => {
  if (aqi <= 50) return 'Good'
  if (aqi <= 100) return 'Moderate'
  if (aqi <= 150) return 'Unhealthy for Sensitive Groups'
  if (aqi <= 200) return 'Unhealthy'
  if (aqi <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}

const getAQIClass = (aqi) => {
  if (aqi <= 50) return 'aqi-good'
  if (aqi <= 100) return 'aqi-moderate'
  if (aqi <= 150) return 'aqi-unhealthy-sensitive'
  if (aqi <= 200) return 'aqi-unhealthy'
  return 'aqi-very-unhealthy'
}

const getColor = (aqi) => {
  if (aqi <= 50) return '#00e400'
  if (aqi <= 100) return '#ffff00'
  if (aqi <= 150) return '#ff7e00'
  if (aqi <= 200) return '#ff0000'
  if (aqi <= 300) return '#99004c'
  return '#7e0023'
}

// New function to add wind layer
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

// API fetching and map rendering
const cities = Object.keys(cityCoordinates).filter(city => !['Paris', 'Berlin', 'Toronto', 'Singapore', 'Dubai'].includes(city))
const fetchAllAQI = async () => {
  isLoading.value = true
  cityData.value = []

  // Fetch data from API-Ninjas
  for (const city of cities) {
    try {
      const response = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${city}`, {
        headers: { 'X-Api-Key': NINJA_API_KEY }
      })
      const aqi = response.data.overall_aqi
      cityData.value.push({
        name: city,
        aqi: aqi,
        level: getAQILevel(aqi),
        lat: cityCoordinates[city].lat,
        lng: cityCoordinates[city].lng
      })
    } catch (err) {
      console.error(`Failed to fetch AQI for ${city}`, err)
    }
  }

  // Fetch data from WAQI API
  try {
    const bounds = '-85,-180,85,180'
    const url = `https://api.waqi.info/map/bounds/?latlng=${bounds}&token=${WAQI_TOKEN}`
    const { data } = await axios.get(url)

    if (data.status === 'ok') {
      const waqiData = data.data.map(station => ({
        name: station.station.name.split(',')[1]?.trim() || station.station.name,
        aqi: parseInt(station.aqi),
        level: getAQILevel(parseInt(station.aqi)),
        lat: station.lat,
        lng: station.lon
      }))

      const cityNames = new Set(cityData.value.map(city => city.name.toLowerCase()))
      for (const station of waqiData) {
        if (!cityNames.has(station.name.toLowerCase()) && station.lat && station.lng) {
          cityData.value.push(station)
          cityNames.add(station.name.toLowerCase())
        }
      }
    }
  } catch (err) {
    console.error('Failed to fetch WAQI AQI:', err)
  }

  // Update summary data
  summaryData.totalCities = cityData.value.length
  const newMostPolluted = mostPollutedCity.value
  summaryData.mostPollutedCity = {
    name: newMostPolluted.name,
    aqi: newMostPolluted.aqi,
    level: newMostPolluted.level
  }
  summaryData.apiStatus = cityData.value.length > 0 ? 'Operational' : 'Issues Detected'

  // Update map and charts
  renderMap()
  updateCharts()

  isLoading.value = false
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

const renderMap = () => {
  if (markers) markers.forEach(marker => marker.remove())
  markers = []

  cityData.value.forEach(city => {
    const color = getColor(parseInt(city.aqi))
    const marker = L.circleMarker([city.lat, city.lng], {
      radius: 6,
      fillColor: color,
      color: '#000',
      weight: 0.8,
      opacity: 1,
      fillOpacity: 0.8,
    }).addTo(map)

    marker.bindPopup(`
      <div style="font-family: Arial; font-size: 13px;">
        <b>${city.name}</b><br/>
        AQI: <strong style="color: ${color}">${city.aqi}</strong><br/>
        Level: ${city.level}
      </div>
    `)

    markers.push(marker)
  })
}

const updateCharts = () => {
  // Destroy existing charts if they exist
  if (mostPollutedChart) mostPollutedChart.destroy()
  if (leastPollutedChart) leastPollutedChart.destroy()

  // Most Polluted Chart
  const mostPollutedCtx = document.getElementById('mostPollutedChart').getContext('2d')
  mostPollutedChart = new Chart(mostPollutedCtx, {
    type: 'bar',
    data: {
      labels: top10HighAQI.value.map(city => city.name),
      datasets: [{
        label: 'AQI Levels',
        data: top10HighAQI.value.map(city => parseInt(city.aqi)),
        backgroundColor: top10HighAQI.value.map(city => getColor(parseInt(city.aqi))),
        borderColor: '#000',
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } },
    },
  })

  // Least Polluted Chart
  const leastPollutedCtx = document.getElementById('leastPollutedChart').getContext('2d')
  leastPollutedChart = new Chart(leastPollutedCtx, {
    type: 'bar',
    data: {
      labels: top10LowAQI.value.map(city => city.name),
      datasets: [{
        label: 'AQI Levels',
        data: top10LowAQI.value.map(city => parseInt(city.aqi)),
        backgroundColor: top10LowAQI.value.map(city => getColor(parseInt(city.aqi))),
        borderColor: '#000',
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } },
    },
  })
}

// Methods
const viewCitiesDetail = () => { console.log('Opening cities detail view...') }
const viewAPIStatus = () => { console.log('Opening API status dashboard...') }
const viewPollutedCity = () => { console.log(`Viewing details for ${summaryData.mostPollutedCity.name}`) }
const refreshMapData = async () => {
  isRefreshing.value = true
  await fetchAllAQI()
  isRefreshing.value = false
}
const refreshAllData = async () => {
  isLoading.value = true
  await fetchAllAQI()
  isLoading.value = false
}
const clearCache = async () => {
  isLoading.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1000))
  } finally { isLoading.value = false }
}
const addNewCity = async () => {
  const availableCities = ['Paris', 'Berlin', 'Toronto', 'Singapore', 'Dubai']
  const randomCity = availableCities[Math.floor(Math.random() * availableCities.length)]
  if (cityData.value.some(city => city.name.toLowerCase() === randomCity.toLowerCase())) {
    return
  }
  isLoading.value = true
  try {
    const response = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${randomCity}`, {
      headers: { 'X-Api-Key': NINJA_API_KEY }
    })
    const aqi = response.data.overall_aqi
    cityData.value.push({
      name: randomCity,
      aqi: aqi,
      level: getAQILevel(aqi),
      lat: cityCoordinates[randomCity].lat,
      lng: cityCoordinates[randomCity].lng
    })
    summaryData.totalCities++
    const newMostPolluted = mostPollutedCity.value
    summaryData.mostPollutedCity = { name: newMostPolluted.name, aqi: newMostPolluted.aqi, level: newMostPolluted.level }
    renderMap()
    updateCharts()
  } finally { isLoading.value = false }
}
const viewFullLogs = () => { console.log('Opening full logs view...') }

const startRealTimeUpdates = () => {
  realTimeInterval = setInterval(async () => {
    await fetchAllAQI()
  }, 300000)
}

// Lifecycle hooks
onMounted(() => {
  initMap()
  fetchAllAQI()
  startRealTimeUpdates()
})

onUnmounted(() => {
  if (realTimeInterval) clearInterval(realTimeInterval)
  if (map) map.remove()
  if (mostPollutedChart) mostPollutedChart.destroy()
  if (leastPollutedChart) leastPollutedChart.destroy()
})
</script>

<style scoped>
/* Reset and Base Styles */
.dashboard {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  background-color: #f8fafc;
  color: #334155;
  line-height: 1.6;
  min-height: 100vh;
}

/* Header Styles */
.header {
  background: linear-gradient(90deg, #ffffff 0%, #f8fafc 100%);
  border-bottom: 1px solid #e2e8f0;
  padding: 0 24px;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  max-width: 1200px;
  margin: 0 auto;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon {
  width: 32px;
  height: 32px;
  color: #3b82f6;
}

.header-info h1 {
  font-size: 20px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.header-info p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.icon-btn {
  background: none;
  border: none;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  color: #64748b;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-btn:hover {
  background-color: #f1f5f9;
  color: #3b82f6;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #e0f2fe;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #0369a1;
}

/* Main Content */
.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
}

/* Summary Cards */
.summary-section {
  margin-bottom: 24px;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
}

.summary-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
  cursor: pointer;
}

.summary-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.card-header h3 {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  margin: 0;
}

.card-value {
  font-size: 32px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.card-subtitle {
  font-size: 12px;
  color: #64748b;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-icon {
  color: #10b981;
  width: 20px;
  height: 20px;
}

.pollution-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

/* AQI Badges and Colors */
.aqi-badge {
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  color: white;
}

.aqi-good {
  background-color: #10b981;
}
.aqi-moderate {
  background-color: #f59e0b;
}
.aqi-unhealthy-sensitive {
  background-color: #f97316;
}
.aqi-unhealthy {
  background-color: #ef4444;
}
.aqi-very-unhealthy {
  background-color: #8b5cf6;
}

/* Dashboard Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  margin-bottom: 24px;
}

.dashboard-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-header-with-action {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.card-header-with-action h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.card-header-with-action p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.refresh-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  color: #475569;
  transition: all 0.2s ease;
}

.refresh-btn:hover:not(:disabled) {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.refresh-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Map Styles */
.map-container {
  height: 300px;
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
}

/* Quick Actions */
.quick-actions-section {
  margin-bottom: 24px;
}

.quick-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  color: #475569;
  transition: all 0.2s ease;
}

.action-btn:hover:not(:disabled) {
  background: #f1f5f9;
  border-color: #cbd5e1;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.action-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Animations */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-content {
    padding: 0 16px;
  }

  .header-info h1 {
    font-size: 18px;
  }

  .main-content {
    padding: 16px;
  }

  .summary-cards {
    grid-template-columns: 1fr;
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .quick-actions {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .header-right {
    gap: 8px;
  }

  .user-info span {
    display: none;
  }

  .card-header-with-action {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .action-btn {
    flex: 1;
    justify-content: center;
    min-width: 140px;
  }
}
</style>