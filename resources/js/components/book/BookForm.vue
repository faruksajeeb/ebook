<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add publisher
            </h3>
            <h3 class="text-white-900" v-else>
              <i class="fa fa-pencil"></i> Edit publisher
            </h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                id="form"
                class="publisher"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />
                <div v-if="!isNew && !publisher">
                  <LoadingSpinner/>
                </div>
                <div class="form-group">
                  <label for="">publisher Name <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your publisher Name"
                    name="publisher_name"
                    v-model="form.publisher_name"
                    :class="{ 'is-invalid': form.errors.has('publisher_name') }"
                  />
                  <HasError :form="form" field="publisher_name" />
                </div>
                <div class="form-group">
                  <label for="">publisher Phone </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your publisher phone"
                    v-model="form.publisher_phone"
                    :class="{ 'is-invalid': form.errors.has('publisher_phone') }"
                  />
                  <HasError :form="form" field="publisher_phone" />
                </div>
                <div class="form-group">
                  <label for="">publisher Email </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your publisher Email"
                    v-model="form.publisher_email"
                    :class="{ 'is-invalid': form.errors.has('publisher_email') }"
                  />
                  <HasError :form="form" field="publisher_email" />
                </div>
                <div class="form-group">
                  <label for="">publisher Country </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your publisher Country"
                    v-model="form.publisher_country"
                    :class="{ 'is-invalid': form.errors.has('publisher_country') }"
                  />
                  <HasError :form="form" field="publisher_country" />
                </div>
                <div class="form-group">
                  <label for="">Address </label>
                  <textarea
                    v-model="form.publisher_address"
                    cols="30"
                    rows="3"
                    :class="{ 'is-invalid': form.errors.has('publisher_address') }"
                    class="form-control"
                  ></textarea>

                  <HasError :form="form" field="publisher_address" />
                </div>
                <div class="form-group">
                  <label for="">publisher Photo </label>
                  <input
                    type="file"
                    class="form-control"
                    placeholder="Enter Your publisher Photo"
                    name="publisher_photo"
                    @change="onFileChange"
                    accept="image/*"
                    :class="{ 'is-invalid': form.errors.has('publisher_photo') }"
                  />
                  <HasError :form="form" field="publisher_photo" />
                  [Allow File type:jpeg,png,jpg,gif,svg & Size:2MB]
                </div>
                <div class="image-item">
                  <!-- <img src="http://127.0.0.1:8000/assets/img/publisher/faizaan.jpg" alt="" width="100" /> -->
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
                  <router-link to="/publishers"> Manage publisher </router-link>                 
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
    publisher: false,
    form: new Form({
      publisher_name: "",
      publisher_phone: "",
      publisher_photo: null,
      publisher_email: "",
      publisher_address: "",
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
  },
  async created() {
    if (!this.isNew) {
      const response = await axios.get(`/api/publishers/${this.$route.params.id}`);

      // console.log(response.data);
     
      this.form.publisher_name = response.data.publisher_name;
      this.form.publisher_phone = response.data.publisher_phone;
      // this.form.publisher_photo = response.data.publisher_photo;
      this.form.publisher_email = response.data.publisher_email;
      this.form.publisher_address = response.data.publisher_address;
      this.form.publisher_country = response.data.publisher_country;
      this.imageUrl =
        `${window.publicPath}assets/img/publisher/thumbnail/` +
        response.data.publisher_photo;
        this.publisher = true;
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
            "publisher_photo",
            "File size exceeds the maximum allowed size."
          );
          Notification.error("File size exceeds the maximum allowed size.");
        }
        if (!allowedFileTypes.includes(selectedFile.type)) {
          this.form.errors.set(
            "publisher_photo",
            " File type is not supported. Please choose a valid file type."
          );
          Notification.error(" File type is not supported. Please choose a valid file type.");
        }
        this.form.publisher_photo = selectedFile;

        const reader = new FileReader();

        reader.onload = (e) => {
          this.imageUrl = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
      }
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.form.publisher_photo = null;
    },
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/publishers", this.form)
          .then(() => {
            this.$router.push({ name: "publishers" });
            Notification.success(`Create publisher ${this.form.name} successfully!`);
          })
          .catch((error) => {
            // console.log(error);
            if (error.response.status === 422) {
              this.errors = error.response.data.errors;
              Notification.error("Validation Errors!");
            } else if (error.response.status === 401) {
              // statusText = "Unpublisherized";
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
            .post(`/api/publishers/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("publisher info updated");
              this.$router.push("/publishers");
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
