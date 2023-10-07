<template>
  <div class="text-center">
      <h1 class="text-center my-5 text-bold">Sale Report</h1>
      <hr>
      <router-link
          to="/reports/sale/customer-wise-sale"
          >
      <button class="btn btn-lg btn-outline-primary p-5 fw-bold m-3">Customer Wise Sale</button>
      </router-link>
      <router-link
          to="/reports/sale/category-wise-sale"
          >
      <button class="btn btn-lg btn-outline-primary p-5 fw-bold m-3">Category Wise Sale</button>
    </router-link>
  </div>
</template>

<script>
  export default {
    
  }
</script>

<style scoped>

</style>