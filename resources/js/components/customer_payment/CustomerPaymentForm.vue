<template>
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div class="card shadow-sm my-2">
        <div class="card-header py-2 my-bg-success">
          <h3 class="text-white-900" v-if="isNew">
            <i class="fa fa-plus"></i> Add Customer Payment
          </h3>
          <h3 class="text-white-900" v-else>
            <i class="fa fa-pencil"></i> Edit Customer Payment
          </h3>
          <p class="text-white m-0">
            ফরমের লাল তারকা (<span class="text-danger">*</span>) চিহ্নিত ঘরগুলো অবশ্যই
            পূরণ করুন। অন্যান্য ঘরগুলো পূরণ ঐচ্ছিক।
          </p>
        </div>
        <div class="card-body p-3">
          <div class="form">
            <div v-if="!isNew && !customer_payment">
              <!-- <LoadingSpinner /> -->
            </div>
            <div class="py-2">
              <AlertError :form="form" />
              <form
                id="form"
                class="customer_payment"
                enctype="multipart/form-data"
                @submit.prevent="submitForm"
                @keydown="form.onKeydown($event)"
              >
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-4 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Payment Date
                      <div class="text-danger">*</div></label
                    >
                  </div>
                  <input
                    type="text"
                    class="form-control datecalender"
                    id="datecalander"
                    autocomplete="off"
                    placeholder="Choose payment date"
                    name="payment_date"
                    v-model="form.payment_date"
                    @input="clearError('payment_date')"
                    :class="{ 'is-invalid': form.errors.has('payment_date') }"
                  />
                  <HasError :form="form" field="payment_date" />
                </div>
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-4 mx-0">
                    <label class="input-group-text col-md-12" for="inputGroupSelect01"
                      >Customer
                      <div class="text-danger">*</div></label
                    >
                  </div>
                  <select
                    class="custom-select mx-0 pe-0"
                    v-model="form.customer_id"
                    @change="getBalance"
                    :class="{ 'is-invalid': form.errors.has('customer_id') }"
                  >
                    <option value="" disabled selected>Choose...</option>
                    <option
                      :value="customer.id"
                      v-for="customer in customers"
                      :key="customer.id"
                    >
                      {{ customer.customer_name }}
                    </option>
                  </select>
                  <HasError :form="form" field="customer_id" />
                </div>

                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-5 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Balance <span class="ms-2" v-if="isLoading">
                      <LoadingSpinner />
                    </span></label
                    >
                    
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    readonly
                    v-model="due_balance"
                  />
                </div>
                <!-- <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-5 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Discount Percentage (%)
                      <div class="text-danger"></div
                    ></label>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    placeholder="Choose customer_payment date"
                    name="payment_amount"
                    v-model="form.payment_amount"
                    @input="clearError('payment_amount')"
                    :class="{ 'is-invalid': form.errors.has('payment_amount') }"
                  />
                  <HasError :form="form" field="payment_amount" />
                </div>
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-5 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Discount Amount
                      <div class="text-danger"></div
                    ></label>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    placeholder="Choose customer_payment date"
                    name="payment_amount"
                    v-model="form.payment_amount"
                    @input="clearError('payment_amount')"
                    :class="{ 'is-invalid': form.errors.has('payment_amount') }"
                  />
                  <HasError :form="form" field="payment_amount" />
                </div>
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-5 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Balance After Discount
                      <div class="text-danger"></div
                    ></label>
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    placeholder="Choose customer_payment date"
                    name="payment_amount"
                    v-model="form.payment_amount"
                    @input="clearError('payment_amount')"
                    :class="{ 'is-invalid': form.errors.has('payment_amount') }"
                  />
                  <HasError :form="form" field="payment_amount" />
                </div> -->
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-4 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Payment Amount
                      <div class="text-danger">*</div></label
                    >
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    placeholder="Choose customer_payment date"
                    name="payment_amount"
                    v-model="form.payment_amount"
                    @input="clearError('payment_amount')"
                    :class="{ 'is-invalid': form.errors.has('payment_amount') }"
                  />
                  <HasError :form="form" field="payment_amount" />
                </div>

                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-4 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Paid By 
                      <div class="text-danger"> *</div></label
                    >
                  </div>
                  <input
                    type="text"
                    class="form-control"
                    autocomplete="off"
                    placeholder=""
                    name="paid_by"
                    v-model="form.paid_by"
                    @input="clearError('paid_by')"
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
                      >Payment Method
                      <div class="text-danger">*</div></label
                    >
                  </div>
                  <select
                    name="payment_method"
                    v-model="form.payment_method"
                    @change="clearError('payment_method')"
                    class="form-select"
                    :class="{ 'is-invalid': form.errors.has('payment_method') }"
                  >
                    <option value="" selected>--select payment method--</option>
                    <option :value="payment_method.id" v-for="payment_method in payment_methods">{{ payment_method.name }}</option>
                   
                  </select>
                  <HasError :form="form" field="payment_method" />
                </div>
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-4 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Payment Description:
                      <div class="text-danger"></div
                    ></label>
                  </div>
                  <textarea
                    name=""
                    id=""
                    cols="30"
                    rows="2"
                    class="form-control"
                    v-model="form.payment_description"
                    >{{ form.payment_description }}</textarea
                  >
                </div>
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-4 mx-0">
                    <label
                      class="input-group-text col-md-12"
                      for="inputGroupSelect01"
                      title=""
                      >Sale invoice ID
                      <div class="text-danger"></div></label
                    >
                  </div>
                  <input
                    type="number"
                    class="form-control"
                    autocomplete="off"
                    placeholder=""
                    name="sale_id"
                    v-model="form.sale_id"
                    @input="clearError('sale_id')"
                    :class="{ 'is-invalid': form.errors.has('sale_id') }"
                  />
                  <HasError :form="form" field="sale_id" />
                </div>
                <div class="input-group mb-2 row mx-0 px-0">
                  <div class="input-group-prepend px-0 col-md-5 mx-0">
                    <label class="input-group-text col-md-12" for="inputGroupSelect01"
                      >Attach File (If any)
                      <div class="text-danger"></div
                    ></label>
                  </div>
                  <input
                    type="file"
                    class="form-control col-md-8"
                    placeholder="Choose..."
                    name="file"
                    @change="onFileChange"
                    @input="clearError('file')"
                    accept="image/*,application/pdf"
                    :class="{ 'is-invalid': form.errors.has('file') }"
                  />
                  <HasError :form="form" field="file" />
                  [Allow File type:jpeg,png,jpg,gif,svg,pdf & Max Size:2MB]
                </div>
                <div class="image-item">
                  <img
                    v-if="imageUrl && fileExtension != 'pdf'"
                    :src="imageUrl"
                    alt="Image Preview"
                    width="120"
                  />
                  <button
                    class="remove-button"
                    type="button"
                    v-if="imageUrl"
                    @click="removeSingleImage(image, key)"
                  >
                    x
                  </button>
                </div>
                <iframe
                  v-if="imageUrl && fileExtension == 'pdf'"
                  :src="imageUrl"
                  width="200"
                  height="200"
                ></iframe>

                <div class="form-group mt-2">
                  <!-- <div v-if="form.progress">Progress: {{ form.progress.percentage }}%</div> -->
                  <router-link
                    to="/customer_payments"
                    class="btn btn-lg btn-primary px-2 mx-1"
                    ><i class="fa fa-list"></i> Manage Customer Payment</router-link
                  >
                  <save-button v-if="isNew" :is-submitting="isSubmitting"></save-button>
                  <save-changes-button
                    v-else
                    :is-submitting="isSubmitting"
                  ></save-changes-button>
                  <!-- <reset-button @reset-data="resetData" /> -->
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
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

