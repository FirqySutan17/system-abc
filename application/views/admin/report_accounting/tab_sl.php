<!-- ====================================================== -->
<!-- FILTER -->
<!-- ====================================================== -->

<div class="mb-4">

    <div class="row g-3">

        <!-- SEARCH -->
        <div class="col-md-3">

            <input
                type="text"
                id="savingSearch"
                class="form-control"
                placeholder="Cari nomor saving, pelanggan, penjualan, terkait...">

        </div>


        <!-- CUSTOMER -->
        <div class="col-md-3">

            <select
                id="savingCustomer"
                class="form-select"
                style="width: 100%;">

                <option value="">
                    Semua Pelanggan
                </option>

            </select>

        </div>


        <!-- STATUS -->
        <div class="col-md-2">

            <select
                id="savingStatus"
                class="form-select">

                <option value="">
                    Semua Status
                </option>

                <option value="PAID">
                    Lunas
                </option>

                <option value="OUTSTANDING">
                    Belum Lunas
                </option>

            </select>

        </div>


        <!-- DATE FROM -->
        <div class="col-md-2">

            <input
                type="date"
                id="savingDateFrom"
                class="form-control"
                value="<?= date('Y-m-01'); ?>">

        </div>


        <!-- DATE TO -->
        <div class="col-md-2">

            <input
                type="date"
                id="savingDateTo"
                class="form-control"
                value="<?= date('Y-m-d'); ?>">

        </div>

        <div class="col-md-10"></div>

        <div class="col-md-2">
            <button
                type="button"
                id="btnExportSaving"
                class="btn btn-success" style="width: 100%">

                <i class="fa fa-file-excel-o"></i>
                Export Excel

            </button>
        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- RESULT -->
<!-- ====================================================== -->

<div id="savingResult">

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

    <div id="savingReportInfo"></div>

    <div id="savingReportPagination"></div>

</div>

