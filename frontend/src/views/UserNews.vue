<template>
  <div class="p-6 bg-gradient-to-br from-purple-50 via-white to-purple-100 min-h-screen">
    <!-- Big title and description -->
    <div class="mb-10 text-center">
      <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-700 to-indigo-600 mb-4">
        Latest News Updates
      </h1>
      <p class="text-gray-600 max-w-2xl mx-auto text-lg">
        Stay informed with the latest news and updates from our platform. Browse through recent stories, 
        explore detailed articles, and stay ahead with upcoming news announcements.
      </p>
    </div>
    
    <!-- Top summary stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
      <!-- Total News Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
        <div class="p-5">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total News</h3>
              <p class="text-4xl font-bold text-purple-600 mt-1">{{ totalNews }}</p>
              <p class="text-sm text-gray-600 mt-1">News Articles</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
            </div>
          </div>
          <div class="flex justify-between items-center mt-4">
            <p class="text-xs text-gray-500">Total News Articles</p>
            <button class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Total Categories Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
        <div class="p-5">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Categories</h3>
              <p class="text-4xl font-bold text-green-600 mt-1">{{ totalCategories }}</p>
              <p class="text-sm text-gray-600 mt-1">News Categories</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
            </div>
          </div>
          <div class="flex justify-between items-center mt-4">
            <p class="text-xs text-gray-500">Available Categories</p>
            <button class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Latest Post Card -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
        <div class="p-5">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Latest Post</h3>
              <p class="text-4xl font-bold text-blue-600 mt-1 truncate max-w-full px-2">{{ latestPost?.caption || "No posts" }}</p>
              <p class="text-sm text-gray-600 mt-1">Most Recent</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="flex justify-between items-center mt-4">
            <p class="text-xs text-gray-500">Latest Post</p>
            <button class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- News Feed -->
      <div class="lg:col-span-3 bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4">
          <h2 class="font-bold text-xl text-white flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            News Feed
          </h2>
        </div>
        
        <div class="p-4">
          <div v-if="news.length === 0" class="text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500">No news available at the moment</p>
          </div>
          
          <div v-else>
            <div v-for="n in news" :key="n.id" class="flex items-start p-4 border-b last:border-0 hover:bg-gradient-to-r hover:from-purple-50 hover:to-white rounded-lg transition-all duration-300 mb-4 shadow-sm hover:shadow-md">
              <div class="flex-shrink-0">
                <div class="relative overflow-hidden rounded-xl shadow-md w-24 h-24">
                  <img 
                    v-if="isImage(n.media_urls?.[0])" 
                    :src="n.media_urls[0]" 
                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                    alt="News image" 
                    @error="handleImageError"
                  />
                  <div v-else class="w-full h-full bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
              </div>
              
              <div class="flex-1 ml-4">
                <p class="font-bold text-gray-800 text-lg">{{ n.caption }}</p>
                <div class="flex items-center mt-1 text-sm text-gray-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ new Date(n.created_at).toLocaleString() }}
                </div>
                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ n.description || 'No description available' }}</p>
              </div>
              
              <div class="flex flex-col items-end">
                <button class="px-4 py-2 text-sm bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center" @click="openMedia(n.media_urls)">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  View Media
                </button>
                <div class="mt-2 flex space-x-1">
                  <span v-if="n.media_urls?.length" class="inline-flex items-center text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ n.media_urls.length }} media
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- News Timeline -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4">
          <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-white flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Appointments
            </h2>
          </div>
        </div>
        
        <div class="p-4">
          <div class="flex space-x-2 mb-4 bg-gray-100 p-1 rounded-lg">
            <button 
              class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300"
              :class="tab==='latest' ? 'bg-white shadow text-purple-600' : 'text-gray-600 hover:text-purple-500'"
              @click="tab='latest'"
            >
              Latest
            </button>
            <button 
              class="flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-300"
              :class="tab==='lastNews' ? 'bg-white shadow text-purple-600' : 'text-gray-600 hover:text-purple-500'"
              @click="tab='lastNews'"
            >
              Last News
            </button>
          </div>
          
          <div v-if="tab==='latest'">
            <div v-if="latestNews.length === 0" class="text-center py-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-gray-500 text-sm">No latest news</p>
            </div>
            
            <div v-else>
              <div v-for="n in latestNews" :key="n.id" class="flex items-start p-3 border-b last:border-0 mb-3 hover:bg-gradient-to-r hover:from-purple-50 hover:to-white rounded-lg transition-all duration-300 shadow-sm">
                <div class="flex-shrink-0">
                  <div class="relative overflow-hidden rounded-lg shadow-sm w-16 h-16">
                    <img 
                      v-if="isImage(n.media_urls?.[0])" 
                      :src="n.media_urls[0]" 
                      class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                      alt="News image" 
                      @error="handleImageError"
                    />
                    <div v-else class="w-full h-full bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div class="flex-1 ml-3">
                  <p class="font-bold text-gray-800 line-clamp-1">{{ n.caption }}</p>
                  <div class="flex items-center mt-1 text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ new Date(n.created_at).toLocaleString() }}
                  </div>
                  <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ n.description || 'No description available' }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else>
            <div v-if="lastNews.length === 0" class="text-center py-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-gray-500 text-sm">No previous news</p>
            </div>
            
            <div v-else>
              <div v-for="n in lastNews" :key="n.id" class="flex items-start p-3 border-b last:border-0 mb-3 hover:bg-gradient-to-r hover:from-purple-50 hover:to-white rounded-lg transition-all duration-300 shadow-sm">
                <div class="flex-shrink-0">
                  <div class="relative overflow-hidden rounded-lg shadow-sm w-16 h-16">
                    <img 
                      v-if="isImage(n.media_urls?.[0])" 
                      :src="n.media_urls[0]" 
                      class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" 
                      alt="News image" 
                      @error="handleImageError"
                    />
                    <div v-else class="w-full h-full bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div class="flex-1 ml-3">
                  <p class="font-bold text-gray-800 line-clamp-1">{{ n.caption }}</p>
                  <div class="flex items-center mt-1 text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ new Date(n.created_at).toLocaleString() }}
                  </div>
                  <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ n.description || 'No description available' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 px-4">
        <div class="bg-white p-6 rounded-xl max-w-4xl w-full relative overflow-y-auto max-h-[90vh] shadow-2xl transform transition-all duration-300 scale-95 animate-scale-in">
          <button 
            class="absolute top-3 right-3 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-black rounded-full p-2 transition-all duration-300"
            @click="closeModal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          
          <h3 class="text-xl font-bold text-gray-800 mb-4">Media Preview</h3>
          
          <div v-if="currentMedia.length" class="space-y-6">
            <div v-for="(url, i) in currentMedia" :key="i" class="w-full">
              <div class="bg-gray-50 rounded-lg p-2 shadow-inner">
                <img 
                  v-if="isImage(url)" 
                  :src="url" 
                  class="w-full max-h-[70vh] object-contain rounded-lg shadow-lg" 
                  alt="Media image" 
                  @error="handleImageError"
                />
                <video v-else controls class="w-full max-h-[70vh] rounded-lg shadow-lg">
                  <source :src="url" />
                </video>
              </div>
              <div class="mt-2 text-center">
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                  {{ isImage(url) ? 'Image' : 'Video' }} {{ i + 1 }} of {{ currentMedia.length }}
                </span>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-500">No media available</p>
          </div>
        </div>
      </div>
    </transition>
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
  return url && url.match(/\.(jpeg|jpg|gif|png|webp|svg)$/i)
}
function handleImageError(e) {
  e.target.style.display = 'none' // Hide broken images
}
const latestNews = ref([])
const lastNews = ref([])
async function load() {
  try {
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
  } catch (error) {
    console.error('Error loading news:', error)
  }
}
onMounted(load)
</script>

<style scoped>
/* Custom animations */
@keyframes scale-in {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
.animate-scale-in {
  animation: scale-in 0.3s ease-out forwards;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb {
  background: #c5c5c5;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
/* Line clamp utility */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>