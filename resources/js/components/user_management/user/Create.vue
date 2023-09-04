<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900"><i class="fa fa-plus"></i> Add User</h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                class="user"
                @submit.prevent="userInsert"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />
                <div class="form-group">
                  <label for="">User Name</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your user Name"
                    v-model="form.name"
                    :class="{ 'is-invalid': form.errors.has('name') }"
                  />
                  <HasError :form="form" field="name" />
                </div>
                <div class="form-group">
                  <label for="">User Email</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter user email"
                    v-model="form.email"
                    :class="{ 'is-invalid': form.errors.has('email') }"
                  />
                  <HasError :form="form" field="email" />
                </div>
                <div class="form-group">
                  <label for="">Password</label>
                  <input
                    type="password"
                    class="form-control"
                    placeholder="Enter user password"
                    v-model="form.password"
                    :class="{ 'is-invalid': form.errors.has('password') }"
                  />
                  <HasError :form="form" field="password" />
                </div>
                <div class="form-group">
                  <label for=""
                    >Confirm Password <span class="text-danger">*</span></label
                  >
                  <input
                    type="password"
                    v-model="form.password_confirmation"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
                  />
                  <HasError :form="form" field="password_confirmation" />
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-8">
                      <label for="">User Photo</label>
                      <input
                        type="file"
                        name="file"
                        class="form-control"
                        @change="handleFile"
                      />
                      <HasError :form="form" field="file" />
                      <div v-if="form.progress">
                        Progress: {{ form.progress.percentage }}%
                      </div>
                    </div>
                    <div class="col-md-4 image-container">
                      <div class="image-item">
                        <img
                          src="assets/product/database_server.png"
                          alt=""
                          width="100"
                        />
                        <img
                          v-if="imageUrl"
                          :src="imageUrl"
                          alt="Image Preview"
                          width="150"
                        />
                        <button
                          class="remove-button"
                          type="button"
                          v-if="imageUrl"
                          @click="removeImage(image, key)"
                        >
                          x
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <hr />
                <div class="form-group">
                  <router-link to="/manage-user"> Manage user </router-link>
                  <save-button :is-submitting="isSubmitting"></save-button>
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
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: "/" });
    }
  },

  data: () => ({
    isSubmitting: false,
    imageUrl: null,
    form: new Form({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      file: null,
    }),
  }),
  methods: {
    handleFile(event) {
      // We'll grab just the first file...
      // You can also do some client side validation here.
      const file = event.target.files[0];

      // Set the file object onto the form...
      this.form.file = file;
      const reader = new FileReader();

      reader.onload = (e) => {
        this.imageUrl = e.target.result;
      };
      reader.readAsDataURL(this.form.file);

    },
    removeImage(image, index) {
      this.imageUrl = null;
      this.form.file = null;
    },
    async userInsert() {
      this.isSubmitting = true;
      await this.form
        .post("/api/manage-user", this.form)
        .then(() => {
          this.$router.push({ name: "manage-user" });
          Notification.success("User Inserted Successfully!");
        })
        .catch((error) => {
          // console.log(error);
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
            Notification.error(error.response.statusText);
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
    },
  },
};
</script>

<style type="text/css"></style>
