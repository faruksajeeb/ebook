<template>
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div class="card mt-3">
        <div class="card-header">
          <h3 class="text-center fw-bold">
            <i class="fa fa-chart-pie"></i> Customer Payment Report
          </h3>
        </div>
        <form id="form" @submit.prevent="submitForm">
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
                :class="{ 'is-invalid': form.errors.has('customer_id') }"
              >
                <option value="">--select customer--</option>
                <option :value="customer.id" v-for="customer in customers">
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
              <button
                type="submit"
                @click="getReport('view')"
                :disabled="isSubmitting"
                class="btn btn-lg btn-secondary mx-1 export-btn-view"
              >
                <div><i class="fa fa-save"> </i> View</div>
              </button>
              <button
                @click="getReport('excel')"
                type="submit"
                class="m-1 btn btn-lg my-btn-success export-btn-excel"
              >
                Excel Export
              </button>
              <button @click="getReport('pdf')"
                type="submit" class="m-1 btn btn-lg my-btn-danger export-btn-pdf">PDF Export</button>
              <button @click="getReport('print')"
                type="submit" class="m-1 btn btn-lg my-btn-primary export-btn-print">
                <i class="fa fa-print"></i> Print
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="row" v-if="payments.length > 0">
    <div class="col-md-10 offset-md-1 border my-3 rounded-sm">
      <table class="table">
        <tr>
          <td colspan="5"><h3>Customer Payment</h3></td>
        </tr>
        <tr>
          <td class="fw-bold">Customer Name:</td>
          <td class="fw-bold">{{ customer.customer_name }}</td>
          <td class="fw-bold">Customer Phone:</td>
          <td class="fw-bold" colspan="2">{{ customer.customer_phone }}</td>
        </tr>
        <tr>
          <td class="fw-bold">Customer Address:</td>
          <td class="fw-bold">{{ customer.customer_address }}</td>
          <td class="fw-bold">Customer Phone:</td>
          <td class="fw-bold" colspan="2"></td>
        </tr>
        <tr class="bg-secondary">
          <td class="fw-bold text-white">Payment Date</td>
          <td class="fw-bold text-white">Payment Method</td>
          <td class="fw-bold text-white">Payment Description</td>
          <td class="fw-bold text-white">Paid By</td>
          <td class="fw-bold text-white">Amount</td>
        </tr>
        <tr v-for="payment in payments">
          <td>{{ payment.payment_date }}</td>
          <td>{{ payment.payment_method.name }}</td>
          <td>{{ payment.payment_description }}</td>
          <td>{{ payment.paid_by }}</td>
          <td class="text-right">{{ payment.payment_amount }}</td>
        </tr>
        <tr>
          <td class="fw-bold text-left" colspan="4">Payment Total</td>
          <td class="fw-bold text-right">{{ calculateTotalPayment() }}</td>
        </tr>
      </table>
    </div>
  </div>
  <div v-else class="row my-3 text-center text-danger">
    <loader v-if="isSubmitting"></loader>
    <NoRecordFound v-else v-show="isResponsed" />
  </div>
</template>
<script>
import axios from "axios";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

