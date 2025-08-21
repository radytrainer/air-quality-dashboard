<template>
  <div class="min-h-screen bg-gray-50 p-6" v-click-outside="closeDropdown">
    <div class="max-w-7xl mx-auto">
      <!-- Header and Add User button -->
      <div
        class="flex justify-between items-center mb-6 bg-white p-4 rounded shadow"
      >
        <h1 class="text-2xl font-bold">User Management</h1>
        <button
          @click="openAddUser"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M12 5v14M5 12h14" />
          </svg>
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
        <button
          @click="clearFilters"
          class="bg-gray-200 px-3 py-2 rounded hover:bg-gray-300"
        >
          Clear
        </button>
      </div>
      <!-- Users Table -->
      <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                Users
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                Role
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                Created
              </th>
              <th
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 flex items-center gap-3">
                <img
                  v-if="user.profile_image"
                  :src="user.profile_image"
                  alt="profile"
                  class="w-10 h-10 rounded-full object-cover"
                />
                <div
                  v-else
                  class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg"
                >
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <div class="font-semibold text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  :class="roleBadgeClass(user.role)"
                  class="px-2 py-1 rounded text-xs font-semibold"
                >
                  {{ user.role || "user" }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <button
                  @click="toggleDropdown(user.id)"
                  class="dropdown-button text-gray-600 hover:text-gray-900 focus:outline-none"
                  aria-haspopup="true"
                  :aria-expanded="dropdownOpenId === user.id"
                >
                  <!-- vertical ellipsis icon -->
                  <svg class="w-7 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 4a2 2 0 110-4 2 2 0 010 4zm0 4a2 2 0 110-4 2 2 0 010 4z"
                    />
                  </svg>
                </button>

                <div
                  v-if="dropdownOpenId === user.id"
                  class="dropdown-menu absolute mb-10 w-32 bg-white border border-gray-200 rounded shadow-md z-50"
                >
                  <button
                    @click="
                      viewUser(user);
                      closeDropdown();
                    "
                    class="block w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-100"
                  >
                    <svg
                      class="w-4 h-4 inline-block mr-2"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      />
                    </svg>
                    View
                  </button>
                  <button
                    @click="
                      openEditUser(user);
                      closeDropdown();
                    "
                    class="block w-full text-left px-4 py-2 text-sm text-indigo-600 hover:bg-gray-100"
                  >
                    <svg
                      class="w-4 h-4 inline-block mr-2"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                    Edit
                  </button>
                  <button
                    @click="
                      deleteUser(user);
                      closeDropdown();
                    "
                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                  >
                    <svg
                      class="w-4 h-4 inline-block mr-2"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="loading" class="p-4 text-center text-gray-600">
          Loading users...
        </div>
        <div
          v-if="!loading && filteredUsers.length === 0"
          class="p-4 text-center text-gray-600"
        >
          No users found
        </div>
      </div>
    </div>
    <!--Add User Modal -->
    <div
      v-if="showAddUserModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <h2 class="text-xl font-semibold mb-4">Add New User</h2>
        <form @submit.prevent="submitAddUser" class="space-y-4 text-sm">
          <input
            v-model="addUserForm.name"
            type="text"
            placeholder="Name"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="addUserForm.email"
            type="email"
            placeholder="Email"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="addUserForm.password"
            type="password"
            placeholder="Password"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="addUserForm.password_confirmation"
            type="password"
            placeholder="Confirm Password"
            class="w-full border rounded px-3 py-2"
            required
          />
          <select
            v-model="addUserForm.role"
            class="w-full border rounded px-3 py-2"
            required
          >
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
            <option value="viewer">Viewer</option>
          </select>
          <input
            v-model="addUserForm.phone"
            type="tel"
            placeholder="Phone (optional)"
            class="w-full border rounded px-3 py-2"
          />
          <div>
            <label class="block mb-1 font-medium"
              >Profile Image (optional)</label
            >
            <input @change="onAddImageChange" type="file" accept="image/*" />
            <img
              v-if="addUserImagePreview"
              :src="addUserImagePreview"
              alt="preview"
              class="mt-2 w-24 h-24 rounded object-cover"
            />
          </div>
          <div class="flex justify-end gap-2 mt-4">
            <button
              type="button"
              @click="closeAddUser"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="addUserLoading.value"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              {{ addUserLoading.value ? "Saving..." : "Save" }}
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
    <div
      v-if="showEditUserModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <h2 class="text-xl font-semibold mb-4">Edit User</h2>
        <form @submit.prevent="submitEditUser" class="space-y-4 text-sm">
          <input
            v-model="editUserForm.name"
            type="text"
            placeholder="Name"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="editUserForm.email"
            type="email"
            placeholder="Email"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="editUserForm.password"
            type="password"
            placeholder="New Password (leave blank to keep)"
            class="w-full border rounded px-3 py-2"
          />
          <input
            v-model="editUserForm.password_confirmation"
            type="password"
            placeholder="Confirm Password"
            :required="editUserForm.password.length > 0"
            class="w-full border rounded px-3 py-2"
          />
          <select
            v-model="editUserForm.role"
            class="w-full border rounded px-3 py-2"
            required
          >
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
          </select>
          <input
            v-model="editUserForm.phone"
            type="tel"
            placeholder="Phone (optional)"
            class="w-full border rounded px-3 py-2"
          />
          <div>
            <label class="block mb-1 font-medium"
              >Profile Image (optional)</label
            >
            <input @change="onEditImageChange" type="file" accept="image/*" />
            <img
              v-if="editUserImagePreview"
              :src="editUserImagePreview"
              alt="preview"
              class="mt-2 w-24 h-24 rounded object-cover"
            />
            <img
              v-else-if="editUserForm.profile_image"
              :src="editUserForm.profile_image"
              alt="preview"
              class="mt-2 w-24 h-24 rounded object-cover"
            />
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <button
              type="button"
              @click="closeEditUser"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="editUserLoading.value"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              {{ editUserLoading.value ? "Saving..." : "Save" }}
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
    <div
      v-if="showViewUserModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <!-- Close button -->
        <button
          @click="closeViewUser"
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl font-bold"
        >
          &times;
        </button>

        <!-- User header -->
        <div class="flex gap-4 mb-4 items-center">
          <img
            v-if="viewUserData.profile_image"
            :src="viewUserData.profile_image"
            alt="profile"
            class="w-20 h-20 rounded-full object-cover"
          />
          <div
            class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-3xl"
            v-else
          >
            {{ viewUserData.name?.charAt(0).toUpperCase() }}
          </div>
          <div>
            <h3 class="text-2xl font-semibold">{{ viewUserData.name }}</h3>
          </div>
        </div>

        <!-- Gmail / Email -->
        <p><strong>Email:</strong> {{ viewUserData.email }}</p>

        <!-- Role -->
        <p class="mt-2">
          <strong>Role:</strong> {{ viewUserData.role || "user" }}
        </p>

        <!-- Phone (show only if exists) -->
        <p v-if="viewUserData.phone">
          <strong>Phone:</strong> {{ viewUserData.phone }}
        </p>

        <!-- Created at -->
        <p class="mt-2">
          <strong>Created at:</strong> {{ formatDate(viewUserData.created_at) }}
        </p>
      </div>
    </div>
    <!-- Edit User Modal -->
    <div
      v-if="showEditUserModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <!-- Close button -->
        <button
          @click="closeEditUser"
          class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-xl font-bold"
        >
          &times;
        </button>

        <h3 class="text-2xl font-semibold mb-4">Edit User</h3>

        <form @submit.prevent="submitEditUser">
          <!-- Name -->
          <div class="mb-3">
            <label class="block font-semibold">Name</label>
            <input
              v-model="editUserForm.name"
              type="text"
              class="w-full border rounded px-3 py-2"
              required
            />
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="block font-semibold">Email</label>
            <input
              v-model="editUserForm.email"
              type="email"
              class="w-full border rounded px-3 py-2"
              required
            />
          </div>

          <!-- Role -->
          <div class="mb-3">
            <label class="block font-semibold">Role</label>
            <select
              v-model="editUserForm.role"
              class="w-full border rounded px-3 py-2"
            >
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <!-- Phone -->
          <div class="mb-3">
            <label class="block font-semibold">Phone</label>
            <input
              v-model="editUserForm.phone"
              type="text"
              class="w-full border rounded px-3 py-2"
            />
          </div>

          <!-- Profile Image -->
          <div class="mb-3">
            <label class="block font-semibold">Profile Image</label>
            <input type="file" @change="onEditImageChange" />
            <div v-if="editUserImagePreview" class="mt-2">
              <img
                :src="editUserImagePreview"
                alt="Preview"
                class="w-20 h-20 rounded-full object-cover"
              />
            </div>
          </div>

          <button
            type="submit"
            :disabled="editUserLoading"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            {{ editUserLoading ? "Saving..." : "Save Changes" }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import api from "@/services/api";

const users = ref([]);
const loading = ref(false);

const searchQuery = ref("");
const roleFilter = ref("");
const statusFilter = ref("");

const showAddUserModal = ref(false);
const showEditUserModal = ref(false);
const showViewUserModal = ref(false);

const addUserForm = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "",
  phone: "",
  bio: "",
  profile_image: null,
});
const addUserImagePreview = ref(null);
const addUserErrors = ref({});
const addUserLoading = ref(false);

