<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
      <!-- Header -->
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">
          Latest News Updates
        </h1>
        <p class="text-slate-600 text-sm">Stay informed with the latest news and updates from our platform</p>
      </div>
      
      <!-- Top 4 Metric Boxes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total News -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Total News</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ totalNews }}</p>
              <p class="text-sm text-emerald-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  Active Articles
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
              </div>
              <button @click="exportData('total')" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Total Categories -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Categories</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ totalCategories }}</p>
              <p class="text-sm text-emerald-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  Available Topics
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
              </div>
              <button @click="exportData('categories')" class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Latest Post -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Latest Post</p>
              </div>
              <p class="text-lg font-bold text-slate-900 mb-2 truncate">{{ latestPost?.caption || "No posts" }}</p>
              <p class="text-sm text-indigo-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                  </svg>
                  {{ formatDate(latestPost?.created_at) }}
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <button @click="exportData('latest')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
        
        <!-- Total Media -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center space-x-1 mb-1">
                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Total Media</p>
              </div>
              <p class="text-3xl font-bold text-slate-900 mb-2">{{ totalMedia }}</p>
              <p class="text-sm text-purple-600 font-medium">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                  </svg>
                  Images & Videos
                </span>
              </p>
            </div>
            <div class="flex flex-col items-end space-y-2">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <button @click="exportData('media')" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- News Feed -->
        <div class="lg:col-span-2 bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-8">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-xl font-bold text-slate-900 mb-1">News Feed</h3>
              <p class="text-slate-600 text-sm">Latest articles and updates</p>
            </div>
            <div class="flex items-center space-x-4">
              <select v-model="selectedCategory" @change="filterNews" class="bg-white/80 border border-slate-200 rounded-lg px-4 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
              </select>
              <div class="flex items-center space-x-3 text-xs">
                <div class="flex items-center space-x-1">
                  <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                  <span class="text-slate-600 font-medium">Total: {{ filteredNews.length }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
          </div>
          
          <div v-else-if="filteredNews.length === 0" class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500">No news articles available</p>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="article in filteredNews" :key="article.id" class="bg-gradient-to-r from-slate-50 to-white rounded-xl p-4 border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex flex-row space-x-4">
                <!-- Media Preview Column -->
                <div v-if="article.media_urls && article.media_urls.length > 0" class="flex-shrink-0">
                  <div class="grid grid-cols-2 gap-1">
                    <div
                      v-for="(url, index) in article.media_urls.slice(0, 4)"
                      :key="index"
                      class="w-16 h-16 rounded-md overflow-hidden border border-gray-200"
                      @click="openMediaAt(article.media_urls, index)"
                    >
                      <template v-if="isImage(url)">
                        <img
                          :src="url"
                          class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity"
                          alt="Media preview"
                          loading="lazy"
                          @error="(e) => handleImageError(e, article)"
                        />
                      </template>
                      <template v-else>
                        <div class="w-full h-full bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center cursor-pointer hover:opacity-90 transition-opacity">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                          </svg>
                        </div>
                      </template>
                    </div>
                    
                    <!-- Show more indicator if there are more than 4 media items -->
                    <div
                      v-if="article.media_urls.length > 4"
                      class="w-16 h-16 rounded-md bg-gray-100 flex items-center justify-center cursor-pointer hover:bg-gray-200 transition-colors"
                      @click="openMedia(article.media_urls)"
                    >
                      <span class="text-xs font-medium text-gray-600">+{{ article.media_urls.length - 4 }}</span>
                    </div>
                  </div>
                </div>
                
                <!-- Content Column -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center mb-2">
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">
                      {{ article.category?.name || 'Uncategorized' }}
                    </span>
                    <h3 class="font-bold text-slate-900 text-lg truncate">{{ article.caption }}</h3>
                  </div>
                  <div class="flex items-center text-sm text-gray-500 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ formatDate(article.created_at) }}
                  </div>
                  <p class="text-sm text-gray-600 line-clamp-2">
                    {{ article.description || 'No description available' }}
                  </p>
                  
                  <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center text-xs text-gray-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      {{ article.media_urls.length }} media files
                    </div>
                    <button @click="openMedia(article.media_urls)" class="px-3 py-1 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View All
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- News Timeline -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-5">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold text-slate-900 mb-1">News Timeline</h3>
              <p class="text-slate-600 text-xs">Updates every 30 seconds</p>
            </div>
            <div class="flex space-x-3">
              <select v-model="timelineCategory" @change="updateTimeline" class="bg-white/80 border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
              </select>
              <select v-model="timelineType" @change="updateTimeline" class="bg-white/80 border border-slate-200 rounded-lg px-2 py-1 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="latest">Latest</option>
                <option value="popular">Popular</option>
              </select>
            </div>
          </div>
          
          <div v-if="loading" class="flex justify-center items-center py-6">
            <div class="animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-blue-500"></div>
          </div>
          
          <div v-else class="space-y-3">
            <div v-if="timelineNews.length === 0" class="text-center py-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-gray-500 text-sm">No news available</p>
            </div>
            
            <div v-else>
              <div v-for="article in timelineNews" :key="article.id" class="bg-gradient-to-r from-slate-50 to-white rounded-lg p-3 border border-slate-100 hover:shadow-md transition-all duration-200">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <div class="relative overflow-hidden rounded-md shadow-sm w-12 h-12">
                      <div v-if="isImage(article.media_urls?.[0])" class="w-full h-full">
                        <img 
                          :src="article.media_urls[0]" 
                          class="w-full h-full object-cover" 
                          alt="News image" 
                          loading="lazy"
                          @error="(e) => handleImageError(e, article)"
                          @load="imageLoaded(article.id)"
                          :class="{ 'opacity-0': !loadedImages.includes(article.id) }"
                        />
                        <div v-if="!loadedImages.includes(article.id)" class="absolute inset-0 bg-gray-200 animate-pulse flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                      </div>
                      <div v-else class="w-full h-full bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center mb-1">
                      <span class="inline-block px-1.5 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 mr-1">
                        {{ article.category?.name || 'Uncategorized' }}
                      </span>
                      <h4 class="font-bold text-slate-800 text-xs line-clamp-1">{{ article.caption }}</h4>
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ formatDate(article.created_at) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Additional Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- News by Categories -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">News by Categories</h3>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div v-for="category in categoryStats" :key="category.name" class="bg-gradient-to-br from-white/80 to-slate-50 rounded-lg p-3 border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: category.color }"></div>
                <span class="text-xs font-medium text-slate-700">{{ category.name }}</span>
              </div>
              <div class="mt-2 text-lg font-bold text-slate-900">{{ category.count }}</div>
              <div class="w-full bg-slate-200 h-2 rounded-full mt-2">
                <div class="h-2 rounded-full transition-all duration-500" :style="{ width: `${(category.count / totalNews * 100) || 0}%`, backgroundColor: category.color }"></div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Media Statistics -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">Media Statistics</h3>
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
          </div>
          <div class="grid grid-cols-1 gap-2">
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Total Images</span>
              </div>
              <span class="text-base font-bold text-slate-900">{{ totalImages }}</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Total Videos</span>
              </div>
              <span class="text-base font-bold text-slate-900">{{ totalVideos }}</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-white/80 to-slate-50 rounded-lg border border-slate-100 hover:shadow-md transition-all duration-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
                <span class="text-xs font-medium text-slate-700">Avg Media per News</span>
              </div>
              <span class="text-base font-bold text-slate-900">{{ averageMediaPerNews }}</span>
            </div>
          </div>
        </div>
        
        <!-- Activity Metrics -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-white/20 p-4">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900">Activity Metrics</h3>
            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
          </div>
          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">News Today</span>
              </div>
              <span class="font-bold text-slate-900">{{ newsToday }}</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">News This Week</span>
              </div>
              <span class="font-bold text-slate-900">{{ newsThisWeek }}</span>
            </div>
            <div class="flex items-center justify-between p-2 bg-gradient-to-br from-slate-50 to-white rounded-lg border border-slate-100">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full"></div>
                <span class="text-slate-700 font-medium">News This Month</span>
              </div>
              <span class="font-bold text-slate-900">{{ newsThisMonth }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 px-4">
        <div class="bg-white p-4 rounded-lg max-w-3xl w-full relative overflow-y-auto max-h-[90vh] shadow-lg">
          <button 
            class="absolute top-2 right-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full p-1"
            @click="closeModal"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          
          <h3 class="text-lg font-bold text-gray-800 mb-3">Media Preview</h3>
          
          <div v-if="currentMedia.length" class="space-y-4">
            <div v-for="(url, i) in currentMedia" :key="i" class="w-full">
              <div class="bg-gray-50 rounded-lg p-2">
                <img 
                  v-if="isImage(url)" 
                  :src="url" 
                  class="w-full max-h-[60vh] object-contain rounded-lg" 
                  alt="Media image" 
                  @error="handleModalImageError"
                />
                <video v-else controls class="w-full max-h-[60vh] rounded-lg">
                  <source :src="url" />
                </video>
              </div>
              <div class="mt-1 text-center">
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                  {{ isImage(url) ? 'Image' : 'Video' }} {{ i + 1 }} of {{ currentMedia.length }}
                </span>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8">
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
import { ref, computed, onMounted, nextTick } from "vue";
import api from "@/services/api";

// Reactive data
const news = ref([]);
const categories = ref([]);
const selectedCategory = ref("");
const timelineCategory = ref("");
const timelineType = ref("latest");
const showModal = ref(false);
const currentMedia = ref([]);
const currentMediaIndex = ref(0);
const loading = ref(true);
const loadedImages = ref([]);

// Computed properties
const totalNews = computed(() => news.value.length);
const totalCategories = computed(() => categories.value.length);
const latestPost = computed(() => {
  if (news.value.length === 0) return null;
  return news.value.reduce((latest, current) => {
    return new Date(current.created_at) > new Date(latest.created_at) ? current : latest;
  });
});
const totalMedia = computed(() => {
  return news.value.reduce((total, article) => {
    return total + (article.media_urls?.length || 0);
  }, 0);
});
const totalImages = computed(() => {
  return news.value.reduce((total, article) => {
    return total + (article.media_urls?.filter(url => isImage(url)).length || 0);
  }, 0);
});
const totalVideos = computed(() => {
  return news.value.reduce((total, article) => {
    return total + (article.media_urls?.filter(url => !isImage(url)).length || 0);
  }, 0);
});
const averageMediaPerNews = computed(() => {
  if (news.value.length === 0) return 0;
  return (totalMedia.value / news.value.length).toFixed(1);
});
const newsToday = computed(() => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return news.value.filter(article => {
    const articleDate = new Date(article.created_at);
    return articleDate >= today;
  }).length;
});
const newsThisWeek = computed(() => {
  const today = new Date();
  const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
  return news.value.filter(article => {
    const articleDate = new Date(article.created_at);
    return articleDate >= weekAgo;
  }).length;
});
const newsThisMonth = computed(() => {
  const today = new Date();
  const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
  return news.value.filter(article => {
    const articleDate = new Date(article.created_at);
    return articleDate >= monthAgo;
  }).length;
});
const filteredNews = computed(() => {
  if (!selectedCategory.value) return news.value;
  return news.value.filter(article => article.category_id === selectedCategory.value);
});
const timelineNews = computed(() => {
  let filtered = news.value;
  if (timelineCategory.value) {
    filtered = filtered.filter(article => article.category_id === timelineCategory.value);
  }
  
  if (timelineType.value === "popular") {
    // Sort by media count (more media = more popular)
    return [...filtered].sort((a, b) => {
      const aMedia = a.media_urls?.length || 0;
      const bMedia = b.media_urls?.length || 0;
      return bMedia - aMedia;
    }).slice(0, 10);
  } else {
    // Sort by date (latest first)
    return [...filtered].sort((a, b) => {
      return new Date(b.created_at) - new Date(a.created_at);
    }).slice(0, 10);
  }
});
const categoryStats = computed(() => {
  const stats = {};
  const colors = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
    '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'
  ];
  
  categories.value.forEach((category, index) => {
    const count = news.value.filter(article => article.category_id === category.id).length;
    stats[category.name] = {
      count,
      color: colors[index % colors.length]
    };
  });
  
  return Object.entries(stats).map(([name, data]) => ({
    name,
    count: data.count,
    color: data.color
  }));
});

