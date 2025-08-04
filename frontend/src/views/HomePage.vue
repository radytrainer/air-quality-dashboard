<template>
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
  <!-- Stylish Header -->
    <div class="mt-10 px-6">
      <div class="bg-gradient-to-r from-sky-100 to-white rounded-2xl shadow-sm p-6 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-800 flex items-center justify-center gap-2">
          <span class="text-sky-600 text-4xl"></span> Air Quality Dashboard
        </h1>
        <p class="text-base sm:text-lg text-gray-600 mt-2">Monitor real-time air quality across the globe</p>
        <p class="text-xs text-gray-500 mt-1 italic">Last updated: {{ currentTime.toLocaleString() }}</p>
      </div>
    </div>


    <!-- Search -->
    <div class="px-6 mt-4 flex flex-col items-end space-y-6 mb-10">
      <div class="flex space-x-2 w-full max-w-md">
        <input
          v-model="searchQuery"
          @keydown.enter="searchLocation"
          type="text"
          placeholder="Search by city or country"
          class="flex-1 border border-gray-300 rounded-md px-3 py-2"
        />
        <button @click="searchLocation" class="bg-sky-500 hover:bg-sky-600 text-white rounded-md px-4 py-2">
          Search
        </button>
      </div>
    </div>

    <!-- Main -->
    <main class="p-6 space-y-10">
      <!-- Pollutant Filters -->
    <section>
    <!-- Pollutant Filters -->
    <div class="flex flex-wrap gap-4 mb-4 items-center text-sm text-gray-700">
      <label
        v-for="pollutant in pollutants"
        :key="pollutant.value"
        class="flex items-center gap-2"
      >
        <input
          type="radio"
          name="pollutant"
          :value="pollutant.value"
          v-model="selectedPollutant"
          @change="updateMap"
        />
        {{ pollutant.label }}
      </label>
    </div>

    <div v-if="loading" class="text-center text-gray-600">Loading map and data...</div>
    <div v-if="error" class="text-center text-red-600">{{ error }}</div>

    <!-- Stylish Map -->
    <div class="rounded-2xl border border-gray-300 shadow overflow-hidden">
      <div class="relative h-[600px]">
        <div id="map" class="w-full h-full z-0"></div>
        <div class="absolute top-2 right-2 bg-white text-xs text-gray-600 px-3 py-1 rounded shadow">
          💡 Hold <kbd class="font-semibold">Ctrl</kbd> and scroll to zoom
        </div>
      </div>
    </div>
  </section>


      <!-- Top 10 AQI Rankings -->
      <section class="mt-12 px-2 md:px-6 space-y-8">
        <h2 class="text-2xl font-bold text-gray-800">Global AQI Rankings</h2>

        <div class="grid md:grid-cols-2 gap-8">
          <!-- Most Polluted -->
          <div class="bg-white rounded-2xl shadow p-6 border border-red-200">
            <h3 class="text-xl font-semibold text-red-600 mb-4">Top 10 Most Polluted Cities</h3>
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 border-b">
                  <th class="py-2">City</th>
                  <th class="py-2">Status</th>
                  <th class="py-2 text-center">{{ selectedPollutant.toUpperCase() }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(city, index) in top10MostPolluted"
                  :key="'polluted-' + index"
                  class="hover:bg-red-50 transition"
                >
                  <td class="py-2">{{ city.station.name }}</td>
                  <td class="py-2">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-white text-xs font-semdium"
                      :style="{ backgroundColor: getColor(city.value) }"
                    >
                      {{ getStatusLabel(city.value) }}
                    </span>
                  </td>
                  <td class="py-2 text-center">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-white text-xs font-semdium"
                      :class="{
                        'bg-green-600': city.value <= 50,
                        'bg-yellow-400 text-black': city.value > 50 && city.value <= 100,
                        'bg-orange-500': city.value > 100 && city.value <= 150,
                        'bg-red-600': city.value > 150 && city.value <= 200,
                        'bg-purple-700': city.value > 200 && city.value <= 300,
                        'bg-red-900': city.value > 300 && city.value <= 900
                      }"
                    >
                      {{ city.value }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cleanest -->
          <div class="bg-white rounded-2xl shadow p-6 border border-green-200">
            <h3 class="text-xl font-semibold text-green-600 mb-4">Top 10 Cleanest Cities</h3>
            <table class="w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 border-b">
                  <th class="py-2">City</th>
                  <th class="py-2">Status</th>
                  <th class="py-2 text-center">{{ selectedPollutant.toUpperCase() }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(city, index) in top10LeastPolluted"
                  :key="'cleanest-' + index"
                  class="hover:bg-green-50 transition"
                >
                  <td class="py-2">{{ city.station.name }}</td>
                  <td class="py-2">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-white text-xs font-smedium"
                      :style="{ backgroundColor: getColor(city.value) }"
                    >
                      {{ getStatusLabel(city.value) }}
                    </span>
                  </td>
                  <td class="py-2 text-center">
                    <span
                      class="inline-block px-3 py-1 rounded-full text-white text-xs font-smedium"
                      :class="{
                        'bg-green-600': city.value <= 50,
                        'bg-yellow-400 text-black': city.value > 50 && city.value <= 100,
                        'bg-orange-500': city.value > 100 && city.value <= 150,
                        'bg-red-600': city.value > 150 && city.value <= 200,
                        'bg-purple-700': city.value > 200 && city.value <= 300,
                        'bg-red-900': city.value > 300 && city.value <= 900
                      }"
                    >
                      {{ city.value }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>



<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { computed } from 'vue'

const aqiData = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')
const currentTime = ref(new Date())

let map = null
const markers = ref([])

const pollutants = ref([
  { label: 'AQI', value: 'aqi' },
  { label: 'PM₂.₅', value: 'pm25' },
  { label: 'PM₁₀', value: 'pm10' },
  { label: 'Ozone (O₃)', value: 'o3' },
  { label: 'NO₂', value: 'no2' },
  { label: 'SO₂', value: 'so2' },
  { label: 'CO', value: 'co' },
])
const selectedPollutant = ref('aqi')

const initMap = async () => {
  map = L.map('map', {
    center: [20, 100],
    zoom: 3,
    zoomControl: true,
    scrollWheelZoom: false,
  });

  // Changed map link to OpenStreetMap standard tiles
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: `
      &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a> |
      Powered by <a href="https://yourappwebsite.com" target="_blank" rel="noopener noreferrer">Air Quality Dashboard</a>
    `,
    maxZoom: 19,
    subdomains: 'abc', // optional for OSM, but you can keep or remove
  }).addTo(map);

  const mapContainer = map.getContainer();
  mapContainer.addEventListener('wheel', (e) => {
    if (e.ctrlKey) {
      map.scrollWheelZoom.enable();
    } else {
      map.scrollWheelZoom.disable();
    }
  });
};



