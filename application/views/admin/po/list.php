<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">PO - INVENTORY</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <input id="search" type="text" class="form-control" placeholder="Cari PO, Supplier, Material..." />
                </div>
                <div class="col-md-4 col-sm-12 text-end mt-2 mt-md-0">
                    <div class="btn-group ">
                        <!-- <button class="btn btn-outline-secondary" id="exportExcel">Export Excel</button> -->
                        <!-- <button class="btn btn-outline-secondary" id="exportPdf">Export PDF</button> -->
                    </div>
                    <button id="btnAdd" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#poAdd">
                        <i class="ti ti-plus"></i> Tambah PO
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

                <div class="tableWrapper">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-modern" id="mainTable">

                            <thead>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle">Plant</th>
                                    <th style="text-align: center; vertical-align: middle">PO</th>
                                    <th style="text-align: center; vertical-align: middle">Date</th>
                                    <th style="text-align: center; vertical-align: middle">Supplier</th>
                                    <th style="text-align: center; vertical-align: middle">Material</th>
                                    <th style="text-align: center; vertical-align: middle">Qty / Weight</th>
                                    <th style="text-align: center; vertical-align: middle">Truck</th>
                                    <th style="text-align: center; vertical-align: middle">Status</th>
                                    <th class="text-center">#</th>
                                </tr>
                            </thead>

                            <tbody id="table-body"></tbody>

                        </table>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL ADD PO -->
<style>
    .po-section-card{
        border:1px solid #e9ecef;
        border-radius:16px;
        padding:20px;
        background:#fff;
        box-shadow:0 2px 10px rgba(0,0,0,.04);
        margin-bottom:18px;
    }

    .po-section-title{
        font-size:15px;
        font-weight:700;
        color:#1e325f;
        margin-bottom:18px;
        display:flex;
        align-items:center;
        justify-content:space-between;
    }

    .po-label{
        font-size:13px;
        font-weight:600;
        margin-bottom:6px;
        color:#495057;
    }

    .po-input{
        border-radius:10px !important;
        min-height:42px;
    }

    .po-input:focus{
        border-color:#1e4db7;
        box-shadow:0 0 0 .15rem rgba(30,77,183,.15);
    }

    .po-table thead th{
        background:#1e325f;
        color:#fff;
        font-size:13px;
        font-weight:600;
        text-align:center;
        vertical-align:middle;
        border:none;
        padding:12px 10px;
    }

    .po-table tbody td{
        vertical-align:middle;
    }

    .po-summary{
        background:#f8f9fb;
        border:1px solid #e9ecef;
        border-radius:14px;
        padding:18px;
    }

    .summary-item{
        display:flex;
        justify-content:space-between;
        margin-bottom:8px;
        font-size:13px;
    }

    .summary-item:last-child{
        margin-bottom:0;
    }

    .summary-value{
        font-weight:700;
    }

    .summary-ok{
        color:#198754;
    }

    .summary-over{
        color:#dc3545;
    }

    .btn-modern{
        border-radius:10px;
        min-height:42px;
        font-weight:600;
    }

    .modal-content{
        border:none;
        border-radius:20px;
        overflow:hidden;
    }

    .modal-header{
        background:#1e325f;
        color:#fff;
        border:none;
        padding:18px 22px;
    }

    .modal-header .btn-close{
        filter:brightness(0) invert(1);
    }

    .modal-footer{
        border-top:1px solid #edf0f2;
        padding:18px 22px;
    }

    @media(max-width:768px){

        .po-section-card{
            padding:15px;
        }

        .po-table{
            min-width:900px;
        }
    }
</style>

