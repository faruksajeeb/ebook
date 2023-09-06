import { createWebHistory, createRouter } from "vue-router";
import User from "./Helpers/User";
window.User = User;

import Login from "./components/auth/Login.vue";
import Register from "./components/auth/Register.vue";
import ForgetPassword from "./components/auth/ForgetPassword.vue";
import Logout from "./components/auth/Logout.vue";

import Dashboard from "./components/Dashboard.vue";

// User Files
import UserForm from "./components/user_management/user/UserForm.vue";
import ManageUser from "./components/user_management/user/ManageUser.vue";
import ViewUser from "./components/user_management/user/User.vue";

// Role Files
import RoleForm from "./components/user_management/role/RoleForm.vue";
import ManageRole from "./components/user_management/role/ManageRole.vue";
import Role from "./components/user_management/role/Role.vue";

// Permission Files
import AddPermission from "./components/user_management/permission/Create.vue";
import ManagePermission from "./components/user_management/permission/Index.vue";
import EditPermission from "./components/user_management/permission/Edit.vue";

// Employee Files
import AddEmployee from "./components/employee/create.vue";
import ManageEmployee from "./components/employee/index.vue";
import EditEmployee from "./components/employee/edit.vue";

// Suppler Files
import AddSupplier from "./components/supplier/create.vue";
import ManageSupplier from "./components/supplier/index.vue";
import EditSupplier from "./components/supplier/edit.vue";

// Customer Files
import AddCustomer from "./components/customer/create.vue";
import ManageCustomer from "./components/customer/index.vue";
import EditCustomer from "./components/customer/edit.vue";

// Category Files
import AddCategory from "./components/category/create.vue";
import ManageCategory from "./components/category/index.vue";
import EditCategory from "./components/category/edit.vue";

// Product Files
import AddProduct from "./components/product/create.vue";
import ManageProduct from "./components/product/index.vue";
import EditProduct from "./components/product/edit.vue";

// Expense Files
import AddExpense from "./components/expense/create.vue";
import ManageExpense from "./components/expense/index.vue";
import EditExpense from "./components/expense/edit.vue";

// Salary Component
import Salary from "./components/salary/all_employee.vue";
import PaySalary from "./components/salary/create.vue";

import AllSalary from "./components/salary/index.vue";
import ViewSalary from "./components/salary/view.vue";
import EditSalary from "./components/salary/edit.vue";

import Stock from "./components/stock/view.vue";
import EditStock from "./components/stock/edit.vue";

// POS Component
import Pos from "./components/pos/pointofsale.vue";

// Order Component
import Order from "./components/order/order.vue";
import ViewOrder from "./components/order/vieworder.vue";
import SearchOrder from "./components/order/search.vue";

export const routes = [
    {
        name: "login",
        path: "/",
        component: Login,
        meta: { title: "Login" },
    },
    {
        name: "register",
        path: "/register",
        component: Register,
        meta: { title: "Register" },
    },
    {
        name: "forget_password",
        path: "/forget_password",
        component: ForgetPassword,
        meta: { title: "Forget Password" },
    },
    {
        name: "dashboard",
        path: "/dashboard",
        component: Dashboard,
        meta: { title: "Dashboard" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "logout",
        path: "/logout",
        component: Logout,
    },
    {
        name: "add-employee",
        path: "/add-employee",
        component: AddEmployee,
        meta: { title: "Add Employee" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "manage-employee",
        path: "/manage-employee",
        component: ManageEmployee,
        meta: { title: "Manage Employee" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "edit-employee",
        path: "/edit-employee/:id",
        component: EditEmployee,
        meta: { title: "Edit Employee" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    { name: "add-supplier", path: "/add-supplier", component: AddSupplier },
    {
        name: "manage-supplier",
        path: "/manage-supplier",
        component: ManageSupplier,
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "edit-supplier",
        path: "/edit-supplier/:id",
        component: EditSupplier,
    },

    { name: "add-customer", path: "/add-customer", component: AddCustomer },
    {
        name: "manage-customer",
        path: "/manage-customer",
        component: ManageCustomer,
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "edit-customer",
        path: "/edit-customer/:id",
        component: EditCustomer,
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },

    // Role Paths
    {
        name: "roles",
        path: "/roles",
        component: ManageRole,
        meta: { title: "Manage Roles" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "role.create",
        path: "/roles/create",
        component: RoleForm,
        meta: { title: "Create Role" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "role.edit",
        path: "/roles/:id/edit",
        component: RoleForm,
        meta: { title: "Edit Role" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "role.view",
        path: "/roles/:id",
        component: Role,
        meta: { title: "View Role" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },

    // User Paths
    {
        name: "users",
        path: "/users",
        component: ManageUser,
        meta: { title: "Manage Users" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "user.create",
        path: "/users/create",
        component: UserForm,
        meta: { title: "Add User" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "user.edit",
        path: "/users/:id/edit",
        component: UserForm,
        meta: { title: "Edit User" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },
    {
        name: "user.view",
        path: "/users/:id",
        component: ViewUser,
        meta: { title: "View User" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // if (User) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                // next('/logout');
                next("/");
            }
        },
    },

    // Permission Paths
    {
        name: "add-permission",
        path: "/add-permission",
        component: AddPermission,
        meta: { title: "Add Permission" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "edit-permission",
        path: "/edit-permission/:id",
        component: EditPermission,
        meta: { title: "Edit Permission" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "manage-permission",
        path: "/manage-permission",
        component: ManagePermission,
        meta: { title: "Manage Permission" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    {
        name: "add-category",
        path: "/add-category",
        component: AddCategory,
        meta: { title: "Add Category" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "manage-category",
        path: "/manage-category",
        component: ManageCategory,
        meta: { title: "Manage Category" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "edit-category",
        path: "/edit-category/:id",
        component: EditCategory,
        meta: { title: "Edit Category" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    { name: "add-product", path: "/add-product", component: AddProduct },
    {
        name: "manage-product",
        path: "/manage-product",
        component: ManageProduct,
        meta: { title: "Add Product" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "edit-product",
        path: "/edit-product/:id",
        component: EditProduct,
        meta: { title: "Edit Product" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    {
        name: "add-expense",
        path: "/add-expense",
        component: AddExpense,
        meta: { title: "Add Expense" },
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "manage-expense",
        path: "/manage-expense",
        component: ManageExpense,
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        name: "edit-expense",
        path: "/edit-expense/:id",
        component: EditExpense,
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    // Salary Routes
    {
        path: "/given-salary",
        component: Salary,
        name: "given-salary",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        path: "/pay-salary/:id",
        component: PaySalary,
        name: "pay-salary",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    {
        path: "/salary",
        component: AllSalary,
        name: "salary",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        path: "/view-salary/:id",
        component: ViewSalary,
        name: "view-salary",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        path: "/edit-salary/:id",
        component: EditSalary,
        name: "edit-salary",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    // Stock Routes
    {
        path: "/stock",
        component: Stock,
        name: "stock",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        path: "/edit-stock/:id",
        component: EditStock,
        name: "edit-stock",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    // POS Routes
    {
        path: "/pos",
        component: Pos,
        name: "pos",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },

    // Order Routes
    {
        path: "/order",
        component: Order,
        name: "order",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        path: "/view-order/:id",
        component: ViewOrder,
        name: "view-order",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
    {
        path: "/searchorder",
        component: SearchOrder,
        name: "searchorder",
        beforeEnter: (to, from, next) => {
            if (User.loggedIn()) {
                // User is authenticated, allow access
                next();
            } else {
                // User is not authenticated, redirect to login
                next("/");
            }
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router;
