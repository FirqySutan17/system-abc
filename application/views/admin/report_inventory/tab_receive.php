<?php $userPlant = $this->session->userdata('plant'); ?>

    <!-- <h5 class="card-title fw-semibold mb-4">REPORT PO - INVENTORY</h5> -->

    <!-- FILTER -->
    <div class="row mb-3">

        <div class="col-md-2">
            <label class="form-label">Plant</label>
            <select id="rc_filter_plant" class="form-control" <?= ($this->session->userdata('role_id') != 1) ? 'disabled' : '' ?>>
            <?php foreach($plants as $p): ?>
                <option value="<?= $p->CODE ?>" <?= ($p->CODE==$userPlant)?'selected':'' ?>>
                    <?= $p->CODE_NAME ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Supplier</label>
            <select id="rc_filter_supplier" class="form-control">
                <option value="">-- ALL SUPPLIER --</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s->CUST ?>">
                        <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">No Receive / PO</label>
            <input type="text" id="rc_filter_receive" class="form-control" placeholder="Search Receive / PO">
        </div>

        <div class="col-md-2">
            <label class="form-label">Date From</label>
            <input type="date" id="rc_date_from" class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label">Date To</label>
            <input type="date" id="rc_date_to" class="form-control">
        </div>

        <div class="col-md-1">
            <label class="form-label d-block">&nbsp;</label>
            <button class="btn btn-primary w-100" id="rc_btnFilter">
                <i class="fa fa-search"></i> Filter
            </button>
        </div>

        <div class="col-md-1">
            <label class="form-label d-block">&nbsp;</label>
            <div class="btn-group w-100">
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-download"></i>
                </button>
                <ul class="dropdown-menu w-100">
                    <li>
                        <a class="dropdown-item" href="#" id="rc_exportExcel">
                            <i class="fa fa-file-excel"></i> Export Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="rc_exportPDF">
                            <i class="fa fa-file-pdf"></i> Export PDF
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-bordered" id="receiveTable">
            <thead>
                <tr>
                    <th class="text-center">PLANT</th>
                    <th class="text-center">DATE</th>
                    <th class="text-center">NO RECEIVE</th>
                    <th class="text-center">NO PO</th>
                    <th class="text-center">SUPPLIER</th>
                    <th class="text-center">MATERIAL</th>
                    <th class="text-center">JUMLAH</th>
                    <th class="text-center">BERAT</th>
                    <th class="text-center">HARGA</th>
                    <th class="text-center">TOTAL</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="6" class="text-end detail-row" style="vertical-align: middle">GRAND TOTAL</td>
                    <td class="text-end detail-row" style="vertical-align: middle" id="rc_gt_jumlah">0.00</td>
                    <td class="text-end detail-row" style="vertical-align: middle" id="rc_gt_berat">0.00</td>
                    <td class="text-end detail-row" style="vertical-align: middle"></td>
                    <td class="text-end detail-row" style="vertical-align: middle" id="rc_gt_total">0</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-between mt-3">
        <div id="info"></div>
        <div id="rc_pagination"></div>
    </div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
        vertical-align: middle !important;
    }
</style>

