<!-- src/components/CitySearch.vue -->
<template>
  <div class="relative w-96">
    <!-- Search Input with Icons -->
    <input v-model="search" @input="filterCities" type="text" placeholder="Search any Location, City, State or Country"
      class="bg-gray-100 text-gray-900 px-8 py-1.5 rounded-md w-full placeholder-gray-500 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-sm h-9" />
    <!-- Search Icon -->
    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
    <!-- Locate Icon -->
    <i class="fas fa-crosshairs absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer hover:text-blue-600 transition"
      title="Locate me" @click="locateUser"></i>

    <!-- Autocomplete Dropdown -->
    <ul v-if="filteredCities.length && search"
      class="absolute z-10 bg-white border border-gray-200 w-full mt-1 rounded shadow">
      <li v-for="city in filteredCities" :key="city" @click="selectCity(city)"
        class="p-2 cursor-pointer hover:bg-gray-100">
        {{ city }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['city-selected'])

const search = ref('')
const filteredCities = ref([])

// Example cities
const allCities = [
  'Phnom Penh',
  'Siem Reap',
  'Battambang',
  'Sihanoukville',
  'Kampot',
  'Kep',
  'Poipet',
  'Takeo',
  'Kampong Cham',
  'Koh Kong'
]

function filterCities() {
  const query = search.value.toLowerCase()
  filteredCities.value = allCities.filter(city =>
    city.toLowerCase().includes(query)
  )
}

function selectCity(city) {
  search.value = city
  filteredCities.value = []
  emit('city-selected', city)
}

function locateUser() {
  // Just for demonstration — you can replace this with geolocation
  alert('Locating your position...')
}
</script>

<style scoped>
/* You can improve styles here if needed */
@import "@fortawesome/fontawesome-free/css/all.min.css";
</style>
