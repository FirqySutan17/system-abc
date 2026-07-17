<div class="container-fluid">

    <div class="card">
        <div class="card-body">

            <div class="row g-3 align-items-end">

                <div class="col-md-3">

                    <label class="form-label">
                        Pencarian
                    </label>

                    <input
                        type="text"
                        id="search"
                        class="form-control"
                        placeholder="Cari Nomor CN...">

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Plant
                    </label>

                    <select
                        id="filterPlant"
                        class="form-select">
                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Customer
                    </label>

                    <select
                        id="filterCustomer"
                        class="form-select">
                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        id="dateFrom"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Tanggal Selesai
                    </label>

                    <input
                        type="date"
                        id="dateTo"
                        class="form-control">

                </div>

            </div>

            <div
                class="d-flex
                    justify-content-between
                    align-items-center
                    mt-4">

                <button
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#creditNoteAdd">

                    <i class="ti ti-plus"></i>

                    Tambah Credit Note

                </button>

                <div id="info"></div>

            </div>

        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">

        <div
            id="tableLoading"
            class="
                table-loading
                d-none">

            <div
                class="loading-card">

                <div
                    class="
                        spinner-border
                        text-primary">

                </div>

                <div class="mt-3">

                    Sedang Memuat...

                </div>

            </div>

        </div>

            <div
                class="table-responsive table-box"
                id="tableWrapper">

                <table
                    class="
                        table
                        table-bordered
                        table-modern">

                    <thead>

                        <tr>

                            <th>Nomor CN</th>

                            <th>Tanggal</th>

                            <th>Plant</th>

                            <th>Customer</th>

                            <th
                                class="text-end">

                                Total Amount

                            </th>

                            <th>
                                Dibuat Oleh
                            </th>

                            <th width="150">
                                #
                            </th>

                        </tr>

                    </thead>

                    <tbody id="table-body">

                    </tbody>

                </table>

            </div>

            <div
                class="
                    d-flex
                    justify-content-between
                    mt-3">

                <div id="pagination">

                </div>

            </div>

        </div>
    </div>

</div>

