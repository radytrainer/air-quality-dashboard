<template>
  <div class="p-6 max-w-screen-xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
      <h1 class="text-3xl font-bold mb-6 text-gray-800">City AQI</h1>

      <div class="flex flex-wrap gap-4 items-center mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="Search city..."
          class="p-2 border border-gray-300 rounded-md w-full sm:w-1/3"
        />

        <select v-model="pollutant" class="p-2 border border-gray-300 rounded-md">
          <option value="aqi">AQI</option>
          <option value="pm25">PM2.5</option>
          <option value="pm10">PM10</option>
          <option value="o3">Ozone (O₃)</option>
          <option value="no2">NO₂</option>
          <option value="so2">SO₂</option>
          <option value="co">CO</option>
        </select>

        <select v-model="levelFilter" class="p-2 border border-gray-300 rounded-md">
          <option value="">All Levels</option>
          <option value="Good">Good</option>
          <option value="Moderate">Moderate</option>
          <option value="Unhealthy for Sensitive Groups">
            Unhealthy for Sensitive Groups
          </option>
          <option value="Unhealthy">Unhealthy</option>
          <option value="Very Unhealthy">Very Unhealthy</option>
          <option value="Hazardous">Hazardous</option>
        </select>

        <button
          @click="fetchAQIData"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition"
        >
          🔄 Refresh
        </button>

        <button
          @click="exportToCSV"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition"
        >
          📄 Export CSV
        </button>
      </div>

      <div class="text-sm text-gray-600 mb-4">
        <p>
          Data source:
          <a
            href="https://waqi.info"
            target="_blank"
            class="text-blue-600 underline hover:text-blue-800"
          >
            World Air Quality Index (WAQI)
          </a>
        </p>
        <p>Last updated: {{ lastUpdated }}</p>
      </div>

      <div v-if="loading" class="text-gray-600">Loading data...</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>

      <div v-else class="overflow-auto rounded-md shadow">
        <table class="min-w-full border border-gray-200 text-sm">
          <thead class="bg-gray-100 text-gray-700 font-semibold">
            <tr>
              <th class="border p-3 text-left cursor-pointer" @click="sortBy('name')">City</th>
              <th class="border p-3 text-left">Latitude</th>
              <th class="border p-3 text-left">Longitude</th>
              <th class="border p-3 text-left">Pollutant</th>
              <th class="border p-3 text-left cursor-pointer" @click="sortBy('value')">Value</th>
              <th class="border p-3 text-left">Level</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="city in paginatedCities"
              :key="city.name"
              class="hover:bg-gray-50"
              :class="rowColor(city.level)"
            >
              <td class="border px-3 py-2">{{ city.name }}</td>
              <td class="border px-3 py-2">{{ city.lat }}</td>
              <td class="border px-3 py-2">{{ city.lon }}</td>
              <td class="border px-3 py-2 uppercase">{{ city.pollutant }}</td>
              <td class="border px-3 py-2 font-semibold">{{ city.value }}</td>
              <td class="border px-3 py-2">
                <span
                  :class="levelBadge(city.level)"
                  class="px-2 py-1 rounded text-xs font-bold inline-block"
                >
                  {{ city.level }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-between items-center mt-6">
        <span class="text-sm text-gray-500">
          Showing {{ paginatedCities.length }} of {{ filteredCities.length }} entries
        </span>
        <div class="flex items-center gap-2">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50"
          >
            Prev
          </button>
          <span class="text-sm">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded bg-gray-100 hover:bg-gray-200 disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, watch, computed } from "vue";
import api from "@/services/api";

const search = ref("");
const pollutant = ref("aqi");
const cities = ref([]);
const loading = ref(false);
const error = ref("");
const updatedAt = ref("");
const sortField = ref("name");
const sortDirection = ref("asc");
const lastUpdated = ref("");
const currentPage = ref(1);
const itemsPerPage = 10;
const levelFilter = ref("");
const levelBadge = (level) => {
  switch (level) {
    case "Good":
      return "bg-green-100 text-green-700";
    case "Moderate":
      return "bg-yellow-100 text-yellow-700";
    case "Unhealthy for Sensitive Groups":
      return "bg-orange-100 text-orange-700";
    case "Unhealthy":
      return "bg-red-100 text-red-700";
    case "Very Unhealthy":
      return "bg-purple-100 text-purple-700";
    case "Hazardous":
      return "bg-black text-white";
    default:
      return "bg-gray-100 text-gray-700";
  }
};

const fetchAQIData = async () => {
  try {
    loading.value = true;
    const response = await api.get("/admin/city-aqi", {
      params: { search: search.value, pollutant: pollutant.value },
    });
    cities.value = response.data.data || [];
    lastUpdated.value = new Date().toLocaleString();
  } catch (error) {
    console.error(error);
    error.value = "Failed to load data.";
  } finally {
    loading.value = false;
  }
  setInterval(fetchAQIData, 300000); // 5 minutes = 300000 ms
};

const exportToCSV = () => {
  if (!cities.value || cities.value.length === 0) return;

  const headers = Object.keys(cities.value[0]);
  const rows = cities.value.map((row) =>
    headers.map((field) => `"${row[field]}"`).join(",")
  );

  const csvContent = [headers.join(","), ...rows].join("\n");

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", "city-aqi.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const paginatedCities = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return sortedCities.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() =>
  Math.ceil(sortedCities.value.length / itemsPerPage)
);

const goToPage = (page) => {
  if (page > 0 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    sortField.value = field;
    sortDirection.value = "asc";
  }
};
const filteredCities = computed(() => {
  return cities.value.filter(
    (city) =>
      (!levelFilter.value || city.level === levelFilter.value) &&
      city.name.toLowerCase().includes(search.value.toLowerCase())
  );
});
const sortedCities = computed(() => {
  return [...filteredCities.value].sort((a, b) => {
    let valA = a[sortField.value];
    let valB = b[sortField.value];
    if (typeof valA === "string") valA = valA.toLowerCase();
    if (typeof valB === "string") valB = valB.toLowerCase();
    if (valA < valB) return sortDirection.value === "asc" ? -1 : 1;
    if (valA > valB) return sortDirection.value === "asc" ? 1 : -1;
    return 0;
  });
});

const rowColor = (level) => {
  switch (level) {
    case "Good":
      return "bg-green-100";
    case "Moderate":
      return "bg-yellow-100";
    case "Unhealthy for Sensitive Groups":
      return "bg-orange-100";
    case "Unhealthy":
      return "bg-red-100";
    case "Very Unhealthy":
      return "bg-purple-200";
    case "Hazardous":
      return "bg-black text-white";
    default:
      return "";
  }
};

watch([search, pollutant], fetchAQIData, { immediate: true });
</script>
