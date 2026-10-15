<!-- ==================================================
     REPORT AR - FILTER
=================================================== -->

<div class="mb-4">

    <div class="row g-3">

        <!-- SEARCH -->
        <div class="col-md-4">

            <label class="form-label">
                Search
            </label>

            <input
                type="text"
                id="arSearch"
                class="form-control"
                placeholder="Cari nomor sales, pelanggan, invoice..."
            >

        </div>


        <!-- CUSTOMER -->
        <div class="col-md-3">

            <label class="form-label">
                Customer
            </label>

            <select
                id="arCustomer"
                class="form-select"
                style="width: 100%;"
            >

                <option value="">
                    Semua Pelanggan
                </option>

            </select>

        </div>


        <!-- STATUS -->
        <div class="col-md-2">

            <label class="form-label">
                Status
            </label>

            <select
                id="arStatus"
                class="form-select"
            >

                <option value="">
                    Semua Status
                </option>

                <option value="PARTIAL">
                    Sebagian
                </option>

                <option value="UNPAID">
                    Belum Lunas
                </option>

            </select>

        </div>


        <div class="col-md-3 d-flex align-items-end gap-2">

            <button
                type="button"
                class="btn btn-primary"
                id="btnArSearch"
            >
                Search
            </button>

            <button
                type="button"
                class="btn btn-success"
                id="btnExportAR" style="width: 100%"
            >
                Export Excel
            </button>

        </div>

    </div>

</div>


<!-- ==================================================
     RESULT
=================================================== -->

<div id="arResult">

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5 text-muted">

            Belum ada data

        </div>

    </div>

</div>


<!-- ==================================================
     PAGINATION
=================================================== -->

<div class="d-flex justify-content-between mt-3">

    <div id="arReportInfo"></div>

    <div id="arReportPagination"></div>

</div>


<style>

/* =========================================================
 * AR TABLE
 * ========================================================= */

#arResult .table-modern {

    border-collapse:
        separate;

    border-spacing:
        0;

    width:
        max-content;

    min-width:
        100%;

}


/* =========================================================
 * NO WRAP
 * ========================================================= */

#arResult .table-modern th,
#arResult .table-modern td {

    white-space:
        nowrap !important;

    word-break:
        normal !important;

    overflow-wrap:
        normal !important;

    vertical-align:
        middle;

}


/* =========================================================
 * HEADER
 * ========================================================= */

#arResult .table-modern thead th {

    border-bottom:
        2px solid #d1d5db !important;

    background:
        #f1f3f5;

    text-align:
        center !important;

    vertical-align:
        middle !important;

}


/* =========================================================
 * DETAIL ROW
 * ========================================================= */

#arResult .table-modern tbody td {

    border-bottom:
        1px solid #e2e6ea !important;

}


/* =========================================================
 * CUSTOMER GROUP
 * ========================================================= */

#arResult .ar-customer-header {

    background:
        #f8fafc;

    font-weight:
        700;

    border-top:
        2px solid #c7cdd4;

}


/* =========================================================
 * CUSTOMER TOTAL
 * ========================================================= */

#arResult .ar-customer-total td {

    background:
        #f3f6f9;

    border-top:
        2px solid #c7cdd4 !important;

    font-weight:
        700;

}


/* =========================================================
 * HOVER
 * ========================================================= */

#arResult .table-modern tbody tr:hover td {

    background:
        #f8fafc;

}


/* =========================================================
 * HORIZONTAL SCROLL
 * ========================================================= */

#arResult .table-responsive {

    overflow-x:
        auto;

    overflow-y:
        hidden;

    width:
        100%;

}


/* =========================================================
 * STATUS
 * ========================================================= */

