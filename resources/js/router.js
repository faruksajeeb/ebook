import {createWebHistory, createRouter} from "vue-router";
import Login from './components/auth/Login.vue'
import Register from './components/auth/Register.vue'
import ForgetPassword from './components/auth/ForgetPassword.vue'
import Logout from './components/auth/Logout.vue'

import Dashboard from './components/Dashboard.vue'
import AddEmployee from './components/employee/create.vue'
import ManageEmployee from './components/employee/index.vue'
import EditEmployee from './components/employee/edit.vue'

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
    ,
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard
    }
    ,
    {
        name: 'logout',
        path: '/logout',
        component: Logout
    },
    {
        name: 'add-employee',
        path: '/add-employee',
        component: AddEmployee
    }
    ,
    {
        name: 'manage-employee',
        path: '/manage-employee',
        component: ManageEmployee
    }
    ,
    {
        name: 'edit-employee',
        path: '/edit-employee/:id',
        component: EditEmployee
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});
export default router;