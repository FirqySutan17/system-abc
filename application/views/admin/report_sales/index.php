<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                REPORT SALES
            </h5>

            <!-- TAB NAV -->
            <ul class="nav nav-tabs mb-3" id="reportTab">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-sales">
                        Report Sales
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       data-bs-toggle="tab"
                       href="#tab-item">
                        Report Sales by Item
                    </a>
                </li>
            </ul>

            <!-- TAB CONTENT -->
            <div class="tab-content">

                <div class="tab-pane fade show active" id="tab-sales">
                    <?php $this->load->view('admin/report_sales/sales'); ?>
                </div>

                <div class="tab-pane fade" id="tab-item">
                    <?php $this->load->view('admin/report_sales/tab_item');?>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('shown.bs.tab', function (event) {
    const target = event.target.getAttribute('href');

    console.log('TAB AKTIF:', target);

    if (target === '#tab-item') {
        if (typeof loadPageItem === 'function') {
            loadPageItem(1);
        }
    }
});
</script>