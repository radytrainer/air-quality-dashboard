<template>
  <div class="min-h-screen flex relative overflow-hidden">
    <!-- Video Background -->
    <video
      autoplay
      muted
      loop
      playsinline
      class="absolute inset-0 w-full h-full object-cover z-0"
    >
      <source src="https://cdn.pixabay.com/video/2021/07/21/82389-578175075_large.mp4" type="video/mp4" />
      <img
        src="https://i.pinimg.com/originals/b6/65/67/b66567d57f9a1604e9ce0e68d76e0ee7.jpg"
        alt="Background"
        class="w-full h-full object-cover"
      />
    </video>

    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-black/40 z-10"></div>

    <!-- Left side with title -->
    <div class="flex-1 flex flex-col justify-center px-16 relative z-20">
      <div class="max-w-md">
        <h1 class="text-5xl font-bold text-white mb-4">{{ $t('auth.loginTitle') }}</h1>
        <p class="text-xl text-gray-300">{{ $t('auth.email') }} & {{ $t('auth.password') }}</p>
      </div>
    </div>

    <!-- Right side with form -->
    <div class="flex-1 flex items-center justify-center px-8 relative z-20">
      <div class="w-full max-w-md">
        <!-- Tab navigation -->
        <div class="flex mb-8 border-b border-white/20">
          <button class="px-6 py-2 text-white border-b-2 border-white font-medium">{{ $t('auth.login') }}</button>
          <button @click="$router.push('/register')" class="px-6 py-2 text-gray-300 hover:text-white">
            {{ $t('auth.register') }}
          </button>
        </div>

        <form @submit.prevent="login" class="space-y-6">
          <!-- Email -->
          <div>
            <label class="block text-white text-sm font-medium mb-3">{{ $t('auth.email') }}</label>
            <input
              v-model="email"
              type="email"
              :placeholder="$t('auth.emailPlaceholder')"
              class="w-full px-0 py-3 bg-transparent border-0 border-b border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white transition-colors text-base"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-white text-sm font-medium mb-3">{{ $t('auth.password') }}</label>
            <input
              v-model="password"
              type="password"
              :placeholder="$t('auth.passwordPlaceholder')"
              class="w-full px-0 py-3 bg-transparent border-0 border-b border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white transition-colors text-base"
            />
          </div>

          <!-- Remember me and Forgot password -->
          <div class="flex items-center justify-between mt-8">
            <label class="flex items-center">
              <input
                type="checkbox"
                class="w-4 h-4 text-white bg-transparent border border-white/30 rounded focus:ring-white focus:ring-1 accent-white"
              />
              <span class="ml-3 text-gray-300 text-sm">Remember me</span>
            </label>
            <a href="#" class="text-gray-300 text-sm hover:text-white transition-colors">
              Forgot your password?
            </a>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-black text-white py-4 rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-12 hover:bg-gray-900 text-base"
          >
            <span v-if="loading">{{ $t('auth.loggingIn') }}</span>
            <span v-else>{{ $t('auth.login') }}</span>
          </button>
        </form>

        <!-- Register link -->
        <p class="text-center text-gray-300 mt-8">
          {{ $t('auth.dontHaveAccount') }}
          <button @click="$router.push('/register')" class="text-white hover:underline font-medium ml-1">
            {{ $t('auth.register') }}
          </button>
        </p>

        <!-- Error Message -->
        <p v-if="errorMessage" class="text-sm text-center text-red-400 mt-4">
          {{ errorMessage }}
        </p>
      </div>
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

    // ✅ Store token and role
    const token = response.data.token
    const role = response.data.role
    auth.login(token, role)

    // ✅ Redirect based on role
    if (role === 'admin') {
      router.push('/admin-dashboard') // 🧭 Your admin dashboard route
    } else {
      router.push('/home') // 🧭 Your normal user home page
    }

  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Login failed.'
  } finally {
    loading.value = false
  }
}

</script>
