<template>
  <div
    class="modal fade"
    id="recordModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="recordModalLabel"
  >
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="recordModalLabel">Publisher Details</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Display record details here -->
         
          <div v-if="record">
            <h5>publisher Name: {{ record.publisher_name }}</h5>
            <div class="row">
              <div class="col-md-4">
                <img
                  :src="`${publicPath}assets/img/publisher/`+record.publisher_photo"
                  alt="Cutomer Photo"
                  width="250"
                  class="img-fluid"
                />
              </div>
              <div class="col-md-8">
                <table class="table">
                  <tr>
                    <td>Phone</td>
                    <td>{{ record.publisher_phone }}</td>
                  </tr>
                  <tr>
                    <td>Email</td>
                    <td>{{ record.publisher_email }}</td>
                  </tr>
                  <tr>
                    <td>Country</td>
                    <td>{{ record.publisher_country }}</td>
                  </tr>
                  <tr>
                    <td>Address</td>
                    <td>{{ record.publisher_address }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <div v-else>
            <LoadingSpinner/>
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
    data(){
        return{            
            publicPath:window.publicPath,
        }
    },
    props: {
        record:{},
    },
    created(){
       
    },
}
</script>
