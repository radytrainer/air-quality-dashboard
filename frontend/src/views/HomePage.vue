<template>
  <div class="container mx-auto p-6 space-y-6">
    <!-- Map -->
    <div class="bg-white shadow-lg rounded-xl p-6 relative">
      <h2 class="text-xl font-semibold mb-6 text-gray-800">Air Quality Map</h2>
      <div
        id="map"
        class="h-[500px] w-full overflow-hidden relative rounded-lg"
      >
        <!-- Dynamic Legend on left -->
        <div
          class="absolute bottom-4 left-4 bg-white p-4 shadow-lg rounded-lg z-[1000] max-w-[200px]"
        >
          <div class="text-sm font-semibold mb-3 text-gray-800">
            {{ legendTitle }}
          </div>
          <div class="space-y-2 text-xs">
            <div
              v-for="item in legendItems"
              :key="item.color"
              class="flex items-center"
            >
              <div
                class="w-4 h-4 mr-3 rounded-sm"
                :style="{ backgroundColor: item.color }"
              ></div>
              <span class="text-gray-700">{{ item.label }}</span>
            </div>
          </div>
        </div>

        <!-- Search control on top right - Made bigger -->
        <div class="absolute top-4 right-4 z-[1000]">
          <div class="bg-white shadow-lg rounded-lg overflow-hidden relative">
            <input
              type="text"
              placeholder="Search location..."
              class="px-4 py-3 w-64 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg"
              v-model="searchQuery"
              @keyup="searchLocation"
            />
            <button
              v-if="searchQuery"
              @click="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-xl leading-none hover:text-gray-700 rounded-full w-6 h-6 flex items-center justify-center"
            >
              ×
            </button>
          </div>

          <!-- Zoom controls moved under search and to the right -->
          <div class="flex justify-end mt-3 space-x-2">
            <button
              @click="zoomIn"
              class="bg-white shadow-lg rounded-lg p-2 hover:bg-gray-100 transition-colors flex items-center justify-center w-10 h-10"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-700"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
            <button
              @click="zoomOut"
              class="bg-white shadow-lg rounded-lg p-2 hover:bg-gray-100 transition-colors flex items-center justify-center w-10 h-10"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-700"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 10a1 1 0 011-1h8a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>

        <!-- Pollutant Selector in Map -->
        <div
          class="absolute top-4 left-4 flex items-center gap-1 bg-black text-white p-2 z-[1000] rounded-lg shadow-lg"
        >
          <button
            v-for="option in pollutantOptions"
            :key="option.value"
            @click="selectedPollutant = option.value"
            :class="[
              'p-1.5 hover:bg-gray-700 transition-colors flex items-center justify-center rounded-md text-xs',
              selectedPollutant === option.value
                ? 'bg-yellow-500 text-black'
                : '',
            ]"
            :title="option.label"
          >
            <span v-html="option.icon" class="w-4 h-4"></span>
          </button>
        </div>

        <!-- Search Results - Collapsible with "States" header like the provided image -->
        <div
          v-if="searchResults.length > 0"
          class="absolute top-20 right-4 bg-gray-900 shadow-xl rounded-lg z-[1000] w-72 border border-gray-700"
        >
          <!-- Added States header and show more/less functionality -->
          <div
            class="px-4 py-2 border-b border-gray-700 bg-gray-800 rounded-t-lg"
          >
            <h3 class="text-white font-medium text-sm">States</h3>
          </div>

          <div class="max-h-80 overflow-y-auto">
            <div
              v-for="(result, index) in showAllResults
                ? searchResults
                : searchResults.slice(0, maxVisibleResults)"
              :key="result.name"
              class="flex items-center justify-between p-2.5 border-b border-gray-700 last:border-b-0 hover:bg-gray-800 cursor-pointer transition-colors"
              @click="goToLocation(result)"
            >
              <div class="flex items-center space-x-2.5 flex-1 min-w-0">
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-white truncate">
                    {{ result.name }}, {{ result.country || "Unknown" }}
                  </p>
                </div>
              </div>
              <div class="flex-shrink-0 ml-2">
                <!-- Updated AQI badge styling to match the provided image -->
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold text-white min-w-[2.5rem] justify-center"
                  :style="{ backgroundColor: getColor(result.aqi, 'aqi') }"
                >
                  {{ result.aqi || "N/A" }}
                </span>
              </div>
            </div>

            <!-- Added show more/less button -->
            <div
              v-if="searchResults.length > maxVisibleResults"
              class="p-2 border-t border-gray-700 bg-gray-800"
            >
              <button
                @click="showAllResults = !showAllResults"
                class="w-full text-center text-xs text-gray-300 hover:text-white transition-colors"
              >
                {{
                  showAllResults
                    ? "Show Less"
                    : `Show All (${searchResults.length})`
                }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from "vue";
import { useRouter } from "vue-router";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import axios from "axios";

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

// Inline SVG icons
const pollutantOptions = [
  // AQI - green/yellow/red gauge
  { value: "aqi", label: "AQI", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#FFCC00"/><path d="M12 12l4-4" stroke="#333" stroke-width="2" stroke-linecap="round"/></svg>` },

  // PM2.5 - small gray particles
  { value: "pm25", label: "PM2.5", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="6" cy="12" r="1.5" fill="#888"/><circle cx="12" cy="8" r="1.5" fill="#888"/><circle cx="17" cy="14" r="2" fill="#888"/><circle cx="10" cy="16" r="1.5" fill="#888"/></svg>` },

  // PM10 - bigger brownish particles
  { value: "pm10", label: "PM10", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="5" cy="10" r="2" fill="#A0522D"/><circle cx="12" cy="7" r="3" fill="#A0522D"/><circle cx="18" cy="15" r="2.5" fill="#A0522D"/><circle cx="9" cy="17" r="1.5" fill="#A0522D"/></svg>` },

  // NO2 - dark gray factory
  { value: "no2", label: "NO₂", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="8" fill="#555"/><polygon points="4,10 8,4 12,10" fill="#777"/></svg>` },

  // CO - molecule circles in red
  { value: "co", label: "CO", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="8" cy="12" r="4" fill="#FF4D4D"/><circle cx="17" cy="12" r="3" fill="#FF6666"/></svg>` },

  // O3 - ozone in blue
  { value: "o3", label: "O₃", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="8" cy="12" r="3" fill="#3399FF"/><circle cx="15" cy="9" r="3" fill="#33CCFF"/><circle cx="15" cy="15" r="3" fill="#3399FF"/></svg>` },

  // Temperature - red thermometer
  { value: "temperature", label: "Temperature", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="11" y="4" width="2" height="12" fill="#FF3300"/><circle cx="12" cy="18" r="3" fill="#FF3300"/></svg>` },

  // Humidity - blue water droplet
  { value: "humidity", label: "Humidity", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2.5l5 7a8 8 0 1 1-10 0z" fill="#3399FF"/></svg>` },

  // Pressure - gauge in green
  { value: "pressure", label: "Pressure", icon: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#66CC66"/><path d="M12 12l3-4" stroke="#333" stroke-width="2" stroke-linecap="round"/></svg>` },

  // Wind - light blue flowing lines
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
const legendTitle = computed(
  () => `${selectedPollutant.value.toUpperCase()} Levels`
);

// --- FIX: searchResults must be at top level ---
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
      aqiData.value = assignIDs(data.data); // Assign IDs here
      renderMarkers();
    }
  } catch (error) {
    console.error("Error fetching AQI data:", error);
  }
};

const fetchPhnomPenhAQI = async () => {
  try {
    const { data } = await axios.get(
      "http://127.0.0.1:8000/api/air-quality/phnom-penh"
    );
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
      id: data.id || 9999, // Assign a unique id
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
    id: station.id || index + 1, // Use existing id or fallback to index
  }));
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

    if (
      selectedPollutant.value !== "aqi" &&
      (value === null || value === undefined)
    ) {
      return;
    }

    const color = getColor(value, selectedPollutant.value);
    const status = getStatus(value, selectedPollutant.value);
    const windValue = station.wind_speed || station.wind || "N/A";
    const renderRow = (label, val, unit = "") => {
      if (val === null || val === undefined || val === "N/A") return "";
      return `<div><strong>${label}:</strong> ${val}${unit}</div>`;
    };

    const popupContent = `
      <div>
           <strong>${station.name}</strong><br/>
           AQI: ${station.aqi}<br/>
      </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 11px; color: #000;">
          ${renderRow(selectedPollutant.value.toUpperCase(), value)}
          ${renderRow("Status", status)}
          ${renderRow("AQI", station.aqi)}
          ${renderRow("PM2.5", station.pm25)}
          ${renderRow("PM10", station.pm10)}
          ${renderRow("NO₂", station.no2)}
          ${renderRow("CO", station.co)}
          ${renderRow("O₃", station.o3)}
          ${renderRow("Temp", station.temperature, " °C")}
          ${renderRow("Humidity", station.humidity, " %")}
          ${renderRow("Pressure", station.pressure, " hPa")}
          ${renderRow("Wind", windValue, " m/s")}
        </div>
        <div style="margin-top: 6px; text-align: center;">
      <button id="view-detail-${station.id}" 
        style="color: #3b82f6; font-size: 12px; font-weight: 500; background: none; border: none; text-decoration: underline; cursor: pointer;">
        View City Details
      </button>
    </div>
      </div>
    `;

    const marker = L.circleMarker([station.lat, station.lon], {
      radius: 8,
      fillColor: color,
      color: "#fff",
      weight: 2,
      opacity: 1,
      fillOpacity: 0.8,
    }).addTo(map);

    marker.bindPopup(popupContent);

    // Add SPA-friendly navigation
    marker.on('popupopen', () => {
  const btn = document.getElementById(`view-detail-${station.id}`);
  if (btn) {
    btn.addEventListener('click', () => {
      router.push({ name: 'city-detail', params: { id: station.id } });
    });
  }
    });

    markers.push(marker);
    markerMap.value[station.name] = marker;
  });
};

