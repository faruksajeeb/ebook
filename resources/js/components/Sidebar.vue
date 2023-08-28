<template lang="">
    <div >
        <ul v-show="$route.path === '/' || $route.path === '/register' || $route.path === '/forget_password' ? false : true"
      class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon">
          <img src="assets/img/logo/logo.png">
        </div>
        <div class="sidebar-brand-text mx-3">Faizaan's Shop</div>
      </a>
      <hr class="sidebar-divider my-0">
      <li class="nav-item active">
        <router-link class="nav-link" to="/dashboard">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></router-link>
      </li>
      
     <li class="nav-item bg-info">
        <router-link class="nav-link" to="/pos">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>POS</span></router-link>
      </li>
      <hr class="sidebar-divider">
      <!-- <div class="sidebar-heading">
        Features
      </div> -->
      <div style="max-height: 450px; overflow-y: auto;">
        <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCategory"
          aria-expanded="true" aria-controls="collapseCategory">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Category</span>
        </a>
        <div id="collapseCategory" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <router-link class="collapse-item" to="/add-category">Add Category</router-link>
            <router-link class="collapse-item" to="/manage-category">Manage Category</router-link>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmployee"
          aria-expanded="true" aria-controls="collapseEmployee">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Employee</span>
        </a>
        <div id="collapseEmployee" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <router-link class="collapse-item" to="/add-employee">Add Employee</router-link>
            <router-link class="collapse-item" to="/manage-employee">Manage Employee</router-link>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSupplier" aria-expanded="true"
          aria-controls="collapseSupplier">
          <i class="fas fa-fw fa-table"></i>
          <span>Suppliers</span>
        </a>
        <div id="collapseSupplier" class="collapse" aria-labelledby="headingTable" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <router-link class="collapse-item" to="/add-supplier">Add Supplier</router-link>
            <router-link class="collapse-item" to="/manage-supplier">Manage Supplier</router-link>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomer"
          aria-expanded="true" aria-controls="collapseCustomer">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Customers</span>
        </a>
        <div id="collapseCustomer" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <router-link class="collapse-item" to="/add-customer">Add Customer</router-link>
            <router-link class="collapse-item" to="/manage-customer">Manage Customer</router-link>
          </div>
        </div>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProduct"
          aria-expanded="true" aria-controls="collapseProduct">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Product</span>
        </a>
        <div id="collapseProduct" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <router-link class="collapse-item" to="/add-product">Add Product</router-link>
            <router-link class="collapse-item" to="/manage-product">Manage Product</router-link>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExpense"
          aria-expanded="true" aria-controls="collapseExpense">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Expense</span>
        </a>
        <div id="collapseExpense" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <router-link class="collapse-item" to="/add-expense">Add Expense</router-link>
            <router-link class="collapse-item" to="/manage-expense">Manage Expense</router-link>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSalary"
          aria-expanded="true" aria-controls="collapseSalary">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Salary</span>
        </a>
        <div id="collapseSalary" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">

            <router-link class="collapse-item" to="/given-salary">Add Salary</router-link>
            <router-link class="collapse-item" to="/salary">Manage Salary</router-link>

          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBootstrap51"
          aria-expanded="true" aria-controls="collapseBootstrap51">
          <i class="far fa-fw fa-window-maximize"></i>
          <span>Orders</span>
        </a>
        <div id="collapseBootstrap51" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
           
   <router-link class="collapse-item" to="/order">Today Order</router-link>
   <router-link class="collapse-item" to="/searchorder">Search</router-link>
            
          </div>
        </div>
      </li>
      <li class="nav-item">
        <router-link class="nav-link" to="/stock">
          <i class="fab fa-fw fa-wpforms"></i>
          <span>Stock</span>
        </router-link>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="forms.html">
          <i class="fab fa-fw fa-wpforms"></i>
          <span>Forms</span>
        </a>
      </li>
     
      <li class="nav-item">
        <a class="nav-link" href="ui-colors.html">
          <i class="fas fa-fw fa-palette"></i>
          <span>UI Colors</span>
        </a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Examples
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePage" aria-expanded="true"
          aria-controls="collapsePage">
          <i class="fas fa-fw fa-columns"></i>
          <span>Pages</span>
        </a>
        <div id="collapsePage" class="collapse" aria-labelledby="headingPage" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Example Pages</h6>
            <a class="collapse-item" href="login.html">Login</a>
            <a class="collapse-item" href="register.html">Register</a>
            <a class="collapse-item" href="404.html">404 Page</a>
            <a class="collapse-item" href="blank.html">Blank Page</a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="charts.html">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Charts</span>
        </a>
      </li>
      
      
    </div>
      <hr class="sidebar-divider">
      <div class="version" id="version-ruangadmin"></div>
    </ul>
    
    </div>
</template>
<script>
export default {
    name: 'Sidebar'
}
</script>
<style lang="">
    
</style>
