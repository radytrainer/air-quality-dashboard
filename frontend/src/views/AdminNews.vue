<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6">

    <!-- Buttons -->
    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
      <button
        @click="$router.push('/categories')"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Manage Categories
      </button>

      <button
        @click="showNewsForm = !showNewsForm"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
      >
        {{ showNewsForm ? "Hide Create News" : "Create News" }}
      </button>
    </div>

    <!-- Create News Form -->
    <div v-if="showNewsForm" class="bg-white shadow-md rounded-lg p-6 mt-4 transition-all duration-300">
      <h2 class="text-xl font-semibold mb-4">Create News</h2>

      <input
        v-model="caption"
        placeholder="Caption"
        class="border border-gray-300 rounded-md p-3 w-full mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
      />

      <select
        v-model="selectedCategory"
        class="border border-gray-300 rounded-md p-3 w-full mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
      >
        <option disabled value="">-- Choose Category --</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <input
        type="file"
        multiple
        @change="onSelectFiles"
        class="mb-3 text-gray-600"
        accept="image/*,video/*"
      />

      <input
        v-model="videoLink"
        type="text"
        placeholder="Video link (optional)"
        class="border border-gray-300 rounded-md p-3 w-full mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
      />

      <button
        @click="createNews"
        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
      >
        Post News
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0 items-center">
      <select v-model="filterCategory" class="border rounded-md px-3 py-2">
        <option value="">All Categories</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <input
        v-model="searchTerm"
        type="text"
        placeholder="Search by caption or index"
        class="border rounded-md px-3 py-2 w-full sm:w-64"
      />
    </div>

    <!-- News Table -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-4">
      <h2 class="text-xl font-semibold mb-4">All News</h2>

      <table v-if="paginatedNews.length" class="w-full border border-gray-300">
        <thead>
          <tr class="bg-gray-100">
            <th class="border px-4 py-2">#</th>
            <th class="border px-4 py-2">Caption</th>
            <th class="border px-4 py-2">Category</th>
            <th class="border px-4 py-2">Media</th>
            <th class="border px-4 py-2">Video Link</th>
            <th class="border px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(n, index) in paginatedNews" :key="n.id" class="hover:bg-gray-50">
            <td class="border px-4 py-2">{{ n.globalIndex }}</td>
            <td class="border px-4 py-2">{{ n.caption }}</td>
            <td class="border px-4 py-2">{{ n.category?.name ?? "—" }}</td>
            <td class="border px-4 py-2">
              <div class="flex flex-wrap gap-2">
                <template v-for="(path, i) in n.media" :key="i">
                  <video
                    v-if="path.match(/\.(mp4|webm)$/)"
                    :src="n.media_urls[i]"
                    controls
                    class="w-24 h-24 object-cover rounded-md"
                  ></video>
                  <img
                    v-else
                    :src="n.media_urls[i]"
                    class="w-24 h-24 object-cover rounded-md"
                  />
                </template>
              </div>
            </td>
            <td class="border px-4 py-2">
              <a v-if="n.video_link" :href="n.video_link" target="_blank" class="text-blue-600 underline">Watch</a>
              <span v-else>—</span>
            </td>
            <td class="border px-4 py-2 flex gap-2 justify-center">
              <button
                @click="editNews(n)"
                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded hover:bg-yellow-200 flex items-center gap-1"
              >
                <i class="bi bi-pencil"></i> Edit
              </button>
              <button
                @click="deleteNews(n.id)"
                class="bg-red-100 text-red-800 px-3 py-1 rounded hover:bg-red-200 flex items-center gap-1"
              >
                <i class="bi bi-trash"></i> Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-else class="text-gray-500 text-center py-4">No news items available.</div>

      <!-- Pagination -->
      <div class="flex justify-center mt-4 space-x-2">
        <button
          @click="prevPage"
          :disabled="currentPage === 1"
          class="px-3 py-1 border rounded-md hover:bg-gray-100 disabled:opacity-50"
        >
          Prev
        </button>
        <button
          v-for="p in totalPages"
          :key="p"
          @click="currentPage = p"
          :class="['px-3 py-1 border rounded-md', currentPage === p ? 'bg-blue-600 text-white' : 'hover:bg-gray-100']"
        >
          {{ p }}
        </button>
        <button
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 border rounded-md hover:bg-gray-100 disabled:opacity-50"
        >
          Next
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import Swal from "sweetalert2";
import api from "@/services/api";

const caption = ref("");
const selectedCategory = ref("");
const files = ref([]);
const videoLink = ref("");
const showNewsForm = ref(false);

const categories = ref([]);
const newsList = ref([]);

const filterCategory = ref("");
const searchTerm = ref("");

const currentPage = ref(1);
const perPage = ref(5);

async function fetchCategories() {
  try {
    const { data } = await api.get("/categories");
    categories.value = data;
  } catch (err) {
    console.error(err);
  }
}

async function fetchNews() {
  try {
    const { data } = await api.get("/news");
    newsList.value = data;
  } catch (err) {
    console.error(err);
  }
}

function onSelectFiles(e) {
  files.value = Array.from(e.target.files);
}

