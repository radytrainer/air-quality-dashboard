<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-2 md:p-4">
    <div class="max-w-7xl mx-auto">
      <!-- Header with enhanced styling -->
      <header class="text-center mb-4 md:mb-6">
        <div class="inline-flex flex-col items-center">
          <div class="flex items-center gap-2 md:gap-3 mb-2 md:mb-3">
            <div class="relative">
              <div class="w-2 h-2 md:w-3 md:h-3 bg-green-500 rounded-full animate-pulse"></div>
              <div class="absolute inset-0 w-2 h-2 md:w-3 md:h-3 bg-green-400 rounded-full animate-ping opacity-75"></div>
            </div>
            <h1 class="text-xl md:text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              {{ $t('comparePage.header') }}
            </h1>
            <span class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-2 py-1 rounded-full text-xs font-semibold shadow-md">
              {{ $t('comparePage.liveBadge') }}
            </span>
          </div>
          <p class="text-slate-600 flex items-center gap-1 text-xs md:text-sm">
            <LineChart class="w-3 h-3 md:w-4 md:h-4 text-blue-600 animate-float" />
            {{ $t('comparePage.realTimeInfo') }}
          </p>
        </div>
      </header>
      
      <!-- Enhanced Info Cards - More Compact -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-3 mb-4 md:mb-6">
        <!-- Cities Tracked -->
        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow border border-white/20 p-3 md:p-4 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center gap-1 mb-1">
                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $t('comparePage.citiesTracked') }}</p>
              </div>
              <p class="text-lg md:text-xl font-bold text-slate-900 mb-1">{{ totalCities }}</p>
              <p class="text-xs text-slate-500">{{ $t('comparePage.citiesTrackedDesc') }}</p>
            </div>
            <div class="bg-blue-100 p-1.5 rounded-lg animate-pulse-slow">
              <MapPin class="w-4 h-4 md:w-5 md:h-5 text-blue-600" />
            </div>
          </div>
        </div>
        
        <!-- Countries -->
        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow border border-white/20 p-3 md:p-4 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center gap-1 mb-1">
                <div class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $t('comparePage.countries') }}</p>
              </div>
              <p class="text-lg md:text-xl font-bold text-slate-900 mb-1">{{ totalCountries }}</p>
              <p class="text-xs text-slate-500">{{ $t('comparePage.countriesDesc') }}</p>
            </div>
            <div class="bg-purple-100 p-1.5 rounded-lg animate-pulse-slow">
              <Globe class="w-4 h-4 md:w-5 md:h-5 text-purple-600 animate-rotate-slow" />
            </div>
          </div>
        </div>
        
        <!-- Active Timezones -->
        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow border border-white/20 p-3 md:p-4 hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center gap-1 mb-1">
                <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $t('comparePage.activeTimezones') }}</p>
              </div>
              <p class="text-base md:text-lg font-bold text-slate-900 mb-1">{{ activeTimezonesDisplay }}</p>
              <p class="text-xs text-slate-500">{{ $t('comparePage.activeTimezonesDesc') }}</p>
            </div>
            <div class="bg-amber-100 p-1.5 rounded-lg animate-pulse-slow">
              <Clock class="w-4 h-4 md:w-5 md:h-5 text-amber-600 animate-tick" />
            </div>
          </div>
        </div>
      </div>
      
      <!-- Main Comparison Section - More Compact -->
      <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow border border-white/20 p-4 md:p-5 mb-4 md:mb-6">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 md:mb-5">
          <h2 class="text-base md:text-lg font-bold text-slate-900 flex items-center gap-1 md:gap-2 mb-2 md:mb-0">
            <Search class="w-4 h-4 md:w-5 md:h-5 text-blue-600 animate-pulse-slow" />
            {{ $t('comparePage.selectCities') }}
          </h2>
          <button 
            @click="resetSelection"
            class="flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-3 py-1.5 rounded-lg transition-colors duration-200 text-xs md:text-sm">
            <RotateCcw class="w-3 h-3" />
            {{ $t('comparePage.resetButton') }}
          </button>
        </div>
        
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-6">
          <div class="animate-spin rounded-full h-6 w-6 md:h-8 md:w-8 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        
        <!-- City Selectors -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 mb-5 md:mb-6">
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
        
        <!-- City Comparison Display -->
        <div v-if="city1Data && city2Data && !errorMessage" class="mb-5 md:mb-6">
          <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4 items-center mb-5 md:mb-6">
            <!-- Left City Card -->
            <CityCard 
              :city="city1Data"
              :comparison="city1Data.aqi < city2Data.aqi ? 'better' : 'worse'"
            />
            
            <!-- VS Badge with Animation -->
            <div class="flex justify-center items-center">
              <div class="relative">
                <div class="absolute inset-0 bg-blue-500 rounded-full animate-ping opacity-20"></div>
                <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-3 py-1.5 rounded-full font-bold text-sm md:text-base shadow-lg">
                  {{ $t('comparePage.vs') }}
                </div>
              </div>
            </div>
            
            <!-- Right City Card -->
            <CityCard 
              :city="city2Data"
              :comparison="city2Data.aqi < city1Data.aqi ? 'better' : 'worse'"
            />
          </div>
          
          <!-- Enhanced Alerts - More Compact -->
          <div v-if="city1Data && city1Data.aqi > 100" class="bg-red-50 p-2.5 mb-2.5 rounded-md border border-red-200 flex items-start gap-2 shadow-sm">
            <div class="bg-red-100 p-1.5 rounded-lg flex-shrink-0">
              <AlertTriangle class="w-4 h-4 text-red-600 animate-bounce" />
            </div>
            <div>
              <p class="text-red-700 font-medium text-xs">
                <strong>{{ city1Data.name }}</strong> {{ $t('comparePage.poorAirQuality') }} (AQI: <strong>{{ city1Data.aqi }}</strong>)
              </p>
              <p class="text-red-600 text-xs mt-0.5">{{ $t('comparePage.healthWarning') }}</p>
            </div>
          </div>
          
          <div v-if="city2Data && city2Data.aqi > 100" class="bg-red-50 p-2.5 mb-2.5 rounded-md border border-red-200 flex items-start gap-2 shadow-sm">
            <div class="bg-red-100 p-1.5 rounded-lg flex-shrink-0">
              <AlertTriangle class="w-4 h-4 text-red-600 animate-bounce" />
            </div>
            <div>
              <p class="text-red-700 font-medium text-xs">
                <strong>{{ city2Data.name }}</strong> {{ $t('comparePage.poorAirQuality') }} (AQI: <strong>{{ city2Data.aqi }}</strong>)
              </p>
              <p class="text-red-600 text-xs mt-0.5">{{ $t('comparePage.healthWarning') }}</p>
            </div>
          </div>
          
          <!-- Chart Comparison Section - More Compact -->
          <div class="bg-gradient-to-br from-slate-50 to-slate-100 p-3 rounded-md border border-slate-200 shadow-inner">
            <h3 class="text-sm md:text-base font-bold mb-2 md:mb-3 text-center text-slate-900 flex items-center gap-1 justify-center">
              <BarChart2 class="w-4 h-4 md:w-5 md:h-5 text-blue-600 animate-pulse-slow" />
              {{ $t('comparePage.pollutantComparison') }}
            </h3>
            <div class="bg-white p-2.5 rounded-md shadow-sm">
              <CompareBarChart :city1="city1Data" :city2="city2Data" />
            </div>
          </div>
        </div>
        
        <!-- Error Message - More Compact -->
        <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-2.5 mb-3 rounded-r-md shadow-sm">
          <div class="flex items-center gap-2">
            <AlertCircle class="w-4 h-4 md:w-5 md:h-5 text-red-500 animate-pulse" />
            <p class="text-red-700 font-medium text-xs md:text-sm">{{ errorMessage }}</p>
          </div>
        </div>
        
        <!-- Enhanced AQI Guide - More Compact -->
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-md p-3 md:p-4 border border-slate-200 shadow-inner">
          <h3 class="font-bold text-sm md:text-base mb-2 md:mb-3 text-center text-slate-900 flex items-center gap-1 justify-center">
            <Wind class="w-4 h-4 md:w-5 md:h-5 text-slate-600 animate-pulse-slow" />
            {{ $t('comparePage.aqiGuide') }}
          </h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-1.5 md:gap-2">
            <div class="flex flex-col items-center bg-white p-2 rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
              <div class="w-6 h-6 bg-green-500 rounded-full mb-1 flex items-center justify-center">
                <Check class="w-3 h-3 text-white" />
              </div>
              <span class="text-xs font-semibold text-gray-700">{{ $t('comparePage.good') }}</span>
              <span class="text-xs text-gray-500 mt-0.5">0-50</span>
            </div>
            <div class="flex flex-col items-center bg-white p-2 rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
              <div class="w-6 h-6 bg-yellow-500 rounded-full mb-1 flex items-center justify-center">
                <Minus class="w-3 h-3 text-white" />
              </div>
              <span class="text-xs font-semibold text-gray-700">{{ $t('comparePage.moderate') }}</span>
              <span class="text-xs text-gray-500 mt-0.5">51-100</span>
            </div>
            <div class="flex flex-col items-center bg-white p-2 rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
              <div class="w-6 h-6 bg-orange-500 rounded-full mb-1 flex items-center justify-center">
                <AlertTriangle class="w-3 h-3 text-white" />
              </div>
              <span class="text-xs font-semibold text-gray-700">{{ $t('comparePage.unhealthySensitive') }}</span>
              <span class="text-xs text-gray-500 mt-0.5">101-150</span>
            </div>
            <div class="flex flex-col items-center bg-white p-2 rounded-md shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
              <div class="w-6 h-6 bg-red-500 rounded-full mb-1 flex items-center justify-center">
                <X class="w-3 h-3 text-white" />
              </div>
              <span class="text-xs font-semibold text-gray-700">{{ $t('comparePage.Unhealthy') }}</span>
              <span class="text-xs text-gray-500 mt-0.5">151+</span>
            </div>
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
import { 
  LineChart, 
  Search, 
  AlertTriangle, 
  BarChart2, 
  RotateCcw, 
  Wind,
  MapPin,
  Globe,
  Clock,
  AlertCircle,
  Check,
  Minus,
  X
} from "lucide-vue-next"

