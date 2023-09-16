<template>
  <div class="row">
    <div class="col-md-12 offset-md-0">
      <div class="card shadow-sm my-2">
        <div class="card-header py-2 my-bg-success">
          <h3 class="text-white-900" v-if="isNew">
            <i class="fa fa-plus"></i> Add Purchase
          </h3>
          <h3 class="text-white-900" v-else>
            <i class="fa fa-pencil"></i> Edit purchase
          </h3>
          <!-- <p class="text-white m-0">
            ফরমের লাল তারকা (<span class="text-danger">*</span>) চিহ্নিত ঘরগুলো অবশ্যই
            পূরণ করুন। অন্যান্য ঘরগুলো পূরণ ঐচ্ছিক।
          </p> -->
        </div>
        <div class="card-body p-3">
          <div class="form">
            <AlertError :form="form" />
            <div v-if="!isNew && !purchase">
              <LoadingSpinner />
            </div>
            <div class="row">
              <div class="col-md-5">
                <input
                  type="text"
                  class="form-control"
                  placeholder="Search by publication, author, book name, isbn or genre..."
                  v-model="search"
                />
                <div class="categories my-2">
                  <a
                    href="#"
                    class="p-2 m-1 text-danger text-nowrap"
                    @click="getCatWiseBook('')"
                    >All Category</a
                  >
                  <a
                    href="#"
                    class="p-2 m-1 text-danger text-nowrap"
                    @click="getCatWiseBook(category.id)"
                    v-for="category in categories"
                    >{{ category.category_name }}</a
                  >
                </div>
                <div class="row product-view" v-if="books && paginator.totalRecords > 0">
                  <div class="col-xl-4 col-md-6 mb-2 px-1" v-for="book in books.data">
                    <div class="card h-100">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col mr-2">
                            <div
                              class="text-xs font-weight-bold text-center text-uppercase mb-1"
                            >
                              {{ book.title }}
                            </div>
                            <div class="text-center">
                              <img
                                class="text-center"
                                :src="
                                  `${publicPath}assets/img/book/thumbnail/` + book.photo
                                "
                                alt=""
                                width="100"
                              />
                            </div>
                            <div class="mt-2 text-center font-weight-bold text-gray-800">
                              ৳ {{ book.price }}
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs text-center">
                              <span class="text-success mr-2"
                                ><i class="fa fa-pen"></i>
                                {{ book.author.author_name }}</span
                              >
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-footer text-center py-1">
                        <button
                          @click="addToCart(book)"
                          :class="`btn btn-sm my-btn-primary addToCart${book.id}`"
                        >
                          <i class="fa fa-plus"></i> Add To Cart
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center loading-section">
                  <loader v-if="isLoading"></loader>
                  <NoRecordFound v-else />
                </div>
                <div class="row">
                  <div class="col-xl-12 col-md-12">
                    <pagination
                      align="center"
                      :data="books"
                      :limit="3"
                      @pagination-change-page="getBooks"
                    ></pagination>
                  </div>
                </div>
              </div>
              <div class="col-md-7 border border-solid-1 py-1">
                <form
                  id="form"
                  class="purchase"
                  enctype="multipart/form-data"
                  @submit.prevent="submitForm"
                  @keydown="form.onKeydown($event)"
                >
                  <div class="input-group mb-2 row mx-0 px-0">
                    <div class="input-group-prepend px-0 col-md-4 mx-0">
                      <label class="input-group-text col-md-12" for="inputGroupSelect01"
                        >Supplier
                        <div class="text-danger">*</div></label
                      >
                    </div>
                    <select
                      class="custom-select mx-0 pe-0"
                      v-model="form.supplier_id"
                      :class="{ 'is-invalid': form.errors.has('supplier_id') }"
                    >
                      <option value="" disabled selected>Choose...</option>
                      <option
                        :value="supplier.id"
                        v-for="supplier in suppliers"
                        :key="supplier.id"
                      >
                        {{ supplier.supplier_name }}
                      </option>
                    </select>
                    <HasError :form="form" field="supplier_id" />
                  </div>
                  <div class="input-group mb-2 row mx-0 px-0">
                    <div class="input-group-prepend px-0 col-md-4 mx-0">
                      <label
                        class="input-group-text col-md-12"
                        for="inputGroupSelect01"
                        title=""
                        >Purchase Date
                        <div class="text-danger">*</div></label
                      >
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      placeholder="Enter Your purchase purchase_date"
                      name="purchase_date"
                      v-model="form.purchase_date"
                      :class="{ 'is-invalid': form.errors.has('purchase_date') }"
                    />
                    <HasError :form="form" field="purchase_date" />
                  </div>
                  <table class="table table-sm">
                    <thead>
                      <th class="text-left px-1">Book Name</th>
                      <th class="text-right px-1">Unit Price</th>
                      <th class="text-center px-1">Qty</th>
                      <th class="text-right px-1">Sub Total</th>
                      <th style="width: 50px"></th>
                    </thead>
                    <tbody v-if="cartItems.length > 0">
                      <tr v-for="(item, index) in cartItems" :key="index">
                        <td class="px-1">{{ item.title }}</td>
                        <td class="px-1 text-right">
                          <input
                            type="number"
                            v-model="item.price"
                            @input="updatePrice(index)"
                            style="width: 80px"
                          />
                        </td>
                        <td class="px-1">
                          <!-- {{ item.quantity }} -->
                          <input
                            type="number"
                            v-model="item.quantity"
                            @input="updateQuantity(index)"
                            style="width: 50px"
                          />
                        </td>
                        <td class="text-right px-1">{{ item.price * item.quantity }}</td>
                        <td class="text-center px-1">
                          <a href="#" @click="removeFromCart(index)"
                            ><i class="fa fa-trash text-danger"></i
                          ></a>
                        </td>
                      </tr>
                      <tr>
                        <td class="fw-bold px-1" colspan="2">TOTAL</td>
                        <td class="fw-bold px-1"></td>
                        <td class="fw-bold px-1 text-right">{{ calculateTotal() }}</td>
                        <td class="fw-bold px-1"></td>
                      </tr>
                      <tr>
                        <td class="text-bold px-1" colspan="2">Discount Percentage(%)</td>
                        <td class="text-bold px-1">
                          <input
                            type="number"
                            style="width: 50px"
                            v-model="discountPercentage"
                            @input="updateDiscount"
                          />
                        </td>
                        <td class="text-bold px-1 text-right">
                          {{ calculateDiscountAmount() }}
                        </td>
                        <td class="text-bold px-1"></td>
                      </tr>
                      <tr>
                        <td class="text-bold px-1" colspan="2">Vat Rate(%)</td>
                        <td class="text-bold px-1">
                          <input
                            type="number"
                            style="width: 50px"
                            v-model="vatPercentage"
                            @input="updateVat"
                          />
                        </td>
                        <td class="text-bold px-1 text-right">
                          {{ calculateVatAmount() }}
                        </td>
                        <td class="text-bold px-1"></td>
                      </tr>
                      <tr>
                        <td class="fw-bold px-1" colspan="2">NET TOTAL</td>
                        <td class="fw-bold px-1"></td>
                        <td class="fw-bold px-1 text-right">{{ calculateNetTotal() }}</td>
                        <td class="fw-bold px-1"></td>
                      </tr>
                      <tr>
                        <td class="fw-bold px-1" colspan="2">Pay Amount</td>
                        <td class="fw-bold px-1"></td>
                        <td class="fw-bold px-1 text-right">
                          <input
                            type="number"
                            style="width: 100px"
                            class="text-right form-control-sm"
                            v-model="payAmount"
                            @input="updatePayAmount"
                          />
                        </td>
                        <td class="fw-bold px-1"></td>
                      </tr>
                      <tr>
                        <td class="fw-bold px-1" colspan="2">Due Amount</td>
                        <td class="fw-bold px-1"></td>
                        <td class="fw-bold px-1 text-right">{{ dueAmount() }}</td>
                        <td class="fw-bold px-1"></td>
                      </tr>
                   
                    </tbody>
                    <tbody v-else>
                      <tr>
                        <td colspan="5" class="py-3 text-center">Cart Empty</td>
                      </tr>
                    </tbody>
                  </table>
                  <div class="input-group mb-2 row mx-0 px-0">
                    <div class="input-group-prepend px-0 col-md-4 mx-0">
                      <label
                        class="input-group-text col-md-12"
                        for="inputGroupSelect01"
                        title=""
                        >Paid By
                        <div class="text-danger"></div
                      ></label>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      autocomplete="off"
                      placeholder=""
                      name="paid_by"
                      v-model="form.paid_by"
                      :class="{ 'is-invalid': form.errors.has('paid_by') }"
                    />
                    <HasError :form="form" field="paid_by" />
                  </div>
                  <div class="input-group mb-2 row mx-0 px-0">
                    <div class="input-group-prepend px-0 col-md-4 mx-0">
                      <label
                        class="input-group-text col-md-12"
                        for="inputGroupSelect01"
                        title=""
                        >Attach File (If any)
                        <div class="text-danger"></div
                      ></label>
                    </div>
                    <input
                      type="file"
                      class="form-control"
                      autocomplete="off"
                      placeholder=""
                      name="file"
                      :class="{ 'is-invalid': form.errors.has('file') }"
                    />
                    <HasError :form="form" field="file" />
                  </div>
                  <div class="input-group mb-2 row mx-0 px-0">
                    <div class="input-group-prepend px-0 col-md-4 mx-0">
                      <label
                        class="input-group-text col-md-12"
                        for="inputGroupSelect01"
                        title=""
                        >Purchase Note:
                        <div class="text-danger"></div
                      ></label>
                    </div>
                    <textarea name="" id="" cols="30" rows="2" class="form-control"></textarea> 
                  </div>
                  <div class="form-group mt-2">
                    <!-- <div v-if="form.progress">Progress: {{ form.progress.percentage }}%</div> -->

                    <save-button v-if="isNew" :is-submitting="isSubmitting"></save-button>

                    <!-- <reset-button @reset-data="resetData" /> -->
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script type="text/javascript">
import { mapActions } from "vuex";
export default {
  data() {
    return {
      isSubmitting: false,
      isLoading: false,
      imageUrl: null,
      purchase: false,
      publicPath: window.publicPath,
      cartItems: [],
      discountPercentage: 0, // Initialize with no discount
      vatPercentage: 0, // Initialize with no discount
      payAmount: 0,
      paginator: {
        totalRecords: 0,
        from: 0,
        to: 0,
        current_page: "",
        per_page: "",
      },
      suppliers: [],
      books: {
        type: Object,
        default: null,
      },
      form: new Form({
        title: "",
        isbn: "",
        genre: "",
        price: "",
        supplier_id: "",
        category_id: "",
        sub_category_id: "",
        photo: null,
        buying_discount_percentage: "",
        selling_discount_percentage: "",
        buying_vat_percentage: "",
        selling_vat_percentage: "",
        publication_year: "",
      }),
      params: {
        paginate: 6,
        id: "",
        title: "",
        supplier_id: "",
        author_id: "",
        category_id: "",
        sub_category_id: "",
        sort_field: "created_at",
        sort_direction: "desc",
      },
      search: "",
    };
  },
  watch: {
    params: {
      handler() {
        this.getBooks();
      },
      deep: true,
    },
    search(val, old) {
      if (val.length >= 3 || old.length >= 3) {
        this.getBooks();
      }
    },
  },
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
    categories() {
      return this.$store.state.categories;
    },
  },
  mounted() {
    this.filterFields = { ...this.params };
    this.getBooks();
  },
  async created() {
    this.fetchCategories();
    this.suppliers = this.$store.getters.getSuppliers;
    if (this.suppliers.length == 0) {
      const response = await axios.get("/api/get-publishers");
      this.suppliers = response.data;
    }
    if (!this.isNew) {
      const response = await axios.get(`/api/purchases/${this.$route.params.id}`);

      // console.log(response.data);

      this.form.title = response.data.title;
      this.form.isbn = response.data.isbn;
      this.form.photo = response.data.photo;
      this.form.author_id = response.data.author_id;
      this.form.publisher_id = response.data.publisher_id;
      this.form.category_id = response.data.category_id;
      if (response.data.category_id) {
        this.getSubCategories();
      }
      this.form.sub_category_id = response.data.sub_category_id
        ? response.data.sub_category_id
        : "";
      this.form.price = response.data.price;
      this.form.publication_year = response.data.publication_year;
      this.form.buying_discount_percentage = response.data.buying_discount_percentage;
      this.form.buying_vat_percentage = response.data.buying_vat_percentage;
      this.form.selling_discount_percentage = response.data.selling_discount_percentage;
      this.form.selling_vat_percentage = response.data.selling_vat_percentage;
      this.form.genere = response.data.genere;
      this.imageUrl =
        `${window.publicPath}assets/img/purchase/thumbnail/` + response.data.photo;
      this.purchase = true;
    }
  },
  methods: {
    ...mapActions(["fetchCategories"]),
    async getBooks(page = 1) {
      this.isLoading = true;
      await axios
        .get("/api/books", {
          params: {
            page,
            search: this.search.length >= 3 ? this.search : "",
            ...this.params,
          },
        })
        .then((response) => {
          // console.log(response);
          this.isLoading = false;
          this.books = response.data;
          this.paginator.totalRecords = response.data.total;
          // if (response.data.total <= 0) {
          //   document.querySelector(".loading-section").innerText = "No Record Found!.";
          // }
          this.paginator.from = response.data.from;
          this.paginator.to = response.data.to;
          this.paginator.current_page = response.data.current_page;
          this.paginator.per_page = response.data.per_page;
          this.isRefreshing = false;
        })
        .catch((error) => {
          this.isLoading = false;
          document.querySelector(".loading-section").innerText =
            error.response.data.error;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    getCatWiseBook(categoryId) {
      this.params.category_id = categoryId;
    },
    addToCart(book) {
      const cartItem = this.cartItems.find((item) => item.id === book.id);
      if (cartItem) {
        Notification.success(`Item '${cartItem.title}' Qty. has been updated!`);
        cartItem.quantity++; // If the product already exists, increment its quantity
      } else {
        Notification.success(`Item Added!`);

        document.querySelector(`.addToCart${book.id}`).innerHTML =
          '<i class="fa fa-check"></i> Added';
        book.quantity = 1;
        this.cartItems.push(book);
      }
      // this.calculateTotal();
    },
    removeFromCart(index) {
      // Find the index of the item in the cart array
      // const index = this.cartItems.findIndex(item => item.id === itemId);

      if (index !== -1) {
        document.querySelector(`.addToCart${this.cartItems[index].id}`).innerHTML =
          '<i class="fa fa-plus"></i> Add To Cart';
        // Remove the item from the cart array
        this.cartItems.splice(index, 1);
        Notification.success(`Item Removed!`);
        // this.calculateTotal();
      }
    },
    updatePrice(index) {
      // Ensure price is a positive number
      if (this.cartItems[index].price < 0) {
        this.cartItems[index].price = 0;
      }
    },
    updateQuantity(index) {
      // Ensure quantity is a positive number
      if (this.cartItems[index].quantity < 0) {
        this.cartItems[index].quantity = 0;
      }
    },
    updateDiscount() {
      // Ensure discountPercentage is within a valid range (e.g., between 0 and 100)
      if (this.discountPercentage < 0) {
        this.discountPercentage = 0;
      } else if (this.discountPercentage > 100) {
        this.discountPercentage = 100;
      }
    },
    updateVat() {
      // Ensure vatPercentage is within a valid range (e.g., between 0 and 100)
      if (this.vatPercentage < 0) {
        this.vatPercentage = 0;
      } else if (this.vatPercentage > 100) {
        this.vatPercentage = 100;
      }
    },
    updatePayAmount() {
      const netTotal = this.calculateNetTotal();
      if (this.payAmount < 0) {
        this.payAmount = 0;
      } else if (this.payAmount > netTotal) {
        this.payAmount = netTotal;
      }
    },
    calculateDiscountAmount() {
      const totalBeforeDiscount = this.calculateTotal();
      const discountAmount = (totalBeforeDiscount * this.discountPercentage) / 100;
      // const totalAfterDiscount = totalBeforeDiscount - discountAmount;
      return discountAmount.toFixed(2);
    },
    calculateVatAmount() {
      const totalBeforeDiscount = this.calculateTotal();
      const vatAmount = (totalBeforeDiscount * this.vatPercentage) / 100;
      return vatAmount.toFixed(2);
    },
    calculateTotal() {
      return this.cartItems.reduce((total, item) => {
        const res = total + item.price * item.quantity;
        // this.form.total_amount = res;
        return res;
      }, 0);
    },
    calculateNetTotal() {
      const totalBeforeDiscount = this.calculateTotal();
      const discountAmount = (totalBeforeDiscount * this.discountPercentage) / 100;
      const totalAfterDiscount = totalBeforeDiscount - discountAmount;
      const vatAmount = (totalAfterDiscount * this.vatPercentage) / 100;
      const totalWithVAT = totalAfterDiscount + vatAmount;
      return totalWithVAT.toFixed(2);
    },
    dueAmount() {
      const netAmount = this.calculateNetTotal();
      const dueAmount = netAmount - this.payAmount;
      return dueAmount.toFixed(2);
    },
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/purchases", this.form)
          .then(() => {
            this.$router.push({ name: "purchases" });
            Notification.success(`Create purchase ${this.form.name} successfully!`);
          })
          .catch((error) => {
            // console.log(error);
            if (error.response.status === 422) {
              this.errors = error.response.data.errors;
              Notification.error("Validation Errors!");
            } else if (error.response.status === 401) {
              // statusText = "Unpurchaseized";
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
            .post(`/api/purchases/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("purchase info updated");
              this.$router.push("/purchases");
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
    getSubCategories() {
      axios
        .get("/api/get-category-wise-sub-categories", {
          params: {
            category_id: this.form.category_id,
          },
        })
        .then((response) => {
          this.sub_categories = response.data;
        });
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
