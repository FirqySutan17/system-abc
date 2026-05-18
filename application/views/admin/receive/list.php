<!-- application/views/admin/receive/list.php -->
<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">RECEIVE - INVENTORY</h5>

            <!-- SEARCH + ADD ROW -->
            <div class="row g-2 mb-3">

                <!-- =========================
                SEARCH
                ========================== -->

                <div class="col-md-3">

                    <input
                        id="search"
                        type="text"
                        class="form-control"
                        placeholder="Cari receive, supplier, PO...">

                </div>

                <!-- =========================
                STATUS
                ========================== -->

                <div class="col-md-2">

                    <select
                        id="filterStatus"
                        class="form-control">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="OPEN">
                            OPEN
                        </option>

                        <option value="POSTED">
                            POSTED
                        </option>

                        <option value="CANCEL">
                            CANCEL
                        </option>

                    </select>

                </div>

                <!-- DATE FROM -->
                <div class="col-md-2">

                    <input
                        type="date"
                        id="dateFrom"
                        class="form-control"
                        value="<?= date('Y-m-01'); ?>">

                </div>

                <!-- DATE TO -->
                <div class="col-md-2">

                    <input
                        type="date"
                        id="dateTo"
                        class="form-control"
                        value="<?= date('Y-m-d'); ?>">

                </div>

                <!-- =========================
                RESET
                ========================== -->

                <div class="col-md-1">

                    <button
                        class="btn btn-light w-100"
                        id="btnResetFilter">

                        Reset

                    </button>

                </div>

                <!-- =========================
                ADD
                ========================== -->

                <div class="col-md-2 text-end">

                    <button
                        id="btnAdd"
                        class="btn btn-primary w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#receiveAdd">

                        <i class="ti ti-plus"></i>

                        Tambah Receive

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
                <div id="tableWrapper">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-modern" id="mainTable">
                            <thead>
                                <tr>

                                    <th class="text-center">
                                        Plant
                                    </th>

                                    <th class="text-center">
                                        Receive
                                    </th>

                                    <th class="text-center">
                                        PO
                                    </th>

                                    <th class="text-center">
                                        Date
                                    </th>

                                    <th class="text-center">
                                        Supplier
                                    </th>

                                    <th class="text-center">
                                        Material
                                    </th>

                                    <th class="text-center">
                                        Qty / Weight
                                    </th>

                                    <th class="text-center">
                                        Sales
                                    </th>

                                    <th class="text-center">
                                        Status
                                    </th>

                                    <th class="text-center">
                                        #
                                    </th>

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

