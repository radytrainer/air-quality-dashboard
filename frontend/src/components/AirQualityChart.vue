<template>
  <div class="bg-white rounded-2xl shadow p-6 mt-6">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">AQI Over Time</h2>
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title, Tooltip, Legend,
  LineElement, PointElement,
  CategoryScale, LinearScale
} from 'chart.js'

ChartJS.register(
  Title, Tooltip, Legend,
  LineElement, PointElement,
  CategoryScale, LinearScale
)

const props = defineProps({
  labels: Array,
  values: Array
})

const chartData = {
  labels: props.labels,
  datasets: [
    {
      label: 'AQI',
      backgroundColor: 'rgba(59,130,246,0.1)',
      borderColor: 'rgb(59,130,246)',
      data: props.values,
      tension: 0.4,
      fill: true,
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
}
</script>

<style scoped>
div {
  height: 300px;
}
</style>
