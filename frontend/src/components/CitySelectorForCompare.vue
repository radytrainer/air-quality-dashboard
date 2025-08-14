<template>
  <div class="w-full relative">
    <label class="font-semibold text-black text-xl mb-1 block">{{ label }}</label>
    <p class="text-sm text-gray-500 mb-3">{{ description }}</p>

    <!-- Dropdown toggle -->
    <div
      class="border rounded p-2 flex items-center justify-between cursor-pointer"
      @click="toggleDropdown"
    >
      <div class="flex items-center gap-2">
        <img
          v-if="selectedCity?.flag"
          :src="selectedCity.flag"
          alt="flag"
          class="w-6 h-4 object-cover rounded-sm"
        />
        <span>{{ selectedCity ? getCityAndCountry(selectedCity.name) : 'Select a city' }}</span>
      </div>
      <span>▼</span>
    </div>

    <!-- Dropdown list -->
    <div
      v-if="isOpen"
      class="absolute z-10 bg-white border rounded mt-1 w-full max-h-60 overflow-y-auto shadow"
    >
      <div
        v-for="city in cities"
        :key="city.name"
        class="flex items-center gap-2 p-2 hover:bg-gray-100 cursor-pointer"
        @click="selectCity(city)"
      >
        <img :src="city.flag" class="w-6 h-4 object-cover rounded-sm" />
        <span>{{ getCityAndCountry(city.name) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  label: String,
  description: String,
  cities: Array,
  modelValue: Object
})

const emit = defineEmits(['update:modelValue', 'city-changed'])

const isOpen = ref(false)
const selectedCity = ref(props.modelValue)

watch(() => props.modelValue, (newVal) => {
  selectedCity.value = newVal
})

function toggleDropdown() {
  isOpen.value = !isOpen.value
}

function selectCity(city) {
  selectedCity.value = city
  emit('update:modelValue', city)
  emit('city-changed', city)
  isOpen.value = false
}

function getCityAndCountry(fullName) {
  if (!fullName) return ''
  const parts = fullName.split(',').map((p) => p.trim())
  if (parts.length >= 2) {
    const city = parts[0]
    const country = parts[parts.length - 1]
    return `${city}, ${country}`
  }
  return fullName
}
</script>
