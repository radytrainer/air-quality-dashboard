<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 to-purple-200 p-6">
    <!-- Header -->
    <header class="flex flex-col items-center text-center mb-10">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
        <h1 class="text-4xl font-extrabold text-purple-800">
          Air Quality Comparison 
        </h1>
        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm ml-2">Live</span>
      </div>
      <p class="text-gray-600 text-sm">
        📈 Real-time air quality analysis across global cities with timezone intelligence
      </p>
    </header>

    <!-- Info Boxes -->
     <!-- Stats Overview -->
    <div class="grid md:grid-cols-3 gap-6 mb-12">
      <div class="bg-white shadow-lg p-5 rounded-xl text-center transition hover:shadow-xl">
        <div class="text-purple-600 text-2xl mb-2">📊</div>
        <div class="text-3xl font-bold text-gray-800">{{ totalCities }}</div>
        <div class="text-gray-500 text-xl">Cities Tracked</div>
      </div>
      <div class="bg-white shadow-lg p-5 rounded-xl text-center transition hover:shadow-xl">
        <div class="text-purple-600 text-2xl mb-2">🌐</div>
        <div class="text-3xl font-bold text-gray-800">{{ totalCountries }}</div>
        <div class="text-gray-500 text-xl">Countries</div>
      </div>
      <div class="bg-white shadow-lg p-5 rounded-xl text-center transition hover:shadow-xl">
        <div class="text-purple-600 text-2xl mb-2">🕒</div>
        <div class="text-3xl font-bold text-gray-800">{{ activeZones }}</div>
        <div class="text-gray-500 text-xl">Active Timezones</div>
      </div>
    </div>

    <!-- Compare Section -->
    <div class="bg-white p-8 rounded-2xl shadow-xl">
      <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
        🔍 Select Cities to Compare
      </h2>
      <!-- City Selectors with Timezone & Time -->
      <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-xl shadow-inner">
          <label class="font-semibold mb-1 block">Primary City</label>
          <p class="text-xs text-gray-500 mb-1">Choose your first comparison point</p>
          <div class="flex items-center gap-2 mb-2">
            <img
              v-if="city1Flag"
              :src="city1Flag"
              alt="flag"
              class="w-6 h-4 rounded-sm object-cover"
            />
            <span class="font-mono text-purple-700 text-xl">
              {{ city1TimeDisplay }}
            </span>
          </div>
          <CitySelector v-model="selectedCity1" :cities="cities" placeholder="Choose your first city..." />
        </div>

        <div class="bg-gray-50 p-4 rounded-xl shadow-inner">
          <label class="font-semibold mb-1 block">Secondary City</label>
          <p class="text-xs text-gray-500 mb-1">Choose your second comparison point</p>
          <div class="flex items-center gap-2 mb-2">
            <img
              v-if="city2Flag"
              :src="city2Flag"
              alt="flag"
              class="w-6 h-4 rounded-sm object-cover"
            />
            <span class="font-mono text-purple-700 text-xl">
              {{ city2TimeDisplay }}
            </span>
          </div>
          <CitySelector v-model="selectedCity2" :cities="cities" placeholder="Choose your second city..." />
        </div>
      </div>

      <!-- VS Divider -->
      <div class="flex justify-center items-center mb-6">
        <div class="bg-purple-100 px-6 py-2 rounded-full font-bold text-purple-800 shadow">VS</div>
      </div>

      <!-- Result Cards -->
      <div v-if="city1Data && city2Data" class="grid md:grid-cols-2 gap-6">
        <CityCard :city="city1Data" />
        <CityCard :city="city2Data" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import CitySelector from '@/components/CitySelectorForCompare.vue'
import CityCard from '@/components/CityCard.vue'

// Mock city data with AQI + flags + timezone
const cities = [
  { name: 'Phnom Penh', flag: 'https://flagcdn.com/kh.svg', country: 'Cambodia' },
  { name: 'New Delhi', flag: 'https://flagcdn.com/in.svg', country: 'India' },
  { name: 'Bangkok', flag: 'https://flagcdn.com/th.svg', country: 'Thailand' },
  { name: 'Beijing', flag: 'https://flagcdn.com/cn.svg', country: 'China' },
  { name: 'Tokyo', flag: 'https://flagcdn.com/jp.svg', country: 'Japan' },
  { name: 'Jakarta', flag: 'https://flagcdn.com/id.svg', country: 'Indonesia' }
]

