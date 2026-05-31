<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Total Item</div>
                <div class="fs-4 fw-bold text-primary"
                     id="kpi_total_item">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Net Sales</div>
                <div class="fs-4 fw-bold text-success"
                     id="kpi_net_sales">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Profit</div>
                <div class="fs-4 fw-bold text-primary"
                     id="kpi_profit">0</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 kpi-card">
            <div class="card-body">
                <div class="text-muted small kpi-label">Ending Amount</div>
                <div class="fs-4 fw-bold text-info"
                     id="kpi_ending_amount">0</div>
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

            <input type="month"
                   id="mc_month"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">
                Material
            </label>

            <input type="text"
                   id="mc_filter_item"
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
                        id="exportExcelmc">
                    Excel
                </button>

                <button class="btn btn-danger"
                        id="exportPDFmc">
                    PDF
                </button>

            </div>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm">

    <div id="loadingOverlay">
        Loading Sales P/L Report...
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">

        <h6 class="mb-0 fw-bold">
            Monthly Sales P/L
        </h6>

    </div>

    <div class="card-body pt-2 px-3 pb-3">

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

                    <tr class="fw-bold">

                        <td class="text-end profit-cell">
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

#monthlyPlTable thead tr:nth-child(2) th{
    background:#eef2f7 !important;
    color:#495057 !important;
}

#monthlyPlTable td:first-child{
    min-width:250px;
    position:sticky;
    left:0;
    background:#fff;
    z-index:5;
}

#monthlyPlTable th:first-child{
    min-width:250px;
    position:sticky;
    left:0;
    z-index:20;
    background:#2f3c4f !important;
    color:#fff !important;
}

#monthlyPlTable thead th:first-child{
    background:#2f3c4f !important;
    color:#fff !important;
}

#monthlyPlTable thead th{
    padding:10px 8px !important;
    vertical-align:middle !important;
}

#monthlyPlTable td:first-child,
#monthlyPlTable th:first-child{
    min-width:250px;
    max-width:250px;
}

#monthlyPlTable tbody tr:hover{
    background:#f8fafc;
}

.profit-cell{
    background:#f0fff5;
    color:#198754;
    font-weight:700;
}

tfoot tr{
    background:#eef4ff !important;
    font-weight:700;
}

tfoot td{
    border-top:3px solid #2f3c4f !important;
}

#monthlyPlTable th,
#monthlyPlTable td{
    white-space:nowrap;
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

.kpi-card{
    transition:.2s;
}

.kpi-card:hover{
    transform:translateY(-2px);
}

.px-3{
    padding-right:0rem !important;
    padding-left:0rem !important;
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
            $('#loadingOverlay').show();

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
                $('#loadingOverlay').hide();

                },'json')
                .fail(() => {
                    $('#loadingOverlay').hide();
                });
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

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.begin_amt)}
                        </td>

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.production_amt)}
                        </td>

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.purchase_amt)}
                        </td>

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.adjust_amt)}
                        </td>

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.cogs_amt)}
                        </td>

                        <td class="text-end profit-cell">
                            ${this.rupiah(r.ending_amt)}
                        </td>

                        <td class="text-end profit-cell text-primary fw-bold">
                            ${this.rupiah(r.sales_net_amt)}
                        </td>

                        <td class="text-end profit-cell cost-cell">
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