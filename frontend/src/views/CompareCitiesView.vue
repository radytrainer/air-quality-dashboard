<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Header with enhanced styling -->
      <header class="text-center mb-6">
        <div class="inline-flex flex-col items-center">
          <div class="flex items-center gap-2 mb-2">
            <div class="relative">
              <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              <div class="absolute inset-0 w-2 h-2 bg-green-400 rounded-full animate-ping opacity-75"></div>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              {{ $t('comparePage.header') }}
            </h1>
            <span class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-2 py-1 rounded-full text-sm font-semibold shadow-md">
              {{ $t('comparePage.liveBadge') }}
            </span>
          </div>
          <p class="text-slate-600 flex items-center gap-1 text-sm">
            <LineChart class="w-4 h-4 text-blue-600 animate-float" />
            {{ $t('comparePage.realTimeInfo') }}
          </p>
        </div>
      </header>
      
      <!-- Top 4 Metric Boxes - Following the style of the provided template -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Cities Tracked -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $t('comparePage.citiesTracked') }}</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ totalCities }}</p>
              <p class="text-sm text-blue-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  {{ $t('comparePage.citiesTrackedDesc') }}
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <MapPin class="w-5 h-5 text-white" />
              </div>
              <button @click="exportData('cities')" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Countries -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $t('comparePage.countries') }}</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ totalCountries }}</p>
              <p class="text-sm text-purple-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  {{ $t('comparePage.countriesDesc') }}
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <Globe class="w-5 h-5 text-white animate-rotate-slow" />
              </div>
              <button @click="exportData('countries')" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Active Timezones -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $t('comparePage.activeTimezones') }}</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ activeTimezones }}</p>
              <p class="text-sm text-amber-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  {{ userTimezoneDisplay }}
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg flex items-center justify-center">
                <Clock class="w-5 h-5 text-white animate-tick" />
              </div>
              <button @click="exportData('timezones')" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Average AQI -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Average AQI</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ averageAQI }}</p>
              <p class="text-sm text-indigo-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  Global average
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <BarChart2 class="w-5 h-5 text-white" />
              </div>
              <button @click="exportData('aqi')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Main Comparison Section -->
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2 mb-2 md:mb-0">
            <Search class="w-5 h-5 text-blue-600 animate-pulse-slow" />
            {{ $t('comparePage.selectCities') }}
          </h2>
          <button 
            @click="resetSelection"
            class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-4 py-2 rounded-lg transition-colors duration-200 text-sm">
            <RotateCcw class="w-4 h-4" />
            {{ $t('comparePage.resetButton') }}
          </button>
        </div>
        
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        
        <!-- City Selectors -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
        <div v-if="city1Data && city2Data && !errorMessage" class="mb-8">
          <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-6 items-center mb-8">
            <!-- Left City Card -->
            <CityCard 
              :city="city1Data"
              :comparison="city1Data.aqi < city2Data.aqi ? 'better' : 'worse'"
            />
            
            <!-- VS Badge with Animation -->
            <div class="flex justify-center items-center">
              <div class="relative">
                <div class="absolute inset-0 bg-blue-500 rounded-full animate-ping opacity-20"></div>
                <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 py-2 rounded-full font-bold text-lg shadow-lg">
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
          
          <!-- Enhanced Alerts -->
          <div v-if="city1Data && city1Data.aqi > 100" class="bg-red-50 p-4 mb-4 rounded-xl border border-red-200 flex items-start gap-3 shadow-sm">
            <div class="bg-red-100 p-2 rounded-lg flex-shrink-0">
              <AlertTriangle class="w-5 h-5 text-red-600 animate-bounce" />
            </div>
            <div>
              <p class="text-red-700 font-medium text-sm">
                <strong>{{ city1Data.name }}</strong> {{ $t('comparePage.poorAirQuality') }} (AQI: <strong>{{ city1Data.aqi }}</strong>)
              </p>
              <p class="text-red-600 text-sm mt-1">{{ $t('comparePage.healthWarning') }}</p>
            </div>
          </div>
          
          <div v-if="city2Data && city2Data.aqi > 100" class="bg-red-50 p-4 mb-4 rounded-xl border border-red-200 flex items-start gap-3 shadow-sm">
            <div class="bg-red-100 p-2 rounded-lg flex-shrink-0">
              <AlertTriangle class="w-5 h-5 text-red-600 animate-bounce" />
            </div>
            <div>
              <p class="text-red-700 font-medium text-sm">
                <strong>{{ city2Data.name }}</strong> {{ $t('comparePage.poorAirQuality') }} (AQI: <strong>{{ city2Data.aqi }}</strong>)
              </p>
              <p class="text-red-600 text-sm mt-1">{{ $t('comparePage.healthWarning') }}</p>
            </div>
          </div>
          
          <!-- Chart Comparison Section -->
          <div class="bg-gradient-to-br from-slate-50 to-slate-100 p-6 rounded-xl border border-slate-200 shadow-inner">
            <h3 class="text-lg font-bold mb-4 text-center text-slate-900 flex items-center gap-2 justify-center">
              <BarChart2 class="w-5 h-5 text-blue-600 animate-pulse-slow" />
              {{ $t('comparePage.pollutantComparison') }}
            </h3>
            <div class="bg-white p-4 rounded-xl shadow-sm">
              <CompareBarChart :city1="city1Data" :city2="city2Data" />
            </div>
          </div>
        </div>
        
        <!-- Error Message -->
        <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-lg shadow-sm">
          <div class="flex items-center gap-2">
            <AlertCircle class="w-5 h-5 text-red-500 animate-pulse" />
            <p class="text-red-700 font-medium text-sm">{{ errorMessage }}</p>
          </div>
        </div>
        
        <!-- Enhanced AQI Guide -->
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-6 border border-slate-200 shadow-inner">
          <h3 class="font-bold text-lg mb-4 text-center text-slate-900 flex items-center gap-2 justify-center">
            <Wind class="w-5 h-5 text-slate-600 animate-pulse-slow" />
            {{ $t('comparePage.aqiGuide') }}
          </h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex flex-col items-center bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
              <div class="w-8 h-8 bg-green-500 rounded-full mb-2 flex items-center justify-center">
                <Check class="w-4 h-4 text-white" />
              </div>
              <span class="text-sm font-semibold text-gray-700">{{ $t('comparePage.good') }}</span>
              <span class="text-sm text-gray-500 mt-1">0-50</span>
            </div>
            <div class="flex flex-col items-center bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
              <div class="w-8 h-8 bg-yellow-500 rounded-full mb-2 flex items-center justify-center">
                <Minus class="w-4 h-4 text-white" />
              </div>
              <span class="text-sm font-semibold text-gray-700">{{ $t('comparePage.moderate') }}</span>
              <span class="text-sm text-gray-500 mt-1">51-100</span>
            </div>
            <div class="flex flex-col items-center bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
              <div class="w-8 h-8 bg-orange-500 rounded-full mb-2 flex items-center justify-center">
                <AlertTriangle class="w-4 h-4 text-white" />
              </div>
              <span class="text-sm font-semibold text-gray-700">{{ $t('comparePage.unhealthySensitive') }}</span>
              <span class="text-sm text-gray-500 mt-1">101-150</span>
            </div>
            <div class="flex flex-col items-center bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
              <div class="w-8 h-8 bg-red-500 rounded-full mb-2 flex items-center justify-center">
                <X class="w-4 h-4 text-white" />
              </div>
              <span class="text-sm font-semibold text-gray-700">{{ $t('comparePage.Unhealthy') }}</span>
              <span class="text-sm text-gray-500 mt-1">151+</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, onUnmounted } from 'vue'
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

