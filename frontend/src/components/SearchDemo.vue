<template>
  <div class="p-4 bg-white rounded-lg shadow">
    <h3 class="text-lg font-bold mb-4">Search Demo</h3>
    <CitySearch @location-selected="handleLocationSelected" />
    
    <div v-if="selectedLocation" class="mt-4 p-3 bg-blue-50 rounded-lg">
      <h4 class="font-semibold">Selected Location:</h4>
      <p><strong>Name:</strong> {{ selectedLocation.name }}</p>
      <p><strong>Country:</strong> {{ selectedLocation.country }}</p>
      <p><strong>Coordinates:</strong> {{ selectedLocation.lat }}, {{ selectedLocation.lon }}</p>
      <p v-if="selectedLocation.aqi"><strong>AQI:</strong> {{ selectedLocation.aqi }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import CitySearch from './CitySearch.vue'

const selectedLocation = ref(null)

function handleLocationSelected(location) {
  selectedLocation.value = location
  console.log('Location selected:', location)
}
</script>
