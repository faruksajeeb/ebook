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
                </table>
                <label for="">Attached File:</label>
                <br />

                <!-- <iframe v-if="imageUrl && form.attach_file.type=='application/pdf'"  :src="imageUrl" width="200" height="200"></iframe> -->
                <!-- <embed :src="`${publicPath}assets/img/purchase/`+record.purchase.attach_file" type="application/pdf" width="100%" height="600px"> -->
                <iframe
                  v-if="record.purchase.attach_file != null && fileExtension == 'pdf'"
                  :src="`${publicPath}assets/img/purchase/` + record.purchase.attach_file"
                  width="100%"
                  height="400"
                ></iframe>
                <img
                  v-else
                  :src="`${publicPath}assets/img/purchase/` + record.purchase.attach_file"
                  alt="Purchase File"
                  width="250"
                  class="img-fluid"
                />
              </div>
              <div class="col-md-7">
                <h4>Details</h4>
                <table class="table">
                  <thead>
                    <th>SL No.</th>
                    <th>Book Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th class="text-right">Sub Total</th>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in record.purchase_details">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.book.title }}</td>
                      <td class="text-right">{{ item.unit_price.toFixed(2) }}</td>
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
                    <tr>
                      <td>Paid By:</td>
                      <td colspan="4">{{ record.purchase.paid_by }}</td>
                    </tr>
                    <tr>
                      <td>Purchase Note:</td>
                      <td colspan="4">{{ record.purchase.purchase_note }}</td>
                    </tr>
                  </tfoot>
                </table>
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
    };
  },
  created() {},
  props: {
    record: {},
    fileExtension: String,
  },
  created() {},
};
</script>
