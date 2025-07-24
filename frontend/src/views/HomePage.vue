<template>

    <!-- Header -->
      <div class="max-w-7xl mt-10 mx-auto">
        <h1 class="text-4xl font-bold tracking-wide">🌍 Air Quality Dashboard</h1>
        <p class="text-sm text-black/80 mt-2">Monitor real-time air quality across the globe</p>
        <p class="text-xs text-black/70 mt-1">Last updated: {{ currentTime.toLocaleString() }}</p>
      </div>
      
      <!-- Main Content -->
    <!-- Filter Box - Now Outside of the Map Section -->
    <div class="max-w-7xl mx-auto mt-4 px-6 flex flex-col items-end space-y-6 mb-10">
    <div class="flex space-x-2 w-full max-w-md">
      <input
        v-model="searchQuery"
        @keydown.enter="searchCountry"
        type="text"
        placeholder="Search by city or country"
        class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-400"
      />
      <button
        @click="searchCountry"
        class="bg-sky-500 hover:bg-sky-600 text-white rounded-md px-4 py-2 text-base font-medium"
      >
        Search
      </button>
    </div>
    </div>

    <main class="p-6 space-y-10 max-w-7xl mx-auto">
      <div id="map" class="h-[500px] w-full rounded-xl shadow-md border"></div>


      <!-- AQI Card Grid -->
      <section>
        <h2 class="text-3xl font-semibold mb-8 text-gray-700 flex items-center">
          📊 Air Quality by Location
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(city, index) in cities" :key="index" :class="[
            'hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-gradient-to-br border-0 rounded-lg shadow-md overflow-hidden',
            getBackgroundColor(city.aqi)
          ]">
            <!-- Card Header -->
            <div class="p-4 pb-3">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">{{ city.name }}</h3>
                <span :class="['text-white px-2 py-1 rounded-full text-sm font-medium', getBadgeColor(city.aqi)]">
                  {{ city.aqi }} AQI
                </span>
              </div>
            </div>

            <!-- Card Content -->
            <div class="px-4 pb-4 space-y-4">
              <div class="text-center">
                <p :class="['text-6xl font-extrabold', getColor(city.aqi)]">{{ city.aqi }}</p>
                <p class="text-sm text-gray-600 mt-1 font-medium">{{ getStatus(city.aqi) }}</p>
              </div>

              <!-- PM Readings -->
              <div v-if="city.pm10 && city.pm25" class="grid grid-cols-2 gap-3 text-xs">
                <div class="bg-white/50 rounded-lg p-2 text-center">
                  <p class="font-semibold text-gray-700">PM10</p>
                  <p class="text-gray-600">{{ city.pm10 }} μg/m³</p>
                </div>
                <div class="bg-white/50 rounded-lg p-2 text-center">
                  <p class="font-semibold text-gray-700">PM2.5</p>
                  <p class="text-gray-600">{{ city.pm25 }} μg/m³</p>
                </div>
              </div>

              <!-- Weather Info -->
              <div v-if="city.temperature" class="flex items-center justify-between text-sm bg-white/30 rounded-lg p-3">
                <div class="flex items-center space-x-2">
                  <CloudIcon class="w-4 h-4 text-blue-500" />
                  <span>{{ city.temperature }}°C</span>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                  <div v-if="city.humidity" class="flex items-center space-x-1">
                    <DropletsIcon class="w-3 h-3 text-blue-400" />
                    <span>{{ city.humidity }}%</span>
                  </div>
                  <div v-if="city.windSpeed" class="flex items-center space-x-1">
                    <WindIcon class="w-3 h-3 text-gray-500" />
                    <span>{{ city.windSpeed }} km/h</span>
                  </div>
                  <div v-if="city.uvIndex" class="flex items-center space-x-1">
                    <SunIcon class="w-3 h-3 text-yellow-500" />
                    <span>UV {{ city.uvIndex }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- AQI Scale Reference -->
      <section class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Air Quality Scale</h3>
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0 sm:space-x-4">
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-green-500 rounded"></div>
            <span class="text-sm">Good (0-50)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-yellow-500 rounded"></div>
            <span class="text-sm">Moderate (51-100)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-orange-500 rounded"></div>
            <span class="text-sm">Unhealthy for Sensitive (101-150)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-red-500 rounded"></div>
            <span class="text-sm">Unhealthy (151-200)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-purple-600 rounded"></div>
            <span class="text-sm">Very Unhealthy (201+)</span>
          </div>
        </div>
      </section>
      <!-- Major Air Pollutants Section -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Major Air Pollutants</h2>
            <p class="text-xl text-blue-600 font-medium">Street 96</p>
          </div>
        </div>

        <!-- Pollutants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

  <!-- PM2.5 Card -->
  <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">PM2.5</div>
          <div>
            <h3 class="font-semibold text-gray-900">Particulate Matter</h3>
            <p class="text-sm text-gray-600">(PM2.5)</p>
          </div>
        </div>
        <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
      <div class="flex items-end space-x-2">
        <span class="text-3xl font-bold text-green-600">6</span>
        <span class="text-sm text-gray-500 mb-1">μg/m³</span>
      </div>
    </div>
  </div>

  <!-- PM10 Card -->
  <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">PM10</div>
          <div>
            <h3 class="font-semibold text-gray-900">Particulate Matter</h3>
            <p class="text-sm text-gray-600">(PM10)</p>
          </div>
        </div>
        <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
      <div class="flex items-end space-x-2">
        <span class="text-3xl font-bold text-green-600">25</span>
        <span class="text-sm text-gray-500 mb-1">μg/m³</span>
      </div>
    </div>
  </div>

  <!-- CO Card -->
  <div class="bg-white rounded-2xl shadow-lg border-l-4 border-yellow-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-sm font-bold">CO</div>
          <div>
            <h3 class="font-semibold text-gray-900">Carbon Monoxide</h3>
            <p class="text-sm text-gray-600">(CO)</p>
          </div>
        </div>
        <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
      <div class="flex items-end space-x-2">
        <span class="text-3xl font-bold text-yellow-600">34</span>
        <span class="text-sm text-gray-500 mb-1">ppb</span>
      </div>
    </div>
  </div>

  <!-- SO2 Card -->
  <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">SO₂</div>
          <div>
            <h3 class="font-semibold text-gray-900">Sulfur Dioxide</h3>
            <p class="text-sm text-gray-600">(SO2)</p>
          </div>
        </div>
        <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
      <div class="flex items-end space-x-2">
        <span class="text-3xl font-bold text-green-600">9</span>
        <span class="text-sm text-gray-500 mb-1">ppb</span>
      </div>
    </div>
  </div>

  <!-- NO2 Card -->
  <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">NO₂</div>
          <div>
            <h3 class="font-semibold text-gray-900">Nitrogen Dioxide</h3>
            <p class="text-sm text-gray-600">(NO2)</p>
          </div>
        </div>
        <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
      <div class="flex items-end space-x-2">
        <span class="text-3xl font-bold text-green-600">13</span>
        <span class="text-sm text-gray-500 mb-1">ppb</span>
      </div>
    </div>
  </div>

  <!-- O3 Card -->
  <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 group">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-sm font-bold">O₃</div>
          <div>
            <h3 class="font-semibold text-gray-900">Ozone</h3>
            <p class="text-sm text-gray-600">(O3)</p>
          </div>
        </div>
        <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
      </div>
      <div class="flex items-end space-x-2">
        <span class="text-3xl font-bold text-green-600">10</span>
        <span class="text-sm text-gray-500 mb-1">ppb</span>
      </div>
    </div>
  </div>

</div>

      </div>

      <!-- Bottom Info Section -->
      <section class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <!-- Air Quality Ranking -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <Leaf class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h4 class="font-semibold text-gray-900">Air Quality Ranking</h4>
              <p class="text-sm text-gray-600">
                Currently, Phnom Penh ranks among the cities with the best air quality worldwide.
              </p>
            </div>
          </div>
        </div>

        <!-- AQI Comparison -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <span class="text-2xl font-bold text-yellow-600">1×</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-900">AQI Comparison</h4>
              <p class="text-sm text-gray-600">
                The AQI level on Street 96 is currently the same as in Phnom Penh, showing no difference.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import L from 'leaflet'
import axios from 'axios'
import 'leaflet/dist/leaflet.css'


import {
  MapPin as MapPinIcon,
  Cloud as CloudIcon,
  Droplets as DropletsIcon,
  Wind as WindIcon,
  Sun as SunIcon,
  ChevronRight,
  Smartphone,
  AlertTriangle,
  Leaf
} from 'lucide-vue-next'
// i18n (Composition API)
const { t } = useI18n()
const pollutants = ref({})
const searchQuery = ref('')
const selectedCity = ref('')
const countryInfo = ref(null)
let map
let marker = null

function getPollutantColor(key, value) {
  const num = parseInt(value)
  if (key === 'PM25' || key === 'PM2_5') return num > 35 ? 'red' : 'green'
  if (key === 'PM10') return num > 50 ? 'red' : 'green'
  if (key === 'CO') return num > 4 ? 'red' : 'green'
  if (key === 'NO2') return num > 100 ? 'red' : 'green'
  if (key === 'SO2') return num > 50 ? 'red' : 'green'
  if (key === 'O3') return num > 180 ? 'red' : 'green'
  return 'black'
}

function getPollutantIcon(key) {
  const icons = {
    PM2_5: '🌫',
    PM10: '🌁',
    CO: '🟤',
    NO2: '🟠',
    SO2: '🟡',
    O3: '🟢',
    Temperature: '🌡️',
    Humidity: '💧',
    Wind: '🌬️'
  }
  return icons[key] || '📍'
}

const fetchPollutants = async (lat, lon, name) => {
  try {
    await new Promise(resolve => setTimeout(resolve, 500)) // Simulate delay

    const values = {
      PM2_5: `${Math.floor(Math.random() * 80 + 5)} μg/m³`,
      PM10: `${Math.floor(Math.random() * 100 + 10)} μg/m³`,
      CO: `${(Math.random() * 3 + 0.1).toFixed(2)} ppm`,
      NO2: `${Math.floor(Math.random() * 60 + 5)} ppb`,
      SO2: `${Math.floor(Math.random() * 40 + 2)} ppb`,
      O3: `${Math.floor(Math.random() * 70 + 10)} ppb`,
      Temperature: `${Math.floor(Math.random() * 15 + 20)} °C`,
      Humidity: `${Math.floor(Math.random() * 50 + 30)}%`,
      Wind: `${Math.floor(Math.random() * 20 + 3)} km/h`
    }

    selectedCity.value = name
    pollutants.value = values
  } catch (err) {
    console.error('Simulated fetch error:', err)
    pollutants.value = { Error: 'Failed to load data' }
  }
}

const fetchCountryFlag = async (countryName) => {
  try {
    const res = await axios.get(`https://restcountries.com/v3.1/name/${encodeURIComponent(countryName)}?fullText=true`)
    const data = res.data[0]
    countryInfo.value = {
      name: data.name.common,
      flag: data.flags.svg
    }
  } catch (error) {
    console.error('Error fetching country flag:', error)
    countryInfo.value = null
  }
}

const searchCountry = async () => {
  if (!searchQuery.value.trim()) return

  try {
    const res = await axios.get('https://nominatim.openstreetmap.org/search', {
      params: {
        q: searchQuery.value,
        format: 'json',
        limit: 1
      }
    })

    if (res.data.length === 0) {
      alert('Location not found')
      return
    }

    const redIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowSize: [41, 41]
    })

    L.Marker.prototype.options.icon = redIcon

    const place = res.data[0]
    const lat = parseFloat(place.lat)
    const lon = parseFloat(place.lon)
    const displayName = place.display_name

    // Extract country from display name
    const countryMatch = displayName.split(',').pop()?.trim()
    if (countryMatch) {
      await fetchCountryFlag(countryMatch)
    }

    if (map) map.setView([lat, lon], 10)
    if (marker) map.removeLayer(marker)

    marker = L.marker([lat, lon]).addTo(map)
    marker.bindPopup('Fetching pollutant data...').openPopup()

    await fetchPollutants(lat, lon, displayName)

    if (marker) {
      let html = `<strong><span style="color:Blue">${selectedCity.value}</span></strong><br/>`
      if (countryInfo.value?.flag) {
        html += `<img src="${countryInfo.value.flag}" alt="${countryInfo.value.name} flag" width="40" height="25" style="margin-bottom: 4px"/><br/>`
      }

      for (const [key, val] of Object.entries(pollutants.value)) {
        const num = parseInt(val)
        const color = getPollutantColor(key, num)
        const icon = getPollutantIcon(key)
        html += `${icon} <strong>${key}</strong>: <span style="color:${color}">${val}</span><br/>`
      }
      marker.setPopupContent(html).openPopup()
    }
  } catch (error) {
    console.error('Error searching location:', error)
    alert('Failed to search location')
  }
}

