<template>
  <nav
    class="w-full bg-white text-gray-900 flex items-center justify-between px-6 py-3 shadow-md shadow-blue-100 relative"
  >
    <!-- Left Section: Logo and Search -->
    <div class="flex items-center gap-6">
      <!-- Logo -->
      <img src="/src/assets/images/Logo.png" alt="AQI Logo" class="h-18 w-40" />

      <!-- Search Bar -->
      <div class="relative">

        <input
          type="text"
          placeholder="Search any Location, City, State or Country"
          class="bg-gray-100 text-gray-900 px-10 py-2.5 rounded-md w-96 placeholder-gray-500 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-sky-400 transition"
        />
        <i
          class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"
        ></i>
        <i
          class="fas fa-crosshairs absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer hover:text-blue-600 transition"
          title="Locate me"
        ></i>
          <div>
        <CitySearch @city-selected="handleCitySelected" />
      <p v-if="selectedCity" class="mt-4">You selected: {{ selectedCity }}</p>
      </div>
      </div>
    </div>

    <!-- Center: Menu Links -->
    <div class="flex items-center gap-8 text-base font-semibold">
      <RouterLink to="/home" class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/home' ? 'text-sky-500' : 'text-gray-700'">
        Home
      </RouterLink>
      <RouterLink to="/city-detail" class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/city-detail' ? 'text-sky-500' : 'text-gray-700'">
        City Detail
      </RouterLink>
      <RouterLink to="/compare-cities" class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/compare-cities' ? 'text-sky-500' : 'text-gray-700'">
        Compare Cities
      </RouterLink>
      <RouterLink to="/analytics" class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/analytics' ? 'text-sky-500' : 'text-gray-700'">
        Analytics
      </RouterLink>
    </div>

    <!-- Right Section: AQI Standard, Language, Theme, Login -->
    <div class="flex items-center gap-6 relative">
      <!-- AQI Standard -->
      <div
        class="flex items-center gap-1 border-r border-gray-300 pr-4 text-gray-700 cursor-pointer hover:text-sky-500 transition"
        title="Change AQI Standard">
        <img src="https://flagcdn.com/us.svg" alt="US Flag" class="h-4 w-6 rounded-sm" />
        <span class="text-sm select-none">AQI-US</span>
        <i class="fas fa-caret-down ml-1 text-gray-500"></i>
      </div>

      <!-- Language -->
      <div
        class="flex items-center gap-1 border-r border-gray-300 pr-4 text-gray-700 cursor-pointer hover:text-sky-500 transition"
        title="Change Language">
        <i class="fas fa-globe text-green-500"></i>
        <span class="text-sm select-none">English</span>
        <i class="fas fa-caret-down ml-1 text-gray-500"></i>
      </div>

      <!-- Theme Toggle -->
      <button class="border-r border-gray-300 pr-4 text-yellow-500 cursor-pointer hover:text-yellow-600 transition"
        aria-label="Toggle theme" title="Toggle Light/Dark Mode">
        <i class="fas fa-sun"></i>
      </button>
      <!-- Login / Logout -->
      <div>
        <button
          v-if="!isLoggedIn"
          @click="toggleLoginForm"
          class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-md font-semibold transition"
        >
          Login
        </button>
        <button
          v-else
          @click="logout"
          class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-md font-semibold transition"
        >
          Logout
        </button>

        <!-- Login Form Dropdown -->
        <transition name="fade">
          <div
            v-if="showLoginForm && !isLoggedIn"
            class="absolute right-0 mt-3 w-full max-w-md bg-white rounded-lg shadow-lg p-8 z-50"
          >
            <h2 class="text-2xl font-bold text-yellow-800 mb-6 text-center">
              Login to Planet Air Check
            </h2>

            <div class="space-y-4">
              <input
                v-model="email"
                type="email"
                placeholder="Email"
                class="w-full px-4 py-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
              />

              <input
                v-model="password"
                type="password"
                placeholder="Password"
                class="w-full px-4 py-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
              />

              <button
                @click="login"
                :disabled="loading"
                class="w-full px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold rounded-lg transition duration-300"
              >
                <span v-if="loading">Logging in...</span>
                <span v-else>Login</span>
              </button>

              <p v-if="errorMessage" class="text-red-500 text-center">{{ errorMessage }}</p>
            </div>

            <!-- Register Link -->
            <p class="mt-6 text-center text-sm text-gray-600">
              Don’t have an account?
              <router-link
                to="/register"
                class="text-yellow-600 hover:underline font-semibold"
                @click="closeLoginForm"
              >
                Register
              </router-link>
            </p>
          </div>
        </transition>
      </div>
<!-- Login Button -->
      <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-md font-semibold transition">
        Login

    </div>
  </nav>
</template>

<script setup>

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/airQuality'

const router = useRouter()
const auth = useAuthStore()

const isLoggedIn = ref(auth.isLoggedIn) // Assume you have isLoggedIn state in Pinia
const showLoginForm = ref(false)
const loading = ref(false)
const errorMessage = ref('')

const email = ref('')
const password = ref('')

function toggleLoginForm() {
  showLoginForm.value = !showLoginForm.value
  errorMessage.value = ''
}

function closeLoginForm() {
  showLoginForm.value = false
  errorMessage.value = ''
}

async function login() {
  errorMessage.value = ''
  loading.value = true
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    const token = response.data.token
    auth.login(token) // save token in Pinia + localStorage
    isLoggedIn.value = true
    showLoginForm.value = false

    email.value = ''
    password.value = ''

    router.push('/home')
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || 'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}

function logout() {
  auth.logout() // clear token from Pinia + localStorage
  isLoggedIn.value = false
  showLoginForm.value = false

import CitySearch from './CitySearch.vue'
import { ref } from 'vue'

const selectedCity = ref('')

function handleCitySelected(city) {
  selectedCity.value = city

}
</script>

<style scoped>
@import '@fortawesome/fontawesome-free/css/all.min.css';

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
