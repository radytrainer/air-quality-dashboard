<template>
  <div class="min-h-screen bg-gray-light p-6">
    <!-- Header -->
    <header class="flex flex-col items-center text-center mb-10">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-3 h-3 bg-black-500 rounded-full animate-pulse"></div>
        <h1 class="text-4xl font-extrabold text-black-800">
          Air Quality Comparison 
        </h1>
        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm ml-2">Live</span>
      </div>
      <p class="text-gray-600 text-sm">
        📈 Real-time air quality analysis across global cities with timezone intelligence
      </p>
    </header>

    <!-- Info Boxes Styled Like Frame -->
<div class="grid md:grid-cols-3 gap-6 mb-12">
  <!-- Cities Tracked -->
  <div class="flex items-center bg-white shadow-md rounded-xl p-5 border-l-4 border-purple-500 w-full">
    <div class="ml-3">
      <div class="text-gray-800 font-semibold text-lg">Cities Tracked</div>
      <div class="text-sm text-gray-500">These cities are actively monitored for pollutant levels.</div>
      <div class="mt-2 text-purple-600 text-3xl font-bold">{{ totalCities }}</div>
    </div>
  </div>

  <!-- Countries -->
  <div class="flex items-center bg-white shadow-md rounded-xl p-5 border-l-4 border-blue-500 w-full">
    <div class="ml-3">
      <div class="text-gray-800 font-semibold text-lg">Countries</div>
      <div class="text-sm text-gray-500">Data has been gathered across multiple nations worldwide.</div>
      <div class="mt-2 text-blue-600 text-3xl font-bold">{{ totalCountries }}</div>
    </div>
  </div>

  <!-- Active Timezones -->
  <div class="flex items-center bg-white shadow-md rounded-xl p-5 border-l-4 border-green-500 w-full">
    <div class="ml-3">
      <div class="text-gray-800 font-semibold text-lg">Active Timezones</div>
      <div class="text-sm text-gray-500">Monitoring is currently active in these global timezones.</div>
      <div class="mt-2 text-green-600 text-3xl font-bold">{{ activeZones }}</div>
    </div>
  </div>