<div class="modal fade" id="poAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form id="fpoAdd">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mb-0" style="color:#fff">
                        PURCHASE ORDER - CREATE
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <!-- ================= HEADER ================= -->
                    <div class="po-section-card">

                        <div class="po-section-title">
                            <span>
                                <i class="ti ti-package me-1"></i>
                                PO INFORMATION
                            </span>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="po-label">Plant *</label>

                                <select
                                    id="plantAdd"
                                    class="form-control po-input"
                                    required>
                                </select>

                                <input
                                    type="hidden"
                                    id="hiddenPlantAdd"
                                    name="PLANT">
                            </div>

                            <div class="col-md-4">
                                <label class="po-label">PO Number</label>

                                <input
                                    type="text"
                                    id="PO_ADD_AUTO"
                                    class="form-control po-input"
                                    readonly
                                    placeholder="Auto Generate"
                                    style="background:#f5f6f8">
                            </div>

                            <div class="col-md-4">
                                <label class="po-label">PO Date *</label>

                                <input
                                    type="date"
                                    name="PO_DATE"
                                    class="form-control po-input"
                                    value="<?= date('Y-m-d'); ?>"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label class="po-label">PO Type *</label>

                                <select
                                    id="typeAdd"
                                    class="form-control po-input"
                                    required>
                                </select>

                                <input
                                    type="hidden"
                                    id="hiddenTypeAdd"
                                    name="TYPE">
                            </div>

                            <div class="col-md-4">
                                <label class="po-label">Supplier *</label>

                                <select
                                    id="supplierAdd"
                                    class="form-control po-input"
                                    required>
                                </select>

                                <input
                                    type="hidden"
                                    id="hiddensupplierAdd"
                                    name="SUPPLIER">
                            </div>

                            <div class="col-md-4">
                                <label class="po-label">Material *</label>

                                <select
                                    id="materialAdd"
                                    class="form-control po-input"
                                    required>
                                </select>

                                <input
                                    type="hidden"
                                    id="hiddenMaterialAdd"
                                    name="MATERIAL">
                            </div>

                            <div class="col-md-3">
                                <label class="po-label">Jumlah *</label>

                                <input
                                    type="text"
                                    id="masterJumlah"
                                    class="form-control po-input decimal-input"
                                    placeholder="0,00">
                            </div>

                            <div class="col-md-3">
                                <label class="po-label">Berat *</label>

                                <input
                                    type="text"
                                    id="masterBerat"
                                    class="form-control po-input decimal-input"
                                    placeholder="0,00">
                            </div>

                            <div class="col-md-3">
                                <label class="po-label">Harga *</label>

                                <input
                                    type="text"
                                    id="masterHarga"
                                    class="form-control po-input rupiah-input"
                                    placeholder="0">
                            </div>

                            <div class="col-md-3">
                                <label class="po-label">Total *</label>

                                <input
                                    type="text"
                                    id="masterTotal"
                                    class="form-control po-input rupiah-input"
                                    placeholder="0" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="po-label">No Truck</label>

                                <input
                                    type="text"
                                    id="masterTruck"
                                    class="form-control po-input"
                                    placeholder="Input truck number">
                            </div>

                            <div class="col-md-6">
                                <label class="po-label">Driver</label>

                                <input
                                    type="text"
                                    id="masterDriver"
                                    class="form-control po-input"
                                    placeholder="Input driver name">
                            </div>

                            <div class="col-md-12">
                                <label class="po-label">Remark</label>

                                <textarea
                                    name="REMARK"
                                    rows="2"
                                    class="form-control po-input"
                                    placeholder="Input remark..."></textarea>
                            </div>

                        </div>
                    </div>

                    <!-- ================= DETAIL ================= -->
                    <div class="po-section-card">

                        <div class="po-section-title">

                            <span>
                                <i class="ti ti-list-details me-1"></i>
                                CUSTOMER DETAIL
                            </span>

                            <button
                                type="button"
                                class="btn btn-success btn-sm btn-modern"
                                id="addDetailRowAdd">

                                <i class="ti ti-plus"></i>
                                Add Detail
                            </button>
                        </div>

                        <div class="tableWrapper">
                            <div class="table-responsive">

                                <table
                                    class="table table-bordered po-table"
                                    id="poDetailTableAdd">

                                    <thead>
                                        <tr>
                                            <th style="width:25%">Customer</th>
                                            <th>Jumlah</th>
                                            <th>Berat</th>
                                            <th>Harga</th>
                                            <th>Total</th>
                                            <th style="width:60px">#</th>
                                        </tr>
                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>
                        </div>

                        

                        <!-- ================= SUMMARY ================= -->
                        <div class="po-summary mt-3">

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="summary-item">
                                        <span>Master Jumlah</span>
                                        <span
                                            class="summary-value"
                                            id="sumMasterJumlah">
                                            0.00
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span>Used Jumlah</span>
                                        <span
                                            class="summary-value"
                                            id="sumDetailJumlah">
                                            0.00
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span>Remaining Jumlah</span>
                                        <span
                                            class="summary-value summary-ok"
                                            id="remainingJumlah">
                                            0.00
                                        </span>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="summary-item">
                                        <span>Master Berat</span>
                                        <span
                                            class="summary-value"
                                            id="sumMasterBerat">
                                            0.00
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span>Used Berat</span>
                                        <span
                                            class="summary-value"
                                            id="sumDetailBerat">
                                            0.00
                                        </span>
                                    </div>

                                    <div class="summary-item">
                                        <span>Remaining Berat</span>
                                        <span
                                            class="summary-value summary-ok"
                                            id="remainingBerat">
                                            0.00
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light btn-modern"
                        data-bs-dismiss="modal">

                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary btn-modern">

                        <i class="ti ti-device-floppy me-1"></i>
                        Save PO
                    </button>

                </div>

            </div>

        </form>
    </div>
</div>