const selectedCity1 = ref('')
const selectedCity2 = ref('')
const city1Data = ref(null)
const city2Data = ref(null)

const totalCities = cities.length
const totalCountries = new Set(cities.map(c => c.country)).size
const activeZones = 0

// Your existing mock data for city AQI & weather info
const mockData = {
  "Phnom Penh": {
    name: "Phnom Penh", aqi: 42, level: "Good", color: "bg-green-100", textColor: "text-green-700",
    pm10: 22, pm25: 5, temp: 34, humidity: 56, wind: 26, uv: 3, timezone: "UTC+7"
  },
  "New Delhi": {
    name: "New Delhi", aqi: 175, level: "Unhealthy", color: "bg-red-100", textColor: "text-red-700",
    pm10: 89, pm25: 65, temp: 28, humidity: 45, wind: 8, uv: 6, timezone: "UTC+5.5"
  },
  "Tokyo": {
    name: "Tokyo", aqi: 37, level: "Good", color: "bg-green-100", textColor: "text-green-700",
    pm10: 18, pm25: 8, temp: 24, humidity: 52, wind: 15, uv: 4, timezone: "UTC+9"
  },
  "Beijing": {
    name: "Beijing", aqi: 129, level: "Unhealthy for Sensitive Groups", color: "bg-orange-100", textColor: "text-orange-700",
    pm10: 67, pm25: 42, temp: 22, humidity: 38, wind: 18, uv: 5, timezone: "UTC+8"
  },
  "Bangkok": {
    name: "Bangkok", aqi: 91, level: "Moderate", color: "bg-yellow-100", textColor: "text-yellow-700",
    pm10: 45, pm25: 18, temp: 32, humidity: 68, wind: 12, uv: 8, timezone: "UTC+7"
  },
  "Jakarta": {
    name: "Jakarta", aqi: 78, level: "Moderate", color: "bg-yellow-100", textColor: "text-yellow-700",
    pm10: 35, pm25: 22, temp: 31, humidity: 72, wind: 9, uv: 9, timezone: "UTC+7"
  }
}

// Fetch city data from mock
const fetchCityData = async (cityName) => {
  return mockData[cityName] || null
}

// Helper to parse UTC offset string and get current time in that zone
function getTimeInTimezone(offsetStr) {
  // e.g. "UTC+7", "UTC+5.5"
  const match = offsetStr.match(/UTC([+-])(\d+)(?:\.(\d))?/)
  if (!match) return ''

  const sign = match[1] === '+' ? 1 : -1
  const hours = parseInt(match[2])
  const minutes = match[3] ? parseInt(match[3]) * 6 : 0

  const utc = new Date(new Date().toUTCString().slice(0, -4)) // current UTC time
  utc.setHours(utc.getHours() + sign * hours)
  utc.setMinutes(utc.getMinutes() + sign * minutes)

  return utc.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const city1Flag = computed(() => {
  const city = cities.find(c => c.name === selectedCity1.value)
  return city ? city.flag : ''
})
const city2Flag = computed(() => {
  const city = cities.find(c => c.name === selectedCity2.value)
  return city ? city.flag : ''
})

const city1TimeDisplay = computed(() => {
  if (!city1Data.value) return 'Select a city'
  return `${getTimeInTimezone(city1Data.value.timezone)} (${city1Data.value.timezone})`
})

const city2TimeDisplay = computed(() => {
  if (!city2Data.value) return 'Select a city'
  return `${getTimeInTimezone(city2Data.value.timezone)} (${city2Data.value.timezone})`
})

// Fetch city data when selected changes
watch(selectedCity1, async (newVal) => {
  if (newVal) city1Data.value = await fetchCityData(newVal)
  else city1Data.value = null
})
watch(selectedCity2, async (newVal) => {
  if (newVal) city2Data.value = await fetchCityData(newVal)
  else city2Data.value = null
})
</script>
