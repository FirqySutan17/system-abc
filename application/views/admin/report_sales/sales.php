<?php $userPlant = $this->session->userdata('plant'); ?>

<div class="sales-report-wrap">

    <!-- FILTER -->
    <div class="report-filter-card">

        <div class="row g-3 align-items-end">

            <!-- PLANT -->
            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Plant
                </label>

                <select
                    id="sl_filter_plant"
                    class="form-control">

                    <option value="">
                        Choose Plant
                    </option>

                    <?php foreach ($plants as $p): ?>

                        <?php if ($p->CODE != '*'): ?>

                            <option value="<?= $p->CODE ?>">
                                <?= $p->CODE_NAME ?>
                            </option>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- CUSTOMER -->
            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Customer
                </label>

                <select
                    id="sl_filter_customer"
                    class="form-control">

                    <option value="">
                        Choose Customer
                    </option>

                    <?php foreach ($customers as $c): ?>

                        <option value="<?= $c->CUST ?>">
                            <?= $c->CUST ?>
                            -
                            <?= $c->FULL_NAME ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- SALES -->
            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Sales / Nota
                </label>

                <input
                    type="text"
                    id="sl_filter_sales"
                    class="form-control"
                    placeholder="Search Sales / Nota..."
                >

            </div>

            <!-- DATE FROM -->
            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Date From
                </label>

                <input
                    type="date"
                    id="sl_date_from"
                    class="form-control">

            </div>

            <!-- DATE TO -->
            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Date To
                </label>

                <input
                    type="date"
                    id="sl_date_to"
                    class="form-control">

            </div>

            <!-- STATUS -->
            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Status
                </label>

                <select
                    id="sl_filter_status"
                    class="form-control">

                    <option value="">
                        All Status
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

        </div>

    </div>

    <!-- LOADING -->
    <div id="slLoading" class="report-loading d-none">

        <div class="text-center">

            <div class="spinner-border text-primary"></div>

            <div class="fw-semibold mt-3">
                Loading report...
            </div>

            <small class="text-muted">
                Please wait a moment
            </small>

        </div>

    </div>

    <!-- CONTENT -->
    <div id="salesReportWrapper"></div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-between align-items-center mt-4">

        <div
            id="sl_pageInfo"
            class="text-muted small">
        </div>

        <div id="sl_pagination"></div>

    </div>

</div>

<style>
.sales-report-wrap{
    padding:4px;
}

.report-filter-card{
    background:#fff;
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 25px rgba(15,23,42,.05);
    margin-bottom:24px;
}

.report-loading{
    min-height:280px;
    background:#fff;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    border:1px solid #edf2f7;
}

.sales-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    margin-bottom:24px;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
    border:1px solid #edf2f7;
}

.sales-head{
    background:#0f4c81;
    color:#fff;
    padding:22px 24px;
}

.sales-title{
    font-size:22px;
    font-weight:700;
    letter-spacing:.5px;
}

.status-badge{
    padding:7px 14px;
    border-radius:50px;
    font-size:12px;
    font-weight:700;
}

.status-open{
    background:#fff3cd;
    color:#856404;
}

.status-partial{
    background:#dbeafe;
    color:#1d4ed8;
}

.status-paid{
    background:#d1fae5;
    color:#065f46;
}

.meta-grid{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:10px 40px;

    margin-top:18px;

    font-size:14px;
}

.meta-item{
    display:flex;
    gap:8px;
}

.meta-label{
    min-width:110px;
    opacity:.85;
    font-weight:600;
}

.meta-value{

    flex:1;

    font-weight:500;

    color:#fff;
}

.sales-body{

    padding:0;
}

.attach-badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:50px;
    font-size:12px;
    font-weight:700;
    background:#e0f2fe;
    color:#075985;
}

.table-detail{
    margin:0;
    font-size:14px;
}

.table-detail thead th{
    background:#f8fafc;
    border-color:#e5e7eb;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.4px;
}

.table-detail td{
    border-color:#edf2f7;
    vertical-align:middle;
}

.subtotal-row{
    background:#f8fafc;
    font-weight:700;
}

@media(max-width:768px){

    .meta-grid{
        grid-template-columns:1fr;
        gap:8px;
    }

    .sales-title{
        font-size:18px;
    }

    .sales-head,
    .sales-body{
        padding:18px;
    }
}
</style>

