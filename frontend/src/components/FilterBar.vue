<template>
  <div class="flex flex-wrap gap-4 p-4 bg-white rounded-lg shadow shadow-gray-200 items-center">
    <!-- Pollutant Dropdown -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Pollutant</label>
      <select v-model="filters.pollutant" class="p-2 border rounded w-40">
        <option value="">All</option>
        <option v-for="type in pollutantOptions" :key="type" :value="type">{{ type }}</option>
      </select>
    </div>

    <!-- Country Dropdown -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
      <select v-model="filters.country" class="p-2 border rounded w-40">
        <option value="">All</option>
        <option v-for="country in countryOptions" :key="country.code" :value="country.code">
          {{ country.name }}
        </option>
      </select>
    </div>

    <!-- Start Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
      <input type="date" v-model="filters.startDate" class="p-2 border rounded w-40" />
    </div>

    <!-- End Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
      <input type="date" v-model="filters.endDate" class="p-2 border rounded w-40" />
    </div>

    <!-- Apply Button -->
    <div class="self-end">
      <button
        @click="applyFilters"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        Apply Filters
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['filter-change']);

const filters = ref({
  pollutant: '',
  country: '',
  startDate: '',
  endDate: ''
});

const pollutantOptions = ['PM2.5', 'PM10', 'CO', 'NO2', 'SO2', 'O3'];

// You can replace this list or load dynamically from API
const countryOptions = [
  { code: 'KH', name: 'Cambodia' },
  { code: 'VN', name: 'Vietnam' },
  { code: 'TH', name: 'Thailand' },
  { code: 'CN', name: 'China' },
  { code: 'IN', name: 'India' },
  { code: 'US', name: 'United States' }
];

function applyFilters() {
  emit('filter-change', { ...filters.value });
}
</script>
