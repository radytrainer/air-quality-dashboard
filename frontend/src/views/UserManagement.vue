<template>
  <div class="min-h-screen bg-gray-50 p-6" v-click-outside="closeDropdown">
    <div class="max-w-7xl mx-auto">
      <!-- Header and Add User button -->
      <div
        class="flex justify-between items-center mb-6 bg-white p-4 rounded shadow"
      >
        <h1 class="text-2xl font-bold">{{ $t("user.title") }}</h1>
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
          {{ $t("user.addUser") }}
        </button>
      </div>
      <!-- Search & Filters -->
      <div class="flex flex-wrap gap-4 mb-6 bg-white p-4 rounded shadow">
        <input
          v-model="searchQuery"
          type="search"
          :placeholder="$t('user.searchPlaceholder')"
          class="border rounded px-3 py-2 flex-grow min-w-[200px]"
        />
        <select v-model="roleFilter" class="border rounded px-3 py-2">
          <option value="">{{ $t("user.allRoles") }}</option>
          <option value="admin">{{ $t("user.admin") }}</option>
          <option value="user">{{ $t("user.user") }}</option>
        </select>
        <button
          @click="clearFilters"
          class="bg-gray-200 px-3 py-2 rounded hover:bg-gray-300"
        >
          {{ $t("user.clear") }}
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
                {{ $t("user.users") }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                {{ $t("user.role") }}
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                {{ $t("user.created") }}
              </th>
              <th
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
              >
                {{ $t("user.actions") }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in paginatedUsers" :key="user.id">
              <td class="px-6 py-4 flex items-center gap-3">
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
                    {{ $t("user.view") }}
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
                    {{ $t("user.edit") }}
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
                    {{ $t("user.delete") }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <!-- Pagination -->
<div class="flex justify-between items-center p-4 border-t bg-gray-50">
  <!-- Left side: Page info -->
  <div class="text-sm text-gray-600">
    Page {{ currentPage }} of {{ totalPages }}
  </div>

  <!-- Right side: Prev + Numbers + Next -->
  <div class="flex items-center space-x-1">
    <!-- Prev -->
    <button
      @click="goToPage(currentPage - 1)"
      :disabled="currentPage === 1"
      class="px-3 py-1 rounded-full border text-gray-600 hover:bg-gray-100 disabled:opacity-50"
    >
      «
    </button>

    <!-- Page numbers with ellipsis -->
    <template v-for="page in paginationRange" :key="page">
      <button
        v-if="page !== '...'"
        @click="goToPage(page)"
        :class="[
          'px-3 py-1 rounded-full border',
          currentPage === page
            ? 'bg-blue-600 text-white border-blue-600'
            : 'text-gray-600 hover:bg-gray-100'
        ]"
      >
        {{ page }}
      </button>
      <span v-else class="px-3 py-1 text-gray-400">...</span>
    </template>

    <!-- Next -->
    <button
      @click="goToPage(currentPage + 1)"
      :disabled="currentPage === totalPages"
      class="px-3 py-1 rounded-full border text-gray-600 hover:bg-gray-100 disabled:opacity-50"
    >
      »
    </button>
  </div>
</div>


        <div v-if="loading" class="p-4 text-center text-gray-600">
          {{ $t("user.loading") }}
        </div>
        <div
          v-if="!loading && filteredUsers.length === 0"
          class="p-4 text-center text-gray-600"
        >
          {{ $t("user.noUsers") }}
        </div>
      </div>
    </div>
    <!--Add User Modal -->
    <div
      v-if="showAddUserModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded p-6 w-full max-w-md relative">
        <h2 class="text-xl font-semibold mb-4">{{ $t("user.addTitle") }}</h2>
        <form @submit.prevent="submitAddUser" class="space-y-4 text-sm">
          <input
            v-model="addUserForm.name"
            type="text"
            :placeholder="$t('user.name')"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="addUserForm.email"
            type="email"
            :laceholder="$t('user.email')"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="addUserForm.password"
            type="password"
            :placeholder="$t('user.password')"
            class="w-full border rounded px-3 py-2"
            required
          />
          <input
            v-model="addUserForm.password_confirmation"
            type="password"
            :placeholder="$t('user.confirmPassword')"
            class="w-full border rounded px-3 py-2"
            required
          />
          <select
            v-model="addUserForm.role"
            class="w-full border rounded px-3 py-2"
            required
          >
            <option value="">{{ $t("user.selectRole") }}</option>
            <option value="admin">{{ $t("user.admin") }}</option>
            <option value="user">{{ $t("user.user") }}</option>
          </select>
          <input
            v-model="addUserForm.phone"
            type="tel"
            :placeholder="$t('user.phoneOptional')"
            class="w-full border rounded px-3 py-2"
          />
          <div>
            <label class="block mb-1 font-medium">{{
              $t("user.profileOptions")
            }}</label>
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
              {{ $t("user.cancel") }}
            </button>
            <button
              type="submit"
              :disabled="addUserLoading.value"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              {{ addUserLoading.value ? $t("user.saving") : $t("user.save") }}
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
              <option value="viewer">Viewer</option>
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

          <button
            type="submit"
            :disabled="editUserLoading"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            {{ editUserLoading ? "Saving..." : "Save Changes" }}
          </button>
        </form>

        <div v-if="editUserErrors" class="text-red-600 mt-2 text-xs">
          <div v-for="(msgs, field) in editUserErrors" :key="field">
            <div v-for="msg in msgs" :key="msg">{{ msg }}</div>
          </div>
        </div>
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
            :src="getFullImageUrl(viewUserData.profile_image)"
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
        <p>
          <strong>{{ $t("user.email") }}:</strong> {{ viewUserData.email }}
        </p>

        <!-- Role -->
        <p class="mt-2">
          <strong>{{ $t("user.role") }}:</strong>
          {{ viewUserData.role || "user" }}
        </p>

        <!-- Phone (show only if exists) -->
        <p v-if="viewUserData.phone">
          <strong>{{ $t("user.phone") }}:</strong> {{ viewUserData.phone }}
        </p>

        <!-- Created at -->
        <p class="mt-2">
          <strong> {{ $t("user.createdAt") }}:</strong>
          {{ formatDate(viewUserData.created_at) }}
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

        <h3 class="text-2xl font-semibold mb-4">{{ $t("user.editTitle") }}</h3>

        <form @submit.prevent="submitEditUser">
          <!-- Name -->
          <div class="mb-3">
            <label class="block font-semibold"> {{ $t("user.name") }}</label>
            <input
              v-model="editUserForm.name"
              type="text"
              class="w-full border rounded px-3 py-2"
              required
            />
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="block font-semibold"> {{ $t("user.email") }}</label>
            <input
              v-model="editUserForm.email"
              type="email"
              class="w-full border rounded px-3 py-2"
              required
            />
          </div>

          <!-- Role -->
          <div class="mb-3">
            <label class="block font-semibold"> {{ $t("user.role") }}</label>
            <select
              v-model="editUserForm.role"
              class="w-full border rounded px-3 py-2"
            >
              <option value="user">{{ $t("user.user") }}</option>
              <option value="admin">{{ $t("user.admin") }}</option>
            </select>
          </div>

          <!-- Phone -->
          <div class="mb-3">
            <label class="block font-semibold"> {{ $t("user.phone") }}</label>
            <input
              v-model="editUserForm.phone"
              type="text"
              class="w-full border rounded px-3 py-2"
            />
          </div>

          <!-- Profile Image -->
          <div class="mb-3">
            <label class="block font-semibold">
              {{ $t("user.profileImage") }}</label
            >
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
const currentPage = ref(1);
const itemsPerPage = 5;


const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredUsers.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage))

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

