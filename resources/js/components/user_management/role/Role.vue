<template>
    <div>
      <h2>Role Details</h2>
      <hr/>
      <router-link to="/roles" class="btn my-btn-primary float-right"> Manage Role </router-link>
      <div v-if="role">
        <h3>Role Name: {{ role.name }}</h3>
        
        <h4>Permissions:</h4>
        <ul>
          <li v-for="permission in role.permissions" :key="permission.id">
            {{ permission.name }}
          </li>
        </ul>
      </div>
  
      <div v-else>
        <p>Loading...</p>
      </div>
    </div>
  </template>
  
  <script>

  export default {
    data() {
      return {
        role: null,
      };
    },
    created() {
      // Fetch role data, including permissions, from your API
      this.fetchRoleData();
    },
    methods: {
      async fetchRoleData() {
        const response = await axios.get(`/api/roles/${this.$route.params.id}`);
        // alert(response.data);
        this.role = response.data;

      },
    },
  };
  </script>
  