<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <div class="mb-4">
                <h4 class="fw-bold mb-1">
                    P/L Closing Report
                </h4>

                <p class="text-muted mb-0">
                    Monthly PL
                </p>
            </div>

            <!-- TAB CONTENT -->
            <div class="tab-content">
                <div class="tab-pane fades how active" id="tab-monthly">
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
    
    if (target === '#tab-monthly') {
        if (window.MonthlyClosingPL) {
            MonthlyClosingPL.init();
            console.log('MonthlyClosingPL global:', window.MonthlyClosingPL);
        } else {
            console.warn('MonthlyClosingPL belum tersedia');
            console.log('MonthlyClosingPL global:', window.MonthlyClosingPL);
        }
    }
});
</script>