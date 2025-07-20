<template>
  <div class="bg-white rounded-lg shadow p-6 mb-6">
    <div v-if="selectedCity" class="flex items-center justify-between">
      <div>
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
          <img :src="getFlagUrl(selectedCity.country)" :alt="`${selectedCity.country} flag`" class="w-8 h-auto inline-block" />
          {{ selectedCity.name }}
          <span class="text-base font-normal text-gray-500">({{ selectedCity.country }})</span>
        </h2>
        <p class="text-gray-600 mt-1">
          {{ selectedCity.region }}, {{ selectedCity.continent }}
        </p>
    
      </div>
      <div class="text-right">
        <div class="text-sm text-blue-600">Monitoring Status</div>
        <div class="text-sm font-medium text-blue-900">{{ selectedCity.monitoringLevel || 'Standard' }}</div>
      </div>

    </div>
    <div v-else class="text-center py-8 text-gray-500">
      <p class="text-lg font-medium">No city selected.</p>
      <p class="text-sm">Please select a city from the dropdown above to view its details.</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  selectedCity: {
    type: Object,
    default: null
  },
  currentTime: {
    type: String,
    default: 'N/A'
  }
})

function getFlagUrl(countryName) {
  const countryCodes = {
    'India': 'in',
    'United States': 'us',
    'Canada': 'ca',
    'Mexico': 'mx',
    'United Kingdom': 'gb',
    'France': 'fr',
    'Germany': 'de',
    'Spain': 'es',
    'Japan': 'jp',
    'China': 'cn',
    'South Korea': 'kr',
    'Singapore': 'sg',
    'Thailand': 'th',
    'Cambodia': 'kh',
    'Egypt': 'eg',
    'Nigeria': 'ng',
    'South Africa': 'za',
    'Australia': 'au',
    'New Zealand': 'nz',
    'Brazil': 'br',
    'Argentina': 'ar'
  }
  const code = countryCodes[countryName] || 'un' // 'un' for unknown or default flag
  return `https://flagcdn.com/w80/${code}.png`
}
</script>
