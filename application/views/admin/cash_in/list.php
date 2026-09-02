<div class="container-fluid">

    <div class="card w-100">

        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                CASH IN
            </h5>

            <!-- FILTER -->
            <div class="row mb-3">

                <div class="row g-2 mb-3">

                    <!-- SEARCH -->
                    <div class="col-md-3">

                        <input
                            id="search"
                            type="text"
                            class="form-control"
                            placeholder="Cari cash in, customer, bon...">

                    </div>

                    <!-- PAYMENT -->
                    <div class="col-md-2">

                        <select
                            id="filterPembayaran"
                            class="form-control">

                            <option value="">
                                Semua Pembayaran
                            </option>

                            <option value="CASH">
                                CASH
                            </option>

                            <option value="TRANSFER">
                                TRANSFER
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
                            data-bs-target="#CashInAdd">

                            <i class="ti ti-plus"></i>

                            Tambah Cash In

                        </button>

                    </div>

                </div>

            </div>

            <!-- TABLE -->
            <div class="table-box position-relative">

                <!-- LOADING -->
                <div id="tableLoading" class="table-loading d-none">

                    <div class="loading-card">

                        <div class="spinner-border text-primary"></div>

                        <div class="mt-3 fw-semibold">
                            Memuat data...
                        </div>

                        <small class="text-muted">
                            Mohon tunggu, proses ini mungkin memerlukan waktu beberapa detik
                        </small>

                    </div>

                </div>

                <!-- WRAPPER -->
                <div id="tableWrapper">

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle table-modern"
                            id="mainTable">

                            <thead>

                                <tr>

                                    <th class="text-center" data-sort="PLANT">
                                        Plant
                                    </th>

                                    <th class="text-center" data-sort="CASH_IN">
                                        Cash In
                                    </th>

                                    <th class="text-center" data-sort="CASHIN_DATE">
                                        Tanggal
                                    </th>

                                    <th class="text-center">
                                        Customer
                                    </th>

                                    <th class="text-center" data-sort="PEMBAYARAN">
                                        Tipe Pembayaran
                                    </th>

                                    <th class="text-center">
                                        Nomor Invoice
                                    </th>

                                    <th class="text-center" data-sort="TOTAL">
                                        Total
                                    </th>

                                    <th class="text-center">
                                        Status
                                    </th>

                                    <th class="text-center">
                                        Keterangan
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

            <!-- PAGINATION -->
            <div class="d-flex justify-content-between mt-3">

                <div id="info"></div>

                <div id="pagination"></div>

            </div>

        </div>

    </div>

</div>

