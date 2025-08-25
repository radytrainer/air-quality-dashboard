<template>
  <div class="p-6 max-w-screen-xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
      <h1
        class="text-3xl sm:text-4xl font-extrabold mb-8 text-gray-900 tracking-tight"
      >
        {{ $t("city.title") }}
      </h1>

      <div class="flex flex-wrap gap-4 items-center mb-6">
        <input
          v-model="search"
          type="text"
          :placeholder="$t('city.searchPlaceholder')"
          class="p-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 rounded-md w-full sm:w-1/4 transition"
        />

        <select
          v-model="pollutant"
          class="p-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 rounded-md transition"
        >
          <option value="aqi">{{ $t("city.pollutants.aqi") }}</option>
          <option value="pm25">{{ $t("city.pollutants.pm25") }}</option>
          <option value="pm10">{{ $t("city.pollutants.pm10") }}</option>
          <option value="o3">{{ $t("city.pollutants.o3") }}</option>
          <option value="no2">{{ $t("city.pollutants.no2") }}</option>
          <option value="so2">{{ $t("city.pollutants.so2") }}</option>
          <option value="co">{{ $t("city.pollutants.co") }}</option>
          <option value="temperature">{{ $t("city.pollutants.temperature") }}</option>
          <option value="humidity">{{ $t("city.pollutants.humidity") }}</option>
          <option value="pressure">{{ $t("city.pollutants.pressure") }}</option>
          <option value="wind_speed">{{ $t("city.pollutants.windSpeed") }}</option>
        </select>

        <select
          v-model="levelFilter"
          class="p-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 rounded-md transition"
        >
          <option value="">{{ $t("city.allLevels") }}</option>
          <option value="Good">{{ $t("city.levels.good") }}</option>
          <option value="Moderate">{{ $t("city.levels.moderate") }}</option>
          <option value="Unhealthy for Sensitive Groups">
            {{ $t("city.levels.unhealthySensitive") }}
          </option>
          <option value="Unhealthy">{{ $t("city.levels.unhealthy") }}</option>
          <option value="Very Unhealthy">{{ $t("city.levels.veryUnhealthy") }}</option>
          <option value="Hazardous">{{ $t("city.levels.hazardous") }}</option>
        </select>

        <button
  @click="fetchAQIData"
  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-md flex items-center gap-2 transition-all duration-200"
>
  <ArrowPathIcon class="w-5 h-5" />
  {{ $t("city.refresh") }}
</button>

<button
  @click="exportToCSV"
  class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-md flex items-center gap-2 transition-all duration-200"
>
  <DocumentIcon class="w-5 h-5" />
  {{ $t("city.exportCSV") }}
