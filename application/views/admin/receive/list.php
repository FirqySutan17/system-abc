<!-- application/views/admin/receive/list.php -->
<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">RECEIVE - INVENTORY</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari receive..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group "></div>
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#receiveAdd">
                        <i class="ti ti-plus"></i> Tambah Receive
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>
                            <th data-order="PLANT" style="text-align: center;">Plant</th>
                            <th data-order="RECEIVE" style="text-align: center;">RECEIVE</th>
                            <th data-order="PO" style="text-align: center;">PO</th>
                            <th data-order="RECEIVE_DATE" style="text-align: center;">Tgl Receive</th>
                            <th data-order="SUPPLIER" style="text-align: center;">Supplier</th>
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

<!-- MODAL ADD RECEIVE -->
<div class="modal fade" id="receiveAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="freceiveAdd" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">RECEIVE - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">

                        <div class="col-md-4">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" class="form-control" required></select>
                            <input type="hidden" name="PLANT" id="hiddenPlantAdd">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. Receive</label>
                            <input id="RECEIVE_NO_ADD"
                                class="form-control"
                                placeholder="Auto Generate"
                                readonly
                                style="background:#efefef">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Slip No</label>
                            <input id="SLIP_NO_ADD"
                                class="form-control"
                                placeholder="Auto Generate"
                                readonly
                                style="background:#efefef">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">PO *</label>
                            <select id="poAdd"
                                class="form-control"
                                required
                                style="width:100%"></select>

                            <input type="hidden" name="PO" id="hiddenPoAdd">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Tanggal Receive *</label>
                            <input id="RECEIVE_DATE"
                                name="RECEIVE_DATE"
                                type="date"
                                class="form-control"
                                value="<?= date('Y-m-d'); ?>"
                                required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:0 10px">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="PEMBAYARAN"
                                        value="CASH"
                                        checked>
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

                        <div class="col-md-2">
                            <label class="form-label d-block">Jenis Pembayaran</label>
                            <div style="padding:0 10px">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="JENIS_PAY"
                                        value="LUNAS"
                                        checked>
                                    <label class="form-check-label">LUNAS</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="JENIS_PAY"
                                        value="TEMPO">
                                    <label class="form-check-label">TEMPO</label>
                                </div>
                            </div>
                        </div>

                        <!-- AUTO FROM PO -->
                        <div class="col-md-4">
                            <label class="form-label">Supplier *</label>

                            <input type="text"
                                id="supplierAddText"
                                class="form-control"
                                readonly
                                style="background:#efefef"
                                placeholder="Otomatis dari PO">

                            <input type="hidden"
                                id="hiddensupplierAdd"
                                name="SUPPLIER">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. Nota</label>
                            <input class="form-control"
                                placeholder="No. Nota..."
                                name="NOTA">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. Referensi</label>
                            <input name="NO_REF"
                                class="form-control"
                                placeholder="No DO / Invoice / dll...">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Attachment (Opsional)</label>
                            <input type="file"
                                name="ATTACHMENT"
                                id="ATTACHMENT_ADD"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf,.xlsx,.docx">

                            <div class="form-text">
                                Format: jpg, png, pdf, xlsx, docx (max 10MB)
                            </div>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label class="form-label">Remark</label>
                            <input name="REMARK"
                                class="form-control"
                                placeholder="Input disini..">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="mb-2">
                        <h5 style="margin-bottom:0">Material</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="receiveDetailTableAdd">
                            <thead>
                                <tr>
                                    <th style="text-align:center">Customer</th>
                                    <th style="text-align:center">Material</th>
                                    <th style="text-align:center">Jumlah</th>
                                    <th style="text-align:center">Berat</th>
                                    <th style="text-align:center">Harga</th>
                                    <th style="text-align:center">Total</th>
                                    <th style="text-align:center">Susut Jumlah</th>
                                    <th style="text-align:center">Susut Berat</th>
                                    <th style="text-align:center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
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
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT RECEIVE (PO readonly style="background: #efefef") -->
<div class="modal fade" id="receiveEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="freceiveEdit" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">RECEIVE - EDIT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <input type="hidden" id="PLANT_EDIT" name="PLANT">
                        <div class="col-md-4">
                            <label class="form-label">Plant</label>
                            <input id="PLANT_EDIT_DISPLAY"
                                class="form-control"
                                readonly
                                style="background:#efefef">
                            <input type="hidden" id="PLANT_EDIT" name="PLANT">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. Receive</label>
                            <input name="RECEIVE" id="RECEIVE_EDIT" class="form-control" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Slip No</label>
                            <input name="SLIP_NO" id="SLIP_NO_EDIT" class="form-control" readonly style="background: #efefef">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PO</label>
                            <input id="PO_EDIT_DISPLAY" class="form-control" readonly style="background: #efefef">
                            <input type="hidden" id="PO_EDIT_HIDDEN" name="PO">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tanggal Receive *</label>
                            <input name="RECEIVE_DATE" id="RECEIVE_DATE_EDIT" type="date" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block">Pembayaran</label>
                            <div style="padding:10px 10px">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-pay" type="radio" 
                                           name="PEMBAYARAN_EDIT" id="edit_pay_cash" value="CASH">
                                    <label class="form-check-label" for="edit_pay_cash">CASH</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-pay" type="radio" 
                                           name="PEMBAYARAN_EDIT" id="edit_pay_transfer" value="TRANSFER">
                                    <label class="form-check-label" for="edit_pay_transfer">TRANSFER</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block">Jenis Pembayaran</label>
                            <div style="padding:10px 10px">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-tipe-pay" type="radio" 
                                           name="JENIS_PAY_EDIT" id="edit_pay_lunas" value="LUNAS">
                                    <label class="form-check-label" for="edit_pay_lunas">LUNAS</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-tipe-pay" type="radio" 
                                           name="JENIS_PAY_EDIT" id="edit_pay_tempo" value="TEMPO">
                                    <label class="form-check-label" for="edit_pay_tempo">TEMPO</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier *</label>
                            <select id="supplierEdit" class="form-control" required></select>
                            <input type="hidden" id="hiddensupplierEdit" name="SUPPLIER">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. Nota</label>
                            <input name="NOTA" id="NOTA_EDIT" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. Referensi</label>
                            <input name="NO_REF" id="NO_REF_EDIT" class="form-control" placeholder="No DO / Invoice / dll">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment</label>

                            <input type="file" name="ATTACHMENT" id="ATTACHMENT_EDIT"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf,.xlsx,.docx"><br>
                            <div id="ATTACH_PREVIEW" class="mb-2"></div>
                            <div class="form-text">
                                Upload ulang untuk mengganti attachment
                            </div>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Remark</label>
                            <input name="REMARK" id="REMARK_EDIT" class="form-control" placeholder="Input disini..">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom:0px">Material</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addDetailRowEdit">Tambah Material</button>
                    </div>

                    <table class="table table-bordered" id="receiveDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="text-align:center;">Material</th>
                                <th style="text-align:center;">Berat</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:center;">Harga</th>
                                <th style="text-align:center;">Total</th>
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

