<?php
?>

<div class="po-report-wrap">

    <!-- FILTER -->
    <div class="report-filter-card">

        <div class="row g-3 align-items-end">

            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Plant
                </label>

                <select
                    id="filter_plant"
                    class="form-control"
                >

                    <option value="">
                        Semua Plant
                    </option>

                    <?php foreach ($plants as $p): ?>

                        <?php if ($p->CODE == '*') continue; ?>

                        <option value="<?= $p->CODE ?>">
                            <?= $p->CODE_NAME ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Supplier
                </label>

                <select
                    id="filter_supplier"
                    class="form-control"
                >

                    <option value=""></option>

                    <?php foreach ($suppliers as $s): ?>

                        <option value="<?= $s->CUST ?>">
                            <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Nomor PO
                </label>

                <input
                    type="text"
                    id="filter_po"
                    class="form-control"
                    placeholder="Cari Nomor PO..."
                >

            </div>


            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Tanggal Mulai
                </label>

                <input
                    type="date"
                    id="filter_date_from"
                    class="form-control"
                >

            </div>


            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Tanggal Selesai
                </label>

                <input
                    type="date"
                    id="filter_date_to"
                    class="form-control"
                >

            </div>


            <div class="col-md-10"></div>


            <div class="col-md-2">

                <div class="btn-group w-100">

                    <button
                        class="btn btn-success dropdown-toggle w-100"
                        data-bs-toggle="dropdown"
                    >

                        <i class="fa fa-download me-1"></i>

                        Export

                    </button>


                    <ul class="dropdown-menu w-100">

                        <li>

                            <a
                                href="#"
                                class="dropdown-item"
                                id="exportExcel"
                            >

                                <i class="fa fa-file-excel text-success me-2"></i>

                                Export Excel

                            </a>

                        </li>


                        <li>

                            <a
                                href="#"
                                class="dropdown-item"
                                id="exportPDF"
                            >

                                <i class="fa fa-file-pdf text-danger me-2"></i>

                                Export PDF

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>


    <!-- LOADING -->

    <div
        id="poLoading"
        class="report-loading d-none"
    >

        <div class="text-center">

            <div class="spinner-border text-primary"></div>

            <div class="fw-semibold mt-3">
                Loading report...
            </div>

            <small class="text-muted">
                Mohon tunggu sebentar
            </small>

        </div>

    </div>


    <!-- REPORT -->

    <div id="poReportWrapper"></div>

    <!-- PAGINATION -->

    <div class="d-flex justify-content-between align-items-center mt-4">

        <div
            id="pageInfo"
            class="small text-muted"
        ></div>

        <div id="pagination"></div>

    </div>

</div>


<style>

    .status-open{
    background:#fff3cd;
    color:#9a6700;
}

.status-success{
    background:#d1e7dd;
    color:#0f5132;
}

.status-danger{
    background:#f8d7da;
    color:#842029;
}

.status-default{
    background:#e2e3e5;
    color:#41464b;
}

.payment-paid{
    background:#d1e7dd;
    color:#0f5132;
}

.payment-unpaid{
    background:#fff3cd;
    color:#9a6700;
}

.payment-partial{
    background:#cff4fc;
    color:#055160;
}

.payment-default{
    background:#e2e3e5;
    color:#41464b;
}

/* ======================================================
   WRAPPER
   ====================================================== */

.po-report-wrap{
    padding:4px;
}


/* ======================================================
   FILTER
   ====================================================== */

.report-filter-card{

    background:#fff;

    border:1px solid #edf2f7;

    border-radius:18px;

    padding:24px;

    box-shadow:0 8px 25px rgba(15,23,42,.05);

    margin-bottom:24px;

}


/* ======================================================
   LOADING
   ====================================================== */

.report-loading{

    position:fixed;

    inset:0;

    z-index:9999;

    background:rgba(255,255,255,.75);

    backdrop-filter:blur(2px);

    display:flex;

    justify-content:center;

    align-items:center;

}



/* ======================================================
   TABLE SCROLL
   ====================================================== */

.report-table-scroll{

    width:100%;

    overflow-x:auto;

    overflow-y:visible;

}


