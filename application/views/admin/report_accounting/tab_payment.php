<style>
    .payment-card{

        border: none;

        border-radius: 24px;

        overflow: hidden;

        background: white;

        box-shadow:
            0 10px 30px rgba(15,23,42,.08);

        margin-bottom: 28px;

    }

    .payment-card-header{

        position: relative;

        overflow: hidden;

        padding: 32px;

        background:
            linear-gradient(
                135deg,
                #0f4cbd,
                #2563eb
            );

    }

    .payment-card-header::before{

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

    .payment-card-header *{

        position: relative;

        z-index: 2;

    }

    .payment-title{

        font-size: 34px;

        font-weight: 800;

        color: white;

        letter-spacing: .5px;

        margin-bottom: 24px;

    }

    .payment-info-table{

        width: 100%;
        border: none !important;

    }

    .payment-info-table td{

        border: none !important;

        padding: 8px 0;

        color: rgba(255,255,255,.95);

        font-size: 14px;

    }

    .payment-info-table tr{
        border: none !important;
    }

    .payment-info-table tbody{
        border: none !important;
    }

    .payment-info-table .label{

        width: 140px;

        font-weight: 700;

        opacity: .9;

    }

    .payment-detail-wrapper{

        padding: 0;

        background: #fff;

    }

    .payment-detail-table{

        margin-bottom: 0;

    }

    .payment-detail-table thead th{

        background: #f8fafc;

        border: none;

        padding: 18px 16px;

        font-size: 12px;

        font-weight: 700;

        text-transform: uppercase;

        color: #64748b;

        letter-spacing: .5px;

    }

    .payment-detail-table tbody td{

        padding: 18px 16px;

        border-top:
            1px solid #eef2f7;

        vertical-align: middle;

        font-size: 14px;

        color: #334155;

    }

    .payment-detail-table tbody tr{

        transition: .2s ease;

    }

    .payment-detail-table tbody tr:hover{

        background: #f8fbff;

    }

    .payment-detail-table tbody tr:hover td{

        color: #0f172a;

    }

    .payment-detail-table small{

        display: block;

        margin-top: 4px;

        font-size: 11px;

        color: #94a3b8;

    }

    .payment-subtotal{

        background: #f8fafc;

    }

    .payment-subtotal td{

        font-weight: 700 !important;

        color: #0f172a !important;

        border-top:
            2px solid #e2e8f0 !important;

    }

    .summary-card{

        border: none;

        border-radius: 20px;

        overflow: hidden;

        box-shadow:
            0 6px 20px rgba(15,23,42,.06);

    }

    .summary-card .card-body{

        padding: 28px;

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

    .payment-badge{

        padding: 10px 16px;

        border-radius: 999px;

        font-size: 12px;

        font-weight: 700;

        letter-spacing: .5px;

    }
</style>

<div id="paymentReportWrapper">

<!-- ====================================================== -->
    <!-- SUMMARY -->
    <!-- ====================================================== -->

    <div class="row">

        <!-- TOTAL PAYMENT -->
        <div class="col-md-4">

            <div class="card summary-card bg-primary text-white">

                <div class="card-body">

                    <div class="small">

                        TOTAL PAYMENT

                    </div>

                    <h4 id="summaryPaymentTotal" class="summary-value">

                        Rp 0

                    </h4>

                </div>

            </div>

        </div>

        <!-- TOTAL SUPPLIER -->
        <div class="col-md-4">

            <div class="card summary-card bg-primary text-white">

                <div class="card-body">

                    <div class="small">

                        TOTAL SUPPLIER

                    </div>

                    <h4 id="summaryPaymentSupplier">

                        0

                    </h4>

                </div>

            </div>

        </div>

        <!-- TOTAL PO -->
        <div class="col-md-4">

            <div class="card summary-card bg-primary text-white">

                <div class="card-body">

                    <div class="small">

                        TOTAL PO

                    </div>

                    <h4 id="summaryPaymentPO">

                        0

                    </h4>

                </div>

            </div>

        </div>

    </div>

    <!-- ====================================================== -->
    <!-- FILTER -->
    <!-- ====================================================== -->

    <div class="row g-2 mb-3">

        <!-- SEARCH -->
        <div class="col-md-3">

            <input
                type="text"
                id="paymentSearch"
                class="form-control"
                placeholder="Cari payment, supplier, PO...">

        </div>

        <!-- PLANT -->
        <div class="col-md-2">

            <select
                id="paymentPlant"
                class="form-control">

                <option value="">
                    Semua Plant
                </option>

            </select>

        </div>

        <!-- SUPPLIER -->
        <div class="col-md-3">

            <select
                id="paymentSupplier"
                class="form-control">

            </select>

        </div>

        <!-- DATE FROM -->
        <div class="col-md-2">

            <input
                type="date"
                id="paymentDateFrom"
                class="form-control"
                value="<?= date('Y-m-01'); ?>">

        </div>

        <!-- DATE TO -->
        <div class="col-md-2">

            <input
                type="date"
                id="paymentDateTo"
                class="form-control"
                value="<?= date('Y-m-d'); ?>">

        </div>

        <div class="col-md-10"></div>

        <!-- EXPORT -->
        <div class="col-md-2">

            <button
                type="button"
                class="btn btn-success w-100"
                id="btnExportPayment">

                Export Excel

            </button>

        </div>

    </div>

    

    <!-- ====================================================== -->
    <!-- RESULT -->
    <!-- ====================================================== -->

    <div id="paymentResult">

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

        <div id="paymentReportInfo"></div>

        <div id="paymentReportPagination"></div>

    </div>

</div>

<script>

    window.ReportPayment = (function(){

        let state = {

            page  : 1,

            limit : 20

        };

        let PAYMENT_REPORT_INITIALIZED = false;

        /*
        |--------------------------------------------------------------------------
        | INIT
        |--------------------------------------------------------------------------
        */

        function init()
        {
            if(PAYMENT_REPORT_INITIALIZED){

                return;

            }

            PAYMENT_REPORT_INITIALIZED = true;

            initSupplier();

            loadPlant();

            loadData();

            bindEvents();
        }

        /*
        |--------------------------------------------------------------------------
        | EVENT
        |--------------------------------------------------------------------------
        */

        function bindEvents()
        {
            $('#paymentSearch').off('keyup')
                .on('keyup', function(){

                    loadData(1);

                });

            $('#paymentPlant').off('change')
                .on('change', function(){

                    loadData(1);

                });

            $('#paymentSupplier').off('change')
                .on('change', function(){

                    loadData(1);

                });

            $('#paymentDateFrom').off('change')
                .on('change', function(){

                    loadData(1);

                });

            $('#paymentDateTo').off('change')
                .on('change', function(){

                    loadData(1);

                });

            $('#btnExportPayment').off('click')
            .on('click', function(){

                let url =
                    '<?= base_url("report_accounting/export_payment_excel"); ?>'
                    +
                    '?search='
                    + encodeURIComponent(
                        $('#paymentSearch').val()
                    )

                    +
                    '&plant='
                    + encodeURIComponent(
                        $('#paymentPlant').val()
                    )

                    +
                    '&supplier='
                    + encodeURIComponent(
                        $('#paymentSupplier').val()
                    )

                    +
                    '&date_from='
                    + encodeURIComponent(
                        $('#paymentDateFrom').val()
                    )

                    +
                    '&date_to='
                    + encodeURIComponent(
                        $('#paymentDateTo').val()
                    );

                window.open(
                    url,
                    '_blank'
                );

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

                '<?= base_url("report-accounting/load_payment"); ?>',

                {

                    page      : state.page,

                    limit     : state.limit,

                    search    : $('#paymentSearch').val(),

                    plant     : $('#paymentPlant').val(),

                    supplier  : $('#paymentSupplier').val(),

                    date_from : $('#paymentDateFrom').val(),

                    date_to   : $('#paymentDateTo').val()

                },

                function(res){

                    if(typeof res === 'string'){

                        res = JSON.parse(res);

                    }

                    renderPaymentTable(res.rows);

                    renderSummaryPayment(res.summary);

                    $('#paymentReportPagination')
                        .html(res.pagination);

                    $('#paymentReportInfo')
                        .html(
                            `
                                Total :
                                <b>${res.total}</b>
                                data
                            `
                        );

                },

                'json'

            );
        }

        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        function formatDate(date)
        {
            if(!date){

                return '-';

            }

            let d =
                new Date(date);

            if(isNaN(d.getTime())){

                return date;

            }

            return d.toLocaleDateString(
                'id-ID',
                {

                    day   : '2-digit',

                    month : 'short',

                    year  : 'numeric'

                }
            );
        }

        function formatQty(value)
        {
            return Number(
                value || 0
            ).toLocaleString(
                'id-ID',
                {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }
            );
        }

        function formatWeight(value)
        {
            return Number(
                value || 0
            ).toLocaleString(
                'id-ID',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
        }

        function formatCurrency(value)
        {
            return Number(
                value || 0
            ).toLocaleString(
                'id-ID'
            );
        }

        function renderPaymentTable(rows)
        {
            let wrapper =
                $('#paymentResult');

            wrapper.html('');

            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if(rows.length === 0){

                wrapper.html(`

                    <div class="card border-0 shadow-lg">

                        <div class="card-body text-center py-5 text-muted">

                            Tidak ada data payment

                        </div>

                    </div>

                `);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | LOOP HEADER
            |--------------------------------------------------------------------------
            */

            rows.forEach(function(row){

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                let statusBadge = `
                    <span class="badge bg-success payment-badge">
                        COMPLETED
                    </span>
                `;

                /*
                |--------------------------------------------------------------------------
                | DETAIL ROWS
                |--------------------------------------------------------------------------
                */

                let detailRows = '';

                let subtotalQty    = 0;
                let subtotalWeight = 0;
                let subtotalTotal  = 0;

                /*
                |--------------------------------------------------------------------------
                | DETAIL LOOP
                |--------------------------------------------------------------------------
                */

                (row.DETAILS || []).forEach(function(d){

                    subtotalQty +=
                        Number(d.JUMLAH || 0);

                    subtotalWeight +=
                        Number(d.BERAT || 0);

                    subtotalTotal +=
                        Number(d.TOTAL || 0);

                    detailRows += `

                        <tr>

                            <!-- PO -->
                            <td>

                                <div class="fw-semibold text-primary">

                                    #${d.PO_NO || '-'}

                                </div>

                            </td>

                            <!-- MATERIAL -->
                            <td>

                                <div>

                                    ${d.MATERIAL_NAME || '-'}

                                </div>

                                <small class="text-muted">

                                    TYPE :
                                    ${d.TYPE || '-'}

                                </small>

                            </td>

                            <!-- QTY -->
                            <td class="text-end">

                                ${formatQty(d.JUMLAH || 0)}

                            </td>

                            <!-- BERAT -->
                            <td class="text-end">

                                ${formatWeight(d.BERAT || 0)}

                            </td>

                            <!-- HARGA -->
                            <td class="text-end">

                                ${formatCurrency(d.HARGA || 0)}

                            </td>

                            <!-- TOTAL -->
                            <td class="text-end fw-bold">

                                ${formatCurrency(d.TOTAL || 0)}

                            </td>

                            <!-- REMARK -->
                            <td>

                                ${d.REMARK || '-'}

                            </td>

                        </tr>

                    `;
                });

                /*
                |--------------------------------------------------------------------------
                | CARD
                |--------------------------------------------------------------------------
                */

                let card = `

                    <div class="payment-card">

                        <!-- HEADER -->
                        <div class="payment-card-header p-4">

                            <div class="d-flex justify-content-between">

                                <div>

                                    <h3 class="payment-title">

                                        #${row.PAYMENT}

                                    </h3>

                                </div>

                                <div>

                                    ${statusBadge}

                                </div>

                            </div>

                            <div class="row">

                                <!-- LEFT -->
                                <div class="col-md-6">

                                    <table class="payment-info-table">

                                        <tr>
                                            <td class="label">PLANT</td>
                                            <td>
                                                :
                                                ${row.PLANT_NAME || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>SUPPLIER</b></td>
                                            <td>
                                                :
                                                ${row.SUPPLIER_NAME || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>PAYMENT</b></td>
                                            <td>
                                                :
                                                ${row.PEMBAYARAN || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>SLIP NO</b></td>
                                            <td>
                                                :
                                                ${row.SLIP_NO || '-'}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>REMARK</b></td>
                                            <td>
                                                :
                                                ${row.REMARK || '-'}
                                            </td>
                                        </tr>

                                    </table>

                                </div>

                                <!-- RIGHT -->
                                <div class="col-md-6">

                                    <table class="payment-info-table">

                                        <tr>
                                            <td width="140"><b>PAYMENT DATE</b></td>
                                            <td>
                                                :
                                                ${formatDate(row.PAYMENT_DATE)}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>TOTAL ITEM</b></td>
                                            <td>
                                                :
                                                ${row.TOTAL_ITEM || 0}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><b>GRAND TOTAL</b></td>
                                            <td>
                                                :
                                                Rp ${formatCurrency(row.GRAND_TOTAL || 0)}
                                            </td>
                                        </tr>

                                    </table>

                                </div>

                            </div>

                        </div>

                        <!-- DETAIL -->
                        <div class="table-responsive payment-detail-wrapper">

                            <table class="table payment-detail-table align-middle">

                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            PO
                                        </th>

                                        <th>
                                            Material
                                        </th>

                                        <th class="text-end">
                                            Qty
                                        </th>

                                        <th class="text-end">
                                            Berat
                                        </th>

                                        <th class="text-end">
                                            Harga
                                        </th>

                                        <th class="text-end">
                                            Total
                                        </th>

                                        <th>
                                            Remark
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    ${detailRows}

                                    <!-- SUBTOTAL -->
                                    <tr class="payment-subtotal">

                                        <td colspan="2">

                                            SUBTOTAL

                                        </td>

                                        <td class="text-end">

                                            ${formatQty(subtotalQty)}

                                        </td>

                                        <td class="text-end">

                                            ${formatWeight(subtotalWeight)}

                                        </td>

                                        <td></td>

                                        <td class="text-end">

                                            ${formatCurrency(subtotalTotal)}

                                        </td>

                                        <td></td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                `;

                wrapper.append(card);

            });
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        function renderSummaryPayment(summary)
        {
            $('#summaryPaymentTotal')
                .html(
                    'Rp ' +
                    formatNumber(
                        summary.total_payment || 0
                    )
                );

            $('#summaryPaymentSupplier')
                .html(
                    summary.total_supplier || 0
                );

            $('#summaryPaymentPO')
                .html(
                    summary.total_po || 0
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

                        $('#paymentPlant').append(`

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
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        function initSupplier()
        {
            $('#paymentSupplier').select2({

                placeholder:
                    '-- Semua Supplier --',

                allowClear: true,

                width: '100%',

                ajax: {

                    url:
                        '<?= base_url("payment/get_supplier"); ?>',

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

        /*
        |--------------------------------------------------------------------------
        | FORMAT
        |--------------------------------------------------------------------------
        */

        function formatNumber(value)
        {
            return Number(
                value || 0
            ).toLocaleString('id-ID');
        }

        return {

            init : init,

            loadData : loadData

        };

    })();

    $(document).ready(function(){

        if(window.ReportPayment){

            ReportPayment.init();

        }

    });

</script>