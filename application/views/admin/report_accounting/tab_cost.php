<div class="row mb-4">

    <!-- TOTAL COST -->
    <div class="col-md-4">

        <div class="report-card bg-danger">

            <div class="report-card-title">

                TOTAL COST

            </div>

            <div
                class="report-card-value"
                id="summaryTotalCost">

                Rp 0

            </div>

        </div>

    </div>

    <!-- TOTAL ITEM -->
    <div class="col-md-4">

        <div class="report-card bg-success">

            <div class="report-card-title">

                TOTAL ITEM

            </div>

            <div
                class="report-card-value"
                id="summaryTotalItemCost">

                0

            </div>

        </div>

    </div>

    <!-- TOTAL DOC -->
    <div class="col-md-4">

        <div class="report-card bg-warning">

            <div class="report-card-title">

                TOTAL COST DOC

            </div>

            <div
                class="report-card-value"
                id="summaryTotalCostDoc">

                0

            </div>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- FILTER -->
<!-- ====================================================== -->

<div class="mb-4">

    <div class="row g-3">

        <!-- SEARCH -->
        <div class="col-md-4">

            <input
                type="text"
                id="costSearch"
                class="form-control"
                placeholder="Cari cost, slip, remark...">

        </div>

        <!-- PLANT -->
        <div class="col-md-2">

            <select
                id="costPlant"
                class="form-select">

                <option value="">

                    Semua Plant

                </option>

            </select>

        </div>

        <!-- PAYMENT -->
        <div class="col-md-2">

            <select
                id="costPembayaran"
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

        <!-- DATE FROM -->
        <div class="col-md-2">

            <input
                type="date"
                id="costDateFrom"
                class="form-control"
                value="<?= date('Y-m-01'); ?>">

        </div>

        <!-- DATE TO -->
        <div class="col-md-2">

            <input
                type="date"
                id="costDateTo"
                class="form-control"
                value="<?= date('Y-m-d'); ?>">

        </div>

        <div class="col-md-10"></div>

        <!-- EXPORT -->
        <div class="col-md-2">

            <button
                id="btnExportCost"
                class="btn btn-success w-100">

                Export Excel

            </button>

        </div>

    </div>

</div>

<!-- ====================================================== -->
<!-- RESULT -->
<!-- ====================================================== -->

<div id="costResult">

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

    <div id="costReportInfo"></div>

    <div id="costReportPagination"></div>

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

.cost-header{

    background:
        linear-gradient(
            135deg,
            #ff6b35,
            #ff8c42
        );

    border-radius: 24px 24px 0 0;

    position: relative;

    overflow: hidden;

    padding: 32px;

}

.cost-header::after{

    content: '';

    position: absolute;

    width: 280px;

    height: 280px;

    background: rgba(255,255,255,.08);

    border-radius: 50%;

    right: -100px;

    top: -80px;

}

.cost-info-table tr{
    border: none !important;
}

.cost-info-table tbody{
    border: none !important;
}

.cost-badge{

    padding: 10px 16px;

    border-radius: 999px;

    font-size: 12px;

    font-weight: 700;

    letter-spacing: .5px;

}

</style>

<script>

