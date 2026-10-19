<div class="container-fluid">
    <div class="card w-100">
        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">COST - ACCOUNTING</h5>

            <!-- SEARCH + ADD -->
            <div class="row mb-3">
                <div class="row g-2 mb-3">

                    <!-- SEARCH -->
                    <div class="col-md-3">

                        <input
                            id="search"
                            type="text"
                            class="form-control"
                            placeholder="Cari cost, slip, remark...">

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
                            data-bs-target="#costAdd">

                            <i class="ti ti-plus"></i>

                            Tambah Cost

                        </button>

                    </div>

                 </div>

            </div>
            <div class="table-box position-relative">
                <div id="tableLoading" class="table-loading d-none">
                    <div class="loading-card">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-3 fw-semibold">Memuat data...</div>
                        <small class="text-muted">Mohon tunggu sebentar</small>
                    </div>
                </div>

                <div id="tableWrapper">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-modern" id="mainTable">
                            <thead>
                                <thead>
                                    <tr>

                                        <th class="text-center">
                                            Plant
                                        </th>

                                        <th class="text-center">
                                            Cost
                                        </th>

                                        <th class="text-center">
                                            Tanggal
                                        </th>

                                        <th class="text-center">
                                            Tipe Pembayaran
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

            <div class="d-flex justify-content-between mt-3">
                <div id="info"></div>
                <div id="pagination"></div>
            </div>

        </div>
    </div>
</div>

<style>
    #tableRelation {
        font-size: 13px;
        white-space: nowrap;
    }

    #tableRelation th {
        background: #f3f4f6;
        vertical-align: middle;
        text-align: center;
    }

    #tableRelation td {
        vertical-align: middle;
    }

    #tableRelation td.text-end {
        font-variant-numeric: tabular-nums;
    }

    #tableRelation .btn {
        min-width: 65px;
    }

    /* Modal Pilih PO / Sales */
    #modalPilihRelation {
        z-index: 1065 !important;
    }

    #modalPilihRelation .modal-dialog {
        width: calc(100% - 40px);
        max-width: 1600px;
        margin: 20px auto;
    }

    #modalPilihRelation .modal-content {
        height: calc(100vh - 40px);
        max-height: calc(100vh - 40px);
        border: none;
        border-radius: 14px;
        overflow: hidden;
    }

    #modalPilihRelation .modal-header {
        flex-shrink: 0;
    }

    #modalPilihRelation .modal-body {
        overflow: auto;
        min-height: 0;
    }

    #modalPilihRelation .modal-footer {
        flex-shrink: 0;
    }

    /* Backdrop modal referensi harus di atas backdrop Create */
    .modal-backdrop.relation-backdrop {
        z-index: 1060 !important;
    }

    #tableRelation th,
    #tableRelation td {
        white-space: nowrap;
        vertical-align: middle;
    }

    #btnHapusRelation {
        font-size: 12px;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }

    #btnHapusRelation:hover {
        opacity: 1;
        text-decoration: underline !important;
    }
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


<!-- =======================
     MODAL ADD COST
======================= -->
<div class="modal fade" id="costAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fcostAdd">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">COST - TAMBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant *</label>
                            <select id="plantAdd" class="form-control" required></select>
                            <input type="hidden" name="PLANT" id="PLANT_ADD">
                        </div>
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Nomor Cost</label>
                            <input class="form-control" placeholder="GGenerate Otomatis" readonly style="background:#efefef">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal Cost *</label>
                            <input name="COST_DATE" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Tipe Pembayaran *</label>
                            <div style="padding: 6px 10px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                           name="PEMBAYARAN" value="CASH" required checked>
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                           name="PEMBAYARAN" value="TRANSFER" required>
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Nomor Slip</label>
                            <input class="form-control" placeholder="Auto Generate" readonly style="background:#efefef">
                        </div>

                        <!-- REFERENSI PO / SALES (OPSIONAL) -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Referensi</label>

                            <div style="width: 100%;">
                                <input
                                    type="hidden"
                                    name="RELATION"
                                    id="RELATION_ADD"
                                    value=""
                                >

                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="relationSelectedInfo"
                                        class="form-control"
                                        value="Cost Harian"
                                        readonly
                                        style="background:#efefef;"
                                    >

                                    <button
                                        type="button"
                                        class="btn btn-outline-primary"
                                        id="btnPilihPO">
                                        Pilih PO
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        id="btnPilihSales">
                                        Pilih Sales
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-sm text-danger p-0 mt-1 d-none"
                                    id="btnHapusRelation"
                                    style="text-decoration: none;">
                                    <i class="ti ti-trash me-1"></i>
                                    Hapus referensi
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input name="REMARK" class="form-control" placeholder="Keterangan...">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom:0px">Detail Cost</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addDetailRowAdd">
                            Tambah Detail
                        </button>
                    </div>
                    <div class="table-responsive" style="max-height:45vh; overflow:auto">
                        <table class="table table-bordered" id="costDetailTableAdd">
                            <thead>
                                <tr>
                                    <th style="text-align:center;width:25%">Tipe Cost</th>
                                    <th style="text-align:center;width:10%">Kuantitas</th>
                                    <th style="text-align:center;width:15%">Biaya</th>
                                    <th style="text-align:center;width:20%">Lampiran</th>
                                    <th style="text-align:center;">Keterangan</th>
                                    <th style="text-align:center;width:40px;">#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td  style="text-align:right;">TOTAL</td>
                                    <td colspan="6">
                                        <input id="FOOTER_TOTAL_ADD"
                                            class="form-control"
                                            style="text-align:right;background:#efefef"
                                            readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- =======================
     MODAL EDIT COST
