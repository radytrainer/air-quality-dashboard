<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-4">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Header -->
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">
          Air Quality Monitor
        </h1>
        <p class="text-slate-600 text-sm">Real-time global air pollution tracking</p>
      </div>

      <!-- Top 4 Metric Boxes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Locations -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4 hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Total Locations</p>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ aqiData.length }}</p>
              <p class="text-xs text-emerald-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  Active
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-1">
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <button @click="exportData('total')" class="p-1 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Highest Pollution -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4 hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Highest Pollution</p>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ highestPollution?.aqi || 'N/A' }}</p>
              <p class="text-xs text-red-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  {{ highestPollution?.name || 'No data' }}
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-1">
              <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
              </div>
              <button @click="exportData('highest')" class="p-1 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Lowest Pollution -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4 hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Lowest Pollution</p>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ lowestPollution?.aqi || 'N/A' }}</p>
              <p class="text-xs text-emerald-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  {{ lowestPollution?.name || 'No data' }}
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-1">
              <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <button @click="exportData('lowest')" class="p-1 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Average AQI -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4 hover:shadow-lg transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Average AQI</p>
              </div>
              <p class="text-2xl font-bold text-slate-900 mb-1">{{ averageAQI }}</p>
              <p class="text-xs text-indigo-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  Global average
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-1">
              <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <button @click="exportData('average')" class="p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- AQI Trend Chart -->
        <div class="lg:col-span-2 bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-xl font-bold text-slate-900 mb-1">Pollution Trends</h3>
              <p class="text-slate-600 text-sm">Top 10 vs Bottom 10 locations</p>
            </div>
            <div class="flex items-center space-x-3">
              <select v-model="chartMetric" @change="updateChart" class="bg-white/80 border border-slate-200 rounded-lg px-3 py-1 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="aqi">AQI</option>
                <option value="pm25">PM2.5</option>
                <option value="pm10">PM10</option>
                <option value="temperature">Temperature</option>
                <option value="no2">NO2</option>
                <option value="co">CO</option>
                <option value="o3">O3</option>
                <option value="humidity">Humidity</option>
                <option value="pressure">Pressure</option>
                <option value="wind_speed">Wind Speed</option>
              </select>
              <div class="flex items-center space-x-3 text-xs">
                <div class="flex items-center space-x-1">
                  <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                  <span class="text-slate-600 font-medium">High: {{ chartHighValue }}</span>
                </div>
                <div class="flex items-center space-x-1">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                  <span class="text-slate-600 font-medium">Low: {{ chartLowValue }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="h-64 relative">
            <canvas ref="chartCanvas" class="w-full h-full"></canvas>
          </div>
        </div>

        <!-- Top 5 Locations by Pollution -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-3">
          <div class="flex items-center justify-between mb-3">
            <div>
              <h3 class="text-lg font-bold text-slate-900 mb-1">Top Pollution</h3>
              <p class="text-slate-600 text-xs">Updates every 30s</p>
            </div>
            <div class="flex space-x-2">
              <select v-model="selectedMetric" class="bg-white/80 border border-slate-200 rounded-lg px-2 py-1 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="aqi">AQI</option>
                <option value="pm25">PM2.5</option>
                <option value="pm10">PM10</option>
                <option value="no2">NO2</option>
                <option value="co">CO</option>
                <option value="o3">O3</option>
                <option value="temperature">Temperature</option>
                <option value="humidity">Humidity</option>
                <option value="pressure">Pressure</option>
                <option value="wind_speed">Wind Speed</option>
              </select>
              <select v-model="pollutionType" @change="updatePollutionList" class="bg-white/80 border border-slate-200 rounded-lg px-2 py-1 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="high">High</option>
                <option value="low">Low</option>
              </select>
            </div>
          </div>
          <div class="space-y-2">
            <div v-for="(location, index) in pollutionLocations" :key="location.id" class="bg-gradient-to-r from-slate-50 to-white rounded-lg p-2 border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-2">
                  <div class="flex-shrink-0">
                    <img v-if="location.flag" :src="location.flag" :alt="location.name" class="w-6 h-4 object-cover rounded shadow-sm">
                    <div class="w-6 h-4 bg-gradient-to-br from-slate-200 to-slate-300 rounded" v-else></div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ truncateName(location.name) }}</p>
                  </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                  <div class="text-sm font-bold text-slate-900">{{ formatMetricValue(getMetricValue(location)) }}</div>
                  <div class="w-12 bg-slate-200 rounded-full h-1.5 mt-1">
                    <div 
                      class="h-1.5 rounded-full transition-all duration-500"
                      :class="getPollutionColor(getMetricValue(location))"
                      :style="{ width: `${Math.min(100, (getMetricValue(location) / getMaxMetricValue()) * 100)}%` }"
                    ></div>
                  </div>
                  <div class="text-xs text-slate-500 mt-1 font-medium">
                    {{ Math.round((getMetricValue(location) / getMaxMetricValue()) * 100) }}%
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- AQI Distribution -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">AQI Distribution</h3>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="category in aqiCategories" :key="category.name" class="bg-gradient-to-br from-white/80 to-slate-50 rounded-lg p-3 border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></div>
                <span class="text-xs font-medium text-slate-700">{{ category.name }}</span>
              </div>
              <div class="mt-2 text-lg font-bold text-slate-900">{{ category.count }}</div>
              <div class="w-full bg-slate-200 h-2 rounded-full mt-2">
                <div class="h-2 rounded-full transition-all duration-500" :style="{ width: `${(category.count / aqiData.length * 100) || 0}%`, backgroundColor: category.color }"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Environmental Metrics -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">Environmental Metrics</h3>
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
          </div>
          <div class="grid grid-cols-1 gap-3">
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Temperature</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ averageTemperature }} °C</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Humidity</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ averageHumidity }} %</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Wind Speed</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ averageWindSpeed }} m/s</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Pressure</span>
              </div>
              <span class="text-sm font-bold text-slate-900">{{ averagePressure }} hPa</span>
            </div>
          </div>
        </div>

        <!-- Average Pollution Levels -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">Average Pollution Levels</h3>
            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
          </div>
          <div class="space-y-2 text-xs">
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">PM2.5</span>
              </div>
              <span class="font-bold text-slate-900">{{ averagePM25 }} µg/m³</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">PM10</span>
              </div>
              <span class="font-bold text-slate-900">{{ averagePM10 }} µg/m³</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">NO2</span>
              </div>
              <span class="font-bold text-slate-900">{{ averageNO2 }} ppb</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-gray-500 to-gray-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">CO</span>
              </div>
              <span class="font-bold text-slate-900">{{ averageCO }} ppm</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">O3</span>
              </div>
              <span class="font-bold text-slate-900">{{ averageO3 }} ppb</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">Temperature</span>
              </div>
              <span class="font-bold text-slate-900">{{ averageTemperature }} °C</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">Humidity</span>
              </div>
              <span class="font-bold text-slate-900">{{ averageHumidity }} %</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">Wind Speed</span>
              </div>
              <span class="font-bold text-slate-900">{{ averageWindSpeed }} m/s</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">Pressure</span>
              </div>
              <span class="font-bold text-slate-900">{{ averagePressure }} hPa</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

