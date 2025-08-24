<template>
  <div class="bg-gray-900 p-4 rounded-xl shadow-lg mb-6">
    <form @submit.prevent="applyFilters" class="flex flex-col md:flex-row gap-4 items-center justify-between">
      <!-- Pollutant Filter -->
      <div class="flex flex-col">
        <label class="text-sm text-gray-300 mb-1">Pollutant</label>
        <select v-model="pollutant" class="bg-gray-800 text-white px-4 py-2 rounded-lg border border-gray-600">
          <option value="">All</option>
          <option v-for="item in pollutantOptions" :key="item" :value="item">{{ item }}</option>
        </select>
      </div>

      <!-- Country Filter -->
      <div class="flex flex-col">
        <label class="text-sm text-gray-300 mb-1">Country</label>
        <select v-model="country" class="bg-gray-800 text-white px-4 py-2 rounded-lg border border-gray-600">
          <option value="">All</option>
          <option v-for="item in countryOptions" :key="item.code" :value="item.code">
            {{ item.name }}
          </option>
        </select>
      </div>

      <!-- Date Range -->
      <div class="flex flex-col">
        <label class="text-sm text-gray-300 mb-1">Date Range</label>
        <div class="flex gap-2">
          <input type="date" v-model="startDate" class="bg-gray-800 text-white px-3 py-2 rounded-lg border border-gray-600" />
          <input type="date" v-model="endDate" class="bg-gray-800 text-white px-3 py-2 rounded-lg border border-gray-600" />
        </div>
      </div>

      <!-- Apply Button -->
      <div class="mt-2 md:mt-6">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg">
          Apply Filters
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
const emit = defineEmits(['update:filters'])

const pollutant = ref('')
const country = ref('')
const startDate = ref('')
const endDate = ref('')

const pollutantOptions = ['pm25', 'pm10', 'o3', 'no2', 'so2', 'co']
const countryOptions = [
  { code: 'us', name: 'United States' },
  { code: 'cn', name: 'China' },
  { code: 'kr', name: 'South Korea' },
  { code: 'jp', name: 'Japan' },
  { code: 'fr', name: 'France' },
  { code: 'gb', name: 'United Kingdom' },
  { code: 'kh', name: 'Cambodia' },
  { code: 'in', name: 'India' }
]

const applyFilters = () => {
  emit('update:filters', {
    pollutant: pollutant.value,
    country: country.value,
    startDate: startDate.value,
    endDate: endDate.value
  })
}
</script>
