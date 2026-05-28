<div class="container-fluid">

    <div class="card w-100">

        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">

                REPORT SALES

            </h5>

            <?php
                $this->load->view(
                    'admin/report_sales/sales'
                );
            ?>

        </div>

    </div>

</div>

<script>
document.addEventListener('shown.bs.tab', function (event) {
    const target = event.target.getAttribute('href');

    console.log('TAB AKTIF:', target);

});
</script>