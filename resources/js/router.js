import { createWebHistory, createRouter } from "vue-router";
import Login from './components/auth/Login.vue'
import Register from './components/auth/Register.vue'
import ForgetPassword from './components/auth/ForgetPassword.vue'
import Logout from './components/auth/Logout.vue'

import Dashboard from './components/Dashboard.vue'

// Employee Files
import AddEmployee from './components/employee/create.vue'
import ManageEmployee from './components/employee/index.vue'
import EditEmployee from './components/employee/edit.vue'

// Suppler Files
import AddSupplier from './components/supplier/create.vue'
import ManageSupplier from './components/supplier/index.vue'
import EditSupplier from './components/supplier/edit.vue'

// Customer Files
import AddCustomer from './components/customer/create.vue'
import ManageCustomer from './components/customer/index.vue'
import EditCustomer from './components/customer/edit.vue'

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
    },
    { name: 'add-supplier', path: '/add-supplier', component: AddSupplier },
    { name: 'manage-supplier', path: '/manage-supplier', component: ManageSupplier },
    { name: 'edit-supplier', path: '/edit-supplier/:id', component: EditSupplier },
    { name: 'add-customer', path: '/add-customer', component: AddCustomer },
    { name: 'manage-customer', path: '/manage-customer', component: ManageCustomer },
    { name: 'edit-customer', path: '/edit-customer/:id', component: EditCustomer },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});
export default router;