onMounted(() => {
  map = L.map('map').setView([11.55, 104.91], 6)

  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap & CartoDB',
    subdomains: 'abcd'
  }).addTo(map)

  map.on('click', async (e) => {
    const { lat, lng } = e.latlng
    const name = `Lat: ${lat.toFixed(2)}, Lng: ${lng.toFixed(2)}`
    countryInfo.value = null // reset flag when clicking manually

    if (marker) map.removeLayer(marker)
    marker = L.marker([lat, lng]).addTo(map)
    marker.bindPopup('Fetching pollutant data...').openPopup()

    await fetchPollutants(lat, lng, name)

    if (marker) {
      let html = `<strong>${selectedCity.value}</strong><br/>`
      for (const [key, val] of Object.entries(pollutants.value)) {
        const num = parseInt(val)
        const color = getPollutantColor(key, num)
        const icon = getPollutantIcon(key)
        html += `${icon} <strong>${key}</strong>: <span style="color:${color}">${val}</span><br/>`
      }
      marker.setPopupContent(html).openPopup()
    }
  })
})
// Your generatePopupHTML function looks good as is, no changes needed.




// Reactive data
const currentTime = ref(new Date())
const cities = ref([
  {
    name: 'Phnom Penh',
    aqi: 42,
    pm10: 22,
    pm25: 5,
    temperature: 34,
    humidity: 56,
    windSpeed: 26,
    uvIndex: 3
  },
  {
    name: 'Bangkok',
    aqi: 91,
    pm10: 45,
    pm25: 18,
    temperature: 32,
    humidity: 68,
    windSpeed: 12,
    uvIndex: 8
  },
  {
    name: 'New Delhi',
    aqi: 175,
    pm10: 89,
    pm25: 65,
    temperature: 28,
    humidity: 45,
    windSpeed: 8,
    uvIndex: 6
  },
  {
    name: 'Tokyo',
    aqi: 37,
    pm10: 18,
    pm25: 8,
    temperature: 24,
    humidity: 52,
    windSpeed: 15,
    uvIndex: 4
  },
  {
    name: 'Beijing',
    aqi: 129,
    pm10: 67,
    pm25: 42,
    temperature: 22,
    humidity: 38,
    windSpeed: 18,
    uvIndex: 5
  },
  {
    name: 'Jakarta',
    aqi: 78,
    pm10: 35,
    pm25: 22,
    temperature: 31,
    humidity: 72,
    windSpeed: 9,
    uvIndex: 9
  }
])

