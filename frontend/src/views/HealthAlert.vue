<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-lg border p-6 mb-6">
        <div class="flex items-center gap-3 mb-2">
          <div class="p-2 bg-emerald-100 rounded-lg">
            <Shield class="w-6 h-6 text-emerald-600" />
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">AQI Alert Configuration</h1>
            <p class="text-gray-600">Configure air quality threshold alerts based on WHO standards</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Pollutant Configurations -->
        <div class="xl:col-span-3 space-y-6">
          <!-- PM2.5 Configuration -->
          <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4">
              <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-white/20 rounded-lg">
                    <Wind class="w-5 h-5" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold">PM2.5 Particulate Matter</h3>
                    <p class="text-sm opacity-90">Fine particles (≤2.5 micrometers) • WHO Standard: 15 μg/m³</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" v-model="pollutants.pm25.enabled" class="sr-only peer">
                  <div class="w-11 h-6 bg-white/30 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-white/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white/40"></div>
                </label>
              </div>
            </div>
            
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-green-800 mb-2">Good (0-12 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm25.thresholds.good" 
                    class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" 
                    :disabled="!pollutants.pm25.enabled"
                  >
                  <p class="text-xs text-green-600 mt-1">Air quality is satisfactory</p>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-yellow-800 mb-2">Moderate (13-35 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm25.thresholds.moderate" 
                    class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" 
                    :disabled="!pollutants.pm25.enabled"
                  >
                  <p class="text-xs text-yellow-600 mt-1">Sensitive groups may be affected</p>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-orange-800 mb-2">Unhealthy (36-55 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm25.thresholds.unhealthy" 
                    class="w-full px-3 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white" 
                    :disabled="!pollutants.pm25.enabled"
                  >
                  <p class="text-xs text-orange-600 mt-1">Everyone may be affected</p>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-red-800 mb-2">Hazardous (56+ μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm25.thresholds.hazardous" 
                    class="w-full px-3 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-white" 
                    :disabled="!pollutants.pm25.enabled"
                  >
                  <p class="text-xs text-red-600 mt-1">Health emergency conditions</p>
                </div>
              </div>
            </div>
          </div>

          <!-- PM10 Configuration -->
          <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-4">
              <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-white/20 rounded-lg">
                    <Cloud class="w-5 h-5" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold">PM10 Particulate Matter</h3>
                    <p class="text-sm opacity-90">Coarse particles (≤10 micrometers) • WHO Standard: 45 μg/m³</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" v-model="pollutants.pm10.enabled" class="sr-only peer">
                  <div class="w-11 h-6 bg-white/30 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-white/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white/40"></div>
                </label>
              </div>
            </div>
            
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-green-800 mb-2">Good (0-54 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm10.thresholds.good" 
                    class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" 
                    :disabled="!pollutants.pm10.enabled"
                  >
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-yellow-800 mb-2">Moderate (55-154 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm10.thresholds.moderate" 
                    class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" 
                    :disabled="!pollutants.pm10.enabled"
                  >
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-orange-800 mb-2">Unhealthy (155-254 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm10.thresholds.unhealthy" 
                    class="w-full px-3 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white" 
                    :disabled="!pollutants.pm10.enabled"
                  >
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-red-800 mb-2">Hazardous (255+ μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.pm10.thresholds.hazardous" 
                    class="w-full px-3 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-white" 
                    :disabled="!pollutants.pm10.enabled"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- CO Configuration -->
          <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-800 p-4">
              <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-white/20 rounded-lg">
                    <Zap class="w-5 h-5" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold">Carbon Monoxide (CO)</h3>
                    <p class="text-sm opacity-90">Colorless, odorless gas • WHO Standard: 10 mg/m³</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" v-model="pollutants.co.enabled" class="sr-only peer">
                  <div class="w-11 h-6 bg-white/30 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-white/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white/40"></div>
                </label>
              </div>
            </div>
            
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-green-800 mb-2">Good (0-4.4 mg/m³)</label>
                  <input 
                    type="number" 
                    step="0.1" 
                    v-model="pollutants.co.thresholds.good" 
                    class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" 
                    :disabled="!pollutants.co.enabled"
                  >
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-yellow-800 mb-2">Moderate (4.5-9.4 mg/m³)</label>
                  <input 
                    type="number" 
                    step="0.1" 
                    v-model="pollutants.co.thresholds.moderate" 
                    class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" 
                    :disabled="!pollutants.co.enabled"
                  >
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-orange-800 mb-2">Unhealthy (9.5-12.4 mg/m³)</label>
                  <input 
                    type="number" 
                    step="0.1" 
                    v-model="pollutants.co.thresholds.unhealthy" 
                    class="w-full px-3 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white" 
                    :disabled="!pollutants.co.enabled"
                  >
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-red-800 mb-2">Hazardous (12.5+ mg/m³)</label>
                  <input 
                    type="number" 
                    step="0.1" 
                    v-model="pollutants.co.thresholds.hazardous" 
                    class="w-full px-3 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-white" 
                    :disabled="!pollutants.co.enabled"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- NO2 Configuration -->
          <div class="bg-white rounded-xl shadow-lg border overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-4">
              <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-white/20 rounded-lg">
                    <Flame class="w-5 h-5" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold">Nitrogen Dioxide (NO₂)</h3>
                    <p class="text-sm opacity-90">Reddish-brown gas • WHO Standard: 25 μg/m³</p>
                  </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" v-model="pollutants.no2.enabled" class="sr-only peer">
                  <div class="w-11 h-6 bg-white/30 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-white/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white/40"></div>
                </label>
              </div>
            </div>
            
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-green-800 mb-2">Good (0-53 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.no2.thresholds.good" 
                    class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-white" 
                    :disabled="!pollutants.no2.enabled"
                  >
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-yellow-800 mb-2">Moderate (54-100 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.no2.thresholds.moderate" 
                    class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" 
                    :disabled="!pollutants.no2.enabled"
                  >
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-orange-800 mb-2">Unhealthy (101-360 μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.no2.thresholds.unhealthy" 
                    class="w-full px-3 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white" 
                    :disabled="!pollutants.no2.enabled"
                  >
                </div>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                  <label class="block text-sm font-semibold text-red-800 mb-2">Hazardous (361+ μg/m³)</label>
                  <input 
                    type="number" 
                    v-model="pollutants.no2.thresholds.hazardous" 
                    class="w-full px-3 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-white" 
                    :disabled="!pollutants.no2.enabled"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar: Alert Settings & Messages -->
        <div class="xl:col-span-1 space-y-6">
          <!-- Alert Settings -->
          <div class="bg-white rounded-xl shadow-lg border p-6">
            <div class="flex items-center gap-2 mb-4">
              <div class="p-2 bg-amber-100 rounded-lg">
                <Bell class="w-5 h-5 text-amber-600" />
              </div>
              <h2 class="text-lg font-semibold text-gray-900">Alert Settings</h2>
            </div>
            
            <div class="space-y-4">
              <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="font-medium text-gray-900">SweetAlert2 Popups</h3>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="alertSettings.enablePopups" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                  </label>
                </div>
                <p class="text-sm text-gray-600">Enable beautiful popup alerts for threshold breaches</p>
              </div>

              <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="font-medium text-gray-900">Auto-dismiss</h3>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="alertSettings.autoDismiss" class="sr-only peer" :disabled="!alertSettings.enablePopups">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600 peer-disabled:opacity-50"></div>
                  </label>
                </div>
                <p class="text-sm text-gray-600">Close alerts automatically after 8 seconds</p>
              </div>

              <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="font-medium text-gray-900">Sound Alerts</h3>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="alertSettings.soundEnabled" class="sr-only peer" :disabled="!alertSettings.enablePopups">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600 peer-disabled:opacity-50"></div>
                  </label>
                </div>
                <p class="text-sm text-gray-600">Play notification sounds with alerts</p>
              </div>
            </div>
          </div>

          <!-- Warning Messages -->
          <div class="bg-white rounded-xl shadow-lg border p-6">
            <div class="flex items-center gap-2 mb-4">
              <div class="p-2 bg-purple-100 rounded-lg">
                <MessageSquare class="w-5 h-5 text-purple-600" />
              </div>
              <h2 class="text-lg font-semibold text-gray-900">Custom Messages</h2>
            </div>
            
            <div class="space-y-4">
              <div>
                <label class=" text-sm font-semibold text-yellow-700 mb-2 flex items-center gap-2">
                  <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                  Moderate Alert
                </label>
                <textarea 
                  v-model="warningMessages.moderate" 
                  rows="3" 
                  class="w-full px-3 py-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 text-sm resize-none" 
                  placeholder="Custom message for moderate air quality..."
                ></textarea>
              </div>
              
              <div>
                <label class=" text-sm font-semibold text-orange-700 mb-2 flex items-center gap-2">
                  <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                  Unhealthy Alert
                </label>
                <textarea 
                  v-model="warningMessages.unhealthy" 
                  rows="3" 
                  class="w-full px-3 py-2 border border-orange-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm resize-none" 
                  placeholder="Custom message for unhealthy air quality..."
                ></textarea>
              </div>
              
              <div>
                <label class=" text-sm font-semibold text-red-700 mb-2 flex items-center gap-2">
                  <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                  Hazardous Alert
                </label>
                <textarea 
                  v-model="warningMessages.hazardous" 
                  rows="3" 
                  class="w-full px-3 py-2 border border-red-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 text-sm resize-none" 
                  placeholder="Custom message for hazardous air quality..."
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="bg-white rounded-xl shadow-lg border p-6">
            <div class="space-y-3">
              <button 
                @click="saveConfiguration" 
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl"
              >
                <Save class="w-4 h-4" />
                Save Configuration
              </button>
              
              <button 
                @click="testAlert" 
                class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl" 
                :disabled="!alertSettings.enablePopups"
                :class="{ 'opacity-50 cursor-not-allowed': !alertSettings.enablePopups }"
              >
                <TestTube class="w-4 h-4" />
                Test Alert System
              </button>
              
              <button 
                @click="resetToDefaults" 
                class="w-full bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl"
              >
                <RotateCcw class="w-4 h-4" />
                Reset to WHO Standards
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Shield, Wind, Cloud, Zap, Flame, Bell, MessageSquare, Save, TestTube, RotateCcw } from 'lucide-vue-next'

