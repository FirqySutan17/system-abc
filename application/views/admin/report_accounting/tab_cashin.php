<style>

    .cashin-card{

        border: none;

        border-radius: 24px;

        overflow: hidden;

        background: white;

        box-shadow:
            0 10px 30px rgba(15,23,42,.08);

        margin-bottom: 28px;

    }

    .cashin-card-header{

        position: relative;

        overflow: hidden;

        padding: 32px;

        background:
            linear-gradient(
                135deg,
                #0f766e,
                #14b8a6
            );

    }

    .cashin-card-header::before{

        content: '';

        position: absolute;

        top: -80px;

        right: -80px;

        width: 240px;

        height: 240px;

        border-radius: 50%;

        background:
            rgba(255,255,255,.08);

    }

    .cashin-card-header *{

        position: relative;

        z-index: 2;

    }

    .cashin-title{

        font-size: 34px;

        font-weight: 800;

        color: white;

        letter-spacing: .5px;

        margin-bottom: 24px;

    }

    .cashin-info-table{

        width: 100%;

    }

    .cashin-info-table td{

        padding: 8px 0;

        border: none !important;

        color: rgba(255,255,255,.95);

        font-size: 14px;

    }

    .cashin-info-table .label{

        width: 150px;

        font-weight: 700;

    }

    .cashin-detail-table thead th{

        background: #f8fafc;

        border: none;

        padding: 18px 16px;

        font-size: 12px;

        font-weight: 700;

        text-transform: uppercase;

        color: #64748b;

    }

    .cashin-detail-table tbody td{

        padding: 18px 16px;

        border-top:
            1px solid #eef2f7;

        vertical-align: middle;

    }

    .cashin-subtotal{

        background: #f8fafc;

    }

    .cashin-subtotal td{

        font-weight: 700;

        border-top:
            2px solid #e2e8f0 !important;

    }

</style>

<div id="cashinReportWrapper">

    <!-- ====================================================== -->
    <!-- SUMMARY -->
    <!-- ====================================================== -->

    <div class="row mb-4">

        <div class="col-md-3">

            <div class="card summary-card bg-primary text-white">

                <div class="card-body">

                    <div class="small">

                        TOTAL CASH IN

                    </div>

                    <h4 id="summaryCashinTotal">

                        Rp 0

                    </h4>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card summary-card bg-success text-white">

                <div class="card-body">

                    <div class="small">

                        TOTAL CUSTOMER

                    </div>

                    <h4 id="summaryCashinCustomer">

                        0

                    </h4>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card summary-card bg-warning text-dark">

                <div class="card-body">

                    <div class="small">

                        TOTAL INVOICE

                    </div>

                    <h4 id="summaryCashinInvoice">

                        0

                    </h4>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card summary-card bg-danger text-white">

                <div class="card-body">

                    <div class="small">

                        TOTAL DEPOSIT

                    </div>

                    <h4 id="summaryCashinDeposit">

                        Rp 0

                    </h4>

                </div>

            </div>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- FILTER -->
    <!-- ====================================================== -->

    <div class="row g-2 mb-4">

        <!-- SEARCH -->
        <div class="col-md-3">

            <input
                type="text"
                id="cashinSearch"
                class="form-control"
                placeholder="Cari cash in, customer, sales...">

        </div>

        <!-- PLANT -->
        <div class="col-md-2">

            <select
                id="cashinPlant"
                class="form-control">

                <option value="">
                    Semua Plant
                </option>

            </select>

        </div>

        <!-- CUSTOMER -->
        <div class="col-md-3">

            <select
                id="cashinCustomer"
                class="form-control">

            </select>

        </div>

        <!-- PAYMENT -->
        <div class="col-md-2">

            <select
                id="cashinPayment"
                class="form-control">

                <option value="">
                    Semua Payment
                </option>

                <option value="CASH">
                    CASH
                </option>

                <option value="TRANSFER">
                    TRANSFER
                </option>

            </select>

        </div>

        <!-- MODE -->
        <div class="col-md-2">

            <select
                id="cashinMode"
                class="form-control">

                <option value="">
                    Semua Mode
                </option>

                <option value="FIFO">
                    FIFO
                </option>

                <option value="MANUAL">
                    MANUAL
                </option>

            </select>

        </div>

        <!-- DATE FROM -->
        <div class="col-md-2">

            <input
                type="date"
                id="cashinDateFrom"
                class="form-control"
                value="<?= date('Y-m-01'); ?>">

        </div>

        <!-- DATE TO -->
        <div class="col-md-2">

            <input
                type="date"
                id="cashinDateTo"
                class="form-control"
                value="<?= date('Y-m-d'); ?>">

        </div>

        <div class="col-md-6"></div>

        <!-- EXPORT -->
        <div class="col-md-2">

            <button
                type="button"
                class="btn btn-success w-100"
                id="btnExportCashin">

                Export Excel

            </button>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- RESULT -->
    <!-- ====================================================== -->

    <div id="cashinResult">

        <div class="card border-0 shadow-lg">

            <div class="card-body text-center py-5 text-muted">

                Belum ada data

            </div>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- PAGINATION -->
    <!-- ====================================================== -->

    <div class="d-flex justify-content-between mt-3">

        <div id="cashinReportInfo"></div>

        <div id="cashinReportPagination"></div>

    </div>

