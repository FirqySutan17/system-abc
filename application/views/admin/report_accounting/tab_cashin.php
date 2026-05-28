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

    .cashin-info-table tr{
        border: none !important;
    }

    .cashin-info-table tbody{
        border: none !important;
    }

    .cashin-badge{

        padding: 10px 16px;

        border-radius: 999px;

        font-size: 12px;

        font-weight: 700;

        letter-spacing: .5px;

    }

    .summary-title{

        font-size: 13px;

        font-weight: 600;

        opacity: .9;

        margin-bottom: 10px;

    }

    .summary-value{

        font-size: 29px;

        font-weight: 800;

        line-height: 1;

        color: #fff;

    }

    .cashin-title {
        font-size: 34px;
        font-weight: 800;
        color: white;
        letter-spacing: .5px;
        margin-bottom: 24px;
    }

</style>

<div id="cashinReportWrapper">

    <!-- ====================================================== -->
    <!-- SUMMARY -->
    <!-- ====================================================== -->

    <div class="row mb-4">

        <div class="col-md-4">
            <div class="report-card bg-success">

                <div class="report-card-title">

                    TOTAL CASH IN

                </div>

                <div
                    class="report-card-value"
                    id="summaryCashinTotal">

                    0

                </div>

            </div>

        </div>

        <div class="col-md-4">
            <div class="report-card bg-success">

                <div class="report-card-title">

                    TOTAL CUSTOMER

                </div>

                <div
                    class="report-card-value"
                    id="summaryCashinCustomer">

                    0

                </div>

            </div>

        </div>

        <div class="col-md-4">
            <div class="report-card bg-success">

                <div class="report-card-title">

                    TOTAL INVOICE

                </div>

                <div
                    class="report-card-value"
                    id="summaryCashinInvoice">

                    0

                </div>

            </div>

        </div>

        <!-- <div class="col-md-3">

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

        </div> -->

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

            $(document).on(

                'click',

                '#cashinReportPagination a',

                function(e){

                    e.preventDefault();

                    let page =
                        $(this).data('ci-pagination-page');

                    if(!page){

                        return;

                    }

                    loadData(page);

                }

            );

            $('#btnExportCashin').on(

                'click',

                function(){

                    let params =
                        $.param({

                            search:
                                $('#cashinSearch').val(),

                            plant:
                                $('#cashinPlant').val(),

                            customer:
                                $('#cashinCustomer').val(),

                            pembayaran:
                                $('#cashinPayment').val(),

                            mode:
                                $('#cashinMode').val(),

                            date_from:
                                $('#cashinDateFrom').val(),

                            date_to:
                                $('#cashinDateTo').val()

                        });

                    window.open(

                        '<?= base_url("report-accounting/export_excel_cashin"); ?>?'
                        + params,

                        '_blank'

                    );

                }

            );
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

                    /*
                    |--------------------------------------------------------------------------
                    | SUMMARY
                    |--------------------------------------------------------------------------
                    */

                    renderSummary(
                        res.summary || {}
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | TABLE
                    |--------------------------------------------------------------------------
                    */

                    renderTable(
                        res.rows || []
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | PAGINATION
                    |--------------------------------------------------------------------------
                    */

                    $('#cashinReportPagination')
                        .html(
                            res.pagination || ''
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | INFO
                    |--------------------------------------------------------------------------
                    */

                    $('#cashinReportInfo').html(`

                        Total :
                        <b>${res.total || 0}</b>
                        data

                    `);

                },

                'json'

            );
        }

        function renderSummary(summary)
        {
            $('#summaryCashinTotal').html(
                'Rp ' +
                formatRupiah(
                    summary.TOTAL_CASHIN || 0
                )
            );

            $('#summaryCashinCustomer').html(
                formatRupiah(
                    summary.TOTAL_CUSTOMER || 0
                )
            );

            $('#summaryCashinInvoice').html(
                formatRupiah(
                    summary.TOTAL_INVOICE || 0
                )
            );

            $('#summaryCashinDeposit').html(
                'Rp ' +
                formatRupiah(
                    summary.TOTAL_DEPOSIT || 0
                )
            );
        }

        function renderTable(rows)
        {
            let wrapper =
                $('#cashinResult');

            wrapper.html('');

            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if(rows.length === 0){

                wrapper.html(`

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center py-5 text-muted">

                            Belum ada data cash in

                        </div>

                    </div>

                `);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | LOOP
            |--------------------------------------------------------------------------
            */

            rows.forEach(function(row){

                let detailRows = '';

                /*
                |--------------------------------------------------------------------------
                | DETAIL
                |--------------------------------------------------------------------------
                */

                (row.DETAILS || []).forEach(function(d){

                    let badge = `
                        <span class="cashin-badge badge bg-secondary">
                            OPEN
                        </span>
                    `;

                    if(d.SALES_STATUS === 'PAID'){

                        badge = `
                            <span class="cashin-badge badge bg-success">
                                PAID
                            </span>
                        `;
                    }
                    else if(
                        d.SALES_STATUS === 'PARTIAL'
                    ){

                        badge = `
                            <span class="cashin-badge badge bg-warning text-dark">
                                PARTIAL
                            </span>
                        `;
                    }

                    detailRows += `

                        <tr>

                            <td>

                                <div class="fw-bold text-primary">

                                    #${d.SALES}

                                </div>

                            </td>

                            <td class="text-end">

                                Rp
                                ${formatRupiah(
                                    d.AMOUNT_INVOICE || 0
                                )}

                            </td>

                            <td class="text-end">

                                Rp
                                ${formatRupiah(
                                    d.AMOUNT_OFFSET || 0
                                )}

                            </td>

                            <td class="text-end">

                                Rp
                                ${formatRupiah(
                                    d.REMAINING || 0
                                )}

                            </td>

                            <td class="text-center">

                                ${badge}

                            </td>

                        </tr>

                    `;
                });

                /*
                |--------------------------------------------------------------------------
                | CARD
                |--------------------------------------------------------------------------
                */

                wrapper.append(`

                    <div class="card border-0 shadow-lg mb-4 overflow-hidden">

                        <!-- HEADER -->
                        <div class="cashin-card-header p-4">

                            <div class="d-flex justify-content-between">

                                <h3 class="cashin-title">

                                    #${row.CASH_IN}

                                </h3>

                                <span class="badge cashin-badge">

                                    ${row.PEMBAYARAN || '-'}

                                </span>

                            </div>

                            <div class="row text-white">

                                <div class="col-md-6">

                                    <table class="table table-borderless text-white mb-0 cashin-info-table">

                                        <tr>
                                            <td width="140">
                                                <b>PLANT</b>
                                            </td>
                                            <td>
                                                :
                                                ${row.PLANT_NAME || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>CUSTOMER</b>
                                            </td>
                                            <td>
                                                :
                                                ${row.CUSTOMER_NAME || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>SLIP NO</b>
                                            </td>
                                            <td>
                                                :
                                                ${row.SLIP_NO || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>REMARK</b>
                                            </td>
                                            <td>
                                                :
                                                ${row.REMARK || '-'}
                                            </td>
                                        </tr>

                                    </table>

                                </div>

                                <div class="col-md-6">

                                    <table class="table table-borderless text-white mb-0 cashin-info-table">

                                        <tr>
                                            <td width="140">
                                                <b>DATE</b>
                                            </td>
                                            <td>
                                                :
                                                ${formatDate(
                                                    row.CASHIN_DATE
                                                )}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>TOTAL INVOICE</b>
                                            </td>
                                            <td>
                                                :
                                                ${row.TOTAL_INVOICE || 0}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>TOTAL CASH IN</b>
                                            </td>
                                            <td>
                                                :
                                                Rp
                                                ${formatRupiah(
                                                    row.AMOUNT || 0
                                                )}
                                            </td>
                                        </tr>

                                    </table>

                                </div>

                            </div>

                        </div>

                        <!-- DETAIL -->
                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>Sales</th>

                                        <th class="text-end">
                                            Invoice
                                        </th>

                                        <th class="text-end">
                                            Paid
                                        </th>

                                        <th class="text-end">
                                            Remaining
                                        </th>

                                        <th class="text-center">
                                            Status
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    ${detailRows}

                                </tbody>

                            </table>

                        </div>

                    </div>

                `);

            });
        }

        function formatDate(date)
        {
            if(!date){

                return '-';

            }

            return new Date(date)
                .toLocaleDateString(
                    'id-ID',
                    {
                        day   : '2-digit',
                        month : 'short',
                        year  : 'numeric'
                    }
                );
        }

        function formatRupiah(value)
        {
            return Number(
                value || 0
            ).toLocaleString(
                'id-ID'
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