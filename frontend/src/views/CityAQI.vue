<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">City/Location AQI Management</h1>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <select v-model="selectedCountry" class="border p-2 rounded">
        <option value="">All Countries</option>
        <option v-for="country in uniqueCountries" :key="country">{{ country }}</option>
      </select>

      <select v-model="selectedPollutant" class="border p-2 rounded">
        <option value="">All Pollutants</option>
        <option v-for="pollutant in uniquePollutants" :key="pollutant">{{ pollutant }}</option>
      </select>

      <input
        type="number"
        v-model.number="aqiThreshold"
        placeholder="Max AQI"
        class="border p-2 rounded"
      />
    </div>

    <!-- AQI Table -->
    <table class="min-w-full bg-white border rounded shadow-md">
      <thead class="bg-gray-100">
        <tr>
          <th class="py-2 px-4 border">City</th>
          <th class="py-2 px-4 border">Country</th>
          <th class="py-2 px-4 border">AQI</th>
          <th class="py-2 px-4 border">Pollutant</th>
          <th class="py-2 px-4 border">Status</th>
          <th class="py-2 px-4 border">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="city in filteredCities"
          :key="city.id"
          @click="selectCity(city)"
          class="cursor-pointer hover:bg-gray-100"
        >
          <td class="py-2 px-4 border">{{ city.name }}</td>
          <td class="py-2 px-4 border">{{ city.country }}</td>
          <td class="py-2 px-4 border">{{ city.aqi }}</td>
          <td class="py-2 px-4 border">{{ city.pollutant }}</td>
          <td class="py-2 px-4 border">
            <span
              :class="city.priority ? 'text-red-500 font-bold' : 'text-gray-600'"
            >
              {{ city.priority ? 'High Risk' : 'Normal' }}
            </span>
          </td>
          <td class="py-2 px-4 border">
            <button
              @click.stop="togglePriority(city)"
              class="text-sm bg-blue-500 text-white px-2 py-1 rounded"
            >
              {{ city.priority ? 'Unmark' : 'Mark High Risk' }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- City Details Modal -->
    <div v-if="selectedCity" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded shadow-lg w-[400px]">
        <h2 class="text-xl font-bold mb-4">Details for {{ selectedCity.name }}</h2>
        <ul class="mb-4">
          <li><strong>Country:</strong> {{ selectedCity.country }}</li>
          <li><strong>AQI:</strong> {{ selectedCity.aqi }}</li>
          <li><strong>Main Pollutant:</strong> {{ selectedCity.pollutant }}</li>
          <li><strong>CO:</strong> {{ selectedCity.details.co }} µg/m³</li>
          <li><strong>NO₂:</strong> {{ selectedCity.details.no2 }} µg/m³</li>
          <li><strong>PM2.5:</strong> {{ selectedCity.details.pm25 }} µg/m³</li>
          <li><strong>O₃:</strong> {{ selectedCity.details.o3 }} µg/m³</li>
        </ul>
        <button @click="selectedCity = null" class="bg-gray-600 text-white px-4 py-2 rounded">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const cities = ref([
  {
    id: 1,
    name: 'Phnom Penh',
    country: 'Cambodia',
    aqi: 105,
    pollutant: 'PM2.5',
    priority: false,
    details: { co: 1.2, no2: 22, pm25: 105, o3: 15 },
  },
  {
    id: 2,
    name: 'Bangkok',
    country: 'Thailand',
    aqi: 90,
    pollutant: 'PM10',
    priority: true,
    details: { co: 0.8, no2: 18, pm25: 60, o3: 12 },
  },
  // More cities...
])

const selectedCountry = ref('')
const selectedPollutant = ref('')
const aqiThreshold = ref(null)
const selectedCity = ref(null)

const uniqueCountries = computed(() => [...new Set(cities.value.map(c => c.country))])
const uniquePollutants = computed(() => [...new Set(cities.value.map(c => c.pollutant))])

const filteredCities = computed(() => {
  return cities.value.filter(city => {
    const countryMatch = !selectedCountry.value || city.country === selectedCountry.value
    const pollutantMatch = !selectedPollutant.value || city.pollutant === selectedPollutant.value
    const aqiMatch = !aqiThreshold.value || city.aqi <= aqiThreshold.value
    return countryMatch && pollutantMatch && aqiMatch
  })
})

function selectCity(city) {
  selectedCity.value = city
}

function togglePriority(city) {
  city.priority = !city.priority
}
</script>

<style scoped>
/* You can add more style here if needed */
</style>
