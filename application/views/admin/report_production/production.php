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

    <!-- PRODUCTION NO -->
    <div class="col-md-2">
        <label class="form-label">Production No</label>
        <input type="text" id="filter_production" class="form-control" placeholder="Search Production">
    </div>

    <!-- RECEIVE LB -->
    <div class="col-md-2">
        <label class="form-label">Receive LB</label>
        <input type="text" id="filter_receive_lb" class="form-control" placeholder="Search Receive LB">
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

    <!-- BUTTON FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter" style="margin-top: -23px">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown" aria-expanded="false" style="margin-top: -23px">
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
    <table class="table table-bordered" id="productionReportTable">
        <thead>
            <tr>
                <th style="text-align: center">PLANT</th>
                <th style="text-align: center">DATE</th>
                <th style="text-align: center">PRODUCTION</th>
                <th style="text-align: center">RECEIVE LB</th>
                <th style="text-align: center">ITEM</th>
                <th style="text-align: center">QTY</th>
                <th style="text-align: center">BERAT</th>
                <th style="text-align: center">REMARK</th>
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
    var state = {
        page: 1,
        limit: 10,
        order: 'PRODUCTION_DATE',
        dir: 'DESC',
        plant: $('#filter_plant').val(),
        production: '',
        receive_lb: '',
        date_from: '',
        date_to: ''
    };

    let typingTimer;
    const typingDelay = 300; // ms

    $('#filter_production').on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            state.page = 1;
            state.production = $('#filter_production').val();
            loadPage(1);
        }, typingDelay);
    });

    $('#filter_production').on('input', function () {
        if ($(this).val() === '') {
            state.page = 1;
            state.production = '';
            loadPage(1);
        }
    });

    $('#filter_receive_lb').on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            state.page = 1;
            state.receive_lb = $('#filter_receive_lb').val();
            loadPage(1);
        }, typingDelay);
    });

    $('#filter_receive_lb').on('input', function () {
        if ($(this).val() === '') {
            state.page = 1;
            state.receive_lb = '';
            loadPage(1);
        }
    });

    $('#btnFilter').on('click', function () {
        state.page        = 1;
        state.plant       = $('#filter_plant').val();
        state.production  = $('#filter_production').val();
        state.receive_lb  = $('#filter_receive_lb').val();
        state.date_from   = $('#date_from').val();
        state.date_to     = $('#date_to').val();
        loadPage(1);
    });

    $(document).ready(function () {

        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');

        const dateTo = `${yyyy}-${mm}-${dd}`;

        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const fy = firstDay.getFullYear();
        const fm = String(firstDay.getMonth() + 1).padStart(2, '0');
        const fd = String(firstDay.getDate()).padStart(2, '0');

        const dateFrom = `${fy}-${fm}-${fd}`;

        $('#date_from').val(dateFrom);
        $('#date_to').val(dateTo);

        $('#filter_plant').select2({
            width: '100%'
        });

        // Default pilih plant pertama yang tersedia
        const firstPlant = $('#filter_plant option:first').val();
        $('#filter_plant').val(firstPlant).trigger('change');

        loadPage();
    });

    /* =========================
    UTIL FUNCTIONS
    ========================= */
    function formatDate(dateString){
        if(!dateString) return '-';
        const d = new Date(dateString);
        return `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`;
    }

    function formatDecimal(x){
        return parseFloat(x || 0).toLocaleString('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    function getExportParams() {
        return $.param({
            plant: $('#filter_plant').val(),
            production: $('#filter_production').val(),
            receive_lb: $('#filter_receive_lb').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val()
        });
    }

    $('#exportExcel').on('click', function(e){
        e.preventDefault();
        window.open('<?= base_url("report-production/export_excel_production"); ?>?' + getExportParams(), '_blank');
    });

    $('#exportPDF').on('click', function(e){
        e.preventDefault();
        window.open('<?= base_url("report-production/export_pdf_production"); ?>?' + getExportParams(), '_blank');
    });

    /* =========================
    LOAD DATA
    ========================= */
    function loadPage(page = 1) {
        state.page = page;
        state.plant = $('#filter_plant').val();
        state.production = $('#filter_production').val();
        state.receive_lb = $('#filter_receive_lb').val();
        state.date_from = $('#date_from').val();
        state.date_to = $('#date_to').val();

        const params = {
            page: state.page,
            limit: state.limit,
            plant: state.plant,
            production: state.production,
            receive_lb: state.receive_lb,
            date_from: state.date_from,
            date_to: state.date_to
        };

        console.log('LOAD DATA PARAMS:', params);

        $.get('<?= base_url("report-production/load_production"); ?>', params, function(resp){
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            const tbody = $('#productionReportTable tbody').empty();
            if(!resp.rows || resp.rows.length === 0){
                tbody.html('<tr><td colspan="8" class="text-center">No data found</td></tr>');
                return;
            }
            renderProductionReport(resp.rows);
            $('#pagination').html(resp.pagination || '');
            $('#info').html(`Showing ${((state.page-1)*state.limit+1)} to ${Math.min(state.page*state.limit, resp.total)} of ${resp.total} entries`);
        });
    }

    function renderProductionReport(items){
        const tbody = $('#productionReportTable tbody').empty();
        const grouped = {};

        items.forEach(r=>{
            const key = r.PRODUCTION + '|' + r.PLANT;
            if(!grouped[key]) grouped[key] = [];
            grouped[key].push(r);
        });

        Object.entries(grouped).forEach(([key, group])=>{
            const header = group[0];
            const rowKey = key.replace(/\|/g,'_');

            // ===== HEADER ROW =====
            let headerRow = `
                <tr class="production-header" data-key="${rowKey}" style="cursor:pointer;background:#f8f9fa">
                    <td class="text-center detail-row fw-bold"><b>${header.PLANT_NAME}</b></td>
                    <td class="text-center detail-row">${formatDate(header.PRODUCTION_DATE)}</td>
                    <td class="text-center detail-row">
                        <i class="ti ti-chevron-right me-1 toggle-icon"></i>
                        <b>${header.PRODUCTION}</b>
                    </td>
                    <td class="text-center detail-row">${header.RECEIVE_LB}</td>
                    <td colspan="4" class="text-muted fst-italic text-center detail-row">
                        Click to view details (${group.length} item)
                    </td>
                </tr>
            `;
            tbody.append(headerRow);

            // ===== DETAIL ROWS =====
            group.forEach(r=>{
                let detailRow = `
                    <tr class="production-detail detail-${rowKey}" style="display:none">
                        <td class="detail-row"></td>
                        <td class="detail-row"></td>
                        <td class="detail-row"></td>
                        <td class="detail-row"></td>
                        <td class="text-start detail-row">
                            ${r.ITEM_NAME}<br><b>${r.ITEM}</b>
                        </td>
                        <td class="text-end detail-row">${formatDecimal(r.QTY)}</td>
                        <td class="text-end detail-row">${formatDecimal(r.BERAT)}</td>
                        <td class="text-start detail-row">${r.DETAIL_REMARK ?? ''}</td>
                    </tr>
                `;
                tbody.append(detailRow);
            });
        });
    }

    $(document).on('click', '.production-header', function(){
        const key = $(this).data('key');
        const details = $('.detail-' + key);
        const icon = $(this).find('.toggle-icon');

        if(details.is(':visible')){
            details.hide();
            icon.removeClass('ti-chevron-down').addClass('ti-chevron-right');
        } else {
            details.show();
            icon.removeClass('ti-chevron-right').addClass('ti-chevron-down');
        }
    });

    $(document).on('click', '#pagination a.page-link', function(){
        const page = $(this).text();
        loadPage(parseInt(page));
    });
</script>


