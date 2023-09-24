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

// OptionGroup Files
import OptionGroupForm from "./components/master_data/option_group/OptionGroupForm.vue";
import ManageOptionGroup from "./components/master_data/option_group/ManageOptionGroup.vue";
import ViewOptionGroup from "./components/master_data/option_group/OptionGroup.vue";

// Option Files
import OptionForm from "./components/master_data/option/OptionForm.vue";
import ManageOption from "./components/master_data/option/ManageOption.vue";
import ViewOption from "./components/master_data/option/Option.vue";

// Category Files
import CategoryForm from "./components/master_data/category/CategoryForm.vue";
import ManageCategory from "./components/master_data/category/ManageCategory.vue";
import ViewCategory from "./components/master_data/category/Category.vue";


// SubCategory Files
import SubCategoryForm from "./components/master_data/sub-category/SubCategoryForm.vue";
import ManageSubCategory from "./components/master_data/sub-category/ManageSubCategory.vue";
import ViewSubCategory from "./components/master_data/sub-category/SubCategory.vue";

// Customer Files
import CustomerForm from "./components/customer/CustomerForm.vue";
import ManageCustomer from "./components/customer/ManageCustomer.vue";

// Supplier Files
import SupplierForm from "./components/supplier/SupplierForm.vue";
import ManageSupplier from "./components/supplier/ManageSupplier.vue";

// Author Files
import AuthorForm from "./components/master_data/author/AuthorForm.vue";
import ManageAuthor from "./components/master_data/author/ManageAuthor.vue";

// Publisher Files
import PublisherForm from "./components/master_data/publisher/PublisherForm.vue";
import ManagePublisher from "./components/master_data/publisher/ManagePublisher.vue";

// Book Files
import BookForm from "./components/book/BookForm.vue";
import ManageBook from "./components/book/ManageBook.vue";

// Purchase Files
import PurchaseForm from "./components/purchase/PurchaseForm.vue";
import ManagePurchase from "./components/purchase/ManagePurchase.vue";


// Sale Files
import SaleForm from "./components/sale/SaleForm.vue";
import ManageSale from "./components/sale/ManageSale.vue";

// CustomerPayment Files
import CustomerPaymentForm from "./components/customer_payment/CustomerPaymentForm.vue";
import ManageCustomerPayment from "./components/customer_payment/ManageCustomerPayment.vue";

