<div id="cashinReportWrapper">

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


        <!-- EXPORT -->
        <div class="col-md-12 text-end">

            <button
                type="button"
                class="btn btn-success"
                id="btnExportCashin">

                Export Excel

            </button>

        </div>

    </div>


    <!-- ====================================================== -->
    <!-- RESULT -->
    <!-- ====================================================== -->

    <div id="cashinResult">

        <div class="card border-0 shadow-sm">

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

<style>

    /*
    |--------------------------------------------------------------------------
    | CASH IN TABLE
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern{

        border-collapse:
            separate;

        border-spacing:
            0;

    }


    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern thead th{

        border-bottom:
            2px solid #d1d5db !important;

        white-space:
            nowrap;

    }


    /*
    |--------------------------------------------------------------------------
    | BODY CELL
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern tbody td{

        border-bottom:
            1px solid #e2e6ea !important;

    }


    /*
    |--------------------------------------------------------------------------
    | ROW
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern tbody tr{

        border-bottom:
            1px solid #e2e6ea;

    }


    /*
    |--------------------------------------------------------------------------
    | LAST ROW
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern tbody tr:last-child td{

        border-bottom:
            1px solid #cfd5dc !important;

    }


    /*
    |--------------------------------------------------------------------------
    | FOOTER
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern tfoot td{

        border-top:
            2px solid #c7cdd4 !important;

        border-bottom:
            0 !important;

    }


    /*
    |--------------------------------------------------------------------------
    | CASH IN GROUP
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern tbody tr:hover td{

        background-color:
            #f8fafc;

    }

    #cashinReportPagination .pagination.loading{

        opacity:
            .6;

        pointer-events:
            none;

    }


    #cashinReportPagination .page-link{

        cursor:
            pointer;

    }


    #cashinReportPagination .page-item.disabled
    .page-link{

        cursor:
            default;

    }

    /*
    |--------------------------------------------------------------------------
    | CASH IN TABLE - NO WRAP
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern th,
    #cashinResult .table-modern td {

        white-space: nowrap !important;

        word-break: normal !important;

        overflow-wrap: normal !important;

    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern td {

        vertical-align: middle;

    }


    /*
    |--------------------------------------------------------------------------
    | TABLE WIDTH
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-modern {

        width: max-content !important;

        min-width: 100%;

    }


    /*
    |--------------------------------------------------------------------------
    | HORIZONTAL SCROLL
    |--------------------------------------------------------------------------
    */

    #cashinResult .table-responsive {

        overflow-x: auto;

        overflow-y: hidden;

        width: 100%;

    }

</style>

<script>

