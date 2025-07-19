<!-- components/CitySelector.vue -->
<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="border-t border-gray-200 pt-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Global Location</label>
            <div class="relative">
              <select
                :value="selectedCity.id"
                @change="$emit('cityChange', +$event.target.value)"
                class="w-96 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none"
              >
                <optgroup
                  v-for="continent in groupedCities"
                  :key="continent.name"
                  :label="`${continent.name} (${continent.cities.length} cities)`"
                >
                  <option
                    v-for="city in continent.cities"
                    :key="city.id"
                    :value="city.id"
                  >
                    {{ city.flag }} {{ city.name }}, {{ city.country }}
                  </option>
                </optgroup>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <div class="text-left">
            <div class="text-sm text-gray-500">Population</div>
            <div class="text-sm font-medium text-gray-900">{{ selectedCity.population }}</div>
          </div>
          <div class="text-left">
            <div class="text-sm text-gray-500">Region</div>
            <div class="text-sm font-medium text-gray-900">{{ selectedCity.region }}</div>
          </div>
          <div class="text-left">
            <div class="text-sm text-gray-500">Timezone</div>
            <div class="text-sm font-medium text-gray-900">{{ selectedCity.timezone }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  selectedCity: Object,
  groupedCities: Array
})

const emit = defineEmits(['cityChange'])
</script>
