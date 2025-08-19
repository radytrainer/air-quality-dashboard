<template>
  <div class="flex flex-col md:flex-row min-h-screen bg-gray-50 rounded-2xl">
    <!-- Sidebar -->
    <div class="w-full md:w-64 bg-white shadow-md p-4 rounded-lg">
      <div class="flex flex-col items-center py-6 border-b">
        <div class="relative mb-4">
          <img
            :src="form.profile_image_url || '/default-avatar.png'"
            class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg"
            :alt="t('profile.profileImageAlt')"
          />
          <button
            type="button"
            @click="openFileInput"
            class="absolute bottom-2 right-2 bg-orange-500 text-white p-2 rounded-full hover:bg-orange-600"
            :title="t('profile.changeProfileImage')"
          >
            <i class="fas fa-camera text-sm"></i>
          </button>
          <input
            type="file"
            ref="fileInput"
            class="hidden"
            accept="image/*"
            @change="handleImageUpload"
          />
        </div>
        <h2 class="text-lg font-semibold text-gray-800">{{ form.name }}</h2>
        <p class="text-sm text-gray-500">{{ form.email }}</p>
      </div>

      <nav class="mt-4">
        <button
          @click="activeTab = 'profile'"
          :class="
            activeTab === 'profile'
              ? 'bg-orange-100 text-orange-600 font-semibold shadow-sm'
              : ''
          "
          class="w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors"
        >
          <i class="fas fa-user-circle mr-2 text-orange-500"></i> Profile
          Information
        </button>
        <button
          @click="activeTab = 'password'"
          :class="
            activeTab === 'password'
              ? 'bg-orange-100 text-orange-600 font-semibold shadow-sm'
              : ''
          "
          class="w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors"
        >
          <i class="fas fa-lock mr-2 text-orange-500"></i> Reset Password
        </button>
        <button
          @click="activeTab = 'favourites'"
          :class="
            activeTab === 'favourites'
              ? 'bg-orange-100 text-orange-600 font-semibold shadow-sm'
              : ''
          "
          class="w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors"
        >
          <i class="fas fa-star mr-2 text-orange-500"></i> Favourite Cities
        </button>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">
          {{
            activeTab === "profile" ? "Profile Information" : "Reset Password"
          }}
        </h1>

        <div v-if="loading" class="text-center py-8">
          <div
            class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-orange-500 mx-auto"
          ></div>
          <p class="mt-2 text-sm text-gray-500">
            {{ t("profile.loadingProfile") }}
          </p>
        </div>

        <div v-else>
          <div
            v-if="error"
            class="bg-red-50 text-red-600 text-sm p-3 rounded mb-4"
          >
            <ul v-if="error.errors">
              <li v-for="(errs, field) in error.errors" :key="field">
                {{ field }}: {{ errs.join(", ") }}
              </li>
            </ul>
            <p v-else>{{ error }}</p>
          </div>

          <!-- Profile Information Form -->
          <form
            v-if="activeTab === 'profile'"
            @submit.prevent="updateProfile"
            class="space-y-4 bg-white p-6 rounded-lg shadow"
          >
            <!-- Name -->
            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.name")
              }}</label>
              <input
                type="text"
                v-model="form.name"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                required
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.email")
              }}</label>
              <input
                type="email"
                v-model="form.email"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                required
              />
            </div>

            <!-- Phone -->
            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.phone")
              }}</label>
              <input
                type="text"
                v-model="form.phone"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
              />
            </div>

            <!-- Bio -->
            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.bio")
              }}</label>
              <textarea
                v-model="form.bio"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
              ></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="resetForm"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                {{ t("profile.cancel") }}
              </button>
              <button
                type="submit"
                :disabled="updating"
                class="px-5 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 disabled:bg-orange-400"
              >
                <span v-if="updating"
                  ><i class="fas fa-spinner fa-spin mr-1"></i>
                  {{ t("profile.saving") }}</span
                >
                <span v-else>{{ t("Save Changes") }}</span>
              </button>
            </div>
          </form>

          <!-- Password Reset Form -->
          <form
            v-if="activeTab === 'password'"
            @submit.prevent="updatePassword"
            class="space-y-4 bg-white p-6 rounded-lg shadow"
          >
            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.currentPassword")
              }}</label>
              <input
                type="password"
                v-model="passwordForm.current_password"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                required
              />
            </div>

            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.newPassword")
              }}</label>
              <input
                type="password"
                v-model="passwordForm.new_password"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                required
              />
            </div>

            <div>
              <label class="block mb-2 font-medium text-gray-700">{{
                t("profile.confirmPassword")
              }}</label>
              <input
                type="password"
                v-model="passwordForm.new_password_confirmation"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                required
              />
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="resetPasswordForm"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                {{ t("profile.cancel") }}
              </button>
              <button
                type="submit"
                :disabled="updatingPassword"
                class="px-5 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 disabled:bg-orange-400"
              >
                <span v-if="updatingPassword"
                  ><i class="fas fa-spinner fa-spin mr-1"></i>
                  {{ t("profile.updating") }}</span
                >
                <span v-else>{{ t("Update Password") }}</span>
              </button>
            </div>
          </form>
          <form>
            <!-- Favourite Cities Tab -->
            <div
              v-if="activeTab === 'favourites'"
              class="space-y-4 bg-white p-6 rounded-lg shadow"
            >
              <div v-if="favourites.length === 0" class="text-gray-500 text-sm">
                No favourite cities yet.
              </div>

              <ul>
                <li
                  v-for="city in favourites"
                  :key="city.id"
                  class="flex items-center justify-between p-2 border-b hover:bg-gray-50 cursor-pointer"
                  @click="goToCityOnMap(city)"
                >
                  <div class="flex items-center gap-2">
                    <img
                      :src="city.flag_url"
                      alt="flag"
                      class="w-6 h-4 object-cover rounded-sm"
                    />
                    <span class="font-medium text-gray-800">{{
                      city.name
                    }}</span>
                  </div>
                  <i class="fas fa-arrow-right text-gray-400"></i>
                </li>
              </ul>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import api from "@/services/api";
