<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
if (!is_array($userPlants)) {
    $userPlants = [$this->session->userdata('plant')];
}
?>

<!-- FILTER -->
<div class="row mb-3">

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

    <!-- MATERIAL -->
    <div class="col-md-2">
        <label class="form-label">Material</label>
        <input type="text" id="mc_filter_material" class="form-control" placeholder="Material">
    </div>

    <!-- MONTH FROM -->
    <div class="col-md-2">
        <label class="form-label">Month</label>
        <input type="month" id="mc_month" class="form-control">
    </div>

    <!-- FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="mc_btnFilter">
            Filter
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
                <li><a class="dropdown-item" href="#" id="exportExcel">
                    <i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDF">
                    <i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered" id="monthlyCostTable">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">PLANT</th>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">MATERIAL</th>

                <th class="text-end" style="vertical-align: middle; white-space: nowrap">BG QTY</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">BG BW</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">BG AMOUNT</th>

                <th class="text-end" style="vertical-align: middle; white-space: nowrap">IN QTY</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">IN BW</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">IN AMOUNT</th>

                <th class="text-end" style="vertical-align: middle; white-space: nowrap">OUT QTY</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">OUT BW</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">OUT AMOUNT</th>

                <th class="text-end" style="vertical-align: middle; white-space: nowrap">END QTY</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">END BW</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">END AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="2" class="text-end detail-row">GRAND TOTAL</td>

                <td class="text-end detail-row" id="mc_gt_bg_qty">0.00</td>
                <td class="text-end detail-row" id="mc_gt_bg_bw">0.00</td>
                <td class="text-end detail-row" id="mc_gt_bg_amount">0</td>

                <td class="text-end detail-row" id="mc_gt_in_qty">0.00</td>
                <td class="text-end detail-row" id="mc_gt_in_bw">0.00</td>
                <td class="text-end detail-row" id="mc_gt_in_amount">0</td>

                <td class="text-end detail-row" id="mc_gt_out_qty">0.00</td>
                <td class="text-end detail-row" id="mc_gt_out_bw">0.00</td>
                <td class="text-end detail-row" id="mc_gt_out_amount">0</td>

                <td class="text-end detail-row" id="mc_gt_end_qty">0.00</td>
                <td class="text-end detail-row" id="mc_gt_end_bw">0.00</td>
                <td class="text-end detail-row" id="mc_gt_end_amount">0</td>
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
    window.MonthlyClosingInventoryPriceReport = {

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
            $('#mc_filter_plant').select2({ width:'100%' });

            const now = new Date().toISOString().slice(0,7);
            $('#mc_month').val(now);
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
                }, 400);
            });

            $('#mc_filter_plant, #mc_month').on('change', () => {
                this.page = 1;
                this.load();
            });

            $('#exportExcel').on('click', e => {
                e.preventDefault();
                window.open(this.buildExportUrl('excel'),'_blank');
            });

            $('#exportPDF').on('click', e => {
                e.preventDefault();
                window.open(this.buildExportUrl('pdf'),'_blank');
            });

        },

        buildExportUrl(type){
            const params = $.param({
                plant    : $('#mc_filter_plant').val(),
                material : $('#mc_filter_material').val(),
                month    : this.toYM($('#mc_month').val())
            });

            if(type === 'excel'){
                return '<?= base_url("report-closing-inventory-price/export_excel_monthly_inventory_price"); ?>?' + params;
            }
            return '<?= base_url("report-closing-inventory-price/export_pdf_monthly_inventory_price"); ?>?' + params;
        },

        load(page = null) {
            if (page !== null) this.page = page;

            const params = {
                page     : this.page,
                limit    : 10,
                plant    : $('#mc_filter_plant').val(),
                material : $('#mc_filter_material').val(),
                month    : this.toYM($('#mc_month').val())
            };

            $.get(
                '<?= base_url("report-closing-inventory-price/load_monthly_closing_inventory_price"); ?>',
                params,
                resp => {
                    this.render(resp.rows || []);
                    this.renderGrand(resp.grand || {});
                    $('#mc_pagination').html(resp.pagination || '');
                    $('#mc_info').text(`Total data : ${resp.total || 0}`);
                },
                'json'
            );
        },

        render(rows) {
            const tbody = $('#monthlyCostTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="14" class="text-center" style="vertical-align: middle; white-space: nowrap">No data</td></tr>');
                return;
            }

            rows.forEach(r => {
                tbody.append(`
                    <tr>
                        <td class="text-center" style="vertical-align: middle; white-space: nowrap"><b>${r.plant_name || r.plant}</b></td>
                        <td class="text-center" style="vertical-align: middle; white-space: nowrap">${r.material_name || '-'}<br><b>${r.material}</b></td>

                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.bg_qty)}</td>
                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.bg_bw)}</td>
                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.rupiah(r.bg_amount)}</td>

                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.in_qty)}</td>
                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.in_bw)}</td>
                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.rupiah(r.in_amount)}</td>

                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.out_qty)}</td>
                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.out_bw)}</td>
                        <td class="text-end" style="vertical-align: middle; white-space: nowrap">${this.rupiah(r.out_amount)}</td>

                        <td class="text-end fw-bold" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.end_qty)}</td>
                        <td class="text-end fw-bold" style="vertical-align: middle; white-space: nowrap">${this.decimal(r.end_bw)}</td>
                        <td class="text-end fw-bold" style="vertical-align: middle; white-space: nowrap">${this.rupiah(r.end_amount)}</td>
                    </tr>
                `);
            });
        },

        renderGrand(g) {
            $('#mc_gt_bg_qty').text(this.decimal(g.bg_qty));
            $('#mc_gt_bg_bw').text(this.decimal(g.bg_bw));
            $('#mc_gt_bg_amount').text(this.rupiah(g.bg_amount));

            $('#mc_gt_in_qty').text(this.decimal(g.in_qty));
            $('#mc_gt_in_bw').text(this.decimal(g.in_bw));
            $('#mc_gt_in_amount').text(this.rupiah(g.in_amount));

            $('#mc_gt_out_qty').text(this.decimal(g.out_qty));
            $('#mc_gt_out_bw').text(this.decimal(g.out_bw));
            $('#mc_gt_out_amount').text(this.rupiah(g.out_amount));

            $('#mc_gt_end_qty').text(this.decimal(g.end_qty));
            $('#mc_gt_end_bw').text(this.decimal(g.end_bw));
            $('#mc_gt_end_amount').text(this.rupiah(g.end_amount));
        },

        /* ===== UTIL ===== */

        toYM(v) {
            return v ? v.replace('-', '') : '';
        },

        formatYM(ym) {
            return ym ? ym.substr(4,2) + '/' + ym.substr(0,4) : '-';
        },

        rupiah(x) {
            return parseFloat(x || 0).toLocaleString('id-ID');
        },

        decimal(x) {
            return parseFloat(x || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    /* PAGINATION */
    $(document).on('click', '#mc_pagination a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if (page) MonthlyClosingInventoryPriceReport.load(page);
    });

    /* INIT */
    $(document).ready(() => {
        if (window.MonthlyClosingInventoryPriceReport) {
            MonthlyClosingInventoryPriceReport.init();
        }
    });
</script>