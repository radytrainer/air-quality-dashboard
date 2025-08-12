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
    const response = await axios.get('http://127.0.0.1:8000/api/aqi')
    if (response.data.status === 'ok' && Array.isArray(response.data.data)) {
      // If route param id is like "lat-lon" (e.g. "12.345-67.890"), split it:
      const [latStr, lonStr] = cityIdParam.split('-')
      const lat = parseFloat(latStr)
      const lon = parseFloat(lonStr)

      cityData.value = response.data.data.find(c => {
        return parseFloat(c.lat) === lat && parseFloat(c.lon) === lon
      }) || null
    }
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
