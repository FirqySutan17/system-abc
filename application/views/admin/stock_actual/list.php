<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">STOCK ACTUAL</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari Stock Actual..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group "></div>
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stockActualAdd">
                        <i class="ti ti-plus"></i> Tambah Stock Actual
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PLANT" style="text-align: center;">Plant</th>
                            <th data-order="STOCK_ACTUAL" style="text-align: center;">Stock Actual</th>
                            <th data-order="SA_DATE" style="text-align: center;">Tanggal</th>
                            <th data-order="SA_TIME" style="text-align: center;">Waktu</th>
                            <th data-order="REMARK" style="text-align: center;">Remark</th>
                            <th style="text-align: center;">#</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<style>
    .flex-inline {
        padding: 2px 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        align-content: center;
        flex-wrap: nowrap;
        flex-direction: row;
    }
    label {
        width: 35%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
    .modal-xl {
        --bs-modal-width: 90%;
    }
</style>

<!-- MODAL ADD STOCK ACTUAL -->
<div class="modal fade" id="stockActualAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fstockActualAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">STOCK ACTUAL - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" class="form-control" required></select>
                            <input type="hidden" name="PLANT" id="hiddenPlantAdd">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Stock Actual</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="SA_DATE" name="SA_DATE" type="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Waktu *</label>
                            <input id="SA_TIME" name="SA_TIME" type="time" class="form-control" required>
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">PIC SO *</label>
                            <input id="PIC" name="PIC" type="text" class="form-control" 
                                value="<?= $this->session->userdata('name'); ?>" required>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label">Remark</label>
                            <input name="REMARK" class="form-control" placeholder="Input disini..">
                        </div>
                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom: 0px">Detail Item</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addDetailRowAdd">Tambah Item</button>
                    </div>

                    <table class="table table-bordered" id="stockActualDetailTableAdd">
                        <thead>
                            <tr>
                                <th style="text-align:center;">Item</th>
                                <th style="text-align:center;">Stock Qty</th>
                                <th style="text-align:center;">Stock BW</th>
                                <th style="text-align:center;">Actual QTY</th>
                                <th style="text-align:center;">Actual BW</th>
                                <th style="text-align:center;">Margin QTY</th>
                                <th style="text-align:center;">Margin BW</th>
                                <th style="text-align:center;">Remark</th>
                                <th style="text-align:center; width:40px;">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT STOCK ACTUAL -->
<div class="modal fade" id="stockActualEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fstockActualEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">STOCK ACTUAL - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="PLANT_EDIT" name="PLANT_EDIT">
                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input id="PLANT_EDIT_DISPLAY"
                                class="form-control"
                                readonly
                                style="background:#efefef">
                            <input type="hidden" name="PLANT" id="PLANT_EDIT">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Stock Actual</label>
                            <input id="STOCK_ACTUAL_EDIT" class="form-control" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="SA_DATE_EDIT" name="SA_DATE" type="date" class="form-control" required readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Waktu *</label>
                            <input id="SA_TIME_EDIT" name="SA_TIME" type="time" class="form-control" required readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">PIC SO *</label>
                            <input id="PIC_EDIT" name="PIC" type="text" class="form-control" 
                                value="<?= $this->session->userdata('name'); ?>" required>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label">Remark</label>
                            <input id="REMARK_EDIT" name="REMARK" class="form-control" placeholder="Input disini..">
                        </div>
                    </div>

                    <!-- DETAIL -->
                    <h5 style="margin-bottom: 0px">Detail Item</h5>

                    <table class="table table-bordered" id="stockActualDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="text-align:center;">Item</th>
                                <th style="text-align:center;">Stock Qty</th>
                                <th style="text-align:center;">Stock BW</th>
                                <th style="text-align:center;">Actual QTY</th>
                                <th style="text-align:center;">Actual BW</th>
                                <th style="text-align:center;">Margin QTY</th>
                                <th style="text-align:center;">Margin BW</th>
                                <th style="text-align:center;">Remark</th>
                                <th style="text-align:center; width:40px;">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- DETAIL modal -->
<div class="modal fade" id="stockActualDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Stock Actual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="stockActualDetailBody">Loading...</div>
        </div>
    </div>
</div>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'STOCK_ACTUAL', dir: 'DESC' };

    $('#search').on('keyup', function(){
        state.search = $(this).val();
        loadPage(1);
    });

    function initPlantSelect2(selector, modalId){
        $(selector).select2({
            placeholder: "Pilih PLANT",
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('stock-actual/get_plant_by_user'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return { results: data };
                }
            }
        }).on('select2:select', function(e){
            $('#hiddenPlantAdd').val(e.params.data.id);

            // 🔥 RESET DETAIL (POLA PO)
            $('#stockActualDetailTableAdd tbody').empty();
            loadAllItemsToTable('#stockActualDetailTableAdd', e.params.data.id);
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '-';

        const d = new Date(dateString);
        if (isNaN(d)) return dateString;

        const day = String(d.getDate()).padStart(2, '0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        const month = months[d.getMonth()];
        const year = d.getFullYear();

        return `${day} ${month} ${year}`;
    }

    function loadPage(page = 1) {
        state.page = page;
        $.get('<?= base_url("stock-actual/load_data"); ?>', state, function(resp){
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var tbody = $('#table-body').empty();

            if (!resp.rows || !Array.isArray(resp.rows)) {
                tbody.html('<tr><td colspan="6" class="text-center text-danger">Data tidak tersedia</td></tr>');
                console.error('Invalid response:', resp);
                return;
            }

            resp.rows.forEach(function(row){
                var tr = `<tr>
                    <td style="text-align:center;"><b>${row.PLANT_NAME}</b></td>
                    <td style="text-align:center;"><b>#${row.STOCK_ACTUAL}</b></td>
                    <td style="text-align:center;">${formatDate(row.SA_DATE)}</td>
                    <td style="text-align:center;">${row.SA_TIME ?? '-'}</td>
                    <td style="text-align:center;">${row.REMARK ?? ''}</td>
                    <td style="text-align:center;">
                        <button class="btn btn-sm btn-primary me-1 exportStockActualPdf" data-stock_actual="${row.STOCK_ACTUAL}" data-plant="${row.PLANT}">
                            PDF
                        </button>
                        <button class="btn btn-sm btn-warning me-1 editBtn" data-id="${row.STOCK_ACTUAL}" data-plant="${row.PLANT}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.STOCK_ACTUAL}" data-plant="${row.PLANT}">Hapus</button>
                    </td>
                </tr>`;
                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            $('#info').text(`Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`);
        });
    }

    $('#stockActualAdd').on('shown.bs.modal', function () {
        loadAllItemsToTable('#stockActualDetailTableAdd');
    });

    $(document).on("click", ".exportStockActualPdf", function () {
        let stockActual = $(this).data("stock_actual");
        let plant       = $(this).data("plant");

        window.open(
            "<?= base_url('stock-actual/print_pdf'); ?>?stock_actual=" + stockActual + "&plant=" + plant,
            "_blank"
        );
    });

    function formatDecimalID(value, digit = 2) {
        return Number(value || 0).toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        });
    }

    function toNumberID(val) {
        return Number(val.replace(/\./g, '').replace(',', '.'));
    }

    function formatRupiahLive(value) {
        value = value.replace(/[^0-9]/g, '');
        return value ? Number(value).toLocaleString('id-ID') : '';
    }

    function formatDecimalLive(value, digit = 2) {

        value = value.replace(/[^\d,]/g, '');

        let parts = value.split(',');

        let intPart = parts[0];
        let decPart = parts[1] ? parts[1].substring(0, digit) : '';

        intPart = intPart.replace(/\./g, '');

        let formattedInt = Number(intPart || 0).toLocaleString('id-ID');

        if (decPart !== '') {
            return formattedInt + ',' + decPart;
        }

        return formattedInt;
    }

    function unformatNumber(value) {
        return Number(value.replace(/\./g, '').replace(',', '.')) || 0;
    }

    $(document).on('input', '.qty, .harga, .discount', function () {
        let cursor = this.selectionStart;
        let oldLength = this.value.length;

        this.value = formatRupiahLive(this.value);

        let newLength = this.value.length;
        this.selectionEnd = cursor + (newLength - oldLength);
    });

    $(document).on('input', '.berat', function () {
        let cursor = this.selectionStart;
        let oldLength = this.value.length;

        this.value = formatDecimalLive(this.value, 2);

        let newLength = this.value.length;
        this.selectionEnd = cursor + (newLength - oldLength);
    });

    $(document).on('input', '.qty, .harga, .discount', function () {
        let row = $(this).closest('tr');

        let qty      = unformatNumber(row.find('.qty').val());
        let harga    = unformatNumber(row.find('.harga').val());
        let discount = unformatNumber(row.find('.discount').val());

        let total = (qty * harga) - discount;
        row.find('.total').val(total.toLocaleString('id-ID'));
    });

    $('form').on('submit', function () {
        $('.qty, .berat, .harga, .discount, .total').each(function () {
            $(this).val(unformatNumber($(this).val()));
        });
    });

    $(document).on('input', '.actual_qty', function () {

        let cursor = this.selectionStart;
        let oldLength = this.value.length;

        this.value = formatDecimalLive(this.value, 2);

        let newLength = this.value.length;
        this.selectionEnd = cursor + (newLength - oldLength);

    });

    $(document).on('input', '.actual_bw', function () {
        let cursor = this.selectionStart;
        let oldLength = this.value.length;

        this.value = formatDecimalLive(this.value, 2);

        let newLength = this.value.length;
        this.selectionEnd = cursor + (newLength - oldLength);
    });

    function parseNumberID(value) {
        if (!value) return 0;
        return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
    }

    $(document).on('input', '.actual_qty, .actual_bw', function () {

        const row = $(this).closest('tr');
        hitungMarginRow(row);

        const stockQty  = parseNumberID(row.find('.stock_qty').val());
        const stockBw   = parseNumberID(row.find('.stock_bw').val());
        const actualQty = parseNumberID(row.find('.actual_qty').val());
        const actualBw  = parseNumberID(row.find('.actual_bw').val());

        const marginQty = actualQty - stockQty;
        const marginBw  = actualBw - stockBw;

        row.find('.margin_qty').val(formatDecimalID(marginQty));
        row.find('.margin_bw').val(formatDecimalID(marginBw));
    });

    function loadAllItemsToTable(targetTable, plant){

        if (!plant) {
            $(targetTable + ' tbody')
                .html('<tr><td colspan="9" class="text-center">Pilih plant terlebih dahulu</td></tr>');
            return;
        }

        $.getJSON(
            '<?= base_url("stock-actual/get_all_item"); ?>',
            { plant: plant },
            function (items) {

                const tbody = $(`${targetTable} tbody`);
                tbody.empty();

                if (!items || items.length === 0) {
                    tbody.html('<tr><td colspan="9" class="text-center">Data item tidak ditemukan</td></tr>');
                    return;
                }

                items.forEach(item => {
                    tbody.append(`
                        <tr>
                            <td style="width: 25%">
                                <input class="form-control item"
                                    value="${item.ITEM} - ${item.FULL_NAME}"
                                    readonly style="background:#efefef;">
                            </td>
                            <td><input class="form-control stock_qty text-end"
                                value="${formatDecimalID(item.stock_qty)}" readonly></td>
                            <td><input class="form-control stock_bw text-end"
                                value="${formatDecimalID(item.stock_bw)}" readonly></td>
                            <td><input class="form-control actual_qty text-end" value="0,00"></td>
                            <td><input class="form-control actual_bw text-end" value="0,00"></td>
                            <td><input class="form-control margin_qty text-end" readonly style="background:#efefef;" value="0,00"></td>
                            <td><input class="form-control margin_bw text-end" readonly style="background:#efefef;" value="0,00"></td>
                            <td><input class="form-control remark"></td>
                            <td>-</td>
                        </tr>
                    `);
                });
            }
        );
    }

    /* -------------------------
    Add / remove detail rows
    ------------------------- */
    function addDetailRow(data, targetTable) {

        let tbody = $(`${targetTable} tbody`);
        if (!tbody.length) return;

        data = data || {};

        let item   = data.item || "";
        let berat  = data.berat || "";
        let qty    = data.qty || "";
        let remark = data.remark || "";

        let modalParent = targetTable.includes('Edit')
            ? '#stockActualEdit'
            : '#stockActualAdd';

        let row = `
            <tr>
                <td>
                    <select class="form-control item-select" readonly></select>
                </td>
                <td><input style="text-align:right" class="form-control berat" value="${berat}"></td>
                <td><input style="text-align:right" class="form-control qty" value="${qty}"></td>
                <td><input class="form-control remark" value="${remark}"></td>
                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
        `;

        tbody.append(row);

        // Inisialisasi select2 untuk item
        let $select = tbody.find('.item-select').last();

        $select.select2({
            placeholder: "Pilih Item",
            width: '100%',
            dropdownParent: $(modalParent),
            ajax: {
                url: '<?= base_url("stockActual/get_item"); ?>',
                dataType: 'json',
                delay: 250,
                processResults: function(data){
                    return { results: data };
                }
            }
        });

        // Set value jika ada data
        if(item){
            // Ambil nama item dari server atau bisa cache
            $.get('<?= base_url("stockActual/get_item_by_id"); ?>', {id: item}, function(resp){
                let option = new Option(resp.text, resp.id, true, true);
                $select.append(option).trigger('change');
            }, 'json');
        }

        // Lock select supaya readonly
        $select.prop('disabled', true);
    }

    function calcRow(el){
        let tr = $(el).closest('tr');
        let qty   = parseFloat(tr.find('.qty').val()) || 0;
        let berat = parseFloat(tr.find('.berat').val()) || 0;
        // opsional kalkulasi lainnya jika diperlukan
    }

    function cleanNumber(val){

        if (!val) return 0;

        val = val.toString().trim();

        // jika format indonesia
        if (val.includes(',')) {
            val = val.replace(/\./g,''); // hapus ribuan
            val = val.replace(',', '.'); // koma jadi titik
        }

        return parseFloat(val) || 0;
    }

    function hitungMarginRow(row) {

        const stockQty = parseNumberID(row.find('.stock_qty').val());
        const stockBw  = parseNumberID(row.find('.stock_bw').val());

        let actualQty = parseNumberID(row.find('.actual_qty').val());
        let actualBw  = parseNumberID(row.find('.actual_bw').val());

        // 🔥 LOGIKA BISNIS
        // Jika actual = 0 → anggap sama dengan stock
        if (actualQty === 0) actualQty = stockQty;
        if (actualBw === 0)  actualBw  = stockBw;

        const marginQty = actualQty - stockQty;
        const marginBw  = actualBw - stockBw;

        row.find('.margin_qty').val(formatDecimalID(marginQty));
        row.find('.margin_bw').val(formatDecimalID(marginBw));
    }

    $(function(){
        loadPage(1);

        // add row
        initPlantSelect2('#plantAdd', '#stockActualAdd');
        $('#addDetailRowAdd').click(function(){ addDetailRow(null, '#stockActualDetailTableAdd'); });
        $('#addDetailRowEdit').click(function(){ addDetailRow({}, '#stockActualDetailTableEdit'); });

        // remove row
        $('#stockActualDetailTableAdd, #stockActualDetailTableEdit').on('click','.removeRow', function(){ $(this).closest('tr').remove(); });

        // Submit Add
        $('#fstockActualAdd').submit(function(e){

            e.preventDefault();

            let form = $(this);
            let btn  = form.find('button[type="submit"]');

            if (btn.prop('disabled')) return; // ✅ anti double click

            btn.prop('disabled', true).text('Menyimpan...');

            if (!$('#hiddenPlantAdd').val()) {
                alert('Plant wajib dipilih');
                btn.prop('disabled', false).text('Simpan');
                return;
            }

            var DETAIL = [];

            $('#stockActualDetailTableAdd tbody tr').each(function(){

                var val = $(this).find('.item').val();
                var split = val.split(' - ');

                DETAIL.push({
                    ITEM: split[0]?.trim() || '',
                    ITEM_NAME: split[1]?.trim() || '',
                    ACTUAL_QTY: cleanNumber($(this).find('.actual_qty').val()),
                    ACTUAL_BERAT: cleanNumber($(this).find('.actual_bw').val()),
                    REMARK: $(this).find('.remark').val() || ''
                });

            });

            $.post('<?= base_url("stock-actual/create"); ?>', {
                PLANT: $('#hiddenPlantAdd').val(),
                SA_DATE: $('#SA_DATE').val(),
                PIC: $('#PIC').val(),
                SA_TIME: $('#SA_TIME').val(),
                REMARK: $('input[name="REMARK"]').val(),
                DETAIL: DETAIL
            }, function(resp){

                resp = typeof resp==='string'?JSON.parse(resp):resp;

                alert(resp.message);

                if(resp.status){
                    $('#stockActualAdd').modal('hide');
                    $('#fstockActualAdd')[0].reset();
                    $('#stockActualDetailTableAdd tbody').empty();
                    loadPage(state.page);
                }

            }, 'json')

            .always(function(){
                btn.prop('disabled', false).text('Simpan'); // ✅ unlock
            });

        });

        // Click edit
        $(document).on('click','.editBtn', function(){
            const stockActual = $(this).data('id');
             const plant       = $(this).data('plant');
            $.get('<?= base_url("stock-actual/get_stock_for_edit"); ?>',
            { stock_actual: stockActual, plant: plant },
            function(resp){
                resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
                if(!resp.status){
                    alert(resp.message);
                    return;
                }
                const PLANT = resp.header.PLANT;
                $('#STOCK_ACTUAL_EDIT').val(resp.header.STOCK_ACTUAL);
                $('#PLANT_EDIT').val(resp.header.PLANT);
                $('#PLANT_EDIT_DISPLAY').val(
                    resp.header.PLANT + ' - ' + resp.header.AJ_NAME
                );
                $('#SA_DATE_EDIT').val(resp.header.SA_DATE.substr(0,10));
                $('#SA_TIME_EDIT').val(resp.header.SA_TIME.substr(0,5));
                $('#PIC_EDIT').val(resp.header.PIC);
                $('#REMARK_EDIT').val(resp.header.REMARK || '');

                const tbody = $('#stockActualDetailTableEdit tbody').empty();

                if (!resp.detail || !Array.isArray(resp.detail)) {
                    alert('Data detail tidak valid atau kosong');
                    console.error('RESP DETAIL ERROR:', resp);
                    return;
                }

                resp.detail.forEach(item => {
                    const row = $(`
                        <tr data-seq="${item.SEQ_NO}">
                            <td style="width: 22%">
                                <input class="form-control item"
                                    value="${item.ITEM} - ${item.FULL_NAME}"
                                    readonly style="background:#efefef">
                            </td>
                            <td>
                                <input class="form-control stock_qty text-end"
                                    value="${formatDecimalID(item.STOCK_QTY)}"
                                    readonly style="background:#efefef">
                            </td>
                            <td>
                                <input class="form-control stock_bw text-end"
                                    value="${formatDecimalID(item.STOCK_BERAT)}"
                                    readonly style="background:#efefef">
                            </td>
                            <td>
                                <input class="form-control actual_qty text-end"
                                    value="${formatDecimalID(item.ACTUAL_QTY)}">
                            </td>
                            <td>
                                <input class="form-control actual_bw text-end"
                                    value="${formatDecimalID(item.ACTUAL_BERAT)}">
                            </td>
                            <td>
                                <input class="form-control margin_qty text-end"
                                    readonly style="background:#efefef">
                            </td>
                            <td>
                                <input class="form-control margin_bw text-end"
                                    readonly style="background:#efefef">
                            </td>
                            <td>
                                <input class="form-control remark"
                                    value="${item.REMARK || ''}" placeholder="Remark disini..">
                            </td>
                            <td class="text-center">
                                -
                            </td>
                        </tr>
                    `);
                    tbody.append(row);

                    const stockQty = parseNumberID(row.find('.stock_qty').val());
                    const stockBw  = parseNumberID(row.find('.stock_bw').val());

                    if (parseNumberID(row.find('.actual_qty').val()) === 0) {
                        row.find('.actual_qty').val(formatDecimalID(stockQty));
                    }

                    if (parseNumberID(row.find('.actual_bw').val()) === 0) {
                        row.find('.actual_bw').val(formatDecimalID(stockBw));
                    }

                    hitungMarginRow(row);
                });

                $('#stockActualEdit').modal('show');
            });
        });

        // Submit edit
        $('#fstockActualEdit').submit(function(e){

            e.preventDefault();

            let form = $(this);
            let btn  = form.find('button[type="submit"]');

            if (btn.prop('disabled')) return;

            btn.prop('disabled', true).text('Updating...');

            var DETAIL = [];

            $('#stockActualDetailTableEdit tbody tr').each(function(){

                var val = $(this).find('.item').val() || '';
                var split = val.split(' - ');

                DETAIL.push({
                    SEQ_NO: $(this).data('seq') || null,
                    ITEM: split[0]?.trim() || '',
                    ITEM_NAME: split[1]?.trim() || '',
                    ACTUAL_QTY: cleanNumber($(this).find('.actual_qty').val()),
                    ACTUAL_BERAT: cleanNumber($(this).find('.actual_bw').val()),
                    REMARK: $(this).find('.remark').val() || ''
                });

            });

            $.post('<?= base_url("stock-actual/update"); ?>',{

                PLANT: $('#PLANT_EDIT').val(),
                STOCK_ACTUAL: $('#STOCK_ACTUAL_EDIT').val(),
                SA_DATE: $('#SA_DATE_EDIT').val(),
                SA_TIME: $('#SA_TIME_EDIT').val(),
                PIC: $('#PIC_EDIT').val(),
                REMARK: $('#REMARK_EDIT').val(),
                DETAIL: DETAIL

            }, function(resp){

                resp = typeof resp==='string'?JSON.parse(resp):resp;

                alert(resp.message);

                if(resp.status){
                    $('#stockActualEdit').modal('hide');
                    loadPage(state.page);
                }

            }, 'json')

            .always(function(){
                btn.prop('disabled', false).text('Update');
            });

        });

        // Detail modal
        $(document).on('click', '.detailBtn', function(){
            var id = $(this).data('id');
            $('#stockActualDetailBody').html('Loading...');
            $.get('<?= base_url("stock-actual/detail"); ?>',{id: id}, function(resp){
                $('#stockActualDetailBody').html(resp);
            });
            $('#stockActualDetail').modal('show');
        });

        // Delete
        $(document).on('click', '.deleteBtn', function() {

            const stockActual = $(this).data('id');
            const plant       = $(this).data('plant'); // 🔑 WAJIB

            if (!confirm("Yakin ingin menghapus STOCK ACTUAL: " + stockActual + " ?")) return;

            $.post("<?= base_url('stock-actual/remove'); ?>", {
                stock_actual: stockActual,
                plant: plant
            }, function(res){
                res = typeof res === 'string' ? JSON.parse(res) : res;
                alert(res.message);
                if(res.status) loadPage(state.page);
            });
        });

    }); // end ready

    $('#stockActualAdd').on('shown.bs.modal', function () {
        let today = new Date().toISOString().split("T")[0];
        $('#SA_DATE').val(today); // hari ini
        let now = new Date();
        $('#SA_TIME').val(now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0'));
    });
</script>

