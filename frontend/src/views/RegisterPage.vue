<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-100 via-white to-blue-200 flex items-center justify-center">
    <div class="w-full max-w-sm bg-white rounded-xl shadow-lg p-8">
      <h2 class="text-3xl font-bold text-center text-blue-700 mb-6">Create an Account</h2>

      <div class="space-y-4">
        <input
          v-model="name"
          type="text"
          placeholder="Full Name"
          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
        />

        <input
          v-model="email"
          type="email"
          placeholder="Email Address"
          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
        />

        <input
          v-model="password"
          type="password"
          placeholder="Password"
          class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <button
        @click="register"
        :disabled="loading"
        class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-md font-semibold transition duration-200 disabled:opacity-50"
      >
        {{ loading ? 'Registering...' : 'Register' }}
      </button>

      <p
        v-if="errorMessage"
        class="text-sm text-center text-red-600 mt-4"
      >
        {{ errorMessage }}
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

const name = ref('')
const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

const register = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
    })

    const token = res.data.token
    auth.login(token)

    router.push('/')
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