// Reactive data
const aqiData = ref([])
const selectedMetric = ref('aqi')
const chartMetric = ref('aqi')
const pollutionType = ref('high')
const chartCanvas = ref(null)
const pieCanvas = ref(null)
let chart = null
let pieChart = null
const highSortedRef = ref([])
const lowSortedRef = ref([])

// Computed properties for metrics
const highestPollution = computed(() => {
  if (!aqiData.value.length) return null
  return aqiData.value.reduce((max, current) => {
    const currentAqi = parseInt(current.aqi) || 0
    const maxAqi = parseInt(max.aqi) || 0
    return currentAqi > maxAqi ? current : max
  })
})

const lowestPollution = computed(() => {
  if (!aqiData.value.length) return null
  const validData = aqiData.value.filter(item => parseInt(item.aqi) > 0)
  if (!validData.length) return null
  return validData.reduce((min, current) => {
    const currentAqi = parseInt(current.aqi) || Infinity
    const minAqi = parseInt(min.aqi) || Infinity
    return currentAqi < minAqi ? current : min
  })
})

const averageAQI = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => parseInt(item.aqi) > 0)
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + (parseInt(item.aqi) || 0), 0)
  return Math.round(sum / validData.length)
})

const pollutionLocations = computed(() => {
  if (!aqiData.value.length) return []
  const filteredData = aqiData.value.filter(item => parseInt(item.aqi) > 0)
  return pollutionType.value === 'high'
    ? [...filteredData].sort((a, b) => (parseInt(b[selectedMetric.value]) || 0) - (parseInt(a[selectedMetric.value]) || 0)).slice(0, 5)
    : [...filteredData].sort((a, b) => (parseInt(a[selectedMetric.value]) || 0) - (parseInt(b[selectedMetric.value]) || 0)).slice(0, 5)
})

