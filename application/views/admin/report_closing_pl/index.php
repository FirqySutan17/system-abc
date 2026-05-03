<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT CLOSING P/L
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-daily">
                        Report Daily Closing P/L
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-monthly">
                        Report Monthly Closing P/L
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-daily">
                    <?php $this->load->view('admin/report_closing_pl/daily'); ?>
                </div>
                <div class="tab-pane fade" id="tab-monthly">
                    <?php $this->load->view('admin/report_closing_pl/tab_monthly'); ?>
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
        if (window.DailyClosingPlReport) {
            DailyClosingPlReport.init();
            console.log('DailyClosingPlReport global:', window.DailyClosingPlReport);
        } else {
            console.warn('DailyClosingPlReport belum tersedia');
            console.log('DailyClosingPlReport global:', window.DailyClosingPlReport);
        }
    }

    if (target === '#tab-monthly') {
        if (window.MonthlyClosingPlReport) {
            MonthlyClosingPlReport.init();
            console.log('MonthlyClosingPlReport global:', window.MonthlyClosingPlReport);
        } else {
            console.warn('MonthlyClosingPlReport belum tersedia');
            console.log('MonthlyClosingPlReport global:', window.MonthlyClosingPlReport);
        }
    }
});
</script>