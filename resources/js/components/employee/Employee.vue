<template>
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h2>Sub Category Details</h2>

    <router-link to="/sub-categories" class="btn my-btn-primary float-right">
      Manage Sub Category
    </router-link>
        </div>
        <div class="card-body">
          <div v-if="SubCategory">
      <h3>Sub Category Name: {{ SubCategory.sub_category_name }}</h3>
      <h3>Category: {{ SubCategory.category.category_name }}</h3>

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
      SubCategory: null,
    };
  },
  created() {
    if (!User.loggedIn()) {
      this.$router.push("/");
    }
    // Fetch SubCategory data, including permissions, from your API
    this.fetchSubCategoryData();
  },
  methods: {
    async fetchSubCategoryData() {
      const response = await axios.get(`/api/sub-categories/${this.$route.params.id}`);
      // alert(response.data);
      this.SubCategory = response.data;
    },
  },
};
</script>
