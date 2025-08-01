<template>
  <div class="max-w-xl mx-auto mt-10 p-5 bg-white rounded-xl shadow">
    <h1 class="text-xl font-semibold mb-4 text-gray-800">Edit Profile</h1>

    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-2 text-sm text-gray-500">Loading profile...</p>
    </div>

    <div v-else>
      <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded mb-4">
        <ul v-if="error.errors">
          <li v-for="(errs, field) in error.errors" :key="field">
            {{ field }}: {{ errs.join(', ') }}
          </li>
        </ul>
        <p v-else>{{ error }}</p>
      </div>

      <form @submit.prevent="updateProfile" class="space-y-4 text-sm text-gray-700">
        <!-- Profile Image -->
        <div class="flex justify-center mb-4">
          <div class="relative">
            <img
              :src="form.profile_image_url || '/default-avatar.png'"
              class="w-24 h-24 rounded-full object-cover border shadow"
              alt="Profile"
            />
            <button
              type="button"
              @click="openFileInput"
              class="absolute bottom-0 right-0 bg-blue-600 text-white p-1 rounded-full text-xs hover:bg-blue-700"
              title="Change profile image"
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
        </div>

        <!-- Name -->
        <div>
          <label class="block mb-1">Name</label>
          <input type="text" v-model="form.name" class="w-full px-3 py-1.5 border rounded" required />
        </div>

        <!-- Email -->
        <div>
          <label class="block mb-1">Email</label>
          <input type="email" v-model="form.email" class="w-full px-3 py-1.5 border rounded" required />
        </div>

        <!-- Phone -->
        <div>
          <label class="block mb-1">Phone</label>
          <input type="text" v-model="form.phone" class="w-full px-3 py-1.5 border rounded" />
        </div>

        <!-- Bio -->
        <div>
          <label class="block mb-1">Bio</label>
          <textarea v-model="form.bio" rows="2" class="w-full px-3 py-1.5 border rounded"></textarea>
        </div>

        <!-- Password Section -->
        <div class="border-t pt-4">
          <h3 class="text-sm font-medium mb-2">Change Password</h3>

          <div>
            <label class="block mb-1">Current Password</label>
            <input type="password" v-model="form.current_password" class="w-full px-3 py-1.5 border rounded" />
          </div>

          <div class="mt-2">
            <label class="block mb-1">New Password</label>
            <input type="password" v-model="form.new_password" class="w-full px-3 py-1.5 border rounded" />
          </div>

          <div class="mt-2">
            <label class="block mb-1">Confirm Password</label>
            <input type="password" v-model="form.new_password_confirmation" class="w-full px-3 py-1.5 border rounded" />
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2 pt-4">
          <button
            type="button"
            @click="resetForm"
            class="px-3 py-1.5 text-gray-600 border border-gray-300 rounded hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="updating"
            class="px-4 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-blue-400"
          >
            <span v-if="updating"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
            <span v-else>Save</span>
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