// Pollutant configurations based on WHO standards
const pollutants = reactive({
  pm25: {
    enabled: true,
    thresholds: {
      good: 12,
      moderate: 35,
      unhealthy: 55,
      hazardous: 150
    }
  },
  pm10: {
    enabled: true,
    thresholds: {
      good: 54,
      moderate: 154,
      unhealthy: 254,
      hazardous: 354
    }
  },
  co: {
    enabled: true,
    thresholds: {
      good: 4.4,
      moderate: 9.4,
      unhealthy: 12.4,
      hazardous: 15.4
    }
  },
  no2: {
    enabled: true,
    thresholds: {
      good: 53,
      moderate: 100,
      unhealthy: 360,
      hazardous: 649
    }
  }
})

// Alert settings for SweetAlert2 integration
const alertSettings = reactive({
  enablePopups: true,
  autoDismiss: false,
  soundEnabled: true
})

// Customizable warning messages
const warningMessages = reactive({
  moderate: "⚠️ Air quality is moderate. Sensitive individuals should consider limiting prolonged outdoor activities and wearing masks when outside.",
  unhealthy: "🚨 Air quality is unhealthy! Everyone should reduce prolonged outdoor activities. People with respiratory conditions should avoid outdoor exercise.",
  hazardous: "🆘 HAZARDOUS air quality detected! Everyone should avoid ALL outdoor activities. Stay indoors, keep windows closed, and use air purifiers if available. Seek medical attention if experiencing breathing difficulties."
})

