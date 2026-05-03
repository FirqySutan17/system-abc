<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT CLOSING INVENTORY PRICE
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-daily">
                        Report Daily Closing Inventory Price
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-monthly">
                        Report Monthly Closing Inventory Price
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-daily">
                    <?php $this->load->view('admin/report_closing_inventory_price/daily'); ?>
                </div>
                <div class="tab-pane fade" id="tab-monthly">
                    <?php $this->load->view('admin/report_closing_inventory_price/tab_monthly'); ?>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('shown.bs.tab', function (event) {
    const target = event.target.getAttribute('href');

    console.log('TAB AKTIF:', target);

    if (target === '#tab-daily') {
        if (window.DailyClosingInventoryPriceReport) {
            DailyClosingInventoryPriceReport.init();
            console.log('DailyClosingInventoryPriceReport global:', window.DailyClosingInventoryPriceReport);
        } else {
            console.warn('DailyClosingInventoryPriceReport belum tersedia');
            console.log('DailyClosingInventoryPriceReport global:', window.DailyClosingInventoryPriceReport);
        }
    }

    if (target === '#tab-monthly') {
        if (window.MonthlyClosingInventoryPriceReport) {
            MonthlyClosingInventoryPriceReport.init();
            console.log('MonthlyClosingInventoryPriceReport global:', window.MonthlyClosingInventoryPriceReport);
        } else {
            console.warn('MonthlyClosingInventoryPriceReport belum tersedia');
            console.log('MonthlyClosingInventoryPriceReport global:', window.MonthlyClosingInventoryPriceReport);
        }
    }
});
</script>