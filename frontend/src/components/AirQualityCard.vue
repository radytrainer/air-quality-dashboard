<template>
  <div class="bg-white rounded-2xl shadow p-6 w-full md:w-1/2">
    <h2 class="text-xl font-semibold text-gray-700">Current AQI</h2>
    <p class="text-5xl font-bold text-green-500 mt-4">{{ aqi }}</p>
    <p class="text-gray-500 mt-1">Air Quality Index</p>
  </div>
</template>

<script setup>
defineProps({
  aqi: Number
})
</script>
