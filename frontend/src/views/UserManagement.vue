<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 rounded-lg">
              <Users class="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
              <p class="text-gray-600">Manage dashboard access and user permissions</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="text-right">
              <p class="text-sm text-gray-500">Total Users</p>
              <p class="text-2xl font-bold text-blue-600">{{ users.length }}</p>
            </div>
            <button 
              @click="showAddUserModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center gap-2"
            >
              <UserPlus class="w-4 h-4" />
              Add User
            </button>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search users..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          
          <select 
            v-model="roleFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
            <option value="viewer">Viewer</option>
          </select>
          
          <select 
            v-model="statusFilter"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          
          <button 
            @click="clearFilters"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors flex items-center gap-2"
          >
            <X class="w-4 h-4" />
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Users List</h2>
            <div class="flex items-center gap-2">
              <span class="text-sm text-gray-500">{{ filteredUsers.length }} users found</span>
              <button 
                v-if="selectedUsers.length > 0"
                @click="showBulkActions = !showBulkActions"
                class="bg-orange-100 hover:bg-orange-200 text-orange-700 font-medium py-1 px-3 rounded-lg transition-colors text-sm"
              >
                {{ selectedUsers.length }} selected
              </button>
            </div>
          </div>
          
          <!-- Bulk Actions -->
          <div v-if="showBulkActions && selectedUsers.length > 0" class="mt-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
            <div class="flex items-center gap-3">
              <span class="text-sm font-medium text-orange-800">Bulk Actions:</span>
              <button 
                @click="bulkActivate"
                class="bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded transition-colors"
              >
                Activate
              </button>
              <button 
                @click="bulkDeactivate"
                class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm py-1 px-3 rounded transition-colors"
              >
                Deactivate
              </button>
              <button 
                @click="bulkDelete"
                class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-3 rounded transition-colors"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left">
                  <input 
                    type="checkbox" 
                    @change="toggleSelectAll"
                    :checked="selectedUsers.length === filteredUsers.length && filteredUsers.length > 0"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <input 
                    type="checkbox" 
                    :value="user.id"
                    v-model="selectedUsers"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getRoleBadgeClass(user.role)"
                  >
                    {{ user.role.charAt(0).toUpperCase() + user.role.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ user.status.charAt(0).toUpperCase() + user.status.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatDate(user.lastLogin) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ formatDate(user.createdAt) }}
                </td>
                <td class="px-6 py-4 text-sm font-medium">
                  <div class="flex items-center gap-2">
                    <button 
                      @click="editUser(user)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                      title="Edit User"
                    >
                      <Edit class="w-4 h-4" />
                    </button>
                    <button 
                      @click="toggleUserStatus(user)"
                      class="p-1 rounded hover:bg-yellow-50"
                      :class="user.status === 'active' ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'"
                      :title="user.status === 'active' ? 'Deactivate User' : 'Activate User'"
                    >
                      <Power class="w-4 h-4" />
                    </button>
                    <button 
                      @click="deleteUser(user)"
                      class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                      title="Delete User"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div v-if="filteredUsers.length === 0" class="text-center py-12">
            <UserX class="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <p class="text-gray-500 text-lg">No users found</p>
            <p class="text-gray-400 text-sm">Try adjusting your search or filters</p>
          </div>
        </div>
      </div>

      <!-- Add/Edit User Modal -->
      <div v-if="showAddUserModal || editingUser" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">
                {{ editingUser ? 'Edit User' : 'Add New User' }}
              </h3>
              <button 
                @click="closeModal"
                class="text-gray-400 hover:text-gray-600"
              >
                <X class="w-5 h-5" />
              </button>
            </div>
          </div>
          
          <form @submit.prevent="saveUser" class="p-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input
                  v-model="userForm.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter full name"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                  v-model="userForm.email"
                  type="email"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter email address"
                />
              </div>
              
              <div v-if="!editingUser">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input
                  v-model="userForm.password"
                  type="password"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter password"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select
                  v-model="userForm.role"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Select Role</option>
                  <option value="admin">Admin - Full access to all features</option>
                  <option value="user">User - Standard dashboard access</option>
                  <option value="viewer">Viewer - Read-only access</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                  v-model="userForm.status"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>
            
            <div class="flex items-center gap-3 mt-6">
              <button
                type="submit"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
              >
                {{ editingUser ? 'Update User' : 'Create User' }}
              </button>
              <button
                type="button"
                @click="closeModal"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Users, UserPlus, Search, X, Edit, Trash2, Power, UserX } from 'lucide-vue-next'

