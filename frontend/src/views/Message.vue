<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
        <p class="text-gray-600 mt-2">Manage your account settings and preferences</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <nav class="space-y-2">
              <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                  'w-full flex items-center gap-3 px-4 py-3 text-left rounded-lg transition-colors',
                  activeTab === tab.id
                    ? 'bg-blue-50 text-blue-600 border border-blue-200'
                    : 'text-gray-700 hover:bg-gray-50'
                ]"
              >
                <i :class="tab.icon"></i>
                <span class="font-medium">{{ tab.name }}</span>
              </button>
            </nav>
          </div>
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3">
          <!-- General Settings -->
          <div v-if="activeTab === 'general'" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">General Settings</h2>
            </div>
            <div class="p-6 space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                <select v-model="settings.language" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="en">English</option>
                  <option value="kh">ភាសាខ្មែរ (Khmer)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                <select v-model="settings.timezone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="UTC+7">UTC+7 (Cambodia)</option>
                  <option value="UTC+0">UTC+0 (GMT)</option>
                  <option value="UTC-5">UTC-5 (EST)</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                <select v-model="settings.dateFormat" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                  <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                  <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                  <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                </select>
              </div>

              <button @click="saveGeneralSettings" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>
                Save Changes
              </button>
            </div>
          </div>

          <!-- Security Settings -->
          <div v-if="activeTab === 'security'" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Security Settings</h2>
            </div>
            <div class="p-6 space-y-6">
              <!-- Change Password -->
              <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h3>
                <form @submit.prevent="changePassword" class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input
                      v-model="passwordForm.currentPassword"
                      type="password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter current password"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input
                      v-model="passwordForm.newPassword"
                      type="password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Enter new password"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input
                      v-model="passwordForm.confirmPassword"
                      type="password"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Confirm new password"
                    />
                  </div>
                  <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
                    <i class="fas fa-key mr-2"></i>
                    Update Password
                  </button>
                </form>
              </div>

              <!-- Two-Factor Authentication -->
              <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">Two-Factor Authentication</h3>
                    <p class="text-gray-600 text-sm">Add an extra layer of security to your account</p>
                  </div>
                  <button
                    @click="toggle2FA"
                    :class="[
                      'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                      settings.twoFactorEnabled ? 'bg-blue-600' : 'bg-gray-200'
                    ]"
                  >
                    <span
                      :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        settings.twoFactorEnabled ? 'translate-x-6' : 'translate-x-1'
                      ]"
                    />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Notifications -->
          <div v-if="activeTab === 'notifications'" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Notification Preferences</h2>
            </div>
            <div class="p-6 space-y-6">
              <div v-for="notification in notificationSettings" :key="notification.id" class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0">
                <div>
                  <h3 class="font-medium text-gray-900">{{ notification.title }}</h3>
                  <p class="text-sm text-gray-600">{{ notification.description }}</p>
                </div>
                <button
                  @click="toggleNotification(notification.id)"
                  :class="[
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                    notification.enabled ? 'bg-blue-600' : 'bg-gray-200'
                  ]"
                >
                  <span
                    :class="[
                      'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                      notification.enabled ? 'translate-x-6' : 'translate-x-1'
                    ]"
                  />
                </button>
              </div>
              <button @click="saveNotificationSettings" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>
                Save Preferences
              </button>
            </div>
          </div>

          <!-- Privacy Settings -->
          <div v-if="activeTab === 'privacy'" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-xl font-semibold text-gray-900">Privacy Settings</h2>
            </div>
            <div class="p-6 space-y-6">
              <div v-for="privacy in privacySettings" :key="privacy.id" class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0">
                <div>
                  <h3 class="font-medium text-gray-900">{{ privacy.title }}</h3>
                  <p class="text-sm text-gray-600">{{ privacy.description }}</p>
                </div>
                <button
                  @click="togglePrivacy(privacy.id)"
                  :class="[
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                    privacy.enabled ? 'bg-blue-600' : 'bg-gray-200'
                  ]"
                >
                  <span
                    :class="[
                      'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                      privacy.enabled ? 'translate-x-6' : 'translate-x-1'
                    ]"
                  />
                </button>
              </div>
              <button @click="savePrivacySettings" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>
                Save Settings
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const activeTab = ref('general')

const tabs = [
  { id: 'general', name: 'General', icon: 'fas fa-cog' },
  { id: 'security', name: 'Security', icon: 'fas fa-shield-alt' },
  { id: 'notifications', name: 'Notifications', icon: 'fas fa-bell' },
  { id: 'privacy', name: 'Privacy', icon: 'fas fa-lock' }
]

const settings = reactive({
  language: 'en',
  timezone: 'UTC+7',
  dateFormat: 'MM/DD/YYYY',
  twoFactorEnabled: false
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const notificationSettings = ref([
  {
    id: 'email_alerts',
    title: 'Email Alerts',
    description: 'Receive email notifications for important updates',
    enabled: true
  },
  {
    id: 'push_notifications',
    title: 'Push Notifications',
    description: 'Receive push notifications on your device',
    enabled: false
  },
  {
    id: 'sms_alerts',
    title: 'SMS Alerts',
    description: 'Receive SMS notifications for critical alerts',
    enabled: false
  }
])

const privacySettings = ref([
  {
    id: 'profile_visibility',
    title: 'Profile Visibility',
    description: 'Make your profile visible to other users',
    enabled: true
  },
  {
    id: 'data_sharing',
    title: 'Data Sharing',
    description: 'Allow sharing of anonymized data for research',
    enabled: false
  },
  {
    id: 'activity_tracking',
    title: 'Activity Tracking',
    description: 'Track your activity for personalized experience',
    enabled: true
  }
])

function saveGeneralSettings() {
  console.log('Saving general settings:', settings)
  // API call to save settings
}

function changePassword() {
  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    alert('Passwords do not match')
    return
  }
  console.log('Changing password')
  // API call to change password
}

function toggle2FA() {
  settings.twoFactorEnabled = !settings.twoFactorEnabled
}

function toggleNotification(id) {
  const notification = notificationSettings.value.find(n => n.id === id)
  if (notification) {
    notification.enabled = !notification.enabled
  }
}

function togglePrivacy(id) {
  const privacy = privacySettings.value.find(p => p.id === id)
  if (privacy) {
    privacy.enabled = !privacy.enabled
  }
}

function saveNotificationSettings() {
  console.log('Saving notification settings:', notificationSettings.value)
}

function savePrivacySettings() {
  console.log('Saving privacy settings:', privacySettings.value)
}

onMounted(() => {
  if (route.query.tab) {
    activeTab.value = route.query.tab
  }
})
</script>