<!-- =========================================
STYLE
========================================= -->
<style>
    .table-modern td,
    .table-modern th{
        white-space: nowrap;
        vertical-align: middle;
    }
    .receive-card{
        border:1px solid #e5e7eb;
        border-radius:18px;
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

    .receive-card .form-control,
    .receive-card .form-select{
        border-radius:12px;
        min-height:44px;
        border:1px solid #dbe2ea;
        font-size:12px;
    }

    .receive-card .form-control:focus,
    .receive-card .form-select:focus{
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
    }

    .modal-header{
        background:#f8fafc;
        border-bottom:1px solid #e5e7eb;
    }

    .modal-footer{
        background:#f8fafc;
        border-top:1px solid #e5e7eb;
    }
    .modal-dialog-scrollable .modal-body{
        overflow-y:auto;
        overflow-x:hidden;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--bootstrap-5
    .select2-selection {

        min-height: 44px !important;

        border-radius: 12px !important;

        border: 1px solid #dbe2ea !important;

        font-size: 12px !important;
    }

    .select2-container--bootstrap-5
    .select2-selection--single {

        padding-top: 6px !important;

        padding-left: 12px !important;
    }

    .select2-container--bootstrap-5
    .select2-selection__rendered {

        color: #212529 !important;

        line-height: 28px !important;

        padding-left: 0 !important;
    }

    .select2-container--bootstrap-5
    .select2-selection__arrow {

        height: 42px !important;
    }

    .select2-dropdown {

        z-index: 999999 !important;
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option {
        line-height: 1;
        font-size: 13px !important;
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

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

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

                                <select id="plantAdd" class="form-select"
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

                                <select id="poAdd" class="form-select"
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

                                <select id="paymentAdd" class="form-select"
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

                                <select id="jenisPayAdd" class="form-select"
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

<div class="modal fade"
    id="receiveEdit"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <form id="freceiveEdit"
            enctype="multipart/form-data">

            <div class="modal-content receive-card">

                <!-- HEADER -->
                <div class="modal-header">

                    <h5 class="modal-title">
                        RECEIVE - EDIT
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

                            <!-- RECEIVE -->
                            <div class="col-md-4">

                                <label class="form-label">
                                    No Receive
                                </label>

                                <input type="text"
                                    id="RECEIVE_EDIT_DISPLAY"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                                <input type="hidden"
                                    id="RECEIVE_EDIT_HIDDEN"
                                    name="RECEIVE">

                            </div>

                            <!-- PLANT -->
                            <div class="col-md-4">

                                <label class="form-label">
                                    Plant
                                </label>

                                <input type="text"
                                    id="PLANT_EDIT_DISPLAY"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                                <input type="hidden"
                                    id="PLANT_EDIT_HIDDEN"
                                    name="PLANT">

                            </div>

                            <!-- PO -->
                            <div class="col-md-4">

                                <label class="form-label">
                                    PO
                                </label>

                                <input type="text"
                                    id="PO_EDIT_DISPLAY"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                                <input type="hidden"
                                    id="PO_EDIT_HIDDEN"
                                    name="PO">

                            </div>

                            <!-- RECEIVE DATE -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Receive Date *
                                </label>

                                <input type="date"
                                    id="RECEIVE_DATE_EDIT"
                                    name="RECEIVE_DATE"
                                    class="form-control"
                                    required>

                            </div>

                            <!-- SUPPLIER -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Supplier
                                </label>

                                <input type="text"
                                    id="SUPPLIER_EDIT_DISPLAY"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                                <input type="hidden"
                                    id="SUPPLIER_EDIT_HIDDEN"
                                    name="SUPPLIER">

                            </div>

                            <!-- PAYMENT -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Pembayaran *
                                </label>

                                <select id="paymentEdit"
                                    name="PEMBAYARAN"
                                    class="form-select"
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

                            <!-- JENIS PAY -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Jenis Pay *
                                </label>

                                <select id="jenisPayEdit"
                                    name="JENIS_PAY"
                                    class="form-select"
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

                            <!-- NOTA -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    No Nota
                                </label>

                                <input type="text"
                                    id="NOTA_EDIT"
                                    name="NOTA"
                                    class="form-control"
                                    placeholder="Opsional..">

                            </div>

                            <!-- NO REF -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    No Ref
                                </label>

                                <input type="text"
                                    id="NO_REF_EDIT"
                                    name="NO_REF"
                                    class="form-control"
                                    placeholder="Opsional..">

                            </div>

                            <!-- ATTACHMENT -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Attachment Baru
                                </label>

                                <input type="file"
                                    id="ATTACHMENT_EDIT"
                                    name="ATTACHMENT"
                                    class="form-control">

                            </div>

                            <!-- REMARK -->
                            <div class="col-md-12">

                                <label class="form-label">
                                    Remark
                                </label>

                                <textarea id="REMARK_EDIT"
                                    name="REMARK"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Opsional.."></textarea>

                            </div>
                            
                            <div class="col-md-12" style="margin-top: 0px">
                                <!-- PREVIEW -->
                                <div id="attachmentPreviewEdit"
                                    class="mt-3 d-none">

                                    <!-- IMAGE -->
                                    <img id="attachmentImageEdit"
                                        src=""
                                        class="img-fluid rounded border d-none"
                                        style="
                                            max-height:220px;
                                            object-fit:contain;
                                            background:#fff;
                                            padding:10px;
                                        ">

                                    <!-- PDF -->
                                    <iframe id="attachmentPdfEdit"
                                        class="w-100 border rounded d-none"
                                        style="
                                            height:320px;
                                            background:#fff;
                                        "></iframe>

                                    <!-- FILE -->
                                    <div id="attachmentFileEdit"
                                        class="d-none">

                                        <div class="alert alert-light border mb-0">

                                            <div class="fw-semibold mb-1">
                                                Attachment tersedia
                                            </div>

                                            <a href="#"
                                                target="_blank"
                                                id="ATTACHMENT_EDIT_LINK"
                                                class="btn btn-sm btn-primary">

                                                Download File

                                            </a>

                                        </div>

                                    </div>

                                </div>
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

                            <!-- MATERIAL -->
                            <div class="col-md-4">

                                <label class="form-label">
                                    Material
                                </label>

                                <input type="text"
                                    id="poMaterialEdit"
                                    class="form-control"
                                    readonly>

                            </div>

                            <!-- QTY -->
                            <div class="col-md-2">

                                <label class="form-label">
                                    Qty
                                </label>

                                <input type="text"
                                    id="poJumlahEdit"
                                    class="form-control text-end"
                                    readonly>

                            </div>

                            <!-- BERAT -->
                            <div class="col-md-2">

                                <label class="form-label">
                                    Berat
                                </label>

                                <input type="text"
                                    id="poBeratEdit"
                                    class="form-control text-end"
                                    readonly>

                            </div>

                            <!-- HARGA -->
                            <div class="col-md-2">

                                <label class="form-label">
                                    Harga
                                </label>

                                <input type="text"
                                    id="poHargaEdit"
                                    class="form-control text-end"
                                    readonly>

                            </div>

                            <!-- TOTAL -->
                            <div class="col-md-2">

                                <label class="form-label">
                                    Total
                                </label>

                                <input type="text"
                                    id="poTotalEdit"
                                    class="form-control text-end"
                                    readonly>

                            </div>

                            <!-- TRUCK -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Truck
                                </label>

                                <input type="text"
                                    id="poTruckEdit"
                                    class="form-control"
                                    readonly>

                            </div>

                            <!-- DRIVER -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Driver
                                </label>

                                <input type="text"
                                    id="poDriverEdit"
                                    class="form-control"
                                    readonly>

                            </div>

                        </div>

                    </div>

                    <!-- =====================================
                    DETAIL RECEIVE
                    ====================================== -->

                    <div class="receive-section">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div class="receive-section-title mb-0">
                                DETAIL RECEIVE
                            </div>

                            <button type="button"
                                class="btn btn-warning btn-sm"
                                id="btnAddRemainingRowEdit">

                                + Remaining Row

                            </button>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered"
                                id="receiveDetailTableEdit">

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

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        Update Receive

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- DETAIL modal -->


<style>
    
</style>

<script>
    const LOGIN_ROLE =
        <?= (int)$this->session->userdata('role_id'); ?>;

    const LOGIN_USER =
        '<?= $this->session->userdata('username'); ?>';
    let today = new Date();

    let firstDay = new Date(
        today.getFullYear(),
        today.getMonth(),
        1
    );

    let state = {

        page : 1,

        limit : 10,

        search : '',

        order : 'RECEIVE_DATE',

        dir : 'DESC',

        status : '',

        date_from : convertDateToMysql(
            $('#dateFrom').val()
        ),

        date_to : convertDateToMysql(
            $('#dateTo').val()
        )

    };

    $('#search').on(
        'keyup',
        debounce(function(){

            state.search =
                $('#search').val();

            loadPage(1);

        }, 400)
    );

    $('#filterStatus').change(function(){

        state.status =
            $(this).val();

        loadPage(1);

    });

    $('#dateFrom, #dateTo').change(function(){

        state.date_from =
            convertDateToMysql(
                $('#dateFrom').val()
            );

        state.date_to =
            convertDateToMysql(
                $('#dateTo').val()
            );

        loadPage(1);

    });

    $('#btnResetFilter').click(function(){

        $('#search').val('');

        $('#filterStatus').val('');

        let firstDay =
            new Date(
                new Date().getFullYear(),
                new Date().getMonth(),
                1
            )
            .toISOString()
            .split('T')[0];

        let today =
            new Date()
            .toISOString()
            .split('T')[0];

        $('#dateFrom').val(firstDay);

        $('#dateTo').val(today);

        state.date_from =
            convertDateToMysql(firstDay);

        state.date_to =
            convertDateToMysql(today);

        state.search = '';

        state.status = '';

        loadPage(1);

    });

    function debounce(func, wait){

        let timeout;

        return function(){

            let context = this;

            let args = arguments;

            clearTimeout(timeout);

            timeout = setTimeout(function(){

                func.apply(
                    context,
                    args
                );

            }, wait);

        };

    }

    function convertDateToMysql(date){

        if(!date){
            return '';
        }

        // sudah format mysql
        if(date.includes('-')){
            return date;
        }

        // format dd/mm/yyyy
        let split = date.split('/');

        if(split.length !== 3){
            return '';
        }

        return `${split[2]}-${split[1]}-${split[0]}`;
    }

    function initPlantSelect2(selector, modalId){

        $(selector).select2({
            theme:'bootstrap-5',
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
            theme:'bootstrap-5',
            placeholder:'-- PILIH PAYMENT --',
            dropdownParent: $('#receiveAdd .modal-body'),
            width:'100%',
            minimumResultsForSearch: Infinity
        });

        // ================= JENIS PAY =================

        $('#jenisPayAdd').select2({
            theme:'bootstrap-5',
            placeholder:'-- PILIH JENIS --',
            dropdownParent: $('#receiveAdd .modal-body'),
            width:'100%',
            minimumResultsForSearch: Infinity
        });

    }

    function initReceiveEditSelect2(){
        $('#paymentEdit').select2({
            theme:'bootstrap-5',
            placeholder:'-- PILIH PAYMENT --',
            dropdownParent: $('#receiveEdit .modal-body'),
            width:'100%',
            minimumResultsForSearch: Infinity
        });

        $('#jenisPayEdit').select2({
            theme:'bootstrap-5',
            placeholder:'-- PILIH JENIS --',
            dropdownParent: $('#receiveEdit .modal-body'),
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

    function previewAttachmentEdit(fileName){

        // RESET
        $('#attachmentPreviewEdit').addClass('d-none');

        $('#attachmentImageEdit')
            .addClass('d-none')
            .attr('src','');

        $('#attachmentPdfEdit')
            .addClass('d-none')
            .attr('src','');

        $('#attachmentFileEdit')
            .addClass('d-none');

        if(!fileName){
            return;
        }

        let fileUrl =
            '<?= base_url("uploads/receive/"); ?>' +
            fileName;

        let ext =
            fileName
                .split('.')
                .pop()
                .toLowerCase();

        $('#attachmentPreviewEdit')
            .removeClass('d-none');

        // =========================
        // IMAGE
        // =========================

        if(
            ['jpg','jpeg','png','webp']
            .includes(ext)
        ){

            $('#attachmentImageEdit')
                .removeClass('d-none')
                .attr('src', fileUrl);

        }

        // =========================
        // PDF
        // =========================

        else if(ext === 'pdf'){

            $('#attachmentPdfEdit')
                .removeClass('d-none')
                .attr('src', fileUrl);

        }

        // =========================
        // OTHER FILE
        // =========================

        else{

            $('#attachmentFileEdit')
                .removeClass('d-none');

            $('#ATTACHMENT_EDIT_LINK')
                .attr('href', fileUrl);

        }

    }

    function showTableLoading(){
        $('#tableLoading').removeClass('d-none');
        $('#tableWrapper').addClass('loading-hide');
    }

    function hideTableLoading(){
        $('#tableLoading').addClass('d-none');
        $('#tableWrapper').removeClass('loading-hide');
    }

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

    function loadPage(page = 1){

        state.page = page;

        showTableLoading();
        console.log(state);

        $.get(
            '<?= base_url("receive/load_data"); ?>',
            state,
            function(resp){

                resp =
                    typeof resp === 'string'
                        ? JSON.parse(resp)
                        : resp;

                let tbody =
                    $('#table-body');

                tbody.empty();

                resp.rows.forEach(function(row){

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS BADGE
                    |--------------------------------------------------------------------------
                    */

                    let statusBadge = `
                        <span class="badge bg-warning text-dark">
                            OPEN
                        </span>
                    `;

                    if(
                        row.STATUS_RECEIVE === 'POSTED'
                    ){

                        statusBadge = `
                            <span class="badge bg-success">
                                POSTED
                            </span>
                        `;

                    }

                    if(
                        row.STATUS_RECEIVE === 'CANCEL'
                    ){

                        statusBadge = `
                            <span class="badge bg-danger">
                                CANCEL
                            </span>
                        `;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ATTACHMENT ICON
                    |--------------------------------------------------------------------------
                    */

                    let attachmentIcon = '';

                    if(
                        row.ATTACH_FILE_NAME &&
                        row.ATTACH_FILE_NAME !== ''
                    ){

                        attachmentIcon = `
                            <i class="ti ti-paperclip text-primary ms-1"></i>
                        `;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PO DISPLAY
                    |--------------------------------------------------------------------------
                    */

                    let poDisplay = `
                        <div class="fw-semibold text-muted">
                            NON PO RECEIVE
                        </div>
                    `;

                    if(
                        row.PO &&
                        row.PO !== ''
                    ){

                        poDisplay = `
                            <div class="fw-bold text-primary">
                                #${row.PO}
                            </div>

                            <small class="text-muted">
                                ${row.PO_TYPE_NAME ?? '-'}
                            </small>
                        `;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ACTION BUTTON
                    |--------------------------------------------------------------------------
                    */

                    let actionBtn = `
                        <button
                            class="btn btn-outline-primary exportPdf"
                            data-receive="${row.RECEIVE}"
                            data-plant="${row.PLANT}">

                            Slip

                        </button>
                    `;

                    if(
                        LOGIN_ROLE == 1 ||
                        row.CREATED_BY === LOGIN_USER
                    ){

                        actionBtn += `

                            <button
                                class="btn btn-outline-warning editBtn"
                                data-receive="${row.RECEIVE}"
                                data-plant="${row.PLANT}">

                                Edit

                            </button>

                            <button
                                class="btn btn-outline-danger deleteBtn"
                                data-receive="${row.RECEIVE}"
                                data-plant="${row.PLANT}">

                                Hapus

                            </button>

                        `;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ROW
                    |--------------------------------------------------------------------------
                    */

                    let tr = `

                        <tr>

                            <!-- =========================
                            PLANT
                            ========================== -->

                            <td class="text-center align-middle">

                                <div class="fw-semibold">

                                    ${row.PLANT_NAME || '-'}

                                </div>

                            </td>

                            <!-- =========================
                            RECEIVE
                            ========================== -->

                            <td class="text-center align-middle">

                                <div class="fw-bold text-primary">

                                    #${row.RECEIVE}

                                </div>

                                <small class="text-muted">

                                    ${row.SLIP_NO || '-'}

                                </small>

                            </td>

                            <!-- =========================
                            PO
                            ========================== -->

                            <td class="text-center align-middle">

                                ${poDisplay}

                            </td>

                            <!-- =========================
                            DATE
                            ========================== -->

                            <td class="text-center align-middle">

                                ${formatTanggalIndo(
                                    row.RECEIVE_DATE
                                )}

                            </td>

                            <!-- =========================
                            SUPPLIER
                            ========================== -->

                            <td class="text-center align-middle">

                                <div class="fw-semibold">

                                    ${row.SUPPLIER_NAME || '-'}

                                </div>

                                <small class="text-muted">

                                    ${row.SUPPLIER || '-'}

                                </small>

                            </td>

                            <!-- =========================
                            MATERIAL
                            ========================== -->

                            <td class="text-center align-middle">

                                <div class="fw-semibold">

                                    ${row.MATERIAL_NAME || '-'}

                                </div>

                                <small class="text-muted">

                                    ${row.MATERIAL || '-'}

                                </small>

                            </td>

                            <!-- =========================
                            QTY / WEIGHT
                            ========================== -->

                            <td class="text-end align-middle">

                                <div>

                                    <span class="fw-semibold">
                                        Qty :
                                    </span>

                                    ${formatDecimalID(
                                        row.TOTAL_QTY ?? 0
                                    )}

                                </div>

                                <div>

                                    <span class="fw-semibold">
                                        Weight :
                                    </span>

                                    ${formatDecimalID(
                                        row.TOTAL_BERAT ?? 0
                                    )}

                                </div>

                            </td>

                            <td class="text-center align-middle">

                                <div>

                                    <span class="badge bg-info">

                                        ${row.TOTAL_CUSTOMER ?? 0}
                                        Customer

                                    </span>

                                </div>

                                <div class="mt-1">

                                    <span class="badge bg-secondary">

                                        ${row.TOTAL_SALES ?? 0}
                                        Sales

                                    </span>

                                </div>

                            </td>

                            <!-- =========================
                            STATUS
                            ========================== -->

                            <td class="text-center align-middle">

                                ${statusBadge}

                            </td>

                            <!-- =========================
                            ACTION
                            ========================== -->

                            <td class="text-center align-middle">

                                <div class="btn-group btn-group-sm">

                                    ${actionBtn}

                                </div>

                            </td>

                        </tr>

                    `;

                    tbody.append(tr);

                });

                $('#pagination').html(
                    resp.pagination
                );

                $('#info').text(
                    `Menampilkan halaman ${resp.page} dari ${
                        Math.ceil(
                            resp.total /
                            state.limit
                        )
                    } (Total ${resp.total} data)`
                );

            }

        ).always(function(){

            hideTableLoading();

        });

    }

    /* -------------------------
    Select2 inits
    ------------------------- */
    function initSupplierSelect2(selector, modalId){
        $(selector).select2({
            theme:'bootstrap-5',
            placeholder:'-- PILIH PLANT --',
            dropdownParent: $('#receiveAdd .modal-body'),
            width:'100%',
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
            theme:'bootstrap-5',
            placeholder:'-- PILIH PO --',
            dropdownParent: $('#receiveAdd .modal-body'),
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
            theme:'bootstrap-5',

            placeholder:'-- PILIH TYPE --',

            dropdownParent: $(el).closest('tr'),

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
            theme:'bootstrap-5',

            placeholder:'-- PILIH CUSTOMER --',

            dropdownParent: $('#receiveAdd .modal-body'),

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

    function addRemainingRowEdit(data = null){

        $('#receiveDetailTableEdit tbody').append(`

            <tr class="receive-extra-row"
                data-seq="${data?.SEQ_NO ?? ''}"
                data-is-extra="1">

                <td>
                    <select class="customer-edit"></select>
                </td>

                <td>
                    <select class="po-type-edit"></select>
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end jumlah-edit"
                        value="${formatDecimalID(data?.JUMLAH ?? 0)}">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end berat-edit"
                        value="${formatDecimalID(data?.BERAT ?? 0)}">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end harga-edit"
                        value="${formatMoneyID(data?.HARGA ?? 0)}">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end total-edit"
                        value="${formatMoneyID(data?.TOTAL ?? 0)}">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end susut-jumlah"
                        value="${formatDecimalID(data?.SUSUT_JUMLAH ?? 0)}">
                </td>

                <td>
                    <input type="text"
                        class="form-control text-end susut-berat"
                        value="${formatDecimalID(data?.SUSUT_BERAT ?? 0)}">
                </td>

                <td>
                    <input type="text"
                        class="form-control keterangan"
                        value="${data?.KETERANGAN ?? ''}">
                </td>

            </tr>

        `);

        let lastRow =
            $('#receiveDetailTableEdit tbody tr:last');

        initCustomerSelect2(
            lastRow.find('.customer-edit'),
            '#receiveEdit'
        );

        initPoTypeSelect2(
            lastRow.find('.po-type-edit'),
            '#receiveEdit'
        );

        // =========================
        // CUSTOMER
        // =========================

        let customerOption = new Option(
            data?.CUSTOMER_NAME ?? 'INTERNAL FARM',
            data?.CUSTOMER ?? 'CS000001',
            true,
            true
        );

        lastRow
            .find('.customer-edit')
            .append(customerOption)
            .trigger('change');

        // =========================
        // PO TYPE
        // =========================

        let poTypeText =
            data?.PO_TYPE_NAME ?? '';

        let poTypeValue =
            data?.PO_TYPE ?? '';

        if(
            data?.PO_TYPE &&
            data?.PO_TYPE_NAME
        ){

            let option = new Option(
                data.PO_TYPE_NAME,
                data.PO_TYPE,
                true,
                true
            );

            lastRow
                .find('.po-type-edit')
                .append(option)
                .trigger('change');

        }

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
                $(this)
                    .find('.berat,.berat-extra')
                    .val()
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
        $('#dateFrom').val(state.date_from);
        $('#dateTo').val(state.date_to);
        loadPage(1);

        // init select2 supplier & PO
        initPlantSelect2('#plantAdd', '#receiveAdd');
        setDefaultPlant('#plantAdd');

        initPoSelect2();

        initReceiveAddSelect2();
        initReceiveEditSelect2();
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

        $('#btnAddRemainingRowEdit').click(function(){
            addRemainingRowEdit();
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

        $(document).on(
            'blur',
            '.jumlah-edit,.berat-edit,.susut-jumlah,.susut-berat',
            function(){

                let val = parseDecimalID(
                    $(this).val()
                );

                $(this).val(
                    formatDecimalID(val)
                );

            }
        );

        $(document).on(
            'blur',
            '.harga-edit,.total-edit',
            function(){

                let val = parseRupiah(
                    $(this).val()
                );

                $(this).val(
                    formatMoneyID(val)
                );

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

        $(document).on('click','.editBtn', function(){

            let receive =
                $(this).data('receive');

            let plant =
                $(this).data('plant');

            $.get(
                '<?= base_url("receive/edit"); ?>',
                {
                    receive: receive,
                    plant: plant
                },
                function(resp){

                    resp =
                        typeof resp === 'string'
                            ? JSON.parse(resp)
                            : resp;

                    if(!resp.status){

                        alert(
                            resp.message ||
                            'Gagal mengambil data'
                        );

                        return;
                    }

                    let header =
                        resp.header;

                    let detail =
                        resp.detail;

                    // =================================================
                    // RESET
                    // =================================================

                    $('#freceiveEdit')[0].reset();

                    $('#receiveDetailTableEdit tbody')
                        .empty();

                    // =================================================
                    // HEADER
                    // =================================================

                    $('#RECEIVE_EDIT_DISPLAY')
                        .val(header.RECEIVE);

                    $('#RECEIVE_EDIT_HIDDEN')
                        .val(header.RECEIVE);

                    $('#PLANT_EDIT_DISPLAY')
                        .val(
                            header.PLANT_NAME
                                ? header.PLANT +
                                    ' - ' +
                                    header.PLANT_NAME
                                : header.PLANT
                        );

                    $('#PLANT_EDIT_HIDDEN')
                        .val(header.PLANT);

                    $('#SLIP_NO_EDIT')
                        .val(header.SLIP_NO ?? '');

                    // =================================================
                    // PO
                    // =================================================

                    if(!header.PO){

                        $('#PO_EDIT_DISPLAY')
                            .val('-');

                        $('#PO_EDIT_HIDDEN')
                            .val('');

                    }else{

                        $('#PO_EDIT_DISPLAY')
                            .val(
                                `${header.PO} - ${header.SUPPLIER_NAME}`
                            );

                        $('#PO_EDIT_HIDDEN')
                            .val(header.PO);

                    }

                    // =================================================
                    // DATE
                    // =================================================

                    $('#RECEIVE_DATE_EDIT')
                        .val(
                            header.RECEIVE_DATE
                                ? header.RECEIVE_DATE.substr(0,10)
                                : ''
                        );

                    // =================================================
                    // PAYMENT
                    // =================================================

                    $('#paymentEdit')
                        .val(header.PEMBAYARAN)
                        .trigger('change');

                    $('#jenisPayEdit')
                        .val(header.JENIS_PAY)
                        .trigger('change');

                    // =================================================
                    // SUPPLIER
                    // =================================================

                    let supplierText =
                        `${header.SUPPLIER} - ${header.SUPPLIER_NAME}`;

                    $('#SUPPLIER_EDIT_DISPLAY')
                        .val(supplierText);

                    $('#SUPPLIER_EDIT_HIDDEN')
                        .val(header.SUPPLIER);

                    // =================================================
                    // OTHER
                    // =================================================

                    $('#NOTA_EDIT')
                        .val(header.NOTA ?? '');

                    $('#NO_REF_EDIT')
                        .val(header.NO_REF ?? '');

                    $('#REMARK_EDIT')
                        .val(header.REMARK ?? '');

                    // =================================================
                    // PO MASTER
                    // =================================================

                    $('#poMaterialEdit')
                        .val(
                            header.MATERIAL_NAME ?? '-'
                        );

                    $('#poJumlahEdit')
                        .val(
                            formatDecimalID(
                                header.JUMLAH ?? 0
                            )
                        );

                    $('#poBeratEdit')
                        .val(
                            formatDecimalID(
                                header.BERAT ?? 0
                            )
                        );

                    $('#poHargaEdit')
                        .val(
                            formatMoneyID(
                                header.HARGA ?? 0
                            )
                        );

                    $('#poTotalEdit')
                        .val(
                            formatMoneyID(
                                header.TOTAL ?? 0
                            )
                        );

                    $('#poTruckEdit')
                        .val(
                            header.NO_TRUCK ?? '-'
                        );

                    $('#poDriverEdit')
                        .val(
                            header.DRIVER ?? '-'
                        );

                    // =================================================
                    // ATTACHMENT PREVIEW
                    // =================================================

                    previewAttachmentEdit(
                        header.ATTACH_FILE_NAME
                    );

                    // =================================================
                    // DETAIL
                    // =================================================

                    detail.forEach(function(row){

                        // =====================================================
                        // EXTRA ROW
                        // =====================================================

                        if(parseInt(row.IS_EXTRA) === 1){

                            addRemainingRowEdit(row);

                            return;
                        }

                        

                        // =====================================================
                        // NORMAL ROW
                        // =====================================================

                        $('#receiveDetailTableEdit tbody').append(`

                            <tr
                                class="receive-po-row"
                                data-po-seq="${row.PO_SEQ ?? ''}"
                                data-seq="${row.SEQ_NO}"
                                data-is-extra="0">

                                <td>

                                    <input type="hidden"
                                        class="customer-code"
                                        value="${row.CUSTOMER ?? ''}">

                                    <input type="text"
                                        class="form-control"
                                        value="${row.CUSTOMER_NAME ?? '-'}"
                                        readonly>

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control"
                                        value="${
                                            row.PO_TYPE_NAME &&
                                            row.PO_TYPE_NAME !== ''
                                                ? row.PO_TYPE_NAME
                                                : '-'
                                        }"
                                        readonly>

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control text-end jumlah-edit"
                                        value="${formatDecimalID(row.JUMLAH)}">

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control text-end berat-edit"
                                        value="${formatDecimalID(row.BERAT)}">

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control text-end harga-edit"
                                        value="${formatMoneyID(row.HARGA)}">

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control text-end total-edit"
                                        value="${formatMoneyID(row.TOTAL)}">

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control text-end susut-jumlah"
                                        value="${formatDecimalID(row.SUSUT_JUMLAH ?? 0)}">

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control text-end susut-berat"
                                        value="${formatDecimalID(row.SUSUT_BERAT ?? 0)}">

                                </td>

                                <td>

                                    <input type="text"
                                        class="form-control keterangan"
                                        value="${row.KETERANGAN ?? ''}">

                                </td>

                            </tr>

                        `);

                    });


                    // =================================================
                    // SHOW MODAL
                    // =================================================

                    $('#receiveEdit')
                        .modal('show');

                }
            );

        });

        $('#ATTACHMENT_EDIT').change(function(){

            let file = this.files[0];

            if(!file){
                return;
            }

            let ext =
                file.name
                    .split('.')
                    .pop()
                    .toLowerCase();

            let objectUrl =
                URL.createObjectURL(file);

            // RESET
            $('#attachmentPreviewEdit')
                .removeClass('d-none');

            $('#attachmentImageEdit')
                .addClass('d-none');

            $('#attachmentPdfEdit')
                .addClass('d-none');

            $('#attachmentFileEdit')
                .addClass('d-none');

            // IMAGE
            if(
                ['jpg','jpeg','png','webp']
                .includes(ext)
            ){

                $('#attachmentImageEdit')
                    .removeClass('d-none')
                    .attr('src', objectUrl);

            }

            // PDF
            else if(ext === 'pdf'){

                $('#attachmentPdfEdit')
                    .removeClass('d-none')
                    .attr('src', objectUrl);

            }

            // OTHER
            else{

                $('#attachmentFileEdit')
                    .removeClass('d-none');

                $('#ATTACHMENT_EDIT_LINK')
                    .attr('href', objectUrl);

            }

        });

        $('#freceiveEdit').submit(function(e){

            e.preventDefault();

            let formData =
                new FormData(this);

            let DETAIL = [];

            $('#receiveDetailTableEdit tbody tr')
                .each(function(){

                    DETAIL.push({

                        SEQ_NO :

                            $(this).data('seq'),

                        PO_SEQ :

                            $(this).data('po-seq'),

                        CUSTOMER :

                            $(this)
                                .find('.customer-code')
                                .val()

                            ||

                            $(this)
                                .find('.customer-edit')
                                .val(),

                        PO_TYPE :
                            $(this)
                                .find('.po-type-edit')
                                .val()
                            || null,

                        JUMLAH :

                            parseDecimalID(
                                $(this)
                                    .find('.jumlah-edit')
                                    .val()
                            ),

                        BERAT :

                            parseDecimalID(
                                $(this)
                                    .find('.berat-edit')
                                    .val()
                            ),

                        HARGA :

                            parseRupiah(
                                $(this)
                                    .find('.harga-edit')
                                    .val()
                            ),

                        TOTAL :

                            parseRupiah(
                                $(this)
                                    .find('.total-edit')
                                    .val()
                            ),

                        SUSUT_JUMLAH :

                            parseDecimalID(
                                $(this)
                                    .find('.susut-jumlah')
                                    .val()
                            ),

                        SUSUT_BERAT :

                            parseDecimalID(
                                $(this)
                                    .find('.susut-berat')
                                    .val()
                            ),

                        KETERANGAN :

                            $(this)
                                .find('.keterangan')
                                .val(),

                        IS_EXTRA :

                            $(this)
                                .hasClass('receive-extra-row')
                                    ? 1
                                    : 0

                    });

                });

            formData.set(
                'RECEIVE',
                $('#RECEIVE_EDIT_HIDDEN').val()
            );

            formData.set(
                'PLANT',
                $('#PLANT_EDIT_HIDDEN').val()
            );

            formData.set(
                'PO',
                $('#PO_EDIT_HIDDEN').val()
            );

            formData.set(
                'SUPPLIER',
                $('#SUPPLIER_EDIT_HIDDEN').val()
            );

            formData.set(
                'DETAIL',
                JSON.stringify(DETAIL)
            );

            $.ajax({

                url:
                    "<?= base_url('receive/update'); ?>",

                type: "POST",

                data: formData,

                processData: false,

                contentType: false,

                dataType: "json",

                beforeSend(){

                    $('#freceiveEdit button[type=submit]')
                        .prop('disabled', true);

                },

                success(resp){

                    $('#freceiveEdit button[type=submit]')
                        .prop('disabled', false);

                    alert(resp.message);

                    if(resp.status){

                        $('#receiveEdit')
                            .modal('hide');

                        loadPage(state.page);

                    }

                },

                error(xhr){

                    $('#freceiveEdit button[type=submit]')
                        .prop('disabled', false);

                    console.log(xhr.responseText);

                    alert(
                        'Gagal update receive'
                    );

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
