<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">PRODUCTION</h5>

            <!-- SEARCH + ADD -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari production..." />
                </div>
                <div class="col-md-4 text-end mt-2 mt-md-0">
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productionAdd">
                        <i class="ti ti-plus"></i> Tambah Production
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PRODUCTION" style="text-align:center;">Plant</th>
                            <th data-order="PRODUCTION" style="text-align:center;">No. Produksi</th>
                            <th data-order="PRODUCTION_DATE" style="text-align:center;">Tanggal</th>
                            <th data-order="RECEIVE_LB" style="text-align:center;">No. Receive LB</th>
                            <th data-order="REMARK" style="text-align:center;">Keterangan</th>
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
        width: 35%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
</style>

<!-- =========================
     MODAL ADD PRODUCTION
========================= -->
<div class="modal fade" id="productionAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fproductionAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">PRODUCTION - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Produksi</label>
                            <input class="form-control" placeholder="Automatic" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="PRODUCTION_DATE" type="date" name="PRODUCTION_DATE" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" class="form-control" required></select>
                            <input type="hidden" name="PLANT" id="hiddenPlantAdd">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Receive LB *</label>
                            <div class="input-group">
                                <input id="receiveLbAdd" class="form-control" placeholder="Pilih Receive LB..." readonly required>
                                <button class="btn btn-primary" type="button" id="btnChooseReceiveLB">
                                    Pilih
                                </button>
                            </div>
                            <input type="hidden" name="RECEIVE_LB" id="receiveLbAddHidden">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Supplier *</label>
                            <input id="supplierInput" name="SUPPLIER" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">DO *</label>
                            <input id="doInput" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Mobil / Driver *</label>
                            <input id="mobilInput" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Arrive Schedule *</label>
                            <input id="arriveScheduleInput" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">TOTAL ( QTY )</label>
                            <input id="qtyInput" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">TOTAL ( KG )</label>
                            <input id="weightInput" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label">Keterangan</label>
                            <input name="REMARK" class="form-control" placeholder="Input keterangan...">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom:0">Item Produksi</h5>
                        <!-- <button type="button" class="btn btn-success btn-sm" id="addDetailRowAdd">
                            Tambah Item
                        </button> -->
                    </div>

                    <table class="table table-bordered" id="productionDetailTableAdd">
                        <thead>
                            <tr>
                                <th style="text-align:center;">Item</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:center;">BW (Kg)</th>
                                <th style="text-align:center;">Keterangan</th>
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

<!-- =========================
     MODAL EDIT PRODUCTION
========================= -->
<div class="modal fade" id="productionEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fproductionEdit">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">PRODUCTION - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Produksi</label>
                            <input name="PRODUCTION" id="PRODUCTION_EDIT"
                                   class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="PRODUCTION_DATE_EDIT" type="date"
                                   name="PRODUCTION_DATE"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input id="PLANT_EDIT_DISPLAY" class="form-control" readonly style="background:#efefef">
                            <input type="hidden" name="PLANT" id="PLANT_EDIT">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Receive LB *</label>
                            <div class="input-group">
                                <input id="receiveLbEdit" class="form-control"
                                       placeholder="Pilih Receive LB..." readonly style="background:#efefef" required>
                                <!-- <button class="btn btn-primary" type="button" id="btnChooseReceiveLBEdit" disable>
                                    Pilih
                                </button> -->
                            </div>
                            <input type="hidden" name="RECEIVE_LB" id="receiveLbEditHidden">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Supplier *</label>
                            <input id="supplierInputEdit" class="form-control"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">DO *</label>
                            <input id="doInputEdit" class="form-control"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Mobil / Driver *</label>
                            <input id="mobilInputEdit" class="form-control"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Arrive Schedule *</label>
                            <input id="arriveScheduleInputEdit" class="form-control"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">TOTAL ( QTY )</label>
                            <input id="qtyInputEdit" class="form-control"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">TOTAL ( KG )</label>
                            <input id="weightInputEdit" class="form-control"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label">Keterangan</label>
                            <input name="REMARK" id="REMARK_EDIT"
                                   class="form-control" placeholder="Input keterangan...">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom:0">Item Produksi</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addDetailRowEdit">
                            Tambah Item
                        </button>
                    </div>

                    <table class="table table-bordered" id="productionDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="text-align:center;">Item</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:center;">BW (Kg)</th>
                                <th style="text-align:center;">Keterangan</th>
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

