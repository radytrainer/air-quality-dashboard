<template>
  <div class="p-6 bg-gradient-to-br from-purple-50 via-white to-purple-100 min-h-screen">
    <!-- Big title and description -->
    <div class="mb-8 text-center">
      <h1 class="text-4xl font-bold text-purple-800 mb-2">Latest News Updates</h1>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Stay informed with the latest news and updates from our platform. Browse through recent stories, 
        explore detailed articles, and stay ahead with upcoming news announcements.
      </p>
    </div>

    <!-- Top summary stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="p-4 bg-white rounded-2xl shadow-md flex flex-col items-center">
        <p class="text-xl font-bold text-purple-600">{{ totalNews }}</p>
        <p class="text-gray-500 text-sm">Total News</p>
      </div>
      <div class="p-4 bg-white rounded-2xl shadow-md flex flex-col items-center">
        <p class="text-xl font-bold text-green-600">{{ totalCategories }}</p>
        <p class="text-gray-500 text-sm">Total Categories</p>
      </div>
      <div class="p-4 bg-white rounded-2xl shadow-md flex flex-col items-center">
        <p class="text-xl font-bold text-red-600">{{ latestPost?.caption || "No posts" }}</p>
        <p class="text-gray-500 text-sm">Latest Post</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- News Feed -->
      <div class="lg:col-span-3 bg-white rounded-2xl shadow-md p-4">
        <h2 class="font-semibold text-gray-700 mb-4">News Feed</h2>
        <div v-for="n in news" :key="n.id" class="flex items-start p-3 border-b last:border-0 hover:bg-gray-50 rounded-lg transition mb-4">
          <img 
            v-if="isImage(n.media_urls?.[0])" 
            :src="n.media_urls[0]" 
            class="w-24 h-24 object-cover rounded-lg mr-4" 
            alt="News image" 
            @error="handleImageError"
          />
          <div class="flex-1">
            <p class="font-medium text-gray-800 text-lg">{{ n.caption }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ new Date(n.created_at).toLocaleString() }}</p>
            <p class="text-sm text-gray-600 mt-2">{{ n.description || 'No description available' }}</p>
          </div>
          <button class="px-4 py-2 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600" @click="openMedia(n.media_urls)">
            View Media
          </button>
        </div>
      </div>

      <!-- News Timeline -->
      <div class="bg-white rounded-2xl shadow-md p-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="font-semibold text-gray-700">Appointments</h2>
          <div class="flex space-x-2">
            <button 
              class="px-3 py-1 rounded-lg text-sm"
              :class="tab==='latest' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-600'"
              @click="tab='latest'"
            >
              Latest
            </button>
            <button 
              class="px-3 py-1 rounded-lg text-sm"
              :class="tab==='lastNews' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-600'"
              @click="tab='lastNews'"
            >
              Last News
            </button>
          </div>
        </div>

        <div v-if="tab==='latest'">
          <div v-for="n in latestNews" :key="n.id" class="flex items-start p-3 border-b last:border-0 mb-2">
            <img 
              v-if="isImage(n.media_urls?.[0])" 
              :src="n.media_urls[0]" 
              class="w-16 h-16 object-cover rounded-lg mr-3" 
              alt="News image" 
              @error="handleImageError"
            />
            <div>
              <p class="font-medium text-gray-800">{{ n.caption }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ new Date(n.created_at).toLocaleString() }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ n.description || 'No description available' }}</p>
            </div>
          </div>
        </div>

        <div v-else>
          <div v-for="n in lastNews" :key="n.id" class="flex items-start p-3 border-b last:border-0 mb-2">
            <img 
              v-if="isImage(n.media_urls?.[0])" 
              :src="n.media_urls[0]" 
              class="w-16 h-16 object-cover rounded-lg mr-3" 
              alt="News image" 
              @error="handleImageError"
            />
            <div>
              <p class="font-medium text-gray-800">{{ n.caption }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ new Date(n.created_at).toLocaleString() }}</p>
              <p class="text-xs text-gray-600 mt-1">{{ n.description || 'No description available' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 px-4">
      <div class="bg-white p-6 rounded-xl max-w-2xl w-full relative overflow-y-auto max-h-[90vh]">
        <button 
          class="absolute top-3 right-3 text-gray-600 hover:text-black text-lg"
          @click="closeModal"
        >
          ✖
        </button>
        <div v-if="currentMedia.length" class="space-y-4">
          <div v-for="(url, i) in currentMedia" :key="i" class="w-full">
            <img 
              v-if="isImage(url)" 
              :src="url" 
              class="w-full max-h-96 object-contain rounded-lg" 
              alt="Media image" 
              @error="handleImageError"
            />
            <video v-else controls class="w-full max-h-96 rounded-lg">
              <source :src="url" />
            </video>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const news = ref([])
const tab = ref('latest')
const totalNews = ref(0)
const totalCategories = ref(0)
const latestPost = ref(null)

const showModal = ref(false)
const currentMedia = ref([])

function openMedia(urls) {
  currentMedia.value = urls || []
  showModal.value = true
}
function closeModal() {
  showModal.value = false
  currentMedia.value = []
}
function isImage(url) {
  return url && url.match(/\.(jpeg|jpg|gif|png|webp)$/i)
}
function handleImageError(e) {
  e.target.style.display = 'none' // Hide broken images
}

const latestNews = ref([])
const lastNews = ref([])

async function load() {
  const { data } = await api.get('/news')
  news.value = data
  totalNews.value = data.length
  latestPost.value = data[0] || null

  const now = new Date()
  latestNews.value = data.filter(n => {
    const created = new Date(n.created_at)
    return (now - created) / 3600000 <= 12 // last 12 hours
  })
  lastNews.value = data.filter(n => new Date(n.created_at) <= now)
}

onMounted(load)
</script>

<style scoped>
/* Custom styles to match the provided image design */
.summary-card {
  background: #f9f9ff;
  border-radius: 12px;
  padding: 12px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}
.summary-card:hover {
  transform: scale(1.05);
}
.summary-card .value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #6b7280;
}
.summary-card .label {
  font-size: 0.75rem;
  color: #9ca3af;
}
.summary-card.n-a {
  color: #ef4444;
}
.summary-card.n-a .label {
  color: #dc2626;
}
.summary-card.low {
  background: #ecfdf5;
}
.summary-card.low .value {
  color: #10b981;
}
.summary-card.low .label {
  color: #047857;
}
.summary-card.avg {
  background: #eef2ff;
}
.summary-card.avg .value {
  color: #6366f1;
}
.summary-card.avg .label {
  color: #4f46e5;
}
</style>