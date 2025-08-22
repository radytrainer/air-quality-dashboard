<template>
  <div class="p-6 bg-gradient-to-br to-indigo-100 rounded-2xl">
    <!-- Chart Header -->
    <div class="mb-6 text-center">
      <p class="text-gray-600">Pollution levels across different cities</p>
    </div>

    <!-- Line Chart -->
    <div class="h-80 mb-6 bg-white rounded-xl p-4 shadow-sm">
      <Line :data="chartData" :options="chartOptions" />
    </div>

    <!-- Pollution Data Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <div
        v-for="(label, index) in pollutants"
        :key="label.name"
        class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1"
      >
        <!-- Pollutant Header -->
        <div class="flex items-center justify-center mb-3">
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center mr-3"
            :style="{ backgroundColor: label.color + '20' }"
          >
            <span class="text-lg" :style="{ color: label.color }">{{ label.icon }}</span>
          </div>
          <div>
            <h4 class="font-bold text-gray-800">{{ label.name }}</h4>
            <p class="text-xs text-gray-500">{{ label.unit }}</p>
          </div>
        </div>

        <!-- City Values Comparison -->
        <div class="space-y-2">
          <!-- City 1 -->
          <div
            class="flex items-center justify-between p-2 rounded-lg"
            :style="{ backgroundColor: getAQIColor(city1.aqi) + '10' }"
          >
            <span class="text-sm font-medium text-gray-700">{{ city1.name }}</span>
            <span
              class="px-3 py-1 rounded-full text-white text-sm font-bold"
              :style="{ backgroundColor: getAQIColor(city1.aqi) }"
            >
              {{ city1Data[index] ?? 'N/A' }}
            </span>
          </div>

          <!-- City 2 -->
          <div
            class="flex items-center justify-between p-2 rounded-lg"
            :style="{ backgroundColor: getAQIColor(city2.aqi) + '10' }"
          >
            <span class="text-sm font-medium text-gray-700">{{ city2.name }}</span>
            <span
              class="px-3 py-1 rounded-full text-white text-sm font-bold"
              :style="{ backgroundColor: getAQIColor(city2.aqi) }"
            >
              {{ city2Data[index] ?? 'N/A' }}
            </span>
          </div>
        </div>

        <!-- Comparison Indicator -->
        <div class="mt-3 text-center">
          <div class="text-xs text-gray-500">
            {{ getComparisonText(city1Data[index], city2Data[index]) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 gap-4 mt-6">
      <div class="bg-white p-4 rounded-xl shadow-md">
        <h4 class="font-bold text-gray-800 mb-2">{{ city1.name }}</h4>
        <div class="flex items-center">
          <div class="w-4 h-4 rounded-full mr-2" :style="{ backgroundColor: getAQIColor(city1.aqi) }"></div>
          <span class="text-2xl font-bold" :style="{ color: getAQIColor(city1.aqi) }">{{ city1.aqi }}</span>
          <span class="ml-2 text-sm text-gray-600">AQI</span>
        </div>
      </div>

      <div class="bg-white p-4 rounded-xl shadow-md">
        <h4 class="font-bold text-gray-800 mb-2">{{ city2.name }}</h4>
        <div class="flex items-center">
          <div class="w-4 h-4 rounded-full mr-2" :style="{ backgroundColor: getAQIColor(city2.aqi) }"></div>
          <span class="text-2xl font-bold" :style="{ color: getAQIColor(city2.aqi) }">{{ city2.aqi }}</span>
          <span class="ml-2 text-sm text-gray-600">AQI</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

const props = defineProps({
  city1: Object,
  city2: Object
})

const pollutants = [
  { name: 'PM10', icon: '🌫️', color: '#8B5CF6', unit: 'µg/m³' },
  { name: 'PM2.5', icon: '💨', color: '#06B6D4', unit: 'µg/m³' },
  { name: 'CO', icon: '🏭', color: '#F59E0B', unit: 'ppm' },
  { name: 'NO2', icon: '🚗', color: '#EF4444', unit: 'µg/m³' },
  { name: 'SO2', icon: '⚡', color: '#10B981', unit: 'µg/m³' },
  { name: 'O3', icon: '☀️', color: '#F97316', unit: 'µg/m³' }
]

const labels = pollutants.map(p => p.name)

const getAQIColor = (aqi) => {
  if (aqi <= 50) return '#10B981'
  if (aqi <= 100) return '#F59E0B'
  if (aqi <= 150) return '#F97316'
  return '#EF4444'
}

const getComparisonText = (v1, v2) => {
  if (!v1 || !v2) return 'Data unavailable'
  if (Math.abs(v1 - v2) < 5) return 'Similar levels'
  return v1 > v2 ? `${props.city1.name} higher` : `${props.city2.name} higher`
}

const city1Data = computed(() => [
  props.city1.pm10,
  props.city1.pm25,
  props.city1.co,
  props.city1.no2,
  props.city1.so2,
  props.city1.o3
])

const city2Data = computed(() => [
  props.city2.pm10,
  props.city2.pm25,
  props.city2.co,
  props.city2.no2,
  props.city2.so2,
  props.city2.o3
])

// Line Chart Data
const chartData = computed(() => ({
  labels,
  datasets: [
    {
      label: props.city1.name,
      data: city1Data.value,
      borderColor: '#8B5CF6',
      backgroundColor: 'rgba(139,92,246,0.2)',
      tension: 0.3,
      fill: true,
      pointBackgroundColor: '#8B5CF6'
    },
    {
      label: props.city2.name,
      data: city2Data.value,
      borderColor: '#06B6D4',
      backgroundColor: 'rgba(6,182,212,0.2)',
      tension: 0.3,
      fill: true,
      pointBackgroundColor: '#06B6D4'
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: {
    intersect: false,
    mode: 'index'
  },
  scales: {
    x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } },
    y: {
      beginAtZero: true,
      grid: { color: '#F3F4F6' },
      title: { display: true, text: 'Concentration (µg/m³ or ppm)', font: { weight: 'bold' } },
      ticks: { stepSize: 10 }
    }
  },
  plugins: {
    legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold' } } },
    tooltip: {
      backgroundColor: 'rgba(0,0,0,0.8)',
      titleColor: 'white',
      bodyColor: 'white',
      borderColor: '#E5E7EB',
      borderWidth: 1,
      cornerRadius: 8,
      callbacks: {
        label: (context) => {
          const pollutant = pollutants[context.dataIndex]
          return `${context.dataset.label}: ${context.parsed.y} ${pollutant.unit}`
        }
      }
    }
  },
  animation: { duration: 1000, easing: 'easeInOutQuart' }
}
</script>
