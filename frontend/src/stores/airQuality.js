import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('auth_token') || null)
  const userRole = ref(localStorage.getItem('user_role') || null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => userRole.value === 'admin')

  function login(newToken, role) {
    token.value = newToken
    userRole.value = role
    localStorage.setItem('auth_token', newToken)
    localStorage.setItem('user_role', role)
    api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
  }

  function logout() {
    token.value = null
    userRole.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_role')
    delete api.defaults.headers.common['Authorization']
  }

  return {
    token,
    userRole,
    isAuthenticated,
    isAdmin,
    login,
    logout,
  }
})
