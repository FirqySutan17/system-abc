<div class="mb-4">

    <div class="row g-3">

        <!-- SEARCH -->
        <div class="col-md-3">

            <input
                type="text"
                id="salesSearch"
                class="form-control"
                placeholder="Cari sales, customer, nota...">

        </div>

        <!-- PLANT -->
        <div class="col-md-2">

            <select
                id="salesPlant"
                class="form-select">

                <option value="">

                    Semua Plant

                </option>

            </select>

        </div>

        <!-- CUSTOMER -->
        <div class="col-md-3">

            <select
                id="salesCustomer"
                class="form-select">

            </select>

        </div>

        <!-- PAYMENT -->
        <div class="col-md-2">

            <select
                id="salesPembayaran"
                class="form-select">

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

        <!-- STATUS -->
        <div class="col-md-2">

            <select
                id="salesStatus"
                class="form-select">

                <option value="">

                    Semua Status

                </option>

                <option value="OPEN">

                    OPEN

                </option>

                <option value="PARTIAL">

                    PARTIAL

                </option>

                <option value="PAID">

                    PAID

                </option>

            </select>

        </div>

        <!-- DATE FROM -->
        <div class="col-md-2">

            <input
                type="date"
                id="salesDateFrom"
                class="form-control"
                value="<?= date('Y-m-01'); ?>">

        </div>

        <!-- DATE TO -->
        <div class="col-md-2">

            <input
                type="date"
                id="salesDateTo"
                class="form-control"
                value="<?= date('Y-m-d'); ?>">

        </div>

        <div class="col-md-6"></div>

        <!-- EXPORT -->
        <div class="col-md-2">

            <div class="btn-group w-100">

                <button
                    type="button"
                    class="btn btn-success dropdown-toggle w-100"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">

                    <i class="ti ti-file-export me-1"></i>

                    Export

                </button>

                <ul class="dropdown-menu w-100">

                    <li>

                        <a
                            href="#"
                            class="dropdown-item"
                            id="btnExportSalesExcel">

                            <i class="ti ti-file-spreadsheet me-2 text-success"></i>

                            Export Excel

                        </a>

                    </li>

                    <li>

                        <a
                            href="#"
                            class="dropdown-item"
                            id="btnExportSalesPdf">

                            <i class="ti ti-file-type-pdf me-2 text-danger"></i>

                            Export PDF

                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</div>

<!-- RESULT -->
<div id="salesResult">

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5 text-muted">

            Belum ada data

        </div>

    </div>

</div>

<!-- PAGINATION -->
<div class="d-flex justify-content-between mt-3">

    <div id="salesReportInfo"></div>

    <div id="salesReportPagination"></div>

</div>

