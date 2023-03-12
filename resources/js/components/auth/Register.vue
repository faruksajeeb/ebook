<template>
  <div>
    <!-- Register Content -->
    <div class="container-login">
      <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
          <div class="card shadow-sm my-5">
            <div class="card-body p-0">
              <div class="row">
                <div class="col-lg-12">
                  <div class="login-form">
                    <div class="text-center">
                      <h1 class="h4 text-gray-900 mb-4">Register</h1>
                    </div>
                    <form id="register" @submit.prevent="register">
                      <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" id="exampleInputFirstName" placeholder="Enter First Name" v-model="form.name">
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp"
                          placeholder="Enter Email Address" v-model="form.email">
                      </div>
                      <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword" placeholder="Password" v-model="form.password">
                      </div>
                      <div class="form-group">
                        <label>Repeat Password</label>
                        <input type="password" class="form-control" id="exampleInputPasswordRepeat"
                          placeholder="Repeat Password" v-model="form.password_confirmation">
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                      </div>
                      <!--   <hr>
                      <a href="index.html" class="btn btn-google btn-block">
                        <i class="fab fa-google fa-fw"></i> Register with Google
                      </a>
                      <a href="index.html" class="btn btn-facebook btn-block">
                        <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
                      </a> -->
                    </form>
                    <hr>
                    <div class="text-center">
                      <router-link to="/" class="nav-item nav-link font-weight-bold small">Already have an
                        account?</router-link>
                      <!-- <a class="font-weight-bold small" href="login.html">Already have an account?</a> -->
                    </div>
                    <div class="text-center">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Register Content -->
  </div>
</template>



<script type="text/javascript">

export default {
  created() {
    if (User.loggedIn()) {
      this.$router.push({ name: 'dashboard' })
    }
  },

  data() {
    return {
      form: {
        name: null,
        email: null,
        password: null,
        confirm_password: null
      },
      errors: {}
    }
  },
  methods: {
    register() {
      axios.post('/api/auth/register', this.form)
        .then(res => {
          User.responseAfterLogin(res)
          Toast.fire({
            icon: 'success',
            title: 'Signed in successfully'
          })
          this.$router.push({ name: 'dashboard' })
        })

        .catch(error => this.errors = error.response.data.errors)

    }
  }


} 
</script>


<style type="text/css"></style>