<template>
  <div class="p-4 space-y-2 bg-gradient-to-b from-purple-100 to-white min-h-screen">
    <div 
      v-for="n in news" 
      :key="n.id" 
      class="flex items-center justify-between p-2 bg-white rounded-lg shadow-sm border border-gray-200"
    >
      <!-- Left side: avatar + caption -->
      <div class="flex items-center space-x-2">
        <!-- <img 
          :src="n.avatar_url || 'https://via.placeholder.com/40'" 
          class="w-10 h-10 rounded-full object-cover" 
          alt="Avatar" 
        /> -->
        <div>
          <h3 class="text-sm font-medium text-gray-800">{{ n.caption }}</h3>
          <div class="text-xs text-gray-500">{{ new Date(n.created_at).toLocaleString() }}</div>
        </div>
      </div>

      <!-- Right side: View button first, then media preview -->
      <div class="flex items-center space-x-4">
        <button 
          class="text-xs text-blue-500 hover:text-blue-700"
          @click="openMedia(n.media_urls)"
        >
          View
        </button>

        <div v-if="n.media_urls.length" class="grid grid-cols-2 gap-1">
          <img 
            v-for="(url, i) in n.media_urls.slice(0, 2)" 
            :key="i" 
            :src="url" 
            class="w-12 h-12 object-cover rounded cursor-pointer" 
            alt="Media" 
            @click="openMedia([url])"
          />
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
      <div class="bg-white p-4 rounded-lg max-w-lg w-full relative">
        <button 
          class="absolute top-2 right-2 text-gray-600 hover:text-black"
          @click="closeModal"
        >
          ✖
        </button>
        <div v-if="currentMedia.length" class="space-y-2">
          <div 
            v-for="(url, i) in currentMedia" 
            :key="i" 
            class="w-full"
          >
            <img 
              v-if="isImage(url)" 
              :src="url" 
              class="w-full max-h-96 object-contain rounded" 
              alt="Media" 
            />
            <video 
              v-else 
              controls 
              class="w-full max-h-96 rounded"
            >
              <source :src="url" />
              Your browser does not support the video tag.
            </video>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import api from '@/services/api'

const news = ref([])
let timer = null

// Modal state
const showModal = ref(false)
const currentMedia = ref([])

function openMedia(urls) {
  currentMedia.value = urls
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  currentMedia.value = []
}

function isImage(url) {
  return url.match(/\.(jpeg|jpg|gif|png|webp)$/i)
}

async function load() {
  const { data } = await api.get('/news')
  news.value = data.map(item => ({
    ...item,
    avatar_url: item.avatar_url || 'https://via.placeholder.com/40'
  }))
}

onMounted(() => {
  load()
  timer = setInterval(load, 5000)
})
onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
})
</script>
