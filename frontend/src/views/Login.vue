<template>
  <div class="min-h-screen flex items-center justify-center bg-yellow-50 px-4">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold text-yellow-800 mb-6 text-center">{{ $t('auth.loginTitle') }}</h2>

      <div class="space-y-4">
        <input
          v-model="email"
          type="email"
          :placeholder="$t('auth.email')"
          class="w-full px-4 py-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
        />

        <input
          v-model="password"
          type="password"
          :placeholder="$t('auth.password')"
          class="w-full px-4 py-2 border border-yellow-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400"
        />

        <button
          @click="login"
          :disabled="loading"
          class="w-full px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold rounded-lg transition duration-300"
        >
          <span v-if="loading">{{ $t('auth.loggingIn') }}</span>
          <span v-else>{{ $t('auth.login') }}</span>
        </button>

        <p v-if="errorMessage" class="text-red-500 text-center">{{ errorMessage }}</p>
      </div>

      <!-- Register Link -->
      <p class="mt-6 text-center text-sm text-gray-600">
        {{ $t('auth.dontHaveAccount') }}
        <router-link to="/register" class="text-yellow-600 hover:underline font-semibold">
          {{ $t('auth.register') }}
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
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
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

    // Get role and token from response
    const userRole = response.data.user.role
    const token = response.data.token

    // Debug logs to check role & token
    console.log('API response user role:', userRole)
    console.log('API response token:', token)

    // Save token and role in Pinia store + localStorage
    auth.login(token, userRole)

    console.log('Auth store role after login:', auth.role)
    console.log('Is admin?', auth.isAdmin)

    // Redirect based on role
    if (userRole === 'admin') {
      router.push('/admin-dashboard')
    } else {
      router.push('/home')
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || t('auth.loginFailed')
  } finally {
    loading.value = false
  }
}
</script>