export default {
  data() {
    return {
      isSubmitting: false,
      isLoading: false,
      imageUrl: null,
      publicPath: window.publicPath,
      fileExtension: null,
      due_balance: 0,
      paginator: {
        totalRecords: 0,
        from: 0,
        to: 0,
        current_page: "",
        per_page: "",
      },
      customers: [],
      payment_methods: [],
      customer_payments: {
        type: Object,
        default: null,
      },
      form: new Form({
        sale_id: "",
        customer_id: "",
        payment_date: "",
        payment_amount: "",
        paid_by: "",
        payment_method: "",
        payment_description: "",
      }),
      params: {
        paginate: 6,
        id: "",
        title: "",
        category_id: "",
        sort_field: "created_at",
        sort_direction: "desc",
      },
      search: "",
    };
  },
  watch: {
    params: {
      handler() {},
      deep: true,
    },
  },
  computed: {
    isNew() {
      return !this.$route.path.includes("edit");
    },
  },
  mounted() {
    this.filterFields = { ...this.params };
    flatpickr("#datecalander", {
      dateFormat: "Y-m-d", // Customize the date format as needed
      // Add more Flatpickr options as needed
    });
  },
  async created() {
    this.customers = this.$store.getters.getCustomers;
    if (this.customers.length == 0) {
      const response = await axios.get("/api/get-customers");
      this.customers = response.data;
    }
    this.payment_methods = this.$store.getters.getPaymentMethods;
    if (this.payment_methods.length == 0) {
      const response = await axios.get("/api/get-payment-methods");
      this.payment_methods = response.data;
    }
    if (!this.isNew) {
      const response = await axios.get(`/api/customer-payments/${this.$route.params.id}`);

      this.customer_payment = true;
      this.form.customer_id = response.data.customer_payment.customer_id;
      this.form.payment_date = response.data.customer_payment.payment_date;

      this.form.file = response.data.customer_payment.file;
      this.cartItems = response.data.customer_payment_regular_details;
      this.courtesyCartItems = response.data.customer_payment_courtesy_details;
      this.payAmount = response.data.customer_payment.pay_amount;
      this.form.pay_amount = response.data.customer_payment.pay_amount;

      if (response.data.payment_details.length > 0) {
        this.form.payment_method = response.data.payment_details[0].payment_method;
        this.form.paid_by = response.data.payment_details[0].paid_by;
        this.form.payment_description =
          response.data.payment_details[0].payment_description;
      }
      this.form.customer_payment_note =
        response.data.customer_payment.customer_payment_note;

      this.imageUrl =
        `${window.publicPath}assets/img/customer_payment/` +
        response.data.customer_payment.file;

      const fileName = response.data.customer_payment.file;
      if (fileName) {
        const parts = fileName.split(".");
        if (parts.length > 1) {
          // Get the last part as the file extension
          this.fileExtension = parts[parts.length - 1].toLowerCase();
        }
      }
    } else {
      this.resetData();
    }
  },
  methods: {
    onFileChange(e) {
      let selectedFile = e.target.files[0];
      if (selectedFile) {
        const allowedFileTypes = [
          "image/jpeg",
          "image/jpg",
          "image/png",
          "image/gif",
          "image/svg",
          "application/pdf",
        ];
        if (selectedFile.size > 2048 * 1024) {
          // Change this to your desired maximum file size in bytes
          this.form.errors.set("file", "File size exceeds the maximum allowed size.");
          Notification.error("File size exceeds the maximum allowed size.");
        }
        if (!allowedFileTypes.includes(selectedFile.type)) {
          this.form.errors.set(
            "file",
            " File type is not supported. Please choose a valid file type."
          );
          Notification.error(
            " File type is not supported. Please choose a valid file type."
          );
        }
        this.form.file = selectedFile;
        if (selectedFile.type == "application/pdf") {
          this.fileExtension = "pdf";
        }

        const reader = new FileReader();

        reader.onload = (e) => {
          this.imageUrl = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
      }
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.form.file = null;
    },
    async getBalance() {
      this.form.errors.clear("customer_id");
      this.isLoading = true;
      const customerId = this.form.customer_id;
      const response = await axios.get(`/api/get-customer-balance/${customerId}`);
      this.isLoading = false;
      this.due_balance = response.data.balance;
    },
    updatePayAmount() {
      const netTotal = this.calculateNetTotal();
      if (this.payAmount < 0) {
        this.payAmount = 0;
      } else if (this.payAmount > netTotal) {
        this.payAmount = netTotal;
      }
    },
    calculateTotal() {
      return this.cartItems.reduce((total, item) => {
        const res = total + item.price * item.quantity;
        this.form.payment_amount = res;
        return res;
      }, 0);
    },
    async submitForm() {
      this.isSubmitting = true;
      // console.log(this.form.cartItems);
      if (this.isNew) {
        await this.form
          .post("/api/customer-payments", {
            params: {
              ...this.form,
            },
          })
          .then(() => {
            this.$router.push({ name: "customer_payments" });
            Notification.success(`Create customer_payment successfully!`);
          })
          .catch((error) => {
            // console.log(error);
            if (error.response.status === 422) {
              this.errors = error.response.data.errors;
              Notification.error("Validation Errors!");
            } else if (error.response.status === 401) {
              // statusText = "Uncustomer_paymentized";
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
            .post(`/api/customer-payments/${this.$route.params.id}`, {
              params: {
                cart_items: this.cartItems,
                courtesy_cart_items: this.courtesyCartItems,
                ...this.form,
              },
            })
            .then((response) => {
              Notification.success("customer_payment info updated");
              this.$router.push("/customer_payments");
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
    clearError(fieldName) {
      this.form.errors.clear(fieldName);
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
