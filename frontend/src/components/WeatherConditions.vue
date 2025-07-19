<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900">Environmental Conditions</h3>
      <p class="text-sm text-gray-600">Current weather and atmospheric data</p>
    </div>
    <div class="p-6 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-gray-900">{{ weather?.temperature || 0 }}°C</div>
          <div class="text-xs text-gray-500">Temperature</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-gray-900">{{ weather?.humidity || 0 }}%</div>
          <div class="text-xs text-gray-500">Humidity</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-gray-900">{{ weather?.windSpeed || 0 }}</div>
          <div class="text-xs text-gray-500">Wind (km/h)</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-gray-900">{{ weather?.pressure || 0 }}</div>
          <div class="text-xs text-gray-500">Pressure (hPa)</div>
        </div>
      </div>
      <div class="border-t border-gray-200 pt-4">
        <div class="text-sm text-gray-700">
          <div class="flex justify-between mb-2">
            <span>Condition:</span>
            <span class="font-medium">{{ weather?.condition || 'Clear' }}</span>
          </div>
          <div class="flex justify-between mb-2">
            <span>Visibility:</span>
            <span class="font-medium">{{ weather?.visibility || 10 }} km</span>
          </div>
          <div class="flex justify-between">
            <span>Local Time:</span>
            <span class="font-medium">{{ currentTime || '--:--' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  weather: {
    type: Object,
    default: () => ({
      temperature: 0,
      humidity: 0,
      windSpeed: 0,
      pressure: 0,
      condition: 'Clear',
      visibility: 10
    })
  },
  currentTime: {
    type: String,
    default: '--:--'
  }
})
</script>