<style>

    .table-modern td,
    .table-modern th{
        white-space:nowrap;
        vertical-align:middle;
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

    .modal-dialog-scrollable .modal-body{
        overflow-y:auto;
        overflow-x:hidden;
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIVE STYLE
    |--------------------------------------------------------------------------
    */

    .receive-card{
        border:1px solid #e5e7eb;
        border-radius:18px;
    }

    .receive-card .modal-header{
        background:
            linear-gradient(
                135deg,
                #1e3a8a,
                #2563eb
            );

        color:#fff;
        border:none;
        padding:18px 24px;
    }

    .receive-card .modal-title{
        font-weight:700;
        font-size:18px;
        color:#fff;
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
        box-shadow:
            0 0 0 .15rem
            rgba(
                37,
                99,
                235,
                .15
            );
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

    .summary-box{
        background:#0f172a;
        color:#fff;
        border-radius:16px;
        padding:18px;
        display:flex;
        justify-content:space-around;
        text-align:center;
    }

    .summary-value{
        font-size:20px;
        font-weight:700;
    }

    .summary-label{
        opacity:.8;
        font-size:12px;
    }

    #creditNoteDetailTableAdd,
    #creditNoteDetailTableEdit{
        margin-bottom:0;
    }

    #creditNoteDetailTableAdd thead th,
    #creditNoteDetailTableEdit thead th{
        background:#f1f5f9;
        font-size:12px;
        font-weight:700;
        text-align:center;
        vertical-align:middle;
        white-space:nowrap;
    }

    #creditNoteDetailTableAdd tbody td,
    #creditNoteDetailTableEdit tbody td{
        vertical-align:middle;
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2
    |--------------------------------------------------------------------------
    */

    .select2-container{
        width:100%!important;
    }

    .select2-container--bootstrap-5
    .select2-selection{

        min-height:44px!important;
        border-radius:12px!important;
        border:1px solid #dbe2ea!important;
        font-size:12px!important;
    }

    .select2-container--bootstrap-5
    .select2-selection--single{

        padding-top:6px!important;
        padding-left:12px!important;
    }

    .select2-container--bootstrap-5
    .select2-selection__rendered{

        color:#212529!important;
        line-height:28px!important;
        padding-left:0!important;
    }

    .select2-container--bootstrap-5
    .select2-selection__arrow{

        height:42px!important;
    }

    .select2-dropdown{
        z-index:999999!important;
    }

    .select2-container--bootstrap-5
    .select2-dropdown
    .select2-results__options
    .select2-results__option{

        line-height:1;
        font-size:13px!important;
    }

</style>

<div
    class="modal fade"
    id="creditNoteAdd"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="
            modal-dialog
            modal-xl
            modal-dialog-scrollable">

        <form id="fcreditNoteAdd">

            <input
                type="hidden"
                id="hiddenPlantAdd"
                name="PLANT">

            <input
                type="hidden"
                id="hiddenCustomerAdd"
                name="CUSTOMER">

            <input
                type="hidden"
                id="hiddenCNNoAdd"
                name="CN_NO">

            <div
                class="
                    modal-content
                    receive-card">

                <!-- HEADER -->

                <div class="modal-header">

                    <h5 class="modal-title">

                        CREDIT NOTE - TAMBAH

                    </h5>

                    <button
                        type="button"
                        class="
                            btn-close
                            btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- HEADER SECTION -->

                    <div class="receive-section">

                        <div
                            class="
                                receive-section-title">

                            CREDIT NOTE

                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Plant *

                                </label>

                                <select
                                    id="plantAdd"
                                    class="form-select">
                                </select>

                            </div>

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Nomor Credit Note

                                </label>

                                <input
                                    type="text"
                                    id="CN_NO_ADD"
                                    class="form-control"
                                    readonly
                                    placeholder="Generated Otomatis"
                                    style="background:#f1f5f9">

                            </div>

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Tanggal Credit Note *

                                </label>

                                <input
                                    type="date"
                                    id="CN_DATE_ADD"
                                    name="CN_DATE"
                                    class="form-control"
                                    value="<?= date('Y-m-d'); ?>">

                            </div>

                            <div class="col-md-6">

                                <label
                                    class="form-label">

                                    Customer *

                                </label>

                                <select
                                    id="customerAdd"
                                    class="form-select">
                                </select>

                            </div>

                            <div class="col-md-6">

                                <label
                                    class="form-label">

                                    Keterangan

                                </label>

                                <textarea
                                    name="REMARK"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Opsional..."></textarea>

                            </div>

                        </div>

                    </div>

                    <!-- SUMMARY -->

                    <div class="summary-box mb-3">

                        <div>

                            <div
                                class="summary-value"
                                id="sumSalesAdd">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Jumlah Penjualan

                            </div>

                        </div>

                        <div>

                            <div
                                class="summary-value"
                                id="sumCNAdd">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Jumlah Credit Note

                            </div>

                        </div>

                        <div>

                            <div
                                class="summary-value"
                                id="sumQtyAdd">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Kuantitas

                            </div>

                        </div>

                        <div>

                            <div
                                class="summary-value"
                                id="sumBWAdd">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Berat

                            </div>

                        </div>

                    </div>

                    <!-- DETAIL -->

                    <div class="receive-section">

                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-center
                                mb-3">

                            <div
                                class="
                                    receive-section-title
                                    mb-0">

                                DETAIL CREDIT NOTE

                            </div>

                            <button
                                type="button"
                                id="btnAddCNRow"
                                class="
                                    btn
                                    btn-warning
                                    btn-sm">

                                + Tambah Baris

                            </button>

                        </div>

                        <div
                            class="table-responsive">

                            <table
                                class="
                                    table
                                    table-bordered"
                                id="creditNoteDetailTableAdd">

                                <thead>

                                    <tr>

                                        <th>
                                            Sales
                                        </th>

                                        <th>
                                            Jumlah Penjualan
                                        </th>

                                        <th>
                                            Remaining
                                        </th>

                                        <th>
                                            Kuantitas
                                        </th>

                                        <th>
                                            Berat
                                        </th>

                                        <th>
                                            Jumlah Credit Note
                                        </th>

                                        <th>
                                            Keterangan
                                        </th>

                                        <th>
                                            #
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Simpan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<div
    class="modal fade"
    id="creditNoteEdit"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="
            modal-dialog
            modal-xl
            modal-dialog-scrollable">

        <form id="fcreditNoteEdit">

            <input
                type="hidden"
                id="IDNO_EDIT"
                name="IDNO">

            <input
                type="hidden"
                id="hiddenPlantEdit"
                name="PLANT">

            <input
                type="hidden"
                id="hiddenCustomerEdit"
                name="CUSTOMER">

            <input
                type="hidden"
                id="hiddenCNNoEdit"
                name="CN_NO">

            <div
                class="
                    modal-content
                    receive-card">

                <!-- HEADER -->

                <div class="modal-header">

                    <h5 class="modal-title">

                        CREDIT NOTE - UBAH

                    </h5>

                    <button
                        type="button"
                        class="
                            btn-close
                            btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <!-- HEADER SECTION -->

                    <div class="receive-section">

                        <div
                            class="
                                receive-section-title">

                            CREDIT NOTE

                        </div>

                        <div class="row g-3">

                            <!-- PLANT -->

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Plant *

                                </label>

                                <select
                                    id="plantEdit"
                                    class="form-select">
                                </select>

                            </div>

                            <!-- CN NO -->

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Nomor Credit Note

                                </label>

                                <input
                                    type="text"
                                    id="CN_NO_EDIT"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                            </div>

                            <!-- DATE -->

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Tanggal Credit Note *

                                </label>

                                <input
                                    type="date"
                                    id="CN_DATE_EDIT"
                                    name="CN_DATE"
                                    class="form-control">

                            </div>

                            <!-- CUSTOMER -->

                            <div class="col-md-6">

                                <label
                                    class="form-label">

                                    Customer *

                                </label>

                                <select
                                    id="customerEdit"
                                    class="form-select">
                                </select>

                            </div>

                            <!-- REMARK -->

                            <div class="col-md-6">

                                <label
                                    class="form-label">

                                    Keterangan

                                </label>

                                <textarea
                                    id="REMARK_EDIT"
                                    name="REMARK"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Opsional..."></textarea>

                            </div>

                        </div>

                    </div>

                    <!-- SUMMARY -->

                    <div class="summary-box mb-3">

                        <div>

                            <div
                                class="summary-value"
                                id="sumSalesEdit">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Jumlah Penjualan

                            </div>

                        </div>

                        <div>

                            <div
                                class="summary-value"
                                id="sumCNEdit">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Jumlah Credit Note

                            </div>

                        </div>

                        <div>

                            <div
                                class="summary-value"
                                id="sumQtyEdit">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Kuantitas

                            </div>

                        </div>

                        <div>

                            <div
                                class="summary-value"
                                id="sumBWEdit">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Berat

                            </div>

                        </div>

                    </div>

                    <!-- DETAIL -->

                    <div class="receive-section">

                        <div
                            class="
                                d-flex
                                justify-content-between
                                align-items-center
                                mb-3">

                            <div
                                class="
                                    receive-section-title
                                    mb-0">

                                DETAIL CREDIT NOTE

                            </div>

                            <button
                                type="button"
                                id="btnAddCNRowEdit"
                                class="
                                    btn
                                    btn-warning
                                    btn-sm">

                                + Tambah Baris

                            </button>

                        </div>

                        <div
                            class="table-responsive">

                            <table
                                class="
                                    table
                                    table-bordered"
                                id="creditNoteDetailTableEdit">

                                <thead>

                                    <tr>

                                        <th>
                                            Sales
                                        </th>

                                        <th>
                                            Jumlah Penjualan
                                        </th>

                                        <th>
                                            Remaining
                                        </th>

                                        <th>
                                            Kuantitas
                                        </th>

                                        <th>
                                            Berat
                                        </th>

                                        <th>
                                            Jumlah Credit Note
                                        </th>

                                        <th>
                                            Keterangan
                                        </th>

                                        <th>
                                            #
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Simpan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>

    let state = {

        page:1,
        limit:10,

        search:'',

        plant:'',
        customer:'',

        date_from:'',
        date_to:'',

        order:'cn_date',
        dir:'DESC'

    };

    let searchTimer = null;

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    $('#search').on(
        'keyup',
        function ()
        {
            clearTimeout(
                searchTimer
            );

            searchTimer =
                setTimeout(
                    ()=>{

                        state.search =
                            $(this).val();

                        state.page = 1;

                        loadPage();

                    },
                    500
                );
        }
    );

    /*
    |--------------------------------------------------------------------------
    | FILTER PLANT
    |--------------------------------------------------------------------------
    */

    $('#filterPlant').on(
        'change',
        function ()
        {
            state.plant =
                $(this).val();

            state.page = 1;

            loadPage();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | FILTER CUSTOMER
    |--------------------------------------------------------------------------
    */

    $('#filterCustomer').on(
        'change',
        function ()
        {
            state.customer =
                $(this).val();

            state.page = 1;

            loadPage();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | FILTER DATE
    |--------------------------------------------------------------------------
    */

    $('#dateFrom,#dateTo').on(
        'change',
        function ()
        {
            state.date_from =
                $('#dateFrom').val();

            state.date_to =
                $('#dateTo').val();

            state.page = 1;

            loadPage();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | TABLE LOADING
    |--------------------------------------------------------------------------
    */

    function showTableLoading()
    {
        $('#tableLoading')
            .removeClass(
                'd-none'
            );

        $('#tableWrapper')
            .addClass(
                'loading-hide'
            );
    }

    function hideTableLoading()
    {
        $('#tableLoading')
            .addClass(
                'd-none'
            );

        $('#tableWrapper')
            .removeClass(
                'loading-hide'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT DATE
    |--------------------------------------------------------------------------
    */

    function formatDateIndo(date)
    {
        if (!date)
            return '-';

        return new Date(date)
            .toLocaleDateString(
                'id-ID',
                {

                    day:'2-digit',
                    month:'long',
                    year:'numeric'

                }
            );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT DECIMAL
    |--------------------------------------------------------------------------
    */

    function formatDecimal(val)
    {
        val =
            parseFloat(
                val || 0
            );

        return val.toLocaleString(
            'id-ID',
            {

                minimumFractionDigits:2,
                maximumFractionDigits:2

            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD DATA
    |--------------------------------------------------------------------------
    */

    function loadPage(
        page = 1
    )
    {
        state.page = page;

        showTableLoading();

        $.ajax({

            url:
                "<?= base_url('credit-note/load_data') ?>",

            type:'GET',

            data:state,

            dataType:'json',

            success:function(res)
            {
                renderTable(
                    res.rows || []
                );

                $('#pagination')
                    .html(
                        res.pagination
                    );

                $('#info')
                    .html(

                        `Total :
                        ${res.total}
                        Data`

                    );
            },

            complete:function()
            {
                hideTableLoading();
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER TABLE
    |--------------------------------------------------------------------------
    */

    function renderTable(
        rows
    )
    {
        let html = '';

        if (
            rows.length === 0
        )
        {
            html += `

                <tr>

                    <td
                        colspan="7"
                        class="text-center">

                        Data tidak ditemukan

                    </td>

                </tr>

            `;

            $('#table-body')
                .html(
                    html
                );

            return;
        }

        rows.forEach(
            function(r)
            {
                html += `

                <tr>

                    <td>

                        ${r.cn_no}

                    </td>

                    <td>

                        ${formatDateIndo(
                            r.cn_date
                        )}

                    </td>

                    <td>

                        ${r.PLANT_NAME ?? '-'}

                    </td>

                    <td>

                        ${r.CUSTOMER_NAME ?? '-'}

                    </td>

                    <td
                        class="text-end">

                        ${formatDecimal(
                            r.total_amount
                        )}

                    </td>

                    <td>

                        ${r.created_by ?? '-'}

                    </td>

                    <td>

                        <button
                            class="
                                btn
                                btn-warning
                                btn-sm
                                btnEdit"
                            data-id="${r.idno}">

                            <i
                                class="
                                    ti
                                    ti-pencil">
                            </i>

                        </button>

                        <button
                            class="
                                btn
                                btn-danger
                                btn-sm
                                btnDelete"
                            data-id="${r.idno}">

                            <i
                                class="
                                    ti
                                    ti-trash">
                            </i>

                        </button>

                        <button
                            class="
                                btn
                                btn-primary
                                btn-sm
                                btnPrint"
                            data-id="${r.idno}">

                            <i
                                class="
                                    ti
                                    ti-printer">
                            </i>

                        </button>

                    </td>

                </tr>

                `;
            }
        );

        $('#table-body')
            .html(
                html
            );
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    function loadPagination(
        page
    )
    {
        loadPage(
            page
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 PLANT
    |--------------------------------------------------------------------------
    */

    function initPlantSelect2(selector)
    {
        $(selector).select2({

            theme:'bootstrap-5',

            width:'100%',

            placeholder:
                '-- PILIH PLANT --',

            ajax:{

                url:
                    "<?= base_url('credit-note/get_plant') ?>",

                dataType:'json',

                processResults:
                    function(data)
                    {
                        return {
                            results:data
                        };
                    }
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT2 CUSTOMER
    |--------------------------------------------------------------------------
    */

    function initCustomerSelect2(
        selector,
        plantSelector
    )
    {
        $(selector).select2({

            theme:'bootstrap-5',

            width:'100%',

            placeholder:
                '-- PILIH CUSTOMER --',

            ajax:{

                url:
                    "<?= base_url('credit-note/get-customer') ?>",

                dataType:'json',

                delay:250,

                data:function(params)
                {
                    return {

                        q:
                            params.term,

                        plant:
                            $(plantSelector)
                                .val()

                    };
                },

                processResults:
                    function(data)
                    {
                        return {
                            results:data
                        };
                    }
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE CN NO
    |--------------------------------------------------------------------------
    */

    function generateCNNo()
    {
        let plant =
            $('#plantAdd').val();

        if (!plant)
        {
            $('#CN_NO_ADD')
                .val('');

            $('#hiddenCNNoAdd')
                .val('');

            return;
        }

        $.get(

            "<?= base_url('credit-note/generate_cn_no') ?>",

            {
                plant:plant
            },

            function(res)
            {
                if (!res.status)
                    return;

                $('#CN_NO_ADD')
                    .val(
                        res.cn_no
                    );

                $('#hiddenCNNoAdd')
                    .val(
                        res.cn_no
                    );
            },

            'json'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PLANT ADD CHANGE
    |--------------------------------------------------------------------------
    */

    $('#plantAdd').on(
        'change',
        function()
        {
            let plant =
                $(this).val();

            $('#hiddenPlantAdd')
                .val(
                    plant
                );

            /*
            |------------------------------------
            | RESET CUSTOMER
            |------------------------------------
            */

            $('#customerAdd')
                .empty()
                .trigger('change');

            $('#hiddenCustomerAdd')
                .val('');

            /*
            |------------------------------------
            | RESET DETAIL
            |------------------------------------
            */

            $('#creditNoteDetailTableAdd tbody')
                .html('');

            resetSummaryAdd();

            generateCNNo();
        }
    );

    $('#customerAdd').on(
        'change',
        function()
        {
            $('#hiddenCustomerAdd')
                .val(
                    $(this).val()
                );

            /*
            |------------------------------------
            | RESET DETAIL
            |------------------------------------
            */

            $('#creditNoteDetailTableAdd tbody')
                .html('');

            resetSummaryAdd();
        }
    );

    $('#plantEdit').on(
        'change',
        function()
        {
            $('#hiddenPlantEdit')
                .val(
                    $(this).val()
                );
        }
    );

    $('#customerEdit').on(
        'change',
        function()
        {
            $('#hiddenCustomerEdit')
                .val(
                    $(this).val()
                );
        }
    );

    function resetSummaryAdd()
    {
        $('#sumSalesAdd').html('0');
        $('#sumCNAdd').html('0');
        $('#sumQtyAdd').html('0');
        $('#sumBWAdd').html('0');
    }

    function resetSummaryEdit()
    {
        $('#sumSalesEdit').html('0');
        $('#sumCNEdit').html('0');
        $('#sumQtyEdit').html('0');
        $('#sumBWEdit').html('0');
    }

    function resetAddForm()
    {
        $('#fcreditNoteAdd')[0]
            .reset();

        $('#plantAdd')
            .empty()
            .trigger('change');

        $('#customerAdd')
            .empty()
            .trigger('change');

        $('#creditNoteDetailTableAdd tbody')
            .html('');

        $('#CN_NO_ADD')
            .val('');

        $('#hiddenCNNoAdd')
            .val('');

        resetSummaryAdd();
    }

    function resetEditForm()
    {
        $('#fcreditNoteEdit')[0]
            .reset();

        $('#plantEdit')
            .empty()
            .trigger('change');

        $('#customerEdit')
            .empty()
            .trigger('change');

        $('#creditNoteDetailTableEdit tbody')
            .html('');

        resetSummaryEdit();
    }

    $(function(){

        initPlantSelect2(
            '#filterPlant'
        );

        initCustomerSelect2(
            '#filterCustomer',
            '#filterPlant'
        );

        initPlantSelect2(
            '#plantAdd'
        );

        initCustomerSelect2(
            '#customerAdd',
            '#plantAdd'
        );

        initPlantSelect2(
            '#plantEdit'
        );

        initCustomerSelect2(
            '#customerEdit',
            '#plantEdit'
        );

        loadPage();

    });

    function formatIDNumber(value)
    {
        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {
            return '';
        }

        let num =
            parseFloat(
                value
                    .toString()
                    .replace(/\./g,'')
                    .replace(',','.')
            );

        if (isNaN(num)) {
            return '';
        }

        return num.toLocaleString(
            'id-ID',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );
    }

    $(document).on(
        'input',
        '.decimal-id',
        function ()
        {
            let value =
                $(this).val();

            value =
                value.replace(
                    /[^\d,]/g,
                    ''
                );

            let parts =
                value.split(',');

            let integer =
                parts[0]
                    .replace(/\./g,'');

            integer =
                integer.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );

            let decimal =
                parts.length > 1
                    ? ',' + parts[1]
                    : '';

            $(this).val(
                integer + decimal
            );
        }
    );

    $('#btnAddCNRow').click(
    function ()
    {
        addCNRowAdd();
    });

    function addCNRowAdd()
    {
        let row = `

        <tr>

            <td>

                <select
                    name="SALES_NO[]"
                    class="
                        form-select
                        salesSelect">
                </select>

            </td>

            <td>

                <input
                    type="text"
                    class="
                        form-control
                        text-end
                        salesAmt"
                    readonly>

            </td>

            <td>

                <input
                    type="text"
                    class="
                        form-control
                        text-end
                        remainingAmt"
                    readonly>

            </td>

            <td>

                <input
                    type="text"
                    class="
                        form-control
                        text-end
                        qty"
                    readonly>

            </td>

            <td>

                <input
                    type="text"
                    class="
                        form-control
                        text-end
                        bw"
                    readonly>

            </td>

            <td>

                <input
                    type="text"
                    class="
                        form-control
                        text-end
                        cnAmt
                        decimal-id">

                <input
                    type="hidden"
                    class="cnAmtHidden"
                    name="CN_AMOUNT[]">

            </td>

            <td>

                <input
                    type="text"
                    class="form-control remark"
                    name="DETAIL_REMARK[]">

            </td>

            <td>

                <button
                    type="button"
                    class="
                        btn
                        btn-danger
                        btn-sm
                        btnRemoveRow">

                    <i class="ti ti-trash"></i>

                </button>

            </td>

        </tr>

        `;

        $('#creditNoteDetailTableAdd tbody')
            .append(row);

        let tr =
            $('#creditNoteDetailTableAdd tbody tr')
                .last();

        initSalesSelect2(
            tr.find('.salesSelect')
        );
    }

    $('#btnAddCNRowEdit').click(
    function ()
    {
        addCNRowEdit();
    });

    function addCNRowEdit()
    {
        let row =
            $('#creditNoteDetailTableAdd tbody')
                .html();

        $('#creditNoteDetailTableEdit tbody')
            .append(
                row
            );

        let tr =
            $('#creditNoteDetailTableEdit tbody tr')
                .last();

        initSalesSelect2(
            tr.find('.salesSelect')
        );
    }

    $(document).on(
        'click',
        '.btnRemoveRow',
        function ()
        {
            $(this)
                .closest('tr')
                .remove();

            calculateSummary();
        }
    );

    function initSalesSelect2(selector)
    {
        selector.select2({

            theme:'bootstrap-5',

            width:'100%',

            placeholder:
                '-- PILIH SALES --',

            ajax:{

                url:
                    "<?= base_url('credit-note/get_sales_remaining') ?>",

                dataType:'json',

                delay:250,

                data:function(params)
                {
                    return {

                        q:
                            params.term,

                        plant:
                            $('#hiddenPlantAdd').val()
                            ||
                            $('#hiddenPlantEdit').val(),

                        customer:
                            $('#hiddenCustomerAdd').val()
                            ||
                            $('#hiddenCustomerEdit').val()
                    };
                },

                processResults:
                    function(data)
                    {
                        return {
                            results:data
                        };
                    }
            }

        });
    }

    $(document).on(
        'select2:select',
        '.salesSelect',
        function(e)
        {
            let data =
                e.params.data;

            let tr =
                $(this)
                    .closest('tr');

            tr.find('.salesAmt')
                .val(
                    formatIDNumber(
                        data.amount
                    )
                );

            tr.find('.remainingAmt')
                .val(
                    formatIDNumber(
                        data.remaining
                    )
                );

            tr.find('.qty')
                .val(
                    formatIDNumber(
                        data.qty
                    )
                );

            tr.find('.bw')
                .val(
                    formatIDNumber(
                        data.bw
                    )
                );

            calculateSummary();
        }
    );

    $(document).on(
        'keyup change',
        '.cnAmt',
        function ()
        {
            let val =
                $(this)
                    .val()
                    .replace(/\./g,'')
                    .replace(',','.');

            $(this)
                .closest('td')
                .find('.cnAmtHidden')
                .val(
                    val
                );

            calculateSummary();
        }
    );

    function calculateSummary()
    {
        let sales = 0;
        let cn = 0;
        let qty = 0;
        let bw = 0;

        $('.salesAmt').each(
        function ()
        {
            let v =
                $(this)
                    .val()
                    .replace(/\./g,'')
                    .replace(',','.');

            sales +=
                parseFloat(v)
                || 0;
        });

        $('.cnAmtHidden').each(
        function ()
        {
            cn +=
                parseFloat(
                    $(this).val()
                )
                || 0;
        });

        $('.qty').each(
        function ()
        {
            let v =
                $(this)
                    .val()
                    .replace(/\./g,'')
                    .replace(',','.');

            qty +=
                parseFloat(v)
                || 0;
        });

        $('.bw').each(
        function ()
        {
            let v =
                $(this)
                    .val()
                    .replace(/\./g,'')
                    .replace(',','.');

            bw +=
                parseFloat(v)
                || 0;
        });

        $('#sumSalesAdd,#sumSalesEdit')
            .html(
                formatIDNumber(
                    sales
                )
            );

        $('#sumCNAdd,#sumCNEdit')
            .html(
                formatIDNumber(
                    cn
                )
            );

        $('#sumQtyAdd,#sumQtyEdit')
            .html(
                formatIDNumber(
                    qty
                )
            );

        $('#sumBWAdd,#sumBWEdit')
            .html(
                formatIDNumber(
                    bw
                )
            );
    }

    $(document).on(
        'submit',
        '#fcreditNoteAdd',
        function (e)
        {
            e.preventDefault();

            /*
            |--------------------------------------------------------------------------
            | VALIDASI HEADER
            |--------------------------------------------------------------------------
            */

            if (!$('#hiddenPlantAdd').val())
            {
                Toast.fire({
                    icon:'error',
                    title:'Plant wajib dipilih.'
                });

                return;
            }

            if (!$('#hiddenCustomerAdd').val())
            {
                Toast.fire({
                    icon:'error',
                    title:'Customer wajib dipilih.'
                });

                return;
            }

            if (
                $('#creditNoteDetailTableAdd tbody tr')
                    .length === 0
            )
            {
                Toast.fire({
                    icon:'error',
                    title:'Detail credit note masih kosong.'
                });

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DETAIL
            |--------------------------------------------------------------------------
            */

            let valid = true;

            $('#creditNoteDetailTableAdd tbody tr')
                .each(function(){

                    let sales =
                        $(this)
                            .find('.salesSelect')
                            .val();

                    let amount =
                        $(this)
                            .find('.cnAmtHidden')
                            .val();

                    if (
                        !sales ||
                        !amount ||
                        parseFloat(amount) <= 0
                    )
                    {
                        valid = false;
                        return false;
                    }
                });

            if (!valid)
            {
                Toast.fire({
                    icon:'error',
                    title:'Sales dan CN Amount wajib diisi.'
                });

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            let formData =
                new FormData(this);

            $.ajax({

                url:
                    "<?= base_url('credit-note/create') ?>",

                type:'POST',

                data:formData,

                processData:false,
                contentType:false,

                dataType:'json',

                success:function(res)
                {
                    if (res.status)
                    {
                        $('#creditNoteAdd')
                            .modal('hide');

                        resetAddForm();

                        loadPage();

                        Toast.fire({
                            icon:'success',
                            title:res.message
                        });
                    }
                    else
                    {
                        Toast.fire({
                            icon:'error',
                            title:res.message
                        });
                    }
                }

            });

        }
    );

    $(document).on(
        'submit',
        '#fcreditNoteEdit',
        function (e)
        {
            e.preventDefault();

            let formData =
                new FormData(this);

            $.ajax({

                url:
                    "<?= base_url('credit-note/update') ?>",

                type:'POST',

                data:formData,

                processData:false,
                contentType:false,

                dataType:'json',

                success:function(res)
                {
                    if (res.status)
                    {
                        $('#creditNoteEdit')
                            .modal('hide');

                        resetEditForm();

                        loadPage();

                        Toast.fire({
                            icon:'success',
                            title:res.message
                        });
                    }
                    else
                    {
                        Toast.fire({
                            icon:'error',
                            title:res.message
                        });
                    }
                }

            });

        }
    );

</script>