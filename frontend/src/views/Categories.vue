<template>
  <div class="p-6 max-w-7xl mx-auto">
    <!-- Back Button -->
    <button
      @click="$router.push('/admin-news')"
      class="bg-gray-600 text-white px-4 py-2 rounded-md mb-4"
    >
      Back to News
    </button>

    <!-- Category Panel -->
    <div class="bg-white shadow-md rounded-lg p-6">
      <h2 class="text-xl font-semibold mb-4">Categories</h2>

      <!-- Add Category Button -->
      <button
        @click="showForm = !showForm"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md mb-4"
      >
        {{ showForm ? "Hide Form" : "Add New Category" }}
      </button>

      <!-- Add Category Form -->
      <div v-if="showForm" class="mb-4">
        <input
          v-model="catName"
          placeholder="Category Name"
          class="border p-2 w-full mb-2"
        />
        <input
          v-model="catDesc"
          placeholder="Description"
          class="border p-2 w-full mb-2"
        />
        <button
          @click="createCategory()"
          class="bg-green-600 text-white px-4 py-2 rounded-md"
        >
          Add Category
        </button>
      </div>

      <!-- Category Table -->
      <table class="w-full border">
        <thead>
          <tr class="bg-gray-100">
            <th class="border px-4 py-2">N°</th>
            <th class="border px-4 py-2">Name</th>
            <th class="border px-4 py-2">Description</th>
            <th class="border px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(c, index) in categories" :key="c.id">
            <!-- Show index instead of real DB id -->
            <td class="border px-4 py-2">{{ index + 1 }}</td>
            <td class="border px-4 py-2">{{ c.name }}</td>
            <td class="border px-4 py-2">{{ c.description || "—" }}</td>
            <td class="border px-4 py-2 flex gap-2">
              <button
                @click="editCategory(c)"
                class="bg-yellow-500 text-white px-2 py-1 rounded-md"
              >
                Edit
              </button>
              <button
                @click="deleteCategory(c.id)"
                class="bg-red-600 text-white px-2 py-1 rounded-md"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import api from "@/services/api";

const categories = ref([]);
const catName = ref("");
const catDesc = ref("");
const showForm = ref(false);

// ------------------
// Fetch Categories
// ------------------
async function fetchCategories() {
  try {
    const { data } = await api.get("/categories");
    categories.value = data;
  } catch (err) {
    console.error(err);
  }
}

// ------------------
// Create Category
// ------------------
async function createCategory() {
  if (!catName.value.trim())
    return Swal.fire("Error", "Category name required", "error");

  try {
    await api.post("/categories/create", {
      name: catName.value.trim(),
      description: catDesc.value.trim(),
    });
    catName.value = "";
    catDesc.value = "";
    showForm.value = false;
    await fetchCategories();
    Swal.fire("Success", "Category added!", "success");
  } catch (err) {
    console.error(err);
    Swal.fire("Error", "Failed to add category", "error");
  }
}

// ------------------
// Edit Category
// ------------------
function editCategory(category) {
  Swal.fire({
    title: "Edit Category",
    html: `
      <input id="swal-name" class="swal2-input" placeholder="Category Name" value="${
        category.name
      }">
      <input id="swal-desc" class="swal2-input" placeholder="Description" value="${
        category.description ?? ""
      }">
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: "Save",
    preConfirm: () => {
      const name = document.getElementById("swal-name").value;
      const description = document.getElementById("swal-desc").value;
      return { name, description };
    },
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    const { name, description } = result.value;
    if (!name.trim())
      return Swal.fire("Error", "Category name required", "error");

    try {
      await api.put(`/categories/${category.id}/update`, {
        name: name.trim(),
        description: description.trim(),
      });
      await fetchCategories();
      Swal.fire("Success", "Category updated!", "success");
    } catch (err) {
      console.error(err);
      Swal.fire("Error", "Failed to update category", "error");
    }
  });
}

// ------------------
// Delete Category
// ------------------
async function deleteCategory(id) {
  const confirm = await Swal.fire({
    title: "Are you sure?",
    text: "This action cannot be undone!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  });

  if (confirm.isConfirmed) {
    try {
      await api.delete(`/categories/${id}/delete`);
      await fetchCategories();
      Swal.fire("Deleted!", "Category has been deleted.", "success");
    } catch (err) {
      console.error(err);
      Swal.fire("Error", "Failed to delete category", "error");
    }
  }
}

onMounted(fetchCategories);
</script>
