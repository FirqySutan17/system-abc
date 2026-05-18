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

    function initPlantSelect2() {
        $('#plantAdd').select2({
            theme:'bootstrap-5',

            placeholder:'-- PILIH CUSTOMER --',

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

                                    Slip

                                </button>

                                <!-- INVOICE -->
                                <button
                                    class="btn btn-outline-success exportInvoicePdf"
                                    data-sales="${row.SALES}"
                                    data-plant="${row.PLANT}">

                                    Invoice

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

            dropdownParent: $('#salesAdd .modal-body'),

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

        $(selector).on('change', function(){

            $('#hiddenCustomerAdd').val(
                $(this).val()
            );
        });
    }

    function initCustomerEditSelect2(selector)
    {

        $(selector).select2({

            theme:'bootstrap-5',

            placeholder:'-- PILIH CUSTOMER --',

            dropdownParent: $('#salesEdit .modal-body'),

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

        /*
        |--------------------------------------------------------------------------
        | CHANGE
        |--------------------------------------------------------------------------
        */

        $(selector).on('change', function(){

            $('#hiddenCustomerEdit').val(
                $(this).val()
            );

        });
    }

    $('#customerAdd').on('change', function () {
        $('#hiddenCustomerAdd').val(
            $(this).val()
        );
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

    function initItemSelect2(el, parentModal){
        $(el).select2({
            theme:'bootstrap-5',

            placeholder:'-- PILIH CUSTOMER --',

            dropdownParent: $('#salesAdd .modal-body'),

            width:'100%',
            ajax:{
                url:'<?= base_url("sales/get_material"); ?>',
                dataType:'json',
                delay:400,
                cache:true,
                data: p => ({ q: p.term }),
                processResults: d => ({ results: d })
            }
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
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control jumlah text-end"
                        value="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control berat text-end"
                        value="0">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control harga text-end"
                        value="0">
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

        tr.find('.material-select').select2({

            theme:'bootstrap-5',

            width:'100%',

            dropdownParent: $('#salesAdd .modal-body'),

            placeholder:'-- PILIH MATERIAL --',

            minimumInputLength:2,

            ajax:{

                url:'<?= base_url("sales/get_material"); ?>',

                dataType:'json',

                delay:300,

                data:params => ({
                    q:params.term
                }),

                processResults:data => ({
                    results:data
                })
            }
        });

        recalcGrandTotal();
    }

    function addDetailRowEdit(data = {})
    {
        let tbody = $('#salesDetailTableEdit tbody');

        let html = `
            <tr>

                <td>
                    <select class="form-select material-select"></select>
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control jumlah text-end"
                        value="${formatDecimalID(data.jumlah || 0)}">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control berat text-end"
                        value="${formatDecimalID(data.berat || 0)}">
                </td>

                <td>
                    <input
                        type="text"
                        class="form-control harga text-end"
                        value="${formatCurrencyID(data.harga || 0)}">
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

        tr.find('.material-select').select2({

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
                    q:params.term
                }),
                processResults:data => ({
                    results:data
                })
            }
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
                .trigger('change');
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
        initCustomerSelect2('#customerAdd', '#salesAdd');
        loadDefaultCustomer('#customerAdd');
        initCustomerEditSelect2('#customerEdit', '#salesEdit');

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
    });

    $(document).on('input','#BAYAR_AWAL_EDIT', function(){
        let val = parseRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });
</script>
