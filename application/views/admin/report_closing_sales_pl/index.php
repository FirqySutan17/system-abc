<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT CLOSING SALES P/L
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-daily">
                        Report Daily Closing Sales P/L
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-monthly">
                        Report Monthly Closing Sales P/L
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-summary">
                        Summary
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-daily">
                    <?php $this->load->view('admin/report_closing_sales_pl/daily'); ?>
                </div>
                <div class="tab-pane fade" id="tab-monthly">
                    <?php $this->load->view('admin/report_closing_sales_pl/tab_monthly'); ?>
                </div>
                <div class="tab-pane fade" id="tab-summary">
                    <?php $this->load->view('admin/report_closing_sales_pl/tab_summary'); ?>
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
        if (window.DailyClosingSalesPlReport) {
            DailyClosingSalesPlReport.init();
            console.log('DailyClosingSalesPlReport global:', window.DailyClosingSalesPlReport);
        } else {
            console.warn('DailyClosingSalesPlReport belum tersedia');
            console.log('DailyClosingSalesPlReport global:', window.DailyClosingSalesPlReport);
        }
    }

    if (target === '#tab-monthly') {
        if (window.MonthlySalesPL) {
            MonthlySalesPL.init();
            console.log('MonthlySalesPL global:', window.MonthlySalesPL);
        } else {
            console.warn('MonthlySalesPL belum tersedia');
            console.log('MonthlySalesPL global:', window.MonthlySalesPL);
        }
    }

    if (target === '#tab-summary') {
        if (window.SummaryPL) {
            SummaryPL.init();
            console.log('SummaryPL global:', window.SummaryPL);
        } else {
            console.warn('SummaryPL belum tersedia');
            console.log('SummaryPL global:', window.SummaryPL);
        }
    }
});
</script>