const aqiCategories = computed(() => {
  const categories = [
    { name: 'Good', range: [0, 50], color: '#10b981', count: 0 },
    { name: 'Moderate', range: [51, 100], color: '#f59e0b', count: 0 },
    { name: 'Unhealthy SG', range: [101, 150], color: '#f97316', count: 0 },
    { name: 'Unhealthy', range: [151, 200], color: '#ef4444', count: 0 },
    { name: 'Very Unhealthy', range: [201, 300], color: '#dc2626', count: 0 },
    { name: 'Hazardous', range: [301, Infinity], color: '#991b1b', count: 0 }
  ]
  
  aqiData.value.forEach(item => {
    const aqi = parseInt(item.aqi) || 0
    const category = categories.find(cat => aqi >= cat.range[0] && aqi <= cat.range[1])
    if (category) category.count++
  })
  
  return categories
})

const averageTemperature = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => item.temperature && item.temperature !== 'N/A')
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + (parseFloat(item.temperature) || 0), 0)
  return Math.round(sum / validData.length)
})

const averageHumidity = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => item.humidity && item.humidity !== 'N/A')
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + (parseFloat(item.humidity) || 0), 0)
  return Math.round(sum / validData.length)
})

const averageWindSpeed = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => item.wind_speed && item.wind_speed !== 'N/A')
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + (parseFloat(item.wind_speed) || 0), 0)
  return Math.round(sum / validData.length * 10) / 10
})

const averagePressure = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => item.pressure && item.pressure !== 'N/A')
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + (parseFloat(item.pressure) || 0), 0)
  return Math.round(sum / validData.length)
})

const averagePM25 = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => !isNaN(parseFloat(item.pm25)))
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + parseFloat(item.pm25), 0)
  return (sum / validData.length).toFixed(1)
})

const averagePM10 = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => !isNaN(parseFloat(item.pm10)))
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + parseFloat(item.pm10), 0)
  return (sum / validData.length).toFixed(1)
})

const averageNO2 = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => !isNaN(parseFloat(item.no2)))
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + parseFloat(item.no2), 0)
  return (sum / validData.length).toFixed(1)
})

const averageCO = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => !isNaN(parseFloat(item.co)))
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + parseFloat(item.co), 0)
  return (sum / validData.length).toFixed(1)
})

const averageO3 = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const validData = aqiData.value.filter(item => !isNaN(parseFloat(item.o3)))
  if (!validData.length) return 'N/A'
  const sum = validData.reduce((acc, item) => acc + parseFloat(item.o3), 0)
  return (sum / validData.length).toFixed(1)
})

const dataQualityPercentage = computed(() => {
  if (!aqiData.value.length) return 0
  const completeData = aqiData.value.filter(item => 
    item.aqi && item.aqi !== 'N/A' && 
    item.temperature && item.temperature !== 'N/A' &&
    item.humidity && item.humidity !== 'N/A'
  )
  return Math.round((completeData.length / aqiData.value.length) * 100)
})

const lastUpdated = computed(() => {
  return new Date().toLocaleString()
})

const chartHighValue = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const values = aqiData.value
    .map(item => parseFloat(item[chartMetric.value]) || 0)
    .filter(val => val > 0)
  return values.length ? Math.max(...values).toFixed(3).replace(/\.?0+$/, '') : 'N/A'
})

const chartLowValue = computed(() => {
  if (!aqiData.value.length) return 'N/A'
  const values = aqiData.value
    .map(item => parseFloat(item[chartMetric.value]) || 0)
    .filter(val => val > 0)
  return values.length ? Math.min(...values).toFixed(3).replace(/\.?0+$/, '') : 'N/A'
})

const goodAqiCount = computed(() => {
  return aqiData.value.filter(item => parseInt(item.aqi) <= 50).length
})

const badAqiCount = computed(() => {
  return aqiData.value.filter(item => parseInt(item.aqi) > 50).length
})

const goodAqiPercentage = computed(() => {
  if (!aqiData.value.length) return 0
  return Math.round((goodAqiCount.value / aqiData.value.length) * 100)
})

const temperaturePercentage = computed(() => 35)
const humidityPercentage = computed(() => 40)
const windPercentage = computed(() => 25)

const dataQualityMetrics = computed(() => ({
  completeness: '95%',
  accuracy: '88%',
  freshness: '92%',
  coverage: '85%'
}))

const getMetricValue = (location) => {
  const value = location[selectedMetric.value]
  return value && value !== 'N/A' ? parseFloat(value) || 0 : 0
}

