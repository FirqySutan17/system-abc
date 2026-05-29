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
                <div class="text-muted small kpi-label">
                    Total Item
                </div>

                <div class="fs-4 fw-bold text-primary"
                     id="kpi_total_item">
                    0
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Total Qty
                </div>

                <div class="fs-4 fw-bold text-info"
                     id="kpi_total_qty">
                    0
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Market Amount
                </div>

                <div class="fs-4 fw-bold text-primary"
                     id="kpi_market_amount">
                    0
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">
                    Modal Amount
                </div>

                <div class="fs-4 fw-bold text-success"
                     id="kpi_modal_amount">
                    0
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card mb-4"
     style="background:transparent;border:none !important;box-shadow:none !important">

    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Plant
            </label>

            <select id="monthly_filter_plant"
                    class="form-select">

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

            <input type="month"
                   id="mc_month"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Material
            </label>

            <input type="text"
                   id="mc_filter_material"
                   class="form-control"
                   placeholder="Material Code / Name">
        </div>

        <div class="col-md-3 d-flex align-items-end justify-content-end">

            <button class="btn btn-primary me-2"
                    id="mc_btnFilter">
                Search
            </button>

            <div class="btn-group">

                <button class="btn btn-success"
                        id="exportExcel">
                    Excel
                </button>

                <button class="btn btn-danger"
                        id="exportPDF">
                    PDF
                </button>

            </div>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm">

    <div id="loadingOverlay">
        Loading Closing Cost Report...
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="mb-0 fw-bold">
            Monthly Closing Cost
        </h6>

    </div>

    <div class="card-body pt-2 px-3 pb-3">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0"
                   id="monthlyCostTable">

                <thead>

                    <tr class="table-dark">

                        <th rowspan="2"
                            class="align-middle">
                            Material
                        </th>

                        <th colspan="2"
                            class="text-center">
                            Production
                        </th>

                        <th colspan="2"
                            class="text-center">
                            Index
                        </th>

                        <th colspan="2"
                            class="text-center">
                            Market
                        </th>

                        <th colspan="2"
                            class="text-center">
                            Cost
                        </th>

                    </tr>

                    <tr class="table-secondary">

                        <th>Qty</th>
                        <th>KG</th>

                        <th>Price</th>
                        <th>Amount</th>

                        <th>Price</th>
                        <th>Amount</th>

                        <th>Cost/Unit</th>
                        <th>Modal Amount</th>

                    </tr>

                </thead>

                <tbody></tbody>

                <tfoot>

                    <tr class="table-warning fw-bold">

                        <td class="text-end">
                            GRAND TOTAL
                        </td>

                        <td class="text-end"
                            id="mc_gt_qty">0.00</td>

                        <td class="text-end"
                            id="mc_gt_kg">0.00</td>

                        <td></td>

                        <td class="text-end"
                            id="mc_gt_index_amount">0</td>

                        <td></td>

                        <td class="text-end"
                            id="mc_gt_market_amount">0</td>

                        <td></td>

                        <td class="text-end"
                            id="mc_gt_modal_amount">0</td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>

<div class="d-flex justify-content-between mt-3">
    <div id="mc_info"></div>
    <div id="mc_pagination"></div>
</div>

<style>
    .card{
        border-radius:14px;
        position:relative;
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

    .table-dark th{
        background:#2f3c4f !important;
        color:#fff !important;
        border-color:#3f4d63 !important;
    }

    #monthlyCostTable thead tr:nth-child(2) th{
        background:#eef2f7 !important;
        color:#495057 !important;
    }

    #monthlyCostTable td:first-child{
        min-width:250px;
        position:sticky;
        left:0;
        background:#fff;
        z-index:5;
    }

    #monthlyCostTable th:first-child{
        min-width:250px;
        position:sticky;
        left:0;
        z-index:20;
        background:#2f3c4f !important;
        color:#fff !important;
    }

    #monthlyCostTable thead th:first-child{
        background:#2f3c4f !important;
        color:#fff !important;
    }

    #monthlyCostTable tbody tr:hover{
        background:#f8fafc;
    }

    tfoot tr{
        background:#eef4ff !important;
        font-weight:700;
    }

    tfoot td{
        border-top:3px solid #2f3c4f !important;
    }

    .kpi-card .card-body{
        padding:18px 22px;
    }

    .kpi-label{
        font-size:12px;
        color:#6c757d;
        text-transform:uppercase;
        letter-spacing:1px;
    }

    #loadingOverlay{
        display:none;
        position:absolute;
        inset:0;
        background:rgba(255,255,255,.8);
        z-index:999;
        text-align:center;
        padding-top:150px;
        font-size:18px;
        font-weight:600;
    }
    #monthlyCostTable th,
    #monthlyCostTable td{
        white-space:nowrap;
    }
    .cost-cell{
        background:#f0fff5;
        color:#198754;
        font-weight:700;
    }
    .px-3 {
        padding-right: 0rem !important;
        padding-left: 0rem !important;
    }
    .kpi-card{
        transition:.2s;
    }

    .kpi-card:hover{
        transform:translateY(-2px);
    }