window.ReportCashin = (function(){

    let state = {

        page  : 1,

        limit : 20

    };


    let INITIALIZED = false;


    /*
    |--------------------------------------------------------------------------
    | FORMAT
    |--------------------------------------------------------------------------
    */

    function formatRupiah(value)
    {
        return Number(
            value || 0
        ).toLocaleString(
            'id-ID'
        );
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

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $('#cashinSearch').on(
            'keyup',
            function(){

                loadData(1);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        $('#cashinPayment').on(
            'change',
            function(){

                loadData(1);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $('#cashinCustomer').on(
            'change',
            function(){

                loadData(1);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        $('#cashinDateFrom, #cashinDateTo').on(
            'change',
            function(){

                loadData(1);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'click',
            '#cashinReportPagination a[data-ci-pagination-page]',
            function(e){

                e.preventDefault();

                e.stopPropagation();


                let button =
                    $(this);


                let page =
                    parseInt(
                        button.attr(
                            'data-ci-pagination-page'
                        )
                    );


                if(!page){

                    return false;

                }


                /*
                |--------------------------------------------------------------------------
                | PREVENT DOUBLE CLICK
                |--------------------------------------------------------------------------
                */

                if(
                    button
                        .closest('.pagination')
                        .hasClass('loading')
                ){

                    return false;

                }


                /*
                |--------------------------------------------------------------------------
                | LOADING STATE
                |--------------------------------------------------------------------------
                */

                button
                    .closest('.pagination')
                    .addClass('loading');


                /*
                |--------------------------------------------------------------------------
                | LOAD PAGE
                |--------------------------------------------------------------------------
                */

                loadData(
                    page
                );


                return false;

            }
        );


        /*
        |--------------------------------------------------------------------------
        | EXPORT
        |--------------------------------------------------------------------------
        */

        $('#btnExportCashin').on(
            'click',
            function(){

                let params =
                    $.param({

                        search:
                            $('#cashinSearch').val(),

                        customer:
                            $('#cashinCustomer').val(),

                        pembayaran:
                            $('#cashinPayment').val(),

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
        state.page = parseInt(page) || 1;

        let wrapper = $('#cashinResult');

        /*
        |--------------------------------------------------------------------------
        | LOADING
        |--------------------------------------------------------------------------
        */

        wrapper.css(
            'opacity',
            '0.5'
        );


        /*
        |--------------------------------------------------------------------------
        | REQUEST
        |--------------------------------------------------------------------------
        */

        $.ajax({

            url:
                '<?= base_url("report-accounting/load_cashin"); ?>',

            type:
                'GET',

            dataType:
                'json',

            data: {

                page:
                    state.page,

                limit:
                    state.limit,

                search:
                    $('#cashinSearch').val(),

                customer:
                    $('#cashinCustomer').val(),

                pembayaran:
                    $('#cashinPayment').val(),

                date_from:
                    $('#cashinDateFrom').val(),

                date_to:
                    $('#cashinDateTo').val()

            },

            success:
                function(res){

                    /*
                    |--------------------------------------------------------------------------
                    | RENDER
                    |--------------------------------------------------------------------------
                    */

                    renderSummary(
                        res.summary || {}
                    );


                    renderTable(
                        res.rows || []
                    );


                    $('#cashinReportPagination')
                        .html(
                            res.pagination || ''
                        );


                    $('#cashinReportInfo')
                        .html(

                            'Total : <b>'
                            + (res.total || 0)
                            + '</b> data'

                        );


                    /*
                    |--------------------------------------------------------------------------
                    | REMOVE LOADING
                    |--------------------------------------------------------------------------
                    */

                    wrapper.css(
                        'opacity',
                        '1'
                    );

                    $('#cashinReportPagination')
                    .find('.pagination')
                    .removeClass('loading');


                    /*
                    |--------------------------------------------------------------------------
                    | SCROLL TO REPORT
                    |--------------------------------------------------------------------------
                    */

                    $('html, body')
                        .stop()
                        .animate({

                            scrollTop:
                                $('#cashinReportWrapper')
                                    .offset()
                                    .top - 80

                        }, 250);

                },

            error:
                function(){

                    wrapper.css(
                        'opacity',
                        '1'
                    );

                    $('#cashinReportPagination')
                    .find('.pagination')
                    .removeClass('loading');

                    alert(
                        'Gagal mengambil data Cash In.'
                    );

                }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    function renderSummary(summary)
    {

        $('#summaryCashinTotal')
            .html(

                'Rp ' +
                formatRupiah(
                    summary.TOTAL_CASHIN || 0
                )

            );


        $('#summaryCashinCustomer')
            .html(

                formatRupiah(
                    summary.TOTAL_CUSTOMER || 0
                )

            );


        $('#summaryCashinInvoice')
            .html(

                formatRupiah(
                    summary.TOTAL_INVOICE || 0
                )

            );

    }


    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    function renderTable(rows)
    {

        let wrapper =
            $('#cashinResult');


        wrapper.html('');


        let thead = `

            <thead>

                <tr>

                    <th class="text-center">
                        Cash In
                    </th>

                    <th class="text-center">
                        Plant
                    </th>

                    <th class="text-center">
                        Customer
                    </th>

                    <th class="text-center">
                        Tanggal
                    </th>

                    <th class="text-center">
                        Payment
                    </th>

                    <th class="text-center">
                        Slip No
                    </th>

                    <th class="text-center">
                        Amount
                    </th>

                    <th>
                        Sales
                    </th>

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

        `;


        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

        if(rows.length === 0){

            wrapper.html(`

                <div class="table-responsive">

                    <table class="table table-hover align-middle table-modern">

                        ${thead}

                        <tbody>

                            <tr>

                                <td
                                    colspan="12"
                                    class="text-center text-muted py-4">

                                    Data Tidak Ditemukan

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            `);

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        let totalAmount =
            0;

        let totalInvoice =
            0;

        let totalPaid =
            0;

        let totalRemaining =
            0;


        /*
        |--------------------------------------------------------------------------
        | ROW
        |--------------------------------------------------------------------------
        */

        let rowsHtml =
            rows.map(
                function(row){

                    let details =
                        row.DETAILS || [];


                    let rowCount =
                        Math.max(
                            details.length,
                            1
                        );


                    totalAmount +=
                        Number(
                            row.AMOUNT || 0
                        );


                    let html = '';


                    /*
                    |--------------------------------------------------------------------------
                    | NO DETAIL
                    |--------------------------------------------------------------------------
                    */

                    if(details.length === 0){

                        html += `

                            <tr>

                                <td
                                    class="text-center align-middle"
                                    rowspan="${rowCount}">

                                    <span
                                        class="fw-bold text-success">

                                        ${row.CASH_IN || '-'}

                                    </span>

                                </td>


                                <td
                                    class="text-center align-middle"
                                    rowspan="${rowCount}">

                                    ${row.PLANT_NAME || '-'}

                                </td>


                                <td
                                    class="align-middle"
                                    rowspan="${rowCount}">

                                    ${row.CUSTOMER_NAME || '-'}

                                </td>


                                <td
                                    class="text-center align-middle"
                                    rowspan="${rowCount}">

                                    ${formatDate(
                                        row.CASHIN_DATE
                                    )}

                                </td>


                                <td
                                    class="text-center align-middle"
                                    rowspan="${rowCount}">

                                    ${row.PEMBAYARAN || '-'}

                                </td>


                                <td
                                    class="text-center align-middle"
                                    rowspan="${rowCount}">

                                    ${row.SLIP_NO || '-'}

                                </td>


                                <td
                                    class="text-end align-middle fw-bold"
                                    rowspan="${rowCount}">

                                    Rp ${
                                        formatRupiah(
                                            row.AMOUNT || 0
                                        )
                                    }

                                </td>


                                <td>-</td>

                                <td class="text-end">
                                    Rp 0
                                </td>

                                <td class="text-end">
                                    Rp 0
                                </td>

                                <td class="text-end">
                                    Rp 0
                                </td>

                                <td class="text-center">
                                    -
                                </td>

                            </tr>

                        `;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL
                    |--------------------------------------------------------------------------
                    */

                    else{

                        details.forEach(
                            function(d, idx){

                                totalInvoice +=
                                    Number(
                                        d.AMOUNT_INVOICE || 0
                                    );


                                totalPaid +=
                                    Number(
                                        d.AMOUNT_OFFSET || 0
                                    );


                                totalRemaining +=
                                    Number(
                                        d.REMAINING || 0
                                    );


                                let badge =
                                    '<span class="badge bg-secondary">OPEN</span>';


                                if(
                                    d.SALES_STATUS === 'PAID'
                                ){

                                    badge =
                                        '<span class="badge bg-success">PAID</span>';

                                }
                                else if(
                                    d.SALES_STATUS === 'PARTIAL'
                                ){

                                    badge =
                                        '<span class="badge bg-warning text-dark">PARTIAL</span>';

                                }


                                html += `

                                    <tr>

                                        ${
                                            idx === 0
                                            ? `

                                                <td
                                                    class="text-center align-middle"
                                                    rowspan="${rowCount}">

                                                    <span
                                                        class="fw-bold text-success">

                                                        ${row.CASH_IN || '-'}

                                                    </span>

                                                </td>


                                                <td
                                                    class="text-center align-middle"
                                                    rowspan="${rowCount}">

                                                    ${row.PLANT_NAME || '-'}

                                                </td>


                                                <td
                                                    class="align-middle"
                                                    rowspan="${rowCount}">

                                                    ${row.CUSTOMER_NAME || '-'}

                                                </td>


                                                <td
                                                    class="text-center align-middle"
                                                    rowspan="${rowCount}">

                                                    ${formatDate(
                                                        row.CASHIN_DATE
                                                    )}

                                                </td>


                                                <td
                                                    class="text-center align-middle"
                                                    rowspan="${rowCount}">

                                                    ${row.PEMBAYARAN || '-'}

                                                </td>


                                                <td
                                                    class="text-center align-middle"
                                                    rowspan="${rowCount}">

                                                    ${row.SLIP_NO || '-'}

                                                </td>


                                                <td
                                                    class="text-end align-middle fw-bold"
                                                    rowspan="${rowCount}">

                                                    Rp ${
                                                        formatRupiah(
                                                            row.AMOUNT || 0
                                                        )
                                                    }

                                                </td>

                                            `
                                            : ''
                                        }


                                        <td>

                                            <span
                                                class="fw-semibold text-primary">

                                                #${d.SALES || '-'}

                                            </span>

                                        </td>


                                        <td class="text-end">

                                            Rp ${
                                                formatRupiah(
                                                    d.AMOUNT_INVOICE || 0
                                                )
                                            }

                                        </td>


                                        <td class="text-end">

                                            Rp ${
                                                formatRupiah(
                                                    d.AMOUNT_OFFSET || 0
                                                )
                                            }

                                        </td>


                                        <td class="text-end">

                                            Rp ${
                                                formatRupiah(
                                                    d.REMAINING || 0
                                                )
                                            }

                                        </td>


                                        <td class="text-center">

                                            ${badge}

                                        </td>

                                    </tr>

                                `;

                            }
                        );

                    }


                    return html;

                }
            )
            .join('');


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        let table = `

            <div class="table-responsive">

                <table
                    class="table table-hover align-middle table-modern">

                    ${thead}

                    <tbody>

                        ${rowsHtml}

                    </tbody>


                    <tfoot>

                        <tr
                            class="table-light fw-bold">

                            <td
                                colspan="6"
                                class="text-end">

                                Total Semua

                            </td>


                            <td class="text-end">

                                Rp ${
                                    formatRupiah(
                                        totalAmount
                                    )
                                }

                            </td>


                            <td></td>


                            <td class="text-end">

                                Rp ${
                                    formatRupiah(
                                        totalInvoice
                                    )
                                }

                            </td>


                            <td class="text-end">

                                Rp ${
                                    formatRupiah(
                                        totalPaid
                                    )
                                }

                            </td>


                            <td class="text-end">

                                Rp ${
                                    formatRupiah(
                                        totalRemaining
                                    )
                                }

                            </td>


                            <td></td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        `;


        wrapper.html(
            table
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

            allowClear:
                true,

            minimumInputLength:
                3,

            language: {

                inputTooShort:
                    function(){

                        return 'Ketik minimal 3 karakter';

                    }

            },

            dropdownParent:
                $(document.body),

            width:
                '100%',

            ajax: {

                url:
                    '<?= base_url("cash-in/get-customer"); ?>',

                dataType:
                    'json',

                delay:
                    250,

                data:
                    function(params){

                        return {

                            q:
                                params.term

                        };

                    },

                processResults:
                    function(data){

                        return {

                            results:
                                data

                        };

                    }

            }

        });
    }


    return {

        init:
            init,

        loadData:
            loadData

    };


})();


$(document).ready(
    function(){

        if(window.ReportCashin){

            ReportCashin.init();

        }

    }
);

</script>
