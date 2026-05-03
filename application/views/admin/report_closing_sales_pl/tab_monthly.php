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

        <thead class="table-light">

            <tr>
                <th rowspan="2" class="text-center">PLANT</th>
                <th rowspan="2" class="text-center">MONTH</th>
                <th rowspan="2" class="text-center">ITEM</th>
                <th rowspan="2" class="text-center">ITEM NAME</th>
                <th rowspan="2" class="text-center">CLASS</th>

                <th colspan="3" class="text-center">BEGINNING</th>
                <th colspan="3" class="text-center">PRODUCTION</th>
                <th colspan="3" class="text-center">PURCHASE</th>
                <th colspan="3" class="text-center">ADJUST</th>
                <th colspan="3" class="text-center">COGS</th>

                <th rowspan="2" class="text-center">ENDING</th>
                <th rowspan="2" class="text-center">NET SALES</th>
                <th rowspan="2" class="text-center">PROFIT</th>
            </tr>

            <tr>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

                <th class="text-center">BW</th>
                <th class="text-center">UP</th>
                <th class="text-center">AMT</th>

            </tr>

        </thead>

        <tbody></tbody>

        <tfoot>
            <tr class="table-secondary fw-bold">

                <td colspan="5" class="text-end">GRAND TOTAL</td>

                <td id="mc_gt_bg_bw"></td>
                <td id="mc_gt_bg_up"></td>
                <td id="mc_gt_begin"></td>

                <td id="mc_gt_pd_bw"></td>
                <td id="mc_gt_pd_up"></td>
                <td id="mc_gt_prod"></td>

                <td id="mc_gt_pr_bw"></td>
                <td id="mc_gt_pr_up"></td>
                <td id="mc_gt_purchase"></td>

                <td id="mc_gt_ds_bw"></td>
                <td id="mc_gt_ds_up"></td>
                <td id="mc_gt_adjust"></td>

                <td id="mc_gt_cogs_bw"></td>
                <td id="mc_gt_cogs_up"></td>
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
.detail-row {
    border: 2px solid #efefef !important;
    vertical-align: middle !important;
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
                console.log(resp);

                this.render(resp.rows || []);

                const grand = Array.isArray(resp.grand) ? resp.grand[0] : resp.grand;

                this.renderGrand(grand || {});

                $('#mc_pagination').html(resp.pagination || '');
                $('#mc_info').text(`Total data: ${resp.total || 0}`);
            }, 'json');
        },

        render(rows){
            const tbody = $('#monthlyPlTable tbody').empty();

            if(!rows.length){
                tbody.html('<tr><td colspan="23" class="text-center">No data</td></tr>');
                return;
            }

            rows.forEach(r=>{
                tbody.append(`
                    <tr>

                        <td class="text-center"><b>${r.plant_name}</b></td>
                        <td class="text-center">${this.formatYM(r.ym)}</td>
                        <td class="text-center"><b>${r.item}</b></td>
                        <td>${r.item_name}</td>
                        <td class="text-center">${r.class_name}</td>

                        <td class="text-end">${this.rupiah(r.bg_bw)}</td>
                        <td class="text-end">${this.num(r.bg_up)}</td>
                        <td class="text-end">${this.rupiah(r.begin_amt)}</td>

                        <td class="text-end">${this.rupiah(r.production_bw)}</td>
                        <td class="text-end">${this.num(r.production_up)}</td>
                        <td class="text-end">${this.rupiah(r.production_amt)}</td>

                        <td class="text-end">${this.rupiah(r.purchase_bw)}</td>
                        <td class="text-end">${this.num(r.purchase_up)}</td>
                        <td class="text-end">${this.rupiah(r.purchase_amt)}</td>

                        <td class="text-end">${this.rupiah(r.adjust_bw)}</td>
                        <td class="text-end">${this.num(r.adjust_up)}</td>
                        <td class="text-end">${this.rupiah(r.adjust_amt)}</td>

                        <td class="text-end">${this.rupiah(r.cogs_bw)}</td>
                        <td class="text-end">${this.num(r.cogs_up)}</td>
                        <td class="text-end">${this.rupiah(r.cogs_amt)}</td>

                        <td class="text-end">${this.rupiah(r.ending_amt)}</td>
                        <td class="text-end">${this.rupiah(r.sales_net_amt)}</td>
                        <td class="text-end">${this.rupiah(r.sales_profit_amt)}</td>

                    </tr>
                `);
            });
        },

        renderGrand(g){
            $('#mc_gt_bg_bw').text(this.rupiah(g.bg_bw));
            $('#mc_gt_bg_up').text(this.num(g.bg_up));
            $('#mc_gt_begin').text(this.rupiah(g.begin_amt));

            $('#mc_gt_pd_bw').text(this.rupiah(g.production_bw));
            $('#mc_gt_pd_up').text(this.num(g.production_up));
            $('#mc_gt_prod').text(this.rupiah(g.production_amt));

            $('#mc_gt_pr_bw').text(this.rupiah(g.purchase_bw));
            $('#mc_gt_pr_up').text(this.num(g.purchase_up));
            $('#mc_gt_purchase').text(this.rupiah(g.purchase_amt));

            $('#mc_gt_ds_bw').text(this.rupiah(g.adjust_bw));
            $('#mc_gt_ds_up').text(this.num(g.adjust_up));
            $('#mc_gt_adjust').text(this.rupiah(g.adjust_amt));

            $('#mc_gt_cogs_bw').text(this.rupiah(g.cogs_bw));
            $('#mc_gt_cogs_up').text(this.num(g.cogs_up));
            $('#mc_gt_cogs').text(this.rupiah(g.cogs_amt));

            $('#mc_gt_end').text(this.rupiah(g.ending_amt));
            $('#mc_gt_net').text(this.rupiah(g.sales_net_amt));
            $('#mc_gt_profit').text(this.rupiah(g.sales_profit_amt));
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