let timeInterval = null

onMounted(() => {
  timeInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 60000)
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})

function getStatus(aqi) {
  if (aqi <= 50) return 'Good 🌿'
  if (aqi <= 100) return 'Moderate 😐'
  if (aqi <= 150) return 'Unhealthy for Sensitive Groups 😷'
  if (aqi <= 200) return 'Unhealthy 😷'
  return 'Very Unhealthy 🔥'
}

function getColor(aqi) {
  if (aqi <= 50) return 'text-green-500'
  if (aqi <= 100) return 'text-yellow-500'
  if (aqi <= 150) return 'text-orange-500'
  if (aqi <= 200) return 'text-red-500'
  return 'text-purple-600'
}

function getBadgeColor(aqi) {
  if (aqi <= 50) return 'bg-green-500'
  if (aqi <= 100) return 'bg-yellow-500'
  if (aqi <= 150) return 'bg-orange-500'
  if (aqi <= 200) return 'bg-red-500'
  return 'bg-purple-600'
}

function getBackgroundColor(aqi) {
  if (aqi <= 50) return 'bg-gradient-to-br from-green-50 to-green-100'
  if (aqi <= 100) return 'bg-gradient-to-br from-yellow-50 to-yellow-100'
  if (aqi <= 150) return 'bg-gradient-to-br from-orange-50 to-orange-100'
  if (aqi <= 200) return 'bg-gradient-to-br from-red-50 to-red-100'
  return 'bg-gradient-to-br from-purple-50 to-purple-100'
}
</script>


<style scoped>
/* Custom animations and transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.hover\:shadow-xl:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.hover\:-translate-y-1:hover {
  transform: translateY(-0.25rem);
}

/* Responsive text sizing */
@media (max-width: 640px) {
  .text-6xl {
    font-size: 3rem;
    line-height: 1;
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Custom animation keyframes */
@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }

  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes icon-float {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-4px);
  }
}

@keyframes number-pulse {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.05);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.5s ease forwards;
}

.animate-icon-float {
  animation: icon-float 2s ease-in-out infinite;
}

.animate-number-pulse {
  animation: number-pulse 2s ease-in-out infinite;
}
</style>
