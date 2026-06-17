<div class="container-fluid">

    <div class="card w-100">

        <div class="card-body">

            <h5 class="card-title fw-semibold mb-4">
                CULLING
            </h5>

            <!-- FILTER -->
            <div class="row g-2 mb-3">

                <div class="col-md-3">
                    <input
                        type="text"
                        id="search"
                        class="form-control"
                        placeholder="Cari plant, class out, remark...">
                </div>

                <div class="col-md-2">
                    <select
                        id="filterPlant"
                        class="form-control">
                    </select>
                </div>

                <div class="col-md-2">
                    <input
                        type="date"
                        id="dateFrom"
                        class="form-control"
                        value="<?= date('Y-m-01'); ?>">
                </div>

                <div class="col-md-2">
                    <input
                        type="date"
                        id="dateTo"
                        class="form-control"
                        value="<?= date('Y-m-d'); ?>">
                </div>

                <div class="col-md-1">
                    <button
                        id="btnResetFilter"
                        class="btn btn-light w-100">
                        Reset
                    </button>
                </div>

                <div class="col-md-2 text-end">
                    <button
                        class="btn btn-primary w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#cullingAdd">

                        <i class="ti ti-plus"></i>
                        Tambah Culling

                    </button>
                </div>

            </div>

            <!-- TABLE -->
            <div class="table-box position-relative">

                <div
                    id="tableLoading"
                    class="table-loading d-none">

                    <div class="loading-card">

                        <div
                            class="spinner-border text-primary">
                        </div>

                        <div class="mt-3 fw-semibold">
                            Loading data...
                        </div>

                        <small class="text-muted">
                            Please wait a moment
                        </small>

                    </div>

                </div>

                <div id="tableWrapper">

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle table-modern"
                            id="mainTable">

                            <thead>

                                <tr>

                                    <th style="text-align: center; vertical-align: middle">Plant</th>
                                    <th style="text-align: center; vertical-align: middle">Date</th>
                                    <th style="text-align: center; vertical-align: middle">Class Out</th>
                                    <th style="text-align: center; vertical-align: middle">Jumlah</th>
                                    <th style="text-align: center; vertical-align: middle">Berat</th>
                                    <th style="text-align: center; vertical-align: middle">Remark</th>
                                    <th style="text-align: center; vertical-align: middle">#</th>

                                </tr>

                            </thead>

                            <tbody id="table-body">
                            </tbody>

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

    .modal-dialog-scrollable .modal-body{
        overflow-y:auto;
        overflow-x:hidden;
    }

    .select2-container{
        width:100% !important;
    }

    .select2-container--bootstrap-5
    .select2-selection{
        min-height:44px !important;
        border-radius:12px !important;
        border:1px solid #dbe2ea !important;
        font-size:12px !important;
    }

    .select2-container--bootstrap-5
    .select2-selection--single{
        padding-top:6px !important;
        padding-left:12px !important;
    }

    .select2-container--bootstrap-5
    .select2-selection__rendered{
        color:#212529 !important;
        line-height:28px !important;
        padding-left:0 !important;
    }

    .select2-container--bootstrap-5
    .select2-selection__arrow{
        height:42px !important;
    }

    .select2-dropdown{
        z-index:999999 !important;
    }

    .select2-container--bootstrap-5
    .select2-dropdown
    .select2-results__options
    .select2-results__option{
        line-height:1;
        font-size:13px !important;
    }

    @media(max-width:768px){

        .receive-card .modal-body{
            padding:15px;
        }

        .receive-section{
            padding:15px;
        }

    }

</style>

