<div class="container-fluid">

    <div class="card w-100">

        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                PAYMENT - ACCOUNTING
            </h5>

            <!-- FILTER -->
            <div class="row g-2 mb-3">

                <!-- SEARCH -->
                <div class="col-md-3">

                    <input
                        id="search"
                        type="text"
                        class="form-control"
                        placeholder="Cari payment, slip, supplier...">

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
                        data-bs-target="#paymentAdd">

                        <i class="ti ti-plus"></i>

                        Tambah Payment

                    </button>

                </div>

            </div>

            <div class="table-box position-relative">

                <!-- LOADING -->
                <div id="tableLoading"
                    class="table-loading d-none">

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

                <!-- TABLE -->
                <div id="tableWrapper">

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle table-modern"
                            id="mainTable">

                            <thead>

                                <tr>

                                    <th class="text-center">
                                        Plant
                                    </th>

                                    <th class="text-center">
                                        Payment
                                    </th>

                                    <th class="text-center">
                                        Date
                                    </th>

                                    <th class="text-center">
                                        Supplier
                                    </th>

                                    <th class="text-center">
                                        Payment
                                    </th>

                                    <th class="text-center">
                                        Slip
                                    </th>

                                    <th class="text-center">
                                        Item
                                    </th>

                                    <th class="text-center">
                                        Total
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
.table-modern td,
    .table-modern th{
        white-space: nowrap;
        vertical-align: middle;
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

    .select2-container--open {
        z-index: 999999 !important;
    }

    .select2-dropdown {
        position: absolute !important;
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
                            PO
                        </h5>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="addRow">Pilih PO</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="detailTable">
                            <thead>
                                <tr>
                                    <th style="width:15%">PO No</th>
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

                <!-- HEADER -->
                <div class="modal-header">

                    <h5 class="modal-title">
                        PAYMENT - EDIT
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- HIDDEN -->
                    <input
                        type="hidden"
                        name="PAYMENT">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">

                        <!-- PLANT -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Plant *
                            </label>

                            <select
                                name="PLANT"
                                id="plantEdit"
                                class="form-control"
                                required>

                            </select>

                        </div>

                        <!-- PAYMENT -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                No. Payment
                            </label>

                            <input
                                id="paymentNoEdit"
                                class="form-control"
                                readonly
                                style="background:#efefef">

                        </div>

                        <!-- DATE -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Tanggal *
                            </label>

                            <input
                                name="PAYMENT_DATE"
                                type="date"
                                class="form-control"
                                required>

                        </div>

                        <!-- PEMBAYARAN -->
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
                                        value="CASH">

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

                        <!-- SLIP -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                No. Slip
                            </label>

                            <input
                                name="SLIP_NO"
                                class="form-control"
                                readonly
                                style="background:#efefef">

                        </div>

                        <!-- SUPPLIER -->
                        <div class="col-md-6 flex-inline">

                            <label class="form-label">
                                Supplier
                            </label>

                            <select
                                id="supplierEdit"
                                class="form-control">

                            </select>

                            <input
                                type="hidden"
                                name="SUPPLIER"
                                id="hiddensupplierEdit">

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

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h5 style="margin-bottom:0px">

                            PO

                        </h5>

                        <div>

                            <button
                                type="button"
                                class="btn btn-success btn-sm"
                                id="addRowEdit">

                                Pilih PO

                            </button>

                        </div>

                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive">

                        <table
                            class="table table-bordered"
                            id="detailTableEdit">

                            <thead>

                                <tr>

                                    <th style="width:15%">
                                        PO No
                                    </th>

                                    <th style="width:20%">
                                        Material
                                    </th>

                                    <th style="text-align:center">
                                        Berat
                                    </th>

                                    <th style="text-align:center">
                                        Jumlah ( QTY )
                                    </th>

                                    <th style="text-align:center">
                                        Harga
                                    </th>

                                    <th style="text-align:center">
                                        Total
                                    </th>

                                    <th>
                                        Remark
                                    </th>

                                    <th style="width:5%">
                                        #
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                            </tbody>

                        </table>

                        <!-- GRAND TOTAL -->
                        <div class="row" style="width:100%;">

                            <div class="col-md-6"></div>

                            <div class="col-md-6">

                                <div class="input-group">

                                    <span class="input-group-text fw-bold">

                                        Grand Total

                                    </span>

                                    <input
                                        type="text"
                                        id="grandTotalEdit"
                                        class="form-control text-end fw-bold"
                                        readonly
                                        style="background:#efefef">

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
                        class="btn btn-primary"
                        type="submit">

                        Simpan Perubahan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<style>
    #modalPO {
        background: #000000c7;
    }
</style>

<div class="modal fade"
    id="modalPickPO"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    PILIH PO
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
                            id="searchPO"
                            class="form-control"
                            placeholder="Cari po / material / supplier...">

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
                                    PO
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

                        <tbody id="poListBody"></tbody>

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
                    id="btnChoosePO">

                    Pilih Data

                </button>

            </div>

        </div>

    </div>

</div>

<script>
    let state = {
        page  : 1,
        limit : 10,
        order : 'PAYMENT_DATE',
        dir   : 'DESC'
    };
    let detailIndex = 0;

    let searchTimer;

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

    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    $('#btnResetFilter').click(function(){

        $('#search').val('');

        $('#filterPembayaran').val('');

        $('#dateFrom')
            .val('<?= date('Y-m-01'); ?>');

        $('#dateTo')
            .val('<?= date('Y-m-d'); ?>');

        loadPage(1);

    });

    function formatDate(dateString){
        if(!dateString) return '-';
        const d = new Date(dateString);
        if(isNaN(d)) return dateString;

        const day = String(d.getDate()).padStart(2,'0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    let ajaxListRequest = null;

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

    function loadPage(page = 1)
    {
        showTableLoading();

        state.page = page;

        /*
        |--------------------------------------------------------------------------
        | ABORT PREVIOUS
        |--------------------------------------------------------------------------
        */

        if (ajaxListRequest) {

            ajaxListRequest.abort();

        }

        ajaxListRequest = $.get(

            '<?= base_url("payment/load_data"); ?>',

            {

                page : state.page,

                limit : state.limit,

                search : $('#search').val(),

                pembayaran : $('#filterPembayaran').val(),

                date_from : $('#dateFrom').val(),

                date_to : $('#dateTo').val(),

                order : state.order,

                dir : state.dir

            },

            function(resp){

                ajaxListRequest = null;

                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                let tbody = $('#table-body');

                tbody.empty();

                /*
                |--------------------------------------------------------------------------
                | EMPTY
                |--------------------------------------------------------------------------
                */

                if(resp.rows.length === 0){

                    tbody.html(`

                        <tr>

                            <td colspan="10"
                                class="text-center text-muted py-4">

                                Tidak ada data

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

                resp.rows.forEach(function(row){

                    let tr = `

                        <tr>

                            <!-- PLANT -->
                            <td class="text-center align-middle">

                                <div class="fw-semibold">

                                    ${row.PLANT_NAME || '-'}

                                </div>

                            </td>

                            <!-- PAYMENT -->
                            <td class="text-center align-middle">

                                <div class="fw-bold text-primary">

                                    #${row.PAYMENT}

                                </div>

                            </td>

                            <!-- DATE -->
                            <td class="text-center align-middle">

                                ${formatDate(row.PAYMENT_DATE)}

                            </td>

                            <!-- SUPPLIER -->
                            <td class="align-middle">

                                <div class="fw-semibold">

                                    ${row.SUPPLIER_NAME || '-'}

                                </div>

                            </td>

                            <!-- PAYMENT TYPE -->
                            <td class="text-center align-middle">

                                <span class="
                                    badge
                                    ${row.PEMBAYARAN === 'CASH'
                                        ? 'bg-primary'
                                        : 'bg-success'}
                                ">

                                    ${row.PEMBAYARAN || '-'}

                                </span>

                            </td>

                            <!-- SLIP -->
                            <td class="text-center align-middle">

                                ${row.SLIP_NO || '-'}

                            </td>

                            <!-- ITEM -->
                            <td class="text-center align-middle">

                                <div>

                                    <span class="badge bg-primary">

                                        ${row.TOTAL_ITEM || 0}
                                        Item

                                    </span>

                                </div>

                                <div class="mt-1">

                                    <small class="text-muted">

                                        Berat :
                                        ${numberFormat(row.TOTAL_BERAT || 0)}

                                    </small>

                                </div>

                            </td>

                            <!-- TOTAL -->
                            <td class="text-end align-middle">

                                <div class="fw-bold text-success">

                                    Rp
                                    ${numberFormat(row.GRAND_TOTAL || 0)}

                                </div>

                            </td>

                            <!-- REMARK -->
                            <td class="align-middle">

                                ${row.REMARK || '-'}

                            </td>

                            <!-- ACTION -->
                            <td class="text-center align-middle">

                                <div class="btn-group btn-group-sm">

                                    <!-- PDF -->
                                    <button
                                        class="btn btn-outline-primary btnPdf"
                                        data-payment="${row.PAYMENT}"
                                        data-plant="${row.PLANT}">

                                        Slip

                                    </button>

                                    <!-- EDIT -->
                                    <button
                                        class="btn btn-outline-warning btnEdit"
                                        data-payment="${row.PAYMENT}"
                                        data-plant="${row.PLANT}">

                                        Edit

                                    </button>

                                    <!-- DELETE -->
                                    <button
                                        class="btn btn-outline-danger btnDelete"
                                        data-payment="${row.PAYMENT}"
                                        data-plant="${row.PLANT}">

                                        Hapus

                                    </button>

                                </div>

                            </td>

                        </tr>

                    `;

                    tbody.append(tr);

                });

                /*
                |--------------------------------------------------------------------------
                | PAGINATION
                |--------------------------------------------------------------------------
                */

                $('#pagination')
                    .html(resp.pagination);

                /*
                |--------------------------------------------------------------------------
                | INFO
                |--------------------------------------------------------------------------
                */

                $('#info').text(

                    `Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`

                );

            },

            'json'

        ).always(function(){

            hideTableLoading();

        });
    }

    $(document).on(
        'click',
        '.btnPdf',
        function(){

            let payment =
                $(this).data('payment');

            let plant =
                $(this).data('plant');

            window.open(

                '<?= base_url("payment/print_pdf"); ?>'
                +
                '?payment=' + payment
                +
                '&plant=' + plant,

                '_blank'

            );

        }
    );

    $(document).on(
        'click',
        '#detailTableEdit .removeDetail',
        function(){

            $(this)
                .closest('tr')
                .remove();

            updateGrandTotalEdit();

        }
    );

    function updateGrandTotal(){

        let grandTotal = 0;

        $('#detailTable tbody tr').each(function(){

            let totalText =
                $(this)
                .find('.total-cell')
                .text()
                .replace(/[^\d]/g,'');

            grandTotal +=
                parseFloat(totalText || 0);
        });

        $('#grandTotal').val(

            currencyFormat(grandTotal)
        );
    }

    function updateGrandTotalEdit()
    {
        let grand = 0;

        $('#detailTableEdit tbody tr').each(function(){

            let total =
                parseFloat(
                    $(this)
                    .find('input[name$="[TOTAL]"]')
                    .val()
                ) || 0;

            grand += total;

        });

        $('#grandTotalEdit').val(
            'Rp ' + numberFormat(grand)
        );
    }

    $('#btnChoosePO').click(function(){

            let detailIndex = $('#detailTable tbody tr').length;

            $('.pickPO:checked').each(function(){

            let id = $(this).data('id');

            let po = $(this).data('po');

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
                    $(this).data('po') == po &&
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

            let berat =
                parseFloat($(this).data('berat')) || 0;

            let total =
                berat * harga;

            let row = `

            <tr
                data-id="${id}"
                data-po="${po}"
                data-material="${material}">

                <td>

                    ${po}

                    <input
                        type="hidden"
                        name="DETAIL[${detailIndex}][PO_NO]"
                        value="${po}">
                </td>

                <td>

                    ${$(this).data('material-name')}

                    <input
                        type="hidden"
                        name="DETAIL[${detailIndex}][MATERIAL]"
                        value="${material}">
                </td>

                <td class="text-center">

                    ${numberFormat($(this).data('berat'))}

                    <input
                        type="hidden"
                        name="DETAIL[${detailIndex}][BERAT]"
                        value="${$(this).data('berat')}">
                </td>

                <td class="text-center">

                    ${numberFormat(qty)}

                    <input
                        type="hidden"
                        name="DETAIL[${detailIndex}][JUMLAH]"
                        value="${qty}">
                </td>

                <td class="text-end">

                    ${currencyFormat(harga)}

                    <input
                        type="hidden"
                        name="DETAIL[${detailIndex}][HARGA]"
                        value="${harga}">
                </td>

                <td class="text-end total-cell">

                    ${currencyFormat(total)}

                </td>

                <td>

                    <input
                        type="text"
                        class="form-control"
                        name="DETAIL[${detailIndex}][REMARK]">
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

            detailIndex++;

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

        $('#modalPickPO')
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

        // update semua row po select
        $('#detailTable tbody tr').each(function(){
            let $poSelect = $(this).find('.po-select');
            $poSelect.empty().trigger('change');
        });
    });

    $('#supplierEdit').on('select2:select', function(e){
        let supplierId = e.params.data.id;
        $('#hiddensupplierEdit').val(supplierId);

        $('#detailTableEdit tbody tr').each(function(){
            let $select = $(this).find('.po-select');
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

        loadPOPicker();

        $('#modalPickPO')
            .modal('show');

    });

    
    let searchPOTimer; $('#searchPO').on('keyup', function(){ clearTimeout(searchPOTimer); searchPOTimer = setTimeout(() => { loadPOPicker(); }, 400); });

    function loadPOPicker() {

    $.get(

        '<?= base_url("payment/load_po_picker"); ?>',

        {
            plant :
                $('#plantAdd').val(),

            supplier :
                $('#hiddensupplierAdd').val(),

            search :
                $('#searchPO').val()
        },

        function(rows)
                {

                    let tbody = $('#poListBody');

                    tbody.html('');

                    rows.forEach(function(r){

                        let tr = `

                            <tr>

                                <td class="text-center">

                                    <input
                                        type="checkbox"
                                        class="pickPO"

                                        data-id="${r.ID}"

                                        data-po="${r.PO}"

                                        data-material="${r.MATERIAL}"

                                        data-material-name="${r.MATERIAL_NAME}"

                                        data-qty="${r.JUMLAH}"

                                        data-berat="${r.BERAT}"

                                        data-harga="${r.HARGA}"

                                        data-total="${r.TOTAL}"

                                        data-supplier="${r.SUPPLIER}"

                                        data-supplier-name="${r.SUPPLIER_NAME}"
                                    >

                                </td>

                                <td>
                                    ${r.PO}
                                </td>

                                <td>
                                    ${r.SUPPLIER_NAME || '-'}
                                </td>

                                <td>
                                    ${r.MATERIAL_NAME || '-'}
                                </td>

                                <td class="text-center">

                                    ${Number(r.JUMLAH)
                                        .toLocaleString('id-ID')}

                                </td>

                                <td class="text-center">

                                    ${Number(r.BERAT)
                                        .toLocaleString('id-ID')}

                                </td>

                                <td class="text-end">

                                    Rp
                                    ${Number(r.HARGA)
                                        .toLocaleString('id-ID')}

                                </td>

                                <td class="text-end">

                                    Rp
                                    ${Number(r.TOTAL)
                                        .toLocaleString('id-ID')}

                                </td>

                            </tr>

                        `;

                        tbody.append(tr);

                    });

                },

                'json'
            );
        }

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

        loadPOModal({
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

    $(document).on('click','.btn-choose-po',function(){
        let supplier = $('#hiddensupplierAdd').val();
        if(!supplier) return alert('Pilih supplier terlebih dahulu');
        loadPOModal(supplier);
    });

    $(document).on('click', '.btn-select-po', function () {
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
            PO_NO: $btn.data('po'),
            MATERIAL: $btn.data('material'),
            MATERIAL_NAME: $btn.data('material-name'),
            BERAT: Number($btn.data('berat')) || 0,
            JUMLAH: Number($btn.data('qty')) || 0,
            HARGA: Number($btn.data('harga')) || 0
        };

        if (isMaterialExist(data.PLANT, data.PO_NO, data.MATERIAL, table)) {
            alert('Material ini sudah dipilih');
            $btn.data('locked', false);
            return;
        }

        addDetailRow(table, data);

        $('#modalPO').modal('hide');

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

        $(target).val(numberFormat(total));
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

    function isMaterialExist(plant, po, material, table){
        let exist = false;

        $(table+' tbody tr').each(function(){
            const p = $(this).find('input[name$="[PLANT]"]').val();
            const r = $(this).find('input[name$="[PO_NO]"]').val();
            const m = $(this).find('input[name$="[MATERIAL]"]').val();

            if (
                String(p) === String(plant) &&
                String(r) === String(po) &&
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

    function loadPOModal({ supplier, plant = null, targetTable }) {

        if (!supplier) {
            alert('Supplier tidak valid');
            return;
        }

        $.get(
            '<?= base_url("payment/load_po_modal"); ?>',
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
                                <td class="text-center" style="vertical-align: middle">${formatDateIndo(r.PO_DATE)}</td>
                                <td class="text-center" style="vertical-align: middle">#${r.PO}</td>
                                <td class="text-center" style="vertical-align: middle">
                                    <b>(${r.SUPPLIER})</b><br>${r.SUPPLIER_NAME}
                                </td>
                                <td class="text-center" style="vertical-align: middle">
                                    <b>(${r.MATERIAL})</b><br>${r.MATERIAL_NAME}
                                </td>
                                <td class="text-end" style="vertical-align: middle">${numberFormat(r.TOTAL_BERAT)}</td>
                                <td class="text-end" style="vertical-align: middle">${numberFormat(r.TOTAL_QTY)}</td>
                                <td class="text-end" style="vertical-align: middle">${numberFormat(r.HARGA)}</td>
                                <td class="text-end" style="vertical-align: middle">${numberFormat(r.TOTAL)}</td>
                                <td class="text-center" style="vertical-align: middle">
                                    <button type="button"
                                        class="btn btn-success btn-sm btn-select-po"
                                        data-target="${targetTable}"
                                        data-plant="${r.PLANT}"
                                        data-po="${r.PO}"
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

                $('#tablePO tbody').html(tbody);
                $('#modalPO').modal('show');
            },
            'json'
        );
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
        const total  = berat * harga;

        let row = `
            <tr>
                <td style="vertical-align: middle">
                    #${data.PO_NO}
                    <input type="hidden" name="DETAIL[${idx}][SEQ_NO]" value="${data.SEQ_NO}">
                    <input type="hidden" name="DETAIL[${idx}][PLANT]" value="${data.PLANT}">
                    <input type="hidden" name="DETAIL[${idx}][PO_NO]" value="${data.PO_NO}">
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
                        value="${numberFormat(jumlah)}" readonly>
                    <input type="hidden" name="DETAIL[${idx}][JUMLAH]" value="${jumlah}">
                </td>
                <td>
                    <input type="text"
                        class="form-control text-end harga"
                        value="${numberFormat(harga)}" readonly>
                    <input type="hidden" name="DETAIL[${idx}][HARGA]" value="${harga}">
                </td>
                <td>
                    <input type="text"
                        class="form-control text-end total"
                        value="${numberFormat(total)}" readonly>
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

    function addDetailRowEdit(row)
    {
        let qty =
            parseFloat(row.JUMLAH) || 0;

        let berat =
            parseFloat(row.BERAT) || 0;

        let harga =
            parseFloat(row.HARGA) || 0;

        // TOTAL = BERAT X HARGA
        let total =
            berat * harga;

        let index =
            $('#detailTableEdit tbody tr').length;

        let html = `

            <tr
                data-po="${row.PO_NO}"
                data-material="${row.MATERIAL}">

                <!-- PO -->
                <td>

                    ${row.PO_NO}

                    <input
                        type="hidden"
                        name="DETAIL[${index}][PO_NO]"
                        value="${row.PO_NO}">

                </td>

                <!-- MATERIAL -->
                <td>

                    ${row.MATERIAL_NAME || row.MATERIAL}

                    <input
                        type="hidden"
                        name="DETAIL[${index}][MATERIAL]"
                        value="${row.MATERIAL}">

                </td>

                <!-- BERAT -->
                <td class="text-center">

                    ${numberFormat(berat)}

                    <input
                        type="hidden"
                        name="DETAIL[${index}][BERAT]"
                        value="${berat}">

                </td>

                <!-- JUMLAH -->
                <td class="text-center">

                    ${numberFormat(qty)}

                    <input
                        type="hidden"
                        name="DETAIL[${index}][JUMLAH]"
                        value="${qty}">

                </td>

                <!-- HARGA -->
                <td class="text-end">

                    Rp ${numberFormat(harga)}

                    <input
                        type="hidden"
                        name="DETAIL[${index}][HARGA]"
                        value="${harga}">

                </td>

                <!-- TOTAL -->
                <td class="text-end">

                    Rp ${numberFormat(total)}

                    <input
                        type="hidden"
                        class="total"
                        name="DETAIL[${index}][TOTAL]"
                        value="${total}">

                </td>

                <!-- REMARK -->
                <td>

                    <input
                        type="text"
                        class="form-control"
                        name="DETAIL[${index}][REMARK]"
                        value="${row.REMARK || ''}">

                </td>

                <!-- ACTION -->
                <td class="text-center">

                    <button
                        type="button"
                        class="btn btn-danger btn-sm btn-remove">

                        ×

                    </button>

                </td>

            </tr>

        `;

        $('#detailTableEdit tbody')
            .append(html);

        calculateGrandTotal('#detailTableEdit');
    }

    function normalizeTableSelector(table) {
        if (!table) return null;
        if (typeof table === 'string') return table;
        if (table instanceof jQuery) return '#' + table.attr('id');
        if (table.nodeType === 1) return '#' + table.id;
        return null;
    }

    function numberFormat(value, digit = 0)
    {
        value = parseFloat(value || 0);

        return value.toLocaleString('id-ID', {

            minimumFractionDigits: digit,
            maximumFractionDigits: digit

        });
    }

    function currencyFormat(value)
    {
        return 'Rp ' + numberFormat(value, 0);
    }

    function initPOSelect2(selector, modal, supplier){
        selector.select2({
            placeholder: 'Pilih PO',
            dropdownParent: $(modal),
            width: '100%',
            ajax: {
                url: '<?= base_url("payment/get_po_by_supplier"); ?>',
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
                            id: r.PO,
                            text: `#${r.PO} | ${r.PLANT_NAME} | ${r.PO_DATE}`
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

        $(document).on(
            'click',
            '.btnEdit',
            function(){
                $('#fPaymentEdit')[0].reset();

                $('#detailTableEdit tbody').empty();

                $('#grandTotalEdit').val('');

                let payment =
                    $(this).data('payment');

                let plant =
                    $(this).data('plant');

                $.get(

                    '<?= base_url("payment/edit"); ?>',

                    {
                        payment : payment,
                        plant   : plant
                    },

                    function(res){

                        if(typeof res === 'string'){

                            res = JSON.parse(res);

                        }

                        if(!res.status){

                            alert(res.message);

                            return;

                        }

                        let d =
                            res.data;

                        let form =
                            $('#fPaymentEdit');

                        /*
                        |--------------------------------------------------------------------------
                        | RESET
                        |--------------------------------------------------------------------------
                        */

                        $('#detailTableEdit tbody')
                            .html('');

                        /*
                        |--------------------------------------------------------------------------
                        | HEADER
                        |--------------------------------------------------------------------------
                        */

                        form.find('[name="PAYMENT"]')
                            .val(d.header.PAYMENT);

                        $('#paymentNoEdit')
                            .val(d.header.PAYMENT);

                        let plantOption = new Option(
                            d.header.PLANT_NAME || d.header.PLANT,
                            d.header.PLANT,
                            true,
                            true
                        );

                        $('#plantEdit')
                            .append(plantOption)
                            .trigger('change');

                        form.find('[name="PAYMENT_DATE"]')
                            .val(
                                d.header.PAYMENT_DATE.substr(0,10)
                            );

                        form.find('[name="SLIP_NO"]')
                            .val(d.header.SLIP_NO);

                        form.find('[name="REMARK"]')
                            .val(d.header.REMARK);

                        form.find(
                            '[name="PEMBAYARAN"][value="'+
                            d.header.PEMBAYARAN+
                            '"]'
                        ).prop('checked', true);

                        /*
                        |--------------------------------------------------------------------------
                        | SUPPLIER
                        |--------------------------------------------------------------------------
                        */

                        let supplierOption = new Option(
                            d.header.SUPPLIER_NAME,
                            d.header.SUPPLIER,
                            true,
                            true
                        );

                        $('#supplierEdit')
                            .empty()
                            .append(supplierOption);

                        $('#hiddensupplierEdit')
                            .val(d.header.SUPPLIER);

                        /*
                        |--------------------------------------------------------------------------
                        | DETAIL
                        |--------------------------------------------------------------------------
                        */

                        (d.detail || []).forEach(function(row){

                            addDetailRowEdit({

                                PO_NO :
                                    row.PO_NO,

                                MATERIAL :
                                    row.MATERIAL,

                                MATERIAL_NAME :
                                    row.MATERIAL_NAME,

                                BERAT :
                                    row.BERAT,

                                JUMLAH :
                                    row.JUMLAH,

                                HARGA :
                                    row.HARGA,

                                REMARK :
                                    row.REMARK

                            });

                        });

                        /*
                        |--------------------------------------------------------------------------
                        | SHOW
                        |--------------------------------------------------------------------------
                        */

                        $('#paymentEdit')
                            .modal('show');

                    },

                    'json'
                );

            }
        );

        $('#fPaymentEdit').submit(function(e){

            e.preventDefault();

            let btn =
                $(this).find('button[type="submit"]');

            btn.prop('disabled', true);

            $.post(

                '<?= base_url("payment/update"); ?>',

                $(this).serialize(),

                function(res){

                    if(typeof res === 'string'){

                        res = JSON.parse(res);

                    }

                    if(res.status){

                        $('#paymentEdit')
                            .modal('hide');

                        loadPage(state.page);

                        setTimeout(function(){

                            alert(res.message);

                        }, 300);

                    } else {

                        alert(res.message);

                    }

                },

                'json'

            ).always(function(){

                btn.prop('disabled', false);

            });

        });

    $(document).on(
        'click',
        '.btnDelete',
        function(){

            if(
                !confirm(
                    'Yakin hapus payment ini ?'
                )
            ){
                return;
            }

            let payment =
                $(this).data('payment');

            let plant =
                $(this).data('plant');

            $.post(

                '<?= base_url("payment/remove"); ?>',

                {
                    payment : payment,
                    plant   : plant
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

        $('#paymentEdit').on('hidden.bs.modal', function () {

            $('body').removeClass('modal-open');

            $('.modal-backdrop').remove();

        });
</script>


