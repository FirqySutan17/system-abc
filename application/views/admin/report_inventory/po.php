<?php
$userPlant = $this->session->userdata('plant');
$roleId    = $this->session->userdata('role_id');
?>

<div class="po-report-wrap">
    <!-- SUMMARY -->
    <div class="row mb-4" id="summaryWrapper">

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    OPEN
                </div>

                <div class="summary-value" id="sumSupplier">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    RECEIVED
                </div>

                <div class="summary-value" id="sumCustomer">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="summary-card">
                <div class="summary-label">
                    PAID
                </div>

                <div class="summary-value" id="sumPaid">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-label">
                    TOTAL WEIGHT
                </div>

                <div class="summary-value" id="sumWeight">
                    0
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card summary-money">
                <div class="summary-label">
                    TOTAL AMOUNT
                </div>

                <div class="summary-value" id="sumAmount">
                    0
                </div>
            </div>
        </div>

    </div>

    <!-- FILTER -->
    <div class="report-filter-card">
        <div class="row g-3 align-items-end">

            <div class="col-md-2">
                <label class="form-label fw-semibold">Plant</label>
                <select id="filter_plant" class="form-control">
                    <?php foreach ($plants as $p): ?>
                        <?php if ($p->CODE == '*') continue; ?>
                        <option value="<?= $p->CODE ?>">
                            <?= $p->CODE_NAME ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Supplier</label>
                <select id="filter_supplier" class="form-control">
                    <option value=""></option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s->CUST ?>">
                            <?= $s->CUST ?> - <?= $s->FULL_NAME ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Nomor PO</label>
                <input
                    type="text"
                    id="filter_po"
                    class="form-control"
                    placeholder="Cari Nomor PO..."
                >
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Tanggal Mulai</label>
                <input
                    type="date"
                    id="filter_date_from"
                    class="form-control"
                >
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">Tanggal Selesai</label>
                <input
                    type="date"
                    id="filter_date_to"
                    class="form-control"
                >
            </div>

            <div class="col-md-10"></div>

            <div class="col-md-2">
                <div class="btn-group w-100">
                    <button
                        class="btn btn-success dropdown-toggle w-100"
                        data-bs-toggle="dropdown"
                    >
                        <i class="fa fa-download me-1"></i>
                        Export
                    </button>

                    <ul class="dropdown-menu w-100">
                        <li>
                            <a href="#" class="dropdown-item" id="exportExcel">
                                <i class="fa fa-file-excel text-success me-2"></i>
                                Export Excel
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item" id="exportPDF">
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
    <div id="poLoading" class="report-loading d-none">
        <div class="text-center">
            <div class="spinner-border text-primary"></div>
            <div class="fw-semibold mt-3">Loading report...</div>
            <small class="text-muted">Mohon tunggu sebentar</small>
        </div>
    </div>

    <!-- CONTENT -->
    <div id="poReportWrapper"></div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div id="pageInfo" class="small text-muted"></div>
        <div id="pagination"></div>
    </div>

</div>

