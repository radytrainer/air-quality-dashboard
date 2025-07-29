<template>
  <div class="p-6 max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center">🌍 Global Air Quality Index</h1>

    <div v-if="loading" class="text-center text-gray-600">Loading data...</div>
    <div v-if="error" class="text-center text-red-600">{{ error }}</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="(data, index) in aqiList"
        :key="index"
        class="bg-white shadow-md rounded p-4 border-l-4"
        :class="getAQIColor(data.overall_aqi)"
      >
        <h2 class="text-xl font-semibold mb-2">🌆 {{ data.city }}</h2>
        <ul class="text-sm space-y-1">
          <li><strong>AQI:</strong> {{ data.overall_aqi }}</li>
          <li><strong>CO:</strong> {{ data.CO }}</li>
          <li><strong>NO2:</strong> {{ data.NO2 }}</li>
          <li><strong>OZONE:</strong> {{ data.OZONE }}</li>
          <li><strong>PM10:</strong> {{ data.PM10 }}</li>
          <li><strong>PM2.5:</strong> {{ data.PM25 }}</li>
          <li><strong>SO2:</strong> {{ data.SO2 }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const cities = [
  'London', 'Paris', 'Berlin', 'Madrid', 'Rome', 'Lisbon', 'Vienna', 'Warsaw', 'Budapest', 'Prague',
  'Athens', 'Amsterdam', 'Brussels', 'Dublin', 'Oslo', 'Stockholm', 'Helsinki', 'Copenhagen', 'Zurich', 'Belgrade',
  'Moscow', 'Istanbul', 'Cairo', 'Lagos', 'Nairobi', 'Cape Town', 'Johannesburg', 'Dubai', 'Doha', 'Tehran',
  'Delhi', 'Mumbai', 'Bangalore', 'Beijing', 'Shanghai', 'Tokyo', 'Seoul', 'Bangkok', 'Jakarta', 'Kuala Lumpur',
  'Singapore', 'Manila', 'Hanoi', 'Phnom Penh', 'Ho Chi Minh', 'Mexico City', 'New York', 'Los Angeles', 'Toronto', 'Buenos Aires'
]

const aqiList = ref([])
const loading = ref(true)
const error = ref(null)

const fetchAllAQI = async () => {
  for (const city of cities) {
    try {
      const response = await axios.get(`https://api.api-ninjas.com/v1/airquality?city=${city}`, {
        headers: {
          'X-Api-Key': 'ymN5uLOtTZ0lIYjWUBD30w==OgcDgtbkNz5YMqTo'
        }
      })
      aqiList.value.push({ ...response.data, city })
    } catch (err) {
      console.error(`Failed to fetch AQI for ${city}`, err)
    }
  }
  loading.value = false
}

const getAQIColor = (aqi) => {
  if (aqi <= 50) return 'border-green-500'
  if (aqi <= 100) return 'border-yellow-500'
  if (aqi <= 150) return 'border-orange-500'
  if (aqi <= 200) return 'border-red-500'
  return 'border-purple-700'
}

onMounted(() => {
  fetchAllAQI()
})
</script>

<style scoped>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
</style>
