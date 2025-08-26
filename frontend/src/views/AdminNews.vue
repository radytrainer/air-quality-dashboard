<template>
  <div class="p-6 space-y-6 max-w-7xl mx-auto">
    <!-- Toggleable Create Category Section -->
    <div class="mb-4">
      <button 
        @click="showCategoryForm = !showCategoryForm" 
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors flex items-center gap-2"
      >
        <svg v-if="!showCategoryForm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ showCategoryForm ? 'Hide Category Form' : 'Add New Category' }}
      </button>

      <div v-if="showCategoryForm" class="mt-4 bg-white shadow-md rounded-lg p-6 transition-all duration-300">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          {{ editingCategory ? 'Edit Category' : 'Create Category' }}
        </h2>
        <input 
          v-model="catName" 
          placeholder="Category name" 
          class="border border-gray-300 rounded-md p-3 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
        />
        <input 
          v-model="catDesc" 
          placeholder="Description" 
          class="border border-gray-300 rounded-md p-3 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
        />
        <div class="flex space-x-3">
          <button 
            @click="editingCategory ? saveCategoryEdit() : createCategory()" 
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
          >
            {{ editingCategory ? 'Save Category' : 'Add Category' }}
          </button>
          <button 
            v-if="editingCategory" 
            @click="cancelCategoryEdit" 
            class="px-4 py-2 rounded-md bg-gray-500 text-white hover:bg-gray-600 transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>

    <!-- Categories List Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">All Categories</h2>
      <div v-if="categories.length === 0" class="text-gray-500 text-center py-4">
        No categories available.
      </div>
      <div v-for="c in categories" :key="c.id" class="border border-gray-200 rounded-lg p-4 mb-4 hover:shadow-lg transition-shadow">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-lg font-medium text-gray-800">{{ c.name }}</h3>
            <p class="text-sm text-gray-600">{{ c.description || 'No description' }}</p>
          </div>
          <div class="flex space-x-3">
            <button 
              @click="startCategoryEdit(c)" 
              class="px-4 py-2 rounded-md bg-yellow-500 text-white hover:bg-yellow-600 transition-colors"
            >
              Edit
            </button>
            <button 
              @click="deleteCategory(c.id)" 
              class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create News Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Create News</h2>
      <input 
        v-model="caption" 
        placeholder="Caption" 
        class="border border-gray-300 rounded-md p-3 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
      />
      <select 
        v-model="selectedCategory" 
        class="border border-gray-300 rounded-md p-3 w-full mb-4 focus:outline-none focus:ring-2 focus:ring-indigo-500"
      >
        <option disabled value="">-- Choose Category --</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <input 
        type="file" 
        multiple 
        @change="onSelectFiles" 
        class="mb-4 text-gray-600" 
        accept="image/*,video/*" 
      />
      <button 
        @click="createNews" 
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
      >
        Post News
      </button>
    </div>

    <!-- News List Section -->
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">All News</h2>
      <div v-if="newsList.length === 0" class="text-gray-500 text-center py-4">
        No news items available.
      </div>
      <div v-for="n in newsList" :key="n.id" class="border border-gray-200 rounded-lg p-4 mb-4 hover:shadow-lg transition-shadow">
        <input 
          v-model="n.caption" 
          class="border border-gray-300 rounded-md p-2 w-full mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" 
          :disabled="!editing || editing.id !== n.id"
        />
        <select 
          v-if="editing && editing.id === n.id" 
          v-model="editing.category_id" 
          class="border border-gray-300 rounded-md p-2 w-full mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          <option disabled value="">-- Choose Category --</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <div class="text-sm text-gray-600 mb-3">
          Category: {{ n.category?.name ?? '—' }}
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
          <div v-for="(url, i) in n.media_urls" :key="i" class="border border-gray-200 rounded-lg p-2">
            <template v-if="url.match(/\.(mp4|webm)$/)">
              <video :src="url" controls class="w-full h-40 object-cover rounded-md"></video>
            </template>
            <template v-else>
              <img :src="url" class="w-full h-40 object-cover rounded-md" />
            </template>
            <div v-if="editing && editing.id === n.id" class="mt-2">
              <label class="text-sm text-gray-600 flex items-center gap-2">
                <input 
                  type="checkbox" 
                  :checked="keep.includes(n.media[i])" 
                  @change="toggleKeep(n.media[i])" 
                  class="h-4 w-4 text-indigo-600" 
                />
                Keep this file
              </label>
            </div>
          </div>
        </div>
        <div v-if="editing && editing.id === n.id" class="mb-4">
          <input 
            type="file" 
            multiple 
            @change="onSelectNewFiles" 
            class="text-gray-600" 
            accept="image/*,video/*" 
          />
        </div>
        <div class="flex space-x-3">
          <button 
            v-if="!editing || editing.id !== n.id" 
            @click="startEdit(n)" 
            class="px-4 py-2 rounded-md bg-yellow-500 text-white hover:bg-yellow-600 transition-colors"
          >
            Edit
          </button>
          <button 
            v-else 
            @click="saveEdit" 
            class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 transition-colors"
          >
            Save
          </button>
          <button 
            v-if="editing && editing.id === n.id" 
            @click="editing = null" 
            class="px-4 py-2 rounded-md bg-gray-500 text-white hover:bg-gray-600 transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="deleteNews(n.id)" 
            class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition-colors"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

