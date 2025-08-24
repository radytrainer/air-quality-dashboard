<template>
  <div class="bg-white shadow-lg rounded-xl p-6 mb-10">
    <h2 class="text-2xl font-bold text-center mb-4">📈 Air Pollutant Trends Over Time</h2>
    
    <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
      <!-- City -->
      <div>
        <label for="city" class="block text-sm font-medium text-gray-700">City:</label>
        <select 
          v-model="selectedCity" 
          @change="fetchAQIHistory"
          id="city"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        >
          <option value="Phnom Penh">Phnom Penh</option>
          <option value="Siem Reap">Siem Reap</option>
          <option value="Battambang">Battambang</option>
        </select>
      </div>

      <!-- Pollutant -->
      <div>
        <label for="pollutant" class="block text-sm font-medium text-gray-700">Pollutant:</label>
        <select 
          v-model="selectedPollutant" 
          @change="fetchAQIHistory"
          id="pollutant"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        >
          <option value="overall_aqi">AQI</option>
          <option value="CO">CO</option>
          <option value="NO2">NO2</option>
          <option value="OZONE">OZONE</option>
          <option value="PM10">PM10</option>
          <option value="PM25">PM2.5</option>
          <option value="SO2">SO2</option>
        </select>
      </div>

      <!-- Days -->
      <div>
        <label for="days" class="block text-sm font-medium text-gray-700">Date Range (Days):</label>
        <select 
          v-model="selectedDays" 
          @change="fetchAQIHistory"
          id="days"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        >
          <option value="7">Last 7 Days</option>
          <option value="14">Last 14 Days</option>
          <option value="30">Last 30 Days</option>
        </select>
      </div>
    </div>

    <!-- Chart -->
    <Line :data="chartData" :options="chartOptions" v-if="!loading" />

    <!-- Loading -->
    <div v-if="loading" class="text-center text-gray-500 mt-4">Loading...</div>

    <!-- Avg / Max -->
    <p class="text-center mt-4 text-sm text-gray-600">
      Avg: {{ averageValue }} | Max: {{ maxValue }}
    </p>

    <!-- Download -->
    <div class="flex justify-center mt-4">
      <button @click="downloadChart" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        Download Chart
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement)

const selectedCity = ref('Phnom Penh')
const selectedPollutant = ref('overall_aqi')
const selectedDays = ref('7')
const loading = ref(false)

const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'AQI',
      data: [],
      fill: false,
      borderColor: 'rgba(75, 192, 192, 1)',
      tension: 0.4,
    }
  ]
})

const chartOptions = ref({
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
    },
    title: {
      display: true,
      text: 'Air Pollutant Trend'
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          return `${context.dataset.label}: ${context.parsed.y}`
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Value'
      }
    },
    x: {
      title: {
        display: true,
        text: 'Date'
      }
    }
  }
})

const pollutantColors = {
  overall_aqi: 'rgba(75, 192, 192, 1)',
  CO: 'rgba(255, 99, 132, 1)',
  NO2: 'rgba(255, 206, 86, 1)',
  OZONE: 'rgba(153, 102, 255, 1)',
  PM10: 'rgba(54, 162, 235, 1)',
  PM25: 'rgba(255, 159, 64, 1)',
  SO2: 'rgba(199, 199, 199, 1)'
}

const fetchAQIHistory = async () => {
  loading.value = true
  try {
    const days = parseInt(selectedDays.value)
    const pollutant = selectedPollutant.value
    const city = selectedCity.value

    // Define base values per city and pollutant
    const baseValues = {
      'Phnom Penh': {
        overall_aqi: 90,
        CO: 0.6,
        NO2: 35,
        OZONE: 20,
        PM10: 50,
        PM25: 60,
        SO2: 8
      },
      'Siem Reap': {
        overall_aqi: 70,
        CO: 0.4,
        NO2: 28,
        OZONE: 18,
        PM10: 40,
        PM25: 45,
        SO2: 5
      },
      'Battambang': {
        overall_aqi: 80,
        CO: 0.5,
        NO2: 30,
        OZONE: 15,
        PM10: 45,
        PM25: 50,
        SO2: 6
      }
    }

    // Generate fake data based on base + random noise
    const dummyData = Array.from({ length: days }, (_, i) => {
      const date = new Date()
      date.setDate(date.getDate() - (days - 1 - i))
      const base = baseValues[city][pollutant] || 50
      const value = Math.max(0, parseFloat((base + Math.random() * 10 - 5).toFixed(1)))

      return {
        date: date.toISOString().split('T')[0],
        [pollutant]: value
      }
    })

    chartData.value.labels = dummyData.map(d => d.date)
    chartData.value.datasets[0].data = dummyData.map(d => d[pollutant])
    chartData.value.datasets[0].label = pollutant === 'overall_aqi' ? 'AQI' : pollutant
    chartData.value.datasets[0].borderColor = pollutantColors[pollutant]
    chartOptions.value.plugins.title.text = `${pollutant === 'overall_aqi' ? 'AQI' : pollutant} Trend for ${city}`
  } catch (err) {
    console.error('Failed to fetch trend data', err)
  } finally {
    loading.value = false
  }
}
onMounted(() => {
  fetchAQIHistory()
})
</script>

<style scoped>
canvas {
  max-height: 400px;
}
</style>
