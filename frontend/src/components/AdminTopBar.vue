<template>
  <nav
    class="w-full bg-white/95 backdrop-blur-md text-gray-900 flex items-center justify-between px-8 py-4 shadow-lg border-b border-gray-100 relative z-50"
  >
    <!-- Left Section: Search Bar -->
    <div class="flex items-center gap-8">
      <div class="relative hidden lg:block">
        <CitySearch @city-selected="handleCitySelected" />
        <p v-if="selectedCity" class="mt-4">You selected: {{ selectedCity }}</p>
      </div>
    </div>

    <!-- Right Section: AQI, Language, Theme, Login/Profile -->
    <div class="flex items-center gap-2 relative">
      <!-- AQI Standard -->
      <button
        class="flex items-center justify-center gap-2 px-3 py-2 bg-gray-50 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200 border border-gray-200 w-[100px]"
        title="Change AQI Standard"
      >
        <img
          src="https://flagcdn.com/us.svg"
          alt="US Flag"
          class="h-4 w-5 rounded-sm shadow-sm flex-shrink-0"
        />
        <span class="text-xs font-medium select-none truncate">AQI-US</span>
      </button>

      <!-- Theme Toggle -->
      <button
        class="flex items-center justify-center p-2 bg-gray-50 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200 border border-gray-200 w-10 h-9"
        aria-label="Toggle theme"
        title="Toggle Light/Dark Mode"
      >
        <i class="fas fa-sun text-yellow-500 text-sm"></i>
      </button>

      <!-- Language Switcher -->
      <div class="relative">
        <button
          @click="toggleLanguageDropdown"
          class="flex items-center justify-center gap-2 px-3 py-2 bg-gray-50 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200 border border-gray-200 w-[100px]"
          title="Change Language"
        >
          <i class="fas fa-globe text-blue-500 text-sm flex-shrink-0"></i>
          <span class="text-xs font-medium select-none truncate">
            {{ currentLanguage === 'en' ? 'English' : 'ខ្មែរ' }}
          </span>
        </button>

        <div
          v-if="languageDropdownOpen"
          class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
        >
          <button
            @click="changeLanguage('en')"
            class="block w-full px-4 py-3 text-left text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200 whitespace-nowrap"
          >
            English
          </button>
          <button
            @click="changeLanguage('kh')"
            class="block w-full px-4 py-3 text-left text-sm hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200 whitespace-nowrap"
          >
            ភាសាខ្មែរ
          </button>
        </div>
      </div>

      <!-- Login / Profile -->
      <div v-if="!isLoggedIn">
        <button
          class="flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 rounded-lg text-blue-600 hover:bg-blue-100 transition-all duration-200 border border-blue-200 w-[90px]"
          @click="$router.push('/login')"
        >
          <i class="fas fa-sign-in-alt text-blue-500 text-sm flex-shrink-0"></i>
          <span class="text-xs font-medium select-none truncate">
            {{ $t('auth.login') }}
          </span>
        </button>
      </div>

      <div v-else class="relative">
        <button
          @click="toggleProfileDropdown"
          class="flex items-center justify-center gap-2 px-3 py-2 bg-green-50 rounded-lg text-green-600 hover:bg-green-100 transition-all duration-200 border border-green-200 min-w-[120px] max-w-[150px]"
          title="User Profile"
          :disabled="loadingProfile"
        >
          <i v-if="loadingProfile" class="fas fa-spinner fa-spin text-green-500 text-sm flex-shrink-0"></i>
          <img
            v-else-if="profile.profile_image"
            :src="profile.profile_image"
            alt="Profile Image"
            class="w-6 h-6 rounded-full object-cover flex-shrink-0"
          />
          <i v-else class="fas fa-user-circle text-green-500 text-sm flex-shrink-0"></i>

          <span class="text-xs font-medium select-none truncate">
            {{ displayUserName }}
          </span>
          <i
            class="fas fa-chevron-down text-green-500 text-xs flex-shrink-0 transition-transform duration-200"
            :class="{ 'rotate-180': profileDropdownOpen }"
          ></i>
        </button>

        <div
          v-if="profileDropdownOpen"
          class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
        >
          <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <div v-if="loadingProfile" class="animate-pulse">
              <div class="h-4 bg-gray-300 rounded mb-2"></div>
            </div>
            <div v-else>
              <p class="text-sm font-medium text-gray-900">
                {{ profile.name || 'User' }}
                <span class="text-blue-600">({{ profile.role || 'Role not set' }})</span>
              </p>
              <p class="text-xs text-gray-500 truncate">{{ profile.email || 'No email' }}</p>
            </div>
          </div>

          <div class="py-1">
            <button
              @click="navigateToProfile"
              class="flex items-center w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200"
            >
              <i class="fas fa-user text-gray-400 text-sm w-4 mr-3"></i>
              Profile
            </button>

            <button
              @click="navigateToMessages"
              class="flex items-center w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200"
            >
              <i class="fas fa-envelope text-gray-400 text-sm w-4 mr-3"></i>
              Messages
            </button>

            <div class="border-t border-gray-100 my-1"></div>

            <button
              @click="handleLogout"
              class="flex items-center w-full px-4 py-3 text-left text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
            >
              <i class="fas fa-sign-out-alt text-red-500 text-sm w-4 mr-3"></i>
              {{ $t('auth.logout') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import CitySearch from '@/components/CitySearch.vue'
import { useAuthStore } from '@/stores/airQuality'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

const selectedCity = ref('')
const { locale, t } = useI18n()
const currentLanguage = ref(locale.value)
const languageDropdownOpen = ref(false)
const profileDropdownOpen = ref(false)
const router = useRouter()
const auth = useAuthStore()

const profile = ref({})
const loadingProfile = ref(false)
const profileError = ref(null)

function handleCitySelected(city) {
  selectedCity.value = city
}

function toggleLanguageDropdown() {
  languageDropdownOpen.value = !languageDropdownOpen.value
  profileDropdownOpen.value = false
}

function toggleProfileDropdown() {
  profileDropdownOpen.value = !profileDropdownOpen.value
  languageDropdownOpen.value = false
}

function changeLanguage(lang) {
  currentLanguage.value = lang
  locale.value = lang
  languageDropdownOpen.value = false
}

async function fetchUserProfile() {
  if (!auth.token) return
  loadingProfile.value = true
  profileError.value = null
  try {
    const response = await api.get('/profile')
    profile.value = response.data
    auth.userName = profile.value.name || ''
    auth.userRole = profile.value.role || ''
    localStorage.setItem('user_name', auth.userName)
    localStorage.setItem('user_role', auth.userRole)
  } catch (error) {
    profileError.value = error.response?.data?.message || 'Failed to fetch profile.'
    console.error('Failed to fetch profile:', error)
    if (error.response?.status === 401) {
      auth.logout()
      router.push('/login')
    }
  } finally {
    loadingProfile.value = false
  }
}

// Close dropdowns when clicking outside
function handleClickOutside(event) {
  if (
    !event.target.closest('.relative') &&
    !event.target.closest('button')
  ) {
    languageDropdownOpen.value = false
    profileDropdownOpen.value = false
  }
}

function navigateToProfile() {
  profileDropdownOpen.value = false
  router.push('/profile')
}

function navigateToMessages() {
  profileDropdownOpen.value = false
  router.push('/messages')
}

function handleLogout() {
  profileDropdownOpen.value = false
  auth.logout()
  router.push('/login')
}

const isLoggedIn = computed(() => auth.isAuthenticated)

const displayUserName = computed(() => {
  if (loadingProfile.value) return 'Loading...'
  return profile.value.name || auth.userName || 'User'
})

watch(() => auth.isAuthenticated, (newVal) => {
  if (newVal) fetchUserProfile()
  else profile.value = {}
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  if (auth.isAuthenticated) fetchUserProfile()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
