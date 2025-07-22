<template>
  <div class="p-6 max-w-xl mx-auto bg-white dark:bg-gray-900 rounded-lg shadow-md space-y-8">
    <h1 class="text-2xl font-bold mb-4 text-center text-gray-900 dark:text-white">
      🌤️ ASEAN Cities Weather + AQI
    </h1>

    <div v-if="weatherData.length">
      <div
        v-for="(weather, i) in weatherData"
        :key="weather.location.name"
        class="space-y-2 text-center border-b pb-4"
      >
        <img :src="weather.current.condition.icon" class="mx-auto" />
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
          {{ weather.location.name }}, {{ weather.location.country }}
        </h2>
        <p class="text-gray-700 dark:text-gray-200">🌡️ Temp: {{ weather.current.temp_c }}°C</p>
        <p class="text-gray-700 dark:text-gray-200">💧 Humidity: {{ weather.current.humidity }}%</p>
        <p class="text-gray-700 dark:text-gray-200">🌬️ Wind: {{ weather.current.wind_kph }} kph</p>
        <p class="text-gray-700 dark:text-gray-200">☁️ Condition: {{ weather.current.condition.text }}</p>
        <p class="text-gray-700 dark:text-gray-200">
          🏭 AQI (EU): 
          <span :class="getAqiColor(aqiData[i]?.aqi)">
            {{ aqiData[i]?.aqi ?? 'N/A' }}
          </span>
        </p>
        <p class="text-gray-500 text-sm">Last Updated: {{ weather.current.last_updated }}</p>
      </div>
    </div>

    <div v-else class="text-center text-gray-500">Loading data...</div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const cities = [
  { name: 'Phnom Penh', lat: 11.5564, lon: 104.9282 },
  { name: 'Bangkok', lat: 13.7563, lon: 100.5018 },
  { name: 'Jakarta', lat: -6.2088, lon: 106.8456 },
  { name: 'Hanoi', lat: 21.0285, lon: 105.8542 },
  { name: 'Kuala Lumpur', lat: 3.139, lon: 101.6869 },
  { name: 'Singapore', lat: 1.3521, lon: 103.8198 },
  { name: 'Manila', lat: 14.5995, lon: 120.9842 },
  { name: 'Vientiane', lat: 17.9757, lon: 102.6331 },
  { name: 'Yangon', lat: 16.8409, lon: 96.1735 },
  { name: 'Brunei', lat: 4.5353, lon: 114.7277 }
]

const weatherData = ref([])
const aqiData = ref([])

const fetchData = async () => {
  try {
    const weatherPromises = cities.map(city =>
      axios.get('https://api.weatherapi.com/v1/current.json', {
        params: { key: 'f41466962858499381473152252107', q: city.name }
      })
    )
    const aqiPromises = cities.map(city =>
      axios.get('https://air-quality-api.open-meteo.com/v1/air-quality', {
        params: {
          latitude: city.lat,
          longitude: city.lon,
          hourly: 'pm2_5',
          current: 'european_aqi'
        }
      })
    )

    const [weatherRes, aqiRes] = await Promise.all([
      Promise.all(weatherPromises),
      Promise.all(aqiPromises)
    ])

    weatherData.value = weatherRes.map(r => r.data)
    aqiData.value = aqiRes.map(r => ({
      aqi: r.data?.current?.european_aqi ?? null
    }))
  } catch (err) {
    console.error('Error fetching API data', err)
  }
}

onMounted(() => {
  fetchData()
  const interval = setInterval(fetchData, 60_000)
  onUnmounted(() => clearInterval(interval))
})

function getAqiColor(aqi) {
  if (aqi == null) return 'text-gray-500'
  if (aqi <= 20) return 'text-green-600'
  if (aqi <= 40) return 'text-yellow-500'
  if (aqi <= 60) return 'text-orange-500'
  if (aqi <= 80) return 'text-red-500'
  return 'text-purple-600'
}
</script>

<style scoped>
img { width: 64px; height: 64px; }
</style>
