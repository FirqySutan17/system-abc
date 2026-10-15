<?php $userPlant = $this->session->userdata('plant'); ?>

<div class="receive-report-wrap">

    <!-- FILTER -->
    <div class="report-filter-card">

        <div class="row g-3 align-items-end">

            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Plant
                </label>

                <select
                    id="rc_filter_plant"
                    class="form-control"
                >

                    <option value="">
                        Pilih Plant
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


            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Supplier
                </label>

                <select
                    id="rc_filter_supplier"
                    class="form-control"
                >

                    <option value="">
                        Pilih Supplier
                    </option>

                    <?php foreach ($suppliers as $s): ?>

                        <option value="<?= $s->CUST ?>">
                            <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label fw-semibold">
                    Receive / PO
                </label>

                <input
                    type="text"
                    id="rc_filter_receive"
                    class="form-control"
                    placeholder="Cari Nomor Receive / PO..."
                >

            </div>


            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Tanggal Mulai
                </label>

                <input
                    type="date"
                    id="rc_date_from"
                    class="form-control"
                >

            </div>


            <div class="col-md-2">

                <label class="form-label fw-semibold">
                    Tanggal Selesai
                </label>

                <input
                    type="date"
                    id="rc_date_to"
                    class="form-control"
                >

            </div>


            <div class="col-md-10"></div>


            <div class="col-md-2">

                <div class="d-grid gap-2">

                    <div class="btn-group">

                        <button
                            class="btn btn-success dropdown-toggle"
                            data-bs-toggle="dropdown"
                        >

                            <i class="fa fa-download me-1"></i>
                            Export

                        </button>


                        <ul class="dropdown-menu w-100">

                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item"
                                    id="rc_exportExcel"
                                >

                                    <i class="fa fa-file-excel text-success me-2"></i>
                                    Export Excel

                                </a>

                            </li>


                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item"
                                    id="rc_exportPDF"
                                >

                                    <i class="fa fa-file-pdf text-danger me-2"></i>
                                    Export PDF

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- LOADING -->

    <div
        id="rcLoading"
        class="report-loading d-none"
    >

        <div class="text-center">

            <div class="spinner-border text-primary"></div>

            <div class="fw-semibold mt-3">
                Loading report...
            </div>

            <small class="text-muted">
                Mohon tunggu sebentar
            </small>

        </div>

    </div>


    <!-- CONTENT -->

    <div id="receiveReportWrapper"></div>


    <!-- PAGINATION -->

    <div class="d-flex justify-content-between align-items-center mt-4">

        <div
            id="rc_pageInfo"
            class="text-muted small"
        ></div>

        <div id="rc_pagination"></div>

    </div>

</div>


<style>

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

.receive-table-wrap{
    background:#fff;
    border:1px solid #edf2f7;
    border-radius:18px;
    box-shadow:0 8px 25px rgba(15,23,42,.05);
    overflow:hidden;
}

.receive-table-scroll{
    overflow-x:auto;
    max-width:100%;
}

.receive-table{
    margin:0;
    min-width:2800px;
    font-size:13px;
}

.receive-table thead th{
    background:#f8fafc;
    border-color:#dfe5ec;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.3px;
    white-space:nowrap;
    vertical-align:middle;
    text-align:center;
    position:sticky;
    top:0;
    z-index:5;
}

.receive-table thead .group-header{
    background:#eaf1f8;
    font-size:12px;
    color:#1e293b;
    z-index:6;
}



.receive-table .number{
    text-align:right;
}

.receive-table .center{
    text-align:center;
}

.receive-table .money{
    text-align:right;
}

