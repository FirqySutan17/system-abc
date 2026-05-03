<?php $userPlant = $this->session->userdata('plant'); ?>
<!-- <h5 class="card-title fw-semibold mb-4">REPORT PO - INVENTORY</h5> -->

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="mb_filter_plant" class="form-control" <?= ($this->session->userdata('role_id') != 1) ? 'disabled' : '' ?>>
            <?php foreach($plants as $p): ?>
                <option value="<?= $p->CODE ?>" <?= ($p->CODE==$userPlant)?'selected':'' ?>>
                    <?= $p->CODE_NAME ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- MATERIAL -->
    <div class="col-md-4">
        <label class="form-label">Material No / Nama</label>
        <input type="text" id="mb_filter_material" class="form-control" placeholder="Search Material">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="mb_date_from" class="form-control">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="mb_date_to" class="form-control">
    </div>

    <!-- FILTER BUTTON -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="mb_btnFilter">
            <i class="fa fa-search"></i> Filter
        </button>
    </div>

    <!-- EXPORT -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <div class="btn-group w-100">
            <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown">
                <i class="ti ti-download"></i>
            </button>
            <ul class="dropdown-menu w-100">
                <li>
                    <a class="dropdown-item" href="#" id="mb_exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="mb_exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<!-- TABLE -->
<div class="table-responsive">
    <table class="table table-striped table-hover" id="mbTable">
        <thead>
            <tr>
                <th rowspan="2" style="text-align:center; vertical-align: middle">PLANT</th>
                <th rowspan="2" style="text-align:center; vertical-align: middle">MATERIAL</th>
                <th colspan="2" style="text-align:center; vertical-align: middle">BEGIN</th>
                <th colspan="2" style="text-align:center; vertical-align: middle">IN</th>
                <th colspan="2" style="text-align:center; vertical-align: middle">OUT</th>
                <th colspan="2" style="text-align:center; vertical-align: middle">END</th>
            </tr>
            <tr>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">QTY</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">BW</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">QTY</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">BW</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">QTY</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">BW</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">QTY</th>
                <th style="text-align:center; vertical-align: middle; font-size: 13px">BW</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="mb_info"></div>
    <div id="mb_pagination"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
    #receiveLBTable {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }

    #receiveLBTable th,
    #receiveLBTable td {
        white-space: nowrap;
        word-wrap: break-word;
    }
</style>

<script>
    function formatDecimal(x) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(x || 0);
    }
    window.MaterialBalance = {

    loaded: false,

    init() {
        if (this.loaded) return;
        this.loaded = true;
        this.initFilter();
        this.bindEvent();
        this.load();
    },

    initFilter() {
        $('#mb_filter_plant').select2({width: '100%', allowClear: true});

        const today = new Date();
        const yyyyTo = today.getFullYear();
        const mmTo   = String(today.getMonth() + 1).padStart(2,'0');
        const ddTo   = String(today.getDate()).padStart(2,'0');
        $('#mb_date_to').val(`${yyyyTo}-${mmTo}-${ddTo}`);

        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const yyyyFrom = firstDay.getFullYear();
        const mmFrom   = String(firstDay.getMonth() + 1).padStart(2,'0');
        const ddFrom   = String(firstDay.getDate()).padStart(2,'0');
        $('#mb_date_from').val(`${yyyyFrom}-${mmFrom}-${ddFrom}`);
    },

    bindEvent() {
        $('#mb_btnFilter').on('click', () => this.load(1));

        $('#mb_filter_material').on('keyup', () => {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.load(1), 300);
        });

        $('#mb_filter_plant, #mb_date_from, #mb_date_to')
            .on('change', () => this.load(1));

        $('#mb_exportExcel').on('click', e => {
            e.preventDefault();
            const params = $.param({
                plant     : $('#mb_filter_plant').val(),
                material  : $('#mb_filter_material').val(),
                date_from : $('#mb_date_from').val(),
                date_to   : $('#mb_date_to').val()
            });
            window.open('<?= base_url("report-inventory/export_excel_material_balance"); ?>?' + params,'_blank');
        });

        $('#mb_exportPDF').on('click', e => {
            e.preventDefault();
            const params = $.param({
                plant     : $('#mb_filter_plant').val(),
                material  : $('#mb_filter_material').val(),
                date_from : $('#mb_date_from').val(),
                date_to   : $('#mb_date_to').val()
            });
            window.open('<?= base_url("report-inventory/export_pdf_material_balance"); ?>?' + params,'_blank');
        });
    },

    load(page = 1) {
        const params = {
            page,
            limit: 10,
            plant: $('#mb_filter_plant').val(),
            material: $('#mb_filter_material').val(),
            date_from: $('#mb_date_from').val(),
            date_to: $('#mb_date_to').val()
        };

        $.get('<?= base_url("report-inventory/load_material_balance"); ?>', params, resp => {
            this.render(resp.rows || []);
            $('#mb_pagination').html(resp.pagination || '');
            $('#mb_info').text(`Total data : ${resp.total || 0}`);
        }, 'json');
    },

    render(rows) {
        const tbody = $('#mbTable tbody').empty();

        if (!rows.length) {
            tbody.html('<tr><td colspan="10" class="text-center">No data found</td></tr>');
            return;
        }

        rows.forEach(r => {
            const tr = `<tr>
                <td style="text-align: center; vertical-align: middle" class="l">${r.plant_name}</td>
                <td style="text-align: center; vertical-align: middle" class="l">${r.material_name} <br> <b>${r.material}</b></td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.BEGIN_QTY)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.BEGIN_BW)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.IN_QTY)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.IN_BW)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.OUT_QTY)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.OUT_BW)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.END_QTY)}</td>
                <td style="text-align: right; vertical-align: middle" class="r">${formatDecimal(r.END_BW)}</td>
            </tr>`;
            tbody.append(tr);
        });
    }
};

$(document).ready(() => MaterialBalance.init());

$(document).on('click', '#mb_pagination a', function(e){
    e.preventDefault();
    const page = $(this).data('page');
    if(page) MaterialBalance.load(page);
});
</script>


