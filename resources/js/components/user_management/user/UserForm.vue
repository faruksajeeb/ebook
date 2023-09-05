<template>
  <div>
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add User
            </h3>
            <h3 class="text-white-900" v-else><i class="fa fa-pencil"></i> Edit User</h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                class="user"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="">User Name <span class="text-danger">*</span></label>
                      <input
                        type="text"
                        class="form-control"
                        id="exampleInputFirstName"
                        placeholder="Enter Your user Name"
                        v-model="form.name"
                        :class="{ 'is-invalid': form.errors.has('name') }"
                      />
                      <HasError :form="form" field="name" />
                    </div>
                    <div class="form-group">
                      <label for="">User Email <span class="text-danger">*</span></label>
                      <input
                        type="text"
                        class="form-control"
                        id="exampleInputEmail"
                        placeholder="Enter Your User Email"
                        v-model="form.email"
                        :class="{ 'is-invalid': form.errors.has('email') }"
                      />
                      <HasError :form="form" field="email" />
                    </div>
                    <div class="form-group">
                      <label for="">Password <span class="text-danger">*</span></label>
                      <input
                        type="password"
                        class="form-control"
                        id="exampleInputPassword"
                        placeholder="Enter Your User Password"
                        v-model="form.password" name="password" 
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
                        v-model="form.password_confirmation" name="password_confirmation" 
                        class="form-control"
                        :class="{
                          'is-invalid': form.errors.has('password_confirmation'),
                        }"
                      />
                      <HasError :form="form" field="password_confirmation" />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <!-- List of available roles -->
                      <div class="available-roles">
                        <h5>Available Roles <span class="text-danger">*</span></h5>
                        <HasError :form="form" field="selectedRoles" />
                        <ul>
                          <li v-for="role in roles" :key="role.id">
                            <input
                              type="checkbox"
                              :id="role.id"
                              v-model="form.selectedRoles"
                              :value="role.id"
                              :key="role.id"
                            />
                            <label :for="role.id" class="mx-2">{{ role.name }}</label>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <hr />
                <div class="form-group">
                  <router-link to="/users"> Manage User </router-link>
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
    user: null,
    roles: [],
    form: new Form({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      selectedRoles: [], // Store selected roles
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
  },
  async created() {
    this.roles = this.$store.getters.getRoles;
    if (!User.loggedIn()) {
      // this.$router.push({ name: "/" });
    }
    if (!this.isNew) {
      const response = await axios.get(`/api/users/${this.$route.params.id}`);
      // alert(response.data);
      this.form.name = response.data.name;
      this.form.selectedRoles = response.data.roles.map((role) => role.id);
    }
  },
  methods: {
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/users", this.form)
          .then(() => {
            this.$router.push({ name: "users" });
            Notification.success(
              `Create user & Assigned roles to user ${this.form.name}`
            );
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
      } else {
        try {
          console.log(this.form);
          await this.form
            .put(`/api/users/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("user info Updated");
              this.$router.push("/users");
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
