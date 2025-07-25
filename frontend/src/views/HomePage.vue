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

      <!-- Map Section -->
    <div id="map" class="relative h-[600px] w-full rounded-xl shadow-md border">

      <!-- AQI Legend -->
      <div
        class="absolute bottom-4 left-1/2 -translate-x-1/2 flex flex-wrap justify-center gap-x-4 gap-y-2 p-3 bg-white rounded-md shadow-md text-xs z-[999] pointer-events-none"
      >
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#8BC34A]"></span>Good</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#ffff00]"></span>Moderate</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#ff7e00]"></span>Unhealthy for sensitive groups</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#ff0000]"></span>Unhealthy</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#8f3f97]"></span>Very unhealthy</div>
        <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-[#7e0023]"></span>Hazardous</div>
      </div>
    </div>

      
      <!-- AQI Card Grid -->
      <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">🌍 Global Air Quality Overview</h2>

        <div class="flex flex-row flex-wrap gap-6 justify-center">
          <!-- Top Polluted Cities -->
          <div class="w-full md:w-[48%] bg-white shadow rounded-xl p-4 border border-gray-200">
            <h3 class="text-lg font-semibold mb-4 text-red-600">🏆 Top 10 Most Polluted Cities</h3>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-red-50 text-red-800 uppercase text-xs">
                  <tr>
                    <th class="px-4 py-2">Rank</th>
                    <th class="px-4 py-2">City</th>
                    <th class="px-4 py-2">Country</th>
                    <th class="px-4 py-2">AQI</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(city, index) in pollutedCitiesSorted"
                    :key="index"
                    :class="index % 2 === 0 ? 'bg-white' : 'bg-red-50/20'"
                    class="transition hover:bg-red-100/40"
                  >
                    <td class="px-4 py-2 font-medium">{{ index + 1 }}</td>
                    <td class="px-4 py-2">{{ city.city }}</td>
                    <td class="px-4 py-2">{{ city.country }}</td>
                    <td class="px-4 py-2">
                      <span
                        :class="getColor(city.aqi) + ' text-white px-2 py-1 rounded-full text-xs font-semibold'"
                      >
                        {{ city.aqi }}
                      </span>
                  </td>

                </tr>
              </tbody>
            </table>
          </div>
        </div>

          <!-- Cleanest Cities -->
          <div class="w-full md:w-[48%] bg-white shadow rounded-xl p-4 border border-gray-200">
            <h3 class="text-lg font-semibold mb-4 text-green-600">🌿Top 10 Least Polluted Cities</h3>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-green-50 text-green-800 uppercase text-xs">
                  <tr>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">City</th>
                    <th class="px-4 py-2">Country</th>
                    <th class="px-4 py-2">AQI</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(city, index) in cleanestCitiesSorted"
                    :key="index"
                    :class="index % 2 === 0 ? 'bg-white' : 'bg-green-50/20'"
                    class="transition hover:bg-green-100/40"
                  >
                    <td class="px-4 py-2">Live</td>
                    <td class="px-4 py-2">{{ city.city }}</td>
                    <td class="px-4 py-2">{{ city.country }}</td>
                    <td class="px-4 py-2">
                  <span
                    :class="getColor(city.aqi) + ' text-white px-2 py-1 rounded-full text-xs font-semibold'"
                  >
                    {{ city.aqi }}
                  </span>
                </td>

              </tr>
            </tbody>
          </table>
            </div>
          </div>
        </div>
      </div>
      
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
import { ref, onMounted, onUnmounted, computed } from 'vue'
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

const { t } = useI18n()

const pollutants = ref({})
const searchQuery = ref('')
const selectedCity = ref('')
const countryInfo = ref(null)
let map
let marker = null

// Live current time reactive ref
const currentTime = ref(new Date())
let timer = null
onMounted(() => {
  timer = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})
onUnmounted(() => {
  clearInterval(timer)
})

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
    await new Promise(resolve => setTimeout(resolve, 500))

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
  map = L.map('map', {
    scrollWheelZoom: false
  }).setView([11.55, 104.91], 6)

  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap & CartoDB',
    subdomains: 'abcd'
  }).addTo(map)

  // Enable Ctrl + Scroll zoom ONLY on map
  let hintShown = false

  const mapContainer = map.getContainer()

  mapContainer.addEventListener('wheel', function (e) {
    if (e.ctrlKey) {
      e.preventDefault() // Prevent page scroll
      map.scrollWheelZoom.enable()

      clearTimeout(map._scrollTimeout)
      map._scrollTimeout = setTimeout(() => {
        map.scrollWheelZoom.disable()
      }, 1000)
    } else {
      if (!hintShown) {
        hintShown = true
        console.log('Hold Ctrl + scroll to zoom the map') // Replace with toast if needed
        setTimeout(() => (hintShown = false), 3000)
      }
    }
  }, { passive: false }) // <- Important to allow e.preventDefault()

  // Map click behavior (your existing code)
  map.on('click', async (e) => {
    const { lat, lng } = e.latlng
    const name = `Lat: ${lat.toFixed(2)}, Lng: ${lng.toFixed(2)}`
    countryInfo.value = null

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



const pollutedCities = ref([
  { city: "Delhi", country: "India", aqi: 350 },
  { city: "Beijing", country: "China", aqi: 180 },
  { city: "Los Angeles", country: "USA", aqi: 75 },
  { city: "Paris", country: "France", aqi: 55 },
  { city: "London", country: "UK", aqi: 130 },
  { city: "Tokyo", country: "Japan", aqi: 210 },
  { city: "Bangkok", country: "Thailand", aqi: 190 },
  { city: "Moscow", country: "Russia", aqi: 300 },
  { city: "Cairo", country: "Egypt", aqi: 400 },
  { city: "Jakarta", country: "Indonesia", aqi: 250 }
])

const cleanestCities = ref([
  { city: "Reykjavik", country: "Iceland", aqi: 12 },
  { city: "Wellington", country: "New Zealand", aqi: 45 },
  { city: "Helsinki", country: "Finland", aqi: 55 },
  { city: "Zurich", country: "Switzerland", aqi: 80 },
  { city: "Sydney", country: "Australia", aqi: 150 },
  { city: "Bangkok", country: "Thailand", aqi: 190 },
  { city: "Vancouver", country: "Canada", aqi: 60 },
  { city: "Oslo", country: "Norway", aqi: 30 },
  { city: "Copenhagen", country: "Denmark", aqi: 40 },
  { city: "Dublin", country: "Ireland", aqi: 70 }
])

const AQICategories = [
  { label: "Good", min: 0, max: 50 },
  { label: "Moderate", min: 51, max: 100 },
  { label: "Unhealthy for Sensitive Groups", min: 101, max: 150 },
  { label: "Unhealthy", min: 151, max: 200 },
  { label: "Very Unhealthy", min: 201, max: 300 },
  { label: "Hazardous", min: 301, max: 500 },
]

function getAQICategory(aqi) {
  const num = Number(aqi)
  for (const cat of AQICategories) {
    if (num >= cat.min && num <= cat.max) {
      return cat.label
    }
  }
  return "Unknown"
}

function getColor(aqi) {
  const num = Number(aqi)
  if (num <= 50) return "bg-green-400"
  if (num <= 100) return "bg-yellow-400"
  if (num <= 150) return "bg-orange-400"
  if (num <= 200) return "bg-red-400"
  if (num <= 300) return "bg-purple-400"
  if (num <= 500) return "bg-red-800"
}
const orderTable1 = [
  "Hazardous",
  "Very Unhealthy",
  "Unhealthy",
  "Unhealthy for Sensitive Groups",
  "Moderate",
  "Good",
]

const orderTable2 = [
  
  "Good",
  "Moderate",
  "Unhealthy for Sensitive Groups",
  "Unhealthy",
  "Very Unhealthy",
  "Hazardous",
]

const pollutedCitiesSorted = computed(() => {
  return pollutedCities.value
    .filter(city => orderTable1.includes(getAQICategory(city.aqi)))
    .sort((a, b) =>
      orderTable1.indexOf(getAQICategory(a.aqi)) - orderTable1.indexOf(getAQICategory(b.aqi))
    )
})

const cleanestCitiesSorted = computed(() => {
  return cleanestCities.value
    .filter(city => orderTable2.includes(getAQICategory(city.aqi)))
    .sort((a, b) =>
      orderTable2.indexOf(getAQICategory(a.aqi)) - orderTable2.indexOf(getAQICategory(b.aqi))
    )
})
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