<script>
    window.ReceiveReport = {

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
            $('#rc_filter_plant, #rc_filter_supplier').select2({
                width: '100%',
                allowClear: true
            });

            const today = new Date();
            $('#rc_date_to').val(today.toISOString().slice(0,10));

            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#rc_date_from').val(firstDay.toISOString().slice(0,10));
        },

        bindEvent() {
            let timer = null;

            $('#rc_btnFilter').on('click', () => {
                this.page = 1;
                this.load();
            });

            $('#rc_filter_receive').on('keyup', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    this.page = 1;
                    this.load();
                }, 300);
            });

            $('#rc_filter_plant, #rc_filter_supplier, #rc_date_from, #rc_date_to')
                .on('change', () => {
                    this.page = 1;
                    this.load();
                });

            $('#rc_exportExcel').on('click', function(e){
                e.preventDefault();
                const params = $.param({
                    plant     : $('#rc_filter_plant').val(),
                    supplier  : $('#rc_filter_supplier').val(),
                    receive   : $('#rc_filter_receive').val(),
                    date_from : $('#rc_date_from').val(),
                    date_to   : $('#rc_date_to').val()
                });
                window.open('<?= base_url("report-inventory/export_excel_receive"); ?>?' + params);
            });

            $('#rc_exportPDF').on('click', function(e){
                e.preventDefault();
                const params = $.param({
                    plant     : $('#rc_filter_plant').val(),
                    supplier  : $('#rc_filter_supplier').val(),
                    receive   : $('#rc_filter_receive').val(),
                    date_from : $('#rc_date_from').val(),
                    date_to   : $('#rc_date_to').val()
                });
                window.open('<?= base_url("report-inventory/export_pdf_receive"); ?>?' + params);
            });
        },

        load(page = null) {

            if (page !== null) {
                this.page = page;
            }
            const params = {
                page      : this.page,
                limit     : 10,
                plant     : $('#rc_filter_plant').val(),
                supplier  : $('#rc_filter_supplier').val(),
                receive   : $('#rc_filter_receive').val(),
                date_from : $('#rc_date_from').val(),
                date_to   : $('#rc_date_to').val()
            };

            $.get('<?= base_url("report-inventory/load_receive"); ?>', params, resp => {
                this.render(resp.rows || []);
                this.renderGrand(resp.grand || {jumlah:0, berat:0, total:0});
                $('#rc_pagination').html(resp.pagination || '');
                $('#info').text(`Total data : ${resp.total || 0}`);
            }, 'json');
        },

        render(rows) {
            const tbody = $('#receiveTable tbody').empty();

            if (!rows.length) {
                tbody.html('<tr><td colspan="10" class="text-center">No data found</td></tr>');
                this.renderGrand({jumlah:0, berat:0, total:0});
                return;
            }

            const grouped = {};
            rows.forEach(r => {
                const key = r.RECEIVE + '|' + r.PLANT;
                grouped[key] = grouped[key] || [];
                grouped[key].push(r);
            });

            Object.values(grouped).forEach(group => {
                const rowspan = group.length;

                group.forEach((r,i) => {
                    let tr = '<tr>';

                    if (i === 0) {
                        tr += `
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.PLANT_NAME}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${this.date(r.RECEIVE_DATE)}</td>
                            <td rowspan="${rowspan}" class="detail-row text-center"><b>${r.RECEIVE}</b></td>
                            <td rowspan="${rowspan}" class="detail-row text-center">${r.PO}</td>
                            <td rowspan="${rowspan}" class="detail-row">${r.SUPPLIER_NAME}</td>
                        `;
                    }

                    tr += `
                        <td class="detail-row">${r.MATERIAL_NAME}</td>
                        <td class="detail-row text-end">${this.decimal(r.JUMLAH)}</td>
                        <td class="detail-row text-end">${this.decimal(r.BERAT)}</td>
                        <td class="detail-row text-end">${this.rupiah(r.HARGA)}</td>
                        <td class="detail-row text-end">${this.rupiah(r.TOTAL)}</td>
                    </tr>`;

                    tbody.append(tr);
                });
            });
        },

        renderGrand(grand) {
            if (!grand) return;

            $('#rc_gt_jumlah').text(this.decimal(grand.jumlah));
            $('#rc_gt_berat').text(this.decimal(grand.berat));
            $('#rc_gt_total').text(this.rupiah(grand.total));
        },

        date(d) {
            return d ? new Date(d).toLocaleDateString('id-ID') : '-';
        },

        rupiah(x) {
            return parseFloat(x||0).toLocaleString('id-ID');
        },

        decimal(x) {
            return parseFloat(x||0).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };

    $(document).on('click', '#rc_pagination a', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            ReceiveReport.load(page);
        }
    });
</script>