const formatMetricValue = (value) => {
  return Number.isFinite(value) ? value.toFixed(3).replace(/\.?0+$/, '') : value
}

const truncateName = (name) => {
  if (name.length > 20) {
    return name.length > 23 ? `${name.slice(0, 20)}...` : name
  }
  return name
}

const getMaxMetricValue = () => {
  if (!aqiData.value.length) return 100
  const values = aqiData.value.map(item => getMetricValue(item)).filter(val => val > 0)
  return Math.max(...values, 100)
}

const getPollutionColor = (value) => {
  if (selectedMetric.value === 'aqi') {
    if (value <= 50) return 'bg-green-500'
    if (value <= 100) return 'bg-yellow-500'
    if (value <= 150) return 'bg-orange-500'
    if (value <= 200) return 'bg-red-500'
    return 'bg-red-700'
  }
  return 'bg-blue-500'
}

const updatePollutionList = () => {
  // Trigger re-computation of pollutionLocations when pollutionType changes
}

const fetchAQIData = async () => {
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/aqi')
    let cities = Array.isArray(data.data) ? data.data : []

    try {
      const ppRes = await axios.get('http://127.0.0.1:8000/api/air-quality/phnom-penh')
      const pp = ppRes.data || {}
      const phnomPenh = {
        id: 9999,
        name: 'Phnom Penh',
        lat: 11.562108,
        lon: 104.888535,
        aqi: pp.AQI ?? 'N/A',
        pm25: pp.PM2_5 ?? 'N/A',
        pm10: pp.PM10 ?? 'N/A',
        no2: pp.NO2 ?? 'N/A',
        co: pp.CO ?? 'N/A',
        o3: pp.O3 ?? 'N/A',
        temperature: pp.Temp_C ?? 'N/A',
        humidity: pp.Humidity_percent ?? 'N/A',
        pressure: pp.Pressure_hPa ?? 'N/A',
        wind_speed: pp.Wind_m_s ?? 'N/A',
        flag: 'https://flagcdn.com/w160/kh.png',
      }
      cities = cities.filter(c => c.name !== 'Phnom Penh')
      cities.push(phnomPenh)
    } catch (err) {
      console.error('Failed to fetch Phnom Penh AQI:', err)
    }

    aqiData.value = cities
    updateChart()
  } catch (err) {
    console.error('Error fetching AQI data:', err)
  }
}

