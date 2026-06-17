<?php $userPlant = $this->session->userdata('plant'); ?>

<div class="culling-report-wrap">

    <div class="report-filter-card">

        <div class="row g-3 align-items-end">

            <!-- PLANT -->

            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Plant
                </label>

                <select
                    id="cl_filter_plant"
                    class="form-control">

                    <option value="">
                        Choose Plant
                    </option>

                    <?php foreach ($plants as $p): ?>

                        <?php if ($p->CODE != '*'): ?>

                            <option value="<?= $p->CODE ?>">
                                <?= $p->CODE_NAME ?>
                            </option>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- CLASS OUT -->

            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Class Out
                </label>

                <select
                    id="cl_filter_class_out"
                    class="form-control">

                    <option value="">
                        Choose Class Out
                    </option>

                    <?php foreach ($class_out as $c): ?>

                        <option value="<?= $c->CODE ?>">
                            <?= $c->CODE_NAME ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- SEARCH -->

            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Search
                </label>

                <input
                    type="text"
                    id="cl_filter_search"
                    class="form-control"
                    placeholder="Search remark...">

            </div>

            <!-- DATE FROM -->

            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Date From
                </label>

                <input
                    type="date"
                    id="cl_date_from"
                    class="form-control">

            </div>

            <!-- DATE TO -->

            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Date To
                </label>

                <input
                    type="date"
                    id="cl_date_to"
                    class="form-control">

            </div>

            <div class="col-md-10"></div>

            <!-- EXPORT -->

            <div class="col-md-2">

                <div class="btn-group w-100">

                    <button
                        class="btn btn-success dropdown-toggle"
                        data-bs-toggle="dropdown">

                        <i class="fa fa-download me-1"></i>
                        Export

                    </button>

                    <ul class="dropdown-menu w-100">

                        <li>

                            <a
                                href="#"
                                class="dropdown-item"
                                id="cl_exportExcel">

                                <i class="fa fa-file-excel text-success me-2"></i>

                                Export Excel

                            </a>

                        </li>

                        <li>

                            <a
                                href="#"
                                class="dropdown-item"
                                id="cl_exportPDF">

                                <i class="fa fa-file-pdf text-danger me-2"></i>

                                Export PDF

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

    <!-- LOADING -->

    <div
        id="clLoading"
        class="report-loading d-none">

        <div class="text-center">

            <div
                class="spinner-border text-primary">
            </div>

            <div class="fw-semibold mt-3">
                Loading report...
            </div>

        </div>

    </div>

    <!-- CONTENT -->

    <div id="cullingReportWrapper"></div>

    <!-- PAGINATION -->

    <div
        class="d-flex justify-content-between align-items-center mt-4">

        <div
            id="cl_pageInfo"
            class="text-muted small">
        </div>

        <div id="cl_pagination"></div>

    </div>

</div>

<style>

    .culling-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    margin-bottom:24px;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
    border:1px solid #edf2f7;
}

.culling-head{
    background:
        linear-gradient(
            135deg,
            #0f4c81,
            #2563eb
        );

    color:#fff;
    padding:22px 24px;
}

.culling-title{
    font-size:22px;
    font-weight:700;
}

.culling-body{
    padding:24px;
}

.summary-box{
    display:flex;
    justify-content:space-around;
    text-align:center;
}

.summary-value{
    font-size:28px;
    font-weight:700;
    color:#0f4c81;
}

.summary-label{
    color:#64748b;
}
.receive-report-wrap{
    padding:4px;
}

.report-filter-card{
    background:#fff;
    border:1px solid #edf2f7;
    border-radius:18px;
    padding:24px;
    box-shadow:0 8px 25px rgba(15,23,42,.05);
    margin-bottom:24px;
}

.report-loading{
    min-height:280px;
    background:#fff;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    border:1px solid #edf2f7;
}

.receive-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    margin-bottom:24px;
    box-shadow:0 10px 30px rgba(15,23,42,.06);
    border:1px solid #edf2f7;
}

