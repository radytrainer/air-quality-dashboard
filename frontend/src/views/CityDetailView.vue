<template>
  <div class="container mx-auto p-6 mt-10 max-w-3xl">
    <button
      @click="$router.back()"
      class="mb-6 px-4 py-2 bg-sky-500 text-white rounded hover:bg-sky-600"
    >
      ← Back
    </button>

    <div v-if="loading" class="text-center text-gray-600">
      Loading city data...
    </div>
    <div v-else-if="!cityData" class="text-center text-red-600">
      City data not found.
    </div>
    <div v-else class="bg-white rounded-2xl shadow-md p-6">
      <div class="flex items-center gap-4 mb-6">
        <img
          :src="cityData.flag"
          alt="Flag"
          class="w-16 h-10 object-cover rounded"
        />
        <h1 class="text-3xl font-bold">{{ cityData.name }}</h1>
      </div>

      <table class="w-full text-left text-gray-800">
        <tbody>
          <tr>
            <th class="py-2">AQI</th>
            <td>{{ cityData.aqi }}</td>
          </tr>
          <tr>
            <th class="py-2">PM10</th>
            <td>{{ cityData.pm10 ?? "N/A" }}</td>
          </tr>
          <tr>
            <th class="py-2">PM2.5</th>
            <td>{{ cityData.pm25 ?? "N/A" }}</td>
          </tr>
          <tr>
            <th class="py-2">CO</th>
            <td>{{ cityData.co ?? "N/A" }}</td>
          </tr>
          <tr>
            <th class="py-2">NO2</th>
            <td>{{ cityData.no2 ?? "N/A" }}</td>
          </tr>
          <tr>
            <th class="py-2">SO2</th>
            <td>{{ cityData.so2 ?? "N/A" }}</td>
          </tr>
          <tr>
            <th class="py-2">O3</th>
            <td>{{ cityData.o3 ?? "N/A" }}</td>
          </tr>
          <tr>
            <th class="py-2">Temperature</th>
            <td>{{ cityData.temperature ?? "N/A" }} °C</td>
          </tr>
          <tr>
            <th class="py-2">Humidity</th>
            <td>{{ cityData.humidity ?? "N/A" }} %</td>
          </tr>
          <tr>
            <th class="py-2">Pressure</th>
            <td>{{ cityData.pressure ?? "N/A" }} hPa</td>
          </tr>
          <tr>
            <th class="py-2">Wind</th>
            <td>{{ cityData.wind_speed ?? cityData.wind ?? "N/A" }} m/s</td>
          </tr>

          <tr>
            <th class="py-2">Coordinates</th>
            <td>{{ cityData.lat }}, {{ cityData.lon }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const cityIdParam = route.params.id;
const cityData = ref(null);
const loading = ref(false);

// Assign local IDs just like in home page
const assignIDs = (data) =>
  data.map((station, index) => ({
    ...station,
    id: station.id || index + 1,
  }));

const fetchCityData = async () => {
  loading.value = true;
  try {
    const response = await axios.get("http://127.0.0.1:8000/api/aqi");
    if (response.data.status === "ok" && Array.isArray(response.data.data)) {
      const allCities = assignIDs(response.data.data);
      cityData.value = allCities.find((c) => c.id == cityIdParam) || null;
    }
  } catch (error) {
    console.error("Error fetching city data:", error);
    cityData.value = null;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCityData();
});
</script>

<style scoped>
th {
  font-weight: 600;
  width: 150px;
}
td {
  font-weight: 400;
}
</style>
