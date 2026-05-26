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
                            Loading data...
                        </div>

                        <small class="text-muted">
                            Please wait a moment
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
                                        Date
                                    </th>

                                    <th class="text-center">
                                        Customer
                                    </th>

                                    <th class="text-center" data-sort="PEMBAYARAN">
                                        Payment
                                    </th>

                                    <th class="text-center">
                                        Invoice
                                    </th>

                                    <th class="text-center" data-sort="TOTAL">
                                        Total
                                    </th>

                                    <th class="text-center">
                                        Status
                                    </th>

                                    <th class="text-center">
                                        Remark
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

                            <select
                                name="PLANT"
                                id="plantAdd"
                                class="form-control"
                                required>
                            </select>

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
                                class="form-control"
                                required>

                        </div>

                        <!-- PAYMENT -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Pembayaran *
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

                                Remark
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
                                    ALLOCATED
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
                                    REMAINING
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

                                    <th style="width:15%">
                                        Sales
                                    </th>

                                    <th>
                                        Customer
                                    </th>

                                    <th style="width:12%" class="text-end">
                                        Outstanding
                                    </th>

                                    <th style="width:12%" class="text-end">
                                        Bayar
                                    </th>

                                    <th style="width:12%" class="text-end">
                                        Sisa
                                    </th>

                                    <th style="width:10%" class="text-center">
                                        Status
                                    </th>

                                    <th style="width:20%">
                                        Remark
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
                    <h5 class="modal-title">Cash In - EDIT</h5>
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
                            <label class="form-label d-block">Pembayaran</label>
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
                            <label class="form-label">Attachment</label>
                            <div style="width:100%">
                                
                                <input type="file" name="ATTACHMENT" class="form-control mt-1" accept=".jpg,.jpeg,.png,.pdf">
                                <a id="attachmentPreviewLink" href="#" target="_blank" style="display:none">
                                    Lihat Attachment Saat Ini
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
                    <button type="submit" class="btn btn-primary">Update</button>
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

        dir   : 'ASC'
    };

    function cleanNumber(value)
    {
        return parseFloat(
            String(value || 0)
                .replace(/\./g, '')
                .replace(/,/g, '.')
        ) || 0;
    }

    function formatRupiah(value)
    {
        return Number(
            value || 0
        ).toLocaleString(
            'id-ID'
        );
    }

    $(document).on(
        'keyup',
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

    function renderEmptyDetail(message)
    {
        $('#detailTable tbody').html(`

            <tr class="empty-row">

                <td
                    colspan="8"
                    class="text-center text-muted py-5">

                    ${message}

                </td>

            </tr>

        `);
    }

    function updateSummary()
    {
        /*
        |--------------------------------------------------------------------------
        | TOTAL INPUT
        |--------------------------------------------------------------------------
        */
        let mode =
            $('input[name="MODE_CASH_IN"]:checked').val();

        let totalInput =
            cleanNumber(
                $('#cashInAmount').val()
            );

        /*
        |--------------------------------------------------------------------------
        | ALLOCATED
        |--------------------------------------------------------------------------
        */

        let allocated = 0;

        $('.pay-input').each(function(){

            allocated += cleanNumber(
                $(this).val()
            );

        });

        CURRENT_ALLOCATED = allocated;

        if(mode === 'MANUAL'){

            $('#cashInAmount').val(
                formatRupiah(allocated)
            );

            totalInput = allocated;
        }

        $('#detailTable tbody tr').each(function(){

            let bayar =
                cleanNumber(
                    $(this)
                        .find('.pay-input')
                        .val()
                );

            allocated += bayar;

        });

        /*
        |--------------------------------------------------------------------------
        | REMAINING
        |--------------------------------------------------------------------------
        */

        let remaining =
        Math.max(
            totalInput - allocated,
            0
        );

        /*
        |--------------------------------------------------------------------------
        | DEPOSIT
        |--------------------------------------------------------------------------
        */

        let deposit = 0;

        if(remaining > 0){

            deposit = remaining;

        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE CARD
        |--------------------------------------------------------------------------
        */

        $('#summaryTotalInput').html(
            'Rp ' + formatRupiah(totalInput)
        );

        $('#summaryAllocated').html(
            'Rp ' + formatRupiah(allocated)
        );

        $('#summaryRemaining').html(
            'Rp ' + formatRupiah(remaining)
        );

        $('#summaryDeposit').html(
            'Rp ' + formatRupiah(deposit)
        );

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL
        |--------------------------------------------------------------------------
        */

        $('#grandTotal').val(
            'Rp ' + formatRupiah(allocated)
        );
    }

    $(document).on(
        'keyup change',
        '.pay-input',
        function(){

            updateDetailRow($(this));

            updateSummary();

        }
    );

    function updateDetailRow(el)
    {
        let tr = el.closest('tr');

        let outstanding =
            cleanNumber(
                tr.find('.outstanding-value').val()
            );

        let bayar =
            cleanNumber(
                tr.find('.pay-input').val()
            );

        /*
        |--------------------------------------------------------------------------
        | LIMIT PAYMENT
        |--------------------------------------------------------------------------
        */

        if(bayar > outstanding){

            bayar = outstanding;

            tr.find('.pay-input').val(
                formatRupiah(bayar)
            );

        }

        /*
        |--------------------------------------------------------------------------
        | SISA
        |--------------------------------------------------------------------------
        */

        let sisa =
            outstanding - bayar;

        tr.find('.remaining-text').html(
            'Rp ' + formatRupiah(sisa)
        );

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        let status = 'UNPAID';
        let badge  = 'bg-secondary';

        if(bayar > 0){

            status = 'PARTIAL';
            badge  = 'bg-primary';

        }

        if(
            bayar >= outstanding &&
            outstanding > 0
        ){

            status = 'FULL';
            badge  = 'bg-success';

        }

        tr.find('.status-cell').html(
            `
                <span class="badge ${badge}">
                    ${status}
                </span>
            `
        );
    }

    $('.mode-card input').change(function(){

        $('.mode-card')
            .removeClass('active');

        $(this)
            .closest('.mode-card')
            .addClass('active');

    });

    /*
    |--------------------------------------------------------------------------
    | ADD DETAIL ROW
    |--------------------------------------------------------------------------
    */

    function addDetailRow(
        data,
        isFIFO = false
    )
    {
        /*
        |--------------------------------------------------------------------------
        | DEFAULT
        |--------------------------------------------------------------------------
        */

        let salesNo =
            data.SALES || '-';

        let customer =
            data.CUSTOMER_NAME || '-';

        let outstanding =
            parseFloat(data.OUTSTANDING || 0);

        let bayar =
            parseFloat(data.BAYAR || 0);

        let remaining =
            Math.max(
                outstanding - bayar,
                0
            );

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        let status = 'UNPAID';
        let badge  = 'bg-secondary';

        if(bayar > 0){

            status = 'PARTIAL';
            badge  = 'bg-primary';

        }

        if(
            bayar >= outstanding &&
            outstanding > 0
        ){

            status = 'FULL';
            badge  = 'bg-success';

        }

        /*
        |--------------------------------------------------------------------------
        | ROW
        |--------------------------------------------------------------------------
        */

        let row = `

            <tr class="detail-row">

                <!-- SALES -->
                <td>

                    <div class="fw-semibold text-primary">

                        #${salesNo}

                    </div>

                    <input
                        type="hidden"
                        name="DETAIL[${salesNo}][SALES]"
                        value="${salesNo}">

                </td>

                <!-- CUSTOMER -->
                <td>

                    ${customer}

                </td>

                <!-- OUTSTANDING -->
                <td class="text-end">

                    <div class="fw-semibold">

                        Rp ${formatRupiah(outstanding)}

                    </div>

                    <input
                        type="hidden"
                        class="outstanding-value"
                        value="${outstanding}">

                </td>

                <!-- BAYAR -->
                <td>

                    <input
                        type="text"
                        class="form-control text-end pay-input"
                        name="DETAIL[${salesNo}][BAYAR]"
                        value="${formatRupiah(bayar)}"
                        ${isFIFO ? 'readonly' : ''}
                    >

                </td>

                <!-- SISA -->
                <td class="text-end">

                    <div class="remaining-text">

                        Rp ${formatRupiah(remaining)}

                    </div>

                </td>

                <!-- STATUS -->
                <td class="text-center status-cell">

                    <span class="badge ${badge}">

                        ${status}

                    </span>

                </td>

                <!-- REMARK -->
                <td>

                    <input
                        type="text"
                        class="form-control"
                        name="DETAIL[${salesNo}][REMARK]"
                        value="${data.REMARK || ''}">

                </td>

                <!-- ACTION -->
                <td class="text-center">

                    ${isFIFO ? '' : `

                        <button
                            type="button"
                            class="btn btn-sm btn-danger btnDeleteRow">

                            x

                        </button>

                    `}

                </td>

            </tr>

        `;
        $('#detailTable tbody .empty-row').remove();
        $('#detailTable tbody')
            .append(row);

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        updateSummary();
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

    function loadInvoicePicker()
    {
        $.get(

            '<?= base_url("cashin/load_sales_picker"); ?>',

            {

                plant   : $('#plantAdd').val(),

                customer:
                    $('#hiddenCustomerAdd').val(),

                search  :
                    $('#searchInvoice').val()

            },

            function(rows){

                let tbody =
                    $('#invoiceListBody');

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
                                colspan="7"
                                class="text-center text-muted py-4">

                                Tidak ada invoice outstanding

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

                rows.forEach(function(r){

                    let tr = `

                        <tr>

                            <!-- CHECK -->
                            <td class="text-center">

                                <input
                                    type="checkbox"
                                    class="pickInvoice"

                                    data-sales="${r.SALES}"

                                    data-customer="${r.CUSTOMER}"

                                    data-customer-name="${r.CUSTOMER_NAME}"

                                    data-total="${r.TOTAL}"

                                    data-paid="${r.TOTAL_PAID}"

                                    data-outstanding="${r.OUTSTANDING}"

                                    data-date="${r.SALES_DATE}">

                            </td>

                            <!-- SALES -->
                            <td>

                                <div class="fw-semibold text-primary">

                                    #${r.SALES}

                                </div>

                            </td>

                            <!-- CUSTOMER -->
                            <td>

                                ${r.CUSTOMER_NAME || '-'}

                            </td>

                            <!-- TOTAL -->
                            <td class="text-end">

                                Rp
                                ${formatRupiah(Number(r.TOTAL))}

                            </td>

                            <!-- PAID -->
                            <td class="text-end">

                                Rp
                                ${formatRupiah(Number(r.TOTAL_PAID))}

                            </td>

                            <!-- OUTSTANDING -->
                            <td class="text-end">

                                <div class="fw-bold text-danger">

                                    Rp
                                    ${formatRupiah(Number(r.OUTSTANDING))}

                                </div>

                            </td>

                            <!-- DATE -->
                            <td class="text-center">

                                ${formatDate(r.SALES_DATE)}

                            </td>

                        </tr>

                    `;

                    tbody.append(tr);

                });

            },

            'json'
        );
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
            .prop('readonly', true)
            .css('background', '#efefef');

        /*
        |--------------------------------------------------------------------------
        | AUTO TOTAL FROM DETAIL
        |--------------------------------------------------------------------------
        */

        updateSummary();
    }

    $(document).on(
        'change',
        'input[name="MODE_CASH_IN"]',
        function(){

            toggleCashInMode();

        }
    );

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

    $('#btnChooseInvoice').click(function(){

        $('.pickInvoice:checked').each(function(){

            let sales =
                $(this).data('sales');

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE
            |--------------------------------------------------------------------------
            */

            let exists = false;

            $('#detailTable tbody tr').each(function(){

                let currentSales =
                    $(this)
                        .find('[name="DETAIL[][SALES]"]')
                        .val();

                if(currentSales === sales){

                    exists = true;

                }

            });

            if(exists){

                return;

            }

            /*
            |--------------------------------------------------------------------------
            | ADD ROW
            |--------------------------------------------------------------------------
            */

            addDetailRow({

                SALES :
                    $(this).data('sales'),

                CUSTOMER_NAME :
                    $(this).data('customer-name'),

                OUTSTANDING :
                    $(this).data('outstanding'),

                BAYAR :
                    $(this).data('outstanding')

            });

        });

        /*
        |--------------------------------------------------------------------------
        | CLOSE
        |--------------------------------------------------------------------------
        */

        $('#modalPickInvoice')
            .modal('hide');

    });

    $(document).on(
        'change',
        '[name="MODE_CASH_IN"]',
        function(){

            let mode = $(this).val();

            /*
            |--------------------------------------------------------------------------
            | FIFO
            |--------------------------------------------------------------------------
            */

            if(mode === 'FIFO'){

                $('#btnPickInvoice')
                    .hide();

                runFIFOAllocation();

            }

            else{

                $('#btnPickInvoice')
                    .show();

                renderEmptyDetail(
                    'Silahkan pilih invoice'
                );

                updateSummary();

            }

        }
    );

    $(document).on(
        'input',
        '#cashInAmount',
        function(){

            let value =
                cleanNumber($(this).val());

            $(this).val(
                formatRupiah(value)
            );

            updateSummary();

            let mode =
                $('input[name="MODE_CASH_IN"]:checked')
                    .val();

            if(mode === 'FIFO'){

                runFIFOAllocation();

            }

        }
    );

    $(document).on(
        'change',
        'input[name="MODE_CASH_IN"]',
        function(){

            let mode = $(this).val();

            /*
            |--------------------------------------------------------------------------
            | CLEAR
            |--------------------------------------------------------------------------
            */

            $('#detailTable tbody')
                .html('');

            /*
            |--------------------------------------------------------------------------
            | FIFO
            |--------------------------------------------------------------------------
            */

            if(mode === 'FIFO'){

                $('#btnPickInvoice')
                    .hide();

                runFIFOAllocation();

            }

            /*
            |--------------------------------------------------------------------------
            | MANUAL
            |--------------------------------------------------------------------------
            */

            else{

                $('#btnPickInvoice')
                    .show();

                renderEmptyDetail(
                    'Silahkan pilih invoice'
                );

            }

            updateSummary();

        }
    );

    function runFIFOAllocation()
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        */

        let plant =
            $('#plantAdd').val();

        let customer =
            $('#hiddenCustomerAdd').val();

        let totalInput =
            cleanNumber(
                $('#cashInAmount').val()
            );

        if(!customer){

            renderEmptyDetail(
                'Pilih customer terlebih dahulu'
            );

            return;
        }

        if(totalInput <= 0){

            renderEmptyDetail(
                'Input total cash in terlebih dahulu'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | RESET TABLE
        |--------------------------------------------------------------------------
        */

        $('#detailTable tbody')
            .html('');

        /*
        |--------------------------------------------------------------------------
        | GET FIFO DATA
        |--------------------------------------------------------------------------
        */

        $.get(

            '<?= base_url("cashin/load_sales_picker"); ?>',

            {

                plant   : plant,

                customer: customer

            },

            function(rows){

                /*
                |--------------------------------------------------------------------------
                | EMPTY
                |--------------------------------------------------------------------------
                */

                if(rows.length === 0){

                    renderEmptyDetail(
                        'Customer tidak memiliki invoice outstanding'
                    );

                    updateSummary();

                    return;
                }

                let remainingCash =
                    totalInput;

                /*
                |--------------------------------------------------------------------------
                | LOOP INVOICE
                |--------------------------------------------------------------------------
                */

                rows.forEach(function(r){

                    /*
                    |--------------------------------------------------------------------------
                    | STOP
                    |--------------------------------------------------------------------------
                    */

                    if(remainingCash <= 0){

                        return;

                    }

                    let outstanding =
                        parseFloat(
                            r.OUTSTANDING || 0
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | ALLOCATE
                    |--------------------------------------------------------------------------
                    */

                    let bayar = 0;

                    if(
                        remainingCash >= outstanding
                    ){

                        bayar = outstanding;

                    }else{

                        bayar = remainingCash;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | PUSH ROW
                    |--------------------------------------------------------------------------
                    */

                    addDetailRow({

                        SALES :
                            r.SALES,

                        CUSTOMER_NAME :
                            r.CUSTOMER_NAME,

                        OUTSTANDING :
                            outstanding,

                        BAYAR :
                            bayar

                    }, true);

                    /*
                    |--------------------------------------------------------------------------
                    | REDUCE
                    |--------------------------------------------------------------------------
                    */

                    remainingCash -= bayar;

                });

                /*
                |--------------------------------------------------------------------------
                | UPDATE SUMMARY
                |--------------------------------------------------------------------------
                */

                updateSummary();

            },

            'json'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER CHANGE
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'select2:select',
        '#customerAdd',
        function(e){

            let customerId =
                e.params.data.id;

            /*
            |--------------------------------------------------------------------------
            | SET CUSTOMER
            |--------------------------------------------------------------------------
            */

            $('#hiddenCustomerAdd')
                .val(customerId);

            /*
            |--------------------------------------------------------------------------
            | RESET CASH IN
            |--------------------------------------------------------------------------
            */

            $('#cashInAmount')
                .val('');

            /*
            |--------------------------------------------------------------------------
            | RESET DETAIL
            |--------------------------------------------------------------------------
            */

            $('#detailTable tbody')
                .html('');

            /*
            |--------------------------------------------------------------------------
            | RESET SUMMARY
            |--------------------------------------------------------------------------
            */

            updateSummary();

            /*
            |--------------------------------------------------------------------------
            | MODE
            |--------------------------------------------------------------------------
            */

            let mode =
                $('[name="MODE_CASH_IN"]:checked')
                    .val();

            
            if(mode === 'FIFO'){

                setTimeout(function(){

                    runFIFOAllocation();

                }, 100);

            }

        }
    );

    $('#customerAdd').on(
        'change',
        function(){

            let customer =
                $(this).val();

            $('#hiddenCustomerAdd')
                .val(customer);

            /*
            |--------------------------------------------------------------------------
            | RESET TABLE
            |--------------------------------------------------------------------------
            */

            $('#detailTable tbody')
                .html('');

            updateSummary();

            /*
            |--------------------------------------------------------------------------
            | NO CUSTOMER
            |--------------------------------------------------------------------------
            */

            if(!customer){

                renderEmptyDetail(
                    'Pilih customer terlebih dahulu'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | MODE
            |--------------------------------------------------------------------------
            */

            let mode =
                $('input[name="MODE_CASH_IN"]:checked')
                    .val();

            /*
            |--------------------------------------------------------------------------
            | FIFO
            |--------------------------------------------------------------------------
            */

            if(mode === 'FIFO'){

                runFIFOAllocation();

            }

            /*
            |--------------------------------------------------------------------------
            | MANUAL
            |--------------------------------------------------------------------------
            */

            else{

                renderEmptyDetail(
                    'Silahkan pilih invoice'
                );

            }

        }
    );

    $('#plantAdd').change(function(){

        let mode =
            $('[name="MODE_CASH_IN"]:checked')
                .val();

        if(mode === 'FIFO'){

            setTimeout(function(){

                runFIFOAllocation();

            }, 100);

        }

    });

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

            '<?= base_url("cashin/load_data"); ?>',

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

            /*
            |--------------------------------------------------------------------------
            | STATUS BADGE
            |--------------------------------------------------------------------------
            */

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

            let actionBtn = `

                <div class="btn-group btn-group-sm">

                    <!-- PDF -->
                    <button
                        class="btn btn-outline-primary btnPdf"
                        data-cashin="${row.CASH_IN}"
                        data-plant="${row.PLANT}">

                        Slip

                    </button>

                </div>

            `;

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

                                        Locked

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

        initPlantSelect();

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

            dropdownParent: modalId
                ? $(modalId)
                : $(document.body),

            width: "100%",

            ajax: {

                url: "<?= base_url('cashin/get_customer'); ?>",

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

    let CURRENT_ALLOCATED = 0;

    $(function(){
        loadPage(1);

        $('#fCashInAdd').submit(function(e){

            e.preventDefault();

            /*
            |--------------------------------------------------------------------------
            | VALIDASI
            |--------------------------------------------------------------------------
            */

            let customer =
                $('#hiddenCustomerAdd').val();

            if(!customer){

                alert(
                    'Customer wajib dipilih'
                );

                return;
            }

            let mode =
                $('input[name="MODE_CASH_IN"]:checked').val();

            /*
            |--------------------------------------------------------------------------
            | TOTAL INPUT
            |--------------------------------------------------------------------------
            */

            let totalInput = 0;

            /*
            |--------------------------------------------------------------------------
            | FIFO
            |--------------------------------------------------------------------------
            */

            if(mode === 'FIFO'){

                totalInput =
                    cleanNumber(
                        $('#cashInAmount').val()
                    );

                if(totalInput <= 0){

                    alert(
                        'Total cash in wajib diisi'
                    );

                    return;
                }

            }

            /*
            |--------------------------------------------------------------------------
            | MANUAL
            |--------------------------------------------------------------------------
            */

            else{

                totalInput = CURRENT_ALLOCATED;

                if(totalInput <= 0){

                    alert(
                        'Total invoice belum ada'
                    );

                    return;
                }

            }

            let detailCount =
                $('#detailTable tbody tr')
                    .not('.empty-row')
                    .length;

            if(detailCount <= 0){

                alert(
                    'Detail invoice kosong'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI BAYAR
            |--------------------------------------------------------------------------
            */

            let invalid = false;

            $('.pay-input').each(function(){

                let val =
                    cleanNumber($(this).val());

                if(val <= 0){

                    invalid = true;

                }

            });

            if(invalid){

                alert(
                    'Nominal bayar tidak valid'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SAVE
            |--------------------------------------------------------------------------
            */

            $('#btnSaveCashIn')
                .prop('disabled', true)
                .text('Saving...');

            $.post(

                '<?= base_url("cashin/create"); ?>',

                $(this).serialize(),

                function(res){

                    if(typeof res === 'string'){

                        res = JSON.parse(res);

                    }

                    alert(res.message);

                    if(res.status){

                        /*
                        |--------------------------------------------------------------------------
                        | RESET
                        |--------------------------------------------------------------------------
                        */

                        $('#CashInAdd')
                            .modal('hide');

                        $('#fCashInAdd')[0]
                            .reset();

                        $('#detailTable tbody')
                            .html('');

                        renderEmptyDetail(
                            'Pilih customer terlebih dahulu'
                        );

                        $('#customerAdd')
                            .val(null)
                            .trigger('change');

                        updateSummary();

                        loadPage(state.page);

                    }

                },

                'json'

            ).always(function(){

                $('#btnSaveCashIn')
                    .prop('disabled', false)
                    .text('Simpan');

            });

        });

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