.receive-head{
    background:linear-gradient(135deg,#0f4c81,#2563eb);
    color:#fff;
    padding:22px 24px;
}

.receive-title{
    font-size:22px;
    font-weight:700;
    letter-spacing:.5px;
}

.status-badge{
    padding:7px 14px;
    border-radius:50px;
    font-size:12px;
    font-weight:700;
}

.status-open{
    background:#fff3cd;
    color:#856404;
}

.status-received{
    background:#d1fae5;
    color:#065f46;
}

.meta-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px 40px;
    margin-top:18px;
    font-size:14px;
}

.meta-item{
    display:flex;
    gap:8px;
}

.meta-label{
    min-width:110px;
    opacity:.85;
    font-weight:600;
}

.meta-value{
    flex:1;
    font-weight:500;
}

.receive-body{
    padding:10px;
}

.attach-badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:50px;
    font-size:12px;
    font-weight:700;
    background:#e0f2fe;
    color:#075985;
}

.table-detail{
    margin:0;
    font-size:14px;
}

.table-detail thead th{
    background:#f8fafc;
    border-color:#e5e7eb;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.4px;
}

.table-detail td{
    border-color:#edf2f7;
    vertical-align:middle;
}

.subtotal-row{
    background:#f8fafc;
    font-weight:700;
}

@media(max-width:768px){
    .meta-grid{
        grid-template-columns:1fr;
        gap:8px;
    }

    .receive-title{
        font-size:18px;
    }

    .receive-head,
    .receive-body{
        padding:18px;
    }
}
</style>

<script>

