import './bootstrap';
import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js';
 // Import User Class
 import User from './Helpers/User';
 window.User = User

  // Import Notification Class
  // import Notification from './Helpers/Notification';
  // window.Notification = Notification

 import Swal from 'sweetalert2'
 window.Swal = Swal
 const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})
window.Toast = Toast;
 
import VueProgressBar from "@aacassandra/vue3-progressbar";
const options = {
    color: "red",
    failedColor: "#874b4b",
    thickness: "5px",
    transition: {
      speed: "0.2s",
      opacity: "0.9s",
      termination: 300,
    },
    autoRevert: true,
    location: "top",
    inverse: false,
  };
  
const app = createApp(App);
app.use(VueProgressBar, options)
app.use(router);
app.mount('#app');