const cities = ref([])
const selectedCity1 = ref(null)
const selectedCity2 = ref(null)
const city1Data = ref(null)
const city2Data = ref(null)
const errorMessage = ref('')
const loading = ref(true)
const userTimezone = ref('')
const userTime = ref('')

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
  
  // Update time every second
  const updateTime = () => {
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
  }
  
  updateTime()
  setInterval(updateTime, 1000)
  
  // Fetch cities data
  fetchCities()
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
  loading.value = true
  errorMessage.value = ''
  
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
    console.log('Total cities:', cities.value.length)
  } catch (error) {
    console.error('Failed to fetch cities:', error)
    errorMessage.value = 'Unable to load cities data. Please try again later.'
  } finally {
    loading.value = false
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

// Auto-update card data when selection changes
watch(selectedCity1, updateCity1Info)
watch(selectedCity2, updateCity2Info)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
* {
  font-family: 'Inter', sans-serif;
}

/* Enhanced transitions and animations */
select {
  background-color: white;
}

/* Custom animations */
@keyframes pulse-slow {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}
.animate-pulse-slow {
  animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-3px); }
}
.animate-float {
  animation: float 3s ease-in-out infinite;
}

@keyframes rotate-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-rotate-slow {
  animation: rotate-slow 15s linear infinite;
}

@keyframes tick {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}
.animate-tick {
  animation: tick 1s ease-in-out infinite;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}
.animate-bounce {
  animation: bounce 1s ease-in-out infinite;
}
</style>