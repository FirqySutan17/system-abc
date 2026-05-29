<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
if (!is_array($userPlants)) {
    $userPlants = [$this->session->userdata('plant')];
}
?>

<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Total Material</div>
                <div class="fs-4 fw-bold kpi-value text-primary" id="kpi_total_material">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Beginning Amount</div>
                <div class="fs-4 fw-bold kpi-value text-primary" id="kpi_begin_amount">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Incoming Amount</div>
                <div class="fs-4 fw-bold kpi-value text-info" id="kpi_in_amount">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Ending Amount</div>
                <div class="fs-4 fw-bold kpi-value text-success" id="kpi_end_amount">0</div>
            </div>
        </div>
    </div>

</div>

<div class="card mb-4" style="background: transparent; border: none !important; box-shadow: none !important">

    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Plant
            </label>

            <select id="mc_filter_plant" class="form-select">
                <?php foreach($plants as $i => $p): ?>
                    <option
                        value="<?= $p->CODE ?>"
                        <?= $i == 0 ? 'selected' : '' ?>>
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Closing Month
            </label>

            <input
                type="month"
                id="mc_month"
                class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Material
            </label>

            <input
                type="text"
                id="mc_filter_material"
                class="form-control"
                placeholder="Material Code / Name">
        </div>

        <div class="col-md-3 d-flex align-items-end" style="justify-content: flex-end;">

            <button
                class="btn btn-primary me-2"
                id="mc_btnFilter">

                Search

            </button>

            <div class="btn-group">

                <button
                    class="btn btn-success"
                    id="exportExcel">

                    Excel

                </button>

                <button
                    class="btn btn-danger"
                    id="exportPDF">

                    PDF

                </button>

            </div>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm">
    <div id="loadingOverlay">
        Loading Inventory Report...
    </div>

    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 10px">

        <h6 class="mb-0 fw-bold">
            Monthly Inventory Price Closing
        </h6>

    </div>

    <div class="card-body pt-2 px-3 pb-3">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover mb-0"
                id="monthlyCostTable">

                <thead>

                <tr class="table-dark">

                    <th rowspan="2" class="align-middle">
                        Material
                    </th>

                    <th colspan="3" class="text-center">
                        Beginning
                    </th>

                    <th colspan="3" class="text-center">
                        Incoming
                    </th>

                    <th colspan="3" class="text-center">
                        Outgoing
                    </th>

                    <th colspan="3" class="text-center">
                        Ending
                    </th>

                </tr>

                <tr class="table-secondary">

                    <th>Qty</th>
                    <th>BW</th>
                    <th>Amount</th>

                    <th>Qty</th>
                    <th>BW</th>
                    <th>Amount</th>

                    <th>Qty</th>
                    <th>BW</th>
                    <th>Amount</th>

                    <th>Qty</th>
                    <th>BW</th>
                    <th>Amount</th>

                </tr>

                </thead>

                <tbody></tbody>

                <tfoot>

                <tr class="table-warning fw-bold">

                    <td class="text-end">
                        GRAND TOTAL
                    </td>

                    <td class="text-end" id="mc_gt_bg_qty">0.00</td>
                    <td class="text-end" id="mc_gt_bg_bw">0.00</td>
                    <td class="text-end" id="mc_gt_bg_amount">0</td>

                    <td class="text-end" id="mc_gt_in_qty">0.00</td>
                    <td class="text-end" id="mc_gt_in_bw">0.00</td>
                    <td class="text-end" id="mc_gt_in_amount">0</td>

                    <td class="text-end" id="mc_gt_out_qty">0.00</td>
                    <td class="text-end" id="mc_gt_out_bw">0.00</td>
                    <td class="text-end" id="mc_gt_out_amount">0</td>

                    <td class="text-end" id="mc_gt_end_qty">0.00</td>
                    <td class="text-end" id="mc_gt_end_bw">0.00</td>
                    <td class="text-end" id="mc_gt_end_amount">0</td>

                </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <div></div>

    <div id="mc_pagination"></div>

</div>

