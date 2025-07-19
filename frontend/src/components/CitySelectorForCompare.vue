<template>
  <div>
    <label class="block mb-1 font-large">{{ label }}</label>
    <div class="flex items-center gap-2 mb-2">
      <img v-if="selectedCityFlag" :src="selectedCityFlag" alt="flag" class="w-6 h-4 rounded-sm object-cover" />
      <span v-if="selected">{{ selected }}</span>
      <span v-else class="text-gray-400">No city selected</span>
    </div>
    <select v-model="selected" class="w-full p-2 border rounded-lg">
      <option value="">Select a city</option>
      <option v-for="city in cities" :key="city.name" :value="city.name">
        {{ city.name }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps(['modelValue', 'label'])
const emit = defineEmits(['update:modelValue'])

const cities = [
  { name: 'Phnom Penh', flag: 'https://flagcdn.com/kh.svg' },
  { name: 'New Delhi', flag: 'https://flagcdn.com/in.svg' },
  { name: 'Bangkok', flag: 'https://flagcdn.com/th.svg' },
  { name: 'Beijing', flag: 'https://flagcdn.com/cn.svg' },
  { name: 'Tokyo', flag: 'https://flagcdn.com/jp.svg' },
  { name: 'Jakarta', flag: 'https://flagcdn.com/id.svg' }
]

const selected = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

// Find the flag URL for the selected city
const selectedCityFlag = computed(() => {
  const city = cities.find(c => c.name === selected.value)
  return city ? city.flag : ''
})
</script>
