<template>
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mt-10 px-6">
      <h1 class="text-4xl font-bold tracking-wide">🌍 Air Quality Dashboard</h1>
      <p class="text-sm text-black/80 mt-2">Monitor real-time air quality across the globe</p>
      <p class="text-xs text-black/70 mt-1">Last updated: {{ currentTime.toLocaleString() }}</p>
    </div>

    <!-- Search Box -->
    <div class="px-6 mt-4 flex flex-col items-end space-y-6 mb-10">
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
  </div>

  <!-- Main Content -->
  <main class="p-6 space-y-10">
    <!-- Global AQI Map -->
    <section>
      <div v-if="loading" class="text-center text-gray-600">Loading map and data...</div>
      <div v-if="error" class="text-center text-red-600">{{ error }}</div>
      <div id="map" class="h-[600px] w-full rounded shadow border"></div>
    </section>

    <!-- AQI Card Grid -->
    <section>
      <h2 class="text-2xl font-semibold mb-4 text-gray-700">🌐 Recent Air Quality Data</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="city in cities"
          :key="city"
          class="bg-white rounded-lg shadow-md p-4 border border-gray-100"
        >
          <h3 class="text-lg font-semibold mb-2">{{ city }}</h3>
          <p class="text-sm text-gray-600">AQI: <span class="font-bold">{{ aqiByCity[city] || 'N/A' }}</span></p>
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto p-6">
      <h2 class="text-2xl font-bold mb-6 text-center">🌍 Global Air Quality Overview</h2>

      <div class="flex flex-wrap gap-6 justify-center">
        <!-- Top Polluted Cities -->
        <div class="w-full md:w-[49%] bg-white shadow rounded-xl p-6 border border-red-200">
          <h3 class="text-lg font-semibold text-red-600 mb-4">🏆 Most Polluted Cities</h3>
          <table class="w-full text-sm border border-gray-300 rounded">
            <thead class="bg-red-50 text-red-800 uppercase text-xs">
              <tr>
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">City</th>
                <th class="px-3 py-2">AQI</th>
                <th class="px-3 py-2">Category</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(city, i) in pollutedCities" :key="i" class="hover:bg-red-100">
                <td class="px-3 py-2">{{ i + 1 }}</td>
                <td class="px-3 py-2">{{ city.city.aqi }}</td>
                <td class="px-3 py-2 font-bold" :class="getColor(city.aqi)">{{ city.aqi }}</td>
                <td class="px-3 py-2 italic">{{ getCategory(city.aqi) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Cleanest Cities -->
        <div class="w-full md:w-[49%] bg-white shadow rounded-xl p-6 border border-green-200">
          <h3 class="text-lg font-semibold text-green-600 mb-4">🍃 Cleanest Cities</h3>
          <table class="w-full text-sm border border-gray-300 rounded">
            <thead class="bg-green-50 text-green-800 uppercase text-xs">
              <tr>
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">City</th>
                <th class="px-3 py-2">AQI</th>
                <th class="px-3 py-2">Category</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(city, i) in cleanestCities" :key="i" class="hover:bg-green-100">
                <td class="px-3 py-2">{{ i + 1 }}</td>
                <td class="px-3 py-2">{{ city.city }}</td>
                <td class="px-3 py-2 font-bold" :class="getColor(city.aqi)">{{ city.aqi }}</td>
                <td class="px-3 py-2 italic">{{ getCategory(city.aqi) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const currentTime = ref(new Date())
setInterval(() => {
  currentTime.value = new Date()
}, 1000 * 60) // Update every 1 minute

const cities = [
  'London', 'Paris', 'Berlin', 'Madrid', 'Rome', 'Lisbon', 'Vienna', 'Warsaw', 'Budapest', 'Prague',
  'Athens', 'Amsterdam', 'Brussels', 'Dublin', 'Oslo', 'Stockholm', 'Helsinki', 'Copenhagen', 'Zurich', 'Belgrade',
  'Moscow', 'Istanbul', 'Cairo', 'Lagos', 'Nairobi', 'Cape Town', 'Johannesburg', 'Dubai', 'Doha', 'Tehran',
  'Delhi', 'Mumbai', 'Bangalore', 'Beijing', 'Shanghai', 'Tokyo', 'Seoul', 'Bangkok', 'Jakarta', 'Kuala Lumpur',
  'Singapore', 'Manila', 'Hanoi', 'Phnom Penh', 'Ho Chi Minh', 'Mexico City', 'New York', 'Los Angeles', 'Toronto', 'Buenos Aires'
]

const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')
const aqiByCity = ref({})
const pollutedCities = ref([])
const cleanestCities = ref([])
const map = ref(null)

const getMarkerColor = (aqi) => {
  if (aqi <= 50) return 'green'
  if (aqi <= 100) return 'yellow'
  if (aqi <= 150) return 'orange'
  if (aqi <= 200) return 'red'
  return 'purple'
}

const getCategory = (aqi) => {
  if (aqi <= 50) return 'Good'
  if (aqi <= 100) return 'Moderate'
  if (aqi <= 150) return 'Unhealthy for Sensitive'
  if (aqi <= 200) return 'Unhealthy'
  if (aqi <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}

const getColor = (aqi) => {
  if (aqi <= 50) return 'text-green-600'
  if (aqi <= 100) return 'text-yellow-600'
  if (aqi <= 150) return 'text-orange-500'
  if (aqi <= 200) return 'text-red-600'
  if (aqi <= 300) return 'text-purple-600'
  return 'text-rose-800'
}

const fetchCities = async () => {
  const results = []

  for (const item of cities) {
    try {
      const res = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${encodeURIComponent(item.city)}`, {
        headers: {
          'X-Api-Key': 'ymN5uLOtTZ0lIYjWUBD30w==OgcDgtbkNz5YMqTo'
        }
      })

      if (res.data && res.data.overall_aqi !== undefined) {
        results.push({
          city: item.city,
          country: item.country,
          aqi: res.data.overall_aqi
        })
      } else {
        console.warn("No AQI data for:", item.city)
      }
    } catch (err) {
      console.error(`Failed to fetch AQI for ${item.city}:`, err.message)
    }
  }

  pollutedCities.value = [...results].sort((a, b) => b.aqi - a.aqi).slice(0, 10)
  cleanestCities.value = [...results].sort((a, b) => a.aqi - b.aqi).slice(0, 10)

}

const fetchAndShowAQI = async () => {
  try {
    map.value = L.map('map').setView([20, 0], 2)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap contributors &copy; CartoDB',
      subdomains: 'abcd',
      maxZoom: 19
    }).addTo(map.value)

    for (const city of cities) {
      try {
        const response = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${encodeURIComponent(city)}`, {
          headers: { 'X-Api-Key': 'ymN5uLOtTZ0lIYjWUBD30w==OgcDgtbkNz5YMqTo' }
        })

        const aqiData = response.data
        if (!aqiData.overall_aqi) continue

        const geo = await axios.get(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(city)}&format=json&limit=1`)
        if (!geo.data.length) continue

        const { lat, lon } = geo.data[0]
        const aqi = aqiData.overall_aqi
        const color = getMarkerColor(aqi)

        const marker = L.circleMarker([lat, lon], {
          radius: 8,
          color,
          fillColor: color,
          fillOpacity: 0.8,
        }).addTo(map.value)

        marker.bindPopup(`<strong>${city}</strong><br/>AQI: ${aqi}`)
        aqiByCity.value[city] = aqi
      } catch (err) {
        console.warn(`Failed for ${city}:`, err)
      }
    }
  } catch (err) {
    error.value = 'Failed to load map or AQI data.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const searchCountry = async () => {
  if (!searchQuery.value) return
  const result = await axios.get(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery.value)}&format=json&limit=1`)
  if (result.data.length) {
    const { lat, lon } = result.data[0]
    map.value.setView([lat, lon], 5)
  }
}

onMounted(() => {
  fetchAndShowAQI()
  fetchCities()
  setInterval(fetchCities, 5 * 60 * 1000) // Refresh every 5 mins
  
})
</script>

<style scoped>
#map {
  height: 600px;
  width: 100%;
  z-index: 0;
}
</style>
