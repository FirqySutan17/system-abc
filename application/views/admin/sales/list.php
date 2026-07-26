<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">SALES - INPUT</h5>

            <div class="row g-2 mb-3">

                <!-- SEARCH -->
                <div class="col-md-3">

                    <input
                        id="search"
                        type="text"
                        class="form-control"
                        placeholder="Cari sales, customer, nota...">

                </div>

                <!-- STATUS -->
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

                        <option value="UNPAID">
                            UNPAID
                        </option>

                        <option value="PAID">
                            PAID
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

                <!-- RESET -->
                <div class="col-md-1">

                    <button
                        class="btn btn-light w-100"
                        id="btnResetFilter">

                        Reset

                    </button>

                </div>

                <!-- ADD -->
                <div class="col-md-2 text-end">

                    <button
                        id="btnAdd"
                        class="btn btn-primary w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#salesAdd">

                        <i class="ti ti-plus"></i>

                        Tambah Sales

                    </button>

                </div>

            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="mainTable">
                    <thead>
                        <tr>

                            <th class="text-center">
                                Plant
                            </th>

                            <th class="text-center">
                                Sales
                            </th>

                            <th class="text-center">
                                Date
                            </th>

                            <th class="text-center">
                                Customer
                            </th>

                            <th class="text-center">
                                Material
                            </th>

                            <th class="text-center">
                                Qty / Weight
                            </th>

                            <th class="text-center">
                                Payment
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                            <th class="text-center">
                                Total
                            </th>

                            <th class="text-center">
                                #
                            </th>

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

    .sales-card .select2-container{
        width:100% !important;
    }

    .sales-card .select2-selection{
        min-height:44px !important;
    }
    .sales-card .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered .select2-selection__placeholder {
        line-height: 2;
        font-size: 14px !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option {
        font-size: 1rem;
        font-size: 14px;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 2;
        font-size: 14PX;
    }

    .section-description {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 18px;
    }

    .saving-table { margin-bottom: 0; }
    .saving-table thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 2px solid #e2e8f0;
    }
    .saving-table tbody td {
        vertical-align: middle;
        padding: 12px 10px;
    }
    .saving-footer { display: flex; justify-content: flex-end; }
    .saving-total {
        min-width: 260px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .saving-total span { color: #64748b; font-size: 14px; }
    .saving-total strong { font-size: 20px; color: #059669; font-weight: 700; }

    .payment-table { margin-bottom: 0; }
    .payment-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    .payment-table tbody td {
        padding: 13px 10px;
        vertical-align: middle;
    }
    .payment-table tbody td:not(:first-child) {
        text-align: right;
        font-weight: 600;
    }
    .payment-footer { display: flex; justify-content: flex-end; }
    .payment-summary-box {
        width: 380px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 22px;
    }
    .payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .payment-row.payment-grand {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
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

                        <div class="card-body sales-card">

                            <div class="row g-3">

                                <!-- PLANT -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        Plant *
                                    </label>

                                    <select
                                        id="plantAdd"
                                        name="PLANT"
                                        class="form-select"
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
                                        class="form-select"
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
                                <div class="col-md-8">
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
                                            <th width="30%" class="text-center">
                                                MATERIAL
                                            </th>
                                            <th width="15%" class="text-center">
                                                JUMLAH
                                            </th>
                                            <th width="15%" class="text-center">
                                                BERAT
                                            </th>
                                            <th width="18%" class="text-center">
                                                HARGA / KG
                                            </th>
                                            <th width="18%" class="text-center">
                                                TOTAL
                                            </th>
                                            <th width="4%" class="text-center">
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

                    <!-- SAVING -->
                    <div class="card border-0 shadow-sm mb-4 mt-3">
                        <div class="card-header bg-light fw-bold">
                            <i class="ti ti-pig-money me-2"></i>
                            CUSTOMER SAVING
                        </div>
                        <div class="card-body">
                            <p class="section-description mb-3">
                                Tambahkan tabungan customer apabila ada potongan yang akan disimpan sebagai saldo/tabungan.
                            </p>
                            <div class="table-responsive">
                                <table class="table saving-table table-bordered align-middle" id="savingTableAdd">
                                    <thead>
                                        <tr>
                                            <th style="width:35%">Customer</th>
                                            <th style="width:20%">Saving Amount</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="saving-footer mt-3">
                                <div class="saving-total">
                                    <span>Total Saving</span>
                                    <strong id="savingGrandTotalAdd">Rp 0</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT SUMMARY -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light fw-bold">
                            <i class="ti ti-cash-banknote me-2"></i>
                            PAYMENT SUMMARY
                        </div>
                        <div class="card-body">
                            <p class="section-description mb-3">
                                Ringkasan pembayaran customer. Total pembayaran terdiri dari nilai penjualan (Sales) dan nominal tabungan (Saving).
                            </p>
                            <div class="table-responsive">
                                <table class="table payment-table table-bordered align-middle" id="paymentSummaryTableAdd">
                                    <thead>
                                        <tr>
                                            <th style="width:40%">Customer</th>
                                            <th class="text-end">Sales</th>
                                            <th class="text-end">Saving</th>
                                            <th class="text-end">Total Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="payment-footer mt-4">
                                <div class="payment-summary-box">
                                    <div class="payment-row">
                                        <span>Total Sales</span>
                                        <strong id="grandSalesAdd">Rp 0</strong>
                                    </div>
                                    <div class="payment-row">
                                        <span>Total Saving</span>
                                        <strong id="grandSavingAdd">Rp 0</strong>
                                    </div>
                                    <hr>
                                    <div class="payment-row payment-grand">
                                        <span>Grand Payment</span>
                                        <strong id="grandPaymentAdd">Rp 0</strong>
                                    </div>
                                </div>
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

            <div class="modal-content border-0 shadow-lg">

                <!-- ========================================= -->
                <!-- HEADER -->
                <!-- ========================================= -->

                <div class="modal-header bg-warning text-dark">

                    <h5 class="modal-title fw-bold" style="color: #fff">
                        SALES - EDIT
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- ========================================= -->
                    <!-- INFORMASI SALES -->
                    <!-- ========================================= -->

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-light fw-bold">
                            INFORMASI SALES
                        </div>

                        <div class="card-body sales-card">

                            <div class="row g-3">

                                <!-- PLANT -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Plant
                                    </label>

                                    <input
                                        id="PLANT_NAME_EDIT"
                                        class="form-control bg-light"
                                        readonly>

                                    <input
                                        type="hidden"
                                        name="PLANT"
                                        id="PLANT_EDIT">

                                </div>

                                <!-- SALES -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        No. Sales
                                    </label>

                                    <input
                                        id="SALES_EDIT"
                                        name="SALES"
                                        class="form-control bg-light"
                                        readonly>

                                </div>

                                <!-- DATE -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Tanggal *
                                    </label>

                                    <input
                                        id="SALES_DATE_EDIT"
                                        name="SALES_DATE"
                                        type="date"
                                        class="form-control"
                                        required>

                                </div>

                                <!-- CUSTOMER -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Customer *
                                    </label>

                                    <select
                                        id="customerEdit"
                                        class="form-select"
                                        required>
                                    </select>

                                    <input
                                        type="hidden"
                                        name="CUSTOMER"
                                        id="hiddenCustomerEdit">

                                </div>

                                <!-- PAYMENT -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold d-block">
                                        Pembayaran
                                    </label>

                                    <div
                                        class="mt-2"
                                        style="display:flex;width:100%;padding-top:10px">

                                        <div class="form-check form-check-inline">

                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="PEMBAYARAN_EDIT"
                                                value="CASH">

                                            <label class="form-check-label">
                                                CASH
                                            </label>

                                        </div>

                                        <div class="form-check form-check-inline">

                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="PEMBAYARAN_EDIT"
                                                value="TRANSFER">

                                            <label class="form-check-label">
                                                TRANSFER
                                            </label>

                                        </div>

                                    </div>

                                </div>

                                <!-- JENIS PAY -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold d-block">
                                        Jenis Pembayaran
                                    </label>

                                    <div
                                        class="mt-2"
                                        style="display:flex;width:100%;padding-top:10px">

                                        <div class="form-check form-check-inline">

                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="JENIS_PAY_EDIT"
                                                value="LUNAS">

                                            <label class="form-check-label">
                                                LUNAS
                                            </label>

                                        </div>

                                        <div class="form-check form-check-inline">

                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="JENIS_PAY_EDIT"
                                                value="TEMPO">

                                            <label class="form-check-label">
                                                TEMPO
                                            </label>

                                        </div>

                                    </div>

                                </div>

                                <!-- NOTA -->
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        No. Nota
                                    </label>

                                    <input
                                        id="NOTA_EDIT"
                                        name="NOTA"
                                        class="form-control"
                                        placeholder="Opsional..">

                                </div>

                                <!-- ATTACH -->
                                <div class="col-md-7">

                                    <label class="form-label fw-semibold">
                                        Attachment
                                    </label>

                                    <input
                                        type="file"
                                        name="ATTACHMENT"
                                        class="form-control"
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">

                                </div>

                                <!-- ATTACHMENT PREVIEW -->
                                <div class="col-md-1" style="display: flex; align-items: center; justify-content: center; padding-top: 19px">

                                    <div id="attachmentPreviewEdit"></div>

                                </div>

                                <!-- REMARK -->
                                <div class="col-md-12">

                                    <label class="form-label fw-semibold">
                                        Remark
                                    </label>

                                    <textarea
                                        id="REMARK_EDIT"
                                        name="REMARK"
                                        class="form-control"
                                        placeholder="Opsional.."
                                        rows="2"></textarea>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- ========================================= -->
                    <!-- DETAIL ITEM -->
                    <!-- ========================================= -->

                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-light d-flex justify-content-between align-items-center">

                            <span class="fw-bold">
                                DETAIL ITEM
                            </span>

                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                id="addDetailRowEdit">

                                <i class="fa fa-plus me-1"></i>
                                Tambah Item

                            </button>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered align-middle"
                                    id="salesDetailTableEdit">

                                    <thead class="table-light">

                                        <tr>

                                            <th width="30%" class="text-center">
                                                MATERIAL
                                            </th>

                                            <th width="15%" class="text-center">
                                                JUMLAH
                                            </th>

                                            <th width="15%" class="text-center">
                                                BERAT
                                            </th>

                                            <th width="18%" class="text-center">
                                                HARGA / KG
                                            </th>

                                            <th width="18%" class="text-center">
                                                TOTAL
                                            </th>

                                            <th width="4%" class="text-center">
                                                #
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody></tbody>

                                </table>

                            </div>

                            <!-- GRAND TOTAL -->

                            <div class="text-end mt-3">

                                <h4 class="fw-bold text-warning mb-0">

                                    GRAND TOTAL :

                                    <span id="grandTotalDisplayEdit">
                                        0
                                    </span>

                                </h4>

                            </div>

                        </div>

                    </div>

                    <!-- SAVING EDIT -->
                    <div class="card border-0 shadow-sm mb-4 mt-3">
                        <div class="card-header bg-light fw-bold">
                            <i class="ti ti-pig-money me-2"></i>
                            CUSTOMER SAVING
                        </div>
                        <div class="card-body">
                            <p class="section-description mb-3">
                                Tabungan customer terkait transaksi sales ini.
                            </p>
                            <div class="table-responsive">
                                <table class="table saving-table table-bordered align-middle" id="savingTableEdit">
                                    <thead>
                                        <tr>
                                            <th style="width:35%">Customer</th>
                                            <th style="width:20%">Saving Amount</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="saving-footer mt-3">
                                <div class="saving-total">
                                    <span>Total Saving</span>
                                    <strong id="savingGrandTotalEdit">Rp 0</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PAYMENT SUMMARY EDIT -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-light fw-bold">
                            <i class="ti ti-cash-banknote me-2"></i>
                            PAYMENT SUMMARY
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table payment-table table-bordered align-middle" id="paymentSummaryTableEdit">
                                    <thead>
                                        <tr>
                                            <th style="width:40%">Customer</th>
                                            <th class="text-end">Sales</th>
                                            <th class="text-end">Saving</th>
                                            <th class="text-end">Total Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="payment-footer mt-4">
                                <div class="payment-summary-box">
                                    <div class="payment-row">
                                        <span>Total Sales</span>
                                        <strong id="grandSalesEdit">Rp 0</strong>
                                    </div>
                                    <div class="payment-row">
                                        <span>Total Saving</span>
                                        <strong id="grandSavingEdit">Rp 0</strong>
                                    </div>
                                    <hr>
                                    <div class="payment-row payment-grand">
                                        <span>Grand Payment</span>
                                        <strong id="grandPaymentEdit">Rp 0</strong>
                                    </div>
                                </div>
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

                        <i class="fa fa-save me-1"></i>

                        Update Sales

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>
    var state = { page: 1, limit: 10, search: '', order: 'SALES', dir: 'ASC' };

    const salesState = {
        savingRowAdd: null,
        savingRowEdit: null
    };

    function getCustomerLabel(selector) {
        let data = $(selector).select2('data');

        if (data && data.length > 0 && data[0].text) {
            return data[0].text;
        }

        let val = $(selector).val();

        return val || '-';
    }

    function extractSavingRemark(remark) {
        if (!remark) {
            return '';
        }

        let marker = ' | AUTO FROM SALES ';

        if (remark.indexOf(marker) !== -1) {
            return remark.split(marker)[0];
        }

        if (remark.indexOf('AUTO FROM SALES ') === 0) {
            return '';
        }

        return remark;
    }

    function syncSavingRowAdd() {
        let customerId = $('#hiddenCustomerAdd').val();

        if (!customerId) {
            salesState.savingRowAdd = null;
            renderSavingTableAdd();
            refreshPaymentSummaryAdd();
            return;
        }

        salesState.savingRowAdd = {
            customer: customerId,
            customer_name: getCustomerLabel('#customerAdd'),
            saving: salesState.savingRowAdd ? salesState.savingRowAdd.saving : 0,
            remark: salesState.savingRowAdd ? salesState.savingRowAdd.remark : ''
        };

        renderSavingTableAdd();
        refreshPaymentSummaryAdd();
    }

    function syncSavingRowEdit(customerId, customerName, savingAmount, remark) {
        if (!customerId) {
            salesState.savingRowEdit = null;
            renderSavingTableEdit();
            refreshPaymentSummaryEdit();
            return;
        }

        salesState.savingRowEdit = {
            customer: customerId,
            customer_name: customerName || customerId,
            saving: parseFloat(savingAmount || 0),
            remark: remark || ''
        };

        renderSavingTableEdit();
        refreshPaymentSummaryEdit();
    }

    function renderSavingTableAdd() {
        let tbody = $('#savingTableAdd tbody');
        tbody.empty();

        if (!salesState.savingRowAdd) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">
                        Pilih customer terlebih dahulu
                    </td>
                </tr>
            `);
            $('#savingGrandTotalAdd').text('Rp 0');
            return;
        }

        let row = salesState.savingRowAdd;

        tbody.append(`
            <tr>
                <td>${row.customer_name}</td>
                <td>
                    <input
                        type="text"
                        class="form-control rupiah-input saving-input-add text-end"
                        value="${formatRupiah(row.saving)}">
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control saving-remark-add"
                        value="${row.remark || ''}">
                </td>
            </tr>
        `);

        $('#savingGrandTotalAdd').text(
            'Rp ' + formatRupiah(row.saving || 0)
        );
    }

    function renderSavingTableEdit() {
        let tbody = $('#savingTableEdit tbody');
        tbody.empty();

        if (!salesState.savingRowEdit) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">
                        Customer belum dipilih
                    </td>
                </tr>
            `);
            $('#savingGrandTotalEdit').text('Rp 0');
            return;
        }

        let row = salesState.savingRowEdit;

        tbody.append(`
            <tr>
                <td>${row.customer_name}</td>
                <td>
                    <input
                        type="text"
                        class="form-control rupiah-input saving-input-edit text-end"
                        value="${formatRupiah(row.saving)}">
                </td>
                <td>
                    <input
                        type="text"
                        class="form-control saving-remark-edit"
                        value="${row.remark || ''}">
                </td>
            </tr>
        `);

        $('#savingGrandTotalEdit').text(
            'Rp ' + formatRupiah(row.saving || 0)
        );
    }

    function getSalesGrandTotalAdd() {
        let grand = 0;

        $('#salesDetailTableAdd tbody tr').each(function () {
            grand += clearFormat($(this).find('.total').val());
        });

        return grand;
    }

    function getSalesGrandTotalEdit() {
        let grand = 0;

        $('#salesDetailTableEdit tbody tr').each(function () {
            grand += parseNumber($(this).find('.total').val());
        });

        return grand;
    }

    function refreshPaymentSummaryAdd() {
        let salesTotal = getSalesGrandTotalAdd();
        let savingTotal = salesState.savingRowAdd
            ? parseFloat(salesState.savingRowAdd.saving || 0)
            : 0;

        let tbody = $('#paymentSummaryTableAdd tbody');
        tbody.empty();

        if (!salesState.savingRowAdd) {
            tbody.append(`
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        Pilih customer terlebih dahulu
                    </td>
                </tr>
            `);
        } else {
            tbody.append(`
                <tr>
                    <td>${salesState.savingRowAdd.customer_name}</td>
                    <td class="text-end">${formatRupiah(salesTotal)}</td>
                    <td class="text-end">${formatRupiah(savingTotal)}</td>
                    <td class="text-end">${formatRupiah(salesTotal + savingTotal)}</td>
                </tr>
            `);
        }

        $('#grandSalesAdd').text('Rp ' + formatRupiah(salesTotal));
        $('#grandSavingAdd').text('Rp ' + formatRupiah(savingTotal));
        $('#grandPaymentAdd').text('Rp ' + formatRupiah(salesTotal + savingTotal));
    }

    function refreshPaymentSummaryEdit() {
        let salesTotal = getSalesGrandTotalEdit();
        let savingTotal = salesState.savingRowEdit
            ? parseFloat(salesState.savingRowEdit.saving || 0)
            : 0;

        let tbody = $('#paymentSummaryTableEdit tbody');
        tbody.empty();

        if (!salesState.savingRowEdit) {
            tbody.append(`
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">
                        Customer belum dipilih
                    </td>
                </tr>
            `);
        } else {
            tbody.append(`
                <tr>
                    <td>${salesState.savingRowEdit.customer_name}</td>
                    <td class="text-end">${formatCurrencyID(salesTotal)}</td>
                    <td class="text-end">${formatCurrencyID(savingTotal)}</td>
                    <td class="text-end">${formatCurrencyID(salesTotal + savingTotal)}</td>
                </tr>
            `);
        }

        $('#grandSalesEdit').text(formatCurrencyID(salesTotal));
        $('#grandSavingEdit').text(formatCurrencyID(savingTotal));
        $('#grandPaymentEdit').text(formatCurrencyID(salesTotal + savingTotal));
    }

    function buildSavingPayload(mode) {
        let row = mode === 'edit'
            ? salesState.savingRowEdit
            : salesState.savingRowAdd;

        if (!row || !row.customer) {
            return [];
        }

        return [{
            CUSTOMER: row.customer,
            SAVING_AMOUNT: parseFloat(row.saving || 0),
            REMARK: row.remark || ''
        }];
    }

    function initPlantSelect2() {
        $('#plantAdd').select2({
            theme:'bootstrap-5',

            placeholder:'-- PILIH PLANT --',

            dropdownParent: $('#salesAdd .modal-body'),

            width:'100%',
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

    $('#btnResetFilter').on('click', function(){

        $('#search').val('');

        $('#filterStatus').val('');

        $('#dateFrom').val('<?= date('Y-m-01'); ?>');

        $('#dateTo').val('<?= date('Y-m-d'); ?>');

        state.search = '';

        loadPage(1);

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

    function formatDecimalID(value){

        value = parseFloat(value || 0);

        if(isNaN(value)){
            value = 0;
        }

        return value.toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }

    function formatCurrencyID(value){

        value = parseFloat(value || 0);

        if(isNaN(value)){
            value = 0;
        }

        return Math.round(value)
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseNumber(value){

        if(!value){
            return 0;
        }

        value = value.toString();

        // hapus titik ribuan
        value = value.replace(/\./g, '');

        // ubah koma decimal jadi titik
        value = value.replace(',', '.');

        let result = parseFloat(value);

        return isNaN(result)
            ? 0
            : result;
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

    $('#filterStatus, #dateFrom, #dateTo').on('change', function(){
        loadPage(1);
    });

    let ajaxListRequest = null;

    function loadPage(page = 1) {
        state.page = page;
        state.search = $('#search').val();
        state.status = $('#filterStatus').val();
        state.date_from = $('#dateFrom').val();
        state.date_to = $('#dateTo').val();

        if (ajaxListRequest) {
            ajaxListRequest.abort(); // batalkan request lama
        }

        ajaxListRequest = $.get('<?= base_url("sales/load_data"); ?>', state, function(resp){
            ajaxListRequest = null;

            resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
            var tbody = $('#table-body').empty();

            resp.rows.forEach(function(row){

                console.log(row.AMOUNT);

                let statusBadge = `
                    <span class="badge bg-warning">
                        OPEN
                    </span>
                `;

                if(row.STATUS === 'PAID'){

                    statusBadge = `
                        <span class="badge bg-success">
                            PAID
                        </span>
                    `;
                }

                if(row.STATUS === 'UNPAID'){

                    statusBadge = `
                        <span class="badge bg-info">
                            UNPAID
                        </span>
                    `;
                }

                if(row.STATUS === 'OPEN'){

                    statusBadge = `
                        <span class="badge bg-warning">
                            OPEN
                        </span>
                    `;
                }

                /*
                |--------------------------------------------------------------------------
                | ATTACHMENT
                |--------------------------------------------------------------------------
                */

                let attachmentIcon = '';

                if(
                    row.ATTACHMENT_NAME &&
                    row.ATTACHMENT_NAME !== ''
                ){

                    attachmentIcon = `
                        <i class="ti ti-paperclip text-primary ms-1"></i>
                    `;
                }

                /*
                |--------------------------------------------------------------------------
                | ROW
                |--------------------------------------------------------------------------
                */

                let tr = `

                    <tr>

                        <!-- PLANT -->
                        <td class="text-center align-middle">

                            <div class="fw-semibold">
                                ${row.PLANT_NAME || '-'}
                            </div>

                        </td>

                        <!-- SALES -->
                        <td class="text-center align-middle">

                            <div class="fw-bold text-primary">
                                #${row.SALES}
                            </div>

                            <small class="text-muted">
                                ${row.NOTA || '-'}
                            </small>

                        </td>

                        <!-- DATE -->
                        <td class="text-center align-middle">

                            ${formatTanggalIndo(
                                row.SALES_DATE
                            )}

                        </td>

                        <!-- CUSTOMER -->
                        <td class="text-center align-middle">

                            <div class="fw-semibold">
                                ${row.CUSTOMER_NAME || '-'}
                            </div>

                            <small class="text-muted">
                                ${row.CUSTOMER || '-'}
                            </small>

                        </td>

                        <!-- MATERIAL -->
                        <td class="text-center align-middle">

                            <div class="fw-semibold">
                                ${row.MATERIAL_NAME || '-'}
                            </div>

                            <small class="text-muted">
                                ${row.MATERIAL || '-'}
                            </small>

                        </td>

                        <!-- QTY -->
                        <td class="text-end align-middle">

                            <div>
                                <span class="fw-semibold">
                                    Qty :
                                </span>

                                ${formatDecimalID(
                                    row.JUMLAH || 0
                                )}
                            </div>

                            <div>
                                <span class="fw-semibold">
                                    Weight :
                                </span>

                                ${formatDecimalID(
                                    row.BERAT || 0
                                )}
                            </div>

                        </td>

                        <!-- PAYMENT -->
                        <td class="text-center align-middle">

                            <div>
                                <span class="badge bg-secondary">
                                    ${row.PEMBAYARAN || '-'}
                                </span>
                            </div>

                            <div class="mt-1">
                                <span class="badge bg-info">
                                    ${row.JENIS_PAY || '-'}
                                </span>
                            </div>

                        </td>

                        <!-- STATUS -->
                        <td class="text-center align-middle">

                            ${statusBadge}

                        </td>

                        <!-- TOTAL -->
                        <td class="text-end align-middle">

                            <div class="fw-bold text-success">

                                ${formatRupiahSales(row.AMOUNT)}

                            </div>

                        </td>

                        <!-- ACTION -->
                        <td class="text-center align-middle">

                            <div class="btn-group btn-group-sm">

                                <!-- SLIP -->
                                <button
                                    class="btn btn-outline-primary exportPdf"
                                    data-sales="${row.SALES}"
                                    data-plant="${row.PLANT}">

                                    Invoice

                                </button>

                                <!-- INVOICE -->
                                <button
                                    class="btn btn-outline-success exportInvoicePdf"
                                    data-sales="${row.SALES}"
                                    data-plant="${row.PLANT}">

                                    Slip

                                </button>

                                <!-- EDIT -->
                                <button
                                    class="btn btn-outline-warning editBtn"
                                    data-sales="${row.SALES}"
                                    data-plant="${row.PLANT}">

                                    Edit

                                </button>

                                <!-- DELETE -->
                                <button
                                    class="btn btn-outline-danger deleteBtn"
                                    data-sales="${row.SALES}"
                                    data-plant="${row.PLANT}">

                                    Hapus

                                </button>

                            </div>

                        </td>

                    </tr>

                `;

                tbody.append(tr);
            });

            $('#pagination').html(resp.pagination);
            let start = ((resp.page - 1) * state.limit) + 1;
            let end   = Math.min(resp.page * state.limit, resp.total);

            $('#info').text(`Menampilkan ${start} - ${end} dari ${resp.total} data`);
        });
    }

    function initCustomerSelect2(selector){

        $(selector).select2({

            theme:'bootstrap-5',

            placeholder:'-- PILIH CUSTOMER --',

            minimumInputLength:3,

            dropdownParent: $('#salesAdd .modal-body'),

            width:'100%',

            ajax:{

                url:'<?= base_url("sales/get_customer"); ?>',

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

        $(selector).on('change', function(){
            let value = $(this).val();

            if (!value) {
                value = 'CS000002';
            }

            $('#hiddenCustomerAdd').val(value);
            syncSavingRowAdd();
        });
    }

    function initCustomerEditSelect2(selector)
    {

        $(selector).select2({

            theme:'bootstrap-5',

            placeholder:'-- PILIH CUSTOMER --',

            minimumInputLength:3,

            dropdownParent: $('#salesEdit .modal-body'),

            width:'100%',

            ajax:{

                url:'<?= base_url("sales/get_customer"); ?>',

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

        /*
        |--------------------------------------------------------------------------
        | CHANGE
        |--------------------------------------------------------------------------
        */

        $(selector).on('change', function(){
            let value = $(this).val();

            if (!value) {
                value = 'CS000002';
            }

            $('#hiddenCustomerEdit').val(value);
            syncSavingRowEdit(
                value,
                getCustomerLabel('#customerEdit'),
                salesState.savingRowEdit ? salesState.savingRowEdit.saving : 0,
                salesState.savingRowEdit ? salesState.savingRowEdit.remark : ''
            );

        });
    }

    $('#customerAdd').on('change', function () {
        $('#hiddenCustomerAdd').val(
            $(this).val()
        );
        syncSavingRowAdd();
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

    function ensureDefaultCustomer(selector, hiddenSelector) {
        let current = $(selector).val();

        if (!current) {
            let defaultId = 'CS000002';
            let defaultText = 'CS000002 - COMMON CUSTOMER';
            let opt = new Option(defaultText, defaultId, true, true);

            $(selector)
                .append(opt)
                .trigger('change');
        }

        if (hiddenSelector) {
            $(hiddenSelector).val($(selector).val() || 'CS000002');
        }

        if (selector === '#customerAdd') {
            syncSavingRowAdd();
        }
    }

    function renderMaterialStockPreview($row, plant, materialId) {
        const $preview = $row.find('.stock-preview');

        if (!$preview.length) {
            return;
        }

        if (!plant || !materialId) {
            $preview.html('');
            return;
        }

        $preview.html('<small class="text-muted">Memuat stok...</small>');

        $.getJSON('<?= base_url("sales/get_stock_preview"); ?>', {
            plant: plant,
            material: materialId
        }, function (resp) {
            if (!resp || !resp.status) {
                $preview.html('<small class="text-muted">Stok tidak tersedia</small>');
                return;
            }

            const stock = resp.stock || {};
            $preview.html(
                '<small class="text-primary">Stok: Qty ' + formatDecimalID(stock.QTY || 0) + ' / BW ' + formatDecimalID(stock.BW || 0) + '</small>'
            );
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
        let berat = parseFloat(
            clearFormat(
                tr.find('.berat').val()
            )
        ) || 0;

        let harga = parseFloat(
            clearFormat(
                tr.find('.harga').val()
            )
        ) || 0;

        let total = berat * harga;

        tr.find('.total')
            .val(formatRupiah(total));

        recalcGrandTotal();
    }

    function recalcRowEdit(tr)
    {
        let berat = parseNumber(
            tr.find('.berat').val()
        );

        let harga = parseNumber(
            tr.find('.harga').val()
        );

        let total = berat * harga;

        tr.find('.total').val(
            formatCurrencyID(total)
        );

        recalcGrandTotalEdit();
    }

    function recalcGrandTotalEdit()
    {
        let grand = 0;

        $('#salesDetailTableEdit tbody tr').each(function(){

            grand += parseNumber(
                $(this).find('.total').val()
            );

        });

        $('#grandTotalDisplayEdit').text(
            formatCurrencyID(grand)
        );

        refreshPaymentSummaryEdit();
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

        refreshPaymentSummaryAdd();
    }

    $(document).on(
        'keyup change',
        '.jumlah,.berat,.harga',
        function(){

            let tr = $(this).closest('tr');

            recalcRow(tr);
        }
    );

    $(document).on(
        'keyup change',
        '#salesDetailTableEdit .jumlah, \
        #salesDetailTableEdit .berat, \
        #salesDetailTableEdit .harga',
        function(){

            let tr = $(this).closest('tr');

            recalcRowEdit(tr);
        }
    );

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
                    <select class="form-select material-select"></select>
                    <div class="stock-preview mt-1"></div>
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control jumlah decimal-input text-end"
                        value="0"
                        placeholder="0,00">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control berat decimal-input text-end"
                        value="0"
                        placeholder="0,00">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control harga rupiah-input text-end"
                        value="0"
                        placeholder="0">
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
                        X
                        <i class="fa fa-trash"></i>

                    </button>

                </td>

            </tr>
        `;

        tbody.append(html);

        let tr = tbody.find('tr').last();

        let selectedPlant = $("#plantAdd option:selected").val();

        let $materialSelectAdd = tr.find('.material-select');

        $materialSelectAdd.select2({

            theme:'bootstrap-5',

            width:'100%',

            dropdownParent: $('#salesAdd .modal-body'),

            placeholder:'-- PILIH MATERIAL --',

            ajax:{

                url:'<?= base_url("sales/get_material"); ?>',

                dataType:'json',

                delay:300,

                data:params => ({
                    q:params.term,
                    plant:selectedPlant
                }),

                processResults:data => ({
                    results:data
                })
            }
        });

        $materialSelectAdd.on('select2:select', function (e) {
            let data = e.params.data || {};
            let $select = $(this);
            let $option = $select.find('option[value="' + data.id + '"]');

            if (!$option.length) {
                $option = $(new Option(data.text, data.id, false, true));
                $select.append($option);
            }

            $option.attr('data-bw', data.bw ?? '');
            $option.attr('data-qty', data.qty ?? '');
            renderMaterialStockPreview(tr, selectedPlant, data.id);
        });

        /*
        |--------------------------------------------------------------------------
        | DEFAULT MATERIAL
        |--------------------------------------------------------------------------
        */

        let defaultMaterial = new Option(
            '01220021 - COMMON MATERIAL',
            '01220021',
            true,
            true
        );

        tr.find('.material-select')
            .append(defaultMaterial)
            .val('01220021')
            .trigger('change');

        renderMaterialStockPreview(tr, selectedPlant, '01220021');

        recalcGrandTotal();
    }

    function addDetailRowEdit(data = {})
    {
        let tbody = $('#salesDetailTableEdit tbody');

        let html = `
            <tr>

                <td>
                    <select class="form-select material-select"></select>
                    <div class="stock-preview mt-1"></div>
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control jumlah decimal-input text-end"
                        value="${formatDecimalID(data.jumlah || 0)}"
                        placeholder="0,00">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control berat decimal-input text-end"
                        value="${formatDecimalID(data.berat || 0)}"
                        placeholder="0,00">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control harga rupiah-input text-end"
                        value="${formatCurrencyID(data.harga || 0)}"
                        placeholder="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control total text-end bg-light fw-bold"
                        readonly
                        value="${formatCurrencyID(data.total || 0)}">
                </td>

                <td class="text-center">

                    <button
                        type="button"
                        class="btn btn-danger btn-sm removeRow">

                        X

                    </button>

                </td>

            </tr>
        `;

        tbody.append(html);

        let tr = tbody.find('tr').last();

        let selectedPlant = $('#PLANT_EDIT').val() || '';

        let $materialSelectEdit = tr.find('.material-select');

        $materialSelectEdit.select2({

            theme:'bootstrap-5',

            width:'100%',

            dropdownParent: $('#salesEdit .modal-body'),

            placeholder:'-- PILIH MATERIAL --',

            minimumInputLength:2,

            ajax:{
                url:'<?= base_url("sales/get_material"); ?>',
                dataType:'json',
                delay:300,
                data:params => ({
                    q:params.term,
                    plant:selectedPlant
                }),
                processResults:data => ({
                    results:data
                })
            }
        });

        $materialSelectEdit.on('select2:select', function (e) {
            let data = e.params.data || {};
            let $select = $(this);
            let $option = $select.find('option[value="' + data.id + '"]');

            if (!$option.length) {
                $option = $(new Option(data.text, data.id, false, true));
                $select.append($option);
            }

            $option.attr('data-bw', data.bw ?? '');
            $option.attr('data-qty', data.qty ?? '');
            renderMaterialStockPreview(tr, selectedPlant, data.id);
        });

        if(data.material){

            let opt = new Option(
                data.material_text,
                data.material,
                true,
                true
            );

            tr.find('.material-select')
                .append(opt)
                .val(data.material)
                .trigger('change');

            renderMaterialStockPreview(tr, selectedPlant, data.material);
        }

        recalcGrandTotalEdit();
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
        initCustomerSelect2('#customerAdd');
        ensureDefaultCustomer('#customerAdd', '#hiddenCustomerAdd');
        loadDefaultCustomer('#customerAdd');
        initCustomerEditSelect2('#customerEdit', '#salesEdit');
        ensureDefaultCustomer('#customerEdit', '#hiddenCustomerEdit');

        $('#addDetailRowAdd').on('click', function(){
            addDetailRow();
        });

        $('#addDetailRowEdit').click(function(){
            addDetailRowEdit({}, '#salesDetailTableEdit');
        });

        // update total on input
        // $('#salesDetailTableAdd, #salesDetailTableEdit').on('input','.qty, .harga, .discount', function(){ updateTotalRow($(this).closest('tr')); });

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

                let total = parseRupiah(
                    $(this).find('.total').val()
                );

                if (!material) {
                    return;
                }

                if (berat <= 0) {

                    alert('Berat wajib diisi');

                    throw 'invalid';
                }

                DETAIL.push({

                    MATERIAL : material,

                    JUMLAH : jumlah,

                    BERAT : berat,

                    HARGA : harga,

                    TOTAL : total
                });
            });

            if (DETAIL.length === 0) {

                alert('Detail item tidak boleh kosong');

                return;
            }

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

            formData.set(
                'SAVINGS',
                JSON.stringify(buildSavingPayload('add'))
            );

            $.ajax({

                url:'<?= base_url("sales/create"); ?>',

                method:'POST',

                data:formData,

                processData:false,

                contentType:false,

                dataType:'json',

                success:function(resp){

                    alert(resp.message);

                    if(resp.status){

                        $('#salesAdd').modal('hide');

                        $('#fsalesAdd')[0].reset();

                        $('#salesDetailTableAdd tbody').empty();

                        $('#grandTotalDisplay').html('0');

                        salesState.savingRowAdd = null;
                        renderSavingTableAdd();
                        refreshPaymentSummaryAdd();

                        $('#customerAdd')
                            .val(null)
                            .trigger('change');

                        loadPage(state.page);
                    }
                },

                error:function(xhr){

                    console.log(xhr.responseText);

                    alert('Terjadi error server');
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
            salesState.savingRowEdit = null;

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

                $('#customerEdit')
                    .empty()
                    .trigger('change');

                let customerOption = new Option(
                    h.CUSTOMER_NAME + ' - ' + h.CUSTOMER,
                    h.CUSTOMER,
                    true,
                    true
                );

                $('#customerEdit')
                    .append(customerOption)
                    .trigger('change');

                $('#hiddenCustomerEdit')
                    .val(h.CUSTOMER);

                // attachment preview
                if (h.ATTACHMENT_PATH) {
                    $('#attachmentPreviewEdit').html(
                        `<a href="<?= base_url(); ?>${h.ATTACHMENT_PATH}" target="_blank" class="btn btn-sm btn-info">
                            Lihat Attachment
                        </a>`
                    );
                }

                d.forEach(function(row){
                    addDetailRowEdit({
                        material: row.MATERIAL,
                        material_text:
                            row.MATERIAL + ' - ' + row.MATERIAL_NAME,
                        jumlah: row.JUMLAH,
                        berat: row.BERAT,
                        harga: row.HARGA,
                        total: row.TOTAL
                    });
                });

                let savingAmount = 0;
                let savingRemark = '';

                if (resp.saving) {
                    savingAmount = parseFloat(resp.saving.AMOUNT || 0);
                    savingRemark = extractSavingRemark(resp.saving.REMARK || '');
                }

                syncSavingRowEdit(
                    h.CUSTOMER,
                    h.CUSTOMER_NAME + ' - ' + h.CUSTOMER,
                    savingAmount,
                    savingRemark
                );

                $('#salesEdit').modal('show');

            }, 'json');
        });

        $('#fsalesEdit').submit(function (e) {
            e.preventDefault();

            let DETAIL = [];

            $('#salesDetailTableEdit tbody tr').each(function () {

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

                let total = parseRupiah(
                    $(this).find('.total').val()
                );

                if (!material) {
                    return;
                }

                DETAIL.push({

                    MATERIAL : material,

                    JUMLAH : jumlah,

                    BERAT : berat,

                    HARGA : harga,

                    TOTAL : total
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
            formData.append(
                'SAVINGS',
                JSON.stringify(buildSavingPayload('edit'))
            );

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

        $(document).on(
            'click',
            '.exportPdf',
            function(){

                let sales = $(this).data('sales');

                let plant = $(this).data('plant');

                window.open(

                    '<?= base_url("sales/print_pdf"); ?>'
                    + '?sales=' + sales
                    + '&plant=' + plant,

                    '_blank'
                );
            }
        );

        $(document).on(
            'click',
            '.exportInvoicePdf',
            function(){

                let sales = $(this).data('sales');

                let plant = $(this).data('plant');

                window.open(

                    '<?= base_url("sales/print_invoice_pdf"); ?>'
                    + '?sales=' + sales
                    + '&plant=' + plant,

                    '_blank'
                );
            }
        );

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

        if (isNaN(value)) {
            value = 0;
        }

        value = Math.round(value).toString();

        return value.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            '.'
        );
    }

    function formatRupiahSales(value){

        value = parseFloat(value || 0);

        if (isNaN(value)) {
            value = 0;
        }

        value = Math.round(value).toString();

        return value.replace(
            /\B(?=(\d{3})+(?!\d))/g,
            '.'
        );
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
        syncSavingRowAdd();
    });

    $(document).on('input', '.saving-input-add', function () {
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));

        if (salesState.savingRowAdd) {
            salesState.savingRowAdd.saving = val;
        }

        $('#savingGrandTotalAdd').text('Rp ' + formatRupiah(val));
        refreshPaymentSummaryAdd();
    });

    $(document).on('input', '.saving-remark-add', function () {
        if (salesState.savingRowAdd) {
            salesState.savingRowAdd.remark = $(this).val();
        }
    });

    $(document).on('input', '.saving-input-edit', function () {
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));

        if (salesState.savingRowEdit) {
            salesState.savingRowEdit.saving = val;
        }

        $('#savingGrandTotalEdit').text('Rp ' + formatRupiah(val));
        refreshPaymentSummaryEdit();
    });

    $(document).on('input', '.saving-remark-edit', function () {
        if (salesState.savingRowEdit) {
            salesState.savingRowEdit.remark = $(this).val();
        }
    });

    $(document).on('input','#BAYAR_AWAL_EDIT', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });
</script>
