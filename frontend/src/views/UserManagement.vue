<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">

      <!-- Header and Add User button -->
      <div class="flex justify-between items-center mb-6 bg-white p-4 rounded shadow">
        <h1 class="text-2xl font-bold">User Management</h1>
        <button @click="openAddUser" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
          Add User
        </button>
      </div>

      <!-- Search & Filters -->
      <div class="flex flex-wrap gap-4 mb-6 bg-white p-4 rounded shadow">
        <input
          v-model="searchQuery"
          type="search"
          placeholder="Search name or email..."
          class="border rounded px-3 py-2 flex-grow min-w-[200px]"
        />
        <select v-model="roleFilter" class="border rounded px-3 py-2">
          <option value="">All roles</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
          <option value="viewer">Viewer</option>
        </select>
        <select v-model="statusFilter" class="border rounded px-3 py-2">
          <option value="">All statuses</option>
          <option value="active">Active (verified)</option>
          <option value="inactive">Inactive</option>
        </select>
        <button @click="clearFilters" class="bg-gray-200 px-3 py-2 rounded hover:bg-gray-300">Clear</button>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 flex items-center gap-3">
                <img v-if="user.profile_image" :src="user.profile_image" alt="profile" class="w-10 h-10 rounded-full object-cover" />
                <div v-else class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <div class="font-semibold text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span :class="roleBadgeClass(user.role)" class="px-2 py-1 rounded text-xs font-semibold">
                  {{ user.role || 'user' }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm" :class="user.email_verified_at ? 'text-green-600' : 'text-red-600'">
                {{ user.email_verified_at ? 'active' : 'inactive' }}
              </td>
              <td class="px-6 py-4 text-center">
                <span
                  :class="user.is_online ? 'text-green-600' : 'text-gray-400'"
                  title="User Active Status"
                  class="text-xl leading-none"
                >●</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4 text-right space-x-2">
                <button @click="viewUser(user)" class="text-blue-600 hover:underline">View</button>
                <button @click="editUser(user)" class="text-indigo-600 hover:underline">Edit</button>
                <button @click="deleteUser(user)" class="text-red-600 hover:underline">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="loading" class="p-4 text-center text-gray-600">Loading users...</div>
        <div v-if="!loading && filteredUsers.length === 0" class="p-4 text-center text-gray-600">No users found</div>
      </div>
    </div>

    <!-- Add User Modal -->
    <div v-if="showAddUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <h2 class="text-xl font-semibold mb-4">Add New User</h2>
        <form @submit.prevent="submitAddUser" class="space-y-4 text-sm">
          <input v-model="addUserForm.name" type="text" placeholder="Name" class="w-full border rounded px-3 py-2" required />
          <input v-model="addUserForm.email" type="email" placeholder="Email" class="w-full border rounded px-3 py-2" required />
          <input v-model="addUserForm.password" type="password" placeholder="Password" class="w-full border rounded px-3 py-2" required />
          <input v-model="addUserForm.password_confirmation" type="password" placeholder="Confirm Password" class="w-full border rounded px-3 py-2" required />
          <select v-model="addUserForm.role" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
            <option value="viewer">Viewer</option>
          </select>
          <input v-model="addUserForm.phone" type="tel" placeholder="Phone (optional)" class="w-full border rounded px-3 py-2" />
          <textarea v-model="addUserForm.bio" placeholder="Bio (optional)" rows="3" class="w-full border rounded px-3 py-2"></textarea>

          <div>
            <label class="block mb-1 font-medium">Profile Image (optional)</label>
            <input @change="onAddImageChange" type="file" accept="image/*" />
            <img v-if="addUserImagePreview" :src="addUserImagePreview" alt="preview" class="mt-2 w-24 h-24 rounded object-cover" />
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeAddUser" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" :disabled="addUserLoading" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              {{ addUserLoading ? 'Saving...' : 'Save' }}
            </button>
          </div>
          <div v-if="addUserErrors" class="text-red-600 mt-2 text-xs">
            <div v-for="(msgs, field) in addUserErrors" :key="field">
              <div v-for="msg in msgs" :key="msg">{{ msg }}</div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="showEditUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <h2 class="text-xl font-semibold mb-4">Edit User</h2>
        <form @submit.prevent="submitEditUser" class="space-y-4 text-sm">
          <input v-model="editUserForm.name" type="text" placeholder="Name" class="w-full border rounded px-3 py-2" required />
          <input v-model="editUserForm.email" type="email" placeholder="Email" class="w-full border rounded px-3 py-2" required />
          <input v-model="editUserForm.password" type="password" placeholder="New Password (leave blank to keep)" class="w-full border rounded px-3 py-2" />
          <input v-model="editUserForm.password_confirmation" type="password" placeholder="Confirm Password" :required="editUserForm.password.length > 0" class="w-full border rounded px-3 py-2" />
          <select v-model="editUserForm.role" class="w-full border rounded px-3 py-2" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
            <option value="viewer">Viewer</option>
          </select>
          <input v-model="editUserForm.phone" type="tel" placeholder="Phone (optional)" class="w-full border rounded px-3 py-2" />
          <textarea v-model="editUserForm.bio" placeholder="Bio (optional)" rows="3" class="w-full border rounded px-3 py-2"></textarea>

          <div>
            <label class="block mb-1 font-medium">Profile Image (optional)</label>
            <input @change="onEditImageChange" type="file" accept="image/*" />
            <img v-if="editUserImagePreview || editUserForm.profile_image" :src="editUserImagePreview || editUserForm.profile_image" alt="preview" class="mt-2 w-24 h-24 rounded object-cover" />
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeEditUser" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" :disabled="editUserLoading" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              {{ editUserLoading ? 'Saving...' : 'Save' }}
            </button>
          </div>
          <div v-if="editUserErrors" class="text-red-600 mt-2 text-xs">
            <div v-for="(msgs, field) in editUserErrors" :key="field">
              <div v-for="msg in msgs" :key="msg">{{ msg }}</div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- View User Modal -->
    <div v-if="showViewUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <button @click="closeViewUser" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl font-bold">&times;</button>
        <div class="flex gap-4 mb-4 items-center">
          <img
            v-if="viewUserData.profile_image"
            :src="viewUserData.profile_image"
            alt="profile"
            class="w-20 h-20 rounded-full object-cover"
          />
          <div v-else class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-3xl">
            {{ viewUserData.name?.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h3 class="text-2xl font-semibold">{{ viewUserData.name }}</h3>
            <p class="text-gray-600">{{ viewUserData.email }}</p>
            <p class="italic text-sm mt-1 text-gray-500">Role: {{ viewUserData.role || 'user' }}</p>
          </div>
        </div>

        <p><strong>Phone:</strong> {{ viewUserData.phone || '-' }}</p>
        <p class="mt-2"><strong>Bio:</strong> {{ viewUserData.bio || '-' }}</p>
        <p class="mt-2"><strong>Created at:</strong> {{ formatDate(viewUserData.created_at) }}</p>
        <p class="mt-2"><strong>Status:</strong> 
          <span :class="viewUserData.email_verified_at ? 'text-green-600' : 'text-red-600'">
            {{ viewUserData.email_verified_at ? 'active' : 'inactive' }}
          </span>
        </p>
        <p class="mt-2"><strong>Active:</strong> 
          <span :class="viewUserData.is_online ? 'text-green-600' : 'text-gray-400'">●</span>
        </p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const users = ref([])
const loading = ref(false)

const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')

const showAddUserModal = ref(false)
const showEditUserModal = ref(false)
const showViewUserModal = ref(false)

const addUserForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  phone: '',
  bio: '',
  profile_image: null,
})
const addUserImagePreview = ref(null)
const addUserErrors = ref({})
const addUserLoading = ref(false)

const editUserForm = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
  phone: '',
  bio: '',
  profile_image: null,
})
const editUserImagePreview = ref(null)
const editUserErrors = ref({})
const editUserLoading = ref(false)

