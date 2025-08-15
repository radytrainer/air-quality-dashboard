<template>
  <div class="bg-[#f6fafd] min-h-screen pb-10">
    <!-- Top bar -->
    <div class="flex justify-between items-center max-w-6xl mx-auto pt-8 pb-2">
      <button
        @click="$router.back()"
        class="flex items-center gap-1 px-4 py-2 bg-white text-gray-700 rounded shadow hover:bg-gray-100 transition"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-1"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>
        Back
      </button>
      <div class="text-gray-500 text-sm">
        <span>Last updated:</span>
        <span>{{ lastUpdated }}</span>
      </div>
    </div>

    <!-- Main Card -->
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-2">
      <div
        class="flex flex-col md:flex-row md:items-center md:justify-between gap-6"
      >
        <div class="flex items-center gap-4">
          <img
            :src="cityData?.flag"
            alt="Flag"
            class="w-16 h-12 object-cover rounded"
          />
          <div>
            <h2 class="text-2xl font-bold">{{ cityData?.name }}</h2>
            <div class="text-gray-500 text-base flex items-center gap-1">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a8 8 0 1011.314-11.314l-4.243 4.243a4 4 0 00-5.657 5.657z"
                />
              </svg>
              <span>{{ cityData?.lat }}°, {{ cityData?.lon }}°</span>
            </div>
          </div>
        </div>
        <div class="flex flex-col items-center">
          <span class="text-gray-500 text-sm mb-1">Current AQI</span>
          <div class="flex items-center gap-2">
            <div
              :class="
                aqiBadge(cityData?.aqi) +
                ' text-3xl font-bold rounded-full w-20 h-20 flex items-center justify-center'
              "
            >
              {{ cityData?.aqi ?? "N/A" }}
            </div>
            <span
              class="ml-2 px-3 py-1 rounded-full border text-base font-medium"
              :class="aqiTextBadge(cityData?.aqi)"
            >
              {{ getAQIDescription(cityData?.aqi) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Weather Conditions (longer x2) -->
    <div class="max-w-6xl mx-auto mt-8">
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col gap-4">
        <div class="font-semibold text-lg mb-2 flex items-center gap-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-blue-500 mb-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6v6a4 4 0 104 4V6a4 4 0 10-8 0v10a4 4 0 104 4"
            />
          </svg>
          Weather Conditions
        </div>
        <div class="grid grid-cols-2 md:grid-cols-8 gap-4">
          <div
            class="flex flex-col items-center bg-blue-50 rounded-lg p-3 col-span-2"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-cyan-500 mb-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 3.25C12 3.25 7 9.5 7 13a5 5 0 0010 0c0-3.5-5-9.75-5-9.75z"
              />
            </svg>
            <span class="text-blue-500 text-2xl"
              >{{ cityData?.temperature ?? "N/A" }}°C</span
            >
            <span class="text-gray-500 text-xs">Temperature</span>
          </div>
          <div
            class="flex flex-col items-center bg-blue-50 rounded-lg p-3 col-span-2"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-cyan-500 mb-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 3.25C12 3.25 7 9.5 7 13a5 5 0 0010 0c0-3.5-5-9.75-5-9.75z"
              />
            </svg>
            <span class="text-cyan-500 text-2xl"
              >{{ cityData?.humidity ?? "N/A" }}%</span
            >
            <span class="text-gray-500 text-xs">Humidity</span>
          </div>
          <div
            class="flex flex-col items-center bg-blue-50 rounded-lg p-3 col-span-2"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-gray-500 mb-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 21a9 9 0 100-18 9 9 0 000 18zm0-4v-4m0 0l3-3m-3 3l-3-3"
              />
            </svg>
            <span class="text-gray-700 text-2xl">{{
              cityData?.pressure ?? "N/A"
            }}</span>
            <span class="text-gray-500 text-xs">hPa</span>
          </div>
          <div
            class="flex flex-col items-center bg-green-50 rounded-lg p-3 col-span-2"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6 text-green-500 mb-1"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12h13a4 4 0 100-8 4 4 0 00-4 4m-6 8h9a2 2 0 110 4 2 2 0 01-2-2"
              />
            </svg>
            <span class="text-green-600 text-2xl">{{
              cityData?.wind_speed ?? cityData?.wind ?? "N/A"
            }}</span>
            <span class="text-gray-500 text-xs">m/s</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Air Quality Metrics Table -->
    <div class="max-w-6xl mx-auto mt-8">
      <div
        class="bg-gradient-to-br from-blue-50 via-green-50 to-teal-50 rounded-3xl shadow-lg p-6 flex flex-col gap-4 border border-white/50 backdrop-blur-sm"
      >
        <!-- Title -->
        <div
          class="font-semibold text-lg mb-4 flex items-center gap-2 text-teal-700"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-teal-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 3v18h18"
            />
          </svg>
          Air Quality Metrics
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm border-separate border-spacing-0">
            <thead>
              <tr class="text-teal-700 font-semibold">
                <th class="py-3 px-4 text-left">Pollutant</th>
                <th class="py-3 px-4 text-left">Value</th>
                <th class="py-3 px-4 text-left">Unit</th>
                <th class="py-3 px-4 text-left">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="row in [
                  {
                    label: 'PM2.5',
                    value: cityData?.pm25 ?? 'N/A',
                    unit: 'µg/m³',
                  },
                  {
                    label: 'PM10',
                    value: cityData?.pm10 ?? 'N/A',
                    unit: 'µg/m³',
                  },
                  { label: 'O3', value: cityData?.o3 ?? 'N/A', unit: 'µg/m³' },
                  {
                    label: 'NO2',
                    value: cityData?.no2 ?? 'N/A',
                    unit: 'µg/m³',
                  },
                  {
                    label: 'SO2',
                    value: cityData?.so2 ?? 'N/A',
                    unit: 'µg/m³',
                  },
                  { label: 'CO', value: cityData?.co ?? 'N/A', unit: 'mg/m³' },
                ]"
                :key="row.label"
                class="transition hover:bg-white/70 hover:shadow rounded-xl"
              >
                <td class="py-4 px-4 font-medium text-teal-900">
                  {{ row.label }}
                </td>
                <td class="py-4 px-4 text-gray-800">{{ row.value }}</td>
                <td class="py-4 px-4 text-gray-500">{{ row.unit }}</td>
                <td class="py-4 px-4">
                  <span
                    class="inline-block px-3 py-1 rounded-full bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-xs font-semibold shadow-sm"
                  >
                    Moderate
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pollutant Guide -->
    <div class="max-w-6xl mx-auto mt-8">
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col gap-3">
        <div class="font-semibold text-lg mb-2">Pollutant Guide</div>
        <div class="border-l-4 border-blue-500 pl-4 mb-4">
          <div class="font-bold">PM2.5</div>
          <div class="text-gray-600 text-sm">
            Fine particles that can penetrate deep into lungs
          </div>
          <div class="text-red-500 text-xs">
            Health Impact: Respiratory and cardiovascular issues
          </div>
        </div>
        <div class="border-l-4 border-blue-500 pl-4 mb-4">
          <div class="font-bold">PM10</div>
          <div class="text-gray-600 text-sm">
            Coarse particles that affect upper respiratory system
          </div>
          <div class="text-red-500 text-xs">
            Health Impact: Throat and lung irritation
          </div>
        </div>
        <div class="border-l-4 border-blue-500 pl-4 mb-4">
          <div class="font-bold">O3</div>
          <div class="text-gray-600 text-sm">
            Ground-level ozone formed by chemical reactions
          </div>
          <div class="text-red-500 text-xs">
            Health Impact: Breathing difficulties, chest pain
          </div>
        </div>
      </div>
    </div>

    <!-- Health Advisory at the end -->
    <div class="max-w-6xl mx-auto mt-8">
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col gap-3">
        <div class="font-semibold text-lg mb-2 flex items-center gap-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-yellow-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 9v2m0 4h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"
            />
          </svg>
          Health Advisory
        </div>
        <div :class="aqiAdvisoryBox(cityData?.aqi)">
          <div class="font-bold mb-1">
            {{ getAQIDescription(cityData?.aqi) }}
          </div>
          <div class="text-sm">
            {{ getAQIAdvice(cityData?.aqi) }}
          </div>
        </div>
        <div class="mt-3 text-xs text-gray-700">
          <div class="font-semibold mb-1">General Recommendations:</div>
          <ul class="list-disc ml-5 space-y-1">
            <li>Check AQI before outdoor activities</li>
            <li>Keep windows closed during high pollution</li>
            <li>Use air purifiers indoors</li>
            <li>Wear masks when necessary</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Loading & Error -->
    <div v-if="loading" class="text-center text-gray-600 mt-10">
      Loading city data...
    </div>
    <div v-else-if="!cityData" class="text-center text-red-600 mt-10">
      City data not found.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const route = useRoute();
const cityIdParam = route.params.id;
const cityData = ref(null);
const loading = ref(false);
const lastUpdated = ref("");

const assignIDs = (data) =>
  data.map((station, index) => ({
    ...station,
    id: station.id || index + 1,
  }));

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
  if (isNaN(val)) return "bg-gray-100 text-gray-700 p-3 rounded-lg";
  if (val <= 50) return "bg-green-100 text-green-800 p-3 rounded-lg";
  if (val <= 100) return "bg-yellow-100 text-yellow-800 p-3 rounded-lg";
  if (val <= 150) return "bg-orange-100 text-orange-800 p-3 rounded-lg";
  if (val <= 200) return "bg-red-100 text-red-800 p-3 rounded-lg";
  if (val <= 300) return "bg-purple-100 text-purple-800 p-3 rounded-lg";
  return "bg-rose-100 text-rose-800 p-3 rounded-lg";
};

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
  const messages = stored
    ? JSON.parse(stored)
    : {
        good: { public: "Air quality is good. Enjoy your outdoor activities!" },
        moderate: { public: "Air quality is moderate. Sensitive groups should take care." },
        unhealthySensitive: { public: "Unhealthy for sensitive groups. Limit prolonged outdoor exertion." },
        unhealthy: { public: "Unhealthy air quality. Everyone should reduce outdoor activities." },
      };
  const keyMap = {
    usg: "unhealthySensitive",
    very_unhealthy: "unhealthy",
    hazardous: "unhealthy",
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
    html: getHealthMessage(aqi), // Use html instead of text
    icon: "warning",
    confirmButtonText: "OK",
    customClass: {
      popup: "rounded-xl",
      title: "font-bold",
    },
  });
}

onMounted(() => {
  fetchCityData().then(() => {
    if (cityData.value) {
      showHealthAlert(cityData.value.aqi, cityData.value.name);
    }
  });
});
</script>

<style scoped>
.bg-[#f6fafd] {
  background-color: #f6fafd;
}
</style>
