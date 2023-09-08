<template>
  <div>
    <h2>option Details</h2>
    <hr />
    <router-link to="/options" class="btn my-btn-primary float-right">
      Manage option
    </router-link>
    <div v-if="option">
      <h3>Option Name: {{ option.name }}</h3>
      <h3>Option Group: {{ option.option_group.name }}</h3>

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
      option: null,
    };
  },
  created() {
    if (!User.loggedIn()) {
      this.$router.push("/");
    }
    // Fetch option data, including permissions, from your API
    this.fetchoptionData();
  },
  methods: {
    async fetchoptionData() {
      const response = await axios.get(`/api/options/${this.$route.params.id}`);
      // alert(response.data);
      this.option = response.data;
    },
  },
};
</script>