// News form
const caption = ref('')
const files = ref([])
const selectedCategory = ref('')
const newsList = ref([])
const editing = ref(null)
const keep = ref([])
const newFiles = ref([])

// Categories
const categories = ref([])
const catName = ref('')
const catDesc = ref('')
const showCategoryForm = ref(false)
const editingCategory = ref(null)

// Fetch all news
async function fetchNews() {
  try {
    const { data } = await api.get('/news')
    newsList.value = data
  } catch (error) {
    console.error('Error fetching news:', error)
    alert('Failed to fetch news')
  }
}

// Fetch all categories
async function fetchCategories() {
  try {
    const { data } = await api.get('/categories')
    categories.value = data
  } catch (error) {
    console.error('Error fetching categories:', error)
    alert('Failed to fetch categories')
  }
}

// Create category
async function createCategory() {
  if (!catName.value.trim()) {
    alert('Category name is required')
    return
  }
  try {
    await api.post('/categories/create', {
      name: catName.value,
      description: catDesc.value
    })
    catName.value = ''
    catDesc.value = ''
    showCategoryForm.value = false
    await fetchCategories()
  } catch (error) {
    console.error('Error creating category:', error)
    alert('Failed to create category')
  }
}

// Edit category
function startCategoryEdit(category) {
  editingCategory.value = { ...category }
  catName.value = category.name
  catDesc.value = category.description || ''
  showCategoryForm.value = true
}

async function saveCategoryEdit() {
  if (!catName.value.trim()) {
    alert('Category name is required')
    return
  }
  try {
    await api.patch(`/categories/${editingCategory.value.id}`, {
      name: catName.value,
      description: catDesc.value
    })
    catName.value = ''
    catDesc.value = ''
    showCategoryForm.value = false
    editingCategory.value = null
    await fetchCategories()
  } catch (error) {
    console.error('Error updating category:', error)
    alert('Failed to update category')
  }
}

function cancelCategoryEdit() {
  catName.value = ''
  catDesc.value = ''
  showCategoryForm.value = false
  editingCategory.value = null
}

// Delete category
async function deleteCategory(id) {
  if (!confirm('Are you sure you want to delete this category?')) return
  try {
    await api.delete(`/categories/${id}`)
    await fetchCategories()
  } catch (error) {
    console.error('Error deleting category:', error)
    alert('Failed to delete category')
  }
}

// Create news
async function createNews() {
  if (!selectedCategory.value) {
    alert('Please choose a category')
    return
  }
  try {
    const fd = new FormData()
    fd.append('caption', caption.value)
    fd.append('category_id', selectedCategory.value)
    files.value.forEach(f => fd.append('media[]', f))
    await api.post('/news', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    caption.value = ''
    files.value = []
    selectedCategory.value = ''
    await fetchNews()
  } catch (error) {
    console.error('Error creating news:', error)
    alert('Failed to create news')
  }
}

// File input
function onSelectFiles(e) {
  files.value = Array.from(e.target.files)
}

// Edit news
function startEdit(item) {
  editing.value = { ...item, category_id: item.category_id || item.category?.id }
  keep.value = [...(item.media ?? [])]
  newFiles.value = []
}


function toggleKeep(path) {
  if (keep.value.includes(path)) {
    keep.value = keep.value.filter(p => p !== path)
  } else {
    keep.value.push(path)
  }
}

function onSelectNewFiles(e) {
  newFiles.value = Array.from(e.target.files)
}

async function saveEdit() {
  try {
    const fd = new FormData()
    fd.append('caption', editing.value.caption ?? '')
    fd.append('category_id', editing.value.category_id ?? '')
    keep.value.forEach(p => fd.append('keep[]', p))
    newFiles.value.forEach(f => fd.append('media[]', f))
    await api.patch(`/news/${editing.value.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    editing.value = null
    await fetchNews()
  } catch (error) {
    console.error('Error saving news:', error)
    alert('Failed to save news')
  }
}

// Delete news
async function deleteNews(id) {
  if (!confirm('Are you sure you want to delete this news item?')) return
  try {
    await api.delete(`/news/${id}`)
    await fetchNews()
  } catch (error) {
    console.error('Error deleting news:', error)
    alert('Failed to delete news')
  }
}

onMounted(() => {
  fetchNews()
  fetchCategories()
})
</script>