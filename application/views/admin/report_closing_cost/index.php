<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <div class="mb-4">
                <h4 class="fw-bold mb-1">
                    Cost Closing Report
                </h4>

                <p class="text-muted mb-0">
                    Monthly cost
                </p>
            </div>

            <!-- TAB CONTENT -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-monthly">
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