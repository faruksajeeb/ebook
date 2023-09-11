<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add Supplier
            </h3>
            <h3 class="text-white-900" v-else>
              <i class="fa fa-pencil"></i> Edit Supplier
            </h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                id="form"
                class="supplier"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />
                <div v-if="!isNew && !supplier">
                  <LoadingSpinner/>
                </div>
                <div class="form-group">
                  <label for="">Supplier Name <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your supplier Name"
                    name="supplier_name"
                    v-model="form.supplier_name"
                    :class="{ 'is-invalid': form.errors.has('supplier_name') }"
                  />
                  <HasError :form="form" field="supplier_name" />
                </div>
                <div class="form-group">
                  <label for="">supplier Phone <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your supplier phone"
                    v-model="form.supplier_phone"
                    :class="{ 'is-invalid': form.errors.has('supplier_phone') }"
                  />
                  <HasError :form="form" field="supplier_phone" />
                </div>
                <div class="form-group">
                  <label for="">supplier Email </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your supplier Email"
                    v-model="form.supplier_email"
                    :class="{ 'is-invalid': form.errors.has('supplier_email') }"
                  />
                  <HasError :form="form" field="supplier_email" />
                </div>
                <div class="form-group">
                  <label for="">Address </label>
                  <textarea
                    v-model="form.supplier_address"
                    cols="30"
                    rows="3"
                    :class="{ 'is-invalid': form.errors.has('supplier_address') }"
                    class="form-control"
                  ></textarea>

                  <HasError :form="form" field="supplier_address" />
                </div>
                <div class="form-group">
                  <label for="">supplier Photo </label>
                  <input
                    type="file"
                    class="form-control"
                    placeholder="Enter Your supplier Photo"
                    name="supplier_photo"
                    @change="onFileChange"
                    accept="image/*"
                    :class="{ 'is-invalid': form.errors.has('supplier_photo') }"
                  />
                  <HasError :form="form" field="supplier_photo" />
                  [Allow File type:jpeg,png,jpg,gif,svg & Size:2MB]
                </div>
                <div class="image-item">
                  <!-- <img src="http://127.0.0.1:8000/assets/img/supplier/faizaan.jpg" alt="" width="100" /> -->
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
                  <div v-if="form.progress">
                    Progress: {{ form.progress.percentage }}%
                  </div>
                  <router-link to="/suppliers"> Manage supplier </router-link>                 
                  <save-button v-if="isNew" :is-submitting="isSubmitting"></save-button>
                  <save-changes-button
                    v-else
                    :is-submitting="isSubmitting"
                  ></save-changes-button>
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
    supplier: false,
    form: new Form({
      supplier_name: "",
      supplier_phone: "",
      supplier_photo: null,
      supplier_email: "",
      supplier_address: "",
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
  },
  async created() {
    if (!this.isNew) {
      const response = await axios.get(`/api/suppliers/${this.$route.params.id}`);

      // console.log(response.data);
     
      this.form.supplier_name = response.data.supplier_name;
      this.form.supplier_phone = response.data.supplier_phone;
      // this.form.supplier_photo = response.data.supplier_photo;
      this.form.supplier_email = response.data.supplier_email;
      this.form.supplier_address = response.data.supplier_address;
      this.imageUrl =
        `${window.publicPath}assets/img/supplier/thumbnail/` +
        response.data.supplier_photo;
        this.supplier = true;
    }
  },
  methods: {
    onFileChange(e) {
      let selectedFile = e.target.files[0];
      if (selectedFile) {
        const allowedFileTypes = ["image/jpeg", "image/jpg", "image/png","image/gif","image/svg","application/pdf"];
        if (selectedFile.size > 2048 * 1024) {
          // Change this to your desired maximum file size in bytes
          this.form.errors.set(
            "supplier_photo",
            "File size exceeds the maximum allowed size."
          );
          Notification.error("File size exceeds the maximum allowed size.");
        }
        if (!allowedFileTypes.includes(selectedFile.type)) {
          this.form.errors.set(
            "supplier_photo",
            " File type is not supported. Please choose a valid file type."
          );
          Notification.error(" File type is not supported. Please choose a valid file type.");
        }
        this.form.supplier_photo = selectedFile;

        const reader = new FileReader();

        reader.onload = (e) => {
          this.imageUrl = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
      }
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.form.supplier_photo = null;
    },
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/suppliers", this.form)
          .then(() => {
            this.$router.push({ name: "suppliers" });
            Notification.success(`Create supplier ${this.form.name} successfully!`);
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
          await this.form
            .post(`/api/suppliers/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("Supplier info updated");
              this.$router.push("/suppliers");
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
