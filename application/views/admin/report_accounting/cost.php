<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<!-- SEARCH + EXPORT -->
<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- COST -->
    <div class="col-md-2">
        <label class="form-label">Cost No</label>
        <input type="text" id="filter_cost" class="form-control" placeholder="Search Cost">
    </div>

    <!-- PEMBAYARAN -->
    <div class="col-md-2">
        <label class="form-label">Pembayaran</label>
        <input type="text" id="filter_pembayaran" class="form-control" placeholder="Search Pembayaran">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="date_from" class="form-control">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="date_to" class="form-control">
    </div>

    <!-- FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter" style="margin-top:-23px">
            <i class="fa fa-search"></i> Filter
        </button>
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

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-bordered" id="costReportTable">
        <thead>
            <tr>
                <th class="text-center">PLANT</th>
                <th class="text-center">DATE</th>
                <th class="text-center">COST</th>
                <th class="text-center">PEMBAYARAN</th>
                <th class="text-center">TIPE COST</th>
                <th class="text-center">JUMLAH</th>
                <th class="text-center">TOTAL</th>
                <th class="text-center">REMARK</th>
            </tr>
        </thead>
        <tbody></tbody>
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
/* =========================
STATE
========================= */
var state = {
    page: 1,
    limit: 10,
    plant: '',
    cost: '',
    pembayaran: '',
    date_from: '',
    date_to: ''
};

let typingTimer;
const typingDelay = 400;

/* =========================
FILTER HANDLING
========================= */
$('#filter_cost').on('keyup', function(){
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        state.page = 1;
        state.cost = $(this).val();
        loadPage(1);
    }, typingDelay);
});

$('#filter_pembayaran').on('keyup', function(){
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        state.page = 1;
        state.pembayaran = $(this).val();
        loadPage(1);
    }, typingDelay);
});

function updateState(){
    state.plant      = $('#filter_plant').val();
    state.cost       = $('#filter_cost').val();
    state.pembayaran = $('#filter_pembayaran').val();
    state.date_from  = $('#date_from').val();
    state.date_to    = $('#date_to').val();
}

$('#btnFilter').on('click', function(){
    state.page = 1;
    updateState();
    loadPage(1);
});

$('#filter_cost, #filter_pembayaran').on('keyup', function(){
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        state.page = 1;
        updateState();
        loadPage(1);
    }, typingDelay);
});

/* =========================
INIT
========================= */
$(document).ready(function(){

    // ===== Default Date Range (First day of month - Today)
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth()+1).padStart(2,'0');
    const dd = String(today.getDate()).padStart(2,'0');

    const first = new Date(today.getFullYear(), today.getMonth(), 1);

    state.date_from = `${first.getFullYear()}-${String(first.getMonth()+1).padStart(2,'0')}-${String(first.getDate()).padStart(2,'0')}`;
    state.date_to   = `${yyyy}-${mm}-${dd}`;

    $('#date_from').val(state.date_from);
    $('#date_to').val(state.date_to);

    // ===== Select2
    $('#filter_plant').select2({
        width:'100%',
        allowClear:true,
        placeholder:'-- ALL PLANT --'
    });

    loadPage();
});

/* =========================
UTIL
========================= */
function formatDate(d){
    if(!d) return '-';
    const x = new Date(d);
    return `${String(x.getDate()).padStart(2,'0')}/${String(x.getMonth()+1).padStart(2,'0')}/${x.getFullYear()}`;
}

function formatDecimal(x){
    return parseFloat(x||0).toLocaleString('id-ID',{minimumFractionDigits:2});
}

function getExportParams(){
    return $.param({
        plant: state.plant,
        cost: state.cost,
        pembayaran: state.pembayaran,
        date_from: state.date_from,
        date_to: state.date_to
    });
}

$('#exportExcel').click(e=>{
    e.preventDefault();
    updateState();
    window.open('<?= base_url("report-accounting/export_excel_cost"); ?>?'+getExportParams(),'_blank');
});