// Employee Files
import AddEmployee from "./components/employee/create.vue";
import ManageEmployee from "./components/employee/index.vue";
import EditEmployee from "./components/employee/edit.vue";

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
function guard(to, from, next){
    if(User.loggedIn()) {
        // or however you store your logged in state
        next(); // allow to enter route
    } else{
        next('/'); // go to '/login';

    }
}
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
        beforeEnter: guard,
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
        beforeEnter: guard,
    },
    {
        name: "manage-employee",
        path: "/manage-employee",
        component: ManageEmployee,
        meta: { title: "Manage Employee" },
        beforeEnter: guard,
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


    // Role Paths
    {
        name: "roles",
        path: "/roles",
        component: ManageRole,
        meta: { title: "Manage Roles" },
        beforeEnter: guard,
    },
    {
        name: "role.create",
        path: "/roles/create",
        component: RoleForm,
        meta: { title: "Create Role" },
        beforeEnter: guard,
    },
    {
        name: "role.edit",
        path: "/roles/:id/edit",
        component: RoleForm,
        meta: { title: "Edit Role" },
        beforeEnter: guard,
    },
    {
        name: "role.view",
        path: "/roles/:id",
        component: Role,
        meta: { title: "View Role" },
        beforeEnter: guard,
    },

    // User Paths
    {
        name: "users",
        path: "/users",
        component: ManageUser,
        meta: { title: "Manage Users" },
        beforeEnter: guard,
    },
    {
        name: "user.create",
        path: "/users/create",
        component: UserForm,
        meta: { title: "Add User" },
        beforeEnter: guard,
    },
    {
        name: "user.edit",
        path: "/users/:id/edit",
        component: UserForm,
        meta: { title: "Edit User" },
        beforeEnter: guard,
    },
    {
        name: "user.view",
        path: "/users/:id",
        component: ViewUser,
        meta: { title: "View User" },
        beforeEnter: guard,
    },

    // Permission Paths
    {
        name: "add-permission",
        path: "/add-permission",
        component: AddPermission,
        meta: { title: "Add Permission" },
        beforeEnter: guard,
    },
    {
        name: "edit-permission",
        path: "/edit-permission/:id",
        component: EditPermission,
        meta: { title: "Edit Permission" },
        beforeEnter: guard,
    },
    {
        name: "manage-permission",
        path: "/manage-permission",
        component: ManagePermission,
        meta: { title: "Manage Permission" },
        beforeEnter: guard,
    },
// Option Group Paths
{
    name: "option-groups",
    path: "/option-groups",
    component: ManageOptionGroup,
    meta: { title: "Manage OptionGroup" },
    beforeEnter: guard,
},
{
    name: "option-groups.create",
    path: "/option-groups/create",
    component: OptionGroupForm,
    meta: { title: "Add Option Group" },
    beforeEnter: guard,
},
{
    name: "option-groups.edit",
    path: "/option-groups/:id/edit",
    component: OptionGroupForm,
    meta: { title: "Edit Option Group" },
    beforeEnter: guard,
},
{
    name: "option-groups.view",
    path: "/option-groups/:id",
    component: ViewOptionGroup,
    meta: { title: "View Option Group" },
    beforeEnter: guard,
},
    // Option Paths
    {
        name: "options",
        path: "/options",
        component: ManageOption,
        meta: { title: "Manage Options" },
        beforeEnter: guard,
    },
    {
        name: "options.create",
        path: "/options/create",
        component: OptionForm,
        meta: { title: "Add Option" },
        beforeEnter: guard,
    },
    {
        name: "options.edit",
        path: "/options/:id/edit",
        component: OptionForm,
        meta: { title: "Edit Option" },
        beforeEnter: guard,
    },
    {
        name: "options.view",
        path: "/options/:id",
        component: ViewOption,
        meta: { title: "View Option" },
        beforeEnter: guard,
    },

    // Category Paths
    {
        name: "categories",
        path: "/categories",
        component: ManageCategory,
        meta: { title: "Manage Category" },
        beforeEnter: guard,
    },
    {
        name: "categories.create",
        path: "/categories/create",
        component: CategoryForm,
        meta: { title: "Add Category" },
        beforeEnter: guard,
    },
    {
        name: "categories.edit",
        path: "/categories/:id/edit",
        component: CategoryForm,
        meta: { title: "Edit Category" },
        beforeEnter: guard,
    },
    {
        name: "categories.view",
        path: "/categories/:id",
        component: ViewCategory,
        meta: { title: "View Category" },
        beforeEnter: guard,
    },

    // Sub-Category Paths
    {
        name: "sub-categories",
        path: "/sub-categories",
        component: ManageSubCategory,
        meta: { title: "Manage Sub Category" },
        beforeEnter: guard,
    },
    {
        name: "sub-categories.create",
        path: "/sub-categories/create",
        component: SubCategoryForm,
        meta: { title: "Add Sub-Category" },
        beforeEnter: guard,
    },
    {
        name: "sub-categories.edit",
        path: "/sub-categories/:id/edit",
        component: SubCategoryForm,
        meta: { title: "Edit Sub-Category" },
        beforeEnter: guard,
    },
    {
        name: "sub-categories.view",
        path: "/sub-categories/:id",
        component: ViewSubCategory,
        meta: { title: "View Sub-Category" },
        beforeEnter: guard,
    },

// Author Paths
{
    name: "authors",
    path: "/authors",
    component: ManageAuthor,
    meta: { title: "Manage Author" },
    beforeEnter: guard,
},
{
    name: "authors.create",
    path: "/authors/create",
    component: AuthorForm,
    meta: { title: "Add Author" },
    beforeEnter: guard,
},
{
    name: "authors.edit",
    path: "/authors/:id/edit",
    component: AuthorForm,
    meta: { title: "Edit Author" },
    beforeEnter: guard,
},

// Publisher Paths
{
    name: "publishers",
    path: "/publishers",
    component: ManagePublisher,
    meta: { title: "Manage Publisher" },
    beforeEnter: guard,
},
{
    name: "publishers.create",
    path: "/publishers/create",
    component: PublisherForm,
    meta: { title: "Add Publisher" },
    beforeEnter: guard,
},
{
    name: "publishers.edit",
    path: "/publishers/:id/edit",
    component: PublisherForm,
    meta: { title: "Edit Publisher" },
    beforeEnter: guard,
},
    
    // Customer Paths
    {
        name: "customers",
        path: "/customers",
        component: ManageCustomer,
        meta: { title: "Manage Customer" },
        beforeEnter: guard,
    },
    {
        name: "customers.create",
        path: "/customers/create",
        component: CustomerForm,
        meta: { title: "Add Customer" },
        beforeEnter: guard,
    },
    {
        name: "customers.edit",
        path: "/customers/:id/edit",
        component: CustomerForm,
        meta: { title: "Edit Customer" },
        beforeEnter: guard,
    },
   
     // Supplier Paths
     {
        name: "suppliers",
        path: "/suppliers",
        component: ManageSupplier,
        meta: { title: "Manage Supplier" },
        beforeEnter: guard,
    },
    {
        name: "suppliers.create",
        path: "/suppliers/create",
        component: SupplierForm,
        meta: { title: "Add Supplier" },
        beforeEnter: guard,
    },
    {
        name: "suppliers.edit",
        path: "/suppliers/:id/edit",
        component: SupplierForm,
        meta: { title: "Edit Supplier" },
        beforeEnter: guard,
    },
// Book Paths
{
    name: "books",
    path: "/books",
    component: ManageBook,
    meta: { title: "Manage Book" },
    beforeEnter: guard,
},
{
    name: "books.create",
    path: "/books/create",
    component: BookForm,
    meta: { title: "Add Book" },
    beforeEnter: guard,
},
{
    name: "books.edit",
    path: "/books/:id/edit",
    component: BookForm,
    meta: { title: "Edit Book" },
    beforeEnter: guard,
},
// Purchase Paths
{
    name: "purchases",
    path: "/purchases",
    component: ManagePurchase,
    meta: { title: "Manage Purchase" },
    beforeEnter: guard,
},
{
    name: "purchases.create",
    path: "/purchases/create",
    component: PurchaseForm,
    meta: { title: "Add Purchase" },
    beforeEnter: guard,
},
{
    name: "purchases.edit",
    path: "/purchases/:id/edit",
    component: PurchaseForm,
    meta: { title: "Edit Purchase" },
    beforeEnter: guard,
},


// Sale Paths
{
    name: "sales",
    path: "/sales",
    component: ManageSale,
    meta: { title: "Manage Sale" },
    beforeEnter: guard,
},
{
    name: "sales.create",
    path: "/sales/create",
    component: SaleForm,
    meta: { title: "Add Sale" },
    beforeEnter: guard,
},
{
    name: "sales.edit",
    path: "/sales/:id/edit",
    component: SaleForm,
    meta: { title: "Edit Sale" },
    beforeEnter: guard,
},

// Customer Payments
{
    name: "customer_payments",
    path: "/customer_payments",
    component: ManageCustomerPayment,
    meta: { title: "Manage Customer Payment" },
    beforeEnter: guard,
},
{
    name: "customer_payments.create",
    path: "/customer_payments/create",
    component: CustomerPaymentForm,
    meta: { title: "Add Customer Payment" },
    beforeEnter: guard,
},
{
    name: "customer_payments.edit",
    path: "/customer_payments/:id/edit",
    component: CustomerPaymentForm,
    meta: { title: "Edit Customer Payment" },
    beforeEnter: guard,
},

    { name: "add-product", path: "/add-product", component: AddProduct },
    {
        name: "manage-product",
        path: "/manage-product",
        component: ManageProduct,
        meta: { title: "Add Product" },
        beforeEnter: guard,
    },
    {
        name: "edit-product",
        path: "/edit-product/:id",
        component: EditProduct,
        meta: { title: "Edit Product" },
        beforeEnter: guard,
    },

    {
        name: "add-expense",
        path: "/add-expense",
        component: AddExpense,
        meta: { title: "Add Expense" },
        beforeEnter: guard,
    },
    {
        name: "manage-expense",
        path: "/manage-expense",
        component: ManageExpense,
        beforeEnter: guard,
    },
    {
        name: "edit-expense",
        path: "/edit-expense/:id",
        component: EditExpense,
        beforeEnter: guard,
    },

    // Salary Routes
    {
        path: "/given-salary",
        component: Salary,
        name: "given-salary",
        beforeEnter: guard,
    },
    {
        path: "/pay-salary/:id",
        component: PaySalary,
        name: "pay-salary",
        beforeEnter: guard,
    },

    {
        path: "/salary",
        component: AllSalary,
        name: "salary",
        beforeEnter: guard,
    },
    {
        path: "/view-salary/:id",
        component: ViewSalary,
        name: "view-salary",
        beforeEnter: guard,
    },
    {
        path: "/edit-salary/:id",
        component: EditSalary,
        name: "edit-salary",
        beforeEnter: guard,
    },

    // Stock Routes
    {
        path: "/stock",
        component: Stock,
        name: "stock",
        beforeEnter: guard,
    },
    {
        path: "/edit-stock/:id",
        component: EditStock,
        name: "edit-stock",
        beforeEnter: guard,
    },

    // POS Routes
    {
        path: "/pos",
        component: Pos,
        name: "pos",
        beforeEnter: guard,
    },

    // Order Routes
    {
        path: "/order",
        component: Order,
        name: "order",
        beforeEnter: guard,
    },
    {
        path: "/view-order/:id",
        component: ViewOrder,
        name: "view-order",
        beforeEnter: guard,
    },
    {
        path: "/searchorder",
        component: SearchOrder,
        name: "searchorder",
        beforeEnter: guard,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router;