// Sample user data
const users = ref([
  {
    id: 1,
    name: 'John Doe',
    email: 'john.doe@example.com',
    role: 'admin',
    status: 'active',
    lastLogin: '2024-01-15T10:30:00Z',
    createdAt: '2024-01-01T09:00:00Z'
  },
  {
    id: 2,
    name: 'Jane Smith',
    email: 'jane.smith@example.com',
    role: 'user',
    status: 'active',
    lastLogin: '2024-01-14T15:45:00Z',
    createdAt: '2024-01-02T11:30:00Z'
  },
  {
    id: 3,
    name: 'Mike Johnson',
    email: 'mike.johnson@example.com',
    role: 'viewer',
    status: 'inactive',
    lastLogin: '2024-01-10T08:20:00Z',
    createdAt: '2024-01-03T14:15:00Z'
  },
  {
    id: 4,
    name: 'Sarah Wilson',
    email: 'sarah.wilson@example.com',
    role: 'user',
    status: 'active',
    lastLogin: '2024-01-15T12:00:00Z',
    createdAt: '2024-01-04T16:45:00Z'
  },
  {
    id: 5,
    name: 'David Brown',
    email: 'david.brown@example.com',
    role: 'admin',
    status: 'active',
    lastLogin: '2024-01-15T09:15:00Z',
    createdAt: '2024-01-05T10:20:00Z'
  }
])

// Filters and search
const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')

// Modal states
const showAddUserModal = ref(false)
const editingUser = ref(null)
const showBulkActions = ref(false)

// Selection
const selectedUsers = ref([])

// User form
const userForm = reactive({
  name: '',
  email: '',
  password: '',
  role: '',
  status: 'active'
})

// Computed properties
const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesRole = !roleFilter.value || user.role === roleFilter.value
    const matchesStatus = !statusFilter.value || user.status === statusFilter.value
    
    return matchesSearch && matchesRole && matchesStatus
  })
})

// Methods
const getRoleBadgeClass = (role) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    user: 'bg-blue-100 text-blue-800',
    viewer: 'bg-gray-100 text-gray-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const clearFilters = () => {
  searchQuery.value = ''
  roleFilter.value = ''
  statusFilter.value = ''
}

const toggleSelectAll = () => {
  if (selectedUsers.value.length === filteredUsers.value.length) {
    selectedUsers.value = []
  } else {
    selectedUsers.value = filteredUsers.value.map(user => user.id)
  }
}

const editUser = (user) => {
  editingUser.value = user
  userForm.name = user.name
  userForm.email = user.email
  userForm.role = user.role
  userForm.status = user.status
}

const closeModal = () => {
  showAddUserModal.value = false
  editingUser.value = null
  Object.assign(userForm, {
    name: '',
    email: '',
    password: '',
    role: '',
    status: 'active'
  })
}

const saveUser = () => {
  if (editingUser.value) {
    // Update existing user
    const index = users.value.findIndex(u => u.id === editingUser.value.id)
    if (index !== -1) {
      users.value[index] = {
        ...users.value[index],
        name: userForm.name,
        email: userForm.email,
        role: userForm.role,
        status: userForm.status
      }
    }
    alert('✅ User updated successfully!')
  } else {
    // Add new user
    const newUser = {
      id: Math.max(...users.value.map(u => u.id)) + 1,
      name: userForm.name,
      email: userForm.email,
      role: userForm.role,
      status: userForm.status,
      lastLogin: null,
      createdAt: new Date().toISOString()
    }
    users.value.push(newUser)
    alert('✅ User created successfully!')
  }
  
  closeModal()
}

const deleteUser = (user) => {
  if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
    const index = users.value.findIndex(u => u.id === user.id)
    if (index !== -1) {
      users.value.splice(index, 1)
      selectedUsers.value = selectedUsers.value.filter(id => id !== user.id)
      alert('🗑️ User deleted successfully!')
    }
  }
}

const toggleUserStatus = (user) => {
  const newStatus = user.status === 'active' ? 'inactive' : 'active'
  const action = newStatus === 'active' ? 'activated' : 'deactivated'
  
  if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} ${user.name}?`)) {
    user.status = newStatus
    alert(`✅ User ${action} successfully!`)
  }
}

const bulkActivate = () => {
  if (confirm(`Activate ${selectedUsers.value.length} selected users?`)) {
    selectedUsers.value.forEach(userId => {
      const user = users.value.find(u => u.id === userId)
      if (user) user.status = 'active'
    })
    selectedUsers.value = []
    showBulkActions.value = false
    alert('✅ Users activated successfully!')
  }
}

const bulkDeactivate = () => {
  if (confirm(`Deactivate ${selectedUsers.value.length} selected users?`)) {
    selectedUsers.value.forEach(userId => {
      const user = users.value.find(u => u.id === userId)
      if (user) user.status = 'inactive'
    })
    selectedUsers.value = []
    showBulkActions.value = false
    alert('⚠️ Users deactivated successfully!')
  }
}

const bulkDelete = () => {
  if (confirm(`Delete ${selectedUsers.value.length} selected users? This action cannot be undone.`)) {
    users.value = users.value.filter(user => !selectedUsers.value.includes(user.id))
    selectedUsers.value = []
    showBulkActions.value = false
    alert('🗑️ Users deleted successfully!')
  }
}
</script>
