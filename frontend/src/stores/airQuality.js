// stores/airQuality.js (your auth store)
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('auth_token') || null)
  const role = ref(localStorage.getItem('user_role') || null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => role.value === 'admin')

  function login(newToken, userRole) {
    token.value = newToken
    role.value = userRole
    localStorage.setItem('auth_token', newToken)
    localStorage.setItem('user_role', userRole)
    api.defaults.headers.common['Authorization'] = Bearer ${newToken}
  }

  function logout() {
    token.value = null
    role.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_role')
    delete api.defaults.headers.common['Authorization']
  }

  return {
    token,
    role,
    isAuthenticated,
    isAdmin,
    login,
    logout
  }
})