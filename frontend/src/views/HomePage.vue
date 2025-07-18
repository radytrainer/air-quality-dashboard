<template>
  <div class="min-h-screen bg-gradient-to-br from-sky-50 to-white text-gray-800">
    <!-- Header -->
    <header class="bg-gradient-to-r from-sky-600 to-blue-500 text-white shadow-md py-6 px-6">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold tracking-wide">🌍 Air Quality Dashboard</h1>
        <p class="text-sm text-white/80 mt-2">Monitor real-time air quality across the globe</p>
        <p class="text-xs text-white/70 mt-1">Last updated: {{ currentTime.toLocaleString() }}</p>
      </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 space-y-10 max-w-7xl mx-auto">
      <!-- Map Section -->
      <section
        class="bg-white rounded-2xl shadow-lg h-96 flex items-center justify-center border-2 border-dashed border-sky-300 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-sky-100 opacity-30"></div>
        <div class="text-center text-gray-400 space-y-3 z-10">
          <MapPinIcon class="w-16 h-16 mx-auto text-sky-400" />
          <p class="text-xl font-semibold">Interactive Map</p>
          <p class="text-sm text-gray-500">Real-time AQI monitoring locations</p>
          <div class="flex justify-center space-x-4 mt-4 flex-wrap gap-2">
            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">Good (0-50)</span>
            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-medium">Moderate (51-100)</span>
            <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-medium">Unhealthy (101-150)</span>
            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-medium">Very Unhealthy (151+)</span>
          </div>
        </div>
      </section>

      <!-- AQI Card Grid -->
      <section>
        <h2 class="text-3xl font-semibold mb-8 text-gray-700 flex items-center">
          📊 Air Quality Index by Location
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(city, index) in cities" :key="index" :class="[
            'hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-gradient-to-br border-0 rounded-lg shadow-md overflow-hidden',
            getBackgroundColor(city.aqi)
          ]">
            <!-- Card Header -->
            <div class="p-4 pb-3">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">{{ city.name }}</h3>
                <span :class="['text-white px-2 py-1 rounded-full text-sm font-medium', getBadgeColor(city.aqi)]">
                  {{ city.aqi }} AQI
                </span>
              </div>
            </div>

            <!-- Card Content -->
            <div class="px-4 pb-4 space-y-4">
              <div class="text-center">
                <p :class="['text-6xl font-extrabold', getColor(city.aqi)]">{{ city.aqi }}</p>
                <p class="text-sm text-gray-600 mt-1 font-medium">{{ getStatus(city.aqi) }}</p>
              </div>

              <!-- PM Readings -->
              <div v-if="city.pm10 && city.pm25" class="grid grid-cols-2 gap-3 text-xs">
                <div class="bg-white/50 rounded-lg p-2 text-center">
                  <p class="font-semibold text-gray-700">PM10</p>
                  <p class="text-gray-600">{{ city.pm10 }} μg/m³</p>
                </div>
                <div class="bg-white/50 rounded-lg p-2 text-center">
                  <p class="font-semibold text-gray-700">PM2.5</p>
                  <p class="text-gray-600">{{ city.pm25 }} μg/m³</p>
                </div>
              </div>

              <!-- Weather Info -->
              <div v-if="city.temperature" class="flex items-center justify-between text-sm bg-white/30 rounded-lg p-3">
                <div class="flex items-center space-x-2">
                  <CloudIcon class="w-4 h-4 text-blue-500" />
                  <span>{{ city.temperature }}°C</span>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                  <div v-if="city.humidity" class="flex items-center space-x-1">
                    <DropletsIcon class="w-3 h-3 text-blue-400" />
                    <span>{{ city.humidity }}%</span>
                  </div>
                  <div v-if="city.windSpeed" class="flex items-center space-x-1">
                    <WindIcon class="w-3 h-3 text-gray-500" />
                    <span>{{ city.windSpeed }} km/h</span>
                  </div>
                  <div v-if="city.uvIndex" class="flex items-center space-x-1">
                    <SunIcon class="w-3 h-3 text-yellow-500" />
                    <span>UV {{ city.uvIndex }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- AQI Scale Reference -->
      <section class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Air Quality Index Scale</h3>
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0 sm:space-x-4">
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-green-500 rounded"></div>
            <span class="text-sm">Good (0-50)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-yellow-500 rounded"></div>
            <span class="text-sm">Moderate (51-100)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-orange-500 rounded"></div>
            <span class="text-sm">Unhealthy for Sensitive (101-150)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-red-500 rounded"></div>
            <span class="text-sm">Unhealthy (151-200)</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-4 h-4 bg-purple-600 rounded"></div>
            <span class="text-sm">Very Unhealthy (201+)</span>
          </div>
        </div>
      </section>
      <!-- Major Air Pollutants Section -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Major Air Pollutants</h2>
            <p class="text-xl text-blue-600 font-medium">Street 96</p>
          </div>

          <!-- Get AQI App Button -->
          <button
            class="flex items-center space-x-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-2xl font-medium transition-all duration-300 hover:scale-105 hover:shadow-lg">
            <Smartphone class="h-5 w-5" />
            <span>Get AQI App</span>
          </button>
        </div>

        <!-- Pollutants Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- PM2.5 Card -->
          <div
            class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fade-in-up group">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                  <!-- PM2.5 Icon -->
                  <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center animate-icon-float">
                    <div class="relative">
                      <div class="w-6 h-6 border-2 border-green-600 rounded-full relative">
                        <div class="absolute inset-1 bg-green-600 rounded-full opacity-60"></div>
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full animate-ping"></div>
                      </div>
                      <div
                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-green-600">
                        2.5</div>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">Particulate Matter</h3>
                    <p class="text-sm text-gray-600">(PM2.5)</p>
                  </div>
                </div>
                <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
              </div>

              <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-green-600 animate-number-pulse">6</span>
                <span class="text-sm text-gray-500 mb-1">μg/m³</span>
              </div>
            </div>
          </div>

          <!-- PM10 Card -->
          <div
            class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fade-in-up group"
            style="animation-delay: 0.1s;">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                  <!-- PM10 Icon -->
                  <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center animate-icon-float">
                    <div class="relative">
                      <div class="w-7 h-7 border-2 border-green-600 rounded-full relative">
                        <div class="absolute inset-1 bg-green-600 rounded-full opacity-60"></div>
                        <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full animate-ping"></div>
                      </div>
                      <div
                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-green-600">
                        10</div>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">Particulate Matter</h3>
                    <p class="text-sm text-gray-600">(PM10)</p>
                  </div>
                </div>
                <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
              </div>

              <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-green-600 animate-number-pulse">25</span>
                <span class="text-sm text-gray-500 mb-1">μg/m³</span>
              </div>
            </div>
          </div>

          <!-- CO Card -->
          <div
            class="bg-white rounded-2xl shadow-lg border-l-4 border-yellow-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fade-in-up group"
            style="animation-delay: 0.2s;">
            <div class="p-6 relative">
              <!-- Warning indicator -->
              <div
                class="absolute top-4 right-4 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center animate-pulse">
                <AlertTriangle class="h-3 w-3 text-white" />
              </div>

              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                  <!-- CO Icon -->
                  <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center animate-icon-float">
                    <div class="relative">
                      <div class="w-6 h-6 bg-yellow-600 rounded-full relative">
                        <div class="absolute inset-1 border border-yellow-800 rounded-full"></div>
                        <div class="absolute top-1 left-1 w-1 h-1 bg-yellow-800 rounded-full"></div>
                        <div class="absolute bottom-1 right-1 w-1 h-1 bg-yellow-800 rounded-full"></div>
                      </div>
                      <div
                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-yellow-600">
                        CO</div>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">Carbon Monoxide</h3>
                    <p class="text-sm text-gray-600">(CO)</p>
                  </div>
                </div>
                <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
              </div>

              <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-yellow-600 animate-number-pulse">34</span>
                <span class="text-sm text-gray-500 mb-1">ppb</span>
              </div>
            </div>
          </div>

          <!-- SO2 Card -->
          <div
            class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fade-in-up group"
            style="animation-delay: 0.3s;">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                  <!-- SO2 Icon -->
                  <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center animate-icon-float">
                    <div class="relative">
                      <div class="w-6 h-6 bg-green-600 rounded-lg relative">
                        <div class="absolute top-1 left-1 w-1 h-1 bg-white rounded-full"></div>
                        <div class="absolute top-1 right-1 w-1 h-1 bg-white rounded-full"></div>
                        <div
                          class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-white rounded-full">
                        </div>
                      </div>
                      <div
                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-green-600">
                        SO₂</div>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">Sulfur Dioxide</h3>
                    <p class="text-sm text-gray-600">(SO2)</p>
                  </div>
                </div>
                <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
              </div>

              <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-green-600 animate-number-pulse">9</span>
                <span class="text-sm text-gray-500 mb-1">ppb</span>
              </div>
            </div>
          </div>

          <!-- NO2 Card -->
          <div
            class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fade-in-up group"
            style="animation-delay: 0.4s;">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                  <!-- NO2 Icon -->
                  <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center animate-icon-float">
                    <div class="relative">
                      <div class="w-6 h-6 bg-green-600 rounded-lg relative">
                        <div
                          class="absolute top-0 left-1/2 transform -translate-x-1/2 w-2 h-2 border border-white rounded-full">
                        </div>
                        <div class="absolute bottom-1 left-1 w-1 h-1 bg-white rounded-full"></div>
                        <div class="absolute bottom-1 right-1 w-1 h-1 bg-white rounded-full"></div>
                      </div>
                      <div
                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-green-600">
                        NO₂</div>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">Nitrogen Dioxide</h3>
                    <p class="text-sm text-gray-600">(NO2)</p>
                  </div>
                </div>
                <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
              </div>

              <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-green-600 animate-number-pulse">13</span>
                <span class="text-sm text-gray-500 mb-1">ppb</span>
              </div>
            </div>
          </div>

          <!-- O3 Card -->
          <div
            class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300 animate-fade-in-up group"
            style="animation-delay: 0.5s;">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                  <!-- O3 Icon -->
                  <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center animate-icon-float">
                    <div class="relative">
                      <div class="w-6 h-6 bg-green-600 rounded-full relative">
                        <div class="absolute inset-1 border border-white rounded-full"></div>
                        <div class="absolute top-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-white rounded-full">
                        </div>
                        <div class="absolute bottom-1 left-1 w-1 h-1 bg-white rounded-full"></div>
                        <div class="absolute bottom-1 right-1 w-1 h-1 bg-white rounded-full"></div>
                      </div>
                      <div
                        class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 text-xs font-bold text-green-600">
                        O₃</div>
                    </div>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">Ozone</h3>
                    <p class="text-sm text-gray-600">(O3)</p>
                  </div>
                </div>
                <ChevronRight class="h-5 w-5 text-gray-400 group-hover:text-gray-600 transition-colors" />
              </div>

              <div class="flex items-end space-x-2">
                <span class="text-3xl font-bold text-green-600 animate-number-pulse">10</span>
                <span class="text-sm text-gray-500 mb-1">ppb</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Info Section -->
      <section class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <!-- Air Quality Ranking -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <Leaf class="h-5 w-5 text-green-600" />
            </div>
            <div>
              <h4 class="font-semibold text-gray-900">Air Quality Ranking</h4>
              <p class="text-sm text-gray-600">
                Currently, Phnom Penh ranks among the cities with the best air quality worldwide.
              </p>
            </div>
          </div>
        </div>

        <!-- AQI Comparison -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <span class="text-2xl font-bold text-yellow-600">1×</span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-900">AQI Comparison</h4>
              <p class="text-sm text-gray-600">
                The AQI level on Street 96 is currently the same as in Phnom Penh, showing no difference.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  MapPin as MapPinIcon,
  Cloud as CloudIcon,
  Droplets as DropletsIcon,
  Wind as WindIcon,
  Sun as SunIcon,
  ChevronRight,
  Smartphone,
  AlertTriangle,
  Leaf
} from 'lucide-vue-next'

