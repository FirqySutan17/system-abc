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
        <select id="filter_plant_sm" class="form-control">
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
        <input type="text" id="sm_filter_item" class="form-control" placeholder="Item prefix">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date</label>
        <input type="date" id="sm_date" class="form-control">
    </div>

    <!-- FILTER BUTTON -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="sm_btnFilter">
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
                <li><a class="dropdown-item" href="#" id="exportExcel"><i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="exportPDF"><i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-bordered" id="summaryPLTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-end">PERIOD AMOUNT</th>
                <th class="text-end">YTD AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td class="text-end">GRAND TOTAL</td>
                <td class="text-end" id="sm_gt_tamt">0</td>
                <td class="text-end" id="sm_gt_aamt">0</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="sm_info"></div>
    <div id="sm_pagination"></div>
</div>

<style>
.detail-row {
    border: 2px solid #efefef !important;
    vertical-align: middle !important;
}
</style>

<script>
window.SummaryPL = {

    loaded: false,
    page: 1,

    init() {
        if (this.loaded) return;
        this.loaded = true;

        this.initFilter();
        this.bindEvent();

        $('#exportExcel').click(e=>{
            e.preventDefault();
            window.open(this.buildExportUrl('export_excel'), '_blank');
        });

        $('#exportPDF').click(e=>{
            e.preventDefault();
            window.open(this.buildExportUrl('export_pdf'), '_blank');
        });

        this.load();
    },

    initFilter() {
        $('#filter_plant_sm').select2({ width:'100%', allowClear:true });

        const today = new Date().toISOString().slice(0,10);
        $('#sm_date').val(today);
    },

    bindEvent() {
        $('#sm_btnFilter').on('click', () => { this.page = 1; this.load(); });

        $('#sm_filter_item').on('keyup', () => {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => { this.page = 1; this.load(); }, 300);
        });

        $('#filter_plant_sm, #sm_date').on('change', () => {
            this.page = 1;
            this.load();
        });
    },

    buildParams() {
        return {
            page: this.page,
            limit: 10,
            plant: $('#filter_plant_sm').val(),
            sitem: $('#sm_filter_item').val(),
            date: this.toYMD($('#sm_date').val())
        };
    },

    buildExportUrl(type) {
        const params = $.param(this.buildParams());
        return `<?= base_url("report-summary-pl/"); ?>${type}?${params}`;
    },

    load(page = null) {
        if (page !== null) this.page = page;

        $.get('<?= base_url("report-closing-sales-pl/load_summary"); ?>', this.buildParams(), resp => {
            this.render(resp.rows || []);
            this.renderGrand(resp.grand || {});
            $('#sm_pagination').html(resp.pagination || '');
            $('#sm_info').text(`Total data: ${resp.total || 0}`);
        }, 'json');
    },

    render(rows) {
        const tbody = $('#summaryPLTable tbody').empty();

        if (!rows.length) {
            tbody.html('<tr><td colspan="3" class="text-center">No data</td></tr>');
            return;
        }

        rows.forEach(r => {
            tbody.append(`
                <tr>
                    <td class="text-center"><b>${r.plant_name}</b></td>
                    <td class="text-end">${this.rupiah(r.TAMT)}</td>
                    <td class="text-end">${this.rupiah(r.AAMT)}</td>
                </tr>
            `);
        });
    },

    renderGrand(g) {
        $('#sm_gt_tamt').text(this.rupiah(g.TAMT || 0));
        $('#sm_gt_aamt').text(this.rupiah(g.AAMT || 0));
    },

    toYMD(v) { return v ? v.replaceAll('-', '') : ''; },
    rupiah(x) { return parseFloat(x||0).toLocaleString('id-ID'); }
};

$(document).on('click', '#sm_pagination a', function(e){
    e.preventDefault();
    const page = $(this).data('page');
    if(page) SummaryPL.load(page);
});

$(document).ready(function(){
    SummaryPL.init();
});
</script>