======================= -->
<div class="modal fade" id="costEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="fcostEdit">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">COST - UBAH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- HEADER -->
                    <div class="row g-2 mb-3">
                        <input type="hidden" name="OLD_COST" id="OLD_COST_EDIT">

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Plant</label>
                            <input type="text"
                                id="PLANT_NAME_EDIT"
                                class="form-control"
                                readonly
                                style="background:#efefef">
                            <input type="hidden" name="PLANT" id="PLANT_EDIT">
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Nomor Cost</label>
                            <input name="COST" id="COST_EDIT" class="form-control" readonly style="background:#efefef">
                        </div>
                        

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Tanggal Cost *</label>
                            <input name="COST_DATE" id="COST_DATE_EDIT" type="date" class="form-control" required>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label d-block">Tipe Pembayaran *</label>
                            <div style="padding:6px 10px; width: 100%">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-pay" type="radio" 
                                           name="PEMBAYARAN" value="CASH">
                                    <label class="form-check-label">CASH</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input edit-pay" type="radio" 
                                           name="PEMBAYARAN" value="TRANSFER">
                                    <label class="form-check-label">TRANSFER</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Nomor Slip</label>
                            <input name="SLIP_NO" id="SLIP_NO_EDIT" class="form-control" readonly style="background:#efefef">
                        </div>

                        <!-- REFERENSI PO / SALES (OPSIONAL) -->
                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Referensi</label>

                            <div style="width: 100%;">
                                <input
                                    type="hidden"
                                    name="RELATION"
                                    id="RELATION_EDIT"
                                    value=""
                                >

                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="relationSelectedInfoEdit"
                                        class="form-control"
                                        value="Cost Harian"
                                        readonly
                                        style="background:#efefef;"
                                    >

                                    <button
                                        type="button"
                                        class="btn btn-outline-primary"
                                        id="btnPilihPOEdit">
                                        Pilih PO
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-success"
                                        id="btnPilihSalesEdit">
                                        Pilih Sales
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-sm text-danger p-0 mt-1 d-none"
                                    id="btnHapusRelationEdit"
                                    style="text-decoration:none;">
                                    <i class="ti ti-trash me-1"></i>
                                    Hapus referensi
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 flex-inline">
                            <label class="form-label">Keterangan</label>
                            <input name="REMARK" id="REMARK_EDIT" class="form-control">
                        </div>

                    </div>

                    <!-- DETAIL -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="margin-bottom:0px">Detail Cost</h5>
                        <button type="button" class="btn btn-success btn-sm" id="addDetailRowEdit">
                            Tambah Detail
                        </button>
                    </div>
                    <div class="table-responsive" style="max-height:60vh; overflow:auto">
                        <table class="table table-bordered" id="costDetailTableEdit">
                            <thead>
                                <tr>
                                    <th style="text-align:center;width:25%">Tipe Cost</th>
                                    <th style="text-align:center;width:10%">Kuantitas</th>
                                    <th style="text-align:center;width:15%">Biaya</th>
                                    <th style="text-align:center;width:20%">Lampiran</th>
                                    <th style="text-align:center;">Keterangan</th>
                                    <th style="text-align:center;width:40px;">#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td  style="text-align:right;">TOTAL</td>
                                    <td colspan="6">
                                        <input id="FOOTER_TOTAL_EDIT"
                                            class="form-control"
                                            style="text-align:right;background:#efefef"
                                            readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- MODAL PILIH REFERENSI PO / SALES -->
<div
    class="modal fade"
    id="modalPilihRelation"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="judulModalRelation">
                    Pilih Referensi
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <input
                        type="text"
                        id="searchRelation"
                        class="form-control"
                        placeholder="Cari berdasarkan data dokumen...">
                </div>

                <div class="table-responsive">
                    <table
                        class="table table-bordered table-hover table-sm"
                        id="tableRelation">

                        <thead>
                            <tr id="headRelation"></tr>
                        </thead>

                        <tbody id="bodyRelation">
                            <tr>
                                <td class="text-center">
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

<!-- =======================
     MODAL DETAIL (VIEW ONLY)
======================= -->
<div class="modal fade" id="costDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Cost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="costDetailBody">
                Sedang memuat...
            </div>
        </div>
    </div>
</div>