// Reactive data
const currentTime = ref(new Date())
const cities = ref([
  {
    name: 'Phnom Penh',
    aqi: 42,
    pm10: 22,
    pm25: 5,
    temperature: 34,
    humidity: 56,
    windSpeed: 26,
    uvIndex: 3
  },
  {
    name: 'Bangkok',
    aqi: 91,
    pm10: 45,
    pm25: 18,
    temperature: 32,
    humidity: 68,
    windSpeed: 12,
    uvIndex: 8
  },
  {
    name: 'New Delhi',
    aqi: 175,
    pm10: 89,
    pm25: 65,
    temperature: 28,
    humidity: 45,
    windSpeed: 8,
    uvIndex: 6
  },
  {
    name: 'Tokyo',
    aqi: 37,
    pm10: 18,
    pm25: 8,
    temperature: 24,
    humidity: 52,
    windSpeed: 15,
    uvIndex: 4
  },
  {
    name: 'Beijing',
    aqi: 129,
    pm10: 67,
    pm25: 42,
    temperature: 22,
    humidity: 38,
    windSpeed: 18,
    uvIndex: 5
  },
  {
    name: 'Jakarta',
    aqi: 78,
    pm10: 35,
    pm25: 22,
    temperature: 31,
    humidity: 72,
    windSpeed: 9,
    uvIndex: 9
  }
])

