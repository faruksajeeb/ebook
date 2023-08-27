<template>
  <div class="login">
    <div class="container-login">
      <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-10 col-md-10 offset-md-1">
          <div class="card shadow-sm my-5">
            <div class="card-body p-0">
              <div class="row">
                <div class="col-lg-12">
                  <div class="login-form ">
                    <div class="text-center text-white">
                      <img src="assets/img/logo/logo.png" width="80">
                      <h1 class="h4 text-white-900 mb-4">LOGIN</h1>
                    </div>
                    <form class="user" @submit.prevent="login">
                      <div class="form-group">
                        <input
                          type="email"
                          class="form-control"
                          id="exampleInputEmail"
                          aria-describedby="emailHelp"
                          placeholder="Enter Email Address"
                          v-model="form.email"
                          :class="{ 'is-invalid': errors.email }"
                        />
                        <small class="text-danger" v-if="errors.email">{{
                          errors.email[0]
                        }}</small>
                      </div>
                      <div class="form-group">
                        <input
                          type="password"
                          class="form-control"
                          id="exampleInputPassword"
                          v-model="form.password"
                          :class="{ 'is-invalid': errors.password }"
                          placeholder="Password"
                        />
                        <small class="text-danger" v-if="errors.password">{{
                          errors.password[0]
                        }}</small>
                      </div>
                      <div class="form-group">
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
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-danger btn-block" :disabled="isSubmitting">
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
                    <div class="text-center text-white">
                      <router-link to="/register" class="font-weight-bold small  text-white"
                        >Create an Account!</router-link
                      >
                      <!-- <a class="font-weight-bold small" href="register.html">Create an Account!</a> -->
                    </div>
                    <hr />
                    <div class="text-center">
                      <router-link to="/forget_password" class="font-weight-bold small  text-white"
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
    if (User.loggedIn()) {
      this.$router.push({ name: "dashboard" });
    }
  },
  data() {
    return {
      submitButtonText: "Log In",
      isSubmitting: false,
      form: {
        email: 'ofsajeeb@gmail.com',
        password: '12345678',
      },
      errors: {},
    };
  },
  methods: {
    login() {
      this.isSubmitting = true;
      this.submitButtonText = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loging In...</span>';
      axios.post("/api/auth/login", this.form).then((res) => {
          User.responseAfterLogin(res);
          Notification.success("Signed in successfully");
          this.$router.push({ name: "dashboard" });
        })
        .catch((error) => {
          console.log(error);
          if(error.response.status===422){
            this.errors = error.response.data.errors;
            Notification.error(error.response.statusText);
          }else if(error.response.status===401){
           // statusText = "Unauthorized";
            this.errors = {}
            Notification.error(error.response.data.error);
          }else{
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

<style scoped></style>
