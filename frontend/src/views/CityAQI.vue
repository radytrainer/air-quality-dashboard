<template>
  <div class="p-8 max-w-7xl mx-auto bg-gray-50 min-h-screen">
    <!-- Filters -->
    <div class="bg-white p-6 rounded-xl shadow mb-8 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <!-- Filter by Country -->
      <div>
        <label for="country-filter" class="block mb-2 text-sm font-semibold text-gray-700">Filter by Country</label>
        <select
          id="country-filter"
          v-model="selectedCountry"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-400"
        >
          <option value="">All Countries</option>
          <option v-for="country in uniqueCountries" :key="country" :value="country">{{ country }}</option>
        </select>
      </div>

      <!-- Filter by Pollutant -->
      <div>
        <label for="pollutant-filter" class="block mb-2 text-sm font-semibold text-gray-700">Filter by Pollutant</label>
        <select
          id="pollutant-filter"
          v-model="selectedPollutant"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-400"
        >
          <option value="">All Pollutants</option>
          <option v-for="p in uniquePollutants" :key="p" :value="p">{{ p.replace('_', '.') }}</option>
        </select>
      </div>

      <!-- AQI Min -->
      <div>
        <label for="min-aqi" class="block mb-2 text-sm font-semibold text-gray-700">AQI Min</label>
        <input
          id="min-aqi"
          type="number"
          v-model.number="minAqi"
          placeholder="Minimum AQI"
          min="0"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-400"
        />
      </div>

      <!-- AQI Max -->
      <div>
        <label for="max-aqi" class="block mb-2 text-sm font-semibold text-gray-700">AQI Max</label>
        <input
          id="max-aqi"
          type="number"
          v-model.number="maxAqi"
          placeholder="Maximum AQI"
          min="0"
          class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-400"
        />
      </div>
    </div>

    <!-- AQI Table -->
    <div class="bg-white rounded-xl shadow overflow-auto border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200" role="table" aria-label="Air Quality Index by city">
        <thead class="bg-indigo-50 text-indigo-700">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-semibold">City</th>
            <th class="px-6 py-4 text-left text-sm font-semibold">Country</th>
            <th class="px-6 py-4 text-left text-sm font-semibold">AQI</th>
            <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
            <th class="px-6 py-4 text-right text-sm font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr
            v-for="city in filteredCities"
            :key="city.id"
            @click="selectCity(city)"
            class="hover:bg-indigo-50 cursor-pointer relative"
          >
            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ city.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ city.country }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ city.aqi }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <span v-if="getStatus(city) === 'priority'" class="inline-flex px-3 py-1 rounded-full text-yellow-800 bg-yellow-200 font-semibold text-xs">Priority</span>
              <span v-else-if="getStatus(city) === 'highrisk'" class="inline-flex px-3 py-1 rounded-full text-red-800 bg-red-200 font-semibold text-xs">High Risk</span>
              <span v-else-if="getStatus(city) === 'normal'" class="inline-flex px-3 py-1 rounded-full text-green-800 bg-green-200 font-semibold text-xs">Normal</span>
              <span v-else class="inline-flex px-3 py-1 rounded-full text-gray-800 bg-gray-200 font-semibold text-xs">No Status</span>
            </td>

            <td class="px-6 py-4 text-right relative">
              <!-- Three dots button -->
              <button
                @click.stop="toggleDropdown(city.id)"
                class="inline-flex items-center p-2 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                aria-haspopup="true"
                :aria-expanded="dropdownOpen === city.id ? 'true' : 'false'"
                aria-label="Open status menu"
                title="Change status"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                  <circle cx="10" cy="4" r="2" />
                  <circle cx="10" cy="10" r="2" />
                  <circle cx="10" cy="16" r="2" />
                </svg>
              </button>

              <!-- Dropdown menu -->
              <div
                v-if="dropdownOpen === city.id"
                class="absolute right-0 mt-2 w-36 bg-white border border-gray-300 rounded shadow-lg z-20"
                role="menu"
                aria-orientation="vertical"
                tabindex="-1"
              >
                <button
                  @click="setStatus(city.id, null)"
                  class="w-full text-left px-4 py-2 hover:bg-green-100 text-green-700 font-semibold"
                  role="menuitem"
                  tabindex="0"
                >
                   Normal
                </button>
                <button
                  @click="setStatus(city.id, 'priority')"
                  class="w-full text-left px-4 py-2 hover:bg-yellow-100 text-yellow-700 font-semibold"
                  role="menuitem"
                  tabindex="0"
                >
                  Priority
                </button>
                <button
                  @click="setStatus(city.id, 'highRisk')"
                  class="w-full text-left px-4 py-2 hover:bg-red-100 text-red-700 font-semibold"
                  role="menuitem"
                  tabindex="0"
                >
                  High Risk
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="filteredCities.length === 0">
            <td colspan="5" class="px-6 py-6 text-center text-gray-500 font-semibold">No cities match the filter criteria.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Detailed Pollutant Panel -->
    <div
      v-if="selectedCity"
      tabindex="0"
      role="region"
      :aria-labelledby="`details-title-${selectedCity.id}`"
      class="mt-10 bg-white rounded-xl shadow p-8 border border-gray-200 max-w-4xl mx-auto"
    >
      <h2 :id="`details-title-${selectedCity.id}`" class="text-indigo-700 text-2xl font-bold mb-6">
        Pollutant Levels for {{ selectedCity.name }}
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div
          v-for="(value, pollutant) in selectedCity.pollutants"
          :key="pollutant"
          class="bg-indigo-50 rounded-lg p-4 text-center shadow"
        >
          <p class="text-indigo-700 font-semibold text-sm mb-2">{{ pollutant.replace('_', '.') }}</p>
          <p class="text-indigo-900 text-xl font-bold">{{ value }} µg/m³</p>
        </div>
      </div>

      <button
        @click="selectedCity = null"
        class="mt-8 px-6 py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        aria-label="Close pollutant details panel"
      >
        Close Details
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const STORAGE_KEY = 'admin-aqi-statuses'

