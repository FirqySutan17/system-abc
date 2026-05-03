<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT CLOSING COST
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-daily">
                        Report Daily Closing Cost
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-monthly">
                        Report Monthly Closing Cost
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-daily">
                    <?php $this->load->view('admin/report_closing_cost/daily'); ?>
                </div>
                <div class="tab-pane fade" id="tab-monthly">
                    <?php $this->load->view('admin/report_closing_cost/tab_monthly'); ?>
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
        if (window.DailyClosingCostReport) {
            DailyClosingCostReport.init();
            console.log('DailyClosingCostReport global:', window.DailyClosingCostReport);
        } else {
            console.warn('DailyClosingCostReport belum tersedia');
            console.log('DailyClosingCostReport global:', window.DailyClosingCostReport);
        }
    }

    if (target === '#tab-monthly') {
        if (window.MonthlyClosingCost) {
            MonthlyClosingCost.init();
            console.log('MonthlyClosingCost global:', window.MonthlyClosingCost);
        } else {
            console.warn('MonthlyClosingCost belum tersedia');
            console.log('MonthlyClosingCost global:', window.MonthlyClosingCost);
        }
    }
});
</script>