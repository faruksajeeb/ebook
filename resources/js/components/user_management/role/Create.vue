<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900"><i class="fa fa-plus"></i> Add Role</h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form class="user" @submit.prevent="roleInsert">
                <div class="form-group">
                  <div class="form-row">
                    <div class="col-md-12">
                    <label for="">Role Name</label>
                      <input
                        type="text"
                        class="form-control"
                        id="exampleInputFirstName"
                        placeholder="Enter Your role Name"
                        v-model="form.name"
                        :class="{ 'is-invalid': errors.name }"
                      />
                      <small class="text-danger" v-if="errors.name">
                        {{ errors.name[0] }}
                      </small>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="col-md-12">
                    <label for="">Permissions</label>
                      
                    </div>
                  </div>
                </div>
                <hr />
                <div class="form-group">
                  <router-link to="/manage-role"> Manage role </router-link>
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

  data() {
    return { 
      isSubmitting : false,     
      form: {
        name: null,
      },
      errors: {},
    };
  },

  methods: {
    roleInsert() {
      this.isSubmitting = true;
      axios
        .post("/api/manage-role", this.form)
        .then(() => {
          this.$router.push({ name: "manage-role" });
          Notification.success('Role Inserted Successfully!');
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