window.ReportCost = (function(){

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
        $('#costSearch').on('keyup', function(){

            loadData(1);

        });

        $('#costPlant').on('change', function(){

            loadData(1);

        });

        $('#costPembayaran').on('change', function(){

            loadData(1);

        });

        $('#costDateFrom').on('change', function(){

            loadData(1);

        });

        $('#costDateTo').on('change', function(){

            loadData(1);

        });

        $(document).on(

            'click',

            '#costReportPagination a',

            function(e){

                e.preventDefault();

                let page =
                    $(this)
                        .data('cost-pagination-page');

                if(!page){

                    return;

                }

                loadData(page);

            }

        );

        $('#btnExportCost').on(

            'click',

            function(){

                let params =
                    $.param({

                        search:
                            $('#costSearch').val(),

                        plant:
                            $('#costPlant').val(),

                        pembayaran:
                            $('#costPembayaran').val(),

                        date_from:
                            $('#costDateFrom').val(),

                        date_to:
                            $('#costDateTo').val()

                    });

                window.open(

                    '<?= base_url("report-accounting/export_excel_cost"); ?>?'
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

            '<?= base_url("report-accounting/load_cost"); ?>',

            {

                page       : state.page,

                limit      : state.limit,

                search     : $('#costSearch').val(),

                plant      : $('#costPlant').val(),

                supplier   : $('#costSupplier').val(),

                pembayaran : $('#costPembayaran').val(),

                date_from  : $('#costDateFrom').val(),

                date_to    : $('#costDateTo').val()

            },

            function(res){

                renderSummary(
                    res.summary
                );

                renderTable(
                    res.rows
                );

                $('#costReportPagination')
                    .html(
                        res.pagination
                    );

                $('#costReportInfo')
                    .html(

                        `Total :
                        ${res.total}
                        data`

                    );

            },

            'json'

        );
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    function renderSummary(summary)
    {
        $('#summaryTotalCost').html(

            'Rp ' +
            formatRupiah(
                summary.TOTAL_COST || 0
            )

        );

        $('#summaryTotalCostDoc').html(

            formatRupiah(
                summary.TOTAL_COST_DOC || 0
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
            $('#costResult');

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

                        Tidak ada data cost

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

            let subtotalQty    = 0;
            let subtotalWeight = 0;
            let subtotalTotal  = 0;

            (row.DETAILS || [])
                .forEach(function(d){

                    subtotalQty +=
                        Number(d.QTY || 0);

                    subtotalWeight +=
                        Number(d.BERAT || 0);

                    subtotalTotal +=
                        Number(d.TOTAL || 0);

                    detailRows += `

                        <tr>

                            <!-- TIPE COST -->
                            <td>

                                <div class="fw-semibold text-dark">

                                    ${d.COST_NAME || '-'}

                                </div>

                            </td>

                            <td class="text-end">

                                ${formatQty(d.QTY || 0)}

                            </td>

                            <!-- HARGA -->
                            <td class="text-end">

                                Rp ${formatRupiah(d.JUMLAH)}

                            </td>

                            <!-- TOTAL -->
                            <td class="text-end fw-bold text-danger">

                                Rp ${formatRupiah(d.TOTAL)}

                            </td>

                            <!-- REMARK -->
                            <td>

                                ${d.REMARK || '-'}

                            </td>

                        </tr>

                    `;
                });

            let card = `

                <div class="card border-0 shadow-sm mb-4 overflow-hidden">

                    <!-- HEADER -->
                    <div class="cost-header p-4">

                        <div class="d-flex justify-content-between">

                            <h2 class="cashin-title">

                                #${row.COST}

                            </h2>

                            <span class="badge cashin-badge">

                                ${row.PEMBAYARAN || '-'}

                            </span>

                        </div>

                        <div class="row">

                            <!-- LEFT -->
                            <div class="col-md-6">

                                <table class="table table-borderless text-white mb-0 cashin-info-table">

                                    <tr>
                                        <td width="160">
                                            <b>PLANT</b>
                                        </td>
                                        <td>
                                            :
                                            ${row.PLANT_NAME || '-'}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>PAYMENT</b>
                                        </td>
                                        <td>
                                            :
                                            ${row.PEMBAYARAN || '-'}
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

                            <!-- RIGHT -->
                            <div class="col-md-6">

                                <table class="table table-borderless text-white mb-0 cashin-info-table">

                                    <tr>
                                        <td width="160">
                                            <b>COST DATE</b>
                                        </td>
                                        <td>
                                            :
                                            ${formatDate(row.COST_DATE)}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>TOTAL ITEM</b>
                                        </td>
                                        <td>
                                            :
                                            ${row.TOTAL_ITEM || 0}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <b>GRAND TOTAL</b>
                                        </td>
                                        <td class="fw-bold">
                                            :
                                            Rp ${formatRupiah(row.GRAND_TOTAL)}
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

                                    <th width="35%">
                                        Tipe Cost
                                    </th>

                                    <th class="text-end" width="5%">
                                        QTY
                                    </th>

                                    <th class="text-end" width="15%">
                                        Harga
                                    </th>

                                    <th class="text-end" width="15%">
                                        Total
                                    </th>

                                    <th width="25%">
                                        Remark
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                ${detailRows}

                                <tr class="table-light fw-bold">

                                    <td>

                                        SUBTOTAL

                                    </td>

                                    <td></td>
                                    <td></td>

                                    <td class="text-end text-danger">

                                        Rp ${formatRupiah(subtotalTotal)}

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
    | PLANT
    |--------------------------------------------------------------------------
    */

    function loadPlant()
    {
        $.get(

            '<?= base_url("payment/get_plant"); ?>',

            function(rows){

                rows.forEach(function(r){

                    $('#costPlant').append(`

                        <option value="${r.id}">

                            ${r.text}

                        </option>

                    `);

                });

            },

            'json'

        );
    }

    return {

        init : init,

        loadData : loadData

    };

})();

$(document).ready(function(){

    if(window.ReportCost){

        ReportCost.init();

    }

});

</script>