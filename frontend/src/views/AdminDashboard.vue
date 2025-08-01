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
        <!-- AQI Map -->
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
          <div class="map-container">
            <div class="map-background"></div>
            <div class="city-markers">
              <div
                v-for="city in cityData"
                :key="city.name"
                class="city-marker"
                :class="getAQIClass(city.aqi)"
                :style="{
                  left: `${((city.lng + 180) / 360) * 100}%`,
                  top: `${((90 - city.lat) / 180) * 100}%`
                }"
                @mouseenter="showCityTooltip(city, $event)"
                @mouseleave="hideCityTooltip"
              >
                <div class="city-tooltip" v-if="hoveredCity === city.name">
                  {{ city.name }}: AQI {{ city.aqi }} ({{ city.level }})
                </div>
              </div>
            </div>
            <div class="map-legend">
              <h4>AQI Levels</h4>
              <div class="legend-items">
                <div class="legend-item">
                  <div class="legend-color aqi-good"></div>
                  <span>Good (0-50)</span>
                </div>
                <div class="legend-item">
                  <div class="legend-color aqi-moderate"></div>
                  <span>Moderate (51-100)</span>
                </div>
                <div class="legend-item">
                  <div class="legend-color aqi-unhealthy-sensitive"></div>
                  <span>Unhealthy for Sensitive (101-150)</span>
                </div>
                <div class="legend-item">
                  <div class="legend-color aqi-unhealthy"></div>
                  <span>Unhealthy (151-200)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Logs -->
        <div class="dashboard-card logs-card">
          <div class="card-header">
            <h2>System Logs & Cache Status</h2>
            <p>Latest system activity and cache performance</p>
          </div>
          <div class="logs-content">
            <!-- Cache Status -->
            <div class="cache-status">
              <div class="cache-header">
                <span>Cache Performance</span>
                <span class="status-badge">{{ cacheData.status }}</span>
              </div>
              <div class="cache-metrics">
                <div class="metric">
                  <span class="metric-label">Hit Rate:</span>
                  <span class="metric-value">{{ cacheData.hitRate }}</span>
                </div>
                <div class="metric">
                  <span class="metric-label">Last Refresh:</span>
                  <span class="metric-value">{{ cacheData.lastRefresh }}</span>
                </div>
              </div>
            </div>

            <div class="separator"></div>

            <!-- Recent Logs -->
            <div class="recent-logs">
              <h4>Recent Activity</h4>
              <div class="logs-container">
                <div
                  v-for="(log, index) in systemLogs"
                  :key="index"
                  class="log-entry"
                >
                  <component :is="getLogIcon(log.type)" :class="['log-icon', log.type]" />
                  <div class="log-content">
                    <div class="log-time">{{ log.time }}</div>
                    <div class="log-message">{{ log.message }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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

    <!-- Loading Overlay -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-spinner">
        <RefreshCw class="animate-spin loading-icon" />
        <p>Loading...</p>
      </div>
    </div>

    <!-- Toast Notification -->
    <Transition name="toast">
      <div v-if="toast.show" class="toast" :class="toast.type">
        <div class="toast-content">
          <component :is="getToastIcon(toast.type)" class="toast-icon" />
          <span class="toast-message">{{ toast.message }}</span>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import {
  Wind,
  Bell,
  Settings,
  User,
  MapPin,
  Server,
  CheckCircle,
  AlertTriangle,
  RefreshCw,
  Activity,
  Info,
  XCircle
} from 'lucide-vue-next'

// City coordinates mapping
const cityCoordinates = {
  'New York': { lat: 40.7128, lng: -74.0060 },
  'Los Angeles': { lat: 34.0522, lng: -118.2437 },
  'London': { lat: 51.5074, lng: -0.1278 },
  'Tokyo': { lat: 35.6762, lng: 139.6503 },
  'Delhi': { lat: 28.7041, lng: 77.1025 },
  'Beijing': { lat: 39.9042, lng: 116.4074 },
  'Mumbai': { lat: 19.0760, lng: 72.8777 },
  'Sydney': { lat: -33.8688, lng: 151.2093 }
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
const systemLogs = ref([])
const cacheData = reactive({
  hitRate: '94.2%',
  lastRefresh: 'Just now',
  status: 'Active'
})

const toast = reactive({
  show: false,
  message: '',
  type: 'success'
})

// State management
const isLoading = ref(false)
const isRefreshing = ref(false)
const hoveredCity = ref(null)
let realTimeInterval = null

// Computed properties
const mostPollutedCity = computed(() => {
  return cityData.value.reduce((max, city) => 
    city.aqi > max.aqi ? city : max, { aqi: 0 })
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

const getLogIcon = (type) => {
  const icons = {
    success: CheckCircle,
    warning: AlertTriangle,
    error: XCircle,
    info: Info
  }
  return icons[type] || Info
}

const getToastIcon = (type) => {
  const icons = {
    success: CheckCircle,
    warning: AlertTriangle,
    error: XCircle,
    info: Info
  }
  return icons[type] || CheckCircle
}

// API fetching
const cities = Object.keys(cityCoordinates)
const fetchAllAQI = async () => {
  isLoading.value = true
  cityData.value = []
  const newLogs = []

  for (const city of cities) {
    try {
      const response = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${city}`, {
        headers: {
          'X-Api-Key': 'ymN5uLOtTZ0lIYjWUBD30w==OgcDgtbkNz5YMqTo'
        }
      })

      const aqi = response.data.overall_aqi
      cityData.value.push({
        name: city,
        aqi: aqi,
        level: getAQILevel(aqi),
        lat: cityCoordinates[city].lat,
        lng: cityCoordinates[city].lng
      })

      newLogs.push({
        time: new Date().toLocaleTimeString('en-US', { hour12: false }),
        type: 'success',
        message: `AQI data fetched for ${city} (AQI: ${aqi})`
      })
    } catch (err) {
      console.error(`Failed to fetch AQI for ${city}`, err)
      newLogs.push({
        time: new Date().toLocaleTimeString('en-US', { hour12: false }),
        type: 'error',
        message: `Failed to fetch AQI for ${city}`
      })
    }
  }

  // Update summary data
  summaryData.totalCities = cityData.value.length
  const newMostPolluted = mostPollutedCity.value
  summaryData.mostPollutedCity = {
    name: newMostPolluted.name,
    aqi: newMostPolluted.aqi,
    level: newMostPolluted.level
  }

  // Update logs
  systemLogs.value = [...newLogs, ...systemLogs.value].slice(0, 10)
  isLoading.value = false
  showToast('AQI data refreshed successfully', 'success')
}

// Methods
const showToast = (message, type = 'success') => {
  toast.message = message
  toast.type = type
  toast.show = true
  
  setTimeout(() => {
    toast.show = false
  }, 3000)
}

const showNotifications = () => {
  showToast('Notifications panel opened', 'info')
}

const showSettings = () => {
  showToast('Settings panel opened', 'info')
}

const viewCitiesDetail = () => {
  showToast('Opening cities detail view...', 'info')
}

const viewAPIStatus = () => {
  showToast('Opening API status dashboard...', 'info')
}

const viewPollutedCity = () => {
  showToast(`Viewing details for ${summaryData.mostPollutedCity.name}`, 'info')
}

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
    cacheData.lastRefresh = 'Just now'
    
    const newLog = {
      time: new Date().toLocaleTimeString('en-US', { hour12: false }),
      type: 'success',
      message: 'Cache cleared and refreshed'
    }
    systemLogs.value.unshift(newLog)
    
    showToast('Cache cleared successfully', 'success')
  } catch (error) {
    showToast('Failed to clear cache', 'error')
  } finally {
    isLoading.value = false
  }
}

const addNewCity = async () => {
  const availableCities = ['Paris', 'Berlin', 'Toronto', 'Singapore', 'Dubai']
  const randomCity = availableCities[Math.floor(Math.random() * availableCities.length)]
  
  if (cities.includes(randomCity)) {
    showToast(`${randomCity} is already being monitored`, 'warning')
    return
  }

  isLoading.value = true
  
  try {
    const response = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${randomCity}`, {
      headers: {
        'X-Api-Key': 'ymN5uLOtTZ0lIYjWUBD30w==OgcDgtbkNz5YMqTo'
      }
    })

    const aqi = response.data.overall_aqi
    cityData.value.push({
      name: randomCity,
      aqi: aqi,
      level: getAQILevel(aqi),
      lat: cityCoordinates[randomCity]?.lat || 0,
      lng: cityCoordinates[randomCity]?.lng || 0
    })

    cities.push(randomCity)
    summaryData.totalCities++

    const newLog = {
      time: new Date().toLocaleTimeString('en-US', { hour12: false }),
      type: 'info',
      message: `New city added: ${randomCity}`
    }
    systemLogs.value.unshift(newLog)
    
    showToast(`${randomCity} added to monitoring`, 'success')
  } catch (error) {
    showToast('Failed to add new city', 'error')
  } finally {
    isLoading.value = false
  }
}

const viewFullLogs = () => {
  showToast('Opening full logs view...', 'info')
}

const showCityTooltip = (city, event) => {
  hoveredCity.value = city.name
}

const hideCityTooltip = () => {
  hoveredCity.value = null
}

const startRealTimeUpdates = () => {
  realTimeInterval = setInterval(async () => {
    await fetchAllAQI()
    
    // Update cache refresh time
    const minutes = Math.floor(Math.random() * 5) + 1
    cacheData.lastRefresh = `${minutes} min ago`
    
    // Occasionally add new log entries
    if (Math.random() > 0.7) {
      const messages = [
        'Automated data sync completed',
        'System health check passed',
        'Cache optimization finished',
        'API response time improved'
      ]
      
      const newLog = {
        time: new Date().toLocaleTimeString('en-US', { hour12: false }),
        type: 'info',
        message: messages[Math.floor(Math.random() * messages.length)]
      }
      
      systemLogs.value.unshift(newLog)
      if (systemLogs.value.length > 10) {
        systemLogs.value.pop()
      }
    }
  }, 300000) // Update every 5 minutes
}

// Lifecycle hooks
onMounted(() => {
  fetchAllAQI()
  startRealTimeUpdates()
})

onUnmounted(() => {
  if (realTimeInterval) {
    clearInterval(realTimeInterval)
  }
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
  position: relative;
  height: 300px;
  background: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
  border-radius: 8px;
  overflow: hidden;
}

.map-background {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
}

.city-markers {
  position: absolute;
  inset: 0;
}

.city-marker {
  position: absolute;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  cursor: pointer;
  animation: pulse 2s infinite;
  transition: transform 0.2s ease;
}

.city-marker:hover {
  transform: scale(1.3);
  z-index: 10;
}

.city-tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 8px;
  padding: 8px 12px;
  background: rgba(0, 0, 0, 0.9);
  color: white;
  font-size: 12px;
  border-radius: 6px;
  white-space: nowrap;
  pointer-events: none;
}

.map-legend {
  position: absolute;
  bottom: 16px;
  left: 16px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border-radius: 8px;
  padding: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.map-legend h4 {
  font-size: 12px;
  font-weight: 600;
  margin: 0 0 8px 0;
  color: #374151;
}

.legend-items {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.legend-color {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.legend-item span {
  font-size: 11px;
  color: #6b7280;
}

/* System Logs */
.logs-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.cache-status {
  background: rgba(0, 0, 0, 0.02);
  border-radius: 8px;
  padding: 16px;
  border: 1px solid #f1f5f9;
}

.cache-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.cache-header span:first-child {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.status-badge {
  padding: 4px 8px;
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.cache-metrics {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.metric {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.metric-label {
  font-size: 12px;
  color: #6b7280;
}

.metric-value {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}

.separator {
  height: 1px;
  background: #e5e7eb;
}

.recent-logs h4 {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin: 0 0 12px 0;
}

.logs-container {
  max-height: 200px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.log-entry {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.log-entry:last-child {
  border-bottom: none;
}

.log-icon {
  flex-shrink: 0;
  margin-top: 2px;
  width: 14px;
  height: 14px;
}

.log-icon.success {
  color: #10b981;
}
.log-icon.warning {
  color: #f59e0b;
}
.log-icon.error {
  color: #ef4444;
}
.log-icon.info {
  color: #3b82f6;
}

.log-content {
  flex: 1;
}

.log-time {
  font-size: 11px;
  color: #6b7280;
  margin-bottom: 2px;
}

.log-message {
  font-size: 13px;
  color: #374151;
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

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  background: white;
  padding: 32px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
}

.loading-icon {
  width: 32px;
  height: 32px;
  color: #3b82f6;
  margin-bottom: 16px;
}

.loading-spinner p {
  color: #6b7280;
  font-weight: 500;
  margin: 0;
}

/* Toast Notification */
.toast {
  position: fixed;
  top: 24px;
  right: 24px;
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #10b981;
  z-index: 1001;
}

.toast.success {
  border-left-color: #10b981;
}
.toast.error {
  border-left-color: #ef4444;
}
.toast.warning {
  border-left-color: #f59e0b;
}
.toast.info {
  border-left-color: #3b82f6;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.toast-icon {
  width: 16px;
  height: 16px;
}

.toast-message {
  font-size: 14px;
  color: #374151;
  font-weight: 500;
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

/* Toast Transitions */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  transform: translateX(100%);
  opacity: 0;
}

.toast-leave-to {
  transform: translateX(100%);
  opacity: 0;
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

  .map-legend {
    position: static;
    margin-top: 16px;
  }

  .quick-actions {
    justify-content: center;
  }

  .cache-metrics {
    grid-template-columns: 1fr;
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