<style>

    .card{
        border-radius:14px;
    }

    .table-responsive{
        max-height:700px;
    }

    thead th{
        position:sticky;
        top:0;
        z-index:10;
    }

    tfoot tr{
        position:sticky;
        bottom:0;
        z-index:10;
    }

    .table td{
        vertical-align:middle;
    }

    .table-dark th{
        background:#2f3c4f !important;
        color:#fff !important;
        border-color:#3f4d63 !important;
    }

    .px-3 {
        padding-right: 0rem !important;
        padding-left: 0rem !important;
    }

    .summary-sticky{
        position:sticky;
        top:70px;
        z-index:100;
    }

    #mc_gt_end_amount{
        color:#198754;
        font-size:16px;
    }

    .ending-cell{
        background:#f5f9ff;
        color:#0d6efd;
        font-weight:600;
    }

    tfoot tr{
        background:#f4f7fb !important;
        color:#2f3c4f !important;
    }

    tfoot td{
        border-top:2px solid #2f3c4f !important;
    }

    #monthlyCostTable td:first-child,
    #monthlyCostTable th:first-child{
        min-width:250px;
    }

    #monthlyCostTable thead tr:nth-child(2) th{
        background:#eef2f7 !important;
        color:#495057 !important;
        text-align:center;
        font-weight:600;
        font-size:13px;
    }

    #monthlyCostTable th:first-child,
    #monthlyCostTable td:first-child{
        position:sticky;
        left:0;
        z-index:5;
        background:white;
    }

    #monthlyCostTable tbody tr:hover{
        background:#f8fafc;
    }

    #monthlyCostTable thead th:first-child{
        z-index:20;
        background:#dc3545;
        color:white;
    }

    #monthlyCostTable{
        border-color:#e9ecef;
    }

    #monthlyCostTable td,
    #monthlyCostTable th{
        border-color:#edf0f2;
    }

    #loadingOverlay{
        display:none;
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(255,255,255,.8);
        z-index:999;
        text-align:center;
        padding-top:150px;
        font-size:18px;
        font-weight:600;
    }

    .card{
        position:relative;
    }

    .form-label{
        margin-bottom:4px;
        font-size:13px;
    }

    .form-control,
    .form-select{
        height:40px;
    }

    .kpi-card .card-body{
        padding:18px 22px;
    }

    .kpi-value{
        font-size:28px;
        font-weight:700;
        margin-top:4px;
    }

    .kpi-label{
        font-size:12px;
        color:#6c757d;
        text-transform:uppercase;
        letter-spacing:1px;
    }

    tfoot tr{
        background:#eef4ff !important;
        font-weight:700;
    }

    tfoot td{
        border-top:3px solid #2f3c4f !important;
    }

    @media(max-width:768px){

        .table{
            font-size:12px;
        }

        .fs-3{
            font-size:1.5rem !important;
        }

    }

</style>