</div>
<!-- Compare Section -->
<div class="bg-white p-8 rounded-2xl shadow-xl">
  <!-- Header -->
  <h2 class="text-2xl font-bold mb-8 text-gray-800 flex items-center gap-2">
    <span class="text-purple-600 text-3xl">🔍</span>
    Select Cities to Compare
  </h2>

  <!-- City Selectors -->
  <div class="grid md:grid-cols-2 gap-6 mb-10">
    <!-- Primary City -->
    <div class="bg-white p-6 rounded-xl shadow">
      <label class="font-semibold text-gray-700 text-xl text-base mb-1 block">Primary City</label>
      <p class="text-sm text-gray-500 mb-3">Choose your first comparison point</p>
      <div class="flex items-center gap-3 mb-4">
        <img v-if="city1Flag" :src="city1Flag" alt="flag" class="w-6 h-4 rounded-sm object-cover" />
        <span class="font-mono text-purple-700 text-lg">{{ city1TimeDisplay }}</span>
      </div>
      <CitySelector v-model="selectedCity1" :cities="cities" placeholder="Choose your first city..." />
    </div>

    <!-- Secondary City -->
    <div class="bg-white p-6 rounded-xl shadow">
      <label class="font-semibold text-gray-700 text-xl text-base mb-1 block">Secondary City</label>
      <p class="text-sm text-gray-500 mb-3">Choose your second comparison point</p>
      <div class="flex items-center gap-3 mb-4">
        <img v-if="city2Flag" :src="city2Flag" alt="flag" class="w-6 h-4 rounded-sm object-cover" />
        <span class="font-mono text-purple-700 text-lg">{{ city2TimeDisplay }}</span>
      </div>
      <CitySelector v-model="selectedCity2" :cities="cities" placeholder="Choose your second city..." />
    </div>
  </div>

  <!-- VS Badge -->
  <div class="flex justify-center items-center mb-8">
    <div class="bg-purple-100 px-6 py-2 rounded-full font-bold text-purple-800 shadow-sm tracking-wide text-sm">
      VS
    </div>
  </div>

  <!-- Error Message -->
  <p v-if="errorMessage" class="text-red-600 font-semibold mb-6 text-center text-sm">
    {{ errorMessage }}
  </p>

  <!-- Alerts -->
  <div v-if="city1Data && city1Data.aqi > 100" class="bg-red-50 p-4 mb-4 rounded-xl shadow">
    <p class="text-red-700 font-medium text-sm">
      🚨 <strong>{{ city1Data.name }}</strong> has poor air quality (AQI: <strong>{{ city1Data.aqi }}</strong>) — {{ city1Data.level }}
    </p>
  </div>

  <div v-if="city2Data && city2Data.aqi > 100" class="bg-red-50 p-4 mb-4 rounded-xl shadow">
    <p class="text-red-700 font-medium text-sm">
      🚨 <strong>{{ city2Data.name }}</strong> has poor air quality (AQI: <strong>{{ city2Data.aqi }}</strong>) — {{ city2Data.level }}
    </p>
  </div>

  <!-- Result Cards -->
  <div v-if="city1Data && city2Data && !errorMessage" class="grid md:grid-cols-2 gap-6">
    <CityCard :city="city1Data" />
    <CityCard :city="city2Data" />
  </div>

  <!-- Chart Comparison -->
  <div v-if="city1Data && city2Data && !errorMessage" class="mt-10 bg-gray-50 p-6 rounded-xl shadow-inner">
    <h3 class="text-xl font-bold mb-4 text-center text-gray-700">📊 Pollutant Level Comparison</h3>
    <CompareBarChart :city1="city1Data" :city2="city2Data" />
  </div>

  <!-- Reset Button -->
  <button @click="resetSelection"
    class="mt-10 mx-auto block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-2 rounded shadow transition">
    🔄 Reset Comparison
  </button>
  <!-- AQI Color Guide -->
   <!-- AQI Color Guide -->
<div class="bg-gray-50 rounded-lg p-4 mt-5 text-sm text-gray-700  w-full flex flex-col">
  <h3 class="font-semibold text-center mb-3">🌫 AQI Color Guide</h3>
  <div class="flex justify-center gap-[10px]">
    <div class="flex items-center gap-2 whitespace-nowrap">
      <span class="w-4 h-4 bg-green-500 rounded"></span>
      Good (0–50)
    </div>
    <div class="flex items-center gap-2 whitespace-nowrap">
      <span class="w-4 h-4 bg-yellow-400 rounded"></span>
      Moderate (51–100)
    </div>
    <div class="flex items-center gap-2 whitespace-nowrap">
      <span class="w-4 h-4 bg-orange-500 rounded"></span>
      Unhealthy for Sensitive Groups (101–150)
    </div>
    <div class="flex items-center gap-2 whitespace-nowrap">
      <span class="w-4 h-4 bg-red-500 rounded"></span>
      Unhealthy (151+)
    </div>
  </div>
</div>


</div>
</div>
  

</template>

<script setup>
import { ref, computed, watch } from 'vue'
import CitySelectorForCompare from '@/components/CitySelectorForCompare.vue'
import CityCard from '@/components/CityCard.vue'
import CompareBarChart from '@/components/CompareBarChart.vue'
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

