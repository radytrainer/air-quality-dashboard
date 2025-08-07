<template>
  <div class="p-6 space-y-6">
    <h2 class="text-2xl font-bold">Health Alert Admin Panel</h2>

    <!-- Current AQI display -->
    <div class="bg-white p-4 shadow rounded">
      <h3 class="text-xl font-semibold mb-2">Current AQI Data</h3>
      <div v-if="aqiData">
        <p>PM2.5: {{ aqiData.pm25 }}</p>
        <p>PM10: {{ aqiData.pm10 }}</p>
        <p>CO: {{ aqiData.co }}</p>
        <p>NO₂: {{ aqiData.no2 }}</p>
      </div>
      <div v-else>Loading AQI data...</div>
    </div>

    <!-- Last API fetch info -->
    <div class="bg-white p-4 shadow rounded">
      <h3 class="text-xl font-semibold mb-2">Last API Fetch Info</h3>
      <p>✅ Last Fetch Time: {{ meta.last_fetch_time }}</p>
      <p class="mt-2 font-medium">Cache Keys:</p>
      <ul>
        <li v-for="(status, key) in meta.cache_keys" :key="key">
          - {{ key }}: <span :class="status === 'active' ? 'text-green-600' : 'text-red-600'">{{ status }}</span>
        </li>
      </ul>
      <p class="mt-4 font-medium">Recent Logs:</p>
      <ul class="text-sm text-gray-600 max-h-40 overflow-y-auto">
        <li v-for="(log, index) in meta.logs" :key="index">• {{ log.trim() }}</li>
      </ul>
    </div>

    <!-- AQI Threshold Config -->
    <div class="bg-white p-4 shadow rounded">
      <h3 class="text-xl font-semibold mb-2">AQI Threshold Configuration</h3>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block">PM2.5 Threshold</label>
          <input v-model.number="config.pm25_threshold" type="number" class="input" />
        </div>
        <div>
          <label class="block">PM10 Threshold</label>
          <input v-model.number="config.pm10_threshold" type="number" class="input" />
        </div>
        <div>
          <label class="block">CO Threshold</label>
          <input v-model.number="config.co_threshold" type="number" class="input" />
        </div>
        <div>
          <label class="block">NO₂ Threshold</label>
          <input v-model.number="config.no2_threshold" type="number" class="input" />
        </div>
      </div>
      <div class="mt-4">
        <label class="flex items-center space-x-2">
          <input type="checkbox" v-model="config.enable_alert" />
          <span>Enable Alert Popups</span>
        </label>
      </div>
      <div class="mt-2">
        <label class="block">Custom Alert Message</label>
        <textarea v-model="config.alert_message" class="input" rows="2"></textarea>
      </div>

      <button @click="saveConfig" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Settings</button>

      <button @click="triggerTestAlert" class="mt-4 ml-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Test Alert</button>
    </div>

    <!-- Alert Message History -->
    <div class="bg-white p-4 shadow rounded max-h-64 overflow-y-auto">
      <h3 class="text-xl font-semibold mb-2">Alert Message History</h3>
      <ul class="text-sm text-gray-700">
        <li v-for="message in alertHistory" :key="message.id" class="border-b py-1">
          <p><strong>{{ new Date(message.created_at).toLocaleString() }}:</strong> {{ message.message }}</p>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'

const meta = ref({
  last_fetch_time: '',
  cache_keys: {},
  logs: [],
})

const config = ref({
  pm25_threshold: 25,
  pm10_threshold: 50,
  co_threshold: 10,
  no2_threshold: 40,
  enable_alert: true,
  alert_message: '',
})

const alertHistory = ref([])
const aqiData = ref(null)

// Load initial data
onMounted(async () => {
  try {
    const [resMeta, resConfig, resHistory, resAqi] = await Promise.all([
      api.get('/admin/aqi-meta'),
      api.get('/admin/alert-config'),
      api.get('/admin/alert-history'),
      api.get('/admin/current-aqi'),
    ])

    meta.value = resMeta.data
    config.value = resConfig.data
    alertHistory.value = resHistory.data
    aqiData.value = resAqi.data

    checkAlerts() // Check if alert needs to show on page load
  } catch (error) {
    console.error(error)
  }
})

// Save config
const saveConfig = async () => {
  try {
    await api.post('/admin/alert-config', config.value)
    Swal.fire('Success', 'Alert settings updated!', 'success')
  } catch {
    Swal.fire('Error', 'Failed to save settings.', 'error')
  }
}

// Trigger a test alert popup to admin
const triggerTestAlert = () => {
  Swal.fire({
    icon: 'info',
    title: 'Test Alert',
    text: config.value.alert_message || 'This is a test alert message.',
  })
}

// Check AQI values vs thresholds and alert if needed
const checkAlerts = () => {
  if (!config.value.enable_alert) return

  const exceeded =
    (aqiData.value.pm25 > config.value.pm25_threshold) ||
    (aqiData.value.pm10 > config.value.pm10_threshold) ||
    (aqiData.value.co > config.value.co_threshold) ||
    (aqiData.value.no2 > config.value.no2_threshold)

  if (exceeded) {
    Swal.fire({
      icon: 'warning',
      title: 'Health Alert',
      text: config.value.alert_message || 'Air quality levels have exceeded thresholds!',
      confirmButtonText: 'OK',
    })
    saveAlertMessage(config.value.alert_message) // Save alert message to history
  }
}

// Save alert message to backend history
const saveAlertMessage = async (message) => {
  if (!message) return
  try {
    await api.post('/admin/alert-history', { message })
    const resHistory = await api.get('/admin/alert-history')
    alertHistory.value = resHistory.data
  } catch (error) {
    console.error('Failed to save alert message history', error)
  }
}
</script>

<style scoped>
.input {
  @apply border p-2 rounded w-full;
}
</style>