// Reactive state
const cities = ref([])
const selectedCity1 = ref(null)
const selectedCity2 = ref(null)
const city1Data = ref(null)
const city2Data = ref(null)
const errorMessage = ref('')
const loading = ref(true)
const userTimezone = ref('')
const userTime = ref('')

// Computed properties
const totalCities = computed(() => cities.value.length)
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
const activeTimezones = computed(() => {
  const tzs = cities.value
    .map(city => city.timezone)
    .filter(Boolean)
  if (userTimezone.value && !tzs.includes(userTimezone.value)) {
    tzs.push(userTimezone.value)
  }
  return new Set(tzs).size
})

// User timezone display in small format
const userTimezoneDisplay = computed(() => {
  if (userTimezone.value && userTime.value) {
    // Format to show only the city part of the timezone and time
    const timezoneParts = userTimezone.value.split('/')
    const timezoneCity = timezoneParts.length > 1 ? timezoneParts[1].replace(/_/g, ' ') : userTimezone.value
    return `${timezoneCity} - ${userTime.value}`
  }
  return ''
})

// Average AQI computed property
const averageAQI = computed(() => {
  if (!cities.value.length) return 'N/A'
  const validData = cities.value.filter(item => parseInt(item.aqi) > 0)
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + (parseInt(item.aqi) || 0), 0)
  return Math.round(sum / validData.length)
})

