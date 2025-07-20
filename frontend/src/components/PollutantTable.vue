<template>
  <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900">Pollutant Concentrations</h3>
      <p class="text-sm text-gray-600">Current measurements and regulatory compliance status</p>
    </div>
    <div class="overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pollutant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WHO Limit</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trend</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Limit</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="pollutant in pollutants" :key="pollutant.name" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-md flex items-center justify-center text-xs font-bold text-white mr-3"
                     :class="getPollutantColor(pollutant.name)">
                  {{ getPollutantSymbol(pollutant.name) }}
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ pollutant.name }}</div>
                  <div class="text-xs text-gray-500">{{ pollutant.description || 'Air pollutant' }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ pollutant.value || 0 }}</div>
              <div class="text-xs text-gray-500">{{ pollutant.unit || 'μg/m³' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ pollutant.limit || 0 }} {{ pollutant.unit || 'μg/m³' }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusColor(pollutant.status || 'Good')">
                {{ pollutant.status || 'Good' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <svg v-if="pollutant.trend === 'up'" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7m0 10H7"></path>
                </svg>
                <svg v-else-if="pollutant.trend === 'down'" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7l-9.2 9.2M7 7v10m0-10h10"></path>
                </svg>
                <svg v-else class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
                <span class="ml-1 text-xs text-gray-500 capitalize">{{ pollutant.trend || 'stable' }}</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                  <div class="h-2 rounded-full transition-all duration-300"
                       :class="getProgressBarColor(pollutant.status || 'Good')"
                       :style="{ width: Math.min(pollutant.percentage || 0, 100) + '%' }"></div>
                </div>
                <span class="text-xs text-gray-600">{{ pollutant.percentage || 0 }}%</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  pollutants: {
    type: Array,
    default: () => []
  }
})

const getPollutantSymbol = (name) => {
  const symbols = { 'PM2.5': 'PM', 'PM10': 'PM', 'NO₂': 'NO₂', 'O₃': 'O₃', 'SO₂': 'SO₂', 'CO': 'CO' }
  return symbols[name] || name
}

const getPollutantColor = (name) => {
  const colors = { 'PM2.5': 'bg-red-600', 'PM10': 'bg-orange-600', 'NO₂': 'bg-blue-600', 'O₃': 'bg-green-600', 'SO₂': 'bg-purple-600', 'CO': 'bg-gray-600' }
  return colors[name] || 'bg-gray-600'
}

const getStatusColor = (status) => {
  const colors = { 'Good': 'bg-green-100 text-green-800', 'Moderate': 'bg-yellow-100 text-yellow-800', 'Unhealthy': 'bg-red-100 text-red-800' }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getProgressBarColor = (status) => {
  const colors = { 'Good': 'bg-green-500', 'Moderate': 'bg-yellow-500', 'Unhealthy': 'bg-red-500' }
  return colors[status] || 'bg-gray-500'
}
</script>
