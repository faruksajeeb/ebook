<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add Customer
            </h3>
            <h3 class="text-white-900" v-else>
              <i class="fa fa-pencil"></i> Edit Customer
            </h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
              id="form"
                class="customer"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />

                <div class="form-group">
                  <label for="">Customer Name <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your Customer Name"
                    v-model="form.customer_name"
                    :class="{ 'is-invalid': form.errors.has('customer_name') }"
                  />
                  <HasError :form="form" field="customer_name" />
                </div>
                <div class="form-group">
                  <label for="">Customer Phone <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your Customer phone"
                    v-model="form.customer_phone"
                    :class="{ 'is-invalid': form.errors.has('customer_phone') }"
                  />
                  <HasError :form="form" field="customer_phone" />
                </div>
                <div class="form-group">
                  <label for="">Customer Email </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your Customer Email"
                    v-model="form.customer_email"
                    :class="{ 'is-invalid': form.errors.has('customer_email') }"
                  />
                  <HasError :form="form" field="customer_email" />
                </div>
                <div class="form-group">
                  <label for="">Address </label>
                  <textarea
                    v-model="form.customer_address"
                    cols="30"
                    rows="3"
                    :class="{ 'is-invalid': form.errors.has('customer_address') }"
                    class="form-control"
                  ></textarea>

                  <HasError :form="form" field="customer_address" />
                </div>
                <div class="form-group">
                  <label for="">Customer Photo </label>
                  <input
                    type="file"
                    class="form-control"
                    placeholder="Enter Your Customer Photo"
                    name="customer_photo"
                    @change="onFileChange"
                    accept="image/*"
                    :class="{ 'is-invalid': form.errors.has('customer_photo') }"
                  />
                  <HasError :form="form" field="customer_photo" />
                </div>
                <div class="image-item">
                  <!-- <img src="http://127.0.0.1:8000/assets/img/customer/faizaan.jpg" alt="" width="100" /> -->
                  <img v-if="imageUrl" :src="imageUrl" alt="Image Preview" width="150" />
                  <button
                    class="remove-button"
                    type="button"
                    v-if="imageUrl"
                    @click="removeSingleImage(image, key)"
                  >
                    x
                  </button>
                </div>
                <hr />
                <div class="form-group">
                  <router-link to="/customers"> Manage Customer </router-link>
                  <div v-if="form.progress">Progress: {{ form.progress.percentage }}%</div>
                  <save-button :is-submitting="isSubmitting"></save-button>
                  <reset-button @reset-data="resetData" />
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script type="text/javascript">
export default {
  data: () => ({
    isSubmitting: false,
    imageUrl: null,
    customer: null,
    form: new Form({
      customer_name: "",
      customer_phone: "",
      customer_photo: null,
      customer_email: "",
      customer_address: "",
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
  },
  async created() {
    if (!this.isNew) {
      const response = await axios.get(`/api/customers/${this.$route.params.id}`);
     
      // console.log(response.data);
      this.form.customer_name = response.data.customer_name;
      this.form.customer_phone = response.data.customer_phone;
      // this.form.customer_photo = response.data.customer_photo;
      this.form.customer_email = response.data.customer_email;
      this.form.customer_address = response.data.customer_address;
      this.imageUrl = `${window.publicPath}`+response.data.customer_photo;
    }
  },
  methods: {
    onFileChange(e) {     
      this.form.customer_photo = e.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        this.imageUrl = e.target.result;
        // this.form.customer_photo = e.target.result;
      };
      reader.readAsDataURL(this.form.customer_photo);
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.form.customer_photo = null;
    },
    async submitForm() {
   
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/customers", this.form)
          .then(() => {
            this.$router.push({ name: "customers" });
            Notification.success(`CreateCustomer ${this.form.name} successfully!`);
          })
          .catch((error) => {
            // console.log(error);
            if (error.response.status === 422) {
              this.errors = error.response.data.errors;
              Notification.error("Validation Errors!");
            } else if (error.response.status === 401) {
              // statusText = "Unauthorized";
              this.errors = {};
              Notification.error(error.response.data.error);
            } else {
              Notification.error(error.response.statusText);
            }
          })
          .finally(() => {
            // always executed;
            this.isSubmitting = false;
          });
      } else {
        try {
          // const form = document.getElementById("form");
          // let formData = new FormData(form);
          // formData.append('customer_name', this.form.customer_name);
          // formData.append('customer_phone', this.form.customer_phone);
          // formData.append('customer_email', this.form.customer_email);
          // formData.append('customer_address', this.form.customer_address);
          // formData.append('customer_photo', this.from.customer_photo);
          // this.form.append('customer_photo', this.from.customer_photo);
          await this.form
            .put(`/api/customers/${this.$route.params.id}`, this.form,{
                    headers: {
                        'content-type': 'multipart/form-data',
                    }
                })
            .then((response) => {
              Notification.success("Customer info Updated");
              this.$router.push("/customers");
            })
            .catch((error) => {
              Notification.error(error);
            })
            .finally(() => {
              // always executed;
              this.isSubmitting = false;
            });
        } catch (error) {
          Notification.error(error);
        }
      }
    },
    resetData() {
      this.form.clear();
      this.form.reset();
    },
  },
};
</script>

<style type="text/css" scoped>
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
</style>
