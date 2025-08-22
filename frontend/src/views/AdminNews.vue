<template>
  <div class="p-6 space-y-6">
    <!-- Create -->
    <div class="border rounded p-4">
      <h2 class="font-semibold mb-3">Create News</h2>
      <input v-model="caption" placeholder="Caption" class="border rounded p-2 w-full mb-3" />
      <input type="file" multiple @change="onSelectFiles" class="mb-3" accept="image/*,video/*" />
      <button @click="createNews" class="bg-blue-600 text-white px-4 py-2 rounded">Post</button>
    </div>

    <!-- List -->
    <div class="border rounded p-4">
      <h2 class="font-semibold mb-3">All News</h2>
      <div v-for="n in newsList" :key="n.id" class="border rounded p-3 mb-4">
        <div class="font-medium mb-2">
          <input v-model="n.caption" class="border rounded p-1 w-full" />
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
          <div v-for="(url, i) in n.media_urls" :key="i" class="border rounded p-2">
            <template v-if="url.match(/\.(mp4|webm)$/)">
              <video :src="url" controls class="w-full h-32 object-cover"></video>
            </template>
            <template v-else>
              <img :src="url" class="w-full h-32 object-cover" />
            </template>
            <!-- Show checkbox to keep/remove (needs raw path).
                 Ensure your API also returns n.media (raw paths). -->
            <div v-if="editing && editing.id === n.id" class="mt-2">
              <label class="text-sm">
                <input type="checkbox"
                       :checked="keep.includes(n.media[i])"
                       @change="toggleKeep(n.media[i])" />
                keep this file
              </label>
            </div>
          </div>
        </div>

        <div v-if="editing && editing.id === n.id" class="mb-3">
          <input type="file" multiple @change="onSelectNewFiles" accept="image/*,video/*" />
        </div>

        <div class="space-x-2">
          <button v-if="!editing || editing.id !== n.id"
                  @click="startEdit(n)" class="px-3 py-1 rounded bg-yellow-500 text-white">
            Edit
          </button>
          <button v-else @click="saveEdit" class="px-3 py-1 rounded bg-green-600 text-white">
            Save
          </button>
          <button v-if="editing && editing.id === n.id"
                  @click="editing=null" class="px-3 py-1 rounded bg-gray-500 text-white">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const caption = ref('')
const files = ref([])
const newsList = ref([])
const editing = ref(null) // the news being edited
const keep = ref([])      // paths to keep during edit
const newFiles = ref([])

async function fetchNews() {
  const { data } = await api.get('/news')
  newsList.value = data
}

function onSelectFiles(e) {
  files.value = Array.from(e.target.files)
}

async function createNews() {
  const fd = new FormData()
  fd.append('caption', caption.value)
  files.value.forEach(f => fd.append('media[]', f))
  await api.post('/news', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
  caption.value = ''
  files.value = []
  await fetchNews()
}

function startEdit(item) {
  editing.value = item
  keep.value = [...(item.media ?? [])] // use raw paths if you expose them, else map back
  newFiles.value = []
}

// For simplicity, send the stored `media` paths (not full URLs) as KEEP.
// If your API returns only `media_urls`, also return `media` to know original paths.
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
  const fd = new FormData()
  if (editing.value.caption) fd.append('caption', editing.value.caption)
  keep.value.forEach(p => fd.append('keep[]', p))
  newFiles.value.forEach(f => fd.append('media[]', f))
  await api.patch(`/news/${editing.value.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } })
  editing.value = null
  await fetchNews()
}

onMounted(fetchNews)
</script>
