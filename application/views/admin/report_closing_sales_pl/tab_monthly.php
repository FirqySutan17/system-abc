<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
if (!is_array($userPlants)) {
    $userPlants = [$this->session->userdata('plant')];
}
?>

<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total Item</div>
                <div class="fs-4 fw-bold text-primary"
                     id="kpi_total_item">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Net Sales</div>
                <div class="fs-4 fw-bold text-success"
                     id="kpi_net_sales">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Profit</div>
                <div class="fs-4 fw-bold text-primary"
                     id="kpi_profit">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Ending Amount</div>
                <div class="fs-4 fw-bold text-info"
                     id="kpi_ending_amount">0</div>
            </div>
        </div>
    </div>

</div>

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="mc_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if (in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ITEM -->
    <div class="col-md-2">
        <label class="form-label">Item</label>
        <input type="text" id="mc_filter_item" class="form-control" placeholder="Item">
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

    <div class="col-md-4"></div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="exportExcelmc"><i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDFmc"><i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered" id="monthlyPlTable">

        <thead>

            <tr class="table-dark">

                <th rowspan="2">
                    Material
                </th>

                <th colspan="4" class="text-center">
                    Movement
                </th>

                <th colspan="4" class="text-center">
                    Sales P/L
                </th>

            </tr>

            <tr class="table-secondary">

                <th>Beginning</th>
                <th>Production</th>
                <th>Purchase</th>
                <th>Adjust</th>

                <th>COGS</th>
                <th>Ending</th>
                <th>Net Sales</th>
                <th>Profit</th>

            </tr>

        </thead>

        <tbody></tbody>

        <tfoot>

            <tr class="table-warning fw-bold">

                <td class="text-end">
                    GRAND TOTAL
                </td>

                <td id="mc_gt_begin"></td>
                <td id="mc_gt_prod"></td>
                <td id="mc_gt_purchase"></td>
                <td id="mc_gt_adjust"></td>

                <td id="mc_gt_cogs"></td>
                <td id="mc_gt_end"></td>
                <td id="mc_gt_net"></td>
                <td id="mc_gt_profit"></td>

            </tr>

        </tfoot>
    </table>
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

#monthlyPlTable thead tr:nth-child(2) th{
    background:#eef2f7 !important;
    color:#495057 !important;
}

#monthlyPlTable td:first-child,
#monthlyPlTable th:first-child{
    min-width:250px;
    position:sticky;
    left:0;
    background:white;
    z-index:5;
}

#monthlyPlTable thead th:first-child{
    background:#2f3c4f !important;
    color:#fff !important;
}

#monthlyPlTable tbody tr:hover{
    background:#f8fafc;
}

.cost-cell{
    background:#f5f9ff;
    color:#0d6efd;
    font-weight:600;
}

tfoot tr{
    background:#f4f7fb !important;
}

tfoot td{
    border-top:2px solid #2f3c4f !important;
}

#monthlyPlTable th,
#monthlyPlTable td{
    white-space:nowrap;
}
</style>

