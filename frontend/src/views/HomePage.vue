<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Header -->
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">
          Air Quality Index
        </h1>
        <p class="text-slate-600 text-sm">Monitor air quality levels and pollution data in real-time from around the world</p>
      </div>
      
      <!-- Top 4 Metric Boxes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Favorites -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">My Favorites</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ favorites.length }}</p>
              <p class="text-sm text-emerald-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  Saved Locations
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
              </div>
              <button @click="exportData('favorites')" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Temperature -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Temperature</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ nearestStation?.temperature || 'N/A' }}°C</p>
              <p class="text-sm text-orange-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                  </svg>
                  Current
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <button @click="exportData('temperature')" class="p-2 text-slate-400 hover:text-orange-600 hover:bg-orange-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Small Map Only -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="h-full flex items-center justify-center">
            <div id="small-map" class="h-32 w-full rounded-lg overflow-hidden relative border border-slate-200">
              <!-- Map will be initialized here -->
              <div v-if="!nearestStation" class="absolute inset-0 flex items-center justify-center bg-slate-100 text-slate-400 text-sm">
                No location data available
              </div>
            </div>
          </div>
        </div>
        
        <!-- Health Status -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Health Status</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ getHealthStatus(nearestStation?.aqi) }}</p>
              <p class="text-sm text-purple-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                  </svg>
                  Current Advisory
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </div>
              <button @click="exportData('health')" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Map Section -->
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-xl font-bold text-slate-900 mb-1">Air Quality Map</h3>
            <p class="text-slate-600 text-sm">Interactive map showing pollution levels worldwide</p>
          </div>
        </div>
        
        <div id="map" class="h-[400px] w-full overflow-hidden relative rounded-lg">
          <!-- Dynamic Legend on left -->
          <div class="absolute bottom-4 left-4 bg-white p-3 shadow-lg rounded-lg z-[1000] max-w-[160px]">
            <div class="text-xs font-semibold mb-2 text-slate-800">
              {{ legendTitle }}
            </div>
            <div class="space-y-1 text-xs">
              <div v-for="item in legendItems" :key="item.color" class="flex items-center">
                <div class="w-3 h-3 mr-2 rounded-sm" :style="{ backgroundColor: item.color }"></div>
                <span class="text-slate-700">{{ item.label }}</span>
              </div>
            </div>
          </div>
          
          <!-- Search control on top right -->
          <div class="absolute top-4 right-4 z-[1000]">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden relative">
              <input type="text"
                placeholder="Search location..."
                class="px-3 py-2 w-56 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg"
                v-model="searchQuery" @keyup="searchLocation" />
              <button v-if="searchQuery" @click="clearSearch"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-lg leading-none hover:text-slate-700 rounded-full w-5 h-5 flex items-center justify-center">
                ×
              </button>
            </div>
            
            <!-- Zoom controls -->
            <div class="flex justify-end mt-2 space-x-1">
              <button @click="zoomIn"
                class="bg-white shadow-lg rounded-lg p-1.5 hover:bg-slate-100 transition-all duration-200 flex items-center justify-center w-8 h-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-700" viewBox="0 0 20 20"
                  fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd" />
                </svg>
              </button>
              <button @click="zoomOut"
                class="bg-white shadow-lg rounded-lg p-1.5 hover:bg-slate-100 transition-all duration-200 flex items-center justify-center w-8 h-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-700" viewBox="0 0 20 20"
                  fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M5 10a1 1 0 011-1h8a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                    clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
          
          <!-- Pollutant Selector in Map -->
          <div
            class="absolute top-4 left-4 flex items-center gap-0.5 bg-slate-900 p-1 z-[1000] rounded-lg shadow-sm opacity-90">
            <button v-for="option in pollutantOptions" :key="option.value" @click="selectedPollutant = option.value"
              :class="[ 'p-1 hover:bg-slate-700 transition-all duration-200 flex items-center justify-center rounded-full',
                selectedPollutant === option.value ? 'bg-yellow-500 text-black' : ''
              ]">
              <span v-html="option.icon" class="w-3 h-3"></span>
            </button>
          </div>
          
          <!-- Search Results -->
          <div v-if="searchResults.length > 0"
            class="absolute top-20 right-4 bg-slate-900 shadow-xl rounded-lg z-[1000] w-64 border border-slate-700">
            <div class="px-3 py-1 border-b border-slate-700 bg-slate-800 rounded-t-lg">
              <h3 class="text-white font-medium text-xs">Search Results</h3>
            </div>
            <div class="max-h-80 overflow-y-auto">
              <div v-for="(result, index) in showAllResults ? searchResults : searchResults.slice(0, maxVisibleResults)"
                :key="result.name"
                class="flex items-center justify-between p-1.5 border-b border-slate-700 last:border-b-0 hover:bg-slate-800 cursor-pointer transition-all duration-200"
                @click="goToLocation(result)">
                <div class="flex items-center space-x-1.5 flex-1 min-w-0">
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-white truncate">
                      {{ result.name }}, {{ result.country || "Unknown" }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0 ml-1">
                  <button @click.stop="toggleFavorite(result)"
                    class="px-1 py-0.5 rounded-lg text-white transition-all duration-200"
                    :class="isFavorite(result) ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'">
                    <svg v-if="isFavorite(result)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                  </button>
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold text-white min-w-[2rem] justify-center"
                    :style="{ backgroundColor: getColor(result.aqi, 'aqi') }">
                    {{ result.aqi || "N/A" }}
                  </span>
                </div>
              </div>
            </div>
            <div v-if="searchResults.length > maxVisibleResults" class="p-1 border-t border-slate-700 bg-slate-800">
              <button @click="showAllResults = !showAllResults"
                class="w-full text-center text-xs text-slate-300 hover:text-white transition-all duration-200">
                {{ showAllResults ? 'Show Less' : `Show All (${searchResults.length})` }}
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Global Rankings -->
      <section class="mt-8 px-2 md:px-4 space-y-6">
        <h2 class="text-base font-semibold text-slate-800">Global Rankings</h2>
        <div class="grid md:grid-cols-2 gap-6">
          <!-- Most Polluted -->
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                <h3 class="text-lg font-semibold text-red-600">Most Polluted Cities</h3>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-xs">
                <thead>
                  <tr class="text-left text-slate-600 border-b">
                    <th class="py-2 px-3">Rank</th>
                    <th class="py-2 px-3">City</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3 text-center">AQI</th>
                    <th class="py-2 px-3 text-center">Favorite</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(city, index) in top10MostPolluted" :key="'polluted-' + index" class="hover:bg-red-50 transition-all duration-200">
                    <td class="py-2 px-3 font-medium">{{ index + 1 }}</td>
                    <td class="py-2 px-3">
                      <div class="flex items-center space-x-2">
                        <img v-if="city.flag" :src="city.flag" :alt="city.country" class="w-5 h-3 object-cover rounded-sm shadow-sm" />
                        <span class="font-medium">{{ city.name }}</span>
                      </div>
                    </td>
                    <td class="py-2 px-3">
                      <span class="inline-block px-2 py-1 rounded-full text-white text-xs font-medium"
                            :style="{ backgroundColor: getColorAQI(city.aqi) }">
                        {{ getStatusLabelAQI(city.aqi) }}
                      </span>
                    </td>
                    <td class="py-2 px-3 text-center">
                      <span class="inline-block px-2 py-1 rounded-full text-white text-xs font-bold"
                            :style="{ backgroundColor: getColorAQI(city.aqi) }">
                        {{ city.aqi }}
                      </span>
                    </td>
                    <td class="py-2 px-3 text-center">
                      <button
                        @click="toggleFavorite(city)"
                        class="p-1.5 rounded-lg text-white transition-all duration-200 hover:scale-110"
                        :class="isFavorite(city) ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'">
                        <svg v-if="isFavorite(city)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Cleanest -->
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <h3 class="text-lg font-semibold text-green-600">Cleanest Cities</h3>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-xs">
                <thead>
                  <tr class="text-left text-slate-600 border-b">
                    <th class="py-2 px-3">Rank</th>
                    <th class="py-2 px-3">City</th>
                    <th class="py-2 px-3">Status</th>
                    <th class="py-2 px-3 text-center">AQI</th>
                    <th class="py-2 px-3 text-center">Favorite</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(city, index) in top10LeastPolluted" :key="'cleanest-' + index" class="hover:bg-green-50 transition-all duration-200">
                    <td class="py-2 px-3 font-medium">{{ index + 1 }}</td>
                    <td class="py-2 px-3">
                      <div class="flex items-center space-x-2">
                        <img v-if="city.flag" :src="city.flag" :alt="city.country" class="w-5 h-3 object-cover rounded-sm shadow-sm" />
                        <span class="font-medium">{{ city.name }}</span>
                      </div>
                    </td>
                    <td class="py-2 px-3">
                      <span class="inline-block px-2 py-1 rounded-full text-white text-xs font-medium"
                            :style="{ backgroundColor: getColorAQI(city.aqi) }">
                        {{ getStatusLabelAQI(city.aqi) }}
                      </span>
                    </td>
                    <td class="py-2 px-3 text-center">
                      <span class="inline-block px-2 py-1 rounded-full text-white text-xs font-bold"
                            :style="{ backgroundColor: getColorAQI(city.aqi) }">
                        {{ city.aqi }}
                      </span>
                    </td>
                    <td class="py-2 px-3 text-center">
                      <button
                        @click="toggleFavorite(city)"
                        class="p-1.5 rounded-lg text-white transition-all duration-200 hover:scale-110"
                        :class="isFavorite(city) ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'">
                        <svg v-if="isFavorite(city)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
<script setup>
import { onMounted, onUnmounted, ref, watch, computed, nextTick } from "vue";
import { useRouter } from "vue-router";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import axios from "axios";
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/airQuality";
const router = useRouter();
const selectedPollutant = ref("aqi");
const searchQuery = ref("");
const aqiData = ref([]);
let map = null;
let smallMap = null;
let markers = [];
let smallMapMarker = null;
const markerMap = ref({});
const searchMarkers = ref([]);
const showAllResults = ref(false);
const maxVisibleResults = ref(5);
const favorites = ref([]);
const auth = useAuthStore();
const userLocation = ref(null);
// Computed properties for rankings
const top10MostPolluted = computed(() =>
  [...aqiData.value]
    .filter(city => city.aqi != null)
    .sort((a, b) => b.aqi - a.aqi)
    .slice(0, 10)
);
const top10LeastPolluted = computed(() =>
  [...aqiData.value]
    .filter(city => city.aqi != null)
    .sort((a, b) => a.aqi - b.aqi)
    .slice(0, 10)
);
// Computed property for nearest station
const nearestStation = computed(() => {
  if (!userLocation.value || aqiData.value.length === 0) return null;
  let minDist = Infinity;
  let nearest = null;
  aqiData.value.forEach(station => {
    if (!station.lat || !station.lon) return;
    const dist = calculateDistance(userLocation.value.lat, userLocation.value.lon, station.lat, station.lon);
    if (dist < minDist) {
      minDist = dist;
      nearest = station;
    }
  });
  return nearest;
});
// --- COLOR & STATUS FUNCTIONS ---
const getColorAQI = (value, pollutant = "aqi") => {
  const val = parseFloat(value);
  if (isNaN(val)) return "#999";
  if (pollutant === "aqi") {
    if (val <= 50) return "#00e400";
    if (val <= 100) return "#FFEB3B";
    if (val <= 150) return "#ff7e00";
    if (val <= 200) return "#ff0000";
    if (val <= 300) return "#99004c";
    return "#7e0023";
  }
  return "#999";
};
const getStatusLabelAQI = (value) => getStatusAqi(value, "aqi");
const getStatusAqi = (value, pollutant = "aqi") => {
  const val = parseFloat(value);
  if (isNaN(val)) return "N/A";
  if (pollutant === "aqi") {
    if (val <= 50) return "Good";
    if (val <= 100) return "Moderate";
    if (val <= 150) return "Unhealthy for SG";
    if (val <= 200) return "Unhealthy";
    if (val <= 300) return "Very Unhealthy";
    return "Hazardous";
  }
  return "N/A";
};
// Get health status based on AQI
const getHealthStatus = (aqi) => {
  const val = parseFloat(aqi);
  if (isNaN(val)) return "N/A";
  if (val <= 50) return "Good";
  if (val <= 100) return "Moderate";
  if (val <= 150) return "Unhealthy SG";
  if (val <= 200) return "Unhealthy";
  if (val <= 300) return "Very Unhealthy";
  return "Hazardous";
};
// Pollutant options with improved icons
const pollutantOptions = [
  { value: "aqi", label: "AQI", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#FFCC00" stroke="#333" stroke-width="1.5"/><path d="M12 12l4-4" stroke="#333" stroke-width="2" stroke-linecap="round" fill="none"/></svg>` },
  { value: "pm25", label: "PM2.5", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="6" cy="12" r="1.8" fill="#666" stroke="#333" stroke-width="0.5"/><circle cx="12" cy="8" r="1.8" fill="#666" stroke="#333" stroke-width="0.5"/><circle cx="17" cy="14" r="2.2" fill="#666" stroke="#333" stroke-width="0.5"/><circle cx="10" cy="16" r="1.8" fill="#666" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "pm10", label: "PM10", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="5" cy="10" r="2.2" fill="#A0522D" stroke="#333" stroke-width="0.5"/><circle cx="12" cy="7" r="3" fill="#A0522D" stroke="#333" stroke-width="0.5"/><circle cx="18" cy="15" r="2.5" fill="#A0522D" stroke="#333" stroke-width="0.5"/><circle cx="9" cy="17" r="1.8" fill="#A0522D" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "no2", label: "NO₂", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="8" fill="#555" stroke="#333" stroke-width="0.5" rx="1"/><polygon points="4,10 8,4 12,10" fill="#777" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "co", label: "CO", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="8" cy="12" r="4" fill="#FF4D4D" stroke="#333" stroke-width="0.5"/><circle cx="17" cy="12" r="3" fill="#FF6666" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "o3", label: "O₃", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="8" cy="12" r="3" fill="#3399FF" stroke="#333" stroke-width="0.5"/><circle cx="15" cy="9" r="3" fill="#33CCFF" stroke="#333" stroke-width="0.5"/><circle cx="15" cy="15" r="3" fill="#3399FF" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "temperature", label: "Temperature", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="11" y="4" width="2" height="12" fill="#FF3300" stroke="#333" stroke-width="0.5" rx="0.5"/><circle cx="12" cy="18" r="3" fill="#FF3300" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "humidity", label: "Humidity", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2.5l5 7a8 8 0 1 1-10 0z" fill="#3399FF" stroke="#333" stroke-width="0.5"/></svg>` },
  { value: "pressure", label: "Pressure", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#66CC66" stroke="#333" stroke-width="0.5"/><path d="M12 12l3-4" stroke="#333" stroke-width="2" stroke-linecap="round" fill="none"/></svg>` },
  { value: "wind", label: "Wind", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 12h13a3 3 0 1 0 0-6M3 18h9a3 3 0 1 1 0 6" stroke="#33CCFF" stroke-width="2" stroke-linecap="round" fill="none"/></svg>` }
];
// Color scale
const getColor = (value, pollutant) => {
  const val = parseFloat(value);
  if (isNaN(val)) return "#999";
  switch (pollutant) {
    case "pm25":
    case "pm10":
    case "aqi":
      if (val <= 50) return "#00e400";
      if (val <= 100) return "#FFEB3B";
      if (val <= 150) return "#ff7e00";
      if (val <= 200) return "#ff0000";
      if (val <= 300) return "#99004c";
      return "#7e0023";
    case "no2":
    case "o3":
      if (val <= 40) return "#00e400";
      if (val <= 80) return "#FFEB3B";
      if (val <= 180) return "#ff7e00";
      if (val <= 280) return "#ff0000";
      return "#99004c";
    case "co":
      if (val <= 1) return "#00e400";
      if (val <= 2) return "#FFEB3B";
      if (val <= 10) return "#ff7e00";
      return "#ff0000";
    case "temperature":
      if (val < 0) return "#0000ff";
      if (val < 15) return "#00ffff";
      if (val < 25) return "#00e400";
      if (val < 35) return "#FFEB3B";
      return "#ff0000";
    case "humidity":
      if (val < 30) return "#ff7e00";
      if (val < 60) return "#00e400";
      return "#0000ff";
    case "pressure":
      if (val < 1000) return "#99004c";
      if (val < 1010) return "#ff0000";
      if (val < 1020) return "#00e400";
      return "#00ff00";
    case "wind":
      if (val < 5) return "#00e400";
      if (val < 10) return "#FFEB3B";
      if (val < 15) return "#ff7e00";
      return "#ff0000";
    default:
      return "#999";
  }
};
// Status text
const getStatus = (value, pollutant) => {
  const val = parseFloat(value);
  if (isNaN(val)) return "N/A";
  switch (pollutant) {
    case "pm25":
    case "pm10":
    case "aqi":
      if (val <= 50) return "Good";
      if (val <= 100) return "Moderate";
      if (val <= 150) return "Unhealthy for SG";
      if (val <= 200) return "Unhealthy";
      if (val <= 300) return "Very Unhealthy";
      return "Hazardous";
    case "no2":
    case "o3":
      if (val <= 40) return "Good";
      if (val <= 80) return "Moderate";
      if (val <= 180) return "Unhealthy for SG";
      if (val <= 280) return "Unhealthy";
      return "Very Unhealthy";
    case "co":
      if (val <= 1) return "Good";
      if (val <= 2) return "Moderate";
      if (val <= 10) return "Unhealthy";
      return "Hazardous";
    case "temperature":
      if (val < 0) return "Freezing";
      if (val < 15) return "Cold";
      if (val < 25) return "Mild";
      if (val < 35) return "Warm";
      return "Hot";
    case "humidity":
      if (val < 30) return "Dry";
      if (val < 60) return "Comfortable";
      return "Humid";
    case "pressure":
      if (val < 1000) return "Very Low";
      if (val < 1010) return "Low";
      if (val < 1020) return "Normal";
      return "High";
    case "wind":
      if (val < 5) return "Calm";
      if (val < 10) return "Light";
      if (val < 15) return "Moderate";
      return "Strong";
    default:
      return "N/A";
  }
};
// Legend items
const getLegendItems = (pollutant) => {
  switch (pollutant) {
    case "pm25":
    case "pm10":
    case "aqi":
      return [
        { color: "#00e400", label: "Good (0-50)" },
        { color: "#FFEB3B", label: "Moderate (51-100)" },
        { color: "#ff7e00", label: "Unhealthy for SG (101-150)" },
        { color: "#ff0000", label: "Unhealthy (151-200)" },
        { color: "#99004c", label: "Very Unhealthy (201-300)" },
        { color: "#7e0023", label: "Hazardous (301+)" },
      ];
    case "no2":
    case "o3":
      return [
        { color: "#00e400", label: "Good (0-40)" },
        { color: "#FFEB3B", label: "Moderate (41-80)" },
        { color: "#ff7e00", label: "Unhealthy for SG (81-180)" },
        { color: "#ff0000", label: "Unhealthy (181-280)" },
        { color: "#99004c", label: "Very Unhealthy (281+)" },
      ];
    case "co":
      return [
        { color: "#00e400", label: "Good (0-1)" },
        { color: "#FFEB3B", label: "Moderate (1.1-2)" },
        { color: "#ff7e00", label: "Unhealthy (2.1-10)" },
        { color: "#ff0000", label: "Hazardous (10.1+)" },
      ];
    case "temperature":
      return [
        { color: "#0000ff", label: "Freezing (<0°C)" },
        { color: "#00ffff", label: "Cold (0-15°C)" },
        { color: "#00e400", label: "Mild (15-25°C)" },
        { color: "#FFEB3B", label: "Warm (25-35°C)" },
        { color: "#ff0000", label: "Hot (>35°C)" },
      ];
    case "humidity":
      return [
        { color: "#ff7e00", label: "Dry (0-30%)" },
        { color: "#00e400", label: "Comfortable (30-60%)" },
        { color: "#0000ff", label: "Humid (>60%)" },
      ];
    case "pressure":
      return [
        { color: "#99004c", label: "Very Low (<1000 hPa)" },
        { color: "#ff0000", label: "Low (1000-1010 hPa)" },
        { color: "#00e400", label: "Normal (1010-1020 hPa)" },
        { color: "#00ff00", label: "High (>1020 hPa)" },
      ];
    case "wind":
      return [
        { color: "#00e400", label: "Calm (0-5 m/s)" },
        { color: "#FFEB3B", label: "Light (5-10 m/s)" },
        { color: "#ff7e00", label: "Moderate (10-15 m/s)" },
        { color: "#ff0000", label: "Strong (>15 m/s)" },
      ];
    default:
      return [];
  }
};
const legendItems = computed(() => getLegendItems(selectedPollutant.value));
const legendTitle = computed(() => `${selectedPollutant.value.toUpperCase()} Levels`);
// Search results
const searchResults = computed(() => {
  const searchTerm = searchQuery.value.trim().toLowerCase();
  if (!searchTerm) return [];
  return aqiData.value
    .filter(
      (station) =>
        station.name && station.name.toLowerCase().startsWith(searchTerm)
    )
    .slice(0, 12);
});
// Fetch data
const fetchAQIData = async () => {
  try {
    const { data } = await axios.get("http://127.0.0.1:8000/api/aqi");
    if (data.status === "ok" && Array.isArray(data.data)) {
      aqiData.value = assignIDs(data.data);
      renderMarkers();
      updateSmallMap();
    }
  } catch (error) {
    console.error("Error fetching AQI data:", error);
  }
};
const fetchPhnomPenhAQI = async () => {
  try {
    const { data } = await axios.get("http://127.0.0.1:8000/api/air-quality/phnom-penh");
    const phnomPenhStation = {
      name: "Phnom Penh",
      lat: 11.562108,
      lon: 104.888535,
      aqi: data.AQI,
      pm25: data.PM2_5,
      pm10: data.PM10,
      no2: data.NO2,
      co: data.CO,
      o3: data.O3,
      temperature: data.Temp_C,
      humidity: data.Humidity_percent,
      pressure: data.Pressure_hPa,
      wind: data.Wind_m_s,
      wind_speed: data.Wind_m_s,
      id: data.id || 9999,
      flag: "https://flagcdn.com/w160/kh.png",
    };
    const idx = aqiData.value.findIndex((st) => st.name === "Phnom Penh");
    if (idx !== -1) {
      aqiData.value.splice(idx, 1, phnomPenhStation);
    } else {
      aqiData.value.push(phnomPenhStation);
    }
    renderMarkers();
    updateSmallMap();
  } catch (error) {
    console.error("Error fetching Phnom Penh AQI data:", error);
  }
};
const assignIDs = (data) => {
  return data.map((station, index) => ({
    ...station,
    id: station.id || index + 1,
    flag: station.flag || `https://flagcdn.com/w160/${getCountryCode(station.country || 'kh')}.png`,
  }));
};
// Helper to extract country code from flag URL or country name
const getCountryCode = (country) => {
  const countryCodeMap = {
    'united states': 'us',
    'united kingdom': 'gb',
    'france': 'fr',
    'germany': 'de',
    'italy': 'it',
    'spain': 'es',
    'china': 'cn',
    'india': 'in',
    'japan': 'jp',
    'brazil': 'br',
    'russia': 'ru',
    'canada': 'ca',
    'australia': 'au',
    'cambodia': 'kh',
    'thailand': 'th',
    'singapore': 'sg',
    'malaysia': 'my',
    'indonesia': 'id',
    'philippines': 'ph',
    'vietnam': 'vn',
    'south korea': 'kr',
    'myanmar': 'mm',
    'laos': 'la',
    'bangladesh': 'bd',
    'nepal': 'np',
  };
  if (!country) return 'kh';
  const normalizedCountry = country.toLowerCase();
  return countryCodeMap[normalizedCountry] || 'kh';
};
// Fetch favorites
const fetchFavorites = async () => {
  try {
    const { data } = await axios.get("http://127.0.0.1:8000/api/favourites", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });
    favorites.value = data.map(f => ({
      id: f.id,
      name: f.city_name,
      country_code: f.country_code,
      lat: getCityCoordinates(f.city_name, f.country_code).lat,
      lon: getCityCoordinates(f.city_name, f.country_code).lon,
      flag: `https://flagcdn.com/w160/${f.country_code.toLowerCase()}.png`,
    }));
    renderMarkers();
  } catch (err) {
    console.error("Failed to fetch favorites:", err);
  }
};
// Check if city is favorite
const isFavorite = (station) => {
  return favorites.value.some(fav => fav.name.toLowerCase() === station.name.toLowerCase());
};
// Toggle favorite
const toggleFavorite = async (station) => {
  try {
    const isFav = isFavorite(station);
    if (isFav) {
      const fav = favorites.value.find(f => f.name.toLowerCase() === station.name.toLowerCase());
      if (!fav || !fav.id) {
        throw new Error("Favorite ID not found");
      }
      await axios.delete(
        `http://127.0.0.1:8000/api/favourites/${fav.id}`,
        { headers: { Authorization: `Bearer ${auth.token}` } }
      );
      favorites.value = favorites.value.filter(f => f.id !== fav.id);
      Swal.fire({
        icon: "success",
        title: "Removed from Favorites",
        timer: 1200,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#f6fafd',
        iconColor: '#10b981',
        customClass: {
          popup: 'rounded-xl shadow-lg',
          title: 'font-bold text-gray-800',
        },
      });
    } else {
      const countryCode = station.flag
        ? station.flag.split("/").pop().split(".")[0]
        : getCountryCode(station.country || 'kh');
      await axios.post(
        "http://127.0.0.1:8000/api/favourites",
        {
          city_name: station.name,
          country_code: countryCode,
        },
        { headers: { Authorization: `Bearer ${auth.token}` } }
      );
      await fetchFavorites();
      Swal.fire({
        icon: "success",
        title: "Added to Favorites",
        timer: 1200,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#f6fafd',
        iconColor: '#10b981',
        customClass: {
          popup: 'rounded-xl shadow-lg',
          title: 'font-bold text-gray-800',
        },
      });
    }
    renderMarkers();
  } catch (err) {
    console.error("Error updating favorite:", err);
    Swal.fire({
      icon: "error",
      title: "Failed to update favorite",
      text: err.response?.data?.message || err.message,
      toast: true,
      position: 'top-end',
      background: '#fef2f2',
      iconColor: '#ef4444',
      customClass: {
        popup: 'rounded-xl shadow-lg',
        title: 'font-bold text-gray-800',
      },
    });
  }
};
// Render markers
const renderMarkers = () => {
  markers.forEach((marker) => marker.remove());
  markers = [];
  markerMap.value = {};
  aqiData.value.forEach((station) => {
    if (!station.lat || !station.lon) return;
    let value;
    if (selectedPollutant.value === "wind") {
      value = station.wind_speed || station.wind;
    } else {
      value = station[selectedPollutant.value];
    }
    if (selectedPollutant.value !== "aqi" && (value === null || value === undefined)) {
      return;
    }
    const color = getColor(value, selectedPollutant.value);
    const status = getStatus(value, selectedPollutant.value);
    const isFav = isFavorite(station);
    // Create custom icon for favorite cities
    const icon = isFav ? L.divIcon({
      html: `
        <div style="position: relative;">
          <div style="background-color: ${color}; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 3px rgba(0,0,0,0.3);"></div>
          <div style="position: absolute; top: -6px; right: -6px; background: #FFD700; border-radius: 50%; width: 10px; height: 10px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-star" style="color: #000; font-size: 6px;"></i>
          </div>
        </div>`,
      className: "favorite-marker",
      iconSize: [12, 12],
      iconAnchor: [6, 6],
      popupAnchor: [0, -6]
    }) : L.circleMarker([station.lat, station.lon], {
      radius: 6,
      fillColor: color,
      color: "#fff",
      weight: 1.5,
      opacity: 1,
      fillOpacity: 0.8,
    });
    const renderRow = (label, val, unit = "") => {
      if (val === null || val === undefined || val === "N/A") return "";
      return `<div><strong>${label}:</strong> ${val}${unit}</div>`;
    };
    const popupContent = `
      <div style="font-family: 'Arial', sans-serif; max-width: 200px;">
        <div style="font-weight: 700; font-size: 12px; color: #000000; border-bottom: 1px solid #000000; padding-bottom: 2px; margin-bottom: 4px; text-align: center;">
          ${station.name} ${isFav ? '<i class="fas fa-star text-yellow-400"></i>' : ''}
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 2px; margin-bottom: 4px;">
          ${pollutantOptions.map(opt => `
            <button onclick="document.getElementById('pollutant-${station.id}-${opt.value}').click()"
              style="padding: 1px 4px; font-size: 8px; border-radius: 4px; border: 1px solid #3b82f6; color: #3b82f6; background: ${selectedPollutant.value === opt.value ? '#3b82f6' : 'transparent'}; color: ${selectedPollutant.value === opt.value ? '#fff' : '#3b82f6'}; cursor: pointer;"
              onmouseover="this.style.backgroundColor='#3b82f6'; this.style.color='#fff';"
              onmouseout="this.style.backgroundColor='${selectedPollutant.value === opt.value ? '#3b82f6' : 'transparent'}'; this.style.color='${selectedPollutant.value === opt.value ? '#fff' : '#3b82f6'}';">
              ${opt.label}
            </button>
            <input type="radio" id="pollutant-${station.id}-${opt.value}" name="pollutant-${station.id}" value="${opt.value}" style="display: none;" ${selectedPollutant.value === opt.value ? 'checked' : ''}>
          `).join('')}
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; font-size: 9px; color: #000;">
          ${renderRow(selectedPollutant.value.toUpperCase(), value)}
          ${renderRow("Status", status)}
          ${renderRow("PM2.5", station.pm25)}
          ${renderRow("PM10", station.pm10)}
          ${renderRow("NO₂", station.no2)}
          ${renderRow("CO", station.co)}
          ${renderRow("O₃", station.o3)}
          ${renderRow("Temp", station.temperature, " °C")}
          ${renderRow("Humidity", station.humidity, " %")}
          ${renderRow("Pressure", station.pressure, " hPa")}
          ${renderRow("Wind", station.wind_speed || station.wind, " m/s")}
        </div>
        <div style="margin-top: 4px; text-align: center;">
          <button id="view-detail-${station.id}"
            style="color: #3b82f6; font-size: 8px; font-weight: 500; padding: 2px 6px; border: 1px solid #3b82f6; border-radius: 4px; cursor: pointer; background: transparent; transition: all 0.2s ease-in-out;"
            onmouseover="this.style.backgroundColor='#3b82f6'; this.style.color='#ffffff'; this.style.transform='scale(1.05)';"
            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#3b82f6'; this.style.transform='scale(1)';">
            View City Details
          </button>
          <button id="toggle-favorite-${station.id}"
            class="favorite-btn"
            style="color: #ffffff; font-size: 8px; font-weight: 500; padding: 2px 6px; border-radius: 4px; cursor: pointer; background: ${isFav ? '#ef4444' : '#3b82f6'}; transition: all 0.2s ease-in-out;"
            onmouseover="this.style.backgroundColor='${isFav ? '#dc2626' : '#2563eb'}'; this.style.transform='scale(1.05)';"
            onmouseout="this.style.backgroundColor='${isFav ? '#ef4444' : '#3b82f6'}'; this.style.transform='scale(1)';">
            ${isFav ? 'Remove from Favorites' : 'Add to Favorites'}
          </button>
        </div>
      </div>
    `;
    const marker = isFav ? L.marker([station.lat, station.lon], { icon }) : L.circleMarker([station.lat, station.lon], {
      radius: 6,
      fillColor: color,
      color: "#fff",
      weight: 1.5,
      opacity: 1,
      fillOpacity: 0.8,
    });
    marker.addTo(map).bindPopup(popupContent);
    // Handle popup interactions
    marker.on('popupopen', () => {
      const detailBtn = document.getElementById(`view-detail-${station.id}`);
      if (detailBtn) {
        detailBtn.addEventListener('click', () => {
          router.push({ name: 'city-detail', params: { id: station.id } });
        });
      }
      const favoriteBtn = document.getElementById(`toggle-favorite-${station.id}`);
      if (favoriteBtn) {
        favoriteBtn.addEventListener('click', () => {
          toggleFavorite(station);
          marker.closePopup();
        });
      }
      pollutantOptions.forEach(opt => {
        const radio = document.getElementById(`pollutant-${station.id}-${opt.value}`);
        if (radio) {
          radio.addEventListener('change', (e) => {
            selectedPollutant.value = e.target.value;
            renderMarkers();
          });
        }
      });
    });
    markers.push(marker);
    markerMap.value[station.name] = marker;
  });
  // Add favorite marker from localStorage
  const favoriteCityData = localStorage.getItem('selectedFavoriteCity');
  if (favoriteCityData) {
    try {
      const favoriteCity = JSON.parse(favoriteCityData);
      const marker = markerMap.value[favoriteCity.name];
      if (marker) {
        map.setView([favoriteCity.lat, favoriteCity.lon], 10);
        marker.openPopup();
      }
      localStorage.removeItem('selectedFavoriteCity');
    } catch (e) {
      console.error("Error parsing favorite city data:", e);
    }
  }
};
// Update small map with nearest station
const updateSmallMap = () => {
  if (!smallMap || !nearestStation.value) return;
  
  // Clear existing marker
  if (smallMapMarker) {
    smallMapMarker.remove();
    smallMapMarker = null;
  }
  
  // Center map on nearest station
  smallMap.setView([nearestStation.value.lat, nearestStation.value.lon], 10);
  
  // Add marker for nearest station
  const color = getColor(nearestStation.value.aqi, "aqi");
  smallMapMarker = L.circleMarker([nearestStation.value.lat, nearestStation.value.lon], {
    radius: 8,
    fillColor: color,
    color: "#fff",
    weight: 2,
    opacity: 1,
    fillOpacity: 0.8,
  }).addTo(smallMap);
  
  // Add popup with AQI info
  smallMapMarker.bindPopup(`
    <div style="text-align: center; font-family: system-ui, -apple-system, sans-serif;">
      <h4 style="margin: 0 0 4px 0; font-size: 12px; font-weight: 600;">${nearestStation.value.name}</h4>
      <div style="color: ${color}; font-weight: 600; font-size: 14px;">AQI: ${nearestStation.value.aqi || "N/A"}</div>
      <div style="font-size: 10px; color: #666; margin-top: 4px;">
        ${getStatus(nearestStation.value.aqi, "aqi")}
      </div>
    </div>
  `).openPopup();
};
// Initialize small map
const initSmallMap = () => {
  if (smallMap) {
    smallMap.remove();
  }
  
  smallMap = L.map("small-map", {
    center: [20, 100],
    zoom: 4,
    zoomControl: false,
    scrollWheelZoom: true,
    dragging: true,
    touchZoom: true,
    doubleClickZoom: true,
    boxZoom: true,
    keyboard: false,
  });
  
  L.tileLayer(
    "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
    {
      attribution: "Map data: &copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors, <a href='http://viewfinderpanoramas.org'>SRTM</a> | Map style: &copy; <a href='https://opentopomap.org'>OpenTopoMap</a> (<a href='https://creativecommons.org/licenses/by-sa/3.0/'>CC-BY-SA</a>)",
      maxZoom: 17,
    }
  ).addTo(smallMap);
  
  // Update small map when nearest station changes
  updateSmallMap();
};
// Search location
const searchLocation = () => {
  searchMarkers.value.forEach((marker) => marker.remove());
  searchMarkers.value = [];
  if (searchResults.value.length > 0) {
    searchResults.value.forEach((result) => {
      const isFav = isFavorite(result);
      const marker = L.marker([result.lat, result.lon], {
        icon: L.divIcon({
          html: `
            <div style="position: relative; z-index: 1001;">
              <div style="background: white; border: 2px solid #3b82f6; border-radius: 50%; padding: 2px; box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 16px; height: 16px; display: flex; align-items: center; justify-content: center;">
                <div style="width: 6px; height: 6px; background: #3b82f6; border-radius: 50%;"></div>
              </div>
              <div style="position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: #1f2937; color: white; padding: 1px 2px; border-radius: 2px; font-size: 8px; white-space: nowrap; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                ${result.aqi || "N/A"}
              </div>
              ${isFav ? `
                <div style="position: absolute; top: -6px; right: -6px; background: #FFD700; border-radius: 50%; width: 8px; height: 8px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-star" style="color: #000; font-size: 5px;"></i>
                </div>
              ` : ''}
            </div>`,
          className: "custom-search-marker",
          iconSize: [16, 16],
          iconAnchor: [8, 8],
        }),
      }).addTo(map);
      marker.bindPopup(`
        <div style="text-align: center; font-family: system-ui, -apple-system, sans-serif;">
          <h4 style="margin: 0 0 2px 0; font-size: 10px; font-weight: 600;">${result.name} ${isFav ? '<i class="fas fa-star text-yellow-400"></i>' : ''}</h4>
          <div style="color: ${getColor(result.aqi, "aqi")}; font-weight: 600; font-size: 10px;">AQI: ${result.aqi || "N/A"}</div>
        </div>
      `);
      searchMarkers.value.push(marker);
    });
  }
};
// Clear search
const clearSearch = () => {
  searchQuery.value = "";
  searchMarkers.value.forEach((marker) => marker.remove());
  searchMarkers.value = [];
};
// Go to location
const goToLocation = (result) => {
  clearSearch();
  map.setView([result.lat, result.lon], 10);
  const marker = markerMap.value[result.name];
  if (marker) {
    marker.openPopup();
  }
};
// Zoom controls
const zoomIn = () => map.zoomIn();
const zoomOut = () => map.zoomOut();
// Initialize map
const initMap = () => {
  map = L.map("map", {
    center: [20, 100],
    zoom: 4,
    zoomControl: false,
    scrollWheelZoom: true,
  });
  
  L.tileLayer(
    "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
    {
      attribution: "Map data: &copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors, <a href='http://viewfinderpanoramas.org'>SRTM</a> | Map style: &copy; <a href='https://opentopomap.org'>OpenTopoMap</a> (<a href='https://creativecommons.org/licenses/by-sa/3.0/'>CC-BY-SA</a>)",
      maxZoom: 17,
    }
  ).addTo(map);
};
// Calculate distance between two coordinates
const calculateDistance = (lat1, lon1, lat2, lon2) => {
  const R = 6371; // Radius of the earth in km
  const dLat = deg2rad(lat2 - lat1);
  const dLon = deg2rad(lon2 - lon1);
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
    Math.sin(dLon/2) * Math.sin(dLon/2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
  const d = R * c; // Distance in km
  return d;
};
const deg2rad = (deg) => {
  return deg * (Math.PI/180);
};
// Center main map on user location
const centerMapOnUser = () => {
  if (userLocation.value && map) {
    map.setView([userLocation.value.lat, userLocation.value.lon], 10);
  }
};
// Detect user location
const detectUserLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        userLocation.value = { lat, lon };
        
        if (map) {
          map.setView([lat, lon], 5);
          L.marker([lat, lon]).addTo(map).bindPopup(`You are here! (Last updated: 02:55 PM +07, Aug 21, 2025)`).openPopup();
        }
        
        // Update small map after user location is detected
        updateSmallMap();
      },
      (error) => {
        console.warn("Geolocation error:", error.message);
        // Set a default location if geolocation fails
        userLocation.value = { lat: 11.5564, lon: 104.9282 }; // Default to Phnom Penh
        updateSmallMap();
      }
    );
  } else {
    console.warn("Geolocation is not supported by this browser.");
    // Set a default location if geolocation is not supported
    userLocation.value = { lat: 11.5564, lon: 104.9282 }; // Default to Phnom Penh
    updateSmallMap();
  }
};
// City coordinates helper
const getCityCoordinates = (cityName, countryCode) => {
  const cityCoords = {
    'phnom penh': { lat: 11.5564, lon: 104.9282 },
    'new york': { lat: 40.7128, lon: -74.0060 },
    'london': { lat: 51.5074, lon: -0.1278 },
    'paris': { lat: 48.8566, lon: 2.3522 },
    'tokyo': { lat: 35.6762, lon: 139.6503 },
    'beijing': { lat: 39.9042, lon: 116.4074 },
    'moscow': { lat: 55.7558, lon: 37.6173 },
    'cairo': { lat: 30.0444, lon: 31.2357 },
    'sydney': { lat: -33.8688, lon: 151.2093 },
    'rio de janeiro': { lat: -22.9068, lon: -43.1729 },
    'bangkok': { lat: 13.7563, lon: 100.5018 },
    'singapore': { lat: 1.3521, lon: 103.8198 },
    'kuala lumpur': { lat: 3.1390, lon: 101.6869 },
    'jakarta': { lat: -6.2088, lon: 106.8456 },
    'manila': { lat: 14.5995, lon: 120.9842 },
    'hanoi': { lat: 21.0278, lon: 105.8342 },
    'ho chi minh city': { lat: 10.8231, lon: 106.6297 },
    'seoul': { lat: 37.5665, lon: 126.9780 },
    'delhi': { lat: 28.6139, lon: 77.2090 },
    'mumbai': { lat: 19.0760, lon: 72.8777 },
  };
  const normalizedName = cityName.toLowerCase();
  return cityCoords[normalizedName] || {
    lat: getApproximateLat(countryCode),
    lon: getApproximateLon(countryCode)
  };
};
const getApproximateLat = (countryCode) => {
  const countryCoords = {
    'us': 39.8283, 'gb': 54.7023, 'fr': 46.6033, 'de': 51.1657, 'it': 41.8719,
    'es': 40.4637, 'cn': 35.8617, 'in': 20.5937, 'jp': 36.2048, 'br': -14.2350,
    'ru': 61.5240, 'ca': 56.1304, 'au': -25.2744, 'kh': 12.5657, 'th': 15.8700,
    'sg': 1.3521, 'my': 4.2105, 'id': -0.7893, 'ph': 12.8797, 'vn': 14.0583,
    'kr': 35.9078, 'mm': 21.9162, 'la': 19.8563, 'bd': 23.6850, 'np': 28.3949,
  };
  return countryCoords[countryCode.toLowerCase()] || 0;
};
const getApproximateLon = (countryCode) => {
  const countryCoords = {
    'us': -98.5795, 'gb': -3.2766, 'fr': 1.8883, 'de': 10.4515, 'it': 12.5674,
    'es': -3.7492, 'cn': 104.1954, 'in': 78.9629, 'jp': 138.2529, 'br': -51.9253,
    'ru': 105.3188, 'ca': -106.3468, 'au': 133.7751, 'kh': 104.9910, 'th': 100.9925,
    'sg': 103.8198, 'my': 101.9758, 'id': 113.9213, 'ph': 121.7740, 'vn': 108.2772,
    'kr': 127.7669, 'mm': 95.9560, 'la': 102.4955, 'bd': 90.3563, 'np': 84.1240,
  };
  return countryCoords[countryCode.toLowerCase()] || 0;
};
// Enhanced search location handler for navbar integration
const handleSearchLocationSelected = (event) => {
  const location = event.detail
  if (location && location.lat && location.lon) {
    map.setView([location.lat, location.lon], 12)
    
    // Add special search marker with enhanced styling
    const searchMarker = L.marker([location.lat, location.lon], {
      icon: L.divIcon({
        html: `
          <div style="position: relative;">
            <div style="background: #3b82f6; border: 2px solid #fff; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
              <i class="fas fa-search" style="color: white; font-size: 8px;"></i>
            </div>
            <div style="position: absolute; top: -24px; left: 50%; transform: translateX(-50%); background: #1f2937; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
              <i class="fas fa-map-marker-alt mr-1"></i>${location.name}
            </div>
          </div>`,
        className: "navbar-search-marker",
        iconSize: [16, 16],
        iconAnchor: [8, 8],
      }),
    }).addTo(map)
    
    // Enhanced popup content with location details
    const popupContent = `
      <div style="font-family: 'Arial', sans-serif; max-width: 200px; text-align: center;">
        <div style="font-weight: 700; font-size: 14px; color: #000000; margin-bottom: 8px;">
          <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>${location.name}
        </div>
        <div style="font-size: 11px; color: #666; margin-bottom: 8px;">
          ${location.country}
        </div>
        ${location.aqi ? `
          <div style="margin-bottom: 8px;">
            <span style="font-size: 10px; color: #666;">Air Quality Index:</span><br>
            <span style="font-size: 14px; font-weight: bold; color: ${getColor(location.aqi, 'aqi')};">
              ${location.aqi} - ${getStatus(location.aqi, 'aqi')}
            </span>
          </div>
        ` : `
          <div style="font-size: 11px; color: #666; margin-bottom: 8px;">
            ${location.type === 'geolocation' ? 'Your Current Location' : 'Search Result Location'}
          </div>
        `}
        <div style="font-size: 10px; color: #999;">
          ${location.lat?.toFixed(4)}, ${location.lon?.toFixed(4)}
        </div>
      </div>
    `
    
    searchMarker.bindPopup(popupContent).openPopup()
    
    // Auto-remove marker after 10 seconds
    setTimeout(() => {
      searchMarker.remove()
    }, 10000)
  }
}
// Export data function
const exportData = (type) => {
  let data = [];
  let filename = '';
  let headers = ['name', 'aqi', 'pm25', 'pm10', 'no2', 'co', 'o3', 'temperature', 'humidity', 'pressure', 'wind_speed'];
  
  const escapeCsvValue = (value) => {
    if (value == null) return '';
    const str = String(value);
    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str;
  };
  
  switch(type) {
    case 'favorites':
      data = favorites.value.map(item => 
        headers.map(header => escapeCsvValue(item[header])).join(',')
      );
      data.unshift(headers.join(','));
      filename = 'favorites.csv';
      break;
    case 'temperature':
      if (nearestStation.value) {
        data = [headers.map(header => escapeCsvValue(nearestStation.value[header])).join(',')];
        data.unshift(headers.join(','));
      }
      filename = 'temperature.csv';
      break;
    case 'aqi':
      if (nearestStation.value) {
        data = [headers.map(header => escapeCsvValue(nearestStation.value[header])).join(',')];
        data.unshift(headers.join(','));
      }
      filename = 'aqi.csv';
      break;
    case 'health':
      if (nearestStation.value) {
        data = [headers.map(header => escapeCsvValue(nearestStation.value[header])).join(',')];
        data.unshift(headers.join(','));
      }
      filename = 'health-status.csv';
      break;
  }
  
  const blob = new Blob([data.join('\n')], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};
// Lifecycle hooks with enhanced search integration
onMounted(() => {
  initMap();
  initSmallMap();
  fetchAQIData();
  fetchPhnomPenhAQI();
  fetchFavorites();
  detectUserLocation();
  
  // Listen for navbar search selections
  window.addEventListener('location-search-selected', handleSearchLocationSelected)
  
  // Check for stored search location on mount (for page navigation)
  const storedLocation = localStorage.getItem('selectedSearchLocation');
  if (storedLocation) {
    try {
      const location = JSON.parse(storedLocation);
      handleSearchLocationSelected({ detail: location });
      localStorage.removeItem('selectedSearchLocation');
    } catch (e) {
      console.error('Error parsing stored search location:', e);
    }
  }
  
  // Periodic data refresh
  setInterval(() => {
    fetchAQIData();
    fetchPhnomPenhAQI();
    fetchFavorites();
  }, 30000);
});
watch(selectedPollutant, () => {
  renderMarkers();
});
watch(nearestStation, () => {
  updateSmallMap();
}, { deep: true });
// Cleanup event listeners
onUnmounted(() => {
  window.removeEventListener('location-search-selected', handleSearchLocationSelected);
  if (smallMap) {
    smallMap.remove();
  }
});
</script>
<style scoped>
#map {
  z-index: 0;
}
#small-map {
  z-index: 0;
}
.custom-search-marker {
  z-index: 1001 !important;
}
button {
  transition: all 0.2s ease-in-out;
}
button:hover {
  transform: translateY(-1px);
}
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
.hover\:shadow-xl:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
.hover\:shadow-lg:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>