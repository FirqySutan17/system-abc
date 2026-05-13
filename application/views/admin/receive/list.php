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
             <div class="table-box position-relative">

                <div id="tableLoading" class="table-loading d-none">
                    <div class="loading-card">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-3 fw-semibold">Loading data...</div>
                        <small class="text-muted">Please wait a moment</small>
                    </div>
                </div>

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

            </div>
            

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<!-- =========================================
STYLE
========================================= -->
<style>

.receive-card{
    border:1px solid #e5e7eb;
    border-radius:18px;
    overflow:hidden;
}

.receive-card .modal-header{
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;
    border:none;
    padding:18px 24px;
}

.receive-card .modal-title{
    font-weight:700;
    font-size:18px;
    color: #fff;
}

.receive-card .modal-body{
    background:#f8fafc;
    padding:22px;
}

.receive-card .modal-footer{
    border:none;
    padding:18px 24px;
    background:#fff;
}

.receive-card .form-label{
    font-size:12px;
    font-weight:700;
    color:#475569;
    margin-bottom:6px;
}

.receive-card .form-control{
    border-radius:12px;
    min-height:44px;
    border:1px solid #dbe2ea;
    font-size: 12px;
}

.receive-card .form-control:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 0.15rem rgba(37,99,235,.15);
}

.receive-section{
    background:#fff;
    border-radius:18px;
    padding:18px;
    margin-bottom:18px;
    border:1px solid #e2e8f0;
}

.receive-section-title{
    font-size:15px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:16px;
}

.po-master-card{
    border:1px solid #dbeafe;
    border-radius:18px;
    background:#eff6ff;
    padding:18px;
}

.po-master-title{
    font-size:14px;
    font-weight:700;
    margin-bottom:16px;
    color:#1e3a8a;
}

#receiveDetailTableAdd{
    margin-bottom:0;
}

#receiveDetailTableAdd thead th{
    background:#f1f5f9;
    font-size:12px;
    font-weight:700;
    text-align:center;
    vertical-align:middle;
    white-space:nowrap;
}

#receiveDetailTableAdd tbody td{
    vertical-align:middle;
}

.receive-po-row{
    background:#fff;
}

.receive-extra-row{
    background:#fff7ed;
}

.summary-box{
    background:#0f172a;
    color:#fff;
    border-radius:16px;
    padding:18px;
}

.summary-item{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
}

.summary-item:last-child{
    margin-bottom:0;
}

.summary-label{
    opacity:.8;
}

.summary-value{
    font-weight:700;
}

@media(max-width:768px){

    .receive-card .modal-body{
        padding:15px;
    }

    .receive-section{
        padding:15px;
    }

    #receiveDetailTableAdd{
        min-width:1600px;
    }
}

</style>

<!-- =========================================
MODAL
========================================= -->

