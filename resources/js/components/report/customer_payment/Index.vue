<template>
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div class="card mt-3">
        <div class="card-header">
          <h3 class="text-center fw-bold">
            <i class="fa fa-chart-pie"></i> Customer Payment Report
          </h3>
        </div>
        <form @submit.prevent="submitForm">
          <div class="card-body">
            <div class="form-group">
              <label for=""
                >Customer <span class="my-text-danger fw-bold"> *</span></label
              >
              <select
                name=""
                id=""
                class="form-select"
                v-model="form.customer_id"
                @change="selectCustomer"
              >
                <option value="">--select customer--</option>
                <option value="{{customer.id}}" v-for="customer in customers">
                  {{ customer.customer_name }}
                </option>
              </select>
              <HasError :form="form" field="customer_id" />
            </div>
            <div class="form-group">
              <label for=""
                >Date Range <span class="my-text-danger fw-bold"> *</span></label
              >
              <input
                type="text"
                v-model="form.date_range"
                placeholder="Search By Date Range"
                class="form-control datecalander"
                @input="clearError('date_range')"
                :class="{ 'is-invalid': form.errors.has('date_range') }"
              />
              <HasError :form="form" field="date_range" />
            </div>
          </div>
          <div class="card-footer">
            <div class="form-group text-center">
              <button class="m-1 btn btn-lg btn-secondary" @click="viewReport">
                <i class="fa fa-eye"></i> View
              </button>
              <button class="m-1 btn btn-lg my-btn-success">Excel Export</button>
              <button class="m-1 btn btn-lg my-btn-danger">PDF Export</button>
              <button class="m-1 btn btn-lg my-btn-primary">
                <i class="fa fa-print"></i> Print
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

export default {
  data() {
    return {
      customers: [],
      form: new Form({
        customer_id: "",
        date_range: "",
      }),
    };
  },
  async created() {
    this.customers = this.$store.getters.getCustomers;
    if (this.customers.length == 0) {
      const response = await axios.get("/api/get-customers");
      this.customers = response.data;
    }
  },
  mounted() {
    flatpickr(".datecalander", {
      mode: "range",
      dateFormat: "Y-m-d", // Customize the date format as needed
      // Add more Flatpickr options as needed
    });
  },
  methods: {
    selectCustomer() {
      this.form.errors.clear("customer_id");
    },
    async submitForm() {
      await this.form
        .post("", {
          params: {
            ...this.form,
          },
        })
        .then((response) => {})
        .error((error) => {})
        .finally(() => {});
    },
    async viewReport() {
      await this.form
        .post("", {
          params: {
            ...this.form,
          },
        })
        .then((response) => {})
        .error((error) => {})
        .finally(() => {});
    },
    async printReport() {
      await this.form
        .post("", {
          params: {
            ...this.form,
          },
        })
        .then((response) => {})
        .error((error) => {})
        .finally(() => {});
    },
    async excelExortReport() {
      await this.form
        .post("", {
          params: {
            ...this.form,
          },
        })
        .then((response) => {})
        .error((error) => {})
        .finally(() => {});
    },
    async pdfExportReport() {
      await this.form
        .post("", {
          params: {
            ...this.form,
          },
        })
        .then((response) => {})
        .error((error) => {})
        .finally(() => {});
    },
    clearError(fieldName) {
      this.form.errors.clear(fieldName);
    },
  },
};
</script>
<style></style>
