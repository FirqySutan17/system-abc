<?php $userPlant = $this->session->userdata('plant'); ?>

<!-- <h5 class="card-title fw-semibold mb-4">REPORT PO - INVENTORY</h5> -->

<!-- SEARCH + ADD -->
<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="filter_plant" class="form-control">
            <option value="">-- All Plant --</option>
            <?php foreach ($plants as $p): ?>
                <option value="<?= $p->CODE ?>">
                    <?= $p->CODE_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- SUPPLIER -->
    <div class="col-md-2">
        <label class="form-label">Supplier</label>
        <select id="filter_supplier" class="form-control">
            <option value=""></option> <!-- WAJIB untuk allowClear -->
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s->CUST ?>">
                    <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- PO NUMBER -->
    <div class="col-md-2">
        <label class="form-label">No PO</label>
        <input type="text" id="filter_po" class="form-control" placeholder="Search PO">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="date_from" class="form-control" placeholder="dd/mm/yyyy">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="date_to" class="form-control" placeholder="dd/mm/yyyy">
    </div>

    <!-- BUTTON FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="btnFilter" style="margin-top: -23px">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown" aria-expanded="false" style="margin-top: -23px">
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
    <table class="table table-bordered" id="poReportTable">
        <thead>
            <tr>
                <th style="text-align: center">PLANT</th>
                <th style="text-align: center">DATE</th>
                <th style="text-align: center">NO. PO</th>
                <th style="text-align: center">SUPPLIER</th>
                <th style="text-align: center">MATERIAL</th>
                <th style="text-align: center">JUMLAH</th>
                <th style="text-align: center">BERAT</th>
                <th style="text-align: center">HARGA</th>
                <th style="text-align: center">TOTAL</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="table-secondary fw-bold">
                <td colspan="5" class="text-end detail-row" style="vertical-align: middle">GRAND TOTAL</td>
                <td class="text-end detail-row" style="vertical-align: middle" id="gt_jumlah">0.00</td>
                <td class="text-end detail-row" style="vertical-align: middle" id="gt_berat">0.00</td>
                <td class="text-end detail-row" style="vertical-align: middle"></td>
                <td class="text-end detail-row" style="vertical-align: middle" id="gt_total">0</td>
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
        limit: 10,
        order: 'PO_DATE',
        dir: 'DESC',
        plant: $('#filter_plant').val(),
        supplier: '',
        po: '',
        date_from: '',
        date_to: ''
    };

    let typingTimer;
    const typingDelay = 300; // ms

    $('#filter_po').on('keyup', function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            state.page = 1;
            state.po   = $('#filter_po').val();
            loadPage(1);
        }, typingDelay);
    });

    $('#filter_po').on('input', function () {
        if ($(this).val() === '') {
            state.page = 1;
            state.po   = '';
            loadPage(1);
        }
    });

    $('#btnFilter').on('click', function () {
        state.page      = 1;
        state.plant     = $('#filter_plant').val();
        state.supplier  = $('#filter_supplier').val();
        state.po        = $('#filter_po').val();
        state.date_from = $('#date_from').val();
        state.date_to   = $('#date_to').val();
        loadPage(1);
    });

    $('#search').on('keyup', function(){
        state.search = $(this).val();
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

        /* =====================
        USER INFO
        ===================== */
        const roleId    = '<?= $this->session->userdata("role_id"); ?>';
        const userPlant = '<?= $this->session->userdata("plant"); ?>';

        /* =====================
        INIT SELECT2 PLANT
        ===================== */
        $('#filter_plant').select2({
            width: '100%',
            placeholder: '-- ALL PLANT --',
            allowClear: roleId === '1'
        });

        if (roleId !== '1') {
            $('#filter_plant')
                .val(userPlant)
                .trigger('change')
                .prop('disabled', true);
        } else {
            $('#filter_plant').val('').trigger('change');
        }

        /* =====================
        INIT SELECT2 SUPPLIER
        ===================== */
        $('#filter_supplier').select2({
            width: '100%',
            placeholder: '-- ALL SUPPLIER --',
            allowClear: true
        });

        $('#filter_plant').on('change', function () {
            loadPage();
        });

        // DATE CHANGE
        $('#date_from, #date_to').on('change', function () {
            loadPage();
        });

        /* =====================
        AUTO LOAD DATA
        ===================== */
        loadPage();
    });

    /* =========================
    UTIL
    ========================= */
    function formatDate(dateString){
        if(!dateString) return '-';
        const d = new Date(dateString);
        const day = String(d.getDate()).padStart(2,'0');
        const month = String(d.getMonth()+1).padStart(2,'0');
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatRupiah(x){
        let n = parseFloat(x || 0).toFixed(0).toString().split('.');
        return n[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function formatDecimal(x){
        return parseFloat(x || 0).toFixed(2);
    }

    function getExportParams() {
        return $.param({
            plant     : $('#filter_plant').val(),
            supplier  : $('#filter_supplier').val(),
            po        : $('#filter_po').val(),
            date_from : $('#date_from').val(),
            date_to   : $('#date_to').val()
        });
    }

    $('#exportExcel').on('click', function(e){
        e.preventDefault();
        window.open('<?= base_url("report-inventory/export_excel_po"); ?>?' + getExportParams(), '_blank');
    });

    $('#exportPDF').on('click', function(e){
        e.preventDefault();
        window.open('<?= base_url("report-inventory/export_pdf_po"); ?>?' + getExportParams(), '_blank');
    });

    function renderGrandTotal(grand){
        if(!grand) return;

        $('#gt_jumlah').text(formatDecimal(grand.jumlah));
        $('#gt_berat').text(formatDecimal(grand.berat));
        $('#gt_total').text(formatRupiah(grand.total));
    }

    /* =========================
    LOAD DATA
    ========================= */
    function loadPage(p = 1) {
        state.page = p;
        const params = {
            page      : state.page,
            limit     : state.limit,
            plant     : $('#filter_plant').val(),
            supplier  : $('#filter_supplier').val(),
            po        : $('#filter_po').val(),
            date_from : $('#date_from').val(),
            date_to   : $('#date_to').val()
        };

        console.log('LOAD DATA PARAMS:', params);

        $.get('<?= base_url("report-inventory/load_data"); ?>', params, function (resp) {
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;

            const tbody = $('#poReportTable tbody').empty();

            if (!resp.rows || resp.rows.length === 0) {
                tbody.html('<tr><td colspan="9" class="text-center">No data found</td></tr>');
                renderGrandTotal({jumlah:0, berat:0, total:0});
            } else {
                renderPOReport(resp.rows);
            }

            renderGrandTotal(resp.grand);

            // 🔥 WAJIB DI LUAR IF
            $('#pagination').html(resp.pagination || '');
            $('#info').html(`Showing ${((state.page-1)*state.limit+1)} 
                to ${Math.min(state.page*state.limit, resp.total)} 
                of ${resp.total} entries`);
        });
    }

    function renderPOReport(items){
        const tbody = $('#poReportTable tbody').empty();

        const grouped = {};
        items.forEach(r=>{
            const key = r.PO + '|' + r.PLANT;
            if(!grouped[key]) grouped[key] = [];
            grouped[key].push(r);
        });

        Object.values(grouped).forEach(group=>{
            const rowspan = group.length;

            group.forEach((r, index)=>{
                let tr = '<tr>';

                if(index === 0){
                    tr += `
                        <td rowspan="${rowspan}" class="text-center detail-row" style="vertical-align: middle">${r.PLANT_NAME}</td>
                        <td rowspan="${rowspan}" class="text-center detail-row" style="vertical-align: middle">${formatDate(r.PO_DATE)}</td>
                        <td rowspan="${rowspan}" class="text-center detail-row" style="vertical-align: middle"><b>#${r.PO}</b></td>
                        <td rowspan="${rowspan}" class="text-center detail-row" style="vertical-align: middle">${r.SUPPLIER_NAME} <br> <b>${r.SUPPLIER}</b></td>
                    `;
                }

                tr += `
                    <td class="text-center detail-row">${r.MATERIAL_NAME} <br> <b>${r.MATERIAL}</b></td>
                    <td class="text-end detail-row">${formatDecimal(r.JUMLAH)}</td>
                    <td class="text-end detail-row">${formatDecimal(r.BERAT)}</td>
                    <td class="text-end detail-row">${formatRupiah(r.HARGA)}</td>
                    <td class="text-end detail-row">${formatRupiah(r.TOTAL)}</td>
                `;

                tr += '</tr>';
                tbody.append(tr);
            });
        });
    }

    $(document).on('click', '#pagination a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if(page){
            loadPage(page);
        }
    });

</script>