<!-- MODAL EDIT PO -->
<div class="modal fade" id="poEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form id="fpoEdit">

            <div class="modal-content border-0 shadow-lg">

                <!-- HEADER -->
                <div class="modal-header bg-warning text-dark">

                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            EDIT PURCHASE ORDER
                        </h5>

                        <small>
                            Update master & detail customer
                        </small>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- TOP INFO -->
                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Plant
                            </label>

                            <input
                                type="text"
                                id="PLANT_NAME_EDIT"
                                class="form-control bg-light"
                                readonly>

                            <input
                                type="hidden"
                                id="PLANT_EDIT">
                        </div>

                        <div class="col-md-6">

                            <label class="form-label fw-semibold">
                                PO Number
                            </label>

                            <input
                                type="text"
                                id="PO_EDIT_AUTO"
                                class="form-control bg-light fw-bold"
                                readonly>

                            <input
                                type="hidden"
                                id="orig_po">
                        </div>

                    </div>

                    <!-- MASTER -->
                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-light fw-bold">
                            MASTER INFORMATION
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">
                                        PO Type *
                                    </label>

                                    <select
                                        id="typeEdit"
                                        class="form-control">
                                    </select>

                                    <input
                                        type="hidden"
                                        id="hiddenTypeEdit">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Tanggal *
                                    </label>

                                    <input
                                        type="date"
                                        name="PO_DATE"
                                        class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Supplier *
                                    </label>

                                    <select
                                        id="supplierEdit"
                                        class="form-control">
                                    </select>

                                    <input
                                        type="hidden"
                                        id="hiddensupplierEdit">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Material *
                                    </label>

                                    <select
                                        id="materialEdit"
                                        class="form-control">
                                    </select>

                                    <input
                                        type="hidden"
                                        id="hiddenMaterialEdit">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Jumlah Master *
                                    </label>

                                    <input
                                        type="text"
                                        id="masterJumlahEdit"
                                        class="form-control decimal-input text-end"
                                        placeholder="0,00">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Berat Master *
                                    </label>

                                    <input
                                        type="text"
                                        id="masterBeratEdit"
                                        class="form-control decimal-input text-end"
                                        placeholder="0,00">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Harga Master
                                    </label>

                                    <input
                                        type="text"
                                        id="masterHargaEdit"
                                        class="form-control rupiah-input text-end"
                                        placeholder="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Total Master
                                    </label>

                                    <input
                                        type="text"
                                        id="masterTotalEdit"
                                        class="form-control rupiah-input text-end"
                                        placeholder="0"
                                        readonly
                                        style="background:#f5f6f8">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        No Truck
                                    </label>

                                    <input
                                        type="text"
                                        id="masterTruckEdit"
                                        class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Driver
                                    </label>

                                    <input
                                        type="text"
                                        id="masterDriverEdit"
                                        class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">
                                        Remark
                                    </label>

                                    <textarea
                                        name="REMARK"
                                        class="form-control"
                                        rows="2"></textarea>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-light d-flex justify-content-between align-items-center">

                            <div class="fw-bold">
                                DETAIL CUSTOMER
                            </div>

                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                id="addDetailRowEdit">

                                <i class="ti ti-plus"></i>
                                Tambah Detail
                            </button>

                        </div>

                        <div class="card-body">

                            <!-- SUMMARY -->
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <div class="summary-box">
                                        <small>Master Jumlah</small>
                                        <h6 id="sumMasterJumlahEdit">0</h6>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="summary-box">
                                        <small>Detail Jumlah</small>
                                        <h6 id="sumDetailJumlahEdit">0</h6>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="summary-box">
                                        <small>Remaining Jumlah</small>
                                        <h6 id="remainingJumlahEdit">0</h6>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="summary-box">
                                        <small>Remaining Berat</small>
                                        <h6 id="remainingBeratEdit">0</h6>
                                    </div>
                                </div>

                            </div>

                            <!-- TABLE -->
                            <div class="table-responsive">

                                <table class="table table-bordered align-middle"
                                    id="poDetailTableEdit">

                                    <thead class="table-light">

                                        <tr>

                                            <th class="text-center">
                                                Customer
                                            </th>

                                            <th class="text-center">
                                                Jumlah
                                            </th>

                                            <th class="text-center">
                                                Berat
                                            </th>

                                            <th class="text-center">
                                                Harga
                                            </th>

                                            <th class="text-center">
                                                Total
                                            </th>

                                            <th width="60" class="text-center">
                                                #
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Tutup
                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="ti ti-device-floppy"></i>
                        Update PO
                    </button>

                </div>

            </div>

        </form>
    </div>
</div>

<!-- MODAL DETAIL PO -->
<div class="modal fade" id="poDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="poDetailBody">Loading...</div>
        </div>
    </div>
</div>

<style>
    .table-box{
        min-height:300px;
    }

    .table-modern{
        border-collapse:separate;
        border-spacing:0;
    }

    .table-modern thead th{
        background:#f8fafc;
        font-size:13px;
        font-weight:700;
        color:#334155;
        border-bottom:1px solid #e2e8f0;
        white-space:nowrap;
    }

    .table-modern tbody td{
        vertical-align:middle;
        font-size:13px;
        border-color:#f1f5f9;
    }

    .table-modern tbody tr:hover{
        background:#f8fafc;
    }

    .table-loading{
        position:absolute;
        inset:0;
        background:rgba(255,255,255,.8);
        z-index:10;
        display:flex;
        align-items:center;
        justify-content:center;
        backdrop-filter:blur(2px);
    }

    .loading-card{
        background:#fff;
        padding:25px 35px;
        border-radius:16px;
        box-shadow:0 10px 30px rgba(0,0,0,.08);
        text-align:center;
}

    .loading-hide{
        opacity:.35;
        pointer-events:none;
    }

    .summary-box{
        background:#f8f9fa;
        border-radius:12px;
        padding:12px;
        text-align:center;
        border:1px solid #e9ecef;
    }

    .summary-box h6{
        margin:0;
        font-weight:700;
        font-size:18px;
    }

    .summary-ok{
        color:#198754;
    }

    .summary-over{
        color:#dc3545;
    }
</style>

<script>
    const LOGIN_USER = "<?= $this->session->userdata('username'); ?>";
    const LOGIN_ROLE = "<?= $this->session->userdata('role_id'); ?>";