let timeInterval = null

onMounted(() => {
  timeInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 60000)
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})

function getStatus(aqi) {
  if (aqi <= 50) return 'Good 🌿'
  if (aqi <= 100) return 'Moderate 😐'
  if (aqi <= 150) return 'Unhealthy for Sensitive Groups 😷'
  if (aqi <= 200) return 'Unhealthy 😷'
  return 'Very Unhealthy 🔥'
}

function getColor(aqi) {
  if (aqi <= 50) return 'text-green-500'
  if (aqi <= 100) return 'text-yellow-500'
  if (aqi <= 150) return 'text-orange-500'
  if (aqi <= 200) return 'text-red-500'
  return 'text-purple-600'
}

function getBadgeColor(aqi) {
  if (aqi <= 50) return 'bg-green-500'
  if (aqi <= 100) return 'bg-yellow-500'
  if (aqi <= 150) return 'bg-orange-500'
  if (aqi <= 200) return 'bg-red-500'
  return 'bg-purple-600'
}

function getBackgroundColor(aqi) {
  if (aqi <= 50) return 'bg-gradient-to-br from-green-50 to-green-100'
  if (aqi <= 100) return 'bg-gradient-to-br from-yellow-50 to-yellow-100'
  if (aqi <= 150) return 'bg-gradient-to-br from-orange-50 to-orange-100'
  if (aqi <= 200) return 'bg-gradient-to-br from-red-50 to-red-100'
  return 'bg-gradient-to-br from-purple-50 to-purple-100'
}
</script>


<style scoped>
/* Custom animations and transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.hover\:shadow-xl:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.hover\:-translate-y-1:hover {
  transform: translateY(-0.25rem);
}

/* Responsive text sizing */
@media (max-width: 640px) {
  .text-6xl {
    font-size: 3rem;
    line-height: 1;
  }
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Custom animation keyframes */
@keyframes fade-in-up {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }

  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes icon-float {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-4px);
  }
}

@keyframes number-pulse {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.05);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.5s ease forwards;
}

.animate-icon-float {
  animation: icon-float 2s ease-in-out infinite;
}

.animate-number-pulse {
  animation: number-pulse 2s ease-in-out infinite;
}
</style>
