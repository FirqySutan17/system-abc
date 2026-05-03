<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT ACCOUNTING
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-cost">
                        Report Cost
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-payment">
                        Report Payment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-cash-in">
                        Report Cash In
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-ar">
                        Report AR
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-daily-summary">
                        Daily Summary
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-cost">
                    <?php $this->load->view('admin/report_accounting/cost'); ?>
                </div>

                <div class="tab-pane fade" id="tab-payment">
                    <?php $this->load->view('admin/report_accounting/tab_payment'); ?>
                </div>

                <div class="tab-pane fade" id="tab-cash-in">
                    <?php $this->load->view('admin/report_accounting/tab_cash_in'); ?>
                </div>
                
                <div class="tab-pane fade" id="tab-ar">
                    <?php $this->load->view('admin/report_accounting/tab_ar'); ?>
                </div>

                <div class="tab-pane fade" id="tab-daily-summary">
                    <?php $this->load->view('admin/report_accounting/tab_daily_summary'); ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('shown.bs.tab', function (event) {
    const target = event.target.getAttribute('href');

    console.log('TAB AKTIF:', target);

    if (target === '#tab-payment') {
        if (window.PaymentReport) {
            PaymentReport.init();
            console.log('PaymentReport global:', window.PaymentReport);
        } else {
            console.warn('PaymentReport belum tersedia');
            console.log('PaymentReport global:', window.PaymentReport);
        }
    }

    if (target === '#tab-cash-in') {
        if (window.CashInReport) {
            CashInReport.init();
            console.log('CashInReport global:', window.CashInReport);
        } else {
            console.warn('CashInReport belum tersedia');
            console.log('CashInReport global:', window.CashInReport);
        }
    }

    if (target === '#tab-ar') {
        if (window.ARReport) {
            ARReport.init();
            console.log('ARReport global:', window.ARReport);
        } else {
            console.warn('ARReport belum tersedia');
            console.log('ARReport global:', window.ARReport);
        }
    }

    if (target === '#tab-daily-summary') {
        if (window.DailySummary) {
            DailySummary.init();
            console.log('DailySummary global:', window.DailySummary);
        } else {
            console.warn('Daily Summary belum tersedia');
            console.log('Daily Summary global:', window.DailySummary);
        }
    }
});
</script>