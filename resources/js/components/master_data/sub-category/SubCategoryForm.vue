<template>
  <div>
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm my-4">
          <div class="card-header py-2 my-bg-success">
            <h3 class="text-white-900" v-if="isNew">
              <i class="fa fa-plus"></i> Add SubCategory
            </h3>
            <h3 class="text-white-900" v-else>
              <i class="fa fa-pencil"></i> Edit SubCategory
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
                  <label for="">Category <span class="text-danger">*</span></label>
                  <select
                    name=""
                    id=""
                    v-model="form.category_id"
                    :class="{ 'is-invalid': form.errors.has('category_id') }"
                    class="form-select"
                  >
                    <option value="" selected>--select category--</option>
                    <option
                      :value="category.id"
                      v-for="category in categories"
                      :key="category.id"
                    >
                      {{ category.category_name }}
                    </option>
                  </select>
                  <HasError :form="form" field="category_id" />
                </div>
                <div class="form-group">
                  <label for=""
                    >Sub Category Name <span class="text-danger">*</span></label
                  >
                  <input
                    type="text"
                    class="form-control"
                    id="exampleInputFirstName"
                    placeholder="Enter Your Sub Category Name"
                    v-model="form.sub_category_name"
                    :class="{ 'is-invalid': form.errors.has('sub_category_name') }"
                  />
                  <HasError :form="form" field="sub_category_name" />
                </div>
                <hr />
                <div class="form-group">
                  <router-link to="/sub-categories"> Manage Sub Category </router-link>
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
import { mapActions } from "vuex";
export default {
  data: () => ({
    isSubmitting: false,
    SubCategory: null,
    form: new Form({
      sub_category_name: "",
      category_id: "",
    }),
  }),
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
    categories() {
      return this.$store.state.categories;
    },
  },
  async created() {
    this.fetchCategories();
    if (!this.isNew) {
      const response = await axios.get(`/api/sub-categories/${this.$route.params.id}`);
      // alert(response.data);
      this.form.sub_category_name = response.data.sub_category_name;
      this.form.category_id = response.data.category_id;
    }
  },
  methods: {
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/sub-categories", this.form)
          .then(() => {
            this.$router.push({ name: "sub-categories" });
            Notification.success(`CreateSubCategory ${this.form.name} successfully!`);
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
            .put(`/api/sub-categories/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("Sub-category info Updated");
              this.$router.push("/sub-categories");
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
    ...mapActions(["fetchCategories"]),
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
