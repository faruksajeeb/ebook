<template>
<!--<router-link to="/" class="nav-item nav-link">Home</router-link> || 
<router-link to="/contact" class="nav-item nav-link">Contact</router-link> -->
<router-view></router-view>
<vue-progress-bar></vue-progress-bar>
</template>

 <script>
  export default {
    mounted() {
      this.$Progress.finish();
    },
    created() {
      this.$Progress.start();
      this.$router.beforeEach((to, from, next) => {
        if (to.meta.progress !== undefined) {
          let meta = to.meta.progress;
          this.$Progress.parseMeta(meta);
        }
        this.$Progress.start();
        next();
      });
      this.$router.afterEach((to, from) => {
        this.$Progress.finish();
      });
    },
  };
</script>