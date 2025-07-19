<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
      <AppHeader
        :lastUpdated="lastUpdated"
        :aqi="currentCityData.aqi"
      />

      <CitySelector
        :selectedCity="selectedCity"
        :groupedCities="groupedCities"
        @cityChange="onCityChange"
      />

      <CurrentCityDisplay :selectedCity="selectedCity" :currentTime="currentTime" />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <PollutantTable :pollutants="pollutants" />
        <WeatherConditions :weather="weather" :currentTime="currentTime" />
      </div>

      <ChartsSection
        :selectedPeriod="selectedPeriod"
        :selectedCity="selectedCity"
        :globalComparisons="globalComparisons"
        @periodChange="selectedPeriod = $event"
      />

      <StatsFooter
        :totalCities="totalCities"
        :totalCountries="totalCountries"
        :stationsActive="stationsActive"
        :dataPoints="dataPoints"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import ChartsSection from '@/components/ChartsSection.vue'
import CitySelector from '@/components/CitySelectorForCompare.vue'
import CurrentCityDisplay from '@/components/CurrentCityDisplay.vue'
import PollutantTable from '@/components/PollutantTable.vue'
import WeatherConditions from '@/components/WeatherConditions.vue'
import StatsFooter from '@/components/StatsFooter.vue' // Ensure this is imported

// Props
const props = defineProps({
  city: {
    type: String,
    default: null // Default to null, so no city is selected initially unless specified
  }
})

// Reactive data
const selectedPeriod = ref('24H')
const lastUpdated = ref('2 minutes ago')
const dataPoints = ref(1440)
const stationsActive = ref(12)
const currentTime = ref(new Date().toLocaleTimeString())

// Selected city - initialized to null
const selectedCity = ref(null)

// Simplified cities database
const availableCities = ref([
  // North America
  { id: 1, name: 'San Francisco', country: 'United States', region: 'California', continent: 'North America', population: '874,000', timezone: 'PST', flag: '🇺🇸', monitoringLevel: 'Advanced' },
  { id: 2, name: 'New York', country: 'United States', region: 'New York', continent: 'North America', population: '8,400,000', timezone: 'EST', flag: '🇺🇸', monitoringLevel: 'Advanced' },
  { id: 3, name: 'Toronto', country: 'Canada', region: 'Ontario', continent: 'North America', population: '2,930,000', timezone: 'EST', flag: '🇨🇦', monitoringLevel: 'Advanced' },
  { id: 4, name: 'Mexico City', country: 'Mexico', region: 'Mexico City', continent: 'North America', population: '9,200,000', timezone: 'CST', flag: '🇲🇽', monitoringLevel: 'Standard' },

  // Europe
  { id: 5, name: 'London', country: 'United Kingdom', region: 'England', continent: 'Europe', population: '9,000,000', timezone: 'GMT', flag: '🇬🇧', monitoringLevel: 'Advanced' },
  { id: 6, name: 'Paris', country: 'France', region: 'Île-de-France', continent: 'Europe', population: '2,200,000', timezone: 'CET', flag: '🇫🇷', monitoringLevel: 'Advanced' },
  { id: 7, name: 'Berlin', country: 'Germany', region: 'Berlin', continent: 'Europe', population: '3,700,000', timezone: 'CET', flag: '🇩🇪', monitoringLevel: 'Advanced' },
  { id: 8, name: 'Madrid', country: 'Spain', region: 'Madrid', continent: 'Europe', population: '3,280,000', timezone: 'CET', flag: '🇪🇸', monitoringLevel: 'Advanced' },

  // Asia
  { id: 9, name: 'Tokyo', country: 'Japan', region: 'Tokyo', continent: 'Asia', population: '14,000,000', timezone: 'JST', flag: '🇯🇵', monitoringLevel: 'Advanced' },
  { id: 10, name: 'Beijing', country: 'China', region: 'Beijing', continent: 'Asia', population: '21,000,000', timezone: 'CST', flag: '🇨🇳', monitoringLevel: 'Standard' },
  { id: 11, name: 'Mumbai', country: 'India', region: 'Maharashtra', continent: 'Asia', population: '20,000,000', timezone: 'IST', flag: '🇮🇳', monitoringLevel: 'Basic' },
  { id: 12, name: 'Seoul', country: 'South Korea', region: 'Seoul', continent: 'Asia', population: '9,720,000', timezone: 'KST', flag: '🇰🇷', monitoringLevel: 'Advanced' },
  { id: 13, name: 'Singapore', country: 'Singapore', region: 'Singapore', continent: 'Asia', population: '5,920,000', timezone: 'SGT', flag: '🇸🇬', monitoringLevel: 'Advanced' },
  { id: 14, name: 'Bangkok', country: 'Thailand', region: 'Bangkok', continent: 'Asia', population: '10,560,000', timezone: 'ICT', flag: '🇹🇭', monitoringLevel: 'Standard' },
  { id: 15, name: 'Phnom Penh', country: 'Cambodia', region: 'Phnom Penh', continent: 'Asia', population: '2,280,000', timezone: 'ICT', flag: '🇰🇭', monitoringLevel: 'Basic' },

  // Africa
  { id: 16, name: 'Cairo', country: 'Egypt', region: 'Cairo', continent: 'Africa', population: '10,230,000', timezone: 'EET', flag: '🇪🇬', monitoringLevel: 'Basic' },
  { id: 17, name: 'Lagos', country: 'Nigeria', region: 'Lagos', continent: 'Africa', population: '15,390,000', timezone: 'WAT', flag: '🇳🇬', monitoringLevel: 'Basic' },
  { id: 18, name: 'Johannesburg', country: 'South Africa', region: 'Gauteng', continent: 'Africa', population: '5,930,000', timezone: 'SAST', flag: '🇿🇦', monitoringLevel: 'Standard' },

  // Oceania
  { id: 19, name: 'Sydney', country: 'Australia', region: 'New South Wales', continent: 'Oceania', population: '5,300,000', timezone: 'AEST', flag: '🇦🇺', monitoringLevel: 'Advanced' },
  { id: 20, name: 'Auckland', country: 'New Zealand', region: 'Auckland', continent: 'Oceania', population: '1,720,000', timezone: 'NZST', flag: '🇳🇿', monitoringLevel: 'Advanced' },

  // South America
  { id: 21, name: 'São Paulo', country: 'Brazil', region: 'São Paulo', continent: 'South America', population: '12,300,000', timezone: 'BRT', flag: '🇧🇷', monitoringLevel: 'Standard' },
  { id: 22, name: 'Buenos Aires', country: 'Argentina', region: 'Buenos Aires', continent: 'South America', population: '3,120,000', timezone: 'ART', flag: '🇦🇷', monitoringLevel: 'Standard' }
])

