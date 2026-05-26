<div class="container-fluid">

    <div class="card w-100">

        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">

                REPORT ACCOUNTING

            </h5>

            <!-- ====================================================== -->
            <!-- TAB NAV -->
            <!-- ====================================================== -->

            <ul class="nav nav-tabs mb-3" id="reportAccountingTab">

                <!-- COST -->
                <li class="nav-item">

                    <a
                        class="nav-link active"
                        data-bs-toggle="tab"
                        href="#tab-cost">

                        Report Cost

                    </a>

                </li>

                <!-- PAYMENT -->
                <li class="nav-item">

                    <a
                        class="nav-link"
                        data-bs-toggle="tab"
                        href="#tab-payment">

                        Report Payment

                    </a>

                </li>

                <!-- CASH IN -->
                <li class="nav-item">

                    <a
                        class="nav-link"
                        data-bs-toggle="tab"
                        href="#tab-cashin">

                        Report Cash In

                    </a>

                </li>

            </ul>

            <!-- ====================================================== -->
            <!-- TAB CONTENT -->
            <!-- ====================================================== -->

            <div class="tab-content">

                <!-- ================================================== -->
                <!-- COST -->
                <!-- ================================================== -->

                <div
                    class="tab-pane fade show active"
                    id="tab-cost">

                    <?php
                    $this->load->view(
                        'admin/report_accounting/tab_cost'
                    );
                    ?>

                </div>

                <!-- ================================================== -->
                <!-- PAYMENT -->
                <!-- ================================================== -->

                <div
                    class="tab-pane fade"
                    id="tab-payment">

                    <?php
                    $this->load->view(
                        'admin/report_accounting/tab_payment'
                    );
                    ?>

                </div>

                <!-- ================================================== -->
                <!-- CASH IN -->
                <!-- ================================================== -->

                <div
                    class="tab-pane fade"
                    id="tab-cashin">

                    <?php
                    $this->load->view(
                        'admin/report_accounting/tab_cashin'
                    );
                    ?>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

document.addEventListener(

    'shown.bs.tab',

    function(event){

        const target =
            event.target.getAttribute('href');

        console.log(
            'TAB ACTIVE:',
            target
        );

        /*
        |--------------------------------------------------------------------------
        | COST
        |--------------------------------------------------------------------------
        */

        if(target === '#tab-cost'){

            if(window.ReportCost){

                ReportCost.init();

            }

        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        if(target === '#tab-payment'){

            if(window.ReportPayment){

                ReportPayment.init();

            }

        }

        /*
        |--------------------------------------------------------------------------
        | CASH IN
        |--------------------------------------------------------------------------
        */

        if(target === '#tab-cashin'){

            if(window.ReportCashIn){

                ReportCashIn.init();

            }

        }

    }

);

</script>