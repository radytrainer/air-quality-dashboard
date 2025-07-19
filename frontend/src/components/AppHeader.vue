<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-4">
        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Global Air Quality Monitoring System</h1>
          <p class="text-sm text-gray-600">Worldwide Environmental Data Dashboard</p>
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <div class="text-right">
          <div class="text-sm text-gray-500">Last Updated</div>
          <div class="text-sm font-medium text-gray-900">{{ lastUpdated }}</div>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-2 h-2 bg-green-500 rounded-full"></div>
          <span class="text-sm text-gray-600">Online</span>
        </div>
        <div class="text-right">
          <div class="text-3xl font-bold text-gray-900">{{ aqi }}</div>
          <div class="text-sm text-gray-500">Air Quality Index</div>
          <div class="inline-flex items-center px-2 py-1 rounded text-xs font-medium mt-1" :class="getAQIColor(aqi)">
            {{ getAQIStatus(aqi) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  lastUpdated: String,
  aqi: Number
})

const getAQIColor = (aqi) => {
  if (aqi <= 50) return 'bg-green-100 text-green-800'
  if (aqi <= 100) return 'bg-yellow-100 text-yellow-800'
  if (aqi <= 150) return 'bg-orange-100 text-orange-800'
  if (aqi <= 200) return 'bg-red-100 text-red-800'
  if (aqi <= 300) return 'bg-purple-100 text-purple-800'
  return 'bg-rose-900 text-white' // for AQI > 300 (Hazardous)
}

const getAQIStatus = (aqi) => {
  if (aqi <= 50) return 'Good'
  if (aqi <= 100) return 'Moderate'
  if (aqi <= 150) return 'Unhealthy for Sensitive'
  if (aqi <= 200) return 'Unhealthy'
  if (aqi <= 300) return 'Very Unhealthy'
  return 'Hazardous' // for AQI > 300
}

</script>