const editUserForm = ref({
  id: null,
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "",
  phone: "",
  bio: "",
  profile_image: null,
});
const editUserImagePreview = ref(null);
const editUserErrors = ref({});
const editUserLoading = ref(false); // Fixed: Properly defined as a ref

const viewUserData = ref({});

const dropdownOpenId = ref(null);

// Fetch users from API
const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await api.get("/users");
    users.value = res.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchUsers();
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// Click outside handler to close dropdown
const handleClickOutside = (e) => {
  if (
    !e.target.closest(".dropdown-menu") &&
    !e.target.closest(".dropdown-button")
  ) {
    closeDropdown();
  }
};

// Dropdown toggle and close functions
const toggleDropdown = (id) => {
  if (dropdownOpenId.value === id) {
    dropdownOpenId.value = null;
  } else {
    dropdownOpenId.value = id;
  }
};
const closeDropdown = () => {
  dropdownOpenId.value = null;
};

// Filtering users based on search, role, and status filters
const filteredUsers = computed(() => {
  return users.value.filter((u) => {
    const matchesSearch =
      u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      u.email.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesRole = !roleFilter.value || u.role === roleFilter.value;
    const matchesStatus =
      !statusFilter.value ||
      (statusFilter.value === "active"
        ? u.email_verified_at !== null
        : u.email_verified_at === null);
    return matchesSearch && matchesRole && matchesStatus;
  });
});