<style>
    #modalReceiveLB {
        background: #000000c7;
    }
</style>

<div class="modal fade" id="modalReceiveLB" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Pilih Receive LB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" id="tableReceiveLB">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Plant</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Receive LB</th>
                            <th style="text-align:center;">Supplier</th>
                            <th style="text-align:center;">Total ( Qty )</th>
                            <th style="text-align:center;">Total ( KG )</th>
                            <th style="text-align:center; width:100px;">#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    console.log("FILE JS BERJALAN");
    /* =========================================================
    STATE & SEARCH
    ========================================================= */
    var state = { page: 1, limit: 10, search: '', order: 'PRODUCTION_DATE', dir: 'DESC' };

    function autoSelectSinglePlant() {
        $.getJSON("<?= base_url('production/get_plant_by_user'); ?>", function(data){
            if(data.length === 1){
                let p = data[0];
                let option = new Option(p.text, p.id, true, true);
                $('#plantAdd').append(option).trigger('change');
                $('#plantAdd').prop('disabled', true);
                $('#hiddenPlantAdd').val(p.id);

                loadProductionItems('#productionDetailTableAdd');
            }
        });
    }

    function initPlantSelect2(selector, modalId){
        $(selector).select2({
            placeholder: "Pilih PLANT",
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('production/get_plant_by_user'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return { results: data };
                }
            }
        }).on('select2:select', function(e){
            $('#hiddenPlantAdd').val(e.params.data.id);

            // RESET RECEIVE LB & HEADER
            $('#receiveLbAdd').val('');
            $('#receiveLbAddHidden').val('');
            $('#supplierInput').val('');
            $('#mobilInput').val('');
            $('#doInput').val('');
            $('#qtyInput').val('');
            $('#weightInput').val('');
            $('#arriveScheduleInput').val('');

            // 🔥 RELOAD ITEM PRODUKSI
            $('#productionDetailTableAdd tbody').empty();
            loadProductionItems('#productionDetailTableAdd');
        });
    }

    let searchTimer = null;

    $('#search').on('keyup', function(){
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            state.search = $(this).val();
            loadPage(1);
        }, 500); // tunggu 0.5 detik setelah user berhenti mengetik
    });

    $('#btnChooseReceiveLB').on('click', function () {
        $('#modalReceiveLB').modal('show');
        loadReceiveLBList();
        window.selectMode = 'add';   // ← tandai mode
    });

    $('#btnChooseReceiveLBEdit').on('click', function () {
        $('#modalReceiveLB').modal('show');
        loadReceiveLBList();
        window.selectMode = 'edit';  // ← tandai mode
    });

    let RECEIVE_CACHE = {};
    function loadReceiveLBList() {
        let plant = window.selectMode === 'edit' ? $('#PLANT_EDIT').val() : $('#hiddenPlantAdd').val();
        if (!plant) {
            alert('Pilih plant terlebih dahulu');
            return;
        }

        if(RECEIVE_CACHE[plant]){
            renderReceiveTable(RECEIVE_CACHE[plant]);
            return;
        }

        $.getJSON("<?= base_url('production/get_receive_lb_list'); ?>", { plant: plant }, function(res){
            RECEIVE_CACHE[plant] = res.data;
            renderReceiveTable(res.data);
        });
    }

    function renderReceiveTable(data){
        let rows = "";

        data.forEach(function(row){
            let btnClass = (window.selectMode === 'edit') 
                ? 'btnSelectReceiveLBEdit' 
                : 'btnSelectReceiveLB';

            rows += `
                <tr>
                    <td style="text-align:center; vertical-align: middle">${row.PLANT_NAME}</td>
                    <td style="text-align:center; vertical-align: middle">${row.RECEIVE_DATE}</td>
                    <td style="text-align:center; vertical-align: middle">#${row.RECEIVE}</td>
                    <td style="text-align:center; vertical-align: middle">${row.SUPPLIER} - ${row.FULL_NAME}</td>
                    <td style="text-align:center; vertical-align: middle">${formatDecimal(row.TOTAL_QTY)}</td>
                    <td style="text-align:center; vertical-align: middle">${formatDecimal(row.RECEIVE_AMOUNT)}</td>
                    <td class="text-center" style="vertical-align: middle">
                        <button type="button"
                            class="btn btn-sm btn-primary ${btnClass}"
                            data-id="${row.RECEIVE}"
                            data-supplier="${row.SUPPLIER} - ${row.FULL_NAME}"
                            data-mobil="${row.NO_CAR}"
                            data-driver="${row.DRIVER}"
                            data-do="${row.DO}"
                            data-arrive="${row.ARRIVE_SCHEDULE}"
                            data-qty="${row.TOTAL_QTY}"
                            data-weight="${row.RECEIVE_AMOUNT}">
                            Pilih
                        </button>
                    </td>
                </tr>
            `;
        });

        $("#tableReceiveLB tbody").html(rows);
    }

    // === SELECT ROW ===
    $(document).off("click", ".btnSelectReceiveLB")
           .on("click", ".btnSelectReceiveLB", function () {
        $("#arriveScheduleInput").val($(this).data("arrive"));
        $("#receiveLbAdd").val("#" + $(this).data("id"));
        $("#receiveLbAddHidden").val($(this).data("id"));

        // Supplier format: SUPPLIER - FULL_NAME
        $("#supplierInput").val($(this).data("supplier"));

        // No Mobil + Driver
        $("#mobilInput").val($(this).data("mobil") + " / " + $(this).data("driver"));

        // DO
        $("#doInput").val($(this).data("do"));
        $("#qtyInput").val(formatDecimal($(this).data("qty")));
        $("#weightInput").val(formatDecimal($(this).data("weight")));;

        // Arrive Schedule
        $("#arriveScheduleInput").val($(this).data("arrive"));

        $("#modalReceiveLB").modal("hide");
    });

    $(document).on("click", ".btnSelectReceiveLBEdit", function () {
        $("#arriveScheduleInputEdit").val($(this).data("arrive"));
        $("#receiveLbEdit").val("#" + $(this).data("id"));
        $("#receiveLbEditHidden").val($(this).data("id"));

        $('#supplierInputEdit').val($(this).data("supplier"));
        $('#mobilInputEdit').val($(this).data("mobil") + " / " + $(this).data("driver"));
        $('#doInputEdit').val($(this).data("do"));
        $('#qtyInputEdit').val($(this).data("qty"));
        $('#weightInputEdit').val($(this).data("weight"));

        $("#modalReceiveLB").modal("hide");
    });

    /* =========================================================
    UTIL
    ========================================================= */
    function formatDate(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        if (isNaN(d)) return dateString;
        const day = String(d.getDate()).padStart(2, '0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    $(document).on('click', '.exportProductionPdf', function () {
        let production = $(this).data('production');
        let plant      = $(this).data('plant');

        window.open(
            "<?= base_url('production/print_pdf'); ?>?production=" 
            + production + "&plant=" + plant,
            "_blank"
        );
    });

    $(document).on('keypress', '.qty, .berat', function (e) {
        if (!/[0-9.,]/.test(e.key)) {
            e.preventDefault();
        }
    });
    /* =========================================================
    LOAD DATA
    ========================================================= */
    let currentRequest = null;
    let pagingTimer = null;
    
    function loadPage(page = 1){
        clearTimeout(pagingTimer);
        pagingTimer = setTimeout(() => {
            state.page = page;
            if(currentRequest) currentRequest.abort();
            currentRequest = $.get('<?= base_url("production/load_data"); ?>', state, function(resp){
                resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
                let html = '';

                resp.rows.forEach(function(row){
                    html += `
                        <tr>
                            <td style="text-align:center; vertical-align: middle"><b>${row.PLANT_NAME}</b></td>
                            <td style="text-align:center; vertical-align: middle"><b>#${row.PRODUCTION}</b></td>
                            <td style="text-align:center; vertical-align: middle">${formatDate(row.PRODUCTION_DATE)}</td>
                            <td style="text-align:center; vertical-align: middle">${row.RECEIVE_LB}</td>
                            <td style="text-align:center; vertical-align: middle">${row.REMARK ?? ''}</td>
                            <td style="text-align:center; vertical-align: middle">
                                <button class="btn btn-sm btn-primary exportProductionPdf" data-production="${row.PRODUCTION}" data-plant="${row.PLANT}">PDF</button>
                                ${!['adelia','hellen'].includes(CURRENT_USER) ? 
                                `<button class="btn btn-sm btn-warning editBtn" data-id="${row.PRODUCTION}" data-plant="${row.PLANT}">Edit</button>` 
                                : ''}
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.PRODUCTION}" data-plant="${row.PLANT}">Hapus</button>
                            </td>
                        </tr>
                    `;
                });

                $('#table-body').html(html);

                $('#pagination').html(resp.pagination);
                $('#info').text(`Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`);
            });
        }, 150);
    }

    function loadProductionItems(tableSelector){
        if(!ALL_ITEMS.length){
            console.warn("Item belum siap");
            return;
        }

        $(tableSelector + ' tbody').empty();

        ALL_ITEMS.forEach(function(item){
            addDetailRow({
                ITEM: item.ITEM,
                ITEM_TEXT: item.FULL_NAME,
                QTY: 0,
                BERAT: 0
            }, tableSelector);
        });
    }

    let ALL_ITEMS = [];
    $(document).ready(function () {
        $.getJSON('<?= site_url("production/get_all_item") ?>', function (res) {
            ALL_ITEMS = res; // ✅ simpan saja datanya
        });
    });

    function refreshTabQtyBerat() {
        let tabindex = 1;

        $('.qty, .berat').each(function () {
            $(this).attr('tabindex', tabindex++);
        });
    }

    function addItemRow(item) {
        $('#productionDetailTableAdd tbody').append(`
            <tr>
                <td style="width:30%">
                    <input type="hidden" name="item[]" value="${item.ITEM}">
                    <input type="text" class="form-control" 
                        value="${item.ITEM} - ${item.FULL_NAME}" readonly tabindex="-1" style="background:#efefef">
                </td>
                <td>
                    <input type="text" name="qty_display[]" 
                        class="form-control qty text-end" value="0,00">

                    <input type="hidden" name="qty[]" class="qty_raw" value="0">
                </td>
                <td>
                    <input type="text" 
                       class="form-control berat text-end"
                       value="0,00">
                    <input type="hidden" 
                        name="berat[]" 
                        class="berat_raw" value="0">
                </td>
                <td>
                    <input type="text" name="remark[]" 
                        class="form-control remark" tabindex="-1" placeholder="Remark disini..">
                </td>
            </tr>
        `);
    }

    $(document).on('focus', '.qty, .berat', function () {
        this.select();
    });

    $(document).on('keydown', '.qty, .berat', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let next = $('[tabindex="' + (parseInt($(this).attr('tabindex')) + 1) + '"]');
            if (next.length) next.focus();
        }
    });

    function initItemSelect(selector) {
        $(selector).select2({
            dropdownParent: $(selector).closest('table').parent(),
            placeholder: "Pilih Item",
            allowClear: true,
            ajax: {
                url: "<?= base_url('production/get_item_list'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function (data) {
                    return { results: data };
                }
            }
        });
    }

    function cleanNumber(value) {
        if (!value) return 0;
        value = value.toString().replace(/\./g, "").replace(",", ".");
        return parseFloat(value) || 0;
    }

    function cleanDecimal(val) {
        if (!val) return 0;

        return parseFloat(
            val.toString()
            .replace(/\./g, '') // hapus ribuan
            .replace(',', '.')  // koma → titik
        ) || 0;
    }

    function formatDecimalID(num) {
        return Number(num).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    $(document).on('input', '.qty, .berat', function () {
        let val = cleanDecimal(this.value);

        let $tr = $(this).closest('tr');

        if ($(this).hasClass('qty')) {
            $tr.find('.qty_raw').val(val);
        } else {
            $tr.find('.berat_raw').val(val);
        }
    });

    $(document).on('blur', '.qty, .berat', function () {
        let val = cleanDecimal(this.value);
        this.value = val ? formatDecimalID(val) : '0,00';
    });

    function formatDecimal(val) {
        if (val === null || val === undefined || val === '') return '';

        // JIKA SUDAH FORMAT INDONESIA → JANGAN FORMAT ULANG
        if (typeof val === 'string' && val.includes('.') && val.includes(',')) {
            return val;
        }

        let num = Number(val);
        if (isNaN(num)) return '';

        return num.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /* =========================================================
    ADD / REMOVE DETAIL
    ========================================================= */
    function addDetailRow(data, table) {
        let qty   = Number(data.QTY || 0);
        let berat = Number(data.BERAT || 0);

        $(table + ' tbody').append(`
            <tr>
                ${data.SEQ_NO ? `<input type="hidden" class="seq_no" value="${data.SEQ_NO}">` : ''}

                <td style="width:30%">
                    <input type="hidden" class="item" value="${data.ITEM}">
                    <input type="text" class="form-control"
                        value="${data.ITEM} - ${data.ITEM_TEXT}"
                        readonly style="background:#efefef">
                </td>

                <td>
                    <input type="text"
                        class="form-control qty text-end"
                        value="${formatDecimalID(qty)}">

                    <input type="hidden"
                        class="qty_raw"
                        value="${qty}">
                </td>

                <td>
                    <input type="text"
                        class="form-control berat text-end"
                        value="${formatDecimalID(berat)}">

                    <input type="hidden"
                        class="berat_raw"
                        value="${berat}">
                </td>

                <td>
                    <input type="text"
                        class="form-control remark"
                        value="${data.REMARK || ''}" placeholder="Remark disini..">
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger removeRow">x</button>
                </td>
            </tr>
        `);
    }

    /* =========================================================
    DOM READY
    ========================================================= */
    $(function(){
        loadPage(1);

        initPlantSelect2('#plantAdd', '#productionAdd');
        autoSelectSinglePlant();

        $(document).on('click', '#addDetailRowAdd', function () {
            addDetailRow({}, '#productionDetailTableAdd');
            refreshTabQtyBerat();
        });

        $('#addDetailRowEdit').click(function () {
            addDetailRow({}, '#productionDetailTableEdit');
        });

        $('#productionDetailTableAdd, #productionDetailTableEdit').on('click','.removeRow',function(){
            $(this).closest('tr').remove();
        });

    /* =========================================================
    SUBMIT ADD
    ========================================================= */
    $('#fproductionAdd').submit(function(e){
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Menyimpan...');

        let DETAIL = [];

        if(!$('#receiveLbAddHidden').val()){
            alert('Pilih Receive LB terlebih dahulu');
            btn.prop('disabled', false);
            return;
        }
        $('#productionDetailTableAdd tbody tr').each(function(){
            DETAIL.push({
                ITEM: $(this).find('.item').val(),
                QTY: cleanNumber($(this).find('.qty').val()),
                BERAT: cleanNumber($(this).find('.berat').val()),
                REMARK: $(this).find('.remark').val()
            });
        });

        $.post('<?= base_url("production/create"); ?>', {
            PLANT: $('#hiddenPlantAdd').val(),
            RECEIVE_LB: $('#receiveLbAddHidden').val(),
            PRODUCTION_DATE: $('#PRODUCTION_DATE').val(),
            REMARK: $('[name="REMARK"]').val(),
            DETAIL: DETAIL
        }, function(resp){
            alert(resp.message);
            if(resp.status){
                $('#productionAdd').modal('hide');
                $('#fproductionAdd')[0].reset();
                if($('#plantAdd').prop('disabled')){
                    autoSelectSinglePlant();
                }
                $('#productionDetailTableAdd tbody').empty();
                loadPage(1);
                RECEIVE_CACHE = {};
            }
        }, 'json').always(()=>{
            btn.prop('disabled', false).text('Simpan');
        });
        
    });

    /* =========================================================
    EDIT
    ========================================================= */
    // =========================
    // CLICK EDIT BUTTON
    // =========================
    console.log("CARI EDIT BUTTON:", $('.editBtn').length);
    $(document).on('click', '.editBtn', function () {
        console.log("EDIT BUTTON DITEKAN");

        let id = $(this).data('id');
        let plant = $(this).data('plant');
        console.log("ID:", id);

        $.ajax({
            url: "<?= base_url('production/edit'); ?>",
            type: "GET",
            data: { production: id, plant: plant },
            dataType: "json",
            success: function (res) {
                console.log("RESPONSE:", res);

                if (!res || !res.header) {
                    alert("Data tidak ditemukan");
                    return;
                }
                $('#PLANT_EDIT').val(res.header.PLANT);
                $('#PLANT_EDIT_DISPLAY').val(
                    res.header.PLANT + ' - ' + res.header.PLANT_NAME
                );
                $('#PRODUCTION_EDIT').val(res.header.PRODUCTION);
                $('#PRODUCTION_DATE_EDIT').val(res.header.PRODUCTION_DATE);

                // RECEIVE
                $('#receiveLbEdit').val('#' + res.header.RECEIVE_LB);
                $('#receiveLbEditHidden').val(res.header.RECEIVE_LB);

                // TANGGAL RECEIVE — jika perlu dipakai
                if (res.header.RECEIVE_DATE_RAW) {
                    $('#RECEIVE_DATE_EDIT').val(res.header.RECEIVE_DATE_RAW);
                }

                // SUPPLIER FORMAT ( SUPPLIER ) FULL_NAME
                $('#supplierInputEdit').val(`${res.header.SUPPLIER_NAME}`);

                // Mobil / Driver
                $('#mobilInputEdit').val(res.header.NO_CAR + " / " + res.header.DRIVER);

                // DO
                $('#doInputEdit').val(res.header.DO);

                // Arrive Schedule
                $('#arriveScheduleInputEdit').val(res.header.ARRIVE_SCHEDULE);

                // Qty
                $('#qtyInputEdit').val(formatDecimal(res.header.TOTAL_QTY));

                // Berat
                $('#weightInputEdit').val(formatDecimal(res.header.TOTAL_KG));

                // Remark
                $('#REMARK_EDIT').val(res.header.REMARK);

                $('#productionDetailTableEdit tbody').empty();

                let DETAIL_ITEMS = res.detail.map(d => d.ITEM);

                // Tambahkan semua item dari ALL_ITEMS
                ALL_ITEMS.forEach(item => {
                    let found = res.detail.find(d => d.ITEM === item.ITEM);

                    addDetailRow({
                        SEQ_NO: found ? found.SEQ_NO : null,
                        ITEM: item.ITEM,
                        ITEM_TEXT: item.FULL_NAME,
                        QTY: found ? found.QTY : 0,
                        BERAT: found ? found.BERAT : 0,
                        REMARK: found ? found.REMARK : ''
                    }, '#productionDetailTableEdit');
                });

                $('#productionEdit').modal('show');
            },
            error: function (xhr, status, error) {
                console.log("AJAX ERROR:", error);
                console.log("Response:", xhr.responseText);
            }
        });
    });

    $('#productionEdit').on('shown.bs.modal', function () {
        console.log("MODAL EDIT BERHASIL DIBUKA");

        setTimeout(() => {
            $('#productionDetailTableEdit tbody tr:first .qty').focus();
        }, 300);
    });

    /* =========================================================
    SUBMIT EDIT
    ========================================================= */
    $('#fproductionEdit').submit(function(e){
        e.preventDefault();

        let DETAIL = [];
        $('#productionDetailTableEdit tbody tr').each(function(){
            DETAIL.push({
                SEQ_NO: $(this).find('.seq_no').val() || null,
                ITEM: $(this).find('.item').val(),
                QTY: cleanNumber($(this).find('.qty').val()),
                BERAT: cleanNumber($(this).find('.berat').val()),
                REMARK: $(this).find('.remark').val()
            });
        });

        $.post('<?= base_url("production/update"); ?>', {
            PRODUCTION: $('#PRODUCTION_EDIT').val(),
            PLANT: $('#PLANT_EDIT').val(),
            RECEIVE_LB: $('#receiveLbEditHidden').val(),
            PRODUCTION_DATE: $('#PRODUCTION_DATE_EDIT').val(),
            REMARK: $('#REMARK_EDIT').val(),
            DETAIL: DETAIL
        }, function(resp){
            alert(resp.message);
            if(resp.status){
                $('#productionEdit').modal('hide');
                RECEIVE_CACHE = {};
                loadPage(state.page);
            }
        }, 'json');
    });


    /* =========================================================
    DELETE
    ========================================================= */
       $(document).on('click','.deleteBtn', function(){
            let id = $(this).data('id');
            let plant = $(this).data('plant');

            if(!confirm('Hapus PRODUCTION ' + id + ' - PLANT ' + plant + ' ?')) return;

            $.post('<?= base_url("production/remove"); ?>', {
                production: id,
                plant: plant
            }, function(resp){
                resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
                alert(resp.message);
                if(resp.status) {
                    RECEIVE_CACHE = {};
                    loadPage(state.page);
                } 
            }, 'json');
        });

        let ITEMS_ALREADY_LOADED = false;
        $('#productionAdd').on('shown.bs.modal', function(){
            let today = new Date().toISOString().split("T")[0];
            $('#PRODUCTION_DATE').val(today);

            if (!ITEMS_ALREADY_LOADED) {
                loadProductionItems('#productionDetailTableAdd');
                ITEMS_ALREADY_LOADED = true;
            }

            setTimeout(() => {
                $('#productionDetailTableAdd tbody tr:first .qty').focus();
            }, 300);
        });

        $('#productionAdd').on('hidden.bs.modal', function(){
            $('#productionDetailTableAdd tbody').empty();
            ITEMS_ALREADY_LOADED = false;
        });
    });
</script>

<script>
    const CURRENT_USER = "<?= strtolower($this->session->userdata('username')); ?>";
</script>
