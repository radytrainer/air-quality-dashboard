<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-900">{{ totalCities }}</div>
        <div class="text-sm text-gray-600">Cities Available</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-900">{{ stationsActive }}</div>
        <div class="text-sm text-gray-600">Active Stations</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-900">{{ dataPoints }}</div>
        <div class="text-sm text-gray-600">Data Points/Day</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-900">99.8%</div>
        <div class="text-sm text-gray-600">System Uptime</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-900">{{ totalCountries }}</div>
        <div class="text-sm text-gray-600">Countries</div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  totalCities: Number,
  totalCountries: Number,
  stationsActive: Number,
  dataPoints: Number
})
</script>