// Methods
function openMedia(urls) {
  currentMedia.value = urls || [];
  currentMediaIndex.value = 0;
  showModal.value = true;
}
function openMediaAt(urls, index) {
  currentMedia.value = urls || [];
  currentMediaIndex.value = index;
  showModal.value = true;
}
function closeModal() {
  showModal.value = false;
  currentMedia.value = [];
  currentMediaIndex.value = 0;
}
function isImage(url) {
  if (!url) return false;
  
  // Check for common image extensions
  if (url.match(/\.(jpeg|jpg|gif|png|webp|svg|bmp|ico)(\?.*)?$/i)) {
    return true;
  }
  
  // Check for image content type in URL parameters
  if (url.match(/image\/(jpeg|jpg|gif|png|webp|svg|bmp|ico)/i)) {
    return true;
  }
  
  // Check for common image patterns in URL
  if (url.match(/\/img\/|\/image\/|\/photo\/|\/media\//i)) {
    return true;
  }
  
  return false;
}
function handleImageError(e, item) {
  console.error('Image failed to load:', e.target.src);
  
  // Create a fallback element
  const fallback = document.createElement('div');
  fallback.className = 'w-full h-full bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center';
  fallback.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
  `;
  
  // Replace the broken image with the fallback
  e.target.parentNode.replaceChild(fallback, e.target);
}
function handleModalImageError(e) {
  console.error('Modal image failed to load:', e.target.src);
  e.target.style.display = 'none';
  
  // Create a fallback element
  const fallback = document.createElement('div');
  fallback.className = 'w-full h-64 bg-gradient-to-br from-purple-200 to-indigo-200 flex items-center justify-center rounded-lg';
  fallback.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    <p class="text-purple-700 mt-2">Image failed to load</p>
  `;
  
  // Replace the broken image with the fallback
  e.target.parentNode.replaceChild(fallback, e.target);
}
function imageLoaded(id) {
  if (!loadedImages.value.includes(id)) {
    loadedImages.value.push(id);
  }
}
function formatDate(dateString) {
  if (!dateString) return 'Unknown';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}
function filterNews() {
  // Trigger re-computation of filteredNews
}
function updateTimeline() {
  // Trigger re-computation of timelineNews
}
function exportData(type) {
  let data = [];
  let filename = '';
  let headers = ['id', 'caption', 'category_id', 'created_at', 'updated_at'];
  
  const escapeCsvValue = (value) => {
    if (value == null) return '';
    const str = String(value);
    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
      return `"${str.replace(/"/g, '""')}"`
    }
    return str;
  };
  
  switch(type) {
    case 'total':
      data = news.value.map(item => 
        headers.map(header => escapeCsvValue(item[header])).join(',')
      );
      data.unshift(headers.join(','));
      filename = 'total-news.csv';
      break;
    case 'categories':
      data = categories.value.map(category => 
        [escapeCsvValue(category.id), escapeCsvValue(category.name), escapeCsvValue(category.description)].join(',')
      );
      data.unshift('id,name,description');
      filename = 'categories.csv';
      break;
    case 'latest':
      if (latestPost.value) {
        data = [headers.map(header => escapeCsvValue(latestPost.value[header])).join(',')];
        data.unshift(headers.join(','));
      }
      filename = 'latest-post.csv';
      break;
    case 'media':
      const mediaData = [];
      news.value.forEach(article => {
        if (article.media_urls && article.media_urls.length > 0) {
          article.media_urls.forEach(url => {
            mediaData.push([
              escapeCsvValue(article.id),
              escapeCsvValue(article.caption),
              escapeCsvValue(url),
              escapeCsvValue(isImage(url) ? 'image' : 'video')
            ].join(','));
          });
        }
      });
      data = mediaData;
      data.unshift('id,caption,url,type');
      filename = 'media-files.csv';
      break;
  }
  
  const blob = new Blob([data.join('\n')], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

// Data fetching
async function fetchNews() {
  try {
    loading.value = true;
    const { data } = await api.get("/news");
    news.value = data;
  } catch (error) {
    console.error("Error fetching news:", error);
  } finally {
    loading.value = false;
  }
}
async function fetchCategories() {
  try {
    const { data } = await api.get("/categories");
    categories.value = data;
  } catch (error) {
    console.error("Error fetching categories:", error);
  }
}

// Lifecycle
onMounted(async () => {
  await fetchNews();
  await fetchCategories();
  
  // Auto-refresh every 30 seconds
  setInterval(async () => {
    await fetchNews();
  }, 30000);
});
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
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
img {
  transition: opacity 0.3s ease;
}
/* Custom scrollbar for media preview */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #c5c5c5;
  border-radius: 10px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>