const viewUserData = ref({})

const fetchUsers = async () => {
  loading.value = true
  try {
    const res = await api.get('/users')
    users.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(fetchUsers)

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const matchesSearch = u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      u.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesRole = !roleFilter.value || u.role === roleFilter.value
    const matchesStatus =
      !statusFilter.value ||
      (statusFilter.value === 'active' ? u.email_verified_at !== null : u.email_verified_at === null)
    return matchesSearch && matchesRole && matchesStatus
  })
})

const clearFilters = () => {
  searchQuery.value = ''
  roleFilter.value = ''
  statusFilter.value = ''
}

const openAddUser = () => {
  showAddUserModal.value = true
  addUserErrors.value = {}
  addUserForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    phone: '',
    bio: '',
    profile_image: null,
  }
  addUserImagePreview.value = null
}

const closeAddUser = () => {
  showAddUserModal.value = false
}

const onAddImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    addUserForm.value.profile_image = file
    addUserImagePreview.value = URL.createObjectURL(file)
  } else {
    addUserForm.value.profile_image = null
    addUserImagePreview.value = null
  }
}

const submitAddUser = async () => {
  addUserLoading.value = true
  addUserErrors.value = {}

  const formData = new FormData()
  for (const key in addUserForm.value) {
    if (addUserForm.value[key] !== null) {
      formData.append(key, addUserForm.value[key])
    }
  }

  try {
    const res = await api.post('/users', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    users.value.unshift(res.data.user)
    closeAddUser()
  } catch (err) {
    if (err.response?.status === 422) {
      addUserErrors.value = err.response.data.errors || {}
    } else {
      alert('Failed to add user.')
    }
  } finally {
    addUserLoading.value = false
  }
}

const editUser = (user) => {
  showEditUserModal.value = true
  editUserForm.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
    role: user.role || '',
    phone: user.phone || '',
    bio: user.bio || '',
    profile_image: user.profile_image || null,
  }
  editUserImagePreview.value = null
  editUserErrors.value = {}
}

