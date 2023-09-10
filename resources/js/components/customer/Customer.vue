<template>
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h2>Customer Details</h2>

          <router-link to="/customers" class="btn my-btn-primary float-right">
            Manage Customer
          </router-link>
        </div>
        <div class="card-body">
          <div v-if="customer">
            <h3>Customer Name: {{ customer.customer_name }}</h3>
            <div class="row">
              <div class="col-md-4">
            <img :src="customerPhotoUrl" alt="Cutomer Photo" width="250"  class="img-fluid"/>
              
              </div>
              <div class="col-md-8">
                <table class="table ">
                  <tr>
                    <td>Phone</td>
                    <td>{{ customer.customer_phone }}</td>
                  </tr>
                  <tr>
                    <td>Email</td>
                    <td>{{ customer.customer_email }}</td>
                  </tr>
                  <tr>
                    <td>Address</td>
                    <td>{{ customer.customer_address }}</td>
                  </tr>
                </table>
              </div>
            </div>
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
      customer: null,
      customerPhotoUrl: null,
    };
  },
  created() {
    // Fetch SubCategory data, including permissions, from your API
    this.fetchCustomerData();
  },
  methods: {
    async fetchCustomerData() {
      const response = await axios.get(`/api/customers/${this.$route.params.id}`);
      // alert(response.data);
      this.customer = response.data;
      this.customerPhotoUrl = `${window.publicPath}assets/img/customer/`+response.data.customer_photo;
    },
  },
};
</script>
