<template>
  <div class="login-main ">
    <div class="container-login">
      <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-10 col-md-10 offset-md-1">
          <div class="card shadow-sm my-5">
            <div class="card-body p-0">
              <div class="row">
                <div class="col-lg-12">
                  <div class="login-form">
                    <div class="text-center text-white">
                      <img :src="`${publicPath}assets/img/logo/logo.png`" width="200" />
                      <h1 class="h4 my-text-primary text-bold mb-4">LOGIN</h1>
                    </div>
                    <form class="login" @submit.prevent="login"  @keydown="form.onKeydown($event)">
                      <AlertError :form="form" message="There were some problems with your input." />
                      <div class="form-group">
                        <input
                          type="text"
                          class="form-control"
                          id="exampleInputEmail"
                          aria-describedby="emailHelp"
                          placeholder="Enter Email Address"
                          v-model="form.email"
                          :class="{ 'is-invalid': form.errors.has('email') }"
                        />
                        <HasError :form="form" field="email" />
                        <!-- <small class="text-danger" v-if="errors.email">{{
                          errors.email[0]
                        }}</small> -->
                        <!-- <div v-if="form.errors.has('email')" v-html="form.errors.get('email')" /> -->
                      </div>
                      <div class="form-group">
                        <input
                          type="password"
                          class="form-control"
                          id="exampleInputPassword"
                          v-model="form.password"
                          :class="{ 'is-invalid': form.errors.has('password') }"
                          placeholder="Password"
                        />
                        <HasError :form="form" field="password" />
                        <!-- <div v-if="form.errors.has('password')" v-html="form.errors.get('password')" /> -->
                     
                        <!-- <small class="text-danger" v-if="errors.password">{{
                          errors.password[0]
                        }}</small> -->
                      </div>
                      <!-- <div class="form-group">
                        <div
                          class="custom-control custom-checkbox small"
                          style="line-height: 1.5rem"
                        >
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            id="customCheck"
                          />
                          <label class="custom-control-label" for="customCheck"
                            >Remember Me</label
                          >
                        </div>
                      </div> -->
                      <div class="form-group">
                        <!-- <Button :form="form" class="btn btn-primary">
                        Log In
                      </Button> -->
                        <button
                          type="submit"
                          class="btn my-btn-primary btn-block"
                          :disabled="isSubmitting"
                        >
                          <div v-html="submitButtonText" class="text-white"></div>
                        </button>
                      </div>
                      <!--
                    <hr>
                    <a href="index.html" class="btn btn-google btn-block">
                      <i class="fab fa-google fa-fw"></i> Login with Google
                    </a>
                    <a href="index.html" class="btn btn-facebook btn-block">
                      <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                    </a>
					-->
                    </form>
                    <hr />
                    <!-- <div class="text-center text-white">
                      <router-link
                        to="/register"
                        class="font-weight-bold small text-white"
                        >Create an Account!</router-link
                      >
                    </div>
                    <hr /> -->
                    <div class="text-center">
                      <router-link
                        to="/forget_password"
                        class="font-weight-bold small text-black"
                        >Forget Password</router-link
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: "Login",
  created() {
    // alert('hry');
    if (User.loggedIn()) {
      // alert(888);
     this.$router.push({ name: "dashboard" });
    }
  },
  data: () => ({
    submitButtonText: "Log In",
    isSubmitting: false,
	publicPath: window.publicPath,
    form: new Form({
      email: "ofsajeeb@gmail.com",
      password: "12345678",
    }),
  }),
  methods: {
    async login() {
      this.isSubmitting = true;
      this.submitButtonText =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loging In...</span>';
        await this.form.post("/api/auth/login", this.form)
        .then((res) => {
          User.responseAfterLogin(res);
          Notification.success("Signed in successfully");
          this.$router.push({ name: "dashboard" });
        })
        .catch((error) => {
          console.log(error);
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
            Notification.error(error.response.statusText);
          } else if (error.response.status === 401) {
            // statusText = "Unauthorized";
            this.errors = {};
            Notification.error(error.response.data.error);
          } else {
            Notification.error(error.response.statusText);
          }
        })
        .finally(() => {
          // always executed;

          this.isSubmitting = false;
          this.submitButtonText = "Login";
        });
    },
  },
};
</script>

<style scoped>
.login-main{
  height: 100vh;
  width: 100%;
  /* margin-left:-1.5em; */

}
.container-fluid{
    --bs-gutter-x: 0!important;
    --bs-gutter-y: 0;
    width: 100%;
    padding-right: calc(var(--bs-gutter-x) * 0.5)!important;
    padding-left: calc(var(--bs-gutter-x) * 0.5)!important;
    margin-right: auto;
    margin-left: auto;
}
</style>
