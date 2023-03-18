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

// Category Files
import AddCategory from './components/category/create.vue'
import ManageCategory from './components/category/index.vue'
import EditCategory from './components/category/edit.vue'

// Product Files
import AddProduct from './components/product/create.vue'
import ManageProduct from './components/product/index.vue'
import EditProduct from './components/product/edit.vue'

// Expense Files
import AddExpense from './components/expense/create.vue'
import ManageExpense from './components/expense/index.vue'
import EditExpense from './components/expense/edit.vue'

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
    
    { name: 'add-category', path: '/add-category', component: AddCategory },
    { name: 'manage-category', path: '/manage-category', component: ManageCategory },
    { name: 'edit-category', path: '/edit-category/:id', component: EditCategory },
    
    { name: 'add-product', path: '/add-product', component: AddProduct },
    { name: 'manage-product', path: '/manage-product', component: ManageProduct },
    { name: 'edit-product', path: '/edit-product/:id', component: EditProduct },
    
    { name: 'add-expense', path: '/add-expense', component: AddExpense },
    { name: 'manage-expense', path: '/manage-expense', component: ManageExpense },
    { name: 'edit-expense', path: '/edit-expense/:id', component: EditExpense },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});
export default router;