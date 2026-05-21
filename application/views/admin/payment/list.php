<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">PAYMENT - ACCOUNTING</h5>

            <!-- SEARCH + ADD -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari payment..." />
                </div>
                <div class="col-md-4 text-end mt-2 mt-md-0">
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentAdd">
                        <i class="ti ti-plus"></i> Tambah Payment
                    </button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PLANT" style="text-align:center;">Plant</th>
                            <th data-order="PAYMENT_DATE" style="text-align:center;">Tanggal</th>
                            <th data-order="PAYMENT" style="text-align:center;">No Payment</th>
                            <th data-order="SUPPLIER" style="text-align:center;">Supplier</th>
                            <th data-order="PEMBAYARAN" style="text-align:center;">Pembayaran</th>
                            <th data-order="SLIP_NO" style="text-align:center;">Slip</th>
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
    }
    label {
        width: 35%;
    }
    .space-line {
        border-bottom: 5px double black;
        margin-bottom: 10px
    }
</style>

<!-- ================= MODAL ADD ================= -->
<div class="modal fade" id="paymentAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fPaymentAdd">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">PAYMENT - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select name="PLANT" id="plantAdd" class="form-control" required></select>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Payment</label>
                            <input class="form-control" readonly placeholder="Auto Generate" style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input name="PAYMENT_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Pembayaran *</label>
                            <div style="width:100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PEMBAYARAN" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Slip</label>
                            <input name="SLIP_NO" class="form-control" readonly placeholder="Auto Generate" style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Supplier</label>
                            <select id="supplierAdd" class="form-control"></select>
                            <input type="hidden" name="SUPPLIER" id="hiddensupplierAdd">
                        </div>

                        <div class="col-md-12 flex-inline">
                            <label style="width: 14.5%" class="form-label">Remark</label>
                            <input name="REMARK" class="form-control" placeholder="Keterangan..">
                        </div>
                    </div>

                    <!-- DETAIL -->
                     <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom: 0px">
                            Receive
                        </h5>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="addRow">Pilih Receive</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="detailTable">
                            <thead>
                                <tr>
                                    <th style="width:15%">Receive No</th>
                                    <th style="width:20%">Material</th>
                                    <th style="text-align: center">Berat</th>
                                    <th style="text-align: center">Jumlah ( QTY )</th>
                                    <th style="text-align: center">Harga</th>
                                    <th style="text-align: center">Total</th>
                                    <th>Remark</th>
                                    <th style="width:5%">#</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                        <div class="row" style="width: 100%;">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">Grand Total</span>
                                    <input type="text"
                                        id="grandTotal"
                                        class="form-control text-end fw-bold"
                                        readonly style="background: #efefef">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="paymentEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fPaymentEdit">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">PAYMENT - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- hidden key -->
                    <input type="hidden" name="PAYMENT">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select name="PLANT" id="plantEdit" class="form-control" required></select>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Payment</label>
                            <input class="form-control" id="paymentNoEdit"
                                   readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input name="PAYMENT_DATE" type="date"
                                   class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Pembayaran *</label>
                            <div style="width:100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="PEMBAYARAN"
                                           value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="PEMBAYARAN"
                                           value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Slip</label>
                            <input name="SLIP_NO"
                                   class="form-control"
                                   readonly
                                   style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Supplier</label>
                            <select id="supplierEdit" class="form-control"></select>
                            <input type="hidden" name="SUPPLIER" id="hiddensupplierEdit">
                        </div>

                        <div class="col-md-12 flex-inline">
                            <label style="width: 14.5%" class="form-label">Remark</label>
                            <input name="REMARK"
                                   class="form-control"
                                   placeholder="Keterangan..">
                        </div>
                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom: 0px" id="detailTitleEdit">Receive</h5>
                        <div>
                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    id="addRowEdit">
                                Pilih Receive
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="detailTableEdit">
                            <thead>
                                <tr>
                                    <th style="width:15%">Receive No</th>
                                    <th style="width:20%">Material</th>
                                    <th style="text-align: center">Berat</th>
                                    <th style="text-align: center">Jumlah ( QTY )</th>
                                    <th style="text-align: center">Harga</th>
                                    <th style="text-align: center">Total</th>
                                    <th>Remark</th>
                                    <th style="width:5%">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- diisi via JS -->
                            </tbody>
                        </table>

                        <!-- GRAND TOTAL -->
                        <div class="row" style="width: 100%;">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">
                                        Grand Total
                                    </span>
                                    <input type="text"
                                           id="grandTotalEdit"
                                           class="form-control text-end fw-bold"
                                           readonly
                                           style="background:#efefef">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button class="btn btn-primary" type="submit">
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<style>
    #modalReceive {
        background: #000000c7;
    }
