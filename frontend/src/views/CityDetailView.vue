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
      
      <CurrentCityDisplay :selectedCity="selectedCity" />
      
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
import CitySelector from '@/components/CitySelector.vue'
import CurrentCityDisplay from '@/components/CurrentCityDisplay.vue'
import PollutantTable from '@/components/PollutantTable.vue'
import WeatherConditions from '@/components/WeatherConditions.vue'

// Props
const props = defineProps({
  city: {
    type: String,
    default: 'San Francisco'
  }
})

// Reactive data
const selectedPeriod = ref('24H')
const lastUpdated = ref('2 minutes ago')
const dataPoints = ref(1440)
const stationsActive = ref(12)
const currentTime = ref(new Date().toLocaleTimeString())

// Selected city
const selectedCity = ref({
  id: 1,
  name: 'San Francisco',
  country: 'United States',
  region: 'California',
  continent: 'North America',
  population: '874,000',
  timezone: 'PST',
  flag: '🇺🇸',
  monitoringLevel: 'Advanced'
})

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

// Weather data
const weather = ref({
  temperature: 22,
  condition: 'Partly Cloudy',
  humidity: 65,
  windSpeed: 12,
  pressure: 1013,
  visibility: 10
})

// Pollutant data
const pollutants = ref([
  { name: 'PM2.5', value: 35, unit: 'μg/m³', limit: 15, status: 'Moderate', percentage: 233, trend: 'up', description: 'Fine particulate matter' },
  { name: 'PM10', value: 80, unit: 'μg/m³', limit: 45, status: 'Good', percentage: 100, trend: 'down', description: 'Coarse particulate matter' },
  { name: 'NO₂', value: 28, unit: 'μg/m³', limit: 25, status: 'Moderate', percentage: 112, trend: 'stable', description: 'Nitrogen dioxide' },
  { name: 'O₃', value: 90, unit: 'μg/m³', limit: 100, status: 'Good', percentage: 52, trend: 'up', description: 'Ground-level ozone' },
  { name: 'SO₂', value: 8, unit: 'μg/m³', limit: 40, status: 'Good', percentage: 20, trend: 'down', description: 'Sulfur dioxide' },
  { name: 'CO', value: 1200, unit: 'μg/m³', limit: 4000, status: 'Good', percentage: 30, trend: 'stable', description: 'Carbon monoxide' }
])

// Global comparisons
const globalComparisons = ref([
  { metric: 'Air Quality Rank', description: 'Global city ranking', rank: '2,847', total: '15,000', color: 'text-yellow-600' },
  { metric: 'PM2.5 Level', description: 'Compared to world cities', rank: '4,521', total: '15,000', color: 'text-red-600' },
  { metric: 'Environmental Score', description: 'Overall environmental health', rank: '1,203', total: '15,000', color: 'text-green-600' },
  { metric: 'Population Density', description: 'Urban density ranking', rank: '892', total: '15,000', color: 'text-blue-600' }
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
  // Find the city object by ID from all available cities
  const city = availableCities.value.find(c => c.id === parseInt(cityId))
  if (city) {
    selectedCity.value = city
    generateCityData(city)
    updateCurrentTime()
  }
}

const generateCityData = (city) => {
  const populationNum = parseInt(city.population.replace(/,/g, ''))
  const populationFactor = Math.log10(populationNum) / 7
  
  let basePollutionMultiplier = 1
  
  switch (city.monitoringLevel) {
    case 'Advanced': basePollutionMultiplier = 0.7; break
    case 'Standard': basePollutionMultiplier = 1.0; break
    case 'Basic': basePollutionMultiplier = 1.5; break
  }
  
  if (city.continent === 'Asia' && city.monitoringLevel === 'Basic') {
    basePollutionMultiplier *= 1.8
  } else if (city.continent === 'Europe' || city.continent === 'Oceania') {
    basePollutionMultiplier *= 0.8
  }
  
  pollutants.value = pollutants.value.map(p => {
    const variation = 0.7 + (Math.random() * 0.6)
    const newValue = Math.round(p.value * populationFactor * basePollutionMultiplier * variation)
    const percentage = Math.round((newValue / p.limit) * 100)
    
    return {
      ...p,
      value: Math.max(1, newValue),
      percentage: percentage,
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
}

const calculateAQI = () => {
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
  generateCityData(selectedCity.value)
})
</script>