// ------------------
// Create News
// ------------------
async function createNews() {
  if (!selectedCategory.value) return Swal.fire("Error", "Please choose a category", "error");
  if (!caption.value.trim()) return Swal.fire("Error", "Caption is required", "error");

  const fd = new FormData();
  fd.append("caption", caption.value.trim());
  fd.append("category_id", selectedCategory.value);
  files.value.forEach(f => fd.append("media[]", f));
  if (videoLink.value?.trim()) fd.append("video_link", videoLink.value.trim());

  try {
    await api.post("/news", fd);
    caption.value = "";
    selectedCategory.value = "";
    files.value = [];
    videoLink.value = "";
    showNewsForm.value = false;
    await fetchNews();
    Swal.fire("Success", "News created!", "success");
  } catch (err) {
    Swal.fire("Error", "Failed to create news", "error");
  }
}

// ------------------
// Edit News
// ------------------
function editNews(news) {
  // Clone current media and prepare newFiles array
  let keepFiles = [...(news.media ?? [])];
  let newFilesLocal = [];

  Swal.fire({
    title: 'Edit News',
    width: 700,
    html: `
      <input id="swal-caption" class="swal2-input" placeholder="Caption" value="${news.caption}">
      <select id="swal-category" class="swal2-select mb-2">
        <option value="">--Choose Category--</option>
        ${categories.value
          .map(
            (c) =>
              `<option value="${c.id}" ${c.id === news.category_id ? 'selected' : ''}>${c.name}</option>`
          )
          .join('')}
      </select>
      <input type="file" id="swal-files" multiple class="swal2-file mb-2" accept="image/*,video/*">
      <input id="swal-video" class="swal2-input" placeholder="Video link (optional)" value="${news.video_link ?? ''}">
      <div id="current-media" class="flex flex-wrap gap-2 mt-2">
        ${news.media_urls
          ?.map(
            (url, i) => `
          <div class="w-24 h-24 border p-1 rounded-md relative">
            ${url.match(/\.(mp4|webm)$/)
              ? `<video src="${url}" class="w-full h-full object-cover rounded-md" controls></video>`
              : `<img src="${url}" class="w-full h-full object-cover rounded-md"/>`}
            <label class="flex items-center gap-1 text-xs mt-1">
              <input type="checkbox" class="keep-file" data-path="${news.media[i]}" checked> Keep
            </label>
          </div>`
          )
          .join('') ?? ''}
      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: "Save",
    didOpen: () => {
      const fileInput = document.getElementById('swal-files');
      fileInput.addEventListener('change', (e) => {
        newFilesLocal = Array.from(e.target.files);
      });
    },
    preConfirm: () => {
      const caption = document.getElementById('swal-caption').value;
      const category_id = document.getElementById('swal-category').value;
      const video_link = document.getElementById('swal-video').value;
      const keep = Array.from(document.querySelectorAll('.keep-file'))
        .filter(el => el.checked)
        .map(el => el.dataset.path);
      return { caption, category_id, video_link, keep };
    },
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    const { caption, category_id, video_link, keep } = result.value;
    if (!caption || !category_id) {
      return Swal.fire("Error", "Caption and category are required", "error");
    }

    try {
      const fd = new FormData();
      fd.append("_method", "PATCH");
      fd.append("caption", caption.trim());
      fd.append("category_id", category_id);
      fd.append("video_link", video_link?.trim() ?? "");
      keep.forEach(p => fd.append("keep[]", p));
      if (newFilesLocal.length) newFilesLocal.forEach(f => fd.append("media[]", f));

      await api.post(`/news/${news.id}`, fd, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      await fetchNews();
      Swal.fire("Success", "News updated!", "success");
    } catch (err) {
      console.error(err);
      Swal.fire("Error", "Failed to update news", "error");
    }
  });
}


// ------------------
// Delete News
// ------------------
async function deleteNews(id) {
  const confirm = await Swal.fire({
    title: 'Are you sure?',
    text: "This action cannot be undone!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  });
  if (confirm.isConfirmed) {
    try {
      await api.delete(`/news/${id}`);
      await fetchNews();
      Swal.fire("Deleted!", "News has been deleted.", "success");
    } catch (err) {
      Swal.fire("Error", "Failed to delete news", "error");
    }
  }
}

const filteredNews = computed(() => {
  let list = [...newsList.value];
  if (filterCategory.value) list = list.filter(n => n.category_id === filterCategory.value);
  list = list.map((n, idx) => ({ ...n, globalIndex: idx + 1 }));
  if (searchTerm.value.trim()) {
    const term = searchTerm.value.toLowerCase();
    list = list.filter(n => n.caption.toLowerCase().includes(term) || String(n.globalIndex).includes(term));
  }
  return list;
});

const totalPages = computed(() => Math.ceil(filteredNews.value.length / perPage.value));
const paginatedNews = computed(() =>
  filteredNews.value.slice((currentPage.value - 1) * perPage.value, currentPage.value * perPage.value)
);

function prevPage() { if (currentPage.value > 1) currentPage.value--; }
function nextPage() { if (currentPage.value < totalPages.value) currentPage.value++; }

onMounted(() => {
  fetchCategories();
  fetchNews();
});
</script>