const mockData = {
  'Phnom Penh': { name: 'Phnom Penh', country: 'Cambodia', aqi: 42, level: 'Good', color: 'bg-green-100', textColor: 'text-green-700', pm10: 22, pm25: 5, co: 0.3, no2: 4, so2: 2, o3: 5, temp: 34, humidity: 56, wind: 26, uv: 3, timezone: 'UTC+7' },
  'New Delhi': { name: 'New Delhi', country: 'India', aqi: 175, level: 'Unhealthy', color: 'bg-red-100', textColor: 'text-red-700', pm10: 89, pm25: 65, co: 1.2, no2: 30, so2: 10, o3: 12, temp: 28, humidity: 45, wind: 8, uv: 6, timezone: 'UTC+5.5' },
  'Tokyo': { name: 'Tokyo', country: 'Japan', aqi: 37, level: 'Good', color: 'bg-green-100', textColor: 'text-green-700', pm10: 18, pm25: 8, co: 0.2, no2: 6, so2: 1, o3: 9, temp: 24, humidity: 52, wind: 15, uv: 4, timezone: 'UTC+9' },
  'Beijing': { name: 'Beijing', country: 'China', aqi: 129, level: 'Unhealthy for Sensitive Groups', color: 'bg-orange-100', textColor: 'text-orange-700', pm10: 67, pm25: 42, co: 0.8, no2: 24, so2: 7, o3: 10, temp: 22, humidity: 38, wind: 18, uv: 5, timezone: 'UTC+8' },
  'Bangkok': { name: 'Bangkok', country: 'Thailand', aqi: 91, level: 'Moderate', color: 'bg-yellow-100', textColor: 'text-yellow-700', pm10: 45, pm25: 18, co: 0.5, no2: 10, so2: 3, o3: 7, temp: 32, humidity: 68, wind: 12, uv: 8, timezone: 'UTC+7' },
  'Jakarta': { name: 'Jakarta', country: 'Indonesia', aqi: 78, level: 'Moderate', color: 'bg-yellow-100', textColor: 'text-yellow-700', pm10: 35, pm25: 22, co: 0.6, no2: 11, so2: 4, o3: 8, temp: 31, humidity: 72, wind: 9, uv: 9, timezone: 'UTC+7' }
}

function getTimeInTimezone(offsetStr) {
  const match = offsetStr.match(/UTC([+-])(\d+)(?:\.(\d))?/)
  if (!match) return ''
  const sign = match[1] === '+' ? 1 : -1
  const hours = parseInt(match[2])
  const minutes = match[3] ? parseInt(match[3]) * 6 : 0
  const utc = new Date(new Date().toUTCString().slice(0, -4))
  utc.setHours(utc.getHours() + sign * hours)
  utc.setMinutes(utc.getMinutes() + sign * minutes)
  return utc.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const city1Flag = computed(() => cities.find(c => c.name === selectedCity1.value)?.flag || '')
const city2Flag = computed(() => cities.find(c => c.name === selectedCity2.value)?.flag || '')

const city1TimeDisplay = computed(() => city1Data.value ? `${getTimeInTimezone(city1Data.value.timezone)} (${city1Data.value.timezone})` : 'Select a city')
const city2TimeDisplay = computed(() => city2Data.value ? `${getTimeInTimezone(city2Data.value.timezone)} (${city2Data.value.timezone})` : 'Select a city')

const errorMessage = ref('')

watch([selectedCity1, selectedCity2], () => {
  errorMessage.value = ''

  if (selectedCity1.value && selectedCity2.value) {
    if (selectedCity1.value === selectedCity2.value) {
      errorMessage.value = 'Please select two different cities.'
      city1Data.value = null
      city2Data.value = null
      return
    }
    if (mockData[selectedCity1.value].country === mockData[selectedCity2.value].country) {
      errorMessage.value = 'Cannot compare two cities from the same country. Please select cities from different countries.'
      city1Data.value = null
      city2Data.value = null
      return
    }
  }

  city1Data.value = selectedCity1.value ? mockData[selectedCity1.value] : null
  city2Data.value = selectedCity2.value ? mockData[selectedCity2.value] : null
})

const resetSelection = () => {
  selectedCity1.value = ''
  selectedCity2.value = ''
  city1Data.value = null
  city2Data.value = null
  errorMessage.value = ''
}
</script>
