<template>
  <div class="container mx-auto p-6 mt-10 max-w-3xl">
    <button
      @click="$router.back()"
      class="mb-6 px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600"
    >
      ← Back
    </button>

    <div v-if="loading" class="text-center text-gray-600">Loading city data...</div>
    <div v-else-if="!cityData" class="text-center text-red-600">
      City data not found.
    </div>
    <div v-else class="bg-white rounded-2xl shadow-md p-6">
      <div class="flex items-center gap-4 mb-6">
        <img :src="cityData.flag" alt="Flag" class="w-16 h-10 object-cover rounded" />
        <h1 class="text-3xl font-bold">{{ cityData.name }}</h1>
      </div>

      <table class="w-full text-left text-gray-800">
        <tbody>
          <tr>
            <th class="py-2">AQI</th>
            <td>
              <span
                class="px-3 py-1 rounded text-white font-semibold"
                :style="{ backgroundColor: getColor(cityData.aqi) }"
              >
                {{ cityData.aqi }} ({{ getStatus(cityData.aqi) }})
              </span>
            </td>
          </tr>
          <tr>
            <th class="py-2">PM10</th>
            <td>{{ cityData.pm10 }}</td>
          </tr>
          <tr>
            <th class="py-2">PM2.5</th>
            <td>{{ cityData.pm25 }}</td>
          </tr>
          <tr>
            <th class="py-2">CO</th>
            <td>{{ cityData.co }}</td>
          </tr>
          <tr>
            <th class="py-2">NO2</th>
            <td>{{ cityData.no2 }}</td>
          </tr>
          <tr>
            <th class="py-2">SO2</th>
            <td>{{ cityData.so2 }}</td>
          </tr>
          <tr>
            <th class="py-2">O3</th>
            <td>{{ cityData.o3 }}</td>
          </tr>
          <tr>
            <th class="py-2">Coordinates</th>
            <td>{{ cityData.lat }}, {{ cityData.lon }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'

const route = useRoute()
const cityIdParam = route.params.id 
const cityData = ref(null)
const loading = ref(false)

const getColor = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return '#3cb43c'
  if (val <= 100) return '#e6e600'
  if (val <= 150) return '#ff9900'
  if (val <= 200) return '#ff4d4d'
  if (val <= 300) return '#9b59b6'
  return '#c0392b'
}

const getStatus = (aqi) => {
  const val = parseInt(aqi)
  if (val <= 50) return 'Good'
  if (val <= 100) return 'Moderate'
  if (val <= 150) return 'Unhealthy for Sensitive Groups'
  if (val <= 200) return 'Unhealthy'
  if (val <= 300) return 'Very Unhealthy'
  return 'Hazardous'
}

const fetchCityData = async () => {
  loading.value = true
  try {
    // Fetch both global AQI data and Phnom Penh data
    const [globalRes, phnomRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/aqi'),
      axios.get('http://127.0.0.1:8000/api/air-quality/phnom-penh')
    ])

    let allCities = []

    // Global cities data
    if (globalRes.data.status === 'ok' && Array.isArray(globalRes.data.data)) {
      allCities = [...globalRes.data.data]
    }

    // Phnom Penh data from OpenWeather
    if (phnomRes.data && phnomRes.data.list && phnomRes.data.list.length > 0) {
      const ppm = phnomRes.data.list[0]
      const phnomPenhAQI = ppm.main.aqi

      // Convert 1–5 scale to 0–500 scale
      const scaleMap = { 1: 50, 2: 100, 3: 150, 4: 200, 5: 300 }
      const convertedAQI = scaleMap[phnomPenhAQI] || 0

      const phnomPenhData = {
        name: 'Phnom Penh',
        country: 'Cambodia',
        flag: 'https://flagcdn.com/w320/kh.png',
        lat: 11.562108,
        lon: 104.888535,
        aqi: convertedAQI,
        pm10: ppm.components.pm10,
        pm25: ppm.components.pm2_5,
        co: ppm.components.co,
        no2: ppm.components.no2,
        so2: ppm.components.so2,
        o3: ppm.components.o3
      }

      allCities.push(phnomPenhData)
    }

    // Match the city by lat-lon from the route
    const [latStr, lonStr] = cityIdParam.split('-')
    const lat = parseFloat(latStr)
    const lon = parseFloat(lonStr)

    cityData.value = allCities.find(c =>
      parseFloat(c.lat) === lat && parseFloat(c.lon) === lon
    ) || null

  } catch (error) {
    console.error('Error fetching city AQI:', error)
    cityData.value = null
  } finally {
    loading.value = false
  }
}


onMounted(() => {
  fetchCityData()
})
</script>

<style scoped>
th {
  font-weight: 600;
  width: 150px;
}
td {
  font-weight: 400;
}
</style>
