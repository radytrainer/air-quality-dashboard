<template>
  <div
    class="rounded-2xl p-6 relative overflow-hidden shadow-lg  bg-gray-100 backdrop-blur-sm  "
    :style="{
      background: 'linear-gradient(135deg, rgba(255,255,255,0.85), rgba(255,255,255,0.75))',
    }"
  >
    <!-- Background Pattern -->
    <div
      class="absolute inset-0 opacity-10"
      :style="{
        backgroundImage: 'url(https://via.placeholder.com/400x300?text=Pattern)',
        backgroundSize: 'cover',
        backgroundPosition: 'center'
      }"
    ></div>

    <!-- Header -->
    <div class="flex justify-between items-center relative z-10 mb-4">
      <div class="flex items-center gap-3">
        <img
          v-if="city.flag"
          :src="city.flag"
          alt="Flag"
          class="w-8 h-6 rounded shadow-sm object-cover"
        />
        <h3 class="text-2xl font-bold text-gray-900">{{ city.name }}</h3>
      </div>

      <span
        :class="[badgeColor, 'px-4 py-1.5 rounded-full text-white font-semibold shadow']"
      >
        {{ city.aqi }} AQI
      </span>
    </div>

    <!-- AQI Value -->
    <div class="text-center mb-6 relative z-10">
      <h2 :class="['text-6xl font-extrabold drop-shadow-sm', textColor]">
        {{ city.aqi }}
      </h2>
      <p class="text-lg font-medium text-gray-700">{{ computedLevel }}</p>

      <!-- Alerts -->
      <p
        v-if="city.aqi > 150"
        class="bg-red-100 text-red-700 p-3 rounded-md mt-3 font-semibold"
      >
        🚨 Unhealthy air quality! Take care.
      </p>
      <p
        v-else-if="city.aqi > 100"
        class="bg-orange-100 text-orange-700 p-3 rounded-md mt-3 font-semibold"
      >
        ⚠️ Unhealthy for sensitive groups.
      </p>
      <p
        v-else-if="city.aqi > 50"
        class="bg-yellow-100 text-yellow-700 p-3 rounded-md mt-3 font-semibold"
      >
        ⚠️ Moderate air quality.
      </p>
      <p
        v-else
        class="bg-green-100 text-green-700 p-3 rounded-md mt-3 font-semibold"
      >
        ✅ Good air quality.
      </p>
    </div>

    <!-- Pollutants -->
    <div class="grid grid-cols-2 gap-4 mb-6 relative z-10">
      <div class="bg-white/80 p-3 rounded-lg shadow-inner text-center">
        <span class="block text-gray-500 text-sm"> PM10</span>
        <span class="text-lg font-medium">{{ city.pm10 ?? 'N/A' }} µg/m³</span>
      </div>
      <div class="bg-white/80 p-3 rounded-lg shadow-inner text-center">
        <span class="block text-gray-500 text-sm">PM2.5</span>
        <span class="text-lg font-medium">{{ city.pm25 ?? 'N/A' }} µg/m³</span>
      </div>
    </div>

    <!-- Weather -->
    <div class="grid grid-cols-3 gap-3 relative z-10">
      <div class="bg-white/80 p-3 rounded-lg shadow-inner text-center">
        🌡️ <span class="font-medium">{{ city.temperature ?? 'N/A' }}°C</span>
      </div>
      <div class="bg-white/80 p-3 rounded-lg shadow-inner text-center">
        💧 <span class="font-medium">{{ city.humidity ?? 'N/A' }}%</span>
      </div>
      <div class="bg-white/80 p-3 rounded-lg shadow-inner text-center">
        🌬 <span class="font-medium">{{ city.wind_speed ?? 'N/A' }} km/h</span>
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

const badgeColor = computed(() => {
  const aqi = props.city.aqi
  if (aqi <= 50) return 'bg-green-500'
  if (aqi <= 100) return 'bg-yellow-500'
  if (aqi <= 150) return 'bg-orange-500'
  return 'bg-red-500'
})

const textColor = computed(() => {
  const aqi = props.city.aqi
  if (aqi <= 50) return 'text-green-600'
  if (aqi <= 100) return 'text-yellow-600'
  if (aqi <= 150) return 'text-orange-600'
  return 'text-red-600'
})

const computedLevel = computed(() => {
  if (props.city.level) return props.city.level
  const aqi = props.city.aqi
  if (aqi > 150) return 'Unhealthy'
  if (aqi > 100) return 'Unhealthy for Sensitive Groups'
  if (aqi > 50) return 'Moderate'
  return 'Good'
})
</script>