import { useAuthStore } from "@/stores/airQuality";
import { useRouter } from "vue-router";
const { t } = useI18n();

const auth = useAuthStore();
const loading = ref(true);
const updating = ref(false);
const updatingPassword = ref(false);
const error = ref(null);
const fileInput = ref(null);
const activeTab = ref("profile");
const favourites = ref([]);
const router = useRouter();

const form = ref({
  name: "",
  email: "",
  phone: "",
  bio: "",
  profile_image: null,
  profile_image_url: "",
});

const passwordForm = ref({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

const fetchProfile = async () => {
  loading.value = true;
  error.value = null;

  try {
    const res = await api.get("/profile");
    form.value.name = res.data.name;
    form.value.email = res.data.email;
    form.value.phone = res.data.phone || "";
    form.value.bio = res.data.bio || "";
    form.value.profile_image_url = res.data.profile_image;
  } catch (err) {
    error.value = err.response?.data || { message: t("profile.fetchFailed") };
  } finally {
    loading.value = false;
  }
};

const openFileInput = () => fileInput.value.click();

const handleImageUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.profile_image = file;
    form.value.profile_image_url = URL.createObjectURL(file);
  }
};

const updateProfile = async () => {
  updating.value = true;
  error.value = null;

  const formData = new FormData();
  formData.append("name", form.value.name);
  formData.append("email", form.value.email);
  formData.append("phone", form.value.phone);
  formData.append("bio", form.value.bio);

  if (form.value.profile_image) {
    formData.append("profile_image", form.value.profile_image);
  }

  try {
    const res = await api.post("/profile/update", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    auth.userName = res.data.user.name;
    auth.userEmail = res.data.user.email;
    auth.userProfileImage = res.data.user.profile_image || "";

    alert(t("profile.updateSuccess"));
  } catch (err) {
    error.value = err.response?.data?.errors
      ? err.response.data
      : { message: err.response?.data?.message || t("profile.updateFailed") };
  } finally {
    updating.value = false;
  }
};

const updatePassword = async () => {
  updatingPassword.value = true;
  error.value = null;

  try {
    await api.post("/profile/password", passwordForm.value);
    alert(t("profile.passwordUpdateSuccess"));
    resetPasswordForm();
  } catch (err) {
    error.value = err.response?.data || { message: t("profile.updateFailed") };
  } finally {
    updatingPassword.value = false;
  }
};

const resetForm = () => {
  fetchProfile();
};

const resetPasswordForm = () => {
  passwordForm.value = {
    current_password: "",
    new_password: "",
    new_password_confirmation: "",
  };
};

const fetchFavourites = async () => {
  try {
    const res = await api.get("/favourites"); // Adjust endpoint if needed
    favourites.value = res.data.map((city) => ({
      id: city.id,
      name: city.city_name,
      flag_url: `https://flagcdn.com/w40/${city.country_code.toLowerCase()}.png`,
    }));
  } catch (err) {
    console.error("Failed to fetch favourites", err);
  }
};
const goToCityOnMap = (city) => {
  router.push({ name: "home", params: { cityId: city.id } });
};


onMounted(() => {
  fetchProfile();
  fetchFavourites();
});
</script>

<style scoped>
input[type="file"] {
  display: none;
}
</style>
