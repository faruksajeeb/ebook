<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900"><i class="fa fa-plus"></i> Add Permission</h3>
          </div>
          <div class="card-body p-3">
            <form class="user" @submit.prevent="permissionInsert">
              <div class="form-group">
                <label for="">Permission Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="exampleInputFirstName"
                  placeholder="Enter Your permission Name"
                  v-model="form.name"
                  :class="{ 'is-invalid': errors.name }"
                />
                <small class="text-danger" v-if="errors.name">
                  {{ errors.name[0] }}
                </small>
              </div>
              <div class="form-group">
                <label for="">Permission Group</label>
                <select v-model="form.group_name" class="form-select"  :class="{ 'is-invalid': errors.group_name }">
                    <option value="">--select group--</option>
                    <option value="user">User</option>
                    <option value="role">Role</option>
                    <option value="permission">Permission</option>
                    <option value="category">Category</option>
                </select>
                <small class="text-danger" v-if="errors.group_name">
                  {{ errors.group_name[0] }}
                </small>
              </div>
              <hr />
              <div class="form-group">
                <router-link to="/manage-permission"> Manage Permission </router-link>
                <save-button :is-submitting="isSubmitting"></save-button>
              </div>
            </form>
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
      this.$router.push("/");
    }
  },

  data() {
    return {
      isSubmitting: false,
      form: {
        name: null,
        group_name: null,
      },
      errors: {},
    };
  },

  methods: {
    permissionInsert() {
      this.isSubmitting = true;
      axios
        .post("/api/manage-permission", this.form)
        .then(() => {
          this.$router.push({ name: "manage-permission" });
          Notification.success("Permission Inserted Successfully!");
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
