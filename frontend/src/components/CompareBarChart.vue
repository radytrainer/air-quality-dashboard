<template>
  <div>
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
  city1: Object,
  city2: Object
})

// Function to get color based on AQI
const getAQIColor = (aqi) => {
  if (aqi <= 50) return 'rgba(16, 185, 129, 0.7)'   // green-500
  if (aqi <= 100) return 'rgba(251, 191, 36, 0.7)'  // yellow-400
  if (aqi <= 150) return 'rgba(249, 115, 22, 0.7)'  // orange-500
  return 'rgba(239, 68, 68, 0.7)'                   // red-500
}

const chartData = computed(() => ({
  labels: ['PM10', 'PM2.5', 'CO', 'NO2', 'SO2', 'O3'],
  datasets: [
    {
      label: props.city1.name,
      data: [
        props.city1.pm10,
        props.city1.pm25,
        props.city1.co,
        props.city1.no2,
        props.city1.so2,
        props.city1.o3
      ],
      backgroundColor: getAQIColor(props.city1.aqi)
    },
    {
      label: props.city2.name,
      data: [
        props.city2.pm10,
        props.city2.pm25,
        props.city2.co,
        props.city2.no2,
        props.city2.so2,
        props.city2.o3
      ],
      backgroundColor: getAQIColor(props.city2.aqi)
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'µg/m³ or ppm'
      }
    }
  },
  plugins: {
    legend: {
      position: 'top'
    },
    title: {
      display: false
    }
  }
}
</script>

<style scoped>
div {
  height: 300px;
}
</style>
