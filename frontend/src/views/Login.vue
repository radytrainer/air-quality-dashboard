<template>
  <div class="min-h-screen flex items-center justify-center bg-yellow-50 px-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold text-yellow-800 mb-6 text-center">Login to Planet Air Check</h2>

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
        <router-link to="/register" class="text-yellow-600 hover:underline font-semibold">
          Register
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/airQuality'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)

const login = async () => {
  errorMessage.value = ''
  loading.value = true
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    const token = response.data.token
    auth.login(token) // save token in Pinia + localStorage

    router.push('/home') // redirect to home
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>
