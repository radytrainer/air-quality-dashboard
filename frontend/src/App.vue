<template>
  <div class="min-h-screen" :class="showLayout ? 'flex bg-gray-100' : 'bg-gray-100'">
    <!-- Layout only shown if NOT login/register -->
    <template v-if="showLayout">
      <!-- Sidebar for admin -->
      <div v-if="auth.userRole === 'admin'" class="fixed top-0 left-0 h-screen w-64 bg-white shadow z-40">
        <Sidebar />
      </div>

      <!-- Main content area including navbar and content -->
      <div class="flex-1 flex flex-col min-h-screen" :class="auth.userRole === 'admin' ? 'ml-64' : ''">
        <!-- Navbar for non-admin users -->
        <div v-if="auth.userRole !== 'admin'" class="sticky top-0 z-30">
          <Navbar />
        </div>

        <!-- Admin topbar for admin -->
        <div v-if="auth.userRole === 'admin'" class="sticky top-0 z-30">
          <AdminTopBar />
        </div>

        <!-- Main content area -->
        <main class="flex-1 overflow-auto p-6">
          <RouterView />
        </main>
      </div>
    </template>

    <!-- Login/Register pages -->
    <template v-else>
      <RouterView />
    </template>

    <!-- Always include contact form modal -->
    <ContactView />
  </div>
</template>


<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/airQuality'

import Sidebar from './components/Sidebar.vue'
import Navbar from './components/Navbar.vue'
import AdminTopBar from './components/AdminTopBar.vue'
import ContactView from './views/ContactView.vue'

const route = useRoute()
const auth = useAuthStore()

const showLayout = computed(() => !['/login', '/register'].includes(route.path))
</script>
