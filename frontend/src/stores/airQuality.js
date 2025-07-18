// src/stores/auth.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('auth_token') || null)
  const isAuthenticated = computed(() => !!token.value)

  function login(newToken) {
    token.value = newToken
    localStorage.setItem('auth_token', newToken)
    api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
  }

  function logout() {
    token.value = null
    localStorage.removeItem('auth_token')
    delete api.defaults.headers.common['Authorization']
  }

  return {
    token,
    isAuthenticated,
    login,
    logout
  }
})
