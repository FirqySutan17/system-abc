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

    /*======================================================
    SECTION
    ======================================================*/

    .po-section-card{

        background:#fff;

        border:1px solid #e8edf5;

        border-radius:14px;

        padding:22px;

        margin-bottom:24px;

        box-shadow:0 2px 10px rgba(0,0,0,.03);

    }

    /*======================================================
    TITLE
    ======================================================*/

    .po-section-title{

        font-size:18px;

        font-weight:600;

        color:#1f2937;

        margin-bottom:20px;

        padding-bottom:12px;

        border-bottom:1px solid #edf2f7;

    }

    /*======================================================
    GROUP
    ======================================================*/

    .receive-group-title{

        display:flex;

        align-items:center;

        font-size:13px;

        font-weight:600;

        color:#2563eb;

        text-transform:uppercase;

        letter-spacing:.5px;

        margin-bottom:14px;

        padding-left:10px;

        border-left:4px solid #2563eb;

    }

    /*======================================================
    LABEL
    ======================================================*/

    .po-label{

        font-size:13px;

        font-weight:500;

        color:#4b5563;

        margin-bottom:6px;

    }

    /*======================================================
    REQUIRED
    ======================================================*/

    .required{

        color:#dc2626;

    }

    /*======================================================
    INPUT
    ======================================================*/

    .po-select{

        width:100%;

    }

    .po-input{

        height:42px;

        border-radius:10px;

        border:1px solid #d7dee8;

        font-size:14px;

        transition:.2s;

        box-shadow:none;

    }

    textarea.po-input{

        height:auto;

        min-height:90px;

        resize:vertical;

    }

    /*======================================================
    FOCUS
    ======================================================*/

    .po-input:focus{

        border-color:#2563eb;

        box-shadow:0 0 0 .18rem rgba(37,99,235,.15);

    }

    /*======================================================
    READONLY
    ======================================================*/

    .po-input.readonly,

    .po-input:read-only{

        background:#f8fafc;

        color:#374151;

        cursor:not-allowed;

    }

    /*======================================================
    METRIC CARD
    ======================================================*/

    .metric-card{

        background:#fff;

        border:1px solid #e6edf5;

        border-radius:14px;

        padding:18px;

        text-align:center;

        height:100%;

        transition:.25s;

    }

    .metric-card:hover{

        transform:translateY(-2px);

        box-shadow:0 8px 20px rgba(0,0,0,.07);

    }

    /*======================================================
    TITLE
    ======================================================*/

    .metric-label{

        font-size:12px;

        color:#64748b;

        text-transform:uppercase;

        letter-spacing:.5px;

        font-weight:600;

    }

    /*======================================================
    VALUE
    ======================================================*/

    .metric-value{

        margin-top:10px;

        font-size:28px;

        font-weight:700;

        color:#1e40af;

    }

    .metric-money{

        margin-top:12px;

        font-size:24px;

        font-weight:700;

        color:#059669;

    }

    /*======================================================
    UNIT
    ======================================================*/

    .metric-unit{

        margin-top:4px;

        color:#94a3b8;

        font-size:12px;

    }

    /*======================================================
    COLOR
    ======================================================*/

    .metric-card.success{

        border-left:5px solid #16a34a;

    }

    .metric-card.warning{

        border-left:5px solid #f59e0b;

    }

    .metric-card.danger{

        border-left:5px solid #dc2626;

    }

    .metric-card.primary{

        border-left:5px solid #2563eb;

    }

    /*======================================================
    INFO BOX
    ======================================================*/

    .info-box{

        background:#f8fafc;

        border:1px solid #e2e8f0;

        border-radius:14px;

        padding:18px;

        height:100%;

    }

    .info-item{

        display:flex;

        justify-content:space-between;

        align-items:center;

    }

    .info-item label{

        color:#64748b;

        font-size:13px;

        margin:0;

    }

    .info-item span{

        font-size:15px;

        font-weight:600;

        color:#1f2937;

    }

    .info-divider{

        border-top:1px dashed #d1d5db;

        margin:15px 0;

    }

    /*======================================================
    CUSTOMER TABLE
    ======================================================*/

    .customer-table{

        margin-bottom:0;

    }

    .customer-table thead th{

        background:#f8fafc;

        color:#475569;

        font-size:13px;

        font-weight:600;

        border-bottom:2px solid #e2e8f0;

        white-space:nowrap;

    }

    .customer-table tbody td{

        vertical-align:middle;

        padding:12px 10px;

    }

    .customer-table input,
    .customer-table select{

        height:40px;

        border-radius:8px;

    }

    /*======================================================
    SUMMARY
    ======================================================*/

    .summary-card{

        border:1px solid #e2e8f0;

        border-radius:14px;

        padding:18px;

        background:#fff;

        height:100%;

    }

    .summary-title{

        font-weight:700;

        margin-bottom:15px;

        color:#334155;

    }

    .summary-row{

        display:flex;

        justify-content:space-between;

        padding:10px 0;

        border-bottom:1px dashed #e2e8f0;

    }

    .summary-row:last-child{

        border:none;

    }

    .summary-row.total strong{

        font-size:18px;

        color:#2563eb;

    }

    /*======================================================
    PROGRESS
    ======================================================*/

    .progress-card{

        border:1px solid #e2e8f0;

        border-radius:14px;

        padding:18px;

        background:#fff;

        height:100%;

    }

    .progress-title{

        margin-bottom:15px;

        font-weight:700;

        color:#334155;

    }

    .progress{

        height:28px;

        border-radius:20px;

        background:#e5e7eb;

    }

    .progress-bar{

        font-weight:600;

        font-size:12px;

        line-height:28px;

    }

    /*======================================================
    SECTION DESCRIPTION
    ======================================================*/

    .section-description{

        color:#64748b;

        font-size:13px;

        margin-bottom:18px;

    }

    /*======================================================
    SAVING TABLE
    ======================================================*/

    .saving-table{

        margin-bottom:0;

    }

    .saving-table thead th{

        background:#f8fafc;

        color:#475569;

        font-weight:600;

        font-size:13px;

        border-bottom:2px solid #e2e8f0;

        white-space:nowrap;

    }

    .saving-table tbody td{

        vertical-align:middle;

        padding:12px 10px;

    }

    .saving-table input,
    .saving-table select{

        height:40px;

        border-radius:8px;

    }

    /*======================================================
    FOOTER
    ======================================================*/

    .saving-footer{

        display:flex;

        justify-content:flex-end;

    }

    .saving-total{

        min-width:260px;

        background:#f8fafc;

        border:1px solid #e2e8f0;

        border-radius:12px;

        padding:14px 18px;

        display:flex;

        justify-content:space-between;

        align-items:center;

    }

    .saving-total span{

        color:#64748b;

        font-size:14px;

    }

    .saving-total strong{

        font-size:20px;

        color:#059669;

        font-weight:700;

    }

    /*======================================================
    PAYMENT TABLE
    ======================================================*/

    .payment-table{

        margin-bottom:0;

    }

    .payment-table thead th{

        background:#f8fafc;

        color:#475569;

        font-size:13px;

        font-weight:600;

        border-bottom:2px solid #e2e8f0;

    }

    .payment-table tbody td{

        padding:13px 10px;

        vertical-align:middle;

    }

    .payment-table tbody td:not(:first-child){

        text-align:right;

        font-weight:600;

    }

    /*======================================================
    PAYMENT FOOTER
    ======================================================*/

    .payment-footer{

        display:flex;

        justify-content:flex-end;

    }

    .payment-summary-box{

        width:380px;

        background:#f8fafc;

        border:1px solid #e2e8f0;

        border-radius:14px;

        padding:18px 22px;

    }

    .payment-row{

        display:flex;

        justify-content:space-between;

        align-items:center;

        margin:10px 0;

    }

    .payment-row span{

        color:#64748b;

    }

    .payment-row strong{

        font-size:18px;

    }

    .payment-summary-box hr{

        margin:15px 0;

        border-top:1px dashed #d6dce5;

    }

    .payment-grand span{

        font-size:16px;

        font-weight:700;

        color:#1f2937;

    }

    .payment-grand strong{

        font-size:24px;

        color:#16a34a;

    }

    .modal-backdrop.show{
        opacity:.5;
        background:#000;
    }

    #lookupModal{
        z-index:1065;
    }

    #lookupModal .modal-dialog{

        max-width:1100px;

    }

    #lookupTable tbody tr{

        cursor:pointer;

    }

    #lookupTable tbody tr:hover{

        background:#eef5ff;

    }

    #lookupKeyword{

        border-radius:10px;

    }

    .lookup-row{

        cursor:pointer;

    }

    .lookup-row:hover{

        background:#eef5ff;

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

<div class="modal fade"
    id="receiveAdd"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl">

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

                    <!-- ==========================================================
                    RECEIVE INFORMATION
                    =========================================================== -->
                    <div class="po-section-card">

                        <div class="po-section-title">
                            <span>
                                <i class="ti ti-truck-delivery me-1"></i>
                                RECEIVE INFORMATION
                            </span>
                        </div>

                        <!-- ===================================================== -->
                        <!-- GENERAL INFORMATION -->
                        <!-- ===================================================== -->

                        <div class="receive-group-title">
                            General Information
                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="po-label">
                                    Plant <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="plantAdd"
                                    name="PLANT"
                                    class="form-control po-input">

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="po-label">
                                    Receive Date <span class="required">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="RECEIVE_DATE"
                                    name="RECEIVE_DATE"
                                    class="form-control po-input"
                                    value="<?= date('Y-m-d'); ?>"
                                    required>

                            </div>

                            <div class="col-md-4">

                                <label class="po-label">
                                    Purchase Order <span class="required">*</span>
                                </label>

                                <div class="input-group">

                                    <div class="input-group">

                                        <input
                                            type="text"
                                            id="poText"
                                            class="form-control"
                                            placeholder="Choose Purchase Order..."
                                            readonly>

                                        <button
                                            class="btn btn-primary"
                                            type="button"
                                            id="btnLookupPO">

                                            <i class="ti ti-search"></i>

                                        </button>
                                    </div>

                                </div>

                                <input
                                    type="hidden"
                                    id="poAdd"
                                    name="PO">

                            </div>

                            <div class="col-md-4">

                                <label class="po-label">
                                    Supplier
                                </label>

                                <input
                                    type="text"
                                    id="supplierAddText"
                                    class="form-control po-input"
                                    readonly>

                                <input
                                    type="hidden"
                                    id="hiddensupplierAdd"
                                    name="SUPPLIER">

                            </div>

                            <div class="col-md-4">

                                <label class="po-label">
                                    Material
                                </label>

                                <input
                                    type="text"
                                    id="poMaterialAdd"
                                    class="form-control po-input"
                                    readonly>

                            </div>

                        </div>

                        <!-- ===================================================== -->
                        <!-- DOCUMENT -->
                        <!-- ===================================================== -->

                        <div class="receive-group-title">
                            Document Information
                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="po-label">
                                    Receive Number
                                </label>

                                <input
                                    type="text"
                                    id="RECEIVE_NO_ADD"
                                    class="form-control po-input readonly"
                                    readonly
                                    placeholder="Auto Generate">

                            </div>

                            <div class="col-md-6">

                                <label class="po-label">
                                    Slip Number
                                </label>

                                <input
                                    type="text"
                                    id="SLIP_NO_ADD"
                                    class="form-control po-input readonly"
                                    readonly
                                    placeholder="Auto Generate">

                            </div>

                            <div class="col-md-6">

                                <label class="po-label">
                                    Nota
                                </label>

                                <input
                                    type="text"
                                    name="NOTA"
                                    class="form-control po-input"
                                    placeholder="Optional">

                            </div>

                            <div class="col-md-6">

                                <label class="po-label">
                                    No. Ref
                                </label>

                                <input
                                    type="text"
                                    name="NO_REF"
                                    class="form-control po-input"
                                    placeholder="Optional">

                            </div>

                        </div>

                        <!-- ===================================================== -->
                        <!-- PAYMENT -->
                        <!-- ===================================================== -->

                        <div class="receive-group-title">
                            Payment Information
                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">

                                <label class="po-label">
                                    Payment <span class="required">*</span>
                                </label>

                                <select
                                    id="paymentAdd"
                                    name="PEMBAYARAN"
                                    class="form-select po-select"
                                    required>

                                    <option value="">-- SELECT --</option>
                                    <option value="CASH">CASH</option>
                                    <option value="TRANSFER">TRANSFER</option>

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="po-label">
                                    Payment Type <span class="required">*</span>
                                </label>

                                <select
                                    id="jenisPayAdd"
                                    name="JENIS_PAY"
                                    class="form-select po-select"
                                    required>

                                    <option value="">-- SELECT --</option>
                                    <option value="LUNAS">LUNAS</option>
                                    <option value="TEMPO">TEMPO</option>

                                </select>

                            </div>

                        </div>

                        <!-- ===================================================== -->
                        <!-- OTHER -->
                        <!-- ===================================================== -->

                        <div class="receive-group-title">
                            Other Information
                        </div>

                        <div class="row g-3">

                            <div class="col-md-12">

                                <label class="po-label">
                                    Attachment
                                </label>

                                <input
                                    type="file"
                                    id="ATTACHMENT_ADD"
                                    name="ATTACHMENT"
                                    class="form-control po-input">

                            </div>

                            <div class="col-md-12">

                                <label class="po-label">
                                    Remark
                                </label>

                                <textarea
                                    name="REMARK"
                                    rows="3"
                                    class="form-control po-input"
                                    placeholder="Input remark..."></textarea>

                            </div>

                        </div>

                    </div>

                    <!-- ==========================================================
                    PO ACTUAL INFORMATION
                    =========================================================== -->
                    <div class="po-section-card">

                        <div class="po-section-title">

                            <span>

                                <i class="ti ti-chart-bar me-2"></i>

                                ACTUAL RECEIVE SUMMARY

                            </span>

                        </div>

                        <!-- ========================================================= -->
                        <!-- Production -->
                        <!-- ========================================================= -->

                        <div class="receive-group-title">
                            Production
                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-3">

                                <div class="metric-card">

                                    <div class="metric-label">
                                        Qty Actual
                                    </div>

                                    <div
                                        id="masterQtyAdd"
                                        class="metric-value">
                                        -
                                    </div>

                                    <div class="metric-unit">
                                        Ekor
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="metric-card">

                                    <div class="metric-label">
                                        Weight
                                    </div>

                                    <div
                                        id="masterBwAdd"
                                        class="metric-value">
                                        -
                                    </div>

                                    <div class="metric-unit">
                                        Kg
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="metric-card">

                                    <div class="metric-label">
                                        Avg BW
                                    </div>

                                    <div
                                        id="masterAvgBwAdd"
                                        class="metric-value">
                                        -
                                    </div>

                                    <div class="metric-unit">
                                        Kg
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="metric-card">

                                    <div class="metric-label">
                                        Price
                                    </div>

                                    <div
                                        id="masterHargaAdd"
                                        class="metric-money">
                                        Rp 0
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ========================================================= -->
                        <!-- Quality -->
                        <!-- ========================================================= -->

                        <div class="receive-group-title">
                            Quality
                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-3">

                                <div class="metric-card danger">

                                    <div class="metric-label">
                                        Dead Qty
                                    </div>

                                    <div
                                        id="masterMatiQtyAdd"
                                        class="metric-value">
                                        0
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="metric-card danger">

                                    <div class="metric-label">
                                        Dead BW
                                    </div>

                                    <div
                                        id="masterMatiBwAdd"
                                        class="metric-value">
                                        0
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="metric-card warning">

                                    <div class="metric-label">
                                        Shrink BW
                                    </div>

                                    <div
                                        id="masterSusutBwAdd"
                                        class="metric-value">
                                        0
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-3">

                                <div class="metric-card success">

                                    <div class="metric-label">
                                        Receive Qty
                                    </div>

                                    <div
                                        id="masterTerimaQtyAdd"
                                        class="metric-value">
                                        0
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ========================================================= -->
                        <!-- Financial & Logistic -->
                        <!-- ========================================================= -->

                        <div class="receive-group-title">
                            Financial & Logistic
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <div class="metric-card success">

                                    <div class="metric-label">
                                        Receive BW
                                    </div>

                                    <div
                                        id="masterTerimaBwAdd"
                                        class="metric-value">
                                        0
                                    </div>

                                    <div class="metric-unit">
                                        Kg
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="metric-card primary">

                                    <div class="metric-label">
                                        Grand Total
                                    </div>

                                    <div
                                        id="masterTotalAdd"
                                        class="metric-money">
                                        Rp 0
                                    </div>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="info-box">

                                    <div class="info-item">

                                        <label>Truck</label>

                                        <span id="masterTruckAdd">
                                            -
                                        </span>

                                    </div>

                                    <div class="info-divider"></div>

                                    <div class="info-item">

                                        <label>Driver</label>

                                        <span id="masterDriverAdd">
                                            -
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- ==========================================================
                    CUSTOMER DETAIL
                    =========================================================== -->
                    <div class="po-section-card">

                        <div class="po-section-title d-flex justify-content-between align-items-center">

                            <span>

                                <i class="ti ti-users me-2"></i>

                                CUSTOMER ALLOCATION

                            </span>

                            <button
                                type="button"
                                id="btnAddCustomer"
                                class="btn btn-success btn-modern">

                                <i class="ti ti-plus"></i>

                                Add Customer

                            </button>

                        </div>

                        <div class="table-responsive">

                            <table
                                class="table customer-table align-middle"
                                id="customerTableAdd">

                                <thead>

                                    <tr>

                                        <th style="width:24%">Customer</th>
                                        <th style="width:10%">Qty</th>
                                        <th style="width:10%">BW</th>
                                        <th style="width:12%">Price</th>
                                        <th style="width:12%">Discount</th>
                                        <th>Remark</th>
                                        <th style="width:14%">Total</th>
                                        <th style="width:5%"></th>

                                    </tr>

                                </thead>

                                <tbody>

                                </tbody>

                            </table>

                        </div>

                        <div class="allocation-summary mt-4">

                            <div class="row">

                                <div class="col-md-5">

                                    <div class="summary-card">

                                        <div class="summary-title">

                                            Qty Allocation

                                        </div>

                                        <div class="summary-row">

                                            <span>Actual</span>

                                            <strong id="summaryQtyActual">
                                                0
                                            </strong>

                                        </div>

                                        <div class="summary-row">

                                            <span>Allocated</span>

                                            <strong id="summaryQtyUsed">
                                                0
                                            </strong>

                                        </div>

                                        <div class="summary-row total">

                                            <span>Remaining</span>

                                            <strong id="summaryQtyRemaining">
                                                0
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-5">

                                    <div class="summary-card">

                                        <div class="summary-title">

                                            BW Allocation

                                        </div>

                                        <div class="summary-row">

                                            <span>Actual</span>

                                            <strong id="summaryBwActual">
                                                0
                                            </strong>

                                        </div>

                                        <div class="summary-row">

                                            <span>Allocated</span>

                                            <strong id="summaryBwUsed">
                                                0
                                            </strong>

                                        </div>

                                        <div class="summary-row total">

                                            <span>Remaining</span>

                                            <strong id="summaryBwRemaining">
                                                0
                                            </strong>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-2">

                                    <div class="progress-card">

                                        <div class="progress-title">

                                            Allocation

                                        </div>

                                        <div class="progress">

                                            <div
                                                id="allocationProgress"
                                                class="progress-bar"
                                                role="progressbar"
                                                style="width:0%">

                                                0%

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- ==========================================================
                    SAVING
                    =========================================================== -->
                    <div class="po-section-card">

                        <div class="po-section-title d-flex justify-content-between align-items-center">

                            <span>

                                <i class="ti ti-pig-money me-2"></i>

                                CUSTOMER SAVING

                            </span>

                            <!-- <button
                                type="button"
                                id="btnAddSaving"
                                class="btn btn-success btn-modern">

                                <i class="ti ti-plus"></i>

                                Add Saving

                            </button> -->

                        </div>

                        <p class="section-description">

                            Tambahkan tabungan customer apabila ada potongan yang akan disimpan
                            sebagai saldo/tabungan.

                        </p>

                        <div class="table-responsive">

                            <table
                                class="table saving-table align-middle"
                                id="savingTableAdd">

                                <thead>

                                    <tr>

                                        <th style="width:35%">
                                            Customer
                                        </th>

                                        <th style="width:20%">
                                            Saving Amount
                                        </th>

                                        <th>
                                            Remark
                                        </th>

                                        <th style="width:6%">
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                </tbody>

                            </table>

                        </div>

                        <div class="saving-footer mt-3">

                            <div class="saving-total">

                                <span>

                                    Total Saving

                                </span>

                                <strong id="savingGrandTotal">

                                    Rp 0

                                </strong>

                            </div>

                        </div>

                    </div>

                    <!-- ==========================================================
                    PAYMENT SUMMARY
                    =========================================================== -->
                    <div class="po-section-card">

                        <div class="po-section-title">

                            <span>

                                <i class="ti ti-cash-banknote me-2"></i>

                                PAYMENT SUMMARY

                            </span>

                        </div>

                        <p class="section-description">

                            Ringkasan pembayaran customer setelah dikurangi discount dan ditambah tabungan.

                        </p>

                        <div class="table-responsive">

                            <table
                                class="table payment-table align-middle"
                                id="paymentSummaryTableAdd">

                                <thead>

                                    <tr>

                                        <th style="width:40%">
                                            Customer
                                        </th>

                                        <th class="text-end">
                                            Sales
                                        </th>

                                        <th class="text-end">
                                            Saving
                                        </th>

                                        <th class="text-end">
                                            Total Payment
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                </tbody>

                            </table>

                        </div>

                        <div class="payment-footer mt-4">

                            <div class="payment-summary-box">

                                <div class="payment-row">

                                    <span>Total Sales</span>

                                    <strong id="grandSalesAdd">

                                        Rp 0

                                    </strong>

                                </div>

                                <div class="payment-row">

                                    <span>Total Saving</span>

                                    <strong id="grandSavingAdd">

                                        Rp 0

                                    </strong>

                                </div>

                                <hr>

                                <div class="payment-row payment-grand">

                                    <span>Grand Payment</span>

                                    <strong id="grandPaymentAdd">

                                        Rp 0

                                    </strong>

                                </div>

                            </div>

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

<!-- ===================================================== -->
<!-- LOOKUP MODAL -->
<!-- ===================================================== -->

<div
    class="modal fade"
    id="lookupModal"
    tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="lookupTitle">

                    Lookup

                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="row mb-3">

                    <div class="col-md-12">

                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="ti ti-search"></i>

                            </span>

                            <input
                                id="lookupKeyword"
                                class="form-control"
                                placeholder="Search...">

                        </div>

                        <div
                            id="lookupLoading"
                            class="text-center py-4 d-none">

                            <div
                                class="spinner-border text-primary">

                            </div>

                        </div>

                    </div>

                </div>

                <div class="table-responsive" style="max-height:420px">

                    <table
                        id="lookupTable"
                        class="table table-hover table-bordered align-middle">

                        <thead>

                        </thead>

                        <tbody>

                        </tbody>

                    </table>

                </div>

                <div
                    id="lookupEmpty"
                    class="text-center py-4 d-none">

                    Tidak ada data.

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    const base_url = "<?= base_url(); ?>";
/**************************************************************************
 *
 * RECEIVE CREATE
 *
 **************************************************************************/

/*
|--------------------------------------------------------------------------
| UTILITY
|--------------------------------------------------------------------------
*/

function liveDecimal(input){

    let value = input.value;

    value = value.replace(/\./g,'');

    value = value.replace(",", ".");

    let number = parseFloat(value);

    if(isNaN(number)){
        number = 0;
    }

    input.value = formatDecimal(number);

}

function liveMoney(input){

    let value = input.value;

    value = value.replace(/[^\d]/g,'');

    if(value==""){
        value=0;
    }

    input.value = formatMoney(value);

}

function parseDecimal(value)
{
    if(value === null || value === undefined){
        return 0;
    }

    value = value.toString();

    value = value
        .replace(/\./g,'')
        .replace(',','.');

    let number = parseFloat(value);

    return isNaN(number)
        ? 0
        : number;
}

function formatDecimal(value)
{
    value = parseFloat(value);

    if(isNaN(value)){
        value = 0;
    }

    return value.toLocaleString(
        'id-ID',
        {
            minimumFractionDigits:2,
            maximumFractionDigits:2
        }
    );
}

function parseMoney(value)
{
    if(value === null || value === undefined){
        return 0;
    }

    value = value.toString();

    value = value.replace(/[^\d]/g,'');

    return parseFloat(value) || 0;
}

function formatMoney(value)
{
    value = parseFloat(value);

    if(isNaN(value)){
        value = 0;
    }

    return value.toLocaleString('id-ID');
}

function formatDecimalID(value, decimals = 2) {
    if (value === null || value === '' || isNaN(value)) return '';

    return parseFloat(value)
        .toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
}

function parseDecimalID(value) {
        if (!value) return 0;
        return parseFloat(
            value.replace(/\./g, '').replace(',', '.')
        ) || 0;
    }

$(document).on('input', '.decimal-input', function () {

    let value = $(this).val();

    value = value.replace(/[^0-9,]/g, '');

    let parts = value.split(',');

    let integer = parts[0];

    let decimal = parts.length > 1
        ? parts[1].substring(0, 2)
        : '';

    integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    if(parts.length > 1){

        value = integer + ',' + decimal;

    }else{

        value = integer;

    }

    $(this).val(value);

});

$(document).on('blur', '.decimal-input', function () {
    let val = parseDecimalID(this.value);
    this.value = formatDecimalID(val);
});

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

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const receiveState = {

    po : null,

    customerMaster : [],

    customerRows : [],

    savingRows : [],

    actual : {

        qty : 0,

        bw : 0,

        avgBw : 0,

        matiQty : 0,

        matiBw : 0,

        susutBw : 0,

        receiveQty : 0,

        receiveBw : 0,

        harga : 0,

        total : 0

    }

};

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

function openLookup(type){

    lookupType = type;

    $("#lookupKeyword").val("");

    switch(type){

        case "PO":

            $("#lookupTitle").text("Purchase Order");

            renderPOHeader();

            loadLookupPO();

            break;

        case "CUSTOMER":

            $("#lookupTitle").text("Customer");

            renderCustomerHeader();

            loadLookupCustomer();

            break;

    }

    bootstrap.Modal
    .getOrCreateInstance(
        document.getElementById("lookupModal")
    )
    .show();

}

$("#btnLookupPO").click(function(){

    openLookup("PO");

});

function renderPOHeader(){

    $("#lookupTable thead").html(`
        <tr>
            <th>PO</th>
            <th>Supplier</th>
            <th>Material</th>
            <th class="text-end">Qty</th>
            <th class="text-end">BW</th>
        </tr>
    `);

}

function renderCustomerHeader(){
    $("#lookupTable thead").html(`
        <tr>
            <th>Customer</th>
            <th>Nama</th>
            <th>Alamat</th>
        </tr>
    `);
}

function renderSavingTable(){
    let tbody = $("#savingTableAdd tbody");
    tbody.empty();
    $.each(receiveState.savingRows,function(i,row){
        tbody.append(`
            <tr>
                <td>
                    ${row.customer_name}
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control rupiah-input saving-input"
                        data-index="${i}"
                        value="${formatMoney(row.saving)}">
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control saving-remark"
                        data-index="${i}"
                        value="${row.remark}">
                </td>
            </tr>
        `);
    });
}

function renderSavingTotal(){

    let total = 0;

    $.each(receiveState.savingRows,function(i,row){

        total += row.saving;

    });

    $("#savingGrandTotal").text(
        formatMoney(total)
    );

}

function renderPaymentSummary(){

    let summary=
        calculatePaymentSummary();

    let tbody=
        $("#paymentSummaryTableAdd tbody");

    tbody.empty();

    $.each(summary,function(i,row){

        tbody.append(`

            <tr>

                <td>${row.customer_name}</td>

                <td class="text-end">

                    ${formatMoney(row.sales)}

                </td>

                <td class="text-end">

                    ${formatMoney(row.saving)}

                </td>

                <td class="text-end">

                    ${formatMoney(row.grandTotal)}

                </td>

            </tr>

        `);

    });

    let total = calculateGrandTotal(summary);

    $("#grandSalesAdd").text(
        formatMoney(total.sales)
    );

    $("#grandSavingAdd").text(
        formatMoney(total.saving)
    );

    $("#grandPaymentAdd").text(
        formatMoney(total.grand)
    );

}

let lookupTimer;

$("#lookupKeyword").keyup(function(){

    clearTimeout(lookupTimer);

    const keyword=$(this).val();

    lookupTimer=setTimeout(function(){

        switch(lookupType){

            case "PO":

                loadLookupPO(keyword);

                break;

            case "CUSTOMER":

                loadLookupCustomer(keyword);

                break;

        }

    },300);

});

function showLookupLoading(){

    $("#lookupLoading").removeClass("d-none");
    $("#lookupEmpty").addClass("d-none");
    $("#lookupTable").hide();

}

function hideLookupLoading(hasData){

    $("#lookupLoading").addClass("d-none");

    if(hasData){

        $("#lookupEmpty").addClass("d-none");
        $("#lookupTable").show();

    }else{

        $("#lookupEmpty").removeClass("d-none");
        $("#lookupTable").hide();

    }

}

let lookupType = "";
let lookupData = [];

function loadLookupPO(keyword = ""){

    $.ajax({

        url : base_url+"receive/lookup_po",

        type : "GET",

        dataType : "json",

        data : {

            plant : $("#plantAdd").val(),

            keyword : keyword

        },

        beforeSend:function(){

            showLookupLoading();

        },

        success:function(res){

            lookupData = res;

            renderLookupPO();

        },

        complete:function(){

            hideLookupLoading(lookupData.length > 0);

        }

            });
        }

function loadLookupCustomer(keyword=""){

    $.ajax({

        url:base_url+"receive/lookup_customer",

        type:"GET",

        dataType:"json",

        data:{
            keyword:keyword
        },

        beforeSend:function(){

            showLookupLoading();

        },

        success:function(res){

            lookupData = res;

            renderLookupCustomer();

        },

        complete:function(){

            hideLookupLoading(lookupData.length > 0);

        }

    });

}

function renderLookupPO(){

    let tbody=$("#lookupTable tbody");

    tbody.empty();

    $.each(lookupData,function(i,row){

        let tr=$(`
            <tr class="lookup-row">
                <td>${row.PO}</td>
                <td>${row.SUPPLIER_NAME}</td>
                <td>${row.MATERIAL_NAME}</td>
                <td class="text-end">${formatDecimal(row.TOTAL_TERIMA_QTY)}</td>
                <td class="text-end">${formatDecimal(row.TOTAL_TERIMA_BW)}</td>
            </tr>
        `);

        tr.data("row",row);

        tbody.append(tr);

    });

}

function renderLookupCustomer(){

    let tbody=$("#lookupTable tbody");

    tbody.empty();

    $.each(lookupData,function(i,row){

        let tr=$(`
            <tr class="lookup-row">
                <td>${row.CUSTOMER}</td>
                <td>${row.CUSTOMER_NAME}</td>
                <td>${row.ADDRESS ?? "-"}</td>
            </tr>
        `);

        tr.data("row",row);

        tbody.append(tr);

    });

}

$(document).on("click",".lookup-row",function(){

    const row=$(this).data("row");

    switch(lookupType){

        case "PO":

            fillPO(row);

            break;

        case "CUSTOMER":

            addCustomer(row);

            break;

    }

    bootstrap.Modal
        .getOrCreateInstance(
            document.getElementById("lookupModal")
        )
        .hide();

});

function fillPO(po)
{
    /*
    |--------------------------------------------------------------------------
    | RECEIVE INFORMATION
    |--------------------------------------------------------------------------
    */
    receiveState.po = po;
    receiveState.actual = {

        qty : Number(po.JUMLAH),

        bw : Number(po.BERAT),

        avgBw : Number(po.AVG_BW),

        matiQty : Number(po.MATI_QTY),

        matiBw : Number(po.MATI_BW),

        susutBw : Number(po.SUSUT_BW),

        receiveQty : Number(po.TOTAL_TERIMA_QTY),

        receiveBw : Number(po.TOTAL_TERIMA_BW),

        harga : Number(po.HARGA),

        total : Number(po.TOTAL)

    };

    $("#poText").val(po.PO);

    $("#poAdd").val(po.PO);

    $("#supplierAddText").val(po.SUPPLIER_NAME);

    $("#hiddensupplierAdd").val(po.SUPPLIER);

    $("#poMaterialAdd").val(po.MATERIAL_NAME);

    /*
    |--------------------------------------------------------------------------
    | PO INFORMATION
    |--------------------------------------------------------------------------
    */

    $("#masterQtyAdd").text(
        formatDecimal(po.JUMLAH)
    );

    $("#masterBwAdd").text(
        formatDecimal(po.BERAT)
    );

    $("#masterAvgBwAdd").text(
        formatDecimal(po.AVG_BW)
    );

    $("#masterMatiQtyAdd").text(
        formatDecimal(po.MATI_QTY)
    );

    $("#masterMatiBwAdd").text(
        formatDecimal(po.MATI_BW)
    );

    $("#masterSusutBwAdd").text(
        formatDecimal(po.SUSUT_BW)
    );

    $("#masterTerimaQtyAdd").text(
        formatDecimal(po.TOTAL_TERIMA_QTY)
    );

    $("#masterTerimaBwAdd").text(
        formatDecimal(po.TOTAL_TERIMA_BW)
    );

    $("#masterHargaAdd").text(
        formatMoney(po.HARGA)
    );

    $("#masterTotalAdd").text(
        formatMoney(po.TOTAL)
    );

    $("#masterTruckAdd").text(
        po.NO_TRUCK
    );

    $("#masterDriverAdd").text(
        po.DRIVER
    );

    /*
    |--------------------------------------------------------------------------
    | RESET DETAIL
    |--------------------------------------------------------------------------
    */

    resetCustomerSummary();

    receiveState.customerRows = [];
    receiveState.savingRows = [];

    refreshCustomer();

    /*
    |--------------------------------------------------------------------------
    | CLOSE LOOKUP
    |--------------------------------------------------------------------------
    */

    bootstrap.Modal
        .getOrCreateInstance(
            document.getElementById("lookupModal")
        )
        .hide();
}

function addCustomer(customer)
{
    console.log("ADD CUSTOMER", customer);
    let exists =
        receiveState.customerRows.some(function(item){

            return item.customer === customer.CUSTOMER;

        });

    if(exists){

        Swal.fire({

            icon:"warning",

            title:"Customer sudah dipilih."

        });

        return;

    }

    receiveState.customerRows.push({

        customer : customer.CUSTOMER,

        customer_name : customer.CUSTOMER_NAME,

        qty : 0,

        bw : 0,

        harga : receiveState.actual.harga,

        discount : 0,

        remark : "",

        avgBw : 0,

        total : 0

    });

    receiveState.savingRows.push({

        customer : customer.CUSTOMER,

        customer_name : customer.CUSTOMER_NAME,

        saving : 0,

        remark : ""

    });

    refreshCustomer();
    $("#lookupKeyword").val("");

    bootstrap.Modal
    .getOrCreateInstance(
        document.getElementById("lookupModal")
    )
    .hide();
}

function updateCustomerRow(index)
{
    let row = receiveState.customerRows[index];

    row.total =
        (row.bw * row.harga)
        - row.discount;

    if(row.total < 0){
        row.total = 0;
    }
}

function getCustomer(index)
{
    return receiveState.customerRows[index];
}

function removeCustomer(index){
    receiveState.customerRows.splice(index,1);
    receiveState.savingRows.splice(index,1);
    refreshCustomer();
}

function renderCustomerTable()
{
    let tbody =
        $("#customerTableAdd tbody");

    tbody.empty();

    

    $.each(
        receiveState.customerRows,
        function(i,row){
            let avg = 0;

            if(row.qty>0){

                avg = row.bw / row.qty;

            }

            tbody.append(`

                <tr>

                    <td>

                        <strong>

                            ${row.customer_name}

                        </strong>

                    </td>

                    <td>
                        <input
                            type="text"
                            class="form-control decimal-input qty-input"
                            data-index="${i}"
                            value="${formatDecimalID(row.qty)}">
                    </td>

                    <td>
                        <input
                            type="text"
                            class="form-control decimal-input bw-input"
                            data-index="${i}"
                            value="${formatDecimalID(row.bw)}">
                    </td>

                    <td>
                        <input
                            type="text"
                            class="form-control rupiah-input harga-input"
                            data-index="${i}"
                            value="${formatMoney(row.harga)}">
                    </td>

                    <td>
                        <input
                            type="text"
                            class="form-control rupiah-input discount-input"
                            data-index="${i}"
                            value="${formatMoney(row.discount)}">
                    </td>

                    <td>

                        <input
                            type="text"
                            class="form-control remark-input"
                            data-index="${i}"
                            value="${row.remark}">

                    </td>

                    <td>

                        <input
                            class="form-control"
                            readonly
                            value="${formatMoney(row.total)}">

                    </td>

                    <td>

                        <button
                            class="btn btn-danger btn-sm deleteCustomer"
                            data-index="${i}">

                            <i class="ti ti-trash"></i>

                        </button>

                    </td>

                </tr>

            `);

        }

    );

}

function renderCustomerSummary()
{
    let summary =
        calculateAllocation();

    $("#summaryQtyActual")

        .text(

            formatDecimal(
                receiveState.actual.receiveQty
            )

        );

    $("#summaryQtyUsed")

        .text(

            formatDecimal(
                summary.qtyUsed
            )

        );

    $("#summaryQtyRemaining")

        .text(

            formatDecimal(
                summary.qtyRemaining
            )

        );

    $("#summaryBwActual")

        .text(

            formatDecimal(
                receiveState.actual.receiveBw
            )

        );

    $("#summaryBwUsed")

        .text(

            formatDecimal(
                summary.bwUsed
            )

        );

    $("#summaryBwRemaining")

        .text(

            formatDecimal(
                summary.bwRemaining
            )

        );

}

function resetCustomerSummary(){

    $("#summaryQtyActual").text(
        formatDecimal(receiveState.actual.receiveQty)
    );

    $("#summaryQtyUsed").text("0.00");

    $("#summaryQtyRemaining").text(
        formatDecimal(receiveState.actual.receiveQty)
    );

    $("#summaryBwActual").text(
        formatDecimal(receiveState.actual.receiveBw)
    );

    $("#summaryBwUsed").text("0.00");

    $("#summaryBwRemaining").text(
        formatDecimal(receiveState.actual.receiveBw)
    );
}

function resetReceiveForm()
{
    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    $("#freceiveAdd")[0].reset();

    /*
    |--------------------------------------------------------------------------
    | DEFAULT PLANT
    |--------------------------------------------------------------------------
    */

    $("#plantAdd")
        .val("0001")
        .trigger("change");

    /*
    |--------------------------------------------------------------------------
    | CLEAR PO
    |--------------------------------------------------------------------------
    */

    clearPO();

    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    receiveState.po = null;

    receiveState.customerRows = [];

    receiveState.savingRows = [];

    receiveState.actual = {
        qty : 0,
        bw : 0,
        avgBw : 0,
        matiQty : 0,
        matiBw : 0,
        susutBw : 0,
        receiveQty : 0,
        receiveBw : 0,
        harga : 0,
        total : 0
    };
}

function clearPO()
{

    $("#poAdd").val("");

    $("#supplierAddText").val("");

    $("#hiddensupplierAdd").val("");

    $("#poMaterialAdd").val("");

    $("#masterQtyAdd").text("-");

    $("#masterBwAdd").text("-");

    $("#masterAvgBwAdd").text("-");

    $("#masterMatiQtyAdd").text("0");

    $("#masterMatiBwAdd").text("0");

    $("#masterSusutBwAdd").text("0");

    $("#masterTerimaQtyAdd").text("0");

    $("#masterTerimaBwAdd").text("0");

    $("#masterHargaAdd").text("Rp 0");

    $("#masterTotalAdd").text("Rp 0");

    $("#masterTruckAdd").text("-");

    $("#masterDriverAdd").text("-");

    resetCustomerSummary();

    refreshCustomer();

    receiveState.po = null;
    receiveState.customerRows = [];
    receiveState.savingRows = [];
}

function bindCustomerEvent()
{
    $("#btnAddCustomer").on("click",function(){

        openLookup("CUSTOMER");

    });

    $(document).on(
        "click",
        ".deleteCustomer",
        function(){

            let index =
                $(this).data("index");

            removeCustomer(index);

        }
    );

    $(document).on("input",".qty-input",function(){

        let index=$(this).data("index");

        receiveState.customerRows[index].qty=
            parseDecimalID($(this).val());

        updateCustomerRow(index);
        refreshCalculation();

    });

    $(document).on(
        "input",
        ".bw-input",
        function(){

            let index=$(this).data("index");

            receiveState.customerRows[index].bw =
                parseDecimalID($(this).val());

            updateCustomerRow(index);
            refreshCalculation();

        }
    );

    $(document).on(
        "input",
        ".harga-input",
        function(){

            let index=$(this).data("index");

            receiveState.customerRows[index].harga =
                parseMoney($(this).val());

            updateCustomerRow(index);
            refreshCalculation();

        }
    );

    $(document).on(
        "input",
        ".discount-input",
        function(){

            let index=$(this).data("index");

            receiveState.customerRows[index].discount =
                parseMoney($(this).val());

            updateCustomerRow(index);
            refreshCalculation();

        }
    );

    $(document).on(
        "input",
        ".remark-input",
        function(){

            let index=
                $(this).data("index");

            receiveState
                .customerRows[index]
                .remark=

                $(this).val();

        }
    );

    $(document).on(
        "input",
        ".saving-input",
        function(){

            const index = $(this).data("index");

            receiveState.savingRows[index].saving =
                parseMoney($(this).val());

            renderSavingTable();

            renderPaymentSummary();

        }
    );

    $(document).on(
        "keyup",
        ".saving-remark",
        function(){

            const index = $(this).data("index");

            receiveState.savingRows[index].remark =
                $(this).val();

        }
    );
}

function calculateAllocation()
{
    let qty=0;

    let bw=0;

    $.each(
        receiveState.customerRows,
        function(i,row){

            qty+=row.qty;

            bw+=row.bw;

        }
    );

    return{

        qtyUsed:qty,

        bwUsed:bw,

        qtyRemaining:

            receiveState.actual.receiveQty
            -
            qty,

        bwRemaining:

            receiveState.actual.receiveBw
            -
            bw

    };

}

function calculatePaymentSummary(){

    let result=[];

    $.each(receiveState.customerRows,function(i,customer){

        let saving=
            receiveState.savingRows[i];

        let sales=customer.total;

        let savingNominal=
            saving
            ? saving.saving
            : 0;

        result.push({

            customer:customer.customer,

            customer_name:customer.customer_name,

            sales:sales,

            saving:savingNominal,

            grandTotal:
                sales-savingNominal

        });

    });

    return result;

}

function calculateGrandTotal(summary){

    let sales=0;

    let saving=0;

    let grand=0;

    $.each(summary,function(i,row){

        sales+=row.sales;

        saving+=row.saving;

        grand+=row.grandTotal;

    });

    return{

        sales:sales,

        saving:saving,

        grand:grand

    };

}

function refreshCustomer(){
    renderCustomerTable();
    renderSavingTable();
    renderCustomerSummary();
    renderPaymentSummary();
}

function refreshCalculation(){

    renderCustomerSummary();

    renderPaymentSummary();

    renderSavingTotal();

    renderAllocationProgress();

}

let receiveInitialized = false;

$("#receiveAdd").on("shown.bs.modal", function(){
    if(!receiveInitialized){
        receiveInitialized = true;
        initReceive();
    }
    resetReceiveForm();
});

function initReceive()
{
    initPlant();

    bindEvents();
}

/*
|--------------------------------------------------------------------------
| EVENT
|--------------------------------------------------------------------------
*/

function bindEvents()
{
    bindCustomerEvent();
}

/*
|--------------------------------------------------------------------------
| PLANT
|--------------------------------------------------------------------------
*/

function initPlant()
{
    loadPlant();

    $("#plantAdd").on("change", function(){

        $("#hiddenPlantAdd").val(
            $(this).val()
        );

    });
}

function loadPlant(){

    $.get(
        base_url + "receive/get_plant",
        function(res){

            let html = "";

            $.each(res,function(i,row){

                let selected = "";

                if(row.id==="0001"){
                    selected="selected";
                }

                html += `
                    <option
                        value="${row.id}"
                        ${selected}>

                        ${row.id} - ${row.text}

                    </option>
                `;

            });

            let select = $("#plantAdd");

            select.html(html);

            select.val("0001");

            select.trigger("change");

        },
        "json"
    );

}


/*
|--------------------------------------------------------------------------
| PO
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| SAVING
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| PAYMENT SUMMARY
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| RESET
|--------------------------------------------------------------------------
*/




/*
|--------------------------------------------------------------------------
| RECEIVE NUMBER
|--------------------------------------------------------------------------
*/

function loadReceiveNumber()
{

}

/*
|--------------------------------------------------------------------------
| SLIP NUMBER
|--------------------------------------------------------------------------
*/

function loadSlipNumber()
{

}
</script>