<style>

    .report-card{

        border-radius: 24px;

        padding: 24px;

        color: #fff;

        box-shadow:
            0 10px 25px rgba(0,0,0,.08);

    }


    .report-card-title{

        font-size: 14px;

        opacity: .9;

        margin-bottom: 8px;

    }


    .report-card-value{

        font-size: 38px;

        font-weight: 700;

        line-height: 1.1;

    }


    .sales-header{

        background:
            linear-gradient(
                135deg,
                #2563eb,
                #3b82f6
            );

        border-radius: 24px 24px 0 0;

        position: relative;

        overflow: hidden;

        padding: 32px;

    }


    .sales-header::after{

        content: '';

        position: absolute;

        width: 280px;

        height: 280px;

        background: rgba(255,255,255,.08);

        border-radius: 50%;

        right: -100px;

        top: -80px;

    }


    .sales-title{

        color: #fff;

        font-size: 34px;

        font-weight: 700;

    }


    .sales-badge{

        padding: 10px 16px;

        border-radius: 999px;

        font-size: 12px;

        font-weight: 700;

        letter-spacing: .5px;

    }


    .sales-info-table tr{

        border: none !important;

    }


    .sales-info-table tbody{

        border: none !important;

    }


    .sales-info-table td{

        border: none !important;

    }


    /* =========================================================
       REPORT SALES - GROUPED TABLE
       ========================================================= */

    .sales-report-wrapper{

        width: 100%;

        overflow: visible;

        padding: 4px;

        position: relative;

    }


    /* =========================================================
       TABLE
       ========================================================= */

    .sales-report-table{

        width: 100%;

        border-collapse: separate !important;

        border-spacing: 0;

        font-size: 12px;

        margin-bottom: 0;

    }

    /* =========================================================
    TABLE HEADER - STICKY
    ========================================================= */

    .sales-report-table > thead > tr > th{

        position: sticky;

        top: 0;

        z-index: 100;

        background: #f1f4f8;

        color: #64748b;

        font-weight: 600;

        border: none !important;

        border-bottom: 1px solid #dbe3ec !important;

        padding: 11px 9px;

        white-space: nowrap;

        box-shadow:
            0 2px 6px rgba(15,23,42,.08);

    }


    /* =========================================================
       SALES GROUP
       ========================================================= */

    .sales-report-group{

        position: relative;

    }


    /* =========================================================
       SALES HEADER
       ========================================================= */

    .sales-group-header td{

        border: none !important;

        padding: 0 !important;

    }


    .sales-group-box{

        margin-top: 14px;

        border: 1px solid #d8e3f0;

        border-bottom: none;

        border-radius: 9px 9px 0 0;

        overflow: hidden;

        background: #f8fbff;

    }


    /* =========================================================
       SALES INFORMATION
       ========================================================= */

    .sales-group-info{

        min-height: 62px;

        padding: 9px 13px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 18px;

    }


    /* =========================================================
       SALES MAIN INFO
       ========================================================= */

    .sales-main-info{

        display: grid;

        grid-template-columns:
            max-content
            max-content
            max-content
            minmax(150px, 1.4fr)
            max-content
            max-content
            max-content;

        align-items: center;

        gap: 15px;

        flex: 1;

        min-width: 0;

    }


    /* =========================================================
       SALES NUMBER
       ========================================================= */

    .sales-number{

        color: #0d6efd;

        font-weight: 700;

        font-size: 14px;

        white-space: nowrap;

    }


    /* =========================================================
       LABEL
       ========================================================= */

    .sales-label{

        display: block;

        margin-bottom: 3px;

        color: #94a3b8;

        font-size: 9px;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: .3px;

        line-height: 1;

    }


    /* =========================================================
       DATE
       ========================================================= */

    .sales-date{

        color: #475569;

        font-size: 11px;

        white-space: nowrap;

    }


    /* =========================================================
       PLANT
       ========================================================= */

    .sales-plant{

        color: #475569;

        font-size: 11px;

        white-space: nowrap;

    }


    /* =========================================================
       CUSTOMER
       ========================================================= */

    .sales-customer{

        min-width: 150px;

        max-width: 220px;

        overflow: hidden;

    }


    .sales-customer .fw-semibold{

        color: #334155;

        font-size: 11px;

        line-height: 1.2;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

    }


    .sales-customer small{

        display: block;

        margin-top: 2px;

        font-size: 9px;

        line-height: 1.1;

    }


    /* =========================================================
       SOURCE
       ========================================================= */

    .sales-source{

        text-align: center;

        white-space: nowrap;

    }


    /* =========================================================
       PAYMENT
       ========================================================= */

    .sales-payment{

        text-align: center;

        white-space: nowrap;

    }


    /* =========================================================
       STATUS
       ========================================================= */

    .sales-status{

        text-align: center;

        white-space: nowrap;

    }


    /* =========================================================
       TOTAL SALES
       ========================================================= */

    .sales-total{

        min-width: 155px;

        padding-left: 16px;

        border-left: 1px solid #dbe4ef;

        text-align: right;

        white-space: nowrap;

    }


    .sales-total span{

        display: block;

        color: #64748b;

        font-size: 9px;

        font-weight: 600;

        text-transform: uppercase;

        letter-spacing: .3px;

        line-height: 1;

    }


    .sales-total strong{

        display: block;

        margin-top: 4px;

        color: #0d47a1;

        font-size: 15px;

        font-weight: 700;

        line-height: 1;

    }


    /* =========================================================
       DETAIL
       ========================================================= */

    .sales-detail-row td{

        background: #ffffff;

        border-top: 1px solid #edf1f5 !important;

        border-bottom: none !important;

        padding: 8px 9px;

    }


    .sales-detail-row:last-of-type td{

        border-bottom: 1px solid #dbe4f0 !important;

    }


    /* =========================================================
       DETAIL HOVER
       ========================================================= */

    .sales-detail-row:hover td{

        background: #f8fbff;

    }


    /* =========================================================
       SAVING
       ========================================================= */

    .sales-saving-row td{

        background: #fff8e6;

        border-top: 1px dashed #f1d58a !important;

        border-bottom: none !important;

        padding: 8px 9px;

    }


    .sales-saving-row:hover td{

        background: #fff4d6;

    }


    .saving-icon{

        display: inline-flex;

        align-items: center;

        justify-content: center;

        width: 20px;

        height: 20px;

        border-radius: 50%;

        background: #f59e0b;

        color: #ffffff;

        font-weight: bold;

    }


    /* =========================================================
       SUBTOTAL
       ========================================================= */

    .sales-subtotal-row td{

        background: #edf5ff;

        color: #334155;

        border-top: 1px solid #dbe7f5 !important;

        border-bottom: 1px solid #dbe7f5 !important;

        padding: 8px 9px;

        font-weight: 600;

    }


    /* =========================================================
       SPACER
       ========================================================= */

    .sales-group-spacer td{

        height: 14px;

        padding: 0 !important;

        border: none !important;

        background: transparent !important;

    }


    /* =========================================================
       GRAND TOTAL
       ========================================================= */

    .sales-grand-total td{

        background: #f1f5f9;

        border-top: 2px solid #cbd5e1 !important;

        padding: 12px 9px;

        font-weight: 700;

    }


    /* =========================================================
       BADGE
       ========================================================= */

    .sales-report-table .badge{

        font-size: 10px;

        padding: 5px 7px;

        white-space: nowrap;

    }


    /* =========================================================
       MOBILE / TABLET
       ========================================================= */

    @media (max-width: 1400px){

        .sales-group-info{

            gap: 12px;

        }


        .sales-main-info{

            grid-template-columns:
                max-content
                max-content
                max-content
                minmax(130px, 1fr)
                max-content
                max-content
                max-content;

            gap: 10px;

        }


        .sales-customer{

            min-width: 130px;

            max-width: 180px;

        }


        .sales-total{

            min-width: 145px;

        }

    }


    @media (max-width: 1200px){

        .sales-group-info{

            align-items: flex-start;

        }


        .sales-main-info{

            grid-template-columns:
                max-content
                max-content
                max-content
                minmax(130px, 1fr);

            gap: 9px 12px;

        }


        .sales-total{

            min-width: 145px;

        }

    }


    @media (max-width: 768px){

        .sales-group-info{

            flex-direction: column;

            align-items: stretch;

        }


        .sales-main-info{

            width: 100%;

            grid-template-columns:
                1fr 1fr;

            gap: 10px;

        }


        .sales-number{

            grid-column: 1 / -1;

        }


        .sales-customer{

            max-width: none;

        }


        .sales-total{

            width: 100%;

            min-width: 0;

            padding-left: 0;

            padding-top: 10px;

            border-left: none;

            border-top: 1px solid #dbe4ef;

            text-align: left;

        }

    }