<script>
    const BASE_URL = "<?= base_url(); ?>";
    var state = {

        page: 1,
        limit: 10,

        search: '',

        pembayaran: '',

        date_from: '<?= date('Y-m-01'); ?>',
        date_to: '<?= date('Y-m-d'); ?>',

        order: 'COST_DATE',
        dir: 'DESC'

    };

    /* ======================
    SEARCH
    ====================== */
    let searchTimer;
    $('#search').on('keyup', function(){
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            state.search = $(this).val();
            loadPage(1);
        }, 500); // delay 500ms setelah berhenti mengetik
    });

    $('#filterPembayaran').change(function(){
        state.pembayaran = $(this).val();
        loadPage(1);
    });

    $('#dateFrom, #dateTo').change(function(){
        state.date_from = $('#dateFrom').val();

        state.date_to = $('#dateTo').val();

        loadPage(1);
    });

    $('#btnResetFilter').click(function(){

        $('#search').val('');
        $('#filterPembayaran').val('');
        $('#dateFrom').val('<?= date('Y-m-01'); ?>');
        $('#dateTo').val('<?= date('Y-m-d'); ?>');

        state.search = '';
        state.pembayaran = '';
        state.date_from = $('#dateFrom').val();
        state.date_to = $('#dateTo').val();

        loadPage(1);

    });

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

    /* ======================
    DATE FORMAT
    ====================== */
    function formatDate(dateString){
        if(!dateString) return '-';
        const d = new Date(dateString);
        if(isNaN(d)) return dateString;

        const day = String(d.getDate()).padStart(2,'0');
        const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
        return `${day} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    function initCostSelect2($el, modalParent){
        if ($el.hasClass("select2-hidden-accessible")) {
            $el.select2('destroy');
        }
        $el.select2({
            width: '100%',
            dropdownParent: $(modalParent),
            dropdownAutoWidth: true,
            placeholder: 'Pilih Tipe Cost',
            allowClear: true,
            ajax: {
                url: '<?= base_url("mcost/get_cost"); ?>',
                dataType: 'json',
                delay: 250,
                data: function(params){
                    return {
                        q: params.term,
                        limit: 20
                    };
                },
                processResults: function(data){
                    return { results: data };
                }
            }
        });
    }

    $(document).on('select2:open', () => {
        document.querySelector('.select2-container--open .select2-search__field').focus();
    });

    function showTableLoading(){

    $('#tableLoading').removeClass('d-none');

    }

    function hideTableLoading(){

    $('#tableLoading').addClass('d-none');

    }

    /* ======================
    LOAD TABLE
    ====================== */
    let ajaxListRequest = null;

    function loadPage(page = 1){
        showTableLoading();
        state.page = page;

        if (ajaxListRequest) {
            ajaxListRequest.abort(); // batalkan request sebelumnya
        }

        ajaxListRequest = $.get(
        '<?= base_url("mcost/load_data"); ?>',
        state,
        function(resp){
            ajaxListRequest = null;

            resp = typeof resp === 'string'
                ? JSON.parse(resp)
                : resp;

            let tbody = $('#table-body').empty();

            resp.rows.forEach(function(row){

                let tr = `

                    <tr>

                        <!-- PLANT -->
                        <td class="text-center align-middle">

                            <div class="fw-semibold">

                                ${row.PLANT_NAME || '-'}

                            </div>

                        </td>

                        <!-- COST -->
                        <td class="text-center align-middle">

                            <div class="fw-bold text-primary">

                                #${row.COST}

                            </div>

                        </td>

                        <!-- DATE -->
                        <td class="text-center align-middle">

                            ${formatDate(row.COST_DATE)}

                        </td>

                        <!-- PAYMENT -->
                        <td style="text-align:center;">

                            <span class="
                                badge
                                ${row.PEMBAYARAN === 'CASH'
                                    ? 'bg-primary'
                                    : 'bg-success'}
                            ">

                                ${row.PEMBAYARAN ?? '-'}

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
                                    ${formatQty(row.TOTAL_BERAT || 0)}

                                </small>

                            </div>

                        </td>

                        <!-- TOTAL -->
                        <td class="text-end align-middle">

                            <div class="fw-bold text-success">

                                Rp
                                ${formatMoney(row.GRAND_TOTAL || 0)}

                            </div>

                        </td>

                        <!-- REMARK -->
                        <td class="align-middle">

                            ${row.REMARK || '-'}

                        </td>

                        <!-- ACTION -->
                        <td class="text-center align-middle">

                            <div class="btn-group btn-group-sm">

                                <button
                                    class="btn btn-outline-primary exportPdf"
                                    data-cost="${row.COST}"
                                    data-plant="${row.PLANT}">

                                    Slip

                                </button>

                                <button
                                    class="btn btn-outline-warning editBtn"
                                    data-cost="${row.COST}"
                                    data-plant="${row.PLANT}">

                                    Ubah

                                </button>

                                <button
                                    class="btn btn-outline-danger deleteBtn"
                                    data-cost="${row.COST}"
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

            $('#info').text(
                `Menampilkan halaman ${resp.page} dari ${Math.ceil(resp.total/state.limit)} (Total ${resp.total} data)`
            );

        },
        'json'

        ).always(function(){
            hideTableLoading();
        });

    }

    function unformatMoney(angka) {
        return angka.replace(/\./g, '');
    }

    $(document).on('keyup', '.jumlah', function () {
        let clean = cleanNumber($(this).val());
        $(this).val(formatMoney(clean));
    });

    function addDetailRow(data = {}, targetTable){
        let tbody = $(`${targetTable} tbody`);
        if (!tbody.length) return;

        let qty    = data.QTY ?? 1;
        let jumlah = data.JUMLAH ? formatMoney(data.JUMLAH) : '';
        let remark = data.REMARK ?? '';

        let modalParent = targetTable.includes('Edit')
            ? '#costEdit'
            : '#costAdd';

        // 🔥 SEQ ANTI TABRAKAN
        let seq;
        if (data.SEQ_NO) {
            seq = parseInt(data.SEQ_NO);
        } else {
            let maxSeq = 0;
            $(`${targetTable} tbody tr`).each(function(){
                let s = parseInt($(this).data('seq')) || 0;
                if (s > maxSeq) maxSeq = s;
            });
            seq = maxSeq + 1;
        }

        let row = `
            <tr data-seq="${seq}">
                <td style="vertical-align: middle">
                    <select class="form-control cost-type-select"></select>
                </td>

                <td style="vertical-align: middle">
                    <input type="number" min="1"
                        class="form-control qty text-end"
                        value="${qty}">
                </td>

                <td style="vertical-align: middle">
                    <input class="form-control jumlah text-end"
                        value="${jumlah}">
                </td>

                <td style="vertical-align: middle">
                    <div class="existing-files"></div>
                    <input type="file"
                        class="form-control cost-file"
                        multiple
                        accept=".jpg,.jpeg,.png,.pdf">
                </td>

                <td style="vertical-align: middle">
                    <input class="form-control remark"
                        value="${remark}">
                </td>

                <td class="text-center" style="vertical-align: middle">
                    <button class="btn btn-danger btn-sm removeRow">X</button>
                </td>
            </tr>
        `;

        tbody.append(row);

        let $lastRow = tbody.find('tr:last');

        /* INIT COST SELECT2 */
        let $select = $lastRow.find('.cost-type-select');
        initCostSelect2($select, modalParent);

        if (data.TIPE_COST && data.TIPE_COST_TEXT) {
            let opt = new Option(data.TIPE_COST_TEXT, data.TIPE_COST, true, true);
            $select.append(opt).trigger('change');
        }

        /* EXISTING FILES (EDIT ONLY) */
        if (Array.isArray(data.FILES)) {
            let $filesBox = $lastRow.find('.existing-files');

            data.FILES.forEach(f => {
                if (!f.url || !f.id) return;

                $filesBox.append(`
                    <div class="old-file">
                        <a href="${f.url}" target="_blank">
                            ${f.name} <i>(Klik untuk preview)</i>
                        </a>
                        <input type="hidden"
                            name="OLD_ATTACHMENT[${seq}][]"
                            value="${f.id}">
                    </div>
                `);
            });
        }

        $lastRow.find('.cost-file').val(''); // 🔥 pastikan kosong

        updateFooterTotal(
            targetTable,
            targetTable.includes('Edit')
                ? '#FOOTER_TOTAL_EDIT'
                : '#FOOTER_TOTAL_ADD'
        );
    }

    function updateFooterTotal(targetTable, footerInput){
        let total = 0;

        $(targetTable + ' tbody tr').each(function(){
            let qty    = parseInt($(this).find('.qty').val()) || 0;
            let jumlah = cleanNumber($(this).find('.jumlah').val());
            total += qty * jumlah;
        });

        $(footerInput).val(formatMoney(total.toString()));
    }


    function autoSelectSinglePlantCost(){
        $.getJSON("<?= base_url('mcost/get_plant_by_user'); ?>", function(data){
            if(data.length === 1){
                let p = data[0];

                let option = new Option(p.text, p.id, true, true);
                $('#plantAdd').append(option).trigger('change');

                $('#plantAdd').prop('disabled', true);
                $('#PLANT_ADD').val(p.id);
            }
        });
    }

    $(function(){

        loadPage(1);
        initPlantSelect2('#plantAdd', '#costAdd');
        autoSelectSinglePlantCost();

        /* ADD ROW */
        $('#addDetailRowAdd').click(function(){
            addDetailRow({}, '#costDetailTableAdd');
        });

        $('#addDetailRowEdit').click(function(){
            addDetailRow({}, '#costDetailTableEdit');
        });

        /* REMOVE ROW */
        $('#costDetailTableAdd, #costDetailTableEdit')
        .on('click','.removeRow', function(){
            let table = $(this).closest('table').attr('id');
            $(this).closest('tr').remove();

            if(table === 'costDetailTableAdd'){
                updateFooterTotal('#costDetailTableAdd', '#FOOTER_TOTAL_ADD');
            } else {
                updateFooterTotal('#costDetailTableEdit', '#FOOTER_TOTAL_EDIT');
            }
        });

        $('#costDetailTableAdd, #costDetailTableEdit').on(
            'input',
            '.qty, .jumlah',
            function(){
                let table = $(this).closest('table').attr('id');
                updateFooterTotal(
                    '#' + table,
                    table.includes('Add')
                        ? '#FOOTER_TOTAL_ADD'
                        : '#FOOTER_TOTAL_EDIT'
                );
            }
        );

        $('#costAdd').on('shown.bs.modal', function () {
            if ($('#costDetailTableAdd tbody tr').length === 0) {
                addDetailRow({}, '#costDetailTableAdd');
            }
        });

        $('#costAdd').on('hidden.bs.modal', function () {
            $('#plantAdd').val(null).trigger('change');
            $('#RELATION_ADD').val('');

            $('#relationSelectedInfo').text(
                'Tidak ada referensi — Cost Harian'
            );

            $('#btnHapusRelation').addClass('d-none');
        });

        // =====================================================
        // CREATE COST - REFERENSI PO / SALES
        // =====================================================

        let relationRows = [];
        let relationType = '';
        let relationColumns = [];

        function escapeRelationHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char];
            });
        }

        function formatRelationDate(value) {
            if (!value) return '-';

            const datePart = String(value).substring(0, 10);
            const parts = datePart.split('-');

            if (parts.length !== 3) return value;

            // Format YYYY-MM-DD menjadi DD-MM-YYYY
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }

        function formatRelationDecimal(value) {
            if (value === null || value === undefined || value === '') {
                return '0,00';
            }

            const number = Number(value);

            if (!Number.isFinite(number)) return '0,00';

            return number.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function renderRelationRows() {
            const keyword = ($('#searchRelation').val() || '')
                .toLowerCase()
                .trim();

            const $body = $('#bodyRelation');
            $body.empty();

            let filtered = relationRows.filter(function (row) {
                return Object.values(row).some(function (value) {
                    return String(value ?? '')
                        .toLowerCase()
                        .includes(keyword);
                });
            });

            if (!filtered.length) {
                $body.html(`
                    <tr>
                        <td colspan="${relationType === 'PO' ? 9 : relationColumns.length + 1}"
                            class="text-center text-muted">
                            Data tidak ditemukan
                        </td>
                    </tr>
                `);
                return;
            }

            filtered.forEach(function (row) {
                let cells = '';

                if (relationType === 'PO') {
                    cells = `
                        <td>#${escapeRelationHtml(row.PO)}</td>

                        <td class="text-center">${escapeRelationHtml(
                            formatRelationDate(row.PO_DATE)
                        )}</td>

                        <td class="text-center">${escapeRelationHtml(
                            row.PO_TYPE_NAME || row.PO_TYPE || '-'
                        )}</td>

                        <td class="text-center">${escapeRelationHtml(
                            row.SUPPLIER_NAME || row.SUPPLIER || '-'
                        )}</td>

                        <td class="text-center">${escapeRelationHtml(
                            row.MATERIAL_NAME || row.MATERIAL || '-'
                        )}</td>

                        <td class="text-center">
                            <div class="text-center">${escapeRelationHtml(row.NO_TRUCK || '-')}</div>
                            <div class="text-muted text-center small">
                                ${escapeRelationHtml(row.DRIVER || '-')}
                            </div>
                        </td>

                        <td class="text-end">
                            ${formatRelationDecimal(row.TOTAL_TERIMA_BW)}
                        </td>

                        <td class="text-end">
                            ${formatRelationDecimal(row.TOTAL_TERIMA_QTY)}
                        </td>
                    `;
                } else {
                    // Kolom khusus Sales
                    cells = `
                        <td>#${escapeRelationHtml(row.SALES || '-')}</td>

                        <td class="text-center">${escapeRelationHtml(
                            formatRelationDate(row.SALES_DATE)
                        )}</td>

                        <td class="text-center">${escapeRelationHtml(
                            row.CUSTOMER_NAME || row.CUSTOMER || '-'
                        )}</td>

                        <td class="text-center">${escapeRelationHtml(row.NOTA || '-')}</td>
                    `;
                }

                const originalIndex = relationRows.indexOf(row);

                $body.append(`
                    <tr>
                        ${cells}

                        <td class="text-center">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm btnSelectRelation"
                                data-index="${originalIndex}">
                                Pilih
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        $('#modalPilihRelation')
        .off('shown.bs.modal.relationBackdrop')
        .on('shown.bs.modal.relationBackdrop', function () {
            const $backdrops = $('.modal-backdrop');

            // Backdrop terakhir adalah milik modal referensi.
            $backdrops.last().addClass('relation-backdrop');
        });

        function openRelationModal(type) {
            relationType = type;

            const title = type === 'PO'
                ? 'Pilih Purchase Order (PO)'
                : 'Pilih Sales';

            const url = type === 'PO'
                ? '<?= base_url("mcost/get_po_reference"); ?>'
                : '<?= base_url("mcost/get_sales_reference"); ?>';

            $('#judulModalRelation').text(title);
            $('#searchRelation').val('');
            $('#headRelation').empty();

            $('#bodyRelation').html(`
                <tr>
                    <td class="text-center">Memuat data...</td>
                </tr>
            `);

            const modalElement = document.getElementById('modalPilihRelation');

            const modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            modal.show();

            $.getJSON(url)
                .done(function (resp) {
                    if (!resp || resp.status !== true) {
                        $('#bodyRelation').html(`
                            <tr>
                                <td class="text-center text-danger">
                                    Gagal memuat data
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    relationRows = Array.isArray(resp.rows)
                        ? resp.rows
                        : [];

                    if (!relationRows.length) {
                        relationColumns = [];

                        const colspan = type === 'PO' ? 9 : 5;

                        $('#bodyRelation').html(`
                            <tr>
                                <td colspan="${colspan}"
                                    class="text-center text-muted">
                                    Belum ada data ${escapeRelationHtml(type)}
                                </td>
                            </tr>
                        `);
                        return;
                    }

                    // =========================================
                    // HEADER TABEL PO
                    // =========================================
                    if (relationType === 'PO') {

                        relationColumns = [
                            'PO',
                            'PO_DATE',
                            'PO_TYPE_NAME',
                            'SUPPLIER_NAME',
                            'MATERIAL_NAME',
                            'NO_TRUCK',
                            'TOTAL_TERIMA_BW',
                            'TOTAL_TERIMA_QTY'
                        ];

                        const headers = [
                            'PO',
                            'PO Date',
                            'PO Type',
                            'Supplier',
                            'Material',
                            'Truck / Driver',
                            'Total Terima BW',
                            'Total Terima Qty',
                            'Aksi'
                        ];

                        headers.forEach(function (header) {
                            $('#headRelation').append(`
                                <th>${escapeRelationHtml(header)}</th>
                            `);
                        });

                    } else {
                        // Header khusus modal Sales
                        relationColumns = [
                            'SALES',
                            'SALES_DATE',
                            'CUSTOMER_NAME',
                            'NOTA'
                        ];

                        const headers = [
                            'Sales',
                            'Sales Date',
                            'Customer',
                            'Nota',
                            'Aksi'
                        ];

                        headers.forEach(function (header) {
                            $('#headRelation').append(`
                                <th>${escapeRelationHtml(header)}</th>
                            `);
                        });
                    }

                    // Render data menggunakan fungsi existing.
                    renderRelationRows();
                })
                .fail(function () {
                    $('#bodyRelation').html(`
                        <tr>
                            <td class="text-center text-danger">
                                Terjadi kesalahan saat mengambil data
                            </td>
                        </tr>
                    `);
                });
        }

        // Buka modal pilihan
        $('#btnPilihPO').on('click', function () {
            relationTarget = 'ADD';
            openRelationModal('PO');
        });

        $('#btnPilihSales').on('click', function () {
            relationTarget = 'ADD';
            openRelationModal('SALES');
        });

        // Pencarian di tabel modal
        $('#searchRelation').on('input', function () {
            renderRelationRows();
        });

        // Simpan pilihan ke hidden input form Create
        
        /* ================= PILIH PO / SALES ================= */
        $(document).on('click', '.btnSelectRelation', function () {
            const index = parseInt($(this).attr('data-index'), 10);
            const row = relationRows[index];

            if (!row) {
                alert('Data referensi tidak ditemukan');
                return;
            }

            const documentNo = relationType === 'PO'
                ? row.PO
                : row.SALES;

            if (!documentNo) {
                alert('Nomor dokumen referensi tidak ditemukan');
                return;
            }

            const relationValue = relationType + '|' + documentNo;
            const relationLabel = relationType + ' - ' + documentNo;

            // Tentukan form tujuan berdasarkan tombol yang membuka modal.
            if (relationTarget === 'EDIT') {
                $('#RELATION_EDIT').val(relationValue);
                $('#relationSelectedInfoEdit').val(relationLabel);
                $('#btnHapusRelationEdit').removeClass('d-none');
            } else {
                $('#RELATION_ADD').val(relationValue);
                $('#relationSelectedInfo').val(relationLabel);
                $('#btnHapusRelation').removeClass('d-none');
            }

            // Tutup modal pemilihan.
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('modalPilihRelation')
            ).hide();
        });

        // Hapus pilihan sehingga menjadi Cost Harian
        $('#btnHapusRelation').on('click', function () {

            // Kosongkan nilai RELATION yang dikirim ke backend
            $('#RELATION_ADD').val('');

            // Kembalikan tampilan field ke Cost Harian
            $('#relationSelectedInfo')
                .val('Cost Harian')
                .trigger('change');

            // Sembunyikan tombol hapus
            $(this).addClass('d-none');

        });

        let relationTarget = 'ADD';

        /* ================= REFERENSI PO / SALES EDIT ================= */

        // Gunakan modal referensi yang sama.
        // Tandai bahwa pilihan berasal dari form Edit.
        $('#btnPilihPOEdit').on('click', function () {
            relationTarget = 'EDIT';
            openRelationModal('PO');
        });

        $('#btnPilihSalesEdit').on('click', function () {
            relationTarget = 'EDIT';
            openRelationModal('SALES');
        });

        // Hapus referensi Edit
        $('#btnHapusRelationEdit').on('click', function () {
            $('#RELATION_EDIT').val('');
            $('#relationSelectedInfoEdit').val('Cost Harian');
            $(this).addClass('d-none');
        });


        /* ======================
        SUBMIT ADD
        ====================== */
        $('#fcostAdd').submit(function(e){
            e.preventDefault();

            let $btn = $(this).find('button[type="submit"]');
            if ($btn.prop('disabled')) return;

            let hasError = false;
            lockSubmit($btn);

            let formData = new FormData(this);
            let seq = 1;

            $('#costDetailTableAdd tbody tr').each(function(){

                let tipe = $(this).find('.cost-type-select').val();
                let qty  = parseInt($(this).find('.qty').val()) || 0;
                let jml  = cleanNumber($(this).find('.jumlah').val());

                if (!tipe) {
                    alert('Tipe cost wajib dipilih');
                    hasError = true;
                    return false;
                }

                if (qty <= 0 || jml <= 0) {
                    alert('Qty dan Jumlah harus > 0');
                    hasError = true;
                    return false;
                }

                // ================= DETAIL =================
                formData.append(`DETAIL[${seq}][SEQ_NO]`, seq);
                formData.append(`DETAIL[${seq}][TIPE_COST]`, tipe);
                formData.append(`DETAIL[${seq}][QTY]`, qty);
                formData.append(`DETAIL[${seq}][JUMLAH]`, jml);
                formData.append(
                    `DETAIL[${seq}][REMARK]`,
                    $(this).find('.remark').val()
                );

                let files = $(this).find('.cost-file')[0].files;
                for (let i = 0; i < files.length; i++) {
                    formData.append(`ATTACHMENT[${seq}][]`, files[i]);
                }

                seq++;
            });

            if (hasError) {
                unlockSubmit($btn);
                return; // 🔥 WAJIB
            }

            formData.append('PLANT', $('#PLANT_ADD').val());

            $.ajax({
                url: '<?= base_url("mcost/create"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp){
                    alert(resp.message);
                    if(resp.status){
                        $('#costAdd').modal('hide');
                        $('#fcostAdd')[0].reset();
                        $('#costDetailTableAdd tbody').empty();
                        $('#plantAdd').val(null).trigger('change');
                        $('#FOOTER_TOTAL_ADD').val('');
                        loadPage(1);
                    }
                },
                error: function(){
                    alert('Terjadi kesalahan server');
                },
                complete: function(){  // 🔥 TAMBAH
                    unlockSubmit($btn);
                }
            });
        });

        /* ======================
        CLICK EDIT
        ====================== */
        $(document).on('click','.editBtn',function(){
            let cost  = $(this).data('cost');
            let plant = $(this).data('plant');

            $.get('<?= base_url("mcost/edit"); ?>',{
                cost  : cost,
                plant : plant   // 🔥 kirim plant asal data
            },function(resp){
                resp = typeof resp==='string'?JSON.parse(resp):resp;

                if(resp.status){
                    let h = resp.header;
                    let d = resp.detail;

                    $('#COST_EDIT').val(h.COST);
                    $('#OLD_COST_EDIT').val(h.COST);

                    // 🔥 SIMPAN PLANT ASLI
                    $('#PLANT_EDIT').val(h.PLANT);
                    $('#PLANT_NAME_EDIT').val(h.PLANT_NAME);

                    $('#SLIP_NO_EDIT').val(h.SLIP_NO);
                    $('#COST_DATE_EDIT').val(h.COST_DATE?.substr(0,10));
                    $('#REMARK_EDIT').val(h.REMARK);

                    /* ================= REFERENSI EDIT ================= */
                    const relationEdit = h.RELATION || '';

                    $('#RELATION_EDIT').val(relationEdit);

                    if (relationEdit) {
                        const separatorIndex = relationEdit.indexOf('|');

                        const relationTypeEdit = separatorIndex >= 0
                            ? relationEdit.substring(0, separatorIndex)
                            : '';

                        const relationNoEdit = separatorIndex >= 0
                            ? relationEdit.substring(separatorIndex + 1)
                            : '';

                        if (
                            ['PO', 'SALES'].includes(relationTypeEdit) &&
                            relationNoEdit
                        ) {
                            $('#relationSelectedInfoEdit').val(
                                relationTypeEdit + ' - ' + relationNoEdit
                            );

                            $('#btnHapusRelationEdit').removeClass('d-none');
                        } else {
                            $('#RELATION_EDIT').val('');
                            $('#relationSelectedInfoEdit').val('Cost Harian');
                            $('#btnHapusRelationEdit').addClass('d-none');
                        }
                    } else {
                        $('#relationSelectedInfoEdit').val('Cost Harian');
                        $('#btnHapusRelationEdit').addClass('d-none');
                    }

                    $('input[name="PEMBAYARAN"][value="'+h.PEMBAYARAN+'"]').prop('checked',true);

                    $('#costDetailTableEdit tbody').html(''); // 🔥 reset total
                    d.forEach(row => addDetailRow(row, '#costDetailTableEdit'));
                    updateFooterTotal('#costDetailTableEdit', '#FOOTER_TOTAL_EDIT');

                    $('#costEdit').modal('show');
                }else{
                    alert(resp.message || 'Gagal load data COST');
                }
            });
        });

        $('#fcostEdit').submit(function(e){
            e.preventDefault();

            let $btn = $(this).find('button[type="submit"]');
            if ($btn.prop('disabled')) return;

            lockSubmit($btn);

            let formData = new FormData(this);
            let hasError = false;

            $('#costDetailTableEdit tbody tr').each(function () {

                let $tr = $(this);
                let seq = $tr.data('seq');

                let tipe = $tr.find('.cost-type-select').val();
                let qty  = parseInt($tr.find('.qty').val()) || 0;
                let jml  = cleanNumber($tr.find('.jumlah').val());

                if (!tipe || qty <= 0 || jml <= 0) {
                    alert('Data detail tidak valid');
                    hasError = true;
                    return false;
                }

                formData.append(`DETAIL[${seq}][SEQ_NO]`, seq);
                formData.append(`DETAIL[${seq}][TIPE_COST]`, tipe);
                formData.append(`DETAIL[${seq}][QTY]`, qty);
                formData.append(`DETAIL[${seq}][JUMLAH]`, jml);
                formData.append(`DETAIL[${seq}][REMARK]`, $tr.find('.remark').val());

                let files = $tr.find('.cost-file')[0].files;
                let hasNewFile = files.length > 0;

                // 🔥 UPLOAD FILE BARU
                for (let i = 0; i < files.length; i++) {
                    formData.append(`ATTACHMENT[${seq}][]`, files[i]);
                }

                // 🔥 HANYA KIRIM OLD_ATTACHMENT JIKA TIDAK ADA FILE BARU
                if (!hasNewFile) {
                    $tr.find('input[name^="OLD_ATTACHMENT"]').each(function(){
                        formData.append(this.name, this.value);
                    });
                }
            });

            if (hasError) {
                unlockSubmit($btn);
                return;
            }

            formData.append('PLANT', $('#PLANT_EDIT').val());
            formData.append('OLD_COST', $('#OLD_COST_EDIT').val());
            formData.append('COST', $('#COST_EDIT').val());
            formData.append('COST_DATE', $('#COST_DATE_EDIT').val());
            formData.append('PEMBAYARAN', $('#fcostEdit input[name="PEMBAYARAN"]:checked').val());
            formData.append('REMARK', $('#REMARK_EDIT').val());
            formData.set('RELATION', $('#RELATION_EDIT').val() || '');

            $.ajax({
                url: '<?= base_url("mcost/update"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp){
                    alert(resp.message);
                    if(resp.status){
                        $('#costEdit').modal('hide');
                        loadPage(state.page);
                    } else {
                        unlockSubmit($btn);
                    }
                },
                error: function(){
                    alert('Terjadi kesalahan server');
                    unlockSubmit($btn);
                }
            });
        });

        /* ======================
        DELETE
        ====================== */
        $(document).on('click', '.deleteBtn', function () {

            let cost  = $(this).data('cost');
            let plant = $(this).data('plant');

            if (!confirm('Yakin ingin menghapus COST: ' + cost + ' ?')) return;

            $.post('<?= base_url("mcost/remove"); ?>', {
                cost: cost,
                plant: plant   // ⬅ PENTING
            }, function (resp) {

                resp = typeof resp === 'string' ? JSON.parse(resp) : resp;
                alert(resp.message);

                if (resp.status) {
                    loadPage(state.page);
                }

            }, 'json');
        });

        /* ======================
        PDF
        ====================== */
        $(document).on("click", ".exportPdf", function () {
            let cost  = $(this).data("cost");
            let plant = $(this).data("plant");

            window.open(
                "<?= base_url('mcost/print_pdf'); ?>?cost=" + cost + "&plant=" + plant,
                "_blank"
            );
        });

    }); // end ready

    function cleanNumber(val){
        if (!val) return 0;

        val = val.toString().trim(); // 🔥 buang spasi

        let num = val.replace(/[^0-9]/g, '');

        return num ? parseInt(num, 10) : 0;
    }

    function formatMoney(angka){
        angka = parseInt(angka || 0, 10).toString();
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatMoney(value){
        return Number(value || 0).toLocaleString('id-ID');
    }

    function formatQty(value){
        return parseFloat(value || 0).toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }
        );
    }

    function unlockSubmit($btn){
        let text = $btn.data('orig-text') || 'Simpan';
        $btn
            .prop('disabled', false)
            .html(text);
    }

    function lockSubmit($btn){
        $btn
            .data('orig-text', $btn.html())
            .prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
    }
</script>