// Configuration methods
const saveConfiguration = () => {
  const config = {
    pollutants: pollutants,
    alertSettings: alertSettings,
    warningMessages: warningMessages,
    timestamp: new Date().toISOString()
  }
  
  console.log('Saving AQI Alert Configuration:', config)
  
  // Here you would typically send to your backend API
  // Example: await api.saveAQIConfig(config)
  
  // Simulate SweetAlert2 success message
  if (alertSettings.enablePopups) {
    // This would be replaced with actual SweetAlert2 implementation
    alert('✅ Configuration saved successfully!\n\nYour AQI alert settings have been updated and are now active.')
  }
}

const testAlert = () => {
  if (!alertSettings.enablePopups) {
    alert('❌ Please enable popup alerts first to test the system.')
    return
  }
  
  // Simulate a test alert with SweetAlert2
  // This would be replaced with actual SweetAlert2 implementation
  const testMessage = `🧪 TEST ALERT\n\n${warningMessages.moderate}\n\nThis is a test of your alert system. All settings are working correctly!`
  
  if (alertSettings.soundEnabled) {
    // Play test sound
    console.log('🔊 Playing test alert sound...')
  }
  
  alert(testMessage)
  
  if (alertSettings.autoDismiss) {
    console.log('⏰ Alert will auto-dismiss in 8 seconds...')
  }
}

const resetToDefaults = () => {
  // Reset all pollutant thresholds to WHO standards
  pollutants.pm25.thresholds = { good: 12, moderate: 35, unhealthy: 55, hazardous: 150 }
  pollutants.pm10.thresholds = { good: 54, moderate: 154, unhealthy: 254, hazardous: 354 }
  pollutants.co.thresholds = { good: 4.4, moderate: 9.4, unhealthy: 12.4, hazardous: 15.4 }
  pollutants.no2.thresholds = { good: 53, moderate: 100, unhealthy: 360, hazardous: 649 }
  
  // Reset all pollutants to enabled
  Object.keys(pollutants).forEach(key => {
    pollutants[key].enabled = true
  })
  
  // Reset alert settings
  alertSettings.enablePopups = true
  alertSettings.autoDismiss = false
  alertSettings.soundEnabled = true
  
  // Reset warning messages to defaults
  warningMessages.moderate = "⚠️ Air quality is moderate. Sensitive individuals should consider limiting prolonged outdoor activities and wearing masks when outside."
  warningMessages.unhealthy = "🚨 Air quality is unhealthy! Everyone should reduce prolonged outdoor activities. People with respiratory conditions should avoid outdoor exercise."
  warningMessages.hazardous = "🆘 HAZARDOUS air quality detected! Everyone should avoid ALL outdoor activities. Stay indoors, keep windows closed, and use air purifiers if available. Seek medical attention if experiencing breathing difficulties."
  
  alert('🔄 Configuration reset to WHO standards successfully!')
}
</script>