</style>

<script>
    window.ReportSales = (function(){

        let state = {

            page  : 1,

            limit : 20

        };

        let INITIALIZED = false;

        function formatRupiah(value)
        {
            return Number(
                value || 0
            ).toLocaleString(
                'id-ID'
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
                        month : 'long',
                        year  : 'numeric'

                    }

                );
        }

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

        function initCustomer()
        {
            $('#salesCustomer').select2({

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

        function loadData(page = 1)
        {
            state.page = page;

            $.get(

                '<?= base_url("report-sales/load_sales"); ?>',

                {

                    page        : state.page,

                    limit       : state.limit,

                    search      : $('#salesSearch').val(),

                    plant       : $('#salesPlant').val(),

                    customer    : $('#salesCustomer').val(),

                    pembayaran  : $('#salesPembayaran').val(),

                    status      : $('#salesStatus').val(),

                    date_from   : $('#salesDateFrom').val(),

                    date_to     : $('#salesDateTo').val()

                },

                function(res){

                    renderSummary(
                        res.summary
                    );

                    renderTable(
                        res.rows
                    );

                    $('#salesReportPagination')
                        .html(
                            res.pagination
                        );

                    $('#salesReportInfo')
                        .html(

                            `Total :
                            ${res.total}
                            data`

                        );

                },

                'json'

            );
        }

        function renderSummary(summary)
        {
            $('#summaryTotalSales').html(

                'Rp ' +
                formatRupiah(
                    summary.TOTAL_SALES || 0
                )

            );

            $('#summaryTotalItemSales').html(

                formatRupiah(
                    summary.TOTAL_ITEM || 0
                )

            );

            $('#summaryTotalDocSales').html(

                formatRupiah(
                    summary.TOTAL_DOC || 0
                )

            );

            $('#summaryTotalCustomerSales').html(

                formatRupiah(
                    summary.TOTAL_CUSTOMER || 0
                )

            );
        }

        function renderTable(rows)
        {
            let wrapper = $('#salesResult');

            wrapper.html('');


            /*
            |--------------------------------------------------------------------------
            | TABLE HEADER
            |--------------------------------------------------------------------------
            */

            let thead = `
                <thead>
                    <tr>

                        <th class="text-center" style="width:45px;">
                            #
                        </th>

                        <th>
                            Material
                        </th>

                        <th class="text-center">
                            Basis
                        </th>

                        <th class="text-end">
                            Ekor
                        </th>

                        <th class="text-end">
                            Berat
                        </th>

                        <th class="text-end">
                            Avg BW
                        </th>

                        <th class="text-end">
                            Harga
                        </th>

                        <th class="text-end">
                            Discount
                        </th>

                        <th class="text-end">
                            Total
                        </th>

                    </tr>
                </thead>
            `;


            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if(!rows || rows.length === 0){

                wrapper.html(`

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle table-modern"
                        >

                            ${thead}

                            <tbody>

                                <tr>

                                    <td
                                        colspan="9"
                                        class="text-center text-muted py-5"
                                    >

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

            let totalQty = 0;

            let totalBerat = 0;

            let totalDiscount = 0;

            let totalDetail = 0;


            /*
            |--------------------------------------------------------------------------
            | SALES GROUP
            |--------------------------------------------------------------------------
            */

            let rowsHtml = rows.map(function(row){

                let details =
                    Array.isArray(row.DETAILS)
                        ? row.DETAILS
                        : [];


                let savings =
                    Array.isArray(row.SAVINGS)
                        ? row.SAVINGS
                        : [];


                /*
                |----------------------------------------------------------------------
                | SOURCE RECEIVE
                |----------------------------------------------------------------------
                */

                let receiveNo =
                    row.RECEIVE || '';


                /*
                |----------------------------------------------------------------------
                | FALLBACK REMARK
                |----------------------------------------------------------------------
                */

                if(!receiveNo){

                    let receiveMatch =
                        String(
                            row.REMARK || ''
                        ).match(
                            /AUTO FROM RECEIVE\s+([A-Z0-9]+)/i
                        );

                    if(receiveMatch){

                        receiveNo =
                            receiveMatch[1];

                    }

                }


                /*
                |----------------------------------------------------------------------
                | SOURCE
                |----------------------------------------------------------------------
                */

                let sourceHtml = '';

                if(receiveNo){

                    sourceHtml = `

                        <div>

                            <span
                                class="badge bg-info text-dark"
                            >

                                FROM RECEIVE

                            </span>

                        </div>

                        <div
                            class="small text-muted mt-1"
                        >

                            ${receiveNo}

                        </div>

                    `;

                }
                else {

                    sourceHtml = `

                        <span
                            class="badge bg-secondary"
                        >

                            MANUAL

                        </span>

                    `;

                }


                /*
                |----------------------------------------------------------------------
                | STATUS
                |----------------------------------------------------------------------
                */

                let statusBadge =
                    '<span class="badge bg-warning text-dark">OPEN</span>';


                if(row.STATUS === 'PAID'){

                    statusBadge =
                        '<span class="badge bg-success">PAID</span>';

                }
                else if(row.STATUS === 'PARTIAL'){

                    statusBadge =
                        '<span class="badge bg-info">PARTIAL</span>';

                }


                /*
                |----------------------------------------------------------------------
                | PAYMENT
                |----------------------------------------------------------------------
                */

                let paymentHtml = `

                    <span class="badge bg-secondary">

                        ${row.PEMBAYARAN || '-'}

                    </span>

                `;


                /*
                |----------------------------------------------------------------------
                | SALES TOTAL
                |----------------------------------------------------------------------
                */

                let salesAmount =
                    Number(
                        row.AMOUNT || 0
                    );


                /*
                |----------------------------------------------------------------------
                | GROUP START
                |----------------------------------------------------------------------
                */

                let html = `

                    <tbody class="sales-report-group">

                        <!-- SALES HEADER -->

                        <tr class="sales-group-header">

                            <td
                                colspan="9"
                                style="padding:0;"
                            >

                                <div class="sales-group-box">

                                    <div class="sales-group-info">

                                        <div class="sales-main-info">

                                            <!-- SALES -->

                                            <div class="sales-number">

                                                ${row.SALES || '-'}

                                            </div>


                                            <!-- DATE -->

                                            <div class="sales-date">

                                                <span class="sales-label">
                                                    Tanggal
                                                </span>

                                                <span>
                                                    ${formatDate(
                                                        row.SALES_DATE
                                                    )}
                                                </span>

                                            </div>


                                            <!-- PLANT -->

                                            <div class="sales-plant">

                                                <span class="sales-label">
                                                    Plant
                                                </span>

                                                <span>
                                                    ${row.PLANT_NAME || '-'}
                                                </span>

                                            </div>


                                            <!-- CUSTOMER -->

                                            <div class="sales-customer">

                                                <span class="sales-label">
                                                    Customer
                                                </span>

                                                <div class="fw-semibold">

                                                    ${row.CUSTOMER_NAME || '-'}

                                                </div>

                                                <small class="text-muted">

                                                    ${row.CUSTOMER || ''}

                                                </small>

                                            </div>


                                            <!-- SOURCE -->

                                            <div class="sales-source">

                                                <span class="sales-label">
                                                    Source
                                                </span>

                                                <div>

                                                    ${sourceHtml}

                                                </div>

                                            </div>


                                            <!-- PAYMENT -->

                                            <div class="sales-payment">

                                                <span class="sales-label">
                                                    Payment
                                                </span>

                                                <div>

                                                    ${paymentHtml}

                                                </div>

                                            </div>


                                            <!-- STATUS -->

                                            <div class="sales-status">

                                                <span class="sales-label">
                                                    Status
                                                </span>

                                                <div>

                                                    ${statusBadge}

                                                </div>

                                            </div>

                                        </div>


                                        <!-- TOTAL -->

                                        <div class="sales-total">

                                            <span>
                                                Total Sales
                                            </span>

                                            <strong>

                                                Rp ${formatRupiah(
                                                    salesAmount
                                                )}

                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </td>

                        </tr>
                `;


                /*
                |----------------------------------------------------------------------
                | DETAIL
                |----------------------------------------------------------------------
                */

                if(details.length === 0){

                    html += `

                        <tr>

                            <td
                                class="text-center text-muted"
                            >

                                -

                            </td>

                            <td
                                class="text-muted"
                            >

                                Tidak ada detail

                            </td>

                            <td class="text-center">

                                -

                            </td>

                            <td class="text-end">

                                -

                            </td>

                            <td class="text-end">

                                -

                            </td>

                            <td class="text-end">

                                -

                            </td>

                            <td class="text-end">

                                -

                            </td>

                            <td class="text-end">

                                -

                            </td>

                            <td class="text-end">

                                -

                            </td>

                        </tr>

                    `;

                }
                else {

                    details.forEach(function(d, idx){

                        let qty =
                            Number(
                                d.JUMLAH || 0
                            );


                        let berat =
                            Number(
                                d.BERAT || 0
                            );


                        let avgBw =
                            Number(
                                d.AVG_BW || 0
                            );


                        let harga =
                            Number(
                                d.HARGA || 0
                            );


                        let discount =
                            Number(
                                d.DISCOUNT || 0
                            );


                        let total =
                            Number(
                                d.TOTAL || 0
                            );


                        /*
                        |------------------------------------------------------------------
                        | GLOBAL TOTAL
                        |------------------------------------------------------------------
                        */

                        totalQty += qty;

                        totalBerat += berat;

                        totalDiscount += discount;

                        totalDetail += total;


                        /*
                        |------------------------------------------------------------------
                        | DETAIL
                        |------------------------------------------------------------------
                        */

                        html += `

                            <tr
                                class="sales-detail-row"
                            >

                                <td
                                    class="text-center text-muted"
                                >

                                    ${idx + 1}

                                </td>


                                <td
                                    class="fw-semibold"
                                >

                                    ${d.MATERIAL_NAME || '-'}

                                </td>


                                <td
                                    class="text-center"
                                >

                                    ${d.CALC_BASIS || '-'}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${formatQty(
                                        qty
                                    )}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${formatWeight(
                                        berat
                                    )}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${formatWeight(
                                        avgBw
                                    )}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    Rp ${formatRupiah(
                                        harga
                                    )}

                                </td>


                                <td
                                    class="text-end text-danger"
                                >

                                    Rp ${formatRupiah(
                                        discount
                                    )}

                                </td>


                                <td
                                    class="text-end fw-semibold"
                                >

                                    Rp ${formatRupiah(
                                        total
                                    )}

                                </td>

                            </tr>

                        `;

                    });

                }


                /*
                |----------------------------------------------------------------------
                | SAVING
                |----------------------------------------------------------------------
                */

                if(savings.length > 0){

                    savings.forEach(function(saving){

                        let basis =
                            saving.BASIS || '-';


                        let qty =
                            parseFloat(
                                saving.TOTAL_QTY || 0
                            );


                        let weight =
                            parseFloat(
                                saving.TOTAL_WEIGHT || 0
                            );


                        let rate =
                            parseFloat(
                                saving.RATE || 0
                            );


                        let total =
                            parseFloat(
                                saving.AMOUNT ||
                                saving.TOTAL ||
                                0
                            );


                        html += `

                            <tr
                                class="sales-saving-row"
                            >

                                <td
                                    class="text-center"
                                >

                                    <span
                                        class="saving-icon"
                                    >

                                        +

                                    </span>

                                </td>


                                <td
                                    class="fw-semibold"
                                >

                                    SAVING

                                    ${
                                        saving.SV_NO
                                            ? `
                                                <small
                                                    class="text-muted ms-1"
                                                >

                                                    ${saving.SV_NO}

                                                </small>
                                            `
                                            : ''
                                    }

                                </td>


                                <td
                                    class="text-center"
                                >

                                    ${basis}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${
                                        qty
                                            ? formatQty(qty)
                                            : '-'
                                    }

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${
                                        weight
                                            ? formatWeight(weight)
                                            : '-'
                                    }

                                </td>


                                <td
                                    class="text-end"
                                >

                                    -

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${
                                        rate
                                            ? 'Rp ' +
                                            formatRupiah(
                                                rate
                                            )
                                            : '-'
                                    }

                                </td>


                                <td
                                    class="text-end"
                                >

                                    -

                                </td>


                                <td
                                    class="text-end fw-bold"
                                >

                                    Rp ${formatRupiah(
                                        total
                                    )}

                                </td>

                            </tr>

                        `;

                    });

                }


                /*
                |----------------------------------------------------------------------
                | SUBTOTAL
                |----------------------------------------------------------------------
                */

                let subtotalQty = 0;

                let subtotalBerat = 0;

                let subtotalDiscount = 0;

                let subtotalTotal = 0;


                details.forEach(function(d){

                    subtotalQty +=
                        Number(
                            d.JUMLAH || 0
                        );

                    subtotalBerat +=
                        Number(
                            d.BERAT || 0
                        );

                    subtotalDiscount +=
                        Number(
                            d.DISCOUNT || 0
                        );

                    subtotalTotal +=
                        Number(
                            d.TOTAL || 0
                        );

                });


                html += `

                        <tr
                            class="sales-subtotal-row"
                        >

                            <td
                                colspan="3"
                            >

                                Subtotal Detail
                                (${details.length} item)

                            </td>


                            <td
                                class="text-end"
                            >

                                ${formatQty(
                                    subtotalQty
                                )}

                            </td>


                            <td
                                class="text-end"
                            >

                                ${formatWeight(
                                    subtotalBerat
                                )}

                            </td>


                            <td>

                            </td>


                            <td>

                            </td>


                            <td
                                class="text-end"
                            >

                                ${
                                    subtotalDiscount
                                        ? 'Rp ' +
                                        formatRupiah(
                                            subtotalDiscount
                                        )
                                        : '-'
                                }

                            </td>


                            <td
                                class="text-end"
                            >

                                Rp ${formatRupiah(
                                    subtotalTotal
                                )}

                            </td>

                        </tr>


                        <!-- SPACER -->

                        <tr
                            class="sales-group-spacer"
                        >

                            <td
                                colspan="9"
                            >

                            </td>

                        </tr>

                    </tbody>

                `;


                return html;

            }).join('');


            /*
            |--------------------------------------------------------------------------
            | FINAL TABLE
            |--------------------------------------------------------------------------
            */

            let table = `

                <div class="table-responsive sales-report-wrapper">

                    <table
                        class="
                            table
                            align-middle
                            table-modern
                            sales-report-table
                        "
                    >

                        ${thead}

                        ${rowsHtml}


                        <tfoot>

                            <tr
                                class="sales-grand-total"
                            >

                                <td
                                    colspan="3"
                                    class="text-end"
                                >

                                    Total Semua

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${formatQty(
                                        totalQty
                                    )}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    ${formatWeight(
                                        totalBerat
                                    )}

                                </td>


                                <td>

                                </td>


                                <td>

                                </td>


                                <td
                                    class="text-end"
                                >

                                    Rp ${formatRupiah(
                                        totalDiscount
                                    )}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    Rp ${formatRupiah(
                                        totalDetail
                                    )}

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            `;


            wrapper.html(
                table
            );
        }

        function loadPlant()
        {
            $.get(

                '<?= base_url("payment/get_plant"); ?>',

                function(rows){

                    rows.forEach(function(r){

                        $('#salesPlant').append(`

                            <option value="${r.id}">

                                ${r.text}

                            </option>

                        `);

                    });

                },

                'json'

            );
        }

        function bindEvents()
        {
            /*
            |--------------------------------------------------------------------------
            | FILTER
            |--------------------------------------------------------------------------
            */

            $('#salesSearch').on('keyup', function(){

                loadData(1);

            });

            $('#salesPlant').on('change', function(){

                loadData(1);

            });

            $('#salesCustomer').on('change', function(){

                loadData(1);

            });

            $('#salesPembayaran').on('change', function(){

                loadData(1);

            });

            $('#salesStatus').on('change', function(){

                loadData(1);

            });

            $('#salesDateFrom').on('change', function(){

                loadData(1);

            });

            $('#salesDateTo').on('change', function(){

                loadData(1);

            });

            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            $(document).on(

                'click',

                '#salesReportPagination a',

                function(e){

                    e.preventDefault();


                    let page =
                        $(this)
                            .data('sales-pagination-page');


                    if(!page){

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SCROLL TO TOP
                    |--------------------------------------------------------------------------
                    */

                    window.scrollTo({

                        top: 0,

                        behavior: 'smooth'

                    });


                    loadData(page);

                }

            );

            /*
            |--------------------------------------------------------------------------
            | EXPORT
            |--------------------------------------------------------------------------
            */

            function getSalesExportParams()
            {
                return $.param({

                    search:
                        $('#salesSearch').val(),

                    plant:
                        $('#salesPlant').val(),

                    customer:
                        $('#salesCustomer').val(),

                    pembayaran:
                        $('#salesPembayaran').val(),

                    status:
                        $('#salesStatus').val(),

                    date_from:
                        $('#salesDateFrom').val(),

                    date_to:
                        $('#salesDateTo').val()

                });
            }


            /*
            |--------------------------------------------------------------------------
            | EXPORT EXCEL
            |--------------------------------------------------------------------------
            */

            $('#btnExportSalesExcel').on(

                'click',

                function(e){

                    e.preventDefault();

                    let params =
                        getSalesExportParams();

                    window.open(

                        '<?= base_url("report-sales/export_excel_sales"); ?>?'
                        + params,

                        '_blank'

                    );

                }

            );


            /*
            |--------------------------------------------------------------------------
            | EXPORT PDF
            |--------------------------------------------------------------------------
            */

            $('#btnExportSalesPdf').on(

                'click',

                function(e){

                    e.preventDefault();

                    let params =
                        getSalesExportParams();

                    window.open(

                        '<?= base_url("report-sales/export_pdf_sales"); ?>?'
                        + params,

                        '_blank'

                    );

                }

            );
        }

        return {

            init : init,

            loadData : loadData

        };

    })();

    $(document).ready(function(){

        if(window.ReportSales){

            ReportSales.init();

        }

    });
</script>