<div class="modal fade"
    id="receiveAdd"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-fullscreen-lg-down modal-xl">

        <form id="freceiveAdd"
            enctype="multipart/form-data">

            <div class="modal-content receive-card">

                <!-- HEADER -->
                <div class="modal-header">

                    <h5 class="modal-title">
                        RECEIVE - TAMBAH
                    </h5>

                    <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <!-- =====================================
                    HEADER SECTION
                    ====================================== -->

                    <div class="receive-section">

                        <div class="receive-section-title">
                            HEADER RECEIVE
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">
                                    Plant *
                                </label>

                                <select id="plantAdd"
                                    required></select>

                                <input type="hidden"
                                    id="hiddenPlantAdd"
                                    name="PLANT">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    No Receive
                                </label>

                                <input type="text"
                                    id="RECEIVE_NO_ADD"
                                    class="form-control"
                                    readonly
                                    placeholder="Auto Generate"
                                    style="background:#f1f5f9">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Slip No
                                </label>

                                <input type="text"
                                    id="SLIP_NO_ADD"
                                    class="form-control"
                                    readonly
                                    placeholder="Auto Generate"
                                    style="background:#f1f5f9">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    PO *
                                </label>

                                <select id="poAdd"
                                    required></select>

                                <input type="hidden"
                                    id="hiddenPoAdd"
                                    name="PO">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Receive Date
                                </label>

                                <input type="date"
                                    id="RECEIVE_DATE"
                                    name="RECEIVE_DATE"
                                    class="form-control"
                                    value="<?= date('Y-m-d'); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Supplier
                                </label>

                                <input type="text"
                                    id="supplierAddText"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                                <input type="hidden"
                                    id="hiddensupplierAdd"
                                    name="SUPPLIER">
                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Pembayaran *
                                </label>

                                <select id="paymentAdd"
                                    name="PEMBAYARAN"
                                    required>

                                    <option value="">
                                        -- PILIH --
                                    </option>

                                    <option value="CASH">
                                        CASH
                                    </option>

                                    <option value="TRANSFER">
                                        TRANSFER
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Jenis Pay *
                                </label>

                                <select id="jenisPayAdd"
                                    name="JENIS_PAY"
                                    required>

                                    <option value="">
                                        -- PILIH --
                                    </option>

                                    <option value="LUNAS">
                                        LUNAS
                                    </option>

                                    <option value="TEMPO">
                                        TEMPO
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    No Nota
                                </label>

                                <input type="text"
                                    name="NOTA"
                                    class="form-control" placeholder="Opsional..">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    No Ref
                                </label>

                                <input type="text"
                                    name="NO_REF"
                                    class="form-control" placeholder="Opsional..">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Attachment
                                </label>

                                <input type="file"
                                    id="ATTACHMENT_ADD"
                                    name="ATTACHMENT"
                                    class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">
                                    Remark
                                </label>

                                <textarea name="REMARK"
                                    rows="2"
                                    class="form-control" placeholder="Opsional.."></textarea>
                            </div>

                        </div>

                    </div>

                    <!-- =====================================
                    MASTER PO
                    ====================================== -->

                    <div class="po-master-card mb-3">

                        <div class="po-master-title">
                            MASTER PO INFORMATION
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">
                                    Material
                                </label>

                                <input type="text"
                                    id="poMaterialAdd"
                                    class="form-control"
                                    readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Qty
                                </label>

                                <input type="text"
                                    id="poJumlahAdd"
                                    class="form-control text-end"
                                    readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Berat
                                </label>

                                <input type="text"
                                    id="poBeratAdd"
                                    class="form-control text-end"
                                    readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Harga
                                </label>

                                <input type="text"
                                    id="poHargaAdd"
                                    class="form-control text-end"
                                    readonly>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">
                                    Total
                                </label>

                                <input type="text"
                                    id="poTotalAdd"
                                    class="form-control text-end"
                                    readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Truck
                                </label>

                                <input type="text"
                                    id="poTruckAdd"
                                    class="form-control"
                                    readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Driver
                                </label>

                                <input type="text"
                                    id="poDriverAdd"
                                    class="form-control"
                                    readonly>
                            </div>

                        </div>

                    </div>

                    <!-- =====================================
                    DETAIL
                    ====================================== -->

                    <div class="receive-section">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div class="receive-section-title mb-0">
                                DETAIL RECEIVE
                            </div>

                            <button type="button"
                                class="btn btn-warning btn-sm"
                                id="btnAddRemainingRow">

                                + Remaining Row

                            </button>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered"
                                id="receiveDetailTableAdd">

                                <thead>

                                    <tr>

                                        <th>Customer</th>
                                        <th>PO Type</th>
                                        <th>Jumlah</th>
                                        <th>Berat</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>Susut Jumlah</th>
                                        <th>Susut Berat</th>
                                        <th>Keterangan</th>

                                    </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        Simpan Receive

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
                        <div class="col-md-4">
                            <label class="form-label">Plant</label>
                            <input id="PLANT_EDIT_DISPLAY"
                                class="form-control"
                                readonly
                                style="background:#efefef">
                            <input type="hidden" id="PLANT_EDIT_HIDDEN" name="PLANT">
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

                            <label class="form-label">
                                Pembayaran *
                            </label>

                            <select id="paymentEdit"
                                name="PEMBAYARAN_EDIT"
                                class="form-control"
                                required>

                                <option value="">
                                    -- PILIH --
                                </option>

                                <option value="CASH">
                                    CASH
                                </option>

                                <option value="TRANSFER">
                                    TRANSFER
                                </option>

                            </select>

                        </div>

                        <div class="col-md-2">

                            <label class="form-label">
                                Jenis Pembayaran *
                            </label>

                            <select id="jenisPayEdit"
                                name="JENIS_PAY_EDIT"
                                class="form-control"
                                required>

                                <option value="">
                                    -- PILIH --
                                </option>

                                <option value="LUNAS">
                                    LUNAS
                                </option>

                                <option value="TEMPO">
                                    TEMPO
                                </option>

                            </select>

                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier *</label>

                            <input type="text"
                                id="supplierEdit"
                                class="form-control"
                                readonly
                                style="background:#efefef">

                            <input type="hidden"
                                id="hiddensupplierEdit"
                                name="SUPPLIER">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. Nota</label>
                            <input name="NOTA" id="NOTA_EDIT" class="form-control">
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
                        <!-- <button type="button" class="btn btn-success btn-sm" id="addDetailRowEdit">Tambah Material</button> -->
                    </div>

                    <table class="table table-bordered" id="receiveDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="text-align:center">Customer</th>
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

