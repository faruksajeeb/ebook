<template>
  ijihihijkhjk
  {{ record.title }}
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
          <h3 class="modal-title" id="recordModalLabel">Book Details</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Display record details here -->
         
          <div v-if="record.author">
            <h4>Book Title: {{ record.title }}</h4>
            <div class="row">
              <div class="col-md-4">
                <img
                  :src="`${publicPath}assets/img/book/`+record.photo"
                  alt="Cutomer Photo"
                  width="250"
                  class="img-fluid"
                />
              </div>
              <div class="col-md-8">
                <table class="table">
                  <tr>
                    <td>ISBN</td>
                    <td>{{ record.isbn }}</td>
                  </tr>
                  <tr>
                    <td>Author</td>
                    <td>{{ record.author.author_name }}</td>
                  </tr>
                  <tr>
                    <td>Publisher</td>
                    <td>{{ record.publisher.publisher_name }}</td>
                  </tr>
                  <tr>
                    <td>Category</td>
                    <td>{{ record.category.category_name }}</td>
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