// Initialize component
onMounted(() => {
  // Detect user timezone
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
  const timeInterval = setInterval(updateTime, 1000)
  
  // Fetch cities data
  fetchCities()
  
  // Cleanup interval on unmount
  onUnmounted(() => clearInterval(timeInterval))
})

// Fetch cities including Phnom Penh
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
  } catch (error) {
    console.error('Failed to fetch cities:', error)
    errorMessage.value = 'Unable to load cities data. Please try again later.'
  } finally {
    loading.value = false
  }
}

// Update city card data
function updateCity1Info(city) {
  city1Data.value = city || null
}
function updateCity2Info(city) {
  city2Data.value = city || null
}

// Reset selections
function resetSelection() {
  selectedCity1.value = null
  selectedCity2.value = null
  city1Data.value = null
  city2Data.value = null
  errorMessage.value = ''
}

// Export data function
function exportData(type) {
  let data = []
  let filename = ''
  let headers = []
  
  const escapeCsvValue = (value) => {
    if (value == null) return ''
    const str = String(value)
    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str
  }
  
  switch(type) {
    case 'cities':
      headers = ['name', 'aqi', 'timezone', 'flag', 'countryCode']
      data = cities.value.map(item => 
        headers.map(header => escapeCsvValue(item[header])).join(',')
      )
      data.unshift(headers.join(','))
      filename = 'cities-tracked.csv'
      break
    case 'countries':
      const countries = [...new Set(cities.value.map(city => {
        if (city.name) {
          const parts = city.name.split(',')
          return parts[parts.length - 1].trim()
        }
        return null
      }).filter(Boolean))]
      data = countries.map(country => escapeCsvValue(country))
      data.unshift('Country')
      filename = 'countries-tracked.csv'
      break
    case 'timezones':
      const timezones = [...new Set(cities.value
        .map(city => city.timezone)
        .filter(Boolean))]
      if (userTimezone.value && !timezones.includes(userTimezone.value)) {
        timezones.push(userTimezone.value)
      }
      data = timezones.map(tz => escapeCsvValue(tz))
      data.unshift('Timezone')
      filename = 'active-timezones.csv'
      break
    case 'aqi':
      data = [
        `metric,value,timestamp`,
        `average_aqi,${escapeCsvValue(averageAQI.value)},${escapeCsvValue(new Date().toISOString())}`
      ]
      filename = 'average-aqi.csv'
      break
  }
  
  const blob = new Blob([data.join('\n')], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = filename
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}

// Watch for selection changes
watch(selectedCity1, (newCity) => {
  updateCity1Info(newCity)
})
watch(selectedCity2, (newCity) => {
  updateCity2Info(newCity)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

</style>