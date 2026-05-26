<style>
  :root {
  --primary: #e60012;     /* merah ABC */
  --primary-dark: #b8000e;
  --secondary: #0072bc;   /* biru ABC */
  --bg-soft: #f4f6fb;
  --text-dark: #1f2937;
  --text-light: #6b7280;
}

/* SIDEBAR */
.left-sidebar {
  background: linear-gradient(180deg, #1e3282 0%, #16245c 100%);
  color: #fff;
  width: 260px;
  transition: all 0.3s ease;
  box-shadow: 4px 0 20px rgba(0,0,0,0.1);
}

/* LOGO */
.brand-logo {
  padding: 15px;
}

.brand-logo img {
  width: 170px;
}

.brand-logo div {
  font-size: 14px !important;
  color: #fff !important;
}

/* USER PROFILE */
.left-sidebar img.rounded-circle {
  border: 3px solid rgba(255,255,255,0.3);
}

.left-sidebar h5 {
  color: #fff;
  font-size: 14px;
}

.left-sidebar p {
  color: #cbd5e1;
  font-size: 12px;
}

/* MENU */
.sidebar-link {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #cbd5e1 !important;
  transition: all 0.25s ease;
  border-radius: 10px;
}

/* HOVER */
.sidebar-link:hover {
  background: rgba(255,255,255,0.08);
  color: #fff !important;
  transform: translateX(5px);
}

/* ACTIVE */
.sidebar-link.active {
  background: linear-gradient(90deg, #4f46e5, #1e3282);
  color: #fff !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* SUB MENU */
.first-level {
  background: rgba(255,255,255,0.05) !important;
  padding: 5px;
}

.child-drop a {
  font-size: 12px;
  color: #cbd5e1 !important;
}

.child-drop a:hover {
  color: #fff !important;
}

/* ICON */
.sidebar-icon i {
  font-size: 18px;
}

/* ARROW ROTATE */
.custom-arrow {
  transition: transform 0.3s ease;
}

.custom-arrow.rotate {
  transform: rotate(90deg);
}

/* HEADER */
.app-header {
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.navbar {
  padding: 10px 20px;
}

/* PROFILE IMAGE */
.navbar img {
  border: 2px solid var(--primary);
}

.left-sidebar {
  background: transparent;
  border-right: 0px solid #e5e7eb;
  box-shadow: 2px 0 12px rgba(0,0,0,0.05);
}

/* BRAND */
.brand-logo div {
  color: var(--primary) !important;
}

/* USER */
.left-sidebar h5 {
  color: var(--text-dark);
}

.left-sidebar p {
  color: var(--text-light);
}

/* MENU DEFAULT */
.sidebar-link {
  color: var(--text-dark) !important;
  font-weight: 500;
}

/* HOVER */
.sidebar-link:hover {
  background: rgba(230, 0, 18, 0.08);
  color: var(--primary) !important;
  transform: translateX(4px);
}

/* ACTIVE MENU */
.sidebar-link.active {
  background: linear-gradient(90deg, #e60012, #ff4d4f);
  color: #fff !important;
  box-shadow: 0 4px 10px rgba(230, 0, 18, 0.3);
}

/* SUBMENU */
.first-level {
  background: #f9fafb !important;
}

/* SUBMENU ITEM */
.child-drop a {
  color: var(--text-light) !important;
}

.child-drop a:hover {
  color: var(--primary) !important;
}

.app-header {
    background: #ffffff00;
    border-bottom: 0px solid var(--secondary);
    box-shadow: none;
}

/* PROFILE */
.navbar img {
  border: 2px solid var(--primary);
}

.brand-logo img {
  width: 170px;
}

.brand-logo {
  padding: 15px 20px;
  justify-content: center;
  border-bottom: 3px solid #000;
  margin: 0px 10px
}

.nav-small-cap .hide-menu {
  font-size: 11px !important;
  color: #000;
  letter-spacing: 1px;
}

.sidebar-link:hover {
  background: linear-gradient(90deg, rgba(230,0,18,0.08), rgba(0,114,188,0.05));
}

.sidebar-link i {
  transition: 0.2s;
}

.sidebar-link:hover i {
  color: var(--primary);
  transform: scale(1.1);
}

/* RESPONSIVE */
@media (max-width: 992px) {
  .left-sidebar {
    position: fixed;
    left: -260px;
  }

  .left-sidebar.show {
    left: 0;
  }
}
</style>

<aside class="left-sidebar">
  <div>
    <div class="brand-logo d-flex align-items-center">
      <a href="<?= base_url('dashboard'); ?>" class="text-nowrap logo-img" style="display: flex;align-content: center; align-items: center">
        <img src="<?= base_url('assets/img/abc-trans.png'); ?>" alt="" /> 
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-8"></i>
      </div>
    </div>
    <div style="display: flex; align-items: center; justify-content: center; align-content: center; padding: 20px 5px; border-bottom: 3px solid #000; margin: 0px 10px;">
      <img src="<?= base_url('assets/img/user-1.jpg'); ?>" alt="" width="60" height="60" class="rounded-circle">
      <div style="display: flex; flex-direction: column; text-align: left; justify-content: center; align-items: center">
        <h5 style="margin-top: 10px; font-weight: 700; text-transform: uppercase; text-align: center; margin-bottom: 0px"><?= $this->session->userdata('name'); ?></h5>
        <p style="margin-bottom: 0px; font-weight: 700; text-transform: capitalize"><?= $this->session->userdata('username'); ?></p>
      </div>
      
      <!-- <p style="margin-bottom: 0px; font-weight: 700; text-transform: capitalize"><?= $this->session->userdata('plant_name'); ?></p> -->
    </div>
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      
      <ul id="sidebarnav">
        <!-- <li class="nav-small-cap">
          <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
          <span class="hide-menu">Home</span>
        </li> -->
        <style>
        .custom-arrow.rotate {
            transform: rotate(90deg);
        }
        </style>
        <li class="sidebar-item" style="margin-top: 24px">
          <a class="sidebar-link" href="<?= base_url('dashboard'); ?>" aria-expanded="false" style="border-radius: 10px; padding: 10px">
            <span><i class="ti ti-layout-dashboard"></i></span>
            <span class="hide-menu" style="font-weight: 700; font-size: 12px;">DASHBOARD</span>
          </a>
        </li>

        <?php if (
            has_permission('base_general_code') ||
            has_permission('base_account') ||
            has_permission('base_cost') ||
            has_permission('base_item') ||
            has_permission('base_material') ||
            has_permission('base_customer')
        ): ?>
        <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
            <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                    <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">BASE DATA</span>
                </div> 

                <!-- ARROW -->
                <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
            </a>

            <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
              <?php if (has_permission('base_general_code')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('code'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">General Code</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('base_account')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('account'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Account</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('base_cost')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('cost'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Cost</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('base_item')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('item'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Item</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('base_material')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('Material'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Material</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('base_customer')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('Customer'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Customer</span>
                    </a>
                </li>
              <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (
            has_permission('inventory_po') ||
            has_permission('inventory_receive') ||
            has_permission('inventory_receive_lb') ||
            has_permission('inventory_material_balance') ||
            has_permission('report_inventory_po') ||
            has_permission('report_inventory_receive') ||
            has_permission('report_inventory_receive_lb') ||
            has_permission('report_inventory_material_balance')
        ): ?>
        <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
            <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                    <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">INVENTORY</span>
                </div>

                <!-- ARROW -->
                <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
            </a>

            <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
              <?php if (has_permission('inventory_po')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('po'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">PO</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('inventory_receive')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('receive'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Receive</span>
                    </a>
                </li>
              <?php endif; ?>

              <!-- <?php if (has_permission('inventory_receive_lb')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('receive-lb'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Receive LB</span>
                    </a>
                </li>
              <?php endif; ?> -->

              <?php if (has_permission('report_inventory_po') || has_permission('report_inventory_receive') || has_permission('report_inventory_receive_lb') || has_permission('report_inventory_material_balance')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('report-inventory'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Report Inventory</span>
                    </a>
                </li>
              <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (
            has_permission('productions_production') ||
            has_permission('productions_stock_actual') ||
            has_permission('productions_item_balance') ||
            has_permission('report_productions_production') ||
            has_permission('report_productions_stock_actual') ||
            has_permission('report_productions_item_balance')
        ): ?>
        <!-- <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
            <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                    <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">PRODUCTION</span>
                </div>
                <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
            </a>

            <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
                <?php if (has_permission('productions_production')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('production'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Production</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('productions_stock_actual')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('stock-actual'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Stock Actual</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('productions_moving')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('moving'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Moving</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('productions_process')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('reprocess'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Reprocess</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('report_productions_production') || has_permission('report_productions_stock_actual') || has_permission('report_productions_item_balance')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-production'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Report Production</span>
                      </a>
                  </li>
                <?php endif; ?>
              </ul>

        </li> -->
        <?php endif; ?>

        <?php if (
            has_permission('accounting_cost_entry') ||
            has_permission('accounting_payment_entry') ||
            has_permission('accounting_cash_in') ||
            has_permission('report_accounting_cost') ||
            has_permission('report_accounting_payment') ||
            has_permission('report_accounting_cash_in')
        ): ?>
          <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
              <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                  <div style="display: flex; align-items: center;">
                      <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                      <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">ACCOUNTING</span>
                  </div>

                  <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
              </a>

              <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
                <?php if (has_permission('accounting_cost_entry')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('mcost'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Cost Entry</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('accounting_payment_entry')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('payment'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Payment Entry</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('accounting_cash_in')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('cash-in'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Cash In Entry</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('report_accounting_cost') || has_permission('report_accounting_payment') || has_permission('report_accounting_cash_in')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-accounting'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Report Accounting</span>
                      </a>
                  </li>
                <?php endif; ?>
              </ul>
          </li>
        <?php endif; ?>

        <?php if (
            has_permission('productions_sales') ||
            has_permission('repot_productions_sales')
        ): ?>
        <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
            <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                    <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">SALES</span>
                </div>

                <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
            </a>

            <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
                <?php if (has_permission('productions_sales')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('sales'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Sales</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('report_productions_sales')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-sales'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Report Sales</span>
                      </a>
                  </li>
                <?php endif; ?>
              </ul>

        </li>
        <?php endif; ?>

        <?php if (
            has_permission('closing_process') ||
            has_permission('closing_cost') ||
            has_permission('closing_inventory_price') ||
            has_permission('closing_pl') ||
            has_permission('closing_sales_pl')
        ): ?>
          <!-- <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
              <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                  <div style="display: flex; align-items: center;">
                      <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                      <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">CLOSING</span>
                  </div>

                  <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
              </a>

              <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
                <?php if (has_permission('closing_process')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('closing-process'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Closing Process</span>
                      </a>
                  </li>
                <?php endif; ?>
                
                <?php if (has_permission('closing_inventory_price')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-closing-inventory-price'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Closing Inventory Price</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('closing_cost')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-closing-cost'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Closing Cost</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('closing_sales_pl')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-closing-sales-pl'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Closing Sales PL</span>
                      </a>
                  </li>
                <?php endif; ?>

                <?php if (has_permission('closing_pl')): ?>
                  <li class="sidebar-item child-drop">
                      <a href="<?= base_url('report-closing-pl'); ?>" class="sidebar-link">
                          <i class="ti ti-dots"></i>
                          <span class="hide-menu">Closing PL</span>
                      </a>
                  </li>
                <?php endif; ?>

              </ul>
          </li> -->
        <?php endif; ?>

        <?php if (
            has_permission('settings_users') ||
            has_permission('settings_roles')
        ): ?>
        <!-- <li class="sidebar-item nav-small-cap" style="padding: 0px; margin-top: 15px">
            <a class="sidebar-link custom-arrow-toggle" href="javascript:void(0)" style="border-radius: 10px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <span class="sidebar-icon"><i class="ti ti-database"></i></span>
                    <span class="hide-menu" style="font-weight: 700; font-size: 12px; margin-left: 8px;">SETTINGS</span>
                </div>

                <i class="ti ti-chevron-right custom-arrow" style="transition: 0.3s; font-size: 14px;"></i>
            </a>

            <ul aria-expanded="false" class="collapse first-level" style="background: #efefef; border-radius:10px; margin-top: 7px">
              <?php if (has_permission('settings_users')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('users'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Users</span>
                    </a>
                </li>
              <?php endif; ?>

              <?php if (has_permission('settings_roles')): ?>
                <li class="sidebar-item child-drop">
                    <a href="<?= base_url('roles'); ?>" class="sidebar-link">
                        <i class="ti ti-dots"></i>
                        <span class="hide-menu">Roles</span>
                    </a>
                </li>
              <?php endif; ?>
            </ul>
        </li> -->
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</aside>
<div class="body-wrapper">
  <!-- Header -->
  <header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item d-block d-xl-none">
          <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link nav-icon-hover" href="javascript:void(0)">
            <i class="ti ti-bell-ringing"></i>
            <div class="notification bg-primary rounded-circle"></div>
          </a>
        </li> -->
      </ul>
      <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
          
          <li class="nav-item dropdown">
            <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
               aria-expanded="false">
              <img src="<?= base_url('assets/img/user-1.jpg'); ?>" alt="" width="40" height="40" class="rounded-circle" style="border: 3px solid #fff">
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
              <div class="message-body">
                <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                  <i class="ti ti-user fs-6"></i>
                  <p class="mb-0 fs-3">My Profile</p>
                </a>
                <a href="<?= base_url('auth/logout'); ?>" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </header>
