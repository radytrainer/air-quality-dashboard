<template>
  <div class="min-h-screen bg-cover bg-center bg-no-repeat flex" style="background-image: url('https://i.pinimg.com/originals/b6/65/67/b66567d57f9a1604e9ce0e68d76e0ee7.jpg');">
    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-black/40"></div>
    
    <!-- Left side with form -->
    <div class="flex-1 flex items-center justify-center px-8 relative z-10">
      <div class="w-full max-w-md">
        <!-- Tab navigation -->
        <div class="flex mb-8 border-b border-white/20">
          <button @click="$router.push('/login')" class="px-6 py-2 text-gray-300 hover:text-white">Login</button>
          <button class="px-6 py-2 text-white border-b-2 border-white font-medium">Sign Up</button>
        </div>

        <form @submit.prevent="register" class="space-y-6">
          <!-- Full Name -->
          <div>
            <label class="block text-white text-sm font-medium mb-3">Full Name</label>
            <input
              v-model="name"
              type="text"
              placeholder="Enter your full name"
              class="w-full px-0 py-3 bg-transparent border-0 border-b border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white transition-colors text-base"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-white text-sm font-medium mb-3">E-mail</label>
            <input
              v-model="email"
              type="email"
              placeholder="Enter your e-mail"
              class="w-full px-0 py-3 bg-transparent border-0 border-b border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white transition-colors text-base"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-white text-sm font-medium mb-3">Password</label>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                class="w-full px-0 py-3 bg-transparent border-0 border-b border-white/30 text-white placeholder-gray-400 focus:outline-none focus:border-white transition-colors text-base pr-10"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-0 top-3 text-gray-400 hover:text-white transition-colors"
              >
                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Terms and conditions -->
          <div class="flex items-start mt-8">
            <input type="checkbox" v-model="acceptTerms" class="w-4 h-4 text-white bg-transparent border border-white/30 rounded focus:ring-white focus:ring-1 mt-1 accent-white">
            <span class="ml-3 text-gray-300 text-sm">
              I agree to the <a href="#" class="text-white hover:underline">Terms and Conditions</a> and <a href="#" class="text-white hover:underline">Privacy Policy</a>
            </span>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || !acceptTerms"
            class="w-full bg-black text-white py-4 rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-12 hover:bg-gray-900 text-base"
          >
            {{ loading ? 'Creating Account...' : 'Create Account' }}
          </button>
        </form>

        <!-- Login link -->
        <p class="text-center text-gray-300 mt-8">
          Already have an account? 
          <button @click="$router.push('/login')" class="text-white hover:underline font-medium ml-1">Login here</button>
        </p>

        <p
          v-if="errorMessage"
          class="text-sm text-center text-red-400 mt-4"
        >
          {{ errorMessage }}
        </p>
      </div>
    </div>

    <!-- Right side with title -->
    <div class="flex-1 flex flex-col justify-center px-16 relative z-10">
      <div class="max-w-md">
        <h1 class="text-5xl font-bold text-white mb-4">Get Started</h1>
        <p class="text-xl text-gray-300">Create your new account</p>
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

const name = ref('')
const email = ref('')
const password = ref('')
const acceptTerms = ref(false)
const showPassword = ref(false)
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
      err.response?.data?.message || t('auth.registerFailed') || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>