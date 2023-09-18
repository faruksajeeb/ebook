<template>
    <div>
    <flat-pickr v-model="selectedDate" :config="config"></flat-pickr>
    <!-- <p>Selected Date: {{ selectedDate }}</p> -->
  </div>
  </template>
  
  <script>
  import DateCalander from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  components: {
    flatPickr: DateCalander,
  },
  data() {
    return {
      selectedDate: null,
      config: {
        enableTime: false, // Set to true if you want to include time selection
        dateFormat: 'Y-m-d', // Customize the date format as needed
        // Add more Flatpickr options as needed
      },
    };
  },
};
  </script>
  