$('#exportPDF').click(e=>{
    e.preventDefault();
    updateState();
    window.open('<?= base_url("report-accounting/export_pdf_cost"); ?>?'+getExportParams(),'_blank');
});

/* =========================
LOAD DATA
========================= */
function loadPage(page=1){

    state.page = page;
    updateState();

    $.get('<?= base_url("report-accounting/load_cost"); ?>', {
        page: state.page,
        limit: state.limit,
        plant: state.plant,
        cost: state.cost,
        pembayaran: state.pembayaran,
        date_from: state.date_from,
        date_to: state.date_to
    }, function(resp){

        resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

        const tbody = $('#costReportTable tbody').empty();

        if(!resp.rows || resp.rows.length === 0){
            tbody.html('<tr><td colspan="8" class="text-center">No data found</td></tr>');
            $('#pagination').html('');
            $('#info').html('');
            return;
        }

        renderCostReport(resp.rows);

        $('#pagination').html(resp.pagination||'');
        $('#info').html(
            `Showing ${((state.page-1)*state.limit+1)} 
             to ${Math.min(state.page*state.limit, resp.total)} 
             of ${resp.total} entries`
        );
    });
}

/* =========================
RENDER
========================= */
function renderCostReport(items){

    const tbody = $('#costReportTable tbody').empty();
    const grouped = {};

    // GROUP DATA
    items.forEach(r=>{
        const key = r.COST+'|'+r.PLANT;
        if(!grouped[key]) grouped[key]=[];
        grouped[key].push(r);
    });

    Object.values(grouped).forEach(group=>{
        const h = group[0];
        const rowKey = (h.COST+'_'+h.PLANT).replace(/\W/g,'');

        // ===== HITUNG SUBTOTAL =====
        let subtotal = 0;
        group.forEach(r=>{
            subtotal += parseFloat(r.TOTAL || 0);
        });

        // ===== HEADER ROW =====
        tbody.append(`
            <tr class="cost-header" data-key="${rowKey}" style="cursor:pointer;background:#f8f9fa">
                <td class="text-center detail-row fw-bold"><b>${h.PLANT_NAME}</b></td>
                <td class="text-center detail-row">${formatDate(h.COST_DATE)}</td>
                <td class="text-center detail-row">
                    <i class="ti ti-chevron-right me-1 toggle-icon"></i>
                    <b>${h.COST}</b>
                </td>
                <td class="text-center detail-row">${h.PEMBAYARAN}</td>
                <td colspan="4" class="text-center detail-row">
                    <span class="text-muted fst-italic">
                        Click to view details (${group.length} item)
                    </span>
                    <span class="fw-bold ms-2">
                        | Subtotal : ${formatDecimal(subtotal)}
                    </span>
                </td>
            </tr>
        `);

        // ===== DETAIL ROWS =====
        group.forEach(r=>{
            tbody.append(`
                <tr class="cost-detail detail-${rowKey}" style="display:none">
                    <td class="detail-row"></td>
                    <td class="detail-row"></td>
                    <td class="detail-row"></td>
                    <td class="detail-row"></td>
                    <td class="detail-row text-start">${r.TIPE_COST_NAME}</td>
                    <td class="detail-row text-end">${formatDecimal(r.JUMLAH)}</td>
                    <td class="detail-row text-end">${formatDecimal(r.TOTAL)}</td>
                    <td class="detail-row text-start">${r.DETAIL_REMARK ?? ''}</td>
                </tr>
            `);
        });
    });
}

$(document).on('click','.cost-header',function(){
    const key = $(this).data('key');
    const d = $('.detail-'+key);
    const icon = $(this).find('.toggle-icon');

    if(d.is(':visible')){
        d.hide();
        icon.removeClass('ti-chevron-down').addClass('ti-chevron-right');
    } else {
        d.show();
        icon.removeClass('ti-chevron-right').addClass('ti-chevron-down');
    }
});

$(document).on('click','#pagination a.page-link',function(){
    loadPage(parseInt($(this).text()));
});
</script>