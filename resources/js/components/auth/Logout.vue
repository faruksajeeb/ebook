

<template>

</template>



<script type="text/javascript">
	
export default {
   created(){
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    Toast.fire({
          icon: 'success',
          title: 'Logout successfully'
        })
    this.$router.push({ name: 'login'})

   }
 

}




</script>


<style type="text/css">
	
</style>