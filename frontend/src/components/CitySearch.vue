<template>
  <div class="relative w-96">
    <!-- Search Input with Icons -->
    <input
      v-model="search"
      @input="searchLocations"
      type="text"
      :placeholder="t('search.placeholder')"
      class="bg-gray-100 text-gray-900 px-8 py-1.5 rounded-md w-full placeholder-gray-500 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-sm h-9"
    />
    <!-- Search Icon -->
    <i
      class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"
    ></i>
    <!-- Locate Icon -->
    <i
      class="fas fa-crosshairs absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer hover:text-blue-600 transition"
      :title="t('search.locateMe')"
      @click="locateUser"
    ></i>

    <!-- Autocomplete Dropdown with Enhanced Results -->
    <ul v-if="searchResults.length && search" class="absolute z-50 bg-white border border-gray-200 w-full mt-1 rounded-lg shadow-xl max-h-80 overflow-y-auto">
      <li v-if="loading" class="p-3 text-center text-gray-500 text-sm">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        Searching globally...
      </li>
      <li
        v-else
        v-for="location in searchResults"
        :key="location.full_name"
        @click="selectLocation(location)"
        class="flex items-center justify-between p-3 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition-all duration-200"
      >
        <div class="flex items-center space-x-3">
          <!-- Dynamic icons based on search type -->
          <i :class="getSearchIcon(location.type)" class="text-blue-500"></i>
          <div>
            <div class="font-medium text-gray-900 text-sm">
              {{ location.name }}
              <!-- Country badge for country matches -->
              <span v-if="location.type === 'country'" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">
                Country
              </span>
            </div>
            <div class="text-xs text-gray-500">{{ location.country }}</div>
          </div>
        </div>
        <!-- AQI Badge -->
        <div v-if="location.aqi" class="flex items-center space-x-2">
          <span class="text-xs text-gray-500">AQI:</span>
          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white"
                :style="{ backgroundColor: getAQIColor(location.aqi) }">
            {{ location.aqi }}
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

const { t } = useI18n()

// Component events
const emit = defineEmits(['location-selected'])

// Reactive state
const search = ref('')
const searchResults = ref([])
const loading = ref(false)
let searchTimeout = null

// AQI Color mapping for visual indicators
const getAQIColor = (aqi) => {
  const val = parseFloat(aqi)
  if (isNaN(val)) return '#999'
  if (val <= 50) return '#00e400'
  if (val <= 100) return '#FFEB3B'
  if (val <= 150) return '#ff7e00'
  if (val <= 200) return '#ff0000'
  if (val <= 300) return '#99004c'
  return '#7e0023'
}

// Dynamic icons based on search match type
const getSearchIcon = (type) => {
  switch (type) {
    case 'country':
    case 'country_partial':
      return 'fas fa-globe' // Globe for countries
    case 'city':
    case 'city_partial':
      return 'fas fa-city' // City icon for cities
    default:
      return 'fas fa-map-marker-alt' // Map marker for general
  }
}

// Search function with debouncing
function searchLocations() {
  clearTimeout(searchTimeout)
  
  if (search.value.length < 2) {
    searchResults.value = []
    return
  }

  loading.value = true
  
  // Debounce search by 300ms for better UX
  searchTimeout = setTimeout(async () => {
    try {
      const response = await axios.get(`http://127.0.0.1:8000/api/search-locations?q=${encodeURIComponent(search.value)}`)
      if (response.data.status === 'ok') {
        searchResults.value = response.data.data || []
      }
    } catch (error) {
      console.error('Search failed:', error)
      searchResults.value = []
    } finally {
      loading.value = false
    }
  }, 300)
}

// Handle location selection
function selectLocation(location) {
  search.value = location.name
  searchResults.value = []
  emit('location-selected', location)
}

// Geolocation functionality
function locateUser() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const location = {
          name: 'Your Location',
          full_name: 'Your Current Location',
          country: 'Current Position',
          lat: position.coords.latitude,
          lon: position.coords.longitude,
          aqi: null,
          type: 'geolocation'
        }
        selectLocation(location)
      },
      (error) => {
        alert(t('search.locatingError') || 'Unable to get your location')
      }
    )
  } else {
    alert(t('search.locatingNotSupported') || 'Geolocation is not supported by this browser')
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (e) => {
  if (!e.target.closest('.relative')) {
    searchResults.value = []
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  clearTimeout(searchTimeout)
})
</script>

<style scoped>
@import "@fortawesome/fontawesome-free/css/all.min.css";

/* Enhanced hover effects */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Smooth scrollbar for results */
::-webkit-scrollbar {
  width: 4px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}
</style>