export default {
  data() {
    return {
      isSubmitting: false,
      isResponsed: false,
      customer: {},
      customers: [],
      payments: [],

      form: new Form({
        customer_id: "",
        date_range: "",
        btn_type: null,
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
    getReport(btnType) {
      this.form.btn_type = btnType;
    },
    async submitForm() {
      this.isSubmitting = true;
      let resType = "";
      if(this.form.btn_type == "view"){
        let loader =
        '<span class="spinner-border spinner-border-sm" customer_payment="status" aria-hidden="true" ></span> Generating...';
      document.querySelector(".export-btn-view").innerHTML = loader;
      }else if (this.form.btn_type == "excel") {
        let loader =
        '<span class="spinner-border spinner-border-sm" customer_payment="status" aria-hidden="true" ></span> Exporting...';
      document.querySelector(".export-btn-excel").innerHTML = loader;
        resType = "arraybuffer";
      } else if (this.form.btn_type == "pdf") {
        let loader =
        '<span class="spinner-border spinner-border-sm" customer_payment="status" aria-hidden="true" ></span> Exporting...PDF';
      document.querySelector(".export-btn-pdf").innerHTML = loader;
        resType = "blob";
      }else if(this.form.btn_type == "print"){
        let loader =
        '<span class="spinner-border spinner-border-sm" customer_payment="status" aria-hidden="true" ></span> Printing...';
      document.querySelector(".export-btn-print").innerHTML = loader;
      }
      await this.form
        .post("/api/report/customer-payment", {
          params: {
            ...this.form,
          },
          responseType: resType,
        })
        .then((response) => {
          this.isResponsed = true;
          if (response.data.report_type == "view") {
            this.customer = response.data.customer;
            this.payments = response.data.customer_payments;
          } else {
            if (this.form.btn_type == "excel") {
              document.querySelector(".export-btn-excel").innerText = "Export to Excel";
              Notification.success("Exported Successfully");
              var fileURL = window.URL.createObjectURL(new Blob([response.data]));
              var fileLink = document.createElement("a");
              fileLink.href = fileURL;
              fileLink.setAttribute("download", "customer-payment-report.xlsx");
              document.body.appendChild(fileLink);
              fileLink.click();
            } else if (this.form.btn_type == "pdf") {
              document.querySelector(".export-btn-pdf").innerText = "Export PDF";
              Notification.success("Exported Successfully");
              var fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
              );
              var fileLink = document.createElement("a");
              fileLink.href = fileURL;
              fileLink.setAttribute("download", "customer_payment_list.pdf");
              document.body.appendChild(fileLink);
              fileLink.click();
            } else if (this.form.btn_type == "print") {
              document.querySelector(".export-btn-print").innerText = "Print";
            }
          }
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
            Notification.error("Validation Errors!");
          } else if (error.response.status === 401) {
            this.errors = {};
            Notification.error(error.response.data.error);
          } else {
            Notification.error(error.response.statusText);
          }
        })
        .finally(() => {
          this.isSubmitting = false;
        });
    },
    calculateTotalPayment() {
      return this.payments.reduce(
        (total, payment) => total + Number(payment.payment_amount),
        0
      );
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
    exportExcel() {
      let loader =
        '<span class="spinner-border spinner-border-sm" customer_payment="status" aria-hidden="true" ></span> Exporting...';
      document.querySelector(".export-excel-btn").innerHTML = loader;
      try {
        axios
          // .get("/api/products-export")
          .post("/api/report/customer-payment-export-excel", {
            ...this.form,
            responseType: "arraybuffer",
          })
          .then((response) => {
            if (response.status == 200) {
              document.querySelector(".export-excel-btn").innerText = "Export to Excel";
              Notification.success("Exported Successfully");
              var fileURL = window.URL.createObjectURL(new Blob([response.data]));
              var fileLink = document.createElement("a");
              fileLink.href = fileURL;
              fileLink.setAttribute("download", "customer_payment_list.xlsx");
              document.body.appendChild(fileLink);
              fileLink.click();
            } else {
              this.$swal("ERROR!", `${response.data.message}`, "error");
            }
          });
      } catch (error) {
        this.$swal("ERROR!", `${error}`, "error");
        // console.error(error);
      }
    },
    exportPdf() {
      let loader =
        '<span class="spinner-border spinner-border-sm" customer_payment="status" aria-hidden="true" ></span>  Exporting...PDF';
      document.querySelector(".export-btn-pdf").innerHTML = loader;
      axios
        .get("/api/customer_payment-export-pdf", { responseType: "blob" })
        .then((response) => {
          document.querySelector(".export-btn-pdf").innerText = "Export PDF";
          Notification.success("Exported Successfully");
          var fileURL = window.URL.createObjectURL(
            new Blob([response.data], { type: "application/pdf" })
          );
          var fileLink = document.createElement("a");
          fileLink.href = fileURL;
          fileLink.setAttribute("download", "customer_payment_list.pdf");
          document.body.appendChild(fileLink);
          fileLink.click();
        });
    },
  },
};
</script>
<style></style>
