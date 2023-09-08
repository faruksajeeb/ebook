      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMatserDataMgt"
            aria-expanded="true" aria-controls="collapseMatserDataMgt">
            <i class="far fa-fw fa-window-maximize"></i>
            <span>Master Data</span>
          </a>
          <div id="collapseMatserDataMgt" class="collapse" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
            <ul id="accordionMasterDataMgtBar" class="navbar-nav accordion px-3" >
            <li class="nav-item py-2 ">
                    <a class="nav-link-layer-two collapsed" href="#" data-toggle="collapse" data-target="#collapseOption"
                      aria-expanded="true" aria-controls="collapseOption">                
                      <span>Option</span>
                    </a>
                    <div id="collapseOption" class="collapse ms-0" aria-labelledby="headingBootstrap" data-parent="#accordionMasterDataMgtBar">
                      <div class="bg-white py-2  rounded">
                        <router-link class="collapse-item px-3" to="/roles/create">Add Option</router-link>
                        <router-link class="collapse-item px-3" to="/roles">Manage Option</router-link>
                      </div>
                  </div>
            </li>
            <li class="nav-item py-2">
                    <a class="nav-link-layer-two collapsed" href="#" data-toggle="collapse" data-target="#collapseCategory"
                      aria-expanded="true" aria-controls="collapseCategory">                   
                      <span>Category</span>
                    </a>
                    <div id="collapseCategory" class="collapse ms-0" aria-labelledby="headingBootstrap" data-parent="#accordionMasterDataMgtBar">
                      <div class="bg-white py-2  rounded">
                        <router-link v-if="userPermissions.includes('user.create')" class="collapse-item px-3" to="/users/create">Add Category</router-link>
                      
                        <router-link v-if="userPermissions.includes('user.manage')"   class="collapse-item px-3" to="/users">Manage Category</router-link>
                      
                      </div>
                  </div>
            </li>
            <li class="nav-item py-2">
                    <a class="nav-link-layer-two collapsed" href="#" data-toggle="collapse" data-target="#collapseSubCategory"
                      aria-expanded="true" aria-controls="collapseSubCategory">                   
                      <span>Sub-category</span>
                    </a>
                    <div id="collapseSubCategory" class="collapse ms-0" aria-labelledby="headingBootstrap" data-parent="#accordionMasterDataMgtBar">
                      <div class="bg-white py-2  rounded">
                        <router-link class="collapse-item px-3" to="/add-permission">Add Sub-category</router-link>
                        <router-link class="collapse-item px-3" to="/manage-permission">Manage Sub-category</router-link>
                      </div>
                  </div>
            </li>
            </ul>           
            </div>
          </div>
      </li>