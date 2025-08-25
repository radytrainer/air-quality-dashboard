<template>
  <div class="p-4 space-y-2 bg-gradient-to-b from-purple-100 to-white min-h-screen">
    <div v-for="n in news" :key="n.id" class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="flex items-center space-x-2">
        <img :src="n.avatar_url || 'https://via.placeholder.com/40'" class="w-10 h-10 rounded-full object-cover" alt="Avatar" />
        <div>
          <h3 class="text-sm font-medium text-gray-800">{{ n.caption }}</h3>
          <div class="text-xs text-gray-500">{{ new Date(n.created_at).toLocaleString() }}</div>
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <div v-if="n.media_urls.length" class="grid grid-cols-2 gap-1">
          <img v-for="(url, i) in n.media_urls.slice(0, 2)" :key="i" :src="url" class="w-12 h-12 object-cover rounded" alt="Media" />
        </div>
        <button class="text-xs text-blue-500 hover:text-blue-700">View</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import api from '@/services/api'

const news = ref([])
let timer = null

async function load() {
  const { data } = await api.get('/news')
  news.value = data.map(item => ({
    ...item,
    avatar_url: item.avatar_url || 'https://via.placeholder.com/40' // Fallback avatar
  }))
  console.log('News data:', news.value) // Debug: Check data structure
}

onMounted(() => {
  load()
  timer = setInterval(load, 5000)
})
onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
})
</script>