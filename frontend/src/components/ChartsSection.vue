<template>
  <div class="bg-white rounded-lg shadow-md border border-gray-100 mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Historical Trends</h3>
          <p class="text-sm text-gray-600">Pollutant concentration over time</p>
        </div>
        <div class="flex space-x-1">
          <button 
            v-for="period in ['24H', '7D', '30D']" 
            :key="period"
            class="px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200"
            :class="selectedPeriod === period ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            @click="$emit('periodChange', period)"
          >
            {{ period }}
          </button>
        </div>
      </div>
    </div>
    <div class="p-6">
      <div class="h-64 relative">
        <apexchart 
          type="area" 
          height="100%" 
          :options="chartOptions" 
          :series="chartSeries"
        />
      </div>
      
      <!-- Custom Legend -->
      <div class="mt-6 flex items-center justify-center space-x-8">
        <div class="flex items-center space-x-2">
          <div class="w-4 h-1 bg-red-600 rounded-full"></div>
          <span class="text-sm text-gray-700 font-medium">PM2.5</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-4 h-1 bg-orange-600 rounded-full"></div>
          <span class="text-sm text-gray-700 font-medium">PM10</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-4 h-1 bg-blue-600 rounded-full"></div>
          <span class="text-sm text-gray-700 font-medium">NO₂</span>
        </div>
      </div>
      
      <!-- Current Values -->
      <div class="mt-6 grid grid-cols-3 gap-4">
        <div class="text-center p-3 bg-red-50 rounded-lg border border-red-100">
          <div class="text-xl font-bold text-red-800">{{ currentPM25 }}</div>
          <div class="text-xs text-red-600">PM2.5 μg/m³</div>
        </div>
        <div class="text-center p-3 bg-orange-50 rounded-lg border border-orange-100">
          <div class="text-xl font-bold text-orange-800">{{ currentPM10 }}</div>
          <div class="text-xs text-orange-600">PM10 μg/m³</div>
        </div>
        <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-100">
          <div class="text-xl font-bold text-blue-800">{{ currentNO2 }}</div>
          <div class="text-xs text-blue-600">NO₂ μg/m³</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import ApexChart from 'vue3-apexcharts'

const props = defineProps({
  selectedPeriod: {
    type: String,
    default: '24H'
  },
  selectedCity: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['periodChange'])

const historicalData = ref({
  pm25: [],
  pm10: [],
  no2: []
})

// Generate historical data based on city and period
const generateHistoricalData = (city, period) => {
  if (!city) return

  const dataPoints = period === '24H' ? 24 : period === '7D' ? 7 : 30
  const populationNum = city.population ? parseInt(city.population.replace(/,/g, '')) : 1000000
  const populationFactor = Math.log10(populationNum) / 7

  let basePollutionMultiplier = 1
  switch (city.monitoringLevel) {
    case 'Advanced': basePollutionMultiplier = 0.7; break
    case 'Standard': basePollutionMultiplier = 1.0; break
    case 'Basic': basePollutionMultiplier = 1.5; break
  }

  // Regional adjustments
  if (city.continent === 'Asia' && city.monitoringLevel === 'Basic') {
    basePollutionMultiplier *= 1.8
  } else if (city.continent === 'Africa' && city.monitoringLevel === 'Basic') {
    basePollutionMultiplier *= 1.6
  } else if (city.continent === 'Europe' || city.continent === 'Oceania') {
    basePollutionMultiplier *= 0.8
  }

  const generateDataSeries = (baseValue, variation = 0.3) => {
    const data = []
    for (let i = 0; i < dataPoints; i++) {
      const timeVariation = period === '24H' 
        ? Math.sin((i / 24) * Math.PI * 2) * 0.3 + 0.7
        : 0.7 + (Math.random() * 0.6)
      
      const value = baseValue * populationFactor * basePollutionMultiplier * 
                    timeVariation * (1 + (Math.random() - 0.5) * variation)
      data.push(Number(value.toFixed(1)))  // keep one decimal point
    }
    return data
  }

  historicalData.value = {
    pm25: generateDataSeries(35, 0.4),
    pm10: generateDataSeries(45, 0.3),
    no2: generateDataSeries(28, 0.5)
  }
}

// Watch props to regenerate data
watch(() => props.selectedPeriod, (newPeriod) => {
  if (props.selectedCity) generateHistoricalData(props.selectedCity, newPeriod)
}, { immediate: true })

watch(() => props.selectedCity, (newCity) => {
  if (newCity) generateHistoricalData(newCity, props.selectedPeriod)
}, { immediate: true })

// Compute max value for Y-axis scaling
const maxValue = computed(() => {
  const allValues = [
    ...historicalData.value.pm25,
    ...historicalData.value.pm10,
    ...historicalData.value.no2
  ]
  return Math.max(...allValues, 100)
})

// Compute X-axis labels
const xAxisLabels = computed(() => {
  const period = props.selectedPeriod
  if (period === '24H') {
    return Array.from({ length: 24 }, (_, i) => `${i.toString().padStart(2, '0')}:00`)
  } else if (period === '7D') {
    return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  } else {
    return Array.from({ length: 5 }, (_, i) => `Week ${i + 1}`)
  }
})

// Current pollutant values with 1 decimal place formatting
const currentPM25 = computed(() => {
  const val = historicalData.value.pm25.slice(-1)[0] || 0
  return val.toFixed(1)
})
const currentPM10 = computed(() => {
  const val = historicalData.value.pm10.slice(-1)[0] || 0
  return val.toFixed(1)
})
const currentNO2 = computed(() => {
  const val = historicalData.value.no2.slice(-1)[0] || 0
  return val.toFixed(1)
})

// Chart series data
const chartSeries = computed(() => [
  { name: 'PM2.5', data: historicalData.value.pm25 },
  { name: 'PM10', data: historicalData.value.pm10 },
  { name: 'NO₂', data: historicalData.value.no2 }
])

// Chart options for Area chart with gradient fill
const chartOptions = computed(() => ({
  chart: {
    height: '100%',
    type: 'area',
    toolbar: { show: false },
    zoom: { enabled: false }
  },
  dataLabels: {
    enabled: false // <- Add this to hide numbers on points
  },
  colors: ['#dc2626', '#ea580c', '#2563eb'],
  stroke: {
    curve: 'smooth',
    width: 2
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.4,
      opacityTo: 0,
      stops: [0, 90, 100]
    }
  },
  markers: {
    size: 4,
    colors: ['#dc2626', '#ea580c', '#2563eb'],
    strokeColors: '#fff',
    strokeWidth: 2,
    hover: { size: 6 }
  },
  xaxis: {
    categories: xAxisLabels.value,
    labels: { style: { colors: '#6b7280' } },
    axisBorder: { show: false },
    axisTicks: { show: false }
  },
  yaxis: {
    min: 0,
    max: maxValue.value,
    labels: {
      formatter: val => val.toFixed(0),
      style: { colors: '#6b7280' }
    },
    title: {
      text: 'Concentration (µg/m³)',
      style: { color: '#4b5563', fontSize: '12px' }
    }
  },
  grid: {
    borderColor: '#e5e7eb',
    strokeDashArray: 4,
    xaxis: { lines: { show: false } }
  },
  tooltip: {
    x: { show: true },
    y: {
      formatter: val => `${val.toFixed(1)} µg/m³`
    }
  },
  legend: {
    show: false
  }
}))

</script>

<script>
// Local registration of ApexChart component
import ApexChart from 'vue3-apexcharts'
export default {
  components: {
    apexchart: ApexChart
  }
}
</script>
