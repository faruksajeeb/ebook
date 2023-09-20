<template>
  <div
    class="modal fade"
    id="recordModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="recordModalLabel"
  >
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="recordModalLabel">Purchase Details</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Display record details here -->

          <div v-if="record.purchase">
            <div class="row">
              <div class="col-md-5">
                <h4>Purchase ID: {{ record.purchase.id }}</h4>
                <table class="table">
                  <tr>
                    <td>Purchase Date</td>
                    <td>{{ record.purchase.purchase_date }}</td>
                  </tr>
                  <tr>
                    <td>Supplier</td>
                    <td>{{ record.purchase.supplier.supplier_name }}</td>
                  </tr>
                  <tr>
                    <td>Paid By:</td>
                    <td>{{ record.purchase.paid_by }}</td>
                  </tr>
                  <tr>
                    <td>Purchase Note:</td>
                    <td>{{ record.purchase.purchase_note }}</td>
                  </tr>
                </table>

                <fieldset class="reset">
                  <legend class="reset p-1">Attached File (If Any)</legend>
                  <iframe
                    v-if="record.purchase.attach_file != null && fileExtension == 'pdf'"
                    :src="
                      `${publicPath}assets/img/purchase/` + record.purchase.attach_file
                    "
                    width="100%"
                    height="400"
                  ></iframe>
                  <img
                    v-else
                    :src="
                      `${publicPath}assets/img/purchase/` + record.purchase.attach_file
                    "
                    alt="Purchase File"
                    width="250"
                    class="img-fluid"
                  />
                </fieldset>
              </div>
              <div class="col-md-7">
                <fieldset class="reset" v-if="record.purchase_regular_details.length > 0">
                  <legend class="reset h5 p-2 bg-success text-white">
                    Regular Item Details
                  </legend>
                  <table class="table">
                    <thead>
                      <th>SL No.</th>
                      <th>Book Name</th>
                      <th class="text-right">Unit Price</th>
                      <th class="text-center">Quantity</th>
                      <th class="text-right">Sub Total</th>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in record.purchase_regular_details"
                        :key="item.id"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.title }}</td>
                        <td class="text-right">{{ item.price.toFixed(2) }}</td>
                        <td class="text-center">{{ item.quantity }}</td>
                        <td class="text-right">{{ item.sub_total.toFixed(2) }}</td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td class="text-left fw-bold" colspan="4">Total</td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.total_amount.toFixed(2) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left fw-bold" colspan="3">Discount</td>
                        <td class="text-center fw-bold">
                          {{ record.purchase.discount_percentage }}%
                        </td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.discount_amount.toFixed(2) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left fw-bold" colspan="3">Vat</td>
                        <td class="text-center fw-bold">
                          {{ record.purchase.vat_percentage }}%
                        </td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.vat_amount.toFixed(2) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left fw-bold" colspan="4">Net Amount</td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.net_amount.toFixed(2) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left fw-bold" colspan="4">Pay Amount</td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.pay_amount.toFixed(2) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left fw-bold" colspan="4">Due Amount</td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.due_amount.toFixed(2) }}
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                  <div v-if="record.payment_details.length>0"  class="accordion" id="accordionExample">
                    <div class="accordion-item">
                      <h2 class="accordion-header" id="headingTwo">
                        <button
                          class="accordion-button collapsed"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#collapseTwo"
                          aria-expanded="false"
                          aria-controls="collapseTwo"
                        >
                          Payment Details
                        </button>
                      </h2>
                      <div
                        id="collapseTwo"
                        class="accordion-collapse collapse"
                        aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample"
                      >
                        <div class="accordion-body">
                          <table class="table table-sm">
                            <tr>
                              <td>Sl</td>
                              <td>Payment Date</td>
                              <td class="text-center">Payment Amount</td>
                              <td>Paid By</td>
                              <td>Payment Description</td>
                            </tr>
                            <tr v-for="(payItem, index) in record.payment_details">
                              <td>{{ index + 1 }}</td>
                              <td>{{ payItem.payment_date }}</td>
                              <td class="text-center">{{ payItem.payment_amount }}</td>
                              <td>{{ payItem.paid_by }}</td>
                              <td>{{ payItem.payment_description }}</td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- <p>{{ displayedText }}</p>
                  <a href="#" class="text-btn" @click="expandText" v-if="isCollapsed"
                    >Show more</a
                  >
                  <a href="#" class="text-btn" @click="collapseText(100)" v-else
                    >Show less</a
                  > -->
                </fieldset>
                <fieldset
                  class="reset mt-3"
                  v-if="record.purchase_courtesy_details.length > 0"
                >
                  <legend class="reset h5 p-2 bg-danger text-white">
                    Courtesy Item Details (সৌজন্য সংখ্যা)
                  </legend>
                  <table class="table">
                    <thead>
                      <th>SL No.</th>
                      <th>Book Name</th>
                      <th class="text-right">Unit Price</th>
                      <th class="text-center">Quantity</th>
                      <th class="text-right">Sub Total</th>
                    </thead>
                    <tbody>
                      <tr v-for="(item, index) in record.purchase_courtesy_details">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.title }}</td>
                        <td class="text-right">{{ item.unit_price.toFixed(2) }}</td>
                        <td class="text-center">{{ item.courtesy_quantity }}</td>
                        <td class="text-right">{{ item.sub_total.toFixed(2) }}</td>
                      </tr>
                      <tr>
                        <td class="text-left fw-bold" colspan="4">Total</td>
                        <td class="text-right fw-bold">
                          {{ record.purchase.courtesy_total_amount }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </fieldset>
              </div>
            </div>
          </div>

          <div v-else>
            <LoadingSpinner />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      publicPath: window.publicPath,
      paragarph:
        "Vue.js is a popular JavaScript framework that allows developers to create dynamic and interactive user interfaces. With Vue.js, developers can build a wide range of UI elements, including the 'Show More/Show Less' feature. This feature is a common design pattern that allows users to expand or collapse content within a webpage. It is particularly useful for displaying long-form content, such as articles, product descriptions, and user reviews. In this article, we will explore how to implement the 'Show More/Show Less' feature in Vue.js",
      displayedText: "",
      isCollapsed: false,
    };
  },
  created() {},
  props: {
    record: {},
    fileExtension: String,
  },
  mounted() {
    this.collapseText(100);
  },
  methods: {
    collapseText(textSize) {
      this.displayedText = this.paragarph.slice(0, textSize);
      this.isCollapsed = !this.isCollapsed;
    },
    expandText() {
      this.displayedText = this.paragarph;
      this.isCollapsed = !this.isCollapsed;
    },
  },
};
</script>