window.CullingReport = {

    loaded: false,
    page: 1,
    limit: 10,
    searchTimer: null,

    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    init()
    {
        if (this.loaded)
            return;

        this.loaded = true;

        this.initSelect2();

        this.bind();

        this.bindExport();

        this.setDefault();

        this.load();
    },

    /*
    |--------------------------------------------------------------------------
    | SELECT2
    |--------------------------------------------------------------------------
    */

    initSelect2()
    {
        $('#cl_filter_plant').select2({

            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Choose Plant',
            allowClear: true

        });

        $('#cl_filter_class_out').select2({

            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Choose Class Out',
            allowClear: true

        });
    },

    /*
    |--------------------------------------------------------------------------
    | DEFAULT
    |--------------------------------------------------------------------------
    */

    setDefault()
    {
        $('#cl_date_from').val('');
        $('#cl_date_to').val('');
    },

    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    query()
    {
        return {

            page:
                this.page,

            limit:
                this.limit,

            plant:
                $('#cl_filter_plant').val(),

            class_out:
                $('#cl_filter_class_out').val(),

            search:
                $('#cl_filter_search').val(),

            date_from:
                $('#cl_date_from').val(),

            date_to:
                $('#cl_date_to').val()
        };
    },

    /*
    |--------------------------------------------------------------------------
    | BIND
    |--------------------------------------------------------------------------
    */

    bind()
    {
        const self = this;

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $('#cl_filter_search')
            .off('keyup')
            .on(
                'keyup',
                function ()
        {
            clearTimeout(
                self.searchTimer
            );

            self.searchTimer =
                setTimeout(() => {

                    self.page = 1;

                    self.load();

                }, 500);
        });

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $('#cl_filter_plant, #cl_filter_class_out')
            .off('change')
            .on(
                'change',
                function ()
        {
            self.page = 1;

            self.load();
        });

        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        $('#cl_date_from, #cl_date_to')
            .off('change')
            .on(
                'change',
                function ()
        {
            self.page = 1;

            self.load();
        });

    },

    /*
    |--------------------------------------------------------------------------
    | EXPORT
    |--------------------------------------------------------------------------
    */

    bindExport()
    {
        $('#cl_exportExcel')
            .off('click')
            .on(
                'click',
                (e)=>
        {
            e.preventDefault();

            window.open(

                '<?= base_url("report-inventory/export_excel_culling"); ?>?'
                + $.param(
                    this.query()
                ),

                '_blank'
            );
        });

        $('#cl_exportPDF')
            .off('click')
            .on(
                'click',
                (e)=>
        {
            e.preventDefault();

            window.open(

                '<?= base_url("report-inventory/export_pdf_culling"); ?>?'
                + $.param(
                    this.query()
                ),

                '_blank'
            );
        });
    },

    /*
    |--------------------------------------------------------------------------
    | LOADING
    |--------------------------------------------------------------------------
    */

    showLoading()
    {
        $('#clLoading')
            .removeClass('d-none');
    },

    hideLoading()
    {
        $('#clLoading')
            .addClass('d-none');
    },

    /*
    |--------------------------------------------------------------------------
    | LOAD
    |--------------------------------------------------------------------------
    */

    load(page = null)
    {
        if (page) {
            this.page = page;
        }

        this.showLoading();

        $.ajax({

            url:
                '<?= base_url("report-inventory/load_culling"); ?>',

            type:
                'GET',

            data:
                this.query(),

            dataType:
                'json',

            success:
                (resp)=>
            {
                this.render(
                    resp.rows || []
                );

                $('#cl_pagination')
                    .html(
                        resp.pagination || ''
                    );

                $('#cl_pageInfo')
                    .html(

                        `Total Data :
                        ${resp.total || 0}`

                    );
            },

            complete:
                ()=>
            {
                this.hideLoading();
            }

        });
    },

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    render(rows)
    {
        let wrap =
            $('#cullingReportWrapper');

        wrap.empty();

        if (!rows.length)
        {
            wrap.html(

                `
                <div
                    class="alert alert-light text-center">

                    Data tidak ditemukan

                </div>
                `

            );

            return;
        }

        rows.forEach((r)=>{

            wrap.append(`

                <div class="culling-card">

                    <div class="culling-head">

                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-center">

                            <div
                                class="culling-title">

                                #${r.idno}

                            </div>

                            <span
                                class="badge bg-light text-dark">

                                CULLING

                            </span>

                        </div>

                        <div
                            class="meta-grid">

                            <div
                                class="meta-item">

                                <span
                                    class="meta-label">

                                    PLANT

                                </span>

                                <span
                                    class="meta-value">

                                    :
                                    ${r.PLANT_NAME ?? '-'}

                                </span>

                            </div>

                            <div
                                class="meta-item">

                                <span
                                    class="meta-label">

                                    DATE

                                </span>

                                <span
                                    class="meta-value">

                                    :
                                    ${this.dateIndoLong(
                                        r.ymd
                                    )}

                                </span>

                            </div>

                            <div
                                class="meta-item">

                                <span
                                    class="meta-label">

                                    CLASS OUT

                                </span>

                                <span
                                    class="meta-value">

                                    :
                                    ${r.CLASS_OUT_NAME ?? '-'}

                                </span>

                            </div>

                            <div
                                class="meta-item">

                                <span
                                    class="meta-label">

                                    CREATED BY

                                </span>

                                <span
                                    class="meta-value">

                                    :
                                    ${r.CREATED_BY ?? '-'}

                                </span>

                            </div>

                            <div
                                class="meta-item"
                                style="
                                    grid-column:
                                    1/-1;
                                ">

                                <span
                                    class="meta-label">

                                    REMARK

                                </span>

                                <span
                                    class="meta-value">

                                    :
                                    ${r.remark ?? '-'}

                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="culling-body">

                        <div
                            class="summary-box">

                            <div>

                                <div
                                    class="summary-value">

                                    ${this.decimal(
                                        r.jumlah
                                    )}

                                </div>

                                <div
                                    class="summary-label">

                                    Jumlah

                                </div>

                            </div>

                            <div>

                                <div
                                    class="summary-value">

                                    ${this.decimal(
                                        r.berat
                                    )}

                                </div>

                                <div
                                    class="summary-label">

                                    Berat

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            `);

        });

    },

    /*
    |--------------------------------------------------------------------------
    | DECIMAL
    |--------------------------------------------------------------------------
    */

    decimal(val)
    {
        val =
            parseFloat(
                val || 0
            );

        return val.toLocaleString(
            'id-ID',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 6
            }
        );
    },

    /*
    |--------------------------------------------------------------------------
    | DATE INDONESIA
    |--------------------------------------------------------------------------
    */

    dateIndoLong(date)
    {
        if (!date)
            return '-';

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

        let d =
            new Date(date);

        if (isNaN(d))
            return date;

        return (

            d.getDate() +
            ' ' +
            bulan[
                d.getMonth()
            ] +
            ' ' +
            d.getFullYear()

        );
    }

};

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

function loadCullingPage(page)
{
    if (
        window.CullingReport
    ) {

        CullingReport.load(
            page
        );

    }
}

</script>