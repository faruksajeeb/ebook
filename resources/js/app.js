import './bootstrap';
import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js';
 // Import User Class
 import User from './Helpers/User';
 window.User = User
 
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