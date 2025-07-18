<!-- src/App.vue -->
<template>
  <Navbar />
  <div class="min-h-screen bg-gray-100">
    <RouterView />
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>