</style>

<script>
window.MonthlyClosingCost = {

    loaded: false,
    page: 1,

    init() {
        if (this.loaded) return;
        this.loaded = true;

        this.initFilter();
        this.bindEvent();
        this.load();
    },

    initFilter() {
        $('#monthly_filter_plant').select2({
            width:'100%'
        });

        const now = new Date();
        const ym = now.toISOString().slice(0,7);

        $('#mc_month').val(ym);
    },

    bindEvent() {
        let timer = null;

        $('#mc_btnFilter').on('click', () => {
            this.page = 1;
            this.load();
        });

        $('#mc_filter_material').on('keyup', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                this.page = 1;
                this.load();
            }, 300);
        });

        $('#monthly_filter_plant, #mc_month')
            .on('change', () => {
                this.page = 1;
                this.load();
            });
    },

    load(page = null) {

        if (page !== null) this.page = page;

        const params = {
            page     : this.page,
            limit    : 50,
            plant    : $('#monthly_filter_plant').val(),
            material : $('#mc_filter_material').val(),
            month    : this.toYM($('#mc_month').val())
        };

        $.get('<?= base_url("report-closing-cost/load_monthly_closing_cost"); ?>', params, resp => {

            this.render(resp.rows || []);
            this.renderGrand(resp.grand || {});
            this.renderSummary(resp);
            $('#mc_pagination').html(resp.pagination || '');
            $('#mc_info').text(`Total data : ${resp.total || 0}`);

        }, 'json');
    },

    renderSummary(resp){

        const g = resp.grand || {};

        $('#kpi_total_item')
            .text(g.total_item || 0);

        $('#kpi_total_qty')
            .text(this.decimal(g.qty));

        $('#kpi_market_amount')
            .text(this.rupiah(g.market_amount));

        $('#kpi_modal_amount')
            .text(this.rupiah(g.modal_amount));

    },

    render(rows){

        const tbody = $('#monthlyCostTable tbody');

        tbody.empty();

        if(!rows.length){

            tbody.html(`
                <tr>
                    <td colspan="9"
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
                            ${r.item_name || '-'}
                        </div>

                        <small class="text-muted">
                            ${r.item}
                        </small>

                    </td>

                    <td class="text-end">
                        ${this.decimal(r.qty)}
                    </td>

                    <td class="text-end">
                        ${this.decimal(r.kg)}
                    </td>

                    <td class="text-end">
                        ${this.decimal(r.index_price)}
                    </td>

                    <td class="text-end">
                        ${this.rupiah(r.index_amount)}
                    </td>

                    <td class="text-end">
                        ${this.decimal(r.market_price)}
                    </td>

                    <td class="text-end">
                        ${this.rupiah(r.market_amount)}
                    </td>

                    <td class="text-end">
                        ${this.decimal(r.cost_up)}
                    </td>

                    <td class="text-end cost-cell">
                        ${this.rupiah(r.modal_amount)}
                    </td>

                </tr>

            `);

        });

    },

    renderGrand(g) {

        $('#mc_gt_qty')
            .text(this.decimal(g.qty || 0));

        $('#mc_gt_kg')
            .text(this.decimal(g.kg || 0));

        $('#mc_gt_index_amount')
            .text(this.rupiah(g.index_amount || 0));

        $('#mc_gt_market_amount')
            .text(this.rupiah(g.market_amount || 0));

        $('#mc_gt_modal_amount')
            .text(this.rupiah(g.modal_amount || 0));
    },
    

    /* ===== UTIL ===== */

    toYM(v) {
        return v ? v.replace('-', '') : '';
    },

    formatYM(ym) {
        return ym ? ym.slice(4,6)+'/'+ym.slice(0,4) : '-';
    },

    rupiah(x) {
        return parseFloat(x||0).toLocaleString('id-ID');
    },

    decimal(x) {
        return parseFloat(x||0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }
};

$(document).on('click', '#mc_pagination a', function(e){
    e.preventDefault();
    const page = $(this).data('page');
    if (page) MonthlyClosingCost.load(page);
});

$(document).ready(function(){
    MonthlyClosingCost.init();
});
</script>


