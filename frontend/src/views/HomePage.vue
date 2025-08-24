<template>
  <div class="container mx-auto p-4 space-y-4">
    <!-- Map -->
    <div class="bg-white shadow-lg rounded-xl p-4 relative">
      <h2 class="text-lg font-semibold mb-4 text-gray-800">{{ $t('home.airQualityMap') }}</h2>
      <div id="map" class="h-[400px] w-full overflow-hidden relative rounded-lg">
        <!-- Dynamic Legend on left -->
        <div class="absolute bottom-4 left-4 bg-white p-2 shadow-lg rounded-lg z-[1000] max-w-[160px]">
          <div class="text-xs font-semibold mb-2 text-gray-800">
            {{ legendTitle }}
          </div>
          <div class="space-y-1 text-xs">
            <div v-for="item in legendItems" :key="item.color" class="flex items-center">
              <div class="w-3 h-3 mr-2 rounded-sm" :style="{ backgroundColor: item.color }"></div>
              <span class="text-gray-700">{{ item.label }}</span>
            </div>
          </div>
        </div>
        <!-- Search control on top right -->
        <div class="absolute top-4 right-4 z-[1000]">
          <div class="bg-white shadow-lg rounded-lg overflow-hidden relative">
            <input type="text"
              :placeholder="$t('home.searchLocation')"
              class="px-3 py-2 w-56 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 rounded-lg"
              v-model="searchQuery" @keyup="searchLocation" />
            <button v-if="searchQuery" @click="clearSearch"
              class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-lg leading-none hover:text-gray-700 rounded-full w-5 h-5 flex items-center justify-center">
              ×
            </button>
          </div>
          <!-- Zoom controls -->
          <div class="flex justify-end mt-2 space-x-1">
            <button @click="zoomIn"
              class="bg-white shadow-lg rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200 flex items-center justify-center w-8 h-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd" />
              </svg>
            </button>
            <button @click="zoomOut"
              class="bg-white shadow-lg rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200 flex items-center justify-center w-8 h-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700" viewBox="0 0 20 20"
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
          class="absolute top-4 left-4 flex items-center gap-0.5 bg-gray-900 p-1 z-[1000] rounded-lg shadow-sm opacity-90">
          <button v-for="option in pollutantOptions" :key="option.value" @click="selectedPollutant = option.value"
            :class="[ 'p-1 hover:bg-gray-700 transition-all duration-200 flex items-center justify-center rounded-full',
              selectedPollutant === option.value ? 'bg-yellow-500 text-black' : ''
            ]">
            <span v-html="option.icon" class="w-3 h-3"></span>
          </button>
        </div>
        <!-- Search Results -->
        <div v-if="searchResults.length > 0"
          class="absolute top-20 right-4 bg-gray-900 shadow-xl rounded-lg z-[1000] w-64 border border-gray-700">
          <div class="px-3 py-1 border-b border-gray-700 bg-gray-800 rounded-t-lg">
            <h3 class="text-white font-medium text-xs">{{ $t('home.states') }}</h3>
          </div>
          <div class="max-h-80 overflow-y-auto">
            <div v-for="(result, index) in showAllResults ? searchResults : searchResults.slice(0, maxVisibleResults)"
              :key="result.name"
              class="flex items-center justify-between p-1.5 border-b border-gray-700 last:border-b-0 hover:bg-gray-800 cursor-pointer transition-all duration-200"
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
            <div v-if="searchResults.length > maxVisibleResults" class="p-1 border-t border-gray-700 bg-gray-800">
              <button @click="showAllResults = !showAllResults"
                class="w-full text-center text-xs text-gray-300 hover:text-white transition-all duration-200">
                {{ showAllResults ? $t('home.showLess') : $t('home.showAll', { count: searchResults.length }) }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Global Rankings -->
    <section class="mt-8 px-2 md:px-4 space-y-6">
      <h2 class="text-base font-semibold text-gray-800">{{ $t('home.globalRanking') }}</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <!-- Most Polluted -->
        <div class="bg-white rounded-2xl shadow-lg p-4 border border-red-200 transition-all duration-300 hover:shadow-xl">
          <h3 class="text-lg font-semibold text-red-600 mb-3">{{ $t('home.mostPolluted') }}</h3>
          <table class="w-full text-xs">
            <thead>
              <tr class="text-left text-gray-600 border-b">
                <th class="py-1">{{ $t('home.rank') }}</th>
                <th class="py-1">{{ $t('home.city') }}</th>
                <th class="py-1">{{ $t('home.status') }}</th>
                <th class="py-1 text-center">{{ $t('home.aqi') }}</th>
                <th class="py-1 text-center">{{ $t('home.favorite') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(city, index) in top10MostPolluted" :key="'polluted-' + index" class="hover:bg-red-50 transition-all duration-200">
                <td class="py-1">{{ index + 1 }}</td>
                <td class="py-1">{{ city.name }}</td>
                <td class="py-1">
                  <span class="inline-block px-2 py-0.5 rounded-full text-white text-xs font-medium"
                        :style="{ backgroundColor: getColorAQI(city.aqi) }">
                    {{ getStatusLabelAQI(city.aqi) }}
                  </span>
                </td>
                <td class="py-1 text-center">
                  <span class="inline-block px-2 py-0.5 rounded-full text-white text-xs font-medium"
                        :style="{ backgroundColor: getColorAQI(city.aqi) }">
                    {{ city.aqi }}
                  </span>
                </td>
                <td class="py-1 text-center">
                  <button
                    @click="toggleFavorite(city)"
                    class="px-2 py-1 rounded-lg text-white transition-all duration-200"
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

        <!-- Cleanest -->
        <div class="bg-white rounded-2xl shadow-lg p-4 border border-green-200 transition-all duration-300 hover:shadow-xl">
          <h3 class="text-lg font-semibold text-green-600 mb-3">{{ $t('home.cleanest') }}</h3>
          <table class="w-full text-xs">
            <thead>
              <tr class="text-left text-gray-600 border-b">
                <th class="py-1">{{ $t('home.rank') }}</th>
                <th class="py-1">{{ $t('home.city') }}</th>
                <th class="py-1">{{ $t('home.status') }}</th>
                <th class="py-1 text-center">{{ $t('home.aqi') }}</th>
                <th class="py-1 text-center">{{ $t('home.favorite') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(city, index) in top10LeastPolluted" :key="'cleanest-' + index" class="hover:bg-green-50 transition-all duration-200">
                <td class="py-1">{{ index + 1 }}</td>
                <td class="py-1">{{ city.name }}</td>
                <td class="py-1">
                  <span class="inline-block px-2 py-0.5 rounded-full text-white text-xs font-medium"
                        :style="{ backgroundColor: getColorAQI(city.aqi) }">
                    {{ getStatusLabelAQI(city.aqi) }}
                  </span>
                </td>
                <td class="py-1 text-center">
                  <span class="inline-block px-2 py-0.5 rounded-full text-white text-xs font-medium"
                        :style="{ backgroundColor: getColorAQI(city.aqi) }">
                    {{ city.aqi }}
                  </span>
                </td>
                <td class="py-1 text-center">
                  <button
                    @click="toggleFavorite(city)"
                    class="px-2 py-1 rounded-lg text-white transition-all duration-200"
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
    </section>
  </div>
</template>


<script setup>
import { onMounted, ref, watch, computed, nextTick } from "vue";
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
let markers = [];
const markerMap = ref({});
const searchMarkers = ref([]);
const showAllResults = ref(false);
const maxVisibleResults = ref(5);
const favorites = ref([]);
const auth = useAuthStore();

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

// Pollutant options
const pollutantOptions = [
  { value: "aqi", label: "AQI", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#FFCC00"/><path d="M12 12l4-4" stroke="#333" stroke-width="2" stroke-linecap="round"/></svg>` },
  { value: "pm25", label: "PM2.5", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="6" cy="12" r="1.5" fill="#888"/><circle cx="12" cy="8" r="1.5" fill="#888"/><circle cx="17" cy="14" r="2" fill="#888"/><circle cx="10" cy="16" r="1.5" fill="#888"/></svg>` },
  { value: "pm10", label: "PM10", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="5" cy="10" r="2" fill="#A0522D"/><circle cx="12" cy="7" r="3" fill="#A0522D"/><circle cx="18" cy="15" r="2.5" fill="#A0522D"/><circle cx="9" cy="17" r="1.5" fill="#A0522D"/></svg>` },
  { value: "no2", label: "NO₂", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="8" fill="#555"/><polygon points="4,10 8,4 12,10" fill="#777"/></svg>` },
  { value: "co", label: "CO", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="8" cy="12" r="4" fill="#FF4D4D"/><circle cx="17" cy="12" r="3" fill="#FF6666"/></svg>` },
  { value: "o3", label: "O₃", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="8" cy="12" r="3" fill="#3399FF"/><circle cx="15" cy="9" r="3" fill="#33CCFF"/><circle cx="15" cy="15" r="3" fill="#3399FF"/></svg>` },
  { value: "temperature", label: "Temperature", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="11" y="4" width="2" height="12" fill="#FF3300"/><circle cx="12" cy="18" r="3" fill="#FF3300"/></svg>` },
  { value: "humidity", label: "Humidity", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2.5l5 7a8 8 0 1 1-10 0z" fill="#3399FF"/></svg>` },
  { value: "pressure", label: "Pressure", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#66CC66"/><path d="M12 12l3-4" stroke="#333" stroke-width="2" stroke-linecap="round"/></svg>` },
  { value: "wind", label: "Wind", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 12h13a3 3 0 1 0 0-6M3 18h9a3 3 0 1 1 0 6" stroke="#33CCFF" stroke-width="2" stroke-linecap="round"/></svg>` }
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
    "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
    {
      attribution: "&copy; OpenStreetMap & CARTO",
      subdomains: "abcd",
      maxZoom: 19,
    }
  ).addTo(map);
};

// Detect user location
const detectUserLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        map.setView([lat, lon], 5);
        L.marker([lat, lon]).addTo(map).bindPopup(`You are here! (Last updated: 02:55 PM +07, Aug 21, 2025)`).openPopup();
      },
      (error) => {
        console.warn("Geolocation error:", error.message);
      }
    );
  } else {
    console.warn("Geolocation is not supported by this browser.");
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

// Lifecycle hooks
onMounted(() => {

  initMap();
  fetchAQIData();
  fetchPhnomPenhAQI();
  fetchFavorites();
  detectUserLocation();
  setInterval(() => {
    fetchAQIData();
    fetchPhnomPenhAQI();
    fetchFavorites();
  }, 30000);
});

watch(selectedPollutant, () => {
  renderMarkers();
});
</script>

<style scoped>
#map {
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
</style>