/* ======================================================
   TABLE
   ====================================================== */

.po-report-table{

    width:max-content;

    min-width:100%;

    margin:0;

    border-collapse:separate;

    border-spacing:0;

    font-size:13px;

}


/* ======================================================
   GROUP HEADER
   ====================================================== */

.po-report-table thead .group-header th{

    position:sticky;

    top:0;

    z-index:20;

    background:#0F4C81;

    color:#fff;

    text-align:center;

    font-size:14px;

    font-weight:700;

    letter-spacing:.5px;

    padding:12px 14px;

    border-right:1px solid rgba(255,255,255,.18);

    white-space:nowrap;

}


/* ======================================================
   COLUMN HEADER
   ====================================================== */

.po-report-table thead .column-header th{

    position:sticky;

    top:42px;

    z-index:19;

    background:#f8fafc;

    color:#334155;

    font-size:12px;

    font-weight:700;

    padding:11px 12px;

    border-bottom:2px solid #dbe3ec;

    border-right:1px solid #e5e7eb;

    white-space:nowrap;

}


/* ======================================================
   BODY
   ====================================================== */

.po-report-table tbody td{

    padding:11px 12px;

    vertical-align:middle;

    border-bottom:1px solid #edf2f7;

    border-right:1px solid #edf2f7;

    white-space:nowrap;

    background:#fff;

}


/* ======================================================
   HOVER
   ====================================================== */

.po-report-table tbody tr:hover td{

    background:#f8fafc;

}


/* ======================================================
   PO
   ====================================================== */

.po-number{

    font-weight:700;

    color:#0F4C81;

}


/* ======================================================
   TEXT
   ====================================================== */

.po-text{

    max-width:220px;

    overflow:hidden;

    text-overflow:ellipsis;

    white-space:nowrap;

}


/* ======================================================
   REMARK
   ====================================================== */

.po-remark{

    max-width:250px;

    overflow:hidden;

    text-overflow:ellipsis;

    white-space:nowrap;

}


/* ======================================================
   STATUS
   ====================================================== */

.po-status{

    display:inline-block;

    padding:5px 10px;

    border-radius:20px;

    font-size:11px;

    font-weight:700;

    white-space:nowrap;

}


/* ======================================================
   PAYMENT STATUS
   ====================================================== */

.payment-status{

    display:inline-block;

    padding:5px 10px;

    border-radius:20px;

    font-size:11px;

    font-weight:700;

    white-space:nowrap;

}


/* ======================================================
   EMPTY
   ====================================================== */

.po-empty{

    padding:50px !important;

    color:#64748b;

    text-align:center;

}


/* ======================================================
   NUMERIC
   ====================================================== */

.po-report-table .text-end{

    font-variant-numeric:tabular-nums;

}


/* ======================================================
   DATE
   ====================================================== */

.po-date{

    white-space:nowrap;

}


/* ======================================================
   SCROLLBAR
   ====================================================== */

.report-table-scroll::-webkit-scrollbar{

    height:10px;

}

.report-table-scroll::-webkit-scrollbar-track{

    background:#f1f5f9;

}

.report-table-scroll::-webkit-scrollbar-thumb{

    background:#cbd5e1;

    border-radius:10px;

}

.report-table-scroll::-webkit-scrollbar-thumb:hover{

    background:#94a3b8;

}

/* ======================================================
   PO CARD
   ====================================================== */

.po-card{

    background:#fff;

    border:1px solid #e5e7eb;

    border-radius:14px;

    overflow:hidden;

    margin-bottom:18px;

    box-shadow:0 4px 18px rgba(15,23,42,.05);

}


/* ======================================================
   PO HEADER
   ====================================================== */

.po-head{

    background:#f1f7fc;

    border-bottom:1px solid #dce8f2;

    padding:14px 16px;

}


/* ======================================================
   PO TITLE
   ====================================================== */

.po-title{

    font-size:14px;

    font-weight:700;

    color:#0F4C81;

}


/* ======================================================
   PO STATUS
   ====================================================== */

.po-status{

    display:inline-block;

    padding:4px 10px;

    border-radius:20px;

    font-size:10px;

    font-weight:700;

    white-space:nowrap;

}

