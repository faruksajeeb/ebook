<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add Option
            </h3>
            <h3 class="text-white-900" v-else>
              <i class="fa fa-pencil"></i> Edit option
            </h3>
          </div>
          <div class="card-body p-3">
            <div class="form">
              <form
                class="user"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <AlertError :form="form" />

                <div class="form-group mt-3">
                  <label for="">Option Group <span class="text-danger">*</span></label>
                  <select
                    name=""
                    id=""
                    v-model="form.option_group_id"
                    :class="{ 'is-invalid': form.errors.has('option_group_id') }"
                    class="form-select"
                  >
                    <option value="" selected>--select option group--</option>
                    <option :value="optionGroup.id" v-for="optionGroup in optionGroups" :key="optionGroup.id">{{ optionGroup.name }}</option>
                  </select>
                  <HasError :form="form" field="option_group_id" />
                </div>
                <div class="form-group">
                  <label for="">Option Name <span class="text-danger">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    id="exampleInputFirstName"
                    placeholder="Enter Your option Name"
                    v-model="form.name"
                    :class="{ 'is-invalid': form.errors.has('name') }"
                  />
                  <HasError :form="form" field="name" />
                </div>
                <hr />
                <div class="form-group">
                  <router-link to="/options"> Manage Option </router-link>
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
// import {Form} from "vform";
import { mapActions } from 'vuex';
export default {
  data: () => ({
    isSubmitting: false,
    option: null,
    form: new Form({
      name: "",
      option_group_id: "",
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
    optionGroups() {
      return this.$store.state.option_groups;
    },
  },
  async created() {
    this.fetchOptionGroups();
    if (!this.isNew) {
      const response = await axios.get(`/api/options/${this.$route.params.id}`);
      // alert(response.data);
      this.form.name = response.data.name;
      this.form.option_group_id = response.data.option_group_id;
    }
  },
  methods: {
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/options", this.form)
          .then(() => {
            this.$router.push({ name: "options" });
            Notification.success(`Create option ${this.form.name} successfully!`);
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
          console.log(this.form);
          await this.form
            .put(`/api/options/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("option info Updated");
              this.$router.push("/options");
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
    ...mapActions(['fetchOptionGroups']),
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
