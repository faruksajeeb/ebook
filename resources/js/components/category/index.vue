<script type="text/javascript">
export default {
  name: "Category",
  data() {
    return {
      checked: [],
      paginator: {
        totalRecords: 0,
        from: 0,
        to: 0,
        current_page: "",
        per_page: "",
      },
      categories: {
        type: Object,
        default: null,
      },
      params: {
        paginate: 5,
        id: "",
        category_name: "",
        sort_field: "created_at",
        sort_direction: "desc",
      },
      search: "",
    };
  },
  mounted() {
    this.getCategories();
  },
  watch: {
    params: {
      handler() {
        this.getCategories();
      },
      deep: true,
    },
    search(val, old) {
      if (val.length >= 3 || old.length >= 3) {
        this.getCategories();
      }
    },
  },
  computed: {},
  methods: {
    async getCategories(page = 1) {
      await axios
        // .get(`/api/products?page=${page}`)
        // .get(`/api/products?page=${page}&category_id=${this.params.category_id}&sort_field=${this.params.sort_field}&sort_direction=${this.params.sort_direction}`)
        .get("/api/manage-category", {
          params: {
            page,
            search: this.search.length >= 3 ? this.search : "",
            ...this.params,
          },
        })
        .then((response) => {
          // console.log(response);
          this.categories = response.data;
          this.paginator.totalRecords = response.data.total;
          if (response.data.total <= 0) {
            document.querySelector(".loading-section").innerText = "No Record Found!.";
          }
          this.paginator.from = response.data.from;
          this.paginator.to = response.data.to;
          this.paginator.current_page = response.data.current_page;
          this.paginator.per_page = response.data.per_page;
        });
    },
    refreshData() {
      this.getCategories();
    },
    changeShort(field) {
      if (this.params.sort_field === field) {
        this.params.sort_direction =
          this.params.sort_direction === "asc" ? "desc" : "asc";
      } else {
        this.params.sort_field = field;
        this.params.sort_direction = "asc";
      }
      // this.getProducts();
    },
  },
  // created() {
  //   if (!User.loggedIn()) {
  //     this.$router.push({ name: "/" });
  //   }
  // },
  // data() {
  //   return {
  //     categories: [],
  //     searchTerm: "",
  //   };
  // },
  // computed: {
  //   filtersearch() {
  //     return this.categories.filter((category) => {
  //       return category.category_name.match(this.searchTerm);
  //     });
  //   },
  // },

  // methods: {
  //   allCategory() {
  //     axios
  //       .get("/api/manage-category/")
  //       .then(({ data }) => (this.categories = data))
  //       .catch();
  //   },
  //   deleteCategory(id) {
  //     Swal.fire({
  //       title: "Are you sure?",
  //       text: "You won't be able to revert this!",
  //       icon: "warning",
  //       showCancelButton: true,
  //       confirmButtonColor: "#3085d6",
  //       cancelButtonColor: "#d33",
  //       confirmButtonText: "Yes, delete it!",
  //     }).then((result) => {
  //       if (result.value) {
  //         axios
  //           .delete("/api/manage-category/" + id)
  //           .then(() => {
  //             this.categories = this.categories.filter((category) => {
  //               return category.id != id;
  //             });
  //           })
  //           .catch(() => {
  //             this.$router.push({ name: "manage-category" });
  //           });
  //         Swal.fire("Deleted!", "Your file has been deleted.", "success");
  //       }
  //     });
  //   },
  // },
  // created() {
  //   this.allCategory();
  // },
};
</script>
<template>
  <div>
    <div class="row">
      <div class="col-lg-12 mb-4">
        <!-- Simple Tables -->
        <div class="card">
          <div
            class="card-header py-3 d-flex flex-row align-items-center justify-content-between"
          >
            <h3 class="m-0 font-weight-bold text-success">Category List</h3>
          </div>
          <div class="card-body p-0 m-0">
            <!-- <div class="row p-2">
              <div class="col-md-6">
                <input
                  type="text"
                  v-model="searchTerm"
                  class="form-control"
                  style="width: 300px"
                  placeholder="Search Here"
                />
              </div>
              <div class="col-md-6">
                <router-link to="/add-category" class="btn btn-primary float-right">
                  <i class="fa fa-solid fa-plus"></i>
                  Add Category
                </router-link>
              </div>
            </div> -->
            <!-- <div class="row justify-content-between"> -->
            <div class="row p-2">
              <div class="input-group">
                <div class="d-flex col-md-2">
                  <label for="">Per Page: </label>
                  <select v-model="params.paginate" id="" class="">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Search by category. (Type and Enter)"
                  v-model.lazy="search"
                />
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col" class="text-center">
                      <input type="checkbox" class="form-check p-3" />
                    </th>
                    <th scope="col">
                      <a href="#" @click.prevent="changeShort('id')">#ID</a>
                      <span
                        v-if="
                          this.params.sort_field == 'id' &&
                          this.params.sort_direction == 'asc'
                        "
                        >↑</span
                      >
                      <span
                        v-if="
                          this.params.sort_field == 'id' &&
                          this.params.sort_direction == 'desc'
                        "
                        >↓</span
                      >
                    </th>
                    <th scope="col">
                      <a href="#" @click.prevent="changeShort('category_name')">Name</a>
                      <!-- <a href="#">Name</a> -->
                      <span
                        v-if="
                          this.params.sort_field == 'category_name' &&
                          this.params.sort_direction == 'asc'
                        "
                        >↑</span
                      >
                      <span
                        v-if="
                          this.params.sort_field == 'category_name' &&
                          this.params.sort_direction == 'desc'
                        "
                        >↓</span
                      >
                    </th>
                    <th class="text-right">Action</th>
                  </tr>
                </thead>
                <tbody v-if="categories && paginator.totalRecords > 0">
                  <tr v-for="category in categories.data" :key="category.id">
                    <td class="text-center">
                      <input
                        type="checkbox"
                        :value="category.id"
                        v-model="checked"
                        class="form-check-input"
                      />
                    </td>
                    <td class="text-nowrap">{{ category.id }}</td>
                    <td>{{ category.category_name }}</td>

                    <td class="text-right">
                      <router-link
                        :to="{ name: 'edit-category', params: { id: category.id } }"
                        class="btn btn-sm btn-primary px-1 mx-1"
                        ><i class="fa fa-edit"></i> Edit</router-link
                      >

                      <a class="btn btn-sm btn-danger px-1 mx-1">
                        <!-- <a @click="deleteCategory(category.id)" class="btn btn-sm btn-danger px-1 mx-1" > -->
                        <font color="#ffffff"><i class="fa fa-trash"></i> Delete</font></a
                      >
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <td colspan="3" class="text-center loading-section">
                      <span
                        class="spinner-border spinner-border-lg"
                        role="status"
                        aria-hidden="true"
                      ></span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="col-md-6">
                <p>
                  Current Page: {{ paginator.current_page }} Per Page:
                  {{ paginator.per_page }}, Showing {{ paginator.from }} to
                  {{ paginator.to }} of {{ paginator.totalRecords }} entries
                </p>
              </div>
              <div class="col-md-6">
                <pagination
                  align="right"
                  :data="categories"
                  :limit="5"
                  @pagination-change-page="getCategories"
                ></pagination>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--Row-->
  </div>
</template>

<style type="text/css">
#em_photo {
  height: 40px;
  width: 40px;
}
</style>