.ar-status {

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


/* =========================================================
 * PARTIAL
 * ========================================================= */

.ar-status-partial {

    background:
        #dbeafe;

    color:
        #1d4ed8;

}


/* =========================================================
 * UNPAID
 * ========================================================= */

.ar-status-unpaid {

    background:
        #fef3c7;

    color:
        #92400e;

}


/* =========================================================
 * PAYMENT BUTTON
 * ========================================================= */

.ar-payment-btn {

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


.ar-payment-btn:hover {

    background:
        #f1f3f5;

    border-color:
        #adb5bd;

}


/* =========================================================
 * PAYMENT ROW
 * ========================================================= */

#arResult .ar-payment-row td {

    background:
        #fafafa;

    border-bottom:
        1px solid #e9ecef !important;

}


/* =========================================================
 * PAYMENT WRAPPER
 * ========================================================= */

.ar-payment-wrapper {

    padding:
        12px 15px;

}


/* =========================================================
 * PAYMENT TITLE
 * ========================================================= */

.ar-payment-title {

    font-weight:
        700;

    font-size:
        13px;

    margin-bottom:
        8px;

    color:
        #495057;

}


/* =========================================================
 * PAYMENT TABLE
 * ========================================================= */

.ar-payment-table {

    width:
        max-content;

    min-width:
        750px;

    margin-bottom:
        0;

    border-collapse:
        collapse;

    font-size:
        12px;

}


.ar-payment-table th {

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


.ar-payment-table td {

    padding:
        7px 10px;

    border:
        1px solid #dee2e6;

    white-space:
        nowrap;

    vertical-align:
        middle;

}


.ar-payment-table .payment-total td {

    background:
        #f3f6f9;

    font-weight:
        700;

    border-top:
        2px solid #c7cdd4;

}


/* =========================================================
 * PAYMENT TYPE
 * ========================================================= */

.ar-payment-type {

    display:
        inline-block;

    padding:
        3px 7px;

    border-radius:
        4px;

    font-size:
        10px;

    font-weight:
        700;

}


/* SALES */

.ar-payment-type-sales {

    background:
        #e8f1ff;

    color:
        #1d4ed8;

}


/* SAVING */

.ar-payment-type-saving {

    background:
        #e9f8ef;

    color:
        #15803d;

}


/* =========================================================
 * PAGINATION LOADING
 * ========================================================= */

#arReportPagination .pagination.loading {

    opacity:
        .6;

    pointer-events:
        none;

}

</style>


<script>

window.ReportAR = (function(){


    /* =====================================================
     * STATE
     * ===================================================== */

    let state = {

        page:
            1,

        limit:
            20

    };


    let INITIALIZED =
        false;


    /* =====================================================
     * FORMAT RUPIAH
     * ===================================================== */

    function formatRupiah(value)
    {

        return Number(
            value || 0
        ).toLocaleString(
            'id-ID'
        );

    }


    /* =====================================================
     * FORMAT DATE
     * ===================================================== */

    function formatDate(date)
    {

        if (!date) {

            return '-';

        }


        let parsed =
            new Date(date);


        if (
            isNaN(
                parsed.getTime()
            )
        ) {

            return '-';

        }


        return parsed.toLocaleDateString(

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


    /* =====================================================
     * ESCAPE HTML
     * ===================================================== */

    function escapeHtml(value)
    {

        return $('<div>')
            .text(
                value == null
                    ? ''
                    : value
            )
            .html();

    }


    /* =====================================================
     * INIT
     * ===================================================== */

    function init()
    {

        if (INITIALIZED) {

            return;

        }


        INITIALIZED =
            true;


        initCustomerSelect2();

        bindEvents();

        loadCustomer();

        loadData(1);

    }


    /* =====================================================
     * EVENTS
     * ===================================================== */

    function bindEvents()
    {


        /* -------------------------------------------------
         * SEARCH ENTER
         * ------------------------------------------------- */

        $('#arSearch').on(

            'keyup',

            function(e){

                if (
                    e.key === 'Enter' ||
                    e.which === 13
                ){

                    e.preventDefault();

                    loadData(1);

                }

            }

        );


        /* -------------------------------------------------
         * SEARCH BUTTON
         * ------------------------------------------------- */

        $('#btnArSearch').on(

            'click',

            function(){

                loadData(1);

            }

        );


        /* -------------------------------------------------
         * CUSTOMER
         * ------------------------------------------------- */

        $('#arCustomer').on(

            'change',

            function(){

                loadData(1);

            }

        );


        /* -------------------------------------------------
         * STATUS
         * ------------------------------------------------- */

        $('#arStatus').on(

            'change',

            function(){

                loadData(1);

            }

        );

        $('#btnExportAR').on(
            'click',
            function(){

                const params = $.param({

                    search:
                        $('#arSearch').val(),

                    customer:
                        $('#arCustomer').val(),

                    status:
                        $('#arStatus').val()

                });


                const url =
                    '<?= base_url("report-accounting/export_ar_excel"); ?>'
                    + '?'
                    + params;


                window.open(
                    url,
                    '_blank'
                );

            }
        );


        /* =================================================
         * PAGINATION
         * ================================================= */

        $(document).on(

            'click',

            '#arReportPagination a[data-ar-pagination-page]',

            function(e){

                e.preventDefault();

                e.stopPropagation();


                let button =
                    $(this);


                let page =
                    parseInt(

                        button.attr(
                            'data-ar-pagination-page'
                        )

                    );


                if (!page) {

                    return false;

                }


                let pagination =
                    button.closest(
                        '.pagination'
                    );


                if (
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


        /* =================================================
         * PAYMENT TOGGLE
         * ================================================= */

        $(document).on(

            'click',

            '#arResult .ar-payment-btn',

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


                if (
                    row.length === 0
                ){

                    return;

                }


                row.toggleClass(
                    'd-none'
                );


                let count =
                    button.attr(
                        'data-payment-count'
                    );


                if (
                    row.hasClass(
                        'd-none'
                    )
                ){

                    button.text(

                        'Lihat Pembayaran (' +
                        count +
                        ')'

                    );

                }

                else {

                    button.text(

                        'Sembunyikan Pembayaran (' +
                        count +
                        ')'

                    );

                }

            }

        );

    }


    /* =====================================================
     * LOAD DATA
     * ===================================================== */

    function loadData(
        page = 1
    )
    {

        state.page =
            parseInt(page) || 1;


        let wrapper =
            $('#arResult');


        wrapper.css(
            'opacity',
            '0.5'
        );


        $.ajax({

            url:
                '<?= base_url("report-accounting/load_ar"); ?>',

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
                    $('#arSearch').val(),

                customer:
                    $('#arCustomer').val(),

                status:
                    $('#arStatus').val()

            },


            /* =========================================
             * SUCCESS
             * ========================================= */

            success:
                function(res)
                {

                    renderTable(
                        res.rows || []
                    );


                    $('#arReportPagination')
                        .html(
                            res.pagination || ''
                        );


                    $('#arReportInfo')
                        .html(

                            'Total Pelanggan : <b>' +

                            (
                                res.total || 0
                            )

                            +

                            '</b>'

                        );


                    wrapper.css(
                        'opacity',
                        '1'
                    );


                    $('#arReportPagination')
                        .find('.pagination')
                        .removeClass(
                            'loading'
                        );


                    /*
                    * Scroll ke hasil ketika
                    * pindah halaman.
                    */

                    if (
                        state.page > 1
                    ){

                        $('html, body')
                            .stop()
                            .animate(

                                {

                                    scrollTop:
                                        $('#arResult')
                                            .offset()
                                            .top - 80

                                },

                                250

                            );

                    }

                },


            /* =========================================
             * ERROR
             * ========================================= */

            error:
                function()
                {

                    wrapper.css(
                        'opacity',
                        '1'
                    );


                    $('#arReportPagination')
                        .find('.pagination')
                        .removeClass(
                            'loading'
                        );


                    $('#arResult')
                        .html(`

                            <div
                                class="
                                    card
                                    border-0
                                    shadow-sm
                                "
                            >

                                <div
                                    class="
                                        card-body
                                        text-center
                                        py-5
                                        text-danger
                                    "
                                >

                                    Gagal mengambil
                                    data laporan AR.

                                </div>

                            </div>

                        `);

                }

        });

    }


    /* =====================================================
     * PAYMENT HISTORY
     * ===================================================== */

    function buildPembayaranHistory(

        payments,

        salesNo,

        targetId

    )
    {

        if (
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

            function(payment)
            {

                let offset =
                    Number(
                        payment.AMOUNT_OFFSET || 0
                    );


                let cashInNilai =
                    Number(
                        payment.CASH_IN_AMOUNT || 0
                    );


                let allocationType =
                    String(

                        payment.ALLOCATION_TYPE
                        ||
                        'SALES'

                    ).toUpperCase();


                let allocationText =

                    allocationType === 'SAVING'

                        ? 'SAVING'

                        : 'SALES';


                let allocationClass =

                    allocationType === 'SAVING'

                        ? 'ar-payment-type-saving'

                        : 'ar-payment-type-sales';


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
                                class="fw-semibold"
                            >

                                ${
                                    escapeHtml(
                                        payment.CASH_IN || '-'
                                    )
                                }

                            </span>

                        </td>


                        <td>

                            <span
                                class="
                                    ar-payment-type
                                    ${allocationClass}
                                "
                            >

                                ${allocationText}

                            </span>

                        </td>


                        <td>

                            ${
                                escapeHtml(
                                    payment.PEMBAYARAN || '-'
                                )
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
                class="ar-payment-row d-none"
            >

                <td
                    colspan="10"
                >

                    <div
                        class="ar-payment-wrapper"
                    >

                        <div
                            class="ar-payment-title"
                        >

                            RIWAYAT PEMBAYARAN -

                            ${
                                escapeHtml(
                                    salesNo || '-'
                                )
                            }

                        </div>


                        <div
                            class="table-responsive"
                        >

                            <table
                                class="ar-payment-table"
                            >

                                <thead>

                                    <tr>

                                        <th>
                                            Tanggal
                                        </th>

                                        <th>
                                            Cash In
                                        </th>

                                        <th>
                                            Alokasi
                                        </th>

                                        <th>
                                            Pembayaran
                                        </th>

                                        <th class="text-end">
                                            Cash In Nilai
                                        </th>

                                        <th class="text-end">
                                            AR Offset
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    ${paymentRows}


                                    <tr
                                        class="payment-total"
                                    >

                                        <td
                                            colspan="5"
                                            class="text-end"
                                        >

                                            TOTAL DIBAYAR

                                        </td>


                                        <td
                                            class="text-end"
                                        >

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


    /* =====================================================
     * TABLE
     * ===================================================== */

    function renderTable(
        rows
    )
    {

        let wrapper =
            $('#arResult');


        wrapper.html('');


        /* =================================================
         * EMPTY
         * ================================================= */

        if (
            !rows ||
            rows.length === 0
        ){

            wrapper.html(`

                <div
                    class="table-responsive"
                >

                    <table
                        class="
                            table
                            table-hover
                            align-middle
                            table-modern
                        "
                    >

                        <thead>

                            <tr>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    No. Sales
                                </th>

                                <th>
                                    Plant
                                </th>

                                <th>
                                    Nilai
                                </th>

                                <th>
                                    Saving
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
                                    colspan="10"
                                    class="
                                        text-center
                                        text-muted
                                        py-4
                                    "
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


        /* =================================================
         * BUILD CUSTOMER GROUP
         * ================================================= */

        let rowsHtml =
            '';


        rows.forEach(

            function(group)
            {

                /*
                * Backend:
                *
                * DETAILS
                *
                * bukan ROWS
                */

                let details =
                    group.DETAILS || [];


                let totalNilai =
                    0;


                let totalSaving =
                    0;


                let totalDibayar =
                    0;


                let totalSisa =
                    0;


                /* =========================================
                 * CUSTOMER HEADER
                 * ========================================= */

                rowsHtml += `

                    <tr
                        class="ar-customer-header"
                    >

                        <td
                            colspan="10"
                        >

                            <span class="fw-bold">

                                PELANGGAN :

                                ${
                                    escapeHtml(
                                        group.CUSTOMER || '-'
                                    )
                                }

                            </span>


                            <span
                                class="ms-2 text-muted"
                            >

                                -

                                ${
                                    escapeHtml(
                                        group.CUSTOMER_NAME || '-'
                                    )
                                }

                            </span>

                        </td>

                    </tr>

                `;


                /* =========================================
                 * NO HISTORY
                 * ========================================= */

                if (
                    details.length === 0
                ){

                    rowsHtml += `

                        <tr>

                            <td
                                colspan="10"
                                class="
                                    text-center
                                    text-muted
                                "
                            >

                                Tidak ada riwayat AR

                            </td>

                        </tr>

                    `;

                    return;

                }


                /* =========================================
                 * DETAILS
                 * ========================================= */

                details.forEach(

                    function(d, index)
                    {

                        /* ---------------------------------
                         * SALES VALUE
                         * --------------------------------- */

                        let amount =
                            Number(
                                d.SALES_AMOUNT || 0
                            );


                        /* ---------------------------------
                         * SAVING VALUE
                         *
                         * Total Saving yang terkait
                         * dengan Sales.
                         * --------------------------------- */

                        let saving =
                            Number(
                                d.SAVING_AMOUNT || 0
                            );


                        /* ---------------------------------
                         * TOTAL PAID
                         *
                         * Sales Payment
                         * +
                         * Saving Payment
                         * --------------------------------- */

                        let paid =
                            Number(
                                d.TOTAL_PAID || 0
                            );


                        /* ---------------------------------
                         * GRAND OUTSTANDING
                         *
                         * Sales Remain
                         * +
                         * Saving Remain
                         * --------------------------------- */

                        let remain =
                            Number(
                                d.GRAND_OUTSTANDING || 0
                            );


                        /* =================================
                         * STATUS
                         * ================================= */

                        let status =
                            String(
                                d.AR_STATUS || ''
                            ).toUpperCase();


                        let statusClass =
                            'ar-status-unpaid';


                        let statusText =
                            'BELUM LUNAS';


                        if (
                            status === 'PARTIAL'
                        ){

                            statusClass =
                                'ar-status-partial';


                            statusText =
                                'SEBAGIAN';

                        }


                        /* =================================
                         * PAYMENTS
                         * ================================= */

                        let payments =
                            d.PAYMENTS || [];


                        let paymentButton =
                            '';


                        let paymentRow =
                            '';


                        if (
                            payments.length > 0
                        ){

                            let safeId =

                                'arPembayaran_'

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
                                    d.SALES || ''
                                )
                                .replace(
                                    /[^a-zA-Z0-9_-]/g,
                                    ''
                                );


                            paymentButton = `

                                <button
                                    type="button"
                                    class="ar-payment-btn"
                                    data-payment-target="${safeId}"
                                    data-payment-count="${payments.length}"
                                >

                                    Lihat Pembayaran
                                    (${payments.length})

                                </button>

                            `;


                            paymentRow =
                                buildPembayaranHistory(

                                    payments,

                                    d.SALES,

                                    safeId

                                );

                        }


                        /* =================================
                         * CUSTOMER TOTAL
                         * ================================= */

                        totalNilai +=
                            amount;


                        totalSaving +=
                            saving;


                        totalDibayar +=
                            paid;


                        totalSisa +=
                            remain;


                        /* =================================
                         * AR ROW
                         * ================================= */

                        rowsHtml += `

                            <tr>

                                <!-- TANGGAL -->

                                <td>

                                    ${
                                        formatDate(
                                            d.SALES_DATE
                                        )
                                    }

                                </td>


                                <!-- NO SALES -->

                                <td>

                                    <span
                                        class="
                                            fw-semibold
                                            text-primary
                                        "
                                    >

                                        ${
                                            escapeHtml(
                                                d.SALES || '-'
                                            )
                                        }

                                    </span>

                                </td>


                                <!-- PLANT -->

                                <td>

                                    ${
                                        escapeHtml(
                                            d.PLANT || '-'
                                        )
                                    }

                                </td>


                                <!-- NILAI -->

                                <td
                                    class="text-end"
                                >

                                    Rp

                                    ${
                                        formatRupiah(
                                            amount
                                        )
                                    }

                                </td>


                                <!-- SAVING -->

                                <td
                                    class="text-end"
                                >

                                    Rp

                                    ${
                                        formatRupiah(
                                            saving
                                        )
                                    }

                                </td>


                                <!-- DIBAYAR -->

                                <td
                                    class="text-end"
                                >

                                    Rp

                                    ${
                                        formatRupiah(
                                            paid
                                        )
                                    }

                                </td>


                                <!-- SISA -->

                                <td
                                    class="text-end"
                                >

                                    Rp

                                    ${
                                        formatRupiah(
                                            remain
                                        )
                                    }

                                </td>


                                <!-- STATUS -->

                                <td
                                    class="text-center"
                                >

                                    <span
                                        class="
                                            ar-status
                                            ${statusClass}
                                        "
                                    >

                                        ${statusText}

                                    </span>

                                </td>


                                <!-- PEMBAYARAN -->

                                <td>

                                    ${paymentButton}

                                </td>


                                <!-- KETERANGAN -->

                                <td>

                                    ${
                                        escapeHtml(

                                            d.SALES_REMARK
                                            ||
                                            d.REMARK
                                            ||
                                            '-'

                                        )
                                    }

                                </td>

                            </tr>


                            ${paymentRow}

                        `;

                    }

                );


                /* =========================================
                 * CUSTOMER TOTAL
                 * ========================================= */

                rowsHtml += `

                    <tr
                        class="ar-customer-total"
                    >

                        <td
                            colspan="3"
                            class="text-end"
                        >

                            TOTAL PELANGGAN

                        </td>


                        <!-- TOTAL NILAI -->

                        <td
                            class="text-end"
                        >

                            Rp

                            ${
                                formatRupiah(
                                    totalNilai
                                )
                            }

                        </td>


                        <!-- TOTAL SAVING -->

                        <td
                            class="text-end"
                        >

                            Rp

                            ${
                                formatRupiah(
                                    totalSaving
                                )
                            }

                        </td>


                        <!-- TOTAL DIBAYAR -->

                        <td
                            class="text-end"
                        >

                            Rp

                            ${
                                formatRupiah(
                                    totalDibayar
                                )
                            }

                        </td>


                        <!-- TOTAL SISA -->

                        <td
                            class="text-end"
                        >

                            Rp

                            ${
                                formatRupiah(
                                    totalSisa
                                )
                            }

                        </td>


                        <td
                            colspan="3"
                        ></td>

                    </tr>

                `;

            }

        );


        /* =================================================
         * TABLE
         * ================================================= */

        let table = `

            <div
                class="table-responsive"
            >

                <table
                    class="
                        table
                        table-hover
                        align-middle
                        table-modern
                    "
                >

                    <thead>

                        <tr>

                            <th>
                                Tanggal
                            </th>

                            <th>
                                No. Sales
                            </th>

                            <th>
                                Plant
                            </th>

                            <th class="text-end">
                                Nilai
                            </th>

                            <th class="text-end">
                                Saving
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


    /* =====================================================
     * CUSTOMER SELECT2
     * ===================================================== */

    function initCustomerSelect2()
    {

        $('#arCustomer').select2({

            width:
                '100%',

            placeholder:
                'Semua Pelanggan',

            allowClear:
                true

        });

    }


    /* =====================================================
     * LOAD CUSTOMER
     * ===================================================== */

    function loadCustomer()
    {

        $.get(

            '<?= base_url("saving/get_customer"); ?>',

            function(rows)
            {

                let select =
                    $('#arCustomer');


                select.empty();


                select.append(`

                    <option value="">
                        Semua Pelanggan
                    </option>

                `);


                if (
                    rows &&
                    rows.length
                ){

                    rows.forEach(

                        function(r)
                        {

                            select.append(`

                                <option
                                    value="${escapeHtml(r.id)}"
                                >

                                    ${
                                        escapeHtml(
                                            r.text
                                        )
                                    }

                                </option>

                            `);

                        }

                    );

                }


                select.trigger(
                    'change.select2'
                );

            },

            'json'

        );

    }


    /* =====================================================
     * RETURN
     * ===================================================== */

    return {

        init:
            init,

        loadData:
            loadData

    };

})();


/* =========================================================
 * DOCUMENT READY
 * ========================================================= */

$(document).ready(

    function()
    {

        /*
        * Jangan langsung load AR.
        *
        * Report AR akan di-init oleh
        * report_accounting/index.php
        * ketika tab AR dibuka.
        */

    }

);

</script>