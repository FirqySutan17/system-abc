<div class="row mb-4">

    <!-- TOTAL SALES -->
    <div class="col-md-4">

        <div class="report-card bg-primary">

            <div class="report-card-title">

                TOTAL SALES

            </div>

            <div
                class="report-card-value"
                id="summaryTotalSales">

                Rp 0

            </div>

        </div>

    </div>

    <!-- TOTAL DOC -->
    <div class="col-md-4">

        <div class="report-card bg-warning">

            <div class="report-card-title">

                TOTAL SALES DOC

            </div>

            <div
                class="report-card-value"
                id="summaryTotalDocSales">

                0

            </div>

        </div>

    </div>

    <!-- TOTAL CUSTOMER -->
    <div class="col-md-4">

        <div class="report-card bg-danger">

            <div class="report-card-title">

                TOTAL CUSTOMER

            </div>

            <div
                class="report-card-value"
                id="summaryTotalCustomerSales">

                0

            </div>

        </div>

    </div>

</div>

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

            <button
                id="btnExportSales"
                class="btn btn-success w-100">

                Export Excel

            </button>

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

            let thead = `
                <thead>
                    <tr>
                        <th class="text-center">Sales</th>
                        <th class="text-center">Plant</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Payment</th>
                        <th class="text-center">Nota</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Sisa</th>
                        <th>Material</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Berat</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Total</th>
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
                                    <td colspan="15" class="text-center text-muted py-4">
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
            | LOOP
            |--------------------------------------------------------------------------
            */

            let totalGrandTotal = 0;
            let totalRemain     = 0;
            let totalQty        = 0;
            let totalBerat      = 0;
            let totalDetailAmt  = 0;

            let rowsHtml = rows.map(function(row){

                let details  = row.DETAILS || [];
                let rowCount = Math.max(details.length, 1);

                totalGrandTotal += Number(row.GRAND_TOTAL || 0);
                totalRemain     += Number(row.REMAIN      || 0);

                /*
                |--------------------------------------------------------------------------
                | STATUS BADGE
                |--------------------------------------------------------------------------
                */

                let statusBadge = '<span class="badge bg-warning text-dark">OPEN</span>';

                if(row.STATUS === 'PAID'){
                    statusBadge = '<span class="badge bg-success">PAID</span>';
                } else if(row.STATUS === 'PARTIAL'){
                    statusBadge = '<span class="badge bg-info">PARTIAL</span>';
                }

                /*
                |--------------------------------------------------------------------------
                | KOLOM HEADER (rowspan)
                |--------------------------------------------------------------------------
                */

                let headerCols = `
                    <td class="text-center align-middle fw-bold text-primary" rowspan="${rowCount}">
                        #${row.SALES || '-'}
                    </td>
                    <td class="text-center align-middle" rowspan="${rowCount}">
                        ${row.PLANT_NAME || '-'}
                    </td>
                    <td class="text-center align-middle" rowspan="${rowCount}">
                        ${formatDate(row.SALES_DATE)}
                    </td>
                    <td class="align-middle" rowspan="${rowCount}">
                        <div class="fw-semibold">${row.CUSTOMER_NAME || '-'}</div>
                        <small class="text-muted">${row.CUSTOMER || ''}</small>
                    </td>
                    <td class="text-center align-middle" rowspan="${rowCount}">
                        <span class="badge bg-secondary">${row.PEMBAYARAN || '-'}</span>
                    </td>
                    <td class="text-center align-middle" rowspan="${rowCount}">
                        ${row.NOTA || '-'}
                    </td>
                    <td class="text-center align-middle" rowspan="${rowCount}">
                        ${statusBadge}
                    </td>
                    <td class="text-end align-middle fw-bold" rowspan="${rowCount}">
                        Rp ${formatRupiah(row.GRAND_TOTAL || 0)}
                    </td>
                    <td class="text-end align-middle" rowspan="${rowCount}">
                        Rp ${formatRupiah(row.REMAIN || 0)}
                    </td>
                `;

                let html = '';

                if(details.length === 0){

                    html += `
                        <tr>
                            ${headerCols}
                            <td>-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                    `;

                } else {

                    details.forEach(function(d, idx){

                        totalQty       += Number(d.JUMLAH   || 0);
                        totalBerat     += Number(d.BERAT    || 0);
                        totalDetailAmt += Number(d.TOTAL    || 0);

                        html += `
                            <tr>
                                ${idx === 0 ? headerCols : ''}
                                <td class="fw-semibold">${d.MATERIAL_NAME || '-'}</td>
                                <td class="text-end">${formatQty(d.JUMLAH || 0)}</td>
                                <td class="text-end">${formatWeight(d.BERAT || 0)}</td>
                                <td class="text-end">Rp ${formatRupiah(d.HARGA || 0)}</td>
                                <td class="text-end text-danger">Rp ${formatRupiah(d.DISCOUNT || 0)}</td>
                                <td class="text-end fw-semibold text-primary">Rp ${formatRupiah(d.TOTAL || 0)}</td>
                            </tr>
                        `;
                    });
                }

                return html;

            }).join('');

            let table = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-modern">
                        ${thead}
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="7" class="text-end">Total Semua</td>
                                <td class="text-end">Rp ${formatRupiah(totalGrandTotal)}</td>
                                <td class="text-end">Rp ${formatRupiah(totalRemain)}</td>
                                <td></td>
                                <td class="text-end">${formatQty(totalQty)}</td>
                                <td class="text-end">${formatWeight(totalBerat)}</td>
                                <td></td>
                                <td></td>
                                <td class="text-end">Rp ${formatRupiah(totalDetailAmt)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;

            wrapper.html(table);
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

                    loadData(page);

                }

            );

            /*
            |--------------------------------------------------------------------------
            | EXPORT
            |--------------------------------------------------------------------------
            */

            $('#btnExportSales').on(

                'click',

                function(){

                    let params =
                        $.param({

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

                    window.open(

                        '<?= base_url("report-sales/export_excel_sales"); ?>?'
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