</button>
      </div>

      <div class="text-sm text-gray-500 mb-4 space-y-1">
        <p>
          {{ $t("city.dataSource") }}
          <a
            href="https://waqi.info"
            target="_blank"
            class="text-blue-600 underline hover:text-blue-800"
          >
            World Air Quality Index (WAQI)
          </a>
        </p>
        <p>{{ $t("city.lastUpdated") }}: {{ lastUpdated }}</p>
      </div>

      <div v-if="loading" class="text-gray-600">{{ $t("city.loading") }}</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>

      <div v-else class="overflow-auto rounded-md shadow">
        <table
          class="min-w-full border border-gray-300 text-sm divide-y divide-gray-200"
        >
          <thead
            class="bg-gray-100 text-gray-800 font-semibold uppercase tracking-wide text-xs"
          >
            <tr>
              <th
                class="p-3 text-left cursor-pointer border-b-2 border-gray-300 hover:text-blue-600 transition"
                @click="sortBy('name')"
              >
                {{ $t("city.table.city") }}
              </th>
              <th class="p-3 text-left border-b-2 border-gray-300">{{ $t("city.table.flag") }}</th>
              <th class="p-3 text-left border-b-2 border-gray-300">{{ $t("city.table.latitude") }}</th>
              <th class="p-3 text-left border-b-2 border-gray-300">{{ $t("city.table.longitude") }}</th>
              <th class="p-3 text-left border-b-2 border-gray-300">{{ $t("city.table.pollutant") }}</th>
              <th
                class="p-3 text-left cursor-pointer border-b-2 border-gray-300 hover:text-blue-600 transition"
                @click="sortBy('value')"
              >
                {{ $t("city.table.value") }}
              </th>
              <th class="p-3 text-left border-b-2 border-gray-300">{{ $t("city.table.level") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="city in paginatedCities"
              :key="city.name"
              class="hover:bg-gray-100 transition"
              :class="rowColor(city.level)"
            >
              <td class="border px-3 py-2">{{ city.name }}</td>
              <td class="border px-3 py-2">
                <img
                  v-if="city.flag"
                  :src="city.flag"
                  :alt="$t('city.flagAlt')"
                  class="w-6 h-4 object-cover rounded border"
                />
              </td>
              <td class="border px-3 py-2">{{ city.lat }}</td>
              <td class="border px-3 py-2">{{ city.lon }}</td>
              <td class="border px-3 py-2 uppercase">{{ city.pollutant }}</td>
              <td class="border px-3 py-2 font-bold text-gray-800">
                {{ city.value }}
              </td>
              <td class="border px-3 py-2">
                <span
                  :class="levelBadge(city.level)"
                  class="px-2 py-1 rounded-full text-xs font-semibold inline-block shadow-sm transition"
                >
                  {{ city.level }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        class="flex flex-col sm:flex-row justify-between items-center mt-8 gap-4"
      >
        <span class="text-sm text-gray-500">
          {{ $t("city.showing") }} {{ paginatedCities.length }}
          {{ $t("city.of") }} {{ filteredCities.length }} {{ $t("city.entries") }}
        </span>
        <div class="flex items-center gap-2">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded bg-gray-50 hover:bg-gray-200 disabled:opacity-40 transition"
          >
            {{ $t("city.prev") }}
          </button>
          <span class="text-sm">
            {{ $t("city.page") }} {{ currentPage }} {{ $t("city.of") }} {{ totalPages }}
          </span>
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded bg-gray-50 hover:bg-gray-200 disabled:opacity-40 transition"
          >
            {{ $t("city.next") }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import api from "@/services/api";
import { ArrowPathIcon, DocumentIcon } from '@heroicons/vue/24/solid'
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
      return "bg-[#A8E05F] text-[#1E5A00]";
    case "Moderate":
      return "bg-[#FDD64B] text-[#8C6D1F]";
    case "Unhealthy for Sensitive Groups":
      return "bg-[#FF9B57] text-[#7B3C00]";
    case "Unhealthy":
      return "bg-[#FE6A69] text-[#7A0000]";
    case "Very Unhealthy":
      return "bg-[#A97ABC] text-[#3E005E]";
    case "Hazardous":
      return "bg-[#A87383] text-white";
    default:
      return "bg-gray-100 text-gray-700";
  }
};

const fetchAQIData = async () => {
  try {
    loading.value = true;
    const response = await api.get("/aqi"); // global cities
    const rawData = response.data.data || [];

    // Fetch Phnom Penh separately
    let phnomPenh = null;
    try {
      const ppRes = await api.get("/air-quality/phnom-penh");
      phnomPenh = {
        id: 9999,
        name: "Phnom Penh",
        lat: 11.562108,
        lon: 104.888535,
        aqi: ppRes.data.AQI ?? "N/A",
        pm25: ppRes.data.PM2_5 ?? "N/A",
        pm10: ppRes.data.PM10 ?? "N/A",
        co: ppRes.data.CO ?? "N/A",
        no2: ppRes.data.NO2 ?? "N/A",
        so2: ppRes.data.SO2 ?? "N/A",
        o3: ppRes.data.O3 ?? "N/A",
        temperature: ppRes.data.Temp ?? "N/A",
        humidity: ppRes.data.Humidity ?? "N/A",
        pressure: ppRes.data.Pressure ?? "N/A",
        wind_speed: ppRes.data.Wind ?? "N/A",
        flag: "https://flagcdn.com/w160/kh.png",
      };
    } catch (err) {
      console.error("Failed to fetch Phnom Penh:", err);
    }

    // Map global cities
    let combinedData = rawData.map((city) => ({
      ...city,
      pollutant: pollutant.value,
      value: city[pollutant.value],
      level: getAQILevel(city[pollutant.value]),
    }));

    // Merge Phnom Penh
    if (phnomPenh) {
      combinedData = combinedData.filter((c) => c.name !== "Phnom Penh");
      combinedData.push({
        ...phnomPenh,
        pollutant: pollutant.value,
        value: phnomPenh[pollutant.value],
        level: getAQILevel(phnomPenh[pollutant.value]),
      });
    }

    cities.value = combinedData;
    lastUpdated.value = new Date().toLocaleString();
  } catch (err) {
    console.error(err);
    error.value = "Failed to load data.";
  } finally {
    loading.value = false;
  }
};


// helper to classify pollutant levels (basic AQI logic, can be customized)
const getAQILevel = (value) => {
  if (value === null || value === undefined) return "Unknown";
  if (value <= 50) return "Good";
  if (value <= 100) return "Moderate";
  if (value <= 150) return "Unhealthy for Sensitive Groups";
  if (value <= 200) return "Unhealthy";
  if (value <= 300) return "Very Unhealthy";
  return "Hazardous";
};
watch(pollutant, () => {
  cities.value = cities.value.map((city) => ({
    ...city,
    pollutant: pollutant.value,
    value: city[pollutant.value],
    level: getAQILevel(city[pollutant.value]),
  }));
});

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
  return cities.value.filter((city) => {
    const hasValue =
      city.value !== null && city.value !== undefined && city.value !== "";
    return (
      hasValue && // exclude cities with missing data
      (!levelFilter.value || city.level === levelFilter.value) &&
      city.name.toLowerCase().includes(search.value.toLowerCase())
    );
  });
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
      return "bg-rose-200 ";
    default:
      return "";
  }
};

watch([search, pollutant], fetchAQIData, { immediate: true });
</script>
