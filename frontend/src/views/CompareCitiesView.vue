<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <header class="flex flex-col items-center text-center mb-10">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
        <h1 class="text-4xl font-bold text-gray-900">
          Air Quality Comparison
        </h1>
        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
          Live
        </span>
      </div>
      <p class="text-gray-600 flex items-center gap-2">
        <LineChart class="w-4 h-4 text-blue-600" />
        Real-time air quality analysis across global cities
      </p>
    </header>

    <!-- Info Boxes with clean white cards -->
    <div class="grid md:grid-cols-3 gap-6 mb-10">
      <!-- Cities Tracked -->
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="text-gray-900 font-semibold text-lg mb-1">Cities Tracked</div>
        <div class="text-sm text-gray-500 mb-3">Actively monitored cities worldwide</div>
        <div class="text-blue-600 text-3xl font-bold">{{ totalCities }}</div>
      </div>

      <!-- Countries -->
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="text-gray-900 font-semibold text-lg mb-1">Countries</div>
        <div class="text-sm text-gray-500 mb-3">Nations with data coverage</div>
        <div class="text-blue-600 text-3xl font-bold">{{ totalCountries }}</div>
      </div>

      <!-- Active Timezones -->
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="text-gray-900 font-semibold text-lg mb-1">Active Timezones</div>
        <div class="text-sm text-gray-500 mb-3">Global monitoring coverage</div>
        <div class="text-blue-600 text-xl font-bold">
          {{ activeTimezonesDisplay }}
        </div>
      </div>
    </div>

    <!-- Compare Section -->
    <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
      <!-- Header -->
      <h2 class="text-2xl font-bold mb-8 text-gray-900 flex items-center gap-2">
        <Search class="w-6 h-6 text-blue-600" />
        Select Cities to Compare
      </h2>

      <!-- Two selectors side by side -->
      <div class="grid md:grid-cols-2 gap-8 mb-8">
        <CitySelectorForCompare
          :label="$t('comparePage.primaryCity')"
          :description="$t('comparePage.primaryCityDesc')"
          :cities="cities"
          v-model="selectedCity1"
          @city-changed="updateCity1Info"
        />

        <CitySelectorForCompare
          :label="$t('comparePage.secondaryCity')"
          :description="$t('comparePage.secondaryCityDesc')"
          :cities="cities"
          v-model="selectedCity2"
          @city-changed="updateCity2Info"
        />
      </div>

      <!-- Display the two city cards with VS in the middle -->
      <div v-if="city1Data && city2Data && !errorMessage"
           class="grid md:grid-cols-[1fr_auto_1fr] gap-6 items-center mb-8">

        <!-- Left City -->
        <CityCard 
          :city="city1Data"
          :comparison="city1Data.aqi < city2Data.aqi ? 'better' : 'worse'"
        />

        <!-- Simplified VS badge -->
        <div class="flex justify-center items-center">
          <div class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold text-sm">
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
      <p v-if="errorMessage" class="text-red-600 font-medium mb-6 text-center bg-red-50 p-4 rounded-lg border border-red-200">
        {{ errorMessage }}
      </p>

      <!-- Simplified alerts with cleaner styling -->
      <div v-if="city1Data && city1Data.aqi > 100" class="bg-red-50 p-4 mb-4 rounded-lg border border-red-200 flex items-center gap-2">
        <AlertTriangle class="w-5 h-5 text-red-600" />
        <p class="text-red-700 font-medium">
          <strong>{{ city1Data.name }}</strong> has poor air quality (AQI: <strong>{{ city1Data.aqi }}</strong>)
        </p>
      </div>

      <div v-if="city2Data && city2Data.aqi > 100" class="bg-red-50 p-4 mb-4 rounded-lg border border-red-200 flex items-center gap-2">
        <AlertTriangle class="w-5 h-5 text-red-600" />
        <p class="text-red-700 font-medium">
          <strong>{{ city2Data.name }}</strong> has poor air quality (AQI: <strong>{{ city2Data.aqi }}</strong>)
        </p>
      </div>

      <!-- Chart Comparison -->
      <div v-if="city1Data && city2Data && !errorMessage" class="mt-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
        <h3 class="text-xl font-semibold mb-4 text-center text-gray-900 flex items-center gap-2 justify-center">
          <BarChart2 class="w-5 h-5 text-blue-600" />
          Pollutant Level Comparison
        </h3>
        <CompareBarChart :city1="city1Data" :city2="city2Data" />
      </div>

      <!-- Simplified reset button -->
      <button @click="resetSelection"
        class="mt-8 mx-auto flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium px-6 py-2 rounded-lg transition-colors">
        <RotateCcw class="w-4 h-4" />
        Reset Comparison
      </button>

      <!-- Simplified AQI color guide -->
      <div class="bg-gray-50 rounded-lg p-6 mt-6 border border-gray-200">
        <h3 class="font-semibold text-center mb-4 text-gray-900 flex items-center gap-2 justify-center">
          <Wind class="w-5 h-5 text-gray-600" />
          AQI Color Guide
        </h3>
        <div class="flex justify-center gap-4 flex-wrap">
          <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm">
            <span class="w-4 h-4 bg-green-500 rounded"></span>
            <span class="text-sm font-medium text-gray-700">Good (0–50)</span>
          </div>
          <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm">
            <span class="w-4 h-4 bg-yellow-500 rounded"></span>
            <span class="text-sm font-medium text-gray-700">Moderate (51–100)</span>
          </div>
          <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm">
            <span class="w-4 h-4 bg-orange-500 rounded"></span>
            <span class="text-sm font-medium text-gray-700">Unhealthy (101–150)</span>
          </div>
          <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm">
            <span class="w-4 h-4 bg-red-500 rounded"></span>
            <span class="text-sm font-medium text-gray-700">Unhealthy (151+)</span>
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
import { LineChart, Search, AlertTriangle, BarChart2, RotateCcw, Wind } from "lucide-vue-next";

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
