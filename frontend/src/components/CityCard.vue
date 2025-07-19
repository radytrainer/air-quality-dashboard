<template>
  <div :class="['rounded-2xl p-6', city.color, 'relative overflow-hidden']" :style="{ backgroundImage: 'ur[](https://via.placeholder.com/400x300?text=Background+Pattern)', backgroundSize: 'cover', backgroundPosition: 'center' }">
    <!-- City Name and AQI Badge -->
    <div class="flex justify-between items-start mb-4">
      <h3 class="text-2xl font-bold text-gray-900">{{ city.name }}</h3>
      <span class="bg-red-500 text-white px-4 py-2 rounded-full text-lg font-semibold"> {{ city.aqi }} AQI</span>
    </div>

    <!-- AQI Value and Level -->
    <div class="text-center mb-6">
      <h2 class="text-6xl font-extrabold text-red-600">{{ city.aqi }}</h2>
      <p class="text-lg font-medium text-gray-700"> {{ city.level }}</p>
    </div>

    <!-- Pollutant and Weather Details -->
    <div class="grid grid-cols-2 gap-4 mb-6">
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-2">
        <span class="text-gray-600">PM10</span>
        <span class="font-medium">{{ city.pm10 }} µg/m³</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-2">
        <span class="text-gray-600">PM2.5</span>
        <span class="font-medium">{{ city.pm25 }} µg/m³</span>
      </div>
    </div>

    <div class="grid grid-cols-4 gap-2">
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-blue-500">🌡️</span>
        <span class="text-gray-600">{{ city.temp }}°C</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-blue-400">💧</span>
        <span class="text-gray-600">{{ city.humidity }}%</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-yellow-500">🌬</span>
        <span class="text-gray-600">{{ city.wind }} km/h</span>
      </div>
      <div class="bg-white bg-opacity-80 p-3 rounded-lg shadow-inner flex items-center gap-1">
        <span class="text-yellow-500">☀️</span>
        <span class="text-gray-600">UV {{ city.uv }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['city'])
</script>