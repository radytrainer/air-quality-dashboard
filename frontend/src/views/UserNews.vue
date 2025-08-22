
<template>
  <div><h1>New</h1></div>
  <div class="p-6 space-y-6">
    <div v-for="n in news" :key="n.id" class="border rounded p-4">
      <h3 class="font-semibold mb-2">{{ n.caption }}</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div v-for="(url, i) in n.media_urls" :key="i" class="border rounded p-2">
          <template v-if="url.match(/\.(mp4|webm)$/)">
            <video :src="url" controls class="w-full h-32 object-cover"></video>
          </template>
          <template v-else>
            <img :src="url" class="w-full h-32 object-cover" />
          </template>
        </div>
      </div>
      <div class="text-xs text-gray-500 mt-2">
        {{ new Date(n.created_at).toLocaleString() }}
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
  news.value = data
}

onMounted(() => {
  load()
  timer = setInterval(load, 5000)
})
onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
})
</script>
