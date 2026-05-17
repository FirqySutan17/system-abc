<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">SALES - INPUT</h5>

            <!-- SEARCH + ADD SALES -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari sales..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salesAdd">
                        <i class="ti ti-plus"></i> Tambah Sales
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PLANT" style="text-align:center;">Plant</th>
                            <th data-order="SALES" style="text-align:center;">No. Sales</th>
                            <th data-order="CUSTOMER" style="text-align:center;">Customer</th>
                            <th data-order="SALES_DATE" style="text-align:center;">Tanggal</th>
                            <th data-order="PEMBAYARAN" style="text-align:center;">Pembayaran</th>
                            <th data-order="JENIS_PAY" style="text-align:center;">Jenis Payment</th>
                            <th data-order="REMARK" style="text-align:center;">No. Nota / Ket.</th>
                            <th style="text-align:center;">#</th>
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
        width: 50%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
    .form-check.form-check-inline {
        width: 100%
    }
</style>

<!-- MODAL ADD SALES -->
<div class="modal fade" id="salesAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fsalesAdd" enctype="multipart/form-data">

            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" style="color: #fff">
                        SALES - TAMBAH
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <!-- ========================================= -->
                    <!-- HEADER -->
                    <!-- ========================================= -->

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-light fw-bold">
                            INFORMASI SALES
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <!-- PLANT -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Plant *
                                    </label>

                                    <select
                                        id="plantAdd"
                                        name="PLANT"
                                        class="form-control"
                                        required>
                                    </select>
                                </div>

                                <!-- SALES -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        No. Sales
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control bg-light"
                                        readonly
                                        placeholder="AUTO GENERATE">
                                </div>

                                <!-- DATE -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Tanggal *
                                    </label>

                                    <input
                                        type="date"
                                        name="SALES_DATE"
                                        class="form-control"
                                        required>
                                </div>

                                <!-- CUSTOMER -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Customer *
                                    </label>

                                    <select
                                        id="customerAdd"
                                        class="form-control"
                                        required>
                                    </select>

                                    <input
                                        type="hidden"
                                        name="CUSTOMER"
                                        id="hiddenCustomerAdd">
                                </div>

                                <!-- PAYMENT -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold d-block">
                                        Pembayaran
                                    </label>

                                    <div class="mt-2" style="display: flex; width: 100%; padding-top: 10px">

                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="PEMBAYARAN"
                                                value="CASH"
                                                checked>

                                            <label class="form-check-label">
                                                CASH
                                            </label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="PEMBAYARAN"
                                                value="TRANSFER">

                                            <label class="form-check-label">
                                                TRANSFER
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <!-- JENIS PAY -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold d-block">
                                        Jenis Pembayaran
                                    </label>

                                    <div class="mt-2" style="display: flex; width: 100%; padding-top: 10px">

                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="JENIS_PAY"
                                                value="LUNAS"
                                                checked>

                                            <label class="form-check-label">
                                                LUNAS
                                            </label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="JENIS_PAY"
                                                value="TEMPO">

                                            <label class="form-check-label">
                                                TEMPO
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <!-- DP -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        DP Amount
                                    </label>

                                    <input
                                        type="text"
                                        name="DP_AMOUNT"
                                        class="form-control text-end amount-field"
                                        placeholder="0">
                                </div>

                                <!-- NOTA -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        No. Nota
                                    </label>

                                    <input
                                        type="text"
                                        name="NOTA"
                                        class="form-control"
                                        placeholder="Opsional..">
                                </div>

                                <!-- ATTACH -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Attachment
                                    </label>

                                    <input
                                        type="file"
                                        name="ATTACHMENT"
                                        class="form-control"
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                </div>

                                <!-- REMARK -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">
                                        Remark
                                    </label>

                                    <textarea
                                        name="REMARK"
                                        class="form-control"
                                        placeholder="Opsional.."
                                        rows="2"></textarea>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- ========================================= -->
                    <!-- DETAIL -->
                    <!-- ========================================= -->

                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-light d-flex justify-content-between align-items-center">

                            <span class="fw-bold">
                                DETAIL ITEM
                            </span>

                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                id="addDetailRowAdd">

                                <i class="fa fa-plus me-1"></i>
                                Tambah Item

                            </button>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered align-middle" id="salesDetailTableAdd">

                                    <thead class="table-light">
                                        <tr>

                                            <th width="28%" class="text-center">
                                                MATERIAL
                                            </th>

                                            <th width="12%" class="text-center">
                                                JUMLAH
                                            </th>

                                            <th width="12%" class="text-center">
                                                BERAT
                                            </th>

                                            <th width="15%" class="text-center">
                                                HARGA
                                            </th>

                                            <th width="12%" class="text-center">
                                                DISCOUNT
                                            </th>

                                            <th width="18%" class="text-center">
                                                TOTAL
                                            </th>

                                            <th width="3%" class="text-center">
                                                #
                                            </th>

                                        </tr>
                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>

                            <div class="text-end mt-3">

                                <h4 class="fw-bold text-primary mb-0">
                                    GRAND TOTAL :
                                    <span id="grandTotalDisplay">
                                        0
                                    </span>
                                </h4>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fa fa-save me-1"></i>
                        Simpan Sales

                    </button>

                </div>

            </div>

        </form>
    </div>