// Group cities by continent
const groupedCities = computed(() => {
  const continents = {}

  availableCities.value.forEach(city => {
    if (!continents[city.continent]) {
      continents[city.continent] = []
    }
    continents[city.continent].push(city)
  })

  return [
    { name: 'North America', cities: continents['North America'] || [] },
    { name: 'South America', cities: continents['South America'] || [] },
    { name: 'Europe', cities: continents['Europe'] || [] },
    { name: 'Asia', cities: continents['Asia'] || [] },
    { name: 'Africa', cities: continents['Africa'] || [] },
    { name: 'Oceania', cities: continents['Oceania'] || [] }
  ]
})

// Computed totals
const totalCities = computed(() => availableCities.value.length)
const totalCountries = computed(() => {
  const countries = new Set(availableCities.value.map(city => city.country))
  return countries.size
})

// Weather data - initialized with null/N/A values
const weather = ref({
  temperature: null,
  condition: 'N/A',
  humidity: null,
  windSpeed: null,
  pressure: null,
  visibility: null
})

// Pollutant data - initialized as empty
const pollutants = ref([])

// Global comparisons
const globalComparisons = ref([
  { metric: 'Air Quality Rank', description: 'Global city ranking', rank: 'N/A', total: 'N/A', color: 'text-gray-500' },
  { metric: 'PM2.5 Level', description: 'Compared to world cities', rank: 'N/A', total: 'N/A', color: 'text-gray-500' },
  { metric: 'Environmental Score', description: 'Overall environmental health', rank: 'N/A', total: 'N/A', color: 'text-gray-500' },
  { metric: 'Population Density', description: 'Urban density ranking', rank: 'N/A', total: 'N/A', color: 'text-gray-500' }
])

// Computed property for current city data
const currentCityData = computed(() => {
  return {
    aqi: calculateAQI(),
    pollutants: pollutants.value
  }
})

// Methods
const onCityChange = (cityId) => {
  if (!cityId) {
    selectedCity.value = null
    pollutants.value = []
    weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
    globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
    updateCurrentTime()
    return
  }
  // Parse cityId safely and find city
  const id = parseInt(cityId)
  if (isNaN(id)) {
    selectedCity.value = null
    pollutants.value = []
    weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
    globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
    updateCurrentTime()
    return
  }
  const city = availableCities.value.find(c => c.id === id)
  if (city) {
    selectedCity.value = city
    generateCityData(city)
    updateCurrentTime()
  } else {
    selectedCity.value = null
    pollutants.value = []
    weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
    globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
    updateCurrentTime()
  }
}

