<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Air Quality Dashboard</h1>
    <div class="flex flex-wrap gap-4">
      <AirQualityCard :aqi="currentAQI" />
    </div>
    <AirQualityChart :labels="chartLabels" :values="chartValues" />
  </div>
</template>

<script setup>
import AirQualityCard from '@/components/AirQualityCard.vue'
import AirQualityChart from '@/components/AirQualityChart.vue'

const currentAQI = 82

const chartLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
const chartValues = [78, 82, 85, 79, 91, 87, 82]
</script>