<style>

    /*
    |--------------------------------------------------------------------------
    | SAVING TABLE
    |--------------------------------------------------------------------------
    */

    #savingResult .table-modern{

        border-collapse:
            separate;

        border-spacing:
            0;

        width:
            max-content;

        min-width:
            100%;

    }


    /*
    |--------------------------------------------------------------------------
    | NO WRAP
    |--------------------------------------------------------------------------
    */

    #savingResult .table-modern th,
    #savingResult .table-modern td{

        white-space:
            nowrap !important;

        word-break:
            normal !important;

        overflow-wrap:
            normal !important;

        vertical-align:
            middle;

    }


    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    #savingResult .table-modern thead th{

        border-bottom:
            2px solid #d1d5db !important;

        background:
            #f1f3f5;

    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL ROW
    |--------------------------------------------------------------------------
    */

    #savingResult .table-modern tbody td{

        border-bottom:
            1px solid #e2e6ea !important;

    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER GROUP
    |--------------------------------------------------------------------------
    */

    #savingResult .saving-customer-header{

        background:
            #f8fafc;

        font-weight:
            700;

        border-top:
            2px solid #c7cdd4;

    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOMER TOTAL
    |--------------------------------------------------------------------------
    */

    #savingResult .saving-customer-total td{

        background:
            #f3f6f9;

        border-top:
            2px solid #c7cdd4 !important;

        font-weight:
            700;

    }


    /*
    |--------------------------------------------------------------------------
    | HOVER
    |--------------------------------------------------------------------------
    */

    #savingResult .table-modern tbody tr:hover td{

        background:
            #f8fafc;

    }


    /*
    |--------------------------------------------------------------------------
    | HORIZONTAL SCROLL
    |--------------------------------------------------------------------------
    */

    #savingResult .table-responsive{

        overflow-x:
            auto;

        overflow-y:
            hidden;

        width:
            100%;

    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .saving-status{

        display:
            inline-block;

        padding:
            5px 10px;

        border-radius:
            999px;

        font-size:
            11px;

        font-weight:
            700;

        white-space:
            nowrap;

    }


    .saving-status-paid{

        background:
            #d1fae5;

        color:
            #047857;

    }


    .saving-status-outstanding{

        background:
            #fef3c7;

        color:
            #92400e;

    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT BUTTON
    |--------------------------------------------------------------------------
    */

    .saving-payment-btn{

        border:
            1px solid #ced4da;

        background:
            #ffffff;

        color:
            #495057;

        font-size:
            12px;

        font-weight:
            600;

        padding:
            4px 9px;

        border-radius:
            5px;

        cursor:
            pointer;

        white-space:
            nowrap;

    }


    .saving-payment-btn:hover{

        background:
            #f1f3f5;

        border-color:
            #adb5bd;

    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT ROW
    |--------------------------------------------------------------------------
    */

    #savingResult .saving-payment-row td{

        background:
            #fafafa;

        border-bottom:
            1px solid #e9ecef !important;

    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT WRAPPER
    |--------------------------------------------------------------------------
    */

    .saving-payment-wrapper{

        padding:
            12px 15px;

    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT TITLE
    |--------------------------------------------------------------------------
    */

    .saving-payment-title{

        font-weight:
            700;

        font-size:
            13px;

        margin-bottom:
            8px;

        color:
            #495057;

    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT TABLE
    |--------------------------------------------------------------------------
    */

    .saving-payment-table{

        width:
            max-content;

        min-width:
            650px;

        margin-bottom:
            0;

        border-collapse:
            collapse;

        font-size:
            12px;

    }


    .saving-payment-table th{

        background:
            #f1f3f5;

        font-weight:
            700;

        padding:
            7px 10px;

        border:
            1px solid #dee2e6;

        white-space:
            nowrap;

    }


    .saving-payment-table td{

        padding:
            7px 10px;

        border:
            1px solid #dee2e6;

        white-space:
            nowrap;

        vertical-align:
            middle;

    }


    .saving-payment-table .payment-total td{

        background:
            #f3f6f9;

        font-weight:
            700;

        border-top:
            2px solid #c7cdd4;

    }


    /*
    |--------------------------------------------------------------------------
    | PAGINATION LOADING
    |--------------------------------------------------------------------------
    */

    #savingReportPagination
    .pagination.loading{

        opacity:
            .6;

        pointer-events:
            none;

    }

    /*
    |--------------------------------------------------------------------------
    | THEAD CENTER
    |--------------------------------------------------------------------------
    */

    #savingResult .table-modern thead th{

        text-align:
            center !important;

        vertical-align:
            middle !important;

    }

</style>

<script>

    window.ReportSaving = (function(){

        let state = {

            page:
                1,

            limit:
                20

        };


        let INITIALIZED =
            false;


        /*
        |--------------------------------------------------------------------------
        | FORMAT RUPIAH
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


        /*
        |--------------------------------------------------------------------------
        | FORMAT NUMBER
        |--------------------------------------------------------------------------
        */

        function formatNumber(value)
        {

            return Number(
                value || 0
            ).toLocaleString(

                'id-ID',

                {

                    minimumFractionDigits:
                        0,

                    maximumFractionDigits:
                        2

                }

            );

        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT DATE
        |--------------------------------------------------------------------------
        */

        function formatDate(date)
        {

            if(!date){

                return '-';

            }


            return new Date(
                date
            ).toLocaleDateString(

                'id-ID',

                {

                    day:
                        '2-digit',

                    month:
                        'short',

                    year:
                        'numeric'

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


            INITIALIZED =
                true;


            loadCustomer();

            initCustomerSelect2();

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

            $('#savingSearch')
                .on(
                    'keyup',
                    function(){

                        loadData(1);

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            */

            $('#savingCustomer')
                .on(
                    'change',
                    function(){

                        loadData(1);

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $('#savingStatus')
                .on(
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

            $('#savingDateFrom, #savingDateTo')
                .on(
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

                '#savingReportPagination a[data-saving-pagination-page]',

                function(e){

                    e.preventDefault();

                    e.stopPropagation();


                    let button =
                        $(this);


                    let page =
                        parseInt(
                            button.attr(
                                'data-saving-pagination-page'
                            )
                        );


                    if(!page){

                        return false;

                    }


                    let pagination =
                        button.closest(
                            '.pagination'
                        );


                    if(
                        pagination.hasClass(
                            'loading'
                        )
                    ){

                        return false;

                    }


                    pagination.addClass(
                        'loading'
                    );


                    loadData(
                        page
                    );


                    return false;

                }

            );

            /*
            |--------------------------------------------------------------------------
            | EXPORT EXCEL
            |--------------------------------------------------------------------------
            */

            $('#btnExportSaving').on(
                'click',
                function(){

                    const params = $.param({

                        search:
                            $('#savingSearch').val(),

                        customer:
                            $('#savingCustomer').val(),

                        status:
                            $('#savingStatus').val(),

                        date_from:
                            $('#savingDateFrom').val(),

                        date_to:
                            $('#savingDateTo').val()

                    });


                    const url =
                        '<?= base_url("report-accounting/export_excel_saving"); ?>'
                        + '?'
                        + params;


                    window.location.href =
                        url;

                }
            );


            /*
            |--------------------------------------------------------------------------
            | PAYMENT TOGGLE
            |--------------------------------------------------------------------------
            */

            $(document).on(

                'click',

                '#savingResult .saving-payment-btn',

                function(e){

                    e.preventDefault();

                    e.stopPropagation();


                    let button =
                        $(this);


                    let target =
                        button.attr(
                            'data-payment-target'
                        );


                    let row =
                        $('#' + target);


                    if(
                        row.length === 0
                    ){

                        return;

                    }


                    row.toggleClass(
                        'd-none'
                    );


                    if(
                        row.hasClass(
                            'd-none'
                        )
                    ){

                        button.text(
                            'Lihat Pembayaran ('
                            +
                            button.attr(
                                'data-payment-count'
                            )
                            +
                            ')'
                        );

                    }else{

                        button.text(
                            'Sembunyikan Pembayaran ('
                            +
                            button.attr(
                                'data-payment-count'
                            )
                            +
                            ')'
                        );

                    }

                }

            );

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        function loadData(
            page = 1
        )
        {

            state.page =
                parseInt(page) || 1;


            let wrapper =
                $('#savingResult');


            wrapper.css(
                'opacity',
                '0.5'
            );


            $.ajax({

                url:
                    '<?= base_url("report-accounting/load_saving"); ?>',

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
                        $('#savingSearch').val(),

                    customer:
                        $('#savingCustomer').val(),

                    status:
                        $('#savingStatus').val(),

                    date_from:
                        $('#savingDateFrom').val(),

                    date_to:
                        $('#savingDateTo').val()

                },


                success:
                    function(res){

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

                        $('#savingReportPagination')
                            .html(
                                res.pagination || ''
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | INFO
                        |--------------------------------------------------------------------------
                        */

                        $('#savingReportInfo')
                            .html(

                                'Total Pelanggan : <b>'
                                +
                                (
                                    res.total || 0
                                )
                                +
                                '</b>'

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


                        $('#savingReportPagination')
                            .find('.pagination')
                            .removeClass(
                                'loading'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | SCROLL
                        |--------------------------------------------------------------------------
                        */

                        $('html, body')
                            .stop()
                            .animate({

                                scrollTop:
                                    $('#savingResult')
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


                        $('#savingReportPagination')
                            .find('.pagination')
                            .removeClass(
                                'loading'
                            );


                        alert(
                            'Gagal mengambil data laporan Saving.'
                        );

                    }

            });

        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT HISTORY HTML
        |--------------------------------------------------------------------------
        */

        function buildPembayaranHistory(
            payments,
            savingNo,
            targetId
        )
        {

            if(
                !payments ||
                payments.length === 0
            ){

                return '';

            }


            let paymentRows =
                '';


            let totalOffset =
                0;


            payments.forEach(
                function(payment){

                    let offset =
                        Number(
                            payment.AMOUNT_OFFSET || 0
                        );


                    let cashInNilai =
                        Number(
                            payment.CASH_IN_AMOUNT || 0
                        );


                    totalOffset +=
                        offset;


                    paymentRows += `

                        <tr>

                            <td>

                                ${
                                    formatDate(
                                        payment.DATE_OFFSET
                                        ||
                                        payment.CASHIN_DATE
                                    )
                                }

                            </td>


                            <td>

                                <span
                                    class="fw-semibold">

                                    ${
                                        payment.CASH_IN || '-'
                                    }

                                </span>

                            </td>


                            <td>

                                ${
                                    payment.PEMBAYARAN || '-'
                                }

                            </td>


                            <td class="text-end">

                                Rp
                                ${
                                    formatRupiah(
                                        cashInNilai
                                    )
                                }

                            </td>


                            <td class="text-end">

                                Rp
                                ${
                                    formatRupiah(
                                        offset
                                    )
                                }

                            </td>

                        </tr>

                    `;

                }
            );


            return `

                <tr
                    id="${targetId}"
                    class="saving-payment-row d-none">

                    <td
                        colspan="14">

                        <div
                            class="saving-payment-wrapper">

                            <div
                                class="saving-payment-title">

                                RIWAYAT PEMBAYARAN -
                                ${savingNo || '-'}

                            </div>


                            <div
                                class="table-responsive">

                                <table
                                    class="saving-payment-table">

                                    <thead>

                                        <tr>

                                            <th>
                                                Tanggal
                                            </th>

                                            <th>
                                                Cash In
                                            </th>

                                            <th>
                                                Pembayaran
                                            </th>

                                            <th class="text-end">
                                                Cash In Nilai
                                            </th>

                                            <th class="text-end">
                                                Saving Offset
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        ${paymentRows}


                                        <tr
                                            class="payment-total">

                                            <td
                                                colspan="4"
                                                class="text-end">

                                                TOTAL DIBAYAR

                                            </td>


                                            <td
                                                class="text-end">

                                                Rp
                                                ${
                                                    formatRupiah(
                                                        totalOffset
                                                    )
                                                }

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </td>

                </tr>

            `;

        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        function renderTable(
            rows
        )
        {

            let wrapper =
                $('#savingResult');


            wrapper.html('');


            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if(
                !rows ||
                rows.length === 0
            ){

                wrapper.html(`

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle table-modern">

                            <thead>

                                <tr>

                                    <th>
                                        Tanggal
                                    </th>

                                    <th>
                                        No. Saving
                                    </th>

                                    <th>
                                        Terkait
                                    </th>

                                    <th>
                                        Penjualan
                                    </th>

                                    <th>
                                        Nilai
                                    </th>

                                    <th>
                                        Dasar
                                    </th>

                                    <th>
                                        Tarif
                                    </th>

                                    <th>
                                        Kuantitas
                                    </th>

                                    <th>
                                        Berat
                                    </th>

                                    <th>
                                        Dibayar
                                    </th>

                                    <th>
                                        Sisa
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Pembayaran
                                    </th>

                                    <th>
                                        Keterangan
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr>

                                    <td
                                        colspan="14"
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
            | BUILD CUSTOMER GROUP
            |--------------------------------------------------------------------------
            */

            let rowsHtml =
                '';


            rows.forEach(
                function(group){

                    let details =
                        group.DETAILS || [];


                    let totalNilai =
                        0;


                    let totalDibayar =
                        0;


                    let totalSisa =
                        0;


                    /*
                    |--------------------------------------------------------------------------
                    | CUSTOMER HEADER
                    |--------------------------------------------------------------------------
                    */

                    rowsHtml += `

                        <tr
                            class="saving-customer-header">

                            <td colspan="14">

                                <span class="fw-bold">

                                    CUSTOMER :
                                    ${group.CUSTOMER || '-'}

                                </span>

                                <span class="ms-2 text-muted">

                                    -
                                    ${group.CUSTOMER_NAME || '-'}

                                </span>

                            </td>

                        </tr>

                    `;


                    /*
                    |--------------------------------------------------------------------------
                    | NO HISTORY
                    |--------------------------------------------------------------------------
                    */

                    if(
                        details.length === 0
                    ){

                        rowsHtml += `

                            <tr>

                                <td
                                    colspan="14"
                                    class="text-center text-muted">

                                    Tidak ada riwayat Saving

                                </td>

                            </tr>

                        `;

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DETAILS
                    |--------------------------------------------------------------------------
                    */

                    details.forEach(
                        function(d, index){

                            let amount =
                                Number(
                                    d.AMOUNT || 0
                                );


                            let remain =
                                Number(
                                    d.REMAIN || 0
                                );


                            let paid =
                                amount - remain;


                            /*
                            |--------------------------------------------------------------------------
                            | STATUS
                            |--------------------------------------------------------------------------
                            */

                            let status =
                                remain <= 0
                                    ? 'PAID'
                                    : 'OUTSTANDING';


                            let statusClass =
                                remain <= 0
                                    ? 'saving-status-paid'
                                    : 'saving-status-outstanding';


                            /*
                            |--------------------------------------------------------------------------
                            | PAYMENTS
                            |--------------------------------------------------------------------------
                            */

                            let payments =
                                d.PAYMENTS || [];


                            let paymentButton =
                                '';


                            let paymentRow =
                                '';


                            if(
                                payments.length > 0
                            ){

                                /*
                                |--------------------------------------------------------------------------
                                | UNIQUE ID
                                |--------------------------------------------------------------------------
                                */

                                let safeId =
                                    'savingPembayaran_'
                                    +
                                    state.page
                                    +
                                    '_'
                                    +
                                    index
                                    +
                                    '_'
                                    +
                                    String(
                                        d.SV_NO || ''
                                    )
                                    .replace(
                                        /[^a-zA-Z0-9_-]/g,
                                        ''
                                    );


                                paymentButton = `

                                    <button
                                        type="button"
                                        class="saving-payment-btn"
                                        data-payment-target="${safeId}"
                                        data-payment-count="${payments.length}">

                                        View Pembayaran
                                        (${payments.length})

                                    </button>

                                `;


                                paymentRow =
                                    buildPembayaranHistory(

                                        payments,

                                        d.SV_NO,

                                        safeId

                                    );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | CUSTOMER TOTAL
                            |--------------------------------------------------------------------------
                            */

                            totalNilai +=
                                amount;


                            totalDibayar +=
                                paid;


                            totalSisa +=
                                remain;


                            /*
                            |--------------------------------------------------------------------------
                            | SAVING ROW
                            |--------------------------------------------------------------------------
                            */

                            rowsHtml += `

                                <tr>

                                    <td>

                                        ${
                                            formatDate(
                                                d.SV_DATE
                                            )
                                        }

                                    </td>


                                    <td>

                                        <span
                                            class="fw-semibold text-primary">

                                            ${
                                                d.SV_NO || '-'
                                            }

                                        </span>

                                    </td>


                                    <td>

                                        ${
                                            d.RELATED || '-'
                                        }

                                    </td>


                                    <td>

                                        ${
                                            d.SALES || '-'
                                        }

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        ${
                                            formatRupiah(
                                                amount
                                            )
                                        }

                                    </td>


                                    <td>

                                        ${
                                            d.BASIS || '-'
                                        }

                                    </td>


                                    <td class="text-end">

                                        ${
                                            formatNumber(
                                                d.RATE
                                            )
                                        }

                                    </td>


                                    <td class="text-end">

                                        ${
                                            formatNumber(
                                                d.TOTAL_QTY
                                            )
                                        }

                                    </td>


                                    <td class="text-end">

                                        ${
                                            formatNumber(
                                                d.TOTAL_WEIGHT
                                            )
                                        }

                                    </td>


                                    <!-- DIBAYAR -->

                                    <td class="text-end">

                                        Rp
                                        ${
                                            formatRupiah(
                                                paid
                                            )
                                        }

                                    </td>


                                    <!-- SISA -->

                                    <td class="text-end">

                                        Rp
                                        ${
                                            formatRupiah(
                                                remain
                                            )
                                        }

                                    </td>


                                    <!-- STATUS -->

                                    <td class="text-center">

                                        <span
                                            class="saving-status ${statusClass}">

                                            ${
                                                status === 'PAID'
                                                    ? 'LUNAS'
                                                    : 'OUTSTANDING'
                                            }

                                        </span>

                                    </td>


                                    <!-- PEMBAYARAN -->

                                    <td>

                                        ${
                                            paymentButton
                                        }

                                    </td>


                                    <!-- REMARK -->

                                    <td>

                                        ${
                                            d.REMARK || '-'
                                        }

                                    </td>

                                </tr>


                                ${
                                    paymentRow
                                }

                            `;

                        }
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | CUSTOMER TOTAL
                    |--------------------------------------------------------------------------
                    */

                    rowsHtml += `

                        <tr
                            class="saving-customer-total">

                            <td
                                colspan="4"
                                class="text-end">

                                TOTAL PELANGGAN

                            </td>


                            <td class="text-end">

                                Rp
                                ${
                                    formatRupiah(
                                        totalNilai
                                    )
                                }

                            </td>


                            <td colspan="4">

                            </td>


                            <td class="text-end">

                                Rp
                                ${
                                    formatRupiah(
                                        totalDibayar
                                    )
                                }

                            </td>


                            <td>

                            </td>


                            <td class="text-end">

                                Rp
                                ${
                                    formatRupiah(
                                        totalSisa
                                    )
                                }

                            </td>


                            <td colspan="2">

                            </td>

                        </tr>

                    `;

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TABLE
            |--------------------------------------------------------------------------
            */

            let table = `

                <div class="table-responsive">

                    <table
                        class="table table-hover align-middle table-modern">

                        <thead>

                            <tr>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    No. Saving
                                </th>

                                <th>
                                    Terkait
                                </th>

                                <th>
                                    Penjualan
                                </th>

                                <th class="text-end">
                                    Nilai
                                </th>

                                <th>
                                    Dasar
                                </th>

                                <th class="text-end">
                                    Tarif
                                </th>

                                <th class="text-end">
                                    Kuantitas
                                </th>

                                <th class="text-end">
                                    Berat
                                </th>

                                <th class="text-end">
                                    Dibayar
                                </th>

                                <th class="text-end">
                                    Sisa
                                </th>

                                <th class="text-center">
                                    Status
                                </th>

                                <th>
                                    Pembayaran
                                </th>

                                <th>
                                    Keterangan
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            ${rowsHtml}

                        </tbody>

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

        function initCustomerSelect2()
        {

            $('#savingCustomer').select2({

                width: '100%',

                placeholder: 'Semua Pelanggan',

                allowClear: true

            });

        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        function loadCustomer()
        {

            $.get(

                '<?= base_url("saving/get_customer"); ?>',

                function(rows){

                    let select =
                        $('#savingCustomer');

                    select.empty();

                    select.append(`

                        <option value="">
                            Semua Pelanggan
                        </option>

                    `);

                    rows.forEach(
                        function(r){

                            select.append(`

                                <option
                                    value="${r.id}">

                                    ${r.text}

                                </option>

                            `);

                        }
                    );

                    select.trigger('change.select2');

                },

                'json'

            );

        }


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return {

            init:
                init,

            loadData:
                loadData

        };

    })();


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT READY
    |--------------------------------------------------------------------------
    */

    $(document).ready(
        function(){

            if(
                window.ReportSaving
            ){

                ReportSaving.init();

            }

        }
    );

</script>