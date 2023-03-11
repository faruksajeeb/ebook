<template>
    <div class="login">
      <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-10 col-md-10 offset-md-1">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Login</h1>
                  </div>
                  <form class="user" @submit.prevent="login">
                    <div class="form-group">
                      <input type="email" class="form-control" id="exampleInputEmail" aria-describedby="emailHelp"
                        placeholder="Enter Email Address" v-model="form.email">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control" id="exampleInputPassword"  v-model="form.password" placeholder="Password">
                    </div>
                    <div class="form-group">
                      <div class="custom-control custom-checkbox small" style="line-height: 1.5rem;">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Remember
                          Me</label>
                      </div>
                    </div>
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">Login</button>
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
                  <hr>
                  <div class="text-center">
                    <router-link to="/register" class="font-weight-bold small">Create an Account!</router-link>
                    <!-- <a class="font-weight-bold small" href="register.html">Create an Account!</a> -->
                  </div>
                  <hr>
                  <div class="text-center">
                    <router-link to="/forget_password" class="font-weight-bold small">Forget Password</router-link>                    
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
    name: 'Login',
    data () {
      return {
       form:{
        email:null,
        password:null
       },
      errors:{}
      }
    },
    methods:{
      login(){
        axios.post('/api/auth/login',this.form)
        .then(res => User.responseAfterLogin(res))
        .catch(error => console.log(error.response.data.errors))
      }
    }
  }
</script>
  
<style scoped>
  
</style>