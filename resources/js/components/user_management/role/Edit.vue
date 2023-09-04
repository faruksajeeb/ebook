<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-light-900"><i class="fa fa-plus"></i> Edit Role</h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                class="user"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <!-- <AlertError :form="form" /> -->
                <div class="form-group">
                  <label for="">Role Name:</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Enter Your role Name"
                    v-model="form.name"
                    
                  />
                  <!-- <HasError :form="form" field="name" /> -->
                </div>
                <div class="form-group mt-3">
                  <!-- List of available permissions -->
                  <!-- <div class="available-permissions">
                    <h5>Available Permissions</h5>
                    <ul>
                      <li v-for="permission in permissions" :key="permission.id">
                        <input
                          type="checkbox"
                          :id="permission.id"
                          v-model="form.selectedPermissions"
                          :value="permission.id"
                        />
                        <label :for="permission.id">{{
                          permission.permission_name
                        }}</label>
                      </li>
                    </ul>
                  </div> -->
                </div>
                <hr />
                <div class="form-group">
                  <router-link to="/manage-role"> Manage role </router-link>
                  <save-changes-button
                    :is-submitting="isSubmitting"
                  ></save-changes-button>
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
    form: new Form({
      name: "",
      selectedPermissions: [], // Store selected permissions
    }),
  }),
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: "/" });
    }

    let id = this.$route.params.id;

    axios
      .get("/api/manage-role/" + id)
      .then(({ data }) => (this.form = data))
      .catch((error) => {
        console.log(error);
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
      });
  },
  computed: {
    permissions() {
      // console.log(this.$store.getters.getPemissions);
      // return this.$store.state.permissions;
      return this.$store.getters.getPemissions;
    },
  },
  methods: {
    async submitForm() {
      this.isSubmitting = true;
      let id = this.$route.params.id;
      await axios.patch("/api/manage-role/" + id, this.form)
        .then(() => {
          this.$router.push({ name: "manage-role" });
          Notification.success("Data Updated Successfully!");
        })
        .catch((error) => {
          console.log(error);
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
<style type="text/css" scoped>
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
</style>