const top10MostPolluted = computed(() => {
  return [...aqiData.value]
    .filter(d => d.value !== undefined && d.value !== '-')
    .sort((a, b) => b.value - a.value)
    .slice(0, 10)
})

const top10LeastPolluted = computed(() => {
  return [...aqiData.value]
    .filter(d => d.value !== undefined && d.value !== '-')
    .sort((a, b) => a.value - b.value)
    .slice(0, 10)
})

const updateMap = async () => {
  loading.value = true
  aqiData.value = []
  markers.value.forEach(marker => map.removeLayer(marker))
  markers.value = []

  try {
    // ✅ Call your Laravel API instead of WAQI directly
    const { data } = await axios.get('http://127.0.0.1:8000/api/airquality') // Replace with your deployed URL if needed

    if (data.status === 'ok') {
      const stations = data.data.slice(0, 200)
      const results = []

      for (const s of stations) {
        const value = selectedPollutant.value === 'aqi'
          ? s.aqi
          : s.iaqi?.[selectedPollutant.value]?.v

        if (value !== undefined && value !== '-') {
          results.push({ ...s, value })

          const marker = L.circleMarker([s.lat, s.lon], {
            radius: 6,
            fillColor: getColor(value),
            color: '#000',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
          }).addTo(map)

          marker.bindPopup(`
            <div style="font-family: Arial; font-size: 12px;">
              <b>${s.station.name}</b><br/>
              ${selectedPollutant.value.toUpperCase()}: <strong>${value}</strong><br/>
              Status: <span style="color:${getColor(value)}">${getStatusLabel(value)}</span>
            </div>
          `)

          markers.value.push(marker)
        }
      }

      aqiData.value = results
    } else {
      error.value = 'Failed to load station data.'
    }
  } catch (err) {
    console.error(err)
    error.value = 'Error loading data.'
  } finally {
    loading.value = false
    currentTime.value = new Date()
  }
}

const searchLocation = async () => {
  if (!searchQuery.value) return
  try {
    const result = await axios.get(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery.value)}&format=json&limit=1`)
    if (result.data.length) {
      const { lat, lon } = result.data[0]
      map.setView([parseFloat(lat), parseFloat(lon)], 8)
    } else {
      alert('Location not found')
    }
  } catch (e) {
    alert('Error searching location')
  }
}

const getColor = (value) => {
  const v = parseInt(value)
  if (v <= 50) return '#00e400'     // Good
  if (v <= 100) return '#f0dd0e'    // Moderate
  if (v <= 150) return '#ff7e00'    // Unhealthy for sensitive groups
  if (v <= 200) return '#ff0000'    // Unhealthy
  if (v <= 300) return '#7B1FA2'    // Very Unhealthy
  return '#7e0023'  // Hazardous
}

const getStatusLabel = (value) => {
  const v = parseInt(value)
  if (v <= 50) return 'Good'
  if (v <= 100) return 'Moderate'
  if (v <= 150) return 'USG'
  if (v <= 200) return 'Unhealthy'
  if (v <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}


onMounted(async () => {
  await initMap()
  await updateMap()
})
</script>

<style scoped>
.map-wrapper {
  height: 600px;
  width: 100%;
  padding: 0 1rem;
  position: relative;
  z-index: 0;
}

#map {
  width: 100%;
  height: 100%;
  border-radius: 0.75rem;
  box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
}
</style>