<div class="modal fade"
     id="cullingAdd"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <form id="fcullingAdd">

            <div class="modal-content receive-card">

                <!-- HEADER -->

                <div class="modal-header">

                    <h5 class="modal-title">
                        CULLING - TAMBAH
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="receive-section">

                        <div class="receive-section-title">
                            HEADER CULLING
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Plant *
                                </label>

                                <select
                                    id="plantAdd"
                                    name="PLANT"
                                    class="form-select"
                                    required>
                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Date *
                                </label>

                                <input
                                    type="date"
                                    name="YMD"
                                    class="form-control"
                                    value="<?= date('Y-m-d'); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Jumlah *
                                </label>

                                <input
                                    type="text"
                                    id="JUMLAH_ADD"
                                    class="form-control text-end decimal-id"
                                    autocomplete="off">

                                <input
                                    type="hidden"
                                    id="JUMLAH_ADD_HIDDEN"
                                    name="JUMLAH">

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Berat *
                                </label>

                                <input
                                    type="text"
                                    id="BERAT_ADD"
                                    class="form-control text-end decimal-id"
                                    autocomplete="off">

                                <input
                                    type="hidden"
                                    id="BERAT_ADD_HIDDEN"
                                    name="BERAT">

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Class Out *
                                </label>

                                <select
                                    id="classOutAdd"
                                    name="CLASS_OUT"
                                    class="form-select"
                                    required>
                                </select>

                            </div>

                            <div class="col-md-12">

                                <label class="form-label">
                                    Remark
                                </label>

                                <textarea
                                    name="REMARK"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Opsional..."></textarea>

                            </div>

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

                        Simpan Culling

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="modal fade"
     id="cullingEdit"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <form id="fcullingEdit">

            <input
                type="hidden"
                id="IDNO_EDIT"
                name="IDNO">

            <div class="modal-content receive-card">

                <div class="modal-header">

                    <h5 class="modal-title">
                        CULLING - EDIT
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="receive-section">

                        <div class="receive-section-title">
                            HEADER CULLING
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Plant *
                                </label>

                                <select
                                    id="plantEdit"
                                    name="PLANT"
                                    class="form-select"
                                    required>
                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Date *
                                </label>

                                <input
                                    type="date"
                                    id="YMD_EDIT"
                                    name="YMD"
                                    class="form-control"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Jumlah *
                                </label>

                                <input
                                    type="text"
                                    id="JUMLAH_EDIT_DISPLAY"
                                    class="form-control text-end decimal-id">

                                <input
                                    type="hidden"
                                    id="JUMLAH_EDIT"
                                    name="JUMLAH">

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Berat *
                                </label>

                                <input
                                    type="text"
                                    id="BERAT_EDIT_DISPLAY"
                                    class="form-control text-end decimal-id">

                                <input
                                    type="hidden"
                                    id="BERAT_EDIT"
                                    name="BERAT">

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Class Out *
                                </label>

                                <select
                                    id="classOutEdit"
                                    name="CLASS_OUT"
                                    class="form-select"
                                    required>
                                </select>

                            </div>

                            <div class="col-md-12">

                                <label class="form-label">
                                    Remark
                                </label>

                                <textarea
                                    id="REMARK_EDIT"
                                    name="REMARK"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Opsional..."></textarea>

                            </div>

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

                        Update Culling

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let state = {
        page: 1,
        limit: 10,
        search: '',
        plant: '',
        date_from: $('#dateFrom').val(),
        date_to: $('#dateTo').val(),
        order: 'CREATED_AT',
        dir: 'DESC'
    };

    let searchTimer = null;

    $('#search').on('keyup', function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {

            state.search = $(this).val();
            state.page = 1;

            loadPage();

        }, 500);

    });

    $('#filterPlant').on('change', function () {

        state.plant = $(this).val();
        state.page = 1;

        loadPage();

    });

    $('#dateFrom, #dateTo').on('change', function () {

        state.date_from =
            $('#dateFrom').val();

        state.date_to =
            $('#dateTo').val();

        state.page = 1;

        loadPage();

    });

    $('#btnResetFilter').click(function () {

        $('#search').val('');

        $('#filterPlant').val(null).trigger('change.select2');

        $('#dateFrom')
            .val('');

        $('#dateTo')
            .val('');

        state = {

            page: 1,
            limit: 10,
            search: '',
            plant: '',
            date_from: '',
            date_to: '',
            order: 'CREATED_AT',
            dir: 'DESC'

        };

        loadPage();

    });

    function formatDecimal(val)
    {
        val = parseFloat(val || 0);

        return val.toLocaleString(
            'en-US',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 6
            }
        );
    }

    function formatDecimalInput(value)
    {
        if (!value) {
            return '';
        }

        let num =
            parseFloat(
                value.toString()
                    .replace(/,/g,'')
            );

        if (isNaN(num)) {
            return '';
        }

        return num.toLocaleString(
            'en-US',
            {
                minimumFractionDigits: 0,
                maximumFractionDigits: 6
            }
        );
    }

    function formatTanggalIndonesia(date)
    {
        if (!date) {
            return '-';
        }

        const bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        let d = new Date(date);

        if (isNaN(d)) {
            return date;
        }

        let tanggal = d.getDate();
        let namaBulan = bulan[d.getMonth()];
        let tahun = d.getFullYear();

        return `${tanggal} ${namaBulan} ${tahun}`;
    }

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
            parseFloat(value);

        if (isNaN(num)) {
            return '';
        }

        return num.toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 6
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
            value.replace(/[^\d,]/g,'');

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
    });

    function showTableLoading(){

        $('#tableLoading')
            .removeClass('d-none');

        $('#tableWrapper')
            .addClass('loading-hide');

    }

    function hideTableLoading(){

        $('#tableLoading')
            .addClass('d-none');

        $('#tableWrapper')
            .removeClass('loading-hide');

    }

    function initPlantSelect2(selector){

        $(selector).select2({

            theme:'bootstrap-5',
            placeholder:'-- PILIH PLANT --',
            width:'100%',

            ajax:{

                url:
                    "<?= base_url('culling/get_plant'); ?>",

                dataType:'json',

                processResults:function(data){

                    return {
                        results:data
                    };

                }

            }

        });

    }

    function initClassOutSelect2(selector){

        $(selector).select2({

            theme:'bootstrap-5',
            placeholder:'-- PILIH CLASS OUT --',
            width:'100%',

            ajax:{

                url:
                    "<?= base_url('culling/get_class_out'); ?>",

                dataType:'json',

                processResults:function(data){

                    return {
                        results:data
                    };

                }

            }

        });

    }

    function loadPage(page = 1)
    {
        state.page = page;

        showTableLoading();

        $.ajax({

            url:
                "<?= base_url('culling/load_data') ?>",

            type: 'GET',

            data: state,

            dataType: 'json',

            success: function (res)
            {
                renderTable(res.rows);

                $('#pagination')
                    .html(
                        res.pagination
                    );

                $('#info').html(

                    `Total :
                    ${res.total}
                    Data`

                );
            },

            complete: function ()
            {
                hideTableLoading();
            }

        });
    }

    function renderTable(rows)
    {
        let html = '';

        if (rows.length === 0)
        {
            html += `
                <tr>
                    <td colspan="7"
                        class="text-center">
                        Data tidak ditemukan
                    </td>
                </tr>
            `;

            $('#table-body').html(html);

            return;
        }

        rows.forEach(function (r) {

            html += `

            <tr>

                <td class="text-center fw-semibold" style="vertical-align: middle;">
                    ${r.PLANT_NAME ?? '-'}
                </td>

                <td class="text-center" style="vertical-align: middle">
                    ${formatTanggalIndonesia(r.ymd)}
                </td>

                <td class="text-center" style="vertical-align: middle">
                    ${r.CLASS_OUT_NAME ?? '-'}
                </td>

                <td class="text-end">
                    ${formatIDNumber(r.jumlah)}
                </td>

                <td class="text-end">
                    ${formatIDNumber(r.berat)}
                </td>

                <td class="text-center" style="vertical-align: middle">
                    ${r.remark ?? '-'}
                </td>

                <td class="text-center" style="vertical-align: middle">
                    <button
                        class="btn btn-outline-primary btnPrint"
                        data-id="${r.idno}">

                        Slip

                    </button>

                    <button
                        class="btn btn-outline-warning btnEdit"
                        data-id="${r.idno}">

                        Edit

                    </button>

                    <button
                        class="btn btn-outline-danger btnDelete"
                        data-id="${r.idno}">

                        Hapus

                    </button>
                </td>

            </tr>

            `;
        });

        $('#table-body').html(html);
    }

    const Toast =
    Swal.mixin({

        toast:true,
        position:'top-end',
        timer:3000,
        timerProgressBar:true,
        showConfirmButton:false

    });

    $('#fcullingAdd').submit(function (e) {
        e.preventDefault();

        $('#JUMLAH_ADD_HIDDEN').val(

            $('#JUMLAH_ADD')
                .val()
                .replace(/\./g,'')
                .replace(',','.')

        );

        $('#BERAT_ADD_HIDDEN').val(

            $('#BERAT_ADD')
                .val()
                .replace(/\./g,'')
                .replace(',','.')

        );

        console.log($('#plantAdd').val());
        console.log($('#classOutAdd').val());

        for (let pair of new FormData(this).entries()) {
            console.log(pair[0], pair[1]);
        }

        let formData =
            new FormData(this);

        $.ajax({

            url:
                "<?= base_url('culling/create') ?>",

            type: 'POST',

            data: formData,

            processData: false,
            contentType: false,

            dataType: 'json',

            success: function (res)
            {
                if (res.status)
                {
                    $('#cullingAdd')
                        .modal('hide');

                    $('#fcullingAdd')[0]
                        .reset();

                    $('#plantAdd')
                        .val(null)
                        .trigger('change');

                    $('#classOutAdd')
                        .val(null)
                        .trigger('change');

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
    });

    $(document).on(
        'click',
        '.btnEdit',
        function ()
    {
        let id =
            $(this).data('id');

        $.get(

            "<?= base_url('culling/edit') ?>",

            {
                idno: id
            },

            function (res)
            {
                if (!res.status)
                {
                    Toast.fire({
                        icon:'success',
                        title:res.message
                    });

                    return;
                }

                let h =
                    res.header;

                $('#IDNO_EDIT')
                    .val(h.idno);

                $('#YMD_EDIT')
                    .val(h.ymd);

                $('#JUMLAH_EDIT_DISPLAY')
                    .val(
                        formatIDNumber(
                            h.jumlah
                        )
                    );

                $('#JUMLAH_EDIT')
                    .val(h.jumlah);

                $('#BERAT_EDIT_DISPLAY')
                    .val(
                        formatIDNumber(
                            h.berat
                        )
                    );

                $('#BERAT_EDIT')
                    .val(h.berat);

                $('#REMARK_EDIT')
                    .val(h.remark);

                /*
                |--------------------------------------------------------------------------
                | PLANT
                |--------------------------------------------------------------------------
                */

                let plantOption =
                    new Option(
                        h.PLANT_NAME,
                        h.plant,
                        true,
                        true
                    );

                $('#plantEdit').empty();

                $('#plantEdit')
                    .append(plantOption)
                    .trigger('change');

                /*
                |--------------------------------------------------------------------------
                | CLASS OUT
                |--------------------------------------------------------------------------
                */

                let classOption =
                    new Option(
                        h.CLASS_OUT_NAME,
                        h.class_out,
                        true,
                        true
                    );

                $('#classOutEdit').empty();

                $('#classOutEdit')
                    .append(classOption)
                    .trigger('change');

                $('#cullingEdit')
                    .modal('show');
            },

            'json'

        );
    });

    $('#fcullingEdit').submit(
    function (e)
    {
        e.preventDefault();

        $('#JUMLAH_EDIT').val(

            $('#JUMLAH_EDIT_DISPLAY')
                .val()
                .replace(/\./g,'')
                .replace(',','.')

        );

        $('#BERAT_EDIT').val(

            $('#BERAT_EDIT_DISPLAY')
                .val()
                .replace(/\./g,'')
                .replace(',','.')

        );

        let formData =
            new FormData(this);

        $.ajax({

            url:
                "<?= base_url('culling/update') ?>",

            type: 'POST',

            data: formData,

            processData: false,
            contentType: false,

            dataType: 'json',

            success: function (res)
            {
                if (res.status)
                {
                    $('#cullingEdit')
                        .modal('hide');

                    loadPage();

                    Toast.fire({
                        icon:'success',
                        title:res.message
                    });
                }
                else
                {
                    Toast.fire({
                        icon:'success',
                        title:res.message
                    });
                }
            }

        });
    });

    $(document).on(
        'click',
        '.btnDelete',
        function ()
    {
        let id =
            $(this).data('id');

        Swal.fire({

            title:
                'Hapus Data ?',

            text:
                'Data tidak dapat dikembalikan',

            icon:
                'warning',

            showCancelButton:
                true

        }).then((result) => {

            if (!result.isConfirmed)
            {
                return;
            }

            $.ajax({

                url:
                    "<?= base_url('culling/remove') ?>",

                type:
                    'POST',

                data: {
                    idno: id
                },

                dataType:
                    'json',

                success:
                    function (res)
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

        });

    });

    $(document).on(
        'click',
        '.btnPrint',
        function ()
    {
        let id =
            $(this).data('id');

        window.open(
            "<?= base_url('culling/print_pdf?idno=') ?>" +
            id,
            '_blank'
        );
    });

    $(function () {

        initPlantSelect2(
            '#filterPlant'
        );

        initPlantSelect2(
            '#plantAdd'
        );

        initPlantSelect2(
            '#plantEdit'
        );

        initClassOutSelect2(
            '#classOutAdd'
        );

        initClassOutSelect2(
            '#classOutEdit'
        );

        loadPage();

    });
</script>
