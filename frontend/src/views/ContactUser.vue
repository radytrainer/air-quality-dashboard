<template>
  <div class="admin-contact-page">
    <h1>User Contact Messages</h1>

    <table class="contacts-table">
      <thead>
        <tr>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Purpose</th>
          <th>Organisation</th>
          <th>Message</th>
          <th>Received At</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="contact in contacts.data" :key="contact.id">
          <td>{{ contact.full_name }}</td>
          <td>{{ contact.email }}</td>
          <td>{{ contact.phone_number }}</td>
          <td>{{ contact.purpose_of_contact }}</td>
          <td>{{ contact.organisation }}</td>
          <td>{{ contact.message }}</td>
          <td>{{ new Date(contact.created_at).toLocaleString() }}</td>
        </tr>
      </tbody>
    </table>

    <div class="pagination-controls" v-if="contacts.last_page > 1">
      <button @click="fetchContacts(contacts.current_page - 1)" :disabled="contacts.current_page === 1">
        Previous
      </button>
      <span>Page {{ contacts.current_page }} / {{ contacts.last_page }}</span>
      <button @click="fetchContacts(contacts.current_page + 1)" :disabled="contacts.current_page === contacts.last_page">
        Next
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminContactPage',
  data() {
    return {
      contacts: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
    };
  },
  created() {
    this.fetchContacts();
  },
  methods: {
    async fetchContacts(page = 1) {
      try {
        const res = await axios.get(`http://localhost:8000/api/admin/contacts?page=${page}`);
        this.contacts = res.data;
      } catch (error) {
        alert('Failed to load contacts');
        console.error(error);
      }
    },
  },
};
</script>

<style scoped>
.admin-contact-page {
  padding: 20px;
  max-width: 1000px;
  margin: auto;
  font-family: Arial, sans-serif;
}

.contacts-table {
  width: 100%;
  border-collapse: collapse;
}

.contacts-table th, .contacts-table td {
  border: 1px solid #ddd;
  padding: 8px;
}

.contacts-table th {
  background-color: #4f46e5;
  color: white;
  text-align: left;
}

.pagination-controls {
  margin-top: 15px;
  text-align: center;
}

.pagination-controls button {
  padding: 6px 12px;
  margin: 0 5px;
  cursor: pointer;
}
</style>
