<template>
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card shadow-sm my-2">
        <div class="card-header py-2 my-bg-success">
          <h3 class="text-white-900" v-if="isNew"><i class="fa fa-plus"></i> Add Damange Item </h3>
          <h3 class="text-white-900" v-else><i class="fa fa-pencil"></i> Edit Damange Item</h3>
          <p class="text-white m-0">ফরমের লাল তারকা (<span class="text-danger">*</span>) চিহ্নিত ঘরগুলো অবশ্যই পূরণ করুন। অন্যান্য ঘরগুলো পূরণ ঐচ্ছিক।</p>
        </div>
        <div class="card-body p-3">
          <div class="form">
            <form
              id="form"
              class="book"
              enctype="multipart/form-data"
              @submit.prevent="submitForm"
              @keydown="form.onKeydown($event)"
            >
              <AlertError :form="form" />
              <div v-if="!isNew && !book">
                <LoadingSpinner />
              </div>
              <div class="row">
                
              </div>

              <hr />
              <div class="form-group">
                <!-- <div v-if="form.progress">Progress: {{ form.progress.percentage }}%</div> -->
                <router-link to="/damage-items" class="btn btn-lg btn-outline-primary"> &lt;&lt; Manage Damage Item </router-link>
                <save-button v-if="isNew" :is-submitting="isSubmitting"></save-button>
                <save-changes-button
                  v-else
                  :is-submitting="isSubmitting"
                ></save-changes-button>
                <reset-button @reset-data="resetData" />
              </div>
            </form>
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
      imageUrl: null,
      book: false,
      sub_categories: [],
      authors: [],
      publishers: [],
      form: new Form({
        title: "",
        isbn: "",
        genre: "",
        price: "",
        author_id: "",
        publisher_id: "",
        category_id: "",
        sub_category_id: "",
        photo: null,
        buying_discount_percentage: "",
        selling_discount_percentage: "",
        buying_vat_percentage: "",
        selling_vat_percentage: "",
        publication_year: "",
      }),
    };
  },
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
    this.authors = this.$store.getters.getAuthors;
    if (this.authors.length == 0) {
      const response = await axios.get("/api/get-authors");
      this.authors = response.data;
    }
    this.publishers = this.$store.getters.getPublishers;
    if (this.publishers.length == 0) {
      const response = await axios.get("/api/get-publishers");
      this.publishers = response.data;
    }
    if (!this.isNew) {
      const response = await axios.get(`/api/damage-items/${this.$route.params.id}`);

      // console.log(response.data);

      this.form.title = response.data.title;
      this.form.isbn = response.data.isbn;
      this.form.photo = response.data.photo;
      this.form.author_id = response.data.author_id;
      this.form.publisher_id = response.data.publisher_id;
      this.form.category_id = response.data.category_id;
      if(response.data.category_id){
        this.getSubCategories()
      }
      this.form.sub_category_id = response.data.sub_category_id ? response.data.sub_category_id : "";
      this.form.price = response.data.price;
      this.form.publication_year = response.data.publication_year;
      this.form.buying_discount_percentage = response.data.buying_discount_percentage;
      this.form.buying_vat_percentage = response.data.buying_vat_percentage;
      this.form.selling_discount_percentage = response.data.selling_discount_percentage;
      this.form.selling_vat_percentage = response.data.selling_vat_percentage;
      this.form.genere = response.data.genere;
      this.imageUrl =
        `${window.publicPath}assets/img/book/thumbnail/` + response.data.photo;
      this.book = true;
    }
  },
  methods: {
    ...mapActions(["fetchCategories"]),
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
          this.form.errors.set(
            "photo",
            "File size exceeds the maximum allowed size."
          );
          Notification.error("File size exceeds the maximum allowed size.");
        }
        if (!allowedFileTypes.includes(selectedFile.type)) {
          this.form.errors.set(
            "photo",
            " File type is not supported. Please choose a valid file type."
          );
          Notification.error(
            " File type is not supported. Please choose a valid file type."
          );
        }
        this.form.photo = selectedFile;

        const reader = new FileReader();

        reader.onload = (e) => {
          this.imageUrl = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
      }
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.form.photo = null;
    },
    async submitForm() {
      this.isSubmitting = true;
      if (this.isNew) {
        await this.form
          .post("/api/damage-items", this.form)
          .then(() => {
            this.$router.push({ name: "books" });
            Notification.success(`Create book ${this.form.name} successfully!`);
          })
          .catch((error) => {
            // console.log(error);
            if (error.response.status === 422) {
              this.errors = error.response.data.errors;
              Notification.error("Validation Errors!");
            } else if (error.response.status === 401) {
              // statusText = "Unbookized";
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
            .post(`/api/damage-items/${this.$route.params.id}`, this.form)
            .then((response) => {
              Notification.success("book info updated");
              this.$router.push("/damage-items");
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
      axios.get("/api/get-category-wise-sub-categories", {
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
