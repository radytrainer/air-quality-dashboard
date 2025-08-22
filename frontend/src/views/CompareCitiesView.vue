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
      <div class="text-sm text-gray-500">
        Monitoring is currently active in these global timezones.
      </div>
      <div class="mt-2 text-green-600 text-xl font-bold">
        {{ activeTimezonesDisplay }}
      </div>
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

      <!-- Two selectors side by side -->
      <div class="grid md:grid-cols-2 gap-10 mb-10">
        <CitySelectorForCompare
          label="Primary City"
          description="Choose your first comparison point"
          :cities="cities"
          v-model="selectedCity1"
          @city-changed="updateCity1Info"
        />

        <CitySelectorForCompare
          label="Secondary City"
          description="Choose your second comparison point"
          :cities="cities"
          v-model="selectedCity2"
          @city-changed="updateCity2Info"
        />
      </div>

      <!-- Display the two city cards with VS in the middle -->
<div v-if="city1Data && city2Data && !errorMessage" 
     class="grid md:grid-cols-[1fr_auto_1fr] gap-6 items-center">

  <!-- Left City -->
  <CityCard 
    :city="city1Data" 
    :comparison="city1Data.aqi < city2Data.aqi ? 'better' : 'worse'" 
  />

  <!-- VS Badge -->
  <div class="flex justify-center items-center">
    <div class="bg-purple-100 px-6 py-2 rounded-full font-bold text-purple-800 shadow-sm tracking-wide text-sm">
      VS
    </div>
  </div>

  <!-- Right City -->
  <CityCard 
    :city="city2Data" 
    :comparison="city2Data.aqi < city1Data.aqi ? 'better' : 'worse'" 
  />
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
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import CitySelectorForCompare from '@/components/CitySelectorForCompare.vue'
import CityCard from '@/components/CityCard.vue'
import CompareBarChart from '@/components/CompareBarChart.vue'

const cities = ref([])
const selectedCity1 = ref(null)
const selectedCity2 = ref(null)
const city1Data = ref(null)
const city2Data = ref(null)
const errorMessage = ref('')

const userTimezone = ref('') // User's timezone
const userTime = ref('')     // Current time in user's timezone

// ------------------
// Detect user's timezone & update current time
// ------------------
onMounted(() => {
  try {
    userTimezone.value = Intl.DateTimeFormat().resolvedOptions().timeZone
  } catch (err) {
    console.error('Cannot detect timezone:', err)
    userTimezone.value = 'Unknown'
  }

  setInterval(() => {
    if (userTimezone.value && userTimezone.value !== 'Unknown') {
      const now = new Date()
      userTime.value = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: userTimezone.value
      })
    }
  }, 1000)
})

// ------------------
// Computed: total cities count
// ------------------
const totalCities = computed(() => cities.value.length)

// ------------------
// Computed: total countries count
// ------------------
const totalCountries = computed(() => {
  const countries = cities.value
    .map(city => {
      if (city.name) {
        const parts = city.name.split(',')
        return parts[parts.length - 1].trim()
      }
      return null
    })
    .filter(Boolean)
  return new Set(countries).size
})

// ------------------
// Computed: active timezones including user's timezone
// ------------------
const activeTimezones = computed(() => {
  const tzs = cities.value
    .map(city => city.timezone)
    .filter(Boolean)
  
  if (userTimezone.value && !tzs.includes(userTimezone.value)) {
    tzs.push(userTimezone.value)
  }

  return new Set(tzs).size
})

const activeTimezonesDisplay = computed(() => {
  return userTimezone.value && userTime.value
    ? `${activeTimezones.value} (You: ${userTimezone.value} - ${userTime.value})`
    : activeTimezones.value > 0
      ? activeTimezones.value
      : 'N/A'
})

// ------------------
// Fetch cities including Phnom Penh
// ------------------
async function fetchCities() {
  try {
    const [globalRes, phnomRes] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/aqi'),
      axios.get('http://127.0.0.1:8000/api/air-quality/phnom-penh')
    ])

    let allCities = []
    if (globalRes.data?.status === 'ok') allCities = [...globalRes.data.data]

    if (phnomRes.data?.status === 'ok') {
      const phnom = phnomRes.data.data

      // Normalize Phnom Penh structure to match global cities
      const phnomNormalized = {
        name: phnom.name || 'Phnom Penh, Cambodia',
        aqi: phnom.aqi || null,
        timezone: phnom.timezone || 'Asia/Phnom_Penh',
        flag: phnom.flag || 'https://flagcdn.com/kh.svg',
        countryCode: 'KH'
      }

      // Avoid duplicate if already exists
      const exists = allCities.some(city => city.name === phnomNormalized.name)
      if (!exists) allCities.push(phnomNormalized)
    }

    cities.value = allCities

    console.log('Total cities:', cities.value.length) // should be 600
  } catch (error) {
    console.error('Failed to fetch cities:', error)
    errorMessage.value = 'Unable to load cities data.'
  }
}

// ------------------
// Update city card data
// ------------------
function updateCity1Info(city) {
  city1Data.value = city || null
}

function updateCity2Info(city) {
  city2Data.value = city || null
}

// ------------------
// Reset selections
// ------------------
function resetSelection() {
  selectedCity1.value = null
  selectedCity2.value = null
  city1Data.value = null
  city2Data.value = null
  errorMessage.value = ''
}

onMounted(fetchCities)

// Auto-update card data when selection changes
watch(selectedCity1, updateCity1Info)
watch(selectedCity2, updateCity2Info)
</script>


<style scoped>
select {
  background-color: white;
}
</style>