<style>

    /*
    |--------------------------------------------------------------------------
    | FILTER AREA
    |--------------------------------------------------------------------------
    */

    .sales-filter-card{

        border: 0;

        border-radius: 18px;

        background: #fff;

        box-shadow:
            0 4px 18px rgba(0,0,0,.05);

        margin-bottom: 20px;
    }

    .sales-filter-header{

        padding: 18px 22px;

        border-bottom:
            1px solid #edf1f7;

        font-size: 18px;

        font-weight: 700;

        color: #2d3748;
    }

    .sales-filter-body{

        padding: 22px;
    }

    /*
    |--------------------------------------------------------------------------
    | REPORT CARD
    |--------------------------------------------------------------------------
    */

    .sales-card{

        border-radius: 18px;

        overflow: hidden;

        background: #fff;

        margin-bottom: 24px;

        box-shadow:
            0 5px 18px rgba(0,0,0,.06);

        border:
            1px solid #eef2f7;
    }

    /*
    |--------------------------------------------------------------------------
    | HEAD
    |--------------------------------------------------------------------------
    */

    .sales-head{

        padding: 22px;

        background:
            linear-gradient(
                135deg,
                #0d6efd 0%,
                #3f8cff 100%
            );

        color: #fff;
    }

    .sales-title{

        font-size: 22px;

        font-weight: 700;

        letter-spacing: .5px;
    }

    /*
    |--------------------------------------------------------------------------
    | META
    |--------------------------------------------------------------------------
    */

    .meta-grid{

        display:grid;

        grid-template-columns:1fr 1fr;

        gap:10px 40px;

        margin-top:18px;

        font-size:14px;
    }

    .meta-item{

        display:flex;

        gap:8px;

        background:none;

        border:0;

        padding:0;
    }

    .meta-label{

        min-width:110px;

        opacity:.85;

        font-weight:600;

        color:#fff;
    }

    .meta-value{

        font-size: 14px;

        font-weight: 600;

        word-break: break-word;
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    .status-badge{

        padding:7px 14px;

        border-radius:50px;

        font-size:12px;

        font-weight:700;
    }

    .status-open{

        background:
            linear-gradient(
                135deg,
                #f59e0b,
                #fbbf24
            );
    }

    .status-partial{

        background:
            linear-gradient(
                135deg,
                #0ea5e9,
                #38bdf8
            );
    }

    .status-paid{

        background:
            linear-gradient(
                135deg,
                #10b981,
                #34d399
            );
    }

    /*
    |--------------------------------------------------------------------------
    | BODY
    |--------------------------------------------------------------------------
    */

    .sales-body{

        padding: 20px;
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    .table-detail{

        margin-bottom: 0;
    }

    .table-detail thead th{

        background:#f1f5f9;

        border-color:#e5e7eb;

        font-size:12px;

        text-transform:uppercase;

        letter-spacing:.4px;
    }

    .table-detail tbody td{

        padding: 12px 10px;

        vertical-align: middle;

        border-color: #eef2f7;
    }

    .table-detail tbody tr:hover{

        background: #fafcff;
    }

    /*
    |--------------------------------------------------------------------------
    | SUBTOTAL
    |--------------------------------------------------------------------------
    */

    .subtotal-row td{

        background: #f8fafc;

        font-size: 14px;

        font-weight: 700;

        border-top:
            2px solid #dbe4f0;
    }

    /*
    |--------------------------------------------------------------------------
    | ATTACHMENT
    |--------------------------------------------------------------------------
    */

    .attach-badge{

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding: 6px 12px;

        border-radius: 999px;

        background: rgba(255,255,255,.18);

        border:
            1px solid rgba(255,255,255,.18);

        color: #fff;

        font-size: 12px;

        font-weight: 700;

        transition: .2s ease;
    }

    .attach-badge:hover{

        background: rgba(255,255,255,.28);

        color: #fff;
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    .pagination{

        gap: 6px;
    }

    .pagination .page-link{

        border: 0;

        border-radius: 10px;

        color: #334155;

        padding:
            8px 14px;

        font-weight: 600;

        box-shadow:
            0 2px 8px rgba(0,0,0,.05);
    }

    .pagination .active .page-link{

        background: #0d6efd;

        color: #fff;
    }

    /*
    |--------------------------------------------------------------------------
    | LOADING
    |--------------------------------------------------------------------------
    */

    .sales-loading{

        min-height: 300px;

        display: flex;

        align-items: center;

        justify-content: center;
    }

    .loading-card{

        text-align: center;

        background: #fff;

        border-radius: 20px;

        padding: 30px 40px;

        box-shadow:
            0 6px 24px rgba(0,0,0,.08);
    }

    /*
    |--------------------------------------------------------------------------
    | MOBILE
    |--------------------------------------------------------------------------
    */

    @media(max-width:768px){

        .sales-head{

            padding: 18px;
        }

        .sales-title{

            font-size: 18px;
        }

        .meta-grid{

            grid-template-columns: 1fr;
        }

        .table-detail{

            min-width: 800px;
        }
    }

</style>

<script>

    let sl_state = {

        page: 1,

        limit: 10,

        search: '',

        plant: '',

        customer: '',

        status: '',

        date_from: '',

        date_to: ''
    };

    let sl_ajax = null;

    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    $(function(){

        /*
        |--------------------------------------------------------------------------
        | DEFAULT DATE
        |--------------------------------------------------------------------------
        */

        let today = new Date();

        let firstDay = new Date(
            today.getFullYear(),
            today.getMonth(),
            1
        );

        $('#sl_date_from').val(
            firstDay.toISOString().split('T')[0]
        );

        $('#sl_date_to').val(
            today.toISOString().split('T')[0]
        );

        /*
        |--------------------------------------------------------------------------
        | SELECT2
        |--------------------------------------------------------------------------
        */

        $('#sl_filter_plant').select2({
            theme:'bootstrap-5',
            width:'100%'
        });

        $('#sl_filter_customer').select2({
            theme:'bootstrap-5',
            width:'100%'
        });

        /*
        |--------------------------------------------------------------------------
        | LOAD
        |--------------------------------------------------------------------------
        */

        loadSalesReport();

    });

    /*
    |--------------------------------------------------------------------------
    | FILTER CHANGE
    |--------------------------------------------------------------------------
    */

    $('#sl_filter_plant').on('change', function(){

        sl_state.plant = $(this).val();

        sl_state.page = 1;

        loadSalesReport();

    });

    $('#sl_filter_customer').on('change', function(){

        sl_state.customer = $(this).val();

        sl_state.page = 1;

        loadSalesReport();

    });

    $('#sl_filter_status').on('change', function(){

        sl_state.status = $(this).val();

        sl_state.page = 1;

        loadSalesReport();

    });

    $('#sl_date_from').on('change', function(){

        sl_state.date_from = $(this).val();

        sl_state.page = 1;

        loadSalesReport();

    });

    $('#sl_date_to').on('change', function(){

        sl_state.date_to = $(this).val();

        sl_state.page = 1;

        loadSalesReport();

    });

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    let searchTimer;

    $('#sl_filter_sales').on('keyup', function(){

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function(){

            sl_state.search =
                $('#sl_filter_sales').val();

            sl_state.page = 1;

            loadSalesReport();

        }, 400);

    });

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.sl-page-link',
        function(e){

            e.preventDefault();

            let page = $(this).data('page');

            if(!page){
                return;
            }

            sl_state.page = page;

            loadSalesReport();

        }
    );

    /*
    |--------------------------------------------------------------------------
    | LOAD REPORT
    |--------------------------------------------------------------------------
    */

    function loadSalesReport(){

        if(sl_ajax){
            sl_ajax.abort();
        }

        $('#slLoading').removeClass('d-none');

        $('#salesReportWrapper').html('');

        sl_ajax = $.ajax({

            url:
                '<?= base_url("report-sales/load_sales"); ?>',

            type:'GET',

            data: sl_state,

            dataType:'json',

            success:function(resp){

                renderSalesReport(resp);

            },

            complete:function(){

                $('#slLoading')
                    .addClass('d-none');

                sl_ajax = null;

            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    function renderSalesReport(resp){

        let html = '';

        if(!resp.rows.length){

            html = `

                <div class="alert alert-warning">

                    Data sales tidak ditemukan

                </div>

            `;

            $('#salesReportWrapper').html(html);

            return;
        }

        resp.rows.forEach(function(row){

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            let statusClass = 'status-open';

            if(row.STATUS === 'PARTIAL'){
                statusClass = 'status-partial';
            }

            if(row.STATUS === 'PAID'){
                statusClass = 'status-paid';
            }

            /*
            |--------------------------------------------------------------------------
            | DETAIL ROWS
            |--------------------------------------------------------------------------
            */

            let detailHtml = '';

            if(row.DETAILS){

                row.DETAILS.forEach(function(d, i){

                    detailHtml += `

                        <tr>

                            <td class="text-center">

                                ${i + 1}

                            </td>

                            <td>

                                <div class="fw-semibold">

                                    ${d.MATERIAL_NAME || '-'}

                                </div>

                            </td>

                            <td class="text-end">

                                ${formatDecimalID(
                                    d.JUMLAH || 0
                                )}

                            </td>

                            <td class="text-end">

                                ${formatDecimalID(
                                    d.BERAT || 0
                                )}

                            </td>

                            <td class="text-end">

                                ${formatRupiah(
                                    d.HARGA || 0
                                )}

                            </td>

                            <td class="text-end fw-bold text-success">

                                ${formatRupiah(
                                    d.TOTAL || 0
                                )}

                            </td>

                        </tr>

                    `;
                });
            }

            /*
            |--------------------------------------------------------------------------
            | ATTACHMENT
            |--------------------------------------------------------------------------
            */

            let attachment = '-';

            if(row.ATTACHMENT_PATH){

                attachment = `

                    <a
                        href="<?= base_url(); ?>${row.ATTACHMENT_PATH}"
                        target="_blank"
                        class="attach-badge text-decoration-none">

                        VIEW FILE

                    </a>

                `;
            }

            /*
            |--------------------------------------------------------------------------
            | CARD
            |--------------------------------------------------------------------------
            */

            html += `

                <div class="sales-card">

                    <!-- HEADER -->
                    <div class="sales-head">

                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

                            <div>

                                <div class="sales-title">

                                    SALES #${row.SALES}

                                </div>

                                <div class="mt-1 opacity-75">

                                    ${row.PLANT_NAME || '-'}

                                </div>

                            </div>

                            <div>

                                <span class="status-badge ${statusClass}">

                                    ${row.STATUS}

                                </span>

                            </div>

                        </div>

                        <div class="meta-grid">

                            <div class="meta-item">

                                <div class="meta-label">
                                    Customer
                                </div>

                                <div class="meta-value">

                                    ${row.CUSTOMER_NAME || '-'}
                                    (${row.CUSTOMER || '-'})

                                </div>

                            </div>

                            <div class="meta-item">

                                <div class="meta-label">
                                    Date
                                </div>

                                <div class="meta-value">

                                    ${formatTanggalIndo(
                                        row.SALES_DATE
                                    )}

                                </div>

                            </div>

                            <div class="meta-item">

                                <div class="meta-label">
                                    Payment
                                </div>

                                <div class="meta-value">

                                    ${row.PEMBAYARAN || '-'}
                                    /
                                    ${row.JENIS_PAY || '-'}

                                </div>

                            </div>

                            <div class="meta-item">

                                <div class="meta-label">
                                    Nota
                                </div>

                                <div class="meta-value">

                                    ${row.NOTA || '-'}

                                </div>

                            </div>

                            <div class="meta-item">

                                <div class="meta-label">
                                    Attachment
                                </div>

                                <div class="meta-value">

                                    ${attachment}

                                </div>

                            </div>

                            <div class="meta-item">

                                <div class="meta-label">
                                    Remark
                                </div>

                                <div class="meta-value">

                                    ${row.REMARK || '-'}

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- BODY -->
                    <div class="sales-body">

                        <div class="table-responsive">

                            <table class="table table-detail">

                                <thead>

                                    <tr>

                                        <th width="5%">
                                            #
                                        </th>

                                        <th>
                                            Material
                                        </th>

                                        <th width="12%" class="text-end">
                                            Qty
                                        </th>

                                        <th width="12%" class="text-end">
                                            Berat
                                        </th>

                                        <th width="15%" class="text-end">
                                            Harga
                                        </th>

                                        <th width="18%" class="text-end">
                                            Total
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    ${detailHtml}

                                    <tr class="subtotal-row">

                                        <td colspan="5" class="text-end">

                                            GRAND TOTAL

                                        </td>

                                        <td class="text-end text-success">

                                            ${formatRupiah(
                                                row.AMOUNT || 0
                                            )}

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            `;
        });

        $('#salesReportWrapper').html(html);

        $('#sl_pagination').html(
            resp.pagination
        );

        $('#sl_pageInfo').html(

            `Menampilkan
            ${resp.start}
            -
            ${resp.end}
            dari
            ${resp.total}
            data`

        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT
    |--------------------------------------------------------------------------
    */

    function formatRupiah(value){

        value = parseFloat(value || 0);

        if(isNaN(value)){
            value = 0;
        }

        value = Math.round(value)
            .toString();

        return value.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            '.'
        );
    }

    function formatDecimalID(value){

        value = parseFloat(value || 0);

        if(isNaN(value)){
            value = 0;
        }

        return value.toLocaleString(
            'id-ID',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );
    }

    function formatTanggalIndo(dateStr){

        if(!dateStr){
            return '-';
        }

        let d = new Date(dateStr);

        return d.toLocaleDateString(
            'id-ID',
            {
                day:'2-digit',
                month:'short',
                year:'numeric'
            }
        );
    }

</script>