<template>
  <nav
    class="w-full bg-white/95 backdrop-blur-md text-gray-900 flex items-center justify-between px-8 py-4 shadow-lg border-b border-gray-100 relative z-50"
  >
    <!-- Left Section: Logo and Search -->
    <div class="flex items-center gap-8">
      <!-- Logo -->
      <div class="w-16 h-16 rounded-2xl">
        <img
          src="../assets/videos/18454482.gif"
          alt="Air Quality Icon"
          class="w-full h-full object-cover"
        />
      </div>

      <!-- Search Bar -->
      <div class="relative hidden lg:block">
        <CitySearch @city-selected="handleCitySelected" />
        <p v-if="selectedCity" class="mt-4">{{ $t('search.selectedCity') }}: {{ selectedCity }}</p>
      </div>
    </div>

    <!-- Center: Menu Links -->
    <div class="hidden md:flex items-center gap-2 text-sm font-medium">
      <RouterLink
        :to="auth.userRole === 'admin' ? '/admin-dashboard' : '/home'"
        class="px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:text-blue-600 relative group whitespace-nowrap"
        :class="[$route.path === '/home' || $route.path === '/admin-dashboard' ? 'text-blue-600 bg-blue-50' : 'text-gray-700']"
      >
        {{ homeLabel }}
        <div
          v-if="$route.path === '/home' || $route.path === '/admin-dashboard'"
          class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-blue-600 rounded-full"
        />
      </RouterLink>

      <RouterLink
        to="/compare-cities"
        class="px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:text-blue-600 relative group whitespace-nowrap"
        :class="$route.path === '/compare-cities' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'"
      >
        {{ $t('nav.compareCities') }}
        <div
          v-if="$route.path === '/compare-cities'"
          class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-blue-600 rounded-full"
        />
      </RouterLink>

      <RouterLink
        to="/analytics"
        class="px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:text-blue-600 relative group whitespace-nowrap"
        :class="$route.path === '/analytics' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'"
      >
        {{ $t('nav.analytics') }}
        <div
          v-if="$route.path === '/analytics'"
          class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-blue-600 rounded-full"
        />
      </RouterLink>

      <RouterLink
        to="/user-news"
        class="px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:text-blue-600 relative group whitespace-nowrap"
        :class="$route.path === '/user-news' ? 'text-blue-600 bg-blue-50' : 'text-gray-700'"
      >
        {{ $t('nav.userNews') }}
        <div
          v-if="$route.path === '/user-news'"
          class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-blue-600 rounded-full"
        />
      </RouterLink>
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
          title="$t('profile.title')"
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
                {{ profile.name || $t('profile.user') }}
                  <span>({{ profile.role || $t('profile.roleNotSet') }})</span>
              </p>
              <p class="text-xs text-gray-500 truncate">  {{ profile.email || $t('profile.noEmail') }}</p>
            </div>
          </div>

          <div class="py-1">
            <button
              @click="navigateToProfile"
              class="flex items-center w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200"
            >
              <i class="fas fa-user text-gray-400 text-sm w-4 mr-3"></i>
              {{ $t('profile.profile') }}
            </button>

            <button
              @click="navigateToMessages"
              class="flex items-center w-full px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200"
            >
              <i class="fas fa-envelope text-gray-400 text-sm w-4 mr-3"></i>
                {{ $t('profile.messages') }}
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

    <!-- Mobile Menu Button -->
    <button
      class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
      @click="toggleMobileMenu"
    >
      <i class="fas fa-bars text-gray-700"></i>
    </button>

    <!-- Mobile Menu Overlay -->
    <div
      v-if="mobileMenuOpen"
      class="md:hidden absolute top-full left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40"
    >
      <div class="p-4 space-y-2">
        <RouterLink
          to="/home"
          class="block px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          @click="mobileMenuOpen = false"
        >
          {{ $t('nav.home') }}
        </RouterLink>
        <RouterLink
          to="/city-detail"
          class="block px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          @click="mobileMenuOpen = false"
        >
          {{ $t('nav.cityDetail') }}
        </RouterLink>
        <RouterLink
          to="/compare-cities"
          class="block px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          @click="mobileMenuOpen = false"
        >
          {{ $t('nav.compareCities') }}
        </RouterLink>
        <RouterLink
          to="/analytics"
          class="block px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          @click="mobileMenuOpen = false"
        >
          {{ $t('nav.analytics') }}
        </RouterLink>

        <!-- Mobile Profile Section -->
        <div v-if="isLoggedIn" class="border-t border-gray-200 pt-2 mt-4">
          <div class="px-4 py-2 text-sm text-gray-600">
            {{ profile.name || auth.userName || 'User' }}
            <span class="text-blue-600">({{ profile.role || auth.userRole || 'Loading...' }})</span>
            <p class="text-xs text-gray-500 truncate">  {{ profile.email || $t('profile.noEmail') }}</p>
          </div>
          <button
            @click="navigateToProfile(); mobileMenuOpen = false"
            class="block w-full text-left px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          >
              {{ $t('profile.profile') }}
          </button>
          <button
            @click="navigateToMessages(); mobileMenuOpen = false"
            class="block w-full text-left px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          >
          {{ $t('profile.messages') }}
          </button>
          <button
            @click="handleLogout(); mobileMenuOpen = false"
            class="block w-full text-left px-4 py-2 rounded-lg hover:bg-red-50 hover:text-red-600"
          >
            {{ $t('auth.logout') }}
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import CitySearch from '@/components/CitySearch.vue'
import { useAuthStore } from '@/stores/airQuality'
import { useI18n } from 'vue-i18n'
import api from '@/services/api'

const selectedCity = ref('')
const { locale, t } = useI18n()
const currentLanguage = ref(locale.value)
const languageDropdownOpen = ref(false)
const profileDropdownOpen = ref(false)
const mobileMenuOpen = ref(false)
const router = useRouter()
const route = useRoute()
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

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value
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
  // Close if click outside dropdown buttons or dropdown containers
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

const homeLabel = computed(() => {
  return auth.userRole === 'admin' ? t('nav.dashboard') : t('nav.home')
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

<style>
@import '@fortawesome/fontawesome-free/css/all.min.css';
</style>

<style scoped>
/* Custom scrollbar for dropdown */
.overflow-hidden::-webkit-scrollbar {
  width: 4px;
}
.overflow-hidden::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

/* Ensure text truncation works */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Smooth rotation for chevron */
.transition-transform {
  transition: transform 0.2s ease-in-out;
}
.rotate-180 {
  transform: rotate(180deg);
}

/* Loading animation */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.fa-spin {
  animation: spin 1s linear infinite;
}

/* Pulse animation for loading states */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
.animate-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}
</style>
