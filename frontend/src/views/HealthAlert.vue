<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header with Status Overview -->
      <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-teal-600 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-3xl font-bold">Health Alert Dashboard</h1>
                <p class="text-blue-100 mt-2">Real-time air quality monitoring and alert management</p>
              </div>
              <div class="text-right">
                <div class="text-2xl font-bold">{{ totalAlerts }}</div>
                <div class="text-blue-100">Active Alerts</div>
              </div>
            </div>
          </div>
          
          <!-- Status Summary Cards -->
          <div class="p-6 bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
              <div 
                v-for="(status, key) in alertStatus" 
                :key="key"
                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300"
                :class="getStatusCardClass(key)"
              >
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-2xl font-bold" :class="getStatusTextClass(key)">{{ status.count }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ getStatusLabel(key) }}</div>
                  </div>
                  <div class="w-3 h-3 rounded-full" :class="getStatusDotClass(key)"></div>
                </div>
                <div class="mt-2">
                  <div class="w-full bg-gray-200 rounded-full h-1">
                    <div 
                      class="h-1 rounded-full transition-all duration-500" 
                      :class="getStatusBarClass(key)"
                      :style="{ width: `${(status.count / Math.max(...Object.values(alertStatus).map(s => s.count))) * 100}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Alert Configuration Cards -->
      <div class="grid gap-6">
        <!-- Good Air Quality -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-4">
            <div class="flex items-center justify-between text-white">
              <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                  <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold">Good Air Quality (0-50 AQI)</h3>
              </div>
              <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-medium">Minimal Risk</span>
            </div>
          </div>
          <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Public Message</label>
                <textarea
                  v-model="messages.good.public"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Message for general public..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Sensitive Groups</label>
                <textarea
                  v-model="messages.good.sensitive"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Message for sensitive individuals..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>


        <!-- Moderate Air Quality -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="bg-gradient-to-r from-yellow-500 to-amber-500 px-6 py-4">
            <div class="flex items-center justify-between text-white">
              <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                  <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold">Moderate Air Quality (51-100 AQI)</h3>
              </div>
              <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-medium">Low Risk</span>
            </div>
          </div>
          <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Public Message</label>
                <textarea
                  v-model="messages.moderate.public"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Message for general public..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Sensitive Groups</label>
                <textarea
                  v-model="messages.moderate.sensitive"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Message for sensitive individuals..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>


        <!-- Unhealthy for Sensitive Groups -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="bg-gradient-to-r from-orange-500 to-red-400 px-6 py-4">
            <div class="flex items-center justify-between text-white">
              <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                  <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold">Unhealthy for Sensitive Groups (101-150 AQI)</h3>
              </div>
              <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-medium">Moderate Risk</span>
            </div>
          </div>
          <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Public Message</label>
                <textarea
                  v-model="messages.unhealthySensitive.public"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Message for general public..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Sensitive Groups Alert</label>
                <textarea
                  v-model="messages.unhealthySensitive.sensitive"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Important alert for sensitive individuals..."
                ></textarea>
              </div>
            </div>
            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-700">Recommended Actions</label>
              <textarea
                v-model="messages.unhealthySensitive.actions"
                class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 resize-none"
                rows="2"
                placeholder="Specific actions and precautions..."
              ></textarea>
            </div>
          </div>
        </div>


        <!-- Unhealthy Air Quality -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="bg-gradient-to-r from-red-500 to-pink-500 px-6 py-4">
            <div class="flex items-center justify-between text-white">
              <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                  <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold">Unhealthy Air Quality (151-200 AQI)</h3>
              </div>
              <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-medium">High Risk</span>
            </div>
          </div>
          <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Public Health Warning</label>
                <textarea
                  v-model="messages.unhealthy.public"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Health warning for everyone..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Sensitive Groups Alert</label>
                <textarea
                  v-model="messages.unhealthy.sensitive"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Critical alert for sensitive groups..."
                ></textarea>
              </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Emergency Actions</label>
                <textarea
                  v-model="messages.unhealthy.emergency"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 resize-none"
                  rows="2"
                  placeholder="Emergency precautions and actions..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Activity Restrictions</label>
                <textarea
                  v-model="messages.unhealthy.restrictions"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 resize-none"
                  rows="2"
                  placeholder="Outdoor activity restrictions..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>


        <!-- Very Unhealthy Air Quality -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between text-white">
              <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                  <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold">Very Unhealthy Air Quality (201-300 AQI)</h3>
              </div>
              <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-medium">Very High Risk</span>
            </div>
          </div>
          <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Public Health Warning</label>
                <textarea
                  v-model="messages.veryUnhealthy.public"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Health warning for everyone..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Sensitive Groups Alert</label>
                <textarea
                  v-model="messages.veryUnhealthy.sensitive"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Critical alert for sensitive groups..."
                ></textarea>
              </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Emergency Actions</label>
                <textarea
                  v-model="messages.veryUnhealthy.emergency"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                  rows="2"
                  placeholder="Emergency precautions and actions..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Activity Restrictions</label>
                <textarea
                  v-model="messages.veryUnhealthy.restrictions"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                  rows="2"
                  placeholder="Outdoor activity restrictions..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>


        <!-- Hazardous Air Quality -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
          <div class="bg-gradient-to-r from-gray-800 to-red-900 px-6 py-4">
            <div class="flex items-center justify-between text-white">
              <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                  <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                </div>
                <h3 class="text-xl font-bold">Hazardous Air Quality (301+ AQI)</h3>
              </div>
              <span class="bg-red-600 px-4 py-2 rounded-full text-sm font-medium animate-pulse">Extreme Risk</span>
            </div>
          </div>
          <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Public Health Emergency</label>
                <textarea
                  v-model="messages.hazardous.public"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Health emergency for everyone..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Sensitive Groups Emergency</label>
                <textarea
                  v-model="messages.hazardous.sensitive"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-all duration-200 resize-none"
                  rows="3"
                  placeholder="Critical emergency for sensitive groups..."
                ></textarea>
              </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Emergency Actions</label>
                <textarea
                  v-model="messages.hazardous.emergency"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-all duration-200 resize-none"
                  rows="2"
                  placeholder="Strict emergency precautions and actions..."
                ></textarea>
              </div>
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Activity Restrictions</label>
                <textarea
                  v-model="messages.hazardous.restrictions"
                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-all duration-200 resize-none"
                  rows="2"
                  placeholder="All outdoor activities prohibited..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Action Panel -->
      <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
          <div class="flex flex-wrap items-center gap-4">
            <button
              @click="saveMessages"
              class="bg-gradient-to-r from-blue-600 to-teal-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-teal-700 focus:ring-4 focus:ring-blue-300 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              Save All Messages
            </button>
            <button
              @click="openPreview"
              class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 px-8 py-3 rounded-xl hover:from-gray-200 hover:to-gray-300 focus:ring-4 focus:ring-gray-300 transition-all duration-200 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            >
              Preview Messages
            </button>
            <button
              @click="resetDefaults"
              class="bg-gradient-to-r from-red-100 to-red-200 text-red-700 px-8 py-3 rounded-xl hover:from-red-200 hover:to-red-300 focus:ring-4 focus:ring-red-300 transition-all duration-200 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            >
              Reset to Default
            </button>
          </div>
          <div class="flex items-center">
            <transition name="fade">
              <div v-if="saved" class="text-green-600 font-semibold flex items-center bg-green-50 px-4 py-2 rounded-xl border border-green-200">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Messages Saved Successfully!
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>


    <!-- Enhanced Preview Modal -->
    <transition name="modal">
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-teal-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
              <h2 class="text-2xl font-bold">Health Alert Messages Preview</h2>
              <button 
                @click="showModal = false"
                class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>
          <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
            <div class="grid gap-6">
              <div 
                v-for="(msg, key) in messages" 
                :key="key" 
                class="border rounded-xl p-6 hover:shadow-md transition-all duration-200"
                :class="getPreviewCardClass(key)"
              >
                <div class="flex items-center mb-4">
                  <div class="w-4 h-4 rounded-full mr-3" :class="getStatusDotClass(key)"></div>
                  <div class="font-bold text-lg" :class="getStatusTextClass(key)">{{ getAQILabel(key) }}</div>
                </div>
                <div class="space-y-3 text-sm">
                  <div v-if="msg.public" class="bg-gray-50 p-3 rounded-lg">
                    <strong class="text-gray-700">Public:</strong> 
                    <span class="text-gray-600 ml-2">{{ msg.public }}</span>
                  </div>
                  <div v-if="msg.sensitive" class="bg-gray-50 p-3 rounded-lg">
                    <strong class="text-gray-700">Sensitive Groups:</strong> 
                    <span class="text-gray-600 ml-2">{{ msg.sensitive }}</span>
                  </div>
                  <div v-if="msg.actions" class="bg-gray-50 p-3 rounded-lg">
                    <strong class="text-gray-700">Recommended Actions:</strong> 
                    <span class="text-gray-600 ml-2">{{ msg.actions }}</span>
                  </div>
                  <div v-if="msg.emergency" class="bg-gray-50 p-3 rounded-lg">
                    <strong class="text-gray-700">Emergency Actions:</strong> 
                    <span class="text-gray-600 ml-2">{{ msg.emergency }}</span>
                  </div>
                  <div v-if="msg.restrictions" class="bg-gray-50 p-3 rounded-lg">
                    <strong class="text-gray-700">Activity Restrictions:</strong> 
                    <span class="text-gray-600 ml-2">{{ msg.restrictions }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";


const defaultMessages = {
  good: {
    public: "Air quality is good. Enjoy your outdoor activities!",
    sensitive: "No special precautions needed for sensitive groups.",
  },
  moderate: {
    public: "Air quality is moderate. Sensitive groups should take care.",
    sensitive: "Sensitive individuals should consider limiting prolonged outdoor exertion.",
  },
  unhealthySensitive: {
    public: "Unhealthy for sensitive groups. Limit prolonged outdoor exertion.",
    sensitive: "Sensitive groups should avoid outdoor activities.",
    actions: "Consider wearing masks and staying indoors.",
  },
  unhealthy: {
    public: "Unhealthy air quality. Everyone should reduce outdoor activities.",
    sensitive: "Critical alert for sensitive groups.",
    emergency: "Follow emergency precautions.",
    restrictions: "Outdoor activity restrictions in effect.",
  },
  veryUnhealthy: {
    public: "Very unhealthy air quality. Health alert for everyone.",
    sensitive: "Serious risk for sensitive groups.",
    emergency: "Avoid all outdoor activities. Follow emergency instructions.",
    restrictions: "Strict outdoor activity restrictions in effect.",
  },
  hazardous: {
    public: "Hazardous air quality. Health emergency for everyone.",
    sensitive: "Severe risk for sensitive groups.",
    emergency: "Remain indoors. Follow all emergency instructions.",
    restrictions: "All outdoor activities prohibited.",
  },
};

const messages = ref(JSON.parse(JSON.stringify(defaultMessages)));
const saved = ref(false);
const showModal = ref(false);

// Computed properties for dashboard stats
const alertStatus = computed(() => ({
  good: { count: Math.floor(Math.random() * 15) + 5 },
  moderate: { count: Math.floor(Math.random() * 10) + 3 },
  unhealthySensitive: { count: Math.floor(Math.random() * 8) + 2 },
  unhealthy: { count: Math.floor(Math.random() * 5) + 1 },
  veryUnhealthy: { count: Math.floor(Math.random() * 3) + 1 },
  hazardous: { count: Math.floor(Math.random() * 2) }
}));

const totalAlerts = computed(() => 
  Object.values(alertStatus.value).reduce((sum, status) => sum + status.count, 0)
);

onMounted(() => {
  const stored = localStorage.getItem("aqiHealthMessages");
  if (stored) {
    const loaded = JSON.parse(stored);
    messages.value = {
      good: { ...defaultMessages.good, ...(loaded.good || {}) },
      moderate: { ...defaultMessages.moderate, ...(loaded.moderate || {}) },
      unhealthySensitive: { ...defaultMessages.unhealthySensitive, ...(loaded.unhealthySensitive || {}) },
      unhealthy: { ...defaultMessages.unhealthy, ...(loaded.unhealthy || {}) },
      veryUnhealthy: { ...defaultMessages.veryUnhealthy, ...(loaded.veryUnhealthy || {}) },
      hazardous: { ...defaultMessages.hazardous, ...(loaded.hazardous || {}) },
    };
  }
});

function saveMessages() {
  localStorage.setItem("aqiHealthMessages", JSON.stringify(messages.value));
  saved.value = true;
  setTimeout(() => (saved.value = false), 3000);
}

function openPreview() {
  showModal.value = true;
}

function resetDefaults() {
  messages.value = JSON.parse(JSON.stringify(defaultMessages));
  localStorage.setItem("aqiHealthMessages", JSON.stringify(defaultMessages));
}

function getAQILabel(key) {
  const labels = {
    good: "Good (0-50 AQI)",
    moderate: "Moderate (51-100 AQI)",
    unhealthySensitive: "Unhealthy for Sensitive Groups (101-150 AQI)",
    unhealthy: "Unhealthy (151-200 AQI)",
    veryUnhealthy: "Very Unhealthy (201-300 AQI)",
    hazardous: "Hazardous (301+ AQI)"
  };
  return labels[key] || key;
}

function getStatusLabel(key) {
  const labels = {
    good: "Good",
    moderate: "Moderate", 
    unhealthySensitive: "USG",
    unhealthy: "Unhealthy",
    veryUnhealthy: "Very Unhealthy",
    hazardous: "Hazardous"
  };
  return labels[key] || key;
}


function getStatusCardClass(key) {
  const classes = {
    good: "border-l-4 border-green-500",
    moderate: "border-l-4 border-yellow-500",
    unhealthySensitive: "border-l-4 border-orange-500",
    unhealthy: "border-l-4 border-red-500",
    veryUnhealthy: "border-l-4 border-purple-500",
    hazardous: "border-l-4 border-gray-800"
  };
  return classes[key] || "";
}

function getStatusTextClass(key) {
  const classes = {
    good: "text-green-600",
    moderate: "text-yellow-600",
    unhealthySensitive: "text-orange-600",
    unhealthy: "text-red-600",
    veryUnhealthy: "text-purple-600",
    hazardous: "text-gray-800"
  };
  return classes[key] || "";
}

function getStatusDotClass(key) {
  const classes = {
    good: "bg-green-500",
    moderate: "bg-yellow-500",
    unhealthySensitive: "bg-orange-500",
    unhealthy: "bg-red-500",
    veryUnhealthy: "bg-purple-500",
    hazardous: "bg-gray-800"
  };
  return classes[key] || "";
}

function getStatusBarClass(key) {
  const classes = {
    good: "bg-green-500",
    moderate: "bg-yellow-500",
    unhealthySensitive: "bg-orange-500",
    unhealthy: "bg-red-500",
    veryUnhealthy: "bg-purple-500",
    hazardous: "bg-gray-800"
  };
  return classes[key] || "";
}

function getPreviewCardClass(key) {
  const classes = {
    good: "border-green-200 bg-green-50",
    moderate: "border-yellow-200 bg-yellow-50",
    unhealthySensitive: "border-orange-200 bg-orange-50",
    unhealthy: "border-red-200 bg-red-50",
    veryUnhealthy: "border-purple-200 bg-purple-50",
    hazardous: "border-gray-300 bg-gray-50"
  };
  return classes[key] || "";
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.9);
}
</style>
