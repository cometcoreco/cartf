<template>
  <div class="dashboard__left dashboard-left-content">
    <div class="dashboard__left__main">
      <div class="dashboard__left__close close-bars"> <i class="fa-solid fa-times"></i> </div>
      <div class="dashboard__top">
        <div class="dashboard__top__logo">
          <a href="index_new.html">
            <img class="main_logo" src="assets/img/logo.png" alt="logo">
            <img class="iocn_view__logo" src="assets/img/logo_icon.png" alt="logo_icon">
          </a>
        </div>
      </div>
      <div class="dashboard__bottom mt-5">
        <ul class="dashboard__bottom__list dashboard-list">
          <li class="dashboard__bottom__list__item">
            <a href="dashboard.html"><i class="material-symbols-outlined">dashboard</i> <span class="icon_title">Dashboard</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="inventory.html"><i class="material-symbols-outlined">inventory</i> <span class="icon_title">Inventory</span></a>
          </li>
          <li class="dashboard__bottom__list__item has-children show open active">
            <a href="#1"><i class="material-symbols-outlined">barcode_scanner</i> <span class="icon_title">Point of Sale</span></a>
            <ul class="submenu">
              <li class="dashboard__bottom__list__item selected">
                <a href="pos_system.html">POS System</a>
              </li>
              <li class="dashboard__bottom__list__item">
                <a href="branches.html">Branches</a>
              </li>
            </ul>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="messages.html"><i class="material-symbols-outlined">mail</i> <span class="icon_title">Messages</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="orders.html"><i class="material-symbols-outlined">list_alt</i> <span class="icon_title">Orders</span> <span class="badge_notification"> 10 </span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="customers.html"><i class="material-symbols-outlined">group</i> <span class="icon_title">Customers</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="marketing.html"><i class="material-symbols-outlined">campaign</i> <span class="icon_title">Marketing</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="support.html"><i class="material-symbols-outlined">help_center</i> <span class="icon_title">Support</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="invoice.html"><i class="material-symbols-outlined">receipt_long</i> <span class="icon_title">Invoice</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="integration.html"><i class="material-symbols-outlined">webhook</i> <span class="icon_title">Integrations</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="settings.html"><i class="material-symbols-outlined">settings</i> <span class="icon_title">Settings</span></a>
          </li>
          <li class="dashboard__bottom__list__item">
            <a href="#1"><i class="material-symbols-outlined">logout</i> <span class="icon_title">Log Out</span></a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LeftSidebar"
}
</script>

<style scoped>

</style>