const clearFilters = () => {
  searchQuery.value = "";
  roleFilter.value = "";
  statusFilter.value = "";
};

const openAddUser = () => {
  showAddUserModal.value = true;
  addUserErrors.value = {};
  addUserForm.value = {
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "",
    phone: "",
    bio: "",
    profile_image: null,
  };
  addUserImagePreview.value = null;
};

const closeAddUser = () => {
  showAddUserModal.value = false;
};

const onAddImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    addUserForm.value.profile_image = file;
    addUserImagePreview.value = URL.createObjectURL(file);
  } else {
    addUserForm.value.profile_image = null;
    addUserImagePreview.value = null;
  }
};

const submitAddUser = async () => {
  addUserLoading.value = true;
  addUserErrors.value = {};
  try {
    const formData = new FormData();
    for (const key in addUserForm.value) {
      if (addUserForm.value[key] !== null) {
        formData.append(key, addUserForm.value[key]);
      }
    }
    await api.post("/users", formData);
    await fetchUsers();
    closeAddUser();
  } catch (e) {
    if (e.response && e.response.data.errors) {
      addUserErrors.value = e.response.data.errors;
    } else {
      alert("Failed to add user");
    }
  } finally {
    addUserLoading.value = false;
  }
};

const openEditUser = (user) => {
  showEditUserModal.value = true;
  editUserErrors.value = {};
  editUserForm.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    password: "",
    password_confirmation: "",
    role: user.role,
    phone: user.phone || "",
    bio: user.bio || "",
    profile_image: null,
  };
  editUserImagePreview.value = user.profile_image || null;
};

const closeEditUser = () => {
  showEditUserModal.value = false;
};

const onEditImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    editUserForm.value.profile_image = file;
    editUserImagePreview.value = URL.createObjectURL(file);
  } else {
    editUserForm.value.profile_image = null;
    editUserImagePreview.value = null;
  }
};

const submitEditUser = async () => {
  editUserLoading.value = true;
  editUserErrors.value = {};
  try {
    const formData = new FormData();
    for (const key in editUserForm.value) {
      if (editUserForm.value[key] !== null) {
        formData.append(key, editUserForm.value[key]);
      }
    }
    await api.put(`/users/${editUserForm.value.id}`, formData, {
      headers: { "X-HTTP-Method-Override": "PATCH" },
    });
    await fetchUsers();
    closeEditUser();
  } catch (e) {
    if (e.response && e.response.data.errors) {
      editUserErrors.value = e.response.data.errors;
    } else {
      alert("Failed to update user");
    }
  } finally {
    editUserLoading.value = false;
  }
};

const viewUser = (user) => {
  viewUserData.value = user;
  showViewUserModal.value = true;
};

const closeViewUser = () => {
  showViewUserModal.value = false;
};

const deleteUser = async (user) => {
  if (confirm(`Are you sure you want to delete user "${user.name}"?`)) {
    try {
      await api.delete(`/users/${user.id}`);
      await fetchUsers();
    } catch {
      alert("Failed to delete user");
    }
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  const d = new Date(dateStr);
  return d.toLocaleDateString() + " " + d.toLocaleTimeString();
};

const roleBadgeClass = (role) => {
  switch (role) {
    case "admin":
      return "bg-red-100 text-red-700";
    case "user":
      return "bg-blue-100 text-blue-700";
    case "viewer":
      return "bg-gray-100 text-gray-700";
    default:
      return "bg-gray-100 text-gray-700";
  }
};
</script>

<style scoped>
/* Add pointer cursor to dropdown button */
.dropdown-button {
  cursor: pointer;
}
/* You can customize dropdown-menu styles here if needed */
</style>