const cities = ref([
  {
    id: 1,
    name: 'Beijing',
    country: 'China',
    aqi: 150,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 100, PM10: 120, CO: 5, O3: 80, NO2: 60, SO2: 30 },
  },
  {
    id: 2,
    name: 'London',
    country: 'UK',
    aqi: 45,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 20, PM10: 30, CO: 1, O3: 40, NO2: 25, SO2: 10 },
  },
  {
    id: 3,
    name: 'New Delhi',
    country: 'India',
    aqi: 200,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 150, PM10: 180, CO: 10, O3: 90, NO2: 70, SO2: 50 },
  },
  {
    id: 4,
    name: 'Tokyo',
    country: 'Japan',
    aqi: 70,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 25, PM10: 35, CO: 2, O3: 50, NO2: 30, SO2: 15 },
  },
  {
    id: 5,
    name: 'Sydney',
    country: 'Australia',
    aqi: 30,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 10, PM10: 15, CO: 0.2, O3: 20, NO2: 10, SO2: 5 },
  },
  {
    id: 6,
    name: 'Mexico City',
    country: 'Mexico',
    aqi: 130,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 70, PM10: 85, CO: 7, O3: 95, NO2: 55, SO2: 40 },
  },
  {
    id: 7,
    name: 'Phnom Penh',
    country: 'Cambodia',
    aqi: 60,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 30, PM10: 40, CO: 3, O3: 45, NO2: 20, SO2: 12 },
  },
  {
    id: 8,
    name: 'Singapore',
    country: 'Singapore',
    aqi: 25,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 5, PM10: 10, CO: 0.1, O3: 15, NO2: 5, SO2: 2 },
  },  
  {
    id: 9,
    name: 'Bangkok',
    country: 'Thailand',
    aqi: 80,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 40, PM10: 50, CO: 4, O3: 60, NO2: 35, SO2: 20 },
  },
  {
    id: 10,
    name: 'Berlin',
    country: 'Germany',
    aqi: 55,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 15, PM10: 25, CO: 1.5, O3: 30, NO2: 20, SO2: 8 },
  },
  {
    id: 11,
    name: 'Hanoi',
    country: 'Vietnam',
    aqi: 90,
    priority: null,
    highRisk: null,
    pollutants: { PM2_5: 50, PM10: 60, CO: 6, O3: 70, NO2: 45, SO2: 25 },
  },
])

const selectedCountry = ref('')
const selectedPollutant = ref('')
const minAqi = ref(null)
const maxAqi = ref(null)
const selectedCity = ref(null)
const dropdownOpen = ref(null)

const loadStatuses = () => {
  const stored = localStorage.getItem(STORAGE_KEY)
  if (stored) {
    const parsed = JSON.parse(stored)
    cities.value.forEach(city => {
      if (parsed[city.id]) {
        city.priority = parsed[city.id].priority
        city.highRisk = parsed[city.id].highRisk
      }
    })
  }
}

const saveStatuses = () => {
  const statusMap = {}
  cities.value.forEach(city => {
    statusMap[city.id] = { priority: city.priority, highRisk: city.highRisk }
  })
  localStorage.setItem(STORAGE_KEY, JSON.stringify(statusMap))
}

const toggleDropdown = (id) => {
  dropdownOpen.value = dropdownOpen.value === id ? null : id
}
const getStatus = (city) => {
  if (city.priority === null && city.highRisk === null) return 'none' // not clicked yet
  if (city.priority) return 'priority'
  if (city.highRisk) return 'highrisk'
  return 'normal' // explicitly set to normal
}

const setStatus = (id, status) => {
  const city = cities.value.find(c => c.id === id)
  if (!city) return

  if (status === 'priority') {
    city.priority = true
    city.highRisk = false
  } else if (status === 'highRisk') {
    city.highRisk = true
    city.priority = false
  } else if (status === null) {
    city.priority = false
    city.highRisk = false
  }
  saveStatuses()
  dropdownOpen.value = null
}

const uniqueCountries = computed(() => [...new Set(cities.value.map(c => c.country))].sort())

const uniquePollutants = computed(() => {
  const set = new Set()
  cities.value.forEach(c => {
    Object.keys(c.pollutants).forEach(p => set.add(p))
  })
  return [...set].sort()
})

const filteredCities = computed(() => {
  return cities.value.filter(city => {
    if (selectedCountry.value && city.country !== selectedCountry.value) return false
    if (
      selectedPollutant.value &&
      (!city.pollutants[selectedPollutant.value] || city.pollutants[selectedPollutant.value] <= 0)
    )
      return false
    if (minAqi.value !== null && city.aqi < minAqi.value) return false
    if (maxAqi.value !== null && city.aqi > maxAqi.value) return false
    return true
  })
})

const selectCity = (city) => {
  selectedCity.value = city
}

// Close dropdown if click outside
const onClickOutside = (event) => {
  if (!event.target.closest('.dropdown-wrapper')) {
    dropdownOpen.value = null
  }
}

onMounted(() => {
  loadStatuses()
  document.addEventListener('click', onClickOutside)
})

</script>

<style scoped>
/* Make sure dropdown stays above */
.relative > div[role="menu"] {
  z-index: 50;
}
</style>