const generateCityData = (city) => {
  if (!city || !city.population) {
    // Handle missing city or population safely by clearing data
    pollutants.value = []
    weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
    globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
    return
  }
  const populationNum = parseInt(city.population.replace(/,/g, ''))
  if (isNaN(populationNum) || populationNum <= 0) {
    pollutants.value = []
    weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
    globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
    return
  }
  const populationFactor = Math.log10(populationNum) / 7
  let basePollutionMultiplier = 1
  switch (city.monitoringLevel) {
    case 'Advanced': basePollutionMultiplier = 0.7; break
    case 'Standard': basePollutionMultiplier = 1.0; break
    case 'Basic': basePollutionMultiplier = 1.5; break
    default: basePollutionMultiplier = 1.0
  }
  if (city.continent === 'Asia' && city.monitoringLevel === 'Basic') {
    basePollutionMultiplier *= 1.8
  } else if (city.continent === 'Europe' || city.continent === 'Oceania') {
    basePollutionMultiplier *= 0.8
  }
  pollutants.value = [
    { name: 'PM2.5', value: 35, unit: 'μg/m³', limit: 15, status: 'Moderate', percentage: 233, trend: 'up', description: 'Fine particulate matter' },
    { name: 'PM10', value: 80, unit: 'μg/m³', limit: 45, status: 'Good', percentage: 100, trend: 'down', description: 'Coarse particulate matter' },
    { name: 'NO₂', value: 28, unit: 'μg/m³', limit: 25, status: 'Moderate', percentage: 112, trend: 'stable', description: 'Nitrogen dioxide' },
    { name: 'O₃', value: 90, unit: 'μg/m³', limit: 100, status: 'Good', percentage: 52, trend: 'up', description: 'Ground-level ozone' },
    { name: 'SO₂', value: 8, unit: 'μg/m³', limit: 40, status: 'Good', percentage: 20, trend: 'down', description: 'Sulfur dioxide' },
    { name: 'CO', value: 1200, unit: 'μg/m³', limit: 4000, status: 'Good', percentage: 30, trend: 'stable', description: 'Carbon monoxide' }
  ].map(p => {
    const variation = 0.7 + (Math.random() * 0.6)
    const newValue = Math.round(p.value * populationFactor * basePollutionMultiplier * variation)
    const percentage = Math.round((newValue / p.limit) * 100)
    return {
      ...p,
      value: Math.max(1, newValue),
      percentage,
      status: percentage <= 100 ? 'Good' : percentage <= 200 ? 'Moderate' : 'Unhealthy',
      trend: ['up', 'down', 'stable'][Math.floor(Math.random() * 3)]
    }
  })
  const latitudeFactor = city.continent === 'Africa' ? 0.2 :
                         city.continent === 'Asia' ? 0.4 :
                         city.continent === 'Europe' ? 0.6 : 0.5

  weather.value = {
    temperature: Math.round(15 + (Math.random() * 20) + (latitudeFactor * 10)),
    condition: ['Sunny', 'Partly Cloudy', 'Cloudy', 'Overcast', 'Clear'][Math.floor(Math.random() * 5)],
    humidity: Math.round(30 + (Math.random() * 50)),
    windSpeed: Math.round(3 + (Math.random() * 20)),
    pressure: Math.round(995 + (Math.random() * 40)),
    visibility: Math.round(5 + (Math.random() * 10))
  }

  // Generate dynamic global comparisons based on selected city
  const randomRank = (max) => Math.floor(Math.random() * max) + 1;
  const totalGlobalCities = 15000;

  globalComparisons.value = [
    { metric: 'Air Quality Rank', description: 'Global city ranking', rank: randomRank(totalGlobalCities).toLocaleString(), total: totalGlobalCities.toLocaleString(), color: 'text-yellow-600' },
    { metric: 'PM2.5 Level', description: 'Compared to world cities', rank: randomRank(totalGlobalCities).toLocaleString(), total: totalGlobalCities.toLocaleString(), color: 'text-red-600' },
    { metric: 'Environmental Score', description: 'Overall environmental health', rank: randomRank(totalGlobalCities).toLocaleString(), total: totalGlobalCities.toLocaleString(), color: 'text-green-600' },
    { metric: 'Population Density', description: 'Urban density ranking', rank: randomRank(totalGlobalCities).toLocaleString(), total: totalGlobalCities.toLocaleString(), color: 'text-blue-600' }
  ];
}

const calculateAQI = () => {
  if (!pollutants.value || pollutants.value.length === 0) {
    return 0 // Return 0 or a default value when no pollutants data is available
  }
  const maxPercentage = Math.max(...pollutants.value.map(p => p.percentage))
  return Math.min(Math.round(maxPercentage * 1.2), 500)
}

const updateCurrentTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', {
    hour12: false,
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  updateCurrentTime()
  setInterval(updateCurrentTime, 60000)

  // Check if a city prop was provided to initialize the selected city
  if (props.city) {
    const initialCity = availableCities.value.find(c => c.name === props.city)
    if (initialCity) {
      selectedCity.value = initialCity
      generateCityData(initialCity)
    } else {
      // If prop city not found, start with no city selected
      selectedCity.value = null
      pollutants.value = []
      weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
      globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
    }
  } else {
    // If no city prop, start with no city selected
    selectedCity.value = null
    pollutants.value = []
    weather.value = { temperature: null, condition: 'N/A', humidity: null, windSpeed: null, pressure: null, visibility: null }
    globalComparisons.value = globalComparisons.value.map(item => ({ ...item, rank: 'N/A', total: 'N/A', color: 'text-gray-500' }));
  }
})
</script>
