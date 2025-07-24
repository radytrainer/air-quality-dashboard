<template>
  <div class="flex flex-col md:flex-row gap-6 bg-white rounded-xl shadow-lg p-6 mx-auto max-w-6xl">
    <!-- Chart Section -->
    <div class="flex-1">
      <div class="relative w-full h-[360px] md:h-[420px]">
        <Bar :data="chartData" :options="chartOptions" />
      </div>
    </div>
</div>
</template>

<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";
import { Bar } from "vue-chartjs";

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
  city1: Object,
  city2: Object,
});

function getAQIColor(aqi) {
  if (aqi <= 50) return "rgba(34, 197, 94, 0.7)"; // green
  if (aqi <= 100) return "rgba(234, 179, 8, 0.7)"; // yellow
  if (aqi <= 150) return "rgba(249, 115, 22, 0.7)"; // orange
  return "rgba(220, 38, 38, 0.7)"; // red
}

const pollutantLabels = ["PM2.5", "PM10", "CO", "NO₂", "SO₂", "O₃"];

const chartData = {
  labels: pollutantLabels,
  datasets: [
    {
      label: props.city1.name || "City 1",
      backgroundColor: getAQIColor(props.city1.aqi),
      borderRadius: 6,
      data: [
        props.city1.pm25 || 0,
        props.city1.pm10 || 0,
        props.city1.co || 0,
        props.city1.no2 || 0,
        props.city1.so2 || 0,
        props.city1.o3 || 0,
      ],
    },
    {
      label: props.city2.name || "City 2",
      backgroundColor: getAQIColor(props.city2.aqi),
      borderRadius: 6,
      data: [
        props.city2.pm25 || 0,
        props.city2.pm10 || 0,
        props.city2.co || 0,
        props.city2.no2 || 0,
        props.city2.so2 || 0,
        props.city2.o3 || 0,
      ],
    },
  ],
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  layout: {
    padding: 16,
  },
  plugins: {
    legend: {
      position: "top",
      labels: {
        font: {
          size: 14,
          weight: "600",
          family: "Inter, sans-serif",
        },
      },
    },
    tooltip: {
      backgroundColor: "#fff",
      titleColor: "#000",
      bodyColor: "#000",
      borderColor: "#ddd",
      borderWidth: 1,
      callbacks: {
        label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y} µg/m³`,
      },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: "Concentration (µg/m³)",
        font: { size: 14, weight: "600", family: "Inter, sans-serif" },
        color: "#4B5563",
      },
      ticks: {
        font: { size: 13, family: "Inter, sans-serif" },
        color: "#6B7280",
      },
      grid: {
        color: "#E5E7EB",
      },
    },
    x: {
      title: {
        display: true,
        text: "Pollutants",
        font: { size: 14, weight: "600", family: "Inter, sans-serif" },
        color: "#4B5563",
      },
      ticks: {
        font: { size: 13, family: "Inter, sans-serif" },
        color: "#6B7280",
      },
      grid: {
        display: false,
      },
    },
  },
};
</script>

<style scoped>
/* Flex behavior for small screens */
@media (max-width: 768px) {
  .chart-container {
    flex-direction: column;
  }
}
</style>
