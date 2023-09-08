<template>
  <div class="col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h2>Category Details</h2>
        <router-link to="/categories" class="btn my-btn-primary float-right">
          Manage Category
        </router-link>
      </div>
      <div class="card-body">
        <div v-if="category">
          <h3>Category Name: {{ category.category_name }}</h3>
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
      category: null,
    };
  },
  created() {
    this.fetchCategoryData();
  },
  methods: {
    async fetchCategoryData() {
      const response = await axios.get(`/api/categories/${this.$route.params.id}`);
      // alert(response.data);
      this.category = response.data;
    },
  },
};
</script>