.receive-table .status-badge{
    display:inline-block;
    padding:4px 9px;
    border-radius:50px;
    font-size:10px;
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

.extra-badge{
    display:inline-block;
    padding:3px 7px;
    border-radius:50px;
    background:#fff3cd;
    color:#856404;
    font-size:10px;
    font-weight:700;
}

.sales-badge{
    display:inline-block;
    padding:3px 7px;
    border-radius:50px;
    background:#d1fae5;
    color:#065f46;
    font-size:10px;
    font-weight:700;
}

.saving-badge{
    display:inline-block;
    padding:3px 7px;
    border-radius:50px;
    background:#dbeafe;
    color:#1d4ed8;
    font-size:10px;
    font-weight:700;
}

.receive-table tfoot td{
    background:#f8fafc;
    font-weight:700;
    border-color:#dfe5ec;
    white-space:nowrap;
}

@media(max-width:768px){

    .report-filter-card{
        padding:18px;
    }

}

.receive-table tbody td{

    border-left:1px solid #edf2f7;

    border-right:1px solid #edf2f7;

    border-top:0;

    border-bottom:1px solid #c5d0dc !important;

    vertical-align:middle;

    white-space:nowrap;

}


.receive-table tbody tr{

    border-bottom:1px solid #c5d0dc !important;

}


.receive-table tbody tr:hover{

    background:#f8fafc;

}

.receive-table{

    margin:0;

    min-width:2800px;

    font-size:13px;

    border-collapse:separate !important;

    border-spacing:0;

}


.receive-table tbody td{

    vertical-align:middle;

    white-space:nowrap;

    border-top:0 !important;

    border-left:1px solid #e5e7eb !important;

    border-right:1px solid #e5e7eb !important;

    border-bottom:1px solid #cbd5e1 !important;

}


.receive-table tbody tr{

    border-bottom:1px solid #cbd5e1 !important;

}


.receive-table tbody tr:hover td{

    background:#f8fafc;

}


/*
|--------------------------------------------------------------------------
| GARIS PEMISAH RECEIVE
|--------------------------------------------------------------------------
|
| Row pertama dari Receive baru diberi garis lebih tegas.
|
*/

.receive-table tbody tr.receive-group-start td{

    border-top:2px solid #94a3b8 !important;

}


/*
|--------------------------------------------------------------------------
| GARIS ROW BIASA
|--------------------------------------------------------------------------
*/

.receive-table tbody tr.receive-group-row td{

    border-bottom:1px solid #d5dee8 !important;

}

</style>


<script>

window.ReceiveReport = {

    loaded:false,

    page:1,

    limit:10,


    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    init(){

        if(this.loaded) return;

        this.loaded = true;

        this.initSelect2();

        this.bind();

        setTimeout(()=>{

            this.setDefault();

            this.load();

        },100);

    },


    /*
    |--------------------------------------------------------------------------
    | SELECT2
    |--------------------------------------------------------------------------
    */

    initSelect2(){

        $('#rc_filter_plant').select2({

            width:'100%',

            placeholder:'Pilih Plant'

        });


        $('#rc_filter_supplier').select2({

            width:'100%',

            placeholder:'Pilih Supplier',

            allowClear:true

        });

    },


    /*
    |--------------------------------------------------------------------------
    | DEFAULT
    |--------------------------------------------------------------------------
    */

    setDefault(){

        const now = new Date();

        const yyyy = now.getFullYear();

        const mm = String(
            now.getMonth()+1
        ).padStart(2,'0');

        const dd = String(
            now.getDate()
        ).padStart(2,'0');


        $('#rc_date_from').val(
            `${yyyy}-${mm}-01`
        );


        $('#rc_date_to').val(
            `${yyyy}-${mm}-${dd}`
        );


        const firstPlant =
            $('#rc_filter_plant option')

            .filter(function(){

                return $(this).val() !== '';

            })

            .first()

            .val();


        if(firstPlant){

            $('#rc_filter_plant')
                .val(firstPlant)
                .trigger('change');

        }

    },


    /*
    |--------------------------------------------------------------------------
    | BIND
    |--------------------------------------------------------------------------
    */

    bind(){

        let timer;


        $('#rc_btnFilter').on(
            'click',
            ()=>{

                this.page = 1;

                this.load();

            }
        );


        $('#rc_filter_receive').on(
            'keyup',
            ()=>{

                clearTimeout(timer);


                timer = setTimeout(()=>{

                    this.page = 1;

                    this.load();

                },300);

            }
        );


        $('#rc_filter_plant,#rc_filter_supplier,#rc_date_from,#rc_date_to')

            .on(
                'change',
                ()=>{

                    this.page = 1;

                    this.load();

                }
            );


        $('#rc_exportExcel').on(
            'click',
            (e)=>{

                e.preventDefault();


                window.open(

                    '<?= base_url("report-inventory/export_excel_receive"); ?>?'
                    + $.param(this.query()),

                    '_blank'

                );

            }
        );


        $('#rc_exportPDF').on(
            'click',
            (e)=>{

                e.preventDefault();


                window.open(

                    '<?= base_url("report-inventory/export_pdf_receive"); ?>?'
                    + $.param(this.query()),

                    '_blank'

                );

            }
        );


        $(document).on(

            'click',

            '#rc_pagination a',

            (e)=>{

                e.preventDefault();


                const page =
                    $(e.currentTarget)
                        .data('page');


                if(page){

                    this.load(page);

                }

            }

        );

    },


    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    query(){

        return {

            page:this.page,

            limit:this.limit,

            plant:$('#rc_filter_plant').val(),

            supplier:$('#rc_filter_supplier').val(),

            receive:$('#rc_filter_receive').val(),

            date_from:$('#rc_date_from').val(),

            date_to:$('#rc_date_to').val()

        };

    },


    /*
    |--------------------------------------------------------------------------
    | LOADING
    |--------------------------------------------------------------------------
    */

    showLoading(){

        $('#rcLoading')
            .removeClass('d-none');

        $('#receiveReportWrapper')
            .hide();

    },


    hideLoading(){

        $('#rcLoading')
            .addClass('d-none');

        $('#receiveReportWrapper')
            .show();

    },


    /*
    |--------------------------------------------------------------------------
    | LOAD
    |--------------------------------------------------------------------------
    */

    load(page=null){

        if(page){

            this.page = page;

        }


        this.showLoading();


        $.get(

            '<?= base_url("report-inventory/load_receive"); ?>',

            this.query(),

            (resp)=>{

                resp =
                    typeof resp === 'string'

                    ? JSON.parse(resp)

                    : resp;


                this.render(
                    resp.rows || []
                );


                $('#rc_pagination')
                    .html(
                        resp.pagination || ''
                    );


                $('#rc_pageInfo')
                    .html(
                        `Total data : ${resp.total || 0}`
                    );

            }

        )

        .always(()=>{

            this.hideLoading();

        });

    },


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    render(rows){

        const wrap = $('#receiveReportWrapper');

        wrap.empty();

        if(!rows || !rows.length){

            wrap.html(`
                <div class="text-center py-5 text-muted bg-white rounded-4 border">
                    Data tidak ditemukan
                </div>
            `);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | GROUP ROW BY RECEIVE + PLANT
        |--------------------------------------------------------------------------
        */

        const grouped = {};

        rows.forEach(r => {

            const key =
                (r.RECEIVE || '') +
                '|' +
                (r.PLANT || '');

            if(!grouped[key]){

                grouped[key] = [];

            }

            grouped[key].push(r);

        });


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        let totalQty = 0;
        let totalWeight = 0;
        let totalShrinkQty = 0;
        let totalShrinkWeight = 0;
        let totalReceive = 0;


        /*
        |--------------------------------------------------------------------------
        | BUILD ROW
        |--------------------------------------------------------------------------
        */

        let html = '';


        Object.keys(grouped).forEach(key => {

            const group = grouped[key];

            const rowspan = group.length;


            group.forEach((r, index) => {

                /*
                |--------------------------------------------------------------------------
                | TOTAL
                |--------------------------------------------------------------------------
                */

                totalQty += Number(
                    r.JUMLAH || 0
                );

                totalWeight += Number(
                    r.BERAT || 0
                );

                totalShrinkQty += Number(
                    r.SUSUT_JUMLAH || 0
                );

                totalShrinkWeight += Number(
                    r.SUSUT_BERAT || 0
                );

                totalReceive += Number(
                    r.TOTAL || 0
                );


                /*
                |--------------------------------------------------------------------------
                | RECEIVE STATUS
                |--------------------------------------------------------------------------
                */

                let receiveStatus = `
                    <span class="status-badge status-received">
                        RECEIVED
                    </span>
                `;


                if(r.STATUS_RECEIVE === 'OPEN'){

                    receiveStatus = `
                        <span class="status-badge status-open">
                            OPEN
                        </span>
                    `;

                }else if(r.STATUS_RECEIVE){

                    receiveStatus = `
                        <span class="status-badge status-received">
                            ${r.STATUS_RECEIVE}
                        </span>
                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | EXTRA
                |--------------------------------------------------------------------------
                */

                let extra = '-';


                if(Number(r.IS_EXTRA) === 1){

                    extra = `
                        <span class="extra-badge">
                            EXTRA
                        </span>
                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | SALES
                |--------------------------------------------------------------------------
                */

                const salesNo =
                    r.SALES_REPORT_NO || '-';

                const salesStatus =
                    r.SALES_STATUS || '-';


                /*
                |--------------------------------------------------------------------------
                | SAVING
                |--------------------------------------------------------------------------
                */

                const savingNo =
                    r.SAVING_NO_LIST || '-';

                const savingStatus =
                    r.SAVING_STATUS || '-';


                /*
                |--------------------------------------------------------------------------
                | PO
                |--------------------------------------------------------------------------
                */

                const poText =
                    r.PO || 'DIRECT RECEIVE';


                /*
                |--------------------------------------------------------------------------
                | RECEIVE INFORMATION
                |--------------------------------------------------------------------------
                |
                | Hanya row pertama yang memiliki cell RECEIVE.
                | Row berikutnya otomatis mengikuti rowspan.
                |
                */

                let receiveInformation = '';


                if(index === 0){

                    receiveInformation = `

                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            <strong>
                                ${r.RECEIVE || '-'}
                            </strong>

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            ${this.dateIndoLong(
                                r.RECEIVE_DATE
                            )}

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            ${r.PLANT_NAME || r.PLANT || '-'}

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            ${poText}

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            ${r.SUPPLIER || '-'}

                            <div class="small text-muted">

                                ${r.SUPPLIER_NAME || ''}

                            </div>

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            ${r.NOTA || '-'}

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle"
                        >

                            ${r.NO_REF || '-'}

                        </td>


                        <td
                            rowspan="${rowspan}"
                            class="align-middle text-center"
                        >

                            ${receiveStatus}

                        </td>

                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | ROW
                |--------------------------------------------------------------------------
                */

                html += `

                    <tr class="${
                            index === 0
                                ? 'receive-group-start'
                                : 'receive-group-row'
                        }">

                        ${receiveInformation}


                        <!-- RECEIVE DETAIL -->

                        <td>

                            ${r.CUSTOMER || '-'}

                            <div class="small text-muted">

                                ${r.CUSTOMER_NAME || ''}

                            </div>

                        </td>


                        <td>

                            ${r.MATERIAL || '-'}

                            <div class="small text-muted">

                                ${r.MATERIAL_NAME || ''}

                            </div>

                        </td>


                        <td>

                            ${r.PO_TYPE_NAME || r.PO_TYPE || '-'}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.JUMLAH
                            )}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.BERAT
                            )}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.AVG_BW
                            )}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.SUSUT_JUMLAH
                            )}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.SUSUT_BERAT
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.HARGA
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.DISCOUNT
                            )}

                        </td>


                        <td class="money fw-semibold">

                            ${this.money(
                                r.TOTAL
                            )}

                        </td>


                        <td class="center">

                            ${extra}

                        </td>


                        <!-- SALES -->

                        <td>

                            ${salesNo}

                        </td>


                        <td>

                            ${
                                r.SALES_REPORT_DATE

                                ? this.dateIndoLong(
                                    r.SALES_REPORT_DATE
                                )

                                : '-'
                            }

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_AMOUNT
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_MODAL
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_BIAYA
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_DP
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_DISCOUNT
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_ROUNDING
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SALES_REMAIN
                            )}

                        </td>


                        <td>

                            ${salesStatus}

                        </td>


                        <!-- SAVING -->

                        <td
                            class="${index === 0 ? '' : ''}"
                        >

                            ${savingNo}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SAVING_AMOUNT
                            )}

                        </td>


                        <td>

                            ${r.SAVING_BASIS || '-'}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.SAVING_RATE
                            )}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.SAVING_QTY
                            )}

                        </td>


                        <td class="number">

                            ${this.decimal(
                                r.SAVING_WEIGHT
                            )}

                        </td>


                        <td class="money">

                            ${this.money(
                                r.SAVING_REMAIN
                            )}

                        </td>


                        <td>

                            ${savingStatus}

                        </td>

                    </tr>

                `;

            });

        });


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        wrap.html(`

            <div class="receive-table-wrap">

                <div class="receive-table-scroll">

                    <table
                        class="table table-bordered table-hover receive-table"
                    >

                        <thead>

                            <tr>

                                <th
                                    colspan="8"
                                    class="group-header"
                                >
                                    RECEIVE INFORMATION
                                </th>

                                <th
                                    colspan="12"
                                    class="group-header"
                                >
                                    RECEIVE DETAIL
                                </th>

                                <th
                                    colspan="10"
                                    class="group-header"
                                >
                                    SALES
                                </th>

                                <th
                                    colspan="8"
                                    class="group-header"
                                >
                                    SAVING
                                </th>

                            </tr>


                            <tr>

                                <!-- RECEIVE INFORMATION -->

                                <th>RECEIVE</th>
                                <th>TANGGAL</th>
                                <th>PLANT</th>
                                <th>PO</th>
                                <th>SUPPLIER</th>
                                <th>NOTA</th>
                                <th>REF NO</th>
                                <th>STATUS RECEIVE</th>


                                <!-- RECEIVE DETAIL -->

                                <th>CUSTOMER</th>
                                <th>MATERIAL</th>
                                <th>PO TYPE</th>
                                <th>QTY</th>
                                <th>WEIGHT</th>
                                <th>AVG BW</th>
                                <th>SHRINK QTY</th>
                                <th>SHRINK WEIGHT</th>
                                <th>PRICE</th>
                                <th>DISCOUNT</th>
                                <th>TOTAL</th>
                                <th>EXTRA</th>


                                <!-- SALES -->

                                <th>SALES NO</th>
                                <th>SALES DATE</th>
                                <th>AMOUNT</th>
                                <th>MODAL</th>
                                <th>BIAYA</th>
                                <th>DP</th>
                                <th>DISCOUNT</th>
                                <th>ROUNDING</th>
                                <th>REMAINING</th>
                                <th>STATUS</th>


                                <!-- SAVING -->

                                <th>SAVING NO</th>
                                <th>AMOUNT</th>
                                <th>BASIS</th>
                                <th>RATE</th>
                                <th>TOTAL QTY</th>
                                <th>TOTAL WEIGHT</th>
                                <th>REMAINING</th>
                                <th>STATUS</th>

                            </tr>

                        </thead>


                        <tbody>

                            ${html}

                        </tbody>


                        <tfoot>

                            <tr>

                                <td
                                    colspan="11"
                                    class="text-end"
                                >
                                    TOTAL
                                </td>


                                <td class="number">

                                    ${this.decimal(
                                        totalQty
                                    )}

                                </td>


                                <td class="number">

                                    ${this.decimal(
                                        totalWeight
                                    )}

                                </td>


                                <td></td>


                                <td class="number">

                                    ${this.decimal(
                                        totalShrinkQty
                                    )}

                                </td>


                                <td class="number">

                                    ${this.decimal(
                                        totalShrinkWeight
                                    )}

                                </td>


                                <td></td>

                                <td></td>


                                <td class="money">

                                    ${this.money(
                                        totalReceive
                                    )}

                                </td>


                                <td colspan="18"></td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        `);

    },


    /*
    |--------------------------------------------------------------------------
    | DATE
    |--------------------------------------------------------------------------
    */

    dateIndoLong(date){

        if(!date) return '-';


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


        const d = new Date(date);


        return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;

    },


    /*
    |--------------------------------------------------------------------------
    | MONEY
    |--------------------------------------------------------------------------
    */

    money(v){

        return Number(
            v || 0
        ).toLocaleString(
            'id-ID'
        );

    },


    /*
    |--------------------------------------------------------------------------
    | DECIMAL
    |--------------------------------------------------------------------------
    */

    decimal(v){

        return Number(
            v || 0
        ).toLocaleString(
            'id-ID',
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );

    }

};

</script>