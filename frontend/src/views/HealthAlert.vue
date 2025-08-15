<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-8 py-6 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">Health Alert Message Configuration</h1>
          <p class="text-gray-600 mt-2">Customize health advisory messages for different air quality levels</p>
        </div>
      </div>

      <!-- Alert Messages Form -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-8 py-6">
          <form @submit.prevent="saveMessages" class="space-y-8">
            <!-- Good Air Quality -->
            <div class="border-l-4 border-green-500 bg-green-50 p-6 rounded-r-lg">
              <div class="flex items-center mb-4">
                <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                <h3 class="text-lg font-semibold text-green-800">Good (0-50 AQI)</h3>
                <span class="ml-auto text-sm text-green-600 bg-green-100 px-3 py-1 rounded-full">Minimal Risk</span>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Public Message</label>
                  <textarea
                    v-model="messages.good.public"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    rows="3"
                    placeholder="Message for general public..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sensitive Groups</label>
                  <textarea
                    v-model="messages.good.sensitive"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    rows="3"
                    placeholder="Message for sensitive individuals..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Moderate Air Quality -->
            <div class="border-l-4 border-yellow-500 bg-yellow-50 p-6 rounded-r-lg">
              <div class="flex items-center mb-4">
                <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                <h3 class="text-lg font-semibold text-yellow-800">Moderate (51-100 AQI)</h3>
                <span class="ml-auto text-sm text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full">Low Risk</span>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Public Message</label>
                  <textarea
                    v-model="messages.moderate.public"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                    rows="3"
                    placeholder="Message for general public..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sensitive Groups</label>
                  <textarea
                    v-model="messages.moderate.sensitive"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                    rows="3"
                    placeholder="Message for sensitive individuals..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Unhealthy for Sensitive Groups -->
            <div class="border-l-4 border-orange-500 bg-orange-50 p-6 rounded-r-lg">
              <div class="flex items-center mb-4">
                <div class="w-4 h-4 bg-orange-500 rounded-full mr-3"></div>
                <h3 class="text-lg font-semibold text-orange-800">Unhealthy for Sensitive Groups (101-150 AQI)</h3>
                <span class="ml-auto text-sm text-orange-600 bg-orange-100 px-3 py-1 rounded-full">Moderate Risk</span>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Public Message</label>
                  <textarea
                    v-model="messages.unhealthySensitive.public"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    rows="3"
                    placeholder="Message for general public..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sensitive Groups Alert</label>
                  <textarea
                    v-model="messages.unhealthySensitive.sensitive"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    rows="3"
                    placeholder="Important alert for sensitive individuals..."
                  ></textarea>
                </div>
              </div>
              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Recommended Actions</label>
                <textarea
                  v-model="messages.unhealthySensitive.actions"
                  class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                  rows="2"
                  placeholder="Specific actions and precautions..."
                ></textarea>
              </div>
            </div>

            <!-- Unhealthy Air Quality -->
            <div class="border-l-4 border-red-500 bg-red-50 p-6 rounded-r-lg">
              <div class="flex items-center mb-4">
                <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                <h3 class="text-lg font-semibold text-red-800">Unhealthy (151-200 AQI)</h3>
                <span class="ml-auto text-sm text-red-600 bg-red-100 px-3 py-1 rounded-full">High Risk</span>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Public Health Warning</label>
                  <textarea
                    v-model="messages.unhealthy.public"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    rows="3"
                    placeholder="Health warning for everyone..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sensitive Groups Alert</label>
                  <textarea
                    v-model="messages.unhealthy.sensitive"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    rows="3"
                    placeholder="Critical alert for sensitive groups..."
                  ></textarea>
                </div>
              </div>
              <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Actions</label>
                  <textarea
                    v-model="messages.unhealthy.emergency"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    rows="2"
                    placeholder="Emergency precautions and actions..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Activity Restrictions</label>
                  <textarea
                    v-model="messages.unhealthy.restrictions"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    rows="2"
                    placeholder="Outdoor activity restrictions..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Very Unhealthy Air Quality -->
            <div class="border-l-4 border-purple-500 bg-purple-50 p-6 rounded-r-lg">
              <div class="flex items-center mb-4">
                <div class="w-4 h-4 bg-purple-500 rounded-full mr-3"></div>
                <h3 class="text-lg font-semibold text-purple-800">Very Unhealthy (201-300 AQI)</h3>
                <span class="ml-auto text-sm text-purple-600 bg-purple-100 px-3 py-1 rounded-full">Very High Risk</span>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Public Health Warning</label>
                  <textarea
                    v-model="messages.veryUnhealthy.public"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    rows="3"
                    placeholder="Health warning for everyone..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sensitive Groups Alert</label>
                  <textarea
                    v-model="messages.veryUnhealthy.sensitive"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    rows="3"
                    placeholder="Critical alert for sensitive groups..."
                  ></textarea>
                </div>
              </div>
              <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Actions</label>
                  <textarea
                    v-model="messages.veryUnhealthy.emergency"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    rows="2"
                    placeholder="Emergency precautions and actions..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Activity Restrictions</label>
                  <textarea
                    v-model="messages.veryUnhealthy.restrictions"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    rows="2"
                    placeholder="Outdoor activity restrictions..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Hazardous Air Quality -->
            <div class="border-l-4 border-rose-700 bg-rose-50 p-6 rounded-r-lg">
              <div class="flex items-center mb-4">
                <div class="w-4 h-4 bg-rose-700 rounded-full mr-3"></div>
                <h3 class="text-lg font-semibold text-rose-800">Hazardous (301+ AQI)</h3>
                <span class="ml-auto text-sm text-rose-700 bg-rose-100 px-3 py-1 rounded-full">Extreme Risk</span>
              </div>
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Public Health Emergency</label>
                  <textarea
                    v-model="messages.hazardous.public"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-rose-700 focus:border-rose-700"
                    rows="3"
                    placeholder="Health emergency for everyone..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Sensitive Groups Emergency</label>
                  <textarea
                    v-model="messages.hazardous.sensitive"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-rose-700 focus:border-rose-700"
                    rows="3"
                    placeholder="Critical emergency for sensitive groups..."
                  ></textarea>
                </div>
              </div>
              <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Actions</label>
                  <textarea
                    v-model="messages.hazardous.emergency"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-rose-700 focus:border-rose-700"
                    rows="2"
                    placeholder="Strict emergency precautions and actions..."
                  ></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Activity Restrictions</label>
                  <textarea
                    v-model="messages.hazardous.restrictions"
                    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-2 focus:ring-rose-700 focus:border-rose-700"
                    rows="2"
                    placeholder="All outdoor activities prohibited..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
              <div class="flex items-center space-x-4">
                <button
                  type="submit"
                  class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium"
                >
                  Save All Messages
                </button>
                <button
                  type="button"
                  class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium"
                  @click="openPreview"
                >
                  Preview Messages
                </button>
                <button
                  type="button"
                  class="bg-red-100 text-red-700 px-6 py-3 rounded-lg hover:bg-red-200 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors font-medium"
                  @click="resetDefaults"
                >
                  Reset to Default
                </button>
              </div>
              <div class="flex items-center">
                <span v-if="saved" class="text-green-600 font-medium flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  Messages Saved Successfully!
                </span>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Preview Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-lg relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" @click="showModal = false">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <h2 class="text-xl font-bold mb-4">Preview Health Messages</h2>
        <div v-for="(msg, key) in messages" :key="key" class="mb-4 border-b pb-3 last:border-b-0 last:pb-0">
          <div class="font-semibold capitalize mb-1">{{ getAQILabel(key) }}</div>
          <div class="text-sm mb-1" v-if="msg.public"><strong>Public:</strong> {{ msg.public }}</div>
          <div class="text-sm mb-1" v-if="msg.sensitive"><strong>Sensitive Groups:</strong> {{ msg.sensitive }}</div>
          <div class="text-sm mb-1" v-if="msg.actions"><strong>Recommended Actions:</strong> {{ msg.actions }}</div>
          <div class="text-sm mb-1" v-if="msg.emergency"><strong>Emergency Actions:</strong> {{ msg.emergency }}</div>
          <div class="text-sm" v-if="msg.restrictions"><strong>Activity Restrictions:</strong> {{ msg.restrictions }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

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

onMounted(() => {
  const stored = localStorage.getItem("aqiHealthMessages");
  if (stored) {
    // Deep merge to ensure all keys exist
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
  setTimeout(() => (saved.value = false), 1500);
}

function openPreview() {
  showModal.value = true;
}

function resetDefaults() {
  messages.value = JSON.parse(JSON.stringify(defaultMessages));
  localStorage.setItem("aqiHealthMessages", JSON.stringify(defaultMessages));
}

function getAQILabel(key) {
  switch (key) {
    case "good":
      return "Good (0-50 AQI)";
    case "moderate":
      return "Moderate (51-100 AQI)";
    case "unhealthySensitive":
      return "Unhealthy for Sensitive Groups (101-150 AQI)";
    case "unhealthy":
      return "Unhealthy (151-200 AQI)";
    case "veryUnhealthy":
      return "Very Unhealthy (201-300 AQI)";
    case "hazardous":
      return "Hazardous (301+ AQI)";
    default:
      return key;
  }
}
</script>