const closeEditUser = () => {
  showEditUserModal.value = false
}

const onEditImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    editUserForm.value.profile_image = file
    editUserImagePreview.value = URL.createObjectURL(file)
  } else {
    editUserForm.value.profile_image = null
    editUserImagePreview.value = null
  }
}

const submitEditUser = async () => {
  editUserLoading.value = true
  editUserErrors.value = {}

  const formData = new FormData()
  for (const key in editUserForm.value) {
    if (key === 'id') continue
    if (editUserForm.value[key] !== null) {
      formData.append(key, editUserForm.value[key])
    }
  }

  try {
    const res = await api.post(`/users/${editUserForm.value.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      method: 'POST',
    })
    const idx = users.value.findIndex(u => u.id === editUserForm.value.id)
    if (idx !== -1) {
      users.value[idx] = res.data.user
    }
    closeEditUser()
  } catch (err) {
    if (err.response?.status === 422) {
      editUserErrors.value = err.response.data.errors || {}
    } else {
      alert('Failed to update user.')
    }
  } finally {
    editUserLoading.value = false
  }
}

const viewUser = (user) => {
  showViewUserModal.value = true
  viewUserData.value = user
}

const closeViewUser = () => {
  showViewUserModal.value = false
}

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete user "${user.name}"? This action cannot be undone.`)) {
    return
  }
  try {
    await api.delete(`/users/${user.id}`)
    users.value = users.value.filter(u => u.id !== user.id)
  } catch {
    alert('Failed to delete user.')
  }
}

const roleBadgeClass = (role) => {
  switch (role) {
    case 'admin': return 'bg-red-100 text-red-800'
    case 'user': return 'bg-blue-100 text-blue-800'
    case 'viewer': return 'bg-gray-100 text-gray-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString() + ' ' + d.toLocaleTimeString()
}
</script>

<style scoped>
/* minimal styling */
</style>
