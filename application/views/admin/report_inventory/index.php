<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT INVENTORY
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-po">
                        Report PO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-receive">
                        Report Receive
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-receive-lb">
                        Receive LB
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-balance">
                        Material Balance
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-po">
                    <?php $this->load->view('admin/report_inventory/po'); ?>
                </div>

                <div class="tab-pane fade" id="tab-receive">
                    <?php $this->load->view('admin/report_inventory/tab_receive'); ?>
                </div>

                <div class="tab-pane fade" id="tab-receive-lb">
                    <?php $this->load->view('admin/report_inventory/tab_receive_lb'); ?>
                </div>

                <div class="tab-pane fade" id="tab-balance">
                    <?php $this->load->view('admin/report_inventory/tab_material_balance'); ?>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('shown.bs.tab', function (event) {
    const target = event.target.getAttribute('href');

    console.log('TAB AKTIF:', target);

    if (target === '#tab-receive') {
        if (window.ReceiveReport) {
            ReceiveReport.init();
            console.log('ReceiveReport global:', window.ReceiveReport);
        } else {
            console.warn('ReceiveReport belum tersedia');
            console.log('ReceiveReport global:', window.ReceiveReport);
        }
    }

    if (target === '#tab-receive-lb') {
        if (window.ReceiveLBReport) {
            ReceiveLBReport.init(true); // force init
        }
    }
});
</script>