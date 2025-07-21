<template>
  <nav
    class="w-full bg-white/95 backdrop-blur-md text-gray-900 flex items-center justify-between px-8 py-4 shadow-lg border-b border-gray-100 relative z-50"
  >
    <!-- Left Section: Logo and Search -->
    <div class="flex items-center gap-8">
      <!-- Logo -->
      <div class="w-16 h-16 rounded-2xl ">
        <img
          src="../assets/images/Logo.png"
          alt="Air Quality Icon"
          class="w-full h-full object-cover"
        />
      </div>
      <!-- Search Bar -->
      <div class="relative hidden lg:block">
        <div>
          <CitySearch @city-selected="handleCitySelected" />
          <p v-if="selectedCity" class="mt-4">You selected: {{ selectedCity }}</p>
        </div>
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
        ></div>
      </RouterLink>

      <RouterLink
  :to="auth.userRole === 'admin' ? '/cityaqi' : '/city-detail'"
  class="px-4 py-2 rounded-lg transition-all duration-300 hover:bg-blue-50 hover:text-blue-600 relative group whitespace-nowrap"
  :class="[
    $route.path === '/city-detail' || $route.path === '/cityaqi'
      ? 'text-blue-600 bg-blue-50'
      : 'text-gray-700'
  ]"
>
  {{ auth.userRole === 'admin' ? 'City AQI' : $t('nav.cityDetail') }}
  <div
    v-if="$route.path === '/city-detail' || $route.path === '/cityaqi'"
    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-0.5 bg-blue-600 rounded-full"
  ></div>
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
        ></div>
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
        ></div>
      </RouterLink>
    </div>

    <!-- Right Section: AQI Standard, Language, Theme, Login -->
    <div class="flex items-center gap-2 relative">
      <!-- AQI Standard - Fixed size button -->
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

      <!-- Theme Toggle - Fixed size button -->
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

      <!-- Login / Logout - Fixed size button -->
      <button
        v-if="!isLoggedIn"
        class="flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 rounded-lg text-blue-600 hover:bg-blue-100 transition-all duration-200 border border-blue-200 w-[90px]"
        @click="$router.push('/login')"
      >
        <i class="fas fa-sign-in-alt text-blue-500 text-sm flex-shrink-0"></i>
        <span class="text-xs font-medium select-none truncate">{{
          $t('auth.login')
        }}</span>
      </button>
      <button
        v-else
        class="flex items-center justify-center gap-2 px-3 py-2 bg-red-50 rounded-lg text-red-600 hover:bg-red-100 transition-all duration-200 border border-red-200 w-[90px]"
        @click="logout"
      >
        <i class="fas fa-sign-out-alt text-red-500 text-sm flex-shrink-0"></i>
        <span class="text-xs font-medium select-none truncate">{{
          $t('auth.logout')
        }}</span>
      </button>
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
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import CitySearch from '@/components/CitySearch.vue'
import { useAuthStore } from '@/stores/airQuality' // your Pinia store path
import { useI18n } from 'vue-i18n'

const selectedCity = ref('')
const { locale, t } = useI18n()
const currentLanguage = ref(locale.value)
const languageDropdownOpen = ref(false)
const mobileMenuOpen = ref(false)

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

function handleCitySelected(city) {
  selectedCity.value = city
}

function toggleLanguageDropdown() {
  languageDropdownOpen.value = !languageDropdownOpen.value
}

function changeLanguage(lang) {
  currentLanguage.value = lang
  locale.value = lang
  languageDropdownOpen.value = false
}

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

// Reactive login status
const isLoggedIn = computed(() => auth.isAuthenticated)

// Dynamic label for Home/Dashboard link based on user role only
const homeLabel = computed(() => {
  return auth.userRole === 'admin' ? t('nav.dashboard') : t('nav.home')
})

function logout() {
  auth.logout()
  router.push('/login')
}
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
</style>
