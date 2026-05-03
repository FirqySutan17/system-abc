<?php 
$userPlants = json_decode($this->session->userdata('plant'), true);
?>

<div class="row mb-3 align-items-end">

    <!-- PLANT -->
    <div class="col-md-2">
        <label class="form-label">Plant</label>
        <select id="ib_filter_plant" class="form-control">
            <?php foreach($plants as $p): ?>
                <?php if(in_array($p->CODE, $userPlants)): ?>
                    <option value="<?= $p->CODE ?>">
                        <?= $p->CODE_NAME ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ITEM -->
    <div class="col-md-4">
        <label class="form-label">Item No / Nama</label>
        <input type="text" id="ib_filter_item" class="form-control" placeholder="Search Item">
    </div>

    <!-- DATE FROM -->
    <div class="col-md-2">
        <label class="form-label">Date From</label>
        <input type="date" id="ib_date_from" class="form-control">
    </div>

    <!-- DATE TO -->
    <div class="col-md-2">
        <label class="form-label">Date To</label>
        <input type="date" id="ib_date_to" class="form-control">
    </div>

    <!-- FILTER -->
    <div class="col-md-1">
        <label class="form-label d-block">&nbsp;</label>
        <button class="btn btn-primary w-100" id="ib_btnFilter">
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
                    <a class="dropdown-item" href="#" id="ib_exportExcel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="ib_exportPDF">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<div class="table-responsive">
    <table class="table table-striped table-hover" id="ibTable">
        <thead>
            <tr>
                <th rowspan="2" style="text-align:center; vertical-align: middle">PLANT</th>
                <th rowspan="2" style="text-align:center; vertical-align: middle">ITEM</th>
                <th colspan="2" style="text-align:center">BEGIN</th>
                <th colspan="2" style="text-align:center">IN</th>
                <th colspan="2" style="text-align:center">OUT</th>
                <th colspan="2" style="text-align:center">END</th>
            </tr>
            <tr>
                <th style="text-align:center">QTY</th>
                <th style="text-align:center">BW</th>
                <th style="text-align:center">QTY</th>
                <th style="text-align:center">BW</th>
                <th style="text-align:center">QTY</th>
                <th style="text-align:center">BW</th>
                <th style="text-align:center">QTY</th>
                <th style="text-align:center">BW</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="d-flex justify-content-between mt-3">
    <div id="ib_info"></div>
    <div id="ib_pagination"></div>
</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
</style>

<script>
    function formatDecimal(x) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(x || 0);
    }

    window.ItemBalance = {

        loaded: false,
        timer: null,

        init() {
            if (this.loaded) return;
            this.loaded = true;
            this.initFilter();
            this.bindEvent();
            this.load();
        },

        initFilter() {
            $('#ib_filter_plant').select2({ width: '100%' });

            const today = new Date();
            $('#ib_date_to').val(today.toISOString().slice(0,10));

            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#ib_date_from').val(firstDay.toISOString().slice(0,10));

            // pilih plant pertama sebagai default
            const firstPlant = $('#ib_filter_plant option:first').val();
            $('#ib_filter_plant').val(firstPlant).trigger('change');
        },

        bindEvent() {
            $('#ib_btnFilter').on('click', () => this.load(1));

            $('#ib_filter_item').on('keyup', () => {
                clearTimeout(this.timer);
                this.timer = setTimeout(() => this.load(1), 300);
            });

            $('#ib_filter_plant, #ib_date_from, #ib_date_to')
                .on('change', () => this.load(1));

            $('#ib_exportExcel').on('click', e => {
                e.preventDefault();
                const params = $.param({
                    plant     : $('#ib_filter_plant').val(),
                    item      : $('#ib_filter_item').val(),
                    date_from : $('#ib_date_from').val(),
                    date_to   : $('#ib_date_to').val()
                });
                window.open('<?= base_url("report-production/export_excel_item_balance"); ?>?' + params, '_blank');
            });

            $('#ib_exportPDF').on('click', e => {
                e.preventDefault();
                const params = $.param({
                    plant     : $('#ib_filter_plant').val(),
                    item      : $('#ib_filter_item').val(),
                    date_from : $('#ib_date_from').val(),
                    date_to   : $('#ib_date_to').val()
                });
                window.open('<?= base_url("report-production/export_pdf_item_balance"); ?>?' + params, '_blank');
            });
        },

        load(page = 1) {
            const params = {
                page,
                limit: 10,
                plant: $('#ib_filter_plant').val(),
                item: $('#ib_filter_item').val(),
                date_from: $('#ib_date_from').val(),
                date_to: $('#ib_date_to').val()
            };

            $.get('<?= base_url("report-production/load_item_balance"); ?>', params, resp => {
                this.render(resp.rows || []);
                $('#ib_pagination').html(resp.pagination || '');
                $('#ib_info').text(`Total data : ${resp.total || 0}`);
            }, 'json');
        },

        render(rows) {
            const tbody = $('#ibTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="10" class="text-center">No data found</td></tr>');
                return;
            }

            rows.forEach(r => {
                tbody.append(`
                    <tr>
                        <td class="text-center detail-row" style="vertical-align: middle"><b>${r.plant_name}</b></td>
                        <td class="text-center detail-row" style="vertical-align: middle">
                            ${r.item_name}<br><b>${r.item}</b>
                        </td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.BEGIN_QTY)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.BEGIN_BW)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.IN_QTY)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.IN_BW)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.OUT_QTY)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.OUT_BW)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.END_QTY)}</td>
                        <td class="text-end detail-row" style="vertical-align: middle">${formatDecimal(r.END_BW)}</td>
                    </tr>
                `);
            });
        }
    };

    $(document).ready(() => ItemBalance.init());

    $(document).on('click', '#ib_pagination a', function(e){
        e.preventDefault();
        const page = $(this).data('page');
        if(page) ItemBalance.load(page);
    });
</script>


