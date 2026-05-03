<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
if (!is_array($userPlants)) {
    $userPlants = [$this->session->userdata('plant')];
}
?>

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="monthly_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if (in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- MATERIAL -->
    <div class="col-md-2">
        <label class="form-label">Item</label>
        <input type="text" id="mc_filter_material" class="form-control" placeholder="Item">
    </div>

    <!-- MONTH FROM -->
    <div class="col-md-2">
        <label class="form-label">Month</label>
        <input type="month" id="mc_month" class="form-control">
    </div>

    <!-- FILTER BUTTON -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="mc_btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <!-- MONTH TO -->
    <div class="col-md-2">
    </div>

    <div class="col-md-2">
    </div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="exportExcel"><i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDF"><i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered" id="monthlyCostTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">MONTH</th>
                <th class="text-center">ITEM</th>
                <th class="text-center">ITEM NAME</th>
                <th class="text-center">CLASS</th>

                <th class="text-end">QTY</th>
                <th class="text-end">KG</th>

                <th class="text-end">INDEX AMOUNT</th>
                <th class="text-end">MARKET AMOUNT</th>
                <th class="text-end">MODAL AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="5" class="text-end detail-row">GRAND TOTAL</td>
                <td class="text-end detail-row" id="mc_gt_qty">0.00</td>
                <td class="text-end detail-row" id="mc_gt_bw">0.00</td>
                <td class="text-end detail-row" id="mc_gt_index_amt">0</td>
                <td class="text-end detail-row" id="mc_gt_market_amt">0</td>
                <td class="text-end detail-row" id="mc_gt_modal_amt">0</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="mc_info"></div>
    <div id="mc_pagination"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
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
        $('#monthly_filter_plant').select2({ width:'100%', allowClear:true });

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
            $('#mc_pagination').html(resp.pagination || '');
            $('#mc_info').text(`Total data : ${resp.total || 0}`);

        }, 'json');
    },

    render(rows) {
        const tbody = $('#monthlyCostTable tbody').empty();

        if (!rows.length) {
            tbody.html('<tr><td colspan="10" class="text-center">No data</td></tr>');
            return;
        }

        rows.forEach(r => {
        tbody.append(`
            <tr>
                <td class="text-center"><b>${r.plant_name || r.plant}</b></td>
                <td class="text-center">${this.formatYM(r.ym)}</td>

                <td class="text-center"><b>${r.item}</b></td>
                <td class="text-center">${r.item_name}</td>
                <td class="text-center">${r.class_name}</td>

                <td class="text-end">${this.decimal(r.qty)}</td>
                <td class="text-end">${this.decimal(r.kg)}</td>

                <td class="text-end">${this.rupiah(r.index_amount)}</td>
                <td class="text-end">${this.rupiah(r.market_amount)}</td>
                <td class="text-end">${this.rupiah(r.modal_amount)}</td>
                </tr>
            `);
        });
    },

    renderGrand(g) {
        $('#mc_gt_qty').text(this.decimal(g.qty || 0));
        $('#mc_gt_bw').text(this.decimal(g.kg || 0));
        $('#mc_gt_index_amt').text(this.rupiah(g.index_amount || 0));
        $('#mc_gt_market_amt').text(this.rupiah(g.market_amount || 0));
        $('#mc_gt_modal_amt').text(this.rupiah(g.modal_amount || 0));
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


