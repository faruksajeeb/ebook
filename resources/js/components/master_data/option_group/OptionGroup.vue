<template>
  <div class="col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h2>Option Group Details</h2>
        <router-link to="/option-groups" class="btn my-btn-primary float-right">
          Manage optionGroup
        </router-link>
      </div>
      <div class="card-body">
        <div v-if="optionGroup">
          <h3>Option Group Name: {{ optionGroup.name }}</h3>
        </div>
        <div v-else>
          <p>Loading...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      optionGroup: null,
    };
  },
  created() {
    this.fetchOptionGroupData();
  },
  methods: {
    async fetchOptionGroupData() {
      const response = await axios.get(`/api/option-groups/${this.$route.params.id}`);
      // alert(response.data);
      this.optionGroup = response.data;
    },
  },
};
</script>
