<template>
  <div
    :class="['rounded-2xl p-6', city.color || 'bg-gray-100', 'relative overflow-hidden']"
    :style="{
      backgroundImage: 'url(https://via.placeholder.com/400x300?text=Background+Pattern)',
      backgroundSize: 'cover',
      backgroundPosition: 'center'
    }"
  >
    <!-- City Name and AQI Badge -->
    <div class="flex justify-between items-start mb-4">
      <h3 class="text-2xl font-bold text-gray-900">{{ city.name }}</h3>
      <span class="bg-red-500 text-white px-4 py-2 rounded-full text-lg font-semibold">
        {{ city.aqi }} AQI
      </span>
    </div>

    <!-- AQI Value and Level -->
    <div class="text-center mb-6">
      <h2 class="text-6xl font-extrabold text-red-600">{{ city.aqi }}</h2>
      <p class="text-lg font-medium text-gray-700">{{ computedLevel }}</p>

      <!-- AQI Alert -->
      <div v-if="city.aqi > 150" class="bg-red-100 text-red-700 p-3 rounded-md mt-3 font-semibold">
        🚨 Unhealthy air quality! Take care.
      </div>
      <div v-else-if="city.aqi > 100" class="bg-orange-100 text-orange-700 p-3 rounded-md mt-3 font-semibold">
        ⚠️ Unhealthy for sensitive groups.
      </div>
      <div v-else-if="city.aqi > 50" class="bg-yellow-100 text-yellow-700 p-3 rounded-md mt-3 font-semibold">
        ⚠️ Moderate air quality.
      </div>
      <div v-else class="bg-green-100 text-green-700 p-3 rounded-md mt-3 font-semibold">
        ✅ Good air quality.
      </div>
    </div>

    <!-- Pollutant and Weather Details -->
    <div class="grid grid-cols-2 gap-4 mb-6">
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-2">
        <span class="text-gray-600">PM10</span>
        <span class="font-medium">{{ city.pm10 ?? 'N/A' }} µg/m³</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-2">
        <span class="text-gray-600">PM2.5</span>
        <span class="font-medium">{{ city.pm25 ?? 'N/A' }} µg/m³</span>
      </div>
    </div>

    <div class="grid grid-cols-4 gap-2">
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-blue-500">🌡️</span>
        <span class="text-gray-600">{{ city.temperature ?? 'N/A' }}°C</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-blue-400">💧</span>
        <span class="text-gray-600">{{ city.humidity ?? 'N/A' }}%</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-yellow-500">🌬</span>
        <span class="text-gray-600">{{ city.wind_speed ?? 'N/A' }} km/h</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-yellow-500">☀️</span>
        <span class="text-gray-600">UV {{ city.uv ?? 'N/A' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  city: {
    type: Object,
    required: true,
  },
})

// Compute AQI level text if not directly provided
const computedLevel = computed(() => {
  if ('level' in props.city && props.city.level) {
    return props.city.level
  }

  const aqi = props.city.aqi
  if (aqi > 150) return 'Unhealthy'
  if (aqi > 100) return 'Unhealthy for Sensitive Groups'
  if (aqi > 50) return 'Moderate'
  return 'Good'
})
</script>

<style scoped>
body {
  background: black;
}
</style>
