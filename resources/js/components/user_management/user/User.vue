<template>
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h2>User Details</h2>
          <router-link to="/users" class="btn my-btn-primary float-right">
            Manage User
          </router-link>
        </div>
        <div class="card-body">
          <div v-if="user">
            <h5>User Name: {{ user.name }}</h5>
            <p>User Email: {{ user.email }}</p>
            <h5>Roles:</h5>
            <ul>
              <li v-for="role in user.roles" :key="role.id">
                {{ role.name }}
              </li>
            </ul>
          </div>

          <div v-else>
            <p>Loading...</p>
          </div>
        </div>
      </div>
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
    if (!User.loggedIn()) {
      this.$router.push("/");
    }
    // Fetch user data, including roles, from your API
    this.fetchUserData();
  },
  methods: {
    async fetchUserData() {
      const response = await axios.get(`/api/users/${this.$route.params.id}`);
      // alert(response.data);
      this.user = response.data;
    },
  },
};
</script>