</style>

```html
<!-- =========================================
MODAL PICK RECEIVE
========================================== -->

<div class="modal fade"
    id="modalPickReceive"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    PILIH RECEIVE
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <!-- SEARCH -->
                <div class="row mb-3">

                    <div class="col-md-6">

                        <input
                            type="text"
                            id="searchReceive"
                            class="form-control"
                            placeholder="Cari receive / material / supplier...">

                    </div>

                </div>

                <!-- TABLE -->
                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>

                                <th width="5%">
                                    #
                                </th>

                                <th>
                                    Receive
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Material
                                </th>

                                <th class="text-center">
                                    Qty
                                </th>

                                <th class="text-center">
                                    Berat
                                </th>

                                <th class="text-end">
                                    Harga
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                            </tr>

                        </thead>

                        <tbody id="receiveListBody"></tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Tutup

                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnChooseReceive">

                    Pilih Data

                </button>

            </div>

        </div>

    </div>

</div>
```


<script>
    var state = { page: 1, limit: 10, search: '', order: 'PAYMENT', dir: 'DESC' };
    let detailIndex = 0;

    $('#search').on('keyup', function(){
        state.search = $(this).val();
        loadPage(1);
    });

    /* =========================
    UTIL
    ========================= */
    function formatDate(dateString){
        if(!dateString) return '-';
        const d = new Date(dateString);
        if(isNaN(d)) return dateString;

        const day = String(d.getDate()).padStart(2,'0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    function cleanNumber(val){
        if(!val) return 0;
        val = val.toString();
        if(val.includes('.') && /^[0-9]+\.[0-9]{2}$/.test(val)) return parseFloat(val);
        return parseFloat(val.replace(/\./g,'')) || 0;
    }

    function formatRupiah(x){
        let n = cleanNumber(x).toString().split('.');
        return n[0].replace(/\B(?=(\d{3})+(?!\d))/g,'.') + (n[1] ? '.'+n[1] : '');
    }

    /* =========================
    LOAD DATA
    ========================= */
    function loadPage(page = 1){
        state.page = page;

        $.get('<?= base_url("payment/load_data"); ?>', state, function(resp){
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            let tbody = $('#table-body').empty();

            resp.rows.forEach(r=>{
                tbody.append(`
                    <tr>
                        <td class="text-center" style="vertical-align: middle"><b>${r.PLANT_NAME}</b></td>
                        <td class="text-center" style="vertical-align: middle">${formatDateIndo(r.PAYMENT_DATE)}</td>
                        <td class="text-center" style="vertical-align: middle">#${r.PAYMENT}</td>
                        <td class="text-center" style="vertical-align: middle">${r.SUPPLIER_NAME}</td>
                        <td class="text-center" style="vertical-align: middle">${r.PEMBAYARAN}</td>
                        <td class="text-center" style="vertical-align: middle">#${r.SLIP_NO ?? '-'}</td>
                        <td class="text-center" style="vertical-align: middle">
                            <button class="btn btn-sm btn-primary exportPdf" data-payment="${r.PAYMENT}" data-plant="${r.PLANT}">PDF</button>
                            
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${r.PAYMENT}" data-plant="${r.PLANT}">Hapus</button>
                        </td>
                    </tr>
                `);
            });

            $('#pagination').html(resp.pagination);
        });
    }

    function updateGrandTotal()
    {
        let total = 0;

        $('#detailTable tbody tr').each(function(){

            let rowTotal =
                cleanNumber(
                    $(this)
                        .find('.total-cell')
                        .text()
                );

            total += rowTotal;
        });

        $('#grandTotal')
            .val(
                formatRupiah(total)
            );
    }

    $('#btnChooseReceive').click(function(){

        $('.pickReceive:checked').each(function(){

            let id = $(this).data('id');

            let receive = $(this).data('receive');

            let material = $(this).data('material');

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE
            |--------------------------------------------------------------------------
            */

            let exists = false;

            $('#detailTable tbody tr').each(function(){

                if (
                    $(this).data('id') == id &&
                    $(this).data('receive') == receive &&
                    $(this).data('material') == material
                ) {

                    exists = true;
                }
            });

            if (exists) {
                return;
            }

            let qty =
                parseFloat($(this).data('qty')) || 0;

            let harga =
                parseFloat($(this).data('harga')) || 0;

            let total =
                parseFloat($(this).data('total')) || 0;

            let row = `
                <tr
                    data-receive="${receive}"
                    data-material="${material}">

                    <td>
                        ${receive}

                        <input type="hidden"
                            name="DETAIL[][RECEIVE_NO]"
                            value="${receive}">
                    </td>

                    <td>
                        ${$(this).data('material-name')}

                        <input type="hidden"
                            name="DETAIL[][MATERIAL]"
                            value="${material}">
                    </td>

                    <td class="text-center">

                        ${formatRupiah(
                            $(this).data('berat')
                        )}

                        <input type="hidden"
                            name="DETAIL[][BERAT]"
                            value="${$(this).data('berat')}">
                    </td>

                    <td class="text-center">

                        ${formatRupiah(qty)}

                        <input type="hidden"
                            name="DETAIL[][JUMLAH]"
                            value="${qty}">
                    </td>

                    <td class="text-end">

                        ${formatRupiah(harga)}

                        <input type="hidden"
                            name="DETAIL[][HARGA]"
                            value="${harga}">
                    </td>

                    <td class="text-end total-cell">

                        ${formatRupiah(total)}

                    </td>

                    <td>

                        <input
                            type="text"
                            class="form-control"
                            name="DETAIL[][REMARK]">
                    </td>

                    <td class="text-center">

                        <button
                            type="button"
                            class="btn btn-danger btn-sm removeDetail">

                            X

                        </button>

                    </td>

                </tr>
            `;

            $('#detailTable tbody')
                .append(row);

            /*
            |--------------------------------------------------------------------------
            | AUTO SUPPLIER
            |--------------------------------------------------------------------------
            */

            $('#hiddensupplierAdd')
                .val($(this).data('supplier'));

        });

        updateGrandTotal();

        $('#modalPickReceive')
            .modal('hide');

    });

    $(document).on(
        'click',
        '.removeDetail',
        function(){

            $(this)
                .closest('tr')
                .remove();

            updateGrandTotal();
        }
    );

    $(document).on("click", ".exportPdf", function () {
        let payment    = $(this).data("payment");
        let plant = $(this).data("plant");

        window.open(
            "<?= base_url('payment/print_pdf'); ?>?payment=" + payment + "&plant=" + plant,
            "_blank"
        );
    });

    /* =========================
    SELECT2 SUPPLIER
    ========================= */
    function initSupplierSelect2(selector, modal){
        $(selector).select2({
            placeholder:'Pilih Supplier',
            dropdownParent:$(modal),
            width:'100%',
            ajax:{
                url:'<?= base_url("payment/get_supplier"); ?>',
                dataType:'json',
                delay:250,
                data:p=>({q:p.term}),
                processResults:d=>({results:d})
            }
        }).on('select2:select', function(e){
            $(this).closest('form')
                .find('input[name="SUPPLIER"]')
                .val(e.params.data.id);
        });
    }

    function setDefaultSupplierCS000001() {
        $.ajax({
            url: '<?= base_url("payment/get_supplier"); ?>',
            dataType: 'json',
            data: { q: 'CS000001' },
            success: function (data) {

                if (!data || data.length === 0) return;

                let supplier = data.find(s => s.id === 'CS000001');
                if (!supplier) return;

                let option = new Option(supplier.text, supplier.id, true, true);
                $('#supplierAdd').append(option).trigger('change');

                $('#hiddensupplierAdd').val(supplier.id);
            }
        });
    }


    $('#supplierAdd').on('select2:select', function(e){
        let supplierId = e.params.data.id;
        $('#hiddensupplierAdd').val(supplierId);

        // update semua row receive select
        $('#detailTable tbody tr').each(function(){
            let $receiveSelect = $(this).find('.receive-select');
            $receiveSelect.empty().trigger('change');
        });
    });

    $('#supplierEdit').on('select2:select', function(e){
        let supplierId = e.params.data.id;
        $('#hiddensupplierEdit').val(supplierId);

        $('#detailTableEdit tbody tr').each(function(){
            let $select = $(this).find('.receive-select');
            $select.empty().trigger('change');
        });
    });

    $('#supplierAdd').on('change', function(){

        $('#detailTable tbody')
            .html('');
    
        updateGrandTotal();

    });

    $('#addRow').click(function(){

        let supplier =
            $('#hiddensupplierAdd').val();

        if(!supplier){

            alert(
                'Pilih supplier terlebih dahulu'
            );

            return;
        }

        loadReceivePicker();

        $('#modalPickReceive')
            .modal('show');

    });

    
    let searchReceiveTimer; $('#searchReceive').on('keyup', function(){ clearTimeout(searchReceiveTimer); searchReceiveTimer = setTimeout(() => { loadReceivePicker(); }, 400); });

    function loadReceivePicker() { $.get( '<?= base_url("payment/load_receive_picker"); ?>', { plant : $('#plantAdd').val(),supplier :
                $('#hiddensupplierAdd').val(), search : $('#searchReceive').val() }, function(rows){ let tbody = $('#receiveListBody'); tbody.html(''); rows.forEach(function(r){ let tr = ` <tr> <td class="text-center"> <input type="checkbox" class="pickReceive" data-id="${r.ID}" data-receive="${r.RECEIVE}" data-material="${r.MATERIAL}" data-material-name="${r.MATERIAL_NAME}" data-qty="${r.JUMLAH}" data-berat="${r.BERAT}" data-harga="${r.HARGA}" data-total="${r.TOTAL}" data-supplier="${r.SUPPLIER}" data-supplier-name="${r.SUPPLIER_NAME}"> </td> <td> ${r.RECEIVE} </td> <td> ${r.SUPPLIER_NAME || '-'} </td> <td> ${r.MATERIAL_NAME || '-'} </td> <td class="text-center"> ${formatRupiah(r.JUMLAH)} </td> <td class="text-center"> ${formatRupiah(r.BERAT)} </td> <td class="text-end"> ${formatRupiah(r.HARGA)} </td> <td class="text-end"> ${formatRupiah(r.TOTAL)} </td> </tr> `; tbody.append(tr); }); }, 'json' ); }

    $('#addRowEdit').on('click', function () {

        let supplier = $('#hiddensupplierEdit').val();
        let plant    = $('#plantEdit').val();

        if (!plant) {
            alert('Plant tidak valid');
            return;
        }

        if (!supplier) {
            alert('Pilih supplier terlebih dahulu');
            return;
        }

        loadReceiveModal({
            supplier: supplier,
            plant: plant,
            targetTable: '#detailTableEdit'
        });
    });

    // Modal Edit
    // $('#addRowEdit').off('click').on('click', function(){
    //     let count = $('#detailTableEdit tbody tr').length;
    //     addDetailRowEdit({}, count, $('#hiddensupplierEdit').val());
    // });

    $(document).on('click','.btn-choose-receive',function(){
        let supplier = $('#hiddensupplierAdd').val();
        if(!supplier) return alert('Pilih supplier terlebih dahulu');
        loadReceiveModal(supplier);
    });

    $(document).on('click', '.btn-select-receive', function () {
        const $btn = $(this);
        if ($btn.data('locked')) return;
        $btn.data('locked', true);

        let table = normalizeTableSelector($btn.data('target'));

        if (!table) {
            alert('Target table tidak valid');
            $btn.data('locked', false);
            return;
        }

        let data = {
            PLANT: $btn.data('plant'),
            RECEIVE_NO: $btn.data('receive'),
            MATERIAL: $btn.data('material'),
            MATERIAL_NAME: $btn.data('material-name'),
            BERAT: Number($btn.data('berat')) || 0,
            JUMLAH: Number($btn.data('qty')) || 0,
            HARGA: Number($btn.data('harga')) || 0
        };

        if (isMaterialExist(data.PLANT, data.RECEIVE_NO, data.MATERIAL, table)) {
            alert('Material ini sudah dipilih');
            $btn.data('locked', false);
            return;
        }

        addDetailRow(table, data);

        $('#modalReceive').modal('hide');

        setTimeout(() => $btn.data('locked', false), 300);
    });

    function reindexDetail(form) {
        $(form).find('tbody tr').each(function (i) {
            $(this).find('input').each(function () {
                this.name = this.name.replace(/DETAIL\[\d+\]/, `DETAIL[${i}]`);
            });
        });
    }

    $(document).on('input','input[name$="[JUMLAH]"], input[name$="[HARGA]"]', calculateGrandTotal);

    $(document).on('click', '.btn-remove', function () {
        let table = '#' + $(this).closest('table').attr('id');
        $(this).closest('tr').remove();
        calculateGrandTotal(table);
    });

    function calculateGrandTotal(table) {
        let total = 0;

        $(`${table} tbody tr`).each(function () {
            total += Number($(this).find('.total').val()) || 0;
        });

        const target = table === '#detailTableEdit'
            ? '#grandTotalEdit'
            : '#grandTotal';

        $(target).val(formatRupiah(total));
    }

    $(document).on('input', '.jumlah, .harga', function () {
        let $row = $(this).closest('tr');

        let jumlah = Number($row.find('.jumlah').val()) || 0;
        let harga  = Number($row.find('.harga').val()) || 0;

        let total = jumlah * harga;
        $row.find('.total').val(total);

        let table = '#' + $row.closest('table').attr('id');
        calculateGrandTotal(table);
    });

    function isMaterialExist(plant, receive, material, table){
        let exist = false;

        $(table+' tbody tr').each(function(){
            const p = $(this).find('input[name$="[PLANT]"]').val();
            const r = $(this).find('input[name$="[RECEIVE_NO]"]').val();
            const m = $(this).find('input[name$="[MATERIAL]"]').val();

            if (
                String(p) === String(plant) &&
                String(r) === String(receive) &&
                String(m) === String(material)
            ) {
                exist = true;
                return false;
            }
        });

        return exist;
    }

    function formatDateIndo(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }

    function rupiah(n){
        n = Number(n) || 0;
        return n.toLocaleString('id-ID');
    }

    function loadReceiveModal({ supplier, plant = null, targetTable }) {

        if (!supplier) {
            alert('Supplier tidak valid');
            return;
        }

        $.get(
            '<?= base_url("payment/load_receive_modal"); ?>',
            {
                supplier: supplier,
                plant: plant
            },
            function (res) {

                let tbody = '';

                if (!res || !res.length) {
                    tbody = `
                        <tr>
                            <td colspan="10" class="text-center" style="vertical-align: middle">Tidak ada data</td>
                        </tr>`;
                } else {
                    res.forEach(r => {
                        tbody += `
                            <tr>
                                <td class="text-center" style="vertical-align: middle">${r.PLANT_NAME}</td>
                                <td class="text-center" style="vertical-align: middle">${formatDateIndo(r.RECEIVE_DATE)}</td>
                                <td class="text-center" style="vertical-align: middle">#${r.RECEIVE}</td>
                                <td class="text-center" style="vertical-align: middle">
                                    <b>(${r.SUPPLIER})</b><br>${r.SUPPLIER_NAME}
                                </td>
                                <td class="text-center" style="vertical-align: middle">
                                    <b>(${r.MATERIAL})</b><br>${r.MATERIAL_NAME}
                                </td>
                                <td class="text-end" style="vertical-align: middle">${formatNumberID(r.TOTAL_BERAT)}</td>
                                <td class="text-end" style="vertical-align: middle">${formatNumberID(r.TOTAL_QTY)}</td>
                                <td class="text-end" style="vertical-align: middle">${formatRupiah(r.HARGA)}</td>
                                <td class="text-end" style="vertical-align: middle">${formatRupiah(r.TOTAL)}</td>
                                <td class="text-center" style="vertical-align: middle">
                                    <button type="button"
                                        class="btn btn-success btn-sm btn-select-receive"
                                        data-target="${targetTable}"
                                        data-plant="${r.PLANT}"
                                        data-receive="${r.RECEIVE}"
                                        data-material="${r.MATERIAL}"
                                        data-material-name="${r.MATERIAL_NAME}"
                                        data-berat="${r.TOTAL_BERAT}"
                                        data-qty="${r.TOTAL_QTY}"
                                        data-harga="${r.HARGA}">
                                        Pilih
                                    </button>
                                </td>
                            </tr>`;
                    });
                }

                $('#tableReceive tbody').html(tbody);
                $('#modalReceive').modal('show');
            },
            'json'
        );
    }

    function formatNumberID(value, digit = 2) {
        if (!value) return '0,00';

        // pastikan numeric
        value = Number(value);

        return value.toLocaleString('id-ID', {
            minimumFractionDigits: digit,
            maximumFractionDigits: digit
        });
    }

    function formatDecimal(value, digit = 2) {
        return Number(value || 0)
            .toLocaleString('id-ID', {
                minimumFractionDigits: digit,
                maximumFractionDigits: digit
            });
    }

    function formatRupiah(value) {
        return Number(value || 0).toLocaleString('id-ID');
    }

    /* =========================
    DETAIL ROW
    ========================= */
    function addDetailRow(table, data) {
        table = normalizeTableSelector(table);
        if (!table) return;

        let idx = $(`${table} tbody tr`).length;

        const berat  = Number(data.BERAT)  || 0;
        const jumlah = Number(data.JUMLAH) || 0;
        const harga  = Number(data.HARGA)  || 0;
        const total  = jumlah * harga;

        let row = `
            <tr>
                <td style="vertical-align: middle">
                    #${data.RECEIVE_NO}
                    <input type="hidden" name="DETAIL[${idx}][SEQ_NO]" value="${data.SEQ_NO}">
                    <input type="hidden" name="DETAIL[${idx}][PLANT]" value="${data.PLANT}">
                    <input type="hidden" name="DETAIL[${idx}][RECEIVE_NO]" value="${data.RECEIVE_NO}">
                </td>
                <td style="vertical-align: middle">
                    ${data.MATERIAL} - ${data.MATERIAL_NAME}
                    <input type="hidden" name="DETAIL[${idx}][MATERIAL]" value="${data.MATERIAL}">
                </td>
                <td>
                    <input type="text"
                        class="form-control text-end berat"
                        value="${formatDecimal(berat)}" readonly>
                    <input type="hidden" name="DETAIL[${idx}][BERAT]" value="${berat}">
                </td>
                <td>
                    <input type="text"
                        class="form-control text-end jumlah"
                        value="${formatRupiah(jumlah)}" readonly>
                    <input type="hidden" name="DETAIL[${idx}][JUMLAH]" value="${jumlah}">
                </td>
                <td>
                    <input type="text"
                        class="form-control text-end harga"
                        value="${formatRupiah(harga)}" readonly>
                    <input type="hidden" name="DETAIL[${idx}][HARGA]" value="${harga}">
                </td>
                <td>
                    <input type="text"
                        class="form-control text-end total"
                        value="${formatRupiah(total)}" readonly>
                    <input type="hidden" name="DETAIL[${idx}][TOTAL]" value="${total}">
                </td>
                <td>
                    <input name="DETAIL[${idx}][REMARK]" class="form-control" value="${data.REMARK || ''}" placeholder="Remark disini...">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-remove">×</button>
                </td>
            </tr>
        `;

        $(`${table} tbody`).append(row);
        calculateGrandTotal(table);
    }

    function normalizeTableSelector(table) {
        if (!table) return null;
        if (typeof table === 'string') return table;
        if (table instanceof jQuery) return '#' + table.attr('id');
        if (table.nodeType === 1) return '#' + table.id;
        return null;
    }

    function initReceiveSelect2(selector, modal, supplier){
        selector.select2({
            placeholder: 'Pilih Receive',
            dropdownParent: $(modal),
            width: '100%',
            ajax: {
                url: '<?= base_url("payment/get_receive_by_supplier"); ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        supplier: supplier
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(r => ({
                            id: r.RECEIVE,
                            text: `#${r.RECEIVE} | ${r.PLANT_NAME} | ${r.RECEIVE_DATE}`
                        }))
                    };
                }
            }
        });
    }

    function initPlantSelect2(selector, modalId){

        if ($(selector).hasClass("select2-hidden-accessible")) {

            $(selector).select2('destroy');

        }

        $(selector).select2({

            placeholder: "Pilih PLANT",

            dropdownParent: $(modalId),

            width: "100%",

            multiple: false,

            ajax: {

                url: "<?= base_url('mcost/get_plant'); ?>",

                dataType: "json",

                delay: 250,

                processResults: function (data) {

                    return {

                        results: data.map(p => ({

                            id: p.id,

                            text: p.text

                        }))

                    };

                }

            }

        });

        $(selector).on(
            'select2:select',
            function(e){

                $('#PLANT_ADD').val(
                    e.params.data.id
                );
            }
        );

        $.getJSON(
            "<?= base_url('mcost/get_plant'); ?>",
            function(data){

                if(
                    data &&
                    data.length > 0
                ){

                    let first = data[0];

                    let option = new Option(

                        first.text,
                        first.id,
                        true,
                        true

                    );

                    $(selector)
                        .append(option)
                        .trigger('change');

                    $('#PLANT_ADD').val(
                        first.id
                    );
                }

            }
        );
    }

    $(function(){

        loadPage(1);

        initSupplierSelect2('#supplierAdd','#paymentAdd');
        setDefaultSupplierCS000001();
        initSupplierSelect2('#supplierEdit','#paymentEdit');

        initPlantSelect2('#plantAdd','#paymentAdd');
        initPlantSelect2('#plantEdit','#paymentEdit');

        $('#detailTable').on('click','.btn-remove', function(){ $(this).closest('tr').remove(); });

        /* =========================
        CREATE
        ========================= */
        $('#fPaymentAdd').submit(function(e){
            e.preventDefault();

            $.post('<?= base_url("payment/create"); ?>', $(this).serialize(), function(res){
                res = typeof res==='string'?JSON.parse(res):res;
                alert(res.message);
                if(res.status){
                    $('#paymentAdd').modal('hide');
                    $('#fPaymentAdd')[0].reset();
                    loadPage(state.page);
                }
            },'json');
        });

        /* =========================
        EDIT
        ========================= */
        $(document).on('click', '.editBtn', function () {
            activeDetailTable = '#detailTableEdit';
            let payment = $(this).data('payment');
            let plant   = $(this).data('plant');

            if (!payment || !plant) {
                alert('Payment atau Plant tidak ditemukan');
                return;
            }

            $.get('<?= base_url("payment/edit"); ?>', {
                payment: payment,
                plant: plant
            }, function(resp){

                if (typeof resp === 'string') resp = JSON.parse(resp);
                if (!resp.status){
                    alert(resp.message);
                    return;
                }

                let d = resp.data;
                let form = $('#fPaymentEdit');

                /* ===== hidden key ===== */
                form.find('[name="PAYMENT"]').remove();
                form.find('[name="PLANT"]').remove();

                form.prepend(`<input type="hidden" name="PAYMENT" value="${d.header.PAYMENT}">`);
                form.prepend(`<input type="hidden" name="PLANT" value="${d.header.PLANT}">`);

                /* ===== header ===== */
                $('#paymentNoEdit').val(d.header.PAYMENT);
                form.find('[name="PAYMENT_DATE"]').val(d.header.PAYMENT_DATE.substr(0,10));
                form.find('[name="SLIP_NO"]').val(d.header.SLIP_NO);
                form.find('[name="REMARK"]').val(d.header.REMARK);
                form.find('[name="PEMBAYARAN"][value="'+d.header.PEMBAYARAN+'"]').prop('checked',true);

                /* ===== supplier ===== */
                let opt = new Option(
                    d.header.SUPPLIER + ' - ' + (d.header.SUPPLIER_NAME||''),
                    d.header.SUPPLIER,
                    true,
                    true
                );

                $('#supplierEdit').empty().append(opt).trigger('change');
                $('#hiddensupplierEdit').val(d.header.SUPPLIER);
                $('#paymentTypeEdit').val(d.header.PAYMENT_TYPE).trigger('change');

                /* ===== detail ===== */
                $('#detailTableEdit tbody').empty();

                (d.detail || []).forEach(r => {
                    addDetailRow('#detailTableEdit', {
                        SEQ_NO: r.SEQ_NO,
                        PLANT: r.PLANT,
                        RECEIVE_NO: r.RECEIVE_NO,
                        MATERIAL: r.MATERIAL,
                        MATERIAL_NAME: r.MATERIAL_NAME,
                        BERAT: r.BERAT,
                        JUMLAH: r.JUMLAH,
                        HARGA: r.HARGA,
                        REMARK: r.REMARK
                    });
                });

                $('#paymentEdit').modal('show');

            }, 'json');
        });

        // submit update
        $('#fPaymentEdit').submit(function(e){
            e.preventDefault();
            let f = $(this);

            $.post('<?= base_url("payment/update"); ?>', f.serialize(), function(res){
                if(typeof res==='string') res = JSON.parse(res);
                alert(res.message);
                if(res.status){
                    $('#paymentEdit').modal('hide');
                    loadPage(state.page);
                }
            },'json');
        });

        /* =========================
        DELETE
        ========================= */
        $(document).on('click', '.deleteBtn', function () {
            if (!confirm('Yakin hapus data ini?')) return;

            let payment = $(this).data('id');
            let plant   = $(this).data('plant');

            $.post('<?= base_url("payment/remove"); ?>', {
                payment: payment,
                plant: plant
            }, function (res) {
                res = typeof res === 'string' ? JSON.parse(res) : res;
                alert(res.message);
                if (res.status) loadPage(state.page);
            }, 'json');
        });

    });
    </script>

    <script>
        $('#paymentAdd').on('shown.bs.modal', function(){
            $('#detailTable tbody').empty();
            $('#grandTotal').val('');
            let today = new Date().toISOString().split('T')[0];
            $('input[name="PAYMENT_DATE"]').val(today).attr('min',today);
            setDefaultSupplierCS000001();
        });

        $('#paymentEdit').on('hidden.bs.modal', function(){
            $('#detailTableEdit tbody').empty();
            $('#grandTotalEdit').val('');
        });
</script>


