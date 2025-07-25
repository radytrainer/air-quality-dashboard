<template>
  <div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">My Profile</h1>

    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-2 text-gray-600">Loading profile...</p>
    </div>

    <div v-else>
      <div v-if="error" class="bg-red-50 text-red-600 p-4 rounded mb-6">
        <ul v-if="error.errors">
          <li v-for="(errs, field) in error.errors" :key="field">
            {{ field }}: {{ errs.join(', ') }}
          </li>
        </ul>
        <p v-else>{{ error }}</p>
      </div>

      <form @submit.prevent="updateProfile" class="space-y-6">
        <!-- Profile Image Section -->
        <div class="flex flex-col items-center space-y-4">
          <div class="relative">
            <img
              :src="form.profile_image_url || '/default-avatar.png'"
              alt="Profile Image"
              class="w-32 h-32 rounded-full object-cover border-4 border-white shadow"
            />
            <button
              type="button"
              @click="openFileInput"
              class="absolute bottom-0 right-0 bg-blue-500 text-white rounded-full p-2 hover:bg-blue-600 transition"
              title="Change profile picture"
            >
              <i class="fas fa-camera"></i>
            </button>
            <input
              type="file"
              ref="fileInput"
              class="hidden"
              accept="image/*"
              @change="handleImageUpload"
            />
          </div>
          <!-- <button
            v-if="form.profile_image_url"
            type="button"
            @click="removeProfileImage"
            class="text-sm text-red-500 hover:text-red-700"
          >
            Remove Profile Image
          </button> -->
        </div>

        <!-- Full Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
          <input
            type="text"
            v-model="form.name"
            class="w-full px-4 py-2 border rounded-lg"
            required
          />
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
          <input
            type="email"
            v-model="form.email"
            class="w-full px-4 py-2 border rounded-lg"
            required
          />
        </div>

        <!-- Phone -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
          <input
            type="text"
            v-model="form.phone"
            class="w-full px-4 py-2 border rounded-lg"
            placeholder="Optional phone number"
          />
        </div>

        <!-- Bio -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
          <textarea
            v-model="form.bio"
            rows="3"
            class="w-full px-4 py-2 border rounded-lg"
            placeholder="Tell us about yourself"
          ></textarea>
        </div>

        <!-- Change Password -->
        <div class="border-t pt-6">
          <h3 class="text-lg font-medium mb-4">Change Password</h3>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input
              type="password"
              v-model="form.current_password"
              class="w-full px-4 py-2 border rounded-lg"
              placeholder="Enter current password"
            />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input
              type="password"
              v-model="form.new_password"
              class="w-full px-4 py-2 border rounded-lg"
              placeholder="Enter new password"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input
              type="password"
              v-model="form.new_password_confirmation"
              class="w-full px-4 py-2 border rounded-lg"
              placeholder="Confirm new password"
            />
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-4 pt-4">
          <button
            type="button"
            @click="resetForm"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="updating"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-400"
          >
            <span v-if="updating">
              <i class="fas fa-spinner fa-spin mr-2"></i> Saving...
            </span>
            <span v-else>Save Changes</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/airQuality'

const auth = useAuthStore()
const loading = ref(true)
const updating = ref(false)
const error = ref(null)
const fileInput = ref(null)

const form = ref({
  name: '',
  email: '',
  phone: '',
  bio: '',
  profile_image: null,
  profile_image_url: '',
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const fetchProfile = async () => {
  loading.value = true
  error.value = null

  try {
    const res = await api.get('/profile')
    form.value.name = res.data.name
    form.value.email = res.data.email
    form.value.phone = res.data.phone || ''
    form.value.bio = res.data.bio || ''
    form.value.profile_image_url = res.data.profile_image
  } catch (err) {
    error.value = err.response?.data || { message: 'Failed to fetch profile.' }
  } finally {
    loading.value = false
  }
}

const openFileInput = () => fileInput.value.click()

const handleImageUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.profile_image = file
    form.value.profile_image_url = URL.createObjectURL(file)
  }
}

const removeProfileImage = async () => {
  try {
    await api.delete('/profile/image')
    form.value.profile_image = null
    form.value.profile_image_url = ''
    auth.userProfileImage = ''
  } catch (err) {
    error.value = err.response?.data || { message: 'Failed to remove image.' }
  }
}

const updateProfile = async () => {
  updating.value = true
  error.value = null

  const formData = new FormData()
  formData.append('name', form.value.name)
  formData.append('email', form.value.email)
  formData.append('phone', form.value.phone)
  formData.append('bio', form.value.bio)

  if (form.value.current_password) {
    formData.append('current_password', form.value.current_password)
    formData.append('new_password', form.value.new_password)
    formData.append('new_password_confirmation', form.value.new_password_confirmation)
  }

  if (form.value.profile_image) {
    formData.append('profile_image', form.value.profile_image)
  }

  try {
    const res = await api.post('/profile/update', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    auth.userName = res.data.user.name
    auth.userEmail = res.data.user.email
    auth.userProfileImage = res.data.user.profile_image || ''

    form.value.current_password = ''
    form.value.new_password = ''
    form.value.new_password_confirmation = ''

    alert('Profile updated successfully!')
  } catch (err) {
    error.value = err.response?.data || { message: 'Failed to update profile.' }
  } finally {
    updating.value = false
  }
}

const resetForm = () => {
  fetchProfile()
  form.value.current_password = ''
  form.value.new_password = ''
  form.value.new_password_confirmation = ''
}

onMounted(() => {
  fetchProfile()
})
</script>

<style scoped>
input[type="file"] {
  display: none;
}
</style>