<style>

    .po-report-wrap{
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

    #poReportWrapper.loading{
        opacity:.35;
        pointer-events:none;
        transition:.25s;
    }
    .po-card{
        border:1px solid #e9ecef;
        border-radius:18px;
        overflow:hidden;
        margin-bottom:22px;
        box-shadow:0 8px 30px rgba(0,0,0,.05);
    }

    .po-head{
        background:linear-gradient(135deg,#0F4C81,#1d6fb1);
        color:#fff;
        padding:18px 22px;
    }

    .po-title{
        font-size:18px;
        font-weight:700;
    }

    .po-body{
        padding:10px;
        background:#fff;
    }

    .status-badge{
        padding:5px 12px;
        border-radius:30px;
        font-size:12px;
        font-weight:700;
    }

    .status-open{
        background:#fff3cd;
        color:#9a6700;
    }

    .status-received{
        background:#d1e7dd;
        color:#0f5132;
    }

    .table-detail th{
        background:#f8f9fa;
        font-size:13px;
        text-transform:uppercase;
    }

    .table-detail td{
        vertical-align:middle;
    }

    .subtotal-row{
        background:#f8fafc;
        font-weight:700;
    }

    .po-meta-grid{
        margin-top:14px;
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:8px 28px;
        font-size:13px;
    }

    .meta-label{
        display:inline-block;
        width:85px;
        font-weight:700;
        color:rgba(255,255,255,.85);
        letter-spacing:.4px;
    }

    .meta-value{
        font-weight:500;
        color:#fff;
    }
    .summary-card{
        background:#fff;
        border-radius:18px;
        padding:20px;
        border:1px solid #edf2f7;
        box-shadow:0 8px 24px rgba(15,23,42,.05);
        height:100%;
        transition:.2s;
    }

    .summary-card:hover{
        transform:translateY(-2px);
    }

    .summary-label{
        font-size:12px;
        font-weight:700;
        color:#64748b;
        letter-spacing:.5px;
        margin-bottom:10px;
    }

    .summary-value{
        font-size:18px;
        font-weight:800;
        color:#0f172a;
    }

    .summary-money .summary-value{
        color:#0F4C81;
    }
    .table th{
        white-space:nowrap;
        font-size:14px;
    }

    .table td{
        white-space:nowrap;
    }

    .table-responsive{

        max-height:550px;

    }

    thead th{

        position:sticky;

        top:0;

        z-index:5;

        background:#0F4C81;

        color:#000;

        font-weight:600;

        border:none;

        padding:14px 12px;

        font-size:13px;

        letter-spacing:.3px;

        text-align: center;

        padding: 10px 15px !important;

    }

    .text-money{

        white-space:nowrap;

    }

    .report-table-card{

        background:#fff;

        border-radius:18px;

        overflow:hidden;

        box-shadow:0 12px 35px rgba(15,23,42,.06);

    }

    tbody tr{

        transition:.2s;

    }

    tbody tr:hover{
        background:#eef7ff;
    }

    tbody tr:nth-child(even){

        background:#fbfcfd;

    }

    .badge-open{

        background:#FFF6D8;

        color:#C68600;

    }

    .badge-received{

        background:#DCFCE7;

        color:#15803D;

    }

    .badge-paid{

        background:#DBEAFE;

        color:#2563EB;

    }

    .po-number{

        font-weight:700;

        color:#0F4C81;

        cursor:pointer;

    }

    .material{

        max-width:180px;

        overflow:hidden;

        text-overflow:ellipsis;

    }
</style>

<script>
const POReport = {

    state:{
        page:1,
        limit:10
    },

    init(){
        this.initSelect2();
        this.bind();

        setTimeout(()=>{
            this.setDefault();
            this.load();
        },100);
    },

    setDefault(){
        const now = new Date();

        const yyyy = now.getFullYear();
        const mm   = String(now.getMonth()+1).padStart(2,'0');
        const dd   = String(now.getDate()).padStart(2,'0');

        const today = `${yyyy}-${mm}-${dd}`;
        const first = `${yyyy}-${mm}-01`;

        $('#filter_date_from').val(first);
        $('#filter_date_to').val(today);

        // skip CODE = *
        const $plant = $('#filter_plant');

        const firstValid = $plant.find('option').filter(function () {
            const val = ($(this).val() || '').trim();

            return val !== '' && val !== '*';
        }).first();

        if(firstValid.length){
            $plant.val(firstValid.val()).trigger('change.select2');
        }

        console.log('DEFAULT PLANT =', $plant.val());
    },

    initSelect2(){
        $('#filter_plant').select2({
            width:'100%'
        });

        $('#filter_supplier').select2({
            width:'100%',
            placeholder:'Pilih Supplier',
            allowClear:true
        });
    },

    showLoading(){
        $('#poLoading').removeClass('d-none');
        $('#poReportWrapper').addClass('loading');
    },

    hideLoading(){
        $('#poLoading').addClass('d-none');
        $('#poReportWrapper').removeClass('loading');
    },

    bind(){
        let timer;

        $('#filter_po').on('keyup', ()=>{
            clearTimeout(timer);
            timer = setTimeout(()=>{
                this.state.page = 1;
                this.load();
            },300);
        });

        $('#filter_plant,#filter_supplier,#filter_date_from,#filter_date_to')
            .on('change', ()=>{
                this.state.page = 1;
                this.load();
            });

        $('#exportExcel').on('click',(e)=>{
            e.preventDefault();
            window.open(
                '<?= base_url("report-inventory/export_excel_po"); ?>?'+this.query(),
                '_blank'
            );
        });

        $('#exportPDF').on('click',(e)=>{
            e.preventDefault();
            window.open(
                '<?= base_url("report-inventory/export_pdf_po"); ?>?'+this.query(),
                '_blank'
            );
        });

        $(document).on('click','#pagination a',(e)=>{
            e.preventDefault();

            const page = $(e.currentTarget).data('page');

            if(page){
                this.state.page = page;
                this.load();
            }
        });
    },

    query(){
        return $.param({
            page      : this.state.page,
            limit     : this.state.limit,
            plant     : $('#filter_plant').val(),
            supplier  : $('#filter_supplier').val(),
            po        : $('#filter_po').val(),
            date_from : $('#filter_date_from').val(),
            date_to   : $('#filter_date_to').val()
        });
    },

    money(x){
        return Number(x || 0).toLocaleString('id-ID');
    },

    decimal(x){
        return Number(x || 0).toLocaleString('id-ID',{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        });
    },

    dateIndoLong(date){
        if(!date) return '-';

        const bulan = [
            'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI',
            'JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'
        ];

        const d = new Date(date);

        return `${String(d.getDate()).padStart(2,'0')} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
    },

    renderSummary(summary){

        if(!summary){
            return;
        }

        $('#sumPO').text(
            this.money(summary.TOTAL_PO || 0)
        );

        $('#sumOpen').text(
            this.money(summary.TOTAL_OPEN || 0)
        );

        $('#sumReceived').text(
            this.money(summary.TOTAL_RECEIVED || 0)
        );

        $('#sumPaid').text(
            this.money(summary.TOTAL_PAID || 0)
        );

        $('#sumWeight').text(
            this.decimal(summary.TOTAL_BERAT || 0)
        );

        $('#sumAmount').text(
            'Rp ' + this.money(summary.TOTAL_AMOUNT || 0)
        );

    },

    render(rows){

        const wrap = $('#poReportWrapper').empty();

        if(!rows || !rows.length){

            wrap.html(`
                <div class="text-center py-5 text-muted">
                    Data tidak ditemukan
                </div>
            `);

            return;
        }

        const badge = (status)=>{

            switch((status || '').toUpperCase()){

                case 'PAID':
                    return '<span class="badge bg-primary">PAID</span>';

                case 'PARTIAL':
                    return '<span class="badge bg-info">PARTIAL</span>';

                case 'RECEIVED':
                    return '<span class="badge bg-success">RECEIVED</span>';

                default:
                    return '<span class="badge bg-warning text-dark">OPEN</span>';

            }

        };

        let html = `
        <div class="report-table-card">
            <div class="table-responsive">

                <table class="table table-bordered table-hover table-sm align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>PO</th>
                            <th>Date</th>
                            <th>Plant</th>
                            <th>Supplier</th>
                            <th>Material</th>

                            <th class="text-end">Qty</th>
                            <th class="text-end">Weight</th>
                            <th class="text-end">Avg BW</th>

                            <th class="text-end">Dead Qty</th>
                            <th class="text-end">Dead BW</th>

                            <th class="text-end">Actual</th>
                            <th class="text-end">Shrink</th>

                            <th class="text-end">Receive Qty</th>
                            <th class="text-end">Receive BW</th>

                            <th class="text-end">Claim Qty</th>
                            <th class="text-end">Claim BW</th>

                            <th class="text-end">Payable BW</th>

                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>

                            <th>Truck</th>
                            <th>Driver</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>
        `;

        rows.forEach(r=>{

            html += `

                <tr>

                    <td class="text-center po-number">#${r.PO}</td>

                    <td class="text-center">${this.dateIndoLong(r.PO_DATE)}</td>

                    <td class="text-center">${r.PLANT_NAME}</td>

                    <td class="text-center">${r.SUPPLIER_NAME}</td>

                    <td class="material">${r.MATERIAL_NAME}</td>

                    <td class="text-end">${this.decimal(r.JUMLAH)}</td>

                    <td class="text-end">${this.decimal(r.BERAT)}</td>

                    <td class="text-end">${this.decimal(r.AVG_BW)}</td>

                    <td class="text-end">${this.decimal(r.MATI_QTY)}</td>

                    <td class="text-end">${this.decimal(r.MATI_BW)}</td>

                    <td class="text-end">${this.decimal(r.ACTUAL_HASIL_TIMBANG)}</td>

                    <td class="text-end">${this.decimal(r.SUSUT_BW)}</td>

                    <td class="text-end">${this.decimal(r.TOTAL_TERIMA_QTY)}</td>

                    <td class="text-end">${this.decimal(r.TOTAL_TERIMA_BW)}</td>

                    <td class="text-end">${this.decimal(r.CLAIM_QTY)}</td>

                    <td class="text-end">${this.decimal(r.CLAIM_BW)}</td>

                    <td class="text-end">${this.decimal(r.TOTAL_BAYAR_BW)}</td>

                    <td class="text-end text-money">
                        Rp ${this.money(r.HARGA)}
                    </td>

                    <td class="text-end text-money fw-bold">
                        Rp ${this.money(r.TOTAL)}
                    </td>

                    <td class="text-center">${r.NO_TRUCK || '-'}</td>

                    <td class="text-center">${r.DRIVER || '-'}</td>

                    <td class="text-center">${badge(r.STATUS)}</td>

                </tr>

            `;

        });

        html += `

                    </tbody>

                </table>

            </div>
        </div>

        `;

        wrap.html(html);

    },

    load(){
        this.showLoading();

        $.get(
            '<?= base_url("report-inventory/load_data"); ?>',
            this.query(),
            (resp)=>{
                resp = typeof resp === 'string'
                    ? JSON.parse(resp)
                    : resp;

                this.renderSummary(resp.summary || {});
                this.render(resp.rows || []);
                $('#pagination').html(resp.pagination || '');
                $('#pageInfo').html(`
                    Showing page ${resp.page} of ${resp.pages}
                    (${this.money(resp.total)} PO)
                `);
            }
        )
        .fail(()=>{
            $('#poReportWrapper').html(`
                <div class="alert alert-danger mb-0">
                    Gagal memuat data
                </div>
            `);
        })
        .always(()=>{
            this.hideLoading();
        });
    },
};

$(function(){
    POReport.init();
});
</script>