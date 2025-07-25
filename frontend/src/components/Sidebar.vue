<template>
  <aside
    class="fixed top-0 left-0 h-screen w-64 bg-white text-gray-900 shadow-lg border-r border-gray-100 z-50 flex flex-col"
  >
    <!-- Logo Section -->
    <div class="p-4 border-b border-gray-100">
      <div class="w-16 h-16 rounded-2xl mx-auto">
        <img
          src="../assets/images/Logo.png"
          alt="Air Quality Icon"
          class="w-full h-full object-cover"
        />
      </div>
    </div>

    <!-- Main Navigation Links -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto font-medium text-sm">
      <!-- Dashboard -->
      <RouterLink
        :to="auth.userRole === 'admin' ? '/admin-dashboard' : '/home'"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="[$route.path === '/home' || $route.path === '/admin-dashboard' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700']"
        @click="mobileMenuOpen = false"
      >
        <LayoutDashboard class="w-5 h-5" />
        {{ homeLabel }}
      </RouterLink>

      <!-- City AQI -->
      <RouterLink
        :to="auth.userRole === 'admin' ? '/cityaqi' : '/city-detail'"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="[$route.path === '/city-detail' || $route.path === '/cityaqi' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700']"
        @click="mobileMenuOpen = false"
      >
        <Landmark class="w-5 h-5" />
        {{ auth.userRole === 'admin' ? 'City AQI' : $t('nav.cityDetail') }}
      </RouterLink>

      <!-- Compare Cities -->
      <RouterLink
        to="/compare-cities"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="$route.path === '/compare-cities' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700'"
        @click="mobileMenuOpen = false"
      >
        <RefreshCcw class="w-5 h-5" />
        {{ $t('nav.compareCities') }}
      </RouterLink>

      <!-- Analytics -->
      <RouterLink
        to="/analytics"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-blue-100 hover:text-blue-600"
        :class="$route.path === '/analytics' ? 'bg-blue-100 text-blue-600 font-semibold' : 'text-gray-700'"
        @click="mobileMenuOpen = false"
      >
        <BarChart2 class="w-5 h-5" />
        {{ $t('nav.analytics') }}
      </RouterLink>
    </nav>
  </aside>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/airQuality'

// Lucide icons
import {
  LayoutDashboard,
  Landmark,
  RefreshCcw,
  BarChart2
} from 'lucide-vue-next'

const auth = useAuthStore()
const route = useRoute()

const mobileMenuOpen = ref(false)

const homeLabel = computed(() => {
  return auth.userRole === 'admin' ? 'Dashboard' : 'Home'
})
</script>