<script>
    window.MonthlySalesPL = {

        loaded: false,
        page: 1,

        init() {
            if (this.loaded) return;
            this.loaded = true;

            this.initFilter();
            this.bindEvent();
            $('#exportExcelmc').click(e=>{
                e.preventDefault();
                const params = $.param({
                    plant: $('#mc_filter_plant').val(),
                    item: $('#mc_filter_item').val(),
                    month: this.toYM($('#mc_month').val())
                });
                window.open('<?= base_url("report-closing-sales-pl/export_excel_monthly_sales_pl"); ?>?'+params,'_blank');
            });

            $('#exportPDFmc').click(e=>{
                e.preventDefault();
                const params = $.param({
                    plant: $('#mc_filter_plant').val(),
                    item: $('#mc_filter_item').val(),
                    month: this.toYM($('#mc_month').val())
                });
                window.open('<?= base_url("report-closing-sales-pl/export_pdf_monthly_sales_pl"); ?>?'+params,'_blank');
            });
            this.load();
        },

        initFilter() {
            $('#mc_filter_plant').select2({ width:'100%', allowClear:true });

            const now = new Date();
            const ym = now.toISOString().slice(0,7);
            $('#mc_month').val(ym);
        },

        bindEvent() {
            let timer = null;

            $('#mc_btnFilter').on('click', () => { this.page = 1; this.load(); });

            $('#mc_filter_item').on('keyup', () => {
                clearTimeout(timer);
                timer = setTimeout(() => { this.page = 1; this.load(); }, 300);
            });

            $('#mc_filter_plant, #mc_month').on('change', () => {
                this.page = 1;
                this.load();
            });
        },

        load(page = null) {
            if (page !== null) this.page = page;

            const params = {
                page: this.page,
                limit: 50,
                plant: $('#mc_filter_plant').val(),
                item: $('#mc_filter_item').val(),
                month: this.toYM($('#mc_month').val())
            };

            $.get('<?= base_url("report-closing-sales-pl/load_monthly_sales_pl"); ?>', params, resp => {
                this.render(resp.rows || []);

                const grand = Array.isArray(resp.grand)
                    ? resp.grand[0]
                    : resp.grand;

                this.renderGrand(grand || {});

                this.renderSummary({
                    grand : grand,
                    total : resp.total
                });

                $('#mc_pagination').html(resp.pagination || '');
                $('#mc_info').text(`Total data: ${resp.total || 0}`);
            }, 'json');
        },

        renderSummary(resp){

            const g = resp.grand || {};

            $('#kpi_total_item')
                .text(g.total_item || 0);

            $('#kpi_net_sales')
                .text(this.rupiah(g.sales_net_amt));

            $('#kpi_profit')
                .text(this.rupiah(g.sales_profit_amt));

            $('#kpi_ending_amount')
                .text(this.rupiah(g.ending_amt));

        },

        render(rows){

            const tbody = $('#monthlyPlTable tbody');

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
                            ${this.rupiah(r.begin_amt)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.production_amt)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.purchase_amt)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.adjust_amt)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.cogs_amt)}
                        </td>

                        <td class="text-end">
                            ${this.rupiah(r.ending_amt)}
                        </td>

                        <td class="text-end text-primary fw-bold">
                            ${this.rupiah(r.sales_net_amt)}
                        </td>

                        <td class="text-end cost-cell">
                            ${this.rupiah(r.sales_profit_amt)}
                        </td>

                    </tr>

                `);

            });

        },

        renderGrand(g){

            $('#mc_gt_begin')
                .text(this.rupiah(g.begin_amt));

            $('#mc_gt_prod')
                .text(this.rupiah(g.production_amt));

            $('#mc_gt_purchase')
                .text(this.rupiah(g.purchase_amt));

            $('#mc_gt_adjust')
                .text(this.rupiah(g.adjust_amt));

            $('#mc_gt_cogs')
                .text(this.rupiah(g.cogs_amt));

            $('#mc_gt_end')
                .text(this.rupiah(g.ending_amt));

            $('#mc_gt_net')
                .text(this.rupiah(g.sales_net_amt));

            $('#mc_gt_profit')
                .text(this.rupiah(g.sales_profit_amt));
        },

        toYM(v) { return v ? v.replace('-', '') : ''; },
        formatYM(ym) { return ym ? ym.slice(4,6)+'/'+ym.slice(0,4) : '-'; },
        rupiah(x){
            const v = parseFloat(x);
            return (isNaN(v) ? 0 : v).toLocaleString('id-ID');
        },
        decimal(x) { return parseFloat(x||0).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}); },
        num(x){
            const v = parseFloat(x);
            return (isNaN(v) ? 0 : v).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
        }
    };

    $(document).on('click', '#mc_pagination a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if(page) MonthlySalesPL.load(page);
    });

    $(document).ready(function(){
        MonthlySalesPL.init();
    });
</script>