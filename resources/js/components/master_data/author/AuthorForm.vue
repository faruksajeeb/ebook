<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add Author
            </h3>
            <h3 class="text-white-900" v-else>
              <i class="fa fa-pencil"></i> Edit Author
            </h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                id="form"
                class="author"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />
                <div v-if="!isNew && !author">
                  <LoadingSpinner/>
                </div>
                <div class="form-group">
                  <label for="">Author Name <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your author Name"
                    name="author_name"
                    v-model="form.author_name"
                    :class="{ 'is-invalid': form.errors.has('author_name') }"
                  />
                  <HasError :form="form" field="author_name" />
                </div>
                <div class="form-group">
                  <label for="">Author Phone </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your author phone"
                    v-model="form.author_phone"
                    :class="{ 'is-invalid': form.errors.has('author_phone') }"
                  />
                  <HasError :form="form" field="author_phone" />
                </div>
                <div class="form-group">
                  <label for="">Author Email </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your author Email"
                    v-model="form.author_email"
                    :class="{ 'is-invalid': form.errors.has('author_email') }"
                  />
                  <HasError :form="form" field="author_email" />
                </div>
                <div class="form-group">
                  <label for="">Author Country </label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your author Country"
                    v-model="form.author_country"
                    :class="{ 'is-invalid': form.errors.has('author_country') }"
                  />
                  <HasError :form="form" field="author_country" />
                </div>
                <div class="form-group">
                  <label for="">Address </label>
                  <textarea
                    v-model="form.author_address"
                    cols="30"
                    rows="3"
                    :class="{ 'is-invalid': form.errors.has('author_address') }"
                    class="form-control"
                  ></textarea>

                  <HasError :form="form" field="author_address" />
                </div>
                <div class="form-group">
                  <label for="">Author Photo </label>
                  <input
                    type="file"
                    class="form-control"
                    placeholder="Enter Your author Photo"
                    name="author_photo"
                    @change="onFileChange"
                    accept="image/*"
                    :class="{ 'is-invalid': form.errors.has('author_photo') }"
                  />
                  <HasError :form="form" field="author_photo" />
                  [Allow File type:jpeg,png,jpg,gif,svg & Size:2MB]
                </div>
                <div class="image-item">
                  <!-- <img src="http://127.0.0.1:8000/assets/img/author/faizaan.jpg" alt="" width="100" /> -->
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
                  <router-link to="/authors"> Manage Author </router-link>                 
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
    author: false,
    form: new Form({
      author_name: "",
      author_phone: "",
      author_photo: null,
      author_email: "",
      author_address: "",
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
  },
  async created() {
    if (!this.isNew) {
      const response = await axios.get(`/api/authors/${this.$route.params.id}`);

      // console.log(response.data);
     
      this.form.author_name = response.data.author_name;
      this.form.author_phone = response.data.author_phone;
      // this.form.author_photo = response.data.author_photo;
      this.form.author_email = response.data.author_email;
      this.form.author_address = response.data.author_address;
      this.form.author_country = response.data.author_country;
      this.imageUrl =
        `${window.publicPath}assets/img/author/thumbnail/` +
        response.data.author_photo;
        this.author = true;
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
            "author_photo",
            "File size exceeds the maximum allowed size."
          );
          Notification.error("File size exceeds the maximum allowed size.");
        }
        if (!allowedFileTypes.includes(selectedFile.type)) {
          this.form.errors.set(
            "author_photo",
            " File type is not supported. Please choose a valid file type."
          );
          Notification.error(" File type is not supported. Please choose a valid file type.");
        }
        this.form.author_photo = selectedFile;

        const reader = new FileReader();

        reader.onload = (e) => {
          this.imageUrl = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
      }
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.form.author_photo = null;
    },
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/authors", this.form)
          .then(() => {
            this.$router.push({ name: "authors" });
            Notification.success(`Create author ${this.form.name} successfully!`);
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
            .post(`/api/authors/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("Author info updated");
              this.$router.push("/authors");
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
