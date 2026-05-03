<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<div class="row mb-3">
    <div class="col-md-3">
        <label class="form-label">Plant</label>
        <select id="sa_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Date</label>
        <input type="date" id="sa_date" class="form-control">
    </div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="sa_btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-4"></div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li><a class="dropdown-item" href="#" id="sa_exportExcel">
                    <i class="fa fa-file-excel"></i> Export Excel</a></li>
                <li><a class="dropdown-item" href="#" id="sa_exportPDF">
                    <i class="fa fa-file-pdf"></i> Export PDF</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered" id="stockActualTable">
    <thead>
        <tr>
            <th rowspan="2" class="text-center">PLANT</th>
            <th rowspan="2" class="text-center">ITEM</th>
            <th colspan="2" class="text-center">SYSTEM</th>
            <th colspan="2" class="text-center">ADJUST</th>
            <th colspan="2" class="text-center">FINAL</th>
        </tr>
        <tr>
            <th class="text-center">QTY</th>
            <th class="text-center">BW</th>
            <th class="text-center">QTY</th>
            <th class="text-center">BW</th>
            <th class="text-center">QTY</th>
            <th class="text-center">BW</th>
        </tr>
    </thead>

    <tbody></tbody>

    <tfoot>
        <tr>
            <th colspan="6" class="text-end detail-row" style="vertical-align: middle">TOTAL:</th>
            <th class="text-end detail-row" style="vertical-align: middle" id="totalFinalQty">0.00</th>
            <th class="text-end detail-row" style="vertical-align: middle" id="totalFinalBW">0.00</th>
        </tr>
    </tfoot>
</table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="sa_info"></div>
    <div id="sa_pagination"></div>
</div>

<script>
    window.StockActualReport = {
        loaded:false, page:1,

        init() {
            if(this.loaded) return;
            this.loaded = true;
            this.initFilter();
            this.bindEvent();
            this.load();
        },

        initFilter() {
            $('#sa_filter_plant').select2({width:'100%'});

            const today = new Date();
            $('#sa_date').val(today.toISOString().slice(0,10));
        },

        bindEvent() {
            $('#sa_btnFilter').on('click', ()=>{ this.page=1; this.load(); });
            $('#sa_filter_plant,#sa_date_from,#sa_date_to').on('change', ()=>{
                this.page=1; this.load();
            });

            $('#sa_exportExcel').on('click', e=>{
                e.preventDefault();
                window.open('<?= base_url("report-production/export_excel_stock_actual"); ?>?'+$.param(this.params()));
            });

            $('#sa_exportPDF').on('click', e=>{
                e.preventDefault();
                window.open('<?= base_url("report-production/export_pdf_stock_actual"); ?>?'+$.param(this.params()));
            });
        },

        params() {
            return {
                page: this.page,
                limit: 50,
                plant: $('#sa_filter_plant').val(),
                date: $('#sa_date').val()
            };
        },

        load(page=null) {
            if(page!==null) this.page=page;
            $.get('<?= base_url("report-production/load_stock_actual"); ?>',
                this.params(),
                resp=>{
                    this.render(resp.rows || []);
                    $('#sa_pagination').html(resp.pagination || '');
                    $('#sa_info').text(`Total data: ${resp.total || 0}`);
                },
            'json');
        },

        render(rows){
            const tbody = $('#stockActualTable tbody').empty();
            let totalQty = 0, totalBW = 0;

            if(!rows.length){
                tbody.html('<tr><td colspan="9" class="text-center">No data found</td></tr>');
            }else{
                rows.forEach(r=>{
                    let fq = parseFloat(r.FINAL_QTY || 0);
                    let fb = parseFloat(r.FINAL_BERAT || 0);

                    totalQty += fq;
                    totalBW  += fb;

                    tbody.append(`
                        <tr>
                            <td class="text-center detail-row" style="vertical-align: center"><b>${r.PLANT_NAME}</b></td>
                            <td class="text-center detail-row" style="vertical-align: center">${r.ITEM_NAME} <br> <b>${r.ITEM}</b></td>
                            <td class="text-end detail-row" style="vertical-align: center">${this.decimal(r.SYSTEM_QTY)}</td>
                            <td class="text-end detail-row" style="vertical-align: center">${this.decimal(r.SYSTEM_BW)}</td>
                            <td class="text-end detail-row" style="vertical-align: center">${this.decimal(r.ADJUST_QTY)}</td>
                            <td class="text-end detail-row" style="vertical-align: center">${this.decimal(r.ADJUST_BW)}</td>
                            <td class="text-end detail-row" style="vertical-align: center">${this.decimal(fq)}</td>
                            <td class="text-end detail-row" style="vertical-align: center">${this.decimal(fb)}</td>
                        </tr>
                    `);
                });
            }

            $('#totalFinalQty').text(this.decimal(totalQty));
            $('#totalFinalBW').text(this.decimal(totalBW));
        },

        decimal(x){
            return parseFloat(x || 0).toLocaleString('id-ID',{
                minimumFractionDigits:2,
                maximumFractionDigits:2
            });
        }
    };

    $(document).on('click','#sa_pagination a',function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if(page) StockActualReport.load(page);
    });

    $(document).ready(()=>{ StockActualReport.init(); });
</script>