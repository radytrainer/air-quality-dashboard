<template>
  <div class="container mx-auto p-8">
    <h1 class="text-2xl font-bold mb-8">Search Functionality Test</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Search Demo -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Search Component</h2>
        <SearchDemo />
      </div>
      
      <!-- Map Demo -->
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Map Integration</h2>
        <p class="text-gray-600 mb-4">Search for a location above to see it on the map</p>
        <div id="test-map" class="h-80 w-full rounded-lg border"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import SearchDemo from '@/components/SearchDemo.vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

let testMap = null

const handleLocationSelected = (event) => {
  const location = event.detail
  if (location && location.lat && location.lon && testMap) {
    testMap.setView([location.lat, location.lon], 10)
    
    // Add marker
    const marker = L.marker([location.lat, location.lon]).addTo(testMap)
    marker.bindPopup(`
      <div style="text-align: center;">
        <h4><i class="fas fa-map-marker-alt"></i> ${location.name}</h4>
        <p>${location.country}</p>
        ${location.aqi ? `<p>AQI: ${location.aqi}</p>` : ''}
      </div>
    `).openPopup()
    
    // Remove marker after 5 seconds
    setTimeout(() => marker.remove(), 5000)
  }
}

onMounted(() => {
  // Initialize test map
  testMap = L.map('test-map', {
    center: [20, 0],
    zoom: 2,
  })
  
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap & CARTO',
  }).addTo(testMap)
  
  // Listen for location selections
  window.addEventListener('location-search-selected', handleLocationSelected)
})

onUnmounted(() => {
  window.removeEventListener('location-search-selected', handleLocationSelected)
  if (testMap) {
    testMap.remove()
  }
})
</script>