<style>
    #mainTable thead th[data-sort]{

        cursor: pointer;

        user-select: none;

        position: relative;

    }

    #mainTable thead th[data-sort]:hover{

        background: #f8f9fa;

    }

    .modal-body{
        max-height:75vh;
        overflow-y:auto;
    }
    .table-modern td,
    .table-modern th{

        white-space: nowrap;

        vertical-align: middle;

    }

    .table-box{

        min-height: 300px;

    }

    .table-loading{

        position: absolute;

        inset: 0;

        z-index: 10;

        background: rgba(255,255,255,.82);

        backdrop-filter: blur(2px);

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 12px;

    }

    .loading-card{

        text-align: center;

        padding: 28px 40px;

        background: #fff;

        border-radius: 18px;

        box-shadow: 0 10px 30px rgba(0,0,0,.08);

    }

    .loading-hide{

        opacity: .35;

        pointer-events: none;

    }

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

    .detail-row {

        border: 2px solid #efefef !important;

    }

    .select2-container--open {

        z-index: 999999 !important;

    }

    .select2-dropdown {

        position: absolute !important;

    }

    .mode-wrapper{

        display:flex;

        gap:15px;

    }

    .mode-card{

        flex:1;

        border:2px solid #e5e7eb;

        border-radius:16px;

        padding:20px;

        cursor:pointer;

        transition:.2s;

        position:relative;

    }

    .mode-card:hover{

        border-color:#2563eb;

    }

    .mode-card.active{
        border:2px solid #4e73df;
        background:#f8fbff;
    }

    .mode-card input{

        position:absolute;

        opacity:0;

    }

    .mode-card input:checked + .mode-content{

        color:#2563eb;

    }

    .mode-card:has(input:checked){

        border-color:#2563eb;

        background:#eff6ff;

    }

    .mode-title{

        font-weight:700;

        font-size:16px;

    }

    .mode-sub{

        margin-top:5px;

        font-size:13px;

        color:#6b7280;

    }

    .summary-card{

        border-radius:18px;

        padding:20px;

        color:white;

        box-shadow:0 10px 25px rgba(0,0,0,0.08);

    }

    .summary-label{

        font-size:13px;

        opacity:.9;

        margin-bottom:10px;

    }

    .summary-value{

        font-size:24px;

        font-weight:bold;

    }

    .summary-blue{

        background:linear-gradient(135deg,#2563eb,#1d4ed8);

    }

    .summary-green{

        background:linear-gradient(135deg,#10b981,#059669);

    }

    .summary-orange{

        background:linear-gradient(135deg,#f59e0b,#d97706);

    }

    .summary-red{

        background:linear-gradient(135deg,#ef4444,#dc2626);

    }

    input[type="date"]{
        min-height: 42px;
    }

    .form-check-inline{
        margin-top: 6px;
    }

    .mode-wrapper{
        display:flex;
        gap:16px;
        flex-wrap:wrap;
    }

    .mode-card{
        flex:1;
        min-width:260px;
    }

</style>

<!-- MODAL ADD CASH IN -->
<div class="modal fade" id="CashInAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fCashInAdd">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cash In - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">

                        <!-- PLANT -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Plant *
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="Jakarta"
                                readonly
                                style="background:#efefef">

                            <input
                                type="hidden"
                                name="PLANT"
                                id="plantAdd"
                                value="0001">

                        </div>

                        <!-- CASH IN -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Cash In
                            </label>

                            <input
                                class="form-control"
                                readonly
                                placeholder="Auto Generate"
                                style="background:#efefef">

                        </div>

                        <!-- DATE -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Tanggal *
                            </label>

                            <input
                                type="date"
                                name="CASHIN_DATE"
                                id="cashInDateAdd"
                                class="form-control"
                                value="<?= date('Y-m-d'); ?>"
                                required>

                        </div>

                        <!-- PAYMENT -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Tipe Pembayaran *
                            </label>

                            <div style="width:100%">

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="PEMBAYARAN"
                                        value="CASH" required>

                                    <label class="form-check-label">
                                        CASH
                                    </label>

                                </div>

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="PEMBAYARAN"
                                        value="TRANSFER" required>

                                    <label class="form-check-label">
                                        TRANSFER
                                    </label>

                                </div>

                            </div>

                        </div>

                        <!-- CUSTOMER -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Customer *
                            </label>

                            <select
                                id="customerAdd"
                                class="form-control">
                            </select>

                            <input
                                type="hidden"
                                name="CUSTOMER"
                                id="hiddenCustomerAdd">

                        </div>

                        <!-- SLIP -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Slip No
                            </label>

                            <input
                                name="SLIP_NO"
                                class="form-control"
                                readonly
                                placeholder="Auto Generate"
                                style="background:#efefef">

                        </div>

                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Total Cash In *
                            </label>

                            <input
                                type="text"
                                id="cashInAmount"
                                name="TOTAL_INPUT"
                                class="form-control text-end"
                                placeholder="0"
                                required>

                        </div>

                        <!-- REMARK -->
                        <div class="col-md-12 flex-inline">

                            <label
                                style="width:14.5%"
                                class="form-label">

                                Keterangan
                            </label>

                            <input
                                name="REMARK"
                                class="form-control"
                                placeholder="Keterangan..">

                        </div>

                    </div>

                    <div class="row mb-4">

                        <div class="col-md-12">

                            <div class="mode-wrapper">

                                <!-- FIFO -->
                                <label class="mode-card">

                                    <input
                                        type="radio"
                                        name="MODE_CASH_IN"
                                        value="FIFO"
                                        checked>

                                    <div class="mode-content">

                                        <div class="mode-title">
                                            FIFO AUTO
                                        </div>

                                        <div class="mode-sub">
                                            System otomatis offset invoice tertua
                                        </div>

                                    </div>

                                </label>

                                <!-- MANUAL -->
                                <label class="mode-card">

                                    <input
                                        type="radio"
                                        name="MODE_CASH_IN"
                                        value="MANUAL">

                                    <div class="mode-content">

                                        <div class="mode-title">
                                            MANUAL
                                        </div>

                                        <div class="mode-sub">
                                            User pilih invoice secara manual
                                        </div>

                                    </div>

                                </label>

                            </div>

                        </div>

                    </div>

                    <div class="row mb-4">

                        <!-- TOTAL INPUT -->
                        <div class="col-md-3">

                            <div class="summary-card summary-blue">

                                <div class="summary-label">
                                    TOTAL CASH IN
                                </div>

                                <div
                                    class="summary-value"
                                    id="summaryTotalInput">

                                    Rp 0

                                </div>

                            </div>

                        </div>

                        <!-- ALLOCATED -->
                        <div class="col-md-3">

                            <div class="summary-card summary-green">

                                <div class="summary-label">
                                    BAYAR
                                </div>

                                <div
                                    class="summary-value"
                                    id="summaryAllocated">

                                    Rp 0

                                </div>

                            </div>

                        </div>

                        <!-- REMAINING -->
                        <div class="col-md-3">

                            <div class="summary-card summary-orange">

                                <div class="summary-label">
                                    SISA
                                </div>

                                <div
                                    class="summary-value"
                                    id="summaryRemaining">

                                    Rp 0

                                </div>

                            </div>

                        </div>

                        <!-- DEPOSIT -->
                        <div class="col-md-3">

                            <div class="summary-card summary-red">

                                <div class="summary-label">
                                    DEPOSIT
                                </div>

                                <div
                                    class="summary-value"
                                    id="summaryDeposit">

                                    Rp 0

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="d-flex justify-content-end mb-2">

                        <button
                            type="button"
                            class="btn btn-success btn-sm"
                            id="btnPickInvoice">

                            Pilih Invoice

                        </button>

                    </div>

                    <div class="table-responsive">

                        <table
                            class="table table-bordered align-middle"
                            id="detailTable">

                            <thead class="table-light">
                                <tr>

                                    <th style="width:14%">
                                        Sales
                                    </th>

                                    <th style="width:12%" class="text-end">
                                        Sales Amount
                                    </th>

                                    <th style="width:10%" class="text-end">
                                        Saving
                                    </th>

                                    <th style="width:14%" class="text-end">
                                        Grand Outstanding
                                    </th>

                                    <th style="width:12%" class="text-end">
                                        Bayar
                                    </th>

                                    <th style="width:12%" class="text-end">
                                        Sisa
                                    </th>

                                    <th style="width:8%" class="text-center">
                                        Status
                                    </th>

                                    <th style="width:13%">
                                        Remark / Keterangan
                                    </th>

                                    <th style="width:5%" class="text-center">
                                        #
                                    </th>

                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-6"></div>

                        <div class="col-md-6">

                            <div class="input-group">

                                <span class="input-group-text fw-bold">

                                    Grand Total

                                </span>

                                <input
                                    type="text"
                                    id="grandTotal"
                                    class="form-control text-end fw-bold"
                                    readonly
                                    style="background:#efefef">

                            </div>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" id="btnSaveCashIn" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT CASH IN -->
<div class="modal fade" id="CashInEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fCashInEdit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cash In - UBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HIDDEN -->
                    <input type="hidden" name="CASH_IN" id="CASH_IN_EDIT">
                    <input type="hidden" name="PLANT" id="PLANT_EDIT">

                    <!-- HEADER (SAMA SEPERTI ADD) -->
                    <div class="row g-2 mb-3">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input type="text" id="PLANT_NAME_EDIT" class="form-control"
                                readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Customer *</label>
                            <input id="CUSTOMER_NAME_EDIT" class="form-control" readonly>
                            <input type="hidden" name="CUSTOMER" id="CUSTOMER_EDIT">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Transaksi</label>
                            <input id="CASH_IN_NO_EDIT" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Tipe Pembayaran</label>
                            <div style="padding:5px 0; width:100%">
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
                            <label class="form-label">Slip No *</label>
                            <input id="SLIP_NO_EDIT" class="form-control" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Rekening *</label>
                            <select id="NO_REK_EDIT" name="NO_REK" class="form-control" required></select>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal *</label>
                            <input id="CASHIN_DATE_EDIT" name="CASHIN_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Jumlah</label>
                            <input name="JUMLAH" id="JUMLAH_EDIT" class="form-control">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Lampiran</label>
                            <div style="width:100%">
                                
                                <input type="file" name="ATTACHMENT" class="form-control mt-1" accept=".jpg,.jpeg,.png,.pdf">
                                <a id="attachmentPreviewLink" href="#" target="_blank" style="display:none">
                                    Lihat Lampiran Saat Ini
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">No. Bon *</label>
                            <input name="BON" id="BON_EDIT" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2 flex-inline">
                            <div id="depositInfoBoxEdit" class="alert alert-info py-2 px-3" style="display:none;margin-bottom:0">
                                💰 Saldo Deposit Customer: <b id="depositAmountEdit">0</b>
                            </div>
                        </div>

                    </div>

                    <!-- DETAIL FIFO PREVIEW -->
                    <h5>Preview Alokasi Otomatis (FIFO)</h5>
                    <table class="table table-bordered" id="stockActualDetailTableEdit">
                        <thead>
                            <tr>
                                <th style="text-align:center;">No. Invoice</th>
                                <th style="text-align:center;">Tanggal Offset</th>
                                <th style="text-align:center;">Invoice</th>
                                <th style="text-align:center;">Remain</th>
                                <th style="text-align:center;">Offset</th>
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
    #modalPickInvoice {
        background: #000000c7;
    }
</style>

<div
    class="modal fade"
    id="modalPickInvoice"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h5 class="modal-title">

                    PILIH INVOICE SALES

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- SEARCH -->
                <div class="row mb-3">

                    <div class="col-md-4">

                        <input
                            type="text"
                            id="searchInvoice"
                            class="form-control"
                            placeholder="Cari sales / customer">

                    </div>

                </div>

                <!-- TABLE -->
                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="5%" class="text-center">
                                    #
                                </th>

                                <th width="15%">
                                    Sales
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th width="10%" class="text-end">
                                    Total
                                </th>

                                <th width="10%" class="text-end">
                                    Paid
                                </th>

                                <th width="10%" class="text-end">
                                    Outstanding
                                </th>

                                <th width="10%" class="text-center">
                                    Date
                                </th>

                            </tr>

                        </thead>

                        <tbody id="invoiceListBody"></tbody>

                    </table>

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
                    type="button"
                    class="btn btn-primary"
                    id="btnChooseInvoice">

                    Pilih Invoice

                </button>

            </div>

        </div>

    </div>

</div>

<style>
    .detail-row {
        border: 2px solid #efefef !important;
    }
</style>

<script>
    let ajaxListRequest = null;

    const state = {

        page  : 1,

        limit : 10,

        order : 'CASHIN_DATE',

        dir   : 'DESC'
    };

    function cleanNumber(value) 
    {

        if (value === null || value === undefined || value === '') {
            return 0;
        }

        if (typeof value === 'number') {
            return Number.isFinite(value) ? value : 0;
        }

        let str = String(value)
            .trim()
            .replace(/\s/g, '')
            .replace(/Rp/gi, '');

        if (str === '') {
            return 0;
        }

        /*
        ============================================================
        FORMAT INDONESIA / DATABASE
        ============================================================

        5.000
            -> 5000

        5.000,50
            -> 5000.50

        14151500.00
            -> 14151500

        14151500
            -> 14151500
        */

        if (str.includes(',')) {

            /*
            Ada comma = comma dianggap decimal separator.
            Semua titik dianggap thousand separator.
            */

            str = str
                .replace(/\./g, '')
                .replace(',', '.');

        } else {

            /*
            Tidak ada comma.

            Kalau ada titik:
            - 14151500.00 -> decimal database
            - 5.000       -> thousand Indonesia
            */

            const parts = str.split('.');

            if (
                parts.length > 1 &&
                parts[parts.length - 1].length === 2
            ) {

                /*
                Kemungkinan decimal database:
                14151500.00
                */

                str = str.replace(/,/g, '');

            } else {

                /*
                Format ribuan:
                5.000
                14.151.500
                */

                str = str.replace(/\./g, '');
            }
        }

        const result = parseFloat(str);

        return Number.isFinite(result)
            ? result
            : 0;
    }

    function roundMoney(value) {

        const number = cleanNumber(value);

        return Math.round(
            (number + Number.EPSILON) * 100
        ) / 100;
    }

    function formatMoney(value) {

        const number = roundMoney(value);

        return number.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    function renderEmptyDetail(message)
    {
        $('#detailTable tbody').html(`

            <tr class="empty-row">

                <td
                    colspan="9"
                    class="text-center text-muted py-5"
                >

                    ${escapeHtml(message)}

                </td>

            </tr>

        `);
    }

    function updateSummary() {

        const totalInput =
            getTotalCashIn();

        let allocated = 0;

        $('#detailTable tbody tr').each(
            function() {

                const input =
                    $(this).find('.bayar-input');

                if (!input.length) {
                    return;
                }

                allocated +=
                    cleanNumber(
                        input.val()
                    );
            }
        );

        allocated =
            roundMoney(allocated);

        let remaining =
            roundMoney(
                totalInput - allocated
            );

        if (remaining < 0) {
            remaining = 0;
        }

        const deposit = remaining;

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $('#summaryTotalInput')
            .text(
                formatRupiah(totalInput)
            );

        $('#summaryAllocated')
            .text(
                formatRupiah(allocated)
            );

        $('#summaryRemaining')
            .text(
                formatRupiah(remaining)
            );

        $('#summaryDeposit')
            .text(
                formatRupiah(deposit)
            );

        $('#grandTotal')
            .val(
                formatMoney(allocated)
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ADD DETAIL ROW
    |--------------------------------------------------------------------------
    */
    
    function formatRupiah(value) {

        return 'Rp ' + formatMoney(value);
    }

    function getTotalCashIn()
    {
        return roundMoney(
            $('#cashInAmount').val()
        );
    }

    window.CASH_IN_INVOICES = [];
    window.CURRENT_SAVING_PAY = 0;

    function findCashInInvoice(sales) {

        return window.CASH_IN_INVOICES.find(
            function(row) {
                return String(row.SALES) === String(sales);
            }
        );
    }

    function getInvoiceGrandOutstanding(row) {

        const salesRemain =
            roundMoney(
                row.SALES_REMAIN || 0
            );

        const savingRemain =
            roundMoney(
                row.SAVING_REMAIN || 0
            );

        return roundMoney(
            salesRemain + savingRemain
        );
    }

    function normalizeSalesPickerRow(row) {

        const salesAmount =
            roundMoney(
                row.SALES_AMOUNT || 0
            );

        const salesRemain =
            roundMoney(
                row.SALES_REMAIN || 0
            );

        const savingAmount =
            roundMoney(
                row.SAVING_AMOUNT || 0
            );

        const savingRemain =
            roundMoney(
                row.SAVING_REMAIN || 0
            );

        const grandOutstanding =
            roundMoney(
                salesRemain + savingRemain
            );

        return {

            ...row,

            SALES_AMOUNT:
                salesAmount,

            SALES_REMAIN:
                salesRemain,

            SAVING_AMOUNT:
                savingAmount,

            SAVING_REMAIN:
                savingRemain,

            GRAND_OUTSTANDING:
                grandOutstanding,

            BAYAR:
                0,

            REMAIN_AFTER:
                grandOutstanding,

            REMARK:
                row.REMARK || ''

        };
    }

    function loadCashInInvoices()
    {
        const customer =
            $('#hiddenCustomerAdd').val();

        const plant =
            $('#plantAdd').val();

        console.log('================================');
        console.log('LOAD CASH IN INVOICES');
        console.log('CUSTOMER:', customer);
        console.log('PLANT:', plant);
        console.log('================================');

        if (!customer || !plant) {

            window.CASH_IN_INVOICES = [];

            $('#detailTable tbody').empty();

            updateSummary();

            return;
        }

        $.ajax({

            url:
                '<?= base_url("cashin/load_sales_picker"); ?>',

            type:
                'GET',

            dataType:
                'json',

            data: {

                plant:
                    plant,

                customer:
                    customer

            }

        })
        .done(function(rows) {

            console.log(
                '=== RAW SALES PICKER ==='
            );

            console.log(rows);

            /*
            |--------------------------------------------------------------------------
            | VALIDATE RESPONSE
            |--------------------------------------------------------------------------
            */

            if (!Array.isArray(rows)) {

                console.error(
                    'Response Sales Picker bukan array',
                    rows
                );

                window.CASH_IN_INVOICES = [];

                $('#detailTable tbody').empty();

                renderEmptyDetail(
                    'Format data invoice tidak valid'
                );

                updateSummary();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALIZE
            |--------------------------------------------------------------------------
            */

            window.CASH_IN_INVOICES =
                rows
                    .map(function(row) {

                        const normalized =
                            normalizeSalesPickerRow(
                                row
                            );

                        console.log(
                            'NORMALIZED SALES:',
                            normalized.SALES,
                            {
                                SALES_AMOUNT:
                                    normalized.SALES_AMOUNT,

                                SALES_REMAIN:
                                    normalized.SALES_REMAIN,

                                SAVING_AMOUNT:
                                    normalized.SAVING_AMOUNT,

                                SAVING_REMAIN:
                                    normalized.SAVING_REMAIN,

                                GRAND_OUTSTANDING:
                                    normalized.GRAND_OUTSTANDING
                            }
                        );

                        return normalized;

                    })
                    .filter(function(row) {

                        return (
                            getInvoiceGrandOutstanding(
                                row
                            ) > 0
                        );

                    });

            /*
            |--------------------------------------------------------------------------
            | DEBUG FINAL
            |--------------------------------------------------------------------------
            */

            console.log(
                '=== FINAL CASH_IN_INVOICES ==='
            );

            console.table(
                window.CASH_IN_INVOICES
            );

            /*
            |--------------------------------------------------------------------------
            | CLEAR DETAIL
            |--------------------------------------------------------------------------
            */

            $('#detailTable tbody').empty();

            /*
            |--------------------------------------------------------------------------
            | EMPTY
            |--------------------------------------------------------------------------
            */

            if (
                window.CASH_IN_INVOICES.length === 0
            ) {

                renderEmptyDetail(
                    'Customer tidak memiliki invoice outstanding'
                );

                updateSummary();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | MODE
            |--------------------------------------------------------------------------
            */

            const mode =
                $('input[name="MODE_CASH_IN"]:checked')
                    .val();

            console.log(
                'CURRENT MODE:',
                mode
            );

            /*
            |--------------------------------------------------------------------------
            | FIFO
            |--------------------------------------------------------------------------
            */

            if (mode === 'FIFO') {

                runFIFOAllocation();

            } else {

                renderEmptyDetail(
                    'Silahkan pilih invoice'
                );

            }

            updateSummary();

        })
        .fail(function(xhr) {

            console.error(
                '=== LOAD SALES PICKER ERROR ==='
            );

            console.error(
                xhr.status
            );

            console.error(
                xhr.responseText
            );

            window.CASH_IN_INVOICES = [];

            $('#detailTable tbody').empty();

            renderEmptyDetail(
                'Gagal mengambil invoice'
            );

            updateSummary();

        });
    }

    function renderManualInvoices() {

        $('#detailTable tbody').empty();

        if (
            !window.CASH_IN_INVOICES ||
            window.CASH_IN_INVOICES.length === 0
        ) {

            renderEmptyDetail(
                'Customer tidak memiliki invoice outstanding'
            );

            updateSummary();

            return;
        }

        window.CASH_IN_INVOICES.forEach(
            function(invoice) {

                const grandOutstanding =
                    getInvoiceGrandOutstanding(
                        invoice
                    );

                if (grandOutstanding <= 0) {
                    return;
                }

                /*
                ----------------------------------------------------
                Manual default BAYAR = 0
                ----------------------------------------------------
                */

                invoice.BAYAR = 0;

                invoice.REMAIN_AFTER =
                    grandOutstanding;

                addDetailRow(
                    {
                        SALES:
                            invoice.SALES,

                        CUSTOMER_NAME:
                            invoice.CUSTOMER_NAME,

                        SALES_AMOUNT:
                            invoice.SALES_AMOUNT,

                        SALES_REMAIN:
                            invoice.SALES_REMAIN,

                        SAVING_AMOUNT:
                            invoice.SAVING_AMOUNT,

                        SAVING_REMAIN:
                            invoice.SAVING_REMAIN,

                        GRAND_OUTSTANDING:
                            grandOutstanding,

                        BAYAR:
                            0,

                        REMAIN_AFTER:
                            grandOutstanding,

                        REMARK:
                            invoice.REMARK || ''

                    },
                    false
                );
            }
        );

        updateSummary();
    }
    
    function addDetailRow(data, autoAllocation) {

        const sales =
            data.SALES;

        // const salesAmount =
        //     roundMoney(
        //         data.SALES_AMOUNT || 0
        //     );

        const salesAmount =
            roundMoney(
                data.SALES_REMAIN || 0
            );

        const savingRemain =
            roundMoney(
                data.SAVING_REMAIN || 0
            );

        const grandOutstanding =
            roundMoney(
                data.GRAND_OUTSTANDING || 0
            );

        let bayar =
            roundMoney(
                data.BAYAR || 0
            );

        let remainAfter =
            roundMoney(
                grandOutstanding - bayar
            );

        if (remainAfter < 0) {
            remainAfter = 0;
        }

        const status =
            getInvoiceStatusHtml(
                bayar,
                grandOutstanding
            );

        const remark =
            data.REMARK || '';

        const row = $(`
            <tr
                data-sales="${escapeHtml(sales)}"
            >

                <!-- SALES -->
                <td>
                    <strong>
                        ${escapeHtml(sales)}
                    </strong>
                </td>

                <!-- SALES AMOUNT -->
                <td class="text-end">
                    ${formatRupiah(salesAmount)}
                </td>

                <!-- SAVING -->
                <td class="text-end">
                    ${formatRupiah(savingRemain)}
                </td>

                <!-- GRAND OUTSTANDING -->
                <td class="text-end">
                    <strong>
                        ${formatRupiah(grandOutstanding)}
                    </strong>
                </td>

                <!-- BAYAR -->
                <td class="text-end">

                    <input
                        type="text"
                        class="form-control form-control-sm text-end bayar-input"
                        value="${
                            bayar > 0
                                ? formatMoney(bayar)
                                : ''
                        }"
                        placeholder="0"
                        autocomplete="off"
                    >

                </td>

                <!-- SISA -->
                <td
                    class="text-end remain-cell"
                >
                    ${formatRupiah(remainAfter)}
                </td>

                <!-- STATUS -->
                <td
                    class="text-center status-cell"
                >
                    ${status}
                </td>

                <!-- REMARK -->
                <td>

                    <input
                        type="text"
                        class="form-control form-control-sm remark-input"
                        value="${escapeHtml(remark)}"
                        placeholder="Keterangan..."
                    >

                </td>

                <!-- DELETE -->
                <td class="text-center">

                    <button
                        type="button"
                        class="btn btn-danger btn-sm btn-remove-detail"
                        title="Hapus"
                    >
                        <i class="bi bi-trash"></i>
                    </button>

                </td>

            </tr>
        `);

        $('#detailTable tbody')
            .find('.empty-row')
            .remove();

        $('#detailTable tbody')
            .append(row);

        /*
        |--------------------------------------------------------------------------
        | BAYAR INPUT
        |--------------------------------------------------------------------------
        */

        row.find('.bayar-input').on(
            'input',
            function () {

                let value =
                    cleanNumber(
                        $(this).val()
                    );

                const invoice =
                    findCashInInvoice(
                        sales
                    );

                if (!invoice) {
                    return;
                }

                const grand =
                    getInvoiceGrandOutstanding(
                        invoice
                    );

                /*
                ------------------------------------------------------
                | BAYAR MAX = GRAND OUTSTANDING
                ------------------------------------------------------
                */

                if (value > grand) {

                    value = grand;

                    $(this).val(
                        formatMoney(value)
                    );
                }

                value =
                    roundMoney(value);

                invoice.BAYAR =
                    value;

                invoice.REMAIN_AFTER =
                    roundMoney(
                        grand - value
                    );

                row.find('.remain-cell')
                    .text(
                        formatRupiah(
                            invoice.REMAIN_AFTER
                        )
                    );

                row.find('.status-cell')
                    .html(
                        getInvoiceStatusHtml(
                            value,
                            grand
                        )
                    );

                /*
                |--------------------------------------------------------------------------
                | HITUNG TOTAL BAYAR SEMUA INVOICE
                |--------------------------------------------------------------------------
                */

                let totalDetailBayar = 0;

                $('#detailTable tbody tr').each(
                    function() {

                        totalDetailBayar +=
                            cleanNumber(
                                $(this)
                                    .find('.bayar-input')
                                    .val()
                            );

                    }
                );

                totalDetailBayar =
                    roundMoney(totalDetailBayar);

                /*
                |--------------------------------------------------------------------------
                | DETAIL MENJADI SUMBER CASH IN
                |--------------------------------------------------------------------------
                */

                if (
                    cashInSource !== 'HEADER'
                ) {

                    cashInSource = 'DETAIL';

                    $('#cashInAmount')
                        .val(
                            formatMoney(
                                totalDetailBayar
                            )
                        );
                }

                updateSummary();

                if (
                    $('input[name="MODE_CASH_IN"]:checked').val()
                    === 'MANUAL'
                ) {
                    updateManualValidationState();
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | REMARK
        |--------------------------------------------------------------------------
        */

        row.find('.remark-input').on(
            'input',
            function () {

                const invoice =
                    findCashInInvoice(
                        sales
                    );

                if (invoice) {

                    invoice.REMARK =
                        $(this).val();
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | REMOVE
        |--------------------------------------------------------------------------
        */

        row.find('.btn-remove-detail').on(
            'click',
            function () {

                const invoice =
                    findCashInInvoice(
                        sales
                    );

                if (invoice) {

                    invoice.BAYAR = 0;

                    invoice.REMAIN_AFTER =
                        getInvoiceGrandOutstanding(
                            invoice
                        );
                }

                row.remove();

                if (cashInSource !== 'HEADER') {

                    let totalDetailBayar = 0;

                    $('#detailTable tbody tr').each(
                        function() {

                            totalDetailBayar +=
                                cleanNumber(
                                    $(this)
                                        .find('.bayar-input')
                                        .val()
                                );

                        }
                    );

                    totalDetailBayar =
                        roundMoney(totalDetailBayar);

                    $('#cashInAmount')
                        .val(
                            formatMoney(
                                totalDetailBayar
                            )
                        );

                    cashInSource =
                        totalDetailBayar > 0
                            ? 'DETAIL'
                            : 'NONE';
                }

                updateSummary();
            }
        );
    }

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function collectCashInDetails() {

        const details = [];

        $('#detailTable tbody tr').each(
            function () {

                const row =
                    $(this);

                const sales =
                    row.attr('data-sales');

                if (!sales) {
                    return;
                }

                const bayar =
                    roundMoney(
                        cleanNumber(
                            row.find('.bayar-input')
                                .val()
                        )
                    );

                const remark =
                    row.find('.remark-input')
                        .val() || '';

                /*
                ------------------------------------------------------
                | HANYA KIRIM YANG ADA PEMBAYARAN
                ------------------------------------------------------
                */

                if (bayar <= 0) {
                    return;
                }

                details.push({

                    SALES:
                        sales,

                    BAYAR:
                        bayar,

                    REMARK:
                        remark

                });
            }
        );

        return details;
    }

    function validateCashInForm() {

        const totalInput =
            getTotalCashIn();

        if (totalInput <= 0) {

            alert(
                'Total Cash In harus lebih dari 0.'
            );

            $('#cashInAmount').focus();

            return false;
        }


        const customer =
            $('#hiddenCustomerAdd').val();

        if (!customer) {

            alert(
                'Customer wajib dipilih.'
            );

            return false;
        }


        const payment =
            $('input[name="PEMBAYARAN"]:checked')
                .val();

        if (!payment) {

            alert(
                'Tipe pembayaran wajib dipilih.'
            );

            return false;
        }


        const details =
            collectCashInDetails();

        if (details.length === 0) {

            alert(
                'Belum ada invoice yang dialokasikan.'
            );

            return false;
        }


        let totalBayar =
            0;

        details.forEach(
            function (d) {

                totalBayar +=
                    cleanNumber(
                        d.BAYAR
                    );

            }
        );

        totalBayar =
            roundMoney(totalBayar);


        /*
        ------------------------------------------------------------
        | Tidak boleh melebihi Cash In
        ------------------------------------------------------------
        */

        if (totalBayar > totalInput) {

            alert(
                'Total pembayaran invoice (' +
                formatRupiah(totalBayar) +
                ') melebihi Total Cash In (' +
                formatRupiah(totalInput) +
                ').'
            );

            return false;
        }

        return true;
    }

    function resetCashInAddForm() {

        const form =
            $('#fCashInAdd')[0];
        
        cashInSource = 'NONE';

        if (form) {
            form.reset();
        }

        /*
        ------------------------------------------------------------
        | Default mode FIFO
        ------------------------------------------------------------
        */

        $(
            'input[name="MODE_CASH_IN"][value="FIFO"]'
        ).prop(
            'checked',
            true
        );


        /*
        ------------------------------------------------------------
        | Customer
        ------------------------------------------------------------
        */

        $('#customerAdd')
            .val(null)
            .trigger('change');

        $('#hiddenCustomerAdd')
            .val('');


        /*
        ------------------------------------------------------------
        | Table
        ------------------------------------------------------------
        */

        $('#detailTable tbody')
            .empty();


        /*
        ------------------------------------------------------------
        | Global
        ------------------------------------------------------------
        */

        window.CASH_IN_INVOICES = [];

        window.CURRENT_SAVING_PAY = 0;


        /*
        ------------------------------------------------------------
        | Summary
        ------------------------------------------------------------
        */

        $('#summaryTotalInput')
            .text('Rp 0');

        $('#summaryAllocated')
            .text('Rp 0');

        $('#summaryRemaining')
            .text('Rp 0');

        $('#summaryDeposit')
            .text('Rp 0');

        $('#grandTotal')
            .val('Rp 0');
    }

    $(document).on(
        'click',
        '.removeRow',
        function(){

            $(this)
                .closest('tr')
                .remove();

            updateSummary();

        }
    );

    $('#btnPickInvoice').click(function(){

        let customer =
            $('#hiddenCustomerAdd').val();

        if(!customer){

            alert('Pilih customer terlebih dahulu');

            return;

        }

        loadInvoicePicker();

        $('#modalPickInvoice')
            .modal('show');

    });

    function isInvoiceAlreadySelected(sales)
    {
        let exists = false;

        $('#detailTable tbody tr').each(
            function() {

                const currentSales =
                    $(this).attr('data-sales');

                if (
                    String(currentSales)
                    ===
                    String(sales)
                ) {

                    exists = true;

                    return false;
                }
            }
        );

        return exists;
    }

    function loadInvoicePicker()
    {
        const plant =
            $('#plantAdd').val();

        const customer =
            $('#hiddenCustomerAdd').val();

        const search =
            $('#searchInvoice').val();

        if (!customer) {

            alert(
                'Pilih customer terlebih dahulu'
            );

            return;
        }

        $.get(
            '<?= base_url("cashin/load_sales_picker"); ?>',
            {
                plant:
                    plant,

                customer:
                    customer,

                search:
                    search
            },
            function(rows) {

                const tbody =
                    $('#invoiceListBody');

                tbody.html('');

                if (!Array.isArray(rows)) {

                    tbody.html(`
                        <tr>
                            <td
                                colspan="7"
                                class="text-center text-danger py-4"
                            >
                                Format data invoice tidak valid
                            </td>
                        </tr>
                    `);

                    return;
                }

                if (rows.length === 0) {

                    tbody.html(`
                        <tr>
                            <td
                                colspan="7"
                                class="text-center text-muted py-4"
                            >
                                Tidak ada invoice outstanding
                            </td>
                        </tr>
                    `);

                    return;
                }

                rows.forEach(function(r) {

                    // const salesAmount =
                    //     roundMoney(
                    //         r.SALES_AMOUNT || 0
                    //     );

                    const salesAmount =
                        roundMoney(
                            r.SALES_REMAIN || 0
                        );

                    const salesRemain =
                        roundMoney(
                            r.SALES_REMAIN || 0
                        );

                    const savingAmount =
                        roundMoney(
                            r.SAVING_AMOUNT || 0
                        );

                    const savingRemain =
                        roundMoney(
                            r.SAVING_REMAIN || 0
                        );

                    const grandOutstanding =
                        roundMoney(
                            r.GRAND_OUTSTANDING ||
                            (
                                salesRemain +
                                savingRemain
                            )
                        );

                    const selected =
                        isInvoiceAlreadySelected(
                            r.SALES
                        );

                    const tr = `
                        <tr>

                            <!-- CHECK -->
                            <td class="text-center">

                                <input
                                    type="checkbox"
                                    class="pickInvoice"

                                    data-sales="${escapeHtml(r.SALES)}"

                                    data-customer="${escapeHtml(r.CUSTOMER || '')}"

                                    data-customer-name="${escapeHtml(r.CUSTOMER_NAME || '')}"

                                    data-sales-amount="${salesAmount}"

                                    data-sales-remain="${salesRemain}"

                                    data-saving-amount="${savingAmount}"

                                    data-saving-remain="${savingRemain}"

                                    data-grand-outstanding="${grandOutstanding}"

                                    data-date="${escapeHtml(r.SALES_DATE || '')}"

                                    ${selected ? 'disabled' : ''}
                                >

                            </td>

                            <!-- SALES -->
                            <td>
                                <strong class="text-primary">
                                    #${escapeHtml(r.SALES)}
                                </strong>
                            </td>

                            <!-- SALES AMOUNT -->
                            <td class="text-end">
                                ${formatRupiah(salesAmount)}
                            </td>

                            <!-- SAVING -->
                            <td class="text-end">
                                ${formatRupiah(savingRemain)}
                            </td>

                            <!-- GRAND OUTSTANDING -->
                            <td class="text-end">
                                <strong>
                                    ${formatRupiah(
                                        grandOutstanding
                                    )}
                                </strong>
                            </td>

                            <!-- DATE -->
                            <td class="text-center">
                                ${formatDate(
                                    r.SALES_DATE
                                )}
                            </td>

                            <!-- STATUS -->
                            <td class="text-center">

                                ${
                                    grandOutstanding > 0
                                        ? `
                                            <span class="badge bg-warning text-dark">
                                                OPEN
                                            </span>
                                        `
                                        : `
                                            <span class="badge bg-success">
                                                PAID
                                            </span>
                                        `
                                }

                            </td>

                        </tr>
                    `;

                    tbody.append(tr);
                });

            },
            'json'
        )
        .fail(function(xhr) {

            console.error(
                'Gagal load invoice picker:',
                xhr.responseText
            );

            $('#invoiceListBody')
                .html(`
                    <tr>
                        <td
                            colspan="7"
                            class="text-center text-danger py-4"
                        >
                            Gagal mengambil data invoice
                        </td>
                    </tr>
                `);
        });
    }

    function toggleCashInMode()
    {
        let mode =
            $('input[name="MODE_CASH_IN"]:checked').val();

        /*
        |--------------------------------------------------------------------------
        | FIFO
        |--------------------------------------------------------------------------
        */

        if(mode === 'FIFO'){

            $('#cashInAmount')
                .prop('readonly', false)
                .css('background', '');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | MANUAL
        |--------------------------------------------------------------------------
        */

        $('#cashInAmount')
            .prop('readonly', false)
            .css('background', '');

        /*
        |--------------------------------------------------------------------------
        | AUTO TOTAL FROM DETAIL
        |--------------------------------------------------------------------------
        */

        updateSummary();
    }

    let searchInvoiceTimer = null;

    $('#searchInvoice').on(
        'keyup',
        function(){

            clearTimeout(searchInvoiceTimer);

            searchInvoiceTimer = setTimeout(function(){

                loadInvoicePicker();

            }, 400);

        }
    );

    $('#btnChooseInvoice')
    .off('click.cashInChoose')
    .on(
        'click.cashInChoose',
        function() {

            const checked =
                $('.pickInvoice:checked');

            if (!checked.length) {

                alert(
                    'Pilih minimal satu invoice.'
                );

                return;
            }

            checked.each(function() {

                const checkbox =
                    $(this);

                const sales =
                    checkbox.data('sales');

                if (
                    isInvoiceAlreadySelected(
                        sales
                    )
                ) {
                    return;
                }

                const invoice = {

                    SALES:
                        sales,

                    CUSTOMER_NAME:
                        checkbox.data(
                            'customer-name'
                        ),

                    SALES_AMOUNT:
                        roundMoney(
                            checkbox.data(
                                'sales-amount'
                            )
                        ),

                    SALES_REMAIN:
                        roundMoney(
                            checkbox.data(
                                'sales-remain'
                            )
                        ),

                    SAVING_AMOUNT:
                        roundMoney(
                            checkbox.data(
                                'saving-amount'
                            )
                        ),

                    SAVING_REMAIN:
                        roundMoney(
                            checkbox.data(
                                'saving-remain'
                            )
                        ),

                    GRAND_OUTSTANDING:
                        roundMoney(
                            checkbox.data(
                                'grand-outstanding'
                            )
                        ),

                    BAYAR:
                        0,

                    REMAIN_AFTER:
                        roundMoney(
                            checkbox.data(
                                'grand-outstanding'
                            )
                        ),

                    REMARK:
                        ''
                };

                window.CASH_IN_INVOICES.push(
                    invoice
                );

                addDetailRow(
                    invoice,
                    false
                );
            });

            $('#modalPickInvoice')
                .modal('hide');

            updateSummary();
        }
    );
    
    $(document).on(
        'blur',
        '#cashInAmount',
        function(){
    
            let value =
                cleanNumber($(this).val());
    
            $(this).val(
                formatRupiah(value)
            );
    
            updateSummary();
    
        }
    );

    $(document).off(
        'change.cashInMode',
        'input[name="MODE_CASH_IN"]'
    );

    $(document).on(
        'change.cashInMode',
        'input[name="MODE_CASH_IN"]',
        function () {

            const mode = $(this).val();

            /*
            |--------------------------------------------------------------------------
            | ACTIVE CARD
            |--------------------------------------------------------------------------
            */

            $('.mode-card')
                .removeClass('active');

            $(this)
                .closest('.mode-card')
                .addClass('active');

            /*
            |--------------------------------------------------------------------------
            | TOGGLE CASH IN
            |--------------------------------------------------------------------------
            */

            toggleCashInMode();

            /*
            |--------------------------------------------------------------------------
            | CLEAR DETAIL
            |--------------------------------------------------------------------------
            */

            $('#detailTable tbody')
                .empty();

            /*
            |--------------------------------------------------------------------------
            | MODE
            |--------------------------------------------------------------------------
            */

            if (mode === 'FIFO') {

                $('#btnPickInvoice')
                    .hide();

                runFIFOAllocation();

            } else {

                $('#btnPickInvoice')
                    .show();

                renderEmptyDetail(
                    'Silahkan pilih invoice'
                );

                updateSummary();
            }
        }
    );

    function runFIFOAllocation() {

        const totalInput =
            getTotalCashIn();

        $('#detailTable tbody').empty();

        /*
        ============================================================
        VALIDASI
        ============================================================
        */

        if (totalInput <= 0) {

            updateSummary();

            return;
        }

        if (
            !window.CASH_IN_INVOICES ||
            window.CASH_IN_INVOICES.length === 0
        ) {

            renderEmptyDetail(
                'Customer tidak memiliki invoice outstanding'
            );

            updateSummary();

            return;
        }

        /*
        ============================================================
        CASH AVAILABLE
        ============================================================
        */

        let remainingCash =
            totalInput;

        /*
        ============================================================
        FIFO
        ============================================================
        */

        window.CASH_IN_INVOICES.forEach(
            function(invoice) {

                if (remainingCash <= 0) {
                    return;
                }

                const grandOutstanding =
                    getInvoiceGrandOutstanding(
                        invoice
                    );

                if (grandOutstanding <= 0) {
                    return;
                }

                /*
                ----------------------------------------------------
                BAYAR = min(CASH, GRAND OUTSTANDING)
                ----------------------------------------------------
                */

                let bayar =
                    Math.min(
                        remainingCash,
                        grandOutstanding
                    );

                bayar =
                    roundMoney(bayar);

                if (bayar <= 0) {
                    return;
                }

                invoice.BAYAR =
                    bayar;

                invoice.REMAIN_AFTER =
                    roundMoney(
                        grandOutstanding - bayar
                    );

                /*
                ----------------------------------------------------
                ADD ROW
                ----------------------------------------------------
                */

                addDetailRow(
                    {
                        SALES:
                            invoice.SALES,

                        CUSTOMER_NAME:
                            invoice.CUSTOMER_NAME,

                        SALES_AMOUNT:
                            invoice.SALES_AMOUNT,

                        SALES_REMAIN:
                            invoice.SALES_REMAIN,

                        SAVING_AMOUNT:
                            invoice.SAVING_AMOUNT,

                        SAVING_REMAIN:
                            invoice.SAVING_REMAIN,

                        GRAND_OUTSTANDING:
                            grandOutstanding,

                        BAYAR:
                            bayar,

                        REMAIN_AFTER:
                            invoice.REMAIN_AFTER,

                        REMARK:
                            invoice.REMARK || ''

                    },
                    true
                );

                /*
                ----------------------------------------------------
                CASH BERKURANG
                ----------------------------------------------------
                */

                remainingCash =
                    roundMoney(
                        remainingCash - bayar
                    );

            }
        );

        updateSummary();
    }

    function updateManualValidationState() {

        const totalInput =
            getTotalCashIn();

        let totalBayar = 0;

        $('#detailTable tbody .bayar-input').each(function () {

            totalBayar +=
                cleanNumber(
                    $(this).val()
                );

        });

        totalBayar =
            roundMoney(totalBayar);

        const invalid =
            totalBayar > totalInput;

        $('#btnSaveCashIn')
            .prop('disabled', invalid);

        $('#summaryAllocated')
            .toggleClass(
                'text-danger',
                invalid
            );

        if (invalid) {

            $('#summaryRemaining')
                .text(
                    '-' +
                    formatRupiah(
                        totalBayar - totalInput
                    )
                )
                .addClass('text-danger');

            $('#summaryDeposit')
                .text('0')
                .addClass('text-danger');

        } else {

            $('#summaryRemaining')
                .removeClass('text-danger');

            $('#summaryDeposit')
                .removeClass('text-danger');
        }
    }

    $('#cashInAmount').on(
        'input',
        function() {

            const mode =
                $('input[name="MODE_CASH_IN"]:checked')
                    .val();

            const totalInput =
                cleanNumber(
                    $(this).val()
                );

            /*
            |--------------------------------------------------------------------------
            | HEADER MENJADI SUMBER CASH IN
            |--------------------------------------------------------------------------
            */

            if (totalInput > 0) {

                cashInSource = 'HEADER';

            } else {

                cashInSource = 'NONE';
            }

            if (mode === 'FIFO') {

                clearTimeout(fifoTimer);

                fifoTimer = setTimeout(
                    function() {

                        runFIFOAllocation();

                    },
                    300
                );

            } else {

                recalculateManualAllocation();

            }

            updateSummary();
        }
    );

    function recalculateManualAllocation() {

        const totalInput =
            getTotalCashIn();

        let totalManual =
            0;

        $('#detailTable tbody tr').each(
            function() {

                const row =
                    $(this);

                const sales =
                    row.attr('data-sales');

                const invoice =
                    findCashInInvoice(sales);

                if (!invoice) {
                    return;
                }

                const input =
                    row.find('.bayar-input');

                if (!input.length) {
                    return;
                }

                let bayar =
                    cleanNumber(
                        input.val()
                    );

                const grandOutstanding =
                    getInvoiceGrandOutstanding(
                        invoice
                    );

                /*
                ----------------------------------------------------
                LIMIT PER INVOICE
                ----------------------------------------------------
                */

                if (
                    bayar >
                    grandOutstanding
                ) {

                    bayar =
                        grandOutstanding;

                    input.val(
                        formatMoney(bayar)
                    );
                }

                bayar =
                    roundMoney(bayar);

                invoice.BAYAR =
                    bayar;

                invoice.REMAIN_AFTER =
                    roundMoney(
                        grandOutstanding - bayar
                    );

                totalManual += bayar;

                /*
                ----------------------------------------------------
                UPDATE SISA
                ----------------------------------------------------
                */

                row.find('.remain-cell')
                    .text(
                        formatRupiah(
                            invoice.REMAIN_AFTER
                        )
                    );

                /*
                ----------------------------------------------------
                STATUS
                ----------------------------------------------------
                */

                row.find('.status-cell')
                    .html(
                        getInvoiceStatusHtml(
                            invoice.BAYAR,
                            grandOutstanding
                        )
                    );
            }
        );

        updateSummary();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI MANUAL VS CASH IN
        |--------------------------------------------------------------------------
        */

        const manualExceeded =
            totalManual > totalInput;

        const excess =
            roundMoney(
                totalManual - totalInput
            );

        $('#btnSaveCashIn')
            .prop(
                'disabled',
                manualExceeded
            );

        if (manualExceeded) {

            $('#summaryAllocated')
                .addClass('text-danger');

            $('#summaryRemaining')
                .addClass('text-danger')
                .text(
                    '-' +
                    formatRupiah(excess)
                );

            $('#summaryDeposit')
                .addClass('text-danger')
                .text(
                    'Rp 0'
                );

        } else {

            $('#summaryAllocated')
                .removeClass('text-danger');

            $('#summaryRemaining')
                .removeClass('text-danger');

            $('#summaryDeposit')
                .removeClass('text-danger');
        }
    }

    function getInvoiceStatusHtml(
        bayar,
        grandOutstanding
    ) {

        bayar =
            roundMoney(bayar);

        grandOutstanding =
            roundMoney(grandOutstanding);

        if (bayar <= 0) {

            return `
                <span class="badge bg-secondary">
                    OPEN
                </span>
            `;
        }

        if (bayar >= grandOutstanding) {

            return `
                <span class="badge bg-success">
                    PAID
                </span>
            `;
        }

        return `
            <span class="badge bg-warning text-dark">
                PARTIAL
            </span>
        `;
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER CHANGE
    |--------------------------------------------------------------------------
    */

    $(document).off(
        'change.cashInCustomer',
        '#customerAdd'
    );

    $(document).on(
        'change.cashInCustomer',
        '#customerAdd',
        function () {

            const customer =
                $(this).val();

            console.log(
                'CUSTOMER SELECTED:',
                customer
            );

            $('#hiddenCustomerAdd')
                .val(customer || '');

            /*
            --------------------------------------------------------
            | RESET CASH IN
            --------------------------------------------------------
            */

            $('#cashInAmount')
                .val('');

            cashInSource = 'NONE';

            /*
            --------------------------------------------------------
            | RESET DETAIL
            --------------------------------------------------------
            */

            $('#detailTable tbody')
                .empty();

            /*
            --------------------------------------------------------
            | RESET STATE
            --------------------------------------------------------
            */

            window.CASH_IN_INVOICES = [];

            window.CURRENT_SAVING_PAY = 0;

            /*
            --------------------------------------------------------
            | SUMMARY
            --------------------------------------------------------
            */

            updateSummary();

            /*
            --------------------------------------------------------
            | LOAD INVOICE
            --------------------------------------------------------
            */

            if (!customer) {
                return;
            }

            loadCashInInvoices();
        }
    );

    $(document).on(
        'change.cashInPlant',
        '#plantAdd',
        function () {

            $('#detailTable tbody')
                .empty();

            window.CASH_IN_INVOICES = [];

            updateSummary();

            const customer =
                $('#hiddenCustomerAdd').val();

            if (!customer) {
                return;
            }

            loadCashInInvoices();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | LOADING
    |--------------------------------------------------------------------------
    */

    function showTableLoading()
    {
        $('#tableLoading')
            .removeClass('d-none');

        $('#tableWrapper')
            .addClass('loading-hide');
    }

    function hideTableLoading()
    {
        $('#tableLoading')
            .addClass('d-none');

        $('#tableWrapper')
            .removeClass('loading-hide');
    }

    function initPlantSelect()
    {
        $.get(
            '<?= base_url("cashin/get_plant_select2"); ?>',
            function(rows){

                let $el = $('#plantAdd');

                $el.empty();

                rows.forEach(function(row){

                    $el.append(`
                        <option value="${row.id}">
                            ${row.text}
                        </option>
                    `);

                });

                if(rows.length > 0){

                    $el.val(rows[0].id)
                    .trigger('change');

                }

            },
            'json'
        );
    }

    $('#btnAddManualRow').on('click', function(){
        addDetailRow(null, '#manualDetailTable');
    });

    $(document).on('input', '#JUMLAH_EDIT', function(){

        let customer = $('#CUSTOMER_EDIT').val();
        let plant    = $('#PLANT_EDIT').val();
        let amount   = toNumber($(this).val());

        loadFifoPreview(customer, plant, amount, '#stockActualDetailTableEdit', 'sisa-row-edit');
    });

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

    function loadPage(page = 1)
    {
        state.page = page;

        showTableLoading();

        /*
        |--------------------------------------------------------------------------
        | ABORT PREVIOUS AJAX
        |--------------------------------------------------------------------------
        */

        if(ajaxListRequest){

            ajaxListRequest.abort();

        }

        ajaxListRequest = $.get(

            '<?= base_url("cash-in/load_data"); ?>',

            {

                page        : state.page,

                limit       : state.limit,

                search      : $('#search').val(),

                pembayaran  : $('#filterPembayaran').val(),

                date_from   : $('#dateFrom').val(),

                date_to     : $('#dateTo').val(),

                order       : state.order,

                dir         : state.dir

            },

            function(res){

                ajaxListRequest = null;

                if(typeof res === 'string'){

                    res = JSON.parse(res);

                }

                renderTable(res.rows);

                $('#pagination')
                    .html(res.pagination);

                $('#info').html(
                    `
                        Menampilkan halaman
                        <b>${res.page}</b>
                        dari
                        <b>${Math.ceil(res.total / state.limit)}</b>

                        (Total
                        <b>${res.total}</b>
                        data)
                    `
                );

            },

            'json'

        ).always(function(){

            hideTableLoading();

        });
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER TABLE
    |--------------------------------------------------------------------------
    */

    function renderTable(rows)
    {
        let tbody = $('#table-body');

        tbody.html('');

        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

        if(rows.length === 0){

            tbody.html(`

                <tr>

                    <td
                        colspan="10"
                        class="text-center text-muted py-5">

                        <div class="mb-2">

                            <i
                                class="ti ti-database-off"
                                style="font-size:40px">
                            </i>

                        </div>

                        Tidak ada data cash in

                    </td>

                </tr>

            `);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | LOOP
        |--------------------------------------------------------------------------
        */

        rows.forEach(function(row){

            if(row.STATUS === 'PAID'){

                statusBadge = `
                    <span class="badge bg-success">
                        PAID
                    </span>
                `;
            }
            else if(row.STATUS === 'PARTIAL'){

                statusBadge = `
                    <span class="badge bg-primary">
                        PARTIAL
                    </span>
                `;
            }
            else if(row.STATUS === 'DEPOSIT'){

                statusBadge = `
                    <span class="badge bg-info">
                        DEPOSIT
                    </span>
                `;
            }

            /*
            |--------------------------------------------------------------------------
            | ACTION
            |--------------------------------------------------------------------------
            */

            let tr = `

                <tr>

                    <!-- PLANT -->
                    <td class="text-center">

                        ${row.PLANT_NAME || '-'}

                    </td>

                    <!-- CASH IN -->
                    <td class="text-center">

                        <div class="fw-bold text-primary">

                            #${row.CASH_IN}

                        </div>

                    </td>

                    <!-- DATE -->
                    <td class="text-center">

                        ${formatDate(row.CASHIN_DATE)}

                    </td>

                    <!-- CUSTOMER -->
                    <td>

                        ${row.CUSTOMER_NAME || '-'}

                    </td>

                    <!-- PAYMENT -->
                    <td class="text-center">

                        <span class="
                            badge
                            ${
                                row.PEMBAYARAN === 'CASH'
                                    ? 'bg-success'
                                    : 'bg-primary'
                            }
                        ">

                            ${row.PEMBAYARAN || '-'}

                        </span>

                    </td>

                    <!-- INVOICE -->
                    <td class="text-center">

                        <span class="badge bg-warning text-dark">

                            ${row.TOTAL_INVOICE || 0} Invoice

                        </span>

                    </td>

                    <!-- TOTAL -->
                    <td class="text-end">

                        <div class="fw-bold text-success">

                            Rp ${formatRupiah(
                                Number(row.AMOUNT || 0)
                            )}

                        </div>

                    </td>

                    <!-- STATUS -->
                    <td class="text-center">

                        ${
                            row.STATUS === 'PAID'
                                ? '<span class="badge bg-success">PAID</span>'
                                : row.STATUS === 'PARTIAL'
                                    ? '<span class="badge bg-primary">PARTIAL</span>'
                                    : row.STATUS === 'DEPOSIT'
                                        ? '<span class="badge bg-warning text-dark">DEPOSIT</span>'
                                        : '<span class="badge bg-secondary">OPEN</span>'
                        }

                    </td>

                    <!-- REMARK -->
                    <td>

                        ${row.REMARK || '-'}

                    </td>

                    <!-- ACTION -->
                    <td class="text-center">

                       <div class="btn-group btn-group-sm">

                            <!-- PDF -->
                            <button
                                class="btn btn-outline-primary btnPdf"
                                data-cashin="${row.CASH_IN}"
                                data-plant="${row.PLANT}">

                                Slip

                            </button>

                            ${
                                Number(row.IS_LATEST) === 1
                                ? `

                                    <button
                                        class="btn btn-outline-danger btnDelete"
                                        data-cashin="${row.CASH_IN}"
                                        data-plant="${row.PLANT}">

                                        Hapus

                                    </button>

                                `
                                : `

                                    <button
                                        class="btn btn-outline-secondary"
                                        disabled
                                        title="Cash in lama tidak bisa dihapus">

                                        Terkunci

                                    </button>

                                `
                            }

                        </div>

                    </td>

                </tr>

            `;

            tbody.append(tr);

        });
    }

    let searchTimer = null;

    $('#search').on('keyup', function(){

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function(){

            loadPage(1);

        }, 400);

    });

    $('#filterPembayaran').change(function(){

        loadPage(1);

    });

    $('#dateFrom').change(function(){

        loadPage(1);

    });

    $('#dateTo').change(function(){

        loadPage(1);

    });

    $('#btnResetFilter').click(function(){

        $('#search').val('');

        $('#filterPembayaran').val('');

        $('#dateFrom').val(
            '<?= date('Y-m-01'); ?>'
        );

        $('#dateTo').val(
            '<?= date('Y-m-d'); ?>'
        );

        state.order = 'CASHIN_DATE';

        state.dir = 'DESC';

        loadPage(1);

    });

    $(document).on(
        'click',
        '#mainTable thead th',
        function(){

            let field =
                $(this).data('sort');

            if(!field){

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | TOGGLE DIR
            |--------------------------------------------------------------------------
            */

            if(state.order === field){

                state.dir =
                    state.dir === 'ASC'
                        ? 'DESC'
                        : 'ASC';

            }else{

                state.order = field;

                state.dir = 'ASC';

            }

            loadPage(1);

        }
    );

    $(document).on(
        'click',
        '.page-link',
        function(e){

            e.preventDefault();

            let page =
                $(this).data('page');

            if(page){

                loadPage(page);

            }

        }
    );

    $(document).on("click", ".btnPdf", function () {
        let cash_in = $(this).data("cashin");
        let plant = $(this).data("plant");

        window.open(
            "<?= base_url('cashin/print_pdf'); ?>?cash_in=" + cash_in + "&plant=" + plant,
            "_blank"
        );
    });

    $('#CashInAdd').on('shown.bs.modal', function () {

        initCustomerSelect2('#customerAdd', '#CashInAdd');

        initRekeningSelect2('#NO_REK', '#CashInAdd');

        $('#cashInDateAdd').val(
            '<?= date('Y-m-d'); ?>'
        );

        $('#plantAdd').val('0001');

        window.CURRENT_SAVING_PAY = 0;

        $('input[name="MODE_CASH_IN"][value="FIFO"]')
            .prop('checked', true)
            .trigger('change');

        renderEmptyDetail(
            'Pilih customer terlebih dahulu'
        );

    });

    $('#CashInEdit').on('shown.bs.modal', function () {
        initRekeningSelect2('#NO_REK_EDIT', '#CashInEdit');
    });

    $(document).on('input', '.amount-offset', function () {
        let raw = this.value.replace(/[^\d]/g, '');
        this.value = raw;
    });

    function initCustomerSelect2(selector, modalId){

        if ($(selector).hasClass("select2-hidden-accessible")) {
            return;
        }

        $(selector).select2({

            placeholder: "-- PILIH CUSTOMER --",

            allowClear: true,

            minimumInputLength: 3,

            language: {
                inputTooShort: function () {
                    return 'Ketik minimal 3 karakter';
                }
            },

            dropdownParent: modalId
                ? $(modalId)
                : $(document.body),

            width: "100%",

            ajax: {

                url: "<?= base_url('cash-in/get-customer'); ?>",

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

    function initRekeningSelect2(selector, modalId){
        if ($(selector).hasClass("select2-hidden-accessible")) return;
        $(selector).select2({
            placeholder: "-- PILIH REKENING --",
            allowClear: true,
            dropdownParent: modalId ? $(modalId) : $(document.body),
            width: "100%",
            ajax: {
                url: "<?= base_url('cash-in/get-rekening'); ?>",
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

    $('#cashInDetailTableEdit').on('click', '.pickInvoiceBtn', function(){
        alert('Invoice tidak dapat diganti pada mode edit');
    });
    
    let fifoTimer = null;
    let cashInSource = 'NONE';
    let cashInSubmitting = false;

    $(function(){
        loadPage(1);

        $('#fCashInAdd').on(
            'submit',
            function (e) {

                e.preventDefault();

                if (cashInSubmitting) {
                    return;
                }

                if (!validateCashInForm()) {
                    return;
                }

                cashInSubmitting = true;


                /*
                ========================================================
                | DETAIL
                ========================================================
                */

                const details =
                    collectCashInDetails();


                /*
                ========================================================
                | FORM DATA
                ========================================================
                */

                const formData =
                    new FormData(
                        this
                    );


                /*
                --------------------------------------------------------
                | TOTAL INPUT
                --------------------------------------------------------
                */

                const totalInput =
                    getTotalCashIn();

                formData.set(
                    'TOTAL_INPUT',
                    totalInput
                );


                /*
                --------------------------------------------------------
                | CUSTOMER
                --------------------------------------------------------
                */

                formData.set(
                    'CUSTOMER',
                    $('#hiddenCustomerAdd').val()
                );


                /*
                --------------------------------------------------------
                | MODE
                --------------------------------------------------------
                */

                formData.set(
                    'MODE_CASH_IN',
                    $(
                        'input[name="MODE_CASH_IN"]:checked'
                    ).val()
                );


                /*
                ========================================================
                | DETAIL
                ========================================================
                */

                details.forEach(
                    function (detail, index) {

                        formData.append(
                            `DETAIL[${index}][SALES]`,
                            detail.SALES
                        );

                        formData.append(
                            `DETAIL[${index}][BAYAR]`,
                            detail.BAYAR
                        );

                        formData.append(
                            `DETAIL[${index}][REMARK]`,
                            detail.REMARK
                        );

                    }
                );

                /*
                ========================================================
                | BUTTON
                ========================================================
                */

                const btn =
                    $('#btnSaveCashIn');

                btn
                    .prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm me-1"></span>' +
                        'Menyimpan...'
                    );


                /*
                ========================================================
                | AJAX
                ========================================================
                */

                $.ajax({

                    url:
                        '<?= base_url("cash-in/create"); ?>',

                    type:
                        'POST',

                    data:
                        formData,

                    processData:
                        false,

                    contentType:
                        false,

                    dataType:
                        'json'

                })
                .done(
                    function (response) {

                        if (response.status) {

                            alert(
                                response.message ||
                                'Cash In berhasil disimpan.'
                            );

                            /*
                            ------------------------------------------------
                            | CLOSE MODAL
                            ------------------------------------------------
                            */

                            $('#CashInAdd')
                                .modal('hide');

                            /*
                            ------------------------------------------------
                            | RESET FORM
                            ------------------------------------------------
                            */

                            resetCashInAddForm();

                            /*
                            ------------------------------------------------
                            | RELOAD LIST
                            ------------------------------------------------
                            */

                            loadPage(1);

                        } else {

                            alert(
                                response.message ||
                                'Gagal menyimpan Cash In.'
                            );
                        }

                    }
                )
                .fail(
                    function (xhr) {

                        console.error(
                            '=== CREATE ERROR ==='
                        );

                        console.error(
                            xhr.responseText
                        );

                        alert(
                            'Terjadi error saat menyimpan Cash In.'
                        );
                    }
                )
                .always(
                    function () {

                        cashInSubmitting = false;

                        btn
                            .prop('disabled', false)
                            .html(
                                'Simpan'
                            );
                    }
                );
            }
        );

        $(document).on('click', '.editBtn', function () {

            const cashIn = $(this).data('cashin');

            $.get('<?= base_url("cash-in/edit"); ?>', { cash_in: cashIn }, function(resp){

                if (!resp.status) return alert(resp.message);

                const h = resp.header;

                $('#CASH_IN_EDIT').val(h.CASH_IN);
                $('#PLANT_EDIT').val(h.PLANT);
                $('#PLANT_NAME_EDIT').val(h.PLANT_NAME);
                $('#CUSTOMER_EDIT').val(h.CUSTOMER);
                $('#CUSTOMER_NAME_EDIT').val(h.CUSTOMER + ' - ' + h.CUSTOMER_NAME);
                $('#CASH_IN_NO_EDIT').val(h.CASH_IN);
                $('#SLIP_NO_EDIT').val(h.SLIP_NO);
                $('#BON_EDIT').val(h.BON);
                $('#CASHIN_DATE_EDIT').val(h.CASHIN_DATE);
                $('#JUMLAH_EDIT').val(formatRupiah(h.AMOUNT));

                // loadFifoPreview(h.CUSTOMER, h.PLANT, toNumber(h.AMOUNT), '#stockActualDetailTableEdit', 'sisa-row-edit');

                if (h.ATTACHMENT) {
                    $('#attachmentPreviewLink')
                        .attr('href', '<?= base_url(); ?>' + h.ATTACHMENT)
                        .show();
                } else {
                    $('#attachmentPreviewLink').hide();
                }

                $('input[name="PEMBAYARAN"][value="'+h.PEMBAYARAN+'"]').prop('checked', true);

                initRekeningSelect2('#NO_REK_EDIT', '#CashInEdit');
                let opt = new Option(h.REK_NAME, h.NO_REK, true, true);
                $('#NO_REK_EDIT').append(opt).trigger('change');

                $('#CashInEdit').modal('show');
            }, 'json');
        });

        $('#fCashInEdit').submit(function(e){
            e.preventDefault();

            let btn = $(this).find('button[type=submit]');
            btn.prop('disabled', true).text('Menyimpan...');

            let formData = new FormData(this);

            if ($('#stockActualDetailTableEdit input[name*="[AMOUNT_OFFSET]"]').filter(function(){ 
                return toNumber(this.value) > 0; 
            }).length === 0) {
                alert('Tidak ada invoice yang teralokasi');
                return;
            }

            $.ajax({
                url: '<?= base_url("cash-in/update"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp){
                    alert(resp.message);
                    if(resp.status){
                        $('#CashInEdit').modal('hide');
                        loadPage(state.page);
                    }
                    btn.prop('disabled', false).text('Update');
                },
                error: function(){
                    alert('Terjadi kesalahan server');
                    btn.prop('disabled', false).text('Simpan');
                }
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
        $(document).on(
            'click',
            '.btnDelete',
            function(){

                if(
                    !confirm(
                        'Hapus cash in ini?'
                    )
                ){
                    return;
                }

                let cashin =
                    $(this).data('cashin');

                let plant =
                    $(this).data('plant');

                $.post(

                    '<?= base_url("cashin/remove"); ?>',

                    {

                        cashin : cashin,

                        plant  : plant

                    },

                    function(res){

                        if(typeof res === 'string'){

                            res = JSON.parse(res);

                        }

                        alert(res.message);

                        if(res.status){

                            loadPage(state.page);

                        }

                    },

                    'json'

                );

            }
        );

    }); // end ready

    const CURRENT_USER = "<?= $this->session->userdata('username'); ?>";
</script>

