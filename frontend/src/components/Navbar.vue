<template>
  <nav class="w-full bg-white text-gray-900 flex items-center justify-between px-6 py-3 shadow-md shadow-blue-100">
    <!-- Left Section: Logo and Search -->
    <div class="flex items-center gap-6">
      <!-- Logo -->
      <img
        src="/src/assets/images/Logo.png"
        alt="AQI Logo"
        class="h-18 w-40"
      />

      <!-- Search Bar -->
      <div class="relative">
        <input
          type="text"
          placeholder="Search any Location, City, State or Country"
          class="bg-gray-100 text-gray-900 px-10 py-2.5 rounded-md w-96 placeholder-gray-500 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-sky-400 transition"
        />
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
        <i
          class="fas fa-crosshairs absolute right-3 top-1/2 -translate-y-1/2 text-blue-500 cursor-pointer hover:text-blue-600 transition"
          title="Locate me"
        ></i>
      </div>
    </div>

    <!-- Center: Menu Links -->
    <div class="flex items-center gap-8 text-base font-semibold">
      <RouterLink
        to="/home"
        class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/home' ? 'text-sky-500' : 'text-gray-700'"
      >
        Home
      </RouterLink>
      <RouterLink
        to="/city-detail"
        class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/city-detail' ? 'text-sky-500' : 'text-gray-700'"
      >
        City Detail
      </RouterLink>
      <RouterLink
        to="/compare-cities"
        class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/compare-cities' ? 'text-sky-500' : 'text-gray-700'"
      >
        Compare Cities
      </RouterLink>
      <RouterLink
        to="/analytics"
        class="px-3 py-1 rounded transition-colors duration-200 hover:text-sky-500"
        :class="$route.path === '/analytics' ? 'text-sky-500' : 'text-gray-700'"
      >
        Analytics
      </RouterLink>
    </div>

    <!-- Right Section: AQI Standard, Language, Theme, Login -->
    <div class="flex items-center gap-6">
      <!-- AQI Standard -->
      <div
        class="flex items-center gap-1 border-r border-gray-300 pr-4 text-gray-700 cursor-pointer hover:text-sky-500 transition"
        title="Change AQI Standard"
      >
        <img src="https://flagcdn.com/us.svg" alt="US Flag" class="h-4 w-6 rounded-sm" />
        <span class="text-sm select-none">AQI-US</span>
        <i class="fas fa-caret-down ml-1 text-gray-500"></i>
      </div>

      <!-- Language -->
      <div
        class="flex items-center gap-1 border-r border-gray-300 pr-4 text-gray-700 cursor-pointer hover:text-sky-500 transition"
        title="Change Language"
      >
        <i class="fas fa-globe text-green-500"></i>
        <span class="text-sm select-none">English</span>
        <i class="fas fa-caret-down ml-1 text-gray-500"></i>
      </div>

      <!-- Theme Toggle -->
      <button
        class="border-r border-gray-300 pr-4 text-yellow-500 cursor-pointer hover:text-yellow-600 transition"
        aria-label="Toggle theme"
        title="Toggle Light/Dark Mode"
      >
        <i class="fas fa-sun"></i>
      </button>

      <!-- Login Button -->
      <button
        class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-md font-semibold transition"
      >
        Login
      </button>
    </div>
  </nav>
</template>

<script setup>

</script>

<style scoped>
@import "@fortawesome/fontawesome-free/css/all.min.css";
</style>