// Search location with results panel
const searchLocation = () => {
  searchMarkers.value.forEach((marker) => marker.remove());
  searchMarkers.value = [];

  if (searchResults.value.length > 0) {
    searchResults.value.forEach((result) => {
      const marker = L.marker([result.lat, result.lon], {
        icon: L.divIcon({
          html: `<div style="position: relative; z-index: 1001;">
                   <div style="background: white; border: 2px solid #3b82f6; border-radius: 50%; padding: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                     <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%;"></div>
                   </div>
                   <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); background: #1f2937; color: white; padding: 1px 3px; border-radius: 2px; font-size: 9px; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                     ${result.aqi || "N/A"}
                   </div>
                 </div>`,
          className: "custom-search-marker",
          iconSize: [20, 20],
          iconAnchor: [10, 10],
        }),
      }).addTo(map);

      marker.bindPopup(`
        <div style="text-align: center; font-family: system-ui, -apple-system, sans-serif;">
          <h4 style="margin: 0 0 3px 0; font-size: 13px; font-weight: 600;">${
            result.name
          }</h4>
          <div style="color: ${getColor(
            result.aqi,
            "aqi"
          )}; font-weight: 600; font-size: 12px;">AQI: ${
        result.aqi || "N/A"
      }</div>
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

// Click handler to go to location and open popup
const goToLocation = (result) => {
  clearSearch();
  map.setView([result.lat, result.lon], 10);
  const marker = markerMap.value[result.name];
  if (marker) {
    marker.openPopup();
  }
};

// Zoom controls
const zoomIn = () => {
  map.zoomIn();
};

const zoomOut = () => {
  map.zoomOut();
};

// Init map
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

const detectUserLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        map.setView([lat, lon], 5);
        L.marker([lat, lon]).addTo(map).bindPopup("You are here!").openPopup();
      },
      (error) => {
        console.warn("Geolocation error:", error.message);
      }
    );
  } else {
    console.warn("Geolocation is not supported by this browser.");
  }
};

onMounted(() => {
  initMap();
  fetchAQIData();
  fetchPhnomPenhAQI();
  detectUserLocation();
  setInterval(() => {
    fetchAQIData();
    fetchPhnomPenhAQI();
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

/* Custom scrollbar for search results */
.max-h-96::-webkit-scrollbar {
  width: 6px;
}

.max-h-96::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.max-h-96::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Custom scrollbar for search results - dark theme */
.max-h-80::-webkit-scrollbar {
  width: 4px;
}

.max-h-80::-webkit-scrollbar-track {
  background: #374151;
  border-radius: 2px;
}

.max-h-80::-webkit-scrollbar-thumb {
  background: #6b7280;
  border-radius: 2px;
}

.max-h-80::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>