// Page range with ellipsis
const paginationRange = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  const delta = 2
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= total; i++) {
    if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
      range.push(i)
    }
  }

  for (let i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l > 2) {
        rangeWithDots.push("...")
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
})

const addUserForm = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  role: "",
  phone: "",
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
  profile_image: null,
});
const editUserImagePreview = ref(null);
const editUserErrors = ref({});
const editUserLoading = ref(false);

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

// Helper function to get full image URL

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
    profile_image: null,
  };
  editUserImagePreview.value = null;
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

    if (editUserForm.value.name)
      formData.append("name", editUserForm.value.name);
    if (editUserForm.value.email)
      formData.append("email", editUserForm.value.email);
    if (editUserForm.value.role)
      formData.append("role", editUserForm.value.role);
    if (editUserForm.value.phone)
      formData.append("phone", editUserForm.value.phone);
    if (editUserForm.value.bio) formData.append("bio", editUserForm.value.bio);

    if (editUserForm.value.password) {
      formData.append("password", editUserForm.value.password);
      formData.append(
        "password_confirmation",
        editUserForm.value.password_confirmation
      );
    }

    if (editUserForm.value.profile_image) {
      formData.append("profile_image", editUserForm.value.profile_image);
    }

    // Tell Laravel this is a PUT
    formData.append("_method", "PUT");

    // Send as POST
    await api.post(`/users/${editUserForm.value.id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    await fetchUsers();
    closeEditUser();
  } catch (e) {
    if (e.response && e.response.data.errors) {
      editUserErrors.value = e.response.data.errors;
      console.log("Validation errors:", e.response.data.errors); // <--- log errors
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