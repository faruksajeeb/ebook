<template>
    <div>
      <h2>user Details</h2>
      <hr/>
      <router-link to="/users" class="btn my-btn-primary float-right"> Manage User </router-link>
      <div v-if="user">
        <h3>User Name: {{ user.name }}</h3>
        
        <h4>Permissions:</h4>
        <ul>
          <li v-for="permission in user.permissions" :key="permission.id">
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
        user: null,
      };
    },
    created() {
      // Fetch user data, including permissions, from your API
      this.fetchuserData();
    },
    methods: {
      async fetchuserData() {
        const response = await axios.get(`/api/users/${this.$route.params.id}`);
        // alert(response.data);
        this.user = response.data;

      },
    },
  };
  </script>
  