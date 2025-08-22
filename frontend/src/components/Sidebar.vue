<template>
  <aside
    class="fixed top-0 left-0 h-screen w-64 bg-white text-gray-900 shadow-lg border-r border-gray-100 z-50 flex flex-col">
    <!-- Logo Section -->
    <div class="p-4 border-b border-gray-100">
      <div class="w-16 h-16 rounded-2xl mx-auto">
        <img src="../assets/videos/18454482.gif" alt="Air Quality Icon" class="w-full h-full object-cover" />
      </div>
      <p class="text-center mt-2 text-sm font-semibold text-gray-800">
        Air Quality Dashboard
      </p>
    </div>


    <!-- Main Navigation Links -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto font-medium text-sm">
      <!-- Dashboard -->
      <RouterLink :to="auth.userRole === 'admin' ? '/admin-dashboard' : '/home'"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="[$route.path === '/home' || $route.path === '/admin-dashboard' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700']"
        @click="mobileMenuOpen = false">
        <LayoutDashboard class="w-5 h-5" />
        {{ auth.userRole === 'admin' ? $t('nav.dashboard') : $t('nav.home') }}
      </RouterLink>

      <!-- City AQI / City Detail -->
      <RouterLink :to="auth.userRole === 'admin' ? '/cityaqi' : '/city-detail'"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="[$route.path === '/city-detail' || $route.path === '/cityaqi' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700']"
        @click="mobileMenuOpen = false">
        <Landmark class="w-5 h-5" />
        {{ auth.userRole === 'admin' ? $t('nav.cityAQI') : $t('nav.cityDetail') }}
      </RouterLink>

      <!-- Health Alert -->
      <RouterLink to="/health-alert"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="$route.path === '/health-alert' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700'"
        @click="mobileMenuOpen = false">
        <HeartPulse class="w-5 h-5" />
        {{ $t('nav.healthAlert') }}
      </RouterLink>

      <!-- News -->
      <RouterLink to="/admin-news"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="$route.path === '/admin-news' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700'"
        @click="mobileMenuOpen = false">
        <Users2 class="w-5 h-5" />
        {{ $t('nav.adminNews') }}
      </RouterLink>

      <!-- User Management -->
      <RouterLink to="/user-management"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="$route.path === '/user-management' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700'"
        @click="mobileMenuOpen = false">
        <Users2 class="w-5 h-5" />
        {{ $t('nav.userManagement') }}
      </RouterLink>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/airQuality'
import {
  LayoutDashboard,
  Landmark,
  HeartPulse,
  Users2
} from 'lucide-vue-next'

const auth = useAuthStore()
const route = useRoute()

const mobileMenuOpen = ref(false)
</script>
