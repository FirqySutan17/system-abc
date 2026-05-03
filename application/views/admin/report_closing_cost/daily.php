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
        <button class="btn btn-primary w-100" id="btnFilter" style="margin-top:-23px">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
    </div>

    <div class="col-md-4">
    </div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button class="btn btn-primary w-100" data-bs-toggle="dropdown" style="margin-top:-23px">
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
    <table class="table table-bordered" id="dailyClosingCostTable">
        <thead>
            <tr>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">PLANT</th>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">DATE</th>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">ITEM</th>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">ITEM NAME</th>
                <th class="text-center" style="vertical-align: middle; white-space: nowrap">CLASS</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">QTY</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">KG</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">INDEX PRICE</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">INDEX AMOUNT</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">MARKET PRICE</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">MARKET AMOUNT</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">MODAL</th>
                <th class="text-end" style="vertical-align: middle; white-space: nowrap">MODAL AMOUNT</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="7" class="text-end" style="vertical-align: middle; white-space: nowrap">GRAND TOTAL</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-end" style="vertical-align: middle; white-space: nowrap" id="gt_modal_amt">0</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="info"></div>
    <div id="pagination"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
</style>

<script>
    var state = {
        page: 1,
        limit: 50
    };

    $(document).ready(function(){

        $('#filter_plant').select2({ width:'100%' });

        const today = new Date().toISOString().slice(0,10);
        $('#filter_date').val(today);

        $('#exportExcel').click(function(e){
            e.preventDefault();
            const params = $.param({
                plant: $('#filter_plant').val(),
                date: $('#filter_date').val()
            });
            window.open('<?= base_url("report-closing-cost/export_excel_daily_closing_cost"); ?>?'+params,'_blank');
        });

        $('#exportPDF').click(function(e){
            e.preventDefault();
            const params = $.param({
                plant: $('#filter_plant').val(),
                date: $('#filter_date').val()
            });
            window.open('<?= base_url("report-closing-cost/export_pdf_daily_closing_cost"); ?>?'+params,'_blank');
        });

        loadPage();

        $('#btnFilter').click(()=>loadPage(1));

    });

    /* ================= UTIL ================= */

    function rupiah(x){
        return parseFloat(x||0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g,'.');
    }

    function formatDecimal(x){
        return parseFloat(x||0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    }

    function formatYMD(ymd){
        return ymd
            ? ymd.substr(6,2)+'/'+ymd.substr(4,2)+'/'+ymd.substr(0,4)
            : '-';
    }

    /* ================= LOAD ================= */

    function loadPage(p=1){
        state.page = p;

        const params = {
            page: p,
            limit: state.limit,
            plant: $('#filter_plant').val(),
            date: $('#filter_date').val()
        };

        $.get('<?= base_url("report-closing-cost/load_daily_closing_cost"); ?>', params, function(resp){

            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

            $('#dailyClosingCostTable tbody').empty();

            if(!resp.rows.length){
                $('#dailyClosingCostTable tbody').html(
                    '<tr><td colspan="13" class="text-center" style="vertical-align: middle; white-space: nowrap">No data</td></tr>'
                );
            }else{
                renderTable(resp.rows);
            }

            $('#gt_qty').text(formatDecimal(resp.grand.qty));
            $('#gt_bw').text(formatDecimal(resp.grand.bw));
            $('#gt_modal_amt').text(rupiah(resp.grand.amount));

            $('#pagination').html(resp.pagination);
            $('#info').html(
                `Showing ${(p-1)*state.limit+1} to ${Math.min(p*state.limit,resp.total)} of ${resp.total}`
            );

        });
    }

    /* ================= RENDER ================= */

    function renderTable(rows){
        rows.forEach(r=>{
            $('#dailyClosingCostTable tbody').append(`
                <tr>
                    <td class="text-center" style="vertical-align: middle; white-space: nowrap"><b>${r.plant_name || r.plant}</b></td>
                    <td class="text-center" style="vertical-align: middle; white-space: nowrap">${formatYMD(r.ymd)}</td>
                    <td class="text-center" style="vertical-align: middle; white-space: nowrap"><b>${r.item}</b></td>
                    <td class="text-center" style="vertical-align: middle; white-space: nowrap">${r.item_name}</td>
                    <td class="text-center" style="vertical-align: middle; white-space: nowrap">${r.class_name}</td>

                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${formatDecimal(r.qty)}</td>
                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${formatDecimal(r.kg)}</td>

                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${rupiah(r.harga)}</td>
                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${rupiah(r.amount)}</td>

                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${rupiah(r.trend_market)}</td>
                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${rupiah(r.amount_market)}</td>

                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${rupiah(r.modal)}</td>
                    <td class="text-end" style="vertical-align: middle; white-space: nowrap">${rupiah(r.amount_modal)}</td>
                </tr>
            `);
        });
    }

    /* ================= PAGINATION ================= */

    $(document).on('click','#pagination a',function(e){
        e.preventDefault();
        loadPage($(this).data('page'));
    });
</script>


