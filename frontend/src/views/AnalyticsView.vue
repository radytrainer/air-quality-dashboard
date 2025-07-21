<template>
  <div class="min-h-screen bg-gradient-to-br from-white-100 to-cyan-100 relative overflow-hidden">
    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="i in 10" :key="i" :class="`absolute w-1 h-1 bg-white rounded-full opacity-20 animate-float`"
        :style="{
          left: Math.random() * 100 + '%',
          top: Math.random() * 100 + '%',
          animationDelay: Math.random() * 10 + 's',
          animationDuration: (Math.random() * 10 + 10) + 's'
        }">
      </div>
    </div>

    <div class="relative z-10 p-4 md:p-6">
      <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header with Real-time Clock -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
          <div class="text-gray-900">
            <h1
              class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 bg-clip-text text-transparent">
              Air Quality Analytics
            </h1>
            <p class="text-gray-600 mt-2 text-lg">Real-time pollution monitoring & insights</p>
            <div class="flex items-center gap-4 mt-3">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-teal-400 rounded-full animate-ping"></div>
                <span class="text-teal-400 font-medium">Live Data</span>
              </div>
              <div class="text-gray-500">{{ currentTime }}</div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <select v-model="timeRange"
              class="px-4 py-3 bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-cyan-400 transition-all duration-300">
              <option value="24h" class="text-gray-900">Last 24h</option>
              <option value="7d" class="text-gray-900">Last 7 days</option>
              <option value="30d" class="text-gray-900">Last 30 days</option>
              <option value="90d" class="text-gray-900">Last 90 days</option>
            </select>
            <button
              class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-teal-500 text-white rounded-xl hover:from-cyan-600 hover:to-teal-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
              <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Custom Range
            </button>
          </div>
        </div>

        <!-- Real-time Status Cards with Animations -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div
            class="group relative overflow-hidden bg-white/80 backdrop-blur-md rounded-2xl p-6 border border-gray-200 hover:bg-white/90 transition-all duration-500 transform hover:scale-105 hover:-translate-y-2">
            <div
              class="absolute inset-0 bg-gradient-to-br from-blue-200/20 to-cyan-200/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-medium">Current AQI</h3>
                <div class="p-2 bg-blue-200/20 rounded-lg">
                  <svg class="h-6 w-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>
              <div class="text-4xl font-bold text-gray-900 mb-2 transition-all duration-300">
                {{ animatedAQI }}
              </div>
              <div
                :class="`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${aqiStatus.bgColor} ${aqiStatus.textColor} animate-pulse`">
                <div class="w-2 h-2 rounded-full bg-current mr-2 animate-ping"></div>
                {{ aqiStatus.status }}
              </div>
              <div class="mt-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div
                  class="h-full bg-gradient-to-r from-blue-300 to-cyan-300 rounded-full transition-all duration-1000 ease-out"
                  :style="{ width: (currentAQI / 200) * 100 + '%' }"></div>
              </div>
            </div>
          </div>

          <div
            class="group relative overflow-hidden bg-white/80 backdrop-blur-md rounded-2xl p-6 border border-gray-200 hover:bg-white/90 transition-all duration-500 transform hover:scale-105 hover:-translate-y-2">
            <div
              class="absolute inset-0 bg-gradient-to-br from-teal-200/20 to-blue-200/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-medium">PM2.5 Level</h3>
                <div class="p-2 bg-teal-200/20 rounded-lg">
                  <svg class="h-6 w-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
              </div>
              <div class="text-4xl font-bold text-gray-900 mb-2">{{ animatedPM25 }}</div>
              <p class="text-gray-600 text-sm mb-3">μg/m³</p>
              <div class="flex items-center">
                <svg v-if="avgPM25 > 35" class="h-5 w-5 text-red-400 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <svg v-else class="h-5 w-5 text-teal-400 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                <span :class="`text-sm font-medium ${avgPM25 > 35 ? 'text-red-400' : 'text-teal-400'}`">
                  {{ avgPM25 > 35 ? 'Above WHO limit' : 'Within WHO limit' }}
                </span>
              </div>
            </div>
          </div>

          <div
            class="group relative overflow-hidden bg-white/80 backdrop-blur-md rounded-2xl p-6 border border-gray-200 hover:bg-white/90 transition-all duration-500 transform hover:scale-105 hover:-translate-y-2">
            <div
              class="absolute inset-0 bg-gradient-to-br from-cyan-200/20 to-blue-200/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-medium">PM10 Level</h3>
                <div class="p-2 bg-cyan-200/20 rounded-lg">
                  <svg class="h-6 w-6 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </div>
              </div>
              <div class="text-4xl font-bold text-gray-900 mb-2">{{ animatedPM10 }}</div>
              <p class="text-gray-600 text-sm mb-3">μg/m³</p>
              <div class="flex items-center">
                <svg v-if="avgPM10 > 45" class="h-5 w-5 text-red-400 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                <svg v-else class="h-5 w-5 text-teal-400 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                <span :class="`text-sm font-medium ${avgPM10 > 45 ? 'text-red-400' : 'text-teal-400'}`">
                  {{ avgPM10 > 45 ? 'Above WHO limit' : 'Within WHO limit' }}
                </span>
              </div>
            </div>
          </div>

          <div
            class="group relative overflow-hidden bg-white/80 backdrop-blur-md rounded-2xl p-6 border border-gray-200 hover:bg-white/90 transition-all duration-500 transform hover:scale-105 hover:-translate-y-2">
            <div
              class="absolute inset-0 bg-gradient-to-br from-red-200/20 to-pink-200/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
            </div>
            <div class="relative z-10">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-medium">Health Alerts</h3>
                <div class="p-2 bg-red-200/20 rounded-lg">
                  <svg class="h-6 w-6 text-red-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <div class="text-4xl font-bold text-gray-900 mb-2">{{ animatedAlerts }}</div>
              <p class="text-gray-600 text-sm mb-3">This week</p>
              <div
                class="inline-flex items-center px-3 py-1 bg-red-200/20 text-red-300 rounded-full text-sm font-medium animate-pulse">
                <div class="w-2 h-2 rounded-full bg-red-300 mr-2 animate-ping"></div>
                High Risk Days
              </div>
            </div>
          </div>
        </div>

        <!-- Interactive Tabs with Smooth Transitions -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-gray-200 overflow-hidden">
          <div class="border-b border-gray-200">
            <nav class="flex space-x-0">
              <button v-for="(tab, index) in tabs" :key="tab.id" @click="activeTab = tab.id" :class="`relative px-8 py-6 font-medium text-sm transition-all duration-300 ${activeTab === tab.id
                ? 'text-gray-900 bg-white/80'
                : 'text-gray-500 hover:text-gray-900 hover:bg-white/50'
                }`">
                <span class="relative z-10 flex items-center gap-2">
                  <component :is="tab.icon" class="h-5 w-5" />
                  {{ tab.name }}
                </span>
                <div v-if="activeTab === tab.id"
                  class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 to-teal-400 animate-pulse">
                </div>
              </button>
            </nav>
          </div>

          <!-- Dynamic Tab Content with Animations -->
          <div class="p-8">
            <transition name="fade" mode="out-in">
              <!-- Trends Tab -->
              <div v-if="activeTab === 'trends'" class="space-y-8" key="trends">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <!-- Interactive AQI Chart -->
                  <div
                    class="group bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 hover:bg-white/70 transition-all duration-500">
                    <div class="flex items-center justify-between mb-6">
                      <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Air Quality Index Trend</h3>
                        <p class="text-gray-600">Real-time AQI monitoring</p>
                      </div>
                      <div class="p-3 bg-gradient-to-br from-blue-200/20 to-cyan-200/20 rounded-xl">
                        <svg class="h-6 w-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                      </div>
                    </div>

                    <!-- Animated Chart Visualization -->
                    <div
                      class="h-64 relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-100/10 to-cyan-100/10 border border-gray-200">
                      <div class="absolute inset-4">
                        <!-- Animated Grid Lines -->
                        <div class="absolute inset-0 opacity-20">
                          <div v-for="i in 5" :key="i" class="absolute w-full border-t border-gray-300"
                            :style="{ top: (i * 20) + '%' }">
                          </div>
                          <div v-for="i in 7" :key="'v' + i" class="absolute h-full border-l border-gray-300"
                            :style="{ left: (i * 14.28) + '%' }">
                          </div>
                        </div>

                        <!-- Animated Line Chart -->
                        <svg class="w-full h-full" viewBox="0 0 400 200">
                          <defs>
                            <linearGradient id="aqiGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                              <stop offset="0%" style="stop-color:#38BDF8;stop-opacity:0.8" />
                              <stop offset="100%" style="stop-color:#14B8A6;stop-opacity:0.2" />
                            </linearGradient>
                          </defs>
                          <path :d="aqiChartPath" fill="url(#aqiGradient)" class="animate-draw" />
                          <path :d="aqiLinePath" fill="none" stroke="#38BDF8" stroke-width="3" class="animate-draw" />
                          <!-- Animated Data Points -->
                          <circle v-for="(point, index) in aqiDataPoints" :key="index" :cx="point.x" :cy="point.y" r="4"
                            fill="#38BDF8" class="animate-pulse cursor-pointer hover:r-6 transition-all duration-300"
                            :style="{ animationDelay: index * 0.1 + 's' }" />
                        </svg>
                      </div>

                      <!-- Real-time Data Overlay -->
                      <div class="absolute top-4 right-4 bg-gray-900/50 backdrop-blur-sm rounded-lg p-3">
                        <div class="text-gray-100 text-sm font-medium">Current: {{ currentAQI }}</div>
                        <div :class="`text-xs ${aqiStatus.textColor}`">{{ aqiStatus.status }}</div>
                      </div>
                    </div>
                  </div>

                  <!-- Interactive PM Levels Chart -->
                  <div
                    class="group bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 hover:bg-white/70 transition-all duration-500">
                    <div class="flex items-center justify-between mb-6">
                      <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Particulate Matter</h3>
                        <p class="text-gray-600">PM2.5 & PM10 levels</p>
                      </div>
                      <div class="p-3 bg-gradient-to-br from-teal-200/20 to-blue-200/20 rounded-xl">
                        <svg class="h-6 w-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                      </div>
                    </div>

                    <!-- Animated Bar Chart -->
                    <div
                      class="h-64 relative overflow-hidden rounded-xl bg-gradient-to-br from-teal-100/10 to-blue-100/10 border border-gray-200">
                      <div class="absolute inset-4 flex items-end justify-around">
                        <!-- Use item.date as key to ensure uniqueness -->
                        <div v-for="item in airQualityData.slice(-7)" :key="item.date"
                          class="flex flex-col items-center space-y-2">
                          <!-- PM2.5 Bar -->
                          <div class="relative">
                            <div
                              class="w-6 bg-gradient-to-t from-blue-300 to-blue-500 rounded-t transition-all duration-1000 ease-out"
                              :style="{
                                height: (item.pm25 / 80) * 150 + 'px',
                                animationDelay: (airQualityData.length - 7 + airQualityData.indexOf(item)) * 0.1 + 's'
                              }"></div>
                            <div
                              class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-900 font-medium">
                              {{ item.pm25 }}
                            </div>
                          </div>

                          <!-- PM10 Bar -->
                          <div class="relative">
                            <div
                              class="w-6 bg-gradient-to-t from-teal-300 to-teal-500 rounded-t transition-all duration-1000 ease-out"
                              :style="{
                                height: (item.pm10 / 100) * 76 + 'px',
                                animationDelay: (airQualityData.length - 7 + airQualityData.indexOf(item)) * 0.3 + 0.2 + 's'
                              }"></div>
                            <div
                              class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-900 font-medium">
                              {{ item.pm10 }}
                            </div>
                          </div>

                          <div class="text-xs text-gray-600 transform rotate-45 mt-2">
                            {{ item.date.slice(-2) }}
                          </div>
                        </div>
                      </div>

                      <!-- Legend -->
                      <div class="absolute top-4 left-4 space-y-2">
                        <div class="flex items-center gap-2">
                          <div class="w-3 h-3 bg-blue-300 rounded"></div>
                          <span class="text-gray-900 text-xs">PM2.5</span>
                        </div>
                        <div class="flex items-center gap-2">
                          <div class="w-3 h-3 bg-teal-300 rounded"></div>
                          <span class="text-gray-900 text-xs">PM10</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Weekly Summary Tab -->
              <div v-else-if="activeTab === 'weekly'" class="space-y-8" key="weekly">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <!-- Weekly Trends -->
                  <div class="lg:col-span-2 bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Weekly Performance</h3>

                    <div class="space-y-6">
                      <div v-for="(week, index) in weeklyData" :key="week.week"
                        class="group p-4 bg-white/30 rounded-xl border border-gray-200 hover:bg-white/40 transition-all duration-300"
                        :style="{ animationDelay: index * 0.1 + 's' }">
                        <div class="flex items-center justify-between mb-3">
                          <h4 class="text-gray-900 font-medium">{{ week.week }}</h4>
                          <div
                            :class="`px-3 py-1 rounded-full text-xs font-medium ${getWeekStatus(week.aqi).bgColor} ${getWeekStatus(week.aqi).textColor}`">
                            {{ getWeekStatus(week.aqi).status }}
                          </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                          <div class="text-center">
                            <div class="text-2xl font-bold text-blue-300">{{ week.pm25 }}</div>
                            <div class="text-xs text-gray-600">PM2.5</div>
                          </div>
                          <div class="text-center">
                            <div class="text-2xl font-bold text-teal-300">{{ week.pm10 }}</div>
                            <div class="text-xs text-gray-600">PM10</div>
                          </div>
                          <div class="text-center">
                            <div class="text-2xl font-bold text-cyan-300">{{ week.aqi }}</div>
                            <div class="text-xs text-gray-600">AQI</div>
                          </div>
                        </div>

                        <!-- Progress Bars -->
                        <div class="space-y-2">
                          <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-600 w-12">PM2.5</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                              <div
                                class="h-full bg-gradient-to-r from-blue-300 to-blue-500 rounded-full transition-all duration-1000 ease-out"
                                :style="{ width: (week.pm25 / 80) * 100 + '%', animationDelay: (index * 0.1 + 0.2) + 's' }">
                              </div>
                            </div>
                          </div>
                          <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-600 w-12">PM10</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                              <div
                                class="h-full bg-gradient-to-r from-teal-300 to-teal-500 rounded-full transition-all duration-1000 ease-out"
                                :style="{ width: (week.pm10 / 100) * 100 + '%', animationDelay: (index * 0.1 + 0.4) + 's' }">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Alert Summary -->
                  <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Alert Summary</h3>

                    <div class="space-y-4">
                      <div v-for="(week, index) in weeklyData" :key="'alert-' + week.week"
                        class="flex items-center justify-between p-3 bg-white/30 rounded-lg border border-gray-200"
                        :style="{ animationDelay: index * 0.1 + 's' }">
                        <span class="text-gray-700 text-sm">{{ week.week }}</span>
                        <div class="flex items-center gap-2">
                          <svg v-for="i in week.alerts" :key="i"
                            class="h-4 w-4 text-red-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            :style="{ animationDelay: (i * 0.2) + 's' }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <span class="text-gray-900 font-medium ml-2">{{ week.alerts }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Total Alerts -->
                    <div
                      class="mt-6 p-4 bg-gradient-to-r from-red-200/20 to-pink-200/20 rounded-xl border border-red-200/30">
                      <div class="text-center">
                        <div class="text-3xl font-bold text-red-300 mb-1">{{ totalAlerts }}</div>
                        <div class="text-red-200 text-sm">Total Alerts This Month</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <!-- Enhanced Insights Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- AI-Powered Insights -->
          <div class="bg-white/80 backdrop-blur-md rounded-2xl p-8 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
              <div class="p-2 bg-gradient-to-br from-cyan-200/20 to-teal-200/20 rounded-lg">
                <svg class="h-6 w-6 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
              AI Insights
            </h3>

            <div class="space-y-6">
              <div v-for="(insight, index) in aiInsights" :key="index"
                class="group p-4 bg-white/30 rounded-xl border border-gray-200 hover:bg-white/40 transition-all duration-300"
                :style="{ animationDelay: index * 0.2 + 's' }">
                <div class="flex items-start gap-4">
                  <div :class="`w-3 h-3 rounded-full mt-2 animate-pulse ${insight.color}`"></div>
                  <div class="flex-1">
                    <h4 class="font-semibold text-gray-900 mb-2">{{ insight.title }}</h4>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ insight.description }}</p>
                    <div class="mt-3 flex items-center gap-2">
                      <div :class="`px-2 py-1 rounded text-xs font-medium ${insight.badgeColor}`">
                        {{ insight.confidence }}% confidence
                      </div>
                      <div class="text-gray-500 text-xs">{{ insight.timeAgo }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Dynamic Health Recommendations -->
          <div class="bg-white/80 backdrop-blur-md rounded-2xl p-8 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
              <div class="p-2 bg-gradient-to-br from-teal-200/20 to-blue-200/20 rounded-lg">
                <svg class="h-6 w-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </div>
              Health Recommendations
            </h3>

            <div class="space-y-4">
              <div v-for="(recommendation, index) in healthRecommendations" :key="index"
                :class="`p-4 rounded-xl border transition-all duration-500 hover:scale-105 ${recommendation.bgColor} ${recommendation.borderColor}`"
                :style="{ animationDelay: index * 0.15 + 's' }">
                <div class="flex items-start gap-3">
                  <svg :class="`h-5 w-5 mt-0.5 ${recommendation.iconColor}`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="recommendation.iconPath" />
                  </svg>
                  <div class="flex-1">
                    <h4 :class="`font-semibold mb-2 ${recommendation.titleColor}`">{{ recommendation.title }}</h4>
                    <p :class="`text-sm leading-relaxed ${recommendation.textColor}`">{{ recommendation.description }}
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                      <div
                        :class="`px-2 py-1 rounded text-xs font-medium ${recommendation.tagBg} ${recommendation.tagText}`">
                        {{ recommendation.priority }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  CalendarDaysIcon,
  WindIcon,
  DropletsIcon,
  EyeIcon,
  AlertTriangleIcon,
  TrendingUpIcon,
  TrendingDownIcon,
  BarChart3Icon,
  LineChartIcon,
  ThermometerIcon,
  CloudIcon,
  SparklesIcon,
  HeartIcon,
  ShieldCheckIcon,
  ActivityIcon
} from 'lucide-vue-next'

// Reactive data
const timeRange = ref('7d')
const activeTab = ref('trends')
const currentTime = ref('')
const animatedAQI = ref(0)
const animatedPM25 = ref(0)
const animatedPM10 = ref(0)
const animatedAlerts = ref(0)
const selectedPollutants = ref(['pm25', 'pm10', 'no2', 'o3'])

// Mock data with more realistic variations
const airQualityData = [
  { date: "Jan 1", pm25: 35, pm10: 45, co2: 410, no2: 25, o3: 60, aqi: 85, personally: 22, humidity: 65 },
  { date: "Jan 2", pm25: 42, pm10: 52, co2: 415, no2: 28, o3: 65, aqi: 95, personally: 24, humidity: 62 },
  { date: "Jan 3", pm25: 38, pm10: 48, co2: 408, no2: 22, o3: 58, aqi: 82, personally: 21, humidity: 68 },
  { date: "Jan 4", pm25: 55, pm10: 68, co2: 425, no2: 35, o3: 75, aqi: 125, personally: 26, humidity: 58 },
  { date: "Jan 5", pm25: 48, pm10: 58, co2: 420, no2: 30, o3: 70, aqi: 110, personally: 25, humidity: 60 },
  { date: "Jan 6", pm25: 32, pm10: 40, co2: 405, no2: 20, o3: 55, aqi: 75, personally: 20, humidity: 70 },
  { date: "Jan 7", pm25: 28, pm10: 35, co2: 400, no2: 18, o3: 50, aqi: 65, personally: 19, humidity: 72 },
  { date: "Jan 8", pm25: 45, pm10: 55, co2: 418, no2: 32, o3: 68, aqi: 105, personally: 23, humidity: 64 },
  { date: "Jan 9", pm25: 52, pm10: 62, co2: 422, no2: 38, o3: 72, aqi: 118, personally: 27, humidity: 56 },
  { date: "Jan 10", pm25: 40, pm10: 50, co2: 412, no2: 26, o3: 62, aqi: 90, personally: 22, humidity: 66 },
  { date: "Jan 11", pm25: 36, pm10: 44, co2: 408, no2: 24, o3: 58, aqi: 80, personally: 21, humidity: 68 },
  { date: "Jan 12", pm25: 60, pm10: 75, co2: 430, no2: 42, o3: 80, aqi: 140, personally: 28, humidity: 52 },
  { date: "Jan 13", pm25: 58, pm10: 70, co2: 428, no2: 40, o3: 78, aqi: 135, personally: 27, humidity: 54 },
  { date: "Jan 14", pm25: 44, pm10: 54, co2: 416, no2: 29, o3: 66, aqi: 100, personally: 24, humidity: 61 },
]

const weeklyData = [
  { week: "Week 1", pm25: 38, pm10: 47, aqi: 88, alerts: 2 },
  { week: "Week 2", pm25: 42, pm10: 52, aqi: 95, alerts: 3 },
  { week: "Week 3", pm25: 35, pm10: 43, aqi: 78, alerts: 1 },
  { week: "Week 4", pm25: 48, pm10: 58, aqi: 108, alerts: 4 },
]

const tabs = [
  { id: 'trends', name: 'Pollution Trends', icon: TrendingUpIcon },
  { id: 'weekly', name: 'Weekly Summary', icon: CalendarDaysIcon }
]

const pollutants = [
  { key: 'pm25', name: 'PM2.5', color: '#3B82F6' },
  { key: 'pm10', name: 'PM10', color: '#10B981' },
  { key: 'no2', name: 'NO2', color: '#F59E0B' },
  { key: 'o3', name: 'O3', color: '#EF4444' }
]

const aiInsights = [
  {
    title: "Peak Pollution Pattern Detected",
    description: "AI analysis shows pollution levels spike between 8-10 AM and 6-8 PM, correlating with traffic patterns. Consider avoiding outdoor activities during these hours.",
    color: "bg-red-500",
    badgeColor: "bg-red-500/20 text-red-400",
    confidence: 94,
    timeAgo: "2 hours ago"
  },
  {
    title: "Weather Correlation Identified",
    description: "Strong positive correlation (0.72) between temperature and ozone levels. Higher temperatures lead to increased ground-level ozone formation.",
    color: "bg-yellow-500",
    badgeColor: "bg-yellow-500/20 text-yellow-400",
    confidence: 87,
    timeAgo: "4 hours ago"
  },
  {
    title: "Improvement Trend Detected",
    description: "PM2.5 levels have decreased by 12% compared to last month, indicating positive environmental changes in the area.",
    color: "bg-green-500",
    badgeColor: "bg-green-500/20 text-green-400",
    confidence: 91,
    timeAgo: "1 day ago"
  }
]

const healthRecommendations = [
  {
    title: "High Risk Alert",
    description: "Current AQI levels are unhealthy for sensitive groups. Individuals with respiratory conditions should limit outdoor exposure and consider wearing N95 masks.",
    icon: AlertTriangleIcon,
    priority: "Critical",
    bgColor: "bg-red-500/10",
    borderColor: "border-red-500/30",
    titleColor: "text-red-400",
    textColor: "text-red-300",
    iconColor: "text-red-400",
    tagBg: "bg-red-500/20",
    tagText: "text-red-400"
  },
  {
    title: "Moderate Risk Advisory",
    description: "Air quality is acceptable for most people, but sensitive individuals may experience minor respiratory symptoms. Consider reducing prolonged outdoor exertion.",
    icon: ShieldCheckIcon,
    priority: "Medium",
    bgColor: "bg-yellow-500/10",
    borderColor: "border-yellow-500/30",
    titleColor: "text-yellow-400",
    textColor: "text-yellow-300",
    iconColor: "text-yellow-400",
    tagBg: "bg-yellow-500/20",
    tagText: "text-yellow-400"
  },
  {
    title: "Safe for Activities",
    description: "Air quality is good and safe for all outdoor activities. Perfect time for exercise, sports, and spending time outdoors with family.",
    icon: ActivityIcon, // Changed from personallyIcon to ActivityIcon
    priority: "Low",
    bgColor: "bg-green-500/10",
    borderColor: "border-green-500/30",
    titleColor: "text-green-400",
    textColor: "text-green-300",
    iconColor: "text-green-400",
    tagBg: "bg-green-500/20",
    tagText: "text-green-400"
  }
]

// Computed properties
const currentAQI = computed(() => {
  return airQualityData[airQualityData.length - 1]?.aqi || 0
})

const currentHumidity = computed(() => {
  return airQualityData[airQualityData.length - 1]?.humidity || 0
})

const aqiStatus = computed(() => {
  const aqi = currentAQI.value
  if (aqi <= 50) return {
    status: "Good",
    bgColor: "bg-green-500/20",
    textColor: "text-green-400"
  }
  if (aqi <= 100) return {
    status: "Moderate",
    bgColor: "bg-yellow-500/20",
    textColor: "text-yellow-400"
  }
  if (aqi <= 150) return {
    status: "Unhealthy for Sensitive",
    bgColor: "bg-orange-500/20",
    textColor: "text-orange-400"
  }
  if (aqi <= 200) return {
    status: "Unhealthy",
    bgColor: "bg-red-500/20",
    textColor: "text-red-400"
  }
  return {
    status: "Very Unhealthy",
    bgColor: "bg-purple-500/20",
    textColor: "text-purple-400"
  }
})

const avgPM25 = computed(() => {
  return Math.round(airQualityData.reduce((sum, item) => sum + item.pm25, 0) / airQualityData.length)
})

const avgPM10 = computed(() => {
  return Math.round(airQualityData.reduce((sum, item) => sum + item.pm10, 0) / airQualityData.length)
})

const totalAlerts = computed(() => {
  return weeklyData.reduce((sum, week) => sum + week.alerts, 0)
})

const aqiDataPoints = computed(() => {
  return airQualityData.slice(-10).map((item, index) => ({
    x: 40 + (index * 32),
    y: 180 - (item.aqi / 200) * 160
  }))
})

const aqiChartPath = computed(() => {
  const points = aqiDataPoints.value
  if (points.length === 0) return ''

  let path = `M 40 180 L ${points[0].x} ${points[0].y}`
  for (let i = 1; i < points.length; i++) {
    path += ` L ${points[i].x} ${points[i].y}`
  }
  path += ` L 360 180 Z`
  return path
})

const aqiLinePath = computed(() => {
  const points = aqiDataPoints.value
  if (points.length === 0) return ''

  let path = `M ${points[0].x} ${points[0].y}`
  for (let i = 1; i < points.length; i++) {
    path += ` L ${points[i].x} ${points[i].y}`
  }
  return path
})

const humidityWavePath = computed(() => {
  const points = airQualityData.slice(-10)
  let path = 'M 40 180'

  points.forEach((item, index) => {
    const x = 40 + (index * 32)
    const y = 180 - (item.humidity / 100) * 160
    path += ` L ${x} ${y}`
  })

  path += ' L 360 180 Z'
  return path
})

const humidityLinePath = computed(() => {
  const points = airQualityData.slice(-10)
  let path = ''

  points.forEach((item, index) => {
    const x = 40 + (index * 32)
    const y = 180 - (item.humidity / 100) * 160
    if (index === 0) {
      path += `M ${x} ${y}`
    } else {
      path += ` L ${x} ${y}`
    }
  })

  return path
})

// Methods
const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', {
    hour12: false,
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const animateCounters = () => {
  const duration = 2000
  const steps = 60
  const stepDuration = duration / steps

  const targetAQI = currentAQI.value
  const targetPM25 = avgPM25.value
  const targetPM10 = avgPM10.value
  const targetAlerts = totalAlerts.value

  let currentStep = 0

  const interval = setInterval(() => {
    currentStep++
    const progress = currentStep / steps
    const easeOut = 1 - Math.pow(1 - progress, 3)

    animatedAQI.value = Math.round(targetAQI * easeOut)
    animatedPM25.value = Math.round(targetPM25 * easeOut)
    animatedPM10.value = Math.round(targetPM10 * easeOut)
    animatedAlerts.value = Math.round(targetAlerts * easeOut)

    if (currentStep >= steps) {
      clearInterval(interval)
    }
  }, stepDuration)
}

const togglePollutant = (pollutantKey) => {
  const index = selectedPollutants.value.indexOf(pollutantKey)
  if (index > -1) {
    selectedPollutants.value.splice(index, 1)
  } else {
    selectedPollutants.value.push(pollutantKey)
  }
}

const getPollutantPath = (pollutantKey) => {
  const data = airQualityData.slice(-10)
  const maxValue = pollutantKey === 'pm25' ? 80 : pollutantKey === 'pm10' ? 100 : 50

  let path = 'M 50 300'
  data.forEach((item, index) => {
    const x = 50 + (index * 80)
    const y = 300 - (item[pollutantKey] / maxValue) * 280
    path += ` L ${x} ${y}`
  })
  path += ' L 750 300 Z'
  return path
}

const getPollutantLinePath = (pollutantKey) => {
  const data = airQualityData.slice(-10)
  const maxValue = pollutantKey === 'pm25' ? 80 : pollutantKey === 'pm10' ? 100 : 50

  let path = ''
  data.forEach((item, index) => {
    const x = 50 + (index * 80)
    const y = 300 - (item[pollutantKey] / maxValue) * 280
    if (index === 0) {
      path += `M ${x} ${y}`
    } else {
      path += ` L ${x} ${y}`
    }
  })
  return path
}

const getAveragePollutant = (pollutantKey) => {
  const sum = airQualityData.reduce((acc, item) => acc + item[pollutantKey], 0)
  return Math.round(sum / airQualityData.length)
}

const getTemperatureColor = (temp) => {
  if (temp < 15) return '#3B82F6'
  if (temp < 20) return '#10B981'
  if (temp < 25) return '#F59E0B'
  return '#EF4444'
}

const getWeekStatus = (aqi) => {
  if (aqi <= 50) return {
    status: "Excellent",
    bgColor: "bg-green-500/20",
    textColor: "text-green-400"
  }
  if (aqi <= 100) return {
    status: "Good",
    bgColor: "bg-yellow-500/20",
    textColor: "text-yellow-400"
  }
  return {
    status: "Poor",
    bgColor: "bg-red-500/20",
    textColor: "text-red-400"
  }
}

// Lifecycle
onMounted(() => {
  updateTime()
  animateCounters()

  const timeInterval = setInterval(updateTime, 1000)

  onUnmounted(() => {
    clearInterval(timeInterval)
  })
})
</script>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }

  33% {
    transform: translate(30px, -50px) scale(1.1);
  }

  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }

  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(-20px);
  }
}

@keyframes draw {
  0% {
    stroke-dasharray: 0 1000;
  }

  100% {
    stroke-dasharray: 1000 0;
  }
}

@keyframes wave {
  0% {
    transform: translateX(-100%);
  }

  100% {
    transform: translateX(0%);
  }
}

@keyframes fade-in {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }

  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.animate-float {
  animation: float linear infinite;
}

.animate-draw {
  animation: draw 2s ease-in-out forwards;
}

.animate-wave {
  animation: wave 3s ease-in-out infinite;
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out forwards;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(30px) scale(0.95);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-30px) scale(1.05);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.5);
}
</style>
