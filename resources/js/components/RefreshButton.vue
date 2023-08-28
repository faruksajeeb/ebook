<template>
  <button
    type="submit"
    :disabled="isRefreshing"
    class="btn btn-md btn-warning float-right"
    @click="refreshData"
  >
    <div v-if="isRefreshing">
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">
      </span>
      Refeshing...Data
    </div>
    <div v-else><i class="fa fa-solid fa-rotate"> </i> Refresh Data</div>
  </button>
</template>

<script>
export default {
  props: {
    isRefreshing: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  methods: {
    refreshData() {
      this.$emit("refresh-data");
    },
  },
};
</script>

<style></style>