.po-status-open{

    background:#fff3cd;

    color:#9a6700;

}

.po-status-received{

    background:#d1e7dd;

    color:#0f5132;

}

.po-status-cancel{

    background:#f8d7da;

    color:#842029;

}

.po-status-default{

    background:#e2e3e5;

    color:#41464b;

}


/* ======================================================
   META
   ====================================================== */

.po-meta{

    display:grid;

    grid-template-columns:
        1fr
        1fr
        1fr
        1fr;

    gap:8px 20px;

    margin-top:12px;

}


.po-meta-item{

    min-width:0;

}


.po-meta-label{

    display:block;

    font-size:9px;

    font-weight:700;

    color:#94a3b8;

    letter-spacing:.5px;

    margin-bottom:2px;

}


.po-meta-value{

    font-size:11px;

    font-weight:600;

    color:#334155;

    white-space:nowrap;

    overflow:hidden;

    text-overflow:ellipsis;

}


/* ======================================================
   PO BODY
   ====================================================== */

.po-body{

    padding:12px;

    background:#fff;

}


/* ======================================================
   TABLE WRAPPER
   ====================================================== */

.po-table-scroll{

    width:100%;

    overflow-x:auto;

    overflow-y:visible;

}


/* ======================================================
   DETAIL TABLE
   ====================================================== */

.po-detail-table{

    width:max-content;

    min-width:100%;

    margin:0;

    border-collapse:collapse;

    font-size:11px;

}


/* ======================================================
   GROUP HEADER
   ====================================================== */

.po-detail-table .group-head th{

    background:#eaf3fb;

    color:#0F4C81;

    text-align:center;

    font-size:10px;

    font-weight:700;

    padding:7px 10px;

    border:1px solid #d7e5f0;

    white-space:nowrap;

}


/* ======================================================
   COLUMN HEADER
   ====================================================== */

.po-detail-table .column-head th{

    background:#f8fafc;

    color:#475569;

    font-size:10px;

    font-weight:700;

    padding:7px 9px;

    border:1px solid #e5e7eb;

    white-space:nowrap;

}


/* ======================================================
   TABLE BODY
   ====================================================== */

.po-detail-table td{

    padding:8px 9px;

    border:1px solid #edf2f7;

    color:#475569;

    white-space:nowrap;

    background:#fff;

}


/* ======================================================
   HOVER
   ====================================================== */

.po-detail-table tbody tr:hover td{

    background:#f8fafc;

}


/* ======================================================
   IMPORTANT VALUE
   ====================================================== */

.po-main-value{

    font-weight:700;

    color:#0F4C81;

}


/* ======================================================
   TOTAL
   ====================================================== */

.po-total{

    font-weight:700;

    color:#0F4C81;

}


/* ======================================================
   PAYMENT
   ====================================================== */

.po-payment-paid{

    display:inline-block;

    padding:4px 9px;

    border-radius:20px;

    background:#d1e7dd;

    color:#0f5132;

    font-size:10px;

    font-weight:700;

}

.po-payment-unpaid{

    display:inline-block;

    padding:4px 9px;

    border-radius:20px;

    background:#fff3cd;

    color:#9a6700;

    font-size:10px;

    font-weight:700;

}

.po-payment-partial{

    display:inline-block;

    padding:4px 9px;

    border-radius:20px;

    background:#cff4fc;

    color:#055160;

    font-size:10px;

    font-weight:700;

}

.po-payment-default{

    display:inline-block;

    padding:4px 9px;

    border-radius:20px;

    background:#e2e3e5;

    color:#41464b;

    font-size:10px;

    font-weight:700;

}


/* ======================================================
   REMARK
   ====================================================== */

.po-remark-box{

    margin-top:10px;

    padding:8px 10px;

    background:#f8fafc;

    border-left:3px solid #0F4C81;

    border-radius:4px;

    font-size:10px;

    color:#64748b;

}


/* ======================================================
   RESPONSIVE
   ====================================================== */

@media(max-width:1100px){

    .po-meta{

        grid-template-columns:
            1fr
            1fr;

    }

}


@media(max-width:700px){

    .po-meta{

        grid-template-columns:1fr;

    }

}