</script>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'PO', dir: 'DESC' };

    $('#search').on('keyup', function(){
        state.search = $(this).val();
        loadPage(1);
    });

    function formatTanggalIndo(dateStr) {
        if (!dateStr) return '';

        const bulan = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];

        const d = new Date(dateStr);
        return d.getDate() + ' ' +
            bulan[d.getMonth()] + ' ' +
            d.getFullYear();
    }

    function initPlantSelect2(selector, modalId){
        $(selector).select2({
            placeholder: "-- PILIH PLANT --",
            allowClear: true,
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('po/get_plant'); ?>",
                dataType: "json",
                delay: 250,
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });

        $(selector).on('select2:select', function(e){
            $('#hiddenPlantAdd').val(e.params.data.id);
        });

        $(selector).on('select2:clear', function(){
            $('#hiddenPlantAdd').val('');
        });
    }

    function setDefaultPlant(selector){
        $.ajax({
            url: "<?= base_url('po/get_plant'); ?>",
            dataType: "json",
            success: function(data){

                if(data.length > 0){

                    let first = data[0];

                    $(selector).empty();

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

    function initTypeSelect2(selector, modalId, hiddenInput){
        $(selector).select2({
            placeholder: "-- PILIH PO TYPE --",
            allowClear: true,
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('po/get_po_type'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return { q: params.term };
                },
                processResults: function(data){
                    return { results: data };
                }
            }
        });

        $(selector).on('select2:select', function(e){
            $(hiddenInput).val(e.params.data.id);
        });

        $(selector).on('select2:clear', function(){
            $(hiddenInput).val('');
        });
    }

    function setDefaultType(selector, hiddenInput){
        $.ajax({
            url: "<?= base_url('po/get_po_type'); ?>",
            dataType: "json",
            success: function(data){
                if(data.length > 0){
                    let first = data[0];

                    $(selector).empty();

                    let option = new Option(
                        first.text,
                        first.id,
                        true,
                        true
                    );

                    $(selector)
                        .append(option)
                        .trigger('change');

                    $(hiddenInput).val(first.id);
                }
            }
        });
    }

    function showTableLoading(){
        $('#tableLoading').removeClass('d-none');
        $('#tableWrapper').addClass('loading-hide');
    }

    function hideTableLoading(){
        $('#tableLoading').addClass('d-none');
        $('#tableWrapper').removeClass('loading-hide');
    }

    // Load table
    function loadPage(page = 1) {
        state.page = page;
        showTableLoading();
        $.get('<?= base_url("po/load_data"); ?>', state, function(resp){
            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var tbody = $('#table-body');
            tbody.empty();

            resp.rows.forEach(function(row){
                let actionBtn = `
                    <button
                        class="btn btn-outline-primary exportPdf"
                        title="Print PDF"
                        data-po="${row.PO}"
                        data-plant="${row.PLANT}">
                        Print
                    </button>
                `;

                if (LOGIN_ROLE == 1 || row.CREATED_BY === LOGIN_USER) {
                    actionBtn += `
                        <button class="btn btn-outline-warning editBtn"
                            data-po="${row.PO}" data-plant="${row.PLANT}">
                            Edit
                        </button>
                        <button class="btn btn-outline-danger deleteBtn""
                            data-po="${row.PO}"
                            data-plant="${row.PLANT}">
                            Hapus
                        </button>
                    `;
                }
                let statusBadge = row.STATUS == 1
                    ? `<span class="badge bg-success">RECEIVED</span>`
                    : `<span class="badge bg-warning text-dark">OPEN</span>`;

                var tr = `
                <tr>

                    <td class="text-center" style="vertical-align: middle">
                        <div class="fw-semibold">
                            ${row.PLANT_NAME || '-'}
                        </div>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div class="fw-bold text-primary">
                            #${row.PO}
                        </div>
                        <small class="text-muted">
                            ${row.PO_TYPE_NAME || '-'}
                        </small>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        ${formatTanggalIndo(row.PO_DATE)}
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div class="fw-semibold">
                            ${row.SUPPLIER_NAME || '-'}
                        </div>
                        <small class="text-muted">
                            ${row.SUPPLIER}
                        </small>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div class="fw-semibold">
                            ${row.MATERIAL_NAME || '-'}
                        </div>
                        <small class="text-muted">
                            ${row.MATERIAL || '-'}
                        </small>
                    </td>

                    <td class="text-end" style="vertical-align: middle">
                        <div>
                            <span class="fw-semibold">Qty :</span>
                            ${formatDecimalID(row.JUMLAH)}
                        </div>

                        <div>
                            <span class="fw-semibold">Weight :</span>
                            ${formatDecimalID(row.BERAT)}
                        </div>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div class="fw-semibold">
                            ${row.NO_TRUCK || '-'}
                        </div>

                        <small class="text-muted">
                            ${row.DRIVER || '-'}
                        </small>
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        ${statusBadge}
                    </td>

                    <td class="text-center" style="vertical-align: middle">
                        <div class="btn-group btn-group-sm">
                            ${actionBtn}
                        </div>
                    </td>

                </tr>
                `;
                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            $('#info').text(`Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`);
        }).always(function(){
            hideTableLoading();
        });
    }

    // Inisialisasi select2 supplier
    function initSupplierSelect2(selector, modalId, hiddenInput){
        $(selector).select2({
            placeholder: "-- PILIH SUPPLIER --",
            allowClear: true,
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('po/get_supplier'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });

        $(selector).on('select2:select', function(e){
            $(hiddenInput).val(e.params.data.id);
        });

        $(selector).on('select2:clear', function(){
            $(hiddenInput).val('');
        });
    }

    function initCustomerSelect2(el, parentModal){
        $(el).select2({
            placeholder: "-- PILIH CUSTOMER --",
            allowClear: true,
            width: "100%",
            dropdownParent: $(parentModal),
            ajax: {
                url: "<?= base_url('po/get_customer'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });
    }

    // Inisialisasi select2 material

    function initMaterialSelect2Header(selector, modalId){

        $(selector).select2({
            placeholder: "-- PILIH MATERIAL --",
            allowClear: true,
            dropdownParent: $(modalId),
            width: "100%",
            ajax: {
                url: "<?= base_url('po/get_material'); ?>",
                dataType: "json",
                delay: 250,
                data: function(params){
                    return {
                        q: params.term
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });

        $(selector).on('select2:select', function(e){
            $('#hiddenMaterialAdd').val(e.params.data.id);
        });

        $(selector).on('select2:clear', function(){
            $('#hiddenMaterialAdd').val('');
        });
    }

    function setDefaultMaterial(selector, hiddenInput){

        $.ajax({

            url: "<?= base_url('po/get_material'); ?>",

            dataType: "json",

            data: {
                q: '01220021'
            },

            success: function(data){

                if(data.length > 0){

                    let material = data.find(x => x.id == '01220021');

                    if(!material){
                        material = data[0];
                    }

                    $(selector).empty();

                    let option = new Option(
                        material.text,
                        material.id,
                        true,
                        true
                    );

                    $(selector)
                        .append(option)
                        .trigger('change');

                    $(hiddenInput).val(material.id);
                }
            }
        });
    }

    $(document).on('input', '.rupiah-input', function(){

        let value = $(this).val().replace(/\D/g,'');

        if(value === ''){
            $(this).val('');
            return;
        }

        $(this).val(
            parseInt(value,10).toLocaleString('id-ID')
        );
    });

    function cleanInt(val){
        return val ? val.replace(/\D/g,'') : '0';
    }

    function cleanDecimal(val){
        if(!val) return '0';
        return val.replace(/,/g,''); // hanya hapus pemisah ribuan
    }

    function cleanRupiah(val){
        if(!val) return '0';
        return val.replace(/\./g,'').replace(/,/g,'');
    }

    function formatDecimalID(value, decimals = 2) {
        if (value === null || value === '' || isNaN(value)) return '';

        return parseFloat(value)
            .toLocaleString('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
    }

    function formatMoneyID(value){
        if(value === null || value === '' || isNaN(value)) return '';

        return parseFloat(value).toLocaleString('id-ID',{
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function parseDecimalID(value) {
        if (!value) return 0;
        return parseFloat(
            value.replace(/\./g, '').replace(',', '.')
        ) || 0;
    }

    function addDetailRow(tableId, modalId, data = null){

        let customer = data?.CUSTOMER ?? '';
        let customerText = customer && data?.CUSTOMER_NAME
            ? customer + ' - ' + data.CUSTOMER_NAME
            : '';

        let row = `
        <tr>

            <input
                type="hidden"
                class="detail-id"
                value="${data?.ID ?? ''}">

            <td>

                <select
                    class="form-control customerSelect"
                    name="DETAIL[][CUSTOMER]">
                </select>

            </td>

            <td>

                <input
                    type="text"
                    class="form-control jumlah decimal-input"
                    style="text-align:right"
                    value="${data ? formatDecimalID(data.JUMLAH) : ''}"
                    placeholder="0,00">

            </td>

            <td>

                <input
                    type="text"
                    class="form-control berat decimal-input"
                    style="text-align:right"
                    value="${data ? formatDecimalID(data.BERAT) : ''}"
                    placeholder="0,00">

            </td>

            <td>

                <input
                    type="text"
                    class="form-control harga rupiah-input"
                    style="text-align:right"
                    value="${data ? formatMoneyID(data.HARGA) : ''}"
                    placeholder="0">

            </td>

            <td>

                <input
                    type="text"
                    class="form-control total rupiah-input"
                    style="text-align:right;background:#f5f6f8"
                    value="${data ? formatMoneyID(data.TOTAL) : ''}"
                    placeholder="0">

            </td>

            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-danger btn-sm removeRow">

                    <i class="ti ti-trash"></i>
                </button>

            </td>

        </tr>`;

        $(tableId + ' tbody').append(row);

        let $last = $(tableId + ' tbody tr:last');

        let $customer = $last.find('.customerSelect');

        initCustomerSelect2($customer, modalId);

        if(customer){

            let opt = new Option(
                customerText,
                customer,
                true,
                true
            );

            $customer.append(opt).trigger('change');
        }

        calculateSummary();
    }

    function calculateSummary(){

        let masterJumlah = parseDecimalID(
            $('#masterJumlah').val()
        );

        let masterBerat = parseDecimalID(
            $('#masterBerat').val()
        );

        let detailJumlah = 0;
        let detailBerat  = 0;

        $('#poDetailTableAdd tbody tr').each(function(){

            detailJumlah += parseDecimalID(
                $(this).find('.jumlah').val()
            );

            detailBerat += parseDecimalID(
                $(this).find('.berat').val()
            );
        });

        let remainJumlah = masterJumlah - detailJumlah;
        let remainBerat  = masterBerat - detailBerat;

        $('#sumMasterJumlah').text(
            formatDecimalID(masterJumlah)
        );

        $('#sumMasterBerat').text(
            formatDecimalID(masterBerat)
        );

        $('#sumDetailJumlah').text(
            formatDecimalID(detailJumlah)
        );

        $('#sumDetailBerat').text(
            formatDecimalID(detailBerat)
        );

        $('#remainingJumlah')
            .text(formatDecimalID(remainJumlah))
            .removeClass('summary-ok summary-over')
            .addClass(remainJumlah < 0
                ? 'summary-over'
                : 'summary-ok');

        $('#remainingBerat')
            .text(formatDecimalID(remainBerat))
            .removeClass('summary-ok summary-over')
            .addClass(remainBerat < 0
                ? 'summary-over'
                : 'summary-ok');
    }

    function calculateSummaryEdit(){

        let masterJumlah = parseDecimalID(
            $('#masterJumlahEdit').val()
        );

        let masterBerat = parseDecimalID(
            $('#masterBeratEdit').val()
        );

        let detailJumlah = 0;
        let detailBerat  = 0;

        $('#poDetailTableEdit tbody tr').each(function(){

            detailJumlah += parseDecimalID(
                $(this).find('.jumlah').val()
            );

            detailBerat += parseDecimalID(
                $(this).find('.berat').val()
            );

        });

        let remainJumlah = masterJumlah - detailJumlah;
        let remainBerat  = masterBerat - detailBerat;

        $('#sumMasterJumlahEdit')
            .text(formatDecimalID(masterJumlah));

        $('#sumDetailJumlahEdit')
            .text(formatDecimalID(detailJumlah));

        $('#remainingJumlahEdit')
            .text(formatDecimalID(remainJumlah))
            .removeClass('summary-ok summary-over')
            .addClass(
                remainJumlah < 0
                ? 'summary-over'
                : 'summary-ok'
            );

        $('#remainingBeratEdit')
            .text(formatDecimalID(remainBerat))
            .removeClass('summary-ok summary-over')
            .addClass(
                remainBerat < 0
                ? 'summary-over'
                : 'summary-ok'
            );
    }

    $(document).on('input', '.decimal-input', function () {
        let input = this;
        let cursor = input.selectionStart;

        // Ambil angka mentah
        let raw = input.value
            .replace(/\./g, '')
            .replace(/[^0-9,]/g, '');

        // Pisahkan decimal
        let parts = raw.split(',');
        let integer = parts[0];
        let decimal = parts[1] ? parts[1].substring(0, 2) : '';

        let number = integer;
        if (decimal !== '') number += '.' + decimal;

        if (isNaN(number)) return;

        // Format ke Indonesia
        let formatted = parseFloat(number).toLocaleString('id-ID', {
            minimumFractionDigits: decimal !== '' ? decimal.length : 0,
            maximumFractionDigits: 2
        });

        input.value = formatted;

        // Perbaiki posisi cursor
        let diff = formatted.length - raw.length;
        input.setSelectionRange(cursor + diff, cursor + diff);

    });

    $(document).on(
        'input',
        '#masterJumlah, #masterBerat, .jumlah, .berat',
        function(){
            calculateSummary();
        }
    );

    $(document).on(
        'input',
        '#masterJumlahEdit, #masterBeratEdit, #poDetailTableEdit .jumlah, #poDetailTableEdit .berat',
        function(){
            calculateSummaryEdit();
        }
    );

    $(document).on(
        'input',
        '#masterBerat, #masterHarga',
        function(){

            calculateMasterTotal();
        }
    );

    $(document).on(
        'input',
        '#masterBeratEdit, #masterHargaEdit',
        function(){

            calculateMasterTotalEdit();
        }
    );
    

    $(document).on('blur', '.decimal-input', function () {
        let val = parseDecimalID(this.value);
        this.value = formatDecimalID(val);
    });

    function parseRupiah(value){

        if(!value) return 0;

        return parseFloat(
            value.replace(/\./g,'')
                .replace(',', '.')
        ) || 0;
    }

    function calculateMasterTotal(){

        let berat = parseDecimalID(
            $('#masterBerat').val()
        );

        let harga = parseRupiah(
            $('#masterHarga').val()
        );

        let total = berat * harga;

        $('#masterTotal').val(
            total.toLocaleString('id-ID')
        );
    }

    function calculateMasterTotalEdit(){

        let berat = parseDecimalID(
            $('#masterBeratEdit').val()
        );

        let harga = parseRupiah(
            $('#masterHargaEdit').val()
        );

        let total = berat * harga;

        $('#masterTotalEdit').val(
            total.toLocaleString('id-ID')
        );
    }

    $(function(){
        loadPage(1);

        // Inisialisasi select2 add
        initPlantSelect2('#plantAdd', '#poAdd');
        setDefaultPlant('#plantAdd');
        initSupplierSelect2('#supplierAdd', '#poAdd', '#hiddensupplierAdd');
        initTypeSelect2('#typeAdd', '#poAdd', '#hiddenTypeAdd');
        initTypeSelect2('#typeEdit', '#poEdit', '#hiddenTypeEdit');
        setDefaultType('#typeAdd', '#hiddenTypeAdd');
        initMaterialSelect2Header(
            '#materialAdd',
            '#poAdd'
        );
        initMaterialSelect2Header(
            '#materialEdit',
            '#poEdit'
        );
        setDefaultMaterial(
            '#materialAdd',
            '#hiddenMaterialAdd'
        );
        $('#addDetailRowAdd').click(function(){ addDetailRow('#poDetailTableAdd','#poAdd'); });

        // Inisialisasi select2 edit
        initSupplierSelect2(
            '#supplierEdit',
            '#poEdit',
            '#hiddensupplierEdit'
        );
        $('#addDetailRowEdit').click(function(){ addDetailRow('#poDetailTableEdit','#poEdit'); });

        // Remove row
        $('#poDetailTableAdd, #poDetailTableEdit').on('click','.removeRow', function(){ $(this).closest('tr').remove(); calculateSummary(); });

        // Submit Add
        // $('#fpoAdd').submit(function(e){
        //     e.preventDefault();
        //     var DETAIL = [];
        //     $('#poDetailTableAdd tbody tr').each(function(){
        //         DETAIL.push({
        //             CUSTOMER : $(this).find('.customerSelect').val(),
        //             MATERIAL : $(this).find('.materialSelect').val(),
        //             JUMLAH   : parseDecimalID($(this).find('.jumlah').val()),
        //             BERAT    : parseDecimalID($(this).find('.berat').val()),
        //             HARGA    : parseDecimalID($(this).find('.harga').val()),
        //             TOTAL    : parseDecimalID($(this).find('.total').val())
        //         });
        //     });
        //     $.post('<?= base_url("po/create"); ?>',{
        //         PLANT   : $('#hiddenPlantAdd').val(),
        //         TYPE     : $('#hiddenTypeAdd').val(),
        //         PO_DATE: $('input[name="PO_DATE"]').val(),
        //         SUPPLIER: $('#hiddensupplierAdd').val(),
        //         REMARK: $('input[name="REMARK"]').val(),
        //         DETAIL: DETAIL
        //     }, function(resp){
        //         resp = typeof resp==='string'?JSON.parse(resp):resp;
        //         alert(resp.message);
        //         if(resp.status){
        //             $('#PO_ADD_AUTO').val(resp.po);
        //             $('#poAdd').modal('hide');
        //             $('#fpoAdd')[0].reset();
        //             $('#poDetailTableAdd tbody').empty();
        //             loadPage(state.page);
        //             $('#plantAdd').val(null).trigger('change');
        //             $('#supplierAdd').val(null).trigger('change');
        //             $('#hiddenPlantAdd').val('');
        //             $('#hiddensupplierAdd').val('');
        //             $('#fpoAdd')[0].reset();
        //         }

        //     },'json');
        // });

        $('#fpoAdd').submit(function(e){

        e.preventDefault();
        let masterJumlah = parseDecimalID(
            $('#masterJumlah').val()
        );

        let masterBerat = parseDecimalID(
            $('#masterBerat').val()
        );

        if($('#poDetailTableAdd tbody tr').length === 0){

            alert('Minimal 1 detail customer');

            return;
        }

        if(!$('#hiddenMaterialAdd').val()){
            alert('Material wajib dipilih');
            return;
        }

        if(!$('#hiddensupplierAdd').val()){
            alert('Supplier wajib dipilih');
            return;
        }

        if(masterJumlah <= 0){
            alert('Jumlah master harus lebih dari 0');
            return;
        }

        if(masterBerat <= 0){
            alert('Berat master harus lebih dari 0');
            return;
        }

        

        let totalJumlah = 0;
        let totalBerat  = 0;

        let DETAIL = [];
        let hasError = false;

        $('#poDetailTableAdd tbody tr').each(function(){

            let jumlah = parseDecimalID(
                $(this).find('.jumlah').val()
            );

            let berat = parseDecimalID(
                $(this).find('.berat').val()
            );

            let customer = $(this)
                .find('.customerSelect')
                .val();

            if(!customer){
                alert('Customer detail wajib dipilih');
                hasError = true;
                return false;
            }

            totalJumlah += jumlah;
            totalBerat += berat;

            DETAIL.push({

                CUSTOMER : $(this)
                    .find('.customerSelect')
                    .val(),

                JUMLAH : jumlah,

                BERAT : berat,

                HARGA : parseRupiah(
                    $(this).find('.harga').val()
                ),

                TOTAL : parseRupiah(
                    $(this).find('.total').val()
                )
            });
        });

        if(hasError){
            return;
        }

    // ================= VALIDATION =================
    if(totalJumlah > masterJumlah){

        alert(
            'Total jumlah detail melebihi master jumlah'
        );

        return;
    }

    if(totalBerat > masterBerat){

        alert(
            'Total berat detail melebihi master berat'
        );

        return;
    }

    // ================= SUBMIT =================
    $('#fpoAdd button[type=submit]')
        .prop('disabled', true);

    $.post('<?= base_url("po/create"); ?>', {

        PLANT : $('#hiddenPlantAdd').val(),

        TYPE : $('#hiddenTypeAdd').val(),

        MATERIAL : $('#hiddenMaterialAdd').val(),

        PO_DATE : $('input[name="PO_DATE"]').val(),

        SUPPLIER : $('#hiddensupplierAdd').val(),

        JUMLAH : masterJumlah,

        BERAT : masterBerat,

        HARGA : parseRupiah($('#masterHarga').val()),
        TOTAL : parseRupiah($('#masterTotal').val()),

        NO_TRUCK : $('#masterTruck').val(),

        DRIVER : $('#masterDriver').val(),

        REMARK : $('textarea[name="REMARK"]').val(),

        DETAIL : DETAIL

    }, function(resp){

        $('#fpoAdd button[type=submit]')
            .prop('disabled', false);

        resp = typeof resp === 'string'
            ? JSON.parse(resp)
            : resp;

        alert(resp.message);

        if(resp.status){

            $('#poAdd').modal('hide');

            $('#fpoAdd')[0].reset();

            $('#poDetailTableAdd tbody').empty();

            $('#plantAdd').val(null).trigger('change');
            $('#supplierAdd').val(null).trigger('change');
            $('#materialAdd').val(null).trigger('change');
            $('#hiddenMaterialAdd').val('');
            $('#hiddenTypeAdd').val('');
            $('#hiddenPlantAdd').val('');
            $('#hiddensupplierAdd').val('');

            calculateSummary();

            loadPage(state.page);
        }

    }, 'json');
});

        // Click edit
        $(document).on('click','.editBtn', function(){

            let po    = $(this).data('po');
            let plant = $(this).data('plant');

            $.get('<?= base_url("po/edit"); ?>',{ po, plant }, function(resp){

                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                if(!resp.status){

                    alert('Gagal mengambil data');

                    return;
                }

                let h = resp.header;

                // ================= HEADER =================

                $('#PLANT_EDIT').val(h.PLANT);

                $('#PLANT_NAME_EDIT').val(
                    h.PLANT_NAME
                );

                $('#PO_EDIT_AUTO').val(h.PO);

                $('#orig_po').val(h.PO);

                $('#fpoEdit input[name="PO_DATE"]')
                    .val(h.PO_DATE);

                $('#fpoEdit textarea[name="REMARK"]')
                    .val(h.REMARK);

                // ================= TYPE =================

                let typeOpt = new Option(
                    h.PO_TYPE_NAME,
                    h.PO_TYPE,
                    true,
                    true
                );

                $('#typeEdit')
                    .empty()
                    .append(typeOpt)
                    .trigger('change');

                $('#hiddenTypeEdit').val(h.PO_TYPE);

                // ================= SUPPLIER =================

                let supplierOpt = new Option(
                    h.SUPPLIER + ' - ' + h.SUPPLIER_NAME,
                    h.SUPPLIER,
                    true,
                    true
                );

                $('#supplierEdit')
                    .empty()
                    .append(supplierOpt)
                    .trigger('change');

                $('#hiddensupplierEdit')
                    .val(h.SUPPLIER);

                // ================= MATERIAL =================

                let materialOpt = new Option(
                    h.MATERIAL_NAME,
                    h.MATERIAL,
                    true,
                    true
                );

                $('#materialEdit')
                    .empty()
                    .append(materialOpt)
                    .trigger('change');

                $('#hiddenMaterialEdit')
                    .val(h.MATERIAL);

                // ================= MASTER =================

                $('#masterJumlahEdit').val(
                    formatDecimalID(h.JUMLAH)
                );

                $('#masterBeratEdit').val(
                    formatDecimalID(h.BERAT)
                );

                $('#masterHargaEdit').val(
                    formatMoneyID(h.HARGA)
                );

                $('#masterTotalEdit').val(
                    formatMoneyID(h.TOTAL)
                );

                $('#masterTruckEdit').val(
                    h.NO_TRUCK
                );

                $('#masterDriverEdit').val(
                    h.DRIVER
                );

                // ================= DETAIL =================

                $('#poDetailTableEdit tbody').empty();

                resp.detail.forEach(row => {

                    addDetailRow(
                        '#poDetailTableEdit',
                        '#poEdit',
                        row
                    );

                });

                calculateSummaryEdit();

                $('#poEdit').modal('show');

            });

        });

        // Submit edit
        $('#fpoEdit').submit(function(e){

            e.preventDefault();

            if ($('#poDetailTableEdit tbody tr').length === 0) {

                alert('Minimal 1 detail customer');

                return;
            }

            // ================= VALIDASI MASTER =================

            let masterJumlah = parseDecimalID(
                $('#masterJumlahEdit').val()
            );

            let masterBerat = parseDecimalID(
                $('#masterBeratEdit').val()
            );

            if(masterJumlah <= 0){

                alert('Jumlah master harus lebih dari 0');

                return;
            }

            if(masterBerat <= 0){

                alert('Berat master harus lebih dari 0');

                return;
            }

            $('#fpoEdit button[type=submit]')
                .prop('disabled', true);

            // ================= DETAIL =================

            let DETAIL = [];

            let totalJumlah = 0;

            let totalBerat = 0;

            $('#poDetailTableEdit tbody tr').each(function(){

                let jumlah = parseDecimalID(
                    $(this).find('.jumlah').val()
                );

                let berat = parseDecimalID(
                    $(this).find('.berat').val()
                );

                totalJumlah += jumlah;

                totalBerat += berat;

                DETAIL.push({

                    ID : $(this).find('.detail-id').val(),

                    CUSTOMER : $(this)
                        .find('.customerSelect')
                        .val(),

                    JUMLAH : jumlah,

                    BERAT : berat,

                    HARGA : parseRupiah(
                        $(this).find('.harga').val()
                    ),

                    TOTAL : parseRupiah(
                        $(this).find('.total').val()
                    )
                });

            });

            // ================= VALIDASI DETAIL =================

            if(totalJumlah > masterJumlah){

                alert(
                    'Total jumlah detail melebihi master'
                );

                $('#fpoEdit button[type=submit]')
                    .prop('disabled', false);

                return;
            }

            if(totalBerat > masterBerat){

                alert(
                    'Total berat detail melebihi master'
                );

                $('#fpoEdit button[type=submit]')
                    .prop('disabled', false);

                return;
            }

            // ================= SUBMIT =================

            $.post('<?= base_url("po/update"); ?>',{

                orig_po : $('#orig_po').val(),

                PLANT : $('#PLANT_EDIT').val(),

                TYPE : $('#hiddenTypeEdit').val(),

                MATERIAL : $('#hiddenMaterialEdit').val(),

                PO_DATE : $('#poEdit input[name="PO_DATE"]').val(),

                SUPPLIER : $('#hiddensupplierEdit').val(),

                JUMLAH : masterJumlah,

                BERAT : masterBerat,

                HARGA : parseRupiah(
                    $('#masterHargaEdit').val()
                ),

                TOTAL : parseRupiah(
                    $('#masterTotalEdit').val()
                ),

                NO_TRUCK : $('#masterTruckEdit').val(),

                DRIVER : $('#masterDriverEdit').val(),

                REMARK : $('#fpoEdit textarea[name="REMARK"]').val(),

                DETAIL : DETAIL

            }, function(resp){

                $('#fpoEdit button[type=submit]')
                    .prop('disabled', false);

                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                alert(resp.message);

                if(resp.status){

                    $('#poEdit').modal('hide');

                    loadPage(state.page);
                }

            }, 'json');

        });

        $(document).on("click", ".exportPdf", function () {
            let po    = $(this).data("po");
            let plant = $(this).data("plant");

            window.open(
                "<?= base_url('po/print_pdf'); ?>?po=" + po + "&plant=" + plant,
                "_blank"
            );
        });

        $(document).on('click', '.deleteBtn', function () {

            let po    = $(this).data('po');
            let plant = $(this).data('plant');

            if (!confirm(`Yakin ingin menghapus PO ${po} ?`)) return;

            $.ajax({
                url: "<?= base_url('po/remove'); ?>",
                type: "POST",
                data: {
                    po: po,
                    plant: plant
                },
                success: function (res) {
                    res = typeof res === 'string' ? JSON.parse(res) : res;

                    alert(res.message);

                    if (res.status) {
                        loadPage(state.page);
                    }
                },
                error: function () {
                    alert("Gagal menghubungi server");
                }
            });
        });
    });

    $('#poEdit').on('hidden.bs.modal', function(){

        $('#fpoEdit')[0].reset();

        $('#poDetailTableEdit tbody').empty();

        $('#supplierEdit').val(null).trigger('change');

        $('#materialEdit').val(null).trigger('change');

        $('#typeEdit').val(null).trigger('change');

        $('#hiddenMaterialEdit').val('');

        $('#hiddenTypeEdit').val('');

        $('#hiddensupplierEdit').val('');

        calculateSummaryEdit();
    });
</script>