<style>
    .table-box{
        min-height:300px;
    }

    .table-loading{
        position:absolute;
        inset:0;
        z-index:10;
        background:rgba(255,255,255,.82);
        backdrop-filter:blur(2px);
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:12px;
    }

    .loading-card{
        text-align:center;
        padding:28px 40px;
        background:#fff;
        border-radius:18px;
        box-shadow:0 10px 30px rgba(0,0,0,.08);
    }

    .loading-hide{
        opacity:.35;
        pointer-events:none;
    }

    .modal-content{
        border:none;
        border-radius:20px;
        overflow:visible;
    }

    .modal-header{
        background:#f8fafc;
        border-bottom:1px solid #e5e7eb;
    }

    .modal-footer{
        background:#f8fafc;
        border-top:1px solid #e5e7eb;
    }
</style>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'RECEIVE', dir: 'DESC' };

    $('#search').on('keyup', function(){
        state.search = $(this).val();
        loadPage(1);
    });

    function initPlantSelect2(selector, modalId){

        $(selector).select2({

            placeholder:'-- PILIH PLANT --',

            dropdownParent: $(modalId),

            width:'100%',

            ajax:{

                url:'<?= base_url("receive/get_plant"); ?>',

                dataType:'json',

                delay:250,

                processResults:function(data){

                    return {
                        results:data
                    };

                }

            }

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

    function initReceiveAddSelect2(){
        // ================= PAYMENT =================

        $('#paymentAdd').select2({

            placeholder:'-- PILIH PAYMENT --',

            dropdownParent: $('#receiveAdd'),

            width:'100%',

            minimumResultsForSearch: Infinity

        });

        // ================= JENIS PAY =================

        $('#jenisPayAdd').select2({

            placeholder:'-- PILIH JENIS --',

            dropdownParent: $('#receiveAdd'),

            width:'100%',

            minimumResultsForSearch: Infinity

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

    function showTableLoading(){
        $('#tableLoading').removeClass('d-none');
        $('#tableWrapper').addClass('loading-hide');
    }

    function hideTableLoading(){
        $('#tableLoading').addClass('d-none');
        $('#tableWrapper').removeClass('loading-hide');
    }

    function loadPage(page = 1) {
        state.page = page;
        showTableLoading();
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
        }).always(function(){
            hideTableLoading();
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

    function formatMoneyID(value){

        if(
            value === null ||
            value === '' ||
            isNaN(value)
        ){
            return '';
        }

        return Number(value).toLocaleString(
            'id-ID',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );

    }

    function parseRupiah(value){

        if(!value) return 0;

        return parseFloat(
            value.toString()
                .replace(/\./g,'')
                .replace(',', '.')
        ) || 0;

    }

    function initPoSelect2(){

        $('#poAdd').select2({

            placeholder:'-- PILIH PO --',

            dropdownParent: $('#receiveAdd'),

            width:'100%',

            ajax:{

                url:'<?= base_url("receive/get_po"); ?>',

                dataType:'json',

                delay:250,

                data:function(params){

                    return {

                        q: params.term,

                        plant: $('#hiddenPlantAdd').val()

                    };

                },

                processResults:function(data){

                    return {
                        results:data
                    };

                }

            }

        });

    }

    function fillPoMaster(header){

        $('#supplierAddText').val(
            header.SUPPLIER + ' - ' + header.SUPPLIER_NAME
        );

        $('#hiddensupplierAdd').val(
            header.SUPPLIER
        );

        $('#poMaterialAdd').val(
            header.MATERIAL + ' - ' + header.MATERIAL_NAME
        );

        $('#poJumlahAdd').val(
            formatDecimalID(header.JUMLAH)
        );

        $('#poBeratAdd').val(
            formatDecimalID(header.BERAT)
        );

        $('#poHargaAdd').val(
            formatMoneyID(header.HARGA)
        );

        $('#poTotalAdd').val(
            formatMoneyID(header.TOTAL)
        );

        $('#poTruckAdd').val(
            header.NO_TRUCK
        );

        $('#poDriverAdd').val(
            header.DRIVER
        );

    }

    function formatDecimalID(value, decimals = 2){

        if(
            value === null ||
            value === '' ||
            isNaN(value)
        ){
            return '';
        }

        return Number(value).toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }
        );

    }

    function parseDecimalID(value){

        if(!value) return 0;

        value = value.toString();

        // hapus titik ribuan
        value = value.replace(/\./g,'');

        // ubah koma decimal ke titik
        value = value.replace(',', '.');

        return parseFloat(value) || 0;

    }

    function unformatNumber(value) {
        if (!value) return 0;
        return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
    }

    /* -------------------------
    Load PO detail into table
    ------------------------- */
    function loadPoDetail(po, plant){

        $.get(
            '<?= base_url("receive/get_po_detail"); ?>',
            {
                po:po,
                plant:plant
            },
            function(resp){

                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                if(!resp.status){

                    alert(resp.message);

                    return;

                }

                fillPoMaster(resp.header);

                let tbody = $('#receiveDetailTableAdd tbody');

                tbody.empty();

                resp.detail.forEach(function(row){

                    tbody.append(`
                        <tr class="receive-po-row">

                            <td>
                                <input type="hidden"
                                    class="po-seq"
                                    value="${row.SEQ_NO}">

                                <input type="hidden"
                                    class="customer-code"
                                    value="${row.CUSTOMER}">

                                <input type="text"
                                    class="form-control"
                                    value="${row.CUSTOMER_NAME}"
                                    readonly>
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control"
                                    value="${resp.header.PO_TYPE_NAME}"
                                    readonly>
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control text-end jumlah"
                                    value="${formatDecimalID(row.JUMLAH)}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control text-end berat"
                                    value="${formatDecimalID(row.BERAT)}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control text-end harga"
                                    value="${formatMoneyID(row.HARGA)}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control text-end total"
                                    value="${formatMoneyID(row.TOTAL)}">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control text-end susut-jumlah"
                                    value="0">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control text-end susut-berat"
                                    value="0">
                            </td>

                            <td>
                                <input type="text"
                                    class="form-control keterangan">
                            </td>

                        </tr>
                    `);

                });

                calculateReceiveSummary();

            }
        );

    }

    function initPoTypeSelect2(el, modal){

        $(el).select2({

            placeholder:'-- PILIH TYPE --',

            dropdownParent: $(modal),

            width:'100%',

            ajax:{
                url:'<?= base_url("receive/get_po_type"); ?>',

                dataType:'json',

                delay:250,

                data:function(params){

                    return {
                        q:params.term
                    };

                },

                processResults:function(data){

                    return {
                        results:data
                    };

                }
            }

        });

    }

    function initCustomerSelect2(el, modal){

        $(el).select2({

            placeholder:'-- PILIH CUSTOMER --',

            dropdownParent: $(modal),

            width:'100%',

            ajax:{

                url:'<?= base_url("receive/get_customer"); ?>',

                dataType:'json',

                delay:250,

                data:function(params){

                    return {
                        q: params.term
                    };

                },

                processResults:function(data){

                    return {
                        results:data
                    };

                }

            }

        });

    }

    function addRemainingRow(){

        $('#receiveDetailTableAdd tbody').append(`

            <tr class="receive-extra-row">

                <td>
                    <select class="customer-extra"></select>
                </td>

                <td>
                    <select class="po-type-extra"></select>
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end jumlah-extra">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end berat-extra">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end harga-extra">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end total-extra">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end susut-jumlah"
                        value="0">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end susut-berat"
                        value="0">
                </td>

                <td>
                    <input type="text"
                        class="form-control keterangan">
                </td>

            </tr>

        `);

        // =========================================
        // LAST ROW
        // =========================================

        let lastRow =
            $('#receiveDetailTableAdd tbody tr:last');

        // =========================================
        // INIT CUSTOMER SELECT2
        // =========================================

        initCustomerSelect2(
            lastRow.find('.customer-extra'),
            '#receiveAdd'
        );

        // =========================================
        // INIT PO TYPE SELECT2
        // =========================================

        initPoTypeSelect2(
            lastRow.find('.po-type-extra'),
            '#receiveAdd'
        );

        // =========================================
        // DEFAULT CUSTOMER
        // =========================================

        let customerOption = new Option(
            'INTERNAL FARM',
            'INTERNAL FARM',
            true,
            true
        );

        lastRow
            .find('.customer-extra')
            .append(customerOption)
            .trigger('change');

        // =========================================
        // DEFAULT TYPE
        // =========================================

        let typeOption = new Option(
            'AMBIL SENDIRI',
            'AMBIL SENDIRI',
            true,
            true
        );

        lastRow
            .find('.po-type-extra')
            .append(typeOption)
            .trigger('change');

    }

    function calculateReceiveSummary(){

        let qty = 0;

        let berat = 0;

        let total = 0;

        $('#receiveDetailTableAdd tbody tr').each(function(){

            qty += parseDecimalID(
                $(this)
                    .find('.jumlah,.jumlah-extra')
                    .val()
            );

            berat += parseDecimalID(
                $(this).find('.berat').val()
            );

            total += parseRupiah(
                $(this).find('.total').val()
            );

        });

        $('#summaryQty').text(
            formatDecimalID(qty)
        );

        $('#summaryBerat').text(
            formatDecimalID(berat)
        );

        $('#summaryTotal').text(
            formatMoneyID(total)
        );

    }

    /* -------------------------
    DOM Ready
    ------------------------- */
    $(function(){
        loadPage(1);

        // init select2 supplier & PO
        initPlantSelect2('#plantAdd', '#receiveAdd');
        setDefaultPlant('#plantAdd');

        initPoSelect2();

        initReceiveAddSelect2();
        $('#paymentAdd').val('').trigger('change');
        $('#jenisPayAdd').val('').trigger('change');

        // remove row
        $('#receiveDetailTableAdd, #receiveDetailTableEdit').on('click','.removeRow', function(){ $(this).closest('tr').remove(); });

        
        $('#plantAdd').on(
            'select2:select',
            function(e){

                $('#hiddenPlantAdd').val(
                    e.params.data.id
                );

                $('#poAdd')
                    .val(null)
                    .trigger('change');

                $('#receiveDetailTableAdd tbody').empty();

            }
        );

        /* =========================================================
        SELECT PO
        ========================================================= */

        $('#poAdd').on(
            'select2:select',
            function(e){

                $('#hiddenPoAdd').val(
                    e.params.data.id
                );

                let po = e.params.data.id;

                let plant = $('#hiddenPlantAdd').val();

                loadPoDetail(po, plant);

            }
        );

        /* =========================================================
        ADD REMAINING ROW
        ========================================================= */

        $('#btnAddRemainingRow').click(function(){

            addRemainingRow();

        });

        $(document).on(
            'blur',
            '.jumlah,.berat,.susut-jumlah,.susut-berat,.jumlah-extra,.berat-extra',
            function(){

                let val = parseDecimalID(
                    $(this).val()
                );

                $(this).val(
                    formatDecimalID(val)
                );

                calculateReceiveSummary();

            }
        );

        $(document).on(
            'blur',
            '.harga,.total,.harga-extra,.total-extra',
            function(){

                let val = parseRupiah(
                    $(this).val()
                );

                $(this).val(
                    formatMoneyID(val)
                );

                calculateReceiveSummary();

            }
        );

        function validateRemainingRow(){

            let valid = true;

            $('.receive-extra-row').each(function(){

                let customer = $(this)
                    .find('.customer-extra')
                    .val();

                let qty = parseDecimalID(
                    $(this)
                        .find('.jumlah-extra')
                        .val()
                );

                let berat = parseDecimalID(
                    $(this)
                        .find('.berat-extra')
                        .val()
                );

                if(
                    !customer ||
                    qty <= 0 ||
                    berat <= 0
                ){

                    valid = false;

                }

            });

            if(!valid){

                alert(
                    'Remaining row belum lengkap'
                );

            }

            return valid;

        }

        // Submit Add
        $('#freceiveAdd').submit(function(e){

            e.preventDefault();

            if(!validateRemainingRow()){
                return;
            }

            let btn = $('#freceiveAdd button[type=submit]');

            btn.prop('disabled', true);

            btn.html(`
                <span class="spinner-border spinner-border-sm"></span>
                Saving...
            `);

            let DETAIL = [];

            $('#receiveDetailTableAdd tbody tr').each(function(){

                let isExtra = $(this).hasClass('receive-extra-row')
                    ? 1
                    : 0;

                DETAIL.push({

                    PO_SEQ : $(this).find('.po-seq').val() || null,

                    IS_EXTRA : isExtra,

                    CUSTOMER :
                    isExtra
                        ? $(this).find('.customer-extra').val()
                        : $(this).find('.customer-code').val(),

                    PO_TYPE :
                        isExtra
                            ? $(this).find('.po-type-extra').val()
                            : null,

                    JUMLAH :
                        parseDecimalID(
                            $(this).find('.jumlah,.jumlah-extra').val()
                        ),

                    BERAT :
                        parseDecimalID(
                            $(this).find('.berat,.berat-extra').val()
                        ),

                    HARGA :
                        parseRupiah(
                            $(this).find('.harga,.harga-extra').val()
                        ),

                    TOTAL :
                        parseRupiah(
                            $(this).find('.total,.total-extra').val()
                        ),

                    SUSUT_JUMLAH :
                        parseDecimalID(
                            $(this).find('.susut-jumlah').val()
                        ),

                    SUSUT_BERAT :
                        parseDecimalID(
                            $(this).find('.susut-berat').val()
                        ),

                    KETERANGAN :
                        $(this).find('.keterangan').val()

                });

            });

            let formData = new FormData(this);

            formData.append(
                'DETAIL',
                JSON.stringify(DETAIL)
            );

            $.ajax({

                url:'<?= base_url("receive/create"); ?>',

                type:'POST',

                data:formData,

                processData:false,

                contentType:false,

                success:function(resp){

                    resp = typeof resp === 'string'
                        ? JSON.parse(resp)
                        : resp;

                    alert(resp.message);

                    if(resp.status){

                        $('#receiveAdd').modal('hide');

                        $('#freceiveAdd')[0].reset();

                        $('#receiveDetailTableAdd tbody').empty();

                        loadPage(1);

                    }

                },
                complete:function(){

                    btn.prop('disabled', false);

                    btn.html('Simpan Receive');

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
                    $('#PLANT_EDIT_HIDDEN').val(plant);
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
                        $('#PO_EDIT_DISPLAY').val('-');
                        $('#PO_EDIT_HIDDEN').val('');
                    } else {
                        $('#PO_EDIT_DISPLAY').val(
                            `${header.PLANT_NAME} - ${header.PO} - ${header.SUPPLIER_NAME}`
                        );

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

                    let supplierText =
                        `${header.SUPPLIER} - ${header.SUPPLIER_NAME}`;

                    $('#supplierEdit').val(supplierText);
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

                        $('#receiveDetailTableEdit tbody').append(`
                            <tr data-po-seq="${row.PO_SEQ}" data-seq="${row.SEQ_NO}">
                                <td>
                                    <input type="hidden" class="customer-code" value="${row.CUSTOMER ?? ''}">
                                    <input type="text"
                                        class="form-control"
                                        value="${row.CUSTOMER_NAME ?? ''}"
                                        readonly
                                        style="background:#efefef">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control jumlah"
                                        value="${formatDecimalID(row.JUMLAH,2)}"
                                        readonly
                                        style="background:#efefef;text-align:right">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control berat"
                                        value="${formatDecimalID(row.BERAT,2)}"
                                        readonly
                                        style="background:#efefef;text-align:right">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control harga"
                                        value="${formatDecimalID(row.HARGA,2)}"
                                        readonly
                                        style="background:#efefef;text-align:right">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control total"
                                        value="${formatDecimalID(row.TOTAL,2)}"
                                        readonly
                                        style="background:#efefef;text-align:right">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control susut-jumlah"
                                        value="${formatDecimalID(row.SUSUT_JUMLAH ?? 0,2)}"
                                        style="text-align:right">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control susut-berat"
                                        value="${formatDecimalID(row.SUSUT_BERAT ?? 0,2)}"
                                        style="text-align:right">
                                </td>

                                <td>
                                    <input type="text"
                                        class="form-control keterangan"
                                        value="${row.KETERANGAN ?? ''}">
                                </td>
                            </tr>
                        `);

                    });

                    $('#receiveEdit').modal('show');
                } else {
                    alert(resp.message || 'Gagal mengambil data RECEIVE');
                }
            });
        });

        // Submit edit
        $('#freceiveEdit').submit(function(e){
            e.preventDefault();

            let formData = new FormData(this); // <- penting

            let DETAIL = [];

            $('#receiveDetailTableEdit tbody tr').each(function(){
                DETAIL.push({
                    SEQ_NO       : $(this).data('seq'),
                    PO_SEQ       : $(this).data('po-seq'),
                    CUSTOMER     : $(this).find('.customer-code').val(),
                    MATERIAL     : $(this).find('.material-code').val(),
                    JUMLAH       : parseDecimalID($(this).find('.jumlah').val()),
                    BERAT        : parseDecimalID($(this).find('.berat').val()),
                    HARGA        : parseDecimalID($(this).find('.harga').val()),
                    TOTAL        : parseDecimalID($(this).find('.total').val()),
                    SUSUT_JUMLAH : parseDecimalID($(this).find('.susut-jumlah').val()),
                    SUSUT_BERAT  : parseDecimalID($(this).find('.susut-berat').val()),
                    KETERANGAN   : $(this).find('.keterangan').val()
                });
            });

            formData.set('PLANT', $('#PLANT_EDIT_HIDDEN').val());
            formData.set('PO', $('#PO_EDIT_HIDDEN').val());
            formData.set('SUPPLIER', $('#hiddensupplierEdit').val());
            formData.set('DETAIL', JSON.stringify(DETAIL));

            $.ajax({
                url: "<?= base_url('receive/update'); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success(resp){
                    alert(resp.message);
                    if(resp.status){
                        $('#receiveEdit').modal('hide');
                        loadPage(state.page);
                    }
                },
                error(xhr){
                    console.log(xhr.responseText);
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

        $('#receiveAdd').on(
            'hidden.bs.modal',
            function(){

                $('#freceiveAdd')[0].reset();

                $('#receiveDetailTableAdd tbody').empty();

                $('#poAdd')
                    .val(null)
                    .trigger('change');

                $('#poMaterialAdd').val('');

                $('#poJumlahAdd').val('');

                $('#poBeratAdd').val('');

                $('#poHargaAdd').val('');

                $('#poTotalAdd').val('');

                $('#poTruckAdd').val('');

                $('#poDriverAdd').val('');

                $('#supplierAddText').val('');

            }
        );
</script>