/* ======================================================
   REPORT TABLE
   ====================================================== */

.po-report-card{

    background:#fff;

    border:1px solid #edf2f7;

    border-radius:16px;

    box-shadow:0 6px 22px rgba(15,23,42,.05);

    overflow:hidden;

}


/* ======================================================
   TABLE SCROLL
   ====================================================== */

.po-table-scroll{

    width:100%;

    overflow-x:auto;

    overflow-y:visible;

}


/* ======================================================
   MAIN TABLE
   ====================================================== */

.po-report-table{

    width:max-content;

    min-width:100%;

    margin:0;

    border-collapse:separate;

    border-spacing:0;

    font-size:11px;

}


/* ======================================================
   GROUP HEADER
   ====================================================== */

.po-report-table thead .group-header th{

    position:sticky;

    top:0;

    z-index:20;

    background:#eaf3fb;

    color:#0F4C81;

    text-align:center;

    font-size:13px;

    font-weight:700;

    letter-spacing:.3px;

    padding:8px 10px;

    border-top:1px solid #dce8f2;

    border-right:1px solid #d7e5f0;

    border-bottom:1px solid #d7e5f0;

    white-space:nowrap;

}


/* ======================================================
   COLUMN HEADER
   ====================================================== */

.po-report-table thead .column-header th{

    position:sticky;

    top:34px;

    z-index:19;

    background:#f8fafc;

    color:#475569;

    text-align:left;

    font-size:13px;

    font-weight:700;

    padding:8px 9px;

    border-right:1px solid #e5e7eb;

    border-bottom:2px solid #dbe3ec;

    white-space:nowrap;

}


/* ======================================================
   BODY
   ====================================================== */

.po-report-table tbody td{

    padding:8px 9px;

    vertical-align:middle;

    color:#475569;

    background:#fff;

    border-right:1px solid #edf2f7;

    border-bottom:1px solid #edf2f7;

    white-space:nowrap;

}


/* ======================================================
   HOVER
   ====================================================== */

.po-report-table tbody tr:hover td{

    background:#f8fafc;

}


/* ======================================================
   PO
   ====================================================== */

.po-report-table .po-number{

    color:#0066a1;

    font-weight:700;

}


/* ======================================================
   TEXT COLUMN
   ====================================================== */

.po-report-table .po-text{

    max-width:180px;

    overflow:hidden;

    text-overflow:ellipsis;

    white-space:nowrap;

}


/* ======================================================
   NUMERIC
   ====================================================== */

.po-report-table .text-end{

    text-align:right !important;

    font-variant-numeric:tabular-nums;

}


/* ======================================================
   IMPORTANT TOTAL
   ====================================================== */

.po-report-table .po-total{

    font-weight:700;

    color:#0F4C81;

}


/* ======================================================
   STATUS
   ====================================================== */

.po-status{

    display:inline-block;

    padding:4px 9px;

    border-radius:20px;

    font-size:9px;

    font-weight:700;

    line-height:1;

    white-space:nowrap;

}


.po-status-open{

    background:#fff3cd;

    color:#9a6700;

}


.po-status-received{

    background:#d1e7dd;

    color:#0f5132;

}


.po-status-cancel{

    background:#f8d7da;

    color:#842029;

}


.po-status-default{

    background:#e2e3e5;

    color:#41464b;

}


/* ======================================================
   PAYMENT STATUS
   ====================================================== */

.po-payment-status{

    display:inline-block;

    padding:4px 9px;

    border-radius:20px;

    font-size:9px;

    font-weight:700;

    line-height:1;

    white-space:nowrap;

}


.po-payment-paid{

    background:#d1e7dd;

    color:#0f5132;

}


.po-payment-unpaid{

    background:#fff3cd;

    color:#9a6700;

}


.po-payment-partial{

    background:#cff4fc;

    color:#055160;

}


.po-payment-default{

    background:#e2e3e5;

    color:#41464b;

}


/* ======================================================
   REMARK
   ====================================================== */

.po-report-table .po-remark{

    max-width:180px;

    overflow:hidden;

    text-overflow:ellipsis;

    white-space:nowrap;

}