</div>

<!-- MODAL EDIT SALES -->
<div class="modal fade" id="salesEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fsalesEdit" enctype="multipart/form-data">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">SALES - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- HEADER -->
                    <div class="row g-2 mb-3">

                        <!-- Plant (readonly) -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input id="PLANT_NAME_EDIT" class="form-control" readonly style="background:#efefef">
                            <input type="hidden" name="PLANT" id="PLANT_EDIT">
                        </div>

                        <!-- Sales No -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Sales</label>
                            <input id="SALES_EDIT" name="SALES" class="form-control" readonly style="background:#efefef">
                        </div>

                        <!-- Tanggal -->
                        <div class="col-md-6 flex-inline mt-2">
                            <label class="form-label">Tanggal *</label>
                            <input id="SALES_DATE_EDIT" name="SALES_DATE" type="date" class="form-control" required>
                        </div>

                        <!-- Customer -->
                        <div class="col-md-6 flex-inline mt-2">
                            <label class="form-label">Customer *</label>
                            <select id="customerEdit" class="form-control" required></select>
                            <input type="hidden" name="CUSTOMER" id="hiddenCustomerEdit">
                        </div>

                        

                        <!-- Attachment -->
                         
                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Attachment</label>
                            <input type="file" name="ATTACHMENT" class="form-control"
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                            <!-- <small class="text-muted">
                                Kosongkan jika tidak ingin mengganti attachment
                            </small> -->
                        </div>

                        <!-- Remark -->
                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">No. Nota *</label>
                            <input id="NOTA_EDIT" name="NOTA" class="form-control">
                        </div>

                        <!-- Pembayaran -->
                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:0 10px">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="PEMBAYARAN_EDIT" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="PEMBAYARAN_EDIT" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Bayar Awal</label>
                            <input name="BAYAR_AWAL_EDIT" id="BAYAR_AWAL_EDIT" class="form-control text-end" placeholder="0">
                        </div>

                        <!-- Jenis Pembayaran -->
                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label d-block">Jenis Pembayaran</label>
                            <div style="padding:0 10px">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="JENIS_PAY_EDIT" value="LUNAS">
                                    <label class="form-check-label">LUNAS</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="JENIS_PAY_EDIT" value="TEMPO">
                                    <label class="form-check-label">TEMPO</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mt-2 flex-inline" style="justify-content: space-between">
                            <label class="form-label">Attachment</label>
                            <div id="attachmentPreviewEdit"></div>
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input id="REMARK_EDIT" name="REMARK" class="form-control">
                        </div>
                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between mb-2">
                        <h5>Item</h5>
                        <button type="button"
                                class="btn btn-success btn-sm"
                                id="addDetailRowEdit">
                            Tambah Item
                        </button>
                    </div>

                    <table class="table table-bordered" id="salesDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="width:22%; text-align:center;">Item</th>
                                <th style="width:10%; text-align:center;">Metode</th>
                                <th style="width:10%; text-align:center;">BW (KG)</th>
                                <th style="width:10%; text-align:center;">Qty</th>
                                <th style="width:15%; text-align:center;">Harga</th>
                                <th style="width:15%; text-align:center;">Discount</th>
                                <th style="width:15%; text-align:center;">Total</th>
                                <th style="width:3%;  text-align:center;">#</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="text-end">
                        <strong>Total Sales :</strong>
                        <span id="grandTotalDisplayEdit">0</span>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Update
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'SALES', dir: 'DESC' };

    function initPlantSelect2() {
        $('#plantAdd').select2({
            placeholder: 'Pilih PLANT',
            width: '100%',
            dropdownParent: $('#salesAdd .modal-body'),
            ajax: {
                url: '<?= base_url("sales/get_plant_by_user"); ?>',
                dataType: 'json',
                delay: 250,
                cache: true,
                processResults: data => ({ results: data })
            }
        }).on('select2:select', function(e){
            $('#plantAdd').val(e.params.data.id);
        });

        // 🔥 AUTO SELECT JIKA CUMA 1 PLANT
        $.getJSON('<?= base_url("sales/get_plant_by_user"); ?>', function(data){
            if(data.length === 1){
                let p = data[0];
                let option = new Option(p.text, p.id, true, true);
                $('#plantAdd').append(option).trigger('change');
                $('#plantAdd').prop('disabled', true);
            }
        });
    }

    function setDefaultPlantAdd()
    {
        const $plant = $('#plantAdd');

        const firstValid = $plant.find('option').filter(function () {

            const val = ($(this).val() || '').trim();

            return val !== '' && val !== '*';

        }).first();

        if(firstValid.length){

            $plant
                .val(firstValid.val())
                .trigger('change.select2');
        }
    }

    $(document).on('input','input[name="DP_AMOUNT"]', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });

    let searchTimer = null;

    $('#search').on('keyup', function(){
        clearTimeout(searchTimer);
        let val = $(this).val();

        searchTimer = setTimeout(function(){
            state.search = val;
            loadPage(1);
        }, 400); // tunggu 400ms setelah user berhenti ngetik
    });

    function parseDecimalID(val) {
        if (!val) return 0;
        return parseFloat(val.toString().replace(/\./g, '').replace(',', '.')) || 0;
    }

    function formatDecimalID(num, digit = 2) {
        num = Number(num || 0);
        return num.toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
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

    let ajaxListRequest = null;

    function loadPage(page = 1) {
        state.page = page;

        if (ajaxListRequest) {
            ajaxListRequest.abort(); // batalkan request lama
        }

        ajaxListRequest = $.get('<?= base_url("sales/load_data"); ?>', state, function(resp){
            ajaxListRequest = null;

            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var tbody = $('#table-body').empty();

            resp.rows.forEach(function(row){
                var tr = `<tr>
                    <td style="text-align:center; vertical-align: middle"><b>${row.PLANT_NAME}</b></td>
                    <td style="text-align:center; vertical-align: middle"><b>#${row.SALES}</b></td>
                    <td style="text-align:center; vertical-align: middle">${row.CUSTOMER_NAME || ''}<br><b>${row.CUSTOMER}</b></td>
                    <td style="text-align:center; vertical-align: middle">${formatDate(row.SALES_DATE)}</td>
                    <td style="text-align:center; vertical-align: middle">${row.PEMBAYARAN || ''}</td>
                    <td style="text-align:center; vertical-align: middle">${row.JENIS_PAY || ''}</td>
                    <td style="text-align:center; vertical-align: middle"><b>${row.NOTA ?? '-'}</b> <br> ${row.REMARK ?? ''}</td>
                    <td style="text-align:center; vertical-align: middle">
                        <button class="btn btn-sm btn-primary me-1 exportPdf" data-sales="${row.SALES}" data-plant="${row.PLANT}">Slip</button>
                        <button class="btn btn-sm btn-primary me-1 exportInvoicePdf" data-sales="${row.SALES}" data-plant="${row.PLANT}">Invoice</button>
                        <button class="btn btn-sm btn-warning me-1 editBtn" data-sales="${row.SALES}" data-plant="${row.PLANT}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-sales="${row.SALES}" data-plant="${row.PLANT}">Hapus</button>
                    </td>
                </tr>`;
                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            let start = ((resp.page - 1) * state.limit) + 1;
            let end   = Math.min(resp.page * state.limit, resp.total);

            $('#info').text(`Menampilkan ${start} - ${end} dari ${resp.total} data`);
        });
    }

    /* -------------------------
    Select2 inits
    ------------------------- */
    function initCustomerSelect2(selector, modalId){
       $(selector).select2({
        placeholder: "Pilih CUSTOMER",
        dropdownParent: $(modalId),
        width: "100%",
        minimumInputLength: 2,
        ajax: {
                url: "<?= base_url('sales/get_customer'); ?>",
                dataType: "json",
                delay: 400,
                cache: true,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
        }
        }).on('select2:select', function(e){
            $(this).closest('form').find('input[name="CUSTOMER"]').val(e.params.data.id);
        }).on('select2:clear', function(){
            $(this).closest('form').find('input[name="CUSTOMER"]').val('');
        });
    }

    $('#customerAdd').on('change', function () {
        $('#hiddenCustomerAdd').val(
            $(this).val()
        );
    });

    function setDefaultCustomer(selector, custId, custName) {
        let opt = new Option(
            custId + ' - ' + custName,
            custId,
            true,
            true
        );

        $(selector)
            .append(opt)
            .trigger('change');

        $('#hiddenCustomerAdd').val(custId);
    }

    function initItemSelect2(el, parentModal){
        $(el).select2({
            placeholder: "Pilih ITEM",
            width:'100%',
            dropdownParent: $(parentModal),
            minimumInputLength: 2,
            ajax:{
                url:'<?= base_url("sales/get_material"); ?>',
                dataType:'json',
                delay:400,
                cache:true,
                data: p => ({ q: p.term }),
                processResults: d => ({ results: d })
            }
        });
    }

    function formatDecimal(val, digit = 2) {
        val = Number(val || 0);
        return val.toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        });
    }

    function toNumber(val) {
        if (!val) return 0;
        return Number(val.toString().replace(/\./g, '').replace(',', '.'));
    }

    $(document).on(
        'input',
        '.qty, .berat, .harga, .discount',
        function () {

            let row = $(this).closest('tr');

            let qty      = toNumber(row.find('.qty').val());
            let berat    = toNumber(row.find('.berat').val());
            let harga    = toNumber(row.find('.harga').val());
            let discount = toNumber(row.find('.discount').val());

            let method = row.find('.method').val();

            let basis = method === 'BW'
                ? berat
                : qty;

            let total = (basis * harga) - discount;

            row.find('.total').val(
                formatRupiah(total)
            );

            recalcGrandTotal();
        }
    );

    $(document).on('input', '.berat, .qty', function () {
        let val = $(this).val();

        // izinkan angka, titik, koma
        val = val.replace(/[^0-9.,]/g, '');

        // cegah koma lebih dari 1
        let commaCount = (val.match(/,/g) || []).length;
        if (commaCount > 1) {
            val = val.substring(0, val.lastIndexOf(','));
        }

        $(this).val(val);
    });

    $(document).on('blur', '.berat, .qty', function () {
        let num = parseDecimalID($(this).val());
        $(this).val(formatDecimalID(num));
    });

    $(document).on('input', '.harga, .discount', function () {
        let val = toNumber($(this).val());
        $(this).val(formatRupiah(val));
    });

    function parseRupiah(value) {
        if (!value) return 0;
        return parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;
    }

    function recalcRow(tr)
    {
        let jumlah   = parseFloat(
            unformatNumber(
                tr.find('.jumlah').val()
            )
        ) || 0;

        let berat    = parseFloat(
            unformatNumber(
                tr.find('.berat').val()
            )
        ) || 0;

        let harga    = parseFloat(
            unformatNumber(
                tr.find('.harga').val()
            )
        ) || 0;

        let discount = parseFloat(
            unformatNumber(
                tr.find('.discount').val()
            )
        ) || 0;

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        | PRIORITAS:
        | BERAT jika ada
        | selain itu JUMLAH
        */

        let basis = berat > 0
            ? berat
            : jumlah;

        let total = (basis * harga) - discount;

        if(total < 0){
            total = 0;
        }

        tr.find('.total')
            .val(formatNumber(total));

        recalcGrandTotal();
    }

    function recalcGrandTotalEdit() {
        let grand = 0;
        $('#salesDetailTableEdit tbody tr').each(function () {
            grand += recalcRowTotal($(this));
        });
        $('#grandTotalDisplayEdit').text(formatRupiah(grand));
    }

    function recalcGrandTotal(){
        let grand = 0;
        $('#salesDetailTableAdd tbody tr').each(function(){
            grand += clearFormat(
                $(this).find('.total').val()
            );
        });

        $('#grandTotalDisplay').html(
            formatRupiah(grand)
        );
    }

    $(document).on(
        'keyup change',
        '.jumlah,.berat,.harga,.discount',
        function(){

            let tr = $(this).closest('tr');

            recalcRow(tr);
        }
    );

    $(document).on('keyup change', '#salesDetailTableEdit .qty, #salesDetailTableEdit .berat, #salesDetailTableEdit .harga, #salesDetailTableEdit .discount, #salesDetailTableEdit .method', function () {
        recalcGrandTotalEdit();
    });

    $(document).on('click', '.removeRow', function(){
        $(this).closest('tr').remove();
        recalcGrandTotal();
    });

    $(document).on('change', '.method', function () {
        let row = $(this).closest('tr');
        let table = row.closest('table').attr('id');

        if ($(this).val() === 'BW') {
            row.find('.berat').prop('disabled', false);
            row.find('.qty').prop('disabled', true).val('');
        } else {
            row.find('.qty').prop('disabled', false);
            row.find('.berat').prop('disabled', true).val('');
        }

        recalcRow(row);

        if (table === 'salesDetailTableEdit') {
            recalcGrandTotalEdit();
        } else {
            recalcGrandTotal();
        }
    });

    function addDetailRow()
    {
        let tbody = $('#salesDetailTableAdd tbody');

        let html = `
            <tr>

                <td>
                    <select class="form-control material-select"></select>
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control jumlah text-end amount-field"
                        value="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control berat text-end amount-field"
                        value="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control harga text-end amount-field"
                        value="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control discount text-end amount-field"
                        value="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control total text-end bg-light fw-bold"
                        readonly
                        value="0">
                </td>

                <td class="text-center">

                    <button
                        type="button"
                        class="btn btn-danger btn-sm removeRow">

                        <i class="fa fa-trash"></i>

                    </button>

                </td>

            </tr>
        `;

        tbody.append(html);

        let tr = tbody.find('tr').last();

        /*
        |--------------------------------------------------------------------------
        | MATERIAL
        |--------------------------------------------------------------------------
        */

        tr.find('.material-select').select2({

            width:'100%',

            dropdownParent: $('#salesAdd'),

            placeholder:'Pilih Material',

            minimumInputLength:2,

            ajax:{

                url:'<?= base_url("sales/get_material"); ?>',

                dataType:'json',

                delay:300,

                data:params => ({
                    q:params.term
                }),

                processResults:data => ({
                    results:data
                })
            }
        });

        recalcGrandTotal();
    }

    function updateTotalRow(row){
        var qty = parseFloat(cleanNumber(row.find('.qty').val())) || 0;
        var harga = parseFloat(cleanNumber(row.find('.harga').val())) || 0;
        var disc = parseFloat(cleanNumber(row.find('.discount').val())) || 0;

        // jika discount disimpan sebagai jumlah (bukan persen)
        var total = (qty * harga) - disc;
        row.find('.total').val(formatRupiah(total.toString()));
    }

    function loadDefaultCustomer(selector) {
        $.getJSON('<?= base_url("sales/get_customer_default"); ?>', function (res) {
            if (!res) return;

            let opt = new Option(res.text, res.id, true, true);
            $(selector)
                .append(opt)
                .trigger('change');

            $('#hiddenCustomerAdd').val(res.id);
        });
    }

    function formatRupiahEdit(val) {
        val = parseInt(val || 0);
        return val.toLocaleString('id-ID');
    }

    function parseRupiahEdit(val) {
        if (!val) return 0;
        return parseInt(val.toString().replace(/\D/g, '')) || 0;
    }

    /* -------------------------
    DOM Ready
    ------------------------- */
    $(function(){
        loadPage(1);

        // init select2 customer
        initPlantSelect2('#plantAdd', '#salesAdd');
        initCustomerSelect2('#customerAdd', '#salesAdd');
        loadDefaultCustomer('#customerAdd');
        initCustomerSelect2('#customerEdit', '#salesEdit');

        $('#addDetailRowAdd').on('click', function(){
            addDetailRow();
        });

        $('#addDetailRowEdit').click(function(){
            addDetailRow({}, '#salesDetailTableEdit');
        });

        // update total on input
        $('#salesDetailTableAdd, #salesDetailTableEdit').on('input','.qty, .harga, .discount', function(){ updateTotalRow($(this).closest('tr')); });

        $('#fsalesAdd').submit(function (e) {

            e.preventDefault();

            let DETAIL = [];

            $('#salesDetailTableAdd tbody tr').each(function () {

                let material = $(this)
                    .find('.material-select')
                    .val();

                let jumlah = parseDecimalID(
                    $(this).find('.jumlah').val()
                );

                let berat = parseDecimalID(
                    $(this).find('.berat').val()
                );

                let harga = parseRupiah(
                    $(this).find('.harga').val()
                );

                let discount = parseRupiah(
                    $(this).find('.discount').val()
                );

                let total = parseRupiah(
                    $(this).find('.total').val()
                );

                /*
                |--------------------------------------------------------------------------
                | VALIDASI
                |--------------------------------------------------------------------------
                */

                if (!material) {
                    return;
                }

                if (jumlah <= 0 && berat <= 0) {

                    alert(
                        'Jumlah atau Berat wajib diisi'
                    );

                    throw 'invalid';
                }

                DETAIL.push({

                    MATERIAL : material,

                    JUMLAH : jumlah,

                    BERAT : berat,

                    HARGA : harga,

                    DISCOUNT : discount,

                    TOTAL : total
                });
            });

            /*
            |--------------------------------------------------------------------------
            | DETAIL EMPTY
            |--------------------------------------------------------------------------
            */

            if (DETAIL.length === 0) {

                alert(
                    'Detail item tidak boleh kosong'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | FORM DATA
            |--------------------------------------------------------------------------
            */

            let formData = new FormData(this);

            formData.set(
                'PLANT',
                $('#plantAdd').val()
            );

            formData.set(
                'CUSTOMER',
                $('#hiddenCustomerAdd').val()
            );

            formData.set(
                'SALES_DATE',
                $('input[name="SALES_DATE"]').val()
            );

            formData.set(
                'DETAIL',
                JSON.stringify(DETAIL)
            );

            console.log(formData.get('PLANT'));
            console.log(formData.get('CUSTOMER'));
            console.log(formData.get('SALES_DATE'));
            console.log(formData.get('DETAIL'));

            $.ajax({

                url:
                    '<?= base_url("sales/create"); ?>',

                method: 'POST',

                data: formData,

                processData: false,

                contentType: false,

                dataType: 'json',

                success: function (resp) {

                    alert(resp.message);

                    if (resp.status) {

                        $('#salesAdd').modal('hide');

                        $('#fsalesAdd')[0].reset();

                        $('#salesDetailTableAdd tbody').empty();

                        $('#grandTotalDisplay').html('0');

                        $('#customerAdd')
                            .val(null)
                            .trigger('change');

                        loadPage(state.page);
                    }
                },

                error: function(xhr){

                    console.log(xhr.responseText);

                    alert(
                        'Terjadi error server'
                    );
                }
            });
        });

        $('.qty, .harga, .discount, .berat').each(function () {
            $(this).val(toNumber($(this).val()));
        });

        $(document).on('click', '.editBtn', function () {

            let sales = $(this).data('sales');
            let plant = $(this).data('plant');

            // reset dulu
            $('#fsalesEdit')[0].reset();
            $('#salesDetailTableEdit tbody').empty();
            $('#attachmentPreviewEdit').html('');
            $('#grandTotalDisplayEdit').text('0');

            $.get('<?= base_url("sales/edit"); ?>', { sales: sales, plant: plant }, function(resp){

                if (typeof resp === 'string') resp = JSON.parse(resp);

                if (!resp.status) {
                    alert(resp.message);
                    return;
                }

                let h = resp.header;
                let d = resp.detail;

                /* ===== HEADER ===== */
                $('#SALES_EDIT').val(h.SALES);
                $('#PLANT_EDIT').val(h.PLANT);
                $('#PLANT_NAME_EDIT').val(h.PLANT_NAME);
                $('#SALES_DATE_EDIT').val(h.SALES_DATE.split(' ')[0]);
                $('#NOTA_EDIT').val(h.NOTA);
                $('#REMARK_EDIT').val(h.REMARK);
                $('#BAYAR_AWAL_EDIT').val(formatRupiahEdit(h.DP_AMOUNT || 0));

                // pembayaran
                $('input[name="PEMBAYARAN_EDIT"][value="'+h.PEMBAYARAN+'"]').prop('checked', true);
                $('input[name="JENIS_PAY_EDIT"][value="'+h.JENIS_PAY+'"]').prop('checked', true);

                // customer select2
                let opt = new Option(h.CUSTOMER + ' - ' + h.CUSTOMER_NAME, h.CUSTOMER, true, true);
                $('#customerEdit').append(opt).trigger('change');
                $('#hiddenCustomerEdit').val(h.CUSTOMER);

                // attachment preview
                if (h.ATTACHMENT_PATH) {
                    $('#attachmentPreviewEdit').html(
                        `<a href="<?= base_url(); ?>${h.ATTACHMENT_PATH}" target="_blank" class="btn btn-sm btn-info">
                            Lihat Attachment
                        </a>`
                    );
                }

                /* ===== DETAIL ===== */
                d.forEach(function(row){
                    addDetailRowEdit({
                        material: row.MATERIAL,
                        material_text: row.MATERIAL + ' - ' + row.MATERIAL_NAME,
                        method: row.BERAT > 0 ? 'BW' : 'QTY',
                        berat: row.BERAT,
                        qty: row.QTY,
                        harga: row.HARGA,
                        discount: row.DISCOUNT,
                        total: row.AMOUNT
                    });
                });

                $('#salesEdit').modal('show');

            }, 'json');
        });

        $('#fsalesEdit').submit(function (e) {
            e.preventDefault();

            let DETAIL = [];

            $('#salesDetailTableEdit tbody tr').each(function () {

                let method = $(this).find('.method').val();
                let qty    = parseDecimalID($(this).find('.qty').val());
                let berat  = parseDecimalID($(this).find('.berat').val());

                if (method === 'BW' && berat <= 0) {
                    alert('Berat wajib diisi (BW)');
                    throw 'invalid';
                }
                if (method === 'QTY' && qty <= 0) {
                    alert('Qty wajib diisi (QTY)');
                    throw 'invalid';
                }

                DETAIL.push({
                    MATERIAL : $(this).find('.material-select').val(),
                    METHOD   : method,
                    QTY      : qty,
                    BERAT    : berat,
                    HARGA    : parseRupiah($(this).find('.harga').val()),
                    DISCOUNT : parseRupiah($(this).find('.discount').val()),
                    AMOUNT   : parseRupiah($(this).find('.total').val())
                });
            });

            if (!DETAIL.length) {
                alert('Detail tidak boleh kosong');
                return;
            }

            let formData = new FormData(this);
            formData.append('SALES', $('#SALES_EDIT').val());
            formData.append('BAYAR_AWAL', $('#BAYAR_AWAL_EDIT').val());
            formData.append('CUSTOMER', $('#hiddenCustomerEdit').val());
            formData.append('DETAIL', JSON.stringify(DETAIL));

            $.ajax({
                url: '<?= base_url("sales/update"); ?>',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (resp) {
                    alert(resp.message);
                    if (resp.status) {
                        $('#salesEdit').modal('hide');
                        loadPage(state.page);
                    }
                }
            });
        });

        // PDF
        $(document).on("click", ".exportPdf", function () {
            let sales    = $(this).data("sales");
            let plant = $(this).data("plant");

            window.open(
                "<?= base_url('sales/print_pdf'); ?>?sales=" + sales + "&plant=" + plant,
                "_blank"
            );
        });

        $(document).on("click", ".exportInvoicePdf", function () {
            let sales = $(this).data("sales");
            let plant = $(this).data("plant");

            window.open(
                "<?= base_url('sales/print_invoice_pdf'); ?>?sales=" 
                + sales + "&plant=" + plant,
                "_blank"
            );
        });

        // Delete
        $(document).on('click', '.deleteBtn', function() {
            let sales = $(this).data('sales');
            let plant = $(this).data('plant');

            Swal.fire({
                title: 'Hapus Sales?',
                text: `Sales ${sales} akan dihapus permanen`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.post("<?= base_url('sales/remove'); ?>", {
                    sales: sales,
                    plant: plant
                }, function(res){
                    if (res.status) {
                        showToast('success', res.message);
                        loadPage(state.page);
                    } else {
                        showToast('error', res.message);
                    }
                }, 'json');
            });
        });

    }); // end ready

    function cleanNumber(val) {
        if (val === null || val === undefined) return 0;
        val = val.toString().trim();
        if (val.includes('.') && /^[0-9]+\.[0-9]{2}$/.test(val)) {
            return parseFloat(val);
        }
        val = val.replace(/\./g, "");
        return parseFloat(val) || 0;
    }

    function formatRupiah(value){
        value = parseFloat(value || 0);
        return value.toLocaleString('id-ID');
    }

    function clearFormat(value){
        value = String(value || '');
        return parseFloat(
            value.replace(/\./g,'').replace(/,/g,'.')
        ) || 0;
    }

    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({ icon: type, title: message });
    }
</script>

<script>
    $('#salesAdd').on('shown.bs.modal', function () {
        let today = new Date().toISOString().split("T")[0];
        const dateInput = $(this).find('input[name="SALES_DATE"]')[0];
        if(dateInput){
            dateInput.value = today; // hari ini
        }
        if(
            $('#salesDetailTableAdd tbody tr').length === 0
        ){
            addDetailRow();
        }
        setDefaultPlantAdd();
    });

    $(document).on('input','#BAYAR_AWAL_EDIT', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });
</script>
