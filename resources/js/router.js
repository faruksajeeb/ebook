import {createWebHistory, createRouter} from "vue-router";
import Login from './components/auth/Login.vue'
import Register from './components/auth/Register.vue'
import ForgetPassword from './components/auth/ForgetPassword.vue'

export const routes = [
    {
        name: 'login',
        path: '/',
        component: Login
    }
    ,
    {
        name: 'register',
        path: '/register',
        component: Register
    }
    ,
    {
        name: 'forget_password',
        path: '/forget_password',
        component: ForgetPassword
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});
export default router;