<script>

    window.MonthlyClosingInventoryPriceReport = {

        loaded : false,
        page   : 1,

        init() {

            if (this.loaded) return;

            this.loaded = true;

            this.initFilter();
            this.bindEvent();
            this.load();
        },

        initFilter() {

            $('#mc_filter_plant').select2({
                width : '100%'
            });

            const now = new Date();

            const currentMonth =
                now.getFullYear() +
                '-' +
                String(now.getMonth() + 1).padStart(2,'0');

            $('#mc_month').val(currentMonth);
        },

        bindEvent() {

            let timer;

            $('#mc_btnFilter').on('click', () => {

                this.page = 1;
                this.load();

            });

            $('#mc_filter_material').on('keyup', () => {

                clearTimeout(timer);

                timer = setTimeout(() => {

                    this.page = 1;
                    this.load();

                }, 500);

            });

            $('#mc_filter_plant, #mc_month').on('change', () => {

                this.page = 1;
                this.load();

            });

            $('#exportExcel').on('click', e => {

                e.preventDefault();

                window.open(
                    this.buildExportUrl('excel'),
                    '_blank'
                );

            });

            $('#exportPDF').on('click', e => {

                e.preventDefault();

                window.open(
                    this.buildExportUrl('pdf'),
                    '_blank'
                );

            });

        },

        buildExportUrl(type) {

            const params = $.param({

                plant    : $('#mc_filter_plant').val(),
                material : $('#mc_filter_material').val(),
                month    : this.toYM($('#mc_month').val())

            });

            if(type === 'excel') {

                return '<?= base_url("report-closing-inventory-price/export_excel_monthly_inventory_price"); ?>?' + params;

            }

            return '<?= base_url("report-closing-inventory-price/export_pdf_monthly_inventory_price"); ?>?' + params;
        },

        load(page = null) {
            $('#loadingOverlay').show();

            if(page !== null){
                this.page = page;
            }

            const params = {

                page     : this.page,
                limit    : 10,

                plant    : $('#mc_filter_plant').val(),

                material : $('#mc_filter_material').val(),

                month    : this.toYM(
                    $('#mc_month').val()
                )

            };

            $.get(

                '<?= base_url("report-closing-inventory-price/load_monthly_closing_inventory_price"); ?>',

                params,

                (resp) => {

                    this.render(resp.rows || []);

                    this.renderGrand(resp.grand || {});

                    this.renderSummary(resp);

                    $('#mc_pagination').html(
                        resp.pagination || ''
                    );

                    $('#mc_info').text(
                        'Total Data : ' + (resp.total || 0)
                    );

                    $('#loadingOverlay').hide();

                },

                'json'
            ).fail(()=>{

                $('#loadingOverlay').hide();

            }

            );

        },

        renderSummary(resp) {

            const grand = resp.grand || {};

            $('#kpi_total_material')
                .text(resp.total || 0);

            $('#kpi_begin_amount')
                .text(
                    this.rupiah(grand.bg_amount)
                );

            $('#kpi_in_amount')
                .text(
                    this.rupiah(grand.in_amount)
                );

            $('#kpi_end_amount')
                .text(
                    this.rupiah(grand.end_amount)
                );
        },

        render(rows) {

            const tbody = $('#monthlyCostTable tbody');

            tbody.empty();

            if(!rows.length){

                tbody.html(`
                    <tr>
                        <td colspan="13"
                            class="text-center py-4">

                            No data found

                        </td>
                    </tr>
                `);

                return;
            }

            rows.forEach(r => {

                tbody.append(`

                    <tr>

                        <td>

                            <div class="fw-bold">
                                ${r.material_name || '-'}
                            </div>

                            <small class="text-muted">
                                ${r.material}
                            </small>

                        </td>

                        <td class="text-end">
                            ${this.decimal(r.bg_qty)}
                        </td>

                        <td class="text-end">
                            ${this.decimal(r.bg_bw)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.bg_amount)}
                        </td>

                        <td class="text-end">
                            ${this.decimal(r.in_qty)}
                        </td>

                        <td class="text-end">
                            ${this.decimal(r.in_bw)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.in_amount)}
                        </td>

                        <td class="text-end">
                            ${this.decimal(r.out_qty)}
                        </td>

                        <td class="text-end">
                            ${this.decimal(r.out_bw)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.out_amount)}
                        </td>

                        <td class="text-end ending-cell">
                            ${this.decimal(r.end_qty)}
                        </td>

                        <td class="text-end ending-cell">
                            ${this.decimal(r.end_bw)}
                        </td>

                        <td class="text-end ending-cell">
                            ${this.rupiah(r.end_amount)}
                        </td>

                    </tr>

                `);

            });

        },

        renderGrand(g) {

            $('#mc_gt_bg_qty')
                .text(
                    this.decimal(g.bg_qty)
                );

            $('#mc_gt_bg_bw')
                .text(
                    this.decimal(g.bg_bw)
                );

            $('#mc_gt_bg_amount')
                .text(
                    this.rupiah(g.bg_amount)
                );

            $('#mc_gt_in_qty')
                .text(
                    this.decimal(g.in_qty)
                );

            $('#mc_gt_in_bw')
                .text(
                    this.decimal(g.in_bw)
                );

            $('#mc_gt_in_amount')
                .text(
                    this.rupiah(g.in_amount)
                );

            $('#mc_gt_out_qty')
                .text(
                    this.decimal(g.out_qty)
                );

            $('#mc_gt_out_bw')
                .text(
                    this.decimal(g.out_bw)
                );

            $('#mc_gt_out_amount')
                .text(
                    this.rupiah(g.out_amount)
                );

            $('#mc_gt_end_qty')
                .text(
                    this.decimal(g.end_qty)
                );

            $('#mc_gt_end_bw')
                .text(
                    this.decimal(g.end_bw)
                );

            $('#mc_gt_end_amount')
                .text(
                    this.rupiah(g.end_amount)
                );

        },

        toYM(v) {

            return v
                ? v.replace('-', '')
                : '';

        },

        formatYM(ym) {

            if(!ym) return '-';

            return ym.substr(4,2) +
                '/' +
                ym.substr(0,4);

        },

        rupiah(x) {

            return parseFloat(x || 0)
                .toLocaleString(
                    'id-ID'
                );

        },

        decimal(x) {

            return parseFloat(x || 0)
                .toLocaleString(
                    'id-ID',
                    {
                        minimumFractionDigits : 2,
                        maximumFractionDigits : 2
                    }
                );

        }

    };

    /* ===========================
    AJAX PAGINATION
    =========================== */

    $(document).on(
        'click',
        '#mc_pagination a',
        function(e){

            e.preventDefault();

            const page =
                $(this).data('page');

            if(page){

                MonthlyClosingInventoryPriceReport
                    .load(page);

            }

        }
    );

    /* ===========================
    INITIALIZE
    =========================== */

    $(document).ready(() => {

        if(window.MonthlyClosingInventoryPriceReport){

            MonthlyClosingInventoryPriceReport
                .init();

        }

    });

</script>