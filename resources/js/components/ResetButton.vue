<template>
  <button
    type="button"
    :disabled="isRefreshing"
    class="btn btn-lg btn-outline-secondary float-right mx-1"
    @click="resetData"
  >
    <i class="fa fa-solid fa-undo"> </i> Reset Form
  </button>
</template>

<script>
export default {
  props: {
    
  },
  methods: {
    resetData() {
      this.$emit("reset-data");
    },
  },
};
</script>

<style></style>
