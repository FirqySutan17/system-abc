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
                        placeholder="Cari Nomor SV...">

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
                    data-bs-target="#savingAdd">

                    <i class="ti ti-plus"></i>

                    Tambah Saving

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

                    Sedang memuat...

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

                            <th>Nomor SV</th>

                            <th>Tanggal</th>

                            <th>Plant</th>

                            <th>Customer</th>
                            
                            <th>Related</th>

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
    id="savingAdd"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="
            modal-dialog
            modal-xl
            modal-dialog-scrollable">

        <form id="fsavingAdd">

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
                id="hiddenSVNoAdd"
                name="SV_NO">

            <div
                class="
                    modal-content
                    receive-card">

                <!-- HEADER -->

                <div class="modal-header">

                    <h5 class="modal-title">

                        SAVING - TAMBAH

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

                            SAVING

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

                                    Nomor Saving

                                </label>

                                <input
                                    type="text"
                                    id="SV_NO_ADD"
                                    class="form-control"
                                    readonly
                                    placeholder="Otomatis terisi"
                                    style="background:#f1f5f9">

                            </div>

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Tanggal Saving *

                                </label>

                                <input
                                    type="date"
                                    id="CN_DATE_ADD"
                                    name="SV_DATE"
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

                                    Amount

                                </label>

                                <input
                                    type="text"
                                    id="AMOUNT"
                                    class="form-control"
                                    name="AMOUNT"
                                    placeholder="Amount">

                            </div>

                            <div class="col-md-12">

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
                                id="sumSVAmount">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Jumlah Saving

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

                                HISTORI SAVING

                            </div>

                        </div>

                        <div
                            class="table-responsive">

                            <table
                                class="
                                    table
                                    table-bordered"
                                id="savingDetailTableAdd">

                                <thead>

                                    <tr>

                                        <th>
                                            Nomor Saving
                                        </th>

                                        <th>
                                            Tanggal Saving
                                        </th>

                                        <th>
                                            Jumlah Saving
                                        </th>

                                        <th>
                                            Dibuat Oleh
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
    id="savingEdit"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="
            modal-dialog
            modal-xl
            modal-dialog-scrollable">

        <form id="fsavingEdit">

            <input
                type="hidden"
                id="IDNO_EDIT"
                name="SVNoEdit">

            <input
                type="hidden"
                id="hiddenPlantEdit">

            <input
                type="hidden"
                id="hiddenCustomerEdit">

            <div
                class="
                    modal-content
                    receive-card">

                <!-- HEADER -->

                <div class="modal-header">

                    <h5 class="modal-title">

                        SAVING - UBAH

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

                            SAVING

                        </div>

                        <div class="row g-3">

                            <!-- PLANT -->

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Plant *

                                </label>

                                <input
                                    type="text"
                                    id="SV_PLANT_EDIT"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                            </div>

                            <!-- SV NO -->

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Nomor Saving

                                </label>

                                <input
                                    type="text"
                                    id="SV_NO_EDIT"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                            </div>

                            <!-- DATE -->

                            <div class="col-md-4">

                                <label
                                    class="form-label">

                                    Tanggal Saving *

                                </label>

                                <input
                                    type="date"
                                    id="SV_DATE_EDIT"
                                    name="SV_DATE"
                                    class="form-control">

                            </div>

                            <!-- CUSTOMER -->

                            <div class="col-md-6">

                                <label
                                    class="form-label">

                                    Customer *

                                </label>

                                <input
                                    type="text"
                                    id="SV_CUSTOMER_EDIT"
                                    class="form-control"
                                    readonly
                                    style="background:#f1f5f9">

                            </div>

                            <div class="col-md-6">

                                <label
                                    class="form-label">

                                    Jumlah Saving

                                </label>

                                <input
                                    type="text"
                                    id="AMOUNT_EDIT"
                                    class="form-control"
                                    name="AMOUNT"
                                    placeholder="Amount">

                            </div>

                            <!-- REMARK -->

                            <div class="col-md-12">

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
                                id="sumSVAmountEdit">

                                0

                            </div>

                            <div
                                class="summary-label">

                                Jumlah Saving

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

                                HISTORI SAVING

                            </div>

                        </div>

                        <div
                            class="table-responsive">

                            <table
                                class="
                                    table
                                    table-bordered"
                                id="savingHistoryTableEdit">

                                <thead>

                                    <tr>

                                        <th>
                                            Nomor Saving
                                        </th>

                                        <th>
                                            Tanggal Saving
                                        </th>

                                        <th>
                                            Jumlah Saving
                                        </th>

                                        <th>
                                            Dibuat Oleh
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

        order:'SV_DATE',
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
                "<?= base_url('saving/load_data') ?>",

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
            function(r, index)
            {
                console.log(index);
                buttonRender = `<button
                            class="
                                btn
                                btn-warning
                                btn-sm
                                btnEdit"
                            onClick="triggerSavingEdit('${r.SV_NO}')">

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
                            onClick="triggerDelete('${r.SV_NO}')">

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

                        </button>`;
                if (index > 0) {
                    buttonRender = '';
                }

                html += `

                <tr>

                    <td>

                        ${r.SV_NO}

                    </td>

                    <td>

                        ${formatDateIndo(
                            r.SV_DATE
                        )}

                    </td>

                    <td>

                        ${r.PLANT_NAME ?? '-'}

                    </td>

                    <td>

                        ${r.CUSTOMER_NAME ?? '-'}

                    </td>

                    <td>

                        ${r.RELATED ?? '-'}

                    </td>

                    <td
                        class="text-end">

                        ${formatDecimal(
                            r.AMOUNT
                        )}

                    </td>

                    <td>

                        ${r.CREATED_BY ?? '-'}

                    </td>

                    <td>

                        ${buttonRender}

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

    function initPlantSelect2(selector, defaultSelect = false)
    {
        $(selector).select2({

            theme:'bootstrap-5',

            width:'100%',

            placeholder:
                '-- PILIH PLANT --',

            ajax:{

                url:
                    "<?= base_url('saving/get_plant') ?>",

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


        if (defaultSelect) {
            
            const defaultId = "0001";
            const defaultText = "JAKARTA";
            const defaultOption = new Option(defaultText, defaultId, true, true);
            $(selector).append(defaultOption).trigger('change');
            $(selector).trigger({
                type: 'select2:select',
                params: {
                    data: {id: defaultId, text: defaultText}
                }
            });
        }

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
                    "<?= base_url('saving/get-customer') ?>",

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
    | GENERATE SV NO
    |--------------------------------------------------------------------------
    */

    function generateSVNo()
    {
        let plant =
            $('#plantAdd').val();

        if (!plant)
        {
            $('#SV_NO_ADD')
                .val('');

            $('#hiddenSVNoAdd')
                .val('');

            return;
        }

        $.get(

            "<?= base_url('saving/generate_sv_no') ?>",

            {
                plant:plant
            },

            function(res)
            {
                if (!res.status)
                    return;

                $('#SV_NO_ADD')
                    .val(
                        res.sv_no
                    );

                $('#hiddenSVNoAdd')
                    .val(
                        res.sv_no
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
            resetSummaryAdd();

            generateSVNo();
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

            resetSummaryAdd();
            loadSVHistory();
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
        
        $('#savingDetailTableAdd tbody')
            .html('');
        $('#sumSVAmount').html('0');
    }

    function resetSummaryEdit()
    {
        $('#sumSVAmountEdit').html('0');
    }

    function resetAddForm()
    {
        $('#fsavingAdd')[0]
            .reset();

        $('#plantAdd')
            .empty()
            .trigger('change');

        $('#customerAdd')
            .empty()
            .trigger('change');

        $('#savingDetailTableAdd tbody')
            .html('');

        $('#SV_NO_ADD')
            .val('');

        $('#hiddenSVNoAdd')
            .val('');

        resetSummaryAdd();
    }

    function resetEditForm()
    {
        $('#fsavingEdit')[0]
            .reset();

        $('#plantEdit')
            .empty()
            .trigger('change');

        $('#customerEdit')
            .empty()
            .trigger('change');

        $('#savingHistoryTableEdit tbody')
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
            '#plantAdd',
            true
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

    function triggerSavingEdit(savingNo) {
        $.ajax({
            url:
                "<?= base_url('saving/edit') ?>",

            type:'POST',

            data:{
                'sv_no': savingNo
            },

            dataType:'json',

            success:function(res)
            {
                if (res.status)
                {
                    const header = res.header;
                    const details = res.detail;
                    
                    // HEADER

                    $("#IDNO_EDIT").val(header.SV_NO);
                    $("#SV_PLANT_EDIT").val(header.PLANT_NAME);
                    $("#SV_CUSTOMER_EDIT").val(header.CUSTOMER_NAME);
                    $("#SV_NO_EDIT").val(header.SV_NO);
                    $("#SV_DATE_EDIT").val(header.SV_DATE);
                    $("#AMOUNT_EDIT").val(header.AMOUNT);
                    $("#REMARK_EDIT").val(header.REMARK);

                    let svAmount = header.AMOUNT;

                    // DETAIL
                    let htmlTableHistory = "";
                    if (details.length > 0) {
                        details.forEach(
                            function(r)
                            {
                                svAmount += r.AMOUNT;
                                htmlTableHistory += `

                                    <tr>

                                        <td>

                                            ${r.SV_NO}

                                        </td>

                                        <td>

                                            ${formatDateIndo(
                                                r.SV_DATE
                                            )}

                                        </td>

                                        <td
                                            class="text-end totalAmountEdit">

                                            ${formatDecimal(
                                                r.AMOUNT
                                            )}

                                        </td>

                                        <td>

                                            ${r.CREATED_BY ?? '-'}

                                        </td>

                                    </tr>

                                `;
                            }
                        );

                        $("#savingHistoryTableEdit tbody").html(htmlTableHistory);
                    }
                    calculateSummaryEdit();
                    $('#savingEdit').modal('show');
                }
            }

        });
    }

    function loadSVHistory() {
        const plant = $("#hiddenPlantAdd").val();
        const customer = $("#hiddenCustomerAdd").val();
        $.ajax({
            url:
                "<?= base_url('saving/get_sv_history') ?>",
            type:'POST',
            data:{
                'plant': plant,
                'customer': customer
            },
            dataType:'json',

            success:function(res)
            {
                if (res.status)
                {
                    const data = res.data;
                    let htmlTableHistory = "";
                    console.log(data);
                    if (data.length > 0) {
                        data.forEach(
                            function(r)
                            {
                                htmlTableHistory += `

                                    <tr>

                                        <td>

                                            ${r.SV_NO}

                                        </td>

                                        <td>

                                            ${formatDateIndo(
                                                r.SV_DATE
                                            )}

                                        </td>

                                        <td
                                            class="text-end totalAmount">

                                            ${formatDecimal(
                                                r.AMOUNT
                                            )}

                                        </td>

                                        <td>

                                            ${r.CREATED_BY ?? '-'}

                                        </td>

                                    </tr>

                                `;
                            }
                        );

                        $("#savingDetailTableAdd tbody").html(htmlTableHistory);
                    }
                    calculateSummary();
                }
            }

        });
    }

    function triggerDelete(savingNo) {
        $.ajax({
            url:
                "<?= base_url('saving/remove') ?>",

            type:'POST',

            data:{
                'sv_no': savingNo
            },

            dataType:'json',

            success:function(res)
            {
                if (res.status)
                {

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

    $(document).on(
        'keyup change',
        '#AMOUNT',
        function ()
        {
            calculateSummary();
        }
    );

    $(document).on(
        'keyup change',
        '#AMOUNT_EDIT',
        function ()
        {
            calculateSummaryEdit();
        }
    );

    function calculateSummary()
    {
        let savingAmount = 0;

        $('.totalAmount').each(
        function ()
        {
            let v =
                $(this)
                    .text()
                    .replace(/\./g,'')
                    .replace(',','.');

            savingAmount +=
                parseFloat(v)
                || 0;
        });

        savingAmount += parseFloat($('#AMOUNT').val()) || 0;

        $('#sumSVAmount')
            .html(
                formatIDNumber(
                    savingAmount
                )
            );
    }

    function calculateSummaryEdit()
    {
        let savingAmount = 0;

        $('.totalAmountEdit').each(
        function ()
        {
            let v =
                $(this)
                    .text()
                    .replace(/\./g,'')
                    .replace(',','.');

            savingAmount +=
                parseFloat(v)
                || 0;
        });

        savingAmount += parseFloat($('#AMOUNT_EDIT').val()) || 0;

        $('#sumSVAmountEdit')
            .html(
                formatIDNumber(
                    savingAmount
                )
            );
    }

    $(document).on(
        'submit',
        '#fsavingAdd',
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

            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            let formData =
                new FormData(this);

            $.ajax({

                url:
                    "<?= base_url('saving/create') ?>",

                type:'POST',

                data:formData,

                processData:false,
                contentType:false,

                dataType:'json',

                success:function(res)
                {
                    if (res.status)
                    {
                        $('#savingAdd')
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
        '#fsavingEdit',
        function (e)
        {
            e.preventDefault();

            let formData =
                new FormData(this);

            $.ajax({

                url: "<?= base_url('saving/update') ?>",
                type:'POST',
                data:formData,
                processData:false,
                contentType:false,
                dataType:'json',

                success:function(res)
                {
                    if (res.status)
                    {
                        $('#savingEdit')
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