</div>

<script>

    window.ReportCashin = (function(){

        let state = {

            page  : 1,

            limit : 20

        };

        let INITIALIZED = false;

        /*
        |--------------------------------------------------------------------------
        | INIT
        |--------------------------------------------------------------------------
        */

        function init()
        {
            if(INITIALIZED){

                return;

            }

            INITIALIZED = true;

            loadPlant();

            initCustomer();

            bindEvents();

            loadData();
        }

        /*
        |--------------------------------------------------------------------------
        | EVENTS
        |--------------------------------------------------------------------------
        */

        function bindEvents()
        {
            $('#cashinSearch').on('keyup', function(){

                loadData(1);

            });

            $('#cashinPlant').on('change', function(){

                loadData(1);

            });

            $('#cashinCustomer').on('change', function(){

                loadData(1);

            });

            $('#cashinPayment').on('change', function(){

                loadData(1);

            });

            $('#cashinMode').on('change', function(){

                loadData(1);

            });

            $('#cashinDateFrom').on('change', function(){

                loadData(1);

            });

            $('#cashinDateTo').on('change', function(){

                loadData(1);

            });
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        function loadData(page = 1)
        {
            state.page = page;

            $.get(

                '<?= base_url("report-accounting/load_cashin"); ?>',

                {

                    page      : state.page,

                    limit     : state.limit,

                    search    : $('#cashinSearch').val(),

                    plant     : $('#cashinPlant').val(),

                    customer  : $('#cashinCustomer').val(),

                    pembayaran: $('#cashinPayment').val(),

                    mode      : $('#cashinMode').val(),

                    date_from : $('#cashinDateFrom').val(),

                    date_to   : $('#cashinDateTo').val()

                },

                function(res){

                    console.log(res);

                },

                'json'

            );
        }

        /*
        |--------------------------------------------------------------------------
        | PLANT
        |--------------------------------------------------------------------------
        */

        function loadPlant()
        {
            $.get(

                '<?= base_url("payment/get_plant"); ?>',

                function(rows){

                    rows.forEach(function(r){

                        $('#cashinPlant').append(`

                            <option value="${r.id}">

                                ${r.text}

                            </option>

                        `);

                    });

                },

                'json'

            );
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        function initCustomer()
        {
            $('#cashinCustomer').select2({

                placeholder:
                    '-- Semua Customer --',

                allowClear: true,

                width: '100%',

                ajax: {

                    url:
                        '<?= base_url("cashin/get_customer"); ?>',

                    dataType: 'json',

                    delay: 250,

                    data: function(params){

                        return {

                            q: params.term

                        };

                    },

                    processResults: function(data){

                        return {

                            results: data

                        };

                    }

                }

            });
        }

        return {

            init : init,

            loadData : loadData

        };

    })();

    $(document).ready(function(){

        if(window.ReportCashin){

            ReportCashin.init();

        }

    });

</script>