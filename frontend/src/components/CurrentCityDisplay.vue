<template>
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <div class="w-20 h-12  shadow-md">
          <img
            :src="getFlagUrl(selectedCity.country)"
            alt="Flag"
            class="w-full h-full object-cover"
          />
        </div>
        <div>
          <h2 class="text-xl font-semibold text-blue-900">{{ selectedCity.name }}</h2>
          <p class="text-sm text-blue-700">{{ selectedCity.region }}, {{ selectedCity.country }}</p>
          <p class="text-xs text-blue-600">{{ selectedCity.continent }}</p>
        </div>
      </div>
      <div class="text-right">
        <div class="text-sm text-blue-600">Monitoring Status</div>
        <div class="text-sm font-medium text-blue-900">{{ selectedCity.monitoringLevel || 'Standard' }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  selectedCity: Object
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

  const code = countryCodes[countryName] || 'un'
  return `https://flagcdn.com/w80/${code}.png`
}
</script>
