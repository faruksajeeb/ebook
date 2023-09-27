<template>
  <div class="p-1 text-center my-3" >
    <h3 >
      <i class="pi pi-chart-bar" ></i>Reports
    </h3>
    <h6 class="font-weight-bold" >
      Get the complete reporting of store.
    </h6>
  </div>
  <section class="section-menu" >
    <div class="col-md-12" >
      <div class="row" >
        <a
          href="#/store/user-report"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/user_report.png`"
             />
          <h5 class="menu-link-settings" >Customer Report</h5>
          <p class="menu-link-detail-settings" ></p></a
        ><a
          href="#/store/sales-refund-reports"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/sales_refund_report.png`"
             />
          <h5 class="menu-link-settings" >Sales/Refund Report</h5>
          <p class="menu-link-detail-settings" ></p
        ></a>

        <a
          href="#/store/transfer-reports"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/stock_transfer.png?f7df416b6f5119216f29735302a4a844`"
             />
          <h5 class="menu-link-settings" >Transfer Stock Report</h5>
          <p class="menu-link-detail-settings" ></p></a
        ><a
          href="#/store/purchasing-report"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/purchasing.png?3e1cc6b17120ccb9c2cc620f46c04f28`"
             />
          <h5 class="menu-link-settings" >Purchasing Report</h5>
          <p class="menu-link-detail-settings" ></p
        ></a>

        <a
          href="#/store/income-statement-report"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/income.png`"
             />
          <h5 class="menu-link-settings" >Income Statement</h5>
          <p class="menu-link-detail-settings" ></p
        ></a>
        <a
          href="#/store/stock-report"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/stock.png`"
             />
          <h5 class="menu-link-settings" >Stock Report</h5>
          <p class="menu-link-detail-settings" ></p
        ></a>
        <a
          href="#/store/stock-alert-report"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/alert.png`"
             />
          <h5 class="menu-link-settings" >Stock Alert Report</h5>
          <p class="menu-link-detail-settings" ></p></a
        ><a
          href="#/store/activity-report"
          class="col-12 col-md-3 text-center hover-settings"
          
          ><img
            class="menu-img-settings"
            :src="`${publicPath}assets/img/report_icon/activity.png`"
             />
          <h5 class="menu-link-settings" >Activity Report</h5>
          <p class="menu-link-detail-settings" ></p
        ></a>
      </div>
    </div>
  </section>
</template>
<script>
export default {
data(){
	 return {	 
		publicPath: window.publicPath,
	 }
	},
}
</script>
<style scoped>
.row {
  display: flex;
  flex-wrap: wrap;
  margin-left: -15px;
  margin-right: -15px;
}
.hover-settings {
  border: 1px solid #ccc;
  padding: 10px;
}
img {
  width: 100px;
  padding: 10px;
}
</style>
