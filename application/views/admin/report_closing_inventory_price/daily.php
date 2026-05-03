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
        <select id="filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if (in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date</label>
        <input type="date" id="filter_date" class="form-control">
    </div>
    
    <!-- FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-2">
    </div>

    <div class="col-md-3"></div>

    <!-- EXPORT -->
    <div class="col-md-2">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li>
                    <a class="dropdown-item" href="#" id="exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
<table class="table table-bordered" id="inventoryPriceTable">
    <thead>
        <tr>
            <th class="text-center" style="vertical-align: middle; white-space:nowrap">PLANT</th>
            <th class="text-center" style="vertical-align: middle; white-space:nowrap">MATERIAL</th>

            <th class="text-end" style="vertical-align: middle; white-space:nowrap">BG QTY</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">BG BW</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">BG AMOUNT</th>

            <th class="text-end" style="vertical-align: middle; white-space:nowrap">IN QTY</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">IN BW</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">IN AMOUNT</th>

            <th class="text-end" style="vertical-align: middle; white-space:nowrap">OUT QTY</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">OUT BW</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">OUT AMOUNT</th>

            <th class="text-end" style="vertical-align: middle; white-space:nowrap">END QTY</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">END BW</th>
            <th class="text-end" style="vertical-align: middle; white-space:nowrap">END AMOUNT</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr class="table-secondary fw-bold">
            <td colspan="2" class="text-end" style="vertical-align: middle; white-space:nowrap">GRAND TOTAL</td>

            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_bg_qty">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_bg_bw">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_bg_amount">0</td>

            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_in_qty">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_in_bw">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_in_amount">0</td>

            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_out_qty">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_out_bw">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_out_amount">0</td>

            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_end_qty">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_end_bw">0.00</td>
            <td class="text-end" style="vertical-align: middle; white-space:nowrap" id="gt_end_amount">0</td>
        </tr>
    </tfoot>
</table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="info"></div>
    <div id="pagination"></div>
</div>

<script>
    var state = { page:1, limit:10 };

    $(document).ready(function(){

        $('#filter_plant').select2({ width:'100%' });

        const today = new Date().toISOString().slice(0,10);
        $('#filter_date').val(today);

        loadPage();

        $('#btnFilter').click(()=>loadPage(1));

        $('#exportExcel').click(function(e){
            e.preventDefault();
            window.open(buildExportUrl('excel'),'_blank');
        });

        $('#exportPDF').click(function(e){
            e.preventDefault();
            window.open(buildExportUrl('pdf'),'_blank');
        });
    });

    /* ===== UTIL FORMAT ===== */
    function dec(x){
        return parseFloat(x||0).toLocaleString('id-ID',{minimumFractionDigits:2, maximumFractionDigits:2});
    }
    function rupiah(x){
        return parseFloat(x||0).toLocaleString('id-ID');
    }

    /* ===== EXPORT URL ===== */
    function buildExportUrl(type){
        const params = $.param({
            plant : $('#filter_plant').val(),
            date  : $('#filter_date').val()
        });

        if(type==='excel'){
            return '<?= base_url("report-closing-inventory-price/export_excel_daily_inventory_price"); ?>?'+params;
        }
        return '<?= base_url("report-closing-inventory-price/export_pdf_daily_inventory_price"); ?>?'+params;
    }

    /* ===== LOAD DATA ===== */
    function loadPage(p=1){
        state.page = p;

        $('#inventoryPriceTable tbody').html('<tr><td colspan="14" class="text-center" style="vertical-align: middle; white-space:nowrap">Loading...</td></tr>');

        $.get(
            '<?= base_url("report-closing-inventory-price/load_daily_inventory_price"); ?>',
            {
                page:p,
                limit:state.limit,
                plant:$('#filter_plant').val(),
                date:$('#filter_date').val()
            },
            function(resp){

                resp = typeof resp==='string'?JSON.parse(resp):resp;
                const tbody = $('#inventoryPriceTable tbody').empty();

                if(!resp.rows.length){
                    tbody.html('<tr><td colspan="14" class="text-center" style="vertical-align: middle; white-space:nowrap">No data</td></tr>');
                }else{
                    resp.rows.forEach(r=>{
                        tbody.append(`
                            <tr>
                                <td class="text-center" style="vertical-align: middle; white-space:nowrap"><b>${r.plant_name || r.plant}</b></td>
                                <td class="text-center" style="vertical-align: middle; white-space:nowrap">${r.material_name || '-'}<br><b>${r.material}</b></td>

                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${dec(r.bg_qty)}</td>
                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${dec(r.bg_bw)}</td>
                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${rupiah(r.bg_amount)}</td>

                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${dec(r.in_qty)}</td>
                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${dec(r.in_bw)}</td>
                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${rupiah(r.in_amount)}</td>

                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${dec(r.out_qty)}</td>
                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${dec(r.out_bw)}</td>
                                <td class="text-end" style="vertical-align: middle; white-space:nowrap">${rupiah(r.out_amount)}</td>

                                <td class="text-end fw-bold" style="vertical-align: middle; white-space:nowrap">${dec(r.end_qty)}</td>
                                <td class="text-end fw-bold" style="vertical-align: middle; white-space:nowrap">${dec(r.end_bw)}</td>
                                <td class="text-end fw-bold" style="vertical-align: middle; white-space:nowrap">${rupiah(r.end_amount)}</td>
                            </tr>
                        `);
                    });
                }

                // GRAND TOTAL
                const g = resp.grand || {};
                $('#gt_bg_qty').text(dec(g.bg_qty));
                $('#gt_bg_bw').text(dec(g.bg_bw));
                $('#gt_bg_amount').text(rupiah(g.bg_amount));

                $('#gt_in_qty').text(dec(g.in_qty));
                $('#gt_in_bw').text(dec(g.in_bw));
                $('#gt_in_amount').text(rupiah(g.in_amount));

                $('#gt_out_qty').text(dec(g.out_qty));
                $('#gt_out_bw').text(dec(g.out_bw));
                $('#gt_out_amount').text(rupiah(g.out_amount));

                $('#gt_end_qty').text(dec(g.end_qty));
                $('#gt_end_bw').text(dec(g.end_bw));
                $('#gt_end_amount').text(rupiah(g.end_amount));

                $('#pagination').html(resp.pagination||'');
                $('#info').html(`Total data : ${resp.total||0}`);
            }
        ).fail(()=>{
            $('#inventoryPriceTable tbody').html('<tr><td colspan="14" class="text-center text-danger">Failed to load data</td></tr>');
        });
    }

    /* ===== PAGINATION ===== */
    $(document).on('click','#pagination a',function(e){
        e.preventDefault();
        loadPage($(this).data('page'));
    });
</script>