<template>
  <div class="bg-gradient-page">
    <!-- Top bar -->
    <div class="top-bar">
      <button @click="$router.back()" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        {{$t('CityDetail.Back')}}
      </button>
      <div class="last-updated">
        <span>{{$t('CityDetail.Lastupdated')}}:</span>
        <span>{{ lastUpdated }}</span>
      </div>
    </div>

    <!-- Main Card -->
    <div class="card-container">
      <div class="main-card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex items-center gap-3">
            <img :src="cityData?.flag" alt="Flag" class="city-flag" />
            <div>
              <h2 class="city-name">{{ cityData?.name }}</h2>
              <div class="city-coordinates">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a8 8 0 1011.314-11.314l-4.243 4.243a4 4 0 00-5.657 5.657z" />
                </svg>
                <span>{{ cityData?.lat }}°, {{ cityData?.lon }}°</span>
              </div>
            </div>
          </div>
          <div class="flex flex-col items-center">
            <span class="text-gray-500 text-xs mb-1">{{$t('CityDetail.CurrentAQI')}}</span>
            <div class="flex items-center gap-2">
              <div :class="aqiBadge(cityData?.aqi) + ' aqi-badge'">
                <span class="aqi-badge-text">{{ cityData?.aqi ?? "N/A" }}</span>
              </div>
              <span class="aqi-text-badge" :class="aqiTextBadge(cityData?.aqi)">
                {{ getAQIDescription(cityData?.aqi) }}
              </span>
            </div>
            <button @click="toggleFavourite" class="favourite-button" :class="isFavourite ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'">
              <svg v-if="isFavourite" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
              {{ isFavourite ? 'Remove' : 'Add' }}
            </button>
          </div>    
        </div>
      </div>
    </div>

    <!-- Weather Conditions -->
    <div class="card-container">
      <div class="weather-card">
        <div class="weather-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 014-4h1a4 4 0 014-4h0a4 4 0 014 4h1a4 4 0 014 4v1a4 4 0 01-4 4H7a4 4 0 01-4-4v-1z" />
          </svg>
          {{$t('CityDetail.WeatherConditions')}}
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="weather-metric">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
              <defs>
                <linearGradient id="gradTemp" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#f87171" />
                  <stop offset="100%" stop-color="#fca5a5" />
                </linearGradient>
              </defs>
              <path fill="url(#gradTemp)" d="M14 14.76V4a2 2 0 10-4 0v10.76A4 4 0 1014 14.76zM12 20a2 2 0 002-2h-4a2 2 0 002 2z" />
            </svg>
            <span class="weather-value">{{ cityData?.temperature ?? "N/A" }}°C</span>
            <span class="weather-label">{{$t('CityDetail.Temperature')}}</span>
          </div>
          <div class="weather-metric">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24">
              <defs>
                <linearGradient id="gradHum" x1="0%" y1="0%" x2="0%" y2="100%">
                  <stop offset="0%" stop-color="#22d3ee" />
                  <stop offset="100%" stop-color="#06b6d4" />
                </linearGradient>
              </defs>
              <path fill="url(#gradHum)" d="M12 2.69l.59.81C16.14 8.28 18 12 18 15a6 6 0 11-12 0c0-3 1.86-6.72 5.41-11.50l.59-.81z" />
            </svg>
            <span class="weather-value">{{ cityData?.humidity ?? "N/A" }}%</span>
            <span class="weather-label">{{$t('CityDetail.Humidity')}}</span>
          </div>
          <div class="weather-metric">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="12" cy="12" r="10" stroke-width="2" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
            </svg>
            <span class="weather-value">{{ cityData?.pressure ?? "N/A" }}</span>
            <span class="weather-label">{{$t('CityDetail.Pressure')}}</span>
          </div>
          <div class="weather-metric">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h13a4 4 0 100-8 4 4 0 00-4 4m-6 8h9a2 2 0 110 4 2 2 0 01-2-2" />
            </svg>
            <span class="weather-value">{{ cityData?.wind_speed ?? cityData?.wind ?? "N/A" }}</span>
            <span class="weather-label">{{$t('CityDetail.Wind')}}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Air Quality Metrics Table -->
    <div class="card-container">
      <div class="aqi-table-card">
        <div class="aqi-table-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h13a4 4 0 100-8 4 4 0 00-4 4m-6 8h9a2 2 0 110 4 2 2 0 01-2-2" />
          </svg>
          {{$t('CityDetail.AirQualityMetrics')}}
        </div>
        <table class="w-full text-xs">
          <thead>
            <tr class="text-teal-700 font-semibold">
              <th class="py-2 px-3 text-left">{{$t('CityDetail.Pollutant')}}</th>
              <th class="py-2 px-3 text-left">{{$t('CityDetail.Value')}}</th>
              <th class="py-2 px-3 text-left">{{$t('CityDetail.Unit')}}</th>
              <th class="py-2 px-3 text-left">{{$t('CityDetail.Status')}}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in pollutants" :key="row.label" class="transition">
              <td class="py-3 px-3 font-medium text-teal-900">{{ row.label }}</td>
              <td class="py-3 px-3 text-gray-800">{{ row.value }}</td>
              <td class="py-3 px-3 text-gray-500">{{ row.unit }}</td>
              <td class="py-3 px-3">
                <span class="status-badge" :class="getStatusClass(row.value)">
                  {{ getStatusLabel(row.value) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pollutant Guide -->
    <div v-if="cityData" class="card-container">
      <div class="pollutant-guide-card">
        <div class="font-semibold text-base mb-3">{{$t('CityDetail.Top3Pollutants')}}</div>
        <div v-for="pollutant in topPollutants" :key="pollutant.label" class="pollutant-item">
          <div class="pollutant-label">{{ pollutant.label }}</div>
          <div class="pollutant-description">{{ pollutant.description }}</div>
          <div class="pollutant-health">{{$t('CityDetail.HealthImpact')}} :{{ pollutant.healthImpact }}</div>
          <div class="pollutant-value">{{$t('CityDetail.Value')}}: {{ pollutant.value }} {{ pollutant.unit }}</div>
        </div>
      </div>
    </div>

    <!-- Health Advisory -->
    <div class="card-container">
      <div class="health-advisory-card">
        <div class="health-advisory-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
          </svg>
          {{$t('CityDetail.HealthAdvisory')}}
        </div>
        <div :class="aqiAdvisoryBox(cityData?.aqi) + ' advisory-box'">
          <div class="font-bold mb-1 text-ml">{{ getAQIDescription(cityData?.aqi) }}</div>
          <div class="text-sm">{{ getAQIAdvice(cityData?.aqi) }}</div>
        </div>
        <div class="mt-3 text-xs text-gray-700">
          <div class="font-semibold mb-1 text-xl">{{$t('CityDetail.GeneralRecommendations')}}:</div>
          <ul class="recommendations-list ml-5 space-y-1 text-sm">
            <li>{{$t('CityDetail.CheckAQIbeforeoutdooractivities')}}</li>
            <li>{{$t('CityDetail.Keepwindowsclosedduringhighpollution')}}</li>
            <li>{{$t('CityDetail.Useairpurifiersindoors')}}</li>
            <li>{{$t('CityDetail.Wearmaskswhennecessary')}}</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Loading & Error -->
    <div v-if="loading" class="loading-message">
      {{$t('CityDetail.Loadingcitydata')}}...
    </div>
    <div v-else-if="!cityData" class="error-message">
      {{$t('CityDetail.Citydatanotfound')}}.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";
import { useAuthStore } from "@/stores/airQuality";

// --------------------------
// ROUTE & STATE
// --------------------------
const route = useRoute();
const cityIdParam = route.params.id;
const cityData = ref(null);
const loading = ref(false);
const lastUpdated = ref("");
const favourites = ref([]);
const auth = useAuthStore();

// --------------------------
// HELPER FUNCTIONS
// --------------------------
const assignIDs = (data) =>
  data.map((station, index) => ({
    ...station,
    id: station.id || index + 1,
  }));

// --------------------------
// AQI FUNCTIONS
// --------------------------
const getAQIDescription = (aqi) => {
  const val = Number(aqi);
  if (isNaN(val)) return "Unknown";
  if (val <= 50) return "Good";
  if (val <= 100) return "Moderate";
  if (val <= 150) return "Unhealthy for Sensitive Groups";
  if (val <= 200) return "Unhealthy";
  if (val <= 300) return "Very Unhealthy";
  return "Hazardous";
};

const getAQIAdvice = (aqi) => {
  const val = Number(aqi);
  if (isNaN(val)) return "";
  if (val <= 50) return "Air quality is satisfactory.";
  if (val <= 100)
    return "Sensitive individuals should consider limiting prolonged outdoor exertion.";
  if (val <= 150)
    return "People with respiratory issues should reduce outdoor activities.";
  if (val <= 200)
    return "Everyone may begin to experience health effects; limit outdoor exertion.";
  if (val <= 300)
    return "Health warnings of emergency conditions. Avoid outdoor activities.";
  return "Serious health effects. Remain indoors and avoid all outdoor exertion.";
};

const aqiBadge = (aqi) => {
  const val = Number(aqi);
  if (isNaN(val)) return "bg-gray-400";
  if (val <= 50) return "bg-green-400 text-white";
  if (val <= 100) return "bg-yellow-400 text-gray-900";
  if (val <= 150) return "bg-orange-400 text-white";
  if (val <= 200) return "bg-red-400 text-white";
  if (val <= 300) return "bg-purple-500 text-white";
  return "bg-rose-700 text-white";
};

const aqiTextBadge = (aqi) => {
  const val = Number(aqi);
  if (isNaN(val)) return "border-gray-400 text-gray-600";
  if (val <= 50) return "border-green-400 text-green-700";
  if (val <= 100) return "border-yellow-400 text-yellow-700";
  if (val <= 150) return "border-orange-400 text-orange-700";
  if (val <= 200) return "border-red-400 text-red-700";
  if (val <= 300) return "border-purple-500 text-purple-700";
  return "border-rose-700 text-rose-700";
};

const aqiAdvisoryBox = (aqi) => {
  const val = Number(aqi);
  if (isNaN(val)) return "bg-gray-100 text-gray-700 p-2 rounded-md";
  if (val <= 50) return "bg-green-100 text-green-800 p-2 rounded-md";
  if (val <= 100) return "bg-yellow-100 text-yellow-800 p-2 rounded-md";
  if (val <= 150) return "bg-orange-100 text-orange-800 p-2 rounded-md";
  if (val <= 200) return "bg-red-100 text-red-800 p-2 rounded-md";
  if (val <= 300) return "bg-purple-100 text-purple-800 p-2 rounded-md";
  return "bg-rose-100 text-rose-800 p-2 rounded-md";
};

// --------------------------
// POLLUTANT STATUS FUNCTIONS
// --------------------------
const getStatusLabel = (value) => {
  if (value === "N/A") return "No Data";
  const v = Number(value);
  if (v <= 50) return "Good";
  if (v <= 100) return "Moderate";
  if (v <= 150) return "Unhealthy for Sensitive Groups";
  if (v <= 200) return "Unhealthy";
  if (v <= 300) return "Very Unhealthy";
  return "Hazardous";
};

const getStatusClass = (value) => {
  if (value === "N/A") return "bg-gray-200 text-gray-800";
  const v = Number(value);
  if (v <= 50) return "bg-green-200 text-green-800";
  if (v <= 100) return "bg-yellow-200 text-yellow-800";
  if (v <= 150) return "bg-orange-200 text-orange-800";
  if (v <= 200) return "bg-red-200 text-red-800";
  if (v <= 300) return "bg-purple-200 text-purple-800";
  return "bg-rose-300 text-rose-900";
};

// --------------------------
// COMPUTED: pollutants array
// --------------------------
const pollutants = computed(() => [
  { label: "PM2.5", value: cityData.value?.pm25 ?? "N/A", unit: "µg/m³" },
  { label: "PM10", value: cityData.value?.pm10 ?? "N/A", unit: "µg/m³" },
  { label: "O3", value: cityData.value?.o3 ?? "N/A", unit: "µg/m³" },
  { label: "NO2", value: cityData.value?.no2 ?? "N/A", unit: "µg/m³" },
  { label: "SO2", value: cityData.value?.so2 ?? "N/A", unit: "µg/m³" },
  { label: "CO", value: cityData.value?.co ?? "N/A", unit: "mg/m³" },
]);

// Example dynamic data for pollutants
// Computed array with descriptions and health impact
const pollutantsGuide = computed(() => [
  {
    label: "PM2.5",
    value: cityData.value?.pm25 ?? 0,
    unit: "µg/m³",
    description: cityData.value?.pm25Description ?? "Fine particles that can penetrate deep into lungs",
    healthImpact: cityData.value?.pm25Health ?? "Respiratory and cardiovascular issues",
  },
  {
    label: "PM10",
    value: cityData.value?.pm10 ?? 0,
    unit: "µg/m³",
    description: cityData.value?.pm10Description ?? "Coarse particles that affect upper respiratory system",
    healthImpact: cityData.value?.pm10Health ?? "Throat and lung irritation",
  },
  {
    label: "O3",
    value: cityData.value?.o3 ?? 0,
    unit: "µg/m³",
    description: cityData.value?.o3Description ?? "Ground-level ozone formed by chemical reactions",
    healthImpact: cityData.value?.o3Health ?? "Breathing difficulties, chest pain",
  },
  {
    label: "NO2",
    value: cityData.value?.no2 ?? 0,
    unit: "µg/m³",
    description: cityData.value?.no2Description ?? "Nitrogen dioxide from combustion sources",
    healthImpact: cityData.value?.no2Health ?? "Respiratory irritation",
  },
  {
    label: "SO2",
    value: cityData.value?.so2 ?? 0,
    unit: "µg/m³",
    description: cityData.value?.so2Description ?? "Sulfur dioxide from burning fossil fuels",
    healthImpact: cityData.value?.so2Health ?? "Lung inflammation, asthma aggravation",
  },
  {
    label: "CO",
    value: cityData.value?.co ?? 0,
    unit: "mg/m³",
    description: cityData.value?.coDescription ?? "Carbon monoxide from incomplete combustion",
    healthImpact: cityData.value?.coHealth ?? "Reduces oxygen transport in blood",
  },
]);

// Top 3 highest pollutants
const topPollutants = computed(() => {
  return pollutantsGuide.value
    .filter(p => p.value !== "N/A")       // Ignore missing data
    .sort((a, b) => b.value - a.value)    // Sort descending
    .slice(0, 3);                         // Take top 3
});

// --------------------------
// FAVOURITE FUNCTIONS
// --------------------------
const isFavourite = computed(() => {
  return favourites.value.some(city => city.name === cityData.value?.name);
});

// LOAD FAVOURITES FROM BACKEND
// --------------------------
const loadFavourites = async () => {
  try {
    const { data } = await axios.get("http://127.0.0.1:8000/api/favourites", {
      headers: { Authorization: `Bearer ${auth.token}` },
    });

    // Store favourites using city_name, id, and flag
    favourites.value = data.map(f => ({
      id: f.id, // Include the favourite ID from backend
      name: f.city_name,
      flag: `https://flagcdn.com/w160/${f.country_code.toLowerCase()}.png`,
    }));
  } catch (err) {
    console.error("Failed to load favourites:", err);
  }
};

const toggleFavourite = async () => {
  if (!cityData.value) return;

  try {
    if (isFavourite.value) {
      // Find the favourite entry to get its ID
      const favourite = favourites.value.find(c => c.name === cityData.value.name);
      if (!favourite || !favourite.id) {
        throw new Error("Favourite ID not found");
      }

      // Remove from backend using the favourite ID
      await axios.delete(
        `http://127.0.0.1:8000/api/favourites/${favourite.id}`,
        { headers: { Authorization: `Bearer ${auth.token}` } }
      );

      // Remove from local state
      favourites.value = favourites.value.filter(c => c.name !== cityData.value.name);

      Swal.fire({
        icon: "success",
        title: "Removed from favourites",
        text: "",
        customClass: {
          popup: "swal-custom",
          title: "swal-title",
          confirmButton: "swal-button",
        },
        showConfirmButton: false,
        timer: 1200,
      });
    } else {
      // Add to backend
      await axios.post(
        "http://127.0.0.1:8000/api/favourites",
        {
          city_name: cityData.value.name,
          country_code: cityData.value.flag
            ? cityData.value.flag.split("/").pop().split(".")[0]
            : "kh",
        },
        { headers: { Authorization: `Bearer ${auth.token}` } }
      );

      // Reload favourites to get the new favourite's ID
      await loadFavourites();

      Swal.fire({
        icon: "success",
        title: "Added to favourites",
        text: "",
        customClass: {
          popup: "swal-custom",
          title: "swal-title",
          confirmButton: "swal-button",
        },
        showConfirmButton: false,
        timer: 1200,
      });
    }
  } catch (err) {
    console.error("Error updating favourite:", err);
    Swal.fire({
      icon: "error",
      title: "Failed to update favourite",
      text: err.response?.data?.message || err.message,
      customClass: {
        popup: "swal-custom",
        title: "swal-title",
        confirmButton: "swal-button",
      },
      confirmButtonText: "OK",
    });
  }
};

// --------------------------
// FETCH CITY DATA
// --------------------------
const fetchCityData = async () => {
  loading.value = true;
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/aqi");
    let allCities = [];
    if (response.data.status === "ok" && Array.isArray(response.data.data)) {
      allCities = assignIDs(response.data.data);
    }

    try {
      const { data } = await axios.get(
        "http://127.0.0.1:8000/api/air-quality/phnom-penh"
      );
      const phnomPenhStation = {
        id: 9999,
        name: "Phnom Penh",
        lat: 11.562108,
        lon: 104.888535,
        aqi: data.AQI ?? "N/A",
        pm25: data.PM2_5 ?? "N/A",
        pm10: data.PM10 ?? "N/A",
        no2: data.NO2 ?? "N/A",
        co: data.CO ?? "N/A",
        o3: data.O3 ?? "N/A",
        so2: data.SO2 ?? "N/A",
        temperature: data.Temp_C ?? "N/A",
        humidity: data.Humidity_percent ?? "N/A",
        pressure: data.Pressure_hPa ?? "N/A",
        wind_speed: data.Wind_m_s ?? "N/A",
        flag: "https://flagcdn.com/w160/kh.png",
      };
      allCities = allCities.filter((c) => c.name !== "Phnom Penh");
      allCities.push(phnomPenhStation);
    } catch (err) {
      console.error("Failed to fetch Phnom Penh AQI:", err);
    }

    cityData.value = allCities.find((c) => c.id == cityIdParam) || null;
    lastUpdated.value = new Date().toLocaleString();
  } catch (error) {
    console.error("Error fetching city data:", error);
    cityData.value = null;
  } finally {
    loading.value = false;
  }
};

function getAQILevelKey(aqi) {
  const val = Number(aqi);
  if (isNaN(val)) return "good";
  if (val <= 50) return "good";
  if (val <= 100) return "moderate";
  if (val <= 150) return "usg";
  if (val <= 200) return "unhealthy";
  if (val <= 300) return "very_unhealthy";
  return "hazardous";
}

function getHealthMessage(aqi) {
  const stored = localStorage.getItem("aqiHealthMessages");
  const defaultMessages = {
    good: {
      public: "Air quality is good. Enjoy your outdoor activities!",
      sensitive: "No special precautions needed for sensitive groups.",
    },
    moderate: {
      public: "Air quality is moderate. Sensitive groups should take care.",
      sensitive: "Sensitive individuals should consider limiting prolonged outdoor exertion.",
    },
    unhealthySensitive: {
      public: "Unhealthy for sensitive groups. Limit prolonged outdoor exertion.",
      sensitive: "Sensitive groups should avoid outdoor activities.",
      actions: "Consider wearing masks and staying indoors.",
    },
    unhealthy: {
      public: "Unhealthy air quality. Everyone should reduce outdoor activities.",
      sensitive: "Critical alert for sensitive groups.",
      emergency: "Follow emergency precautions.",
      restrictions: "Outdoor activity restrictions in effect.",
    },
    veryUnhealthy: {
      public: "Very unhealthy air quality. Health alert for everyone.",
      sensitive: "Serious risk for sensitive groups.",
      emergency: "Avoid all outdoor activities. Follow emergency instructions.",
      restrictions: "Strict outdoor activity restrictions in effect.",
    },
    hazardous: {
      public: "Hazardous air quality. Health emergency for everyone.",
      sensitive: "Severe risk for sensitive groups.",
      emergency: "Remain indoors. Follow all emergency instructions.",
      restrictions: "All outdoor activities prohibited.",
    },
  };
  const messages = stored ? JSON.parse(stored) : defaultMessages;
  const keyMap = {
    good: "good",
    moderate: "moderate",
    usg: "unhealthySensitive",
    unhealthy: "unhealthy",
    very_unhealthy: "veryUnhealthy",
    hazardous: "hazardous",
  };
  let key = getAQILevelKey(aqi);
  key = keyMap[key] || key;

  const msg = messages[key] || {};
  let html = "<ul style='padding-left:1.2em'>";
  if (msg.public) html += `<li><strong>Public:</strong> ${msg.public}</li>`;
  if (msg.sensitive) html += `<li><strong>Sensitive Groups:</strong> ${msg.sensitive}</li>`;
  if (msg.actions) html += `<li><strong>Recommended Actions:</strong> ${msg.actions}</li>`;
  if (msg.emergency) html += `<li><strong>Emergency Actions:</strong> ${msg.emergency}</li>`;
  if (msg.restrictions) html += `<li><strong>Activity Restrictions:</strong> ${msg.restrictions}</li>`;
  html += "</ul>";
  return html;
}

function showHealthAlert(aqi, cityName) {
  Swal.fire({
    title: `Health Alert for ${cityName}`,
    html: getHealthMessage(aqi),
    icon: "warning",
    confirmButtonText: "OK",
    customClass: {
      popup: "swal-custom",
      title: "swal-title",
      htmlContainer: "swal-html",
      confirmButton: "swal-button",
    },
    buttonsStyling: false,
  });
}

// --------------------------
// ON MOUNT
// --------------------------
onMounted(() => {
  fetchCityData().then(() => {
    if (cityData.value) {
      showHealthAlert(cityData.value.aqi, cityData.value.name);
    }
    loadFavourites();
  });
});
</script>

<style scoped>
/* General Page Styling */
.bg-gradient-page {
  background: linear-gradient(135deg, #f0f4ff 0%, #e6f0fa 100%);
  min-height: 100vh;
  padding: 1.5rem 0.75rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Card Container for consistent width */
.card-container {
  max-width: 1000px;
  margin: 0 auto 1rem;
}

/* Top Bar */
.top-bar {
  animation: fadeInUp 0.5s ease-out;
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1000px;
  margin: 0 auto 1rem;
  padding: 0 0.75rem;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  background: #ffffff;
  color: #374151;
  font-weight: 500;
  border-radius: 0.375rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
  font-size: 0.75rem;
}

.back-button:hover {
  background: #f0f4ff;
  transform: translateY(-1px);
  box-shadow: 0 3px 5px rgba(0, 0, 0, 0.08);
}

.last-updated {
  background: #e5efff;
  color: #4b5563;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Main Card */
.main-card {
  animation: fadeInUp 0.6s ease-out 0.1s both;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.city-flag {
  width: 3.5rem;
  height: 2.25rem;
  object-fit: cover;
  border-radius: 0.25rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.city-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.125rem;
}

.city-coordinates {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  color: #6b7280;
  font-size: 0.75rem;
}

.aqi-badge {
  width: 3.5rem;
  height: 3.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: 700;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.aqi-badge-text {
  color: #000000;
  font-weight: 700;
}

.aqi-text-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 0.75rem;
  font-weight: 500;
  font-size: 0.75rem;
  border-width: 1px;
}

.favourite-button {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.375rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: white;
  margin-top: 0.75rem;
  transition: all 0.2s ease;
}

.favourite-button:hover {
  transform: scale(1.03);
}

.favourite-button svg {
  width: 1rem;
  height: 1rem;
}

/* Weather Conditions */
.weather-card {
  animation: fadeInUp 0.7s ease-out 0.2s both;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.weather-title {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1e40af;
  margin-bottom: 1rem;
}

.weather-metric {
  background: #f9faff;
  border-radius: 0.5rem;
  padding: 0.75rem;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease;
}

.weather-metric:hover {
  transform: translateY(-2px);
}

.weather-value {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0.375rem 0;
}

.weather-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
}

/* Air Quality Metrics Table */
.aqi-table-card {
  animation: fadeInUp 0.8s ease-out 0.3s both;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.aqi-table-title {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #0f766e;
  margin-bottom: 1rem;
}

table {
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
  table-layout: fixed;
}

th {
  background: #f9faff;
  padding: 0.75rem;
  font-weight: 600;
  color: #0f766e;
  border-bottom: 1px solid #e5efff;
  width: 25%; /* Equally distribute width */
}

tr {
  transition: all 0.2s ease;
}

tr:hover {
  background: #f9faff;
  transform: translateX(2px);
}

td {
  padding: 0.75rem;
  border-bottom: 1px solid #e5efff;
  width: 25%; /* Equally distribute width */
  word-wrap: break-word; /* Allow text to wrap if needed */
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
  transition: transform 0.2s ease;
}

.status-badge:hover {
  transform: scale(1.03);
}

/* Pollutant Guide */
.pollutant-guide-card {
  animation: fadeInUp 0.9s ease-out 0.4s both;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.pollutant-item {
  border-left-width: 3px;
  border-color: #3b82f6;
  padding: 0.75rem 0.75rem 0.75rem 1rem;
  margin-bottom: 1rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}

.pollutant-item:hover {
  background: #f9faff;
  border-color: #2563eb;
  transform: translateX(2px);
}

.pollutant-label {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.125rem;
}

.pollutant-description {
  font-size: 0.75rem;
  color: #6b7280;
  margin-bottom: 0.125rem;
}

.pollutant-health {
  font-size: 0.75rem;
  color: #dc2626;
  margin-bottom: 0.125rem;
}

.pollutant-value {
  font-size: 0.75rem;
  color: #6b7280;
}

/* Health Advisory */
.health-advisory-card {
  animation: fadeInUp 1s ease-out 0.5s both;
  background: #ffffff;
  border-radius: 0.75rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.health-advisory-title {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: #d97706;
  margin-bottom: 1rem;
}

.advisory-box {
  padding: 1rem;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}

.advisory-box:hover {
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

.recommendations-list li {
  position: relative;
  padding-left: 1.25rem;
}

.recommendations-list li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #10b981;
  font-weight: bold;
}

/* Loading & Error */
.loading-message, .error-message {
  animation: fadeInUp 0.5s ease-out;
  text-align: center;
  margin: 2rem auto;
  font-size: 0.875rem;
  font-weight: 500;
  max-width: 1000px;
}

.loading-message {
  color: #4b5563;
}

.error-message {
  color: #dc2626;
}

/* SweetAlert2 Custom Styles (Applied via customClass) */
.swal-custom {
  border-radius: 0.75rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  background: #ffffff;
  padding: 1rem;
  width: 320px;
}

.swal-title {
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.swal-html {
  font-size: 0.75rem;
  color: #4b5563;
  line-height: 1.4;
}

.swal-html ul {
  padding-left: 1.25em;
  margin: 0;
}

.swal-html ul li {
  margin-bottom: 0.375rem;
}

.swal-html ul li strong {
  color: #1e40af;
}

.swal-button {
  padding: 0.375rem 1rem;
  background: #3b82f6;
  color: #ffffff;
  border-radius: 0.375rem;
  font-weight: 500;
  font-size: 0.75rem;
  transition: all 0.2s ease;
}

.swal-button:hover {
  background: #2563eb;
  transform: translateY(-1px);
}
</style>