<!-- DETAIL modal -->
<div class="modal fade" id="receiveDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Receive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="receiveDetailBody">Loading...</div>
        </div>
    </div>
</div>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'RECEIVE', dir: 'DESC' };

    $('#search').on('keyup', function(){
        state.search = $(this).val();
        loadPage(1);
    });

    function initPlantSelect2(selector, modalId){
        $(selector).select2({
            placeholder: "-- PILIH PLANT --",
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('receive/get_plant'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function(data){
                    return { results: data };
                }
            }
        })
        .on('select2:select', function(e){

            $('#hiddenPlantAdd').val(e.params.data.id);

            // reset PO
            $('#poAdd').empty().trigger('change');
            $('#hiddenPoAdd').val('');

            // reset supplier
            $('#supplierAddText').val('');
            $('#hiddensupplierAdd').val('');

            // reset detail
            $('#receiveDetailTableAdd tbody').empty();
        });
    }

    function setDefaultPlant(selector){
        $.ajax({
            url: "<?= base_url('receive/get_plant'); ?>",
            dataType: "json",
            success: function(data){

                if(data.length > 0){

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

                    $('#hiddenPlantAdd').val(first.id);
                }
            }
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
        $.get('<?= base_url("receive/load_data"); ?>', state, function(resp){
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var tbody = $('#table-body').empty();

            resp.rows.forEach(function(row){
                var tr = `<tr>
                    <td style="text-align:center; vertical-align: middle"><b>${row.AJ_NAME}</b></td>
                    <td style="text-align:center; vertical-align: middle"><b>#${row.RECEIVE}</b></td>

                    <!-- ===============================
                        PO: jika null → Receive tanpa PO
                    ================================ -->
                    <td style="text-align:center; vertical-align: middle">
                        ${(row.PO && row.PO !== "" && row.PO !== null) ? '#' + row.PO : 'Receive tanpa PO'}
                    </td>

                    <!-- ===============================
                        Tanggal format d M Y
                    ================================ -->
                    <td style="text-align:center; vertical-align: middle">${formatDate(row.RECEIVE_DATE)}</td>

                    <td style="text-align:center; vertical-align: middle">${row.SUPPLIER_NAME}<br><b>${row.SUPPLIER}</b></td>
                    <td style="text-align:center; vertical-align: middle">${row.REMARK ?? ''}</td>
                    <td style="text-align:center; vertical-align: middle">
                        <button class="btn btn-sm btn-primary me-1 exportPdf" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">SLIP</button>
                        <button class="btn btn-sm btn-warning me-1 editBtn" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">Edit</button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-receive="${row.RECEIVE}" data-plant="${row.PLANT}">Hapus</button>
                    </td>
                </tr>`;
                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            $('#info').text(`Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`);
        });
    }

    /* -------------------------
    Select2 inits
    ------------------------- */
    function initSupplierSelect2(selector, modalId){
        $(selector).select2({
            placeholder: "Pilih SUPPLIER",
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('receive/get_supplier'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){ return { q: params.term }; },
                processResults: function(data){ return { results: data }; }
            }
        }).on('select2:select', function(e){
            let isEdit = $(this).attr('id') === 'supplierEdit';
            if (isEdit) {
                $('#hiddensupplierEdit').val(e.params.data.id);
            } else {
                $('#hiddensupplierAdd').val(e.params.data.id);
            }
        });
    }

    function setDefaultSupplier(selector){
        $.ajax({
            url: "<?= base_url('receive/get_supplier'); ?>",
            dataType: "json",
            success: function(data){
                let found = data.find(x => x.id === 'CS000001');
                if(found){
                    let option = new Option(found.text, found.id, true, true);
                    $(selector).append(option).trigger('change');
                    $('#hiddensupplierAdd').val(found.id);
                }
            }
        });
    }

    function setSupplierFromPO(selector, supplierId, supplierText){
        let option = new Option(supplierText, supplierId, true, true);
        $(selector).empty().append(option).trigger('change');
        $('#hiddensupplierAdd').val(supplierId);
    }

    function setSupplier(selector, supplierId, supplierText){
        let option = new Option(supplierText, supplierId, true, true);
        $(selector)
            .empty()                 // hapus supplier lama
            .append(option)
            .trigger('change');

        $('#hiddensupplierAdd').val(supplierId);
    }

    function initMaterialSelect2(el, parentModal){
        $(el).select2({
            placeholder: "Pilih MATERIAL",
            width:'100%',
            dropdownParent: $(parentModal),
            ajax:{
                url:'<?= base_url("receive/get_material"); ?>',
                dataType:'json',
                delay:250,
                data: function(params){ return { q: params.term }; },
                processResults: function(data){ return { results: data }; }
            }
        });
    }

    function formatMoneyID(value){
        if(value === null || value === '' || isNaN(value)) return '';

        return parseFloat(value).toLocaleString('id-ID',{
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function initPOSelect2(selector, modalId){

        $(selector).select2({
            placeholder: "-- PILIH PO --",
            dropdownParent: $(modalId),
            width: "100%",
            allowClear: false,
            ajax: {
                url: "<?= base_url('receive/get_po'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return {
                        q: params.term,
                        plant: $('#hiddenPlantAdd').val()
                    };
                },
                processResults: function(data){
                    return { results: data };
                }
            }
        })
        .on('select2:select', function(e){

            let data = e.params.data;

            $('#hiddenPoAdd').val(data.id);

            // auto supplier
            $('#supplierAddText').val(data.supplier_text);
            $('#hiddensupplierAdd').val(data.supplier);

            // load detail
            loadPoDetailToTable(data.id, data.plant);
        });
    }

    function formatDecimalID(value, decimal = null) {
        if (value === '' || value === null) return '';

        let number = value.toString()
            .replace(/[^0-9,]/g, '')
            .replace(',', '.');

        if (number === '') return '';

        let num = parseFloat(number);
        if (isNaN(num)) return '';

        // kalau decimal = null → JANGAN paksa desimal
        if (decimal === null) {
            return num.toLocaleString('id-ID');
        }

        return num.toLocaleString('id-ID', {
            minimumFractionDigits: decimal,
            maximumFractionDigits: decimal
        });
    }

    function unformatNumber(value) {
        if (!value) return 0;
        return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
    }

    /* -------------------------
    Add / remove detail rows
    ------------------------- */
    function addDetailRow(data, targetTable, isLoaded = false, isFromPO = false) {

        let tbody = $(`${targetTable} tbody`);
        if (!tbody.length) return;

        data = data || {};

        let material      = data.material || "";
        let material_text = data.material_text || "";
        let berat  = data.berat  ? Number(data.berat)  : "";
        let jumlah = data.jumlah ? Number(data.jumlah) : "";
        let harga  = data.harga  ? Number(data.harga)  : "";
        let total  = data.total  ? Number(data.total)  : "";

        let modalParent = targetTable.includes('Edit')
            ? '#receiveEdit'
            : '#receiveAdd';

        let lock = isFromPO ? 'disabled' : '';
        let seqNo = data.seq_no || '';

        let row = `
            <tr data-seq="${seqNo}">
                <td style="width: 30%">
                    <select class="form-control material-select" ${lock} ></select>
                </td>
                <td><input style="text-align: right" class="form-control berat" value="${berat}"></td>
                <td><input style="text-align: right" class="form-control jumlah" value="${jumlah}"></td>
                <td><input style="text-align: right" class="form-control harga" value="${harga}"></td>
                <td><input style="text-align: right" class="form-control total" value="${total}" readonly></td>
                <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
            </tr>
        `;

        tbody.append(row);
        let $row = tbody.find('tr').last();

        $row.find('.berat, .jumlah').on('input', function(){
            calcReceiveRow($row); // HITUNG SAJA
        });

        $row.find('.berat').on('blur', function(){
            this.value = formatDecimalID(this.value, 2);
        });

        $row.find('.jumlah').on('blur', function(){
            this.value = formatDecimalID(this.value, 2);
        });

        $row.find('.harga').on('input', function(){
            calcReceiveRow($row);
        });

        $row.find('.harga').on('blur', function(){
            this.value = formatDecimalID(this.value, 0);
        });
        $row.find('.total').on('blur', function(){
            this.value = formatDecimalID(this.value, 0);
        });

        if (isLoaded) {
            $row.find('.berat').trigger('blur');
            $row.find('.jumlah').trigger('blur');
            $row.find('.harga').trigger('blur');
            $row.find('.total').trigger('blur');
        }

        let $select = tbody.find('.material-select').last();

        /** INIT SELECT2 **/
        $select.select2({
            placeholder: "Pilih MATERIAL",
            width: '100%',
            dropdownParent: $(modalParent),
            ajax: {
                url: '<?= base_url("receive/get_material"); ?>',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
            }
        });

        /** AUTO SET VALUE (EDIT / PO) **/
        if (material && material_text) {
            let option = new Option(material_text, material, true, true);
            $select.append(option).trigger('change');
        }

        /** LOCK MATERIAL FROM PO **/
        if (isFromPO) {
            $select.prop('disabled', true);
        }
    }

    function calcReceiveRow($row){
        let jumlah = cleanNumber($row.find('.jumlah').val());
        let harga  = cleanNumber($row.find('.harga').val());

        let total = jumlah * harga;

        $row.find('.total').val(
            total ? formatDecimalID(total, 0) : ''
        );
    }

    function calcRow(el){
        let tr = $(el).closest('tr');
        let qty   = cleanNumber(tr.find('.jumlah').val());
        let price = cleanNumber(tr.find('.harga').val());

        let total = qty * price;
        tr.find('.total').val(total);
    }

    function updateTotalRow(row){
        var jumlah = cleanNumber(row.find('.jumlah').val());
        var harga  = cleanNumber(row.find('.harga').val());
        row.find('.total').val(formatRupiah((jumlah * harga).toString()));
    }

    /* -------------------------
    Load PO detail into table
    ------------------------- */
    function loadPoDetailToTable(po, plant){

        $.get("<?= base_url('receive/load_po_detail'); ?>", {
            po: po,
            plant: plant
        }, function(resp){

            resp = typeof resp === 'string'
                ? JSON.parse(resp)
                : resp;

            if(!resp.status){
                alert(resp.message);
                return;
            }

            let tbody = $('#receiveDetailTableAdd tbody');
            tbody.empty();

            resp.detail.forEach(function(row){

                tbody.append(`
                    <tr data-po-seq="${row.SEQ_NO}">
                        <td>
                            <input type="hidden" class="customer-code" value="${row.CUSTOMER ?? ''}">
                            <input type="text"
                                class="form-control"
                                value="${row.CUSTOMER ?? ''} - ${row.CUSTOMER_NAME ?? ''}"
                                readonly
                                style="background:#efefef">
                        </td>

                        <td>
                            <input type="hidden" class="material-code" value="${row.MATERIAL}">
                            <input type="text"
                                class="form-control"
                                value="${row.MATERIAL} - ${row.MATERIAL_NAME ?? ''}"
                                readonly
                                style="background:#efefef">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control jumlah"
                                value="${formatDecimalID(row.JUMLAH)}"
                                readonly
                                style="background:#efefef;text-align:right">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control berat"
                                value="${formatDecimalID(row.BERAT)}"
                                readonly
                                style="background:#efefef;text-align:right">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control harga"
                                value="${formatMoneyID(row.HARGA)}"
                                readonly
                                style="background:#efefef;text-align:right">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control total"
                                value="${formatMoneyID(row.TOTAL)}"
                                readonly
                                style="background:#efefef;text-align:right">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control susut-jumlah decimal-input"
                                placeholder="0,00"
                                style="text-align:right">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control susut-berat decimal-input"
                                placeholder="0,00"
                                style="text-align:right">
                        </td>

                        <td>
                            <input type="text"
                                class="form-control keterangan"
                                placeholder="Input keterangan">
                        </td>
                    </tr>
                `);
            });

        }, 'json');
    }

    /* -------------------------
    DOM Ready
    ------------------------- */
    $(function(){
        loadPage(1);

        // init select2 supplier & PO
        initPlantSelect2('#plantAdd', '#receiveAdd');
        setDefaultPlant('#plantAdd');
        initSupplierSelect2('#supplierAdd', '#receiveAdd');
        initSupplierSelect2('#supplierEdit', '#receiveEdit');

        initPOSelect2('#poAdd', '#receiveAdd');
        setDefaultSupplier('#supplierAdd');

        // add row
        $('#addDetailRowAdd').click(function(){
            addDetailRow(null, '#receiveDetailTableAdd');
        });
        $('#addDetailRowEdit').click(function(){
            addDetailRow({}, '#receiveDetailTableEdit');
        });

        // paste PO detail button
        $('#pastePoDetail').click(function(){
            var po = $('#poAdd').val();
            if(!po){ alert('Pilih PO terlebih dahulu, atau kosongkan jika tidak menggunakan PO'); return; }
            loadPoDetailToTable(po, '#receiveAdd');
        });

        // remove row
        $('#receiveDetailTableAdd, #receiveDetailTableEdit').on('click','.removeRow', function(){ $(this).closest('tr').remove(); });

        // update total
        // $('#receiveDetailTableAdd, #receiveDetailTableEdit').on('input','.jumlah,.harga', function(){ updateTotalRow($(this).closest('tr')); });

        // Submit Add
        $('#freceiveAdd').submit(function(e){
            e.preventDefault();

            let btn = $(this).find('button[type=submit]');
            btn.prop('disabled', true);

            let formData = new FormData();

            formData.append('PLANT', $('#hiddenPlantAdd').val());
            formData.append('PO', $('#hiddenPoAdd').val());
            formData.append('RECEIVE_DATE', $('#RECEIVE_DATE').val());
            formData.append('PEMBAYARAN', $('input[name="PEMBAYARAN"]:checked').val());
            formData.append('JENIS_PAY', $('input[name="JENIS_PAY"]:checked').val());
            formData.append('NOTA', $('input[name="NOTA"]').val());
            formData.append('NO_REF', $('input[name="NO_REF"]').val());
            formData.append('REMARK', $('input[name="REMARK"]').val());

            let file = $('#ATTACHMENT_ADD')[0].files[0];
            if(file){
                formData.append('ATTACHMENT', file);
            }

            let DETAIL = [];

            $('#receiveDetailTableAdd tbody tr').each(function(){

                DETAIL.push({
                    PO_SEQ       : $(this).data('po-seq'),
                    CUSTOMER     : $(this).find('.customer-code').val(),
                    MATERIAL     : $(this).find('.material-code').val(),
                    JUMLAH       : parseDecimalID($(this).find('.jumlah').val()),
                    BERAT        : parseDecimalID($(this).find('.berat').val()),
                    HARGA        : parseDecimalID($(this).find('.harga').val()),
                    TOTAL        : parseDecimalID($(this).find('.total').val()),
                    SUSUT_JUMLAH : parseDecimalID($(this).find('.susut-jumlah').val() || 0),
                    SUSUT_BERAT  : parseDecimalID($(this).find('.susut-berat').val() || 0),
                    KETERANGAN   : $(this).find('.keterangan').val()
                });
            });

            formData.append('DETAIL', JSON.stringify(DETAIL));

            $.ajax({
                url: "<?= base_url('receive/create'); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(resp){

                    alert(resp.message);

                    if(resp.status){
                        $('#receiveAdd').modal('hide');
                        $('#freceiveAdd')[0].reset();

                        $('#poAdd').empty().trigger('change');
                        $('#receiveDetailTableAdd tbody').empty();
                        $('#supplierAddText').val('');
                        $('#hiddensupplierAdd').val('');

                        setDefaultPlant('#plantAdd');

                        loadPage(state.page);
                    }
                },
                complete: function(){
                    btn.prop('disabled', false);
                }
            });
        });

        // Click edit
        $(document).on('click','.editBtn', function(){ 
            var receive = $(this).data('receive');
            var plant   = $(this).data('plant'); // ambil plant dari row
            $.get('<?= base_url("receive/edit"); ?>',{receive: receive, plant: plant}, function(resp){
                resp = typeof resp==='string'?JSON.parse(resp):resp;
                if(resp.status){
                    var header = resp.header;
                    var detail = resp.detail;

                    // simpan plant di hidden input
                    $('#PLANT_EDIT').val(plant);
                    $('#PLANT_EDIT_DISPLAY').val(
                        header.PLANT_NAME
                            ? header.PLANT + ' - ' + header.PLANT_NAME
                            : header.PLANT
                    );

                    // header
                    $('#RECEIVE_EDIT').val(header.RECEIVE);
                    $('#SLIP_NO_EDIT').val(header.SLIP_NO);
                    $('#NOTA_EDIT').val(header.NOTA);

                    if (!header.PO) {
                        $('#PO_EDIT_DISPLAY').val('Receive tanpa PO');
                        $('#PO_EDIT_HIDDEN').val('');
                    } else {    
                        $('#PO_EDIT_DISPLAY').val('#' + header.PO);
                        $('#PO_EDIT_HIDDEN').val(header.PO);
                    }

                    $('#RECEIVE_DATE_EDIT').val(header.RECEIVE_DATE ? header.RECEIVE_DATE.substr(0,10) : '');
                    $('#NO_REF_EDIT').val(header.NO_REF);
                    $('#REMARK_EDIT').val(header.REMARK);

                    // pembayaran
                    $('input[name="PEMBAYARAN_EDIT"]').prop('checked', false);
                    if (header.PEMBAYARAN) $('#edit_pay_' + header.PEMBAYARAN.toLowerCase()).prop('checked', true);

                    $('input[name="JENIS_PAY_EDIT"]').prop('checked', false);
                    if (header.JENIS_PAY) $('#edit_pay_' + header.JENIS_PAY.toLowerCase()).prop('checked', true);

                    // supplier
                    var opt = new Option(header.SUPPLIER + ' - ' + (header.SUPPLIER_NAME || ''), header.SUPPLIER, true, true);
                    $('#supplierEdit').empty().append(opt).trigger('change');
                    $('#hiddensupplierEdit').val(header.SUPPLIER);

                    if (header.ATTACH_FILE_NAME) {
                        let url = "<?= base_url(); ?>" + header.ATTACH_PATH;
                        $('#ATTACH_PREVIEW').html(`
                            <a href="${url}" target="_blank" class="btn btn-sm btn-info">
                                Lihat Attachment
                            </a>
                        `);
                    } else {
                        $('#ATTACH_PREVIEW').html('<i>Tidak ada attachment</i>');
                    }

                    // detail
                    $('#receiveDetailTableEdit tbody').empty();
                    detail.forEach(function(row){
                        addDetailRow({
                            material: row.MATERIAL,
                            material_text: row.MATERIAL_NAME,
                            berat: Number(row.BERAT),
                            jumlah: Number(row.JUMLAH),
                            harga: Number(row.HARGA),
                            total: Number(row.TOTAL),
                            seq_no: row.SEQ_NO 
                        }, '#receiveDetailTableEdit', true, false);
                    });

                    $('#addDetailRowEdit').hide(); 
                    $('#receiveDetailTableEdit .removeRow').hide(); 

                    $('#receiveEdit').modal('show');
                } else {
                    alert(resp.message || 'Gagal mengambil data RECEIVE');
                }
            });
        });

        // Submit edit
        $('#freceiveEdit').submit(function(e){
            e.preventDefault();

            let formData = new FormData(this);

            let DETAIL = [];
            $('#receiveDetailTableEdit tbody tr').each(function(){
                DETAIL.push({
                    SEQ_NO: $(this).data('seq') || null,
                    MATERIAL: $(this).find('select').val(),
                    JUMLAH: cleanNumber($(this).find('.jumlah').val()),
                    BERAT: cleanNumber($(this).find('.berat').val()),
                    HARGA: cleanNumber($(this).find('.harga').val()),
                    TOTAL: cleanNumber($(this).find('.total').val())
                });
            });

            formData.append('DETAIL', JSON.stringify(DETAIL));
            formData.append('PLANT', $('#PLANT_EDIT').val());

            $.ajax({
                url: "<?= base_url('receive/update'); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp){
                    alert(resp.message);
                    if(resp.status){
                        $('#receiveEdit').modal('hide');
                        loadPage(state.page);
                    }
                },
                error: function(){
                    alert('Gagal update receive');
                }
            });
        });

        // PDF
        $(document).on("click", ".exportPdf", function () {
            let receive = $(this).data("receive");
            let plant   = $(this).data("plant");

            window.open(
                "<?= base_url('receive/print_slip_pdf'); ?>?receive=" 
                + receive + "&plant=" + plant,
                "_blank"
            );
        });

        // Delete
        $(document).on('click', '.deleteBtn', function() {
            var receive = $(this).data('receive');
            var plant   = $(this).data('plant'); // ambil plant dari row
            if (!confirm("Yakin ingin menghapus RECEIVE: " + receive + " ?")) return;

            $.ajax({
                url: "<?= base_url('receive/remove'); ?>",
                type: "POST",
                data: { receive: receive, plant: plant },
                success: function(res) {
                    res = typeof res === 'string' ? JSON.parse(res) : res;
                    alert(res.message);
                    if (res.status) loadPage(state.page);
                },
                error: function(){ alert("Gagal menghubungi server"); }
            });
        });

    }); // end ready

    function cleanNumber(val) {
        if (val === null || val === undefined || val === '') return 0;

        val = val.toString();

        // format ID:
        // 1.000.000,50 → 1000000.50
        return parseFloat(
            val.replace(/\./g, '').replace(',', '.')
        ) || 0;
    }

    // Format ribuan TAPI tidak mengubah desimal
    function formatRupiah(x){
        if (x === null || x === undefined || x === '') return '';

        let num = cleanNumber(x);
        let parts = num.toString().split('.');

        let integer = parts[0];
        let decimal = parts.length > 1 ? '.' + parts[1] : '';

        let ribuan = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return ribuan + decimal;
    }
</script>

<script>
        $('#receiveAdd').on('shown.bs.modal', function () {
            let today = new Date().toISOString().split("T")[0];
            const dateInput = document.getElementById("RECEIVE_DATE");
            dateInput.value = today; // hari ini
            dateInput.min = today;   // tidak bisa backdate
        });
</script>
