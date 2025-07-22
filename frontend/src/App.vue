<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Show Navbar only when not on login or register -->
    <Navbar v-if="showNavbar" />
    
    <!-- Contact Form Modal Component -->
    <ContactView/>
    
    <RouterView />
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import Navbar from './components/Navbar.vue'
import ContactView from './views/ContactView.vue'


const route = useRoute()

// Check if route path is NOT /login or /register
const showNavbar = computed(() => {
  return !['/login', '/register'].includes(route.path)
})
</script>