const initChart = () => {
  if (!chartCanvas.value) return
  
  const ctx = chartCanvas.value.getContext('2d')
  
  chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [
        {
          label: 'Top 10 High Pollution',
          data: [],
          borderColor: '#ef4444',
          backgroundColor: 'rgba(239, 68, 68, 0.1)',
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#ef4444',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          borderWidth: 2
        },
        {
          label: 'Bottom 10 Low Pollution',
          data: [],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          fill: false,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          borderWidth: 2,
          borderDash: [6, 3]
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            usePointStyle: true,
            padding: 15,
            font: {
              size: 11,
              weight: '600'
            },
            color: '#475569'
          }
        },
        tooltip: {
          backgroundColor: 'rgba(255, 255, 255, 0.95)',
          titleColor: '#1e293b',
          bodyColor: '#475569',
          borderColor: '#e2e8f0',
          borderWidth: 1,
          cornerRadius: 8,
          padding: 10,
          displayColors: true,
          titleFont: {
            size: 12,
            weight: '600'
          },
          bodyFont: {
            size: 11
          },
          callbacks: {
            title: function(tooltipItems) {
              return `Rank ${tooltipItems[0].dataIndex + 1}`
            },
            label: function(context) {
              const sorted = context.datasetIndex === 0 ? highSortedRef.value : lowSortedRef.value
              const item = sorted[context.dataIndex] || {}
              return `${context.dataset.label}: ${context.raw} (${item.name || 'Unknown'})`
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#64748b',
            font: {
              size: 11,
              weight: '500'
            },
            maxRotation: 45
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(148, 163, 184, 0.1)',
            drawBorder: false
          },
          ticks: {
            color: '#64748b',
            font: {
              size: 11,
              weight: '500'
            },
            padding: 6
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  })
}

const updateChart = () => {
  if (!chart || !aqiData.value.length) return
  
  highSortedRef.value = [...aqiData.value]
    .filter(item => !isNaN(parseFloat(item[chartMetric.value])))
    .sort((a, b) => parseFloat(b[chartMetric.value]) - parseFloat(a[chartMetric.value]))
    .slice(0, 10)
  
  lowSortedRef.value = [...aqiData.value]
    .filter(item => !isNaN(parseFloat(item[chartMetric.value])))
    .sort((a, b) => parseFloat(a[chartMetric.value]) - parseFloat(b[chartMetric.value]))
    .slice(0, 10)
  
  const labels = Array(10).fill(0).map((_, i) => `Rank ${i + 1}`)
  const highData = highSortedRef.value.map(item => parseFloat(item[chartMetric.value]))
  const lowData = lowSortedRef.value.map(item => parseFloat(item[chartMetric.value]))
  
  chart.data.labels = labels
  chart.data.datasets[0].data = highData
  chart.data.datasets[0].label = `Highest 10 ${chartMetric.value.toUpperCase()}`
  chart.data.datasets[1].data = lowData
  chart.data.datasets[1].label = `Lowest 10 ${chartMetric.value.toUpperCase()}`
  chart.update('active')
}

const initPieChart = () => {
  if (!pieCanvas.value) return
  
  const ctx = pieCanvas.value.getContext('2d')
  
  pieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Temperature', 'Humidity', 'Wind Speed', 'Pressure'],
      datasets: [{
        data: [35, 40, 25, 20],
        backgroundColor: [
          'rgba(59, 130, 246, 0.8)',
          'rgba(16, 185, 129, 0.8)',
          'rgba(249, 115, 22, 0.8)',
          'rgba(99, 102, 241, 0.8)'
        ],
        borderColor: [
          '#3b82f6',
          '#10b981',
          '#f97316',
          '#6366f1'
        ],
        borderWidth: 2,
        hoverBackgroundColor: [
          'rgba(59, 130, 246, 1)',
          'rgba(16, 185, 129, 1)',
          'rgba(249, 115, 22, 1)',
          'rgba(99, 102, 241, 1)'
        ],
        hoverBorderWidth: 3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(255, 255, 255, 0.95)',
          titleColor: '#1e293b',
          bodyColor: '#475569',
          borderColor: '#e2e8f0',
          borderWidth: 1,
          cornerRadius: 8,
          padding: 8
        }
      },
      cutout: '65%',
      animation: {
        animateRotate: true,
        duration: 800
      }
    }
  })
}

const exportData = (type) => {
  let data = []
  let filename = ''
  let headers = ['name', 'aqi', 'pm25', 'pm10', 'no2', 'co', 'o3', 'temperature', 'humidity', 'pressure', 'wind_speed']
  
  const escapeCsvValue = (value) => {
    if (value == null) return ''
    const str = String(value)
    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str
  }
  
  switch(type) {
    case 'total':
      data = aqiData.value.map(item => 
        headers.map(header => escapeCsvValue(item[header])).join(',')
      )
      data.unshift(headers.join(','))
      filename = 'total-locations.csv'
      break
    case 'highest':
      if (highestPollution.value) {
        data = [headers.map(header => escapeCsvValue(highestPollution.value[header])).join(',')]
        data.unshift(headers.join(','))
      }
      filename = 'highest-pollution.csv'
      break
    case 'lowest':
      if (lowestPollution.value) {
        data = [headers.map(header => escapeCsvValue(lowestPollution.value[header])).join(',')]
        data.unshift(headers.join(','))
      }
      filename = 'lowest-pollution.csv'
      break
    case 'average':
      data = [
        `average_aqi,${escapeCsvValue(averageAQI.value)},timestamp,${escapeCsvValue(new Date().toISOString())}`,
        `average_temperature,${escapeCsvValue(averageTemperature.value)},timestamp,${escapeCsvValue(new Date().toISOString())}`,
        `average_humidity,${escapeCsvValue(averageHumidity.value)},timestamp,${escapeCsvValue(new Date().toISOString())}`,
        `average_wind_speed,${escapeCsvValue(averageWindSpeed.value)},timestamp,${escapeCsvValue(new Date().toISOString())}`,
        `average_pressure,${escapeCsvValue(averagePressure.value)},timestamp,${escapeCsvValue(new Date().toISOString())}`
      ]
      data.unshift('metric,value,timestamp')
      filename = 'average-metrics.csv'
      break
  }
  
  const blob = new Blob([data.join('\n')], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = filename
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}

onMounted(async () => {
  await nextTick()
  initChart()
  initPieChart()
  fetchAQIData()
  
  setInterval(fetchAQIData, 30000)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

* {
  font-family: 'Inter', sans-serif;
}

.bg-gradient-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

.hover\:shadow-lg:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style>