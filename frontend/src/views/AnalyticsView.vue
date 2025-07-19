<template>
  <div class="p-6 max-w-3xl mx-auto space-y-4">
    <h1 class="text-2xl font-bold">Phnom Penh Live Weather & AQI</h1>

    <div v-if="loading" class="text-gray-500">Loading...</div>
    <div v-else>
      <div class="bg-white rounded-xl p-4 shadow space-y-2">
        <h2 class="text-lg font-semibold">Weather</h2>
        <p>🌡 Temperature: {{ weather.temp }}°C (Feels like {{ weather.feels_like }}°C)</p>
        <p>💧 Humidity: {{ weather.humidity }}%</p>
        <p>🌬 Wind Speed: {{ weather.wind_speed }} m/s</p>
        <p>☀️ UV Index: {{ weather.uvi }}</p>
      </div>

      <div class="bg-white rounded-xl p-4 shadow space-y-2 mt-4">
        <h2 class="text-lg font-semibold">Air Quality</h2>
        <p>🌫 AQI Level: {{ getAqiLevel(aqi.main.aqi) }} ({{ aqi.main.aqi }})</p>
        <p>PM2.5: {{ aqi.components.pm2_5 }} µg/m³</p>
        <p>PM10: {{ aqi.components.pm10 }} µg/m³</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const weather = ref({})
const aqi = ref({})
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await axios.get('http://localhost:8000/api/weather-aqi')
    weather.value = res.data.weather
    aqi.value = res.data.aqi
  } catch (err) {
    console.error('Failed to fetch data:', err)
    alert('Failed to load weather or AQI data.')
  } finally {
    loading.value = false
  }
})

function getAqiLevel(aqi) {
  if (aqi === 1) return 'Good'
  if (aqi === 2) return 'Fair'
  if (aqi === 3) return 'Moderate'
  if (aqi === 4) return 'Poor'
  if (aqi === 5) return 'Very Poor'
  return 'Unknown'
}
</script>

<style>
body {
  background-color: #f9fafb;
  font-family: 'Segoe UI', sans-serif;
}
</style>
