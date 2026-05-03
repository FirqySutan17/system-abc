<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT PRODUCTION
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-production">
                        Report Production
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-stock-actual">
                        Report Stock Actual
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-item-balance">
                        Report Item Balance
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-production">
                    <?php $this->load->view('admin/report_production/production'); ?>
                </div>

                <div class="tab-pane fade" id="tab-stock-actual">
                    <?php $this->load->view('admin/report_production/tab_stock_actual'); ?>
                </div>

                <div class="tab-pane fade" id="tab-item-balance">
                    <?php $this->load->view('admin/report_production/tab_item_balance'); ?>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('shown.bs.tab', function (event) {
    const target = event.target.getAttribute('href');

    console.log('TAB AKTIF:', target);

    if (target === '#tab-stock-actual') {
        if (window.StockActualReport) {
            StockActualReport.init();
            console.log('StockActualReport global:', window.StockActualReport);
        } else {
            console.warn('StockActualReport belum tersedia');
            console.log('StockActualReport global:', window.StockActualReport);
        }
    }

    if (target === '#tab-item-balance') {
        if (window.ItemBalanceReport) {
            ItemBalanceReport.init();
            console.log('ItemBalanceReport global:', window.ItemBalanceReport);
        } else {
            console.warn('ItemBalanceReport belum tersedia');
            console.log('ItemBalanceReport global:', window.ItemBalanceReport);
        }
    }
});
</script>