/* ======================================================
   EMPTY
   ====================================================== */

.po-empty{

    text-align:center;

    padding:45px !important;

    color:#94a3b8 !important;

}


/* ======================================================
   SCROLLBAR
   ====================================================== */

.po-table-scroll::-webkit-scrollbar{

    height:9px;

}


.po-table-scroll::-webkit-scrollbar-track{

    background:#f1f5f9;

}


.po-table-scroll::-webkit-scrollbar-thumb{

    background:#cbd5e1;

    border-radius:10px;

}


.po-table-scroll::-webkit-scrollbar-thumb:hover{

    background:#94a3b8;

}

</style>


<script>

const POReport = {

    state:{

        page:1,

        limit:20

    },


    init(){

        this.initSelect2();

        this.bind();

        this.setDefault();

        this.load();

    },


    /* ==================================================
       DEFAULT
       ================================================== */

    setDefault(){

        const now = new Date();

        const yyyy = now.getFullYear();

        const mm = String(
            now.getMonth() + 1
        ).padStart(2,'0');

        const dd = String(
            now.getDate()
        ).padStart(2,'0');


        const today =
            `${yyyy}-${mm}-${dd}`;

        const first =
            `${yyyy}-${mm}-01`;


        $('#filter_date_from')
            .val(first);

        $('#filter_date_to')
            .val(today);


        // IMPORTANT:
        // Plant default = ALL

        $('#filter_plant')
            .val('')
            .trigger('change.select2');

    },


    /* ==================================================
       SELECT2
       ================================================== */

    initSelect2(){

        $('#filter_plant').select2({

            width:'100%'

        });


        $('#filter_supplier').select2({

            width:'100%',

            placeholder:'Pilih Supplier',

            allowClear:true

        });

    },


    /* ==================================================
       BIND
       ================================================== */

    bind(){

        let timer;


        $('#filter_po').on(
            'keyup',
            ()=>{

                clearTimeout(timer);

                timer = setTimeout(()=>{

                    this.state.page = 1;

                    this.load();

                },300);

            }
        );


        $(
            '#filter_plant,' +
            '#filter_supplier,' +
            '#filter_date_from,' +
            '#filter_date_to'
        ).on(
            'change',
            ()=>{

                this.state.page = 1;

                this.load();

            }
        );


        $('#exportExcel').on(
            'click',
            (e)=>{

                e.preventDefault();

                window.open(

                    '<?= base_url("report-inventory/export_excel_po"); ?>?' +
                    this.query(),

                    '_blank'

                );

            }
        );


        $('#exportPDF').on(
            'click',
            (e)=>{

                e.preventDefault();

                window.open(

                    '<?= base_url("report-inventory/export_pdf_po"); ?>?' +
                    this.query(),

                    '_blank'

                );

            }
        );


        $(document).on(
            'click',
            '#pagination a',
            (e)=>{

                e.preventDefault();

                const page =
                    $(e.currentTarget)
                    .data('page');


                if(page){

                    this.state.page = page;

                    this.load();

                }

            }
        );

    },


    /* ==================================================
       QUERY
       ================================================== */

    query(){

        return $.param({

            page:
                this.state.page,

            limit:
                this.state.limit,

            plant:
                $('#filter_plant').val(),

            supplier:
                $('#filter_supplier').val(),

            po:
                $('#filter_po').val(),

            date_from:
                $('#filter_date_from').val(),

            date_to:
                $('#filter_date_to').val()

        });

    },


    /* ==================================================
       FORMAT
       ================================================== */

    money(value){

        return Number(
            value || 0
        ).toLocaleString(
            'id-ID'
        );

    },


    decimal(value){

        return Number(
            value || 0
        ).toLocaleString(
            'id-ID',
            {

                minimumFractionDigits:2,

                maximumFractionDigits:2

            }
        );

    },


    dateIndo(date){

        if(!date){

            return '-';

        }


        const value =
            String(date)
            .substring(0,10)
            .split('-');


        if(value.length !== 3){

            return date;

        }


        const bulan = [

            'JANUARI',

            'FEBRUARI',

            'MARET',

            'APRIL',

            'MEI',

            'JUNI',

            'JULI',

            'AGUSTUS',

            'SEPTEMBER',

            'OKTOBER',

            'NOVEMBER',

            'DESEMBER'

        ];


        return (

            value[2] +
            ' ' +
            bulan[
                Number(value[1]) - 1
            ] +
            ' ' +
            value[0]

        );

    },


    escape(value){

        if(
            value === null ||
            value === undefined
        ){

            return '-';

        }


        return String(value)

            .replace(
                /&/g,
                '&amp;'
            )

            .replace(
                /</g,
                '&lt;'
            )

            .replace(
                />/g,
                '&gt;'
            )

            .replace(
                /"/g,
                '&quot;'
            )

            .replace(
                /'/g,
                '&#039;'
            );

    },


    /* ==================================================
       STATUS
       ================================================== */

    renderStatus(status){

        const value =
            String(
                status || '-'
            );

        const upper =
            value.toUpperCase();


        let cls =
            'status-default';


        if(
            upper === 'OPEN'
        ){

            cls =
                'status-open';

        }
        else if(
            upper === 'RECEIVED' ||
            upper === 'CLOSED' ||
            upper === 'DONE' ||
            upper === 'COMPLETE'
        ){

            cls =
                'status-success';

        }
        else if(
            upper === 'CANCEL' ||
            upper === 'CANCELLED'
        ){

            cls =
                'status-danger';

        }


        return `

            <span
                class="po-status ${cls}"
            >

                ${this.escape(value)}

            </span>

        `;

    },


    /* ==================================================
       PAYMENT STATUS
       ================================================== */

    renderPaymentStatus(status){

        const value =
            String(
                status || '-'
            );

        const upper =
            value.toUpperCase();


        let cls =
            'payment-default';


        if(
            upper === 'PAID'
        ){

            cls =
                'payment-paid';

        }
        else if(
            upper === 'UNPAID'
        ){

            cls =
                'payment-unpaid';

        }
        else if(
            upper.includes('PART')
        ){

            cls =
                'payment-partial';

        }


        return `

            <span
                class="payment-status ${cls}"
            >

                ${this.escape(value)}

            </span>

        `;

    },


    /* ==================================================
       RENDER
       ================================================== */

    render(rows){

        const wrap =
            $('#poReportWrapper');

        wrap.empty();


        if(
            !rows ||
            !rows.length
        ){

            wrap.html(`

                <div class="po-report-card">

                    <div class="po-empty">

                        Data tidak ditemukan

                    </div>

                </div>

            `);

            return;

        }


        wrap.html(`

            <div class="po-report-card">

                <div class="po-table-scroll">

                    <table class="po-report-table">

                        <thead>

                            <!-- ==================================
                                GROUP HEADER
                                ================================== -->

                            <tr class="group-header">

                                <th colspan="9">
                                    PO INFORMATION
                                </th>

                                <th colspan="5">
                                    PO QUANTITY & VALUE
                                </th>

                                <th colspan="6">
                                    ACTUAL & RECEIVE
                                </th>

                                <th colspan="3">
                                    CLAIM & PAYABLE
                                </th>

                                <th colspan="3">
                                    PAYMENT
                                </th>

                                <th colspan="1">
                                    ADDITIONAL
                                </th>

                            </tr>


                            <!-- ==================================
                                COLUMN HEADER
                                ================================== -->

                            <tr class="column-header">


                                <!-- PO INFORMATION -->

                                <th>
                                    PO
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Plant
                                </th>

                                <th>
                                    PO Type
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Material
                                </th>

                                <th>
                                    Truck
                                </th>

                                <th>
                                    Driver
                                </th>

                                <th>
                                    Status
                                </th>


                                <!-- QUANTITY & VALUE -->

                                <th class="text-end">
                                    Qty
                                </th>

                                <th class="text-end">
                                    Weight
                                </th>

                                <th class="text-end">
                                    Avg BW
                                </th>

                                <th class="text-end">
                                    Price
                                </th>

                                <th class="text-end">
                                    Total
                                </th>


                                <!-- ACTUAL & RECEIVE -->

                                <th class="text-end">
                                    Dead Qty
                                </th>

                                <th class="text-end">
                                    Dead BW
                                </th>

                                <th class="text-end">
                                    Actual Weight
                                </th>

                                <th class="text-end">
                                    Shrink
                                </th>

                                <th class="text-end">
                                    Receive Qty
                                </th>

                                <th class="text-end">
                                    Receive BW
                                </th>


                                <!-- CLAIM -->

                                <th class="text-end">
                                    Claim Qty
                                </th>

                                <th class="text-end">
                                    Claim BW
                                </th>

                                <th class="text-end">
                                    Payable BW
                                </th>


                                <!-- PAYMENT -->

                                <th class="text-end">
                                    Paid Amount
                                </th>

                                <th class="text-end">
                                    Remaining
                                </th>

                                <th>
                                    Payment Status
                                </th>


                                <!-- ADDITIONAL -->

                                <th>
                                    Remark
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                            ${
                                rows.map((row)=>{


                                    /* ============================
                                    STATUS
                                    ============================ */

                                    const status =
                                        String(
                                            row.STATUS || '-'
                                        ).toUpperCase();


                                    let statusClass =
                                        'po-status-default';


                                    if(
                                        status === 'OPEN'
                                    ){

                                        statusClass =
                                            'po-status-open';

                                    }
                                    else if(
                                        status === 'RECEIVED' ||
                                        status === 'CLOSED' ||
                                        status === 'DONE' ||
                                        status === 'COMPLETE'
                                    ){

                                        statusClass =
                                            'po-status-received';

                                    }
                                    else if(
                                        status === 'CANCEL' ||
                                        status === 'CANCELLED'
                                    ){

                                        statusClass =
                                            'po-status-cancel';

                                    }


                                    /* ============================
                                    PAYMENT
                                    ============================ */

                                    const paymentStatus =
                                        String(
                                            row.PAYMENT_STATUS || '-'
                                        ).toUpperCase();


                                    let paymentClass =
                                        'po-payment-default';


                                    if(
                                        paymentStatus === 'PAID'
                                    ){

                                        paymentClass =
                                            'po-payment-paid';

                                    }
                                    else if(
                                        paymentStatus === 'UNPAID'
                                    ){

                                        paymentClass =
                                            'po-payment-unpaid';

                                    }
                                    else if(
                                        paymentStatus.includes('PART')
                                    ){

                                        paymentClass =
                                            'po-payment-partial';

                                    }


                                    return `

                                        <tr>


                                            <!-- =====================
                                                PO INFORMATION
                                                ===================== -->


                                            <td>

                                                <span
                                                    class="po-number"
                                                >

                                                    ${this.escape(
                                                        row.PO
                                                    )}

                                                </span>

                                            </td>


                                            <td>

                                                ${this.dateIndo(
                                                    row.PO_DATE
                                                )}

                                            </td>


                                            <td>

                                                ${this.escape(
                                                    row.PLANT_NAME ||
                                                    row.PLANT ||
                                                    '-'
                                                )}

                                            </td>


                                            <td>

                                                ${this.escape(
                                                    row.PO_NAME ||
                                                    row.PO_TYPE ||
                                                    '-'
                                                )}

                                            </td>


                                            <td>

                                                <div
                                                    class="po-text"
                                                    title="${this.escape(
                                                        row.SUPPLIER_NAME ||
                                                        row.SUPPLIER ||
                                                        '-'
                                                    )}"
                                                >

                                                    ${this.escape(
                                                        row.SUPPLIER_NAME ||
                                                        row.SUPPLIER ||
                                                        '-'
                                                    )}

                                                </div>

                                            </td>


                                            <td>

                                                <div
                                                    class="po-text"
                                                    title="${this.escape(
                                                        row.MATERIAL_NAME ||
                                                        row.MATERIAL ||
                                                        '-'
                                                    )}"
                                                >

                                                    ${this.escape(
                                                        row.MATERIAL_NAME ||
                                                        row.MATERIAL ||
                                                        '-'
                                                    )}

                                                </div>

                                            </td>


                                            <td>

                                                ${this.escape(
                                                    row.NO_TRUCK ||
                                                    '-'
                                                )}

                                            </td>


                                            <td>

                                                <div
                                                    class="po-text"
                                                    title="${this.escape(
                                                        row.DRIVER ||
                                                        '-'
                                                    )}"
                                                >

                                                    ${this.escape(
                                                        row.DRIVER ||
                                                        '-'
                                                    )}

                                                </div>

                                            </td>


                                            <td>

                                                <span
                                                    class="
                                                        po-status
                                                        ${statusClass}
                                                    "
                                                >

                                                    ${this.escape(
                                                        status
                                                    )}

                                                </span>

                                            </td>


                                            <!-- =====================
                                                QUANTITY & VALUE
                                                ===================== -->


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.JUMLAH
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.BERAT
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.AVG_BW
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                Rp
                                                ${this.money(
                                                    row.HARGA
                                                )}

                                            </td>


                                            <td
                                                class="
                                                    text-end
                                                    po-total
                                                "
                                            >

                                                Rp
                                                ${this.money(
                                                    row.TOTAL
                                                )}

                                            </td>


                                            <!-- =====================
                                                ACTUAL & RECEIVE
                                                ===================== -->


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.MATI_QTY
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.MATI_BW
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.ACTUAL_HASIL_TIMBANG
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.SUSUT_BW
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.TOTAL_TERIMA_QTY
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.TOTAL_TERIMA_BW
                                                )}

                                            </td>


                                            <!-- =====================
                                                CLAIM
                                                ===================== -->


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.CLAIM_QTY
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                ${this.decimal(
                                                    row.CLAIM_BW
                                                )}

                                            </td>


                                            <td
                                                class="
                                                    text-end
                                                    po-total
                                                "
                                            >

                                                ${this.decimal(
                                                    row.TOTAL_BAYAR_BW
                                                )}

                                            </td>


                                            <!-- =====================
                                                PAYMENT
                                                ===================== -->


                                            <td
                                                class="text-end"
                                            >

                                                Rp
                                                ${this.money(
                                                    row.PAID_AMOUNT
                                                )}

                                            </td>


                                            <td
                                                class="text-end"
                                            >

                                                Rp
                                                ${this.money(
                                                    row.REMAINING
                                                )}

                                            </td>


                                            <td>

                                                <span
                                                    class="
                                                        po-payment-status
                                                        ${paymentClass}
                                                    "
                                                >

                                                    ${this.escape(
                                                        paymentStatus
                                                    )}

                                                </span>

                                            </td>


                                            <!-- =====================
                                                ADDITIONAL
                                                ===================== -->


                                            <td>

                                                <div
                                                    class="po-remark"
                                                    title="${this.escape(
                                                        row.REMARK || '-'
                                                    )}"
                                                >

                                                    ${this.escape(
                                                        row.REMARK || '-'
                                                    )}

                                                </div>

                                            </td>


                                        </tr>

                                    `;

                                }).join('')

                            }


                        </tbody>

                    </table>

                </div>

            </div>

        `);

    },

    /* ==================================================
       LOAD
       ================================================== */

    load(){

        this.showLoading();


        $.get(

            '<?= base_url("report-inventory/load_data"); ?>',

            this.query(),

            (resp)=>{

                resp =
                    typeof resp === 'string'

                    ? JSON.parse(resp)

                    : resp;


                this.render(
                    resp.rows || []
                );


                $('#pagination')
                    .html(
                        resp.pagination || ''
                    );


                $('#pageInfo')
                    .html(`

                        Showing page
                        ${resp.page}
                        of
                        ${resp.pages}

                        (${this.money(
                            resp.total
                        )} PO)

                    `);

            }

        )

        .fail(()=>{

            $('#poReportBody')
                .html(`

                    <tr>

                        <td
                            colspan="27"
                            class="text-center text-danger py-5"
                        >

                            Gagal memuat data

                        </td>

                    </tr>

                `);

        })

        .always(()=>{

            this.hideLoading();

        });

    },


    /* ==================================================
       LOADING
       ================================================== */

    showLoading(){

        $('#poLoading')
            .removeClass('d-none');

    },


    hideLoading(){

        $('#poLoading')
            .addClass('d-none');

    }

};


$(function(){

    POReport.init();

});

</script>